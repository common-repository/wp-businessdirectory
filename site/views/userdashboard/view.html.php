<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::enqueueScript('libraries/raphael/raphael-min.js');
JBusinessUtil::enqueueScript('libraries/morris/morris.min.js');
JBusinessUtil::enqueueStyle('libraries/morris/morris.css');

JBusinessUtil::enqueueScript('libraries/moment/moment.min.js');
JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');
JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewUserDashboard extends JBusinessDirectoryFrontEndView {
	public function __construct() {
		$this->userDashboard = true;
		parent::__construct();
	}
	
	public function display($tpl = null) {
		$this->actions = JBusinessDirectoryHelper::getActions();
		$this->appSettings =  JBusinessUtil::getApplicationSettings();
		$this->statistics = $this->get('Statistics');
		
		parent::display($tpl);
	
		if(!empty($this->jbdTemplate->menus)){
			//redirect to the first menu item available
			$menuItem = $this->jbdTemplate->menus[0];
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_($menuItem["link"], false));
		}
	}
}
