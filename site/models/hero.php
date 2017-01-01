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

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
require_once(JPATH_COMPONENT.DS.'script'.DS.'diablo_3_api'.DS.'diablo3.api.class.php');
require_once(JPATH_COMPONENT.DS.'helper'.DS.'diablo3helper.php');

class Diablo3ArmoryModelHero extends JModelItem
{
	protected $_data;

	public function getData($id,$heroId)
	{	
		$params = JComponentHelper::getParams('com_diablo3armory');		
		$cachetimeout = $params->get('cachetimeout', 1800);
		$cachetimeout = $cachetimeout * 60;
		
		// First load BattleTag
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('d.tag, d.server');		
		$query->from('#__diablo3armory_battletag d');	
		$query->where('d.published = 1 AND d.id='.$id);
		
		$db->setQuery($query);
		$dbEntry = $db->loadObject();			
		
		$savedHero = $this->loadHero($heroId);
		
		$doreload = false;
		
		if ($savedHero)
		{
			$curtime = time();
			if ($curtime - $savedHero->cachetime > $cachetimeout){
				$this->updateHero($dbEntry->tag,$heroId,$dbEntry->server,true);
				$doreload = true;
			}
		}
		else
		{
			$this->updateHero($dbEntry->tag,$heroId,$dbEntry->server,false);
			$doreload = true;
		}
		
		if ($doreload) {
			$savedHero =$this->loadHero($heroId);
		}		

		$savedHero->details = unserialize(base64_decode($savedHero->cache));
		$savedHero->server = $dbEntry->server;
		
		return $savedHero;
	}
	
	private function loadHero($heroId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('h.name, h.cache, h.cachetime');		
		$query->from('#__diablo3armory_hero h');	
		$query->where('h.id='.$heroId);
		
		$db->setQuery($query);
		return $db->loadObject();		
	}
	
	private function updateHero($battleTag, $heroId, $server,$isUpdate)
	{	
		// Load the parameters.
		$params = JComponentHelper::getParams('com_diablo3armory');		
		
		$apikey = $params->get('apikey', '');
		$language = $params->get('language', 'de_DE');
		$storeimages = $params->get('storeimages', 0);
		
		$imageFolder = JPATH_ROOT.'/images/diablo3armory/';
		
		$diabloPlayer = new Diablo3($battleTag,$server,$language,$apikey,$imageFolder);
		$hero = $diabloPlayer->getHero($heroId);		
		
		if ($storeimages == 1)
		{
			$diabloPlayer->getAllHeroItemImages($hero['id'],'small');
			$diabloPlayer->getAllHeroSkillImages($hero['id'], 42);
		}
		
		$hero['item_img_url'] = $diabloPlayer->http_item_img_url;
		$hero['skill_img_url'] = $diabloPlayer->http_skill_img_url;
		
		$db = JFactory::getDbo();	
		$query = $db->getQuery(true);
		
		if ($isUpdate)
		{		
			$query->update('#__diablo3armory_hero');
			$query->where('id = ' . $heroId);
		}
		else
		{
			$query->insert('#__diablo3armory_hero');			
		}
		
		$query->set('id = '.$heroId);
		$query->set('battletag = '.$db->quote($battleTag));
		$query->set('name = '.$db->quote($hero['name']));
		$query->set('cachetime = '.time());
		$query->set('cache = '.$db->quote(base64_encode(serialize($hero))));
		
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
