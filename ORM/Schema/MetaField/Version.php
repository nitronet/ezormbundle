<?php
namespace Nitronet\eZORMBundle\ORM\Schema\MetaField;


use eZ\Publish\API\Repository\Values\Content\Content;
use Nitronet\eZORMBundle\ORM\Schema\AbstractMetaField;
use Nitronet\eZORMBundle\ORM\Schema\MetaFieldInterface;

class Version extends AbstractMetaField implements MetaFieldInterface
{
    const DEFAULT_ATTR_NAME = '_version';

    /**
     * @param object $entity
     * @param Content $content
     * @param MetaFieldInterface $metaField
     *
     * @return mixed
     */
    public function toEntityValue($entity, Content $content, MetaFieldInterface $metaField)
    {
        return $content->contentInfo->currentVersionNo;
    }

}