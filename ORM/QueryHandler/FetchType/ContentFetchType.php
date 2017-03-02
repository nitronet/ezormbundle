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


use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Nitronet\eZORMBundle\ORM\QueryHandler\FetchTypeInterface;

class ContentFetchType implements FetchTypeInterface
{
    /**
     * Extract only Content's from an eZ Search Result
     *
     * @param SearchResult $searchResult
     * @param string|null $language
     *
     * @return Content[]
     */
    public function transform(SearchResult $searchResult, $language = null)
    {
        $results = array();
        foreach ($searchResult->searchHits as $searchHit) {
            $results[] = $searchHit->valueObject;
        }

        return $results;
    }
}