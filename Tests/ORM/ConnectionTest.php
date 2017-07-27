<?php
namespace Nitronet\eZORMBundle\Tests\ORM;


use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\Tests\Utils\ContainerFactory;
use Nitronet\eZORMBundle\Tests\Utils\MockFactory;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    public function setUp()
    {
        $factory = new MockFactory();
        $container = ContainerFactory::factory();

        $this->connection = new Connection(
            $factory->repositoryFactory(),
            $container
        );
    }

    public function testConnection()
    {
        $this->assertNotNull($this->connection->table('ez:content'), 'thing is not null');
    }
}