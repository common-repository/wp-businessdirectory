<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

require_once BD_HELPERS_PATH.'/helper.php';

/**
 * The Region HTML View.
 */
class JBusinessDirectoryViewRegion extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;
	protected $countries;

	/**
	 * Display the view
	 *
	 * @param null $tpl
	 *
	 * @return bool
	 */
	public function display($tpl = null) {
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->countries = $this->get('Countries');

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

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_REGION' : 'COM_JBUSINESSDIRECTORY_EDIT_REGION'), 'menu.png');

		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('region.apply');
			JToolbarHelper::save('region.save');
		}

		JToolbarHelper::cancel('region.cancel', 'JTOOLBAR_CLOSE');

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_JBUSINESSDIRECTORY_REGION_EDIT');
	}
}
