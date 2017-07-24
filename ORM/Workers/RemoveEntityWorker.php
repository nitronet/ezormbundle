<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Workers;


use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\WorkerInterface;

class RemoveEntityWorker implements WorkerInterface
{
    /**
     * @param Connection $connection
     * @return mixed
     */
    public function execute(Connection $connection)
    {
    }
}