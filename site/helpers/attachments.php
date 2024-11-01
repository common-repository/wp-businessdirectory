<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryAttachments {
	
	/**
	 * Class constructor
	 */
	public function __construct() {
	}

	/**
	 * Get the attachments for an item based on the item type and ID
	 *
	 * @param string $attachmentType the type of the attach
	 * @param int $objectId id of the object that has the attach
	 * @param bool $active the status of the attach
	 * @return mixed|null
	 */
	public static function getAttachments($attachmentType, $objectId, $active=false) {
		$activeFilter = "";
		if ($active) {
			$activeFilter = " and status=1 ";
		}
		
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "select * from  #__jbusinessdirectory_company_attachments where type=$attachmentType $activeFilter and object_id=$objectId ORDER BY ordering asc, name,path ";
			$db->setQuery($query);
			$attachments = $db->loadObjectList();
			return $attachments;
		}
		
		return null;
	}

	/**
	 * Delete attachemnts for an item based on its type and id
	 *
	 * @param $attachmentType int object type (listin.event.offer)
	 * @param $objectId int object ID
	 */
	public static function deleteAttachmentsForObject($attachmentType, $objectId) {
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "delete from #__jbusinessdirectory_company_attachments where type=$attachmentType and object_id=$objectId";
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Add attachment details on the databse
	 *
	 * @param $attachmentType int object type (listin.event.offer)
	 * @param $objectId int object ID
	 * @param $name string attachment name
	 * @param $path string attachment saved path
	 * @param $status int status of attachment published or not
	 * @param int $ordering order of the attach
	 */
	public static function saveAttachment($attachmentType, $objectId, $name, $path, $status, $ordering = 0) {
		$db =JFactory::getDBO();
		$name = $db->escape($name);
		$path = $db->escape($path);
	
		$query = "insert into #__jbusinessdirectory_company_attachments(type,object_id,name,path,status, ordering) values($attachmentType,$objectId,'$name','$path',$status, $ordering)";
		
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Save attachments details on the databse
	 *
	 * @param $attachmentType int object type (listin.event.offer)
	 * @param $objectId int object ID of the item that owns the attachment
	 * @param $name string attachment name
	 * @param $attachmentPath string attachment saved path
	 * @param $status int status of attachment (published or not)
	 * @param $data array attachment data
	 * @param $id int object ID of the item that owns the attachment before the saving in the databse
	 * @return boolean true of false
	 */
	public static function saveAttachments($attachmentType, $attachmentPath, $objectId, $data, $id) {
		if (empty($data['process-attachments'])) {
			return null;
		}
		
		self::deleteAttachmentsForObject($attachmentType, $objectId);
		
		// Check and remove unused files
		$usedFiles = array();
		if (!empty($data['attachment_path'])) {
			$usedFiles = $data['attachment_path'];
		}
		$attachments_path = JBusinessUtil::makePathFile(BD_ATTACHMENT_UPLOAD_PATH);
		$item_attachments_path = JBusinessUtil::makePathFile($attachmentPath.($id)."/");
		JBusinessUtil::removeUnusedFiles($usedFiles, $attachments_path, $item_attachments_path);

		if (empty($data['attachment_path'])) {
			return null;
		}

		$ordering = 1;
		if (!empty($data['attachment_name'])) {
			foreach ($data['attachment_name'] as $i=>$name) {
				$path = $data['attachment_path'][$i];
				$status = $data['attachment_status'][$i];
				
				$path_old = JBusinessUtil::makePathFile(BD_ATTACHMENT_UPLOAD_PATH .$attachmentPath.$id."/");
				$path_new = JBusinessUtil::makePathFile(BD_ATTACHMENT_UPLOAD_PATH .$attachmentPath.$objectId."/");

				if (!is_dir($path_new)) {
					if (!@mkdir($path_new)) {
						return false;
					}
				}
	
				if ($path_old.basename($path) != $path_new.basename($path)) {
					if (@rename($path_old.basename($path), $path_new.basename($path))) {
						$path = $attachmentPath.($objectId).'/'.basename($path);
					} else {
						return false;
					}
				}
				
				self::saveAttachment($attachmentType, $objectId, $name, $path, $status, $ordering);
				$ordering++;
			}
		}
		return true;
	}

	/**
	 * Retrieve the coference Session Attachments by object type and id
	 *
	 * @param $attachmentType int attachments type (in this case SESSION_ATTACHMENTS)
	 * @param $objectId int the conference id that owns the attachment
	 * @param int $active status of the attachment
	 * @return mixed|null object with all details of the attachment or null if no attachment is found
	 */
	public static function getConferenceSessionAttachments($attachmentType, $objectId, $active = 1) {
		if (!empty($objectId)) {
			$activeFilter = "";
			if ($active) {
				$activeFilter = " and status=1 ";
			}
			
			$db =JFactory::getDBO();
			$query = "select * from  #__jbusinessdirectory_conference_session_attachments where type=$attachmentType and object_id=$objectId $activeFilter order by ordering asc";
			$db->setQuery($query);
			$attachments = $db->loadObjectList();
			return $attachments;
		}
		return null;
	}

	/**
	 * Save conference Sessions attachments details. Removes the old ones and add the new ones
	 *
	 * @param $attachmentType int attachments type (in this case SESSION_ATTACHMENTS)
	 * @param $attachmentPath string attachment saved path
	 * @param $objectId int object ID of the item that owns the attachment
	 * @param $data array attachment data
	 * @param $id int object ID of the item that owns the attachment before the saving in the databse
	 * @return bool
	 */
	public static function saveConferenceSessionAttachments($attachmentType, $attachmentPath, $objectId, $data, $id) {
		self::deleteConferenceSessionAttachmentsForObject($attachmentType, $objectId);

        // Check and remove unused files
		$usedFiles = array();
		if (!empty($data['attachment_path'])) {
            $usedFiles = $data['attachment_path'];
        }
		$attachments_path = JBusinessUtil::makePathFile(BD_ATTACHMENT_UPLOAD_PATH);
		$item_attachments_path = JBusinessUtil::makePathFile($attachmentPath.($id)."/");
		JBusinessUtil::removeUnusedFiles($usedFiles, $attachments_path, $item_attachments_path);

		if (!empty($data['attachment_name'])) {
			$ordering = 1;
			foreach ($data['attachment_name'] as $i=>$name) {
				$path = $data['attachment_path'][$i];
				$status = $data['attachment_status'][$i];
				
				$path_old = JBusinessUtil::makePathFile(BD_ATTACHMENT_UPLOAD_PATH .$attachmentPath.$id."/");
				$path_new = JBusinessUtil::makePathFile(BD_ATTACHMENT_UPLOAD_PATH .$attachmentPath.$objectId."/");


				if (!is_dir($path_new)) {
					if (!@mkdir($path_new)) {
						return false;
					}
				}
				if ($path_old.basename($path) != $path_new.basename($path)) {
					if (@rename($path_old.basename($path), $path_new.basename($path))) {
						$path = $attachmentPath.($objectId).'/'.basename($path);
					} else {
						return false;
					}
				}
				self::saveConferenceSessionAttachment($attachmentType, $objectId, $name, $path, $status);
				$ordering++;
			}
		}
		return true;
	}

	/**
	 * Delete conference session attachment for an item based on it type and id
	 *
	 * @param $attachmentType int attachments type (in this case SESSION_ATTACHMENTS)
	 * @param $objectId int object ID of the item that owns the attachment
	 */
	public static function deleteConferenceSessionAttachmentsForObject($attachmentType, $objectId) {
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "delete from #__jbusinessdirectory_conference_session_attachments where type=$attachmentType and object_id=$objectId";
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Save conference session attachment details on database
	 *
	 * @param $attachmentType int attachments type (in this case SESSION_ATTACHMENTS)
	 * @param $objectId int object ID of the item that owns the attachment
	 * @param $name string attachment name
	 * @param $path string attachment saved path
	 * @param $status int status of attachment (published or not)
	 * @param int $ordering order of the attach
	 */
	public static function saveConferenceSessionAttachment($attachmentType, $objectId, $name, $path, $status, $ordering = 0) {
		$db =JFactory::getDBO();
		$name = $db->escape($name);
		$path = $db->escape($path);
		
		$query = "insert into #__jbusinessdirectory_conference_session_attachments(type,object_id,name,path,status,ordering) values($attachmentType,$objectId,'$name','$path',$status, $ordering)";
		$db->setQuery($query);
		$db->execute();
	}
}
