<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata\Extractor;

use Doctrine\Common\Annotations\AnnotationReader;
use KAGOnlineTeam\LdapBundle\Annotation;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\DnMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\Extractor\AnnotationExtractor;
use KAGOnlineTeam\LdapBundle\Metadata\PropertyMetadata;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class AnnotationExtractorTest extends TestCase
{
    public function testAnnotationExtractor()
    {
        $entryAnnotation = $this->prophesize(Annotation\Entry::class);
        $entryAnnotation->repositoryClass = 'RepositoryClass';
        $entryAnnotation->objectClasses = ['top', 'device'];
        $dnAnnotation = $this->prophesize(Annotation\DistinguishedName::class);
        $dnAnnotation->type = 'string';
        $attributeAnnotation = $this->prophesize(Annotation\Attribute::class);
        $attributeAnnotation->description = 'uid';
        $attributeAnnotation->type = 'scalar';

        $reader = $this->prophesize(AnnotationReader::class);
        $reader->getClassAnnotations(Argument::which('getName', DummyUser::class))->willReturn([$entryAnnotation->reveal()])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which('getName', 'dn'))->willReturn([$dnAnnotation->reveal()])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which('getName', 'username'))->willReturn([$attributeAnnotation->reveal()])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which('getName', 'name'))->willReturn([])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which('getName', 'other'))->willReturn([])->shouldBeCalled();

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getClass()->willReturn(DummyUser::class);
        $metadata->setRepositoryClass('RepositoryClass')->shouldBeCalled();
        $metadata->setObjectClasses(['top', 'device'])->shouldBeCalled();
        $metadata->setDn(new DnMetadata('dn', 'string'))->shouldBeCalled();

        $propertyMetadata = new PropertyMetadata('username', 'uid', 'scalar');
        $metadata->setProperties(Argument::exact([$propertyMetadata]))->shouldBeCalled();

        $extractor = new AnnotationExtractor($reader->reveal());
        $extractor->extractFor($metadata->reveal());
    }
}
