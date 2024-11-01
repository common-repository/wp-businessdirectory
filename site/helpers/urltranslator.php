<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 */
if (!defined('ABSPATH'))
	die ('Restricted access');

/**
 *
 * @package		JBD.Plugin
 * @subpackage	System.remember
 */
class WBDUrlTranslator 
{
    
    var $mappings = array("catalog"=>"catalog","listings"=>"search", "offers"=>"offers", "events"=>"events", "control-panel"=>"useroptions","payment-plans"=>"packages",
						"categories"=>"categories", 
						"user-dashboard"=>"userdashboard",
						"trips"=>"trips",
						"videos"=>"videos",
						"quoterequests"=>"requestquotes",
						"conferences"=>"conferences","speakers"=>"speakers","sessions"=>"conferencesessions");
    
	function translateRoute($category, $keyword)
	{
		//do now execute admin
		if (is_admin()) {
			return;
		}

		$appSettings = JBusinessUtil::getApplicationSettings();
		
		$jinput = JFactory::getApplication()->input;
		$view = $jinput->get->getString("view",null);
		$task = $jinput->get->getString("task",null);
		
		if(!empty($view) || !empty($task)){
		    return;
		}

		global $wp;
		$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
		$url = $base_url . $_SERVER["REQUEST_URI"];
		
		$url = str_replace(add_query_arg($wp->query_vars, home_url())."/", "", $url);
		$url = "/".$url;
		$url = str_replace("/index.php", "", $url);
		$url = str_replace("index.php", "", $url);

		$lang = JBusinessUtil::getCurrentLanguageCode();
		$url = str_replace("/".$lang."/","",$url);
		if(!empty($appSettings->url_menu_alias)){
			$url = str_replace($appSettings->url_menu_alias."/","",$url);
		}
		
		if(strpos($url,"/") === 0){
			$url = substr($url,1);
		}

		$pieces = explode("/", $url);
		if (count($pieces)>1) {
			$keyword= end($pieces);
			$category = reset($pieces);
		} else {
			$keyword = $url;
		}
		
		if (strpos($keyword, "?")) {
			$arr = explode("?", $keyword);
			$keyword = $arr[0];
		}
		
		if (strpos($keyword, "&")) {
			$arr = explode("&", $keyword);
			$keyword = $arr[0];
		}

		if (strpos($keyword, "&?")) {
			$arr = explode("&", $keyword);
			$keyword = $arr[0];
		}

		$params = array();
		if(isset($this->mappings[$category])){
		    $params = $this->getMappingParams($this->mappings[$category]);
		}else if ($category== $appSettings->category_url_naming) {
			$params = $this->getCategoryParms($keyword, "search", CATEGORY_TYPE_BUSINESS);
		} elseif ($category==$appSettings->offer_category_url_naming) {
			$params = $this->getCategoryParms($keyword, "offers", CATEGORY_TYPE_OFFER);
		} elseif ($category==$appSettings->event_category_url_naming) {
			$params = $this->getCategoryParms($keyword, "events", CATEGORY_TYPE_EVENT);
		} elseif ($category==$appSettings->offer_url_naming) {
			$params = $this->getOffersParms($keyword);
		} elseif ($category==$appSettings->event_url_naming) {
			$params = $this->getEventParms($keyword);
		} elseif ($category==$appSettings->city_url_naming) {
			$params = $this->getCityParams($keyword);
		} elseif ($category==$appSettings->region_url_naming) {
			$params = $this->getRegionParams($keyword);
		} elseif ($category==$appSettings->conference_url_naming) {
			$params = $this->getConferenceParms($keyword);
		} elseif ($category==$appSettings->conference_session_url_naming) {
			$params = $this->getConferenceSessionParms($keyword);
		} elseif ($category==$appSettings->speaker_url_naming) {
			$params = $this->getSpeakerParms($keyword);
		} elseif ($category==$appSettings->video_url_naming) {
			$params = $this->getVideoParms($keyword);
		} elseif ($category==$appSettings->trip_url_naming) {
			$params = $this->getTripParms($keyword);
		} elseif ($category==$appSettings->videos_url_naming) {
			$params = $this->getCategoryParms($keyword, "videos", CATEGORY_TYPE_VIDEO);
		} else {
			$params = $this->getCategoryParms($keyword, "search", CATEGORY_TYPE_BUSINESS);
			if (empty($params) || (!empty($params) && (isset($params["option"]) && $params["option"] != "com_jbusinessdirectory"))) {
				$params = $this->getBusinessListingParms($keyword);
			}
		}

		if (!empty($params)) {
			$jinput = JFactory::getApplication()->input;
			foreach ($params as $key => $param) {
				$jinput->set($key, $param);
			}
		}

		return $params;
	}

