<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Greenkey\Component\Geocontact\Administrator\Helper\FormHelper;
use Greenkey\Component\Geocontact\Administrator\Helper\GeocontactHelper;
use Joomla\CMS\Form\Form;
use RuntimeException;

/**
 * Geocontact model
 */
class GeocontactsModel extends ListModel
{
    /**
     * @var        array        An array with the filtering columns
     */
    protected $filter_fields;

    /**
     * Constructor
     *
     * @param array            An optional associative array of configuration settings
     *
     * @see      JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'description', 'a.description',
                'stand', 'a.stand',
                'address', 'a.address',
                'name', 'a.name',
                'phones', 'a.phones',
                'latlong', 'a.latlong',
                'caption', 'a.caption',
                'created_by', 'a.created_by',
                'state', 'a.state',
                'ordering', 'a.ordering',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state
     *
     * Note. Calling getState in this method will result in recursion
     *
     * @param null $ordering
     * @param null $direction
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables
        $app = Factory::getApplication('administrator');

        // Load the filter state
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'int');
        $this->setState('filter.state', $published);

        // List state information
        $value = $app->input->get('limit', $app->get('list_limit', 20), 'uint');
        $this->setState('list.limit', $value);

        $value = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $value);

        // Load the parameters
        $params = ComponentHelper::getParams('com_geocontact');
        $this->setState('params', $params);

        // List state information
        parent::populateState('a.ordering', 'asc');
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    DatabaseQuery
     * @since    1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.id, a.description, a.stand');
        $query->select('a.address, a.name, a.phones');
        $query->select('a.latlong, a.caption, a.state');
        $query->select('a.ordering');
        $query->select('c.title AS `category_title` ');

        $query->from($db->quoteName('#__geocontact_geocontacts', 'a'));

        $query->select('i.name AS `created_by`');
        $query->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid');
        $query->leftJoin($db->quoteName('#__users', 'i') . ' ON i.id = a.created_by');

        // Filter by published state
        $state = $this->getState('filter.published');

        if (is_numeric($state)) {
            $query->where('a.state = ' . (int)$state);
        } elseif ($state !== '*') {
            $query->where('(a.state IN (0, 1))');
        }

        // Search for this word
        $searchPhrase = $this->getState('filter.search');

        // Search in these columns
        $searchColumns = array(
            'a.description',
            'a.stand',
            'a.address',
            'a.name',
            'a.phones',
            'a.latlong',
            'a.caption',
            'i.name',
        );

        if (!empty($searchPhrase)) {
            if (stripos($searchPhrase, 'id:') === 0) {
                // Build the ID search
                $idPart = (int)substr($searchPhrase, 3);
                $query->where($db->quoteName('a.id') . ' = ' . $db->quote($idPart));
            } else {
                // Build the search query from the search word and search columns
                $query = GeocontactHelper::buildSearchQuery($searchPhrase, $searchColumns, $query);
            }
        }

        $query->group($db->quoteName('a.id'));

        // Add the list ordering clause
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    /**
     * Получить операции, выполняемые над записями
     *
     * @return  array|boolean
     */
    public function getTransitions()
    {
        // Get a storage key.
        $store = $this->getStoreId('getTransitions');

        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }

        $db   = $this->getDatabase();
        $user = $this->getCurrentUser();

        $items = $this->getItems();

        if ($items === false) {
            return false;
        }
        return [];
    }

    /**
     * Method to get an array of data items
     *
     * @return  mixed An array of data on success, false on failure.
     */
    public function getItems()
    {
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_geocontact/forms');
        $form = $this->loadForm('com_geocontact.geocontact', 'geocontact', [
            'control' => 'jform',
            'load_data' => true
        ]);
        $formHelper = new FormHelper($form);
        return $formHelper->appendFieldOptions(parent::getItems())->getAll();
    }

    protected function rus2lat($s)
    {
        $s = (string)$s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        return $s; // возвращаем результат return iconv("UTF-8","UTF-8//IGNORE", $s);
    }

    public function loadItems(): int
    {
        $xmlPath = JPATH_ADMINISTRATOR . '/components/com_geocontact/towns.xml';

        if (!is_file($xmlPath)) {
            throw new RuntimeException(Text::_('COM_GEOCONTACT_ERROR_TOWNS_XML_NOT_FOUND'));
        }

        $towns = simplexml_load_file($xmlPath);

        if ($towns === false) {
            throw new RuntimeException(Text::_('COM_GEOCONTACT_ERROR_TOWNS_XML_INVALID'));
        }

        $categories   = Categories::getInstance('com_geocontact', ['countItems' => false]);
        $rootNode     = $categories->get();
        $categoryNodes = $rootNode ? $rootNode->getChildren() : [];
        $regions      = [];

        foreach ($categoryNodes as $node) {
            $regions[(string) $node->title] = (int) $node->id;
        }

        $user  = Factory::getApplication()->getIdentity();
        $count = 0;

        foreach ($towns as $town) {
            $table = $this->getTable('Geocontact');
            $alias = trim((string) $town->alias);

            if ($alias === '') {
                $alias = $this->rus2lat((string) $town->caption);
            }

            $table->caption     = (string) $town->caption;
            $table->alias       = $alias;
            $table->latlong     = (string) $town->latlong;
            $table->phones      = (string) $town->phones;
            $table->name        = (string) $town->name;
            $table->address     = (string) $town->address;
            $table->stand       = (string) $town->stand;
            $table->description = (string) $town->description;
            $table->state       = 1;
            $table->created_by  = (int) $user->id;

            $region = (string) $town['region'];

            if ($region !== '' && isset($regions[$region])) {
                $table->catid = $regions[$region];
            }

            if (!$table->store()) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    public function saveItems(): bool
    {
        return false;
    }
}
