<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\Model;

// No direct access
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Greenkey\Component\Geocontact\Administrator\Helper\FormHelper;
use Greenkey\Component\Geocontact\Site\Helper\DatetimeHelper;
use RuntimeException;

/**
 * Geocontact detail model
 * @since 5.0.0
 */
class GeocontactModel extends FormModel
{
	/**
	 * The item to hold data
     *
	 * @since 5.0.0
	 * @return object
	 */
    protected object $_item;

    /**
     * @since 5.0.0
     * @return void
     * @throws Exception
     */
    private function fetchItem()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.id, a.description, a.stand');
		$query->select('a.address, a.name, a.phones');
		$query->select('a.latlong, a.caption, a.state');
		$query->select('a.ordering');

        $query->from('#__geocontact_geocontacts as a');

        		$query->select('i.name AS `created_by`');
		$query->leftJoin($this->_db->qn('#__users') . ' AS `i` ON i.id = a.created_by');

        $query->where($db->qn('a.id') . ' = ' . $db->q($this->getId()));
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }

        $this->_item = $db->loadObject();
    }

    /**
     * @since 5.0.0
     * @return int
     * @throws Exception
     */
    private function getId(): int
    {
        if (!$app = Factory::getApplication()) {
            throw new RuntimeException('Error app');
        }

        $id = $app->input->getInt('id');
        $params = $app->getParams();

        $paramId = $params->get('id');
        if ($paramId && $id === null) {
            return (int)$paramId;
        }

        return $id;
    }

    /**
     * Get the data
     *
     * @param null $pk
     *
     * @return  object
     *
     * @throws Exception
     * @since   1.6
     */
	public function getItem($pk = null): object
    {
		if (isset($this->_item)) {
			return $this->_item;
		}

        $this->fetchItem();

        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_geocontact/forms');
        $form = $this->loadForm('com_geocontact.geocontact', 'geocontact', [
            'control' => 'jform',
            'load_data' => true
        ]);
        $formHelper = new FormHelper($form);
        return $formHelper->appendFieldOptions([$this->_item])->getOne();
	}

    /**
     * Method to get the form.
     *
     * The base form is loaded from XML
     *
     * @param array $data An optional array of data for the form to interogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return false|Form A JForm object on success, false on failure
     * @throws Exception
     * @since    1.6
     */
    public function getForm($data = [], $loadData = true): false|Form
    {
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_geocontact/forms');

        if (!$app = Factory::getApplication()) {
            throw new RuntimeException('Error app');
        }
        $id = $app->input->getInt('id');
        $params = $app->getParams();
        $paramId = $params->get('id');
        if ($paramId && !$id) {
            $id = $paramId;
        }
        if (empty($id)) {
            $loadData = false;
        }

        // Get the form
        $form = $this->loadForm('com_geocontact.geocontact', 'geocontact', ['control' => 'jform', 'load_data' => $loadData]);
        if (empty($form)) {
            return false;
        }

        return $form;
    }
}
