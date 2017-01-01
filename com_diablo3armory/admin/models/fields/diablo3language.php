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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldDiablo3Language extends JFormFieldList
{
	protected $type = 'Diablo3Language';

	protected function getOptions() 
	{
		$options = array();		

		$options[] = JHtml::_('select.option', "en_US", 'en_US');
		$options[] = JHtml::_('select.option', "en_GB", 'en_GB');
		$options[] = JHtml::_('select.option', "es_MX", 'es_MX');
		$options[] = JHtml::_('select.option', "es_ES", 'es_ES');
		$options[] = JHtml::_('select.option', "it_IT", 'it_IT');
		$options[] = JHtml::_('select.option', "pt_PT", 'pt_PT');
		$options[] = JHtml::_('select.option', "pt_BR", 'pt_BR');
		$options[] = JHtml::_('select.option', "fr_FR", 'fr_FR');
		$options[] = JHtml::_('select.option', "ru_RU", 'ru_RU');
		$options[] = JHtml::_('select.option', "pl_PL", 'pl_PL');
		$options[] = JHtml::_('select.option', "de_DE", 'de_DE');
		$options[] = JHtml::_('select.option', "ko_KR", 'ko_KR');
		$options[] = JHtml::_('select.option', "zh_TW", 'zh_TW');
		$options[] = JHtml::_('select.option', "zh_CN", 'zh_CN');

		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}

?>