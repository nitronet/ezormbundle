<?php
namespace Nitronet\eZORMBundle\ORM\Exception;

use Nitronet\eZORMBundle\Exception;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\TableInterface;
use eZ\Publish\API\Repository\Values\Content\Query as eZContentQuery;
use eZ\Publish\API\Repository\Values\Content\LocationQuery as eZLocationQuery;

class QueryException extends Exception
{
    protected static $queryTypesString = array(
        Query::QUERY_TYPE_UNKNOWN   => 'unknown',
        Query::QUERY_TYPE_DELETE    => 'delete',
        Query::QUERY_TYPE_INSERT    => 'insert',
        Query::QUERY_TYPE_UPDATE    => 'update',
        Query::QUERY_TYPE_SELECT    => 'select'
    );

    /**
     * This exception is thrown when trying to performs an invalid type of SELECT query
     *
     * @param string $selectType
     * @param array  $allowedTypes
     *
     * @return QueryException
     */
    public static function invalidSelectTypeFactory($selectType, array $allowedTypes)
    {
        return new self(sprintf(
            'Invalid SELECT type: %s. Should be one of: %s',
            $selectType,
            implode(', ', $allowedTypes)
        ));
    }

    /**
     * @param int $newType
     * @param int $oldType
     *
     * @return QueryException
     */
    public static function queryTypeChangeFactory($newType, $oldType)
    {
        return new self(sprintf(
            'You cannot change the Query type "%s" to "%s"',
            (isset(self::$queryTypesString[$newType]) ? self::$queryTypesString[$newType] : $newType),
            (isset(self::$queryTypesString[$oldType]) ? self::$queryTypesString[$oldType] : $oldType)
        ));
    }

    /**
     * This exception is thrown when trying to pass something other than an eZ SortClause
     * to Query::sort()
     *
     * @param mixed $sortClause
     * @param mixed $index
     *
     * @return QueryException
     */
    public static function invalidSortClauseFactory($sortClause, $index = null)
    {
        return new self(sprintf(
            'Invalid SortClause: %s%s, expected instance of %s.',
            (is_object($sortClause) ? get_class($sortClause) : gettype($sortClause)),
            ($index !== null ? sprintf(' at index "%s"', (string)$index) : null),
            eZContentQuery\SortClause::class
        ));
    }

    /**
     * This exception is thrown when trying to do a query on an invalid target
     *
     * @param mixed $type
     *
     * @return QueryException
     */
    public static function invalidTableTypeFactory($type)
    {
        return new self(sprintf(
            'Invalid Table/target type: %s. Should be one of: %s',
            (empty($type) ? '(empty)' : gettype($type)),
            implode(', ', [TableInterface::class, 'string'])
        ));
    }

    /**
     * Thrown when a Query field cannot be parsed
     *
     * @param string $strType  The "field" that was erroneous
     * @param string $errMsg   The error message
     * @param array  $examples List of valid examples
     *
     * @return QueryException
     */
    public static function queryParseErrorFactory($strType, $errMsg, array $examples = array())
    {
        return new self(sprintf(
            'Parse error at "%s": %s %s',
            strtoupper($strType),
            $errMsg,
            (!count($examples) ?: '('. implode(', ', $examples) .')')
        ));
    }

    /**
     * This exception is thrown when trying to apply things to an unsupported eZ Query
     * Only Content and Location queries are supported.
     *
     * @param object $ezQuery
     *
     * @return QueryException
     */
    public static function invalidEzQueryTypeFactory($ezQuery)
    {
        return new self(sprintf(
            'Invalid eZ Query type: %s. It should only be an instance of %s',
            get_class($ezQuery),
            implode(' or ', [eZContentQuery::class, eZLocationQuery::class])
        ));
    }

    /**
     * This exception is thrown when the Query doesn't provide informations to load
     * an eZ Content-Type object
     *
     * @param $table
     *
     * @return QueryException
     */
    public static function invalidQueryTableExceptionFactory($table)
    {
        return new self(sprintf(
            'Invalid Table identifier: "%s"',
            $table
        ));
    }

    /**
     * This exception is thrown when the Query doesn't provide any Location
     *
     * @param string $queryType
     *
     * @return QueryException
     */
    public static function queryNeedsLocationsExceptionFactory($queryType)
    {
        return new self(sprintf(
            'Query type "%s" require at least one location',
            $queryType
        ));
    }


    /**
     * This exception is thrown when the Query doesn't provide any Location
     *
     * @param mixed $locationId
     *
     * @return QueryException
     */
    public static function invalidLocationIdentifierExceptionFactory($locationId)
    {
        return new self(sprintf(
            'Invalid location identifier: "%s"',
            (is_object($locationId) ? $locationId : (string)$locationId)
        ));
    }
}
