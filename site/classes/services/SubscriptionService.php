<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');

class SubscriptionService {
	public static function getSubscription($id) {
		$subTable     = JTable::getInstance("Subscription", "JTable");
		$subscription = $subTable->getSubscription($id);

		return $subscription;
	}

	public static function getSubscriptionBySubscriptionId($subscriptionId) {
		$subTable     = JTable::getInstance("Subscription", "JTable");
		$subscription = $subTable->getSubscriptionBySubscriptionId($subscriptionId);

		return $subscription;
	}

	public static function getCompanyLastActiveSubscription($companyId) {
		$subTable     = JTable::getInstance("Subscription", "JTable");
		$subscription = $subTable->getCompanyLastActiveSubscription($companyId);

		return $subscription;
	}

	public static function getSubscriptionByOrder($orderId) {
		$subTable     = JTable::getInstance("Subscription", "JTable");
		$subscription = $subTable->getSubscriptionByOrder($orderId);

		return $subscription;
	}

	public static function getSubscriptionByPayment($paymentId) {
		$subTable     = JTable::getInstance("Subscription", "JTable");
		$subscription = $subTable->getSubscriptionByPayment($paymentId);

		return $subscription;
	}

	public static function saveSubscription($subscription) {
		$table = JTable::getInstance('Subscription', 'JTable', array());
		if (!$table->bind($subscription)) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');

			return false;
		}

		if (!$table->check()) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');

