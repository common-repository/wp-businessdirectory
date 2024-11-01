<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
JBusinessUtil::enqueueScript('libraries/jquery/jquery-ui.js');
JBusinessUtil::enqueueScript('libraries/jquery/ui/jquery.ui.tabs.js');
JBusinessUtil::enqueueScript('libraries/raphael/raphael-min.js');
JBusinessUtil::enqueueScript('libraries/morris/morris.min.js');
JBusinessUtil::enqueueStyle('libraries/morris/morris.css');
JBusinessUtil::enqueueScript('libraries/moment/moment.min.js');
JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');
JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewStatistics extends JBusinessDirectoryAdminView {
	public function display($tpl = null) {
		$this->get('ArchiveStatistics');

		$this->listingCategoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		$this->offerCategoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_OFFER);
		$this->eventCategoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_EVENT);

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		$canDo = JBusinessDirectoryHelper::getActions();
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title('J-BusinessDirectory : ' . JText::_('LNG_STATISTICS'), 'generic.png');

		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#export-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-download" title="'.JText::_('LNG_EXPORT_CSV').'"></i>'.JText::_('LNG_EXPORT_CSV').'</button>';
		$bar->appendButton('Custom', $dhtml, 'statistics.showExportCsv');

		$dhtml = '<button data-toggle="modal" onclick="jQuery( \'#delete-model\' ).jbdModal({attachTo: \'adminForm\'});" class="btn btn-small">
		<i class="icon-delete" title="'.JText::_('LNG_DELETE').'"></i>'.JText::_('LNG_DELETE').'</button>';
		$bar->appendButton('Custom', $dhtml, 'statistics.deleteByDate');
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}

	}
}
