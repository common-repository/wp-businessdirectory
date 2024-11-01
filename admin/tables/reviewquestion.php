<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JTableReviewQuestion extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_reviews_question', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getQuestions() {
		$db = JFactory::getDBO();
		$query = "select * 
                  from #__jbusinessdirectory_company_reviews_question 
                  where 1 and published = 1
                  order by ordering";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Retrieves all the review questions along with the associated answers
	 * @return mixed
	 */
	public function getQuestionAnswers() {
		$db = JFactory::getDBO();
		$query = "select rq.name as question, rq.type, rq.ordering, qa.answer, qa.user_id, qa.id
                  from
                  #__jbusinessdirectory_company_reviews_question rq
                  left join #__jbusinessdirectory_company_reviews_question_answer qa on qa.question_id = rq.id
                  order by rq.ordering";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Retrieves the review questions (along with it's answers) that belong to a certain review
	 * @param $reviewId
	 * @return mixed
	 */
	public function getQuestionAnswersByReview($reviewId) {
		$db = JFactory::getDBO();
		$query = "select rq.name as question, rq.type, rq.ordering, qa.answer, qa.user_id, qa.id
                  from
                  #__jbusinessdirectory_company_reviews_question rq
                  left join #__jbusinessdirectory_company_reviews_question_answer qa on qa.question_id = rq.id
                  where qa.review_id = $reviewId
                  order by rq.ordering";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function changeState($id, $value) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews_question SET published = '$value' WHERE id = ".$id ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function deleteAllDependencies($itemId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_company_reviews_question_answer where question_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		return true;
	}
}
