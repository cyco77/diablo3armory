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

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class Diablo3ArmoryModelBattleTag extends JModelAdmin
{
	public function getTable($type = 'BattleTag', $prefix = 'Diablo3ArmoryTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_diablo3armory.battletag', 'battletag', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_diablo3armory.edit.battletag.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}
	
	public function save($data)
	{		
		$result = parent::save($data);
		
		if ($data->ordering < 1)
		{
			$table = $this->getTable();
			$table->reorder();	
		}
						
		return $result;
	}
}
