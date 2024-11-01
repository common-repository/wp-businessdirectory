<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableRegion extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_regions', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getRegion($regionId) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}

		$where = "name = '$regionId'";
		if(is_numeric($regionId)){
			$where = "id = $regionId";
		}

		$query = "SELECT *, name as regionName FROM #__jbusinessdirectory_regions where $where ORDER BY ".$orderBy;
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getRegionByName($region) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}

		$db    = JFactory::getDbo();
		$query = "SELECT *, name as regionName FROM #__jbusinessdirectory_regions where name = '$region' ORDER BY ".$orderBy;
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getRegions() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}

		$query = "SELECT * FROM #__jbusinessdirectory_regions ORDER BY " .$orderBy;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getRegionsByCountry($countryId) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' rg.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' rg.ordering asc';
		}


		$whereCond = '';
		if (!empty($countryId)) {
			$whereCond = ' and rg.country_id = ' . $countryId;
		}

		$query = "select rg.* 
				  from #__jbusinessdirectory_regions as rg
				  where 1 $whereCond
				  order by $orderBy";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getRegionSuggestions($keyword){
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' rg.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' rg.ordering asc';
		}

		$whereCond = '';
		if (!empty($keyword)) {
			$whereCond = " and rg.name like '%$keyword%'";
		}

		$query = "select rg.*, cnt.id as country_id, cnt.country_name as country_name
					from #__jbusinessdirectory_regions as rg 
					left join #__jbusinessdirectory_countries as cnt on cnt.id = rg.country_id
					where 1 $whereCond
					order by cnt.country_name,$orderBy
					limit 0, 10";
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;

	}

	public function checkRegionExists($regionName, $countryId) {
		$db     = JFactory::getDbo();
		$regionName = $db->escape($regionName);

		$query = "select rg.id
				  from #__jbusinessdirectory_regions as rg
				  where rg.name = '$regionName' and rg.country_id = '$countryId'";

		$db->setQuery($query);		
		return $db->loadResult();
	}
}
