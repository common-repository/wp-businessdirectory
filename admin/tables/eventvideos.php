<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableEventVideos extends JTable {
	public $id				= null;
	public $eventId		= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_event_videos', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	/**
	 * Delete all videos of the event with id = $eventId
	 *
	 * @param $eventId int Id of the event which videos will be deleted
	 * @return mixed
	 */
	public function deleteAllForEvent($eventId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_event_videos where eventId=".$eventId;
		$db->setQuery($sql);
		return $db->execute();
	}

	/**
	 * Get all videos of the event with id = $eventId
	 *
	 * @param $eventId int The id of event which videos are required
	 * @return object[] All videos of event
	 */
	public function getEventVideos($eventId) {
		$db =JFactory::getDBO();
		$sql = "select * from #__jbusinessdirectory_event_videos where eventId=". $eventId ." ORDER BY id" ;
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
}
