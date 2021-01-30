<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Response\EntriesResponse;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;
use KAGOnlineTeam\LdapBundle\Serializer\ReflectionSerializer;
use KAGOnlineTeam\LdapBundle\Serializer\SerializerInterface;

/**
 * Manages the instances of Ldap entries and tracks the changes.
 */
class Worker
{
    /**
     * State and marks provide information for usage in a bitmask about the state and commit actions of an instance. 
     */
    const STATE_MANAGED = 1;
    const MARK_LOADED = 2;
    const MARK_COMMITTING = 4;
    const MARK_PERSISTENCE = 8;
    const MARK_REMOVAL = 16;
    const MARK_READ_ONLY = 32;

    private $metadata;
    private $serializer;

    /**
     * A list of all active instances with the object hash as array key.
     * 
     * @var object[]
     */
    private $entries = [];

    /**
     * Stores information about all active instances in the form:
     *     <spl-object-hash> => [
     *         'state' => The state (managed/unmanaged) and additional marks
     *         'original' => The original data set of the entry
     *         'changes' => The currently used change set 
     *     ]
     * 
     * @var array
     */
    private $data = [];

    public function __construct(ClassMetadata $metadata, SerializerInterface $serializer = null)
    {
        $this->metadata = $metadata;
        $this->serializer = null === $serializer ? new ReflectionSerializer($metadata) : $serializer;
    }

    /**
     * Updates the worker by adding the entries from a response.
     */
    public function update(EntriesResponse $response): void
    {
        foreach ($response->getEntries() as [$dn, $objectClasses, $attributes]) {
            // Check if all objectClasses are present.
            if (!empty(\array_diff($this->metadata->getObjectClasses(), $objectClasses))) {
                continue;
            }

            $entry = $this->serializer->denormalize($dn, $attributes);
            $id = \spl_object_hash($entry);

            $this->entries[$id] = $entry;
            $this->data[$id] = [
                'state' => $response->isReadOnly() ? self::STATE_MANAGED | self::MARK_LOADED | self::MARK_READ_ONLY : self::STATE_MANAGED | self::MARK_LOADED,
                'original' => [
                    'dn' => $dn,
                    'objectclasses' => $objectClasses,
                    'attributes' => $attributes,
                ],
                'changes' => null,
            ];
        }
    }

    public function fetchLatest(): iterable
    {
        // Set the internal pointer to the last element and return if the array is empty.
        if (false === \end($this->entries)) {
            return [];
        }

        // Return all entries which have not been loaded.
        do {
            $entry = \current($this->entries);
            $id = \spl_object_hash($entry);
            $state = $this->data[$id]['state'];

            if (0 === (self::MARK_LOADED & $state)) {
                break;
            }

            if (0 !== (self::MARK_READ_ONLY & $state)) {
                unset($this->entries[$id]);
                unset($this->data[$id]);
            } else {
                $this->data[$id]['state'] = $state ^ self::MARK_LOADED;
            }

            yield $entry;
            
        } while (false !== \prev($this->entries));
    }

    /**
     * Adds a mark to the state of an entry which will be used in the commit call.
     */
    public function mark(object $entry, int $mark): void
    {
        if (!\in_array($mark, [self::MARK_READ_ONLY, self::MARK_PERSISTENCE, self::MARK_REMOVAL])) {
            throw new \InvalidArgumentException('Invalid mark given.');
        }

        $id = \spl_object_hash($entry);

        if (self::MARK_PERSISTENCE !== $mark and 0 !== ($mark & $this->data[$id]['state'])) {
            throw new \InvalidArgumentException('The mark is already set.');
        }

        switch ($mark) {
            case self::MARK_READ_ONLY:
                $this->data[$id]['state'] = self::MARK_READ_ONLY | $this->data[$id]['state']; 
                break;
            case self::MARK_PERSISTENCE:
                $this->addEntry($id, $entry);
                break;
            case self::MARK_REMOVAL:
                $this->data[$id]['state'] = self::MARK_REMOVAL | $this->data[$id]['state']; 
                break;
        }
    }

