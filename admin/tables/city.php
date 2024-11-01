<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCity extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_cities', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCity($cityId) {
		$db    = JFactory::getDbo();

		$where = "name = '$cityId'";
		if(is_numeric($cityId)){
			$where = "id = $cityId";
		}

		$query = "SELECT *, name as cityName FROM #__jbusinessdirectory_cities where $where ORDER BY name";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCityByName($city) {
		$db    = JFactory::getDbo();
		$query = "SELECT *, name as cityName FROM #__jbusinessdirectory_cities where name = '$city' ORDER BY name";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCities() {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$orderBy = ' name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}
		
		$db    = JFactory::getDbo();
		$query = "SELECT * FROM #__jbusinessdirectory_cities ORDER BY " .$orderBy;
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getCitiesByRegions($regionIds) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$orderBy = ' c.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = 'c.ordering asc';
		}

		$db    = JFactory::getDbo();
		$query = "select c.* 
				  from #__jbusinessdirectory_cities as c
				  where c.region_id in ($regionIds)
				  order by $orderBy";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getCitiesByRegion($regionId) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' c.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' c.ordering asc';
		}

		$whereCond = '';
		if (!empty($regionId)) {
			$whereCond = ' and c.region_id=' . $regionId;
		}

		$query = "select c.* 
				  from #__jbusinessdirectory_cities as c
				  left join #__jbusinessdirectory_regions as rg on rg.id = c.region_id
				  where 1 $whereCond
				  order by $orderBy";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getCitiesByRegionName($region) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' c.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' c.ordering asc';
		}
		
		$_region = $db->escape($region);
		$query = "select c.* 
				  from #__jbusinessdirectory_cities as c
				  left join #__jbusinessdirectory_regions as rg on rg.id = c.region_id
				  where rg.name = '$_region'
				  order by $orderBy";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getCitiesByCountry($countryId) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' c.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' c.ordering asc';
		}

		$whereCond = '';
		if (!empty($countryId)) {
			$whereCond = ' and rg.country_id = ' . $countryId;
		}

		$query = "select c.* 
				  from #__jbusinessdirectory_cities as c
				  left join #__jbusinessdirectory_regions as rg on rg.id = c.region_id
				  left join #__jbusinessdirectory_countries as ctr on ctr.id  = rg.country_id
				  where 1 $whereCond
				  order by $orderBy";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getCitiesForExport() {
		$db =JFactory::getDBO();
		$query = "select name as cityName, region_id as regionId from #__jbusinessdirectory_cities order by id desc";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get city suggestions based on keyword
	 *
	 * @return void
	 */
	public function getCitySuggestions($keyword){
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDbo();

		$orderBy = ' c.name';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' c.ordering asc';
		}

		$whereCond = '';
		if (!empty($keyword)) {
			$whereCond = " and c.name like '%$keyword%'";
		}

		$query = "select c.*, rg.name as region_name, rg.id as region_id, cnt.id as country_id
					from #__jbusinessdirectory_cities as c
					left join #__jbusinessdirectory_regions as rg on rg.id = c.region_id
					left join #__jbusinessdirectory_countries as cnt on cnt.id = rg.country_id
					where 1 $whereCond
					order by cnt.country_name, $orderBy, rg.name
					limit 0,10";
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;

	}
	
	public function checkCityExists($cityName, $regionId) {
		$db     = JFactory::getDbo();
		$cityName = $db->escape($cityName);

		$query = "select c.id
				  from #__jbusinessdirectory_cities as c
				  where c.name = '$cityName' and c.region_id = '$regionId'";

		$db->setQuery($query);		
		return $db->loadResult();
	}
}
