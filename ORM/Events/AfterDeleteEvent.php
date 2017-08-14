<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Events;



use Nitronet\eZORMBundle\ORM\Connection;
use Symfony\Component\EventDispatcher\Event;

class AfterDeleteEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var object
     */
    protected $entity;

    /**
     * AfterDeleteEvent constructor.
     *
     * @param Connection $connection
     * @param object $entity
     */
    public function __construct(Connection $connection, $entity)
    {
        $this->connection = $connection;
        $this->entity =& $entity;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}