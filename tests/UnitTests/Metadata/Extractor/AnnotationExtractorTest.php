<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Metadata\Extractor;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Doctrine\Common\Annotations\AnnotationReader;
use KAGOnlineTeam\LdapBundle\Metadata\Extractor\AnnotationExtractor;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Annotation;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use ReflectionClass;
use ReflectionProperty;

class AnnotationExtractorTest extends TestCase
{
    public function testAnnotationExtractor()
    {
        $entryAnnotation = $this->prophesize(Annotation\Entry::class);
        $entryAnnotation->repositoryClass = "RepositoryClass"; 
        $entryAnnotation->objectClasses = ["top", "device"];
        $dnAnnotation = $this->prophesize(Annotation\DistinguishedName::class);
        $attributeAnnotation = $this->prophesize(Annotation\Attribute::class);
        $attributeAnnotation->description = "uid";

        $reader = $this->prophesize(AnnotationReader::class);
        $reader->getClassAnnotations(Argument::which("getName", DummyUser::class))->willReturn([$entryAnnotation->reveal()])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which("getName", "dn"))->willReturn([$dnAnnotation->reveal()])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which("getName", "username"))->willReturn([$attributeAnnotation->reveal()])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which("getName", "name"))->willReturn([])->shouldBeCalled();
        $reader->getPropertyAnnotations(Argument::which("getName", "other"))->willReturn([])->shouldBeCalled();

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()->willReturn(new ReflectionClass(DummyUser::class))->shouldBeCalled();
        $metadata->setRepositoryClass("RepositoryClass")->will(function() {return $this;})->shouldBeCalled();
        $metadata->setObjectClasses(["top", "device"])->will(function() {return $this;})->shouldBeCalled();
        $metadata->setDnProperty(new ReflectionProperty(DummyUser::class, "dn"))->shouldBeCalled();
        $metadata->addProperty(Argument::which("getAttribute", "uid"))->shouldBeCalled();

        $extractor = new AnnotationExtractor($reader->reveal());
        $extractor->extractFor($metadata->reveal());
    }
}