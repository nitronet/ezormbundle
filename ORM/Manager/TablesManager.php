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

use Nitronet\eZORMBundle\eZORMBundle;
use Nitronet\eZORMBundle\ORM\Exception\TableException;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\TableInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TablesManager implements ContainerAwareInterface
{
    /**
     * @var TableInterface[]
     */
    protected $tables = array();

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * TablesManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
    }

    /**
     * Loads a Table instance according to its $tableName
     *
     * $tableName can be:
     * - A table alias (ie: ez:users)
     * - Any string (will return a ContentTable instance)
     *
     * @param string $tableName
     *
     * @return TableInterface
     * @throws \Exception when feature not implemented yet
     * @throws TableException when table/target not found
     */
    public function load($tableName)
    {
        if (empty($tableName)) {
            $tableName = eZORMBundle::CONTENT_TABLE_ALIAS;
        }

        if (!array_key_exists($tableName, $this->tables)) {
            $aliases = $this->container->getParameter(eZORMBundle::PARAMETER_TABLES_ALIASES);
            if (isset($aliases[$tableName])) {
                $tableImpl = $this->container->get($aliases[$tableName]);
            } else {
                $tableImpl = $this->container->get($aliases[eZORMBundle::CONTENT_TABLE_ALIAS]);
            }

            if (!$tableImpl instanceof TableInterface) {
                throw TableException::invalidTargetImplementationFactory($tableImpl);
            }

            $this->tables[$tableName] = $tableImpl;
        }

        return $this->tables[$tableName];
    }

    /**
     * Finds the appropriate target for a Query
     *
     * @param Query $query
     *
     * @return TableInterface
     * @throws TableException If the target can't be found
     */
    public function findForQuery(Query $query)
    {
        $table = $query->getTable();
        if ($table instanceof TableInterface) {
            return $table;
        }

        return  $this->load($table);
    }

    /**
     * Defines the Dependency Injection Container
     *
     * @param ContainerInterface|null $container
     *
     * @return TablesManager
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        return $this;
    }
}