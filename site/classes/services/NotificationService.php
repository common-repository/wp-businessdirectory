<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');

class NotificationService {
	private static $url = 'https://fcm.googleapis.com/fcm/send';

	/**
	 * Retrieves the owner of the company that has been bookmarked,
	 * and sends a notification to his mobile device
	 *
	 * @param $itemId int ID of the company/offer
	 * @param $itemType int type of the item (company or offer)
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	public static function sendBookmarkNotification($itemId, $itemType = ITEM_TYPE_BUSINESS) {
		$userId = null;
		$name = '';
		$title = '';
		if ($itemType == ITEM_TYPE_BUSINESS) {
			$companyTable = JTable::getInstance('Company', 'JTable');
			$companyTable->load($itemId);

			$userId = $companyTable->userId;
			if (empty($userId)) {
				return false;
			}

			$title = JText::_('LNG_NEW_COMPANY_BOOKMARK');
			$name = $companyTable->name;
		} else {
			$offerTable = JTable::getInstance('Offer', 'JTable');
			$offerTable->load($itemId);

			$userId = isset($offerTable->user_id) ? $offerTable->user_id : null;
			if (empty($userId)) {
				$companyId = isset($offerTable->companyId) ? $offerTable->companyId : null;
				if (empty($companyId)) {
					return false;
				}

				$companyTable = JTable::getInstance('Company', 'JTable');
				$companyTable->load($companyId);

				$userId = $companyTable->userId;
				if (empty($userId)) {
					return false;
				}
			}

			$title = JText::_('LNG_NEW_OFFER_BOOKMARK');
			$name = $offerTable->subject;
		}

		$deviceTable = JTable::getInstance('MobileDevice', 'JTable');
		$devices     = $deviceTable->getDevicesByUser($userId);

		$body = '';
		$body .= $name . ': ';
		$body .= $title;

		$notification             = array();
		$notification["title"]    = JText::_('LNG_NEW_BOOKMARK');
		$notification["body"]     = $body;
		$notification["type"]     = NOTIFICATION_TYPE_BOOKMARK;
		$notification["itemId"]   = (int) $itemId;
		$notification["itemType"] = $itemType;

		$receivers = array();
		foreach ($devices as $device) {
			if($device->enable_push_notifications) {
				$receivers[] = $device->firebase_token;
			}
		}

		if (count($receivers) == 0) {
			return self::storeUserNotifications($notification, $receivers, $registered, $userId);
		}

		return self::sendNotification($notification, $receivers);
	}

	/**
	 * Retrieves the owner of the company that has been reviewed,
	 * and sends a notification to his mobile device
	 *
	 * @param $itemId int ID of the company/offer
	 * @param $itemType int type of the item (company or offer)
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	public static function sendReviewNotification($itemId, $itemType = ITEM_TYPE_BUSINESS) {
		$userId = null;
		$name = '';
		$title = '';
		if ($itemType == ITEM_TYPE_BUSINESS) {
			$companyTable = JTable::getInstance('Company', 'JTable');
			$companyTable->load($itemId);

			$userId = $companyTable->userId;
			if (empty($userId)) {
				return false;
			}

			$title = JText::_('LNG_NEW_COMPANY_REVIEW');
			$name = $companyTable->name;
		} else {
			$offerTable = JTable::getInstance('Offer', 'JTable');
			$offerTable->load($itemId);

			$userId = isset($offerTable->user_id) ? $offerTable->user_id : null;
			if (empty($userId)) {
				$companyId = isset($offerTable->companyId) ? $offerTable->companyId : null;
				if (empty($companyId)) {
					return false;
				}

				$companyTable = JTable::getInstance('Company', 'JTable');
				$companyTable->load($companyId);

				$userId = $companyTable->userId;
				if (empty($userId)) {
					return false;
				}
			}

			$title = JText::_('LNG_NEW_OFFER_REVIEW');
			$name = $offerTable->subject;
		}

		$deviceTable = JTable::getInstance('MobileDevice', 'JTable');
		$devices     = $deviceTable->getDevicesByUser($userId);

		$body = '';
		$body .= $name . ': ';
		$body .= $title;

		$notification             = array();
		$notification["title"]    = JText::_('LNG_NEW_REVIEW');
		$notification["body"]     = $body;
		$notification["type"]     = NOTIFICATION_TYPE_REVIEW;
		$notification["itemId"]   = (int) $itemId;
		$notification["itemType"] = $itemType;

		$receivers = array();
		foreach ($devices as $device) {
			if($device->enable_push_notifications) {
				$receivers[] = $device->firebase_token;
			}
		}

		if (count($receivers) == 0) {
			return self::storeUserNotifications($notification, $receivers, $registered, $userId);
		}

		return self::sendNotification($notification, $receivers);
	}

	/**
	 * Sends a notification to the receiver's mobile device
	 *
	 * @param $senderId   int ID of the sender
	 * @param $receiverId int ID of the receiver
	 * @param $subject    string message subject
	 * @param $body       string message body
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	public static function sendMessageNotification($senderId, $receiverId, $subject, $body) {
		$deviceTable = JTable::getInstance('MobileDevice', 'JTable');
		$devices     = $deviceTable->getDevicesByUser($receiverId);

		$notification                 = array();
		$notification["title"]        = $subject;
		$notification["body"]         = $body;
		$notification["type"]         = NOTIFICATION_TYPE_MESSAGE;

		$notification["senderId"]   = $senderId;

		$receivers = array();
		foreach ($devices as $device) {
			if($device->enable_push_notifications) {
				$receivers[] = $device->firebase_token;
			}
		}

		if (count($receivers) == 0) {
			return false;
		}

		return self::sendNotification($notification, $receivers);
	}

	/**
	 * Sends the notification to the specified receivers.
	 *
	 * @param $notification array data for the notification
	 * @param $receivers array firebase tokens of the receivers
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	private static function sendNotification($notification, $receivers) {
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		if ($notification['type'] == NOTIFICATION_TYPE_MESSAGE) {
			$fields = array(
				'registration_ids' => $receivers,
				'notification'	   => array(
					"android_channel_id" => "business_dir_mobile",
					'alert'		=> true,
					'title'     => $notification['title'],
					'body'      => $notification['body'],
					"type"      => $notification['type'],
					"id"        => $notification['senderId'],
					"name"      => $notification['name'],
					"timestamp" => date("Y-m-d G:i:s"),
				),
				'data'			=> array(
					'title' => $notification['title'],
					'body'  => $notification['body'],
					"type"     => $notification['type'],
					"id"        => $notification['senderId'],
					"name"      => $notification['name'],
				), // Payload data
				
			);
		} else {
			$fields = array(
				'registration_ids' => $receivers,
				'notification'	   => array(
					"android_channel_id" => "business_dir_mobile",
					'alert'		=> true,
					'title' => $notification['title'],
					'body'  => $notification['body'],
					"type"     => $notification['type'],
					'itemId'  => $notification['itemId'],
					'itemType'  => $notification['itemType'],
					"timestamp" => date("Y-m-d G:i:s"),
				),
				'data'			=> array(
					'title' => $notification['title'],
					'body'  => $notification['body'],
					'itemId'  => $notification['itemId'],
					'itemType'  => $notification['itemType'],
					"type"     => $notification['type'],
				), // Payload data
			);

			self::storeUserNotifications($notification, $receivers, false);
		}

		$headers = array(
			'Authorization: key='."$mobileAppSettings->firebase_server_key",
			'Content-Type: application/json'
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result);
		if ($result->failure) {
			return false;
		}

		return true;
	}

	/**
	 * Sends a notification to all users that have subscribed themselves to a specific topic.
	 *
	 * @param $notification array data for the notification
	 * @param $topic string name of the topic
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	public static function sendTopicNotifications($notification, $topic) {
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		$fields = array(
			'notification'     => array(
				'title' => $notification['title'],
				'body'  => $notification['body'],
			),
			'to' => '/topics/'.$topic
		);

		$headers = array(
			'Authorization: key='."$mobileAppSettings->firebase_server_key",
			'Content-Type: application/json'
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result);

		self::storeUserNotifications($notification, [], true);

		if (!isset($result->message_id)) {
			return false;
		}

		return true;
	}

	/**
	 * Sends a general notification to specific users
	 *
	 * @param $notification array data for the notification
	 * @param $receivers array list of firebase tokens
	 *
	 * @return bool
	 * @since 5.8.6
	 */
	public static function sendGeneralNotification($notification, $receivers, $registered = false) //Todo: check if we can split the scheduled notifications
	{ 
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		$fields = array(
			'registration_ids' => $receivers,
			'notification'	   => array(
				"android_channel_id" => "business_dir_mobile",
				'alert'		=> true,
				'title' => $notification['title'],
				'body'  => $notification['body'],
			),
			'data'			=> array(
				'title' => $notification['title'],
				'body'  => $notification['body'],
			), // Payload data
			
		);


		$headers = array(
			'Authorization: key='."$mobileAppSettings->firebase_server_key",
			'Content-Type: application/json'
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result);

		self::storeUserNotifications($notification, $receivers, $registered);

		return $result->success;
	}

