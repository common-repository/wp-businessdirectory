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
 JBusinessUtil::enqueueStyle('libraries/chosen/chosenIcon.css');
JBusinessUtil::enqueueScript('libraries/chosen/chosenIcon.jquery.js');
 
 require_once BD_HELPERS_PATH.'/helper.php';
 
 JBusinessUtil::enqueueScript('libraries/cropper/cropper.js');
 JBusinessUtil::enqueueScript('libraries/cropper/canvas-toBlob.js');
 JBusinessUtil::enqueueStyle('libraries/cropper/cropper.css');

class JBusinessDirectoryViewMobileAppConfig extends JBusinessDirectoryAdminView {
	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		JBusinessDirectoryHelper::addSubmenu('MobileAppConfig');

		$this->item = $this->get('Data');
		// temporrary solution
		$this->item->id = 0;
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
		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_MOBILEAPPCONFIG'), 'generic.png');

		if ($input->get('layout') == 'edit') {
			$input->set('hidemainmenu', true);
			JToolbarHelper::apply('mobileappconfig.apply', 'LNG_SAVE_PREVIEW');
			JToolbarHelper::save('mobileappconfig.save');
			JToolBarHelper::cancel('mobileappconfig.cancel');
		} else {
			JToolbarHelper::custom('mobileappconfig.edit', 'cog', 'settings', 'LNG_CONFIGURE_APP', false, false);
			JToolbarHelper::custom('mobileappconfig.reset', 'cog', 'settings', 'LNG_RESET_CONFIGURATIONS', false, false);
		}

		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL . 'businessdiradmin.html#mobile-app');
	}
}
