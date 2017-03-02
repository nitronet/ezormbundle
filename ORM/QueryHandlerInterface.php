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


interface QueryHandlerInterface
{
    /**
     * Defines the Connection to be used with this Query
     *
     * @param Connection $connection the Connection to use
     *
     * @return QueryHandlerInterface
     */
    public function setConnection(Connection $connection);

    /**
     * Transforms (and execute) an ORM Query into an eZ Repository Query and return results
     *
     * @param Query  $query
     * @param string|array Query fetch type (depends on QueryHandler)
     * @param string|null|array $language Language(s) to be used (null = eZ's default)
     *
     * @return mixed
     */
    public function handle(Query $query, $fetchType = null, $language = null);

    /**
     * Determines if Query Fetch type is supported by the QueryHandler
     *
     * @param string $fetchType
     *
     * @return boolean
     */
    public function supports($fetchType);

    /**
     * Returns the default Fetch type name for this Query Handler
     *
     * @return string
     */
    public function getDefaultFetchType();
}