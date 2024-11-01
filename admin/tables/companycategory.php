<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanyCategory extends JTable {
	public $companyId			= null;
	public $categoryId			= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_category', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function insertRelations($companyId, $categoryIds) {
		if (empty($categoryIds)) {
			return;
		}
		
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_company_category(companyId, categoryId) values ";
		foreach ($categoryIds as $categoryId) {
			$query = $query."(".$companyId.",".$categoryId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE companyId=values(companyId), categoryId=values(categoryId) ";
		
		$db->setQuery($query);
		
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		
		$filter ="(";
		foreach ($categoryIds as $categoryId) {
			$filter = $filter.$categoryId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_company_category where companyId =$companyId and categoryId not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		
		return true;
	}
	
	public function getSelectedCategories($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_category  cc inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where companyId=".$companyId;
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getSelectedCategoriesList($companyId) {
		$db =JFactory::getDBO();
		$query = "select categoryId from #__jbusinessdirectory_company_category  cc inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where companyId=".$companyId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->categoryId;
		}
		
		return $result;
	}

	public function updateCategoriesNumber($companyId, $categoryNr) {
		$db =JFactory::getDBO();
		$query = "DELETE FROM #__jbusinessdirectory_company_category 
                    WHERE companyId = $companyId 
                    ORDER BY companyId ASC 
                    LIMIT $categoryNr";
		$db->setQuery($query);
		return $db->execute();
	}

	public function insertVideoRelations($videoId, $categoryIds) {
		if (empty($categoryIds)) {
			return;
		}
		
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_video_category(video_id, category_id) values ";
		foreach ($categoryIds as $categoryId) {
			$query = $query."(".$videoId.",".$categoryId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE video_id=values(video_id), category_id=values(category_id) ";
		
		$db->setQuery($query);
		
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		
		$filter ="(";
		foreach ($categoryIds as $categoryId) {
			$filter = $filter.$categoryId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_video_category where video_id =$videoId and category_id not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		
		return true;
	}
	
	public function getSelectedVideoCategories($videoId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_video_category  cc 
				  inner join #__jbusinessdirectory_categories c  on cc.category_id=c.id  where video_id=".$videoId;
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getSelectedVideoCategoriesList($videoId) {
		$db =JFactory::getDBO();
		$query = "select category_id 
				 from #__jbusinessdirectory_video_category  cc 
				 inner join #__jbusinessdirectory_categories c  on cc.category_id=c.id  where video_id=".$videoId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->category_id;
		}
		
		return $result;
	}
	
	public function getSelectedOfferCategories($offerId) {
		$db =JFactory::getDBO();
		$query = "select categoryId from #__jbusinessdirectory_company_offer_category  cc inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where offerId=".$offerId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->categoryId;
		}
		
		return $result;
	}

	public function getSelectedOfferCategoriesList($offerId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_offer_category  cc inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where offerId=".$offerId;
		$db->setQuery($query);
		$list = $db->loadObjectList();

		return $list;
	}

	
	public function insertOfferRelations($offerId, $categoryIds) {
		$db =JFactory::getDBO();
		
		if (empty($categoryIds)) {
			$query = "delete from #__jbusinessdirectory_company_offer_category where offerId =$offerId ";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
			
			return;
		}
			
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_company_offer_category(offerId, categoryId) values ";
		foreach ($categoryIds as $categoryId) {
			$query = $query."(".$offerId.",".$categoryId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE offerId=values(offerId), categoryId=values(categoryId) ";
	
		$db->setQuery($query);
	
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	
		$filter ="(";
		foreach ($categoryIds as $categoryId) {
			$filter = $filter.$categoryId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_company_offer_category where offerId =$offerId and categoryId not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	
		return true;
	}
	
	public function getSelectedEventCategories($eventId) {
		$db =JFactory::getDBO();
		$query = "select categoryId from #__jbusinessdirectory_company_event_category  cc 
				  inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where eventId=".$eventId;
		
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->categoryId;
		}
	
		return $result;
	}

	public function getSelectedEventCategoriesList($eventId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_event_category  cc 
				  inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where eventId=".$eventId;

		$db->setQuery($query);
		$list = $db->loadObjectList();

		return $list;
	}

	public function getSelectedAttributeCategories($attributeId) {
		$db =JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_attribute_category  cc inner join #__jbusinessdirectory_categories c  on cc.categoryId=c.id  where attributeId=".$attributeId;

		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->categoryId;
		}

		$db->setQuery($query);
		return $result;
	}
	
	public function insertEventRelations($eventId, $categoryIds) {
		$db =JFactory::getDBO();
	
		if (empty($categoryIds)) {
			$query = "delete from #__jbusinessdirectory_company_event_category where eventId =$eventId ";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
				
			return;
		}
			
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_company_event_category(eventId, categoryId) values ";
		foreach ($categoryIds as $categoryId) {
			$query = $query."(".$eventId.",".$categoryId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE eventId=values(eventId), categoryId=values(categoryId) ";
	
		$db->setQuery($query);
	
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	
		$filter ="(";
		foreach ($categoryIds as $categoryId) {
			$filter = $filter.$categoryId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_company_event_category where eventId =$eventId and categoryId not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	
		return true;
	}

	public function getCategoriesByType($searchDetails, $type = 1, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDbo();

		$keyword = isset($searchDetails['keyword'])?$searchDetails['keyword']:null;
		$orderBy = isset($searchDetails["orderBy"])?$searchDetails["orderBy"]:null;
		$asc_desc = isset($searchDetails["asc_desc"])?$searchDetails["asc_desc"]:null;

		$whereNameCond='';
		if (!empty($keyword)) {
			$keyword = $db->escape($keyword);
			$whereNameCond=" and (c.name like '%$keyword%' or c.description like '%$keyword%') ";
		}

		if (empty($asc_desc)) {
			$asc_desc = "";
		}

		if ($orderBy=="rand()" || empty($orderBy)) {
			$orderBy = "c.id";
			$asc_desc = "desc";
		}

		$query = " select c.id, c.name, c.alias, c.description
		 		   from #__jbusinessdirectory_categories c
		 		   where c.published = 1 $whereNameCond and c.type = $type
		 		   order by $orderBy $asc_desc
		 		   ";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function insertAttributeRelations($attributeId, $categoryIds) {
		$db =JFactory::getDBO();

		if (empty($categoryIds)) {
			$query = "delete from #__jbusinessdirectory_attribute_category where attributeId =".$attributeId;
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}

			return;
		}

		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_attribute_category(attributeId, categoryId) values ";
		foreach ($categoryIds as $categoryId) {
			$query = $query."(".$attributeId.",".$categoryId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE attributeId=values(attributeId), categoryId=values(categoryId) ";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		$filter ="(";
		foreach ($categoryIds as $categoryId) {
			$filter = $filter.$categoryId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_attribute_category where attributeId =$attributeId and categoryId not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}
}
