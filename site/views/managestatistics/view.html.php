<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JBusinessUtil::loadJQueryUI();

JBusinessUtil::enqueueScript('libraries/jquery/ui/jquery.ui.tabs.js');
JBusinessUtil::enqueueScript('libraries/raphael/raphael-min.js');
JBusinessUtil::enqueueScript('libraries/morris/morris.min.js');
JBusinessUtil::enqueueStyle('libraries/morris/morris.css');
JBusinessUtil::enqueueScript('libraries/moment/moment.min.js');
JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');
JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');

require_once BD_HELPERS_PATH.'/helper.php';
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageStatistics extends JBusinessDirectoryFrontEndView {

	public function display($tpl = null) {
		$this->listingCategoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		$this->offerCategoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_OFFER);
		$this->eventCategoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_EVENT);

		parent::display($tpl);
	}
}
