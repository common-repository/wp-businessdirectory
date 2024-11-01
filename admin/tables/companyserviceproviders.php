<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JTableCompanyServiceProviders extends JTable {
	/**
	 * Constructor
	 *
	 * @param $db JDatabaseDriver connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_providers', 'id', $db);
	}

	/**
	 * Get the provider based on providerId
	 *
	 *
	 * @param [type] $providerId
	 * @return void
	 */
	public function getProvider($providerId){
		$db =JFactory::getDbo();
		$query = "select * 
                  from #__jbusinessdirectory_company_providers as sp
				  where id=".$providerId;

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}
	
	public function changeState($serviceProviderId) {
		$db = JFactory::getDbo();

		$serviceProviderId = (int) $serviceProviderId;
		$query             = "UPDATE #__jbusinessdirectory_company_providers SET published = IF(published, 0, 1) WHERE id = " . $serviceProviderId;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getStaffTimetable($staffId, $type, $itemType = STAFF_HOURS) {
		$db = JFactory::getDbo();
		$query = "select cph.*, GROUP_CONCAT(start_hour) as startHours, GROUP_CONCAT(end_hour) as endHours, 
                  GROUP_CONCAT(id) as periodIds, GROUP_CONCAT(status) as statuses
                  from #__jbusinessdirectory_company_provider_hours as cph
                  where cph.provider_id = $staffId and cph.type = $type and cph.item_type = $itemType
                  group by cph.weekday
                  order by cph.weekday";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getSelectedServices($providerId) {
		$db =JFactory::getDbo();
		$query = "select cps.service_id as id
                  from #__jbusinessdirectory_company_providers as sp
				  inner join #__jbusinessdirectory_company_provider_services cps  on sp.id = cps.provider_id  
				  where provider_id=".$providerId;

		$db->setQuery($query);
		$list = $db->loadObjectList();

		return $list;
	}

	public function getServiceProviders($serviceId) {
		$db = JFactory::getDbo();

		$serviceId = $db->escape($serviceId);
		$query = "select sp.*
                  from #__jbusinessdirectory_company_providers as sp
				  inner join #__jbusinessdirectory_company_provider_services cps  on sp.id = cps.provider_id  
				  where service_id = $serviceId and sp.published = 1";

		$db->setQuery($query);
		$list = $db->loadObjectList();

		return $list;
	}

	public function getUserProviders($companyIds) {
		$db         = JFactory::getDbo();
		$companyIds = implode(",", $companyIds);

		if (empty($companyIds)) {
			return null;
		}
		$query = "select cpr.*, cp.name as companyName
					from 
					#__jbusinessdirectory_company_providers cpr
					left join #__jbusinessdirectory_companies cp on cp.id = cpr.company_id
					where company_id in ($companyIds)
					group by cpr.id	
					order by cpr.id desc ";

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
