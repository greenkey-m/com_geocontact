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

// Register class prefix
JLoader::registerPrefix('Geocontact', JPATH_COMPONENT);

// Load the controller
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Geocontact');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
