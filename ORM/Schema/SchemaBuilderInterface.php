<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Schema;

use Nitronet\eZORMBundle\ORM\SchemaInterface;

interface SchemaBuilderInterface
{
    /**
     * @return SchemaInterface
     */
    public function build();
}