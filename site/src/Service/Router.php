<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Routing class for the com_geocontact component
 *
 * @since  3.3
 */
class Router extends RouterView
{
    private CategoryFactoryInterface $categoryFactory;

    /**
     * @param SiteApplication $app The application object
     * @param AbstractMenu $menu The menu object to work with
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;

        $this->registerView(new RouterViewConfiguration('geocontacts'));
        $this->registerView(new RouterViewConfiguration('geocontact'));

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Build the route for the com_geocontact component
     *
     * @param array  &$query An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL
     *
     * @since   3.3
     */
    public function build(&$query)
    {
        $segments = array();

        if (isset($query['id'], $query['catid'])) {
            // Get DB
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            // Get item info
            $itemId = (int)$query['id'];
            $item = $db->setQuery('SELECT alias, catid FROM #__geocontact_geocontacts WHERE id = ' . $itemId)->loadObject();

            //$sample = $this->categories->get($item->category_id);

            if ($item) {
                // Get category alias using CategoryFactory
                $options = ['access' => false];
                $categories = $this->categoryFactory->createCategory($options);
                $category = $categories->get($item->catid);
                $catAlias = $category->alias ?? '';
                // Add category alias and item alias
                $segments[] = $catAlias;
                $segments[] = $item->alias;
            }
            unset($query['id'], $query['catid']);
        }

        if (isset($query['view'])) {
            unset($query['view']);
        }
        unset($query['Itemid']);
        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param array  &$segments The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     *
     * @since   3.3
     */
    public function parse(&$segments)
    {
        $vars = array();

        // View is always the first element of the array
        $count = count($segments);

        if ($count) {
            $segment = array_shift($segments);

            if (is_numeric($segment)) {
                $vars['id'] = $segment;
            } else {
                $vars['view'] = "geocontact";
            }

            $segment = array_shift($segments);

            if (is_numeric($segment)) {
                $vars['id'] = $segment;
            } else {
                $db = Factory::getContainer()->get(DatabaseInterface::class);
                // Get item info
                $alias = (string)$segment;

                $dbquery = $db->getQuery(true);
                $dbquery->select('id, catid')
                    ->from('#__geocontact_geocontacts')
                    ->where($dbquery->quoteName('alias') . ' = :a')
                    ->bind(':a', $alias, ParameterType::STRING);
                $item = $db->setQuery($dbquery)->loadObject();
                $vars['id'] = $item->id;
                $vars['catid'] = $item->catid;
            }
        }

        return $vars;
    }
}
