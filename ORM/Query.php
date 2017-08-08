<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalOr;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Nitronet\eZORMBundle\ORM\Exception\QueryException;

class Query
{
    const SELECT_CONTENT        = 'content';
    const SELECT_LOCATION       = 'location';

    const QUERY_TYPE_UNKNOWN    = 0;
    const QUERY_TYPE_SELECT     = 1;
    const QUERY_TYPE_INSERT     = 2;
    const QUERY_TYPE_UPDATE     = 3;
    const QUERY_TYPE_DELETE     = 4;

    const FETCH_ORM             = 'orm';
    const FETCH_EZ              = 'ez';
    const FETCH_ARRAY           = 'array';
    const FETCH_CLASS           = 'class';
    const FETCH_CONTENT         = 'content';
    const FETCH_COUNT           = 'count';
    const FETCH_CONTENT_ID      = 'content_id';
    const FETCH_LOCATION_ID     = 'location_id';

    /**
     * Query type (see constants)
     * @var int
     */
    protected $type = self::QUERY_TYPE_UNKNOWN;

    /**
     * Select type: content or location (see constants)
     * @var string
     */
    protected $selectType;

    /**
     * Query's destination
     * @var string|TableInterface
     */
    protected $table;

    /**
     * Query's limit
     * @var int|string
     */
    protected $limit;

    /**
     * Query Sort Clauses
     * @var SortClause[]
     */
    protected $sortClauses = array();

    /**
     * The actual Connection to the eZ Repository
     * @var Connection
     */
    protected $connection;

    /**
     * Fulltext search query
     * @var string
     */
    protected $text;

    /**
     * Query filters/criterions
     * @var null|CriterionInterface
     */
    protected $where;

    /**
     * Query values (insert)
     * @var array
     */
    protected $values = array();

    /**
     * Query insert location(s) (location(s), remoteId(s), Id(s))
     * @var Location|Location[]|string|string[]|int|int[]
     */
    protected $locations;

    /**
     * Content remoteId (insert)
     * @var string|null
     */
    protected $remoteId = null;

    /**
     * Content visibility (INSERT/UPDATE)
     * @var bool
     */
    protected $hidden = false;

    /**
     * Content draft or published? (INSERT/UPDATE)
     * @var bool
     */
    protected $draft = false;

    /**
     * Performs a SELECT-like Query
     *
     * The eZ Repository usually allows two types of queries: content or location, which is
     * here represented as $selectType.
     *
     * @param string $selectType The type of SELECT (content or location)
     *
     * @return Query
     * @throws QueryException when $selectType is unknown/invalid
     */
    public function select($selectType = self::SELECT_CONTENT)
    {
        $allowedTypes = [self::SELECT_CONTENT, self::SELECT_LOCATION];
        if (!in_array($selectType, $allowedTypes)) {
            throw QueryException::invalidSelectTypeFactory($selectType, $allowedTypes);
        }

        $this->setType(self::QUERY_TYPE_SELECT);
        $this->selectType = $selectType;

        return $this;
    }

    /**
     * Defines the Query's target "table"
     *
     * @param string|TableInterface $table
     *
     * @return Query
     * @throws QueryException when
     */
    public function from($table)
    {
        if ((!is_object($table) || !$table instanceof TableInterface) && !is_string($table)) {
            throw QueryException::invalidTableTypeFactory($table);
        }

        $this->table = $table;

        return $this;
    }

    /**
     * Performs an INSERT-like Query
     * For simplicity the query's target (aka "table") is defined here.
     *
     * @param string|TableInterface $table
     *
     * @return Query
     * @throws QueryException if the query type is already defined
     */
    public function insert($table)
    {
        $this->table = $table;
        $this->setType(self::QUERY_TYPE_INSERT);

        return $this;
    }

    /**
     * Performs an UPDATE-like Query
     * For simplicity the query's target (aka "table") is defined here.
     *
     * @param string|TableInterface $table
     *
     * @return Query
     * @throws QueryException if the query type is already defined
     */
    public function update($table)
    {
        $this->table = $table;
        $this->setType(self::QUERY_TYPE_UPDATE);

        return $this;
    }

    /**
     * Performs an DELETE-like Query
     * For simplicity the query's target (aka "table") is defined here.
     *
     * @param string|TableInterface $table
     *
     * @return Query
     * @throws QueryException if the query type is already defined
     */
    public function delete($table)
    {
        $this->table = $table;
        $this->setType(self::QUERY_TYPE_DELETE);

        return $this;
    }

