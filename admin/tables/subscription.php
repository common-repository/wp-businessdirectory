<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableSubscription extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_subscriptions', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getSubscription($id) {
		$id = (int) $id;

		$db = JFactory::getDBO();

		$query = "select s.*, 
				  p.payment_method, p.transaction_id, p.currency, p.payment_status, p.type, p.message, p.response_code,
				  p.created as paymentCreated, p.order_id, p.payment_date,
				  GROUP_CONCAT(o.id) as orderIds
				  from #__jbusinessdirectory_subscriptions as s
				  left join #__jbusinessdirectory_orders as o on o.subscription_id = s.id
				  left join #__jbusinessdirectory_payments as p on p.payment_id = s.payment_id
				  where 1 and s.id = $id
				  group by s.id";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getSubscriptionBySubscriptionId($subscriptionId) {
		$db = JFactory::getDBO();

		$query = "select s.*, 
				  p.payment_method, p.transaction_id, p.currency, p.payment_status, p.type, p.message, p.response_code,
				  p.created as paymentCreated, p.order_id, p.payment_date,
				  GROUP_CONCAT(o.id) as orderIds
				  from #__jbusinessdirectory_subscriptions as s
				  left join #__jbusinessdirectory_orders as o on o.subscription_id = s.id
				  left join #__jbusinessdirectory_payments as p on p.payment_id = s.payment_id
				  where 1 and s.subscription_id = '$subscriptionId'
				  group by s.id";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getSubscriptionByOrder($orderId) {
		$orderId = (int) $orderId;

		$db = JFactory::getDBO();

		$query = "select s.*
				  from #__jbusinessdirectory_subscriptions as s
				  left join #__jbusinessdirectory_orders as o on o.subscription_id = s.id
				  where 1 and o.id = $orderId";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getSubscriptionByPayment($paymentId) {
		$paymentId = (int) $paymentId;
		$db = JFactory::getDBO();

		$query = "select s.*
				  from #__jbusinessdirectory_subscriptions as s
				  where 1 and s.payment_id = $paymentId";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCompanyLastActiveSubscription($companyId) {
		$companyId = (int) $companyId;
		$db = JFactory::getDBO();

		$query = "select * 
					from #__jbusinessdirectory_subscriptions
					where status != ".SUBSCRIPTION_STATUS_CANCELED." and company_id = $companyId
					order by id desc";

		$db->setQuery($query);
		return $db->loadObject();
	}


}
