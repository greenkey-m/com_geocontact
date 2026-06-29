<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\Database\DatabaseAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_contact
 *
 * @since  4.0.0
 */
class GeocontactComponent extends MVCComponent implements RouterServiceInterface, CategoryServiceInterface, BootableExtensionInterface
{
	use RouterServiceTrait;
    use CategoryServiceTrait;
    use DatabaseAwareTrait;

    // Static variable to store the Categories instance
    public static $categories;

    public function boot(ContainerInterface $container)
    {
        self::$categories = $this->categoryFactory->createCategory();
    }

    protected function getTableNameForSection(?string $section = null)
    {
        return "geocontact_geocontacts";
    }

}
