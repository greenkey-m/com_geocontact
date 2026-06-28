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

use Greenkey\Component\Geocontact\Site\Service\Router;
use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Builds category-alias/item-alias SEF segments.
 */
class AliasBuildRules implements RulesInterface
{
    public function __construct(
        private Router $router,
        private DatabaseInterface $db
    ) {
    }

    public function preprocess(&$query): void
    {
    }

    public function parse(&$segments, &$vars): void
    {
    }

    public function build(&$query, &$segments): void
    {
        if (($query['view'] ?? '') !== 'geocontact' || empty($query['id'])) {
            return;
        }

        $itemId = (int) $query['id'];
        $item   = $this->getItemById($itemId);

        if (!$item || empty($item->alias)) {
            return;
        }

        if (!empty($item->catid)) {
            $category = $this->router->getCategories(['access' => false])->get($item->catid);

            if ($category && $category->alias) {
                $segments[] = $category->alias;
            }
        }

        $segments[] = $item->alias;

        unset($query['id'], $query['catid'], $query['view']);
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
