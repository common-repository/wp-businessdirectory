<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

/**
 * BusinessDirectory component helper.
 *
 * @package    WPBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryHelper {
	/**
	 * Defines the valid request variables for the reverse lookup.
	 */
	protected static $_filter = array('option', 'view', 'layout');

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	The name of the active view.
	 */
	public static function addSubmenu($vName) {
		return;
		
		JSubMenuHelper::addEntry(
			'<i class="icon-home"></i>'.JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SETTINGS'),
			'index.php?option=com_jbusinessdirectory&view=applicationsettings',
			$vName == 'applicationsettings'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CATEGORIES'),
			'index.php?option=com_jbusinessdirectory&view=categories',
			$vName == 'categories'
		);
				
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
			'index.php?option=com_jbusinessdirectory&view=companies',
			$vName == 'companies'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ATTRIBUTES'),
			'index.php?option=com_jbusinessdirectory&view=attributes',
			$vName == 'attributes'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_TYPES'),
			'index.php?option=com_jbusinessdirectory&view=companytypes',
			$vName == 'companytypes'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_PROJECTS'),
			'index.php?option=com_jbusinessdirectory&view=projects',
			$vName == 'projects'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
			'index.php?option=com_jbusinessdirectory&view=offers',
			$vName == 'offers'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
			'index.php?option=com_jbusinessdirectory&view=events',
			$vName == 'events'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_TYPES'),
			'index.php?option=com_jbusinessdirectory&view=eventtypes',
			$vName == 'eventtypes'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_TYPES'),
			'index.php?option=com_jbusinessdirectory&view=offertypes',
			$vName == 'eventtypes'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PACKAGES'),
			'index.php?option=com_jbusinessdirectory&view=packages',
			$vName == 'packages'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_DISCOUNTS'),
			'index.php?option=com_jbusinessdirectory&view=discounts',
			$vName == 'discounts'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ORDERS'),
			'index.php?option=com_jbusinessdirectory&view=orders',
			$vName == 'orders'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PAYMENT_PROCESSORS'),
			'index.php?option=com_jbusinessdirectory&view=paymentprocessors',
			$vName == 'paymentprocessors'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COUNTRIES'),
			'index.php?option=com_jbusinessdirectory&view=countries',
			$vName == 'countries'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_RATING'),
			'index.php?option=com_jbusinessdirectory&view=ratings',
			$vName == 'ratings'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW'),
			'index.php?option=com_jbusinessdirectory&view=reviews',
			$vName == 'reviews'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_QUESTIONS'),
			'index.php?option=com_jbusinessdirectory&view=reviewquestions',
			$vName == 'reviewquestions'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_CRITERIAS'),
			'index.php?option=com_jbusinessdirectory&view=reviewcriterias',
			$vName == 'reviewcriterias'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_RESPONSE'),
			'index.php?option=com_jbusinessdirectory&view=reviewresponses',
			$vName == 'reviewresponses'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_ABUSE'),
			'index.php?option=com_jbusinessdirectory&view=reviewabuses',
			$vName == 'reviewabuses'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REPORTS'),
			'index.php?option=com_jbusinessdirectory&view=reports',
			$vName == 'reports'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EMAILS_TEMPLATES'),
			'index.php?option=com_jbusinessdirectory&view=emailtemplates',
			$vName == 'emailtemplates'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CONFERENCES'),
			'index.php?option=com_jbusinessdirectory&view=conferences',
			$vName == 'conferences'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SPEAKERS'),
			'index.php?option=com_jbusinessdirectory&view=speakers',
			$vName == 'speakers'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SPEAKER_TYPES'),
			'index.php?option=com_jbusinessdirectory&view=speakertypes',
			$vName == 'speakertypes'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSIONS'),
			'index.php?option=com_jbusinessdirectory&view=sessions',
			$vName == 'sessions'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_TYPES'),
			'index.php?option=com_jbusinessdirectory&view=sessiontypes',
			$vName == 'sessiontypes'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_LOCATIONS'),
			'index.php?option=com_jbusinessdirectory&view=sessionlocations',
			$vName == 'sessionlocations'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_LEVELS'),
			'index.php?option=com_jbusinessdirectory&view=sessionlevels',
			$vName == 'sessionlevels'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_UPDATE'),
			'index.php?option=com_jbusinessdirectory&view=updates',
			$vName == 'updates'
		);
	}
	
	/**
	 * Gets a list of capabilities
	 *
	 * @param   integer  The category ID.
	 * @param   integer  The article ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getCapabilities() {
	
		$capabilities = array(
			'core.admin',
	        'core.manage',
	        'core.create',
	        'core.edit',
	        'core.edit.state',
	        'core.delete',
			'directory.access.directory.management',
			'directory.access.directory.settings',
			'directory.access.listings',
			'directory.access.listing.services',
			'directory.access.listing.providers',
			'directory.access.listing.service.reservation',
			'directory.access.listing.types',
			'directory.access.listing.pricelist',
			'directory.access.articles',
			'directory.access.messages',
			'directory.access.attributes',
			'directory.access.projects',
			'directory.access.categories',
			'directory.access.events',
			'directory.access.campaigns',
			'directory.access.campaign.plans',
			'directory.access.event.appointments',
			'directory.access.event.reservation',
			'directory.access.event.tickets',
			'directory.access.event.types',
			'directory.access.offers',
			'directory.access.products',
			'directory.access.payment.config',
			'directory.access.packages',
			'directory.access.countries',
			'directory.access.shipping.methods',
			'directory.access.request.quote',
			'directory.access.request.quote.questions',
			'directory.access.reviews',
			'directory.access.review.questions',
			'directory.access.review.criterias',
			'directory.access.review.response',
			'directory.access.review.abuse',
			'directory.access.announcements',
			'directory.access.emails',
			'directory.access.taxes',
			'directory.access.marketing',
			'directory.access.reports',
			'directory.access.statistics',
			'directory.access.search.logs',
			'directory.access.memberships',
			'directory.access.paymentprocessors',
			'directory.access.listing.registrations',
			'directory.access.cities',
			'directory.access.regions',
			'directory.access.offer.orders',
			'directory.access.orders',
			'directory.access.controlpanel',
			'directory.access.bookmarks',
			'directory.access.offercoupons',
			'directory.access.offer.types',
			'directory.access.discounts',
			'directory.access.conferences',
			'directory.access.sessions',
			'directory.access.session.types',
			'directory.access.session.locations',
			'directory.access.session.levels',
			'directory.access.speakers',
			'directory.access.speaker.types',
			'directory.access.customers',
			'directory.access.videos',
			'directory.access.currencies',
			'directory.access.trips',
			'directory.access.listing.onlyuser'

		);
		
		return $capabilities;
	}


		/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 * @param   integer  The article ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions()
	{
		$result	= new JObject;
	
		$capabilities = self::getCapabilities();
		
		foreach ($capabilities as $capability){
		    $result->set($capability, current_user_can(str_replace(".", "_", $capability)));
		}
		
		return $result;
	}


	/**
	 * Get all package features
	 *
	 * @return array package features
	 */
	public static function getPackageFeatures() {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$features = array(
			FEATURED_COMPANIES => JText::_("LNG_FEATURED_COMPANY"),
			DESCRIPTION => JText::_("LNG_DESCRIPTION"),
			HTML_DESCRIPTION => JText::_("LNG_HTML_DESCRIPTION"),
			SHOW_COMPANY_LOGO => JText::_("LNG_SHOW_COMPANY_LOGO"),
			WEBSITE_ADDRESS => JText::_("LNG_WEBSITE_ADDRESS"),
			IMAGE_UPLOAD => JText::_("LNG_IMAGE_UPLOAD"),
			VIDEOS => JText::_("LNG_VIDEOS"),
			GOOGLE_MAP => JText::_("LNG_MAP"),
			CONTACT_FORM => JText::_("LNG_CONTACT_FORM"),
			COMPANY_OFFERS => JText::_("LNG_COMPANY_OFFERS"),
			COMPANY_EVENTS => JText::_("LNG_COMPANY_EVENTS"),
			SOCIAL_NETWORKS => JText::_("LNG_SOCIAL_NETWORK"),
			PHONE => JText::_("LNG_PHONE"),
			CUSTOM_TAB => JText::_("LNG_CUSTOM_TAB"),
			ATTACHMENTS => JText::_("LNG_ATTACHMENTS"),
			OPENING_HOURS => JText::_("LNG_OPENING_HOURS"),
			RELATED_COMPANIES => JText::_("LNG_RELATED_COMPANIES"),
			SECONDARY_LOCATIONS => JText::_("LNG_SECONDARY_LOCATIONS"),
			LINK_FOLLOW => JText::_("LNG_LINK_FOLLOW"),
			TESTIMONIALS => JText::_("LNG_TESTIMONIALS"),
			SERVICES_LIST=>JText::_("LNG_MENU_SERVICES_FEATURE"),
			//SEND_EMAIL_ON_CONTACT_BUSINESS => JText::_("LNG_SEND_EMAIL_ON_CONTACT_BUSINESS"),
			FEATURED_OFFERS => JText::_("LNG_FEATURED_OFFERS"),
			MEMBERSHIPS => JText::_("LNG_MEMBERSHIPS"),
			PRODUCTS => JText::_("LNG_PRODUCTS"),
			REQUEST_QUOTES_FEATURE => JText::_("LNG_REQUEST_QUOTES_FEATURE"),
			TEAM_FEATURE => JText::_("LNG_TEAM_FEATURE"),
			REVIEWS => JText::_("LNG_REVIEWS"),
			//ZIP_CODES => JText::_("LNG_ZIP_CODES"),
			AREAS_SERVED => JText::_("LNG_AREAS_SERVED"),
			SOUNDS_FEATURE => JText::_("LNG_SOUNDS"),
			PROJECTS => JText::_("LNG_PROJECTS"));
;

		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/companyservice.php')) {
			$features[COMPANY_SERVICES]=JText::_("LNG_COMPANY_SERVICES");
		}
			
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/eventappointment.php')) {
			$features[EVENT_APPOINTMENT]=JText::_("LNG_EVENT_APPOINTMENT");
		}
			
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/eventreservation.php')) {
			$features[EVENT_BOOKINGS]=JText::_("LNG_EVENT_BOOKINGS");
		}
			
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/views/event/tmpl/edit_recurring.php')) {
			$features[EVENT_RECURRING]=JText::_("LNG_EVENT_RECURRING");
		}
			
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/offerorder.php')) {
			$features[SELL_OFFERS]=JText::_("LNG_SELL_OFFERS");
		}
			
		if ($appSettings->enable_announcements) {
			$features[ANNOUNCEMENTS]=JText::_("LNG_ANNOUNCEMENTS");
		}
		
		return $features;
	}
	
	public static function getUserPackageFeatures() {

		$features = array();
		if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) {
			$features = array_merge($features, array(TRIPS => JText::_("LNG_TRIPS")));
		}
		
		return $features;
	}

	
	/**
	 * Get the default package features
	 * Remove the features that are not used in the packages
	 * @return package features
	 */
	public static function getDefaultPackageFeatures($packages) {
		$packageFeatures = array_merge(self::getPackageFeatures(), self::getUserPackageFeatures());

		if (!is_array($packages)) {
			$packages = array($packages);
		}
		
		$result = array();
		$position = 0;
		//check if the attribues are contained in at least one package. If not it will be removed.
		foreach ($packageFeatures as $key=>$value) {
			$found = false;
			foreach ($packages as $package) {
				foreach ($package->features as $feature) {
					if ($feature == $key) {
						$found = true;
					}
				}
			}
				
			if ($found) {
				$result[$key] = $value;
			}
		}
		
		$position = 4;
		$result = array_merge(array_slice($result, 0, $position), array("multiple_categories"=>JText::_("LNG_CATEGORIES")), array_slice($result, $position));
		
		return $result;
	}

	/**
	 * Get company params
	 *
	 * @return array company params
	 */
	public static function getCompanyParams() {
		$params = array("id" => "LNG_ID",
			"name" => "LNG_NAME",
			"comercialName" => "LNG_COMPANY_COMERCIAL_NAME",
			"short_description" => "LNG_SHORT_DESCRIPTION",
			"description" => "LNG_DESCRIPTION",
			"slogan" => "LNG_COMPANY_SLOGAN",
			"street_number" => "LNG_STREET_NUMBER",
			"address" => "LNG_ADDRESS",
			"city" => "LNG_CITY",
			"county" => "LNG_COUNTY",
			"countryName" => "LNG_COUNTRY",
			"website" => "LNG_WEBSITE",
			"keywords" => "LNG_KEYWORDS",
			"registrationCode" => "LNG_REGISTRATION_CODE",
			"phone" => "LNG_PHONE",
			"email" => "LNG_EMAIL",
			"fax" => "LNG_FAX",
			"type" => "LNG_COMPANY_TYPE",
			"creationDate" => "LNG_CREATED",
			"latitude" => "LNG_LATITUDE",
			"longitude" => "LNG_LONGITUDE",
			"activity_radius" => "LNG_ACTIVITY_RADIUS",
			"userName" => "LNG_USER",
			"taxCode" => "LNG_TAX_CODE",
			"package" => "LNG_PACKAGE",
			"facebook" => "LNG_FACEBOOK",
			"twitter" => "LNG_TWITTER",
			"postalCode" => "LNG_POSTAL_CODE",
			"mobile" => "LNG_MOBILE",
			//"viewCount" => "LNG_VIEW_NUMBER",
			//"contactCount" => "LNG_CONTACT_NUMBER",
			//"websiteCount" => "LNG_WEBSITE_CLICKS",
			"contact_name" => "LNG_CONTACT_NAME",
			"contact_email" => "LNG_CONTACT_EMAIL",
			"contact_phone" => "LNG_CONTACT_PHONE",
			"contact_fax" => "LNG_CONTACT_FAX",
			"opening_hours" => "LNG_OPENING_HOURS",
			"start_package" => "LNG_PACKAGE_START_DATE",
			"expire_package" => "LNG_PACKAGE_EXPIRE_DATE",
		);
		return $params;
	}

	/**
	 * Get conference params
	 *
	 * @return array conference params
	 */
	public static function getConferenceParams() {
		$params = array(
			"c_viewCount"=>"LNG_CONFERENCE_VIEW_NUMBER",
			"cs_viewCount"=>"LNG_SESSION_VIEW_NUMBER",
			"csp_viewCount"=>"LNG_SPEAKER_VIEW_NUMBER",
			"cg_clickCount"=>"LNG_CATEGORY_CLICK_NUMBER",
			"cst_clickCount"=>"LNG_SESSION_TYPE_CLICK_NUMBER",
			"categoryName"=>"LNG_CATEGORY_NAME",
			"conferenceName"=>"LNG_CONFERENCE_NAME",
			"speakerName"=>"LNG_SPEAKER_NAME",
			"sessionName"=>"LNG_SESSION_NAME"
		);
		return $params;
	}
	
	/**
	 * Order the paramaters based on selected parameters order. Copy the rest of params at end
	 *
	 * @param object $params
	 * @param object $selectedParams
	 * @return multitype:
	 */
	public static function orderParams($params, $selectedParams) {
		$result = array();
		$result2 = array();
		foreach ($selectedParams as $sp) {
			foreach ($params as $key=>$p) {
				if ($sp == $key) {
					$result[$key]=$p;
				} else {
					$result2[$key]=$p;
				}
			}
		}
			
		$result = array_merge($result, $result2);
			
		return $result;
	}

	/**
	 * Get package custom features
	 *
	 * @return mixed object with the package features
	 */
	public static function getPackageCustomFeatures() {
		$db = JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_attributes where attribute_type=".ATTRIBUTE_TYPE_BUSINESS;
		$db->setQuery($query);
		$feaures = $db->loadObjectList();
		return $feaures;
	}

	/**
	 * Get package with all their options and details
	 *
	 * @param int $price package price, search for package with price higher than this
	 * @param bool $onlyForAdmin option if is searced for only for admin package
	 * @return array
	 */
	public static function getPackageOptions($price=0, $onlyForAdminCheck = false, $packageId = null) {
		
		$groups = JBusinessUtil::getUserGroupCodes();
		$db = JFactory::getDbo();

		//add the current package to the package options - even if it is only for admin
		$packageFilter = "";
		if(!empty($packageId)){
			$packageFilter ="or p.id = $packageId";
		}
			
		if ($onlyForAdminCheck) {
			$packageFilter = " and only_for_admin = 0 $packageFilter";
		}
		
		$price = floatval($price);

        $price = JBusinessUtil::convertPriceToMysql($price);
		$query = "select * from #__jbusinessdirectory_packages p where p.status =1 and price>=$price  $packageFilter order by ordering asc";

		$db->setQuery($query);
		$packages = $db->loadObjectList();

		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updatePackagesTranslation($packages);
		}
		
		$result=array();
		foreach ($packages as $index => $package) {
			foreach ($groups as $group) {
				$packageUsergroup = explode(',', $package->package_usergroup);
				if (!in_array($group, $packageUsergroup) && $group != '8' && !in_array('1', $packageUsergroup)) { //8 is the id of super user, he has the right to show all the packages, '1' is id for public usergroup
					unset($packages[$index]);
				} else {
					$result[$package->id] = $package->name . " - ". JBusinessUtil::getPriceFormat($package->price)." / ". JBusinessUtil::getPackageDuration($package, true) ;
				}
			}
		}
		
		return $result;
	}
	
	
	public static function getCompanyStates() {
	}
	
	public static function getCompanyStatus() {
	}

	/**
	 * Retrieve array with package order states
	 *
	 * @return array with Order states
	 */
	public static function getOrderStates() {
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_NOT_PAID");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_PAID");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 2;
		$state->text = JTEXT::_("LNG_CANCELED");
		$states[] = $state;

		return $states;
	}

	/**
	 * Get statuses
	 *
	 * @return array statuses
	 */
	public static function getStatuses() {
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}

	/**
	 * Get Attribute configuration settings (mandatory, option or not show)
	 *
	 * @return array
	 */
	public static function getAttributeConfiguration() {
		$states = array();
		$state = new stdClass();
		$state->value = ATTRIBUTE_MANDATORY;
		$state->text = JTEXT::_("LNG_MANDATORY");
		$states[] = $state;
		$state = new stdClass();
		$state->value = ATTRIBUTE_OPTIONAL;
		$state->text = JTEXT::_("LNG_OPTIONAL");
		$states[] = $state;
		$state = new stdClass();
		$state->value = ATTRIBUTE_NOT_SHOW;
		$state->text = JTEXT::_("LNG_NOT_SHOW");
		$states[] = $state;
	
		return $states;
	}

	/**
	 * Get payment modes (test or live)
	 *
	 * @return array
	 */
	public static function getModes() {
		$modes = array();
		$state = new stdClass();
		$state->value = "test";
		$state->text = JTEXT::_("LNG_TEST");
		$modes[] = $state;
		$state = new stdClass();
		$state->value = "live";
		$state->text = JTEXT::_("LNG_LIVE");
		$modes[] = $state;
		
		return $modes;
	}
		/**
	 * Get offer custom features
	 *
	 * @return mixed object with the offer features
	 */
	public static function getOfferCustomFeatures() {
		$db = JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_attributes where attribute_type=".ATTRIBUTE_TYPE_OFFER;
		$db->setQuery($query);
		$feaures = $db->loadObjectList();
		return $feaures;
	}
		/**
	 * Get offer params
	 *
	 * @return array offer params
	 */
	public static function getOfferParams() {
		$params = array("id" => "LNG_ID",
			"name" => "LNG_NAME",
			"short_description" => "LNG_SHORT_DESCRIPTION",
			"description" => "LNG_DESCRIPTION",
			"categoryName" => "LNG_CATEGORY",
			"typeName" => "LNG_OFFER_TYPE",
			"startDate" => "LNG_END_DATE",
			"endDate" => "LNG_START_DATE",
			"price" => "LNG_PRICE",
			"specialPrice" => "LNG_SPECIAL_PRICE",
			"total_coupons" => "LNG_COUPONS_NUMBER",
			"price_text" => "LNG_PRICE_TEXT",
			"street_number" => "LNG_STREET_NUMBER",
			"item_selling_type" => "LNG_ITEM_SELLING_TYPE",
			"address" => "LNG_ADDRESS",
			"city" => "LNG_CITY",
			"county" => "LNG_COUNTY",
			"countryName" => "LNG_COUNTRY",
			"latitude" => "LNG_LATITUDE",
			"longitude" => "LNG_LONGITUDE",
			"contact_email" => "LNG_CONTACT_EMAIL",
			"userName" => "LNG_USER",
			"viewCount" => "LNG_VIEW_NUMBER",
		);
		return $params;
	}
}
