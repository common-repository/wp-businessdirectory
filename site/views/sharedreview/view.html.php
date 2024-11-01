<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

JHtml::_('bootstrap.loadCss', true);
JBusinessUtil::enqueueStyle('libraries/magnific-popup/magnific-popup.css');

JBusinessUtil::enqueueScript('libraries/jquery/jquery.opacityrollover.js');
JBusinessUtil::enqueueScript('libraries/magnific-popup/jquery.magnific-popup.min.js');
if ($appSettings->enable_ratings) {
	JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
	JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
}


class JBusinessDirectoryViewSharedReview extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	
	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->review =  $this->get('Review');

		$this->reviewShareType = JFactory::getApplication()->input->get('type');
		if ($this->reviewShareType == REVIEW_TYPE_BUSINESS) {
			$this->reviewAnswers = $this->get('ReviewQuestionAnswers');
			$this->reviewCriterias = $this->get('ReviewCriterias');
			$this->reviewQuestions = $this->get('ReviewQuestions');
		} else {
			$this->reviewAnswers = null;
			$this->reviewCriterias = null;
			$this->reviewQuestions = null;
		}

		parent::display($tpl);
	}
}
