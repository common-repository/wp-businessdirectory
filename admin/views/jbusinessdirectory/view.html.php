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

JBusinessUtil::loadJQueryUI();
JBusinessUtil::enqueueScript('libraries/jquery/ui/jquery.ui.tabs.js');

JBusinessUtil::enqueueScript('libraries/raphael/raphael-min.js');
JBusinessUtil::enqueueScript('libraries/morris/morris.min.js');
JBusinessUtil::enqueueStyle('libraries/morris/morris.css');
JBusinessUtil::enqueueScript('libraries/moment/moment.min.js');
JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');
JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');

JBusinessUtil::enqueueScript('libraries/dropzone/dropzone.js');
JBusinessUtil::enqueueStyle('libraries/dropzone/dropzone.css');

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewJBusinessDirectory extends JBusinessDirectoryAdminView {
	public function display($tpl = null) {
		$this->addToolbar();
		JBusinessUtil::checkJCHPlugin();

		$this->statistics          = $this->get("Statistics");
		$this->income              = $this->get("Income");
		$this->news                = $this->get("LocalNews");
		$this->actions             = $this->get("Actions");
		$this->directoryApps       = $this->get("DirectoryApps");
		$this->directoryExtensions = $this->get("DirectoryExtensions");
		$this->appStatuses         = $this->get("AppStatuses");
		$this->extensionStatuses   = $this->get("ExtensionStatuses");
		//$this->databaseDifferences = $this->get("DatabaseDifferences");
		//$this->schemaVersion = $this->get("SchemaVersion");
		
		$this->appSettings         = JBusinessUtil::getApplicationSettings();
		if ($this->appSettings->enable_item_moderation) {
			$this->pendingListings = $this->get('pendingListings');
			$this->pendingClaimApproval = $this->get('PendingClaimApproval');
			$this->pendingOffers   = $this->get('pendingOffers');
			$this->pendingEvents = $this->get('pendingEvents');
		}
		$this->pendingReviews = $this->get('pendingReviews');
		parent::display($tpl);
	}

	protected function addToolbar() {
		//require_once JPATH_COMPONENT.'/helpers/menus.php';

		JToolBarHelper::title(JText::_('LNG_COM_JBUSINESSDIRECTORY'), 'menumgr.png');
		$canDo = JBusinessDirectoryHelper::getActions();
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}
	}
}
