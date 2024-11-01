<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );




class JBusinessDirectoryViewAbout extends JBusinessDirectoryAdminView
{
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('LNG_ABOUT'), 'generic.png');	
		// JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		JToolBarHelper::custom( 'back', 'back.png', 'back.png', 'Back',false, false );
		parent::display($tpl);
	}
	
}