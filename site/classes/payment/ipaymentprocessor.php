<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

abstract class iPaymentProcessor {
	abstract public function getHtmlFields();
	abstract public function getPaymentProcessorHtml($data = null);
	abstract public function getPaymentDetails($paymentDetails);
	abstract public function processTransaction($data, $controller);

	/**
	 * Override and set to true if payment processor is of recurring/subscription type
	 *
	 * @return bool
	 */
	public function isRecurring() {
		return false;
	}
}
