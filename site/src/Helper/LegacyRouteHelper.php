<?php
/**
 * @package     com_geocontact
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Greenkey\Component\Geocontact\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Redirects legacy /component/geocontact/... URLs to menu-based SEF.
 */
class LegacyRouteHelper
{
    public static function redirectIfNeeded(): void
    {
        if (!self::isLegacyComponentPath()) {
            return;
        }

        $app  = Factory::getApplication();
        $input = $app->getInput();
        $view  = $input->getCmd('view');
        $id    = $input->getInt('id');

        if ($view === 'geocontact' && $id) {
            $itemId = self::getGeocontactsMenuItemId();
            $query  = 'index.php?option=com_geocontact&view=geocontact&id=' . $id;

            if ($itemId) {
                $query .= '&Itemid=' . $itemId;
            }

            $app->redirect(Route::_($query, false, Route::TLS_IGNORE, true), 301);
        }

        if ($view === 'geocontacts' || ($view === '' && $input->getCmd('option') === 'com_geocontact')) {
            $itemId = self::getGeocontactsMenuItemId();
            $query  = 'index.php?option=com_geocontact&view=geocontacts';

            if ($itemId) {
                $query .= '&Itemid=' . $itemId;
            }

            $app->redirect(Route::_($query, false, Route::TLS_IGNORE, true), 301);
        }
    }

    public static function isLegacyComponentPath(): bool
    {
        $path = self::getRequestPath();

        return (bool) preg_match('#^/component/geocontact(/|$)#', $path);
    }

    public static function getGeocontactsMenuItemId(): int
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();
        $menu  = $app->getMenu();
        $itemId = $input->getInt('Itemid');

        if ($itemId) {
            $item = $menu->getItem($itemId);

            if ($item && $item->component === 'com_geocontact' && ($item->query['view'] ?? '') === 'geocontacts') {
                return $itemId;
            }
        }

        foreach ($menu->getItems('component', 'com_geocontact') ?: [] as $item) {
            if (($item->query['view'] ?? '') === 'geocontacts') {
                return (int) $item->id;
            }
        }

        return 0;
    }

    private static function getRequestPath(): string
    {
        $uri  = Uri::getInstance();
        $path = $uri->getPath();
        $base = Uri::base(true);

        if ($base && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        return '/' . trim($path, '/');
    }
}
