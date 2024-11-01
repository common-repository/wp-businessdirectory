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
 * The HTML Menus Menu Menus View.
 *
 * @package     JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryViewCompanyTypes extends JBusinessDirectoryAdminView {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @param null $tpl
	 *
	 * @return bool
	 */
	public function display($tpl = null) {
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$this->companyViews = $this->get('CompanyViews');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		JBusinessDirectoryHelper::addSubmenu('companytypes');

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
		$user  = JBusinessUtil::getUser();

		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_COMPANY_TYPES'), 'generic.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_jbusinessdirectory', 'core.create'))) > 0) {
			JToolbarHelper::addNew('companytype.add');
		}

		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('companytype.edit');
		}

		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'companytypes.delete');
		}

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}

		JToolbarHelper::divider();
		JToolBarHelper::custom('companytypes.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL . 'businessdiradmin.html#business-types');
	}
}
