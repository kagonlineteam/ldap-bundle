<?php

namespace KAGOnlineTeam\LdapBundle\Serializer;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;

class ReflectionSerializer
{
    private $metadata;

    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function denormalize(string $dn, array $attributes): object
    {
        $object = (new \ReflectionClass($this->metadata->getClass()))
            ->newInstanceWithoutConstructor();

        $refl = new \ReflectionProperty($this->metadata->getClass(), $this->metadata->getDn()->getProperty());
        if (!$refl->isPublic()) {
            $refl->setAccessible(true);
        }
        $refl->setValue($object, $dn);

        foreach ($this->metadata->getProperties() as $property) {
            if (!\array_key_exists($property->getAttribute(), $attributes)) {
                continue;
            }

            $refl = new \ReflectionProperty($this->metadata->getClass(), $property->getProperty());
            if (!$refl->isPublic()) {
                $refl->setAccessible(true);
            }
            $refl->setValue($object, $attributes[$property->getAttribute()]);
        }

        return $object;
    }

    public function normalize(object $object): array
    {
        $refl = new \ReflectionProperty($this->metadata->getClass(), $this->metadata->getDn()->getProperty());
        if (!$refl->isPublic()) {
            $refl->setAccessible(true);
        }
        $dn = (string) $refl->getValue($object);

        foreach ($this->metadata->getProperties() as $property) {
            $refl = new \ReflectionProperty($this->metadata->getClass(), $property->getProperty());
            if (!$refl->isPublic()) {
                $refl->setAccessible(true);
            }

            $val = $refl->getValue($object);
            $attributes[$property->getAttribute()] = \is_array($val) ? $val : [$val];
        }

        return [
            'dn' => $dn,
            'objectclasses' => $this->metadata->getObjectClasses(),
            'attributes' => $attributes,
        ];
    }
}