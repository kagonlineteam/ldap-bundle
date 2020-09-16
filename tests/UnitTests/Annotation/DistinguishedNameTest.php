<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use KAGOnlineTeam\LdapBundle\Annotation\DistinguishedName;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class DistinguishedNameTest extends TestCase
{
    public function testWithReader()
    {
        $reader = new AnnotationReader();
        $dn = $reader->getPropertyAnnotation(
            new ReflectionProperty(DummyUser::class, 'dn'),
            DistinguishedName::class
        );

        $this->assertNotNull($dn);
    }
}
