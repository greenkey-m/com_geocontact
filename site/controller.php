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

jimport('joomla.application.component.controller');

class GeocontactController extends JControllerLegacy
{
    /**
     * Method to display a view
     *
     * @param   bool  $cachable   If true, the view output will be cached
     * @param   bool  $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}
     *
     * @return  JControllerLegacy This object to support chaining
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        return parent::display($cachable, $urlparams);
    }
}
