<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableMobileAppMenus extends JTable {
	public $app_id		= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_mobile_app_menus', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function getMobileAppMenus($language = null) {
		$db    = JFactory::getDbo();

		$langFilter = '';
		if(isset($language)) {
			$langFilter = " and lang = '$language'";
		}
		
		$query = "SELECT * FROM #__jbusinessdirectory_mobile_app_menus where title <> '' AND url <> '' $langFilter";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

    public function deleteMobileAppMenus($ids) {
		if (!empty($ids)) {
			$query = "delete from #__jbusinessdirectory_mobile_app_menus where id not in ($ids)";
		} else {
			$query = "delete from #__jbusinessdirectory_mobile_app_menus";
		}
		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	
}
