<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

use MVC\Language\LanguageHelper;

class JBusinessDirectoryModelApplicationSettings extends JModelAdmin {
	public static $appSettings;

	protected $dataToBeCasted = array(
		"max_video",
		"max_pictures",
		"max_sound",
		"max_attachments",
		"logo_width",
		"logo_height",
		"cover_width",
		"cover_height",
		"gallery_width",
		"gallery_height",
		"vat",
		"appointments_commission",
		"offer_selling_commission",
		"event_tickets_commission",
		"map_zoom",
		"marker_size_width",
		"marker_size_height",
		"max_categories",
		"max_business",
		"max_description_length",
		"max_short_description_length",
		"max_slogan_length",
		"search_filter_items",
		"request_quote_radius",
		"max_offers",
		"offer_search_filter_items",
		"max_events",
		"event_booking_timeout",
		"max_listing_events_display",
		"event_search_filter_items",
		"speaker_img_width",
		"speaker_img_height",
	);

	public function __construct() {
		parent::__construct();
		$id = JFactory::getApplication()->input->get('applicationsettings_id', 0);
		$this->setId((int) $id);
	}

	public function setId($applicationsettings_id) {
		// Set id and wipe data
		$this->_applicationsettings_id = $applicationsettings_id;
		$this->_data                   = null;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'ApplicationSettings', $prefix = 'Table', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	public function &getData() {
		$appTable    = $this->getTable('ApplicationSettings');
		$appSettings = $appTable->getApplicationSettings();
		$this->_data = new stdClass();
		foreach ($appSettings as $key => $value) {
			$this->_data->{$value->name} = $value->value;
			//$this->_data->{'hidden_'.$value->name} = $value->id;
		}

		$config = JBusinessUtil::getSiteConfig();
		$this->_data->sendmail_from = $config->mailfrom;
		$this->_data->sendmail_name = $config->fromname;

		if (!$this->_data) {
			$this->_data                            = new stdClass();
			$this->_data->applicationsettings_id    = null;
			$this->_data->company_name              = null;
			$this->_data->company_email             = null;
			$this->_data->currency_id               = null;
			$this->_data->country_ids               = null;
			$this->_data->css_style                 = null;
			$this->_data->css_module_style          = null;
			$this->_data->show_frontend_language    = null;
			$this->_data->default_frontend_language = null;
			$this->_data->default_processor_types   = null;
		}

		if ($this->_data) {
			$this->_data->currencies        = array();
			$query                          = ' SELECT * FROM #__jbusinessdirectory_currencies';
			$this->_data->currencies        = $this->_getList($query);
			$this->_data->countries         = array();
			$query                          = ' SELECT * FROM #__jbusinessdirectory_countries order by country_name';
			$this->_data->countries         = $this->_getList($query);
			$this->_data->dateFormats       = array();
			$query                          = ' SELECT * FROM #__jbusinessdirectory_date_formats';
			$this->_data->dateFormats       = $this->_getList($query);
			$query                          = " SELECT id, name, listing_config AS config FROM #__jbusinessdirectory_default_attributes WHERE listing_config != '-1' ";
			$this->_data->defaultAtrributes = $this->_getList($query);
			$query                          = " SELECT id, name, offer_config AS config FROM #__jbusinessdirectory_default_attributes WHERE offer_config != '-1' ";
			$this->_data->offerAtrributes   = $this->_getList($query);
			$query                          = " SELECT id, name, event_config AS config FROM #__jbusinessdirectory_default_attributes WHERE event_config != '-1' ";
			$this->_data->eventAtrributes   = $this->_getList($query);
			$this->_data->defaultProcessors = JBusinessUtil::getDefaultPaymentProcessors();
		}

		$this->_data->languages = glob(JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . '*', GLOB_ONLYDIR);

		if (!empty($this->_data->country_ids)) {
			$this->_data->country_ids = explode(",", $this->_data->country_ids);
		}

		if (!empty($this->_data->default_processor_types)) {
			$this->_data->default_processor_types = explode(",", $this->_data->default_processor_types);
		}

		if (!empty($this->_data->search_fields)) {
			$this->_data->search_fields = explode(",", $this->_data->search_fields);
		}

		if (!empty($this->_data->search_categories)) {
			$this->_data->search_categories = explode(",", $this->_data->search_categories);
		}

		if (!empty($this->_data->available_languages)){
			$this->_data->available_languages = explode(",", $this->_data->available_languages);
		}		
		
		if (empty($this->_data->dir_list_limit )) {
			$this->_data->dir_list_limit  = 20;
		}
		
		if (!empty($this->_data->type_allowed_registering)) {
			$this->_data->type_allowed_registering = explode(",", $this->_data->type_allowed_registering);
		}

		if (!empty($this->_data->search_filter_fields)) {
			$this->_data->search_filter_fields = explode(",", $this->_data->search_filter_fields);
		}

		if (!empty($this->_data->order_by_fields)) {
			$this->_data->order_by_fields = explode(",", $this->_data->order_by_fields);
		}

		if (!empty($this->_data->offer_search_filter_fields)) {
			$this->_data->offer_search_filter_fields = explode(",", $this->_data->offer_search_filter_fields);
		}

		if (!empty($this->_data->event_search_filter_fields)) {
			$this->_data->event_search_filter_fields = explode(",", $this->_data->event_search_filter_fields);
		}

		if (!empty($this->_data->quotes_search_filter_fields)) {
			$this->_data->quotes_search_filter_fields = explode(",", $this->_data->quotes_search_filter_fields);
		}

		if (!empty($this->_data->simple_form_fields)) {
			$this->_data->simple_form_fields = explode(",", $this->_data->simple_form_fields);
		}

		if (!empty($this->_data->url_fields)) {
			$this->_data->url_fields = explode(",", $this->_data->url_fields);
		}

		if (!empty($this->_data->expiration_day_notice)) {
			$this->_data->expiration_day_notice = explode(",", $this->_data->expiration_day_notice);
		} else {
			$this->_data->expiration_day_notice = array();
		}

		if (!empty($this->_data->marker_size)) {
			$size = explode(';', $this->_data->marker_size);

			if (!empty($size)) {
				$tmp = new stdClass();
				$tmp->width  = $size[0];
				$tmp->height = $size[1];

				$this->_data->marker_size = $tmp;
			}
		}

		$this->_data->categoryViews    = array();
		$this->_data->categoryViews[1] = 'LNG_STYLE_1';
		$this->_data->categoryViews[2] = 'LNG_STYLE_2';
		$this->_data->categoryViews[3] = 'LNG_STYLE_3';
		$this->_data->categoryViews[4] = 'LNG_STYLE_4';
		$this->_data->categoryViews[5] = 'LNG_STYLE_5';

		$this->_data->addressFormats    = array();
		$this->_data->addressFormats[1] = 'LNG_AMERICAN';
		$this->_data->addressFormats[2] = 'LNG_EUROPEAN';
		$this->_data->addressFormats[3] = 'LNG_AMERICAN2';
		$this->_data->addressFormats[4] = 'LNG_EUROPEAN2';
		$this->_data->addressFormats[5] = 'LNG_AMERICAN3';
		$this->_data->addressFormats[6] = 'LNG_EUROPEAN3';
		$this->_data->addressFormats[7] = 'LNG_JAPANESE';
		$this->_data->addressFormats[8] = 'LNG_CUSTOM_ADDRESS';


		$this->_data->searchResultViews    = array();
		$this->_data->searchResultViews[1] = 'LNG_STYLE_1';
		$this->_data->searchResultViews[2] = 'LNG_STYLE_2';
		$this->_data->searchResultViews[3] = 'LNG_STYLE_3';
		$this->_data->searchResultViews[4] = 'LNG_STYLE_4';
		$this->_data->searchResultViews[5] = 'LNG_STYLE_5';
		$this->_data->searchResultViews[6] = 'LNG_STYLE_6';
		$this->_data->searchResultViews[7] = 'LNG_STYLE_7';
		$this->_data->searchResultViews[8] = 'LNG_STYLE_8';
		//$this->_data->searchResultViews[9] = 'LNG_STYLE_9';

		$this->_data->eventSearchResultViews = array();
		$this->_data->eventSearchResultViews[1] = 'LNG_STYLE_1';
		$this->_data->eventSearchResultViews[2] = 'LNG_STYLE_2';
		$this->_data->eventSearchResultViews[3] = 'LNG_STYLE_3';

		$this->_data->orderSearchListings                      = array();
		//$this->_data->orderSearchListings['viewCount desc'] = 'LNG_MOST_POPULAR';
		$this->_data->orderSearchListings['packageOrder desc'] = 'LNG_RELEVANCE';
		$this->_data->orderSearchListings['id desc']           = 'LNG_LAST_ADDED';
		$this->_data->orderSearchListings['id asc']            = 'LNG_FIRST_ADDED';
		$this->_data->orderSearchListings['companyName asc']   = 'A-Z';
		$this->_data->orderSearchListings['companyName desc']  = 'Z-A';
		$this->_data->orderSearchListings['city asc']          = 'LNG_CITY';
		$this->_data->orderSearchListings['review_score desc'] = 'LNG_RATING';
		$this->_data->orderSearchListings['distance asc']      = 'LNG_DISTANCE';
		//$this->_data->orderSearchListings['nr_reviews desc']      = 'LNG_REVIEW_COUNT';
		$this->_data->orderSearchListings['ordering asc']      = 'LNG_DEFAULT';

		$this->_data->secondOrderSearchListings                      = array();
		$this->_data->secondOrderSearchListings['']    				 = 'LNG_NONE';
		$this->_data->secondOrderSearchListings['viewCount desc']    = 'LNG_MOST_POPULAR';
		$this->_data->secondOrderSearchListings['id desc']           = 'LNG_LAST_ADDED';
		$this->_data->secondOrderSearchListings['id asc']            = 'LNG_FIRST_ADDED';
		$this->_data->secondOrderSearchListings['companyName asc']   = 'A-Z';
		$this->_data->secondOrderSearchListings['companyName desc']  = 'Z-A';
		$this->_data->secondOrderSearchListings['city asc']          = 'LNG_CITY';
		$this->_data->secondOrderSearchListings['review_score desc'] = 'LNG_RATING';
		$this->_data->secondOrderSearchListings['distance asc']      = 'LNG_DISTANCE';
		$this->_data->secondOrderSearchListings['ordering asc']      = 'LNG_DEFAULT';

		$this->_data->companyViews    = array();
		$this->_data->companyViews[1] = 'LNG_STYLE_1';
		$this->_data->companyViews[2] = 'LNG_STYLE_2';
		$this->_data->companyViews[3] = 'LNG_STYLE_3';
		$this->_data->companyViews[4] = 'LNG_STYLE_4';
		$this->_data->companyViews[5] = 'LNG_STYLE_5';
		$this->_data->companyViews[6] = 'LNG_STYLE_6';
		$this->_data->companyViews[7] = 'LNG_STYLE_7';
		$this->_data->companyViews[8] = 'LNG_STYLE_8';
		//$this->_data->companyViews[9] = 'LNG_STYLE_9';

		$this->_data->orderSearchOffers                      = array();
		$this->_data->orderSearchOffers['']                  = 'LNG_RELEVANCE';
		$this->_data->orderSearchOffers['co.subject']        = 'LNG_NAME';
		$this->_data->orderSearchOffers['co.city']           = 'LNG_CITY';
		$this->_data->orderSearchOffers['co.id desc']        = 'LNG_LAST_ADDED';
		$this->_data->orderSearchOffers['co.id asc']         = 'LNG_FIRST_ADDED';
		$this->_data->orderSearchOffers['co.startDate asc']  = 'LNG_EARLIEST_DATE';
		$this->_data->orderSearchOffers['co.startDate desc'] = 'LNG_LATEST_DATE';
		$this->_data->orderSearchOffers['distance asc']      = 'LNG_DISTANCE';

		$this->_data->orderSearchEvents                    = array();
		$this->_data->orderSearchEvents['']                = 'LNG_RELEVANCE';
		$this->_data->orderSearchEvents['name']            = 'LNG_NAME';
		$this->_data->orderSearchEvents['city']            = 'LNG_CITY';
		$this->_data->orderSearchEvents['id desc']         = 'LNG_LAST_ADDED';
		$this->_data->orderSearchEvents['id asc']          = 'LNG_FIRST_ADDED';
		$this->_data->orderSearchEvents['start_date asc']  = 'LNG_EARLIEST_DATE';
		$this->_data->orderSearchEvents['start_date desc'] = 'LNG_LATEST_DATE';
		$this->_data->orderSearchEvents['distance asc']    = 'LNG_DISTANCE';


		$this->_data->simpleFormFields = array();
		$this->_data->simpleFormFields['type']  = 'LNG_TYPE';
		$this->_data->simpleFormFields['website'] = 'LNG_WEBSITE';
		$this->_data->simpleFormFields['phone'] = 'LNG_PHONE';
		$this->_data->simpleFormFields['email'] = 'LNG_EMAIL';
		$this->_data->simpleFormFields['address'] = 'LNG_ADDRESS';
		$this->_data->simpleFormFields['logo'] = 'LNG_LOGO';
		$this->_data->simpleFormFields['cover_image'] = 'LNG_COVER_IMAGE';
		$this->_data->simpleFormFields['opening_hours'] = 'LNG_OPENING_HOURS';
		$this->_data->simpleFormFields['slogan'] = 'LNG_COMPANY_SLOGAN';
		$this->_data->simpleFormFields['short_description'] = 'LNG_SHORT_DESCRIPTION';
		$this->_data->simpleFormFields['description'] = 'LNG_DESCRIPTION';
		$this->_data->simpleFormFields['category'] = 'LNG_CATEGORIES';
		$this->_data->simpleFormFields['social'] = 'LNG_SOCIAL_NETWORKS';
		$this->_data->simpleFormFields['custom_attributes'] = 'LNG_CUSTOM_ATTRIBUTES';
		
		$this->_data->autoSaveIterval    = array();
		$this->_data->autoSaveIterval[1] = '60000';
		$this->_data->autoSaveIterval[2] = '120000';
		$this->_data->autoSaveIterval[3] = '180000';
		$this->_data->autoSaveIterval[4] = '240000';
		$this->_data->autoSaveIterval[5] = '300000';
		$this->_data->autoSaveIterval[6] = '360000';
		$this->_data->autoSaveIterval[7] = '420000';
		
		$this->_data->numbers = array();
		$this->_data->numbers[0] = "0";
		$this->_data->numbers[1] = "1";
		$this->_data->numbers[2] = "2";
		$this->_data->numbers[3] = "3";

		$this->_data->elasticSearchVersions                 = array();
		$this->_data->elasticSearchVersions['7']            = '7.x';

		$this->_data->vat_configuration = [];
		if (!empty($this->_data->vat_config)) {
			$this->_data->vat_configuration = json_decode($this->_data->vat_config);
		}

		return $this->_data;
	}


	public function store($data) {
		$row        = $this->getTable('ApplicationSettings');
		$jinput     = JFactory::getApplication()->input;
		$defaultLng = JBusinessUtil::getLanguageTag();

		$termConditions         = JBusinessUtil::removeRelAttribute($jinput->getString('terms_conditions_' . $defaultLng, '', 'RAW'));
		$termConditionsReview   = JBusinessUtil::removeRelAttribute($jinput->getString('reviews_terms_conditions_' . $defaultLng, '', 'RAW'));
		$termConditionsContacts = JBusinessUtil::removeRelAttribute($jinput->getString('contact_terms_conditions_' . $defaultLng, '', 'RAW'));
		$contentResponsible     = JBusinessUtil::removeRelAttribute($jinput->getString('content_responsible_' . $defaultLng, '', 'RAW'));
		$privacyPolicy          = JBusinessUtil::removeRelAttribute($jinput->getString('privacy_policy_' . $defaultLng, '', 'RAW'));

		foreach($this->dataToBeCasted as $k=>$v){
			if(isset($data[$v])){
				$data[$v] = (int)$data[$v];
			}
		}
		if (empty($data["available_languages"])) {
		    $data["available_languages"] = "";
		} else {
		    $data["available_languages"] = implode(",", $data["available_languages"]);
		}
		if (empty($data["terms_conditions"]) && !empty($termConditions)) {
			$data["terms_conditions"] = $termConditions;
		}

		if (empty($data["reviews_terms_conditions"]) && !empty($termConditionsReview)) {
			$data["reviews_terms_conditions"] = $termConditionsReview;
		}

		if (empty($data["contact_terms_conditions"]) && !empty($termConditionsContacts)) {
			$data["contact_terms_conditions"] = $termConditionsContacts;
		}

		if (empty($data["content_responsible"]) && !empty($contentResponsible)) {
			$data["content_responsible"] = $contentResponsible;
		}

		if(isset($data["content_responsible"])){
			$data["content_responsible"] = trim($data["content_responsible"]);
		}

		if (empty($data["privacy_policy"]) && !empty($privacyPolicy)) {
			$data["privacy_policy"] = $privacyPolicy;
		}

		$termsConditionsArticleId = $jinput->get('terms_conditions_article_id_'.$defaultLng, '', 'RAW');
		if ((!empty($termsConditionsArticleId) && empty($data["terms_conditions_article_id"]))) {
			$data["terms_conditions_article_id"] = $termsConditionsArticleId;
		}

		$reviewsTermsConditionsArticleId = $jinput->get('reviews_terms_conditions_article_id_'.$defaultLng, '', 'RAW');
		if ((!empty($reviewsTermsConditionsArticleId) && empty($data["reviews_terms_conditions_article_id"]))) {
			$data["reviews_terms_conditions_article_id"] = $reviewsTermsConditionsArticleId;
		}

		$contactTermsConditionsArticleId = $jinput->get('contact_terms_conditions_article_id_'.$defaultLng, '', 'RAW');
		if ((!empty($contactTermsConditionsArticleId) && empty($data["contact_terms_conditions_article_id"]))) {
			$data["contact_terms_conditions_article_id"] = $contactTermsConditionsArticleId;
		}

		$privacyPolicyArticleId = $jinput->get('privacy_policy_article_id_'.$defaultLng, '', 'RAW');
		if ((!empty($privacyPolicyArticleId) && empty($data["privacy_policy_article_id"]))) {
			$data["privacy_policy_article_id"] = $privacyPolicyArticleId;
		}

		if (!empty($data["captcha"])) {
			$config = JFactory::getConfig();
			$captcha = $config->get('captcha');
			if (empty($captcha)) {
				$data["captcha"] = 0;
			}
		}

		$this->assignPackageToCompanies($data["package"]);

		if (empty($data["category_url_naming"])) {
			$data["category_url_naming"] = "category";
		}

		if (empty($data["offer_category_url_naming"])) {
			$data["offer_category_url_naming"] = "offer-category";
		}
		
		if (empty($data["event_category_url_naming"])) {
			$data["event_category_url_naming"] = "event-category";
		}
		
		if (empty($data["offer_url_naming"])) {
			$data["offer_url_naming"] = "offer";
		}
		
		if (empty($data["event_url_naming"])) {
			$data["event_url_naming"] = "event";
		}
		
		if (empty($data["city_url_naming"])) {
			$data["city_url_naming"] = "city";
		}
		
		if (empty($data["region_url_naming"])) {
			$data["region_url_naming"] = "region";
		}
		
		if (empty($data["conference_url_naming"])) {
			$data["conference_url_naming"] = "conference";
		}
		
		if (empty($data["conference_session_url_naming"])) {
			$data["conference_session_url_naming"] = "session";
		}
		
		if (empty($data["speaker_url_naming"])) {
			$data["speaker_url_naming"] = "speaker";
		}
		
		if (empty($data["video_url_naming"])) {
			$data["video_url_naming"] = "video";
		}
		
		if (empty($data["videos_url_naming"])) {
			$data["videos_url_naming"] = "videos";
		}

		if (empty($data["trips_url_naming"])) {
			$data["trips_url_naming"] = "trips";
		}

		if (empty($data["trip_url_naming"])) {
			$data["trip_url_naming"] = "trip";
		}

		if (!empty($data["package_date"])) {
			$data["package_date"] = JBusinessUtil::convertToMysqlFormat($data["package_date"]);
		}
		
		if (!empty($data["country_ids"])) {
			$data["country_ids"] = implode(",", $data["country_ids"]);
		} else {
			$data["country_ids"] = '';
		}
		if (empty($data["search_fields"])) {
			$data["search_fields"] = "";
		} else {
			$data["search_fields"] = implode(",", $data["search_fields"]);
		}

		if (empty($data["search_categories"])) {
			$data["search_categories"] = "";
		} else {
			$data["search_categories"] = implode(",", $data["search_categories"]);
		}

		if (empty($data["type_allowed_registering"])) {
			$data["type_allowed_registering"] = "";
		} else {
			$data["type_allowed_registering"] = array_filter($data["type_allowed_registering"]);
			$data["type_allowed_registering"] = implode(",", $data["type_allowed_registering"]);
		}

		if (empty($data["search_filter_fields"])) {
			$data["search_filter_fields"] = "";
		} else {
			$data["search_filter_fields"] = implode(",", $data["search_filter_fields"]);
		}

		if (empty($data["offer_search_filter_fields"])) {
			$data["offer_search_filter_fields"] = "";
		} else {
			$data["offer_search_filter_fields"] = implode(",", $data["offer_search_filter_fields"]);
		}

		if (empty($data["event_search_filter_fields"])) {
			$data["event_search_filter_fields"] = "";
		} else {
			$data["event_search_filter_fields"] = implode(",", $data["event_search_filter_fields"]);
		}

		if (empty($data["quotes_search_filter_fields"])) {
			$data["quotes_search_filter_fields"] = "";
		} else {
			$data["quotes_search_filter_fields"] = implode(",", $data["quotes_search_filter_fields"]);
		}

		if (empty($data["order_by_fields"])) {
			$data["order_by_fields"] = "companyName_asc";
		}
		
		if (empty($data["expiration_day_notice"])) {
			$data["expiration_day_notice"] = "";
		} else {
			$data["expiration_day_notice"] = implode(",", $data["expiration_day_notice"]);
		}
		
		if (empty($data["url_fields"])) {
			$data["url_fields"] = "";
		} else {
			$data["url_fields"] = implode(",", $data["url_fields"]);
		}

		if (empty($data["custom_address"])) {
			$data["custom_address"] = ADDRESS_STREET_NUMBER . " " . ADDRESS_ADDRESS . "," . ADDRESS_AREA . "," . ADDRESS_CITY . " " . ADDRESS_POSTAL_CODE . "," . ADDRESS_REGION . "," . ADDRESS_PROVINCE . "," . ADDRESS_COUNTRY;
		}

		$usedFiles = array();
		if (!empty($data['map_marker'])) {
			array_push($usedFiles, $data['map_marker']);
		}

		if (!empty($data['feature_map_marker'])) {
			array_push($usedFiles, $data['feature_map_marker']);
		}

		if (!empty($data['logo'])) {
			array_push($usedFiles, $data['logo']);
		}

		if (!empty($data["default_processor_types"])) {
			$data["default_processor_types"] = implode(",", $data["default_processor_types"]);
		} else {
			$data["default_processor_types"] = '';
		}

		if ($data['max_description_length'] > 10000) {
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_MAX_DESCRIPTION_LENGTH_EXCEED'), 'warning');
			$data['max_description_length'] = 10000;
		}
		
		if ($data['max_short_description_length'] > 500) {
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_MAX_SHORT_DESCRIPTION_LENGTH_EXCEED'), 'warning');
			$data['max_short_description_length'] = 500;
		}

		if ($data['max_slogan_length'] > 500) {
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_MAX_SLOGAN_LENGTH_EXCEED'), 'warning');
			$data['max_slogan_length'] = 500;
		}

		if (!empty($data["marker_size_width"]) || !empty($data["marker_size_height"])) {
			$width = $data["marker_size_width"];
			$height = $data["marker_size_height"];

			$data["marker_size"] = !empty($width) ? $width : $height;
			$data["marker_size"] .= ';';
			$data["marker_size"] .= !empty($height) ? $height : $width;
		} else {
			$data["marker_size"] = "";
		}
		
		$pictures_path     = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH);
		$app_pictures_path = JBusinessUtil::makePathFile(BD_PICTURES_PATH);
		JBusinessUtil::removeUnusedFiles($usedFiles, $pictures_path, $app_pictures_path);

		$data['autocomplete_config'] = $this->formatAutocompleteConfiguration($data);
		$data['vat_config'] = $this->formatVatConfiguration($data);

		$data['enable_activity_cities'] = 0;

		$row->updateAppsettings($data);

		$this->storeAttributeConfiguration($data);
		$this->saveCustomCss();

		$appSettings = JBusinessUtil::getApplicationSettings();

		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::saveTranslations(TERMS_CONDITIONS_TRANSLATION, $data['applicationsettings_id'], 'terms_conditions_');
			JBusinessDirectoryTranslations::saveTranslations(TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $data['applicationsettings_id'], 'terms_conditions_article_id_');
			JBusinessDirectoryTranslations::saveTranslations(REVIEWS_TERMS_CONDITIONS_TRANSLATION, $data['applicationsettings_id'], 'reviews_terms_conditions_');
			JBusinessDirectoryTranslations::saveTranslations(REVIEWS_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $data['applicationsettings_id'], 'reviews_terms_conditions_article_id_');
			JBusinessDirectoryTranslations::saveTranslations(CONTACT_TERMS_CONDITIONS_TRANSLATION, $data['applicationsettings_id'], 'contact_terms_conditions_');
			JBusinessDirectoryTranslations::saveTranslations(CONTACT_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $data['applicationsettings_id'], 'contact_terms_conditions_article_id_');
			JBusinessDirectoryTranslations::saveTranslations(RESPONSIBLE_CONTENT_TRANSLATION, $data['applicationsettings_id'], 'content_responsible_');
			JBusinessDirectoryTranslations::saveTranslations(PRIVACY_POLICY_TRANSLATION, $data['applicationsettings_id'], 'privacy_policy_');
			JBusinessDirectoryTranslations::saveTranslations(PRIVACY_POLICY_ARTICLE_ID_TRANSLATION, $data['applicationsettings_id'], 'privacy_policy_article_id_');
		}
		return true;
	}

	public function getLanguages($pathOnInstall = null) {
		if (!empty($pathOnInstall)) {
			$adminPath = $pathOnInstall;
		} else {
			$adminPath = JPATH_COMPONENT_ADMINISTRATOR;
		}
		
		$path = LanguageHelper::getLanguagePath($adminPath);
		$lngs = JBusinessUtil::getLanguages();
		$lngs = array_flip($lngs);

		$dirs = JFolder::folders($path);
		foreach ($dirs as $dir) {
			if (strlen($dir) != 5) {
				continue;
			}
			$iniFiles = JFolder::files($path . DS . $dir, '.ini', false, false);
			$iniFiles = reset($iniFiles);
			if (empty($iniFiles)) {
				continue;
			}
			$fileName              = basename($iniFiles);
			$oneLanguage           = new stdClass();
			$oneLanguage->language = $dir;
			$oneLanguage->name     = substr($fileName, 0, 5);

			if (isset($lngs[$oneLanguage->name])) {
				$oneLanguage->name = $lngs[$oneLanguage->name];
			}

			$languages[] = $oneLanguage;
		}
		return $languages;
	}

	public function storeAttributeConfiguration($data) {
		$db = $this->getDbo();

		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$value = $db->escape($value);
			}
			if (strpos($key, "attribute-", 0) === 0) {
				$obj = new stdClass();
				if (strpos($key, "attribute-listing-", 0) === 0) {
					$obj->id             = substr($key, 18);
					$obj->listing_config = $value;
				} elseif (strpos($key, "attribute-offer-", 0) === 0) {
					$obj->id           = substr($key, 16);
					$obj->offer_config = $value;
				} elseif (strpos($key, "attribute-event-", 0) === 0) {
					$obj->id           = substr($key, 16);
					$obj->event_config = $value;
				}
				$table = $this->getTable("DefaultAttributes");
				// Bind the form fields to the table
				if (!$table->bind($obj)) {
					$this->setError($table->getError());
					return false;
				}
				// Make sure the record is valid
				if (!$table->check()) {
					$this->setError($table->getError());
					return false;
				}
				// Store the web link table to the database
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}
			}
		}
		return true;
	}

	public function assignPackageToCompanies($packageId) {
		if ($packageId > 0) {
			$packageTable = JTable::getInstance("Package", "JTable");
			$packageTable->updateUnassignedCompanies($packageId);
		}
	}

	public function getForm($data = array(), $loadData = true) {
	}

	/**
	 *  function that retrives all category order options possible
	 *
	 * @since 4.9.0
	 */
	public static function getCategoryOrderOptions() {
		$types       = array();
		$type        = new stdClass();
		$type->value = ORDER_ALPHABETICALLY;
		$type->text  = JTEXT::_("LNG_ALPHABETICALLY");
		$types[]     = $type;
		$type        = new stdClass();
		$type->value = ORDER_BY_ORDER;
		$type->text  = JTEXT::_("LNG_BY_ORDER");
		$types[]     = $type;

		return $types;
	}

	public static function getCitiesRegionsOrderOptions() {
		$types       = array();
		$type        = new stdClass();
		$type->value = ORDER_ALPHABETICALLY;
		$type->text  = JTEXT::_("LNG_ALPHABETICALLY");
		$types[]     = $type;
		
		$type        = new stdClass();
		$type->value = ORDER_BY_ORDER;
		$type->text  = JTEXT::_("LNG_BY_ORDER");
		$types[]     = $type;

		return $types;
	}

	/**
	 * Get all the fields available for searching in business listings
	 */
	public function getSearchFields() {
		$fields       = array();
		
		$field        = new stdClass();
		$field->name  = JText::_('LNG_COMPANY_NAME');
		$field->value = 'cp.name';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_COMPANY_SLOGAN');
		$field->value = 'cp.slogan';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_CATEGORY_NAME');
		$field->value = 'cg.name';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_META_DESCRIPTION');
		$field->value = 'cp.meta_description';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_SHORT_DESCRIPTION');
		$field->value = 'cp.short_description';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_DESCRIPTION');
		$field->value = 'cp.description';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_KEYWORDS');
		$field->value = 'cp.keywords';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_CUSTOM_TAB');
		$field->value = 'cp.custom_tab_content';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_PHONE');
		$field->value = 'cp.phone';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_ADDRESS');
		$field->value = 'cp.address';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_POSTAL_CODE');
		$field->value = 'cp.postalCode';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_CITY');
		$field->value = 'cp.city';
		$fields[]     = $field;
		
		$field        = new stdClass();
		$field->name  = JText::_('LNG_REGION');
		$field->value = 'cp.county';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_PROVINCE');
		$field->value = 'cp.province';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_TRANSLATION_NAME');
		$field->value = 't.name';
		$fields[]     = $field;
		

		return $fields;
	}

	/**
	 * Get all the fields available for categories in business listings search filter
	 */
	public function getSearchFilterFields() {
		$fields       = array();

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CATEGORIES');
		$field->value = 'categories';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_TYPES');
		$field->value = 'types';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_COUNTRIES');
		$field->value = 'countries';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_REGIONS');
		$field->value = 'regions';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CITIES');
		$field->value = 'cities';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_AREA');
		$field->value = 'area';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_STAR_RATING');
		$field->value = 'starRating';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_PROVINCE');
		$field->value = 'province';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_MEMBERSHIPS');
		$field->value = 'memberships';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_PACKAGES');
		$field->value = 'packages';
		$fields[]     = $field;

		$table = $this->getTable("Attribute","JTable");
		$attributes = $table->getSearchAttributeValues();

		if(!empty($attributes)){
			foreach($attributes as $attr){
				$field        = new stdClass();
				$field->name  = $attr->name;
				$field->value = $attr->id;
				$fields[]     = $field;
			}
		}


		return $fields;
	}

	/**
	 * Get all the fields available for categories in business listings search filter
	 */
	public function getOfferSearchFilterFields() {
		$fields       = array();

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CATEGORIES');
		$field->value = 'categories';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_TYPES');
		$field->value = 'types';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_COUNTRIES');
		$field->value = 'countries';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_REGIONS');
		$field->value = 'regions';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CITIES');
		$field->value = 'cities';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_AREA');
		$field->value = 'area';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_STAR_RATING');
		$field->value = 'starRating';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_PROVINCE');
		$field->value = 'province';
		$fields[]     = $field;

		$table = $this->getTable("Attribute","JTable");
		$attributes = $table->getSearchAttributeValues(ATTRIBUTE_TYPE_OFFER);

		if(!empty($attributes)){
			foreach($attributes as $attr){
				$field        = new stdClass();
				$field->name  = $attr->name;
				$field->value = $attr->id;
				$fields[]     = $field;
			}
		}


		return $fields;
	}

	/**
	 * Get all the fields available for categories in business listings search filter
	 */
	public function getEventSearchFilterFields() {
		$fields       = array();

		$field        = new stdClass();
        $field->name  = JText::_('LNG_MONTHS');
        $field->value = 'months';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CATEGORIES');
		$field->value = 'categories';
		$fields[]     = $field;
		
		$field        = new stdClass();
		$field->name  = JText::_('LNG_TYPES');
		$field->value = 'types';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_COUNTRIES');
		$field->value = 'countries';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_REGIONS');
		$field->value = 'regions';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CITIES');
		$field->value = 'cities';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_AREA');
		$field->value = 'area';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_STAR_RATING');
		$field->value = 'starRating';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_PROVINCE');
		$field->value = 'province';
		$fields[]     = $field;

		$table = $this->getTable("Attribute","JTable");
		$attributes = $table->getSearchAttributeValues(ATTRIBUTE_TYPE_EVENT);

		if(!empty($attributes)){
			foreach($attributes as $attr){
				$field        = new stdClass();
				$field->name  = $attr->name;
				$field->value = $attr->id;
				$fields[]     = $field;
			}
		}


		return $fields;
	}

	/**
	 * Get all the fields available for order by filter in the listings section
	 */
	public function getOrderByFields() {
		$fields       = array();
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_MOST_POPULAR");
		$field->value = 'most_popular';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_RELEVANCE");
		$field->value = 'packageOrder';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_LAST_ADDED");
		$field->value = 'id_desc';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_('LNG_FIRST_ADDED');
		$field->value = 'id_asc';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("Name A-Z");
		$field->value = 'companyName_asc';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("Name Z-A");
		$field->value = 'companyName_desc';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_CITY");
		$field->value = 'city';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_RATING");
		$field->value = 'review_score';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_DISTANCE");
		$field->value = 'distance';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_REVIEW_COUNT");
		$field->value = 'review_count';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JTEXT::_("LNG_DEFAULT");
		$field->value = 'ordering';
		$fields[]     = $field;

		return $fields;
	}

	/**
	 * Get all the fields available for url in business listings
	 */
	public function getURLFields() {
		$fields       = array();
		$field        = new stdClass();
		$field->name  = JText::_('LNG_CATEGORY');
		$field->value = 'category';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_PROVINCE');
		$field->value = 'province';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_REGION');
		$field->value = 'region';
		$fields[]     = $field;

		$field        = new stdClass();
		$field->name  = JText::_('LNG_CITY');
		$field->value = 'city';
		$fields[]     = $field;

		return $fields;
	}

	/**
	 * Removes the local cache of map tile images for the staticmaplite library of OpenStreetMaps.
	 *
	 * @since 5.1.5
	 */
	public function clearOSMCache() {
		$cacheDir = JPATH_SITE.'/components/com_jbusinessdirectory/libraries/cache';

		$result = true;
		if (file_exists($cacheDir)) {
			$result = JBusinessUtil::recursiveRemoveDirectory($cacheDir);
		}

		return $result;
	}

	public function clearDemoData() {
		$table = $this->getTable('ApplicationSettings');
		$result = $table->deleteDemoData();

		return $result;
	}

	/**
	 * Send an email to all customers that have not activated the subscriptions
	 *
	 * @since 5.3.0
	 */
	public function sendPayamentEmailNotificationsAjax() {
		$table = JTable::getInstance('Company', 'JTable');
		$companies = $table->getNonActiveSubscriptionsCompanies();
		$result = null;

		foreach ($companies as $company) {
			$result = EmailService::sendPayamentEmailNotificationEmail($company);
		}
	   
		return true;
	}
	
	
	/**
	 * Loads the application settings with the translated values for the text and descriptions.
	 *
	 * @since 5.2.0
	 */
	private function loadAppSettings() {
		$table = $this->getTable('ApplicationSettings');
		$appSettings = $table->getApplicationSettings();
		
		foreach ($appSettings as $setting) {
			$setting->text = JText::_($setting->text);
			$setting->description = JText::_($setting->description);
		}
		
		self::$appSettings = $appSettings;
	}
	
	/**
	 * Searches the application settings with a given keyword (keywords) and filters the results
	 *
	 * @param $keyword string search keyword
	 *
	 * @return array
	 * @since 5.2.0
	 */
	public function searchSetting($keyword) {
		$keywords = explode(' ', $keyword);
		
		if (empty(self::$appSettings)) {
			$this->loadAppSettings();
		}
		
		$results = array_filter(self::$appSettings, function ($val, $key) use ($keywords) {
			$result = true;
			foreach ($keywords as $keyword) {
				if (!empty($keyword)) {
					$result = $result && (stripos($val->text, $keyword) || stristr($val->description, $keyword));
				}
			}
			
			return $result;
		}, ARRAY_FILTER_USE_BOTH);
			
		return $results;
	}

	/**
	 * Formats the incoming data for the map autocomplete configuration into the appropriate json structure.
	 *
	 * @param array $data
	 * @return string json
	 *
	 * @since 5.5.0
	 */
	private function formatAutocompleteConfiguration($data) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$config = $appSettings->autocomplete_config;
		$config = json_decode($config, true);

		// get default config as fallback in case fields are left empty
		$defaultConfig = $this->getDefaultAutocompleteConfiguration();

		$newConfig = [];
		foreach ($config as $map => $fields) {
			$newConfig[$map] = [];

			foreach ($fields as $field => $mappedFields) {
				if (isset($data['config-'.$map.'-'.$field])) {
					$newConfig[$map][$field] = $data['config-'.$map.'-'.$field];
				} else {
					$newConfig[$map][$field] = $defaultConfig[$map][$field];
				}
			}
		}

		$newConfig = json_encode($newConfig);

		return $newConfig;
	}


	/**
	 * Formats the vat configuration data input into the correct JSON format
	 *
	 * @param $data array
	 *
	 * @return null|string
	 *
	 * @since 5.5.0
	 */
	public function formatVatConfiguration($data) {
		if (!isset($data['vat_config_country'])) {
			return null;
		}

		$vat_config = [];

		foreach ($data['vat_config_country'] as $key => $countryId) {
			$tmp = new stdClass();
			$tmp->country_id = $countryId;
			$tmp->value = $data['vat_config_value'][$key];

			$vat_config[] = $tmp;
		}

		$result = json_encode($vat_config);
		return $result;
	}

	/**
	 * Gets the map autocomplete configuration json settings and decodes them into a php array
	 *
	 * @return array
	 *
	 * @since 5.5.0
	 */
	public function getAutocompleteConfig() {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$config = $appSettings->autocomplete_config;
		$config = json_decode($config, true);

		return $config;
	}

	/**
	 * Creates and returns an array of all the fields that are expected in the autcomplete suggestion response
	 * from each map type.
	 *
	 * @return array
	 *
	 * @since 5.5.0
	 */
	public function getAutocompleteConfigOptions() {
		$config = [];

		$config['google'] = [];
		$config['google'][] = "premise";
		$config['google'][] = "postal_code";
		$config['google'][] = "street_number";
		$config['google'][] = "route";
		$config['google'][] = "locality";
		$config['google'][] = "country";
		$config['google'][] = "administrative_area_level_1";
		$config['google'][] = "administrative_area_level_2";
		$config['google'][] = "sublocality_level_1";
		$config['google'][] = "sublocality_level_2";
		$config['google'][] = "sublocality_level_3";
		$config['google'][] = "sublocality_level_4";

		$config['bing'] = [];
		$config['bing'][] = "postalCode";
		$config['bing'][] = "addressLine";
		$config['bing'][] = "street_number";
		$config['bing'][] = "city";
		$config['bing'][] = "locality";
		$config['bing'][] = "adminDistrict";
		$config['bing'][] = "district";
		$config['bing'][] = "countryRegion";
		$config['bing'][] = "country_code";

		$config['openstreet'] = [];
		$config['openstreet'][] = "street";
		$config['openstreet'][] = "road";
		$config['openstreet'][] = "street_number";
		$config['openstreet'][] = "house_number";
		$config['openstreet'][] = "neighbourhood";
		$config['openstreet'][] = "municipality";
		$config['openstreet'][] = "suburb";
		$config['openstreet'][] = "city";
		$config['openstreet'][] = "village";
		$config['openstreet'][] = "town";
		$config['openstreet'][] = "postcode";
		$config['openstreet'][] = "county";
		$config['openstreet'][] = "state";
		$config['openstreet'][] = "country";
		$config['openstreet'][] = "country_code";

		return $config;
	}

	/**
	 * Returns the initial default autocomplete configuration
	 *
	 * @return mixed|string
	 *
	 * @since 5.5.0
	 */
	public function getDefaultAutocompleteConfiguration() {
		$config = '{"google":{"street_number":["street_number"],"route":["route"],"locality":["locality","administrative_area_level_1"],"area_id":["administrative_area_level_2"],"administrative_area_level_1":["administrative_area_level_1"],"administrative_area_level_2":["administrative_area_level_2"],"premise":["premise"],"sublocality_level_2":["sublocality_level_2"],"sublocality_level_3":["sublocality_level_3"],"sublocality_level_4":["sublocality_level_4"],"country":["country"],"postal_code":["postal_code"]},"bing":{"street_number":["street_number"],"route":["addressLine"],"locality":["city"],"area_id":["district"],"administrative_area_level_1":["adminDistrict"],"administrative_area_level_2":["district"],"country":["countryRegion"],"postal_code":["postalCode"]},"openstreet":{"street_number":["street_number","house_number"],"route":["street","road","suburb"],"locality":["city","town"],"area_id":["county"],"administrative_area_level_1":["county"],"administrative_area_level_2":["state"],"country":["country"],"postal_code":["postcode"]}}';
		$config = json_decode($config, true);

		return $config;
	}


	/**
	 * Create a custom css file
	 *
	 * @return unknown
	 */
	public function createCustomCss() {
		$jinput     = JFactory::getApplication()->input;
		$content = $jinput->getString('css-content');
		$content = JBusinessUtil::make_safe_for_utf8_use($content);

		if (!empty($content)) {
			$path = JPATH_COMPONENT_SITE .DS.'assets'.DS. 'css' . DS . 'custom.css';

			JFile::write($path, $content);

			$msg = JText::_('LNG_CSS_FILE_SUCCESSFULLY_SAVED', true);
		}
		return $msg;
	}

	public function getCssFile() {
		$app =JFactory::getApplication();

		$file = new stdClass();
		$file->name = 'custom.css';
		$path = JPATH_COMPONENT_SITE .DS.'assets'.DS. 'css' . DS . 'custom.css';
		$file->path = $path;

		jimport('joomla.filesystem.file');		
		if (JFile::exists($path)) {
			$file->content = file_get_contents($path);
		}

		return $file;
	}

	/**
	 * Save the css file together with the custom content
	 *
	 * @param array $onInstallOptions
	 * @return void|unknown
	 */
	public function saveCustomCss($onInstallOptions = array()) {
		$app = JFactory::getApplication();

		if (count($onInstallOptions) > 0) {
			$content = $onInstallOptions ["css-content"];
		} else {
			$content = $_POST['css-content'];
		}

		$path = JPATH_COMPONENT_SITE .DS.'assets'.DS. 'css' . DS . 'custom.css';

		if (file_exists($path)) {
			if (! empty($content)) {
				$content = JBusinessUtil::make_safe_for_utf8_use($content);
				JFile::write($path, $content);
				$msg = JText::_('LNG_CSS_SUCCESSFULLY_SAVED', true);
			} else {
				$empty = ' ';
				JFile::write($path, $empty);
				$msg = JText::_('LNG_CSS_SUCCESSFULLY_SAVED', true);
			}
		} else {
			$this->createCustomCss();
			$msg = JText::_('LNG_CSS_SUCCESSFULLY_SAVED', true);
		}

		return $msg;
	}

	/**
	 * Get all the fields available for categories in quote requests search filter
	 */
	public function getQuotesFilterFields() {
		$fields       = array();
		$field        = new stdClass();
		$field->name  = JText::_('LNG_CATEGORIES');
		$field->value = 'categories';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_DATES');
		$field->value = 'dates';
		$fields[]     = $field;
		$field        = new stdClass();
		$field->name  = JText::_('LNG_COMPANIES');
		$field->value = 'companies';
		$fields[]     = $field;

		return $fields;
	}

	/**
	 * Get listings display info 
	 */
	public function getListingsDisplayInfo() {
		$items       = array();
		$item        = new stdClass();
		$item->name  = JText::_('LNG_OPENING_HOURS');
		$item->value = OPENING_HOURS;
		$items[]     = $item;
		$item        = new stdClass();
		$item->name  = JText::_('LNG_MEMBERSHIPS');
		$item->value = MEMBERSHIPS;
		$items[]     = $item;
		$item        = new stdClass();
		$item->name  = JText::_('LNG_SOCIAL_NETWORKS');
		$item->value = SOCIAL_NETWORKS;
		$items[]     = $item;

		return $items;
	}
}
