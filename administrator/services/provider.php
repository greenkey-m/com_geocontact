<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Greenkey\Component\Geocontact\Administrator\Extension\GeocontactComponent;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory as CategoryFactorServiceProvider;

/**
 * The service provider.
 *
 * @since  4.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container)
	{
        $container->registerServiceProvider(new CategoryFactorServiceProvider('\\Greenkey\\Component\\Geocontact'));
		$container->registerServiceProvider(new MVCFactory('\\Greenkey\\Component\\Geocontact'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Greenkey\\Component\\Geocontact'));
        $container->registerServiceProvider(new RouterFactory('\\Greenkey\\Component\\Geocontact'));

        $container->set(
            ComponentInterface::class,
            function (Container $container)
            {
                $component = new GeocontactComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                $component->setDatabase($container->get(DatabaseInterface::class));

                return $component;
            }
        );
	}
};
