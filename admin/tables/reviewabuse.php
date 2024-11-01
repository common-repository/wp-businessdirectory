<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableReviewabuse extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_review_abuses', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function changeState($reviewabuseId) {
		$db = JFactory::getDBO();
		$query = "UPDATE #__jbusinessdirectory_company_review_abuses SET state = IF(state, 0, 1) WHERE id =".$reviewabuseId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getAllReviewabuses() {
		$db = JFactory::getDBO();
		$query = "select ra.*,  r.subject as subject from #__jbusinessdirectory_company_review_abuses ra
		inner join #__jbusinessdirectory_company_review r on ra.reviewId = r.id order by ra.id desc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getAllActiveReviewabuses() {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_review_abuses where state=1 order by id desc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getNumberOfReviewabuses($reviewId) {
		$db = JFactory::getDBO();
		$query = "select count(*) as nrReviewabuses from #__jbusinessdirectory_company_review_abuses where reviewId=".$reviewId;
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nrReviewabuses;
	}

	public function deleteReviewabuse($reviewabuseId) {
		$db = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_review_abuses WHERE id = ".$reviewabuseId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function deleteAbuseByReview($reviewId) {
		$db = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_review_abuses WHERE reviewId = ".$reviewId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
