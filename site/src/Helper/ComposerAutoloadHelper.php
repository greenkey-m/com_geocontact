<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\Helper;

defined('_JEXEC') or die;

/**
 * Loads Composer autoloader shipped with the component.
 */
class ComposerAutoloadHelper
{
    private static bool $loaded = false;

    public static function register(): void
    {
        if (self::$loaded) {
            return;
        }

        $paths = [];

        if (defined('JPATH_ADMINISTRATOR')) {
            $paths[] = JPATH_ADMINISTRATOR . '/components/com_geocontact/vendor/autoload.php';

            $adminComponent = realpath(JPATH_ADMINISTRATOR . '/components/com_geocontact');

            if ($adminComponent) {
                $paths[] = dirname($adminComponent) . '/vendor/autoload.php';
            }
        }

        $paths[] = dirname(__DIR__, 3) . '/vendor/autoload.php';

        foreach (array_unique($paths) as $path) {
            if (is_file($path)) {
                require_once $path;
                self::$loaded = true;

                return;
            }
        }
    }
}
