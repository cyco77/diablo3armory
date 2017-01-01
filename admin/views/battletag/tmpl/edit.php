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

JHtml::_('behavior.tooltip');
?>
	<form action="<?php echo JRoute::_('index.php?option=com_diablo3armory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">	
		<div>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_DIABLO3ARMORY_BATTLETAG_DETAILS' ); ?></legend>
				<div class="adminformlist">
					<div><?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?></div>
					<div><?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?></div>
					<div><?php echo $this->form->getLabel('tag'); ?>
					<?php echo $this->form->getInput('tag'); ?></div>
					<div><?php echo $this->form->getLabel('server'); ?>
					<?php echo $this->form->getInput('server'); ?></div>
					<div><?php echo $this->form->getLabel('name'); ?>
					<?php echo $this->form->getInput('name'); ?></div>
				</div>
			</fieldset>	
		</div>
		<div>
			<input type="hidden" name="task" value="battletag.edit" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>


