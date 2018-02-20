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

/**
 * Class GeocontactRouter
 *
 * @since  3.3
 */
class GeocontactRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_geocontact component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL
	 *
	 * @since   3.3
	 */
	public function build(&$query)
	{
		$segments = array();

		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			unset($query['view']);
		}

		if (isset($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		$vars = array();

		// View is always the first element of the array
		$count = count($segments);

		if ($count)
		{
			$segment = array_shift($segments);

			if (is_numeric($segment))
			{
				$vars['id'] = $segment;
			}
			else
			{
				$vars['view'] = $segment;
			}

			$segment = array_shift($segments);

			if (is_numeric($segment))
			{
				$vars['id'] = $segment;
			}
		}

		return $vars;
	}
}
