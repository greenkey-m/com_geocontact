<?php
/**
 * @version     1.0.0
 * @package     com_geocontact_1.0.0
 * @copyright   Copyright (C) 2018. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * The form field implementation
 */
class JFormFieldForeignKey extends JFormField
{
    /**
     * The form field type
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'foreignkey';

    /**
     * Method to get the field input markup
     *
     * @return	string	The field input markup
     * @since	1.6
     */
    protected function getInput()
    {
        $db = JFactory::getDbo();

        // Define the attributes to load
        $attributesToLoad = array(
            'table',
            'key',
            'value'
        );

        $attributes = array();
        foreach ($attributesToLoad as $attributeKey)
        {
            $attributes[$attributeKey] = (string) $this->getAttribute($attributeKey);
        }

        // Select key and value from the table
        $query = $db->getQuery(true)
            ->select($attributes['key'] . ', ' . $attributes['value'])
            ->from($db->qn($attributes['table']))
            ->group($db->qn($attributes['value']))
            ->order($db->qn($attributes['value']));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        $options = array();

        if (!empty($rows))
        {
            foreach ($rows as $row) {
                // Add each select option
                $options[] = JHtml::_('select.option', $row[$attributes['key']], $row[$attributes['value']]);
            }
        }

        $html = JHtml::_('select.genericlist', $options, $this->name, '', 'value', 'text', $this->value);

        return $html;
    }
}
