<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'reviews.php');

class JBusinessDirectoryModelManageReviews extends JBusinessDirectoryModelReviews {
	public function __construct() {
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Review', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 *
	 * @return object with data
	 */
	public function getItems() {
		// Load the list items.
		$result = parent::getItems();

		// If empty or an error, just return.
		if (empty($result)) {
			return array();
		} else {
			$companyReviewsTable = $this->getTable("Review");
			foreach ($result as $review) {
				$review->responses = $companyReviewsTable->getCompanyReviewResponse($review->id);
				if (isset($review->scores)) {
					$review->scores = explode(",", $review->scores);
				}
				if (isset($review->criteria_ids)) {
					$review->criteriaIds = explode(",", $review->criteria_ids);
				}
				if (isset($review->answer_ids)) {
					$review->answerIds = explode(",", $review->answer_ids);
				}
				if (isset($review->question_ids)) {
					$review->questionIds = explode(",", $review->question_ids);

					$temp = array();
					$i = 0;
					foreach ($review->questionIds as $val) {
						$temp[$val] = $review->answerIds[$i];
						$i++;
					}
					$review->answerIds = $temp;
				}
				$review->pictures = $companyReviewsTable->getReviewPictures($review->id);
			}
		}

		return $result;
	}

	protected function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JBusinessUtil::getUser();
		$companyIds = JBusinessUtil::getCompaniesByUserId($user->ID,true);

		if (empty($companyIds) || empty($user->ID)) {
			$companyIds = array(-1);
		}
		$companyIds = implode(',', $companyIds);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'cr.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_company_reviews').' AS cr');

		// Join over the company types
		$query->select("
        CASE 
            WHEN cr.review_type = ".REVIEW_TYPE_BUSINESS." THEN cp.name
            WHEN cr.review_type = ".REVIEW_TYPE_OFFER." THEN co.subject
          END as listingName
        ");
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies').' AS cp ON cp.id=cr.itemId');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_offers').' AS co ON co.id=cr.itemId');

		// Join over the company types
		$query->select('u.display_name as user_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS u ON u.id=cr.userId');

		// Join over the company types
		$query->select('rr.response as review_response');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_review_responses').' AS rr ON rr.reviewId=cr.id');
		

		$typeId = $this->getState('filter.type_id');
		if (!empty($typeId)) {
			$query->where('cr.review_type='. $typeId);
		}

		$query->where("(cp.id in ($companyIds) or co.companyId in ($companyIds))");

		$query->group('cr.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'cr.creationDate');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	public function getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$db = $this->getDbo();
			$query = $this->getListQuery();
			$db->setQuery($query);
			$result = $db->loadObjectList();
			require_once(BD_HELPERS_PATH.'/dirpagination.php');
			$this->_pagination = new JBusinessDirectoryPagination(count($result), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}

	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);
		
		// Get a group row instance.
		$table = $this->getTable();
		
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			$review = $table->getReview($itemId);
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}
			$abuseTable = $this->getTable('ReviewAbuse');
			$abuseTable->deleteAbuseByReview($itemId);
			$responseTable = $this->getTable('ReviewResponse');
			$responseTable->deleteResponseByReview($itemId);

			$table->updateReviewScore($review->itemId, $review->review_type);
		}
		
		// Clean the cache
		$this->cleanCache();
		
		return true;
	}

	public function getReviewTypes() {
		$types = array();
		$companyType = new stdClass();
		$companyType->value = REVIEW_TYPE_BUSINESS;
		$companyType->text = JText::_('LNG_COMPANY');
		array_push($types, $companyType);

		$offerType = new stdClass();
		$offerType->value = REVIEW_TYPE_OFFER;
		$offerType->text = JText::_('LNG_OFFER');
		array_push($types, $offerType);

		return $types;
	}
}
