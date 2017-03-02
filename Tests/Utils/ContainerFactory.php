<?php
namespace Nitronet\eZORMBundle\Tests\Utils;



use Nitronet\eZORMBundle\DependencyInjection\eZORMExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerFactory extends KernelTestCase
{
    public static function factory()
    {
        $container = new ContainerBuilder();

        $ezORMExt = new eZORMExtension();
        $ezORMExt->load(array(), $container);

        return $container;
    }
}