<?php

namespace KAGOnlineTeam\LdapBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Basic object for all events during the lifecycle of an entry.
 * 
 * @author Jan Flaßkamp
 */
class LifecycleEvent extends Event
{
    // Before a new entry is going to be persisted.
    public const PRE_PERSIST = 'ldap.pre_persist';
    // After a new entry has been persisted.
    public const POST_PERSIST = 'ldap.post_persist';
    // Before an entry is going to be updated.
    public const PRE_UPDATE = 'ldap.pre_update';
    // After an entry has been updated.
    public const POST_UPDATE = 'ldap.post_update';
    // Before an entry will be removed.
    public const PRE_REMOVE = 'ldap.pre_remove';
    // After an entry has been removed successfully. 
    public const POST_REMOVE = 'ldap.post_remove';
    // This event will be triggered right after an entry has been loaded.
    public const POST_LOAD = 'ldap.post_load';

    /**
     * The entry object for which the event occured.
     * 
     * @var object $object 
     */
    private $object;

    public function __construct(?object $object)
    {
        $this->object = $object;
    }

    /**
     * @return object|null The associated entry object.
     */
    public function getObject(): ?object
    {
        return $this->object;
    }
}
