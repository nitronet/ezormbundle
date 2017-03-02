<?php
namespace Nitronet\eZORMBundle\ORM\Exception;

use Nitronet\eZORMBundle\Exception;
use Nitronet\eZORMBundle\ORM\QueryHandlerInterface;

class QueryHandlerException extends Exception
{
    /**
     * Thrown on unsupported Query fetch type by the QueryHandler
     *
     * @param string $fetchType
     * @param QueryHandlerInterface $queryHandler
     *
     * @return QueryHandlerException
     */
    public static function unsupportedFetchTypeFactory($fetchType, QueryHandlerInterface $queryHandler)
    {
        return new self(sprintf(
            'Unsupported Query Fetch Type: %s. (QueryHandler: %s)',
            (is_array($fetchType) ? implode(', ', $fetchType) : $fetchType),
            get_class($queryHandler)
        ));
    }
}