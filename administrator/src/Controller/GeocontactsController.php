<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\Controller;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Geocontact list controller
 */
class GeocontactsController extends AdminController
{
	/**
	 * Proxy for getModel
	 * @since    1.6
	 *
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 *
	 * @return bool
	 */
	public function getModel($name = 'Geocontact', $prefix = 'Administrator', $config = [])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => true]);
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
