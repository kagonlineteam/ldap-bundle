<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use KAGOnlineTeam\LdapBundle\Annotation\Attribute;
use KAGOnlineTeam\LdapBundle\Tests\Fixtures\DummyUser;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class AttributeTest extends TestCase
{
    public function testWithReader()
    {
        $reader = new AnnotationReader();
        $attribute = $reader->getPropertyAnnotation(
            new ReflectionProperty(DummyUser::class, 'username'),
            Attribute::class
        );

        $this->assertSame('uid', $attribute->description);
    }
}
