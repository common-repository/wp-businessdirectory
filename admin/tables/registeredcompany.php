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

class JTableRegisteredCompany extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 5.4.0
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_registered', 'id', $db);
	}

	public function deleteNotUsedUserCompanies($companies, $companyId, $userId) {
		$db = JFactory::getDbo();
		$notInCondition = '';
		if (!empty($companies)) {
			$notInCondition = ' and joined_company_id not in (' . implode(',', $companies) . ')';
		}
		$query = "delete from #__jbusinessdirectory_company_registered where 1 $notInCondition and company_id = '$companyId' and user_id = '$userId'";
		$db->setQuery($query);
		$db->execute();
		return true;
	}

	public function getExistingUserCompanies($companyId, $userId) {
		$db = JFactory::getDbo();
		$query ="select eac.joined_company_id as companyId
				 FROM #__jbusinessdirectory_company_registered eac
				 where eac.company_id = '$companyId' and eac.user_id = '$userId' ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * insert Relation between company and the other ones that are requestion to join
	 *
	 * @param $companyToJoin int Main Company Id
	 * @param $companies array Ids of the companies trying to join
	 * @return bool
	 */
	public function joinCompany($companyToJoin, $companies) {
		$db = JFactory::getDBO();
		$user = JBusinessUtil::getUser();

		if (empty($companies)) {
			return true;
		}

		$query = 'insert into #__jbusinessdirectory_company_registered(company_id, joined_company_id,approved,user_id) values ';
		foreach ($companies as $company) {
			$query .= '(' . $companyToJoin . ",'" . $db->escape($company) . "','".LISTING_JOIN_STATUS_DISAPPROVED."','" . $user->ID . "'),";
		}
		$query = substr($query, 0, -1);

		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}

	/**
	 * Return companies joined/linked with company with id companyId
	 *
	 * @param $companyId int Id of the company that is searched for
	 * @return mixed
	 */
	public function getRegisteredCompanies($companyId) {
		$db = JFactory::getDbo();
		$query ="select c.id, c.name
                 from #__jbusinessdirectory_companies c
				 LEFT join  #__jbusinessdirectory_company_registered eac on eac.joined_company_id=c.id
				 where eac.company_id = '$companyId' ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 *  Get ids of joined companies
	 *
	 * @param $companyId int Id of the main company
	 * @return array
	 */
	public function getRegisteredCompaniesByMainCompany($companyId) {
		$db =JFactory::getDbo();
		$query = 'select joined_company_id
                  from #__jbusinessdirectory_company_registered eac
                  where eac.company_id="'.$companyId.'"';
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->joined_company_id;
		}

		return $result;
	}

	/**
	 * Change state to approve or disapprove
	 *
	 * @param $itemId int Item Id
	 * @param $value int Value of status
	 * @return bool
	 */
	public function changeState($itemId, $value) {
		$db = JFactory::getDBO();
		$itemId = $db->escape($itemId);
		$query = "UPDATE #__jbusinessdirectory_company_registered SET approved = '$value' WHERE id = " . $itemId;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	/**
	 * Get total join request by user
	 *
	 * @param $userId int user Id
	 * @return int
	 */
	public function getTotalJoinRequestsByUser($userId) {
		$db     = JFactory::getDbo();
		$userId = (int) $userId;

		$query = "select cg.*
				  from #__jbusinessdirectory_company_registered as cg
				  left join #__jbusinessdirectory_companies as cp on cp.id = cg.company_id
				  where cp.userId = $userId";

		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	/**
	 * Get all active associated Companies for a particular company
	 *
	 * @param $companyId int company Id
	 * @return array
	 */
	public function getAssociatedCompaniesDetails($companyId) {
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

		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";

		$state =" and cp.state = 1 and rc.approved='".LISTING_JOIN_STATUS_APPROVED."'";

		$db =JFactory::getDBO();
		$query = "select DISTINCT cp.* from #__jbusinessdirectory_companies cp 
                    left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
                    left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
                    left join #__jbusinessdirectory_company_registered rc on rc.joined_company_id=cp.id  
                    where rc.company_id=$companyId  $packageFilter $companyStatusFilter $publishFilter $state ";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$comp = array();
		foreach ($list as $item) {
			$comp[] = $item;
		}
		return $comp;
	}
}
