<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1>
			<?php if ($this->escape($this->params->get('page_heading'))) : ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
        </h1>
    </div>
<?php endif; ?>
<form action="<?php echo Route::_('index.php?option=com_geocontact&view=geocontacts'); ?>" method="get" name="adminForm" id="adminForm">
    <div id="filter-bar" class="btn-toolbar mb-2">
        <div class="input-group mb-2">
            <input type="text" name="filter_search" id="filter-search" class="form-control" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>..." value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo Text::_('JSEARCH_FILTER'); ?>" />
            <div class="input-group-append">
                <button class="btn btn-secondary" type="submit"><?php echo Text::_('JSEARCH_FILTER'); ?></button>
                <button class="btn btn-secondary" id="clear-search" type="button"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="item-description">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
					</th>
					<th class="item-stand">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_STAND', 'a.stand', $listDirn, $listOrder); ?>
					</th>
					<th class="item-address">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_ADDRESS', 'a.address', $listDirn, $listOrder); ?>
					</th>
					<th class="item-name">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class="item-phones">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_PHONES', 'a.phones', $listDirn, $listOrder); ?>
					</th>
					<th class="item-latlong">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_LATLONG', 'a.latlong', $listDirn, $listOrder); ?>
					</th>
					<th class="item-caption">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_CAPTION', 'a.caption', $listDirn, $listOrder); ?>
					</th>
					<th class="item-created_by">
						<?php echo HTMLHelper::_('grid.sort',  'COM_GEOCONTACT_HEADING_FRONTEND_LIST_GEOCONTACTS_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
					</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                <tr class="<?php echo ($i % 2) ? 'odd' : 'even'; ?>">
                    <td class="item-description">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->description; ?>
						</a>
					</td>
					<td class="item-stand">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->stand; ?>
						</a>
					</td>
					<td class="item-address">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->address; ?>
						</a>
					</td>
					<td class="item-name">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->name; ?>
						</a>
					</td>
					<td class="item-phones">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->phones; ?>
						</a>
					</td>
					<td class="item-latlong">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->latlong; ?>
						</a>
					</td>
					<td class="item-caption">
						<a href="<?php echo Route::_('index.php?option=com_geocontact&view=geocontact&id=' . (int) $item->id); ?>">
							<?php echo $item->caption; ?>
						</a>
					</td>
					<td class="item-created_by">
						<?php echo $item->created_by; ?>
					</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination center">
            <?php echo $this->pagination->getListFooter(); ?>
        </div>
        <input type="hidden" name="view" value="geocontacts" />
        <input type="hidden" name="option" value="com_geocontact" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    </div>
</form>
