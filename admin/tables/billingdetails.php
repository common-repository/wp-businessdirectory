<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableBillingDetails extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_billing_details', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Retrieve user billing details
	 *
	 * @param [type] $userId
	 * @return void
	 */
	public function getBillingDetails($userId) {
		$key = array("user_id" => $userId);
		$this->load($key, true);
		$properties = $this->getProperties(1);
		$value      = MVC\Utilities\ArrayHelper::toObject($properties, 'JObject');
		if (!empty($value->country)){
			if (is_numeric($value->country)) {
				$value->country = JBusinessUtil::getCountry($value->country);
			}else{
				$value->country = JBusinessUtil::getCountryByName($value->country);
			}
		}else{
			$value->country = "";
		}

		return $value;
	}

	public function updateGuestDetails($data) {
		$db =JFactory::getDBO();

		$firstName = $db->escape($data['first_name']);
		$lastNane = $db->escape($data['last_name']);
		$email = $db->escape($data['email']);
		$phone = $db->escape($data['phone']);
		$address = $db->escape($data['address']);
		$postalCode = $db->escape($data['postal_code']);
		$city = $db->escape($data['city']);
		$region = $db->escape($data['region']);
		$country = $db->escape($data['country']);
		$id = $db->escape($data['id']);

		$query = " UPDATE #__jbusinessdirectory_billing_details SET 
                    first_name = '".$firstName."',
                    last_name = '".$lastNane."',
                    email = '".$email."',
                    phone = '".$phone."',
                    address = '".$address."',
                    postal_code = '".$postalCode."',
                    city = '".$city."',
                    region = '".$region."',
                    country = '".$country."'
                    
                    where id = '".$id."'";

		$db->setQuery($query);
		$db->execute();

		return true;
	}


	public function updateBillingDetails($data) {
		$db =JFactory::getDBO();


        $allowedKeys =  array('first_name','last_name','email','phone', 'address','postal_code','city','region', 'country');

		$query = " UPDATE #__jbusinessdirectory_billing_details SET ";
		$comma = " ";
		foreach($data as $key => $val) {
			if(!empty($val)) {
				if(in_array($key, $allowedKeys)) {
					$query .= $comma . $key . " = '" . trim($val) . "'";
					$comma = ", ";
				}
			}
		}

		
		$query .= " where user_id = '".$data['user_id']."'";

		$db->setQuery($query);
		$db->execute();

		return true;
	}
}
