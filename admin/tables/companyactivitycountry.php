<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class JTableCompanyActivityCountry
 */
class JTableCompanyActivityCountry extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_activity_country', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function deleteNotContainedCountries($companyId, $countries) {

		if(empty($countries)){
			$countries = array(-1);
		}

		$db      = JFactory::getDbo();
		$countries = implode(",", $countries);
		$sql     = "delete from #__jbusinessdirectory_company_activity_country where company_id= $companyId and country_id not in ($countries)";

		$db->setQuery($sql);
		return $db->execute();
	}

	public function getActivityCountries($companyId) {
		$db    = JFactory::getDbo();
		$query = "select ac.*, c.country_name as name, c.id from #__jbusinessdirectory_company_activity_country ac
					left join #__jbusinessdirectory_countries c on ac.country_id=c.id
					where company_id= $companyId";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getActivityCountry($companyId, $countryId) {
		$db    = JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_company_activity_country where company_id= $companyId and country_id = $countryId";
		$db->setQuery($query);

		return $db->loadObject();
	}
}
