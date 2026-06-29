<?php
/**
 * @package     com_geocontact
 * @version     6.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

/**
 * The form field implementation
 */
class CreatedbyField extends ListField
{
    protected $type = 'createdby';

    protected function getInput()
    {
        $user        = Factory::getApplication()->getIdentity();
        $userExists  = true;

        if ($this->value) {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $this->value, \Joomla\Database\ParameterType::INTEGER);
            $db->setQuery($query);
            $userId = $db->loadResult();

            if ($userId) {
                $user = Factory::getUser($this->value);
            } else {
                $userExists  = false;
                $this->value = $user->id;
            }
        } else {
            $this->value = $user->id;
        }

        $html = '';

        if ($userExists) {
            $html = $user->name . ' (' . $user->username . ')';
        }

        $html .= '<input type="hidden" name="' . $this->name . '" value="' . (int) $this->value . '">';

        return $html;
    }
}
