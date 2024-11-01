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

class TablePayments extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_payments', 'payment_id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function updatePaymentStatus($orderId, $amount, $transactionId, $paymentMethod, $responseCode, $responseMessage, $transactionTime, $status, $type = PAYMENT_TYPE_PACKAGE) {
		$db = JFactory::getDBO();

		$query = "select * from #__jbusinessdirectory_payments WHERE order_id = $orderId order by payment_id desc";
		$db->setQuery($query);
		$payment = $db->loadObject();

		$query = " UPDATE #__jbusinessdirectory_payments 
				  SET payment_status = $status, transaction_id = '$transactionId', response_code = '$responseCode', payment_method = '$paymentMethod', payment_date = '$transactionTime', message = '$responseMessage'
				  WHERE payment_id = $payment->payment_id and type = $type";

		$db->setQuery($query);
		return $db->execute();
	}

	public function setPaymentStatus($orderId, $type, $status) {
		$db    = JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_payments SET payment_status = $status  WHERE order_id =  $orderId  and type = $type";
		$db->setQuery($query);
		
		return $db->execute();
	}

	public function getPaymentByOrder($orderId, $orderType = PAYMENT_TYPE_PACKAGE) {
		$db = JFactory::getDbo();

		$orderId   = (int) $orderId;
		$orderType = (int) $orderType;

		$query = "select * from #__jbusinessdirectory_payments WHERE order_id = $orderId and type = $orderType";
		$db->setQuery($query);
		return $db->loadObject();
	}
}
