<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once BD_CLASSES_PATH.'/traits/AppointmentsSummary.php';

class AppointmentsService
{
	use AppointmentsSummary;

	/**
	 * Method that retrieves additional details from the database for the service that
	 * has been booked
	 *
	 * @param $serviceDetails object containing existing details about the services
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public static function getRawServiceDetails($serviceDetails) {
		$db = JFactory::getDbo();

		$whereBookingId = "";
		if (isset($serviceDetails->bookingId)) {
			$whereBookingId = " and csb.id = ".$serviceDetails->bookingId;
		}

		$query = "select cs.*, sp.name as providerName, sp.id as providerId, sp.image, cs.duration, cs.name as serviceName,
				  cp.name as companyName, cp.address, cp.street_number, cp.city, cp.county, cp.postalCode, cp.countryId,
				  csb.initial_amount, csb.vat_amount, csb.amount, csb.vat
				  from #__jbusinessdirectory_company_services as cs
				  left join #__jbusinessdirectory_company_service_bookings as csb on csb.service_id = cs.id
				  left join #__jbusinessdirectory_company_provider_services as cps on cs.id = cps.service_id
				  left join #__jbusinessdirectory_company_providers as sp on sp.id = cps.provider_id
				  left join #__jbusinessdirectory_companies as cp on cp.id = cs.company_id
				  where cs.id = $serviceDetails->serviceId and sp.id = $serviceDetails->providerId $whereBookingId";

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

	/**
	 * Calculate the booking details (taxes, vat, total amount)
	 */
	public static function getServiceDetails($serviceDetails) {

		$srvcDetails = self::getRawServiceDetails($serviceDetails);
		$srvcDetails->initial_amount = $srvcDetails->price;

		$countryId = !empty($serviceDetails->country_id)?$serviceDetails->country_id:$srvcDetails->countryId;
		$vatObject = TaxService::getVat($srvcDetails->initial_amount, $countryId);
		
		if(!isset($vatObject->vat)){
			$vatObject->vat = 0;
		}

		$srvcDetails->vat_amount = $vatObject->vat_amount;
		$srvcDetails->vat = $vatObject->vat;

		$taxObject = TaxService::calculateTaxes($srvcDetails->initial_amount, JBD_APP_APPOINTMENTS, $countryId);

		$srvcDetails->taxes = $taxObject->taxes;
		$srvcDetails->amount = $srvcDetails->initial_amount + $srvcDetails->vat_amount + $taxObject->tax_amount;

		return $srvcDetails;
	}

	/**
	 * Save bookings information
	 *
	 * @param $bookingDetails object containing buyerDetails and serviceDetails
	 *
	 * @return int ID of the booking
	 * @throws Exception
	 * @since 5.0.0
	 */
	public static function saveBooking($bookingDetails) {
		$serviceBookingTable = JTable::getInstance('CompanyServiceBookings', 'JTable');

		$bookingDetails->id = 0;
		if (!empty($bookingDetails->serviceDetails->bookingId)) {
			$serviceBookingTable->id = (int) $bookingDetails->serviceDetails->bookingId;
		}

		// Create a booking record on the table
		$serviceBookingTable->service_id  = isset($bookingDetails->serviceDetails->id) ? $bookingDetails->serviceDetails->id : $bookingDetails->serviceDetails->serviceId;
		$serviceBookingTable->provider_id = $bookingDetails->serviceDetails->providerId;
		$serviceBookingTable->currency_id = $bookingDetails->serviceDetails->currency_id;
		$serviceBookingTable->date        = JBusinessUtil::convertToMysqlFormat($bookingDetails->serviceDetails->date);
		$serviceBookingTable->time        = JBusinessUtil::convertTimeToMysqlFormat($bookingDetails->serviceDetails->hour);
		$serviceBookingTable->first_name  = $bookingDetails->buyerDetails->first_name;
		$serviceBookingTable->last_name   = $bookingDetails->buyerDetails->last_name;
		$serviceBookingTable->address     = $bookingDetails->buyerDetails->address;
		$serviceBookingTable->city        = $bookingDetails->buyerDetails->city;
		$serviceBookingTable->region      = $bookingDetails->buyerDetails->county;
		$serviceBookingTable->country_id  = $bookingDetails->buyerDetails->country;
		$serviceBookingTable->postal_code = $bookingDetails->buyerDetails->postalCode;
		$serviceBookingTable->phone       = $bookingDetails->buyerDetails->phone;
		$serviceBookingTable->email       = $bookingDetails->buyerDetails->email;
		$serviceBookingTable->user_id     = isset($bookingDetails->buyerDetails->user_id)?$bookingDetails->buyerDetails->user_id:0;// JBusinessUtil::getUser()->ID;

		if (isset($bookingDetails->amount) && !empty($bookingDetails->amount)) {
			$serviceBookingTable->amount = $bookingDetails->amount;
			$serviceBookingTable->initial_amount = $bookingDetails->amount;
		} else {
			$tmpDetails             = new stdClass();
			$tmpDetails->providerId = $serviceBookingTable->provider_id;
			$tmpDetails->serviceId  = $serviceBookingTable->service_id;

			$tmpData                     = self::getServiceDetails($tmpDetails);
			$serviceBookingTable->initial_amount = $tmpData->price;
			$serviceBookingTable->amount = $tmpData->price;
		}

		$taxObject = TaxService::calculateTaxes($serviceBookingTable->initial_amount, JBD_APP_APPOINTMENTS, $bookingDetails->buyerDetails->country->id);
		if (empty($taxObject->taxes)) {
			$taxObject = TaxService::calculateTaxes($serviceBookingTable->initial_amount, JBD_APP_APPOINTMENTS);
		}

		$vatObject = TaxService::getVat($serviceBookingTable->initial_amount, $bookingDetails->buyerDetails->country->id);
		$serviceBookingTable->vat_amount = $vatObject->vat_amount;
		$serviceBookingTable->vat = $vatObject->vat;

		$serviceBookingTable->amount = $serviceBookingTable->initial_amount + $vatObject->vat_amount + $taxObject->tax_amount;
		$serviceBookingTable->status = SERVICE_BOOKING_CREATED;

		if (!$serviceBookingTable->store()) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($serviceBookingTable->getError(), 'error');
			return false;
		}

