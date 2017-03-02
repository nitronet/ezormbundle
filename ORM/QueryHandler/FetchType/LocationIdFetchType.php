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
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Nitronet\eZORMBundle\ORM\QueryHandler\FetchTypeInterface;

class LocationIdFetchType implements FetchTypeInterface
{
    /**
     * Extract only Location IDs from an eZ Search Result
     *
     * @param SearchResult $searchResult
     * @param string|null $language
     *
     * @return int[]
     */
    public function transform(SearchResult $searchResult, $language = null)
    {
        $results = array();
        foreach ($searchResult->searchHits as $searchHit) {
            if ($searchHit->valueObject instanceof Content) {
                $results[] = $searchHit->valueObject->contentInfo->mainLocationId;
            } elseif ($searchHit->valueObject instanceof Location) {
                $results[] = $searchHit->valueObject->id;
            }
        }

        return $results;
    }
}