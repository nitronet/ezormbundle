<?php
namespace Nitronet\eZORMBundle\ORM\Mapping;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class MetaField
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @package Nitronet\eZORMBundle\ORM\Mapping
 */
class MetaField
{
    /**
     * @Annotation\Required()
     * @var string
     */
    public $service;

    /**
     * @var array
     */
    public $ormSettings = array();
}