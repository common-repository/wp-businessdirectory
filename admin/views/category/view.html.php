<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_categories
 *
 * @copyright  Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html;
 */

defined('_JEXEC') or die('Restricted access');
require_once BD_HELPERS_PATH.'/helper.php';

JBusinessUtil::enqueueStyle('libraries/chosen/chosenIcon.css');
JBusinessUtil::enqueueScript('libraries/chosen/chosenIcon.jquery.js');

JBusinessUtil::enqueueScript('libraries/cropper/cropper.js');
JBusinessUtil::enqueueScript('libraries/cropper/canvas-toBlob.js');
JBusinessUtil::enqueueStyle('libraries/cropper/cropper.css');

JBusinessUtil::enqueueScript('libraries/bootstrap/bootstrap-tagsinput.min.js');
JBusinessUtil::enqueueStyle('libraries/bootstrap/bootstrap-tagsinput.css');

JBusinessUtil::includeValidation();
/**
 * HTML View class for the Categories component
 *
 */
class JBusinessDirectoryViewCategory extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->types = $this->get('CategoryTypes');

		$catId = (int)$this->state->get('category.id');

		$this->appSettings = JBusinessUtil::getApplicationSettings();

		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(CATEGORY_TRANSLATION, $this->item->id);
		$this->translationsMeta = JBusinessDirectoryTranslations::getAllTranslations(CATEGORY_META_TRANSLATION, $this->item->id);
		$this->languages = JBusinessUtil::getLanguages();
		$input = JFactory::getApplication()->input;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		if (isset($this->item->type)) {
			$this->typeSelected = $this->item->type;
		} else {
			$this->typeSelected = $this->state->get('category.type');
		}

		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(false, $this->typeSelected, $catId, true);

		$input->set('hidemainmenu', true);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar() {
		$isNew = ($this->item->id == 0);

		// Get the results for each action.
		$canDo = JBusinessDirectoryHelper::getActions();

		// Prepare the toolbar.
		JToolbarHelper::title(JText::_("LNG_CATEGORY"), ($isNew ? 'add' : 'edit'));

		// For new records, check the create permission.
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('category.apply');
			JToolbarHelper::save('category.save');
		}
		
		if (empty($this->item->id)) {
			JToolbarHelper::cancel('category.cancel');
		} else {
			JToolbarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
		}
		
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#categories');
	}
}
