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
 * The HTML Menus Menu Menus View.
 *
 * @package    JBusinessDirectory
 * @subpackage com_jbusinessdirectory
 */

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewReviewabuses extends JBusinessDirectoryAdminView {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->states		= $this->get('States');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		JBusinessDirectoryHelper::addSubmenu('reviewabuses');

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
		JToolBarHelper::title('J-BusinessDirectory : '.JText::_('LNG_REVIEW_ABUSES'), 'generic.png');
		
		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('reviewabuse.edit');
		}
		
		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'reviewabuses.delete');
		}

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}

		JToolbarHelper::divider();
		JToolBarHelper::custom('reviewabuses.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#review-abuses');
	}
}
