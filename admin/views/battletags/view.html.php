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

class Diablo3ArmoryViewBattleTags extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;
	
	function display($tpl = null) 
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}

		// Set the toolbar
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();

		// Display the template
		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		JToolBarHelper::title(JText::_('COM_DIABLO3ARMORY_BATTLETAGS'));
		JToolBarHelper::addNew('battletag.add');
		JToolBarHelper::editList('battletag.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('battletags.publish');
		JToolBarHelper::unpublishList('battletags.unpublish');	
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'battletags.delete');	
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_diablo3armory',400,650);		
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
	}
	
	protected function getSortFields()
	{
		return array(
			'h.tag' => JText::_('COM_DIABLO3ARMORY_BATTLETAG_HEADING_TAG'),
			'h.name' => JText::_('COM_DIABLO3ARMORY_BATTLETAG_HEADING_NAME'),
			'h.ordering' => JText::_('COM_DIABLO3ARMORY_BATTLETAG_HEADING_ORDERING'),
			'h.published' => JText::_('COM_DIABLO3ARMORY_BATTLETAG_HEADING_PUBLISHED'),
			'h.id' => JText::_('COM_DIABLO3ARMORY_BATTLETAG_HEADING_ID')
			);
	}
}
