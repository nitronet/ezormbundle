<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM;

use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\Schema\MetaFieldInterface;

interface SchemaInterface
{
    /**
     * @return Field
     */
    public function getFields();

    /**
     * @return MetaFieldInterface
     */
    public function getMetaFields();

    /**
     * @param string $attribute
     * @param Field $field
     *
     * @return SchemaInterface
     */
    public function addField($attribute, Field $field);

    /**
     * @param string $attribute
     *
     * @return Field
     */
    public function getField($attribute);

    /**
     * @param string $attribute
     * @return bool
     */
    public function hasField($attribute);

    /**
     * @param string $attribute
     * @param MetaFieldInterface $field
     *
     * @return SchemaInterface
     */
    public function addMetaField($attribute, MetaFieldInterface $field);

    /**
     * @param string $attribute
     *
     * @return Field
     */
    public function getMetaField($attribute);

    /**
     * @param string $attribute
     * @return bool
     */
    public function hasMetaField($attribute);

    /**
     * @return null|string
     */
    public function getEntityClass();
}