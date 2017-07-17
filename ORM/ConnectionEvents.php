<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM;


class ConnectionEvents
{
    const BEFORE_QUERY = 'ezorm.before_query';

    const AFTER_QUERY  = 'ezorm.after_query';
}