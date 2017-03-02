<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM;


use eZ\Publish\API\Repository\Repository;
use Nitronet\eZORMBundle\ORM\Exception\QueryHandlerException;
use Nitronet\eZORMBundle\ORM\Manager\EntityManager;
use Nitronet\eZORMBundle\ORM\Manager\FieldsManager;
use Nitronet\eZORMBundle\ORM\Manager\SchemasManager;
use Nitronet\eZORMBundle\ORM\Manager\TablesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Connection
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var TablesManager
     */
    protected $tablesManager;

    /**
     * @var SchemasManager
     */
    protected $schemasManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $defaultLanguageCode;

    /**
     * Connection constructor.
     * @param Repository $repository
     * @param TablesManager $tablesManager
     * @param SchemasManager $schemasManager
     * @param EntityManager $entityManager
     */
    public function __construct(Repository $repository, TablesManager $tablesManager, SchemasManager $schemasManager,
        EntityManager $entityManager
    ) {
        $this->repository       = $repository;
        $this->tablesManager    = $tablesManager;
        $this->schemasManager   = $schemasManager;
        $this->entityManager    = $entityManager;
        $this->defaultLanguageCode = $repository->getContentLanguageService()->getDefaultLanguageCode();
    }

    /**
     * Executes a Query
     *
     * @param Query $query
     * @param string $fetchType
     * @param null|string|array $language
     *
     * @return mixed
     * @throws QueryHandlerException
     */
    public function execute(Query $query, $fetchType = null, $language = null)
    {
        $table      = $this->tablesManager->findForQuery($query);
        $handler    = $table->getQueryHandler($this);
        if (null === $fetchType) {
            $fetchType = $handler->getDefaultFetchType();
        }

        if (null === $language) {
            $language = $this->defaultLanguageCode;
        }

        if (is_array($fetchType)) {
            foreach ($fetchType as $fetchTypeName) {
                if (false === $handler->supports($fetchTypeName)) {
                    throw QueryHandlerException::unsupportedFetchTypeFactory($fetchTypeName, $handler);
                }
            }
        }

        return $handler->handle($query, $fetchType, $language);
    }

    /**
     * @param string $tableName
     *
     * @return TableInterface
     */
    public function table($tableName)
    {
        return $this->tablesManager->load($tableName);
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Returns the Tables Manager
     *
     * @return TablesManager
     */
    public function getTablesManager()
    {
        return $this->tablesManager;
    }

    /**
     * Returns the Schemas Manager
     *
     * @return SchemasManager
     */
    public function getSchemasManager()
    {
        return $this->schemasManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}