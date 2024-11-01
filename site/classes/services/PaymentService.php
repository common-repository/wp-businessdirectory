<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath('administrator/components/com_jbusinessdirectory/tables');

class PaymentService {
	/**
	 * Create all active payment processors that are displayed on front based on database details
	 *
	 * @param boolean $onlyFrontEnd
	 * @param null    $serviceId int ID of the service
	 * @param int     $companyId int ID of the company, -1 for default processors
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function getPaymentProcessors($onlyFrontEnd = true, $serviceId = null, $companyId = DEFAULT_PAYMENT_PROCESSOR) {
		$paymentProcessors = array();
		$db                = JFactory::getDBO();

		$whereService = '';
		if (!empty($serviceId)) {
			$whereService = " and pps.app_id = $serviceId";
		}

		$whereCompany = '';
		if (!empty($companyId)) {
			if($companyId == DEFAULT_PAYMENT_PROCESSOR){
				$whereCompany = " and (pp.company_id = $companyId || pp.company_id='')";	
			}else{
				$whereCompany = " and pp.company_id = $companyId";
			}
		}

		$query = "SELECT pp.* 
				  FROM #__jbusinessdirectory_payment_processors as pp
				  LEFT JOIN #__jbusinessdirectory_payment_processor_services as pps on pps.processor_id = pp.id
				  WHERE pp.status = 1 AND pp.displayfront = 1 $whereService $whereCompany
				  GROUP BY pp.id
				  ORDER BY pp.ordering";
		$db->setQuery($query);
		$paymentProcessorsDetails = $db->loadObjectList();

		//retrieve the default payment processors if none is defined
		if(empty($paymentProcessorsDetails)){
			$whereCompanyDefault = " and (pp.company_id = -1 || pp.company_id is null)";
			$query = str_replace($whereCompany, $whereCompanyDefault, $query);
			$db->setQuery($query);
			$paymentProcessorsDetails = $db->loadObjectList();
		}
		
		foreach ($paymentProcessorsDetails as $paymentProcessorsDetail) {
			$query = "SELECT * FROM #__jbusinessdirectory_payment_processor_fields where processor_id=$paymentProcessorsDetail->id";
			$db->setQuery($query);
			$fields = $db->loadObjectList();
			foreach ($fields as $field) {
				if ($field->column_mode == '0') {
					if (!empty($field->column_name)) {
						$paymentProcessorsDetail->{'test_'.$field->column_name} = $field->column_value;
					}
				} else {
					if (!empty($field->column_name)) {
						$paymentProcessorsDetail->{$field->column_name} = $field->column_value;
					}
				}
			}

			$processorFactory = new ProcessorFactory();
			$processor        = $processorFactory->getProcessor($paymentProcessorsDetail->type);
			$processor->initialize($paymentProcessorsDetail);
			$processor->company_id = $paymentProcessorsDetail->company_id;
			$paymentProcessors[] = $processor;
		}

		return $paymentProcessors;
	}

	/**
	 * Retreive processor details from database
	 *
	 * @param string $type
	 *
	 * @param  int   $companyId
	 *
	 * @return unknown
	 */

	public static function getPaymentProcessorDetails($type, $companyId = null) {
		$db        = JFactory::getDBO();
		$processor = new stdClass();

		$whereCompany = "";
		if (!empty($companyId)) {
			$whereCompany = " and a.company_id = $companyId";
		}

		if ($type != -1) {
			$query = "select * 
					  from #__jbusinessdirectory_payment_processors a
			          left join #__jbusinessdirectory_payment_processor_fields b on a.id = b.processor_id
					  where a.type='$type' and a.status = 1 $whereCompany
					  order by b.id asc";

			$db->setQuery($query);
			$fields = $db->loadObjectList();
			
			if (!empty($fields)) {
				$processor = $fields[0];
				foreach ($fields as $field) {
					if ($field->column_mode == '0') {
						$processor->fields['test_' . $field->column_name] = $field->column_value;
						if (!empty($field->column_name)) {
							$processor->{'test_' . $field->column_name} = $field->column_value;
						}
					} else {
						$processor->fields[$field->column_name] = $field->column_value;
						if (!empty($field->column_name)) {
							$processor->{$field->column_name} = $field->column_value;
						}
					}
				}

				return $processor;
			}
		}
	
		return null;
	}

	public static function getPaymentDetails($orderId, $type = PAYMENT_TYPE_PACKAGE) {
		$db    = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_payments where order_id='$orderId' and type='$type' and transaction_id IS NOT NULL order by payment_id desc";
		$db->setQuery($query);
		$paymentDetails = $db->loadObject();
		return $paymentDetails;
	}

	/**
	 * Create payment processor
	 *
	 * @param string $type
	 *
	 * @param int    $companyId
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function createPaymentProcessor($type, $companyId = -1) {
		$processorFactory = new ProcessorFactory();
		$processor        = $processorFactory->getProcessor($type);
		
		$initData = self::getPaymentProcessorDetails($type, $companyId);
		if(empty($initData)){
			$initData = self::getPaymentProcessorDetails($type);
		}
		
		$processor->initialize($initData);

		return $processor;
	}

	/**
	 * Add a payment into the databse
	 *
	 * @param object $paymentDetails
	 *
	 * @return bool
	 */
	public static function addPayment($paymentDetails) {
		$log = Logger::getInstance();
		$log->LogDebug("Add Payment: " . json_encode($paymentDetails));
		$payments = JTable::getInstance('Payments', 'Table', array());
		//dump($confirmationsPayments);
		//dump($paymentDetails);
		
		if (!$payments->bind($paymentDetails)) {
			JFactory::getApplication()->enqueueMessage($payments->getError(), 'warning');
			return false;
		}

		if (!$payments->check()) {
			JFactory::getApplication()->enqueueMessage($payments->getError(), 'warning');
			return false;
		}

		if (!$payments->store()) {
			JFactory::getApplication()->enqueueMessage($payments->getError(), 'warning');
			return false;
		}

		return true;
	}

