<?php

namespace KAGOnlineTeam\LdapBundle\Serializer;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Attribute\DistinguishedName;
use KAGOnlineTeam\LdapBundle\Attribute\MultiValue;

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
        switch ($this->metadata->getDn()->getType()) {
            case DnMetadata::TYPE_STRING:
                $dnValue = $dn;
                break;
            case DnMetadata::TYPE_OBJECT:
                $dnValue = DistinguishedName::deserialize($dn);
                break;
            default:
                throw new \RuntimeException('Cannot denormalize with invalid property type.');
        }
        $refl->setValue($object, $dnValue);

        foreach ($this->metadata->getProperties() as $property) {
            if (!\array_key_exists($property->getAttribute(), $attributes)) {
                continue;
            }

            $refl = new \ReflectionProperty($this->metadata->getClass(), $property->getProperty());
            if (!$refl->isPublic()) {
                $refl->setAccessible(true);
            }

            switch ($property->getType()) {
                case PropertyMetadata::TYPE_ARRAY:
                    $value = $attributes[$property->getAttribute()];
                    break;
                case PropertyMetadata::TYPE_SCALAR:
                    $values = $attributes[$property->getAttribute()];
                    if (empty($values)) {
                        $value = null;
                    } else {
                        $value = \reset($values);
                    }
                    break;
                case PropertyMetadata::TYPE_MULIVALUE:
                    $value = MultiValue::deserialize($attributes[$property->getAttribute()]);
                    break;
                default:
                    throw new \RuntimeException('Cannot denormalize with invalid property type.');
            }
            $refl->setValue($object, $value);
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

            switch ($property->getType()) {
                case PropertyMetadata::TYPE_ARRAY:
                    $value = $refl->getValue($object);
                    break;
                case PropertyMetadata::TYPE_SCALAR:
                    $value = [$refl->getValue($object)];
                    break;
                case PropertyMetadata::TYPE_MULIVALUE:
                    $value = $value = $refl->getValue($object)->serialize();
                    break;
                default:
                    throw new \RuntimeException('Cannot normalize with invalid property type.');
            }
            $attributes[$property->getAttribute()] = $value;
        }

        return [
            'dn' => $dn,
            'objectclasses' => $this->metadata->getObjectClasses(),
            'attributes' => $attributes,
        ];
    }
}