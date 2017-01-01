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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class Diablo3ArmoryModelbattletags extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'h.id',
				'tag', 'h.tag',
				'server', 'h.server',
				'name', 'h.name',
				'published', 'h.published',
				'ordering', 'h.ordering'
				);
		}
		
		parent::__construct($config);
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
					'list.select',
					'id,tag, server,name,published,ordering'
					)
				);

		// From the hello table
		$query->from('#__diablo3armory_battletag h');
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('h.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(h.published IN (0, 1))');
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('h.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(h.tag LIKE ' . $search . ' OR h.name LIKE ' . $search . ')');
			}
		}
		
		// Add the list ordering clause
		$orderCol = $this->state->get('list.ordering', 'h.titel');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$context = $this->context;

		$search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_diablo3armory');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('h.ordering', 'asc');
	}
	
	public function getTable($type = 'BattleTag', $prefix = 'BattleTagTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
