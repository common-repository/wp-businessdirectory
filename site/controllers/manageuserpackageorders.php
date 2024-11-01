<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'orders.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'orders.php');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryControllerManageUserPackageOrders extends JBusinessDirectoryControllerOrders {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}
}
