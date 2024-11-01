<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');


class JBusinessDirectoryModelUserOptions extends JModelLegacy {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Companies', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getStatistics() {
		$statistics = new stdClass();
	
		$user = JBusinessUtil::getUser();
		
		$companyTable = JTable::getInstance('Company', 'JTable');
		$statistics->totalListings = (int)$companyTable->getTotalListings($user->ID);
		$statistics->listingsTotalViews = (int)$companyTable->getListingsViewsOnFront($user->ID);
	
		$offersTable = JTable::getInstance('Offer', 'JTable');
		$statistics->totalOffers = (int)$offersTable->getTotalNumberOfOffers($user->ID);
		$statistics->offersTotalViews = (int)$offersTable->getOfferViewsOnFront($user->ID);
	
		$eventsTable = JTable::getInstance('Event', 'JTable');
		$statistics->totalEvents = (int)$eventsTable->getTotalNumberOfEvents($user->ID);
		$statistics->eventsTotalViews = (int)$eventsTable->getEventViewsOnFront($user->ID);
	
		$statistics->totalViews = $statistics->listingsTotalViews + $statistics->offersTotalViews + $statistics->eventsTotalViews;
	
		return $statistics;
	}

	public function getNewCompanies() {
		$user = JBusinessUtil::getUser();
		$start_date = JFactory::getApplication()->input->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = JFactory::getApplication()->input->get('end_date');
		$end_date = date("Y-m-d", strtotime($end_date));

		$companyTable = JTable::getInstance('Company', 'JTable');
		$result = $companyTable->getNewCompanyViews($start_date, $end_date, $user->ID);

		//add start date element if it does not exists
		if (!empty($result)) {
			if ($result[0]->date != $start_date) {
				$item = new stdClass();
				$item->date = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item = new stdClass();
				$item->date = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem = new stdClass();
			$firstItem->date = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem = new stdClass();
			$endItem->date = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	public function getNewOffers() {
		$user = JBusinessUtil::getUser();
		$start_date = JFactory::getApplication()->input->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = JFactory::getApplication()->input->get('end_date');
		$end_date = date("Y-m-d", strtotime($end_date));

		$offerTable = JTable::getInstance('Offer', 'JTable');
		$result = $offerTable->getNewOffersViews($start_date, $end_date, $user->ID);

		//add start date element if it does not exists
		if (!empty($result)) {
			if ($result[0]->date != $start_date) {
				$item = new stdClass();
				$item->date = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item = new stdClass();
				$item->date = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem = new stdClass();
			$firstItem->date = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem = new stdClass();
			$endItem->date = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	public function getNewEvents() {
		$user = JBusinessUtil::getUser();
		$start_date = JFactory::getApplication()->input->get('start_date');
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = JFactory::getApplication()->input->get('end_date');
		$end_date = date("Y-m-d", strtotime($end_date));

		$eventTable = JTable::getInstance('Event', 'JTable');
		$result = $eventTable->getNewEventsViews($start_date, $end_date, $user->ID);

		//add start date element if it does not exists

		if (!empty($result)) {
			if ($result[0]->date != $start_date) {
				$item = new stdClass();
				$item->date = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item = new stdClass();
				$item->date = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem = new stdClass();
			$firstItem->date = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem = new stdClass();
			$endItem->date = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	/**
	 * Get the last unpaid order
	 *
	 * @return void
	 */
	public function getLastUnpaidOrder(){
		$user = JBusinessUtil::getUser();

		$orderTable = JTable::getInstance("Order", "JTable", array());
		$order = $orderTable->getUserLastUnpaidOrder($user->ID);
		
		return $order;
	}

	/**
	 * Check if there are some messages to be displayed
	 *
	 * @return void
	 */
	public function getDisplayMessage(){
		$message = null;

		//check if the email address is verified
		$user = JBusinessUtil::getUser();
		// if(!empty($user->id)){
		// 	$userProfileTable = JTable::getInstance("UserProfile", "JTable");
		// 	$userProfile = $userProfileTable->getUserProfile($user->id);
		// 	if(empty($userProfile->verified)){
		// 		$message = new stdClass;
		// 		$message->title = JText::_("LNG_EMAIL_VERIFICATION_TITLE");
		// 		$message->text = JText::_("LNG_EMAIL_VERIFICATION_TEXT");
		// 		$message->button_text = JText::_("LNG_RESEND_VERIFICATION");
		// 		$message->button_link = JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.resendVerficationEmail');
		// 		return $message;
		// 	}
		// }

		//check if the last order is paid
		$lastUnpaidOrder = $this->getLastUnpaidOrder();
		if(!empty($lastUnpaidOrder)){
			$message = new stdClass;
			$message->title = JText::_("LNG_UNPAID_ORDER_TITLE");
			$message->text = JText::_("LNG_UNPAID_ORDER_TEXT");
			$message->button_text = "<i class=\"la la-money-bill-alt\"></i> ".JText::_("LNG_PAY");;
			$message->button_link = JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.'&orderId='.$lastUnpaidOrder->id.'&companyId='.$lastUnpaidOrder->company_id);
			return $message;
		}

		return null;
	}
}
