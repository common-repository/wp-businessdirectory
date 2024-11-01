<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryModelMobileConnector extends JModelList
{
	protected $appSettings;

	/**
	 * constructor method
	 *
	 * @since 5.0.0
	 */
	function __construct()
	{
		parent::__construct();

		$this->appSettings = JBusinessUtil::getApplicationSettings();
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   string  The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array   Configuration array for model. Optional.
	 * @return  JTable	A database object
	 * @since   5.0.0
	 */
	public function getTable($type = 'Company', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Return company types
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public function getCompanyTypes()
	{
		$table = $this->getTable('CompanyTypes');
		$types = $table->getOrderedCompanyTypes();

		return $types;
	}

	/**
	 * Return event types
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public function getEventTypes()
	{
		$table = $this->getTable('EventType');
		$types = $table->getOrderedEventTypes();

		return $types;
	}

	/**
	 * Provides a suggestion list based on the provided search keyword and item type
	 *
	 * @param $itemType int type of the item (company/event/offer)
	 * @param $keyword string search keyword
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function getSuggestions($itemType, $keyword)
	{
		switch ($itemType) {
			case ITEM_TYPE_EVENT:
				$table = $this->getTable('Event');
				break;
			case ITEM_TYPE_OFFER:
				$table = $this->getTable('Offer');
				break;
			default:
				$table = $this->getTable('Company');
		}

		// return first 10 results if keyword empty
		if (empty($keyword)) {
			$keyword = "a";
			return $table->getSuggestions($keyword, 0, 10);
		}

		return $table->getSuggestions($keyword);    
	}

	/**
	 * Provides a category suggestion list based on the provided search keyword and item type
	 *
	 * @param $itemType int type of the item (company/event/offer)
	 * @param $keyword string search keyword
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function getCategorySuggestions($itemType, $keyword)
	{
		$table = $this->getTable('Category', 'JBusinessTable');
		$limit = 2; // limit categories to 2
		$suggestionList = $table->getCategorySuggestions($keyword, $itemType, $limit);

		return $suggestionList;
	}

	/**
	 * Return categories based on item Type
	 *
	 * @param $itemType int type of the item (company/event/offer)
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public function getCategories($itemType = ITEM_TYPE_BUSINESS)
	{
		$table = $this->getTable('Category', 'JBusinessTable');
		$items = $table->getMainCategoriesByType($itemType);

		return $items;
	}

	/**
	 * Retrieves bookmarks based on the user ID
	 *
	 * @param $userId int ID of the user
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public function getUserBookmarks($userId)
	{
		$userId = (int) $userId;

		$table = $this->getTable('Bookmark');
		$items = $table->getUserBookmarks($userId);

		return $items;
	}

	/**
	 * Removes an existing bookmark based on user_id, item_id and item_type.
	 *
	 * @param $data array
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public function removeBookmark($data)
	{
		$userId   = (int) $data["user_id"];
		$itemId   = (int) $data["item_id"];
		$itemType = (int) $data["item_type"];

		$table    = $this->getTable('Bookmark');
		$result   = $table->deleteBookmark($userId, $itemId, $itemType);

		return $result;
	}

	/**
	 * Checks if an item (company/offer) has been bookmarked or not by a certain
	 * user
	 *
	 * @param int $userId   ID of the user
	 * @param int $itemId   ID of the item
	 * @param int $itemType type of the item (company/offer)
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	public function isBookmarked($userId, $itemId, $itemType = BOOKMARK_TYPE_BUSINESS)
	{
		$bookmarkTable = $this->getTable('Bookmark');
		$bookmark = $bookmarkTable->getBookmark($itemId, $userId, $itemType);

		return !empty($bookmark);
	}

	/**
	 * Retrieves reviews based on the user ID
	 *
	 * @param $userId int ID of the user
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	public function getUserReviews($userId)
	{
		$userId = (int) $userId;

		$table = $this->getTable('Review');
		$items = $table->getUserReviews($userId, $this->appSettings->show_pending_review);

		return $items;
	}

	/**
	 * Enables/disables the push notification setting of a certain device.
	 *
	 * @param $enable int 1 enable 0 disable
	 * @param $token string device token
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function setPushNotifications($enable, $token)
	{
		$table = $this->getTable('MobileDevice');
		$result = $table->updatePushNotification($enable, $token);

		return $result;
	}

	/**
	 * Set's the firebase token of a user/device (identified by the token)
	 *
	 * @param $token string
	 * @param $firebaseToken string
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function setFirebaseToken($token, $firebaseToken)
	{
		$table = $this->getTable('MobileDevice');
		$result = $table->updateFirebaseToken($token, $firebaseToken);

		return $result;
	}

	/**
	 * Generates a token based on the user and device ids and creates a
	 * record on the mobile device table for that user.
	 *
	 * @param $userId int ID of the user
	 * @param $deviceId string device ID
	 *
	 * @return bool|string token
	 *
	 * @since 5.0.0
	 */
	function saveMobileUser($userId, $deviceId)
	{
		$mobileDevice = JTable::getInstance('MobileDevice');
		$token = $this->generateToken($userId, $deviceId);

		$devices = $mobileDevice->getDeviceByToken($token);
		if (count($devices) > 0) {
			return $token;
		}

		$mobileDevice->id        = 0;
		$mobileDevice->user_id   = $userId;
		$mobileDevice->device_id = $deviceId;
		$mobileDevice->token     = $token;

		if (!$mobileDevice->store()) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($mobileDevice->getDbo()->getError(), 'error');
			return false;
		}

		return $token;
	}

	/**
	 * Deletes the device record from the database based on the
	 * session token.
	 *
	 * @param $token string device token
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function logOut($token)
	{
		$table = $this->getTable('MobileDevice');
		$result = $table->deleteDeviceByToken($token);

		return $result;
	}

	/**
	 * Generates a hash based on the userId and deviceId and returns it.
	 *
	 * @param $userId int ID of the user
	 * @param $deviceId string ID of the device
	 *
	 * @return string
	 *
	 * @since 5.0.0
	 */
	function generateToken($userId, $deviceId)
	{
		$suffix = '!@#$%';
		$token = $userId.','.$deviceId.','.$suffix;

		$token = md5($token);

		return $token;
	}

	/**
	 * Retrieves the corresponding devices for a certain user based on his/her ID.
	 *
	 * @param $userId int ID of the user
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function getDevicesByUser($userId)
	{
		$userId = (int) $userId;

		$table   = $this->getTable('MobileDevice');
		$devices = $table->getDevicesByUser($userId);

		return $devices;
	}

	/**
	 * Retrieves the corresponding device record of a token
	 *
	 * @param $token string
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function getDeviceByToken($token)
	{
		$table = $this->getTable('MobileDevice');
		$device = $table->getDeviceByToken($token);

		return $device;
	}

	/**
	 * Creates and returns the user object corresponding to a token.
	 *
	 * @param $token
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	function getUserByToken($token)
	{
		$device = $this->getDeviceByToken($token);

		$userId = $device->user_id;
		$user = JBusinessUtil::getUser($userId);

		return $user;
	}
}