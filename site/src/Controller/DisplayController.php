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

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Base controller class
 *
 * @since  3.1
 */
class DisplayController extends BaseController
{
    /**
     * Method to display a view
     *
     * @param boolean $cachable If true, the view output will be cached
     * @param mixed|boolean $urlparams An array of safe URL parameters and their
     *                                     variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return DisplayController
     *
     * @throws Exception
     * @since   3.1
     */
	public function display($cachable = false, $urlparams = false)
	{
		$user = Factory::getApplication()->getIdentity();

		// Set the default view name and format from the Request
		$vName = $this->input->get('view', 'Geocontacts');
		$this->input->set('view', $vName);

		if ($user->get('id') || ($this->input->getMethod() === 'POST' && $vName === 'Geocontacts')) {
			$cachable = false;
		}

		$safeurlparams = array(
			'id'               => 'ARRAY',
			'type'             => 'ARRAY',
			'limit'            => 'UINT',
			'limitstart'       => 'UINT',
			'filter_order'     => 'CMD',
			'filter_order_Dir' => 'CMD',
			'lang'             => 'CMD'
		);

        Factory::getApplication()->getLanguage()->load('com_geocontact', JPATH_ADMINISTRATOR, 'en-GB', true);

		parent::display($cachable, $safeurlparams);
        return $this;
	}
}
