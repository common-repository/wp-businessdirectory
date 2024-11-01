<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Reports view
 *
 * @package     JBusinessDirectory
 *
 * @since 1.0.0
 */
class JBusinessDirectoryViewPaymentReports extends JBusinessDirectoryAdminView {
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null) {
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->appSettings	= JBusinessUtil::getApplicationSettings();
		$this->headers      = $this->get('ReportHeaders');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar() {
		$canDo = JBusinessDirectoryHelper::getActions();
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_PAYMENT_REPORTS'), 'generic.png');

		JToolBarHelper::custom('paymentreports.exportReportToCSV', 'download', 'download', JText::_("LNG_EXPORT_CSV"), false, false);

		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}

		JToolbarHelper::divider();
		JToolBarHelper::custom('paymentreports.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false);
		JToolBarHelper::help('', false, DOCUMENTATION_URL . 'businessdiradmin.html#reports');
	}
}
