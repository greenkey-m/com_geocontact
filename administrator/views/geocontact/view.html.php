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
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Load the component params
		$this->component_params = JComponentHelper::getParams('com_geocontact');

		$this->state	= $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

		// Throw exeption if errors
		if (count($errors = $this->get('Errors')))
		{
            throw new Exception(implode("\n", $errors));
		}

		// Load the template header here to simplify the template
		$this->loadTemplateHeader();

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);

        if (isset($this->item->checked_out))
		{
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }
		else
		{
            $checkedOut = false;
        }

		$canDo = GeocontactHelpersBackend::getActions();

		JToolBarHelper::title(JText::_('COM_GEOCONTACT_TITLE_GEOCONTACT'), 'geocontacts.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('geocontact.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('geocontact.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			JToolBarHelper::custom('geocontact.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolBarHelper::custom('geocontact.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('geocontact.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('geocontact.cancel', 'JTOOLBAR_CLOSE');
		}

	}

	/**
	 * Load the template header data here to simplify the template
	 */
	protected function loadTemplateHeader()
	{
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
		JHtml::_('behavior.tooltip');
		JHtml::_('behavior.formvalidation');
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('behavior.keepalive');
		JHTML::_('behavior.modal');

		// Import CSS
		$document = JFactory::getDocument();
		$document->addStyleSheet('components/com_geocontact/assets/css/geocontact.css');
		$document->addScript('components/com_geocontact/assets/js/detail.js');
	}
}
