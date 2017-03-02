<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\QueryHandler\FetchType;


use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Nitronet\eZORMBundle\ORM\QueryHandler\FetchTypeInterface;

class CountFetchType implements FetchTypeInterface
{
    /**
     * Extract totalCount value from eZ Search Result
     *
     * @param SearchResult $searchResult
     * @param string|null $language
     *
     * @return int
     */
    public function transform(SearchResult $searchResult, $language = null)
    {
        return $searchResult->totalCount;
    }
}