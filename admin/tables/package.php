<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTablePackage extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_packages', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function getPackage($packageId) {
		$db =JFactory::getDBO();
		$packageId = $db->escape($packageId);
		$query = "select p.* , group_concat(pf.feature) as featuresS
					from #__jbusinessdirectory_packages p
					left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
					where p.id=".$packageId;
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}
	
	public function getCompanyPackage($companyId) {
		$db =JFactory::getDBO();

		$companyId = $db->escape($companyId);
		$query = "select p.*, group_concat(pf.feature) as featuresS, inv.start_date
				  from #__jbusinessdirectory_packages p
				  inner join #__jbusinessdirectory_companies cp on cp.package_id=p.id  
				  left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
				  left join #__jbusinessdirectory_orders inv on p.id=inv.package_id and inv.company_id=cp.id
				  where p.status =1 and cp.id=$companyId and p.package_type=".PACKAGE_TYPE_BUSINESS."
				  group by p.id" ;

		$db->setQuery($query);
		$package = $db->loadObject();
		
		if (isset($package)) {
			$package->features = explode(",", $package->featuresS);
		}
		//dump($query);
		return $package;
	}

	public function getPackages($showAdmin = true, $showAll = false, $type=null) {
		$db =JFactory::getDBO();
		
		JBusinessUtil::setGroupConcatLenght();

		$packageFilter = "";
		if (!$showAdmin) {
			$packageFilter = " and (p.only_for_admin = 0)";
		}

		$filterType = "";
		if (!empty($type)) {
			$filterType = " and package_type = $type ";
		}

		$statusFilter = "";
		if (!$showAll) {
			$statusFilter = " and p.status =1 ";
		}
		
		$query = "select p.* , group_concat(pf.feature order by pf.id separator '#') as featuresS
			from #__jbusinessdirectory_packages p
			left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
			where 1 $statusFilter $packageFilter $filterType 
			group by p.id
			order by p.ordering asc";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	public function changeState($packageId) {
		$db =JFactory::getDBO();
		$packageId = $db->escape($packageId);
		$query = 	" UPDATE #__jbusinessdirectory_packages SET status = IF(status, 0, 1) WHERE id = ".$packageId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function changePopularState($packageId) {
		$db =JFactory::getDBO();
		$packageId = $db->escape($packageId);
		$query = 	" UPDATE #__jbusinessdirectory_packages SET popular = IF(popular, 0, 1) WHERE id = ".$packageId ;
		$db->setQuery($query);
	
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function increaseViewCount($packageId) {
		$db =JFactory::getDBO();
		$packageId = $db->escape($packageId);
		$query = "update  #__jbusinessdirectory_packages set viewCount = viewCount + 1 where id=$packageId";
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function insertRelations($packageId, $features) {
		$db =JFactory::getDBO();
		$packageId = $db->escape($packageId);
		$query = "delete from #__jbusinessdirectory_package_fields where package_id = $packageId";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		
		if (empty($features)) {
			return;
		}
		
		$query = "insert into #__jbusinessdirectory_package_fields(package_id, feature) values ";
		foreach ($features as $feature) {
			$query = $query."(".$packageId.",'".$db->escape($feature)."'),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE package_id=values(package_id), feature=values(feature) ";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	}
	
	public function getSelectedFeatures($packageId = null) {
		$db = JFactory::getDBO();

		if (empty($packageId)) {
			return array();
		}

		$query = "select * from #__jbusinessdirectory_package_fields where package_id=$packageId order by id asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getSelectedFeaturesAsString($packageId) {
		$db =JFactory::getDBO();
		$query = "select feature from #__jbusinessdirectory_package_fields where package_id= $packageId";
		$db->setQuery($query);
		$features = $db->loadObjectList();
		if (empty($features)) {
			return null;
		}

		$result = implode(",", array_column($features, 'feature'));
				
		return $result;
	}
	
	public function getLastActivePackage($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = "select * , inv.id as invoice_id
		          from #__jbusinessdirectory_packages p
				  left join #__jbusinessdirectory_orders inv on p.id=inv.package_id
				  where p.status=1 and inv.company_id=$companyId and inv.state = ".PAYMENT_STATUS_PAID."
				  #group by inv.company_id
				  order by inv.id desc";

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}
	
	public function getLastPaidPackage($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$companyId = intval($companyId);
		$query = "select * , inv.id as invoice_id,  max(p.ordering) 
				from #__jbusinessdirectory_packages p
				left join #__jbusinessdirectory_orders inv on p.id=inv.package_id
				where p.status=1 and inv.company_id=$companyId and inv.state = ".PAYMENT_STATUS_PAID."
				group by inv.company_id
				order by inv.id desc";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function getLastPackage($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = "select inv.* , p.days, inv.start_date, p.expiration_type
				from #__jbusinessdirectory_orders inv
				inner join #__jbusinessdirectory_packages p on p.id=inv.package_id
				where p.status=1 and inv.company_id=$companyId 
				order by inv.start_date desc, inv.id desc";
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	
	public function getCurrentActivePackage($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$companyId = intval($companyId);
		
		$query ="select id, package_id from #__jbusinessdirectory_companies where id= $companyId";
		$db->setQuery($query);
		$company =  $db->loadObject();

		if (empty($company)) {
			$company = new stdClass();
			$company->package_id = -1;
		}
		
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
								((p.expiration_type=1 and p.price=0) or (p.id = $company->package_id and p.price=0)))
						)";

		$query = "select * , p.id as pck_id, inv.id as invoice_id,  max(p.ordering) as ordering, GROUP_CONCAT(pf.feature) as featuresS 
				from #__jbusinessdirectory_packages p
				left join #__jbusinessdirectory_orders inv on p.id=inv.package_id and inv.company_id=$companyId 
				left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
				where p.status=1 $packageFilter 
				group by p.id
				order by p.price desc, p.ordering desc";

		$db->setQuery($query);
		$package = $db->loadObject();
		//dump($package);
		if (isset($package)) {
			$package->features = explode(",", $package->featuresS);
		}
		
		return $package;
	}
	
	public function getUserActivePackage($userId, $packageType) {
		$db =JFactory::getDBO();
		$userId = intval($userId);
		
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
								((p.expiration_type=1 and p.price=0)))
						)";

		$query = "select * , p.id as pck_id, inv.id as invoice_id,  max(p.ordering) as ordering, GROUP_CONCAT(pf.feature) as featuresS 
				from #__jbusinessdirectory_packages p
				left join #__jbusinessdirectory_orders inv on p.id=inv.package_id and inv.user_id=$userId 
				left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
				where p.status=1 and p.package_type=$packageType $packageFilter 
				group by p.id
				order by p.price desc, p.ordering desc";

		$db->setQuery($query);
		$package = $db->loadObject();
		if (isset($package)) {
			$package->features = explode(",", $package->featuresS);
		}
		
		return $package;
	}

	public function getPackagePayment($companyId, $packageId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$companyId = intval($companyId);
		$packageId = $db->escape($packageId);
		
		$query = "select * , inv.id as invoice_id, max(p.ordering) 
				from #__jbusinessdirectory_packages p
				left join #__jbusinessdirectory_orders inv on p.id=inv.package_id
				where p.status=1 and inv.company_id=$companyId and p.id=$packageId 
				and inv.state = ".PAYMENT_STATUS_PAID." and (inv.end_date > now() or inv.end_trial_date > now() or p.expiration_type=1) 
				group by p.id
				order by p.price desc";
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function getDefaultPackage() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_packages where status = 1 and only_for_admin = 0 and package_type = ".PACKAGE_TYPE_BUSINESS." order by price asc, ordering";
		$db->setQuery($query);
		
		return $db->loadObject();
	}
	
	public function getFreePackage() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_packages where status = 1 and price=0 order by ordering";
		$db->setQuery($query);
	
		return $db->loadObject();
	}
	
	public function updateUnassignedCompanies($packageId) {
		$db =JFactory::getDBO();
		$query = "update #__jbusinessdirectory_companies set package_id=$packageId where package_id not in ( select id from #__jbusinessdirectory_packages order by ordering)";
		$db->setQuery($query);
		//dump($query);
		return $db->execute();
	}
	
	public function getUserActivePackages($userId) {
		$db =JFactory::getDBO();
		$userId = $db->escape($userId);

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
								(p.expiration_type=1 and p.price=0))
						)";

		$query = "select *, inv.id as invoice_id,  max(p.ordering) as ordering, GROUP_CONCAT(pf.feature) as featuresS 
				from #__jbusinessdirectory_packages p
				left join #__jbusinessdirectory_orders inv on p.id=inv.package_id and inv.company_id=-1 and inv.user_id = $userId
				left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
				where p.status=1 $packageFilter 
				group by inv.id";

		$db->setQuery($query);
		$packages = $db->loadObjectList();

		if (isset($packages)) {
			foreach($packages as $package) {
				$package->features = explode(",", $package->featuresS);
			}
		}

		return $packages;
	}

}
