<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

use MVC\Plugin\PluginHelper;
use MVC\Factory;

class UserService
{

	/**
	 * Add new user if it does not existings on database
	 * @param unknown_type $data
	 * @return unknown
	 */
	public static function addUser($data)
	{
		$user = JBusinessUtil::getUser();
		if (!$user->ID || $user->guest == 1) {
			$userObj = self::getUserByEmail($data["email"]);
			if (!empty($userObj->id)) {
				$userId = $userObj->id;
			} else {
				$userId = self::addNewUser($data);
			}
		} else {
			$userId = $user->ID;
		}

		return $userId;
	}

	/**
	 * Get user by its email address
	 *
	 * @param $email
	 * @return mixed
	 */
	public static function getUserByEmail($email)
	{
		$user = get_user_by("email",$email);
		return $user;
	}

	/**
	 * Returns the super user
	 *
	 * @param $userId
	 * @return mixed
	 */
	public static function isSuperUser($userId)
	{
		$user	= JBusinessUtil::getUser();
		$isroot	= $user->get('isRoot');
		return $isroot;
	}

	/**
	 * Generate a random password.
	 *
	 * @param $text
	 * @param bool $is_cripted
	 * @return mixed
	 */
	public static function generatePassword($text, $is_cripted = false)
	{
		$password 	=  $text;
		if ($is_cripted == false) {
			return $password;
		}
		jimport('joomla.user.helper');
		$password = JUserHelper::genRandomPassword(8);

		return $password;
	}

	/**
	 * Add user details and create a Joomla User
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function addNewUser($data)
	{
		//prepare user object
		$userdata = array(); // place user data in an array for storing.
		
		$userdata['firstname'] = isset($data["firstname"])?$data["firstname"]:"";
		$userdata['lastname'] = isset($data["lastname"])?$data["lastname"]:"";
		$userdata['username'] = isset($data["username"])?$data["username"]:$data['email'];
		$userdata['display_name'] = $data["firstname"]." ".$data["lastname"];
		$userdata['display_name'] = trim($userdata['display_name']);
		
		$userdata['email'] = isset($data["email"])?$data["email"]:"";
		if(empty($userdata['email'])){
			$userdata['email'] = $data["user_email"];
		}
		
		if (empty($userdata["display_name"])) {
			$userdata['display_name'] = isset($data["name"])?$data["name"]:"";
			if (empty($userdata["display_name"])) {
				$name = explode('@', $data['email']);
				$userdata['display_name']  = $name[0];
			}
		}

		if(!empty($userdata['email'])){
			$usr = get_user_by("email", $userdata['email']);
			if(!empty($usr)){
				return $usr->ID;
			}
		}

		//set password
		if (!empty($data["password"])) {
			$userdata['password'] = $data["password"];
		}elseif (!empty($data["password1"])) {
			$userdata['password'] = $data["password1"];
		} else {
			$userdata['password'] = UserService::generatePassword($userdata['email'], true);
		}

		//$userdata["need_review"] = $data["filter_package"] == 2 ? true:false;
		
		//create the user
		$userId = UserService::createUser($userdata);

		if (!is_numeric($userId)) {
			 // something went wrong!!
			JFactory::getApplication()->enqueueMessage("Could not create user", 'error');
		}else{

			$subject = JText::_("LNG_NEW_USER_EMIAL_SUBJECT");
			$content = JText::_("LNG_NEW_USER_EMAIL_CONTENT");

			$appSettings = JBusinessUtil::getApplicationSettings();

			$subject = str_replace(EMAIL_COMPANY_NAME, $appSettings->company_name, $subject);

			$content = str_replace(EMAIL_USER_NAME, $userdata['username'], $content);
			$content = str_replace(EMAIL_PASSWORD, $userdata['password'], $content);

			EmailService::sendUserMessage($subject, $content, $userId , $userdata['email']);
		}

		return $userId;
	}

	
	/**
	 * Create an user based on the Joomla user registration mechanism
	 */
	public static function createUser($user)
	{

		$user_id = wp_insert_user( array(
			'user_login' => $user['username'],
			'user_pass' => $user['password'],
			'user_email' => $user['email'],
			'first_name' => $user['firstname'],
			'last_name' => $user['lastname'],
			'display_name' => $user['display_name'],
			'role' => 'subscriber'
		));

		return $user_id;
	}

	/**
	 * Create the activation code for the user
	 *
	 * @return void
	 */
	public static function createActivationCode($userId)
	{
		$activationCode = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

		//store user profile with the activation code
		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$userProfileTable = JTable::getInstance("UserProfile", "JTable");

		$userProfileTable->id = 0;
		$userProfileTable->user_id = $userId;
		$userProfileTable->activation_code = $activationCode;
		$userProfileTable->verified = 0;

		if (!$userProfileTable->store()) {
			throw new Exception($userProfileTable->getError());
			return false;
		}

		return $activationCode;
	}


	/**
	 * Send the email activation to the user
	 */
	public static function sendActivationEmail($userId, $activation = null, $need_review = null){

		if (empty($userId)) {
			return null;
		}

		//load the activation code
		if (empty($activation)) {
			JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
			$userProfileTable = JTable::getInstance("UserProfile", "JTable");
			$userProfile = $userProfileTable->getUserProfile($userId);
			if (!empty($userProfile)) {
				$activation = $userProfile->activation_code;
			}
		}

		//if there is no activation code create one
		if (empty($activation)) {
			$activation = self::createActivationCode($userId);
		}

		$config = JFactory::getConfig();

		$data = array();
		$data["userId"] = $userId;
		$data['activation'] = $activation;
		$data['activationLink'] = JRoute::link(
			'site',
			'index.php?option=com_jbusinessdirectory&task=businessuser.verifyEmail&activation_token=' . $data['activation'],
			false,
			1,
			true
		);
		$data["need_review"]= $need_review;

		// Send the registration email.
		$result = EmailService::sendUserEmailConfirmationEmail($data);

		return $result;
	}

	/**
	 * Login user
	 *
	 * @param [type] $data
	 * @return void
	 */
	public static function loginUser($id)
	{
		wp_clear_auth_cookie();
		wp_set_current_user($id);
		wp_set_auth_cookie($id);
	}

	/**
	 * Login a user with provided credentials
	 * 
	 */
	public static function loginUserWithCredentials($credentials)
	{
		$result = wp_signon( $credentials);

		if($result instanceof WP_Error){
			return false;
		}else{
			return true;
		}
	}
}
