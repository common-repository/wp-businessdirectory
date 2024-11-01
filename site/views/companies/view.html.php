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

JBusinessUtil::enqueueStyle('libraries/magnific-popup/magnific-popup.css');

JBusinessUtil::enqueueScript('libraries/jquery/jquery.opacityrollover.js');
JBusinessUtil::enqueueScript('libraries/magnific-popup/jquery.magnific-popup.min.js');

if ($appSettings->enable_reviews) {
	JBusinessUtil::enqueueScript('libraries/dropzone/dropzone.js');
	JBusinessUtil::enqueueStyle('libraries/dropzone/dropzone.css');
}

JBusinessUtil::enqueueStyle('libraries/unitegallery/css/unite-gallery.css');
JBusinessUtil::enqueueStyle('libraries/unitegallery/themes/default/ug-theme-default.css');
JBusinessUtil::enqueueScript('libraries/unitegallery/js/unitegallery.js');
JBusinessUtil::enqueueScript('libraries/unitegallery/themes/default/ug-theme-default.js');

if ($appSettings->enable_events) {
	JBusinessUtil::enqueueStyle('libraries/jquery/jquery.timepicker.css');
	JBusinessUtil::enqueueScript('libraries/jquery/jquery.timepicker.min.js');
}

if ($appSettings->enable_services) {
	JBusinessUtil::enqueueScript('libraries/jquery/jquery.steps.js');
	JBusinessUtil::enqueueStyle('libraries/jquery/jquery.steps.css');
}

JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
JBusinessUtil::enqueueScript('libraries/jquery/jquery-ui.js');


JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');

JBusinessUtil::loadJQueryChosen();

//JBusinessUtil::loadMapScripts();

// following translations will be used in js
JText::script('LNG_BAD');
JText::script('LNG_POOR');
JText::script('LNG_REGULAR');
JText::script('LNG_GOOD');
JText::script('LNG_GORGEOUS');
JText::script('LNG_NOT_RATED_YET');
JText::script('LNG_HIDE_REVIEW_QUESTIONS');
JText::script('LNG_SHOW_REVIEW_QUESTIONS');
JText::script('LNG_READ_MORE');
JText::script('LNG_CLAIM_SUCCESSFULLY');
JText::script('LNG_ERROR_CLAIMING_COMPANY');
JText::script('LNG_YES');
JText::script('LNG_NO');
JText::script('LNG_PRODUCT_CATEGORIES');
JText::script('LNG_PRODUCTS');
JText::script('LNG_PRODUCT_DETAILS');
JText::script('LNG_SUBCATEGORIES');
JText::script('LNG_IMAGE_SIZE_WARNING');
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');
JText::script('LNG_QUOTE');
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_REGISTERED');
JText::script('LNG_PLEASE_SELECT_QUANTITY');
JText::script('LNG_ADDING_PRODUCT_TO_SHOPPING_CART');
JText::script('LNG_CLOSE');
JText::script('LNG_MESSAGE');
JText::script('COM_JBUSINESS_ERROR');

JBusinessUtil::includeValidation();

class JBusinessDirectoryViewCompanies extends JViewLegacy {

	public function display($tpl = null) {
		$jinput = JFactory::getApplication()->input;
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->newTab = ($this->appSettings->open_listing_on_new_tab)?" target='_blank'":"";
		$this->user = JBusinessUtil::getUser();
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);
		$session = JFactory::getSession();

		$tabId = $jinput->getInt('tabId');
		if (!isset($tabId)) {
			$tabId = 1;
		}
		$this->tabId = $tabId;

		$this->cart = OfferSellingService::getCartData();
					
		$this->company = $this->get('Company');

		if(empty($this->company)){
			parent::display($tpl);
			return;
		}

