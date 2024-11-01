<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableOfferAttributes extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_offer_attributes', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getOfferAttributes($id, $categoryId = null, $sellingAttribute = false) {

		JBusinessUtil::setGroupConcatLenght();
		
		$joinAttributeCategoryTable = '';
		if (!empty($categoryId)) {
			$categoryId = trim($categoryId, ',');
			$joinAttributeCategoryTable .= 'right join #__jbusinessdirectory_attribute_category as cag on cag.attributeId = a.id and cag.categoryId in ('.$categoryId.', -1)';
		}

		$sellingCondition = ' and a.use_attribute_for_selling = 0';
		if (!empty($sellingAttribute)) {
			$sellingCondition = ' and a.use_attribute_for_selling = 1';
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
		          left join #__jbusinessdirectory_offer_attributes AS ca on ca.attribute_id = a.id and ca.offer_id=$id
		          $joinAttributeCategoryTable
		          where a.status = 1 and a.attribute_type = ".ATTRIBUTE_TYPE_OFFER." $sellingCondition
				  group by a.id
		          order by a.ordering
				  ";
		
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getAttributeById($offerId, $attributeId, $optionId){

		$query = "select a.*,
				  GROUP_CONCAT(DISTINCT ao.name ORDER BY ao.ordering asc SEPARATOR '|#')  options,
				  GROUP_CONCAT(DISTINCT ao.id ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIDS,
				  GROUP_CONCAT(ao.icon ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIcons,
				  at.code as attributeTypeCode,
				  ca.value as attributeValue
				  from #__jbusinessdirectory_attributes a
		          left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type
		          left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
		          left join #__jbusinessdirectory_offer_attributes AS ca on ca.attribute_id = a.id and ca.offer_id=$offerId
		          where a.status = 1 and a.attribute_type = ".ATTRIBUTE_TYPE_OFFER." and a.status and a.id = $attributeId and ao.id = $optionId
				  group by a.id
		          order by a.ordering
				  ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function deleteOfferAttributes($offerId, $ids = null) {
		$db =JFactory::getDBO();
		
		$whereIdsFilter="";
		if (!empty($ids)) {
			$whereIdsFilter=" and attribute_id in (".implode(",", $ids).")";
		}

		$query ="delete ofr from 
				#__jbusinessdirectory_offer_attributes ofr inner join #__jbusinessdirectory_attributes a on ofr.attribute_id = a.id 
				WHERE offer_id = $offerId $whereIdsFilter and a.use_attribute_for_selling = '0'";

		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		
		return true;
	}
}