	/**
	 * Retrieves offers of bookmarked company of a user
	 * and sends a notification to his mobile device for new offers created
	 *
	 * @param $itemId int ID of the company
	 * @param $userId int ID of the company
	 * @param $itemType int type of the item (company or offer)
	 *
	 * @return bool
	 * @since 5.0.0
	 */
	public static function sendBookmarkedCompanyItemsNotification($companyId, $itemName, $itemId, $itemType = ITEM_TYPE_OFFER) {
		if (empty($companyId)) {
			return false;
		}

		$userId = null;
		$name = '';
		$title = '';

		$bookmarkTable = JTable::getInstance('Bookmark', 'JTable');
		$bookmarks = $bookmarkTable->getItemBookmarks($companyId);

		if (empty($bookmarks)) {
			return false;
		}

		$deviceTable = JTable::getInstance('MobileDevice', 'JTable');
		foreach($bookmarks as $bookmark) {

			$devices     = $deviceTable->getDevicesByUser($bookmark->user_id);
			$notification             = array();
			$notification["body"]     = $itemName;
			$notification["type"]     = NOTIFICATION_TYPE_NEW_BOOKMARK_ITEM;
			$notification["itemId"]   = (int) $itemId;
			$notification["itemType"] = $itemType;

			if ($itemType == ITEM_TYPE_OFFER) {
				$notification["title"]    = JText::sprintf('LNG_NOTIFICATION_TYPE_NEW_BOOKMARK_OFFER', $bookmark->company_name);
			} else {
				$notification["title"]    = JText::sprintf('LNG_NOTIFICATION_TYPE_NEW_BOOKMARK_EVENT', $bookmark->company_name);
			}

			$receivers = array();
			foreach ($devices as $device) {
				if($device->enable_push_notifications) {
					$receivers[] = $device->firebase_token;
				}
			}

			if (count($receivers) == 0) {
				self::storeUserNotifications($notification, $receivers, $registered, $bookmark->user_id);
				continue;
			}

			self::sendNotification($notification, $receivers);
		}

		return true;
	}

