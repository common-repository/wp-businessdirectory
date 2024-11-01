<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once BD_HELPERS_PATH.'/helper.php';
JBusinessUtil::includeValidation();

JBusinessUtil::loadJQueryUI();

JBusinessUtil::enqueueScript('libraries/moment/moment.min.js');
JBusinessUtil::enqueueStyle('libraries/date/daterangepicker.css');
JBusinessUtil::enqueueScript('libraries/date/daterangepicker.js');

JBusinessUtil::enqueueScript('libraries/tippyjs/popper.js');
JBusinessUtil::enqueueScript('libraries/tippyjs/tippy.js');
JBusinessUtil::enqueueScript('libraries/bootstrap/bootstrap.js');

JText::script('LNG_ADDRESES_FORMAT_TEXT_1');
JText::script('LNG_ADDRESES_FORMAT_TEXT_2');
JText::script('LNG_ADDRESES_FORMAT_TEXT_3');
JText::script('LNG_ADDRESES_FORMAT_TEXT_4');
JText::script('LNG_ADDRESES_FORMAT_TEXT_5');
JText::script('LNG_ADDRESES_FORMAT_TEXT_6');
JText::script('LNG_ADDRESES_FORMAT_TEXT_7');
JText::script('LNG_ADDRESES_FORMAT_TEXT_8');
JText::script('LNG_DELETE_DEMO_CONFIRM');

class JBusinessDirectoryViewApplicationSettings extends JBusinessDirectoryAdminView {
	public function display($tpl = null) {
		JBusinessUtil::checkJCHPlugin();
		$this->item = $this->get('Data');
		// temporrary solution
		$this->item->id = 0;
		
		$this->reviews_translations = '';
		$this->translations = '';
		
		$this->languagesTranslations = JBusinessUtil::getLanguages();
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->responsiblePersonPlaceholders = JBusinessUtil::getPlaceholders();
		$this->categoryOrderOptions = $this->get('CategoryOrderOptions');
		$this->citiesRegionsOrderOptions = $this->get('CitiesRegionsOrderOptions');
		$this->cssFile = $this->get("CssFile");

		if ($this->appSettings->enable_multilingual) {
			$this->translations = JBusinessDirectoryTranslations::getAllTranslations(TERMS_CONDITIONS_TRANSLATION, $this->item->applicationsettings_id);
			$this->terms_conditions_article_id_translations = JBusinessDirectoryTranslations::getAllTranslations(TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $this->item->applicationsettings_id);
			$this->reviews_translations = JBusinessDirectoryTranslations::getAllTranslations(REVIEWS_TERMS_CONDITIONS_TRANSLATION, $this->item->applicationsettings_id);
			$this->reviews_terms_conditions_article_id_translations = JBusinessDirectoryTranslations::getAllTranslations(REVIEWS_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $this->item->applicationsettings_id);
			$this->contact_translations = JBusinessDirectoryTranslations::getAllTranslations(CONTACT_TERMS_CONDITIONS_TRANSLATION, $this->item->applicationsettings_id);
			$this->contact_terms_conditions_article_id_translations = JBusinessDirectoryTranslations::getAllTranslations(CONTACT_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $this->item->applicationsettings_id);
			$this->content_responsible_translations = JBusinessDirectoryTranslations::getAllTranslations(RESPONSIBLE_CONTENT_TRANSLATION, $this->item->applicationsettings_id);
			$this->privacy_policy_translations = JBusinessDirectoryTranslations::getAllTranslations(PRIVACY_POLICY_TRANSLATION, $this->item->applicationsettings_id);
			$this->privacy_policy_article_id_translations = JBusinessDirectoryTranslations::getAllTranslations(PRIVACY_POLICY_ARTICLE_ID_TRANSLATION, $this->item->applicationsettings_id);
		}
		
		$this->packageOptions = JBusinessDirectoryHelper::getPackageOptions();
		$this->attributeConfiguration = JBusinessDirectoryHelper::getAttributeConfiguration();
		$this->mainCategoriesOptions =  JBusinessUtil::getCategoriesOptions(true,CATEGORY_TYPE_BUSINESS, null, false, true);

		$this->offerCategoriesOptions =  JBusinessUtil::getCategoriesOptions(true,CATEGORY_TYPE_OFFER , null, false, false);
		$this->eventCategoriesOptions =  JBusinessUtil::getCategoriesOptions(true,CATEGORY_TYPE_EVENT , null, false, false);
		
		JBusinessDirectoryHelper::addSubmenu('applicationsettings');
		$this->languages = $this->get('Languages');
		$this->userGroups = JBusinessUtil::getUserGroups();

		$this->searchFields = $this->get('SearchFields');
		$this->typeAllowedRegistering = JBusinessUtil::getListingTypes();
		$this->searchFilterFields = $this->get('SearchFilterFields');
		$this->offerSearchFilterFields = $this->get('OfferSearchFilterFields');
		$this->eventSearchFilterFields = $this->get('EventSearchFilterFields');
		$this->quotesFilterFields = $this->get('QuotesFilterFields');
		$this->orderByFields = $this->get('OrderByFields');
		$this->listingsDisplayInfo = $this->get('ListingsDisplayInfo');
		
		$this->urlFields = $this->get('URLFields');

		$this->autocompleteConfig = $this->get('AutocompleteConfig');
		$this->autocompleteConfigOptions = $this->get('AutocompleteConfigOptions');
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		$canDo = JBusinessDirectoryHelper::getActions();
		$user = JBusinessUtil::getUser();
		
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		
		JToolBarHelper::title(JText::_('LNG_APPLICATION_SETTINGS'), 'generic.png');
		
		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_jbusinessdirectory', 'core.create'))) > 0) {
			JToolbarHelper::apply('applicationsettings.apply');
			JToolbarHelper::save('applicationsettings.save');
		}
		
		JToolBarHelper::cancel('applicationsettings.cancel');
		
		
		//JToolbarHelper::custom('database.fix', 'refresh', 'refresh', 'LNG_DATABASE_FIX', false);

		JToolbarHelper::custom('applicationsettings.sendTestEmail', 'mail', 'mail', 'Send Email', false);
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jbusinessdirectory');
		}
		JToolBarHelper::help('', false, DOCUMENTATION_URL . 'businessdiradmin.html#general-settings');
	}
}
