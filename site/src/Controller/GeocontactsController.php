<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Geocontact list controller
 * @since 1.0
 */
class GeocontactsController extends BaseController
{
	/**
	 * Proxy for getModel.
     * @param string $name
     * @param string $prefix
     * @param array $config * @return mixed
	 *@since    1.6
	 */
	public function getModel($name = 'geocontact', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
		return parent::getModel($name, $prefix, $config);
	}
}
