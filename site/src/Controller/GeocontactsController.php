<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Joomla\Component\Geocontact\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Geocontact list controller
 */
class GeocontactsController extends BaseController
{
	/**
	 * Proxy for getModel.
	 * @since    1.6
	 *
	 * @param string $name
	 * @param string $prefix
	 *
	 * @return mixed
	 */
	public function &getModel($name = 'geocontact', $prefix = 'Administrator')
	{
		return parent::getModel($name, $prefix, ['ignore_request' => true]);
	}
}
