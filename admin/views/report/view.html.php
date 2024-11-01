<?php
/**
 * @report    JBusinessDirectory
 * @subreport com_jbusinessdirectory
 *
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML View.
 */

JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');

JBusinessUtil::enqueueStyle('libraries/multi/multi.css');
JBusinessUtil::enqueueScript('libraries/multi/multi.js');

JBusinessUtil::includeValidation();

JBusinessUtil::enqueueScript('libraries/moment/moment.min.js');
JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');
JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');
require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewReport extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->item->selected_params = explode(",", $this->item->selected_params);
		$this->item->custom_params = explode(",", $this->item->custom_params);
		$this->state = $this->get('State');
		
		$this->params = JBusinessDirectoryHelper::getCompanyParams();
		$this->conferenceParams = JBusinessDirectoryHelper::getConferenceParams();
		$this->customFeatures = JBusinessDirectoryHelper::getPackageCustomFeatures();
		$this->offerParams = JBusinessDirectoryHelper::getOfferParams();
		$this->offerCustomFeatures = JBusinessDirectoryHelper::getOfferCustomFeatures();
		
		$this->conferenceParams = JBusinessDirectoryHelper::orderParams($this->conferenceParams, $this->item->selected_params);
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
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_REPORT' : 'COM_JBUSINESSDIRECTORY_EDIT_REPORT'), 'menu.png');
		
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('report.apply');
			JToolbarHelper::save('report.save');
		}
		
		JToolbarHelper::cancel('report.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#reports');
	}
}
