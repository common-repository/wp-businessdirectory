<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JBusinessUtil::enqueueScript('libraries/chosen/ajax-chosen.min.js');

/**
 * The HTML View.
 */
require_once BD_HELPERS_PATH.'/helper.php';
JBusinessUtil::includeValidation();

class JBusinessDirectoryViewReviewCriteria extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(REVIEW_CRITERIA_TRANSLATION, $this->item->id);
		$this->languages = JBusinessUtil::getLanguages();

		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);

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

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_REVIEW_CRITERIA' : 'COM_JBUSINESSDIRECTORY_EDIT_REVIEW_CRITERIA'), 'menu.png');
		
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('reviewcriteria.apply');
			JToolbarHelper::save('reviewcriteria.save');
		}

		
		JToolbarHelper::cancel('reviewcriteria.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#review-criteria');
	}
}
