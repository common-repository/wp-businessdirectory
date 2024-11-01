<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JTableReviewQuestionAnswer extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_reviews_question_answer', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getAnswersByCompany($companyId) {
		$db = JFactory::getDBO();
		$query = "select rqa.*
                from #__jbusinessdirectory_company_reviews_question_answer rqa
                left join #__jbusinessdirectory_company_reviews cr on cr.id = rqa.review_id
                where cr.itemId =".$companyId." and review_type = ".REVIEW_TYPE_BUSINESS."
                ";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get all review question answers
	 *
	 * @param $reviewId
	 * @return mixed
	 */
	public function getReviewQuestionAnswer($reviewId) {
		$db = JFactory::getDbo();
		$query = "select ra.*,rq.name review_question,rq.type from #__jbusinessdirectory_company_reviews_question_answer ra LEFT JOIN #__jbusinessdirectory_company_reviews_question rq ON ra.question_id=rq.id WHERE ra.review_id=$reviewId";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Update Review question answer
	 *
	 * @param $reviewId
	 * @param $questionId
	 * @param $answer
	 * @return mixed
	 */
	public function updateReviewQuestionAnswer($reviewId, $questionId, $answer) {
		$db = JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews_question_answer SET answer = '" . $answer . "' where question_id = '" . $questionId . "' and review_id = '" . $reviewId . "' ";
		$db->setQuery($query);
		return $db->execute();
	}
}
