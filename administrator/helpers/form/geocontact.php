<?php

/**
 * @version     1.0.0
 * @package     com_geocontact_1.0.0
 * @copyright   Copyright (C) 2018. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */
defined('_JEXEC') or die;

/**
 * Geocontact form
 */
class FormGeocontactGeocontact extends FOFModel {

    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true, $source = NULL) {
        // Get the form
        $form = $this->loadForm('com_geocontact.geocontact', 'geocontact', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Get the field options from the form fields
     *
     * @return  array   $fieldOptions   An array with the field options
     */
    public function getFieldOptions() {
        $form = $this->getForm();

        if ($form) {

            $xmlFieldset = $form->getXml()->fieldset;

            $fieldOptions = array();
            foreach ($xmlFieldset->children() as $field) {
                $fieldColumn = (string) $field['name'];

                foreach ($field->children() as $option) {
                    $key = (string) $option['value'];
                    $value = (string) $option;

                    $fieldOptions[$fieldColumn][$key] = $value;
                }
            }
            return $fieldOptions;
        } else {
            return false;
        }
    }

}