    /**
     * Defines the Query LIMIT
     *
     * - If $limit is an integer (or is numeric), it'll be used as the query's limit without offset
     * - If $limit is a string having two integers separated by a comma (ie: 0,20), the first integer
     *   will be used as the query's Offset and the other as the query's limit
     * - Any other value will throw an exception
     *
     * @param int|string $limit
     *
     * @return Query
     * @throws QueryException if $limit's format is invalid
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Defines the Query ORDER BY. Can be called multiple times
     * If not defined, eZ's defaults will be used.
     *
     * @param SortClause|SortClause[] $sortClause  Sort clauses (one or list)
     *
     * @return Query
     * @throws QueryException If the $sortClause is invalid
     */
    public function orderBy($sortClause)
    {
        if (is_array($sortClause) || $sortClause instanceof \Traversable) {
            foreach ($sortClause as $idx => $clause) {
                if (!$clause instanceof SortClause) {
                    throw QueryException::invalidSortClauseFactory($sortClause, $idx);
                }

                $this->sortClauses[] = $clause;
            }
        } elseif (!$sortClause instanceof SortClause) {
            throw QueryException::invalidSortClauseFactory($sortClause);
        }

        $this->sortClauses[] = $sortClause;

        return $this;
    }

    /**
     * Performs a fulltext search on the repository.
     *
     * @param string $queryString Fulltext query string
     *
     * @return $this
     */
    public function text($queryString)
    {
        $this->text = $queryString;
        return $this;
    }

    /**
     * Add the WHERE criterion (= eZ Query "filters").
     * If called many times, it'll perform a "AND WHERE" query (LogicalAnd)
     *
     * @param CriterionInterface $criterion
     *
     * @return Query
     */
    public function where(CriterionInterface $criterion)
    {
        if ($this->where instanceof LogicalAnd) {
            array_push($this->where->criteria, $criterion);
        } elseif ($this->where instanceof CriterionInterface) {
            $this->where = new LogicalAnd(array($this->where, $criterion));
        } else {
            $this->where = $criterion;
        }

        return $this;
    }

    /**
     * Adds a Logical OR WHERE
     *
     * @param CriterionInterface $criterion
     */
    public function orWhere(CriterionInterface $criterion)
    {
        if ($this->where instanceof CriterionInterface) {
            $this->where = new LogicalOr(array($this->where, $criterion));
        } else {
            $this->where = $criterion;
        }
    }


    /**
     * @param string|null $remoteId
     *
     * @return Query
     */
    public function remoteId($remoteId = null)
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    /**
     * Defines the Query type (and checks if its not already defined)
     *
     * @param int $type The Query type (see constants)
     *
     * @internal
     * @return void
     * @throws QueryException if the query type is already defined
     */
    protected function setType($type)
    {
        if ($this->type !== self::QUERY_TYPE_UNKNOWN && $this->type !== $type) {
            throw QueryException::queryTypeChangeFactory($type, $this->type);
        }

        $this->type = (int)$type;
    }

    /**
     * Tells if the Query is of type $queryType
     *
     * @param int $queryType
     *
     * @return bool
     */
    public function isType($queryType)
    {
        return ($this->type === $queryType);
    }

    /**
     * Returns the current Connection
     *
     * The Connection is not defined until we Connection::execute() the Query.
     *
     * @return Connection|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Defines the Connection to use for this Query
     *
     * @param Connection $connection
     *
     * @return Query
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return TableInterface|string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function hasLimit()
    {
        return isset($this->limit);
    }

    /**
     * @return int|string
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    public function hasText()
    {
        return isset($this->text);
    }

    /**
     * Returns the fulltext search query or null
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Returns Sort Clauses for this Query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     */
    public function getSortClauses()
    {
        return $this->sortClauses;
    }

    /**
     * @return CriterionInterface|null
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return Query
     */
    public static function factory()
    {
        return new self();
    }

    /**
     * @return null|string
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Defines values for INSERT/UPDATE queries
     *
     * @param array $values
     *
     * @return Query
     */
    public function values(array $values)
    {
        $this->values = array_merge($this->values, $values);

        return $this;
    }

    /**
     * Set one Value for INSERT/UPDATE queries
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Query
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @return Location|Location[]|int|\int[]|string|\string[]
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param Location|Location[]|int|\int[]|string|\string[] $location
     *
     * @return Query
     */
    public function into($location)
    {
        $this->locations = $location;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return Query
     */
    public function hidden($bool)
    {
        $this->hidden = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $bool
     *
     * @return Query
     */
    public function draft($bool)
    {
        $this->draft = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->draft;
    }
}