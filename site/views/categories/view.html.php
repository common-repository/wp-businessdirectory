<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryViewCategories extends JViewLegacy {
	public function display($tpl = null) {
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");
		
		$this->categories = $this->get('Categories');

		$this->categoryType = $this->get('CategoryType');
		$this->categoryIds =  JFactory::getApplication()->input->get("CategoryID");  //$this->get('categoryIds');

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		
		parent::display($tpl);
	}
}
