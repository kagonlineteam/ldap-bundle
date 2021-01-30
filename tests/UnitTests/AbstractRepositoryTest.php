<?php

namespace KAGOnlineTeam\LdapBundle\Tests\UnitTests;

use KAGOnlineTeam\LdapBundle\AbstractRepository;
use KAGOnlineTeam\LdapBundle\ManagerInterface;
use KAGOnlineTeam\LdapBundle\Worker;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class AbstractRepositoryTest extends TestCase
{
    public function testWorkerCalls(): void
    {
        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $manager = $this->prophesize(ManagerInterface::class);
        $manager->getMetadata('App\\Entity\\Device')->willReturn($metadata);
        $manager->update(Argument::type(\Generator::class))->shouldBeCalledTimes(1);

        $object = (object) ['value' => 'qwertz'];

        $worker = $this->prophesize(Worker::class);
        $worker->mark($object, Worker::MARK_PERSISTENCE)->shouldBeCalledTimes(1);
        $worker->mark($object, Worker::MARK_REMOVAL)->shouldBeCalledTimes(1);
        $worker->createRequests()->will(function () {
            yield 'value';
            yield 'return';
            return;
        })->shouldBeCalledTimes(1);

        $repository = new AbstractRepositoryTestClass($manager->reveal(), $worker->reveal());
        $repository->persist($object);
        $repository->remove($object);
        $repository->commit();
    }
}

class AbstractRepositoryTestClass extends AbstractRepository
{
    public function __construct(ManagerInterface $manager, Worker $worker)
    {
        parent::__construct($manager, 'App\\Entity\\Device', $worker);
    }
}

