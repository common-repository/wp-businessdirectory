<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * @package     JBusinessDirectory
 *
 * @since       5.0.0
 */
abstract class PaymentController extends JControllerLegacy {
	const SUCCESS = 0;
	const WAITING = 1;
	const FAILURE = 2;
	const CANCELED = 4;

	/**
	 * Payment model that will be used
	 *
	 * @var MVC\MVC\Model\
	 * @since 5.0.0
	 */
	protected $paymentModel;

	/**
	 * Order or Booking model
	 *
	 * @var MVC\MVC\Model\
	 * @since 5.0.0
	 */
	protected $orderModel;

	/**
	 * Order object containing the order information
	 *
	 * @var stdClass
	 * @since 5.0.0
	 */
	protected $order;

	/**
	 * ID of the order
	 *
	 * @var int
	 * @since 5.0.0
	 */
	protected $orderId;

	/**
	 * Name of the payment controller
	 *
	 * @var string
	 * @since 5.0.0
	 */
	protected $controller;

	/**
	 * Payment type
	 *
	 * @var int
	 * @since 5.0.0
	 */
	protected $type;

	/**
	 * Object containing payment details data
	 *
	 * @var stdClass
	 * @since 5.0.0
	 */
	protected $paymentDetails;

	/**
	 * Get the order data needed for the payment processors to
	 * process the transaction.
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	abstract public function getOrder();

	/**
	 * Method to save the order
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	abstract public function saveOrder();

	/**
	 * Method to update the order with the new payment details
	 *
	 * @param      $paymentDetails stdClass
	 * @param null $processor      payment processor class
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	abstract public function updateOrder($paymentDetails, $processor = null);

	/**
	 * Method to retrieve the corresponding model for the controller
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	abstract public function getPaymentModel();

	/**
	 * Define the redirect views and messages based on the message type
	 *
	 * @param $msgType int
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	abstract public function redirectTo($msgType);

	/**
	 * Define the emails that will be sent based on the email type
	 *
	 * @param $emailType int
	 * @param $details   stdClass payment details
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	abstract public function sendEmail($emailType, $details);

	/**
	 * Returns JTable Instance for the appropriate order/booking table
	 *
	 * @return JTable Instance
	 *
	 * @since 5.1.2
	 */
	abstract public function getTableInstance();

	/**
	 * Returns the companyId needed to identify the correct payment processor
	 **
	 * @return int
	 *
	 * @since 5.5.0
	 */
	abstract public function getProcessorCompanyId();

	/**
	 * Returns the name of the payment view
	 *
	 * @return string
	 *
	 * @since 5.5.0
	 */
	abstract public function getPaymentView();

