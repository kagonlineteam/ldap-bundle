<?php

namespace KAGOnlineTeam\LdapBundle;

use KAGOnlineTeam\LdapBundle\Event\LifecycleEvent;

/**
 * Extends the LifecycleEvent to hold the changeset for update events.
 *
 * @author Jan Flaßkamp
 */
class UpdateEvent extends LifecycleEvent
{
    /**
     * Contains the data which has been changed.
     *
     * @var array
     */
    private $changeSet;

    public function __construct(?object $object, array $changeSet = [])
    {
        $this->changeSet = $changeSet;
        parent::__construct($object);
    }

    /**
     * @return array The changeset of the entry object
     */
    public function getChangeSet(): array
    {
        return $this->changeSet;
    }
}
