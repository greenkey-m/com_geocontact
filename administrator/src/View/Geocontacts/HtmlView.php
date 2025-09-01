<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\View\Geocontacts;

// No direct access
defined('_JEXEC') or die;

use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use JUser;
use Form;
use Exception;
use JHtmlSidebar;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Greenkey\Component\Geocontact\Administrator\Helper\GeocontactHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;

/**
 * Geocontact list view
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The Joomla user object
     *
     * @var JUser
     */
    protected $user;

    /**
     * The search tools form
     *
     * @var    Form
     * @since  1.6
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  1.6
     */
    public $activeFilters = [];

    /**
     * An array of items
     *
     * @var    array
     * @since  1.6
     */
    protected $items = [];

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  1.6
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    Registry
     * @since  1.6
     */
    protected $state;

    /**
     * @var bool
     */
    protected $saveOrder;

    /**
     * @var string
     */
    protected $saveOrderingUrl;

    /**
     * All transition, which can be executed of one if the items
     *
     * @var  array
     */
    protected $transitions = [];

    /**
     * Display the view
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->user = Factory::getUser();

        $model = $this->getModel();
        $this->state = $model->getState();
        $this->items = $model->getItems();
        $this->filterForm = $model->getFilterForm();
        $this->pagination = $model->getPagination();

        $this->transitions = $model->getTransitions();

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        GeocontactHelper::addSubmenu('geocontacts');

        $this->addToolbar();
        $this->loadTemplateHeader();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        $state = $this->getModel()->getState();
        $canDo = GeocontactHelper::getActions($state->get('filter.category_id')); // TODO: учесть фильтр!
        $user = $this->getCurrentUser();
        $toolbar = $this->getDocument()->getToolbar();
        $app = Factory::getApplication();
        $title = Text::_('COM_GEOCONTACT_TITLE_GEOCONTACTS');

        if (!$app || !$toolbar) {
            return;
        }

        ToolbarHelper::title($title, 'fa fa-file-alt');
        $title = strip_tags($title) . ' - ' . $app->get('sitename') . ' - ' . Text::_('JADMINISTRATION');
        $app->getDocument()->setTitle($title);

        if ($canDo['core.create']) {
            $toolbar->addNew('geocontact.add');
        }

        if ($canDo['core.edit'] && isset($this->items[0])) {
            ToolbarHelper::editList('geocontact.edit', 'JTOOLBAR_EDIT');

            // TODO: sample
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);
            $childBar = $dropdown->getChildToolbar();

            if ($canDo['core.edit.state']) {
                if (isset($this->items[0]->state)) {
                    $childBar->publish('geocontacts.publish')->listCheck(true);
                    $childBar->unpublish('geocontacts.publish')->listCheck(true);
                } else if (isset($this->items[0])) {
                    //If this component does not use state then show a direct delete button as we can not trash
                    $childBar->trash('geocontacts.delete')->listCheck(true);
                }

                if (isset($this->items[0]->state)) {
                    $childBar->archive('geocontacts.archive')->listCheck(true);
                }
                if (isset($this->items[0]->checked_out)) {
                    // TODO: непонятно, что это
                    ToolbarHelper::custom('geocontacts.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
                }

                if (isset($this->items[0]->state)) {
                    if ((int)$this->items[0]->state === -2 && $canDo['core.delete']) {
                        $childBar->trash('geocontacts.delete')->listCheck(true);
                    } else if ($canDo['core.edit.state']) {
                        $childBar->trash('geocontacts.trash')->listCheck(true);
                    }
                }
            }

            $childBar->standardButton('downloadxml', 'JTOOLBAR_EXPORT', 'geocontacts.downloadxml')
                ->listCheck(true);
            //ToolBarHelper::custom('geocontacts.downloadxml', 'download.png', 'download_f2.png', 'JTOOLBAR_EXPORT', true);

        }


        if ($user->authorise('core.admin', 'com_geocontact') || $user->authorise('core.options', 'com_geocontact')) {
            $toolbar->preferences('com_geocontact');

            $doc = Factory::getApplication()->getDocument();
            $doc->addStyleDeclaration('#toolbar-upload{float:right;} #toolbar-download{float:right;}');

            ToolBarHelper::custom('geocontacts.uploadxml', 'upload.png', 'upload_f2.png', 'JTOOLBAR_UPLOAD', false);
            //$toolbar->addNew('geocontacts.uploadxml');
        }

        JHtmlSidebar::addFilter(
            Text::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
        );
    }

    /**
     * Load the template header data to simplify the template
     */
    protected function loadTemplateHeader(): void
    {
        HTMLHelper::_('bootstrap.tooltip');
        HTMLHelper::_('behavior.multiselect');

        $document = Factory::getDocument();
        $wa = $document->getWebAssetManager();
        $wa->registerAndUseStyle('my-style', 'components/com_geocontact/assets/css/geocontact.css');
        $wa->registerAndUseScript('my-script', 'components/com_geocontact/assets/js/list.js');

        $user = Factory::getUser();
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn = $this->escape($this->state->get('list.direction'));
        $user->authorise('core.edit.state', 'com_geocontact.category');
        $saveOrder = ((string)$this->listOrder === 'a.ordering');

        if ($saveOrder) {
            HTMLHelper::_('draggablelist.draggable');
            $this->saveOrderingUrl = 'index.php?option=com_geocontact&task=geocontacts.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
        }

        $this->saveOrder = $saveOrder;
    }
}
