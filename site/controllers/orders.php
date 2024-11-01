<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerOrders extends JControllerLegacy {
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Genereate the invoice pdf
	 *
	 * @return void
	 */
	public function generateInvoicePDF() {
		// JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the model.
		JModelLegacy::addIncludePath(JPATH_COMPONENT_SITE . '/models', 'Orders');
		$model = JModelLegacy::getInstance('Orders', 'JBusinessDirectoryModel', array('ignore_request' => true));

		// Show the PDF file.
		$model->generateInvoicePDF();
		exit();
	}

	/**
	 * Sends an email to all users with unpaid orders
	 * Requires a cron job to be configured
	 *
	 * @return void
	 */
	public function sendPaymentReminderEmail() {
		// Get the model.
		JModelLegacy::addIncludePath(JPATH_COMPONENT_SITE . '/models', 'Orders');
		$model = JModelLegacy::getInstance('Orders', 'JBusinessDirectoryModel', array('ignore_request' => true));

		$model->sendPaymentReminderEmail();
	}
}
