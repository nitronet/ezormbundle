<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\QueryHandler;


use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\QueryHandlerInterface;
use eZ\Publish\API\Repository\Values\Content\Query as eZContentQuery;

class ContentQueryHandler extends AbstractQueryHandler implements QueryHandlerInterface
{
    /**
     * Handles the Query and returns raw results
     *
     * @param Query $query      The Query object
     * @param string|array $fetchType The Query fetch type
     * @param string|null $language Language to be used (null = eZ's default)
     *
     * @return mixed
     * @throws \Exception when query type is not implemented yet
     */
    public function handle(Query $query, $fetchType = Query::FETCH_ORM, $language = null)
    {
        if ($query->isType(Query::QUERY_TYPE_SELECT)) {
            $searchResult   = $this->handleSelect($query, $language);

            if (is_array($fetchType)) {
                $results = array();
                foreach ($fetchType as $fetchTypeName) {
                    $results[$fetchTypeName] = $this
                        ->fetchTypeTransformerFactory($fetchTypeName)
                        ->transform($searchResult, $language);
                }

                return $results;
            }

            return $this->fetchTypeTransformerFactory($fetchType)->transform($searchResult, $language);
        }

        throw new \Exception('This query type is not implemented yet');
    }

    /**
     * Handles a SELECT Query
     *
     * @param Query $query
     * @param string|null|array $language
     *
     * @return SearchResult
     */
    protected function handleSelect(Query $query, $language = null)
    {
        $ezQuery = new eZContentQuery();
        if ($query->hasLimit()) {
            $ezQuery->limit     = $this->findLimitFromString($query->getLimit());
            $ezQuery->offset    = $this->findOffsetFromString($query->getLimit());
        }

        if ($query->hasText()) {
            $ezQuery->query     = new eZContentQuery\Criterion\FullText($query->getText());
        }

        $ctypefilter = $this->fromQueryToContentTypeIdentifier($query);
        if ($ctypefilter instanceof eZContentQuery\CriterionInterface) {
            $query->where($ctypefilter);
        }

        $ezQuery->filter        = $query->getWhere();
        $ezQuery->sortClauses   = $query->getSortClauses();

        if (null !== $language && is_string($language))  {
            $language = array($language);
        } elseif (null === $language) {
            $language = array();
        }

        return $this->getConnection()
            ->getRepository()
            ->getSearchService()
            ->findContent($ezQuery, $language);
    }

    /**
     * {@see AbstractQueryHandler::supports()}
     *
     * @param string $fetchType
     *
     * @return bool
     */
    public function supports($fetchType)
    {
        return in_array($fetchType, array(
            Query::FETCH_ORM,
            Query::FETCH_EZ,
            Query::FETCH_ARRAY,
            Query::FETCH_CLASS,
            Query::FETCH_CONTENT,
            Query::FETCH_COUNT,
            Query::FETCH_CONTENT_ID,
            Query::FETCH_LOCATION_ID
        ));
    }

    /**
     * @return string
     */
    public function getDefaultFetchType()
    {
        return Query::FETCH_ORM;
    }
}