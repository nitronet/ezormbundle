<?php
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
    public $name;

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