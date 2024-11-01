<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */
require_once BD_HELPERS_PATH.'/helper.php';
class JBusinessDirectoryViewCity extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;
	protected $regions;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->regions = $this->get('Regions');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
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

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_CITY' : 'COM_JBUSINESSDIRECTORY_EDIT_CITY'), 'menu.png');
		
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('city.apply');
			JToolbarHelper::save('city.save');
		}
		
		JToolbarHelper::cancel('city.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_JBUSINESSDIRECTORY_CITY_EDIT');
	}
}
