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

class eZFetchType implements FetchTypeInterface
{
    /**
     * Returns the raw eZ Result (= does nothing)
     *
     * @param SearchResult $searchResult
     * @param string|null $language
     *
     * @return SearchResult
     */
    public function transform(SearchResult $searchResult, $language = null)
    {
        return $searchResult;
    }
}