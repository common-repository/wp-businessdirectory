<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::enqueueScript('js/companies.js');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_MESSAGE_CONFIRM_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageCompanyMessages extends JBusinessDirectoryFrontEndView
{
    function __construct()
    {
        parent::__construct();
    }

    function display($tpl = null)
    {
        $this->companyId 	= $this->get('CompanyId');
        $this->items		= $this->get('CompanyMessages');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        $this->actions = JBusinessDirectoryHelper::getActions();
        
        parent::display($tpl);
    }
}
?>