<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JBusinessUtil::includeValidation();
/**
 * The HTML  View.
 */
require_once BD_HELPERS_PATH.'/helper.php';
class JBusinessDirectoryViewEmailTemplate extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->types = $this->get('EmailTypes');

		$this->placeHolders = $this->get('AvailablePlaceholders');
		
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(EMAIL_TRANSLATION, $this->item->email_id);
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
		$isNew = ($this->item->email_id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_EMAIL_TEMPLATE' : 'COM_JBUSINESSDIRECTORY_EDIT_EMAIL_TEMPLATE'), 'menu.png');
		
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('emailtemplate.apply');
			JToolbarHelper::save('emailtemplate.save');
		}
		
		JToolbarHelper::cancel('emailtemplate.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#e-mail-templates');
	}
}
