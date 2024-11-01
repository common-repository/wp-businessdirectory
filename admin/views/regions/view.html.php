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
 * The Regions HTML View.
 */
class JBusinessDirectoryViewRegions extends JBusinessDirectoryAdminView {
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
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		JBusinessDirectoryHelper::addSubmenu('regions');

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
	 * @since 1.6
	 */
	protected function addToolbar() {
		$canDo = JBusinessDirectoryHelper::getActions();
		$user  = JBusinessUtil::getUser();
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_REGIONS'), 'generic.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_jbusinessdirectory', 'core.create'))) > 0) {
			JToolbarHelper::addNew('region.add');
		}

		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('region.edit');
		}

		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'regions.delete');
		}

		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#import-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-upload" title="'.JText::_('LNG_IMPORT_REGIONS_AND_CITIES_CSV').'"></i>'.JText::_('LNG_IMPORT_REGIONS_AND_CITIES_CSV').'</button>';
		$bar->appendButton('Custom', $dhtml, 'regions.importFromCsv');

		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#export-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-download" title="'.JText::_('LNG_EXPORT_REGIONS_AND_CITIES_CSV').'"></i>'.JText::_('LNG_EXPORT_REGIONS_AND_CITIES_CSV').'</button>';
		$bar->appendButton('Custom', $dhtml, 'regions.showExportCsv');

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}

		JToolbarHelper::divider();
		JToolBarHelper::custom('regions.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolbarHelper::help('JHELP_COMPANIES');
	}
}
