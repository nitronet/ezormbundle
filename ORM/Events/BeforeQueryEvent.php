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
use Nitronet\eZORMBundle\ORM\Query;
use Symfony\Component\EventDispatcher\Event;

class BeforeQueryEvent extends Event
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var mixed
     */
    protected $queryResult = false;

    /**
     * BeforeQueryEvent constructor.
     *
     * @param Connection $connection
     * @param Query $query
     */
    public function __construct(Connection $connection, Query $query)
    {
        $this->connection = $connection;
        $this->query = $query;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return mixed
     */
    public function getQueryResult()
    {
        return $this->queryResult;
    }

    /**
     * @param mixed $queryResult
     */
    public function setQueryResult($queryResult)
    {
        $this->queryResult = $queryResult;
    }
}