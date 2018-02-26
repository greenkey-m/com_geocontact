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
<div class="table-responsive">
    <table class="table table-striped">
        <tr>
			<th class="item-description">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_DESCRIPTION'); ?>
			</th>
			<td>
				<?php echo $this->item->description; ?>
			</td>
		</tr>
		<tr>
			<th class="item-stand">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_STAND'); ?>
			</th>
			<td>
				<?php echo $this->item->stand; ?>
			</td>
		</tr>
		<tr>
			<th class="item-address">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_ADDRESS'); ?>
			</th>
			<td>
				<?php echo $this->item->address; ?>
			</td>
		</tr>
		<tr>
			<th class="item-name">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_NAME'); ?>
			</th>
			<td>
				<?php echo $this->item->name; ?>
			</td>
		</tr>
		<tr>
			<th class="item-phones">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_PHONES'); ?>
			</th>
			<td>
				<?php echo $this->item->phones; ?>
			</td>
		</tr>
		<tr>
			<th class="item-latlong">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_LATLONG'); ?>
			</th>
			<td>
				<?php echo $this->item->latlong; ?>
			</td>
		</tr>
		<tr>
			<th class="item-caption">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_CAPTION'); ?>
			</th>
			<td>
				<?php echo $this->item->caption; ?>
			</td>
		</tr>
		<tr>
			<th class="item-created_by">
				<?php echo JText::_('COM_GEOCONTACT_HEADING_FRONTEND_DETAIL_CREATED_BY'); ?>
			</th>
			<td>
				<?php echo $this->item->created_by; ?>
			</td>
		</tr>
    </table>
</div>
