<?php
namespace Nitronet\eZORMBundle\ORM\Exception;

use Nitronet\eZORMBundle\Exception;

class ConfigurationException extends Exception
{
    /**
     * Thrown when a table/target service tag is missing the "alias" parameter
     *
     * @param string $parameter
     * @param string $serviceId
     * @param string $tagName
     *
     * @return ConfigurationException
     */
    public static function tagMissingParameterFactory($parameter, $serviceId, $tagName)
    {
        return new self(sprintf(
            'Missing the "%s" tag parameter on service "@%s" (tag: %s)',
            $parameter,
            (empty($serviceId) ? '(empty)' : $serviceId),
            $tagName
        ));
    }
}
