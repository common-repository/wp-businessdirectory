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
if ($appSettings->enable_ratings) {
	JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
	JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
}
JBusinessUtil::loadJQueryChosen();

// following translations will be used in js
JText::script('LNG_BAD');
JText::script('LNG_POOR');
JText::script('LNG_REGULAR');
JText::script('LNG_GOOD');
JText::script('LNG_GORGEOUS');
JText::script('LNG_NOT_RATED_YET');
JText::script('COM_JBUSINESS_ERROR');
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');
JText::script('LNG_HIDE_MAP');
JText::script('LNG_SHOW_MAP');

JBusinessUtil::includeValidation();
class JBusinessDirectoryViewCatalog extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	
	public function display($tpl = null) {
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);

		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");
		
		$categoryId= JFactory::getApplication()->input->get('categoryId');
		$this->letter = $this->get('Letter');
		
		$this->companies = $this->get('CompaniesByLetter');
		$this->letters = $this->get('UsedLetter');
		
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, CONTACT_TERMS_CONDITIONS_TRANSLATION);
		}

		
		$this->location = $this->get("Location");
		
		$this->pagination = $this->get('Pagination');
				
		parent::display($tpl);
	}
}
