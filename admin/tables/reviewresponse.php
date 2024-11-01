<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableReviewresponse extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_review_responses', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function changeState($reviewresponseId, $value) {
		$db =JFactory::getDBO();
		$itemId = $db->escape($reviewresponseId);
		$query = "UPDATE #__jbusinessdirectory_company_review_responses SET state = '$value' WHERE id = ".$itemId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function deleteResponseByReview($reviewId) {
		$db = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_review_responses WHERE reviewId = ".$reviewId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
