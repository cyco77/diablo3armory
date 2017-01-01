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

jimport('joomla.application.component.modellist');
require_once(JPATH_COMPONENT.DS.'script'.DS.'diablo_3_api'.DS.'diablo3.api.class.php');

class Diablo3ArmoryModelBattleTags extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'h.id',
				'tag', 'h.tag',
				'name', 'h.name',
				'paragonlevel', 'h.paragonlevel',
				'paragonlevelhardcore', 'h.paragonlevelhardcore',
				'paragonlevelseason', 'h.paragonlevelseason',
				'paragonlevelseasonhardcore', 'h.paragonlevelseasonhardcore',
				'cache', 'h.cache',
				'cachetime', 'h.cachetime',
				'published', 'h.published',
				'ordering', 'h.ordering'
				);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$orderCol	= JRequest::getCmd('filter_order', 'paragonlevel');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'paragonlevel';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'desc');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);
		
		$params = $app->getParams();
		$this->setState('params', $params);
	}
	
	protected function getListQuery() 
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
					'list.select',
					'id,tag,server,name,paragonlevel,paragonlevelhardcore,paragonlevelseason,paragonlevelseasonhardcore,cache,cachetime,published,ordering'
					)
				);

		$query->from('#__diablo3armory_battletag h');
		$query->where('h.published = 1');
		//$query->order('ordering');
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'paragonlevel');
		$orderDirn	= $this->state->get('list.direction', 'desc');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}

	public function getItems()
	{
		$params = JComponentHelper::getParams('com_diablo3armory');		
		$cachetimeout = $params->get('cachetimeout', 30);
		$cachetimeout = $cachetimeout * 60;		
		
		if (!isset($this->cache['items'])) {
			$items = parent::getItems();			
			
			$doreload = false;		
			
			foreach($items as $item){
				$curtime = time();
				if ($curtime - $item->cachetime	> $cachetimeout){
					$this->updateBattleTag($item);
					$doreload = true;
				}			
			}
			
			if ($doreload) {
				
				$db = JFactory::getDBO();
				$db->setQuery($this->getListQuery());
				$items = $db->loadObjectList();
			}
			
			foreach ($items as $item){
				$item->career = unserialize(base64_decode($item->cache));
			}
								
			$this->cache['items'] = $items;
		}
		
		return $this->cache['items'];
	}	
	
	private function updateBattleTag($item){
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_diablo3armory');		
		
		$apikey = $params->get('apikey', '');
		$language = $params->get('language', 'de_DE');
		$imageFolder = JPATH_ROOT.'/images/diablo3armory/';
		
		$diabloPlayer = new Diablo3($item->tag,$item->server,$language,$apikey,$imageFolder);
		$career = $diabloPlayer->getCareer();
		
		$item->paragonlevel = $career['paragonLevel'];
		$item->paragonlevelhardcore = $career['paragonLevelHardcore'];
		$item->paragonlevelseason = $career['paragonLevelSeason'];
		$item->paragonlevelseasonhardcore = $career['paragonLevelSeasonHardcore'];
		
		$db = JFactory::getDbo();	
		$query = $db->getQuery(true);
		$query->update('#__diablo3armory_battletag h');
		$query->set('h.paragonlevel = '.$item->paragonlevel);
		$query->set('h.paragonlevelhardcore = '.$item->paragonlevelhardcore);
		$query->set('h.paragonlevelseason = '.$item->paragonlevelseason);
		$query->set('h.paragonlevelseasonhardcore = '.$item->paragonlevelseasonhardcore);
		$query->set('h.cachetime = '.time());
		$query->set('h.cache = '.$db->quote(base64_encode(serialize($career))));

		$query->where('h.id = ' . (int)$item->id);
		$db->setQuery($query);
		
		$result = $db->query();
		
		if ($db->getErrorMsg()) 
		{
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		} 
		else 
		{
			return true;
		}		
	}
}
