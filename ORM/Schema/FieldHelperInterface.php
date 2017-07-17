<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Schema;


use Nitronet\eZORMBundle\ORM\Connection;

interface FieldHelperInterface
{
    /**
     * @param mixed $value
     * @param Connection $connection
     * @param Field $field
     *
     * @return mixed
     */
    public function toEntityValue($value, Connection $connection, Field $field);

    /**
     * @param mixed $value
     * @param Connection $connection
     *
     * @return mixed
     */
    public function toEzValue($value, Connection $connection);

    /**
     * @return array
     */
    public function getDefaultORMSettings();
}