	function getMappingParams($mapping){
	    $params = array();
	    $params["option"] = "com_jbusinessdirectory";
	    $params["directory"] = "1";
	    $params["controller"] = $mapping;
	    $params["view"] = $mapping;
	    
	    return $params;
	}
	
	function getBusinessListingParms($companyLink){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();

		$company = null;
		
		if($appSettings->add_url_id == 1){
			$companyId = substr($companyLink, 0, strpos($companyLink, "-"));
			$companyAlias = substr($companyLink, strpos($companyLink, "-")+1);
			$companyAlias = urldecode($companyAlias);

			if(!is_numeric($companyId)){
				return;
			}

			$query= "SELECT * FROM `#__jbusinessdirectory_companies` c where c.id = $companyId";
			$db->setQuery($query, 0, 1);
			$company = $db->loadObject();
		}else{
			$companyAlias = urldecode($companyLink);
			$companyAlias = $db->escape($companyAlias);
			$query= "SELECT * FROM `#__jbusinessdirectory_companies` c where c.alias = '$companyAlias'";
			$db->setQuery($query, 0, 1);
			$company = $db->loadObject();
		}

		if(!empty($company) && strcmp($companyAlias, $company->alias)==0 && !empty($company->alias)){
			$params["option"] = "com_jbusinessdirectory";
			$params["directory"] = "1";
			$params["controller"] = "companies";
			$params["task"] = "showcompany";
			$params["companyId"] = $company->id;
			$params["view"] = "companies";
		}else{
			return null;
		}

		return $params;
	}

