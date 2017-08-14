<?php
namespace Nitronet\eZORMBundle\Tests\ORM;



use Nitronet\eZORMBundle\eZORMBundle;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\Tests\TestCase;

class CRUDTest extends TestCase
{
    public function setUp() {
        self::bootKernel();
    }

    public function testConnection()
    {
        $kernel = static::$kernel;

        $connection = $kernel->getContainer()->get('ezorm.connection');
        $this->assertInstanceOf(Connection::class, $connection);

        $query = new Query();
        $query->select()
            ->from(eZORMBundle::CONTENTTYPE_TABLE_ALIAS);

        $ctypes = $connection->execute($query);
        $this->assertTrue(count($ctypes) > 0);
    }
}