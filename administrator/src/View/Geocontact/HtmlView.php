<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\View\Geocontact;

// No direct access
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Greenkey\Component\Geocontact\Administrator\Helper\GeocontactHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Geocontact detail view
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The Form object
	 *
	 * @var    Form
	 * @since  1.5
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();

		if (count($errors = $this->get('Errors')))
		{
            throw new Exception(implode("\n", $errors));
		}

        $document = Factory::getDocument();
		$wa = $document->getWebAssetManager();
		$wa->registerAndUseStyle('my-style', 'components/com_geocontact/assets/css/geocontact.css');
		$wa->registerAndUseScript('my-script', 'components/com_geocontact/assets/js/detail.js');

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user		= Factory::getApplication()->getIdentity();
		$isNew		= ((int) $this->item->id === 0);
        $app        = Factory::getApplication();

        if (isset($this->item->checked_out))
		{
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
        }
		else
		{
            $checkedOut = false;
        }

		$canDo = GeocontactHelper::getActions();
        $title = Text::_('COM_GEOCONTACT_TITLE_GEOCONTACT');

		ToolbarHelper::title($title, 'map-marker');
		$app->getDocument()->setTitle(
            strip_tags($title) . ' - ' . $app->get('sitename') . ' - ' . Text::_('JADMINISTRATION')
        );

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo['core.edit'] || ($canDo['core.create'])))
		{
			ToolbarHelper::apply('geocontact.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('geocontact.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo['core.create']))
		{
			ToolbarHelper::custom('geocontact.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo['core.create'])
		{
			ToolbarHelper::custom('geocontact.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('geocontact.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::cancel('geocontact.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
