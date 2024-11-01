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

class JBusinessDirectoryViewReports extends JBusinessDirectoryAdminView {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->reports = $this->get('Items');
		$this->state = $this->get('State');
		$this->states = $this->get('States');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		JBusinessDirectoryHelper::addSubmenu('reports');

		$layout = JFactory::getApplication()->input->get("layout");
		if (isset($layout)) {
			$tpl = $layout;
		} else {
			$tpl="standard";
		}
		
		$this->report = $this->get('ReportData');
		$this->params = JBusinessDirectoryHelper::getCompanyParams();
		$this->conferenceParams = JBusinessDirectoryHelper::getConferenceParams();
		$this->offerParams = JBusinessDirectoryHelper::getOfferParams();
		
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
		$canDo = JBusinessDirectoryHelper::getActions();
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title('J-BusinessDirectory : '.JText::_('LNG_REPORTS'), 'generic.png');
		
		if ($canDo->get('core.create')) {
			JToolbarHelper::addNew('report.add');
		}
		
		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('report.edit');
		}
		
		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'reports.delete');
		}
		
		$reportId = JFactory::getApplication()->input->get("reportId", null);
		if (!empty($reportId)) {
			$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#export-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
    		<i class="icon-download" title="'.JText::_('LNG_EXPORT_CSV').'"></i>'.JText::_('LNG_EXPORT_CSV').'</button>';
			$bar->appendButton('Custom', $dhtml, 'reports.showExportCsv');
		}
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}
		
		JToolbarHelper::divider();
		JToolBarHelper::custom('ratings.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#reports');
	}
}
