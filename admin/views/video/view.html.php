<?php
/**
 * @package    WPBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */

JBusinessUtil::enqueueScript('libraries/chosen/ajax-chosen.min.js');
JBusinessUtil::loadJQueryUI();
JBusinessUtil::includeValidation();

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewVideo extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);

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
	 * @since   1.6
	 */
	protected function addToolbar() {
		$canDo = JBusinessDirectoryHelper::getActions();
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_VIDEO' : 'COM_JBUSINESSDIRECTORY_EDIT_VIDEO'), 'menu.png');

		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('video.apply');
			JToolbarHelper::save('video.save');
		}

		JToolbarHelper::cancel('video.cancel', 'JTOOLBAR_CLOSE');

		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#videos');
	}
}
