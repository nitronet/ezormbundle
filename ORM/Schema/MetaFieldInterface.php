<?php
namespace Nitronet\eZORMBundle\ORM\Schema;



use eZ\Publish\API\Repository\Values\Content\Content;

interface MetaFieldInterface
{
    public function toEntityValue($entity, Content $content, MetaFieldInterface $metaField);

    public function getOrmSettings();

    public function getOrmSetting($name, $default = false);

    public function setOrmSettings(array $ormSettings);
}