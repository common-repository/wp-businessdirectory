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
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
require_once BD_HELPERS_PATH.'/helper.php';

JBusinessUtil::loadJQueryUI();

class JBusinessDirectoryViewManageBookmarks extends JBusinessDirectoryFrontEndView {
	protected $items;
	protected $pagination;
	protected $state;

	public function __construct() {
		if (JFactory::getApplication()->input->get("user_dashboard")) {
			$this->userDashboard = true;
		}
		parent::__construct();
	}

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

        $this->bookmarkTypes = $this->get('BookmarkTypes');

		$layout = JFactory::getApplication()->input->get("layout");
		if (isset($layout)) {
			$tpl = $layout;
		}
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->actions = JBusinessDirectoryHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		parent::display($tpl);
	}
}
