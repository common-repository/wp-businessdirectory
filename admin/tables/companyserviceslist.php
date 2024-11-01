<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanyServicesList extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_services_list', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getPriceList($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_services_list where companyId=$companyId order by service_section, id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function deleteCompanyServices($companyId, $ids = null) {
		if (!empty($ids)) {
			$query = "delete from #__jbusinessdirectory_company_services_list where companyId in ($companyId) and id not in ($ids)";
		} else {
			$query = "delete from #__jbusinessdirectory_company_services_list where companyId in ($companyId)";
		}
		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	public function getCompanyOptions($companyId) {
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
                left join #__jbusinessdirectory_orders inv on inv.company_id=c.id 
                left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or c.package_id = p.id) and p.status=1 and $enablePackage)
                left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
                where c.id = '$companyId' $packageFilter
                " . (($enablePackage) ? " HAVING FIND_IN_SET('" . SERVICES_LIST . "',features) " : "") . "
                ";

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

	public function getUserPriceLists($userId, $companyIds, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDBO();
		JBusinessUtil::setGroupConcatLenght();

		if (empty($companyIds) && empty($userId)) {
			return null;
		}

		if (empty($companyIds)) {
			$companyIds = array(-1);
		}

		$companyIds = implode(",", $companyIds);

		$query = "select group_concat(DISTINCT cpl.id,' - ',cpl.service_section,' - ',cpl.service_name,' - ',cpl.service_price  ORDER BY service_section,service_name  SEPARATOR '##') as services,
                      cp.name as companyName,cp.id as companyId
					from #__jbusinessdirectory_company_services_list cpl
					left join #__jbusinessdirectory_companies AS cp ON cp.id=cpl.companyId
					where cpl.companyId in ($companyIds) or cp.userId = $userId
					group by cpl.companyId
					order by cp.id desc ";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getTotalUserPriceLists($companyIds, $userId) {
		$db = JFactory::getDBO();

		if (count($companyIds) == 0 && empty($userId)) {
			return null;
		}

		$comCond = "";
		if (count($companyIds) > 0) {
			$companyIds = implode(",", $companyIds);
			$comCond = " and cpl.companyId in ($companyIds) or cp.userId = $userId";
		} else {
			return 0;
		}

		$query = "select group_concat(DISTINCT cpl.id,' - ',cpl.service_section,' - ',cpl.service_name,' - ',cpl.service_price  ORDER BY service_section,service_name  SEPARATOR '##') as services,
                      cp.name as companyName,cp.id as companyId
					from #__jbusinessdirectory_company_services_list cpl
					left join #__jbusinessdirectory_companies AS cp ON cp.id=cpl.companyId
					where 1 $comCond
					group by cpl.companyId
					order by cp.id desc ";
		$db->setQuery($query);
		$db->execute();

		return $db->getNumRows();
	}
}
