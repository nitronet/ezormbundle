<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class eZORMBundle extends Bundle
{
    const PARAMETER_TABLES_ALIASES      = 'ezorm.tables_aliases';
    const CONTENT_TABLE_ALIAS           = 'ez:content';
    const CONTENTTYPE_TABLE_ALIAS       = 'ez:content_type';
    const SERVICE_FIELD_MANAGER         = 'ezorm.fields_manager';
}
