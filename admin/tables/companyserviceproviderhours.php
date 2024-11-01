<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JTableCompanyServiceProviderHours extends JTable {

	/**
	 * Constructor
	 *
	 * @param $db JDatabaseDriver connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_provider_hours', 'id', $db);
	}

	public function deleteTimePeriods($staffId, $ids, $type, $itemType = STAFF_HOURS) {
		if (!empty($ids)) {
			$query = "delete from #__jbusinessdirectory_company_provider_hours where provider_id in ($staffId) and id not in ($ids) and type = $type and item_type = $itemType";
		} else {
			$query = "delete from #__jbusinessdirectory_company_provider_hours where provider_id in ($staffId) and type = $type and item_type = $itemType";
		}
		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	public function getVacations($staffId) {
		$db =JFactory::getDbo();
		$query = "select sp.*, GROUP_CONCAT(cph.weekday) as breakDays
                  from #__jbusinessdirectory_company_providers as sp
                  left join #__jbusinessdirectory_company_provider_hours as cph on sp.id = cph.provider_id
                  where cph.type = ".STAFF_WORK_HOURS." and cph.status = 0 and sp.id = $staffId";

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

	/**
	 * Get the working days for a provider
	 */
	public function getWorkingDays($providerId) {
		$db =JFactory::getDbo();
		$query = "select cph.date 
                  from  #__jbusinessdirectory_company_provider_hours cph 
                  where cph.type = ".STAFF_WORK_HOURS." and cph.status = 1 and cph.provider_id = $providerId";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getAvailableHours($serviceId, $providerId, $type, $date) {
		$db =JFactory::getDbo();

		$dateFilter = "";
		if ($type==2) {
			$dateFilter = " and cph.date = '".$date."'";
		} else {
			$dateFilter = " and cph.weekday = (dayofweek('".$date."')-1)";
		}

		$query = "select cph.*, cs.duration, cp.time_zone
                  from #__jbusinessdirectory_company_provider_hours as cph
                  left join #__jbusinessdirectory_company_providers as sp on sp.id = cph.provider_id
                  left join #__jbusinessdirectory_company_provider_services as cps on cps.provider_id = sp.id
                  left join #__jbusinessdirectory_company_services as cs on cps.service_id = cs.id
				  left join #__jbusinessdirectory_companies as cp on cp.id = cs.company_id
                  where status=1 and cph.provider_id = $providerId and cps.service_id = $serviceId 
                  $dateFilter
                  order by cph.start_hour, cph.id";

		$db->setQuery($query);
		$list = $db->loadObjectList();

		return $list;
	}
}
