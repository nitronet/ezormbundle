<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Workers;


use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\WorkerInterface;

class SaveEntityWorker implements WorkerInterface
{
    protected $entity;

    protected $location;

    protected $language;

    protected $draft;

    protected $hidden;

    public function __construct($entity, $location = null, $language = null, $draft = false, $hidden = false)
    {
        $this->entity = $entity;
        $this->location = $location;
        $this->language = $language;
        $this->draft = $draft;
        $this->hidden = $hidden;
    }

    /**
     * @param Connection $connection
     * @return mixed
     */
    public function execute(Connection $connection)
    {

    }
}