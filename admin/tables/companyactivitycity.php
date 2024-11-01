<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanyActivityCity extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_activity_city', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	
	public function deleteNotContainedCities($companyId, $cities) {
		
		if(empty($cities)){
			$cities = array(-1);
		}

		$db =JFactory::getDBO();
		$cities = implode(",", $cities);
		$sql = "delete from #__jbusinessdirectory_company_activity_city where company_id= $companyId and city_id not in ($cities)";

		$db->setQuery($sql);
		return $db->execute();
	}
	
	public function getActivityCities($companyId) {
		$db =JFactory::getDBO();
		$query = "select ac.*, c.name, c.id from #__jbusinessdirectory_company_activity_city ac
				  left join #__jbusinessdirectory_cities c on ac.city_id=c.id
				  where company_id= $companyId";
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	public function getActivityCity($companyId, $cityId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_activity_city where company_id= $companyId and city_id=$cityId";
		$db->setQuery($query);
		
		return $db->loadObject();
	}
}
