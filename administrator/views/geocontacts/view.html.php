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
 * Geocontact list view
 */
class GeocontactViewGeocontacts extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $towns;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->user = JFactory::getUser();
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        GeocontactHelpersBackend::addSubmenu('geocontacts');

        $this->addToolbar();

        $this->sortFields = $this->getSortFields();

        $this->sidebar = JHtmlSidebar::render();

        // Load the template header here to simplify the template
        $this->loadTemplateHeader();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/backend.php';

        $state = $this->get('State');
        $canDo = GeocontactHelpersBackend::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_GEOCONTACT_TITLE_GEOCONTACTS'), 'geocontacts.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/geocontact';
        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('geocontact.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('geocontact.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {
            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('geocontacts.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('geocontacts.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'geocontacts.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('geocontacts.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('geocontacts.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'geocontacts.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('geocontacts.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_geocontact');
            $doc = JFactory::getDocument();
            $doc->addStyleDeclaration('#toolbar-upload{float:right;} #toolbar-download{float:right;}');
            JToolBarHelper::custom('geocontacts.uploadxml', 'upload.png', 'upload_f2.png', 'JTOOLBAR_UPLOADXML', false);
            JToolBarHelper::custom('geocontacts.downloadxml', 'download.png', 'download_f2.png', 'JTOOLBAR_DOWNLOADXML', true);
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_geocontact&view=geocontacts');

        $this->extra_sidebar = '';

        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
        );
    }

    /**
     * Get the fields for sorting
     *
     * @return	$sortFields		array	An array with the sort fields
     */
    protected function getSortFields() {
        $sortFields = array(
            'a.id' => JText::_('COM_GEOCONTACT_HEADING_BACKEND_LIST_ID'),
            'a.description' => JText::_('COM_GEOCONTACT_GEOCONTACT_DESCRIPTION_LBL'),
            'a.stand' => JText::_('COM_GEOCONTACT_GEOCONTACT_STAND_LBL'),
            'a.address' => JText::_('COM_GEOCONTACT_GEOCONTACT_ADDRESS_LBL'),
            'a.name' => JText::_('COM_GEOCONTACT_GEOCONTACT_NAME_LBL'),
            'a.phones' => JText::_('COM_GEOCONTACT_GEOCONTACT_PHONES_LBL'),
            'a.latlong' => JText::_('COM_GEOCONTACT_GEOCONTACT_LATLONG_LBL'),
            'a.caption' => JText::_('COM_GEOCONTACT_GEOCONTACT_CAPTION_LBL'),
            'a.created_by' => JText::_('COM_GEOCONTACT_GEOCONTACT_CREATED_BY_LBL'),
            'a.state' => JText::_('COM_GEOCONTACT_GEOCONTACT_STATE_LBL'),
            'a.ordering' => JText::_('COM_GEOCONTACT_GEOCONTACT_ORDERING_LBL'),
        );

        return $sortFields;
    }

    /**
     * Load the template header data here to simplify the template
     */
    protected function loadTemplateHeader() {
        JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_geocontact/assets/css/geocontact.css');
        $document->addScript('components/com_geocontact/assets/js/list.js');

        $user = JFactory::getUser();
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn = $this->escape($this->state->get('list.direction'));
        $user->authorise('core.edit.state', 'com_geocontact.category');
        $saveOrder = $this->listOrder == 'a.ordering';

        if ($saveOrder) {
            $saveOrderingUrl = 'index.php?option=com_geocontact&task=geocontacts.saveOrderAjax&tmpl=component';
            JHtml::_('sortablelist.sortable', 'geocontactList', 'adminForm', strtolower($this->listDirn), $saveOrderingUrl);
        }

        $this->saveOrder = $saveOrder;
    }

}
