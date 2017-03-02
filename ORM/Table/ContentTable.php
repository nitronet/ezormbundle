<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Table;


use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\QueryHandler\ContentQueryHandler;
use Nitronet\eZORMBundle\ORM\TableInterface;

class ContentTable implements TableInterface
{
    /**
     * @param Connection $connection
     *
     * @return ContentQueryHandler
     */
    public function getQueryHandler(Connection $connection)
    {
        return new ContentQueryHandler($connection);
    }
}