<?php

/**
 * @version     1.0.0
 * @package     com_geocontact_1.0.0
 * @copyright   Copyright (C) 2018. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Class GeocontactRouter
 *
 * @since  3.3
 */
class GeocontactRouter extends JComponentRouterBase {

    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $id     ID of the article to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    protected function getAlias($id) {
        $db = JFactory::getDbo();
        $dbquery = $db->getQuery(true);
        $dbquery->select("a.alias AS alias, a.catid AS catid")
                ->from('`#__geocontact_geocontacts` AS a')
                ->where('a.id = ' . $id)
                ->select('c.alias AS catalias')
                ->join('LEFT', ' #__categories AS c' . ' ON c.id = a.catid');
        $db->setQuery($dbquery);

        //$category = JCategories::getInstance('geocontact')->get($id);
        //print_r($category);

        $alias = $db->loadRow();
        //print_r($alias[0]);

        if ($alias) {
            return $alias;
        } else {
            return false;
        };
    }

    /**
     * Method to get the id for an article
     *
     * @param   string  $segment  Segment of the article to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    protected function getArticleId($segment) {
        $db = JFactory::getDbo();
        $dbquery = $db->getQuery(true);
        $dbquery->select($dbquery->qn('id'))
                ->from($dbquery->qn('#__geocontact_geocontacts'))
                ->where('alias = ' . $dbquery->q($segment));
        $db->setQuery($dbquery);

        return (int) $db->loadResult();
    }

    /**
     * Build the route for the com_geocontact component
     *
     * @param   array  &$query  An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL
     *
     * @since   3.3
     */
    public function build(&$query) {
        $segments = array();

        //print_r($query);
        //unset($query['option']);
        //$segments[] = "geocontacts";

        if (isset($query['view'])) {
            //$segments[] = $query['view'];
            unset($query['view']);
        }

        if (isset($query['id'])) {
            $segments[] = $this->getAlias($query['id'])[2];
            $segments[] = $this->getAlias($query['id'])[0];
            unset($query['id']);
        }

        //print_r($query);
        //print_r($segments);

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array  &$segments  The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     *
     * @since   3.3
     */
    public function parse(&$segments) {
        $vars = array();

        // View is always the first element of the array
        $count = count($segments);
        //print_r($segments);

        $vars['view'] = "geocontact";

        if ($count > 1) {
            $segment = array_shift($segments);
        }

        $segment = array_shift($segments);
        $vars['id'] = (int)$this->getArticleId($segment);

        //print_r($vars);

        return $vars;
    }

}
