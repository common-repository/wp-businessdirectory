<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCountry extends JTable {
	public $id = null;
	public $country_name = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_countries', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCountries($countryIds = null) {
		$db    = JFactory::getDBO();

		$countryFilter = "";
		if(!empty($countryIds)){
			if(is_array($countryIds)){
				$countryIds = implode(",",$countryIds);
			}
			$countryFilter = " where id in ($countryIds)";
		}

		$query = "SELECT * FROM #__jbusinessdirectory_countries $countryFilter ORDER BY country_name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get the countries that have regions associated
	 *
	 * @param [type] $countryIds
	 * @return void
	 */
	public function getCountriesWithRegions() {
		$db    = JFactory::getDBO();

		$query = "SELECT c.* FROM #__jbusinessdirectory_countries c
				  inner join #__jbusinessdirectory_regions r on c.id = r.country_id
				  group by c.id
				  ORDER BY c.country_name
				  ";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCountry($countryId) {
		$db        = JFactory::getDBO();
		$countryId = $db->escape($countryId);
		$query     = "select * from #__jbusinessdirectory_countries where id=$countryId";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getRegionsByCountry($countryId, $type = ITEM_TYPE_BUSINESS) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$db        = JFactory::getDBO();
		$countryId = $db->escape($countryId);

		$table = "#__jbusinessdirectory_companies";
		if ($type == ITEM_TYPE_OFFER) {
			$table = "#__jbusinessdirectory_company_offers";
		} elseif ($type == ITEM_TYPE_EVENT) {
			$table = "#__jbusinessdirectory_company_events";
		}

		$whereCond = '';
		if (!empty($countryId)) {
			$whereCond = ' and countryId =' . $countryId;
		}

		$query = "SELECT DISTINCT county FROM " . $table . " WHERE state=1 AND county!='' " . $whereCond . "
                    " . (($appSettings->show_secondary_locations && $type == ITEM_TYPE_BUSINESS) ? "
                  UNION
                  Select distinct county FROM #__jbusinessdirectory_company_locations
                   where 1 " . $whereCond . "
                    " : "") . "
                  ORDER BY county ASC";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCitiesByRegion($region, $type = ITEM_TYPE_BUSINESS) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$region      = $db->escape($region);

		$table = "#__jbusinessdirectory_companies";
		if ($type == ITEM_TYPE_OFFER) {
			$table = "#__jbusinessdirectory_company_offers";
		} elseif ($type == ITEM_TYPE_EVENT) {
			$table = "#__jbusinessdirectory_company_events";
		}

		$whereCond = '';
		if (!empty($region)) {
			$whereCond = ' and county="' . $region . '"';
		}

		$query = "SELECT DISTINCT city FROM " . $table . "
                  WHERE state =1 AND city!='' " . $whereCond . "
                    " . (($appSettings->show_secondary_locations && $type == ITEM_TYPE_BUSINESS) ? "
                  UNION
                  Select distinct city FROM #__jbusinessdirectory_company_locations
                  where 1 " . $whereCond . "
                    " : "") . "
                  ORDER BY city ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCitiesByCountry($countryId, $type = ITEM_TYPE_BUSINESS) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$countryId   = $db->escape($countryId);

		$table = "#__jbusinessdirectory_companies";
		if ($type == ITEM_TYPE_OFFER) {
			$table = "#__jbusinessdirectory_company_offers";
		} elseif ($type == ITEM_TYPE_EVENT) {
			$table = "#__jbusinessdirectory_company_events";
		}

		$whereCond = '';
		if (!empty($countryId)) {
			$whereCond = ' and countryId = ' . $countryId;
		}

		$query = "SELECT DISTINCT city FROM " . $table . "
                  WHERE state =1 AND city!='' " . $whereCond . "
                    " . (($appSettings->show_secondary_locations && $type == ITEM_TYPE_BUSINESS) ? "
                  UNION
                  Select distinct city FROM #__jbusinessdirectory_company_locations
                  where 1 " . $whereCond . "
                    " : "") . "
                  ORDER BY city ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
