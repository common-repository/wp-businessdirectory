<?php
/**
 * @package     JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
require_once BD_HELPERS_PATH.'/helper.php';

/**
 * The HTML Menus Menu Menus View.
 *
 * @package     JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryViewMessages extends JBusinessDirectoryAdminView {
	protected $items;
	protected $pagination;
	protected $state;
	protected $searchType;

	public function display($tpl = null) {
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');


		JBusinessDirectoryHelper::addSubmenu('messages');

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
	protected function addToolBar() {
		$canDo = JBusinessDirectoryHelper::getActions();

		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_MESSAGES'), 'generic.php');

		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'messages.delete');
		}
		JToolBarHelper::divider();
		JToolbarHelper::publish('messages.markRead', 'LNG_MARK_AS_READ', true);
		JToolbarHelper::unpublish('messages.markUnread', 'LNG_MARK_AS_UNREAD', true);
		JToolBarHelper::divider();

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}

		JToolbarHelper::divider();
		JToolBarHelper::custom('messages.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
	}
}
