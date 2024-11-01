<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableBookmark extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_bookmarks', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Check if an item has already been bookmarked
	 *
	 * @param $itemId
	 * @param $userId
	 * @param int $itemType
	 * @return mixed
	 */
	public function getBookmark($itemId, $userId, $itemType = BOOKMARK_TYPE_BUSINESS) {
		$db = JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_bookmarks 
                  where item_id=$itemId and user_id=$userId and item_type=$itemType";
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

	/**
	 * Get bookmarks for an user
	 *
	 * @param $userId
	 * @param null $itemType
	 * @return mixed
	 */
	public function getBookmarks($userId, $itemType = null) {
		$db = JFactory::getDbo();

		$whereType = '';
		if (!empty($itemType)) {
			$whereType = ' and item_type=' . $itemType;
		}

		$query = "select * from #__jbusinessdirectory_bookmarks 
                  where user_id=$userId $whereType";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Get bookmarks for an user
	 *
	 * @param $userId
	 * @param null $itemType
	 * @return mixed
	 */
	public function getItemBookmarks($itemId, $itemType = null) {
		$db = JFactory::getDbo();

		$whereType = '';
		if (!empty($itemType)) {
			$whereType = ' and item_type=' . $itemType;
		}

		$query = "select b.*, cp.name as company_name
				  from #__jbusinessdirectory_bookmarks b
				  left join #__jbusinessdirectory_companies as cp on cp.id = b.item_id and b.item_type = ".BOOKMARK_TYPE_BUSINESS." 
                  where item_id=$itemId $whereType";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getUserBookmarks($userId, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDbo();
		

		$query = "select b.*,
				  CASE 
				    WHEN b.item_type = ".BOOKMARK_TYPE_BUSINESS." THEN cp.name
				    WHEN b.item_type = ".BOOKMARK_TYPE_OFFER." THEN co.subject
				  END as item_name
				  from #__jbusinessdirectory_bookmarks as b
				  left join #__jbusinessdirectory_companies as cp on cp.id = b.item_id and b.item_type = ".BOOKMARK_TYPE_BUSINESS." 
				  left join #__jbusinessdirectory_company_offers as co on co.id = b.item_id and b.item_type = ".BOOKMARK_TYPE_OFFER."
                  where b.user_id = $userId
                  having item_name is not null
                  order by b.id desc";
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Get listing bookmarks together with listing details
	 *
	 * @param $userId
	 * @return mixed
	 */
	public function getListingBookmarksForExport($userId) {
		$db = JFactory::getDbo();
		$query = "select b.*, c.id as item_id,c.name as item_name, c.address as address, c.phone as contact_number, c.street_number, c.city, c.county,
				  group_concat(cph.weekday) as weekdays, group_concat(ifnull(cph.start_hour, '#')) as start_hours, group_concat(ifnull(cph.end_hour, '#')) as end_hours
				  from #__jbusinessdirectory_bookmarks as b
				  right join #__jbusinessdirectory_companies as c on b.item_id = c.id and b.item_type = 1
				  left join #__jbusinessdirectory_company_provider_hours as cph on cph.provider_id = b.item_id and b.item_type = ".BOOKMARK_TYPE_BUSINESS." and cph.item_type = ".BUSINESS_HOURS." and cph.type = ".STAFF_WORK_HOURS." and cph.status = 1
				  where b.user_id = $userId and b.item_type = ".BOOKMARK_TYPE_BUSINESS."
				  group by b.id
				  order by ordering asc";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get Bookmarks for all items
	 *
	 * @param $userId
	 * @return mixed
	 */
	public function getBookmarksForExport($userId) {
		$db = JFactory::getDbo();
		$query = "select b.*, c.name as company_name,c.id as company_id, co.subject as offer_name, 
                  cs.id as speaker_id, cs.name as speaker_name, cs.alias as speaker_alias, 
                  css.id as session_id, css.name as session_name, css.alias as session_alias
				  from #__jbusinessdirectory_bookmarks as b
				  left join #__jbusinessdirectory_companies as c on b.item_id = c.id 
				  left join #__jbusinessdirectory_company_offers as co on b.item_id = co.id 
				  left join #__jbusinessdirectory_conference_speakers as cs on b.item_id = cs.id 
				  left join #__jbusinessdirectory_conference_sessions as css on b.item_id = css.id 
				  
				  where b.user_id = $userId 
				  group by b.id
				  order by ordering asc";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function updateBookmarkOrder($order, $id) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_bookmarks SET ordering = '".$order."' where id='".$id."'";
		$db->setQuery($query);
		$result =  $db->execute();

		return $result;
	}

	public function deleteBookmark($userId, $itemId, $itemType = BOOKMARK_TYPE_BUSINESS) {
		$db = JFactory::getDbo();
		$query = "delete from #__jbusinessdirectory_bookmarks 
				  where user_id = $userId and item_id = $itemId and item_type = $itemType";

		$db->setQuery($query);
		$result =  $db->execute();

		return $result;
	}

	public function updateBookmark($itemId, $userId, $itemType, $note) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_bookmarks SET note = '$note' where  item_id=$itemId and user_id=$userId and item_type=$itemType ";
		$db->setQuery($query);
		$result =  $db->execute();

		return $result;
	}
}
