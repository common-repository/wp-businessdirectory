<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanyLocations extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_locations', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCompanyLocations($companyId) {
		$db =JFactory::getDBO();
		$query = "select a.*,b.country_name as country from #__jbusinessdirectory_company_locations a
				  left join #__jbusinessdirectory_countries b on a.countryId = b.id
				  where company_id=".$companyId;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getMaxLocationNumberOnCompany() {
		$db =JFactory::getDBO();
		$query = "select count(*) as maxLocations FROM #__jbusinessdirectory_company_locations GROUP BY company_id ORDER BY count(*) DESC Limit 1";
		$db->setQuery($query);
		$object = $db->loadObject();
		
		if (empty($object)) {
			return 0;
		}
		
		return $object->maxLocations;
	}

	public function getAllCompanyLocations($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_locations where company_id=$companyId order by id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function deleteAllCompanyLocations($companyId) {
		$db =JFactory::getDBO();
		$query = "delete FROM #__jbusinessdirectory_company_locations WHERE company_id=$companyId";
		$db->setQuery($query);
		return $db->execute();
	}

	public function updateCompanyLocations($companyId, $identifier) {
		$db =JFactory::getDBO();
		$query = "update #__jbusinessdirectory_company_locations
                    set company_id=$companyId
                     WHERE identifier='$identifier' ";
		$db->setQuery($query);
		return $db->execute();
	}

	public function deleteCompanyLocations($identifier) {
		$db =JFactory::getDBO();
		$query = "delete FROM #__jbusinessdirectory_company_locations WHERE identifier='$identifier' ";
		$db->setQuery($query);
		return $db->execute();
	}
}