	//Todo: If user tokens are empty, need to store based on item id.
	public static function storeUserNotifications($notification, $firebaseTokens, $registered, $userId = null)
	{	
		
		JModelLegacy::addIncludePath(JPATH_ROOT . '/administrator/components/com_jbusinessdirectory/models', 'JBusinessDirectoryModel');
		$model = JModelLegacy::getInstance('MobileAppNotifications', 'JBusinessDirectoryModel', array('ignore_request' => true));

		$data = array();
		$data["notification_title"]= $notification["title"];
		$data["notification_body"] = $notification["body"];
		$data["notification_type"] = $notification["type"];
		$data['nr_contacts']	   = $notification["type"] == NOTIFICATION_TYPE_USERGROUP ? JBusinessUtil::countAllUsers() : -1; // Todo: Get count based on type and count firebase tokens
		$data["itemId"]= $notification["itemId"];
		$data["itemType"]= $notification["itemType"];

		if(!$model->save($data)) {
			return false;
		} else {
			$db = JFactory::getDbo();
			$notificationId = $db->insertid();
		}

		$mobileAppNotificationsTable = JTable::getInstance('MobileAppNotifications ', 'JTable');
		$mobileAppNotificationsTable->storeUserNotifications($notificationId, $firebaseTokens, $registered, $userId);
	}
}
