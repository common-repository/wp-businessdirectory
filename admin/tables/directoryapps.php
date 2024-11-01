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
 * Class JTableDirectoryApps
 */
class JTableDirectoryApps extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_directory_apps', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getDirectoryApps($type = null) {
		$db = JFactory::getDbo();

		$whereType = '';
		if (!empty($type)) {
			$whereType = " and type = $type";
		}

		$query = "select * from #__jbusinessdirectory_directory_apps where 1 $whereType order by id";
		$db->setQuery($query);

		return $db->loadObjectList();
	}
	
	public function getDirectoryApp($id) {
		$db    = JFactory::getDbo();
		$query = "select *,
				  from #__jbusinessdirectory_directory_apps 
				  where id = $id 
				  order by id";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getDirectoryAppByName($appName) {
		$db    = JFactory::getDbo();
		$query = "select *
				  from #__jbusinessdirectory_directory_apps 
				  where app_name = '$appName'";

		$db->setQuery($query);
		return $db->loadObject();
	}
}
