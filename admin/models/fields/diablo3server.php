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

class JFormFieldDiablo3Server extends JFormFieldList
{
	protected $type = 'Diablo3Server';

	protected function getOptions() 
	{
		$options = array();		

		$options[] = JHtml::_('select.option', "us", 'us');
		$options[] = JHtml::_('select.option', "eu", 'eu');
		$options[] = JHtml::_('select.option', "tw", 'tw');
		$options[] = JHtml::_('select.option', "kr", 'kr');
		$options[] = JHtml::_('select.option', "cn", 'cn');
		
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}

?>