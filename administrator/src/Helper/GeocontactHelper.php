<?php
/**
 * @package     com_geocontact
 * @version     6.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;

/**
 * Geocontact helper class
 */
class GeocontactHelper
{
    /**
     * Gets a list of the actions that can be performed
     *
     * @return array<string, bool>
     */
    public static function getActions($id = 0): array
    {
        $user   = Factory::getApplication()->getIdentity();
        $result = [];

        $assetName = 'com_geocontact';

        $actions = [
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.edit.own', 'core.delete',
        ];

        foreach ($actions as $action) {
            $result[$action] = $user->authorise($action, $assetName);
        }

        return $result;
    }

    /**
     * Build the search query from the columns
     *
     * @param string         $searchPhrase  Search for this phrase
     * @param array          $searchColumns The columns in the DB to look up
     * @param QueryInterface $query         The query
     *
     * @return QueryInterface
     */
    public static function buildSearchQuery(string $searchPhrase, array $searchColumns, QueryInterface $query): QueryInterface
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $where = [];

        foreach ($searchColumns as $searchColumn) {
            $where[] = $db->quoteName($searchColumn) . ' LIKE ' . $db->quote('%' . $db->escape($searchPhrase, true) . '%');
        }

        if (!empty($where)) {
            $query->where('(' . implode(' OR ', $where) . ')');
        }

        return $query;
    }

    public static function convertStrftimeToDateTimeFormat(string $format): string
    {
        $replacements = [
            '%a' => 'D', '%A' => 'l', '%d' => 'd', '%e' => 'j', '%j' => 'z',
            '%u' => 'N', '%w' => 'w', '%U' => 'W', '%V' => 'W', '%W' => 'W',
            '%b' => 'M', '%B' => 'F', '%m' => 'm', '%C' => 'y', '%g' => 'y',
            '%G' => 'o', '%y' => 'y', '%Y' => 'Y', '%H' => 'H', '%I' => 'h',
            '%l' => 'g', '%M' => 'i', '%p' => 'A', '%P' => 'a', '%r' => 'h:i:s A',
            '%R' => 'H:i', '%S' => 's', '%T' => 'H:i:s', '%X' => 'H:i:s', '%z' => 'O',
            '%Z' => 'T', '%%' => '%',
        ];

        return strtr($format, $replacements);
    }

    public static function convertFromStrftimeFormat(string $value, string $strftimeFormat): string
    {
        $phpFormat = self::convertStrftimeToDateTimeFormat($strftimeFormat);
        $datetime  = \DateTime::createFromFormat($phpFormat, $value);

        if (!$datetime) {
            return '';
        }

        return $datetime->format($phpFormat);
    }
}