	/**
	 * PaymentController constructor. Calls the parent JControllerLegacy
	 * constructor and initializes and a logger instance.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		$this->log = Logger::getInstance();
		parent::__construct();
	}

	public function showPaymentOptions() {
		JFactory::getApplication()->input->set("view", "payment");
		parent::display();
	}

	public function processTransaction() {
		$paymentMethod = JFactory::getApplication()->input->get("payment_method", "nopayment");

		$this->order = $this->getOrder();
		$companyId = $this->getProcessorCompanyId();
		$discountTable = JTable::getInstance("Discount", "JTable", array());

		$processor               = PaymentService::createPaymentProcessor($paymentMethod, $companyId);
		$paymentDetails          = $processor->processTransaction($this->order, $this->controller);
		$paymentDetails->type    = $this->type;
		$paymentDetails->details = $processor->getPaymentDetails($paymentDetails);

		if(!empty($this->order->orderIds)){
			foreach($this->order->orderIds as $orderId){
				$token = $paymentDetails->order_id;
				$paymentDetails->order_id = $orderId;
				//dump($paymentDetails);
				PaymentService::addPayment($paymentDetails);
				$paymentDetails->order_id = $token;
			}
		}else{
			PaymentService::addPayment($paymentDetails);
		}
		
		$subscriptionId = null;
		if (isset($paymentDetails->subscription_id)) {
			$subscriptionId = $paymentDetails->subscription_id;
		}

		if (!empty($processor->recurring)) {
			$subId = SubscriptionService::createSubscription($this->order, $processor, $subscriptionId);

			if (!empty($subId)) {
				$this->order->subscription_id = $subId;
				$paymentDetails->subscription_id = $subId;
				$this->saveOrder();
			}
		}

		$this->paymentDetails = $paymentDetails;

		if ($paymentDetails->status == PAYMENT_REDIRECT) {
			$document               = JFactory::getDocument();
			$viewType               = $document->getType();
			$view                   = $this->getView("payment", $viewType, '', array(
				'base_path' => $this->basePath,
				'layout'    => "redirect"));

			$view->paymentProcessor = $processor;
			$view->display("redirect");
		} elseif ($paymentDetails->status == PAYMENT_SUCCESS) {
			if(isset($this->order->discount_code)) {
				$discountTable->increaseUse($this->order->discount_code);
			}
			$this->updateOrder($paymentDetails, $processor);
			$this->sendEmail(self::SUCCESS, $paymentDetails);
			$this->redirectTo(self::SUCCESS);
		} elseif ($paymentDetails->status == PAYMENT_WAITING) {
			$this->sendEmail(self::WAITING, $paymentDetails);
			$this->redirectTo(self::WAITING);
		} elseif ($paymentDetails->status == PAYMENT_ERROR) {
			JFactory::getApplication()->enqueueMessage($paymentDetails->error_message, 'warning');
			JFactory::getApplication()->input->set('layout', null);
			JFactory::getApplication()->input->set("view", $this->getPaymentView());
			parent::display();
		}
	}

	public function processResponse() {
		$this->log->LogDebug("process response");
		$processorType = JFactory::getApplication()->input->get("processor");

		$method = JFactory::getApplication()->input->getMethod();
		$data   = JFactory::getApplication()->input->get->getArray();
		if ($method == "POST") {
			$data = JFactory::getApplication()->input->post->getArray();
			if($processorType == 'asaas') {
				$data  = JFactory::getApplication()->input->json->getArray();
			}
		}

		$this->log->LogDebug(json_encode($data));

		$appSettings   = JBusinessUtil::getApplicationSettings();
		if ($processorType == 'mollie' || $processorType == 'molliesubscriptions') {
			$orderId = JFactory::getApplication()->input->get("orderId");
			$data    = PaymentService::getPaymentDetails($orderId, $this->type);

			if( $processorType == 'molliesubscriptions'){
				$data->createSubscription = true;
			}
		}

		$processor            = PaymentService::createPaymentProcessor($processorType);
		$paymentDetails       = $processor->processResponse($data);
		$paymentDetails->type = $this->type;

		$this->orderId = $paymentDetails->order_id;

		if ($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR) {
			$this->log->LogDebug("payment status canceled/error");

			$this->redirectTo(self::FAILURE);
		} else {
			if ($appSettings->direct_processing || isset($paymentDetails->processAutomatically)) {
				$this->processAutomaticResponse();
			}

			$this->redirectTo(self::WAITING);
		}
	}

	public function processAutomaticResponse() {
		$processorType = JFactory::getApplication()->input->get("processor");

		$this->log->LogDebug("process automatic response");

		$method = JFactory::getApplication()->input->getMethod();
		$data   = JFactory::getApplication()->input->get->getArray();
		if ($method == "POST") {
			$data = JFactory::getApplication()->input->post->getArray();
			if($processorType == 'asaas') {
				$data  = JFactory::getApplication()->input->json->getArray();
			}
		}
		$this->log->LogDebug('Data' . json_encode($data));

		$this->log->LogDebug("Processor: " . $processorType);
		$processor     = PaymentService::createPaymentProcessor($processorType);
		$processorType = JFactory::getApplication()->input->get("processor");

		if ($processorType == 'mollie' || $processorType == 'molliesubscriptions') {
			$orderId = JFactory::getApplication()->input->get("orderId");
			$data    = PaymentService::getPaymentDetails($orderId, $this->type);
		}

		$paymentDetails       = $processor->processResponse($data);
		//dump($paymentDetails);
		$paymentDetails->type = $this->type;
		$this->log->LogDebug("Payment Details: " . json_encode($paymentDetails));

		if (empty($paymentDetails->order_id)) {
			$this->log->LogDebug("Empty order Id");
			return;
		}

		//dump($paymentDetails->order_id);
		//dump($this->type);
		$initialPaymentDetails = PaymentService::getPaymentDetails($paymentDetails->order_id, $this->type);
		$paymentDetails->payment_id = $initialPaymentDetails->payment_id;
		$this->log->LogDebug("Initial payment details: " . json_encode($initialPaymentDetails));

		//dump($initialPaymentDetails);
		//exit;

		if(isset($paymentDetails->payment_status)){
			if(!empty($initialPaymentDetails)){
				$timeDifference = (time() - strtotime($initialPaymentDetails->created)) / (60 * 60);
				$this->log->LogDebug("Hours difference " . $timeDifference);
				$timeDifference = 50;
				if ($initialPaymentDetails->payment_status == PAYMENT_STATUS_PAID && $timeDifference < 23) {
					$this->log->LogDebug("order has been already paid");
					http_response_code(200);
					return;
				}
			}
			
			// if the initial order is not paid, set it to paid and update the subscription if it is the case (recurring payment)
			if(empty($initialPaymentDetails) || $initialPaymentDetails->payment_status != PAYMENT_STATUS_PAID){
				if ($processorType == 'asaas' && (!is_numeric($paymentDetails->order_id) || OrderService::getOrder($paymentDetails->order_id)->id == null)) {
					$this->log->LogDebug("Order not integer, return 200;");
					http_response_code(200);
					return;
				}
				$subId = SubscriptionService::updateSubscription($paymentDetails);
				$paymentDetails->subscription_id = $subId;
				$this->updateOrder($paymentDetails, $processor);
				if($paymentDetails->payment_status == PAYMENT_STATUS_PAID){
					$this->sendEmail(self::SUCCESS, $paymentDetails);
				}
				PaymentService::updatePayment($paymentDetails);
				http_response_code(200);
				return;
			}

			// if it is a new recurring payment, a new order is created based on the payment
			if ($processor->recurring && $paymentDetails->payment_status == PAYMENT_STATUS_PAID) {
				$table = $this->getTableInstance();
				$this->log->LogDebug("Creating subscription order");
				$orderId = PaymentService::createSubscriptionOrder($paymentDetails, $table);
				$paymentDetails->order_id = $orderId;

				PaymentService::addPayment($paymentDetails);
			}
		}else{
			if ($processor->recurring) {
				$this->log->LogDebug("Updating subscription: ".$paymentDetails->subscription_id);
				$subId = SubscriptionService::updateSubscription($paymentDetails);
				$this->log->LogDebug("Updating order subscription: orderId->".$initialPaymentDetails->order_id);
				SubscriptionService::setOrderSubscription($initialPaymentDetails->order_id, $subId);
			}
		}

		http_response_code(200);
	}

	public function processCardlinkAutomaticResponse () {
		JFactory::getApplication()->input->set("processor", "cardlinksubscriptions");

		return $this->processAutomaticResponse();
	}

	public function processSubscriptionResponse() {
		$this->log->LogDebug("process subscription response");
		$this->log->LogDebug("payment type: ".$this->controller);

		$method = JFactory::getApplication()->input->getMethod();
		$data   = JFactory::getApplication()->input->get->getArray();
		if ($method == "POST") {
			$data = JFactory::getApplication()->input->post->getArray();
		}
		$this->log->LogDebug(json_encode($data));

		$processorType = JFactory::getApplication()->input->get("processor");
		$this->log->LogDebug("Processor: " . $processorType);
		$processor     = PaymentService::createPaymentProcessor($processorType);
		$processorType = JFactory::getApplication()->input->get("processor");

		$paymentDetails       = $processor->processResponse($data);
		$paymentDetails->type = $this->type;
		$this->log->LogDebug("Payment Details: " . json_encode($paymentDetails));

		if (empty($paymentDetails->order_id)) {
			$this->log->LogDebug("Empty order Id");
			return;
		}

		if ($paymentDetails->status == PAYMENT_CANCELED || $paymentDetails->status == PAYMENT_ERROR) {
			$this->log->LogDebug("payment status canceled/error");
		} else {
			$table = $this->getTableInstance();
			$this->log->LogDebug("Creating subscription order");
			$orderId = PaymentService::createSubscriptionOrder($paymentDetails, $table);
			if ($orderId == -1) {
				$this->log->LogDebug("Payfast Subscriptions automatic response");
				$this->processAutomaticResponse();
			} elseif ($orderId) {
				$paymentDetails->order_id = $orderId;
				if ($this->type === PAYMENT_TYPE_EVENT || $this->type === PAYMENT_TYPE_SERVICE) {
					$paymentDetails->booking_id = $orderId;
				}

				PaymentService::addPayment($paymentDetails);
			}
		}
	}

	public function processCancelResponse() {
		$this->log->LogDebug("process cancel response ");
		$data = JFactory::getApplication()->input->post->getArray();
		$this->log->LogDebug(json_encode($data));

		$this->redirectTo(self::CANCELED);
	}

	/**
	 * Endpoint for cancelling a subscription
	 *
	 * @return bool
	 *
	 * @since 5.3.3
	 */
	public function processCancelSubscriptionResponse() {
		$this->log->LogDebug("process cancel subscription response");

		$processorType = JFactory::getApplication()->input->get('processor');
		$processor = PaymentService::createPaymentProcessor($processorType);
		
		try {
			$result = $processor->cancelSubscriptionWebhook();
		} catch (Exception $e) {
			$this->log->LogDebug("Error canceling subscription: ".$e->getMessage());
		}
	}

	public function processCardSaveResponse() {
		JFactory::getApplication()->input->set("processor", "cardsave");
		$this->processResponse();
	}

	public function processCardSaveAutomaticResponse() {
		JFactory::getApplication()->input->set("processor", "cardsave");
		$this->processAutomaticResponse();
	}

	//TODO do we still need this?
	public function processDirectProcessing($paymentDetails) {
		$this->order = $this->getOrder();

		$user         = JBusinessUtil::getUser();
		$companyModel = $this->getModel("ManageCompany");
		$companyModel->updateCompanyOwner($this->order->company_id, $user->ID);

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&layout=edit&id=' . $this->order->company_id . "", false));
	}

	public function processCardlinkResponse() {
		JFactory::getApplication()->input->set("processor", "cardlink");
		$this->processResponse();
	}

	public function processCdlSubRes() {
		JFactory::getApplication()->input->set("processor", "cardlinksubscriptions");
		$this->processResponse();
	}

	public function processAsaasResponse () {
		JFactory::getApplication()->input->set("processor", "asaas");

		$this->log->LogDebug("processAsaasResponse");

		return $this->processAutomaticResponse();
	}
}
