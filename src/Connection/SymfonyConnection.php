<?php

namespace KAGOnlineTeam\LdapBundle\Connection;

use KAGOnlineTeam\LdapBundle\Request;
use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Response;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\UpdateOperation;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\LdapInterface;

/**
 * An adapter for Symfonys Ldap component.
 */
class SymfonyConnection implements ConnectionInterface
{
    private $ldap;
    private $credentials;
    private $baseDn;
    private $bound = false;

    public function __construct(LdapInterface $ldap, string $credentials, string $baseDn)
    {
        $this->ldap = $ldap;
        $this->resolveBindCredentials($credentials);
        $this->baseDn = $baseDn;
    }

    public function isConnected(): bool
    {
        return true;
    }

    public function connect(): void
    {
    }

    public function disconnect(): void
    {
    }

    public function getBaseDn(): string
    {
        return $this->baseDn;
    }

    public function execute(RequestInterface $request): ResponseInterface
    {
        if (!$this->bound) {
            $this->ldap->bind(...$this->credentials);
            $this->bound = true;
        }

        switch (\get_class($request)) {
            case Request\QueryRequest::class:
                $iterator = $this->ldap->query(
                    $request->getDn(), $request->getFilter(), $request->getOptions()
                )->execute()->getIterator();

                return new Response\EntriesResponse($this->processCollection($iterator), $request->isReadOnly());

            case Request\NewEntryRequest::class:
                $manager = $this->ldap->getEntryManager();
                try {
                    $manager->add(new Entry($request->getDn(), $request->getAttributes()));

                    return new Response\SuccessResponse();
                } catch (\Exception $e) {
                    return new Response\FailureResponse($e->getMessage());
                }

            case Request\UpdateRequest::class:
                $manager = $this->ldap->getEntryManager();
                try {
                    if (null !== $request->getChangeSet()['dn']) {
                        $manager->remove(new Entry($request->getDn()));

                        // Update operation with dn change is split into two requests. The additional try-catch acts as a small rollback.
                        try {
                            $attributes = ['objectClass' => $request->getChangeSet()['objectClass']];
                            foreach ($request->getChangeSet()['attributes'] as $attribute => $changes) {
                                $attributes[$attribute] = array_merge($changes['keep'], $changes['add']);
                            }
                            $manager->add(new Entry($request->getChangeSet()['dn'], $attributes));
                        } catch (\Exception $e) {
                            $attributes = ['objectClass' => $request->getChangeSet()['objectClass']];
                            foreach ($request->getChangeSet()['attributes'] as $attribute => $changes) {
                                $attributes[$attribute] = array_merge($changes['keep'], $changes['delete']);
                            }
                            $manager->add(new Entry($request->getDn(), $attributes));

                            throw $e;
                        }
                    } else {
                        $operations = [];
                        foreach ($request->getChangeSet()['attributes'] as $attribute => $changes) {
                            if (empty($changes['add']) && empty($changes['delete'])) {
                                continue;
                            }

                            $operations[] = new UpdateOperation(\LDAP_MODIFY_BATCH_REPLACE, $attribute, array_merge($changes['keep'], $changes['add']));
                        }
                        $manager->applyOperations($request->getDn(), $operations);
                    }

                    return new Response\SuccessResponse();
                } catch (\Exception $e) {
                    return new Response\FailureResponse($e->getMessage());
                }

            case Request\DeleteRequest::class:
                $manager = $this->ldap->getEntryManager();
                try {
                    $manager->remove(new Entry($request->getDn()));

                    return new Response\SuccessResponse();
                } catch (\Exception $e) {
                    return new Response\FailureResponse($e->getMessage());
                }
        }

        throw new \RuntimeException(sprintf('Undefined request of type "%s" given.', \get_class($request)));
    }

    private function resolveBindCredentials(string $bindCredentials): void
    {
        $i = strpos($bindCredentials, '?');
        $this->credentials = [
            substr($bindCredentials, 0, $i),
            substr($bindCredentials, $i + 1),
        ];
    }

    private function processCollection(\Generator $iterator): \Generator
    {
        $iterator->rewind();

        while ($iterator->valid()) {
            $entry = $iterator->current();
            $objectClass = $entry->getAttribute('objectClass');
            $entry->removeAttribute('objectClass');

            yield [
                $entry->getDn(),
                $objectClass,
                $entry->getAttributes(),
            ];

            $iterator->next();
        }
    }
}
