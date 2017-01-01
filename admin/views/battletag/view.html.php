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

// import Joomla view library
jimport('joomla.application.component.view');

class Diablo3ArmoryViewBattleTag extends JViewLegacy
{
	public function display($tpl = null) 
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{		
		JRequest::setVar('hidemainmenu', true);
	
		$isNew = ($this->item->id == 0);
		JToolBarHelper::title($isNew ? JText::_('COM_DIABLO3ARMORY_BATTLETAG_NEW') : JText::_('COM_DIABLO3ARMORY_BATTLETAG_EDIT'));
		JToolBarHelper::apply('battletag.apply');
		JToolBarHelper::save('battletag.save');
		JToolBarHelper::save2copy('battletag.save2copy');
		JToolBarHelper::save2new('battletag.save2new');
		JToolBarHelper::cancel('battletag.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}
