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


use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Nitronet\eZORMBundle\ORM\Exception\QueryException;
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
        } elseif ($query->isType(Query::QUERY_TYPE_INSERT)) {
            return $this->handleInsert($query, $language);
        } elseif ($query->isType(Query::QUERY_TYPE_UPDATE)) {
            return $this->handleUpdate($query, $language);
        } elseif ($query->isType(Query::QUERY_TYPE_DELETE)) {
            return $this->handleDelete($query, $language);
        }

        throw new \Exception('Query type is not implemented yet');
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
     * @param Query $query
     * @param null|string $language
     *
     * @return bool|Content
     * @throws QueryException
     */
    public function handleInsert(Query $query, $language = null)
    {
        $contentService = $this->getConnection()->getRepository()->getContentService();
        $contentType = $this->fromQueryToContentType($query);
        $struct = $contentService->newContentCreateStruct($contentType, $language);
        if (!empty($query->getRemoteId())) {
            $struct->remoteId = $query->getRemoteId();
        }

        if (null !== $language) {
            $struct->mainLanguageCode = $language;
        }

        $values = $query->getValues();
        foreach ($values as $attribute => $value) {
            $struct->setField($attribute, $value);
        }

        $locationService = $this->getConnection()->getRepository()->getLocationService();
        $locations = $query->getLocations();
        $locationsStructs = array();

        if (!count($locations)) {
            throw QueryException::queryNeedsLocationsExceptionFactory('INSERT');
        }

        foreach ($locations as $id => $location) {
            if (is_int($location) || is_numeric($location)) {
                $locStruct = $locationService->newLocationCreateStruct($location);
            } elseif (is_string($location)) {
                $locStruct = $locationService->newLocationCreateStruct(
                    $locationService->loadLocationByRemoteId($location)
                );
            } elseif ($location instanceof Location) {
                $locStruct = $locationService->newLocationCreateStruct($location->id);
            } else {
                throw QueryException::invalidLocationIdentifierExceptionFactory($location);
            }

            $locStruct->hidden = $query->isHidden();
            if (is_string($id) && !empty($id)) {
                $locStruct->remoteId = $id;
            }

            $locationsStructs[] = $locStruct;
        }

        $draft = $contentService->createContent($struct, $locationsStructs);
        if ($query->isDraft()) {
            return $draft;
        }

        return $contentService->publishVersion($draft->versionInfo);
    }


    /**
     * @param Query $query
     * @param null|string $language
     *
     * @return bool|Content
     * @throws QueryException
     */
    public function handleUpdate(Query $query, $language = null)
    {
        $contentService = $this->getConnection()->getRepository()->getContentService();

        $table = $query->getTable();
        if ($table instanceof ContentInfo) {
            $contentInfo = $table;
        } elseif ($table instanceof Content) {
            $contentInfo = $table->contentInfo;
        } else {
            // TODO: select -> update ?
            throw QueryException::invalidUpdateTargetExceptionFactory($table);
        }

        $struct = $contentService->newContentUpdateStruct();
        $struct->initialLanguageCode = $language;
        $values = $query->getValues();
        foreach ($values as $attribute => $value) {
            $struct->setField($attribute, $value);
        }

        $draft = $contentService->createContentDraft($contentInfo);
        $draft = $contentService->updateContent($draft->versionInfo, $struct);

        if ($query->isDraft()) {
            return $draft;
        }

        return $contentService->publishVersion($draft->versionInfo);
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