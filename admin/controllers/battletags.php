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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class Diablo3ArmoryControllerbattleTags extends JControllerAdmin
{
	public function getModel($name = 'BattleTag', $prefix = 'Diablo3ArmoryModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function publish(){
		$this->setRedirect( 'index.php?option=com_diablo3armory&view=battletags' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__diablo3armory_battletag'
			. ' SET published = ' . (int) $publish
			. ' WHERE id IN ( '. $cids .' )';
		
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );
	}	
	
	function saveOrder( )
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		
		// Initialize variables
		$total		= count( $cid );
		$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
		
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$model = $this->getModel('battletag');
			$row = $model->getTable();
			$row->load($cid[$i]);			
			
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		}
		
		$row->reorder();
		
		$msg = 'New ordering saved';
		$this->setRedirect('index.php?option=com_diablo3armory&view=battletags',$msg);
	}
	
	function orderItems( $inc )
	{		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		
		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );
		
		$model = $this->getModel('battletag');
		
		$row = $model->getTable();
		$row->load($cids);
		
		$row->move( $inc );

		$this->setRedirect('index.php?option=com_diablo3armory&view=battletags');
	}
}