<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM;

interface TableInterface
{
    /**
     * Returns the Table's Query Handler
     *
     * @param Connection $connection the Connection to use
     *
     * @return QueryHandlerInterface
     */
    public function getQueryHandler(Connection $connection);
}