	function getCategoryParms($categoryLink, $type, $categoryType){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();

		$category = null;
		if($appSettings->add_url_id == 1){
			$categoryId = substr($categoryLink, 0, strpos($categoryLink, "-"));
			$categoryAlias = substr($categoryLink, strpos($categoryLink, "-")+1);
			$categoryAlias = urldecode($categoryAlias);
				
			if(!is_numeric($categoryId) || empty($categoryId)){
				return;
			}
			$query= "SELECT * FROM #__jbusinessdirectory_categories c where c.id = $categoryId ";
			$db->setQuery($query, 0, 1);
			$category = $db->loadObject();
		}else{
			$categoryAlias = urldecode($categoryLink);
			$categoryAlias = $db->escape($categoryAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_categories c where c.type=$categoryType and c.alias = '$categoryAlias' ";
			$db->setQuery($query, 0, 1);
			$category = $db->loadObject();
		}

		if(!empty($category) && strcmp(strtolower($categoryAlias), (strtolower($category->alias)))==0 && !empty($category->alias)){
			$params["option"] = "com_jbusinessdirectory";
			$params["controller"] = $type;
			$params["directory"] = "1";
				
			$params["menuCategoryId"] = $category->id;
			$params["view"] = $type;
			
			$menuItem = null;
			switch($type){
				case "search":
                    if($appSettings->search_type == 1 ){
                        $params["categoryId"] = $category->id;
                    }
					break;
				case "offers":
                    if($appSettings->offer_search_type == 1 ){
                        $params["categoryId"] = $category->id;
                    }
					break;
				case "events":
                    if($appSettings->event_search_type == 1 ){
                        $params["categoryId"] = $category->id;
                    }
					break;
			}
		}

		return $params;
	}
	
	function getCityParams($keyword){
		$params = array();
		if(!empty($keyword)){
			$params["option"] = "com_jbusinessdirectory";
			$params["directory"] = "1";
			$params["controller"] = "search";
			$params["citySearch"] = $keyword;
			$params["view"] = "search";
			
		}
		return $params;
	}

	function getRegionParams($keyword){
		$params = array();
		if(!empty($keyword)){
			$params["option"] = "com_jbusinessdirectory";
			$params["directory"] = "1";
			$params["controller"] = "search";
			$params["regionSearch"] = $keyword;
			$params["view"] = "search";
			
		}
		return $params;
	}
	

	function getOffersParms($keyword){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();

		$offer = null;
		if($appSettings->add_url_id == 1){
			$offerId = substr($keyword, 0, strpos($keyword, "-"));
			$offerAlias = substr($keyword, strpos($keyword, "-")+1);
			$offerAlias = urldecode($offerAlias);
			$offerAlias = trim($offerAlias);
				
			if(!is_numeric($offerId) || empty($offerId)){
				return;
			}

			$db = JFactory::getDBO();
			$query= "SELECT * FROM #__jbusinessdirectory_company_offers o where o.id = $offerId ";

			$db->setQuery($query, 0, 1);
			$offer = $db->loadObject();
		}else{
			$offerAlias = urldecode($keyword);
			$offerAlias = $db->escape($offerAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_company_offers o where o.alias = '$offerAlias' ";
			$db->setQuery($query, 0, 1);
			$offer = $db->loadObject();
		}

		if(!empty($offer) && strcmp(strtolower($offerAlias), (strtolower($offer->alias)))==0  && !empty($offer->alias)){
			$params["option"] = "com_jbusinessdirectory";
			$params["directory"] = "1";
			$params["controller"] = "offer";
			$params["offerId"] = $offer->id;
			$params["view"] = "offer";
			
		}

		return $params;
	}

	function getEventParms($keyword){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();

		$event = null;
		if($appSettings->add_url_id == 1){
			$eventId = substr($keyword, 0, strpos($keyword, "-"));
			$eventAlias = substr($keyword, strpos($keyword, "-")+1);
			$eventAlias = urldecode($eventAlias);
			$eventAlias = trim($eventAlias);

			if(!is_numeric($eventId)){
				return;
			}

			$db = JFactory::getDBO();
			$query= "SELECT * FROM #__jbusinessdirectory_company_events e where e.id = $eventId ";

			$db->setQuery($query, 0, 1);
			$event = $db->loadObject();

		}else{
			$eventAlias = urldecode($keyword);
			$eventAlias = $db->escape($eventAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_company_events e where e.alias = '$eventAlias' ";
			$db->setQuery($query, 0, 1);
			$event = $db->loadObject();
				
		}

		if(!empty($event) && strcmp(strtolower($eventAlias), (strtolower($event->alias)))==0 && !empty($event->alias)){
			$params["directory"] = "1";
			$params["controller"] = "event";
			$params["eventId"] = $event->id;
			$params["view"] = "event";
		}

		return $params;
	}
	
	function getConferenceParms($keyword){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
	
		$conference = null;
		if($appSettings->add_url_id == 1){
			$conferenceId = substr($keyword, 0, strpos($keyword, "-"));
			$conferenceAlias = substr($keyword, strpos($keyword, "-")+1);
			$conferenceAlias = urldecode($conferenceAlias);
			$conferenceAlias = trim($conferenceAlias);
	
			if(!is_numeric($conferenceId)){
				return;
			}
			$query= "SELECT * FROM #__jbusinessdirectory_conferences c where c.id = $conferenceId ";
	
			$db->setQuery($query, 0, 1);
			$conference = $db->loadObject();
	
		}else{
			$conferenceAlias = urldecode($keyword);
			$conferenceAlias = $db->escape($conferenceAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_conferences c where c.alias = '$conferenceAlias' ";
			$db->setQuery($query, 0, 1);
			$conference = $db->loadObject();
		}
	
		if(!empty($conference) && strcmp(strtolower($conferenceAlias), (strtolower($conference->alias)))==0 && !empty($conference->alias)){
			$params["option"] = "com_jbusinessdirectory";
			$params["controller"] = "conference";
			$params["directory"] = "1";
			$params["conferenceId"] = $conference->id;
			$params["view"] = "conference";

		}
	
		return $params;
	}
	
	function getConferenceSessionParms($keyword){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
		
		$conferenceSession = null;
		if($appSettings->add_url_id == 1){
			$cSessionId = substr($keyword, 0, strpos($keyword, "-"));
			$cSessionAlias = substr($keyword, strpos($keyword, "-")+1);
			$cSessionAlias = urldecode($cSessionAlias);
			$cSessionAlias = trim($cSessionAlias);
			
			if(!is_numeric($cSessionId)){
				return;
			}
	
			$db = JFactory::getDBO();
			$query= "SELECT * FROM #__jbusinessdirectory_conference_sessions cs where cs.id = $cSessionId ";
	
			$db->setQuery($query, 0, 1);
			$conferenceSession = $db->loadObject();
	
		}else{
			$cSessionAlias = urldecode($keyword);
			$cSessionAlias = $db->escape($cSessionAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_conference_sessions cs where cs.alias = '$cSessionAlias' ";
			$db->setQuery($query, 0, 1);
			$conferenceSession = $db->loadObject();
		}
	
		
		if(!empty($conferenceSession) && strcmp(strtolower($cSessionAlias), (strtolower($conferenceSession->alias)))==0 && !empty($conferenceSession->alias)){
			$params["option"] = "com_jbusinessdirectory";
			$params["controller"] = "conferencesession";
			$params["cSessionId"] = $conferenceSession->id;
			$params["directory"] = "1";
			$params["view"] = "conferencesession";
		}

		return $params;
	}

	function getSpeakerParms($keyword){
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
	
		$speaker = null;
		if($appSettings->add_url_id == 1){
			$speakerId = substr($keyword, 0, strpos($keyword, "-"));
			$speakerAlias = substr($keyword, strpos($keyword, "-")+1);
			$speakerAlias = urldecode($speakerAlias);
			$speakerAlias = trim($speakerAlias);
	
			if(!is_numeric($speakerId)){
				return;
			}
			$query= "SELECT * FROM #__jbusinessdirectory_conference_speakers s where s.id = $speakerId ";
	
			$db->setQuery($query, 0, 1);
			$speaker = $db->loadObject();
		}else{
			$speakerAlias = urldecode($keyword);
			$speakerAlias = $db->escape($speakerAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_conference_speakers s where s.alias = '$speakerAlias' ";
			$db->setQuery($query, 0, 1);
			$speaker = $db->loadObject();
		}
	
		if(!empty($speaker) && strcmp(strtolower($speakerAlias), (strtolower($speaker->alias)))==0 && !empty($speaker->alias)){
			$params["option"] = "com_jbusinessdirectory";
			$params["directory"] = "1";
			$params["controller"] = "speaker";
			$params["speakerId"] = $speaker->id;
			$params["view"] = "speaker";

		}
	
		return $params;
	}


	/**
	 * Search for the video and if exists will create the params
	 *
	 * @param [type] $keyword
	 * @return void
	 */
	public function getVideoParms($keyword) {
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
	
		$video = null;
		if ($appSettings->add_url_id == 1) {
			$videoId = substr($keyword, 0, strpos($keyword, "-"));
			$videoAlias = substr($keyword, strpos($keyword, "-")+1);
			$videoAlias = urldecode($videoAlias);
			$videoAlias = trim($videoAlias);
			$videoAlias = $this->cleanAlias($videoAlias);

			if (!is_numeric($videoId)) {
				return;
			}
			$query= "SELECT * FROM #__jbusinessdirectory_videos v where v.id = $videoId ";
	
			$db->setQuery($query, 0, 1);
			$video = $db->loadObject();
		} else {
			$videoAlias = urldecode($keyword);
			$videoAlias = $db->escape($videoAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_videos v where v.alias = '$videoAlias' ";
			$db->setQuery($query, 0, 1);
			$video = $db->loadObject();
		}
	
		if (!empty($video) && strcmp(strtolower($videoAlias), (strtolower($video->alias)))==0 && !empty($video->alias)) {
			$params["option"] = "com_jbusinessdirectory";
			$params["controller"] = "video";
			$params["videoId"] = $video->id;
			$params["view"] = "video";
			$params["layout"] = "default";
			$params["directory"] = "1";
		}

		return $params;
	}

	/**
	 * Search for the trip and if exists will create the params
	 *
	 * @param [type] $keyword
	 * @return void
	 */
	public function getTripParms($keyword) {
		$params = array();
		$db = JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
	
		$trip = null;
		if ($appSettings->add_url_id == 1) {
			$tripId = substr($keyword, 0, strpos($keyword, "-"));
			$tripAlias = substr($keyword, strpos($keyword, "-")+1);
			$tripAlias = urldecode($tripAlias);
			$tripAlias = trim($tripAlias);
			$tripAlias = $this->cleanAlias($tripAlias);

			if (!is_numeric($tripId)) {
				return;
			}
			$query= "SELECT * FROM #__jbusinessdirectory_trips t where t.id = $tripId ";
	
			$db->setQuery($query, 0, 1);
			$trip = $db->loadObject();
		} else {
			$tripAlias = urldecode($keyword);
			$tripAlias = $db->escape($tripAlias);
			$query= "SELECT * FROM #__jbusinessdirectory_trips t where t.alias = '$tripAlias' ";
			$db->setQuery($query, 0, 1);
			$trip = $db->loadObject();
		}
	
		if (!empty($trip) && strcmp(strtolower($tripAlias), (strtolower($trip->alias)))==0 && !empty($trip->alias)) {
			$params["option"] = "com_jbusinessdirectory";
			$params["controller"] = "trip";
			$params["tripId"] = $trip->id;
			$params["view"] = "trip";
			$params["layout"] = "default";
			$params["directory"] = "1";
		}
	
		return $params;
	}

	/**
     * Remove all unwanted parameters from the alias
     *
     * @param $alias
     * @return false|string
     */
	private function cleanAlias($alias){
        if(strpos($alias,"&")!== false){
            $alias=substr($alias,0,strpos($alias,"&"));
        }

        if(strpos($alias,"?")!== false){
            $alias=substr($alias,0,strpos($alias,"?"));
        }

        return $alias;
    }
	
}
