<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableEventAssociatedCompanies extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_event_associated_items', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Get all associated companies for an event
	 *
	 * @param  $eventId  int ID of the event
	 * @return array    list of company Ids
	 */
	public function getAssociatedCompaniesByEvent($eventId) {
		$db =JFactory::getDbo();
		$query = "select company_id
                  from #__jbusinessdirectory_company_event_associated_items eac
                  LEFT join #__jbusinessdirectory_companies c on eac.company_id=c.id
                  where eac.event_id=".$eventId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->company_id;
		}

		return $result;
	}

	/**
	 * Get associated Companies as options
	 *
	 * @param $eventId int
	 * @return mixed
	 */
	public function getAssociatedCompanyOptions($eventId) {
		$db = JFactory::getDbo();
		$ids = $this->getAssociatedCompaniesByEvent($eventId);
		$comp_id = implode(',', $ids);
		$query ="select c.id, c.name
                 from #__jbusinessdirectory_companies c
				 LEFT join  #__jbusinessdirectory_company_event_associated_items eac on eac.company_id=c.id
				 where c.id IN ($comp_id) and eac.event_id = $eventId";
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}


	/**
	 * Add associated Companies in database
	 *
	 * @param $eventId int ID of the event
	 * @param $associatedCompanyIds array ID of the associated companies
	 * @return bool|void
	 */
	public function storeAssociatedCompanies($eventId, $associatedCompanyIds, $excludedIds) {
		$db = JFactory::getDBO();

		if(isset($associatedCompanyIds)){
			$associatedCompanyIds = array_filter($associatedCompanyIds);
		}
		
		if(!empty($associatedCompanyIds)){
			$query = "insert into #__jbusinessdirectory_company_event_associated_items(event_id, company_id) values ";
			foreach ($associatedCompanyIds as $assocId) {
				$query = $query . "(" . $eventId . "," . $assocId . "),";
			}
			$query = substr($query, 0, -1);
			$query = $query . " ON DUPLICATE KEY UPDATE event_id=values(event_id), company_id=values(company_id) ";

			$db->setQuery($query);

			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
		}

		if(!empty($excludedIds)){
			$filter = implode(",",$excludedIds);
			$query = "delete from #__jbusinessdirectory_company_event_associated_items where event_id = $eventId and company_id in ($filter) ";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
		}
		
		if(!empty($excludedIds) && in_array(-11,$excludedIds) && !empty($associatedCompanyIds)){
			$filter = implode(",",$associatedCompanyIds);
			$query = "delete from #__jbusinessdirectory_company_event_associated_items where event_id = $eventId and company_id not in ($filter) ";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
		}
	}

	/**
	 * Get all active associated Companies for a particular event
	 *
	 * @param $searchDetails array containing the search parameters
	 * @return array|void
	 */
	public function getAssociatedCompaniesDetails($searchDetails) {
		$eventId = isset($searchDetails['eventId'])?$searchDetails['eventId']:null;
		$userId = isset($searchDetails["userId"])?$searchDetails["userId"]:null;

		$appSettings = JBusinessUtil::getApplicationSettings();
		$enablePackage = $appSettings->enable_packages;
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));

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

		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}

		$whereUser = '';
		if (!empty($userId)) {
			$whereUser .= " and cp.userId = ".$userId;
		}

		$publishFilter= " ";
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";

		$db =JFactory::getDbo();
		$query = "select DISTINCT cp.* 
                    from #__jbusinessdirectory_companies cp 
                    left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
                    left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
                    WHERE cp.id IN (
                    select eac.company_id 
                    from #__jbusinessdirectory_companies c
                    left join #__jbusinessdirectory_company_event_associated_items eac  
                    on eac.company_id=c.id  where eac.event_id=$eventId
                    ) and cp.state = 1 $packageFilter $companyStatusFilter $publishFilter $whereUser 
					order by cp.name";

		$db->setQuery($query);
		$list = $db->loadObjectList();
		$comp = array();
		foreach ($list as $item) {
			$comp[] = $item;
		}
		return $comp;
	}

	/**
	 * Get all active associated events for a particular company
	 *
	 * @param $companyId int ID of the company
	 * @return array|void
	 */
	public function getAssociatedEventsDetails($companyId, $limitstart = 0, $limit = 0) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$enablePackage = $appSettings->enable_packages;
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));

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

		$approvalFilter="and (ce.approved = ".EVENT_APPROVED.")";
		if ($showPendingApproval) {
			$approvalFilter = "and (ce.approved = ".EVENT_CREATED." or ce.approved = ".EVENT_APPROVED.") ";
		}

		$whereDateCond=" and ce.end_date>= DATE(NOW())";

		$db =JFactory::getDbo();
		$query = "select DISTINCT ce.* 
                    from #__jbusinessdirectory_company_events ce
                    left join #__jbusinessdirectory_companies cp on cp.id = ce.company_id
                    left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
                    left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
                    WHERE ce.id IN (
                    select eac.event_id
                    from #__jbusinessdirectory_company_events co
                    left join #__jbusinessdirectory_company_event_associated_items eac  
                    on eac.event_id=co.id  where eac.company_id=$companyId
                    ) and ce.state = 1 $approvalFilter $packageFilter $whereDateCond";
		
		$db->setQuery($query, $limitstart, $limit);
		$list = $db->loadObjectList();
		$events = array();
		foreach ($list as $item) {
			$events[] = $item;
		}
		return $events;
	}
}
