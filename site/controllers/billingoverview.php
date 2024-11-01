<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerBillingOverview extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}

	public function displayCompany() {
		parent::display();
	}
	
	public function showCompany() {
		$model = $this->getModel('companies');
		$model->increaseViewCount();
		JFactory::getApplication()->input->set("view", "companies");
		parent::display();
	}
}
