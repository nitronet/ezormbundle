<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\QueryHandler;


use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

interface FetchTypeInterface
{
    /**
     * Transforms (and execute) an ORM Query into an eZ Repository Query and return results
     *
     * @param SearchResult $searchResult
     * @param string $language
     *
     * @return mixed
     */
    public function transform(SearchResult $searchResult, $language = null);
}