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

class Diablo3ArmoryViewHero extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		$id = JRequest::getVar( 'id', '', 'default', 'int' );
		$heroId = JRequest::getVar( 'hero', '', 'default', 'int' );
		
		$model = $this->getModel();
		$item = $model->getData($id,$heroId);

		if ($item == null) 
		{
			JError::raiseError(500, 'Item is null: id='.$id);
			return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		$this->assignRef('item',$item);
		
		$params = JComponentHelper::getParams( 'com_diablo3armory' ); 
		$this->assignRef('params',$params);		
		
		// Display the view
		parent::display($tpl);
	}
}
