<?php

/**
 * @version     1.0.0
 * @package     com_geocontact_1.0.0
 * @copyright   Copyright (C) 2018. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * Geocontact Form Field class for the Geocontact component
 *
 * @since  0.0.1
 */
class JFormFieldGeocontact extends JFormFieldList {

    /**
     * The field type.
     *
     * @var         string
     */
    protected $type = 'Geocontact';

    /**
     * Method to get a list of options for a list input.
     *
     * @return  array  An array of JHtml options.
     */
    protected function getOptions() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('#__geocontact.id as id, caption, #__categories.title as category, catid');
        $query->from('#__geocontact');
        $query->leftJoin('#__categories on catid=#__categories.id');
        // Retrieve only published items
        $query->where('#__geocontact.published = 1');
        $db->setQuery((string) $query);
        $messages = $db->loadObjectList();
        $options = array();

        if ($messages) {
            foreach ($messages as $message) {
                $options[] = JHtml::_('select.option', $message->id, $message->caption .
                                ($message->catid ? ' (' . $message->category . ')' : ''));
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
