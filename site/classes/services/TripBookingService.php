<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once BD_CLASSES_PATH.'/traits/TripBookingSummary.php';

class TripBookingService
{
	use TripBookingSummary;


	/**
	 * Save bookings information
	 *
	 * @param $bookingDetails object containing buyerDetails and serviceDetails
	 *
	 * @return int ID of the booking
	 * @throws Exception
	 * @since 6.7.0
	 */
	public static function saveBooking($bookingDetails) {
		$tripBookingsTable = JTable::getInstance('TripBookings', 'JTable');

		// $bookingDetails->id = 0;
		// if (!empty($bookingDetails->serviceDetails->bookingId)) {
		// 	$tripBookingsTable->id = (int) $bookingDetails->serviceDetails->bookingId;
		// }

		// Create a booking record on the table
		$tripBookingsTable->trip_id 	= $bookingDetails->serviceDetails->tripId;
		$tripBookingsTable->trip_date   = JBusinessUtil::convertToMysqlFormat($bookingDetails->serviceDetails->tripDate);
		$tripBookingsTable->trip_time   = JBusinessUtil::convertTimeToMysqlFormat($bookingDetails->serviceDetails->tripTime);
		$tripBookingsTable->first_name  = $bookingDetails->guestDetails->first_name;

		$tripBookingsTable->last_name   = $bookingDetails->guestDetails->last_name;
		$tripBookingsTable->address     = $bookingDetails->guestDetails->address;
		$tripBookingsTable->postal_code = $bookingDetails->guestDetails->postal_code;
		$tripBookingsTable->phone       = $bookingDetails->guestDetails->phone;
		$tripBookingsTable->email       = $bookingDetails->guestDetails->email;
		$tripBookingsTable->user_id     = isset($bookingDetails->guestDetails->user_id)?$bookingDetails->guestDetails->user_id:0;// JBusinessUtil::getUser()->ID;

		$tripBookingsTable->status = TRIP_BOOKING_CREATED;

		if (!$tripBookingsTable->store()) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($tripBookingsTable->getError(), 'error');
			return false;
		}

		$bookingId = $tripBookingsTable->id;

		return $bookingId;
	}

	/**
	 * Prepare the trip booking details
	 *
	 * @since 6.7.0
	 */
	public static function getTripBooking($orderId) {

		if(empty($orderId)){
			return null;
		}

		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$tripBookingsTable = JTable::getInstance('TripBookings', 'JTable', array());
		$booking = $tripBookingsTable->getBookingDetails($orderId);

		$tripsTable = JTable::getInstance('Trips', 'JTable', array());
		$booking->trip = $tripsTable->getTrip($booking->trip_id);
		$booking->trip->pictures = $tripsTable->getTripPictures($booking->trip_id);

		//generate the booking summary
		$booking->bookingDetailsSummary = TripBookingService::getBookingSummary($booking);

		//generate the guest details summary
		$booking->guestDetailsSummary = TripBookingService::getBuyerDetailsSummary($booking);


		return $booking;
	}


	/**
	 * Confirm the booking
	 *
	 * @since 6.7.0
	 */
	public static function confirmBooking($orderId) {

		if(empty($orderId)){
			return null;
		}

		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$tripBookingsTable = JTable::getInstance('TripBookings', 'JTable', array());

		$tripBookingsTable->confirmBooking($orderId);

		return true;
	}
}