		$bookingId = $serviceBookingTable->id;
		$table = JTable::getInstance("Order", "JTable", array());
		$table->deleteOrderTaxes($bookingId, JBD_APP_APPOINTMENTS);
		$table->createOrderTax($bookingId, JBD_APP_APPOINTMENTS, $taxObject->taxes);

		return $bookingId;
	}

	/**
	 * Method that gets the booking details
	 *
	 * @param $bookingId int ID of the booking
	 *
	 * @return array containing the booking details
	 * @since 5.0.0
	 */
	public static function getBookingDetails($bookingId) {
		$serviceBookingTable = JTable::getInstance('CompanyServiceBookings', 'JTable', array());
		$result              = $serviceBookingTable->getBookingDetails($bookingId);

		return $result;
	}

	/**
	 * Retrieves all the unavailable (fully booked) hours for a specific date, provider and service.
	 *
	 * @param $providerId int ID of the provider
	 * @param $serviceId  int ID of the service
	 * @param $date       string date
	 *
	 * @return array
	 *
	 * @since 5.0.0
	 */
	public static function getUnavailableDates($providerId, $serviceId, $date) {
		$serviceBookingTable = JTable::getInstance('CompanyServiceBookings', 'JTable', array());
		$bookedHours         = $serviceBookingTable->getUnavailableHours($date, $providerId, $serviceId);

		if (empty($bookedHours)) {
			return array();
		}

		$unavailableHours = array();
		foreach ($bookedHours as $bookedHour) {
			$unavailableHours[$bookedHour->time] = $bookedHour;
		}

		return $unavailableHours;
	}

	/**
	 * Create user data for storing order details
	 *
	 * @param $data
	 * @param $userData
	 * @return stdClass
	 */
	public static function createUserData() {
		$userData = new stdClass();
		$userData->first_name =  '';
		$userData->last_name =  '';
		$userData->address =  '';
		$userData->city =  '';
		$userData->state_name = '';
		$userData->country =  '';
		$userData->postal_code =  '';
		$userData->phone =  '';
		$userData->email =  '';
		$userData->conf_email =  '';

		return $userData;
	}

	/**
	 * Method to initialize Service User Data
	 *
	 * @param bool|false $resetUserData
	 * @return null|stdClass
	 */
	public static function initializeUserData($resetUserData = false) {
		$get = JFactory::getApplication()->input->get->getArray();
		$data = JFactory::getApplication()->input->post->getArray();
		if (count($data) == 0) {
			$data = $get;
		}

		$userData = isset($_SESSION['serviceUserData']) ? $_SESSION['serviceUserData'] : null;
		if (!isset($userData) || $resetUserData) {
			$userData = self::createUserData();
			$_SESSION['serviceUserData'] = $userData;
		}

		if (!isset($userData->buyerDetails)) {
			$guestDtls = new stdClass();
			$guestDtls->first_name = "";
			$guestDtls->last_name = "";
			$guestDtls->address = "";
			$guestDtls->city = "";
			$guestDtls->county = "";
			$guestDtls->country_name = "";
			$guestDtls->postalCode = "";
			$guestDtls->phone = "";
			$guestDtls->email = "";
			$userData->buyerDetails = $guestDtls;
		}

		if (!isset($userData->serviceDetails)) {
			$srvcDtls = new stdClass();
			$srvcDtls->serviceId = "";
			$srvcDtls->providerId = "";
			$srvcDtls->date = "";
			$srvcDtls->hour = "";

			$userData->serviceDetails = $srvcDtls;
		}

		$_SESSION['serviceUserData'] = $userData;
		return $userData;
	}

	/**
	 * Method to get user data object created from session data
	 *
	 * @return mixed|null|stdClass
	 */
	public static function getUserData() {
		$userData = isset($_SESSION['serviceUserData']) ? $_SESSION['serviceUserData'] : null;
		if (!isset($userData)) {
			$userData = self::initializeUserData();
			$_SESSION['serviceUserData'] = $userData;
		}

		return $userData;
	}

	/**
	 * Adds the buyer details and saves them in the session
	 *
	 * @param $buyerDetails array containing the buyer details
	 */
	public static function addBuyerDetails($buyerDetails) {
		$userData = $_SESSION['serviceUserData'];
		$buyerDtls = new stdClass();
		$buyerDtls->first_name = ucfirst($buyerDetails["first_name"]);
		$buyerDtls->last_name = ucfirst($buyerDetails["last_name"]);
		$buyerDtls->address = ucfirst($buyerDetails["address"]);
		$buyerDtls->city = $buyerDetails["city"];
		$buyerDtls->county = $buyerDetails["region"];

		if(!is_object($buyerDetails["country"])){
			$buyerDetails["country"] = JBusinessUtil::getCountry($buyerDetails["country"]);
		}

		$buyerDtls->country = $buyerDetails["country"];
		$buyerDtls->country_name = !empty($buyerDetails["country"])?$buyerDetails["country"]->country_name:"";
		
		$buyerDtls->postalCode = strtoupper($buyerDetails["postal_code"]);
		$buyerDtls->phone = $buyerDetails["phone"];
		$buyerDtls->email = $buyerDetails["email"];
		$buyerDtls->user_id = $buyerDetails["user_id"];

		$userData->buyerDetails = $buyerDtls;

		$_SESSION['serviceUserData'] = $userData;
	}

	/**
	 * Adds the service details selected by the user and saves them in the session
	 *
	 * @param $serviceDetails array containing info about the service booking details
	 *
	 * @throws Exception
	 */
	public static function addServiceDetails($serviceDetails) {

		if (empty($serviceDetails['serviceId'])) {
			throw new Exception(JText::_('LNG_SERVICE_MISSING'));
		}

		if (empty($serviceDetails['providerId'])) {
			throw new Exception(JText::_('LNG_PROVIDER_MISSING'));
		}

		if (empty($serviceDetails['date'])) {
			throw new Exception(JText::_('LNG_DATE_MISSING'));
		}

		if (empty($serviceDetails['hour'])) {
			throw new Exception(JText::_('LNG_HOUR_MISSING'));
		}

		$userData = $_SESSION['serviceUserData'];
		$srvcDetails = new stdClass();

		$srvcDetails->serviceId = $serviceDetails["serviceId"];
		$srvcDetails->providerId = $serviceDetails["providerId"];
		$srvcDetails->date = $serviceDetails["date"];
		$srvcDetails->hour = $serviceDetails["hour"];
		$srvcDetails->currency_id = $serviceDetails["currency_id"];

		$userData->serviceDetails = $srvcDetails;

		$_SESSION['serviceUserData'] = $userData;
	}

	/**
	 * Method to get the current Joomla Session
	 *
	 * @return JSession
	 * @throws Exception
	 */
	private static function getJoomlaSession() {
		$session = JFactory::getSession();
		if ($session->getState() !== 'active') {
			$app = JFactory::getApplication();
			$msg = "Your session has expired";
			$app->redirect('index.php?option=' . JBusinessUtil::getComponentName() . '&view=companies', $msg);
			$app->enqueueMessage("Your session has expired", 'warning');
		} else {
			return $session;
		}
	}
}