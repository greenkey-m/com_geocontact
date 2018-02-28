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

jimport('joomla.application.component.controlleradmin');

/**
 * Geocontact list controller
 */
class GeocontactControllerGeocontacts extends JControllerAdmin {

    /**
     * Proxy for getModel
     * @since	1.6
     */
    public function getModel($name = 'geocontact', $prefix = 'GeocontactModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax() {
        // Get the input
        $input = JFactory::getApplication()->input;
        $pks = $input->post->get('cid', array(), 'array');
        $order = $input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return) {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    public function uploadxml() {
        // Get the model of Get Contacts
        $model = $this->getModel('geocontacts', 'GeocontactModel');

        // Create new items in DB
        $model->newItems();
    }

    public function downloadxml() {

    }

}
