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

jimport('joomla.application.component.view');

/**
 * Geocontact detail view
 */
class GeocontactViewGeocontact extends JViewLegacy
{
    protected $state;
    protected $item;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
		$app	= JFactory::getApplication();
        $user	= JFactory::getUser();

        $this->state 				= $this->get('State');
        $this->item 				= $this->get('Item');
        $this->pagination           = $this->get('pagination');
        $this->params 				= $app->getParams('com_geocontact');

        // Throw exeption if errors
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }
        
        if($this->_layout == 'edit')
        {
            $authorised = $user->authorise('core.create', 'com_geocontact');

            if ($authorised !== true) {
                throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
            }
        }

        // Load the template header here to simplify the template
        $this->loadTemplateHeader();

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function _prepareDocument()
    {
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        else
        {
            $this->params->def('page_heading', JText::_('COM_GEOCONTACT_DEFAULT_PAGE_TITLE'));
        }

        $title = $this->params->get('page_title', '');

        if (empty($title))
        {
            $title = $app->get('sitename');
        }
        elseif ($app->get('sitename_pagetitles', 0) == 1)
        {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2)
        {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

    /**
     * Load the template header data here to simplify the template
     */
    protected function loadTemplateHeader()
    {
        JHtml::_('jquery.framework');

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_geocontact/assets/css/geocontact.css');
        $document->addScript('components/com_geocontact/assets/js/detail.js');
    }
}
