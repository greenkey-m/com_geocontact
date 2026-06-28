<?php
/**
 * @package     com_geocontact
 * @version     6.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$listOrder = $this->listOrder;
$listDirn  = $this->listDirn;
?>
<form action="<?php echo Route::_('index.php?option=com_geocontact&view=geocontacts'); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span>
            <span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>
        <table class="table" id="geocontactList">
            <caption class="visually-hidden">
                <?php echo Text::_('COM_GEOCONTACT_TITLE_GEOCONTACTS'); ?>
            </caption>
            <thead>
                <tr>
                    <td class="w-1 text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </td>
                    <?php if (isset($this->items[0]->ordering)) : ?>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                        </th>
                    <?php endif; ?>
                    <th scope="col">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_GEOCONTACT_HEADING_BACKEND_LIST_GEOCONTACTS_CAPTION', 'a.caption', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-10 d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_GEOCONTACT_HEADING_BACKEND_LIST_GEOCONTACTS_PHONES', 'a.phones', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-10 d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_GEOCONTACT_HEADING_BACKEND_LIST_GEOCONTACTS_STAND', 'a.stand', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-5">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_GEOCONTACT_HEADING_BACKEND_LIST_GEOCONTACTS_STATE', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-3 d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_GEOCONTACT_HEADING_BACKEND_LIST_GEOCONTACTS_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody <?php if ($this->saveOrder) : ?> class="js-draggable" data-url="<?php echo $this->saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
            <?php foreach ($this->items as $i => $item) :
                $canEdit   = $this->user->authorise('core.edit', 'com_geocontact');
                $canChange = $this->user->authorise('core.edit.state', 'com_geocontact');
                ?>
                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="0">
                    <td class="text-center">
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                    </td>
                    <?php if (isset($this->items[0]->ordering)) : ?>
                        <td class="text-center d-none d-md-table-cell">
                            <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            } elseif (!$this->saveOrder) {
                                $iconClass = ' inactive" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass; ?>">
                                <span class="icon-ellipsis-v" aria-hidden="true"></span>
                            </span>
                            <?php if ($canChange && $this->saveOrder) : ?>
                                <input type="text" class="hidden" name="order[]" size="5" value="<?php echo (int) $item->ordering; ?>">
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo Route::_('index.php?option=com_geocontact&task=geocontact.edit&id=' . (int) $item->id); ?>">
                                <?php echo $this->escape($item->caption); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $this->escape($item->caption); ?>
                        <?php endif; ?>
                        <div class="small text-muted d-md-none">
                            <?php echo $this->escape($item->phones); ?>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <?php echo $this->escape($item->phones); ?>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <?php echo $this->escape($item->stand); ?>
                    </td>
                    <td>
                        <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'geocontacts.', $canChange); ?>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <?php echo (int) $item->id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
    <?php endif; ?>
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
