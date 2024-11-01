<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableReviewCriteria extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_reviews_criteria', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCriterias() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_reviews_criteria
					where published = 1 ";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to retrieve review criterias based on the
	 * company category.
	 *
	 * @param $companyId int ID of the company
	 * @return array containing the review criterias
	 */
	public function getCriteriasByCategory($companyId) {
		$db =JFactory::getDbo();

		$query = "select rc.* 
                  from #__jbusinessdirectory_company_reviews_criteria as rc
                  left join #__jbusinessdirectory_company_reviews_criteria_category as rcc on rcc.criteriaId = rc.id
                  left join #__jbusinessdirectory_companies as cp on cp.mainSubcategory = rcc.categoryId
                  where rc.published = 1 and (cp.id = $companyId or rcc.categoryId = -1)";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get all criteria answers of a review
	 * @param $reviewId
	 * @return mixed
	 */
	public function getCriteriasAnswer($reviewId) {
		$db = JFactory::getDbo();
		$query = "select uc.*,rc.name AS criteria_name from  #__jbusinessdirectory_company_reviews_user_criteria uc LEFT JOIN #__jbusinessdirectory_company_reviews_criteria rc ON uc.criteria_id=rc.id WHERE uc.review_id=$reviewId";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Update criteria answer
	 *
	 * @param $reviewId
	 * @param $criteriaId
	 * @param $score
	 * @return mixed
	 */
	public function updateCriteriaAnswer($reviewId, $criteriaId, $score) {
		$db = JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_reviews_user_criteria SET score = '" . $score . "' where criteria_id = '" . $criteriaId . "' and review_id = '" . $reviewId . "' ";
		$db->setQuery($query);
		return $db->execute();
	}

	/**
	 * Method to retrieve the ID-s of the selected categories
	 * associated to a particular review criteria.
	 *
	 * @param $criteriaId int ID of the criteria
	 * @return array IDs of the selected categories
	 */
	public function getSelectedCriteriaCategories($criteriaId) {
		$db =JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_company_reviews_criteria_category cc 
                  inner join #__jbusinessdirectory_categories c on cc.categoryId=c.id 
                  where criteriaId=$criteriaId";

		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->categoryId;
		}

		$db->setQuery($query);
		return $result;
	}

	/**
	 * Method that stores criterias-categories relations
	 *
	 * @param $criteriaId int ID of the criteria
	 * @param $categoryIds array category ID-s
	 * @return bool
	 */
	public function insertCriteriaRelations($criteriaId, $categoryIds) {
		$db =JFactory::getDbo();

		if (empty($categoryIds)) {
			$query = "delete from #__jbusinessdirectory_company_reviews_criteria_category where criteriaId =$criteriaId";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}

			return;
		}

		$db =JFactory::getDbo();
		$query = "insert into #__jbusinessdirectory_company_reviews_criteria_category(criteriaId, categoryId) values ";
		foreach ($categoryIds as $categoryId) {
			$query = $query."(".$criteriaId.",".$categoryId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE criteriaId=values(criteriaId), categoryId=values(categoryId)";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		$filter ="(";
		foreach ($categoryIds as $categoryId) {
			$filter = $filter.$categoryId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_company_reviews_criteria_category where criteriaId =$criteriaId and categoryId not in $filter";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}

	public function deleteAllDependencies($itemId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_company_reviews_criteria_category where criteriaId= $itemId";
		$db->setQuery($sql);
		$db->execute();

		return true;
	}

	public function changeState($itemId, $value) {
		$db =JFactory::getDBO();
		$itemId = $db->escape($itemId);
		$query = "UPDATE #__jbusinessdirectory_company_reviews_criteria SET published = '$value' WHERE id = ".$itemId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