    /**
     * 
     */
    public function createRequests(): iterable
    {
        // Set the internal pointer to the first element and return if the array is empty.
        if (false === \reset($this->entries)) {
            return [];
        }

        // Try block of the generator.
        try {
            do {
                $entry = \current($this->entries);
                $id = \spl_object_hash($entry);
                $state = $this->data[$id]['state'];

                // Ignore entries which are read only, have not been fetched or are being committed.
                if (0 !== ((self::MARK_READ_ONLY | self::MARK_LOADED | self::MARK_COMMITTING) & $state)) {
                    continue;
                }

                $data = $this->serializer->normalize($entry);

                switch ($state) {
                    // Managed and no marks.
                    case self::STATE_MANAGED:
                        $changeSet = $this->computeChangeSet($this->data[$id]['original'], $data);
                        $this->data[$id]['changes'] = $changeSet;

                        if (null === $changeSet) {
                            break;
                        }

                        $this->data[$id]['state'] = $state | self::MARK_COMMITTING;
                        yield new Request\UpdateRequest($this->data[$id]['original']['dn'], $changeSet);
                        break;
                    // Unmanaged and marked persistance.
                    case self::MARK_PERSISTENCE:
                        $this->data[$id]['state'] = $state | self::MARK_COMMITTING;
                        $this->data[$id]['changes'] = $data;
                        yield new Request\NewEntryRequest($data['dn'], ['objectclass' => $data['objectclasses']] + $data['attributes']);
                        break;
                    // Managed and marked removal.
                    case (self::STATE_MANAGED | self::MARK_REMOVAL):
                        $this->data[$id]['state'] = $state | self::MARK_COMMITTING;
                        yield new Request\DeleteRequest($this->data[$id]['original']['dn']);
                        break;

                    default:
                        throw new \RuntimeException('Undefined state.');
                }
            } while (false !== \next($this->entries));

        } catch (\Exception $e) {
            // Start rollback.
            do {
                $id = \spl_object_hash(\current($this->entries));
                $state = $this->data[$id]['state'];

                // Ignore entries without the committing mark.
                if (0 === (self::MARK_COMMITTING & $state)) {
                    continue;
                }

                switch ($state) {
                    // Managed and committing.
                    case (self::STATE_MANAGED | self::MARK_COMMITTING):
                        $this->data[$id]['state'] = $state ^ self::MARK_COMMITTING;
                        yield new Request\UpdateRequest(
                            $this->data[$id]['changes']['dn'] ?: $this->data[$id]['original']['dn'], 
                            $this->getInverseChangeSet($this->data[$id]['changes'], $this->data[$id]['original']['dn'])
                        );
                        break;
                    // Unmanaged, committing and marked persistance.
                    case (self::MARK_COMMITTING | self::MARK_PERSISTENCE):
                        $this->data[$id]['state'] = $state ^ self::MARK_COMMITTING;
                        yield new Request\DeleteRequest($this->data[$id]['changes']['dn']);
                        break;
                    // Managed, committing and marked removal.
                    case (self::STATE_MANAGED | self::MARK_COMMITTING | self::MARK_REMOVAL):
                        $this->data[$id]['state'] = $state ^ self::MARK_COMMITTING;
                        yield new Request\NewEntryRequest($this->data[$id]['original']['dn'], ['objectclass' => $this->data[$id]['original']['objectclasses']] + $this->data[$id]['original']['attributes']);
                        break;
                    // Undefined behaviour.
                    default:
                        throw new \RuntimeException('Undefined state.');
                }

            } while (false !== \prev($this->entries));

            throw $e;
        }
    }

    private function addEntry(string $id, object $entry): void
    {
        $this->data[$id] = [
            'state' => self::MARK_PERSISTENCE,
            'original' => [],
            'changes' => [],
        ];
        $this->entries[$id] = $entry;
    }

    private function computeChangeSet(array $original, array $current): ?array
    {
        $changeSet = [
            'dn' => $original['dn'] !== $current['dn'] ? $current['dn'] : null,
            'attributes' => [],
        ];

        $hasChanges = null !== $changeSet['dn'];

        // Handle all attributes not present in the current form.
        foreach (\array_diff_key($original['attributes'], $current['attributes']) as $attribute => $values) {
            $changeSet['attributes'][$attribute] = [
                'add' => [],
                'keep' => [],
                'delete' => $values,
            ];
            $hasChanges = true;
        }

        // Handle all attributes not present in the original form.
        foreach (\array_diff_key($current['attributes'], $original['attributes']) as $attribute => $values) {
            $changeSet['attributes'][$attribute] = [
                'add' => $values,
                'keep' => [],
                'delete' => [],
            ];
            $hasChanges = true;
        }

        foreach ($original['attributes'] as $attribute => $oValues) {
            // Skip attributes which cannot be found in the current attributes.
            if (\array_key_exists($attribute, $changeSet['attributes'])) {
                continue;
            }

            $cValues = $current['attributes'][$attribute];

            $changeSet['attributes'][$attribute] = [
                'add' => \array_values(\array_diff($cValues, $oValues)),
                'keep' => \array_intersect($oValues, $cValues),
                'delete' => \array_values(\array_diff($oValues, $cValues)),
            ];

            if (!empty($changeSet['attributes'][$attribute]['add']) or !empty($changeSet['attributes'][$attribute]['delete'])) {
                $hasChanges = true;
            }
        }

        return $hasChanges ? $changeSet : null;
    }

    private function getInverseChangeSet(array $changeSet, string $originalDn)
    {
        $delete = [];
        foreach ($changeSet['attributes'] as $attribute => &$changes) {
            $delete = $changes['delete'];
            $changes['delete'] = $changes['add'];
            $changes['add'] = $delete;
        }

        $changeSet['dn'] = $changeSet['dn'] ? $originalDn : null;

        return $changeSet;
    }
}
