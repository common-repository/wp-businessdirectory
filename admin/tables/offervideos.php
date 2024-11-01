<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableOfferVideos extends JTable {
	public $id				= null;
	public $offerId		= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_offer_videos', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	/**
	 * Delete all videos of the offer with id = $offerId
	 *
	 * @param $offerId int Id of the offer which videos will be deleted
	 * @return mixed
	 */
	public function deleteAllForOffer($offerId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_offer_videos where offerId=".$offerId;
		$db->setQuery($sql);
		return $db->execute();
	}

	/**
	 * Get all videos of the offer with id = $offerId
	 *
	 * @param $offerId int The id of offer which videos are required
	 * @return object[] All videos of offer
	 */
	public function getOfferVideos($offerId) {
		$db =JFactory::getDBO();
		$sql = "select * from #__jbusinessdirectory_offer_videos where offerId=". $offerId ." ORDER BY id" ;
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
}
