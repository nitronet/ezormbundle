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


use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\QueryHandlerInterface;
use eZ\Publish\API\Repository\Values\Content\Query as eZContentQuery;

class ContentTypeQueryHandler extends AbstractQueryHandler implements QueryHandlerInterface
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
            return $this->handleSelect($query, $language);
        }

        throw new \Exception('This query type is not implemented yet');
    }

    /**
     * Handles a SELECT Query
     *
     * @param Query $query
     * @param string|null|array $language
     *
     * @return ContentType[]
     */
    protected function handleSelect(Query $query, $language = null)
    {
        $ctypeService       = $this->getConnection()->getRepository()->getContentTypeService();
        $fullText           = $query->getText();

        // handle some king of SELECT/WHERE combo
        // if nothing to search then return all content types (ressources hungry)
        if (!$query->hasText()) {
            $groups     = $ctypeService->loadContentTypeGroups();
            $results    = array();

            foreach ($groups as $group) {
                $ctypes = $ctypeService->loadContentTypes($group);
                foreach ($ctypes as $contentType) {
                    $results[$contentType->id] = $contentType;
                }
            }

            return $results;
        }

        if (false === strpos(',', $fullText)) {
            $idents = array($fullText);
        } else {
            $idents = explode(',', $fullText);
        }

        $results = array();
        foreach ($idents as $ident) {
            $ident = trim($ident);
            if (empty($ident)) {
                continue;
            } elseif (is_numeric($ident)) {
                $contentType = $ctypeService->loadContentType($ident);
            } elseif (false === strpos('remote_id:', $ident, 0)) {
                $contentType = $ctypeService->loadContentTypeByIdentifier($ident);
            } else {
                $contentType = $ctypeService->loadContentTypeByRemoteId(substr($ident, strlen('remote_id:')));
            }

            $results[$contentType->id] = $contentType;
        }

        return $results;
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
            Query::FETCH_EZ
        ));
    }

    /**
     * @return string
     */
    public function getDefaultFetchType()
    {
        return Query::FETCH_EZ;
    }
}