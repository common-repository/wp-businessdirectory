<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableAttribute extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_attributes', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getAttributes($type = null) {
		$filterType = "";
		if (!empty($type)) {
			$filterType = " and attribute_type = $type ";
		}
		$query = "select a.* from #__jbusinessdirectory_attributes a 
                    where 1 $filterType ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getActiveAttributes() {
		$query = "select a.* from #__jbusinessdirectory_attributes a where status =1 order by a.name";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getAttributesWithTypes() {
		$query = "select a.*, at.code as attr_type
					from #__jbusinessdirectory_attributes a
				  	left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type ";
	
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getAttributesConfiguration($ids) {
		$db =JFactory::getDBO();
		
		JBusinessUtil::setGroupConcatLenght();
		
		$whereIdCond = '';
		if (!empty($ids)) {
			$whereIdCond = 'and a.id in ('.$ids.')';
		}
		$query = "select a.*,
				GROUP_CONCAT(DISTINCT ao.name ORDER BY ao.ordering asc SEPARATOR '|#')  options,
				GROUP_CONCAT(DISTINCT ao.id ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIDS,
				GROUP_CONCAT(DISTINCT ao.icon ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIcons,
				at.code as attributeTypeCode
				from #__jbusinessdirectory_attributes a
				left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type
				left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
				where a.status = 1 
				$whereIdCond
				group by a.id
				order by a.ordering
				";
	
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result;
	}
	
	public function getCustomAttributeValues($ids) {
		if (empty($ids)) {
			return null;
		}
		$query = "select attribute_id,name,id from #__jbusinessdirectory_attribute_options where id in ($ids)";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Retrieve the custom attribtues that are used on the search results
	 *
	 * @param [type] $ids
	 * @return void
	 */
	public function getSearchAttributeValues($type = ATTRIBUTE_TYPE_BUSINESS) {
		
		$query = "select id, name from #__jbusinessdirectory_attributes where status = 1 and attribute_type = $type  order by name";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result;
	}
		
	public function changeState($attributeId) {
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_attributes SET status = IF(status, 0, 1) WHERE id = ".$attributeId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getAttributesByCategory($categoryId, $type = ATTRIBUTE_TYPE_BUSINESS, $itemId = null) {
		$db =JFactory::getDBO();
		
		JBusinessUtil::setGroupConcatLenght();
		
		$joinItemTable = '';
		$selectValue = '';
		if (!empty($itemId)) {
			switch ($type) {
				case ATTRIBUTE_TYPE_OFFER:
					$joinItemTable = 'left join #__jbusinessdirectory_offer_attributes as ia on ia.attribute_id = a.id and ia.offer_id = '.$itemId;
					break;
				case ATTRIBUTE_TYPE_EVENT:
					$joinItemTable = 'left join #__jbusinessdirectory_event_attributes as ia on ia.attribute_id = a.id and ia.event_id = '.$itemId;
					break;
				default:
					$joinItemTable = 'left join #__jbusinessdirectory_company_attributes as ia on ia.attribute_id = a.id and ia.company_id = '.$itemId;
			}

			$selectValue = ' ia.value as attributeValue,';
		}

		$query = "select a.*, 
				  GROUP_CONCAT(DISTINCT ao.name ORDER BY ao.ordering asc SEPARATOR '|#')  options,
				  GROUP_CONCAT(DISTINCT ao.id ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIDS,
				  GROUP_CONCAT(DISTINCT ao.icon ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIcons,
				  $selectValue
				  at.code as attributeTypeCode
				  from #__jbusinessdirectory_attributes a 
		          left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type
		          left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
		          $joinItemTable
		          right join #__jbusinessdirectory_attribute_category AS ca on ca.attributeId = a.id and ca.categoryId in ($categoryId, -1)
		          where a.status = 1 and a.attribute_type=".$type." and a.use_attribute_for_selling = '0'
				  group by a.id 
		          order by a.ordering
				  ";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
