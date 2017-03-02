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


use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Exception\QueryException as QE;
use Nitronet\eZORMBundle\ORM\Exception\QueryHandlerException;
use Nitronet\eZORMBundle\ORM\Query;

abstract class AbstractQueryHandler
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string|null|array
     */
    protected $language;

    /**
     * AbstractQueryHandler constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns the current Connection
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Defines the Connection to use
     *
     * @param Connection $connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Returns the defined language for this query
     * Ex: fre-FR
     *
     * @return string|null|array
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Defines the language to use for the next Query
     * Ex: eng-EN
     *
     * @param string|null|array $language
     */
    public function setLanguage($language = null)
    {
        $this->language = $language;
    }

    /**
     * Strategy Factory to load the required Transformer for $fetchType
     *
     * @param string $fetchType
     *
     * @return FetchTypeInterface
     * @throws QueryHandlerException if $fetchType is unsupported
     */
    public function fetchTypeTransformerFactory($fetchType)
    {
        switch($fetchType)
        {
            case Query::FETCH_CONTENT:
                $fetchType = new FetchType\ContentFetchType();
                break;

            case Query::FETCH_COUNT:
                $fetchType = new FetchType\CountFetchType();
                break;

            case Query::FETCH_EZ:
                $fetchType = new FetchType\eZFetchType();
                break;

            case Query::FETCH_ARRAY:
                $fetchType = new FetchType\ArrayFetchType();
                break;

            case Query::FETCH_CONTENT_ID:
                $fetchType = new FetchType\ContentIdFetchType();
                break;

            case Query::FETCH_LOCATION_ID:
                $fetchType = new FetchType\LocationIdFetchType();
                break;

            case Query::FETCH_ORM:
                $fetchType = new FetchType\ORMFetchType($this->connection);
                break;

            default:
                throw QueryHandlerException::unsupportedFetchTypeFactory($fetchType, $this);
        }

        return $fetchType;
    }

    /**
     * The LIMIT string to integer (or null if empty)
     *
     * @param string $str the LIMIT string
     *
     * @return null|integer
     * @throws QE if parse error
     */
    protected function findLimitFromString($str)
    {
        $str = trim($str);
        if (empty($str)) {
            return 0;
        }

        if (is_int($str) || is_numeric($str)) {
            return (int)$str;
        }

        if (strpos($str, ',') !== false) {
            list($limit, ) = explode(',', $str);
            if (empty($limit)) {
                return 0;
            }

            return (int)$limit;
        }

        throw QE::queryParseErrorFactory('limit', 'invalid format', array('25', '5,25'));
    }

    /**
     * Find the offset value in the LIMIT string
     *
     * @param string $str the LIMIT string
     *
     * @return integer
     * @throws QE if parse error
     */
    protected function findOffsetFromString($str)
    {
        $str = trim($str);
        if (empty($str)) {
            return 0;
        }

        if (is_int($str) || is_numeric($str)) {
            return 0; // this is just the LIMIT part
        }

        if (strpos($str, ',') !== false) {
            list(, $offset) = explode(',', $str);
            if (empty($offset)) {
                return 0;
            }

            return (int)$offset;
        }

        throw QE::queryParseErrorFactory('limit/offset', 'invalid format', array('0,5', '5,25'));
    }

    /**
     * @param Query $query
     *
     * @return null|ContentTypeIdentifier
     */
    protected function fromQueryToContentTypeIdentifier(Query $query)
    {
        $table = $query->getTable();
        if (empty($table)) {
            return null;
        }

        $final = array();
        if (strpos($table, ',') !== false) {
            $list = explode($table, ',');
            foreach ($list as $contentType) {
                $final[] = trim($contentType);
            }
        } else {
            $final[] = trim($table);
        }

        return new ContentTypeIdentifier($final);
    }
}