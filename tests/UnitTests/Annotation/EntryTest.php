<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use KAGOnlineTeam\LdapBundle\Annotation\Entry;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUserRepository;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class EntryTest extends TestCase
{
    public function testWithReader()
    {
        $reader = new AnnotationReader();
        $entry = $reader->getClassAnnotation(
            new ReflectionClass(DummyUser::class),
            Entry::class
        );

        $this->assertSame(DummyUserRepository::class, $entry->repositoryClass);
        $this->assertSame(['inetOrgPerson', 'person', 'top'], $entry->objectClasses);
    }
}
