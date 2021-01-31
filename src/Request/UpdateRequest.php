<?php

namespace KAGOnlineTeam\LdapBundle\Request;

class UpdateRequest implements RequestInterface
{
    private $dn;

    /**
     * Format:
     * [
     *     'dn' => (string) The modified dn or null
     *     'objectClass' => (array) The list of objectClass values
     *     'attributes' => [
     *         '<attribute> => [
     *             'add' => (array) New attribute values
     *             'keep' => (array) Attribute values which should be kept
     *             'delete' => (array) Attribute values which should be deleted
     *         ]
     *         '<other-attribute> => ...
     *     ],
     * ].
     *
     * @var array
     */
    private $changeSet;

    public function __construct(string $dn, array $changeSet)
    {
        $this->dn = $dn;
        $this->changeSet = $changeSet;
    }

    public function getDn(): string
    {
        return $this->dn;
    }

    public function getChangeSet(): array
    {
        return $this->changeSet;
    }
}
