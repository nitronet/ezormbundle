<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Schema\FieldHelper;


use eZ\Publish\Core\FieldType\TextLine\Value;
use eZ\Publish\Core\FieldType\TextBlock\Value as TextBlockValue;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\Schema\FieldHelperInterface;


class TextFieldHelper implements FieldHelperInterface
{
    /**
     * @param mixed $value
     * @param Connection $connection
     * @param Field $field
     *
     * @return mixed
     */
    public function toEntityValue($value, Connection $connection, Field $field)
    {
        if ($value instanceof Value || $value instanceof TextBlockValue) {
            return $value->text;
        }
    }

    /**
     * @param mixed $value
     * @param Connection $connection
     *
     * @return Value
     */
    public function toEzValue($value, Connection $connection)
    {
        return new Value($value);
    }

    /**
     * @return mixed
     */
    public function getDefaultORMSettings()
    {
        return array();
    }
}

