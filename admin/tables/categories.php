<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');



// No direct access
defined('_JEXEC') or die('Restricted access');

class JTableCategories2 extends JTable {
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

	public function getAllCategories() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_categories order by parent_id,name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getAllFrontCategories() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_categories where published=1 order by parent_id,name ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getCategoryById($categoryId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_categories where id=".$categoryId."  order by parent_id, name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function changeCategoryState($companyId) {
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_categories SET published = IF(published, 0, 1) WHERE id = ".$companyId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function getTotalCategories() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_categories";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->nr;
	}
	
	public function deleteSubcategories($categoryId) {
		$db =JFactory::getDBO();
		$query = 	" delete from #__jbusinessdirectory_categories where parent_id = ".$categoryId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public static function getMainCategories() {
		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_categories where parent_id=0 order by name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getSubCategories() {
		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_categories where parent_id!=0 order by name';
		$db->setQuery($query);
		$result = $db->loadObjectList();
	
		return $result;
	}
	
	public function getCategoriesList($keyword) {
		$db =JFactory::getDBO();
		
		$keyword = $db->escape($keyword);
		
		$query = "select distinct name as label, name as value from #__jbusinessdirectory_categories where name like '%$keyword%'";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getCategoriesForExport() {
		$db =JFactory::getDBO();
		$query = "select  c.name, GROUP_CONCAT(cc.name) as subcategories
				  from #__jbusinessdirectory_categories c
				  inner join #__jbusinessdirectory_categories cc on c.id = cc.parent_id
					group by c.id
					order by c.name, cc.name";
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
