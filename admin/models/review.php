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

jimport('joomla.application.component.modeladmin');
/**
 * Company Model for Companies.
 *
 */
class JBusinessDirectoryModelReview extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_REVIEW';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jbusinessdirectory.review';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$id = JFactory::getApplication()->input->getInt('id');
		$this->setState('review.id', $id);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('review.id');
		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');
		
		return $value;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		exit;
		// The folder and element vars are passed when saving the form.
		if (empty($data)) {
			$item		= $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jbusinessdirectory.review', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form)) {
			return false;
		}
		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.review.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	
	public function save($data) {
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('review.id');
		if (empty($data['id'])) {
			$data['id'] = 0;
		}
		$isNew = true;
	
		// Get a row instance.
		$table = $this->getTable();
	
		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}
	
		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
	
		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
	
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		//Store review Criterias data
		if (!$this->saveCriteriasChanges($data, $id)) {
			$this->setError($table->getError());
			return false;
		}

		//Store review Question Answer data
		if (!$this->saveQuestionAnswerChanges($data, $id)) {
			$this->setError($table->getError());
			return false;
		}
	
		$this->setState('review.id', $table->id);

		$oldId = $isNew?0:$id;
		$reviewId = (int) $this->getState('review.id');
		$this->storePictures($data, $reviewId, $oldId);

		$table->updateReviewScore($table->itemId, $table->review_type);
		
		// Clean the cache
		$this->cleanCache();
	
		return true;
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

	public function getStates() {
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}

	public function changeState($id) {
		$this->populateState();
		$reviewsTable = $this->getTable();
		$result =  $reviewsTable->changeState($id);
		
		//update average score
		$review = $reviewsTable->getReview($id);
		$reviewsTable->updateReviewScore($review->itemId, $review->review_type);
		
		return $result;
	}

	public function changeAprovalState($state) {
		$this->populateState();
		$reviewTable = $this->getTable("review");
		$data = $this->getItem($this->getState('review.id'));
		if ($reviewTable->changeAprovalState($this->getState('review.id'), $state)) {
			if ($state == REVIEW_STATUS_APPROVED && $data->approved == REVIEW_STATUS_CREATED) {
				if ($data->review_type == REVIEW_TYPE_BUSINESS) {
					$companiesTable = JTable::getInstance("Company", "JTable");
					$company = $companiesTable->getCompany($data->itemId);
					$ret = EmailService::sendReviewEmail($company, $data);
				} else {
					$offersTable = JTable::getInstance("Offer", "JTable");
					$offer = $offersTable->getOffer($data->itemId);
					$companiesTable = JTable::getInstance("Company", "JTable");
					$company = $companiesTable->getCompany($offer->companyId);
					$ret = EmailService::sendOfferReviewEmail($offer, $company, $data);
				}
				if (!$ret) {
					JFactory::getApplication()->enqueueMessage(JText::_('LNG_ERROR_SENDING_EMAIL'), 'warning');
				}
			}
			
			//update average score
			$reviewTable->updateReviewScore($data->itemId, $data->review_type);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param array $vars
	 * @param array $pks
	 * @param array $contexts
	 * @return bool
	 */
	public function batch($vars, $pks, $contexts) {
		// Sanitize ids.
		$pks = array_unique($pks);
		ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));

			return false;
		}

		$done = false;

		// Set some needed variables.
		$this->user = JBusinessUtil::getUser();
		$this->table = $this->getTable();
		$this->tableClassName = get_class($this->table);
		$this->batchSet = true;
		// Parent exists so let's proceed
		while (!empty($pks)) {
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// Check that the row actually exists
			if (!$this->table->load($pk)) {
				if ($error = $this->table->getError()) {
					// Fatal error
					$this->setError($error);

					return false;
				} else {
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// set new approval state
			if ($vars["approval_status_id"]!="") {
				$this->table->approved = $vars["approval_status_id"];
			}


			// set new approval state
			if ($vars["state_id"]!="") {
				$this->table->state = $vars["state_id"];
			}

			// Check the row.
			if (!$this->table->check()) {
				$this->setError($this->table->getError());

				return false;
			}

			// Store the row.
			if (!$this->table->store()) {
				$this->setError($this->table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to return all Criteria Answers on a Review
	 *
	 * @return mixed
	 */
	public function getReviewCriteriasAnswer() {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('review.id');

		$reviewCriteriaTable = $this->getTable("reviewcriteria");
		$criteriaAnswers = $reviewCriteriaTable->getCriteriasAnswer($itemId);

		return $criteriaAnswers;
	}

	/**
	 * Method to return all Question Answer on a Review
	 * @return mixed
	 */
	public function getReviewQuestionAnswer() {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('review.id');

		$reviewQuestionAnswerTable = $this->getTable("reviewquestionanswer");
		$questionAnswer = $reviewQuestionAnswerTable->getReviewQuestionAnswer($itemId);

		return $questionAnswer;
	}

	public function saveCriteriasChanges($data, $reviewId) {
		$criterias = $data["criteria"];

		$reviewcriteriaTable = $this->getTable('ReviewCriteria');

		foreach ($criterias as $key => $value) {
			if (!$reviewcriteriaTable->updateCriteriaAnswer($reviewId, $key, $value)) {
				return false;
			}
		}

		return true;
	}

	public function saveQuestionAnswerChanges($data, $reviewId) {
		$answers = $data["answer"];

		$questionAnswerTable = $this->getTable('ReviewQuestionAnswer');

		foreach ($answers as $key => $value) {
			if (!$questionAnswerTable->updateReviewQuestionAnswer($reviewId, $key, $value)) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Retrive the pictures for a review
	 * @param $reviewId int this is the id of the review
	 * @return array all the pictures organized in an array
	 */
	public function getReviewPictures() {
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('review.id');
		$query = "SELECT * FROM #__jbusinessdirectory_review_pictures
				WHERE reviewId =" . $itemId . "
				ORDER BY id ";
		$files = $this->_getList($query);
		$pictures = array();
		foreach ($files as $value) {
			$pictures[] = array(
				'id' => $value->id,
				'picture_info' => $value->picture_info,
				'picture_path' => $value->picture_path,
				'picture_enable' => $value->picture_enable,
			);
		}

		return $pictures;
	}

	public function storePictures($data, $reviewId, $oldId) {
		$usedFiles = array();
		if (!empty($data['pictures'])) {
			foreach ($data['pictures'] as $value) {
				array_push($usedFiles, $value["picture_path"]);
			}
		}

		$pictures_path = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH);
		$review_pictures_path = JBusinessUtil::makePathFile(REVIEW_BD_PICTURES_PATH.($reviewId)."/");
		JBusinessUtil::removeUnusedFiles($usedFiles, $pictures_path, $review_pictures_path);

		$picture_ids 	= array();
		foreach ($data['pictures'] as $value) {
			$row = $this->getTable('ReviewPictures');

			$pic = new stdClass();
			$pic->id = 0;
			$pic->reviewId = $reviewId;
			$pic->picture_info = $value['picture_info'];
			$pic->picture_path = $value['picture_path'];
			$pic->picture_enable = $value['picture_enable'];

			$pic->picture_path = JBusinessUtil::moveFile($pic->picture_path, $reviewId, $oldId, REVIEW_BD_PICTURES_PATH);

			//dump("save");
			//dbg($pic);
			//exit;
			if (!$row->bind($pic)) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
			// Make sure the record is valid
			if (!$row->check()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}

			// Store the web link table to the database
			if (!$row->store()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}

			$picture_ids[] = $this->_db->insertid();
		}


		$query = " DELETE FROM #__jbusinessdirectory_review_pictures
				WHERE reviewId = '".$reviewId."'
				".(count($picture_ids)> 0 ? " AND id NOT IN (".implode(',', $picture_ids).")" : "");

		//dbg($query);
		//exit;
		$this->_db->setQuery($query);
		try {
			$this->_db->execute();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}
		//~prepare photos
		//exit;
	}
}
