<?php

namespace KAGOnlineTeam\LdapBundle\Serializer;

interface SerializerInterface
{
    /**
     * 
     */
    public function denormalize(string $dn, array $attributes): object;

    /**
     * 
     */
    public function normalize(object $object): array;
}