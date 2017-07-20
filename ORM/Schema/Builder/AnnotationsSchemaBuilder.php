<?php
namespace Nitronet\eZORMBundle\ORM\Schema\Builder;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Manager\FieldsManager;
use Nitronet\eZORMBundle\ORM\Mapping\ContentType;
use Nitronet\eZORMBundle\ORM\Mapping\Entity;
use Nitronet\eZORMBundle\ORM\Mapping\Field;
use Nitronet\eZORMBundle\ORM\Schema\Schema;
use Nitronet\eZORMBundle\ORM\Schema\SchemaBuilderInterface;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use Nitronet\eZORMBundle\ORM\Schema\Field as SchemaField;

class AnnotationsSchemaBuilder implements SchemaBuilderInterface
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var FieldsManager
     */
    protected $fieldsManager;

    /**
     * AnnotationsSchemaBuilder constructor.
     *
     * @param $className
     * @param FieldsManager $fieldsManager
     */
    public function __construct($className, FieldsManager $fieldsManager)
    {
        $this->className = $className;
        $this->fieldsManager = $fieldsManager;
    }

    /**
     *
     * @return SchemaInterface|null
     */
    public function build()
    {
        $reader = new AnnotationReader();

        if (!class_exists($this->className, true)) {
            throw new \InvalidArgumentException(sprintf('Class not found'));
        }

        $reflectionClass = new \ReflectionClass($this->className);

        $entityAnnotation = $reader->getClassAnnotation($reflectionClass, Entity::class);
        if (!$entityAnnotation instanceof Entity) {
            return null;
        }

        $contentTypeAnnotation = $reader->getClassAnnotation($reflectionClass, ContentType::class);
        if (!$contentTypeAnnotation instanceof ContentType) {
            return null;
        }

        $schema = new Schema($contentTypeAnnotation->identifier);
        $schema->setEntityClass($this->className);

        $schema->setContentTypeDescription($contentTypeAnnotation->description);
        $schema->setContentTypeIsContainer((bool)$contentTypeAnnotation->container);
        $schema->setContentTypeMainLanguageCode($contentTypeAnnotation->mainLanguageCode);
        $schema->setContentTypeUrlAliasSchema($contentTypeAnnotation->urlAlias);

        foreach ($reflectionClass->getProperties() as $prop) {
            $propertyName = $prop->getName();
            $propertyAnnotations = $reader->getPropertyAnnotations($prop);

            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Field) {
                    $field = new SchemaField(
                        $annotation->type,
                        $annotation->searchable,
                        $annotation->required,
                        $annotation->group,
                        $annotation->infoCollector,
                        $annotation->position
                    );

                    $field->setOrmSettings($annotation->ormSettings);
                    $field->setSettings($annotation->settings);
                    $field->setDescription($annotation->description);
                    $field->setTranslatable($annotation->translatable);

                    $field->setFieldHelper($this->fieldsManager->loadFieldHelper($annotation->type));

                    $schema->addField($propertyName, $field);
                }
            }
        }


        return $schema;
    }


}