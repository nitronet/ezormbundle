<?php
namespace Nitronet\eZORMBundle\ORM\Schema;



class AbstractMetaField extends Field
{
    /**
     * AbstractMetaField constructor.
     */
    public function __construct()
    {
        parent::__construct('ezorm:meta', false, false);
    }
}