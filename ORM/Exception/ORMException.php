<?php
namespace Nitronet\eZORMBundle\ORM\Exception;

use Nitronet\eZORMBundle\Exception;
use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\Schema\FieldHelperInterface;
use Nitronet\eZORMBundle\ORM\Schema\MetaFieldInterface;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use Nitronet\eZORMBundle\ORM\WorkerInterface;

class ORMException extends Exception
{
    /**
     * This exception is thrown when trying to query with multiple languages with ORM features
     *
     * @return ORMException
     */
    public static function unsupportedMultiplesLanguagesFactory()
    {
        return new self('Multiple languages are not allowed/supported when using ORM features.');
    }

    /**
     * This exception is thrown when a service should implement SchemaInterface
     *
     * @param object $schema
     * @param string $serviceId
     *
     * @return ORMException
     */
    public static function invalidSchemaImplFactory($schema, $serviceId)
    {
        return new self(sprintf(
            'Invalid schema implementation: %s (@%s). Should be an instance of %s',
            get_class($schema),
            $serviceId,
            SchemaInterface::class
        ));
    }

    /**
     * This exception is thrown when a trying to load an unregistered field helper
     *
     * @param string $fieldTypeIdentifier
     *
     * @return ORMException
     */
    public static function unknownFieldHelperFactory($fieldTypeIdentifier)
    {
        return new self(sprintf('Unregistered FieldHelper for FieldType: %s', $fieldTypeIdentifier));
    }

    /**
     * This exception is thrown when a helper should implement FieldHelperInterface
     *
     * @param object $fieldHelper
     * @param string $serviceId
     *
     * @return ORMException
     */
    public static function invalidFieldHelperImplFactory($fieldHelper, $serviceId)
    {
        return new self(sprintf(
            'Invalid FieldHelper implementation: %s (@%s). Should be an instance of %s',
            get_class($fieldHelper),
            $serviceId,
            FieldHelperInterface::class
        ));
    }

    /**
     * This exception is thrown when a trying to access an unregistered field
     *
     * @param string $fieldName
     *
     * @return ORMException
     */
    public static function unknownFieldFactory($fieldName)
    {
        return new self(sprintf('Unregistered Field: %s', $fieldName));
    }

    /**
     * This exception is thrown when a trying to access an unregistered metaField
     *
     * @param string $fieldName
     *
     * @return ORMException
     */
    public static function unknownMetaFieldFactory($fieldName)
    {
        return new self(sprintf('Unregistered MetaField: %s', $fieldName));
    }

    /**
     * This exception is thrown when using a Field instead of a MetaField
     *
     * @param Field $field
     *
     * @return ORMException
     */
    public static function unsupportedFieldShouldBeMeta(Field $field)
    {
        return new self(sprintf('Expected "%s", got Field (%s)', MetaFieldInterface::class, get_class($field)));
    }

    /**
     * Mapping exception
     *
     * @return ORMException
     */
    public static function mappingExceptionFactory($className, $property, $problem)
    {
        return new self(sprintf('Invalid mapping "%s::%s": %s', $className, $property, $problem));
    }

    /**
     * @param string $className
     *
     * @return ORMException
     */
    public static function schemaByEntityNotFound($className)
    {
        return new self(sprintf('No Schema found for class "%s"', $className));
    }

    /**
     * @param string $id
     *
     * @return ORMException
     */
    public static function schemaByContentTypeIdNotFoundExceptionFactory($id)
    {
        return new self(sprintf('No Schema found for Content-Type identifier ID: "%s"', $id));
    }

    /**
     * @param object $entity
     *
     * @return ORMException
     */
    public static function unregisteredEntityExceptionFactory($entity)
    {
        return new self(sprintf('Entity "%s" is not registered', get_class($entity)));
    }

    /**
     * @param object $entity
     * @param WorkerInterface $worker
     *
     * @return ORMException
     */
    public static function invalidEntityStateExceptionFactory($entity, WorkerInterface $worker)
    {
        return new self(sprintf('Entity "%s" has an invalid state for worker "%s"', get_class($entity), get_class($worker)));
    }

    /**
     * @param object $entity
     *
     * @return ORMException
     */
    public static function schemaMissingExceptionFactory($entity)
    {
        return new self(sprintf('Entity "%s" has no Schema available.', get_class($entity)));
    }

    /**
     *
     * @param $fieldName
     * @param $contentTypeName
     *
     * @return ORMException
     */
    public static function invalidFieldExceptionFactory($fieldName, $contentTypeName)
    {
        return new self(sprintf('Field "%s" does not exists on ContentType "%s".', $fieldName, $contentTypeName));
    }
}
