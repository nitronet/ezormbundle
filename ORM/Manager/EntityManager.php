<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Manager;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityManager implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $registry;

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
     *
     * @param string|null $entityClass
     *
     * @return object
     */
    public function entityFactory($entityClass)
    {
        if (empty($entityClass)) {
            return new \stdClass();
        }

        return new $entityClass;
    }

    public function persist($entity)
    {

    }

    public function remove($entity)
    {

    }

    /**
     * Defines the Dependency Injection Container
     *
     * @param ContainerInterface|null $container
     *
     * @return EntityManager
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }
}