<?php
/**
 * @package     com_geocontact
 * @version     6.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\View\Geocontacts;

defined('_JEXEC') or die;

use Exception;
use Greenkey\Component\Geocontact\Administrator\Helper\GeocontactHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\User\User;
use Joomla\Registry\Registry;

/**
 * Geocontact list view
 */
class HtmlView extends BaseHtmlView
{
    protected User $user;

    public ?Form $filterForm = null;

    public array $activeFilters = [];

    protected array $items = [];

    protected Pagination $pagination;

    protected Registry $state;

    protected bool $saveOrder;

    protected string $saveOrderingUrl = '';

    protected array $transitions = [];

    public string $listOrder = '';

    public string $listDirn = '';

    /**
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->user = $this->getCurrentUser();

        $model              = $this->getModel();
        $this->state        = $model->getState();
        $this->items        = $model->getItems();
        $this->filterForm   = $model->getFilterForm();
        $this->pagination   = $model->getPagination();
        $this->transitions  = $model->getTransitions();

        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        $this->loadTemplateHeader();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $state   = $this->getModel()->getState();
        $canDo   = GeocontactHelper::getActions($state->get('filter.category_id'));
        $user    = $this->getCurrentUser();
        $toolbar = $this->getDocument()->getToolbar();
        $app     = Factory::getApplication();
        $title   = Text::_('COM_GEOCONTACT_TITLE_GEOCONTACTS');

        if (!$app || !$toolbar) {
            return;
        }

        ToolbarHelper::title($title, 'map-marker');
        $app->getDocument()->setTitle(
            strip_tags($title) . ' - ' . $app->get('sitename') . ' - ' . Text::_('JADMINISTRATION')
        );

        if ($canDo['core.create']) {
            $toolbar->addNew('geocontact.add');
        }

        if ($canDo['core.edit'] && isset($this->items[0])) {
            ToolbarHelper::editList('geocontact.edit', 'JTOOLBAR_EDIT');

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
                    $childBar->unpublish('geocontacts.unpublish')->listCheck(true);
                    $childBar->archive('geocontacts.archive')->listCheck(true);
                    $childBar->trash('geocontacts.trash')->listCheck(true);
                } else {
                    $childBar->trash('geocontacts.delete')->listCheck(true);
                }
            }

            $childBar->standardButton('downloadxml', 'JTOOLBAR_EXPORT', 'geocontacts.downloadxml')
                ->listCheck(true);
        }

        if ($user->authorise('core.admin', 'com_geocontact') || $user->authorise('core.options', 'com_geocontact')) {
            $toolbar->preferences('com_geocontact');
            ToolbarHelper::custom('geocontacts.uploadxml', 'upload', 'upload', 'JTOOLBAR_UPLOAD', false);
        }
    }

    protected function loadTemplateHeader(): void
    {
        HTMLHelper::_('bootstrap.tooltip');

        $document = Factory::getDocument();
        $wa       = $document->getWebAssetManager();
        $wa->useScript('multiselect');
        $wa->registerAndUseStyle('com-geocontact.admin', 'administrator/components/com_geocontact/assets/css/geocontact.css');

        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $saveOrder       = $this->listOrder === 'a.ordering';

        if ($saveOrder) {
            HTMLHelper::_('draggablelist.draggable');
            $this->saveOrderingUrl = 'index.php?option=com_geocontact&task=geocontacts.saveOrderAjax&tmpl=component&'
                . Session::getFormToken() . '=1';
        }

        $this->saveOrder = $saveOrder;
    }
}
