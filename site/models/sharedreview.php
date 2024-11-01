<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

JTable::addIncludePath(DS . 'components' . 'com_jbusinessdirectory' . DS . 'tables');

class JBusinessDirectoryModelSharedReview extends JModelItem {
	public function __construct() {
		parent::__construct();
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$reviewShareType = JFactory::getApplication()->input->get('type');
		$this->modelName = 'Companies';
		switch ($reviewShareType) {
			case REVIEW_TYPE_BUSINESS:
				$this->companyId = JFactory::getApplication()->input->get('companyId');
				$this->companyId = intval($this->companyId);
				$this->modelName = 'Companies';
				break;
			case REVIEW_TYPE_OFFER:
				$this->offerId = JFactory::getApplication()->input->get('offerId');
				$this->offerId = intval($this->offerId);
				$this->modelName = 'Offer';
				break;
			default:
				$this->companyId = JFactory::getApplication()->input->get('companyId');
				$this->companyId = intval($this->companyId);
				$this->modelName = 'Companies';
				break;
		}
	}

	public function getItem($pk = NULL) {		
	}

	public function getReview() {
		$reviewId = JFactory::getApplication()->input->getInt("review_id");
		$model = JModelLegacy::getInstance($this->modelName, 'JBusinessDirectoryModel', array('ignore_request' => true));
		return $model->getReviews($reviewId)[0];
	}

	public function getReviewQuestionAnswers() {
		$model = JModelLegacy::getInstance($this->modelName, 'JBusinessDirectoryModel', array('ignore_request' => true));
		return $model->getReviewQuestionAnswers();
	}

	public function getReviewCriterias() {
		$model = JModelLegacy::getInstance($this->modelName, 'JBusinessDirectoryModel', array('ignore_request' => true));
		return $model->getReviewCriterias();
	}

	public function getReviewQuestions() {
		$model = JModelLegacy::getInstance($this->modelName, 'JBusinessDirectoryModel', array('ignore_request' => true));
		return $model->getReviewQuestions();
	}
}
