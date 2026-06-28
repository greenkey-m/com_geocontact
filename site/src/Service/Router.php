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

use Greenkey\Component\Geocontact\Site\Service\Rules\AliasBuildRules;
use Greenkey\Component\Geocontact\Site\Service\Rules\AliasParseRules;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\PreprocessRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
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

    private DatabaseInterface $db;

    /**
     * @param SiteApplication $app The application object
     * @param AbstractMenu $menu The menu object to work with
     */
    public function __construct(
        SiteApplication $app,
        AbstractMenu $menu,
        CategoryFactoryInterface $categoryFactory,
        DatabaseInterface $db
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->db              = $db;

        $geocontacts = new RouterViewConfiguration('geocontacts');
        $this->registerView($geocontacts);

        $geocontact = new RouterViewConfiguration('geocontact');
        $geocontact->setKey('id')->setParent($geocontacts);
        $this->registerView($geocontact);

        parent::__construct($app, $menu);

        $preprocess = new PreprocessRules($geocontact, '#__geocontact_geocontacts', 'id', 'catid');
        $preprocess->setDatabase($this->db);
        $this->attachRule($preprocess);
        $this->attachRule(new AliasBuildRules($this, $this->db));
        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
        $this->attachRule(new AliasParseRules($this->db));
    }

    /**
     * @return CategoryFactoryInterface
     */
    public function getCategoriesFactory(): CategoryFactoryInterface
    {
        return $this->categoryFactory;
    }

    /**
     * Method to get categories
     *
     * @param   array  $options  An array of options
     *
     * @return  \Joomla\CMS\Categories\Categories
     */
    public function getCategories(array $options = []): \Joomla\CMS\Categories\Categories
    {
        return $this->categoryFactory->createCategory($options);
    }

    /**
     * Method to get the segment(s) for a geocontact item
     *
     * @param   string  $id     ID of the item
     * @param   array   $query  The request that is built right now
     *
     * @return  array
     */
    public function getGeocontactSegment($id, $query): array
    {
        $itemId = (int) $id;
        $query  = $this->db->getQuery(true);
        $query->select($this->db->quoteName('alias'))
            ->from($this->db->quoteName('#__geocontact_geocontacts'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $itemId, ParameterType::INTEGER);
        $alias = $this->db->setQuery($query)->loadResult();

        if ($alias) {
            return [$itemId => $alias];
        }

        return [$itemId => $itemId];
    }

    /**
     * Method to get the id for a geocontact segment
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  int|false
     */
    public function getGeocontactId($segment, $query)
    {
        if (is_numeric($segment)) {
            return (int) $segment;
        }

        $alias = (string) $segment;
        $dbQuery = $this->db->getQuery(true);
        $dbQuery->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__geocontact_geocontacts'))
            ->where($this->db->quoteName('alias') . ' = :alias')
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':alias', $alias, ParameterType::STRING);
        $id = $this->db->setQuery($dbQuery)->loadResult();

        return $id ? (int) $id : false;
    }
}
