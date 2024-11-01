<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

use PayPal\Api\Patch;

defined('_JEXEC') or die('Restricted access');

class JTableOrder extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_orders', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getOrder($invoiceId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_orders where id=".$invoiceId;
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}
	
	public function getLastNonPaidCompanyOrder($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_orders where company_id=$companyId and state=0 order by id desc";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}
	
	/**
	 * Get the orders withouth the end date completed
	 *
	 * @param [type] $start
	 * @param [type] $limit
	 * @return void
	 */
	public function getOrdersWithoutEndDate($limitstart, $limit){
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_orders where end_date is null or end_date = '0000-00-00'";
		$db->setQuery($query, $limitstart, $limit);
		//dump($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getOrders($userId, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$query = "select inv.*,cp.name as business_name, cp.id as business_id, p.name, p.price, p.time_unit, p.time_amount, p.expiration_type
				  from #__jbusinessdirectory_companies cp
				  inner join #__jbusinessdirectory_orders inv on cp.id=inv.company_id
				  left join #__jbusinessdirectory_packages p on p.id = inv.package_id
				  where cp.userId = $userId  
				  order by inv.created desc";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	public function getLastUnpaidOrder($companyId, $lastPaidPackage) {
		$db =JFactory::getDBO();
		
		$whereFilter = "";
		if (!empty($lastPaidPackage)) {
			$whereFilter = "and id > $lastPaidPackage->id ";
		}
		
		$query = "select * from #__jbusinessdirectory_orders where company_id=$companyId $whereFilter and state=". PAYMENT_STATUS_NOT_PAID ." order by id desc";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}

	/**
	 * Retrieve the last unpaid order for listing for a user
	 *
	 * @param [type] $companyId
	 * @param [type] $lastPaidPackage
	 * @return void
	 */
	public function getUserLastUnpaidOrder($userId) {
		$db =JFactory::getDBO();
		
		$query = "select * 
					from #__jbusinessdirectory_orders inv
					left join #__jbusinessdirectory_companies cp on cp.id = inv.company_id
					where cp.userId=$userId and inv.state=". PAYMENT_STATUS_NOT_PAID ." 
					order by inv.id desc";

		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}
	
	public function getTotalIncome() {
		$db =JFactory::getDBO();
		$query = "SELECT sum(amount_paid) as amount FROM #__jbusinessdirectory_orders";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->amount;
	}
	
	public function getTodayIncome() {
		$db =JFactory::getDBO();
		$query = "SELECT sum(amount_paid) as amount FROM #__jbusinessdirectory_orders where  DATE(`paid_at`) = CURDATE() ";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->amount;
	}
	
	public function getWeekIncome() {
		$db =JFactory::getDBO();
		$query = "SELECT sum(amount_paid) as amount FROM #__jbusinessdirectory_orders  WHERE WEEKOFYEAR(paid_at)=WEEKOFYEAR(NOW())";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->amount;
	}
	
	public function getMonthIncome() {
		$db =JFactory::getDBO();
		$query = "SELECT sum(amount_paid) as amount FROM #__jbusinessdirectory_orders WHERE MONTH(paid_at)=MONTH(NOW())";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->amount;
	}
	
	public function getYearIncome() {
		$db =JFactory::getDBO();
		$query = "SELECT sum(amount_paid) as amount FROM #__jbusinessdirectory_orders  WHERE YEAR(paid_at)=YEAR(NOW())";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->amount;
	}
	
	public function updateExpirationEmailDate($orderId) {
		$db =JFactory::getDBO();
		$query = "update  #__jbusinessdirectory_orders set expiration_email_date = now() where id=$orderId";
		// 		dump($query);
		$db->setQuery($query);
		return $db->execute();
	}

	public function getNewIncome($start_date, $end_date) {
		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(paid_at, '%Y-%m-%d') as date, SUM(amount_paid) AS value
					from #__jbusinessdirectory_orders
					group by date
					having date IS NOT NULL
					union all
					select '$start_date' as date, 0 as value
					union all
					select '$end_date' as date, 0 as value
					order by date asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getOrdersForExport() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_orders order by id desc";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObjectList();
	}

	public function getTotalOrdersByUserId($userId) {
		$db =JFactory::getDBO();
		$query = "select inv.*,p.name as businessName from #__jbusinessdirectory_companies p
				  inner join #__jbusinessdirectory_orders inv on p.id=inv.company_id
				  where p.userId=$userId  
				  order by created desc";
		$db->setQuery($query);

		$db->execute();
		return $db->getNumRows();
	}
	

	/**
	 * Copy the original values from packages and taxes table
	 *
	 * @param unknown $orderId
	 */
	public function createOrderDetails($orderId, $packageId) {
		$db =JFactory::getDBO();
		$query = "insert into  #__jbusinessdirectory_order_packages (order_id, name, description, expiration_type, price, trial_price, trial_days,trial_period_unit,trial_period_amount,recurrence_count,special_price,special_from_date,
              special_to_date,days,time_unit,time_amount,ordering ) 
                (select $orderId as order_id ,name, SUBSTRING(description,1, 200) as description, expiration_type, price, trial_price, trial_days,trial_period_unit,trial_period_amount,recurrence_count,special_price,special_from_date,
              special_to_date,days,time_unit,time_amount,1 from
            #__jbusinessdirectory_packages p where p.id = $packageId)";
		//dump($query);
		//exit;
		$db->setQuery($query);
		$result = $db->execute();
		if (!$result) {
			return $result;
		}

		return $result;
	}

	public function createOrderTax($orderId, $type, $taxesData){
		$db = JFactory::getDBO();

		if (count($taxesData)==0 || empty($taxesData)) {
			return true;
		}

		$query = "insert into  #__jbusinessdirectory_order_taxes (order_id, tax_name, tax_type, tax_amount, tax_description, ordering,order_type) values";
		foreach ($taxesData as $tax) {
			$amountField = "tax_amount";
			if (isset($tax->country_amount)) {
				$amountField = "country_amount";
			}

			$query .= "('$orderId','" . $tax->tax_name . "','" . $tax->tax_type . "','" . $tax->$amountField . "','" . $tax->tax_description . "','" . $tax->ordering . "','$type'),";
		}
		$query = substr($query, 0, -1);

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}
 
	/**
	 *
	 * @return unknown
	 */
	public function deleteOrderDetails($orderId) {
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_order_packages where order_id = $orderId";
		$db->setQuery($query);
		$result = $db->execute();
		if (!$result) {
			return $result;
		}
		
		$query = "delete from #__jbusinessdirectory_order_taxes where order_id = $orderId";
		//dump($query);
		//exit;
		$db->setQuery($query);
		$result = $db->execute();
		
		$query = "update  #__jbusinessdirectory_orders 
				  set trial_initial_amount = null, trial_amount = null,trial_initial_amount = null, trial_days = null, end_trial_date = null,
				  discount_code =null, discount_amount = null
 				  where id = $orderId";
		$db->setQuery($query);
		return $db->execute();

		return $result;
	}
	
	/**
	 * Retrieve the order package
	 *
	 * @return package
	 */
	public function getOrderPackage($orderId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_order_packages p where order_id = $orderId";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}
	
	/**
	 * Retrieve the order details
	 *
	 * @return list of taxes
	 */
	public function getOrderTaxes($orderId, $type) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_order_taxes where order_id = $orderId and order_type = $type";
		$db->setQuery($query);
		
		$result = $db->loadObjectList();
		return $result;
	}

	public function getOfferOrderTaxes($orderIds, $type, $offerId) {
		$db =JFactory::getDBO();
		if (is_array($orderIds)) {
			$orderIds = implode(',', $orderIds);
		}
		$query = "select ot.*,coop.offer_id as offerId,coop.combination_ids
							from #__jbusinessdirectory_order_taxes ot
							left join #__jbusinessdirectory_company_offer_orders coo on coo.id = ot.order_id
							left join #__jbusinessdirectory_company_offer_order_products coop on coop.order_id=coo.id
					where ot.order_id in ($orderIds) and ot.order_type = $type and coop.offer_id = $offerId
					group by ot.id";
		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	
	public function getOrderByTransactionID($transactionID) {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_orders where transaction_id=".$transactionID;
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function setOrderTransactionID($orderId, $transactionID) {
		$db = JFactory::getDBO();

		$orderId = (int) $orderId;
		$query = "update  #__jbusinessdirectory_orders set transaction_id = '$transactionID' where id = $orderId";
		$db->setQuery($query);

		return $db->execute();
	}

	public function updateOrderVAT($orderId, $vatAmount, $vat) {
		$db = JFactory::getDBO();

		$orderId = (int) $orderId;
		$query = "update  #__jbusinessdirectory_orders set vat_amount = $vatAmount, vat = $vat where id = $orderId";
		$db->setQuery($query);

		return $db->execute();
	}

	public function deleteOrderTaxes($orderId, $type = JBD_PACKAGES) {
		$db = JFactory::getDBO();

		$orderId = (int) $orderId;
		$sql = "delete from #__jbusinessdirectory_order_taxes where order_id = $orderId and order_type = $type";
		$db->setQuery($sql);
		return $db->execute();
	}

	public function getPendingOrders() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_orders where state = ".PAYMENT_STATUS_NOT_PAID." and notify_payment <> 1";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getExpiredOrders() {
		$db =JFactory::getDBO();
		$query = "select inv.*,cp.city, cp.county as region, cp.countryId 
				  from #__jbusinessdirectory_orders inv 
				  left join #__jbusinessdirectory_packages p on inv.package_id=p.id and p.expiration_type=2
				  left join #__jbusinessdirectory_companies cp on inv.company_id = cp.id
				  where	inv.state = ".PAYMENT_STATUS_PAID." and end_date < date(now()) and expiration_processed != 1";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function orderProcessed($orderId){
		$db = JFactory::getDBO();

		$orderId = (int) $orderId;
		$query = "update  #__jbusinessdirectory_orders set expiration_processed = 1 where id = $orderId";
		$db->setQuery($query);

		return $db->execute();
	}

	
}
