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
 
class com_diablo3armoryInstallerScript
{
	function install($parent) 
	{
		$this->createImagesFolder();
	}
 
 function uninstall($parent) 
	{
	}

	function update($parent) 
	{
		$this->createImagesFolder();
	}
 
	function preflight($type, $parent) 
	{
	}
 
	function postflight($type, $parent) 
	{
	}
	
	function createImagesFolder()
	{
		jimport('joomla.filesystem.folder');

		// create a folder inside your images folder
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory');		
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'items');	
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR.'large');	
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR.'small');	
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'skills');
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'skills'.DIRECTORY_SEPARATOR.'21');	
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'skills'.DIRECTORY_SEPARATOR.'42');	
		JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'diablo3armory'.DIRECTORY_SEPARATOR.'skills'.DIRECTORY_SEPARATOR.'64');	
		
	}
}