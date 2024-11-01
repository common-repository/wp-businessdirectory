<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableProductMerchants extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_product_merchants', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Get all product merchants
	 *
	 * @param  $itemId  int ID of the product
	 * @return array    list of company Ids
	 */
	public function getMerchantsByProduct($itemId) {
		$db =JFactory::getDbo();
		$query = "select eac.company_id
                  from #__jbusinessdirectory_company_product_merchants eac
                  where eac.product_id=".$itemId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->company_id;
		}
		return $result;
	}

	/**
	 * Get merchants as options
	 *
	 * @param $itemId int
	 * @return mixed
	 */
	public function getMerchantsOptions($itemId) {
		$db = JFactory::getDbo();
		$ids = $this->getMerchantsByProduct($itemId);
		$comp_id = implode(',', $ids);
		$query ="select c.id, c.name
                 from #__jbusinessdirectory_companies c
				 LEFT join  #__jbusinessdirectory_company_product_merchants eac on eac.company_id=c.id
				 where c.id IN ($comp_id) and eac.product_id = $itemId";
		$db->setQuery($query);

		$options = $db->loadObjectList();
		return $options;
	}


	/**
	 * Add Merchants in database
	 *
	 * @param $productId int ID of the product
	 * @param $merchantIds array ID of the merchants
	 * @return bool|void
	 */
	public function storeMerchants($productId, $merchantIds) {
		$db = JFactory::getDBO();

		if (empty($merchantIds)) {
			$query = "delete from #__jbusinessdirectory_company_product_merchants where product_id =$productId";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
			return true;
		}

		$query = "insert into #__jbusinessdirectory_company_product_merchants(product_id, company_id) values ";
		foreach ($merchantIds as $id) {
			$query = $query . "(" . $productId . "," . $id . "),";
		}
		$query = substr($query, 0, -1);
		$query = $query . " ON DUPLICATE KEY UPDATE product_id=values(product_id), company_id=values(company_id) ";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		$filter = "(";
		foreach ($merchantIds as $assocId) {
			$filter = $filter . $assocId . ",";
		}
		$filter = substr($filter, 0, -1);
		$filter = $filter . ")";
		$query = "delete from #__jbusinessdirectory_company_product_merchants where product_id =$productId and company_id not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	}

	/**
	 * Get all active Merchants for a particular product
	 *
	 * @param $searchDetails array containing the search parameters
	 * @return array|void
	 */
	public function getMerchantsDetails($searchDetails) {
		$itemId = isset($searchDetails['productId'])?$searchDetails['productId']:null;
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

		$db =JFactory::getDbo();
		$query = "select DISTINCT cp.* ,GROUP_CONCAT( DISTINCT cpt.picture_path) as pictures
                    from #__jbusinessdirectory_companies cp 
                    left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
                    left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
                    left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=cp.id and cpt.picture_enable = 1
					WHERE cp.id IN (
                    select eac.company_id 
                    from #__jbusinessdirectory_companies c
                    left join #__jbusinessdirectory_company_product_merchants eac  
                    on eac.company_id=c.id  where eac.product_id=$itemId
                    ) and cp.state = 1 $packageFilter $companyStatusFilter $whereUser 
                    group by cp.id";

		$db->setQuery($query);
		$list = $db->loadObjectList();
		$comp = array();
		foreach ($list as $item) {
			$comp[] = $item;
		}
		return $comp;
	}
}
