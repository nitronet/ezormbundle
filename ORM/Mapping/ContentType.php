<?php
namespace Nitronet\eZORMBundle\ORM\Mapping;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Entity
 *
 * @Annotation
 * @Target("CLASS")
 * @Annotation\Attribute(name="description", required="false", type="string")
 * @Annotation\Attribute(name="container", required="false", type="boolean")
 * @Annotation\Attribute(name="urlAlias", required="false", type="string")
 * @Annotation\Attribute(name="mainLanguageCode", required="false", type="string")
 *
 * @package Nitronet\eZORMBundle\ORM\Mapping
 */
class ContentType
{
    /**
     * @Annotation\Required()
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $description = null;

    /**
     * @var bool
     */
    public $container = false;

    /**
     * @var string
     */
    public $urlAlias = null;

    /**
     * @var string
     */
    public $mainLanguageCode = "eng-GB";

}