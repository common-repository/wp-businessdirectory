<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');



// No direct access
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryViewUpdates extends JBusinessDirectoryAdminView {
	
	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		//$this->items = $this->get('Items');
		$this->currentVersion = JBusinessUtil::getCurrentVersion();
		$this->state = $this->get('State');
		$this->expirationDate= $this->get('ExpirationDate');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
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
		JToolBarHelper::title('J-BusinessDirectory '.JText::_('LNG_UPDATES', true), 'generic.png');
		JToolbarHelper::custom('updates.saveOrder', 'save', 'save', 'LNG_SAVE_ORDER', true, false);
		JToolbarHelper::divider();
		JToolBarHelper::custom('updates.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#updates');
	}
}
