<?php

namespace Nitronet\eZORMBundle\DependencyInjection;

use Nitronet\eZORMBundle\eZORMBundle;
use Nitronet\eZORMBundle\ORM\Exception\ConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class eZORMExtension extends Extension
{
    const TAG_TABLE = 'ezorm.table';
    const TAG_FIELD_HELPER = 'ezorm.field_helper';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // load tables definitions
        $services = $container->findTaggedServiceIds(self::TAG_TABLE);
        if (isset($config[eZORMBundle::PARAMETER_TABLES_ALIASES])) {
            $tablesParams = $config[eZORMBundle::PARAMETER_TABLES_ALIASES];
        } else {
            $tablesParams = array();
        }

        foreach ($services as $id => $tags) {
            foreach ($tags as $tagData) {
                if (!isset($tagData['alias']) || empty($tagData['alias'])) {
                    throw ConfigurationException::tagMissingParameterFactory('alias', $id, self::TAG_TABLE);
                }

                $tablesParams[$tagData['alias']] = $id;
            }
        }

        $container->setParameter(eZORMBundle::PARAMETER_TABLES_ALIASES, $tablesParams);

        // load field types helpers
        $services = $container->findTaggedServiceIds(self::TAG_FIELD_HELPER);
        $helpersDefinitions = array();

        foreach ($services as $id => $tags) {
            foreach ($tags as $tagData) {
                if (!isset($tagData['type']) || empty($tagData['type'])) {
                    throw ConfigurationException::tagMissingParameterFactory('type', $id, self::TAG_FIELD_HELPER);
                }

                $helpersDefinitions[$tagData['type']] = $id;
            }
        }

        $container->getDefinition(eZORMBundle::SERVICE_FIELD_MANAGER)
                ->addMethodCall('setDefinitions', array($helpersDefinitions));
    }
}
