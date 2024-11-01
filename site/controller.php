<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.'/helpers/defines.php';;
require_once BD_HELPERS_PATH.'/logger.php';
require_once BD_HELPERS_PATH.'/utils.php';


class JBusinessDirectoryController extends JControllerLegacy {
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function display($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);
	}
}
