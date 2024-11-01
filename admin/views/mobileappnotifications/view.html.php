<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */


 JBusinessUtil::enqueueScript('libraries/chosen/ajax-chosen.min.js');
 JBusinessUtil::loadJQueryUI();
 JBusinessUtil::includeValidation();
 
require_once HELPERS_PATH.'/helper.php';
 
class JBusinessDirectoryViewMobileAppNotifications extends JBusinessDirectoryAdminView {
	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		JBusinessDirectoryHelper::addSubmenu('MobileAppNotifications');

		$this->mobileAppConfig = JBusinessUtil::getMobileAppSettings();
        $this->items = $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->languages = JBusinessUtil::getLanguages();
		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar() {
		$bar = JToolBar::getInstance('toolbar');
		
		$input = JFactory::getApplication()->input;
		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_MOBILEAPPNOTIFICATIONS'), 'generic.png');


		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL . 'businessdiradmin.html#mobile-app');
	}
}
