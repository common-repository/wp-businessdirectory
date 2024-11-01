<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once BD_CLASSES_PATH.'/payment/paymentcontroller.php';

class JBusinessDirectoryControllerPayment extends PaymentController {
	/**
	 * Constructor (registers additional tasks to methods)
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function __construct() {
		$this->log = Logger::getInstance();
		//$this->log->LogDebug("create payment controller");
		parent::__construct();

		$this->controller = "payment";
		$this->type = PAYMENT_TYPE_PACKAGE;
		$this->orderId = JFactory::getApplication()->input->get("orderId", null);
		$this->paymentModel = $this->getPaymentModel();
		$this->paymentModel->setState('payment.orderId', $this->orderId);
	}

	public function getOrder() {
		$orderModel = $this->getModel("Orders");
		$order = $orderModel->getOrder($this->orderId);

		return $order;
	}

	public function applyDiscount() {
		$model = $this->getModel("Payment");
		$model->applyDiscount();
		$this->setRedirect(JRoute::_("index.php?option=com_jbusinessdirectory&view=payment&orderId=".$this->orderId, false), $msg);
	}
	
	public function saveOrder() {
		$orderModel = $this->getModel("Orders");
		$orderModel->saveOrder($this->order);
	}

	public function updateOrder($paymentDetails, $processor = null) {
		$orderModel = $this->getModel("Orders");
		$this->order = $orderModel->updateOrder($paymentDetails, $processor);
	}

	public function getPaymentModel() {
		return $this->getModel("Payment");
	}

	public function redirectTo($msgType) {
		$msg = "";
		$redirect = "";
		switch ($msgType) {
			case parent::SUCCESS:
				$msg = JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY");
				$redirect = "index.php?option=com_jbusinessdirectory&view=payment&layout=response&orderId=".$this->orderId;
				break;
			case parent::WAITING:
				$msg = JText::_("LNG_PAYMENT_WAITING");
				$redirect = "index.php?option=com_jbusinessdirectory&view=payment&layout=response&orderId=".$this->orderId;
				break;
			case parent::FAILURE:
				$msg = JText::_("LNG_TRANSACTION_FAILED");
				$redirect = "index.php?option=com_jbusinessdirectory&view=payment&orderId=".$this->orderId;
				break;
			case parent::CANCELED:
				$msg = JText::_('LNG_OPERATION_CANCELED_BY_USER');
				$redirect = "index.php?option=com_jbusinessdirectory&view=payment&orderId=".$this->orderId;
				break;
		}

		//$this->setMessage($msg);
		$this->setRedirect(JRoute::_($redirect, false));
	}

	public function sendEmail($emailType, $details) {
		switch ($emailType) {
			case parent::SUCCESS:
				$this->paymentModel->sendPaymentEmail($details);
				break;
			case parent::WAITING:
				$this->paymentModel->sendPaymentDetailsEmail($details);
				break;
		}
	}

	/**
	 * Returns JTable Instance for the appropriate order/booking table
	 *
	 * @return JTable Instance
	 *
	 * @since 5.1.2
	 */
	public function getTableInstance() {
		$table = JTable::getInstance("Order", "JTable");

		return $table;
	}

	/**
	 * Returns the companyId needed to identify the correct payment processor
	 **
	 * @return int
	 *
	 * @since 5.5.0
	 */
	public function getProcessorCompanyId() {
		// default payment processor
		return -1;
	}

	/**
	 * Returns the name of the payment view
	 *
	 * @return string
	 *
	 * @since 5.5.0
	 */
	public function getPaymentView() {
		return "payment";
	}
}
