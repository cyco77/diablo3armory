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

JHtml::_('jquery.framework');
JHtml::_('jquery.ui');

require_once(JPATH_COMPONENT.DS.'helper'.DS.'diablo3helper.php');

$document = JFactory::getDocument();

$document->addScript( JURI::root().'/components/com_diablo3armory/script/battletags.js' );
$document->addScript("http://us.battle.net/d3/static/js/tooltips.js");

$document->addStyleSheet(JURI::base().'components/com_diablo3armory/style/diablo3armory.css');

$params = JComponentHelper::getParams('com_diablo3armory');	
$language = $params->get('language', 'de_DE');

function truncateText($text, $length) {
	$length = abs((int)$length);
	if(strlen($text) > $length) {
		$text = substr($text, 0, $length ) . '...'; 
	}
	
	return($text);
}

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$html = array();

if ($this->params->get('show_page_heading', 1)) : 
	$html[] = '<h1>';
		if ($this->escape($this->params->get('page_heading'))) :
			$html[] = $this->escape($this->params->get('page_heading')); 
		else : 
			$html[] = $this->escape($this->params->get('page_title')); 
		endif;
	$html[] = '</h1>';
 endif; 

$html[] = '<form action="'. JRoute::_('index.php?option=com_diablo3armory&view=battletags').'" method="post" name="adminForm" id="adminForm">';
$html[] = '<table id="diablo3armory_battletaglist">';
$html[] = '	<thead>';
$html[] = '		<tr>';		
$html[] = '			<th>';
$html[] = '			</th>';	
$html[] = '			<th class="diablo3armory_battletaglist_tag">';
$html[] = JHtml::_('grid.sort',  'COM_DIABLO3ARMORY_BATTLETAG_HEADING_TAG', 'tag', $listDirn, $listOrder);
$html[] = '			</th>';	
$html[] = '			<th class="diablo3armory_battletaglist_name battletags_hidemobile">';
$html[] = JHtml::_('grid.sort',  'COM_DIABLO3ARMORY_BATTLETAG_HEADING_NAME', 'name', $listDirn, $listOrder);
$html[] = '			</th>';	
$html[] = '			<th class="diablo3armory_battletaglist_paragon">';
$html[] = JHtml::_('grid.sort',  'COM_DIABLO3ARMORY_BATTLETAG_HEADING_PARAGONLEVEL', 'paragonlevel', $listDirn, $listOrder);
$html[] = '			</th>';	
$html[] = '			<th class="diablo3armory_battletaglist_paragon battletags_hidemobile">';
$html[] = JHtml::_('grid.sort',  'COM_DIABLO3ARMORY_BATTLETAG_HEADING_PARAGONLEVELHARDCORE', 'paragonlevelhardcore', $listDirn, $listOrder);
$html[] = '			</th>';	
$html[] = '			<th class="diablo3armory_battletaglist_paragon battletags_hidemobile">';
$html[] = JHtml::_('grid.sort',  'COM_DIABLO3ARMORY_BATTLETAG_HEADING_PARAGONLEVELSEASON', 'paragonlevelseason', $listDirn, $listOrder);
$html[] = '			</th>';	
$html[] = '			<th class="diablo3armory_battletaglist_paragon battletags_hidemobile">';
$html[] = JHtml::_('grid.sort',  'COM_DIABLO3ARMORY_BATTLETAG_HEADING_PARAGONLEVELSEASONHARDCORE', 'paragonlevelseasonhardcore', $listDirn, $listOrder);
$html[] = '			</th>';	
$html[] = '		</tr>';	
$html[] = '	</thead>';
$html[] = '	<tbody>';
foreach($this->items as $i => $item)
{
	$html[] = '		<tr id="battletag_'.$item->id . '" class="diablo3armory_battletaglist_row'. ($i % 2).'" onclick="showBattleTagDetails(' . $item->id . ')">';
	$html[] = '			<td class="diablo3armory_battletaglist_image">';
	$html[] = '				<img class="heroes_toggleimage" id="battletag_plus_'.$item->id . '" src="'.JURI::base().'components/com_diablo3armory/images/plus.png" alt="" /><img class="heroes_toggleimage" id="battletag_minus_'.$item->id . '" src="'.JURI::base().'components/com_diablo3armory/images/minus.png" alt="" style="display: none" />';
	$html[] = '			</td>';
	$html[] = '			<td class="diablo3armory_battletaglist_tag">';
	$html[] = $item->tag;
	$html[] = '			</td>';
	$html[] = '			<td class="diablo3armory_battletaglist_name battletags_hidemobile">';
	$html[] = $item->name; 
	$html[] = '			</td>';		
	$html[] = '			<td class="diablo3armory_battletaglist_paragon">';
	$html[] = $item->paragonlevel;	 
	$html[] = '			</td>';
	$html[] = '			<td class="diablo3armory_battletaglist_paragon battletags_hidemobile">';
	$html[] = $item->paragonlevelhardcore;	 
	$html[] = '			</td>';
	$html[] = '			<td class="diablo3armory_battletaglist_paragon battletags_hidemobile">';
	$html[] = $item->paragonlevelseason;	 
	$html[] = '			</td>';
	$html[] = '			<td class="diablo3armory_battletaglist_paragon battletags_hidemobile">';
	$html[] = $item->paragonlevelseasonhardcore;	 
	$html[] = '			</td>';
	
	$html[] = '		</tr>';
	
	$html[] = '		<tr id="battletag_details_'.$item->id . '" style="display: none;">';
	$html[] = '			<td>';
	$html[] = '			</td>';
	$html[] = '			<td colspan="6">';
		
	foreach ($item->career['heroes'] as $hero) {
		
		$divstyle = 'battletag_hero_div';
		$levelstyle ='battletag_hero_level';
		$namestyle ='battletag_hero_name';
		
		if ($hero['seasonal'])
		{
			$divstyle = 'battletag_hero_seasonal_div';
			$levelstyle = 'battletag_hero_seasonal_level';
			$namestyle ='battletag_hero_seasonal_name';
		}
		if ($hero['hardcore'])
		{
			$divstyle = 'battletag_hero_hardcore_div';
			$levelstyle = 'battletag_hero_hardcore_level';
			$namestyle ='battletag_hero_hardcore_name';
		}
		
		$link = 'http://'.$item->server.'.battle.net/d3/'.Diablo3Helper::getShortLocale($language).'/profile/'.Diablo3Helper::makeTagLink($item->tag).'/hero/'.$hero['id'];
		
		$html[] = '			<div class="herocell" id="herocell_'.$hero['id'].'">';
		$html[] = '				<div class="hidden_battletag">'.$item->id.'</div>';
		$html[] = '				<div class="hidden_heroid">'.$hero['id'].'</div>';
		$html[] = '				<div class="hidden_detailsloaded">0</div>';
		$html[] = '				<a href=' . $link . ' target="_blank">';
		$html[] = '					<img src="'.Diablo3Helper::getHeroImageUrl($hero['class'],$hero['gender']).'" class="heroimage" width="83" height="66" style="width: 83px; min-width: 83px;height: 66px;"/>';	
		$html[] = '					<br/>';	
		$html[] = '					<div class="' . $divstyle . '">';	
		$html[] = '						<span class="' . $levelstyle . '">' . $hero['level'] . '</span>';
		$html[] = '						<span class="' . $namestyle . '">' . truncateText($hero['name'],6) . '</span>';		
		$html[] = '					</div>';	
		$html[] = '					<div class="herotooltip" id="herotooltip_'.$hero['id'].'" style="display:none;">';	
		$html[] = '<div class="herotooltip_loading">Loading</div>' ;
		$html[] = '					</div>';	
		$html[] = '				</a>';	
		$html[] = '			</div>';
	}
	
	$html[] = '			</td>';
	$html[] = '		</tr>';
}
$html[] = '	</tbody>';
$html[] = '</table>';

$html[] = '	<div>';
$html[] = '		<input type="hidden" name="task" value="" />';
$html[] = '		<input type="hidden" name="boxchecked" value="0" />';
$html[] = '		<input type="hidden" name="filter_order" value="' . $listOrder .'" />';
$html[] = '		<input type="hidden" name="filter_order_Dir" value="' . $listDirn . '" />';
$html[] = JHtml::_('form.token');
$html[] = '	</div>';
$html[] = '</form>';

echo implode("\n", $html); 

?>
