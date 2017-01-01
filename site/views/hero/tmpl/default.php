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

$details = $this->item->details;

$params = JComponentHelper::getParams('com_diablo3armory');	
$language = $params->get('language', 'de_DE');
$storeimages = $params->get('storeimages', 0);

$gender = $this->item->details['gender'];
$heroClass = $this->item->details['class'];

$html = array();

$html[] = '<img class="paperdoll" src="'.Diablo3Helper::getPaperdollImage($heroClass,$gender).'" alt="Paperdoll" />';
$html[] = '<img class="paperdollsmall" src="'.Diablo3Helper::getPaperdollImageSmall($heroClass,$gender).'" alt="Paperdoll" />';

$html[] = '<div class="detail_hero_name">';
$html[] = $this->item->details['name'];
$html[] = '</div>';

$html[] = '<div class="detail_hero_values">';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_LIFE').': </span><span>'.number_format($this->item->details['stats']['life']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_DAMAGE').': </span><span>'.number_format($this->item->details['stats']['damage']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_TOUGHNESS').': </span><span>'.number_format($this->item->details['stats']['toughness']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_HEALING').': </span><span>'.number_format($this->item->details['stats']['healing']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_ATTACKSPEED').': </span><span>'.round($this->item->details['stats']['attackSpeed'], 2).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_ARMOR').': </span><span>'.number_format($this->item->details['stats']['armor']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_STRENGTH').': </span><span>'.number_format($this->item->details['stats']['strength']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_DEXTERITY').': </span><span>'.number_format($this->item->details['stats']['dexterity']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_VITALITY').': </span><span>'.number_format($this->item->details['stats']['vitality']).'</span></div>';
$html[] = '<div class="detail_hero_value"><span>'.JText::_('COM_DIABLO3ARMORY_DETAIL_HERO_VALUE_INTELLIGENCE').': </span><span>'.number_format($this->item->details['stats']['intelligence']).'</span></div>';

$html[] = '</div>';


foreach($details['items'] as $key => $bodypart) {
	
	$html[] = '<div class="detail_hero_div_'.$key.' itembackgound_'.$bodypart['displayColor'].'">';
	$html[] = '</div >';
	$html[] = '<a href="http://'.$this->item->server.'.battle.net/d3/'.Diablo3Helper::getShortLocale($language).'/'.$bodypart['tooltipParams'].'" onclick="return false" class="detail_hero_'.$key.'">';
	
	if ($storeimages == 1)
	{
		$html[] = '<img src="'.JURI::base().'images/diablo3armory/items/small/'.$bodypart['icon'].'.png" alt="'.$bodypart['name'].'" title="'.$bodypart['name'].'" />';
	}
	else
	{
		$html[] = '<img src="'.$details['item_img_url'].'small/'.$bodypart['icon'].'.png" alt="'.$bodypart['name'].'" title="'.$bodypart['name'].'" />';
	}
	
	$html[] = '</a>';
	
}

$html[] = '<div class="detail_hero_active_skills">';

foreach($details['skills']['active'] as $skill) {
	if (isset($skill['skill'])){
		
		$skillUrl = '/class/'.$details['class'].'/active/'.$skill['skill']['slug'];
		
		$html[] = '<a href="http://'.$this->item->server.'.battle.net/d3/'.Diablo3Helper::getShortLocale($language).$skillUrl.'" onclick="return false" class="detail_skill_active">';
		
		if ($storeimages == 1)
		{
			$html[] = '<img src="'.JURI::base().'images/diablo3armory/skills/42/'.$skill['skill']['icon'].'.png" alt="'.$skill['skill']['name'].'" title="'.$skill['skill']['name'].'" />';
		}
		else
		{
			$html[] = '<img src="'.$details['skill_img_url'].'/42/'.$skill['skill']['icon'].'.png" alt="'.$skill['skill']['name'].'" title="'.$skill['skill']['name'].'" />';
		}
		
		$html[] = '</a>';
	}
}

$html[] = '</div >';
$html[] = '<div class="detail_hero_passive_skills">';

foreach($details['skills']['passive'] as $skill) {
	if (isset($skill['skill'])){
		
		$skillUrl = '/class/'.$details['class'].'/passive/'.$skill['skill']['slug'];		
		
		$html[] = '<a href="http://'.$this->item->server.'.battle.net/d3/'.Diablo3Helper::getShortLocale($language).$skillUrl.'" onclick="return false" class="detail_skill_passive">';
		
		if ($storeimages == 1)
		{
			$html[] = '<img src="'.JURI::base().'images/diablo3armory/skills/42/'.$skill['skill']['icon'].'.png" alt="'.$skill['skill']['name'].'" title="'.$skill['skill']['name'].'" />';
		}
		else
		{
			$html[] = '<img src="'.$details['skill_img_url'].'/42/'.$skill['skill']['icon'].'.png" alt="'.$skill['skill']['name'].'" title="'.$skill['skill']['name'].'" />';
		}
		
		$html[] = '</a>';
	}
}

$html[] = '</div >';

echo json_encode(implode("\n", $html)); 

?>