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

class Diablo3Helper{
	
	static function makeTagLink($tag){
		return str_replace('#','-',$tag);
	}
	
	static function getPaperdollImage($heroClass,$gender){
		$base = JURI::base().'components/com_diablo3armory/images/paperdolls/';
		
		$img = $gender == 0 ? $heroClass.'-male.jpg' : $heroClass.'-female.jpg';
		
		return  $base . $img;
	}
	
	static function getPaperdollImageSmall($heroClass,$gender){
		$base = JURI::base().'components/com_diablo3armory/images/paperdollssmall/';
		
		$img = $gender == 0 ? $heroClass.'-male.jpg' : $heroClass.'-female.jpg';
		
		return  $base . $img;
	}
	
	static function getHeroImageUrl($class, $gender) {
		
		$base = JURI::base().'components/com_diablo3armory/images/icons/portraits/';
		
		switch ($class) {
			case 'demon-hunter':
				$img = $gender == 0 ? 'demonhunter_male.jpg' : 'demonhunter_female.jpg';
				break;
			case 'wizard':
				$img = $gender == 0 ? 'wizard_male.jpg' : 'wizard_female.jpg';
				break;
			case 'monk':
				$img = $gender == 0 ? 'monk_male.jpg' : 'monk_female.jpg';
				break;
			case 'barbarian':
				$img = $gender == 0 ? 'barbarian_male.jpg' : 'barbarian_female.jpg';
				break;
			case 'witch-doctor':
				$img = $gender == 0 ? 'witchdoctor_male.jpg' : 'witchdoctor_female.jpg';
				break;
			case 'crusader':
				$img = $gender == 0 ? 'x1_crusader_male.jpg' : 'x1_crusader_female.jpg';
				break;
		}	
		
		return  $base . $img;
	}
	
	static function getShortLocale($value){
		
		switch ($value){
			case 'en_US': return 'en';
			case 'en_GB': return 'en';
			case 'es_MX': return 'es';
			case 'es_ES': return 'es';
			case 'it_IT': return 'it';
			case 'pt_PT': return 'pt';
			case 'pt_BR': return 'pt';
			case 'fr_FR': return 'fr';
			case 'ru_RU': return 'ru';
			case 'pl_PL': return 'pl';
			case 'de_DE': return 'de';
			case 'ko_KR': return 'ko';
			case 'zh_TW': return 'zh';
			case 'zh_CN': return 'zh';			
		}		
	}	
	
}