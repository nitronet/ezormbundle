<?php
namespace Nitronet\eZORMBundle\Tests\ORM;


use Nitronet\eZORMBundle\ORM\Registry\Entry;
use Nitronet\eZORMBundle\ORM\Registry\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new Registry();
    }

    public function testBasicRegistryThings()
    {
        $entity = new \stdClass();
        $entity->test = "test value";
        $entity->second = "second test value";
        $entity->obj = new \stdClass();

        $this->registry->store($entity);

        $entry = $this->registry->getEntry($entity);
        $this->assertTrue($entry instanceof Entry);

        $arr = $entry->toFieldsArray();
        $this->assertTrue(is_array($arr));
        $this->assertCount(3, $arr);
        $this->assertArrayHasKey('test', $arr);
        $this->assertArrayHasKey('second', $arr);
        $this->assertArrayHasKey('obj', $arr);

        $entry->fresh();

        $clone = clone $entity;

        $this->assertFalse($this->registry->getEntry($clone));
        $this->assertFalse($entry->hasChanged());

        $entity->test = "changed";
        $this->assertTrue($entry->hasChanged());

        $entry->fresh();

        $entity->obj = $clone;
        $this->assertTrue($entry->hasChanged());

        $this->assertFalse($entry->matchObject($clone));
        $this->assertTrue($entry->matchObject($entity));
    }
}