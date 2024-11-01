<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableEventAttributes extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_event_attributes', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getEventAttributes($id, $categoryId = null) {
		$joinAttributeCategoryTable = '';
		if (!empty($categoryId)) {
			$joinAttributeCategoryTable .= 'right join #__jbusinessdirectory_attribute_category as cag on cag.attributeId = a.id and cag.categoryId in ('.$categoryId.', -1)';
		}

		$query = "select a.*,
				  GROUP_CONCAT(DISTINCT ao.name ORDER BY ao.ordering asc SEPARATOR '|#')  options,
				  GROUP_CONCAT(DISTINCT ao.id ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIDS,
				  GROUP_CONCAT(ao.icon ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIcons,
				  at.code as attributeTypeCode,
				  ca.value as attributeValue
				  from #__jbusinessdirectory_attributes a
		          left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type
		          left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
		          left join #__jbusinessdirectory_event_attributes AS ca on ca.attribute_id = a.id and ca.event_id=$id
		          $joinAttributeCategoryTable
		          where a.status = 1 and a.attribute_type = ".ATTRIBUTE_TYPE_EVENT."
				  group by a.id
		          order by a.ordering
				  ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function deleteEventAttributes($eventId, $ids = null) {
		$db =JFactory::getDBO();
		
		$whereIdsFilter="";
		if (!empty($ids)) {
			$whereIdsFilter=" and attribute_id in (".implode(",", $ids).")";
		}
		
		$query = " delete from #__jbusinessdirectory_event_attributes WHERE event_id = $eventId $whereIdsFilter";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}

		return true;
	}
}
