<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_ITEMS_CONFIRM_DELETE');

/**
 * The HTML Menus Menu Menus View.
 *
 * @package    WPBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewManageCompanyArticle extends JViewLegacy {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$user = JBusinessUtil::getUser();
		$this->companyOptions = JBusinessUtil::getCompaniesOptions(null, $user->ID);

		parent::display($tpl);
	}
}
