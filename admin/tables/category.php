<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_categories
 *
 * @copyright  Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html;
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.tablenested');
/**
 * Category table
 *
 * @package     JBD.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class JBusinessTableCategory extends JTableNested {
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_categories', 'id', $db);
	}
	
	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	/**
	 * Method to delete a node and, optionally, its child nodes from the table.
	 *
	 */
	public function delete($pk = null, $children = true) {
		return parent::delete($pk, $children);
	}
	
	public function getCategoryById($categoryId) {
		$db =JFactory::getDBO();
		$categoryId = (int)$categoryId;
		$query = "select * from #__jbusinessdirectory_categories c
				 where c.id=$categoryId";
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function getAllCategories($type = CATEGORY_TYPE_BUSINESS, $ignoreType = false) {
		$db =JFactory::getDBO();

		$typeFilter = "and type = $type";
		if($ignoreType){
			$typeFilter = "";
		}

		$query = "select * from #__jbusinessdirectory_categories where published=1 $typeFilter order by lft, parent_id, name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function changeState($itemId, $value) {
		$db =JFactory::getDBO();
		$itemId = $db->escape($itemId);
		$query = "UPDATE #__jbusinessdirectory_categories SET published = '$value' WHERE id = ".$itemId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function getCategoriesForExport() {
		$db =JFactory::getDBO();
		$query = "select c.id, c.name, GROUP_CONCAT(cc.name ORDER BY cc.name) as subcategories, GROUP_CONCAT(cc.id ORDER BY cc.name) as subcategories_ids, c.type
				from #__jbusinessdirectory_categories c
				left join #__jbusinessdirectory_categories cc on c.id = cc.parent_id
				group by c.id
				order by c.lft, cc.name";
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getMainCategories() {
		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_categories where published=1 and parent_id=1 order by name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getSubCategories() {
		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_categories where published=1 and parent_id!=1 order by name';
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;
	}
	
	public function getCategoriesList($keyword, $type, $language) {
		$db =JFactory::getDBO();
		$keyword = $db->escape($keyword);
		$type = intval($type);
		
		$query = "select distinct name as label, name as value, id from #__jbusinessdirectory_categories where name like '%$keyword%' and published=1 and type=$type";
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_multilingual) {
			$query = "select distinct t.name as label, t.name as value, c.id 
					  from #__jbusinessdirectory_categories c
					  left join #__jbusinessdirectory_language_translations t on c.id = t.object_id and t.language_tag = '$language' and t.type = ".CATEGORY_TRANSLATION."		 
					  where t.name like '%$keyword%' and c.published=1 and c.type=$type";
		}
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCategorySuggestions($keyword, $type, $limit = 0) {
		$db      = JFactory::getDbo();
		$keyword = $db->escape($keyword);
		$type = intval($type);
		$query   = "select distinct id as suggestionId, name from #__jbusinessdirectory_categories where name like '%$keyword%' and published=1 and type=$type";
		$db->setQuery($query, 0, $limit);
		return $db->loadObjectList();
	}
	
	public function getTotalCategories() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_categories";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->nr;
	}
	
	public function checkAlias($id, $alias, $type) {
		$db =JFactory::getDBO();
		$id = (int)$id;
		$alias = $db->escape($alias);
		$type = intval($type);
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_categories  WHERE alias='$alias' and id<>$id and type=$type";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
	}
	
	public function increaseClickCount($id) {
		$db =JFactory::getDBO();
		$id = (int)$id;
		$query = "UPDATE #__jbusinessdirectory_categories SET clickCount = clickCount+1 WHERE id = ".$id ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	/**
	 * Retrieves all the subcategories of a parent category
	 * @param $parentId
	 * @param int $type
	 * @return mixed
	 */
	public function getSubcategoriesByParentId($parentId) {

		$parentId = (int)$parentId;
		if(empty($parentId)){
			$parentId = 1;
		}

		$db = JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_categories where parent_id=".$parentId." order by name ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Gets the total number of objects (business, offer, events) for each category they belong to
	 * @param $details (array containing filters)
	 * @param int $type (category type, either business, offer or event category)
	 * @return array|bool
	 */
	public function getCountPerCategory($details, $type = CATEGORY_TYPE_BUSINESS) {
		$db =JFactory::getDBO();
		
		foreach ($details as &$searchDetail) {
			$searchDetail = $db->escape($searchDetail);
		}
		
		$enablePackage = isset($details["enablePackages"])?$details["enablePackages"]:null;
		$showPendingApproval = isset($details["showPendingApproval"])?$details["showPendingApproval"]:null;
		$whereCond = "";
		$select="";
		$feature="";
		$approved="";
		switch ($type) {
			case CATEGORY_TYPE_OFFER:
				$select = "select count(distinct co.id) as nr_listings, cg1.id";
				$statusFilter="and (cp.approved = ".OFFER_APPROVED;
				$created = OFFER_CREATED;
				$table = "from #__jbusinessdirectory_company_offers co 
						  inner join #__jbusinessdirectory_companies cp on co.companyId = cp.id";
				$innerJoin = "inner join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId";
				$whereCond = "and (co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00' or co.publish_start_date is null) and  (co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00' or co.publish_end_date is null)";
				$feature = "and pf.feature='company_offers'";
				$approved = "and (co.approved = ".OFFER_APPROVED.")";
				if ($showPendingApproval) {
					$approved = "and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.")";
				}
				
				break;
			case CATEGORY_TYPE_EVENT:
				$select = "select count(distinct co.id) as nr_listings, cg1.id";
				$statusFilter="and (cp.approved = ".EVENT_APPROVED;
				$created = EVENT_CREATED;
				$table = "from #__jbusinessdirectory_company_events co
						   inner join #__jbusinessdirectory_companies cp on co.company_id = cp.id";
				$innerJoin = "inner join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId";
				$whereCond.=" and co.end_date>= DATE(NOW())";
				$feature = "and pf.feature='company_events'";
				$approved = "and (co.approved = ".EVENT_APPROVED.")";
				if ($showPendingApproval) {
					$approved = "and (co.approved = ".EVENT_CREATED." or co.approved = ".EVENT_APPROVED.")";
				}
				break;
			default:
				$select = "select count(distinct cp.id) as nr_listings, cg1.id";
				$statusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED;
				$created = COMPANY_STATUS_CREATED;
				$table = "from #__jbusinessdirectory_companies cp";
				$innerJoin = "inner join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId";
				break;
		}

		$packageFilter = '';
		if ($enablePackage) {
			$packageFilter = " and (
								(
									(inv.state= ".PAYMENT_STATUS_PAID." and (
										(
											(( now() between inv.start_date and inv.end_date) or p.expiration_type=1)
											or
											(now() between inv.start_trial_date and inv.end_trial_date)
										)
									))
								)
								or (
									((p.expiration_type=1 and p.price=0) or (p.id = cp.package_id and p.price=0)))
							)";
		}
		
		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") $approved";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") $approved";
		}
		
		$query = "$select
				  $table
				  $innerJoin
				  inner join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
				  inner join #__jbusinessdirectory_categories cg1 ON cg1.id = cg.parent_id or cg1.id=cg.id
				  left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
				  left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
				  left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
				  where cp.state = 1 $packageFilter $companyStatusFilter $whereCond
				  group by cg1.id";
		
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}

		$results = $db->loadObjectList();

		$listingsCount = array();

		foreach ($results as $result) {
			$listingsCount[$result->id] = $result;
		}

		return $listingsCount;
	}

	public function getMainCategoriesByType($type = CATEGORY_TYPE_BUSINESS, $language="en") {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_categories where parent_id = 1 and type = $type order by name";

		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_multilingual) {
			$query = "SELECT c.*, COALESCE(t.name, c.name) AS name
			FROM #__jbusinessdirectory_categories c
			LEFT JOIN #__jbusinessdirectory_language_translations t ON c.id = t.object_id AND t.language_tag = '$language' AND t.type = ".CATEGORY_TRANSLATION."
			WHERE c.parent_id = 1 AND c.type=$type ORDER BY name;";
		}


		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getRelatedCategories($catId, $keywords){

		$db = JFactory::getDBO();
		if(empty($keywords)){
			return null;
		}

		$filter = "0";
		foreach($keywords as $keyword){
			$filter .= " or keywords like '%$keyword%'";
		}

		$query = "SELECT id FROM #__jbusinessdirectory_categories where ($filter) and id !=$catId order by name";

		$db->setQuery($query);
		return $db->loadObjectList();

	}
}
