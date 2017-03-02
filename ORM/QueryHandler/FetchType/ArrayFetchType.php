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

class ArrayFetchType implements FetchTypeInterface
{
    /**
     * Transforms each Content as a simple PHP Array
     *
     * NOTE: No Field mapping is done so every value will be an API\Value instance.
     *
     * @param SearchResult $searchResult
     * @param string|null $language
     *
     * @return array
     */
    public function transform(SearchResult $searchResult, $language = null)
    {
        $results = array();
        foreach ($searchResult->searchHits as $searchHit) {
            /** @var Content $content */
            $content    = $searchHit->valueObject;
            $fields     = $content->getFields();
            $item       = array();

            foreach ($fields as $field) {
                $item[$field->fieldDefIdentifier] = $content->getFieldValue($field->fieldDefIdentifier);
            }

            $results[] = $item;
        }

        return $results;
    }
}