	/**
	 * Add a payment into the databse
	 *
	 * @param object $paymentDetails
	 *
	 * @return
	 */
	public static function updatePayment($paymentDetails) {
		$log = Logger::getInstance();
		$log->LogDebug("Update Payment Details: " . json_encode($paymentDetails));
		$payments = JTable::getInstance('Payments', 'Table');

		$result = false;
		if($paymentDetails->type == PAYMENT_TYPE_OFFER){
			$offerOrderTable = JTable::getInstance('OfferOrders', 'JTable', array());
			$orders = $offerOrderTable->getOrdersByToken($paymentDetails->order_id);
			if(!empty($orders)){
				foreach($orders as $order){
					$result = $payments->updatePaymentStatus(
						$order->id,
						$paymentDetails->amount,
						$paymentDetails->transaction_id,
						$paymentDetails->payment_method,
						$paymentDetails->response_code,
						$paymentDetails->response_message,
						$paymentDetails->transactionTime,
						$paymentDetails->payment_status,
						$paymentDetails->type
					);
				}
			}
		}else{
			$result = $payments->updatePaymentStatus(
				$paymentDetails->order_id,
				$paymentDetails->amount,
				$paymentDetails->transaction_id,
				$paymentDetails->payment_method,
				$paymentDetails->response_code,
				$paymentDetails->response_message,
				$paymentDetails->transactionTime,
				$paymentDetails->payment_status,
				$paymentDetails->type
			);
		}

		
		return $result;
	}

	/**
	 * Update Payment Status
	 *
	 * @param [int] $orderId
	 * @param [int] $type
	 * @param [int] $status
	 * @return true if operation has been executed successfully.
	 */
	public static function updatePaymentStatus($orderId, $type, $status){
		$payments = JTable::getInstance('Payments', 'Table');
		$result = $payments->setPaymentStatus($orderId, $type, $status);

		return $result;
	}

	/**
	 * Update order details
	 *
	 * @param [type] $paymentDetails
	 * @return void
	 */
	public static function updateOrderDetails($paymentDetails) {
		$log        = Logger::getInstance();
		$orderTable = JTable::getInstance("Order", "JTable");
		$orderTable->load($paymentDetails->order_id);

		$orderTable->transaction_id = $paymentDetails->transactionId;
		$orderTable->amount_paid    = $paymentDetails->amount;
		$orderTable->paid_at        = date("Y-m-d h:m:s");
		$orderTable->state          = 1;

		if (!$orderTable->store()) {
			$log->LogError("Error updating order. Order ID: " . $paymentDetails->order_id);
		}

		$log->LogDebug("Order has been successfully updated. Order ID: " . $paymentDetails->order_id);
	}

	/**
	 * Creates a new order based on the details of a previous order ($paymentDetails->order_id)
	 *
	 * @param $paymentDetails object must contain payment details
	 *
	 * @param $tableInstance  JTable instance of one of the order table based on the order type (package, appointment, event, offer)
	 *
	 * @return bool
	 */
	public static function createSubscriptionOrder($paymentDetails, $tableInstance) {
		$log = Logger::getInstance();
		$tableInstance->load($paymentDetails->order_id);

		if ($paymentDetails->processor_type == "payfastsubscriptions") {
			$timeDifference = (time() - strtotime($tableInstance->created)) / (60 * 60);
			if ($timeDifference < 24 && $tableInstance->state != PAYMENT_STATUS_PAID) {
				return -1;
			}
		}

		$newOrder                 = clone($tableInstance);
		$newOrder->id             = 0;
		$newOrder->transaction_id = $paymentDetails->transaction_id;
		$newOrder->initial_amount = $paymentDetails->amount;
		$newOrder->amount         = $paymentDetails->amount;
		$newOrder->amount_paid    = $paymentDetails->amount;
		if (isset($newOrder->currency)) {
			$newOrder->currency = $paymentDetails->currency;
		}
		$newOrder->paid_at = $paymentDetails->transactionTime;
		$newOrder->created = date('Y-m-d');

		if (isset($paymentDetails->order_start_date)) {
			$newOrder->start_date = date('Y-m-d', $paymentDetails->order_start_date);
		}

		if (isset($paymentDetails->order_end_date)) {
			$newOrder->end_date = date('Y-m-d', $paymentDetails->order_end_date);
		}

		// Bind the data.
		if (!$tableInstance->bind($newOrder)) {
			$log->logDebug($tableInstance->getError());
			return false;
		}

		// Check the data.
		if (!$tableInstance->check()) {
			$log->logDebug($tableInstance->getError());
			return false;
		}

		// Store the data.
		if (!$tableInstance->store()) {
			$log->logDebug($tableInstance->getError());
			return false;
		}

		return $tableInstance->id;
	}
}
