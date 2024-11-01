<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableAttributeOptions extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_attribute_options', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getAttributeOptions($attributeId) {
		$query = "select ao.*
					from #__jbusinessdirectory_attributes a
					left join #__jbusinessdirectory_attribute_options ao on a.id=ao.attribute_id
					where a.id= $attributeId
					order by ao.ordering, ao.id asc";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getAllAttributesWithOptions() {
		$query = "select a.id as attr_id,a.name as attr_name, a.code,ao.*,at.code			
					from #__jbusinessdirectory_attributes a
					left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type
					left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
					where a.status = 1
					group by ao.id
					";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	
	public function getAllAttributeOptions() {
		$query = "select ao.*
				from #__jbusinessdirectory_attribute_options ao order by ao.id asc";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function deleteAllAtributeOptions($attributeId) {
		$query = "delete from #__jbusinessdirectory_attribute_options where attribute_id in ($attributeId)";
		$this->_db->setQuery($query);
		$this->_db->execute();
	}
	
	public function deleteAtributeOptions($attributeId, $ids) {
		if (!empty($ids)) {
			$query = "delete from #__jbusinessdirectory_attribute_options where attribute_id in ($attributeId) and id not in ($ids)";
		} else {
			$query = "delete from #__jbusinessdirectory_attribute_options where attribute_id in ($attributeId)";
		}
		
		$this->_db->setQuery($query);
		$this->_db->execute();
	}
}
