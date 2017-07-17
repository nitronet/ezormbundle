<?php
namespace Nitronet\eZORMBundle\ORM\Exception;

use Nitronet\eZORMBundle\Exception;
use Nitronet\eZORMBundle\ORM\Schema\FieldHelperInterface;
use Nitronet\eZORMBundle\ORM\SchemaInterface;

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
}