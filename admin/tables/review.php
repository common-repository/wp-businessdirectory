<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableReview extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_reviews', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function changeState($reviewId) {
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_company_reviews SET state = IF(state, 0, 1) WHERE id = ".$reviewId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function getReview($reviewId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_reviews where id=$reviewId ";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}

	public function getReviews($itemId, $showPendingReview, $type, $reviewId = null, $limitstart = 0, $limit = 0) {
		$approvalFilter="and (cr.approved = ".REVIEW_STATUS_APPROVED.")";
		if ($showPendingReview) {
			$approvalFilter = "and (cr.approved = ".REVIEW_STATUS_CREATED." or cr.approved = ".REVIEW_STATUS_APPROVED.") ";
		}

		if (!empty($reviewId)) {
			$reviewCond = ' and cr.id = "'.$reviewId.'"';
		} else {
			$reviewCond = "and cr.itemId=".$itemId." and cr.review_type =".$type." and state=1 $approvalFilter";
		}

		$db =JFactory::getDBO();
		$query = "select cr.*, scores, criteria_ids, answer_ids, question_ids from #__jbusinessdirectory_company_reviews cr
					left join (
						select GROUP_CONCAT(ruc.score) as scores, GROUP_CONCAT(rc.id) as criteria_ids, r.id as cr_id
						from #__jbusinessdirectory_company_reviews r
						left join #__jbusinessdirectory_company_reviews_user_criteria ruc on r.id=ruc.review_id
						left join #__jbusinessdirectory_company_reviews_criteria rc on rc.id=ruc.criteria_id and rc.published =1 
						group by r.id
					) rcs on cr.id = rcs.cr_id
					left join (
						select GROUP_CONCAT(rqa.id) as answer_ids, GROUP_CONCAT(rq.id) as question_ids, r.id as cr_id
						from #__jbusinessdirectory_company_reviews r
						left join #__jbusinessdirectory_company_reviews_question_answer rqa on r.id=rqa.review_id
						left join #__jbusinessdirectory_company_reviews_question rq on rq.id=rqa.question_id
						group by r.id
					) rqs on cr.id = rqs.cr_id
					where 1 $reviewCond   
					order by creationDate desc";
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getTotalReviews ($itemId, $showPendingReview, $type) {
		$approvalFilter="and (cr.approved = ".REVIEW_STATUS_APPROVED.")";
		if ($showPendingReview) {
			$approvalFilter = "and (cr.approved = ".REVIEW_STATUS_CREATED." or cr.approved = ".REVIEW_STATUS_APPROVED.") ";
		}
		
		$reviewCond = "and cr.itemId=".$itemId." and cr.review_type =".$type." and state=1 $approvalFilter";
		
		$db =JFactory::getDBO();
		$query = "select cr.* from #__jbusinessdirectory_company_reviews cr
					where 1 $reviewCond";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getCompanyReviewResponse($reviewId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_review_responses  where reviewId=$reviewId and state=1 order by id";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObjectList();
	}
	
	public function updateReviewScore($itemId, $type) {
		$db =JFactory::getDBO();

		$table = ($type==REVIEW_TYPE_OFFER)? "#__jbusinessdirectory_company_offers" : "#__jbusinessdirectory_companies";
		
		$query = "update $table set review_score=(
					select IF(avg(rating) IS NULL, 0,avg(rating)) from #__jbusinessdirectory_company_reviews 
					where itemId=$itemId and review_type=$type and state=1 and approved!=1) 
				where id= $itemId";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		return true;
	}

	public function deleteReview($reviewId) {
		$db = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_reviews WHERE id = ".$reviewId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function changeAprovalState($reviewId, $state) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews SET approved=$state WHERE id = ".$reviewId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function increaseReviewLike($reviewId) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews SET likeCount=likeCount+1 WHERE id = ".$reviewId ;
	
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function increaseReviewDislike($reviewId) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews SET dislikeCount=dislikeCount+1 WHERE id = ".$reviewId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function increaseReviewLove($reviewId) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews SET loveCount=loveCount+1 WHERE id = ".$reviewId ;
	
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getReviewsByCategory($searchDetails, $maxReviews) {
		$categoriesIDs = isset($searchDetails["categoriesIds"]) ? $searchDetails["categoriesIds"] : null;
		$showPendingReview = isset($searchDetails["showPendingApproval"]) ? $searchDetails["showPendingApproval"] : null;
		$orderBy = isset($searchDetails["orderBy"]) ? $searchDetails["orderBy"] : null;
		$minScore = isset($searchDetails["minScore"]) ? $searchDetails["minScore"] : null;
		$userId = isset($searchDetails["userId"]) ? $searchDetails["userId"] : null;

		$approvalFilter = "and (cr.approved = " . REVIEW_STATUS_APPROVED . ")";
		if ($showPendingReview) {
			$approvalFilter = "and (cr.approved = " . REVIEW_STATUS_CREATED . " or cr.approved = " . REVIEW_STATUS_APPROVED . ") ";
		}

		$whereCatCond = '';
		if (!empty($categoriesIDs) && count($categoriesIDs) > 0) {
			$whereCatCond .= " and cp.mainSubcategory in (";
			$categoryIds = implode(", ", $categoriesIDs);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}

		$whereScoreCond = '';
		if (!empty($minScore)) {
			$whereScoreCond = " and cr.rating >= ".$minScore;
		}

		$whereUser = "";
		if (!empty($userId)) {
			$whereUser = " and cr.userId = $userId";
		}

		if (empty($orderBy)) {
			$orderBy = " cr.id";
		}

		$db = JFactory::getDbo();
		$query = "select cr.*, cp.name as companyName, cp.id as companyId, cp.alias as companyAlias
                  from #__jbusinessdirectory_company_reviews as cr
                  left join #__jbusinessdirectory_companies as cp on cp.id = cr.itemId 
                  where 1 $whereCatCond $approvalFilter $whereScoreCond $whereUser and cr.state = 1 and cr.review_type = " . REVIEW_TYPE_BUSINESS . "
                  order by $orderBy";

		$db->setQuery($query, 0, $maxReviews);

		return $db->loadObjectList();
	}

	public function getReviewPictures($reviewId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_review_pictures where reviewId=$reviewId and picture_enable=1 order by id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getUserReviews($userId, $showPendingReview, $itemId = null, $type = null) {
		$approvalFilter = "and (cr.approved = " . REVIEW_STATUS_APPROVED . ")";
		if ($showPendingReview) {
			$approvalFilter = "and (cr.approved = " . REVIEW_STATUS_CREATED . " or cr.approved = " . REVIEW_STATUS_APPROVED . ") ";
		}

		$whereUser = "";
		if (!empty($userId)) {
			$whereUser = " and cr.userId = $userId";
		}

		$whereType = "";
		if (!empty($type)) {
			$whereType = " and cr.review_type = $type";
		}

		$whereId = "";
		if (!empty($itemId)) {
			$whereId = " and cr.itemId = $itemId";
		}

		$db = JFactory::getDbo();
		$query = "select cr.*,
				  CASE 
				    WHEN cr.review_type = ".REVIEW_TYPE_BUSINESS." THEN cp.name
				    WHEN cr.review_type = ".REVIEW_TYPE_OFFER." THEN co.subject
				  END as item_name
                  from #__jbusinessdirectory_company_reviews as cr
                  left join #__jbusinessdirectory_companies as cp on cp.id = cr.itemId and cr.review_type = ".REVIEW_TYPE_BUSINESS."
                  left join #__jbusinessdirectory_company_offers as co on co.id = cr.itemId and cr.review_type = ".REVIEW_TYPE_OFFER."
                  where 1 $whereUser $approvalFilter $whereType $whereId and cr.state = 1
                  having item_name is not null
                  order by cr.id desc";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getReviewsByStatus($status, $itemNumber = null){
		$limitCondition = '';
		if (!empty($itemNumber)) {
			$limitCondition = ' LIMIT '.$itemNumber;
		}
		$db = JFactory::getDBO();
		$query = "select  bc.*
						   from #__jbusinessdirectory_company_reviews bc 
						   where bc.approved = $status
						   order by bc.creationDate DESC
						   $limitCondition";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
