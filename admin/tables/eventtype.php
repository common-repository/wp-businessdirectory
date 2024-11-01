<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableEventType extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_event_types', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function getEventTypes() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_event_types order by ordering asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getEventType($typeId) {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_event_types where id=$typeId";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getOrderedEventTypes() {
		$db = JFactory::getDBO();
		$query = "select id,name from #__jbusinessdirectory_company_event_types order by ordering asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
