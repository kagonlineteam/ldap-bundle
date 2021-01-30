<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests;

use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactoryInterface;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Connection\ConnectionInterface;
use KAGOnlineTeam\LdapBundle\Request\RequestInterface;
use KAGOnlineTeam\LdapBundle\Response\ResponseInterface;
use KAGOnlineTeam\LdapBundle\Response\FailureResponse;
use KAGOnlineTeam\LdapBundle\Manager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ManagerTest extends TestCase
{
    public function testForwardingMethods(): void
    {
        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
        $metadataFactory->create(Argument::is('App\\Model\\Employee'))->willReturn($metadata)->shouldBeCalled();

        $request = $this->prophesize(RequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->getBaseDn()->willReturn('ou=employees,ou=users')->shouldBeCalled();
        $connection->execute(Argument::is($request))->willReturn($response)->shouldBeCalled();

        $manager = new Manager($metadataFactory->reveal(), $connection->reveal());
        $this->assertSame('ou=employees,ou=users', $manager->getBaseDn());
        $this->assertSame($metadata, $manager->getMetadata('App\\Model\\Employee'));
        $this->assertSame($response, $manager->query($request));
    }

    public function testUpdate(): void
    {
        $metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
        $connection = $this->prophesize(ConnectionInterface::class);

        TestUpdateGeneratorClass::$requests = [
            ($request0 = $this->prophesize(RequestInterface::class)->reveal()),
            ($request1 = $this->prophesize(RequestInterface::class)->reveal()),
            ($request2 = $this->prophesize(RequestInterface::class)->reveal()),
            ($request3 = $this->prophesize(RequestInterface::class)->reveal()),
            ($request4 = $this->prophesize(RequestInterface::class)->reveal()),
            ($request5 = $this->prophesize(RequestInterface::class)->reveal()),
            ($request6 = $this->prophesize(RequestInterface::class)->reveal()),
        ];

        TestUpdateGeneratorClass::$fallbacks = [
            ($fallback0 = $this->prophesize(RequestInterface::class)->reveal()),
            ($fallback1 = $this->prophesize(RequestInterface::class)->reveal()),
            ($fallback2 = $this->prophesize(RequestInterface::class)->reveal()),
            ($fallback3 = $this->prophesize(RequestInterface::class)->reveal()),
            ($fallback4 = $this->prophesize(RequestInterface::class)->reveal()),
            ($fallback5 = $this->prophesize(RequestInterface::class)->reveal()),
            ($fallback6 = $this->prophesize(RequestInterface::class)->reveal()),
        ];

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $failureResponse = $this->prophesize(FailureResponse::class);
        $failureResponse->getMessage()->willReturn('Error message');
        $connection->execute(Argument::is($request0))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($request1))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($request2))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($request3))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($request4))->willReturn($failureResponse->reveal())->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($fallback3))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($fallback2))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($fallback1))->willReturn($response)->shouldBeCalledTimes(1);
        $connection->execute(Argument::is($fallback0))->willReturn($response)->shouldBeCalledTimes(1);

        $manager = new Manager($metadataFactory->reveal(), $connection->reveal());
        $manager->update(TestUpdateGeneratorClass::getTestGenerator());
    }
}

class TestUpdateGeneratorClass
{
    public static $requests;
    public static $fallbacks;

    public static function getTestGenerator(): \Generator
    {
        $key = null;
        try {
            foreach (static::$requests as $key => $request) {
                yield $request;
            }
        } catch (\Exception $e) {
            while ($key >= 0) {
                yield static::$fallbacks[$key];
                $key--;
            }
        }
    }
}
