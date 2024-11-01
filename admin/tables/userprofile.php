<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class JTableUserProfile
 *
 * @since 5.2.2
 */
class JTableUserProfile extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 *
	 * @since 5.2.2
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_user_profiles', 'id', $db);
	}

	/**
	 * Sets key
	 *
	 * @param $k
	 *
	 * @since 5.2.2
	 */
	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Retrieves user by email
	 *
	 * @param $email string email of the user
	 *
	 * @return mixed
	 *
	 * @since 5.2.2
	 */
	public function getOAuthUser($email) {
		$db    = JFactory::getDbo();
		$email = $db->escape($email);

		$query = "select u.*, up.*
				  from #__users as u
				  left join #__jbusinessdirectory_user_profiles as up on up.user_id = u.id
				  where u.email = '$email'";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getUserProfile($userId){
		$db    = JFactory::getDbo();

		$query = "select up.*
				  from #__jbusinessdirectory_user_profiles up
				  where up.user_id = $userId";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getUserByActivationCode($code){
		$db    = JFactory::getDbo();

		$query = "select up.*
				  from #__jbusinessdirectory_user_profiles up
				  where up.activation_code = '$code'";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getUserByUsername($display_name){
		$db    = JFactory::getDbo();

		$query = "select count(*)
			      from #__users
				  where display_name = '$display_name'";

		$db->setQuery($query);
		return $db->loadResult();
	}

	public function getUserByEmail($email){
		$db    = JFactory::getDbo();

		$query = "select count(*)
			      from #__users
				  where email = '$email'";

		$db->setQuery($query);
		return $db->loadResult();
	}
}
