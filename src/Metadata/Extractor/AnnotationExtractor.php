<?php

namespace KAGOnlineTeam\LdapBundle\Metadata\Extractor;

use Doctrine\Common\Annotations\Reader;
use KAGOnlineTeam\LdapBundle\Metadata\Extractor\ExtractorInterface;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Annotation;

/**
 * 
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
     * {@inheritDoc}
     */
    public function extractFor(ClassMetadataInterface $metadata): void
    {
        $reflClass = $metadata->getReflectionClass();
        $class = $reflClass->getName();

        foreach ($this->reader->getClassAnnotations($reflClass) as $annotation) {
            if ($annotation instanceof Annotation\Entry) {
                $metadata->setRepositoryClass($annotation->repositoryClass)
                    ->setObjectClasses($annotation->objectClasses);
            }
        }

        foreach ($reflClass->getProperties() as $property) {
            if ($property->getDeclaringClass()->name === $class) {
                foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                    if ($annotation instanceof Annotation\DistinguishedName) {
                        $metadata->setDnProperty($property);
                    }
                    
                    if ($annotation instanceof Annotation\Attribute) {
                        $propertyMetadata = new PropertyMetadata(
                            $property, $annotation->description
                        );
                        $metadata->addProperty($propertyMetadata);
                    }
                }
            }
        }
    }
}