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

jimport('joomla.application.component.controllerform');

/**
 * Geocontact detail controller
 */
class GeocontactControllerGeocontact extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'geocontacts';
        parent::__construct();
    }
}
