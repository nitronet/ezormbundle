<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Schema\FieldHelper;


use eZ\Publish\Core\FieldType\Relation\Value;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\Schema\FieldHelperInterface;
use eZ\Publish\API\Repository\Values\Content\Query as eZQ;

class RelationFieldHelper implements FieldHelperInterface
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
        if ($value instanceof Value && null !== $value->destinationContentId) {
            if (true === $field->getOrmSetting('eager', true)) {
                $query = new Query();
                $query->select()->where(new eZQ\Criterion\ContentId($value->destinationContentId))->limit(1);
                $results = $connection->execute($query);

                return array_shift($results);
            } else {
                return function() use ($connection, $value) {
                    $query = new Query();
                    $query->select()->where(new eZQ\Criterion\ContentId($value->destinationContentId))->limit(1);
                    $results = $connection->execute($query);

                    return array_shift($results);
                };
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     * @param Connection $connection
     *
     * @return Value
     */
    public function toEzValue($value, Connection $connection)
    {
        // return new Value($value);
    }

    /**
     * @return mixed
     */
    public function getDefaultORMSettings()
    {
        return array(
            'eager' => true
        );
    }
}

