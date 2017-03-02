<?php
namespace Nitronet\eZORMBundle\Tests\Utils;


use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Repository\Tests\Service\Mock\Base;

class MockFactory extends Base
{
    /**
     * @return Repository
     */
    public function repositoryFactory()
    {
        return $this->getRepository();
    }
}