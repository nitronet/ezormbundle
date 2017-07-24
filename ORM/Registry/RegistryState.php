<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Registry;


final class RegistryState
{
    const REGISTERED      = 0x01;
    const FRESH           = 0x02;
    const CHANGED         = 0x03;
    const UNKNOWN         = 0x04;
    const UNREGISTERED    = 0x05;
}