			return false;
		}

		if (!$table->store()) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');

			return false;
		}

		return $table->id;
	}

	public static function createSubscription($order, $processor, $subscriptionId = null) {
		$payment = PaymentService::getPaymentDetails($order->id);

		$subscription                   = array();
		$subscription["id"]             = 0;
		$subscription["start_date"]     = $order->start_date;
		$subscription["trial_start"]    = $order->start_trial_date;
		$subscription["processor_type"] = $processor->type;
		$subscription["amount"]         = $order->amount;
		$subscription["payment_id"]     = $payment->payment_id;
		$subscription["company_id"]     = $order->company_id;
		$subscription["time_unit"]      = $order->package->time_unit;
		$subscription["time_amount"]    = $order->package->time_amount;
		$subscription["status"]         = SUBSCRIPTION_STATUS_INACTIVE;

		if (!empty($subscriptionId)) {
			$subscription["subscription_id"] = $subscriptionId;
			$subscription["status"]          = SUBSCRIPTION_STATUS_ACTIVE;
		}

		$lastSubscription  = self::getCompanyLastActiveSubscription($order->company_id);
		if($lastSubscription) {
			self::cancelSubscription($lastSubscription->id);
		}

		return self::saveSubscription($subscription);
	}

	public static function updateSubscription($paymentDetails, $status = SUBSCRIPTION_STATUS_ACTIVE, $endDate = null) {
		$subscription = self::getSubscriptionByPayment($paymentDetails->payment_id);

		$data           = array();
		$data["id"]     = $subscription->id;
		$data["status"] = $status;

		if($paymentDetails->subscription_status == SUBSCRIPTION_STATUS_CANCELED) {
			$data["status"] = SUBSCRIPTION_STATUS_CANCELED;
		}

		if (!empty($endDate)) {
			$data["end_date"] = $endDate;
		}

		if (isset($paymentDetails->subscription_id)) {
			$data["subscription_id"] = $paymentDetails->subscription_id;
		}

		$log = Logger::getInstance();
		$log->LogDebug("Subscription Details: " . json_encode($data));

		return self::saveSubscription($data);
	}

	public static function setOrderSubscription($orderId, $subscriptionId) {
		$orderTable = JTable::getInstance("Order", "JTable", array());

		$data                    = array();
		$data["id"]              = $orderId;
		$data["subscription_id"] = $subscriptionId;

		if (!$orderTable->bind($data)) {
			JFactory::getApplication()->enqueueMessage($orderTable->getError(), 'warning');

			return false;
		}

		if (!$orderTable->check()) {
			JFactory::getApplication()->enqueueMessage($orderTable->getError(), 'warning');

			return false;
		}

		if (!$orderTable->store()) {
			JFactory::getApplication()->enqueueMessage($orderTable->getError(), 'warning');

			return false;
		}

		return true;
	}


	public static function getSubscriptionDetails($package){
		$descr = "";
		if($package->expiration_type==3){
			
		}
	}

	/**
	 * Retrieves the subscription status
	 */
	public static function getSubscriptionStatus($subscription, $retrieveSubscription = false) {
		if ($retrieveSubscription) {
			$subscription = self::getSubscription($subscription->id);
		}

		if(!isset($subscription)) {
			return SUBSCRIPTION_STATUS_INACTIVE;
		}

		if($subscription->status == SUBSCRIPTION_STATUS_CANCELED) {
			return SUBSCRIPTION_STATUS_CANCELED;
		}

		if($subscription->status == SUBSCRIPTION_STATUS_INACTIVE) {
			return SUBSCRIPTION_STATUS_INACTIVE;
		}

		if (!empty($subscription->end_date)) {
			if (strtotime($subscription->end_date) > strtotime(date('Y-m-d'))) {
				return SUBSCRIPTION_STATUS_CANCELED;
			} else {
				return SUBSCRIPTION_STATUS_INACTIVE;
			}
		}

		$cycle = 'days';
		if ($subscription->time_unit == 'M') {
			$cycle = 'months';
		} else if ($subscription->time_unit == 'Y') {
			$cycle = 'years';
		} 

		$nextCycle = date('Y-m-d', strtotime("+" . $subscription->time_amount . " $cycle", strtotime($subscription->created)));

		if (strtotime($nextCycle) > strtotime(date('Y-m-d'))) {
			return SUBSCRIPTION_STATUS_ACTIVE;
		} else {
			return SUBSCRIPTION_STATUS_INACTIVE;
		}
	}

	/**
	 * Render the subscription status
	 *
	 * @param $subscription
	 * @return void
	 */
	public static function getStatusText($subscription, $retrieveSubscription = false){
		$status = self::getSubscriptionStatus($subscription, $retrieveSubscription);

		$text = "";
		switch ($status) {
			case SUBSCRIPTION_STATUS_ACTIVE:
				$text = JText::_("LNG_ACTIVE");
				$class = "status-badge badge-success";
				break;
			case SUBSCRIPTION_STATUS_INACTIVE:
				$text = JText::_("LNG_INACTIVE");
				$class = "status-badge badge-warning";
				break;	
			case SUBSCRIPTION_STATUS_CANCELED:
				$text = JText::_("LNG_CANCELED");
				$class = "status-badge badge-warning warn";
				break;
			}	

		$result = "<span class='$class'>$text</span>";

		return $result;
	}

	/**
	 * Processes the subscription object and prepares the appropriate subscription button. Button action and text
	 * depends on the subscription status.
	 *
	 * @param $subscription object subscription
	 *
	 * @return string
	 *
	 * @since 5.3.0
	 */
	public static function getSubscriptionButton($subscription, $controller = 'companies', $retrieveSubscription = false) {

		if (empty($subscription->subscription_id) ) {
			return '';
		}

		$status = self::getSubscriptionStatus($subscription, $retrieveSubscription);
		if($status != SUBSCRIPTION_STATUS_ACTIVE){
			return "";
		}

		$html    = '';
		$btnText = '<i class="la la-times-circle mr-1"></i> '.JText::_('LNG_CANCEL_SUBSCRIPTION');
		$action  = self::getSubscriptionCancelLink($subscription, $controller);
		$class="btn btn-warning btn-sm px-3 mt-3 d-flex justify-content-center ml-2";
	

		$html .= "<a href='$action' class='$class'>$btnText</a>";

		return $html;
	}

	public static function processSubscriptions($companies) {
		if (empty($companies)) {
			return $companies;
		}
		
		foreach ($companies as $company) {
			$sub = new stdClass();
			foreach ($company as $key => $val) {
				if (strpos($key, 'sub_') !== false) {
					$field         = substr($key, 4);
					$sub->{$field} = $val;
				}
			}

			$company->subscription = $sub;
		}

		return $companies;
	}

	public static function getSubscriptionCancelLink($subscription, $controller) {
		$link = JRoute::_("index.php?option=com_jbusinessdirectory&task=$controller.cancelSubscription&subscriptionId=" . $subscription->id);

		return $link;
	}

	/**
	 * Cancels a subscription based on the (primary key) ID of the subscription. It retrieves the type of payment processors
	 * used for the subscription and calls the appropriate method for cancelling the subscription.
	 *
	 * If successful it will update the status of the subscription and return true. Otherwise it will return false or
	 * throw an error.
	 *
	 * @param $subscriptionId int ID of the subscription
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function cancelSubscription($subscriptionId) {
		$subscription = self::getSubscription($subscriptionId);
		try {
			$processor = PaymentService::createPaymentProcessor($subscription->processor_type);
		} catch (Exception $e) {
			return false;
		}

		try {
			$response = $processor->cancelSubscription($subscription->subscription_id);

			if ($response) {
				$subscription->status = SUBSCRIPTION_STATUS_CANCELED;
				self::saveSubscription($subscription);

				$log = Logger::getInstance();
				$log->LogDebug("Cancel existing subscription before creating new one. subscription_id: " . $subscription->subscription_id);
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			throw new Exception($e);
		}
	}

	/**
	 * Updates subscription status
	 *
	 * @param string $subscriptionId subscription_id value
	 * @param int $status default canceled
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 5.3.3
	 */
	public static function updateSubscriptionStatus($subscriptionId, $status = SUBSCRIPTION_STATUS_CANCELED) {
		$subscription = self::getSubscriptionBySubscriptionId($subscriptionId);

		if (empty($subscription)) {
			throw new Exception('Subscription not found!');
		}

		$subscription->status = $status;
		$result = self::saveSubscription($subscription);

		return $result;
	}
}
