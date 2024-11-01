<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

use MVC\Utilities\ArrayHelper;

class TableAnnouncements extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_announcements', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCompanyOptions($itemId, $companyId) {
		$enablePackage = JBusinessUtil::getApplicationSettings()->enable_packages;
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

		$db = JFactory::getDbo();
		$query = "select c.id as value, c.name as text, GROUP_CONCAT(DISTINCT pf.feature) as features
                from #__jbusinessdirectory_companies c 
                LEFT join  #__jbusinessdirectory_company_announcements rc on rc.company_id=c.id 
                left join #__jbusinessdirectory_orders inv on inv.company_id=c.id 
                left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or c.package_id = p.id) and p.status=1 and $enablePackage)
                left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
                where c.id = '$companyId' and rc.id=$itemId $packageFilter
                " . (($enablePackage) ? " HAVING FIND_IN_SET('" . ANNOUNCEMENTS . "',features) " : "") . "
                ";

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

	public function changeState($itemId, $value) {
		$db = JFactory::getDBO();
		$itemId = $db->escape($itemId);
		$query = "UPDATE #__jbusinessdirectory_company_announcements SET status = '$value' WHERE id = " . $itemId;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}


	public function getTotalUserAnnouncements($companyIds, $userId, $approved = false) {
		$db = JFactory::getDBO();

		$comCond = "";
		if (count($companyIds) > 0) {
			$companyIds = implode(",", $companyIds);
			$comCond = " and co.company_id in ($companyIds)";
		} else {
			return 0;
		}

		$approvedCond = "";
		if ($approved) {
			$approvedCond = " and co.status='1' ";
		}

		$query = "select * from #__jbusinessdirectory_company_announcements co
					left join #__jbusinessdirectory_companies cp on cp.id = co.company_id 
					where 1 $comCond or cp.userId = $userId $approvedCond";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getUserAnnouncements($userId, $companyIds, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDBO();

		if (empty($companyIds) && empty($userId)) {
			return null;
		}

		if (empty($companyIds)) {
			$companyIds = array(-1);
		}

		$companyIds = implode(",", $companyIds);

		$query = "select co.*, cp.name as companyName 
					from 
					#__jbusinessdirectory_company_announcements co
					left join #__jbusinessdirectory_companies cp on cp.id = co.company_id
					where co.company_id in ($companyIds) or cp.userId = $userId
					group by co.id	
					order by co.id desc ";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getCompanyAnnouncements($companyId) {
		$db = JFactory::getDBO();
		$query = "SELECT co.*,cp.name AS companyName
                      FROM #__jbusinessdirectory_company_announcements co
					LEFT JOIN #__jbusinessdirectory_companies cp ON cp.id = co.company_id 
					WHERE co.status=1 AND cp.id=$companyId and (expiration_date >= DATE(NOW()) or expiration_date is null or expiration_date='0000-00-00')
					ORDER BY co.title";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
