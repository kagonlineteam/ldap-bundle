<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests\Attribute;

use KAGOnlineTeam\LdapBundle\Attribute\DistinguishedName;
use PHPUnit\Framework\TestCase;

class DistinguishedNameTest extends TestCase
{
    /**
     * @dataProvider provideDeserialize
     */
    public function testDeserialize(string $dn, array $rdns)
    {
        $result = (DistinguishedName::deserialize($dn))->all();

        $this->assertEquals($rdns, $result);
    }

    /**
     * @dataProvider provideSerialize
     */
    public function testSerialize(array $rdns, string $dn)
    {
        $result = (new DistinguishedName($rdns))
            ->serialize();

        $this->assertEquals($dn, $result);
    }

    public function testRemoveRdn()
    {
        $parent = (DistinguishedName::deserialize(''))
            ->removeRdn();
        $this->assertEquals((string) $parent, '');

        $parent = (DistinguishedName::deserialize('ou=users,ou=system'))
            ->removeRdn();
        $this->assertEquals((string) $parent, 'ou=system');
    }

    /**
     * @dataProvider provideAddRdnException
     */
    public function testAddRdnException(string $dn, array $pairs)
    {
        $this->expectException(\InvalidArgumentException::class);

        $dn = (DistinguishedName::deserialize($dn))
            ->addRdn(...$pairs);
    }

    /**
     * @dataProvider provideAddRdn
     */
    public function testAddRdn(string $dn, array $pairs, string $result)
    {
        $dnO = (DistinguishedName::deserialize($dn))
            ->addRdn(...$pairs);

        $this->assertEquals((string) $dnO, $result);
    }

    public function provideDeserialize()
    {
        return [
            ['',
                []
            ],
            ['ou=users,ou=system', 
                [
                    [['ou', 'users']], 
                    [['ou', 'system']]
                ] 
            ],
            ['cn=Joachim\\20Mueller+telephoneNumber=023441233,ou=users,ou=system', 
                [
                    [['cn', 'Joachim Mueller'], ['telephoneNumber', '023441233']], 
                    [['ou', 'users']], 
                    [['ou', 'system']]
                ]
            ],
        ];
    }

    public function provideSerialize()
    {
        return [
            [ 
                [
                    [['ou', 'users']], 
                    [['ou', 'system']]
                ] 
            , 'ou=users,ou=system'],
            [ 
                [
                    [['cn', 'Joachim Mueller'], ['telephoneNumber', '023441233']], 
                    [['ou', 'users']], 
                    [['ou', 'system']]
                ]
            , 'cn=Joachim Mueller+telephoneNumber=023441233,ou=users,ou=system'],
        ];
    }

    public function provideAddRdnException()
    {
        return [
            [
                'ou=users,ou=system', []
            ],
            [
                'ou=users,ou=system', ['abc']
            ],
            [
                'ou=users,ou=system', [['rrrrr']]
            ],
            [
                'ou=users,ou=system', [['', [1, 2, 3]], []]
            ],
        ];
    }

    public function provideAddRdn()
    {
        return [
            [
                'ou=users,ou=system', [['uid', 'FMax']], 'uid=FMax,ou=users,ou=system'
            ],
            [
                'ou=users,ou=system', [['uid', 'FMax'], ['telephoneNumber', '0245987622']], 'uid=FMax+telephoneNumber=0245987622,ou=users,ou=system'
            ],
        ];
    }
}