<?php
/**
 * @version     1.0.0
 * @package     com_geocontact_1.0.0
 * @copyright   Copyright (C) 2018. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Geocontact model
 */
class GeocontactModelGeocontacts extends JModelList {

    /**
     * @var		int		An array with the filtering columns
     */
    protected $filter_fields = null;

    /**
     * Constructor
     *
     * @param    array    		An optional associative array of configuration settings
     *
     * @see      JController
     * @since    1.6
     */
    public function __construct($config = array()) {
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
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables
        $app = JFactory::getApplication('administrator');

        // Load the filter state
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        // List state information
        $value = $app->input->get('limit', $app->get('list_limit', 20), 'uint');
        $this->setState('list.limit', $value);

        $value = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $value);

        // Load the parameters
        $params = JComponentHelper::getParams('com_geocontact');
        $this->setState('params', $params);

        // List state information
        parent::populateState('a.ordering', 'asc');
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        $query = $this->_db->getQuery(true);

        $query->select('a.id, a.description, a.stand');
        $query->select('a.address, a.name, a.phones');
        $query->select('a.latlong, a.caption, a.state');
        $query->select('a.catid, a.ordering');

        $query->from('`#__geocontact_geocontacts` AS a');

        // Join over the categories.
        $query->select('c.title AS category_title')
                ->join('LEFT', ' #__categories AS c' . ' ON c.id = a.catid');


        $query->select('i.name as created_by');
        $query->leftJoin($this->_db->qn('#__users') . ' AS i ON i.id = a.created_by');

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where($this->_db->qn('a.state') . ' = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(' . $this->_db->qn('a.state') . ' IN (0, 1))');
        }

        // Search for this word
        $searchWord = $this->getState('filter.search');

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

        if (!empty($searchWord)) {
            if (stripos($searchWord, 'id:') === 0) {
                // Build the ID search
                $idPart = (int) substr($searchWord, 3);
                $query->where($this->_db->qn('a.id') . ' = ' . $this->_db->q($idPart));
            } else {
                // Build the search query from the search word and search columns
                $query = GeocontactHelpersBackend::buildSearchQuery($searchWord, $searchColumns, $query);
            }
        }

        // Add the list ordering clause
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        if ($orderCol && $orderDirn) {
            $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    /** Method to get an array of data items
     *
     * @return  mixed An array of data on success, false on failure.
     */
    public function getItems() {
        $items = parent::getItems();

        //include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/form/geocontact.php';
        include_once JPATH_ADMINISTRATOR . '/components/com_geocontact/helpers/form/geocontact.php';

        $form = new FormGeocontactGeocontact;

        $fieldOptions = $form->getFieldOptions();

        foreach ($items as $i => $item) {
            foreach ($item as $key => $value) {
                // Don't apply to the state
                if ($key == 'state') {
                    continue;
                }

                // If this field has options
                if (isset($fieldOptions[$key])) {
                    // Update the item key with the field option
                    $item->{$key} = JText::_($fieldOptions[$key][$value]);
                }
            }

            $items[$i] = $item;
        }

        return $items;
    }

    /**
     * Returns a reference to the a Table object, always creating it
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional
     * @param	array	Configuration array for model. Optional
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'geocontact', $prefix = 'GeocontactTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    protected function rus2lat($s) {
        $s = (string) $s; // преобразуем в строковое значение
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

    public function newItems() {
        $xmlfile = JURI::base() . 'components/com_geocontact/towns.xml';
        //$this->towns = JFactory::getXML($xmlfile, true);
        $towns = simplexml_load_file($xmlfile);
        //$this->towns = $this->towns[2]->attributes();
        //print_r($towns);

        foreach ($towns as $town) {
            echo "<p>" . $town->caption . "</p>\n";
            // Получаем экземпляр класса TableGeocontact.
            if ($town->alias == "") {
                $town->alias = $this->rus2lat($town->caption);
            }
            $table = $this->getTable();
            $table->caption = (string) $town->caption;
            $table->alias = (string) $town->alias;
            $table->latlong = (string) $town->latlong;
            $table->phones = (string) $town->phones;
            $table->name = (string) $town->name;
            $table->address = (string) $town->address;
            $table->stand = (string) $town->stand;
            $table->description = (string) $town->description;
            $table->store();
            //$table->publish();
        }

        //$db = JFactory::getDbo();
        //$db->setQuery('SELECT MAX(ordering) FROM #__geocontact_geocontacts');
    }

}
