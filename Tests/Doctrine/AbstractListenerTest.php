<?php

namespace FOS\ElasticaBundle\Tests\Doctrine;

/**
 * See concrete MongoDB/ORM instances of this abstract test
 *
 * @author Richard Miller <info@limethinking.co.uk>
 */
abstract class ListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectInsertedOnPersist()
    {
        $persister = $this->getMockPersister();

        $entity = new Listener\Entity(1);
        $eventArgs = $this->createLifecycleEventArgs($entity, $this->getMockObjectManager());

        $listener = $this->createListener($persister, get_class($entity), array());
        $listener->postPersist($eventArgs);

        $this->assertEquals($entity, current($listener->scheduledForInsertion));

        $persister->expects($this->once())
            ->method('insertMany')
            ->with($listener->scheduledForInsertion);

        $listener->postFlush($eventArgs);
    }

    /**
     * @dataProvider provideIsIndexableCallbacks
     */
    public function testNonIndexableObjectNotInsertedOnPersist($isIndexableCallback)
    {
        $persister = $this->getMockPersister();

        $entity = new Listener\Entity(1, false);
        $eventArgs = $this->createLifecycleEventArgs($entity, $this->getMockObjectManager());

        $listener = $this->createListener($persister, get_class($entity), array());
        $listener->setIsIndexableCallback($isIndexableCallback);
        $listener->postPersist($eventArgs);

        $this->assertEmpty($listener->scheduledForInsertion);

        $persister->expects($this->never())
            ->method('insertOne');
        $persister->expects($this->never())
            ->method('insertMany');

        $listener->postFlush($eventArgs);
    }

    public function testObjectReplacedOnUpdate()
    {
        $persister = $this->getMockPersister();

        $entity = new Listener\Entity(1);
        $eventArgs = $this->createLifecycleEventArgs($entity, $this->getMockObjectManager());

        $listener = $this->createListener($persister, get_class($entity), array());
        $listener->postUpdate($eventArgs);

        $this->assertEquals($entity, current($listener->scheduledForUpdate));

        $persister->expects($this->once())
            ->method('replaceMany')
            ->with(array($entity));
        $persister->expects($this->never())
            ->method('deleteById');

        $listener->postFlush($eventArgs);
    }

    /**
     * @dataProvider provideIsIndexableCallbacks
     */
    public function testNonIndexableObjectRemovedOnUpdate($isIndexableCallback)
    {
        $classMetadata = $this->getMockClassMetadata();
        $objectManager = $this->getMockObjectManager();
        $persister = $this->getMockPersister();

        $entity = new Listener\Entity(1, false);
        $eventArgs = $this->createLifecycleEventArgs($entity, $objectManager);

        $objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetadata));

        $classMetadata->expects($this->any())
            ->method('getFieldValue')
            ->with($entity, 'id')
            ->will($this->returnValue($entity->getId()));

        $listener = $this->createListener($persister, get_class($entity), array());
        $listener->setIsIndexableCallback($isIndexableCallback);
        $listener->postUpdate($eventArgs);

        $this->assertEmpty($listener->scheduledForUpdate);
        $this->assertEquals($entity->getId(), current($listener->scheduledForDeletion));

        $persister->expects($this->never())
            ->method('replaceOne');
        $persister->expects($this->once())
            ->method('deleteManyByIdentifiers')
            ->with(array($entity->getId()));

        $listener->postFlush($eventArgs);
    }

    public function testObjectDeletedOnRemove()
    {
        $classMetadata = $this->getMockClassMetadata();
        $objectManager = $this->getMockObjectManager();
        $persister = $this->getMockPersister();

        $entity = new Listener\Entity(1);
        $eventArgs = $this->createLifecycleEventArgs($entity, $objectManager);

        $objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetadata));

        $classMetadata->expects($this->any())
            ->method('getFieldValue')
            ->with($entity, 'id')
            ->will($this->returnValue($entity->getId()));

        $listener = $this->createListener($persister, get_class($entity), array());
        $listener->preRemove($eventArgs);

        $this->assertEquals($entity->getId(), current($listener->scheduledForDeletion));

        $persister->expects($this->once())
            ->method('deleteManyByIdentifiers')
            ->with(array($entity->getId()));

        $listener->postFlush($eventArgs);
    }

    public function testObjectWithNonStandardIdentifierDeletedOnRemove()
    {
        $classMetadata = $this->getMockClassMetadata();
        $objectManager = $this->getMockObjectManager();
        $persister = $this->getMockPersister();

        $entity = new Listener\Entity(1);
        $entity->identifier = 'foo';
        $eventArgs = $this->createLifecycleEventArgs($entity, $objectManager);

        $objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetadata));

        $classMetadata->expects($this->any())
            ->method('getFieldValue')
            ->with($entity, 'identifier')
            ->will($this->returnValue($entity->getId()));

        $listener = $this->createListener($persister, get_class($entity), array(), 'identifier');
        $listener->preRemove($eventArgs);

        $this->assertEquals($entity->identifier, current($listener->scheduledForDeletion));

        $persister->expects($this->once())
            ->method('deleteManyByIdentifiers')
            ->with(array($entity->identifier));

        $listener->postFlush($eventArgs);
    }

    /**
     * @dataProvider provideInvalidIsIndexableCallbacks
     * @expectedException \RuntimeException
     */
    public function testInvalidIsIndexableCallbacks($isIndexableCallback)
    {
        $listener = $this->createListener($this->getMockPersister(), 'FOS\ElasticaBundle\Tests\Doctrine\Listener\Entity', array());
        $listener->setIsIndexableCallback($isIndexableCallback);
    }

    public function provideInvalidIsIndexableCallbacks()
    {
        return array(
            array('nonexistentEntityMethod'),
            array(array(new Listener\IndexableDecider(), 'internalMethod')),
            array(42),
            array('entity.getIsIndexable() && nonexistentEntityFunction()'),
        );
    }

    public function provideIsIndexableCallbacks()
    {
        return array(
            array('getIsIndexable'),
            array(array(new Listener\IndexableDecider(), 'isIndexable')),
            array(function(Listener\Entity $entity) { return $entity->getIsIndexable(); }),
            array('entity.getIsIndexable()')
        );
    }

    abstract protected function getLifecycleEventArgsClass();

    abstract protected function getListenerClass();

    abstract protected function getObjectManagerClass();

    abstract protected function getClassMetadataClass();

    private function createLifecycleEventArgs()
    {
        $refl = new \ReflectionClass($this->getLifecycleEventArgsClass());

        return $refl->newInstanceArgs(func_get_args());
    }

    private function createListener()
    {
        $refl = new \ReflectionClass($this->getListenerClass());

        return $refl->newInstanceArgs(func_get_args());
    }

    private function getMockClassMetadata()
    {
        return $this->getMockBuilder($this->getClassMetadataClass())
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMockObjectManager()
    {
        return $this->getMockBuilder($this->getObjectManagerClass())
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMockPersister()
    {
        return $this->getMock('FOS\ElasticaBundle\Persister\ObjectPersisterInterface');
    }
}

namespace FOS\ElasticaBundle\Tests\Doctrine\Listener;

class Entity
{
    private $id;
    private $isIndexable;

    public function __construct($id, $isIndexable = true)
    {
        $this->id = $id;
        $this->isIndexable = $isIndexable;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIsIndexable()
    {
        return $this->isIndexable;
    }
}

class IndexableDecider
{
    public function isIndexable(Entity $entity)
    {
        return $entity->getIsIndexable();
    }

    protected function internalMethod()
    {
    }
}
