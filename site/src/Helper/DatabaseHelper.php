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

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;

/**
 * Geocontact database helper
 */
class DatabaseHelper
{
	/**
	 * Build the search query from the columns
	 *
	 * @param	string		        $searchPhrase	    Search for this phrase
	 * @param	array		        $searchColumns	    The columns in the DB to look up
	 * @param   MysqliQuery         $query              The query
	 *
	 * @return	MysqliQuery		    $query			    The query (search filters applied)
	 */
    public static function buildSearchQuery(string $searchPhrase, array $searchColumns, QueryInterface $query): QueryInterface
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $where = [];

        foreach ($searchColumns as $i => $searchColumn) {
            $where[] = $db->qn($searchColumn) . ' LIKE ' . $db->q('%' . $db->escape($searchPhrase, true) . '%');
        }

        if (!empty($where)) {
	        $query->where('(' . implode(' OR ', $where) . ')');
        }

        return $query;
    }
}
