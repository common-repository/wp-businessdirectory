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

class JBusinessDirectoryViewCompanies extends JBusinessDirectoryAdminView {
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

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->statuses		= $this->get('Statuses');
		$this->states		= $this->get('States');
		$this->companyTypes	= $this->get('CompanyTypes');
		
		
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		
		JBusinessDirectoryHelper::addSubmenu('companies');

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
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title('J-BusinessDirectory : '.JText::_('LNG_COMPANIES'), 'generic.png');
		
		if ($canDo->get('core.create')) {
			JToolbarHelper::addNew('company.add');
		}
		
		if (($canDo->get('core.edit'))) {
			JToolbarHelper::editList('company.edit');
		}
		
		if ($canDo->get('core.create')) {
			$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#collapseModal\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
			<i class="icon-checkbox-partial" title="'.JText::_('JTOOLBAR_BATCH').'"></i>'.JText::_('JTOOLBAR_BATCH').'</button>';
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		
		
		if ($canDo->get('core.delete')) {
			JToolbarHelper::divider();
			JToolbarHelper::deleteList('', 'companies.delete');
		}
		
		JToolbarHelper::divider();
		
		
		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#import-model\' ).jbdModal({attachTo: \'adminForm\'}); " class="btn btn-small">
		<i class="icon-upload" title="'.JText::_('LNG_IMPORT_CSV').'"></i>'.JText::_('LNG_IMPORT_CSV').'</button>';
		$bar->appendButton('Custom', $dhtml, 'companies.importFromCsv');
		
		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#export-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-download" title="'.JText::_('LNG_EXPORT_CSV').'"></i>'.JText::_('LNG_EXPORT_CSV').'</button>';
		$bar->appendButton('Custom', $dhtml, 'companies.showExportCsv');
		

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}
		
		//JToolBarHelper::custom( 'companies.sendNotificationEmail', 'mail', 'mail', JText::_("LNG_SEND_NOTIFICATION"), false, false );
		JToolBarHelper::custom('companies.generateListingsPDF', 'download', 'download', JText::_("LNG_EXPORT_PDF"), false, false);
		JToolbarHelper::divider();
		JToolBarHelper::custom('companies.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#business-listings');
	}
}
