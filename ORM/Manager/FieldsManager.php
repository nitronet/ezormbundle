<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Manager;

use Nitronet\eZORMBundle\ORM\Exception\ORMException;
use Nitronet\eZORMBundle\ORM\Schema\FieldHelperInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FieldsManager implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $definitions = array();

    /**
     * SchemasManager constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
    }

    /**
     * @param string $fieldTypeIdentifier
     * @param string $definition
     *
     * @return $this
     */
    public function register($fieldTypeIdentifier, $definition)
    {
        $this->definitions[$fieldTypeIdentifier] = $definition;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param array $definitions
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Defines the Dependency Injection Container
     *
     * @param ContainerInterface|null $container
     *
     * @return FieldsManager
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param string $fieldTypeIdentifier
     *
     * @return FieldHelperInterface
     *
     * @throws ORMException when trying to load an unregistered field type helper
     */
    public function loadFieldHelper($fieldTypeIdentifier)
    {
        if (!array_key_exists($fieldTypeIdentifier, $this->definitions)) {
            throw ORMException::unknownFieldHelperFactory($fieldTypeIdentifier);
        }

        $fieldHelper = $this->container->get($this->definitions[$fieldTypeIdentifier]);
        if (!$fieldHelper instanceof FieldHelperInterface) {
            throw ORMException::invalidFieldHelperImplFactory($fieldHelper, $this->definitions[$fieldTypeIdentifier]);
        }

        return $fieldHelper;
    }
}