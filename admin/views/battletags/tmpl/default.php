<?php
/*------------------------------------------------------------------------
# com_diablo3armory - Diablo3Armory!
# ------------------------------------------------------------------------
# author    Lars Hildebrandt
# copyright Copyright (C) 2015 Lars Hildebrandt. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.larshildebrandt.de
# Technical Support:  Forum - http://www.larshildebrandt.de/forum/index.html
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'h.ordering');
$canChange  = true; 
$canOrder	= $user->authorise('core.edit.state',	'com_diablo3armory');
$saveOrder 	= ($listOrder == 'h.ordering' && $listDirn == 'asc');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_diablo3armory&task=battletags.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() 
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		} 
		else 
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_diablo3armory&controller=battletags'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<?php else : ?>
<div id="j-main-container">
	<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_DIABLO3ARMORY_ITEMS_SEARCH_FILTER');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_DIABLO3ARMORY_ITEMS_SEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_TAGS_ITEMS_SEARCH_FILTER'); ?>" />
			</div>
			<div class="btn-group hidden-phone">
				<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>	
	<table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'h.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="20" class="nowrap center hidden-phone">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>			
				<th></th>
				<th style="text-align:left;width:30%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_DIABLO3ARMORY_BATTLETAG_HEADING_TAG', 'h.tag', $listDirn, $listOrder); ?>
				</th>	
				<th style="text-align:left;width:30%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_DIABLO3ARMORY_BATTLETAG_HEADING_NAME', 'h.name', $listDirn, $listOrder); ?>
				</th>				
				<th style="text-align:left;" class="nowrap hidden-phone">
					<?php echo JHtml::_('grid.sort', 'COM_DIABLO3ARMORY_BATTLETAG_HEADING_PUBLISHED', 'h.published', $listDirn, $listOrder); ?>
				</th>
				<th width="5" class="nowrap hidden-phone">
					<?php echo JHtml::_('grid.sort', 'COM_DIABLO3ARMORY_BATTLETAG_HEADING_ID', 'h.id', $listDirn, $listOrder); ?>
				</th>
			</tr>	
		</thead>
		<tbody>
			<?php foreach($this->items as $i => $item): ?>
			<?php
				$pageNav = $this->pagination;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
					endif; ?>
						<span class="sortable-handler hasTooltip<?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
						<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none" name="order[]" size="5"
						value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
					<?php else : ?>
			<span class="sortable-handler inactive" >
<i class="icon-menu"></i>
</span>
					<?php endif; ?>
</td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>				
				<td>
				<?php
				echo '<img src="'.JURI::root().'components/com_diablo3armory/images/logo_24.png" alt="' . $item->name . '" />'; 		
				?>
				</td>
				<td>
					<?php echo $item->tag; ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_diablo3armory&task=battletag.edit&id=' . $item->id); ?>">
						<?php echo $item->name; ?>
					</a>
				</td>				
				<td align="center" class="hidden-phone">					
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'battletags.');?>		
				</td>
				<td class="hidden-phone">
					<?php echo $item->id; ?>
				</td>				
			</tr>
			<?php endforeach; ?>	
		</tbody>
		<tfoot>
			<tr>
				<td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
