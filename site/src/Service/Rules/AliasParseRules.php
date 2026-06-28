<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\Service\Rules;

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Parses category-alias/item-alias SEF segments.
 */
class AliasParseRules implements RulesInterface
{
    public function __construct(private DatabaseInterface $db)
    {
    }

    public function preprocess(&$query): void
    {
    }

    public function parse(&$segments, &$vars): void
    {
        if (empty($segments) || isset($vars['id'])) {
            return;
        }

        $itemAlias = array_pop($segments);

        if ($itemAlias === null || $itemAlias === '') {
            return;
        }

        $item = $this->getItemByAlias((string) $itemAlias);

        if (!$item && is_numeric($itemAlias)) {
            $item = $this->getItemById((int) $itemAlias);
        }

        if (!$item) {
            $segments[] = $itemAlias;

            return;
        }

        $vars['view']  = 'geocontact';
        $vars['id']    = (int) $item->id;
        $vars['catid'] = (int) $item->catid;
        $segments      = [];
    }

    public function build(&$query, &$segments): void
    {
    }

    private function getItemByAlias(string $alias): ?object
    {
        $query = $this->db->getQuery(true);
        $query->select($this->db->quoteName(['id', 'catid', 'alias']))
            ->from($this->db->quoteName('#__geocontact_geocontacts'))
            ->where($this->db->quoteName('alias') . ' = :alias')
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':alias', $alias, ParameterType::STRING);

        return $this->db->setQuery($query)->loadObject() ?: null;
    }

    private function getItemById(int $id): ?object
    {
        $query = $this->db->getQuery(true);
        $query->select($this->db->quoteName(['id', 'catid', 'alias']))
            ->from($this->db->quoteName('#__geocontact_geocontacts'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':id', $id, ParameterType::INTEGER);

        return $this->db->setQuery($query)->loadObject() ?: null;
    }
}