		$this->companyAttributes = $this->get('CompanyAttributes');
		$this->companyContactsEmail = $this->defaultAttributes['contact_person'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyContactsWithEmail'):array();
		$this->companyContacts = $this->defaultAttributes['contact_person'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyContacts'):array();
		$this->companyTestimonials = $this->defaultAttributes['testimonials'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyTestimonials'):array();
		$this->companyDepartments = $this->defaultAttributes['contact_person'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyDepartments'):array();
		$this->pictures = $this->defaultAttributes['pictures'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyImages'):array();
		$this->extraPictures = $this->defaultAttributes['pictures'] != ATTRIBUTE_NOT_SHOW && $this->defaultAttributes['custom_gallery'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyExtraImages'):array();
		
		$this->videos = $this->defaultAttributes['video'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyVideos'):array();
		$this->sounds = $this->defaultAttributes['sounds'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanySounds'):array();
		$this->offers = $this->get('CompanyOffers');
		$this->events = $this->get('CompanyEvents');
		$this->realtedCompanies = $this->defaultAttributes['related_listing'] != ATTRIBUTE_NOT_SHOW?$this->get('RelatedCompanies'):array();
		$this->services_list = $this->get('ServicesList');
		$this->associatedEvents = $this->get('AssociatedEvents');
		$this->memberships = $this->get('CompanyMemberships');
		$this->services = $this->get('Services');
		$session->set('company_yelp_id', $this->company->yelp_id);
		$this->reviews = $this->get('Reviews');
		$this->totalReviews = $this->get('TotalReviews');
		$this->reviewsStatistics = JBusinessUtil::getReviewsStatistics($this->totalReviews);
		$this->reviewCriterias = $this->get('ReviewCriterias');
		$this->reviewQuestions = $this->get('ReviewQuestions');
		$this->reviewAnswers = $this->get('ReviewQuestionAnswers');
		$this->companyProjects = $this->get('CompanyProjects');
		$this->companyAnnouncements = $this->get('CompanyAnnouncements');
		
		//$this->products = $this->get('CompanyProducts');
		//$this->productCategories = $this->get('ProductCategories');
		$this->claimDetails = $this->get('ClaimDetails');
		
		$this->companyArticles = $this->get('CompanyArticles');
		//$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);
		$this->userCompanies = JBusinessUtil::getCompaniesOptions($this->company->id, $this->user->ID);
		$this->joinedCompanies = $this->get('RegisteredCompanies');
		$this->associatedCompanies = $this->get('AssociatedCompanies');
		//$this->rating = $this->get('UserRating');
		$this->teamMembers = $this->get('TeamMembers');
		
		$this->viewCount = $this->get('ViewCount');
		
		$this->package = $this->company->package;

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, REVIEWS_TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, CONTACT_TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, RESPONSIBLE_CONTENT_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, PRIVACY_POLICY_TRANSLATION);

			
			JBusinessDirectoryTranslations::updateProjectsTranslations($this->companyProjects, PROJECT_DESCRIPTION_TRANSLATION);
		}

		$this->appSettings->content_responsible = JBusinessUtil::processResponsibleCotent($this->company, $this->appSettings->content_responsible);

		if ($this->appSettings->enable_packages && !empty($this->package)) {
			$this->videos = array_slice($this->videos, 0, $this->package->max_videos);
			$this->pictures = array_slice($this->pictures, 0, $this->package->max_pictures);
			$this->sounds = array_slice($this->sounds, 0, $this->package->max_sounds);
		} else {
			$this->videos = array_slice($this->videos, 0, $this->appSettings->max_video);
			$this->pictures = array_slice($this->pictures, 0, $this->appSettings->max_pictures);
			$this->sounds = array_slice($this->sounds, 0, $this->appSettings->max_sound);
		}
		
		$maxAttach = !empty($this->package) && $this->appSettings->enable_packages ?$this->package->max_attachments :$this->appSettings->max_attachments;
		if (!empty($this->company->attachments)) {
			$this->company->attachments = array_slice($this->company->attachments, 0, $maxAttach);
		}

		$companyTypeIds = explode(',', $this->company->typeId);
		$this->showListLinkButton = false;
		if(!empty($this->appSettings->type_allowed_registering)){
			$allowedTypes = explode(',', $this->appSettings->type_allowed_registering);
			if(!empty($allowedTypes)){
				foreach ($allowedTypes as $type) {
					if (in_array($type, $companyTypeIds, false)) {
						$this->showListLinkButton = true;
						break;
					}
				}
			}
		}
		
		$this->location = $session->get('location');

		$user = JBusinessUtil::getUser();
		$this->allowReviewResponse = false;
		if ($user->authorise('core.admin') || $user->ID == $this->company->userId) {
			$this->allowReviewResponse = true;
		}

		$layout = $jinput->getString('layout');
		if (!empty($layout)) {
			$tpl = $layout;
			if ($layout == 'default') {
				$tpl = null;
			}
		}
		
		parent::display($tpl);
	}

	public function displayReviews($reviews, $tpl){
		$this->reviews = $reviews;
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->company = $this->get('Company');
		$this->reviewCriterias = $this->get('ReviewCriterias');
		$this->reviewQuestions = $this->get('ReviewQuestions');
		$this->reviewAnswers = $this->get('ReviewQuestionAnswers');

		$this->user = JBusinessUtil::getUser();
		$this->allowReviewResponse = false;
		if ($this->user->authorise('core.admin') || $this->user->ID == $this->company->userId) {
			$this->allowReviewResponse = true;
		}
		parent::display($tpl);
	}
}
