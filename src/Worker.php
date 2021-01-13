<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Response;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;
use KAGOnlineTeam\LdapBundle\Serializer\ReflectionSerializer;
use KAGOnlineTeam\LdapBundle\Serializer\SerializerInterface;

/**
 * 
 */
class Worker
{
    const STATE_UNMANAGED = 0;
    const STATE_LOADED = 1;
    const STATE_READ_ONLY = 2;
    const STATE_MANAGED = 3;
    const STATE_COMMITTING = 4;
    const STATE_COMMITTED = 5;

    const MARK_NULL = 0;
    const MARK_PERSISTENCE = 1;
    const MARK_REMOVAL = 2;

    /**
     * @var ClassMetadata
     */
    private $metadata;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var object[]
     */
    private $entries = [];

    /**
     * @var array
     */
    private $data = [];

    public function __construct(ClassMetadata $metadata, SerializerInterface $serializer = null)
    {
        $this->metadata = $metadata;
        $this->serializer = null === $serializer ? new ReflectionSerializer($metadata) : $serializer;
    }

    /**
     * Marks 
     */
    public function mark(object $entry, int $mark): void
    {
        if (!\in_array($mark, [self::MARK_PERSISTENCE, self::MARK_REMOVAL])) {
            throw new \InvalidArgumentException('Invalid mark given.');
        }

        $id = \spl_object_hash($entry);

        if (self::MARK_PERSISTENCE === $mark and !\array_key_exists($id, $this->data)) {
            $this->entries[$id] = $entry;
            $this->data[$id] = $this->fetchNewData($entry);
        } elseif (self::MARK_REMOVAL === $mark and self::STATE_MANAGED === $this->data[$id]['state']) {
            $this->data[$id]['mark'] = self::MARK_REMOVAL;
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

        do {
            $id = \spl_object_hash(\current($this->entries));
            $data = $this->serializer->normalize(\current($this->entries));

            $state = $this->data[$id]['state'];
            $mark = $this->data[$id]['mark'];

            switch ($mark) {
                case self::MARK_NULL:
                    if (self::STATE_MANAGED === $state) {
                        $changeSet = $this->computeChangeSet($this->data[$id]['original'], $data);
                        yield new Request\UpdateRequest($this->data[$id]['original']['dn'], $changeSet);
                    } 
                    break;

                case self::MARK_PERSISTENCE:
                    if (self::STATE_UNMANAGED === $state) {
                        yield new Request\NewEntryRequest($data['dn'], $data['attributes']);
                    }
                    break;

                case self::MARK_REMOVAL:
                    if (self::STATE_MANAGED === $state) {
                        yield new Request\DeleteRequest($data['dn']);
                    }
                    break;

                default:
                    throw new \RuntimeException('Undefined mark.');
            }

        } while (false !== \next($this->entries));
    }

    /**
     * 
     */
    public function update(ResponseInterface $response): void
    {
        switch (\get_class($response)) {
            case Response\EntriesResponse::class:
                $this->createObjects($response->getEntries());
            break;   
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
            if (self::STATE_LOADED !== $this->data[$id]['state']) {
                break;
            }

            yield $entry;
            $this->data[$id]['state'] = self::STATE_MANAGED;
            
        } while (false !== \prev($this->entries));
    }

    private function fetchNewData(object $entry): array
    {
        return [
            'state' => self::STATE_UNMANAGED,
            'mark' => self::MARK_PERSISTENCE,
            'original' => null,
        ];
    }

    private function fetchLoadData(object $entry): array
    {
        return [
            'state' => self::STATE_LOADED,
            'mark' => self::MARK_NULL,
            'original' => $this->serializer->normalize($entry),
        ];
    }

    private function createObjects(iterable $entries): void
    {
        foreach ($entries as [$dn, $objectClasses, $attributes]) {
            // Check if all objectClasses are present.
            if (!empty(\array_diff($this->metadata->getObjectClasses(), $objectClasses))) {
                continue;
            }

            $entry = $this->serializer->denormalize($dn, $attributes);
            $id = \spl_object_hash($entry);

            $this->entries[$id] = $entry;
            $this->data[$id] = $this->fetchLoadData($entry);
        }
    }

    /**
     * 
     */
    private function computeChangeSet(array $original, array $current): array
    {
        $changeSet = [
            'dn' => $original['dn'] !== $current['dn'] ? $current['dn'] : '',
            'attributes' => [],
        ];

        // Handle all attributes not present in the current form.
        foreach (\array_diff_key($original['attributes'], $current['attributes']) as $attribute => $values) {
            $changeSet['attributes'][$attribute] = [
                'add' => [],
                'keep' => [],
                'delete' => $values,
            ];
        }

        // Handle all attributes not present in the original form.
        foreach (\array_diff_key($current['attributes'], $original['attributes']) as $attribute => $values) {
            $changeSet['attributes'][$attribute] = [
                'add' => $values,
                'keep' => [],
                'delete' => [],
            ];
        }

        foreach ($original['attributes'] as $attribute => $oValues) {
            // Skip attributes which cannot be found in the current attributes.
            if (\array_key_exists($attribute, $changeSet['attributes'])) {
                continue;
            }

            $cValues = $current['attributes'][$attribute];

            $changeSet['attributes'][$attribute] = [
                'add' => \array_diff($cValues, $oValues),
                'keep' => \array_intersect($oValues, $cValues),
                'delete' => \array_diff($oValues, $cValues),
            ];

        }

        return $changeSet;
    }
}
