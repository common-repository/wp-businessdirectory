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

class JBusinessDirectoryViewSearchLogs extends JBusinessDirectoryAdminView {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		
		$this->get('ArchiveSearchLogs');
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->appSettings = JBusinessUtil::getApplicationSettings();

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
		
		JToolBarHelper::title('J-BusinessDirectory : '.JText::_('LNG_SEARCH_LOGS'), 'generic.png');

		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#export-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-download" title="'.JText::_('LNG_EXPORT_CSV').'"></i>'.JText::_('LNG_EXPORT_CSV').'</button>';
		$bar->appendButton('Custom', $dhtml, 'searchlogs.showExportCsv');

		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#delete-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-delete" title="'.JText::_('LNG_DELETE').'"></i>'.JText::_('LNG_DELETE').'</button>';
		$bar->appendButton('Custom', $dhtml, 'searchlogs.deleteByDate');

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}
		
		JToolbarHelper::divider();
		JToolBarHelper::custom('searchlogs.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#searchlogs');
	}
}
