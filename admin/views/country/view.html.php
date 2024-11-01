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
require_once BD_HELPERS_PATH.'/helper.php';

JBusinessUtil::enqueueScript('libraries/cropper/cropper.js');
JBusinessUtil::enqueueScript('libraries/cropper/canvas-toBlob.js');
JBusinessUtil::enqueueStyle('libraries/cropper/cropper.css');

JBusinessUtil::includeValidation();

class JBusinessDirectoryViewCountry extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(COUNTRY_TRANSLATION, $this->item->id);
		$this->languages = JBusinessUtil::getLanguages();
		
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

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_COUNTRY' : 'COM_JBUSINESSDIRECTORY_EDIT_COUNTRY'), 'menu.png');
		
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('country.apply');
			JToolbarHelper::save('country.save');
		}
		
		JToolbarHelper::cancel('country.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#countries');
	}
}
