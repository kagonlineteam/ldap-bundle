<?php

namespace KAGOnlineTeam\LdapBundle\Metadata\Extractor;

use Doctrine\Common\Annotations\Reader;
use KAGOnlineTeam\LdapBundle\Annotation;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;

/**
 * Extracts metadata from annotations.
 *
 * @author Jan Flaßkamp
 */
class AnnotationExtractor implements ExtractorInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFor(ClassMetadata $metadata): void
    {
        $reflection = new \ReflectionClass($metadata->getClass());

        foreach ($this->reader->getClassAnnotations($reflection) as $annotation) {
            if ($annotation instanceof Annotation\Entry) {
                $metadata->setRepositoryClass($annotation->repositoryClass);
                $metadata->setObjectClasses($annotation->objectClasses);
            }
        }

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if ($property->getDeclaringClass()->name === $metadata->getClass()) {
                foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                    if ($annotation instanceof Annotation\DistinguishedName) {
                        $metadata->setDn(new DnMetadata($property->getName(), $annotation->type));
                    } elseif ($annotation instanceof Annotation\Attribute) {
                        $propertyMetadata = new PropertyMetadata($property->getName(), $annotation->description, $annotation->type);

                        $properties[] = $propertyMetadata;
                    }
                }
            }
        }

        $metadata->setProperties($properties);
    }
}
