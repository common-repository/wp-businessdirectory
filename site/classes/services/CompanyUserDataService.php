<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


class CompanyUserDataService
{
    /**
     * Create user data for storing order details
     *
     * @param $data
     * @param $userData
     * @return stdClass
     */
    public static function createUserData($data, $userData)
    {
        if (empty($userData))
            $userData = new stdClass();

        if (!isset($userData->first_name))
            $userData->first_name = '';
        if (!isset($userData->last_name))
            $userData->last_name = '';
        if (!isset($userData->address))
            $userData->address = '';
        if (!isset($userData->city))
            $userData->city = '';
        if (!isset($userData->state_name))
            $userData->state_name = '';
        if (!isset($userData->country))
            $userData->country = '';
        if (!isset($userData->postal_code))
            $userData->postal_code = '';
        if (!isset($userData->phone))
            $userData->phone = '';
        if (!isset($userData->email))
            $userData->email = '';
        if (!isset($userData->conf_email))
            $userData->conf_email = '';
        
        return $userData;
    }

    /**
     * Method to initialize Service User Data
     *
     * @param bool|false $resetUserData
     * @return null|stdClass
     */
    public static function initializeUserData($resetUserData = false)
    {
        $get = JFactory::getApplication()->input->get->getArray();
        $data = JFactory::getApplication()->input->post->getArray();
        if (count($data) == 0)
            $data = $get;

        $userData = isset($_SESSION['serviceUserData']) ? $_SESSION['serviceUserData'] : null;
        if (!isset($userData) || $resetUserData) {
            $userData = self::createUserData($data, $userData);
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

        if(!isset($userData->serviceDetails)) {
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
    public static function getUserData()
    {
        $session = self::getJoomlaSession();
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
    public static function addBuyerDetails($buyerDetails)
    {
        $userData = $_SESSION['serviceUserData'];
        $buyerDtls = new stdClass();
        $buyerDtls->first_name = ucfirst($buyerDetails["first_name"]);
        $buyerDtls->last_name = ucfirst($buyerDetails["last_name"]);
        $buyerDtls->address = ucfirst($buyerDetails["address"]);
        $buyerDtls->city = $buyerDetails["city"];
        $buyerDtls->county = $buyerDetails["region"];
        $buyerDtls->country_name = $buyerDetails["country"];
        $buyerDtls->postalCode = strtoupper($buyerDetails["postal_code"]);
        $buyerDtls->phone = $buyerDetails["phone"];
        $buyerDtls->email = $buyerDetails["email"];

        $userData->buyerDetails = $buyerDtls;

        $_SESSION['serviceUserData'] = $userData;
    }

    /**
     * Adds the service details selected by the user and saves them in the session
     *
     * @param $serviceDetails array containing info about the service booking details
     */
    public static function addServiceDetails($serviceDetails)
    {
        $userData = $_SESSION['serviceUserData'];
        $srvcDetails = new stdClass();
        $srvcDetails->bookingId = $serviceDetails["bookingId"];
        $srvcDetails->serviceId = $serviceDetails["serviceId"];
        $srvcDetails->providerId = $serviceDetails["providerId"];
        $srvcDetails->date = $serviceDetails["date"];
        $srvcDetails->hour = $serviceDetails["hour"];

        $userData->serviceDetails = $srvcDetails;

        $_SESSION['serviceUserData'] = $userData;
    }

    /**
     * Method to get the current Joomla Session
     *
     * @return JSession
     * @throws Exception
     */
    private static function getJoomlaSession()
    {
        $session = JFactory::getSession();
        if ($session->getState() !== 'active') {
            $app = JFactory::getApplication();
            $msg = "Your session has expired";
            $app->redirect('index.php?option=' . JBusinessUtil::getComponentName() . '&view=companies', $msg);
            $app->enqueueMessage("Your session has expired", 'warning');
        } else
            return $session;
    }
}