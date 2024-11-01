<?php
/*------------------------------------------------------------------------
# JAdManager
# author SoftArt
# copyright Copyright (C) 2012 SoftArt.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.SoftArt.com
# Technical Support:  Forum - http://www.SoftArt.com/forum/j-admanger-forum/?p=1
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

JBusinessUtil::loadJQueryUI();

JBusinessUtil::enqueueScript('libraries/chosen/ajax-chosen.min.js');
JBusinessUtil::enqueueScript('libraries/icon-picker/fontawesome-iconpicker.js');
JBusinessUtil::enqueueStyle('libraries/icon-picker/fontawesome-iconpicker.css');

jimport('joomla.html.pane');
require_once BD_HELPERS_PATH.'/helper.php';

// following translations will be used in js
JText::script('LNG_INPUT_TYPE_NO_OPTIONS');

JBusinessUtil::includeValidation();

class JBusinessDirectoryViewAttribute extends JBusinessDirectoryAdminView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->state = $this->get('State');
		$this->item	 = $this->get('Item');
		$this->attributeTypes = $this->get('AttributeTypes');
		$this->attributeOptions = $this->get('AttributeOptions');

		// Check if the attribute is being created or edited, and assign the type value accordingly
		if (!isset($this->item->attribute_type)) {
			$this->type = $this->state->get('attribute.type');
		} else {
			$this->type = $this->item->attribute_type;
		}
		
		$this->states		= JBusinessDirectoryHelper::getStatuses();
		$this->translations = JBusinessDirectoryTranslations::getAllTranslations(ATTRIBUTE_TRANSLATION, $this->item->id);
		$this->languages = JBusinessUtil::getLanguages();
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();


		$type = $this->type;
		if ($type == 4) {
			$type = 5;
		}
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, $type);
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

		JToolbarHelper::title(JText::_($isNew ? 'COM_JBUSINESSDIRECTORY_NEW_ITEM' : 'COM_JBUSINESSDIRECTORY_EDIT_ITEM'), 'menu.png');
		
		if ($canDo->get('core.edit')) {
			JToolbarHelper::apply('attribute.apply');
			JToolbarHelper::save('attribute.save');
		}
		
		JToolbarHelper::cancel('attribute.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
		JToolBarHelper::help('', false, DOCUMENTATION_URL.'businessdiradmin.html#custom-attributes');
	}
}
