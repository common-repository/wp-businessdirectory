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
 * Class JBusinessDirectoryTranslations
 */
#[\AllowDynamicProperties]
class JBusinessDirectoryTranslations {

	/**
	 * JBusinessDirectoryTranslations constructor.
	 */
	private function __construct() {
	}

	/**
	 * @return JBusinessDirectoryTranslations Instance
	 */
	public static function getInstance() {
		static $instance;
		if ($instance === null) {
			$instance = new JBusinessDirectoryTranslations();
		}
		return $instance;
	}

	/**
	 * Get category translations
	 *
	 * @return array category translations
	 */
	public static function getCategoriesTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->categoriesTranslations)) {
			$translations = self::getCategoriesTranslationsObjects();
			$instance->categoriesTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->categoriesTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->categoriesTranslations;
	}

	/**
	 * Retrieve the query for category translations
	 *
	 * @return mixed category translations
	 */
	public static function getCategoriesTranslationsObjects() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_categories c
					inner join  #__jbusinessdirectory_language_translations t on c.id=t.object_id where t.type=".CATEGORY_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		return  $db->loadObjectList();
	}

	/**
	 * Get translation for each business type
	 *
	 * @return array business type translations
	 */
	public static function getBusinessTypesTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->businessTypesTranslations)) {
			$translations = self::getBusinessTypesTranslationsObject();
			$instance->businessTypesTranslations = array();
			foreach ($translations as $translation) {
				$instance->businessTypesTranslations[$translation->object_id]= $translation;
			}
		}
		return $instance->businessTypesTranslations;
	}

	/**
	 * Retrieve the query for the business type translations
	 *
	 * @return object business type translations
	 */
	public static function getBusinessTypesTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
							from  #__jbusinessdirectory_company_types bt
							inner join  #__jbusinessdirectory_language_translations t on bt.id=t.object_id where type=".TYPE_TRANSLATION." and language_tag='$language'";
		
		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
			//JFactory::getApplication()->enqueueMessage($db->error, 'error');
			return true;
		}
		return  $db->loadObjectList();
	}

	/**
	 * Retrieve the translation for each attribute translations
	 *
	 * @return array attributes translations
	 */
	public static function getAttributesTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->attributeTranslations)) {
			$translations = self::getAttributesTranslationsObject();
			$instance->attributeTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->attributeTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->attributeTranslations;
	}

	/**
	 * Prepare the query to retrieve the attribute translations
	 *
	 * @return object attributes translations
	 */
	public static function getAttributesTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_attributes a
					inner join  #__jbusinessdirectory_language_translations t on a.id=t.object_id where t.type=".ATTRIBUTE_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR_!"), 'error');
			return false;
		}
		return  $db->loadObjectList();
	}

	/**
	 * Get the translations for each event type
	 *
	 * @return array event type translations
	 */
	public static function getEventTypesTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();

		if (!isset($instance->eventtypeTranslations)) {
			$translations = self::getEventTypesTranslationsObject();
			$instance->eventtypeTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->eventtypeTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->eventtypeTranslations;
	}


	/**
	 * Update translations for the offer types given to the params
	 *
	 * @param $types array types
	 */
	public static function updateOfferMainTypesTranslation(&$types) {
		if (empty($types)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getOfferTypesTranslations();
		foreach ($types as &$type) {
			if (isset($type->id) && !empty($translations[$type->id])) {
				$type->name = $translations[$type->id]->name;
			}
			if (isset($type->typeId) && !empty($translations[$type->typeId])) {
				$type->typeName = $translations[$type->typeId]->name;
			}
		}
	}

	/**
	 * Get the translations for each offer types
	 *
	 * @return array offer types translations
	 */
	public static function getOfferTypesTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();

		if (!isset($instance->offertypeTranslations)) {
			$translations = self::getOfferTypesTranslationsObject();
			
			$instance->offertypeTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->offertypeTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->offertypeTranslations;
	}

	/**
	 * Prepare the query to retrieve the event type translations
	 *
	 * @return object event type translations
	 */
	public static function getOfferTypesTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_offer_types ot
					inner join  #__jbusinessdirectory_language_translations t on ot.id=t.object_id where t.type=".OFFER_TYPE_TRANSLATION." and language_tag='$language'";

		$db->setQuery($query);
		return  $db->loadObjectList();
	}

	/**
	 * Prepare the query to retrieve the event type translations
	 *
	 * @return object event type translations
	 */
	public static function getEventTypesTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_event_types et
					inner join  #__jbusinessdirectory_language_translations t on et.id=t.object_id where t.type=".EVENT_TYPE_TRANSLATION." and language_tag='$language'";

		$db->setQuery($query);
		return  $db->loadObjectList();
	}

	/**
	 * Get the translations for each event ticket
	 *
	 * @return array event ticket translations
	 */
	public static function getEventTicketsTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();

		if (!isset($instance->eventticketTranslations)) {
			$translations = self::getEventTicketsTranslationsObject();
			$instance->eventticketTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->eventticketTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->eventticketTranslations;
	}

	/**
	 * get event ticket translations
	 *
	 * @return object event ticket translations
	 */
	public static function getEventTicketsTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_event_tickets et
					inner join  #__jbusinessdirectory_language_translations t on et.id=t.object_id where t.type=".EVENT_TICKET_TRANSLATION." and language_tag='$language'";

		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
			return true;
		}
		return  $db->loadObjectList();
	}

	/**
	 * Get the translations for each company services
	 *
	 * @return array company services translations
	 */
	public static function getCompanyServicesTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();

		if (!isset($instance->companyservicesTranslations)) {
			$translations = self::getCompanyServicesTranslationsObject();
			$instance->companyservicesTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->companyservicesTranslations[$translation->object_id]= $translation;
				}
			}
		}

		return $instance->companyservicesTranslations;
	}

	/**
	 * Prepare the query to retrieve the translations for company services
	 *
	 * @return bool|mixed Company services translations
	 * @throws Exception
	 */
	public static function getCompanyServicesTranslationsObject() {
		$db		= JFactory::getDbo();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_services cs
					inner join  #__jbusinessdirectory_language_translations t on cs.id=t.object_id where t.type=".COMPANY_SERVICE_TRANSLATION." and language_tag='$language'";

		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR_S"), 'error');
			return true;
		}
		return  $db->loadObjectList();
	}

	/**
	 * Update the given company services translations
	 *
	 * @param $services object services
	 */
	public static function updateCompanyServicesTranslation(&$services) {
		if (empty($services)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getCompanyServicesTranslations();
		foreach ($services as &$service) {
			if (!empty($translations[$service->id])) {
				$service->name = $translations[$service->id]->name;
			}

			if (!empty($translations[$service->id])) {
				$service->description = $translations[$service->id]->content;
			}
		}
	}

	/**
	 * Retrieve the translation for each company provider
	 *
	 * @return array company providers translations
	 */
	public static function getCompanyProvidersTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();

		if (!isset($instance->companyprovidersTranslations)) {
			$translations = self::getCompanyProvidersTranslationsObject();
			$instance->companyprovidersTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->companyprovidersTranslations[$translation->object_id]= $translation;
				}
			}
		}

		return $instance->companyprovidersTranslations;
	}

	/**
	 * Prepare the query to get the translations for company provider
	 *
	 * @return object company providers translations
	 */
	public static function getCompanyProvidersTranslationsObject() {
		$db		= JFactory::getDbo();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_providers sp
					inner join  #__jbusinessdirectory_language_translations t on sp.id=t.object_id where t.type=".COMPANY_PROVIDER_TRANSLATION." and language_tag='$language'";

		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
			return true;
		}
		return  $db->loadObjectList();
	}

	/**
	 *  Update the translatio for each company provider
	 *
	 * @param $providers object providers that need to be updated
	 */
	public static function updateCompanyProvidersTranslation(&$providers) {
		if (empty($providers)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getCompanyProvidersTranslations();
		foreach ($providers as &$provider) {
			if (!empty($translations[$provider->id]->name)) {
				$provider->name = $translations[$provider->id]->name;
			}

			if (!empty($translations[$provider->id]->content)) {
				$provider->description = $translations[$provider->id]->content;
			}
		}
	}

	/**
	 * Get the translations for each company service list
	 *
	 * @return array Company service lists translations
	 */
	public static function getCompanyServiceListsTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->companyServiceListsTranslations)) {
			$translations = self::getCompanyServiceListsTranslationsObject();
			$instance->companyServiceListsTranslations = array();
	
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->companyServiceListsTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->companyServiceListsTranslations;
	}

	/**
	 * Prepare the query to get the translations for the company service lists
	 *
	 * @return object Company service lists translations
	 */
	public static function getCompanyServiceListsTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_services_list csl
					inner join  #__jbusinessdirectory_language_translations t on csl.id=t.object_id where t.type=".COMPANY_PRICE_LIST_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
			return true;
		}
		return  $db->loadObjectList();
	}
	
	/**
	 * Get the translations for each country
	 *
	 * @return array Country translations
	 */
	public static function getCountryTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->countryTranslations)) {
			$translations = self::getCountryTranslationsObject();
			$instance->countryTranslations = array();
	
			if (!empty($translations) && is_array($translations)) {
				foreach ($translations as $translation) {
					$instance->countryTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->countryTranslations;
	}

	/**
	 * Prepare the query to get the translations for the countries
	 *
	 * @return object Country translations
	 */
	public static function getCountryTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_countries c
					inner join  #__jbusinessdirectory_language_translations t on c.id=t.object_id where t.type=".COUNTRY_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return  $result;
	}

	/**
	 * Get the translations for each announcement
	 *
	 * @return array announcement translations
	 */
	public static function getAnnouncementTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->announcementTranslations)) {
			$translations = self::getAnnouncementTranslationsObject();
			$instance->announcementTranslations = array();
	
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->announcementTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->announcementTranslations;
	}

	/**
	 * Prepare the query to get the translations for the announcements
	 *
	 * @return object Announcements translations
	 */
	public static function getAnnouncementTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_announcements a
					inner join  #__jbusinessdirectory_language_translations t on a.id=t.object_id where t.type=".ANNOUNCEMENT_DESCRIPTION_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
			return true;
		}
		
		return  $db->loadObjectList();
	}

	/**
	 * Get the translations for each project
	 *
	 * @return array projects translations
	 */
	public static function getProjectsTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->projectTranslations)) {
			$translations = self::getProjectTranslationsObject();
			$instance->projectTranslations = array();
	
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->projectTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->projectTranslations;
	}


	/**
	 * Prepare the query to get the translations for the projects
	 *
	 * @return object projects translations
	 */
	public static function getProjectTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
					from  #__jbusinessdirectory_company_projects a
					inner join  #__jbusinessdirectory_language_translations t on a.id=t.object_id where t.type=".PROJECT_DESCRIPTION_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
			return true;
		}
		
		return  $db->loadObjectList();
	}

	/**
	 * Get the translations for each package
	 *
	 * @return array package translations
	 */
	public static function getPackageTranslations() {
		$instance = JBusinessDirectoryTranslations::getInstance();
	
		if (!isset($instance->packageTranslations)) {
			$translations = self::getPackagesTranslationsObject();
			$instance->packageTranslations = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$instance->packageTranslations[$translation->object_id]= $translation;
				}
			}
		}
		return $instance->packageTranslations;
	}

	/**
	 * Prepare the query to get the translations of packages
	 *
	 * @return object package translations
	 */
	public static function getPackagesTranslationsObject() {
		$db		= JFactory::getDBO();
		$language = JBusinessUtil::getLanguageTag();
		$query	= "	SELECT t.*
		from  #__jbusinessdirectory_packages p
		inner join  #__jbusinessdirectory_language_translations t on p.id=t.object_id where t.type=".PACKAGE_TRANSLATION." and language_tag='$language'";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR22"), 'error');
			return true;
		}
		return  $db->loadObjectList();
	}

	/**
	 * Prepare the query to get the translations for an item based on its type, ID and the laguage that is required
	 *
	 * @param $translationType int object that is being translated
	 * @param $objectId int object id that is searched for
	 * @param $language string language that is search for
	 * @return mixed|null translation or null if nothing is found
	 */
	public static function getObjectTranslation($translationType, $objectId, $language) {
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "select * from  #__jbusinessdirectory_language_translations where type=$translationType and object_id=$objectId and language_tag='$language'";
			$db->setQuery($query);
			$translation = $db->loadObject();
			return $translation;
		} else {
			return null;
		}
	}

	/**
	 * Retrieve all the translations saved for an item based on the type and id of the item
	 *
	 * @param $translationType int object type
	 * @param $objectId int object id
	 * @return array
	 */
	public static function getAllTranslations($translationType, $objectId) {
		$translationArray=array();
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "select * from #__jbusinessdirectory_language_translations where type=$translationType and object_id=$objectId order by language_tag";
			$db->setQuery($query);
			$translations = $db->loadObjectList();
			
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$translationArray[$translation->language_tag."_name"]=$translation->name;
					$translationArray[$translation->language_tag]=$translation->content;
					$translationArray[$translation->language_tag."_short"]=$translation->content_short;
					$translationArray[$translation->language_tag."_additional"]=$translation->additional_content;
				}
			}
		}
		return $translationArray;
	}

	/**
	 * Delete all the translations saved for an item based on the type and id of the item
	 *
	 * @param $translationType int
	 * @param $objectId int id of item which translation will be deleted
	 */
	public static function deleteTranslationsForObject($translationType, $objectId) {
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "delete from #__jbusinessdirectory_language_translations where type=$translationType and object_id=$objectId";
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Add a new translation for an item on the database where all the details for translation are passed to the params
	 *
	 * @param $translationType int translation type
	 * @param $objectId int object id
	 * @param $language string language
	 * @param $name string name
	 * @param $shortContent string short content
	 * @param $content string content
	 * @return mixed
	 */
	public static function saveTranslation($translationType, $objectId, $language, $name, $shortContent, $content, $additionalContent = null) {
		$db =JFactory::getDBO();
		$name = $db->escape($name);
		$shortContent = $db->escape($shortContent);
		$content = $db->escape($content);
		$additionalCont = '';
		if (!empty($additionalContent)){
			$additionalCont = $db->escape($additionalContent);
		}
		
		$query = "insert into #__jbusinessdirectory_language_translations(type,object_id,language_tag,name, content_short, content, additional_content) values($translationType,$objectId,'$language','$name','$shortContent','$content','$additionalCont')";
		$db->setQuery($query);
		
		return $db->execute();
	}

	/**
	 * Firstly removes any translation that still exists on db and then add a news one for the item
	 *
	 * @param $translationType int translation type
	 * @param $objectId int object id
	 * @param $identifier string identifier
	 * @param bool $metadataTranslation check if we are saving metadata
	 * @param bool $imported check if we are saving an imported object
	 * @param array $data object details
	 * @throws Exception
	 */
	public static function saveTranslations($translationType, $objectId, $identifier, $metadataTranslation = false, $imported = false, $data = array()) {
		self::deleteTranslationsForObject($translationType, $objectId);
		$languages = JBusinessUtil::getLanguages();

		$db =JFactory::getDBO();
	

		$jinput = JFactory::getApplication()->input;
		foreach ($languages as $lng) {
			if ($imported) {
				$description = $data[$identifier . strtolower($lng)];
				$description = JBusinessUtil::removeRelAttribute($description);
				$shortDescription = $data["short_" . $identifier . strtolower($lng)];
				$additionalDesc = $data["additional_description_" . strtolower($lng)];
				$name = $data["name_" . strtolower($lng)];
				if (empty($name)) {
					$name = $data["subject_" . strtolower($lng)];
				}
				
				if ($metadataTranslation) {
					$description = $data["meta_description_" . strtolower($lng)];
					$description = JBusinessUtil::removeRelAttribute($description);
					$name = $data["meta_title_" . strtolower($lng)];
					$shortDescription = $data["meta_keywords_" . strtolower($lng)];
				}
			} else {
				$description = $_REQUEST[$identifier . $lng];
				$description = JBusinessUtil::removeRelAttribute($description);

				$additionalDescIdentifier = "additional_description_";
				$additionalDesc="";
				if(isset($data[$additionalDescIdentifier . $lng])){
					$additionalDesc = $data[$additionalDescIdentifier . $lng];
				}

				$shortDescFields=["short_description_", "button_text_" , "price_description_"];  
				$idx=0;
				$shortDescription="";
				while(empty($shortDescription) && $idx < count($shortDescFields)) {
					if(isset($data[$shortDescFields[$idx] . $lng])){
						$shortDescription = $data[$shortDescFields[$idx] . $lng];
					}
					$idx++;
				}
				
				$nameFields=["name_", "subject_" , "title_"];  
				$index=0;
				$name="";
				while(empty($name) && $index < count($nameFields)) {					
					$name = $jinput->getString($nameFields[$index] . $lng, '', 'RAW');
					$index++;
				}

				if ($metadataTranslation) {
					$description = $jinput->getString("meta_description_" . $lng, '', 'RAW');
					$description = JBusinessUtil::removeRelAttribute($description);
					$name = $jinput->get("meta_title_" . $identifier . $lng, '', 'RAW');
					$shortDescription = $jinput->getString("meta_keywords_" . $lng, '', 'RAW');
				}
			}
			
			if (! empty($description) || ! empty($shortDescription) || ! empty($name) || ! empty($additionalDesc)) {
				self::saveTranslation($translationType, $objectId, $lng, $name, $shortDescription, $description, $additionalDesc);
			}
		}
	}

	public static function saveCustomTabTranslations($objectId, $imported = false, $data = array())
	{
		self::deleteTranslationsForObject(CUSTOM_TAB_TRANSLATION, $objectId);
		$languages = JBusinessUtil::getLanguages();
		$jinput = JFactory::getApplication()->input;
		foreach ($languages as $lng) {
			if ($imported) {
				$description = $data["custom_tab_content_" . strtolower($lng)];
				$description = JBusinessUtil::removeRelAttribute($description);
				$name = $data["custom_tab_name_" . strtolower($lng)];
			} else {
				$description = $jinput->get("custom_tab_content_" . $lng, '', 'RAW');
				$description = JBusinessUtil::removeRelAttribute($description);
				$name = $jinput->get("custom_tab_name_" . $lng, '', 'RAW');
			}
			if (! empty($description) || ! empty($shortDescription) || ! empty($name)) {
				self::saveTranslation(CUSTOM_TAB_TRANSLATION, $objectId, $lng, $name, '', $description);
			}
		}
	}

		/**
	 * Prepare the query to get the translations for an item based on its type and ID that is required
	 *
	 * @param $translationType int translation type
	 * @param $objectId int object id
	 * @return mixed object with all translation of an object
	 */
	public static function getAllTranslationObjects($translationType, $objectId) {
		if (!empty($objectId)) {
			$db =JFactory::getDBO();
			$query = "select * from #__jbusinessdirectory_language_translations where type=$translationType and object_id=$objectId order by language_tag";
			$db->setQuery($query);
			$translations = $db->loadObjectList();
		}
		return $translations;
	}

	/**
	 * Update all translations for an item based on a type given to the parameters
	 *
	 * @param $object object object to update translation for
	 * @param $translationType int translation type
	 */
	public static function updateEntityTranslation(&$object, $translationType) {
		$language = JBusinessUtil::getLanguageTag();

		if (!isset($object->id)) {
			return null;
		}

		$translation = self::getObjectTranslation($translationType, $object->id, $language);
		if (!empty($translation)) {
			if (!empty($translation->content_short)) {
				$object->short_description = $translation->content_short;
			}
			if (!empty($translation->content)) {
				$object->description = $translation->content;
			}
			if (!empty($translation->additional_content)) {
				switch ($translationType) {
					case OFFER_DESCRIPTION_TRANSLATION:
						$object->price_text = $translation->additional_content;
						break;
					case BUSSINESS_DESCRIPTION_TRANSLATION:
						$object->website = $translation->additional_content;
						break;
				}
			}
			if (!empty($translation->name)) {
				switch ($translationType) {
					case OFFER_DESCRIPTION_TRANSLATION:
						$object->subject = $translation->name;
						break;
					case COUNTRY_TRANSLATION:
						$object->country_name = $translation->name;
						$object->countryName = $translation->name;
						break;
					default:
						$object->name = $translation->name;
				}
			}
		}

		//slogan - for businesses
		if (isset($object->slogan)) {
			$translation = self::getObjectTranslation(BUSSINESS_SLOGAN_TRANSLATION, $object->id, $language);
			if (!empty($translation)) {
				$object->slogan = $translation->content;
			}
		}

		if (isset($object->applicationsettings_id)) {
			$translation = self::getObjectTranslation(TERMS_CONDITIONS_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->terms_conditions = $translation->content;
			}
			$translation = self::getObjectTranslation(TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->terms_conditions_article_id = $translation->content;
			}
			
			$translation = self::getObjectTranslation(REVIEWS_TERMS_CONDITIONS_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->reviews_terms_conditions = $translation->content;
			}
			$translation = self::getObjectTranslation(REVIEWS_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->reviews_terms_conditions_article_id = $translation->content;
			}

			$translation = self::getObjectTranslation(CONTACT_TERMS_CONDITIONS_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->contact_terms_conditions = $translation->content;
			}
			$translation = self::getObjectTranslation(CONTACT_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->contact_terms_conditions_article_id = $translation->content;
			}

			$translation = self::getObjectTranslation(RESPONSIBLE_CONTENT_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->content_responsible = $translation->content;
			}

			$translation = self::getObjectTranslation(PRIVACY_POLICY_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->privacy_policy = $translation->content;
			}
			$translation = self::getObjectTranslation(PRIVACY_POLICY_ARTICLE_ID_TRANSLATION, $object->applicationsettings_id, $language);
			if (!empty($translation)) {
				$object->privacy_policy_article_id = $translation->content;
			}
		}

		if (!empty($object->categories)) {
			$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
			if(!empty($categoryTranslations)){
				
				if(is_string($object->categories)){
					$categories = explode("#|", $object->categories);
				}
				
				$resCategories = array();
				foreach ($categories as &$category) {
					$categoryItem =  explode("|", $category);
					if (!empty($categoryTranslations[$categoryItem[0]])) {
						$categoryItem[1] = $categoryTranslations[$categoryItem[0]]->name;
					}
					$category = implode("|", $categoryItem);
					
					$resCategories[] = $category;
				}
			
				$object->categories = implode("#|", $resCategories);
			}
		}
		
		
		if (!empty($object->typeId)) {
			$typeTranslations = JBusinessDirectoryTranslations::getBusinessTypesTranslations();
			if(is_string($object->typeId)){
				$typesIds = explode(",", $object->typeId);
			}
			if(!empty($object->typeName)){
				$orgTypeNames = explode(",",$object->typeName);
				$typeNames = array();
				if(!empty($typesIds)){
					foreach($typesIds as $i=>$typeId){
						if (!empty($typeTranslations[$typeId])) {
							$typeNames[] = $typeTranslations[$typeId]->name;
						}else{
							$typeNames[] = $orgTypeNames[$i];
						}
			
					}
				}
				$object->typeName = implode(", ",$typeNames);
			}
		}
		
		if (!empty($object->eventTypeId)) {
			$typeTranslations = JBusinessDirectoryTranslations::getEventTypesTranslations();
			if (!empty($typeTranslations[$object->eventTypeId])) {
				$object->eventType = $typeTranslations[$object->eventTypeId]->name;
			}
		}
		
		if (!empty($object->offer_type)) {
			$typeTranslations = JBusinessDirectoryTranslations::getOfferTypesTranslations();
			if (!empty($typeTranslations[$object->offer_type])) {
				$object->offerType = $typeTranslations[$object->offer_type]->name;
			}
		}

		//update country translation
		if (!empty($object->countryId)) {
			$countryTranslations = JBusinessDirectoryTranslations::getCountryTranslations();
			if (!empty($countryTranslations[$object->countryId])) {
				$object->country_name = $countryTranslations[$object->countryId]->name;
			}
		}
		
		//update extra tab translation
		if (isset($object->custom_tab_name)) {
			$translation = self::getObjectTranslation(CUSTOM_TAB_TRANSLATION, $object->id, $language);
			if (!empty($translation)) {
				$object->custom_tab_name = $translation->name;
				$object->custom_tab_content = $translation->content;
			}
		}
	}

	/**
	 * Update translations for a business listing for all the fields that have a translation
	 *
	 * @param $companies array $companies
	 */
	public static function updateBusinessListingsTranslation(&$companies) {
		$ids = array();
		
		if (empty($companies)) {
			return;
		}
		
		foreach ($companies as $company) {
			$ids[] = $company->id;
		}
		$objectIds = implode(',', $ids);
		
		
		$translationType = BUSSINESS_DESCRIPTION_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
		
		$db =JFactory::getDBO();
		$query = "select object_id, name, content_short, content, additional_content from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();

		$companyTranslations = array();
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$companyTranslations[$translation->object_id]= $translation;
			}
		}
		
		foreach ($companies as &$company) {
			if (!empty($companyTranslations[$company->id])) {
				if (!empty($companyTranslations[$company->id]->name)) {
					$company->name = $companyTranslations[$company->id]->name;
				}
				if (!empty($companyTranslations[$company->id]->content_short)) {
					$company->short_description = $companyTranslations[$company->id]->content_short;
				}
				if (!empty($companyTranslations[$company->id]->content)) {
					$company->description = $companyTranslations[$company->id]->content;
				}
				if (!empty($companyTranslations[$company->id]->additional_content)) {
					$company->website = $companyTranslations[$company->id]->additional_content;
				}
			}
			
			$typeTranslations = JBusinessDirectoryTranslations::getBusinessTypesTranslations();
			//to do - when refactored you need to check the other translation
			if (isset($company->typeId) && !empty($typeTranslations[$company->typeId])) {
				$company->typeName = $typeTranslations[$company->typeId]->name;
			}
			
			if (!empty($company->categories)) {
				$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
				$categories = explode("#|", $company->categories);
				$resCategories = array();
				foreach ($categories as &$category) {
					$categoryItem =  explode("|", $category);
					if (!empty($categoryTranslations[$categoryItem[0]])) {
						$categoryItem[1] = $categoryTranslations[$categoryItem[0]]->name;
					}
					$category = implode("|", $categoryItem);
					$resCategories[] = $category;
				}
			
				$company->categories = implode("#|", $resCategories);
			}
			
			//update main category translation
			if (!empty($company->mainCategoryId)) {
				$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
				if (!empty($categoryTranslations[$company->mainCategoryId])) {
					$company->mainCategory = $categoryTranslations[$company->mainCategoryId]->name;
				}
			}

			//update package translation
			if (!empty($company->packageId)) {
				$packageTranslations = JBusinessDirectoryTranslations::getPackageTranslations();
				if (!empty($packageTranslations[$company->packageId])) {
					$company->packageName = $packageTranslations[$company->packageId]->name;
				}
			}
				
			//update country translation
			if (!empty($company->countryId)) {
				$countryTranslations = JBusinessDirectoryTranslations::getCountryTranslations();
				if (!empty($countryTranslations[$company->countryId])) {
					$company->countryName = $countryTranslations[$company->countryId]->name;
				}
			}
		}
	}

	/**
	 * Update translations for a category for all the fields that have a translation
	 *
	 * @param $categories array categories
	 */
	public static function updateCategoriesTranslation(&$categories) {
		if (empty($categories)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getCategoriesTranslations();
		foreach ($categories as &$categoryS) {
			$category = $categoryS;
			if (is_array($category)) {
				if (!empty($category["subCategories"])) {
					$category[0]->subcategories = $category["subCategories"];
				}
				$category = $category[0];
			}
			
			if (!empty($category->id) && isset($translations[$category->id])&& !empty($translations[$category->id]->name)) {
				$category->name = $translations[$category->id]->name;
			}
			if (!empty($category->subcategories)) {
				foreach ($category->subcategories as &$subcat) {
					if (is_array($subcat)) {
						$subcat= $subcat[0];
					}
					//dump($translations[$subcat->id]);
					if (!empty($translations[$subcat->id]) && !empty($translations[$subcat->id]->name)) {
						$subcat->name = $translations[$subcat->id]->name;
					}
				}
			}
		}
	}

	/**
	 * Update translations for company service lists given to the params
	 *
	 * @param $services array company service lists
	 */
	public static function updateCompanyServiceListsTranslation(&$services) {
		if (empty($services)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getCompanyServiceListsTranslations();
		foreach ($services as &$service) {
			if (!empty($translations[$service->id])) {
				$service->service_name = $translations[$service->id]->name;
				$service->service_description = $translations[$service->id]->content;
			}
		}
	}
	/**
	 * Update translations for countries given to the params
	 *
	 * @param $countries array countries
	 */
	public static function updateCountriesTranslation(&$countries) {
		if (empty($countries)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getCountryTranslations();
		foreach ($countries as &$country) {
			if (!empty($translations[$country->id])) {
				$country->country_name = $translations[$country->id]->name;
			}
		}
	}

	/**
	 * Update translations for announcements given to the params
	 *
	 * @param $announcements array announcements
	 */
	public static function updateAnnouncementsTranslation(&$announcements) {
		if (empty($announcements)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getAnnouncementTranslations();
		foreach ($announcements as &$announcement) {
			if (!empty($translations[$announcement->id])) {
				$announcement->button_text = $translations[$announcement->id]->content_short;
				$announcement->title = $translations[$announcement->id]->name;
				$announcement->description = $translations[$announcement->id]->content;
			}
		}
	}

	/**
	 * Update translations for countries given to the params
	 *
	 * @param $attributes array attributes
	 */
	public static function updateAttributesTranslation(&$attributes) {
		if (empty($attributes)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getAttributesTranslations();
		foreach ($attributes as &$attribute) {
			if (!empty($translations[$attribute->id])) {
				$attribute->name = $translations[$attribute->id]->name;
			}
		}
	}

	/**
	 * Update translations for the event types of each event given to the params
	 *
	 * @param $events array events
	 */
	public static function updateEventTypesTranslation(&$events) {
		if (empty($events)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getEventTypesTranslations();
		foreach ($events as &$event) {
			if (isset($event->typeId)) {
				if (!empty($translations[$event->typeId])) {
					$event->typeName = $translations[$event->typeId]->name;
				}
			} else {
				$id = isset($event->type)?$event->type:$event->id;
				if (!empty($translations[$id])) {
					// Check if the object passed to the function is an event, or an event type,
					// and apply the translation accordingly
					if (!empty($event->eventType)) {
						$event->eventType = $translations[$id]->name;
					} else {
						$event->name = $translations[$id]->name;
					}
				}
			}
		}
	}

	/**
	 * Update translations for the offer types of each offer given to the params
	 *
	 * @param $offers array offers
	 */
	public static function updateOfferTypesTranslation(&$offers) {
		if (empty($offers)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getOfferTypesTranslations();
		if (!empty($translations)) {
			foreach ($offers as &$offer) {

				if (isset($offer->typeId)) {
					if (!empty($translations[$offer->typeId])) {
						$offer->typeName = $translations[$offer->typeId]->name;
						$offer->offerType = $translations[$offer->typeId]->name;
					}
				} else {
					$id = isset($offer->type)?$offer->type:$offer->id;
					if (!empty($translations[$id])) {
						// Check if the object passed to the function is an event, or an event type,
						// and apply the translation accordingly
						if (!empty($offer->offerType)) {
							$offer->offerType = $translations[$id]->name;
						} else {
							$offer->name = $translations[$id]->name;
						}
					}
				}
			}
		}
	}

	/**
	 * Update translations for the event tickets of each offer given to the params
	 *
	 * @param $events array events
	 */
	public static function updateEventTicketsTranslation(&$events) {
		if (empty($events)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getEventTicketsTranslations();
		foreach ($events as &$event) {
			$id = isset($event->type)?$event->type:$event->id;
			if (!empty($translations[$id])) {
				// Check if the object passed to the function is an event, or an event type,
				// and apply the translation accordingly
				if (!empty($event->eventType)) {
					$event->eventType = $translations[$id]->name;
				} else {
					$event->name = $translations[$id]->name;
				}
			}
		}
	}

	/**
	 * Update translations for the company types given to the params
	 *
	 * @param $types array types
	 */
	public static function updateTypesTranslation(&$types) {
		if (empty($types)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getBusinessTypesTranslations();
		foreach ($types as &$type) {
			if (isset($type->id) && !empty($translations[$type->id])) {
				$type->name = $translations[$type->id]->name;
			}
			if (isset($type->typeId) && !empty($translations[$type->typeId])) {
				$type->typeName = $translations[$type->typeId]->name;
			}
		}
	}

	/**
	 * Update translations for the listing slogans of each listing given to the params
	 *
	 * @param $companies array companies that need to update the slogan translation
	 */
	public static function updateBusinessListingsSloganTranslation(&$companies) {
		$ids = array();
		
		if (empty($companies)) {
			return;
		}
		
		foreach ($companies as $company) {
			$ids[] = $company->id;
		}
		$objectIds = implode(',', $ids);
	
	
		$translationType = BUSSINESS_SLOGAN_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, content from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
		
		$short_description = array();
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$short_description[$translation->object_id]= $translation->content;
			}
		}
	
		foreach ($companies as &$company) {
			if (!empty($short_description[$company->id])) {
				$company->slogan = $short_description[$company->id];
			}
		}
	
		//dump($companies);
	}

	public static function updateMetaDataTranslation(&$object, $translationType) {
		$language = JBusinessUtil::getLanguageTag();
		$translation = self::getObjectTranslation($translationType, $object->id, $language);
		
		if (!empty($translation)) {
			
			if (!empty($translation->name)) {
				$object->meta_title = $translation->name;
			}

			if (!empty($translation->content_short) && isset($object->meta_keywords)) {
				$object->meta_keywords = $translation->content_short;
			}
			
			if (!empty($translation->content)) {
				$object->meta_description = $translation->content;
			}
			
		}
		
	}
	
	/**
	 * Update all translations for each object of each offer given to the params
	 *
	 * @param $offers array offers
	 */
	public static function updateOffersTranslation(&$offers) {
		$ids = array();
		
		if (empty($offers)) {
			return;
		}
		
		foreach ($offers as $offer) {
			$ids[] = $offer->id;
		}
		$objectIds = implode(',', $ids);
	
	
		$translationType = OFFER_DESCRIPTION_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, name, content_short, additional_content from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
	
		$short_description = array();
		$additional_description = array();
		$subject = array();

		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$short_description[$translation->object_id]= $translation->content_short;
				$additional_description[$translation->object_id]= $translation->additional_content;
				$subject[$translation->object_id]= $translation->name;
			}
		}
		
		foreach ($offers as &$offer) {
			if (!empty($short_description[$offer->id])) {
				$offer->short_description = $short_description[$offer->id];
			}
			if (!empty($additional_description[$offer->id])) {
				$offer->price_text = $additional_description[$offer->id];
			}
			if (!empty($subject[$offer->id])) {
				$offer->subject = $subject[$offer->id];
			}
			
			if (!empty($offer->categories)) {
				$categoryTranslations = self::getInstance()->getCategoriesTranslations();
				$categories = explode("#|", $offer->categories);
				$resCategories = array();
				foreach ($categories as &$category) {
					$categoryItem =  explode("|", $category);
					if (!empty($categoryTranslations[$categoryItem[0]])) {
						$categoryItem[1] = $categoryTranslations[$categoryItem[0]]->name;
					}
					$category = implode("|", $categoryItem);
					$resCategories[] = $category;
				}
			
				$offer->categories = implode("#|", $resCategories);
			}
		}
	}


    /**
     * Update all translations for each item in the cart
     *
     * @param $items
     */
    public static function updateCartTranslation(&$items) {
        $ids = array();

        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            $ids[] = $item->id;
        }

        $objectIds = implode(',', $ids);

        $translationType = OFFER_DESCRIPTION_TRANSLATION;
        $language = JBusinessUtil::getLanguageTag();

        $db =JFactory::getDBO();
        $query = "select object_id, name, content_short from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
        $db->setQuery($query);
        $translations = $db->loadObjectList();

        $short_description = array();
        $subject = array();
        if (!empty($translations)) {
            foreach ($translations as $translation) {
                $short_description[$translation->object_id]= $translation->content_short;
                $subject[$translation->object_id]= $translation->name;
            }
        }

        foreach ($items as &$item) {
            if (!empty($short_description[$item->id])) {
                $item->short_description = $short_description[$item->id];
            }
            if (!empty($subject[$item->id])) {
                $item->name = $subject[$item->id];
            }

            if (!empty($item->categories)) {
                $categoryTranslations = self::getInstance()->getCategoriesTranslations();
                $categories = explode("#|", $item->categories);
                $resCategories = array();
                foreach ($categories as &$category) {
                    $categoryItem =  explode("|", $category);
                    if (!empty($categoryTranslations[$categoryItem[0]])) {
                        $categoryItem[1] = $categoryTranslations[$categoryItem[0]]->name;
                    }
                    $category = implode("|", $categoryItem);
                    $resCategories[] = $category;
                }

                $item->categories = implode("#|", $resCategories);
            }
        }
    }


    /**
	 * Update all translations for each object of each event given to the params
	 *
	 * @param $events array events
	 */
	public static function updateEventsTranslation(&$events) {
		$ids = array();
		
		if (empty($events)) {
			return;
		}
		
		foreach ($events as $event) {
			$ids[] = $event->id;
		}
		$objectIds = implode(',', $ids);
	
		$translationType = EVENT_DESCRIPTION_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, name, content, content_short from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
		$description = array();
		$name = array();
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$description[$translation->object_id]= $translation->content;
				$short_description[$translation->object_id]= $translation->content_short;
				$name[$translation->object_id]= $translation->name;
			}
		}
	
		foreach ($events as &$event) {
			if (!empty($short_description[$event->id])) {
				$event->short_description = $short_description[$event->id];
			}
			if (!empty($description[$event->id])) {
				$event->description = $description[$event->id];
			}
			if (!empty($name[$event->id])) {
				$event->name = $name[$event->id];
			}
			
			if (!empty($event->categories)) {
				$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
				if (!empty($event->categories)) {
					$categories = explode("#|", $event->categories);
					$resCategories = array();
					foreach ($categories as &$category) {
						$categoryItem =  explode("|", $category);
						if (!empty($categoryTranslations[$categoryItem[0]])) {
							$categoryItem[1] = $categoryTranslations[$categoryItem[0]]->name;
						}
						$category = implode("|", $categoryItem);
						$resCategories[] = $category;
					}
						
					$event->categories = implode("#|", $resCategories);
				}
			}
		}
	}

	/**
	 * Update all translations for each package given to the params
	 *
	 * @param $packages array packages
	 */
	public static function updatePackagesTranslation(&$packages) {
		$ids = array();
		
		if (empty($packages)) {
			return;
		}
		
		foreach ($packages as $package) {
			$ids[] = $package->id;
		}
		$objectIds = implode(',', $ids);
	
	
		$translationType = PACKAGE_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, name, content, content_short from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
		$description = array();
		$name = array();
		$priceDescription = array();
		
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$description[$translation->object_id]= $translation->content;
				$name[$translation->object_id] = $translation->name;
				$priceDescription[$translation->object_id] = $translation->content_short;
			}
		}
	
		foreach ($packages as &$package) {
			if (!empty($description[$package->id])) {
				$package->description = $description[$package->id];
			}
			if (!empty($name[$package->id])) {
				$package->name = $name[$package->id];
			}
			if (!empty($priceDescription[$package->id])) {
				$package->price_description = $priceDescription[$package->id];
			}
		}
	}



	/**
	 * Update translations for projects given to the params
	 *
	 * @param $projects array projects
	 */
	public static function updateProjectsTranslations(&$projects) {
		if (empty($projects)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getProjectsTranslations();
		foreach ($projects as &$project) {
			if (!empty($translations[$project->id])) {
				$project->name = $translations[$project->id]->name;
				$project->description = $translations[$project->id]->content;
			}
		}
	}

	/**
	 * Update all translations for each conference given to the params
	 *
	 * @param $conferences array conferences
	 */
	public static function updateConferenceTranslations(&$conferences) {
		$ids = array();
	
		if (empty($conferences)) {
			return;
		}
	
		foreach ($conferences as $conference) {
			$ids[] = $conference->id;
		}
		$objectIds = implode(',', $ids);
	
		$translationType = CONFERENCE_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, content_short, name from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
		
		$conferenceTranslations = array();
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$conferenceTranslations[$translation->object_id]= $translation;
			}
		}
		
		foreach ($conferences as &$conference) {
			if (!empty($conferenceTranslations[$conference->id])) {
				if (!empty($conferenceTranslations[$conference->id]->name)) {
					$conference->name = $conferenceTranslations[$conference->id]->name;
				}
				if (!empty($conferenceTranslations[$conference->id]->content_short)) {
					$conference->short_description = $conferenceTranslations[$conference->id]->content_short;
				}
			}
		}
	}

	/**
	 * Update all translations for each session given to the params
	 *
	 * @param $conferenceSessions array conference sessions
	 */
	public static function updateConferenceSessionsTranslation($conferenceSessions) {
		$ids = array();
		if (empty($conferenceSessions)) {
			return;
		}
		
		if (!is_array($conferenceSessions)) {
			$conferenceSessions = array($conferenceSessions);
		}
	
		foreach ($conferenceSessions as $cSession) {
			if (is_array($cSession)) {
				$ids[] =$cSession[0];
			} else {
				$ids[] = $cSession->id;
			}
		}
			
		$objectIds = implode(',', $ids);
	
		$translationType = CONFERENCE_SESSION_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, name, content_short, content from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$description[$translation->object_id]= $translation;
			}
		}
		
		foreach ($conferenceSessions as &$conferenceSession) {
			if (is_array($conferenceSession)) {
				if (!empty($description[$conferenceSession[0]]->name)) {
					$conferenceSession[1] = $description[$conferenceSession[0]]->name;
				}
			} else {
				if (!empty($description[$conferenceSession->id]->content_short)) {
					$conferenceSession->short_description = $description[$conferenceSession->id]->content_short;
				}
				if (!empty($description[$conferenceSession->id]->content)) {
					$conferenceSession->description = $description[$conferenceSession->id]->content;
				}
				
				if (!empty($description[$conferenceSession->id]->name)) {
					$conferenceSession->name = $description[$conferenceSession->id]->name;
				}
				
				if (!empty($conferenceSession->categories)) {
					$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
					
					foreach ($conferenceSession->categories as &$category) {
						if (!empty($categoryTranslations[$category[0]])) {
							$category[1] = $categoryTranslations[$category[0]]->name;
						}
					}
				}
				
				if (!empty($conferenceSession->types)) {
					$typeTranslations = JBusinessDirectoryTranslations::getBusinessTypesTranslations();
					if (!empty($typeTranslations[$object->typeId])) {
						$object->typeName = $typeTranslations[$object->typeId]->name;
					}
				}
				
				if (!empty($conferenceSession->typeId)) {
					$typeTranslations = JBusinessDirectoryTranslations::getConferenceSessionTypesTranslationsObject();
					if (!empty($typeTranslations[$conferenceSession->typeId])) {
						$conferenceSession->typeName = $typeTranslations[$conferenceSession->typeId]->name;
					}
				}
			}
		}
	}

	/**
	 * Update all translations for each speaker given to the params
	 *
	 * @param $speakers array speakers
	 */
	public static function updateConferenceSpeakersTranslations(&$speakers) {
		$ids = array();
		if (empty($speakers)) {
			return;
		}

		foreach ($speakers as $speaker) {
			$ids[] = $speaker->id;
		}
		$objectIds = implode(',', $ids);
	
		$translationType = CONFERENCE_SPEAKER_TRANSLATION;
		$language = JBusinessUtil::getLanguageTag();
	
		$db =JFactory::getDBO();
		$query = "select object_id, content_short, content  from  #__jbusinessdirectory_language_translations where type=$translationType and object_id in ($objectIds) and language_tag='$language'";
		$db->setQuery($query);
		$translations = $db->loadObjectList();
		
		$speakerTranslations = array();
		if (!empty($translations)) {
			foreach ($translations as $translation) {
				$speakerTranslations[$translation->object_id]= $translation;
			}
		}

		foreach ($speakers as &$speaker) {
			if (!empty($speakerTranslations[$speaker->id])) {
				if (!empty($speakerTranslations[$speaker->id]->content)) {
					$speaker->biography = $speakerTranslations[$speaker->id]->content;
				}
				if (!empty($speakerTranslations[$speaker->id]->content_short)) {
					$speaker->short_biography = $speakerTranslations[$speaker->id]->content_short;
				}
			}
			
			if (!empty($speaker->typeId)) {
				$typeTranslations = JBusinessDirectoryTranslations::getConferenceSpeakerTypeTranslationsObject();
				if (!empty($typeTranslations[$speaker->typeId])) {
					$speaker->typeName = $typeTranslations[$speaker->typeId]->name;
				}
			}
		}
	}

	/**
	 * Update all translations for each conference type given to the params
	 *
	 * @param $types array conference types translation
	 */
	public static function updateConferenceTypesTranslation(&$types) {
		if (empty($types)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getConferenceSessionTypesTranslationsObject();
	
		foreach ($types as &$type) {
			if (!empty($translations[$type->id])) {
				$type->name = $translations[$type->id]->name;
			}
		}
	}

	/**
	 * Update all translations for each speaker type given to the params
	 *
	 * @param $types array conference speaker types
	 */
	public static function updateConferenceSpeakerTypesTranslation(&$types) {
		if (empty($types)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getConferenceSpeakerTypeTranslationsObject();
	
		foreach ($types as &$type) {
			if (!empty($translations[$type->id])) {
				$type->name = $translations[$type->id]->name;
			}
		}
	}

	/**
	 * Update all translations for each conference level given to the params
	 *
	 * @param $levels array levels
	 */
	public static function updateConferenceLevelTranslation(&$levels) {
		if (empty($levels)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getConferenceSessionLevelTranslationsObject();
	
		foreach ($levels as &$level) {
			if (!empty($translations[$level->id])) {
				$level->name = $translations[$level->id]->name;
			}
		}
	}

	/**
	 * Get all translations for session types
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public static function getConferenceSessionTypesTranslationsObject() {
		$instance = JBusinessDirectoryTranslations::getInstance();
		if (!isset($instance->conferenceTypeTranslations)) {
			$db		= JFactory::getDBO();
			$language = JBusinessUtil::getLanguageTag();
			$query	= "	SELECT t.*
				from  #__jbusinessdirectory_conference_session_types bt
				inner join  #__jbusinessdirectory_language_translations t on bt.id=t.object_id where type=".CONFERENCE_TYPE_TRANSLATION." and language_tag='$language'";
			
			$db->setQuery($query);
			if (!$db->execute()) {
				JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
				return true;
			}
			$translations =  $db->loadObjectList();
			
			$result = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$result[$translation->object_id]= $translation;
				}
			}
			
			$instance->conferenceTypeTranslations = $result;
		}
		
		return $instance->conferenceTypeTranslations;
	}

	/**
	 * Get all translations for session levels
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public static function getConferenceSessionLevelTranslationsObject() {
		$instance = JBusinessDirectoryTranslations::getInstance();
		if (!isset($instance->sessionLevelTranslations)) {
			$db		= JFactory::getDBO();
			$language = JBusinessUtil::getLanguageTag();
			$query	= "	SELECT t.*
						from  #__jbusinessdirectory_conference_session_levels bt
						inner join  #__jbusinessdirectory_language_translations t on bt.id=t.object_id where type=".CONFERENCE_LEVEL_TRANSLATION." and language_tag='$language'";
	
			$db->setQuery($query);
			if (!$db->execute()) {
				JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
				return true;
			}
			$translations =  $db->loadObjectList();
				
			$result = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$result[$translation->object_id]= $translation;
				}
			}
			$instance->sessionLevelTranslations = $result;
		}
	
		return $instance->sessionLevelTranslations;
	}

	/**
	 * Get all translations for speaker types
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public static function getConferenceSpeakerTypeTranslationsObject() {
		$instance = JBusinessDirectoryTranslations::getInstance();
		if (!isset($instance->speakerTypesTranslations)) {
			$db		= JFactory::getDBO();
			$language = JBusinessUtil::getLanguageTag();
			$query	= "	SELECT t.*
						from  #__jbusinessdirectory_conference_speaker_types bt
						inner join  #__jbusinessdirectory_language_translations t on bt.id=t.object_id where type=".CONFERENCE_SPEAKER_TYPE_TRANSLATION." and language_tag='$language'";
	
			$db->setQuery($query);
			if (!$db->execute()) {
				JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
				return true;
			}
			$translations =  $db->loadObjectList();
	
			$result = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$result[$translation->object_id]= $translation;
				}
			}

			$instance->speakerTypesTranslations = $result;
		}

		return $instance->speakerTypesTranslations;
	}

	/**
	 * Update translation for all review criterias passed through params
	 *
	 * @param $reviewCriterias array review criteria
	 */
	public static function updateReviewCriteriaTranslation(&$reviewCriterias) {
		if (empty($reviewCriterias)) {
			return;
		}
	
		$translations = JBusinessDirectoryTranslations::getReviewCriteriaTranslationsObject();
		foreach ($reviewCriterias as &$reviewCriteria) {
			if (!empty($translations[$reviewCriteria->id])) {
				$reviewCriteria->name = $translations[$reviewCriteria->id]->name;
			}
		}
	}

	/**
	 * Retrieve all translations for the review criteria
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public static function getReviewCriteriaTranslationsObject() {
		$instance = JBusinessDirectoryTranslations::getInstance();
		if (!isset($instance->reviewCriteriaTranslations)) {
			$db		= JFactory::getDBO();
			$language = JBusinessUtil::getLanguageTag();
			$query	= "	SELECT t.*
						from  #__jbusinessdirectory_company_reviews_criteria bt
						inner join  #__jbusinessdirectory_language_translations t on bt.id=t.object_id where type=".REVIEW_CRITERIA_TRANSLATION." and language_tag='$language'";
	
			$db->setQuery($query);
			if (!$db->execute()) {
				JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
				return true;
			}
			$translations =  $db->loadObjectList();
	
			$result = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$result[$translation->object_id]= $translation;
				}
			}
	
			$instance->reviewCriteriaTranslations = $result;
		}
	
		return $instance->reviewCriteriaTranslations;
	}

	/**
	 * Update translation for every review question passed on params
	 *
	 * @param $reviewQuestions array review questions
	 */
	public static function updateReviewQuestionTranslation(&$reviewQuestions) {
		if (empty($reviewQuestions)) {
			return;
		}

		$translations = JBusinessDirectoryTranslations::getReviewQuestionTranslationsObject();
		foreach ($reviewQuestions as &$reviewQuestion) {
			if (!empty($translations[$reviewQuestion->id])) {
				$reviewQuestion->name = $translations[$reviewQuestion->id]->name;
			}
		}
	}

	/**
	 * Retrieve all review question translations
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public static function getReviewQuestionTranslationsObject() {
		$instance = JBusinessDirectoryTranslations::getInstance();
		if (!isset($instance->reviewQuestionTranslations)) {
			$db		= JFactory::getDBO();
			$language = JBusinessUtil::getLanguageTag();
			$query	= "	SELECT t.*
						from  #__jbusinessdirectory_company_reviews_question bt
						inner join  #__jbusinessdirectory_language_translations t on bt.id=t.object_id where t.type=".REVIEW_QUESTION_TRANSLATION." and language_tag='$language'";

			$db->setQuery($query);
			if (!$db->execute()) {
				JFactory::getApplication()->enqueueMessage(JText::_("LNG_UNKNOWN_ERROR"), 'error');
				return true;
			}
			$translations =  $db->loadObjectList();

			$result = array();
			if (!empty($translations)) {
				foreach ($translations as $translation) {
					$result[$translation->object_id]= $translation;
				}
			}

			$instance->reviewQuestionTranslations = $result;
		}

		return $instance->reviewQuestionTranslations;
	}

	/**
	 * Retrieve translated constact by the name given to the params
	 *
	 * @param $itemName string item name
	 * @return string
	 * @since version
	 */
	public static function getTranslatedItemName($itemName) {

		$appSettings = JBusinessUtil::getApplicationSettings();
		if(!$appSettings->enable_multilingual){
			return $itemName;
		}

		if (false !== strpos($itemName, 'LNG_')) {
			return JText::_($itemName);
		} else {
			$languagePaymentOptions = JText::_('LNG_' . strtoupper(str_replace(" ", "_", $itemName)));
			return $languagePaymentOptions;
		}
	}
}
