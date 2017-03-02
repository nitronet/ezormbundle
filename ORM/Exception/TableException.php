<?php
namespace Nitronet\eZORMBundle\ORM\Exception;

use Nitronet\eZORMBundle\Exception;
use Nitronet\eZORMBundle\ORM\TableInterface;

class TableException extends Exception
{
    /**
     * Thrown when a table/target cannot be defined for a Query
     *
     * @param string $targetName
     *
     * @return TableException
     */
    public static function targetNotFoundFactory($targetName)
    {
        return new self(sprintf(
            'No Table/Target found for identifier: %s',
            (empty($targetName) ? '(empty)' : $targetName)
        ));
    }

    /**
     * Thrown when a table/target class doesn't implements TableInterface
     *
     * @param object $tableImpl
     *
     * @return TableException
     */
    public static function invalidTargetImplementationFactory($tableImpl)
    {
        return new self(sprintf(
            'Table/Target class "%s" MUST implement %s',
            get_class($tableImpl),
            TableInterface::class
        ));
    }
}
