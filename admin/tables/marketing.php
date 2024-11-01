<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableMarketing extends JTable {
	public $id = null;
	public $email_type = null;
	public $number_email_sent = null;
	public $sending_date = null;
	public $failed_sent = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_marketing_email_sent', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Get all offer in a month interval of specific company
	 * @param $companyId
	 * @param int $limitstart
	 * @param int $limit
	 * @return mixed
	 */
	public function getMonthlyOffer($companyId, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDBO();
		$orderByClause = "order by co.id desc";

		if (!empty($orderBy)) {
			$orderByClause = "order by $orderBy";
		}

		$appSettings =  JBusinessUtil::getApplicationSettings();
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));

		$approvalFilter = "and (co.approved = " . OFFER_APPROVED . ")";
		if ($showPendingApproval) {
			$approvalFilter = "and (co.approved = " . OFFER_CREATED . " or co.approved = " . OFFER_APPROVED . ") ";
		}


		$query = " select co.id, co.specialPrice, co.subject, co.short_description, co.address, co.city, co.county, co.latitude, co.longitude, co.viewCount, co.startDate, co.endDate, co.alias, co.view_type, co.article_id, co.url,
					op.picture_info,op.picture_path,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.name separator '#|') as categories 
					from
					#__jbusinessdirectory_company_offers co
					left join  #__jbusinessdirectory_company_offer_pictures op on co.id=op.offerId
					and
						(op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_offers co1
							left join  #__jbusinessdirectory_company_offer_pictures op1 on co1.id=op1.offerId
							where op1.picture_enable=1
							group by co1.id
						)
					)
					left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					where co.companyId=$companyId and co.state=1  and (co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00') and  (co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00') and co.created > DATE_SUB(DATE(NOW()), INTERVAL 1 MONTH) 
					$approvalFilter
					group by co.id
					$orderByClause ";

		//dump($query);
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	/**
	 * Get all event in a month interval of specific company
	 * @param $companyId
	 * @param int $limitstart
	 * @param int $limit
	 * @return mixed
	 */
	public function getMonthlyEvents($companyId, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));

		$approvalFilter = "and (co.approved = " . EVENT_APPROVED . ")";
		if ($showPendingApproval) {
			$approvalFilter = "and (co.approved = " . EVENT_CREATED . " or co.approved = " . EVENT_APPROVED . ") ";
		}

		$query = "select co.*, op.picture_path, et.name as eventType, cg.markerLocation as categoryMarker
					from #__jbusinessdirectory_company_events co
					left join  #__jbusinessdirectory_company_event_pictures op on co.id=op.eventId
					and (op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_events co1
							left join  #__jbusinessdirectory_company_event_pictures op1 on co1.id=op1.eventId
							where op1.picture_enable=1 and company_id=$companyId
							group by co1.id))
					left join  #__jbusinessdirectory_company_event_types et on co.type=et.id
					left join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					where co.state=1 and co.end_date>=DATE(now()) and company_id=$companyId $approvalFilter and co.created > DATE_SUB(DATE(NOW()), INTERVAL 1 MONTH) 
					group by co.id	
					order by co.start_date";

		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();
		return $result;
	}

	/**
	 * Get all reviews in a month interval of specific company
	 * @param $companyId
	 * @param $showPendingReview
	 * @param $type
	 * @return mixed
	 */
	public function getMonthlyReviews($companyId, $showPendingReview, $type) {
		$approvalFilter = "and (cr.approved = " . REVIEW_STATUS_APPROVED . ")";
		if ($showPendingReview) {
			$approvalFilter = "and (cr.approved = " . REVIEW_STATUS_CREATED . " or cr.approved = " . REVIEW_STATUS_APPROVED . ") ";
		}

		$db = JFactory::getDBO();
		$query = "select cr.*, scores, criteria_ids, answer_ids, question_ids from #__jbusinessdirectory_company_reviews cr
					left join (
						select GROUP_CONCAT(ruc.score) as scores, GROUP_CONCAT(rc.id) as criteria_ids, r.id as cr_id
						from #__jbusinessdirectory_company_reviews r
						left join #__jbusinessdirectory_company_reviews_user_criteria ruc on r.id=ruc.review_id
						left join #__jbusinessdirectory_company_reviews_criteria rc on rc.id=ruc.criteria_id
						group by r.id
					) rcs on cr.id = rcs.cr_id
					left join (
						select GROUP_CONCAT(rqa.id) as answer_ids, GROUP_CONCAT(rq.id) as question_ids, r.id as cr_id
						from #__jbusinessdirectory_company_reviews r
						left join #__jbusinessdirectory_company_reviews_question_answer rqa on r.id=rqa.review_id
						left join #__jbusinessdirectory_company_reviews_question rq on rq.id=rqa.question_id
						group by r.id
					) rqs on cr.id = rqs.cr_id
					where cr.itemId=" . $companyId . " and cr.review_type =" . $type . " and state=1 $approvalFilter and creationDate > DATE_SUB(DATE(NOW()), INTERVAL 1 MONTH)   
					order by creationDate desc ";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObjectList();
	}

	/**
	 * Get all companies with a free plan business
	 * @return mixed
	 */
	public function getFreePlanBusiness() {
		$db = JFactory::getDBO();

		$query = "select bc.*  
						  from #__jbusinessdirectory_companies bc
							INNER JOIN #__jbusinessdirectory_packages pac on bc.package_id = pac.id and pac.price = 0 
							";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * get logs of saved data from email history
	 * @return mixed
	 */
	public function getEmailLogs() {
		$db = JFactory::getDBO();

		$query = "select bc.email_type, max(bc.sending_date) as lastSentDate, sum(bc.number_email_sent) as totalSent, sum(bc.failed_sent) as totalFailed 
              from #__jbusinessdirectory_company_marketing_email_sent bc
              group by bc.email_type";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
