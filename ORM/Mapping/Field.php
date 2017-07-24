<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Mapping;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Field
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @package Nitronet\eZORMBundle\ORM\Mapping
 */
class Field
{
    /**
     * @Annotation\Required()
     * @var string
     */
    public $name;

    /**
     * @Annotation\Required()
     * @var string
     */
    public $identifier;

    /**
     * @Annotation\Required()
     * @var string
     */
    public $type;

    public $group = null;

    public $required = false;

    public $position = null;

    public $description = null;

    public $searchable = true;

    public $translatable = true;

    public $container = false;

    public $infoCollector = false;

    public $settings = array();

    public $ormSettings = array();
}