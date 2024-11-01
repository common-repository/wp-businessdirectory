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
 * @subpackage  com_jbusinessdirectory

 */

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewEmailTemplates extends JBusinessDirectoryAdminView {
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
		$this->emailTypes   = $this->get('EmailTypes');
		$this->states		= $this->get('States');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		JBusinessDirectoryHelper::addSubmenu('emails');

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
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title('J-BusinessDirectory : '.JText::_('LNG_EMAIL_TEMPLATES'), 'generic.png');
		
		if ($canDo->get('core.create')) {
			JToolbarHelper::addNew('emailtemplate.add');
		}
		
		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('emailtemplate.edit');
		}
		
		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'emailtemplates.delete');
		}

		if ($canDo->get('core.create')) {
			$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#collapseModal\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
			<i class="icon-checkbox-partial" title="'.JText::_('JTOOLBAR_BATCH').'"></i>'.JText::_('JTOOLBAR_BATCH').'</button>';
			$bar->appendButton('Custom', $dhtml, 'batch');
		}


		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}
		
		JToolbarHelper::divider();
		JToolBarHelper::custom('emailtemplates.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#e-mail-templates');
	}
}
