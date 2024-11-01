<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */
JBusinessUtil::includeValidation();

require_once BD_HELPERS_PATH.'/helper.php';
JBusinessUtil::enqueueScript('libraries/dropzone/dropzone.js');
JBusinessUtil::enqueueStyle('libraries/dropzone/dropzone.css');

JBusinessUtil::loadJQueryUI();

// following translations will be used in js
JText::script('LNG_IMAGE_SIZE_WARNING');

class JBusinessDirectoryViewReview extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->reviewCriteriasAnswer = $this->get('ReviewCriteriasAnswer');
		$this->reviewQuestionAnswer = $this->get('ReviewQuestionAnswer');
		$this->pictures = $this->get('ReviewPictures');
		
		$this->states = $this->get('States');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		$this->addToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar() {
		$canDo = JBusinessDirectoryHelper::getActions();
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_REVIEW' : 'COM_JBUSINESSDIRECTORY_EDIT_REVIEW'), 'menu.png');

		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('review.apply');
			JToolbarHelper::save('review.save');
			JToolbarHelper::divider();
		}
		JToolbarHelper::cancel('review.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#reviews');
	}
}
