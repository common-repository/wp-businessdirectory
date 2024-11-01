<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

use MVC\Factory;
use MVC\HTML\HTMLHelper;

/**
 * JBD utility class
 *
 *
 * @author George
 *
 */
#[\AllowDynamicProperties]
class JBusinessUtil {
	public $applicationSettings ;

	private function __construct() {
	}

	/**
	 * Singletion function for retrieving the unique instance
	 *
	 * @return JBusinessUtil
	 */
	public static function getInstance() {
		static $instance;
		if ($instance === null) {
			$instance = new JBusinessUtil();
		}
		return $instance;
	}

	/**
	 * Retrieve the application setting instance
	 *
	 * @return stdClass application settings
	 */
	public static function getApplicationSettings() {
		$instance = JBusinessUtil::getInstance();

		if (!isset($instance->applicationSettings)) {
			$instance->applicationSettings = self::getAppSettings();
		}
		return $instance->applicationSettings;
	}

	/**
	 * Get the general settings from the database
	 *
	 * @return stdClass app settings
	 */
	private static function getAppSettings() {
		$db		= JFactory::getDBO();
		$query	= "	SELECT fas.*
	                  FROM #__jbusinessdirectory_application_settings fas";

		//dump($query);
		$db->setQuery($query);
		$appSettings =  $db->loadObjectList();
		
		$app = new stdClass();
		foreach ($appSettings as $setting) {
			$app->{$setting->name} = $setting->value;
		}

		$query	= "SELECT fas.*
	                  FROM #__jbusinessdirectory_date_formats fas
	                WHERE fas.id ='".$app->date_format_id."'" ;
		$db->setQuery($query);
		$date =  $db->loadObject();
		foreach ($date as $key=>$item) {
			$app->{$key} = $item;
		}

		$query	= "SELECT fas.*
	                  FROM #__jbusinessdirectory_currencies fas
	                WHERE fas.currency_id ='".$app->currency_id."'" ;
		$db->setQuery($query);
		$date =  $db->loadObject();
		foreach ($date as $key=>$item) {
			if ($key=="currency_symbol" && !empty($app->currency_symbol)) {
				continue;
			}
				
			$app->{$key} = $item;
		}

		$app->url_fields = explode(",", $app->url_fields);
		$app->search_categories = explode(",", $app->search_categories);
		
		if (!empty($app->marker_size)) {
			$size = explode(';', $app->marker_size);
			$tmp = new stdClass();
			$tmp->width = $size[0];
			$tmp->height = $size[1];

			$app->marker_size = $tmp;
		}

		if(!empty($app->vat_config)){
			$app->vat_configuration = json_decode($app->vat_config);
		}else{
			$app->vat_configuration = array();
		}

		return $app;
	}

	/**
	 * Retrieve a property
	 *
	 * @param [type] $prop
	 * @return void
	 */
	public static function getProperty($prop, $default=null) {
		$instance = JBusinessUtil::getInstance();

		if (!isset($instance->properties)) {
			$instance->properties = self::loadProperties();
		}

		if(isset($instance->properties[$prop])){
			return $instance->properties[$prop];
		}else{
			return $default;
		}

	}

	/**
	 * Set a property
	 * 
	 */
	public static function setProperty($prop, $value) {
		$db	= JFactory::getDBO();
		
		$query = "insert into #__jbusinessdirectory_properties(name, value) values ('$prop', '$value')";
		$query = $query." ON DUPLICATE KEY UPDATE `value`=values(`value`)"; 
		
		$db->setQuery($query);
		
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		} 

		$instance = JBusinessUtil::getInstance();
		if (isset($instance->properties)) {
			$instance->properties[$prop] = $value;
		}

		return true;
	}

	/**
	 * Load the properties from the database
	 * 
	 *
	 * @return void
	 */
	private static function loadProperties() {
		$db		= JFactory::getDBO();
		$query	= "SELECT * FROM #__jbusinessdirectory_properties" ;
		
		$db->setQuery($query);
		$result = array();
		$properties =  $db->loadObjectList();
		foreach ($properties as $prop) {
			$result[$prop->name] = $prop->value;
		}

		$instance = JBusinessUtil::getInstance();
		$instance->properties = $result;
	}


	/**
	 * Retrieve the site configs
	 *
	 * @return JConfig
	 */
	public static function getSiteConfig() {
		$config = new stdClass();

		$config->mailfrom = get_bloginfo("admin_email");
		$config->fromname = get_bloginfo("name");
		$config->tmp_path = WPBD_UPLOAD_DIR."/tmp";
		$config->sitename = get_bloginfo("name");
		$config->sef = true;
		$config->sef_rewrite = true;
		$config->metadesc = "";

		return $config;
	}
	
	/**
	 * Set page meta data
	 *
	 * @param $title
	 * @param $description
	 * @param $keywords
	 * @param $useMenuParams
	 * @return boolean
	 */
	public static function setMetaData($title, $description, $keywords, $useMenuParams = false) {
		global $wp_query;
		$metaData = "";

		$metaData .= PHP_EOL . '<meta property="keywords" content="' . $keywords . '"/>';
		$metaData .= PHP_EOL . '<meta property="description" content="' . $description . '"/>';

		$wp_query->pageTitle = $title;
		$wp_query->metaDataDir = $metaData;

		return true;
	}

	/**
	 * Set Facebook meta data
	 *
	 * @param $title
	 * @param $description
	 * @param $logo
	 * @param $url
	 *
	 * @return boolean
	 */

	
	/**
	 * Set Facebook meta data
	 *
	 * @param $title string title meta
	 * @param $description string description
	 * @param $logo string logo
	 * @param $url string url
	 * @return boolean true when finished with success
	 */
	public static function setFacebookMetaData($title, $description, $image, $url) {
		global $wp_query;

		$metaData = "";

		$metaData .= PHP_EOL . '<meta property="og:title" content="' . $title . '"/>';
		$metaData .= PHP_EOL . '<meta property="og:description" content="' . $description . '"/>';

		if (!empty($image)) {
			$metaData .= PHP_EOL . '<meta property="og:image" content="' . BD_PICTURES_PATH . $image . '" /> ';
		}
		$metaData .= PHP_EOL . '<meta property="og:type" content="website"/>';
		$metaData .= PHP_EOL . '<meta property="og:url" content="' . $url . '"/>';
		$metaData .= PHP_EOL . '<meta property="og:site_name" content="' . get_bloginfo() . '"/>';

		$wp_query->facebookMetadata = $metaData;
	}

	/**
	 * Set canonical URL
	 *
	 * @param $url string url
	 * @return boolean true when finished with success
	 */
	public static function setCanonicalURL($url) {
		$document = JFactory::getDocument();

		//reset the previous canonical tag
		foreach ($document->_links as $key=> $value) {
			if (is_array($value)) {
				//dump($value);
				if (array_key_exists('relation', $value)) {
					if ($value['relation'] == 'canonical') {
						//the document link that contains the canonical url found and changed
						$document->_links[$url] = $value;
						unset($document->_links[$key]);
						break;
					}
				}
			}
		}
		
		global $wp_query;

		$metaData = "";

		$metaData .= PHP_EOL . '<link rel="canonical" href="'.$url.'"/>';
		
		$wp_query->metaData .= $metaData;
	
		return true;
	}

	/**
	 * Apply the robots meta tag
	 *
	 * @return void
	 */
	public static function applyRobotsMeta(){
        if(empty($config->robots))
            return;
		$config = self::getSiteConfig();
		$document = JFactory::getDocument();
		$document->setMetadata('robots', $config->robots);

		return true;
	}

	/**
	 * Retrieve the current editor
	 *
	 * @return \MVC\Editor\Editor|string
	 */
	public static function getEditor() {
		if (defined('JVERSION')) {
			$joomlaVersion = (int) JVERSION;
		} else {
			$j = new JVersion();
			$joomlaVersion = (int) $j->getShortVersion();
		}

		if ($joomlaVersion == 3) {
			$editor = JFactory::getEditor();
		} else {
			$editor = self::getSiteConfig()->editor;
			$editor = JEditor::getInstance($editor);
		}

		return $editor;
	}

	/**
	 * Retrieve the current user or an user by id
	 *
	 * @param $id int id of the user if needed specific user
	 * @return \MVC\User\User|null
	 */
	public static function getUser($id = null) {
		$user = null;

		if($id === 0 || $id === '0'){
			return null;
		}

	    if(!empty($id)){
	        $user = get_user_by('id', $id);
	    }else{
	        $user = wp_get_current_user();
		}

		if(!empty($user)){
			$user->name = $user->display_name;
		}else{
			$user = new stdClass;
			$user->ID = 0;
			$user->name = "";
			$user->display_name = "";
		}

		return $user;
	}

	/**
	 * Generate the menu parameter based on the active menu item
	 *
	 * @return string menu item
	 * @throws Exception
	 */
	public static function getActiveMenuItem() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$menuItem = "";

		return $menuItem;
	}


	/**
	 * Retrieve the page title based on the menu item settings
	 * 
	 * @param unknown $defaultTitle
	 * @return unknown
	 */
	public static function getPageTitle($defaultTitle){
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if (isset($activeMenu)) {
			$menuitem = JFactory::getApplication()->getMenu()->getItem($activeMenu->id);
			$params = $menuitem->getParams();
			
			//set page title
			if(!empty($params) && $params->get('page_title') != '' ) {
				$title = $params->get('page_title', '');
				return $title;
			}
		}
		
		return $defaultTitle;
	}
	
	/**
	 * Check if a user has access to a certain view
	 *
	 * @param $permission string permision that need to be checked
	 * @param $view string view to check in
	 * @throws Exception
	 */
	public static function checkPermissions($permission, $view) {
		$menuItemId = self::getActiveMenuItem();
		$appSettings = self::getApplicationSettings();

		$user = JBusinessUtil::getUser();
		if ($user->ID == 0) {
			$redirect = JRoute::_("index.php?option=com_jbusinessdirectory&view=$view" . $menuItemId);
			$login_url = wp_login_url($redirect);
			wp_redirect($login_url);
			exit();
		}

		$actions = JBusinessDirectoryHelper::getActions();
		
		if (!$actions->get($permission) && $appSettings->front_end_acl) {
			$app = JFactory::getApplication();
			if($view != "useroptions"){
				$app->enqueueMessage(JText::_("LNG_ACCESS_RESTRICTED"),'warning');
				$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false));
			}else{
				$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=userdashboard', false));
			}
		}
	}

	/**
	 * Include script files
	 * @param $file string path of the file to include
	 */
	public static function enqueueScript($file) {
		wp_enqueue_script($file, BD_ASSETS_FOLDER_PATH . $file, array('jquery'), null, false);
	}

	/**
	 * Include css files
	 *
	 * @param $file string path of the file to include
	 */
	public static function enqueueStyle($file) {
		wp_enqueue_style($file, BD_ASSETS_FOLDER_PATH . $file);
	}

	/**
	 * Include the default css libraries
	 */
	public static function includeCSSLibraries() {
		JBusinessUtil::enqueueStyle('css/jbd-style.css');
		JBusinessUtil::enqueueStyle('css/common.css');

		if(file_exists(BD_ASSETS_FOLDER_PATH.'css/custom.css')){
			JBusinessUtil::enqueueStyle('css/custom.css');
		}

		JBusinessUtil::enqueueStyle('css/line-awesome.css');

		$appSettings = self::getApplicationSettings();
		if ($appSettings->image_display==2) {
			$document = JFactory::getDocument();
			// Add styles
			$style = '.jbd-container .jitem-card img, .jbd-container .place-card img { object-fit: contain !important; }';
			$document->addStyleDeclaration($style);
		}


		$joomlaVersion = (int) 3;

		if ($joomlaVersion == 3) {
			JBusinessUtil::enqueueStyle('css/jbd-style_v3.css');
		}
		if ($joomlaVersion == 4) {
			JBusinessUtil::enqueueStyle('css/jbd-style_v4.css');
		}
	}

	/**
	 * Sanize all request variables
	 *
	 * @throws Exception
	 */
	public static function sanitizeRequest() {
		$jinput = JFactory::getApplication()->input;
		
		$params = $_REQUEST;
		foreach ($params as $name=>$value) {
			if (strpos($name, "description")===false) {
				$value= str_replace('"', "", $value);
				$jinput->set($name, $value);
			}
		}
	}

	/**
	 * Load all available classes on classes folder
	 */
	public static function loadClasses() {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		//load payment processors
		$classpath = BD_CLASSES_PATH.DS.'payment'.DS.'processors';
		if(file_exists($classpath)){
			foreach (JFolder::files($classpath) as $file) {
				JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
			}
		}

		//load payment processors
		$classpath = BD_CLASSES_PATH.DS.'payment';
		foreach (JFolder::files($classpath) as $file) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}

		//load services
		$classpath = BD_CLASSES_PATH.DS.'services';
		foreach (JFolder::files($classpath) as $file) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}

		//load elasticsearch
		$classpath = BD_CLASSES_PATH.DS.'elasticsearch';
		if(file_exists($classpath)){
			foreach (JFolder::files($classpath) as $file) {
				JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
			}
		}

		$classpath = BD_CLASSES_PATH.DS.'elasticsearch'.DS.'indexer';
		if(file_exists($classpath)){
			foreach (JFolder::files($classpath) as $file) {
				JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
			}
		}

	}

	/**
	 * Retrieve the data from a specific URL based on curl
	 *
	 * @param $url string url that need to get the data from
	 * @return bool|string
	 */
	public static function getURLData($url, $params = null) {
		$data = null;
		$response = wp_remote_get($url, $params);
		if (is_array($response)) {
			$header = $response['headers']; // array of http header lines
			$data = $response['body']; // use the content
		}
		return $data;
	}

	/**
	 * Translated the zip code to map coordinates based on Google geolocation
	 *
	 * @param $zipCode string zip code
	 * @return array|null array with lat and lang or nothing if any error accour
	 */
	public static function getCoordinates($zipCode) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$mapType = $appSettings->map_type;

		$limitCountries = array();
		$location = null;

		if (!empty($appSettings->country_ids)) {
			$countryIDs = explode(",", $appSettings->country_ids);

			foreach ($countryIDs as $countryID) {
				$country = self::getCountry($countryID);
				array_push($limitCountries, $country->country_code);
			}
		}

		$countryParam = "";
		if (!empty($limitCountries)) {
			if ($mapType == MAP_TYPE_GOOGLE) {
				$countries = array();
				foreach ($limitCountries as $country) {
					$countries[]="country:".$country;
				}

				$countries = implode("|", $countries);
				$countryParam ="&components=".$countries;
			} elseif ($mapType == MAP_TYPE_OSM) {
				$countryParam = "&countrycodes=".implode(',', $limitCountries);
			}
		}

		$key="";
		if (!empty($appSettings->google_map_key)) {
			$key="&key=".$appSettings->google_map_key;
			if (!empty($appSettings->google_map_key_zipcode)) {
				$key="&key=".$appSettings->google_map_key_zipcode;
			}
		}

		$url ="https://maps.googleapis.com/maps/api/geocode/json?sensor=false$key$countryParam&address=".urlencode($zipCode);

		if ($mapType == MAP_TYPE_OSM) {
			$url = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=".urlencode($zipCode).$countryParam;
		}

		$data = self::getURLData($url);
		$search_data = json_decode($data);

		$lat = "";
		$lng = "";
		if (!empty($search_data)) {
			if ($mapType == MAP_TYPE_GOOGLE && !empty($search_data->results)) {
				$lat = $search_data->results[0]->geometry->location->lat;
				$lng = $search_data->results[0]->geometry->location->lng;

				if (!empty($limitCountries)) {
					foreach ($search_data->results as $result) {
						$country = "";
						foreach ($result->address_components as $addressCmp) {
							if (!empty($addressCmp->types) && $addressCmp->types[0] == "country") {
								$country = $addressCmp->short_name;
							}
						}
						if (in_array($country, $limitCountries)) {
							$lat = $result->geometry->location->lat;
							$lng = $result->geometry->location->lng;
						}
					}
				}
			} elseif ($mapType == MAP_TYPE_OSM) {
				$lat = $search_data[0]->lat;
				$lng = $search_data[0]->lon;

				if (!empty($limitCountries)) {
					$country = "";
					if (!empty($search_data[0]->address->country)) {
						$country = strtoupper($search_data[0]->address->country_code);
					}

					if (in_array($country, $limitCountries)) {
						$lat = $search_data[0]->lat;
						$lng = $search_data[0]->lon;
					}
				}
			}
		
			$location =  array();
			$location["latitude"] = $lat;
			$location["longitude"] = $lng;
		}
		
		return $location;
	}

	/**
	 * Prepare map locations
	 */
	public static function prepareCompaniesMapLocations($companies){

		$company_locations = array();
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		$newTab = ($appSettings->open_listing_on_new_tab)?" target='_blank'":"";

		$user = JBusinessUtil::getUser();
		$showData = !($user->ID==0 && $appSettings->show_details_user == 1);

		$layout_style_5 = false;
		if ($appSettings->search_result_view == 5 && (empty($params) || empty($params->get('showMap')))) {
			$layout_style_5 = true;
		}

		$index = 1;
		foreach ($companies as $company) {
			$tmp    = array();
			$marker = 0;
		
			if ($company->featured) {
				if (!empty($appSettings->feature_map_marker)) {
					$marker = BD_PICTURES_PATH. $appSettings->feature_map_marker;
				} 
			} 
			
			if (!empty($company->categoryMaker)) {
				$marker = BD_PICTURES_PATH . $company->categoryMaker;
			}
		
			$contentPhone  = ($showData && !empty($company->phone) && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$appSettings->enable_packages)) ?
				'<div class="info-phone"><i class="la la-phone"></i> ' . htmlspecialchars($company->phone, ENT_QUOTES) . '</div>' : "";
			
			$address = $showData?JBusinessUtil::getAddressText($company):"";
			
			
			if(isset($appSettings->map_info_box_style) && $appSettings->map_info_box_style == 1) {
				$contentString = '<div class="info-box">' .
				'<div class="title">' . htmlspecialchars($company->name) . '</div>' .
				'<div class="info-box-content">' .
				'<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . $address . '</div>' .
				$contentPhone .
				'<a '. $newTab .' href="' . htmlspecialchars(JBusinessUtil::getCompanyLink($company), ENT_QUOTES) . '"><i class="la la-external-link"></i> ' . htmlspecialchars(JText::_("LNG_MORE_INFO"), ENT_QUOTES) . '</a>' .
				'</div>' .
				'<div class="info-box-image">' .
				(!empty($company->logoLocation) ? '<img src="' . BD_PICTURES_PATH . htmlspecialchars($company->logoLocation, ENT_QUOTES) . '" alt="' . htmlspecialchars($company->name) . '">' : "") .
				'</div>' .
				'</div>';
			} else {
				$contentString = '<div class="info-box info-box-style-2">' .
				'<div class="title">' . htmlspecialchars($company->name) . '</div>' .
				'<div class="info-box-content">' .
				'<div class="mb-1" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . $address . '</div>' .
				$contentPhone .
				'<div class="info-box-image">' .
				(!empty($company->logoLocation) ? '<img src="' . BD_PICTURES_PATH . htmlspecialchars($company->logoLocation, ENT_QUOTES) . '" alt="' . htmlspecialchars($company->name) . '">' : "") .
				'</div>' .
				'<a class="btn btn-secondary btn-sm w-100 mt-2" '. $newTab .' href="' . htmlspecialchars(JBusinessUtil::getCompanyLink($company), ENT_QUOTES) . '">' . htmlspecialchars(JText::_("LNG_MORE_INFO"), ENT_QUOTES) . '<i class="la la-angle-right"></i> </a>' .
				'</div>' .
				'</div>';
			}
		
		
			if ($layout_style_5) {
				$contentString = intval($company->id);
			}
		
			$searchSecondaries = false;
			if (!empty($company->latitude) && !empty($company->longitude) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP, $company->packageFeatures) || !$appSettings->enable_packages)) {
				$tmp['title']        = htmlspecialchars($company->name);
				$tmp['latitude']     = $company->latitude;
				$tmp['latitude']     = $company->latitude;
				$tmp['longitude']    = $company->longitude;
				$tmp['zIndex']       = (int) $company->id;
				$tmp['content']      = $contentString;
				$tmp[]               = $index;
				$tmp['marker']       = $marker;
		
				if (isset($company->distance)) {
					$distance = $company->distance;
					$secondaryDistances = !empty($company->secondaryDistances) ? explode(',', $company->secondaryDistances) : array(0);
					$secDistance = min($secondaryDistances);
		
					if (!empty($secDistance)) {
						$distance = $distance < $secDistance ? $distance : $secDistance;
					}
		
					$searchSecondaries = true;
					if ($distance == $company->distance) {
						$tmp['in_range'] = 1;
						$searchSecondaries = false;
					}
				}
		
				$company_locations[] = $tmp;
			}
		
			if (!empty($company->latitude) && !empty($company->longitude) && !empty($company->locations) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP, $company->packageFeatures) && in_array(SECONDARY_LOCATIONS, $company->packageFeatures) || !$appSettings->enable_packages)) {
				$locations = explode("#", $company->locations);
				$distances = isset($company->secondaryDistances) ? explode(',', $company->secondaryDistances) : array();
		
				foreach ($locations as $k => $location) {
					$tmp = array();
					$loc = explode("|", $location);
		
					$address = $showData?JBusinessUtil::getLocationAddressText($loc[2], $loc[3], $loc[9], $loc[4], $loc[5], $loc[8], $loc[6], $company->publish_only_city):"";
		
					$contentPhoneLocation = ($showData && !empty($loc[7]) && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$appSettings->enable_packages)) ?
						'<div class="info-phone"><i class="la la-phone"></i> ' . htmlspecialchars($loc[7], ENT_QUOTES) . '</div>' : "";
		
					

						if($appSettings->map_info_box_style == 1) {
							$contentStringLocation = '<div class="info-box">' .
								'<div class="title">' . htmlspecialchars($company->name) . '</div>' .
								'<div class="info-box-content">' .
								'<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . htmlspecialchars($address, ENT_QUOTES) . '</div>' .
								$contentPhoneLocation .
								'<a '. $newTab .' href="' . htmlspecialchars(JBusinessUtil::getCompanyLink($company), ENT_QUOTES) . '"><i class="la la-external-link"></i> ' . htmlspecialchars(JText::_("LNG_MORE_INFO"), ENT_QUOTES) . '</a>' .
								'</div>' .
								'<div class="info-box-image">' .
								(!empty($company->logoLocation) ? '<img src="' . BD_PICTURES_PATH . htmlspecialchars($company->logoLocation, ENT_QUOTES) . '" alt="' . htmlspecialchars($company->name) . '">' : "") .
								'</div>' .
								'</div>';
						} else {
							$contentStringLocation = '<div class="info-box info-box-style-2">' .
								'<div class="title">' . htmlspecialchars($company->name) . '</div>' .
								'<div class="info-box-content">' .
								'<div class="mb-1" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . htmlspecialchars($address, ENT_QUOTES) . '</div>' .
								$contentPhoneLocation .
								'<div class="info-box-image">' .
								(!empty($company->logoLocation) ? '<img src="' . BD_PICTURES_PATH . htmlspecialchars($company->logoLocation, ENT_QUOTES) . '" alt="' . htmlspecialchars($company->name) . '">' : "") .
								'</div>' .
								'<a class="btn btn-secondary btn-sm w-100 mt-2" '. $newTab .' href="' . htmlspecialchars(JBusinessUtil::getCompanyLink($company), ENT_QUOTES) . '">' . htmlspecialchars(JText::_("LNG_MORE_INFO"), ENT_QUOTES) . '<i class="la la-angle-right"></i> </a>' .
								'</div>' .
								'</div>';
						}
						
		
					if ($layout_style_5) {
						$contentStringLocation = intval($company->id);
					}
		
					$tmp['title']     = htmlspecialchars($company->name);
					$tmp['latitude']  = $loc[0];
					$tmp['longitude'] = $loc[1];
					$tmp['zIndex']    = (int) $company->id;
					$tmp['content']   = $contentStringLocation;
					$tmp[]            = $index;
					$tmp['marker']    = $marker;
		
					if ($searchSecondaries) {
						if ($distances[$k] == $secDistance) {
							$tmp['in_range'] = 1;
						}
					}
		
					if(!empty($tmp['latitude']) && !empty($tmp['longitude'])){
						$company_locations[] = $tmp;
					}
				}
			}
		
			$index++;
		}

		return $company_locations;
	}

	/**
	 * Prepare map locations
	 */
	public static function prepareOffersMapLocations($offers){
		
		$db = JFactory::getDBO ();
		$offer_locations = array ();

		$index = 1;
		foreach ( $offers as $offer ) {
			//if offer module is assigned on directory or events
			if (!isset($offer->subject)){
				$offer->subject = "";
			}
			$tmp = array ();
			$marker = 0;
			if (!empty($offer->categoryMaker)) {
				$marker = BD_PICTURES_PATH . $offer->categoryMaker;
			}
			
			$contentString = '<div class="info-box">' . '<div class="title">' . htmlspecialchars($offer->subject) . '</div>' . '<div class="info-box-content">' . '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' .  JBusinessUtil::getAddressText ($offer) . '</div>';
			if (!empty($offer->phone)) {
				$contentString .= '<div class="info-phone"><i class="la la-phone"></i> ' . $db->escape($offer->phone) . '</div>';
			}

			$contentString .= '<a href="' . $db->escape(JBusinessUtil::getOfferLink ($offer->id, $offer->alias)) . '"><i class="la la-external-link"></i> ' . $db->escape(JText::_("LNG_MORE_INFO", true)) . '</a>' . '</div>' . '<div class="info-box-image">' . (!empty($offer->picture_path) ? '<img src="' . BD_PICTURES_PATH . $offer->picture_path . '" alt="' . htmlspecialchars($offer->subject) . '">' : "") . '</div>' . '</div>';
			
			if (! empty ( $offer->latitude ) && ! empty ( $offer->longitude )) {
				$tmp ['title'] = htmlspecialchars($offer->subject);
				$tmp ['latitude'] = $offer->latitude;
				$tmp ['longitude'] = $offer->longitude;
				$tmp ['zIndex'] = 4;
				$tmp ['content'] = $contentString;
				$tmp [] = $index;
				$tmp ['marker'] = $marker;
				$tmp ['in_range'] = 1;

				$offer_locations [] = $tmp;
			}
			
			$index ++;
		}

		return $offer_locations;
	}

	/**
	 * Prepare map locations
	 */
	public static function prepareEventsMapLocations($events){
		$db = JFactory::getDBO ();
		$event_locations = array();

		$index = 1;
		foreach ( $events as $event ) {
			$tmp = array ();
			$marker = 0;
			if (! empty ( $event->categoryMaker )) {
				$marker = BD_PICTURES_PATH . $event->categoryMaker;
			}

			$phone = !empty($event->phone) ? $event->phone : null;
			$event->phone = !empty($event->contact_phone) ? $event->contact_phone : $phone;
			$contentString = '<div class="info-box">' . '<div class="title">' . htmlspecialchars($event->name) . '</div>' . '<div class="info-box-content">' . '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . JBusinessUtil::getAddressText ( $event ) . '</div>';
			if (!empty($event->phone)) {
				$contentString .= '<div class="info-phone"><i class="la la-phone"></i> ' . htmlspecialchars($event->phone, ENT_QUOTES). '</div>';
			}

			$contentString .= '<a href="' . htmlspecialchars(JBusinessUtil::geteventLink ( $event->id, $event->alias )) . '"><i class="la la-external-link"></i> ' . $db->escape ( JText::_ ( "LNG_MORE_INFO", true ) ) . '</a>' . '</div>' . '<div class="info-box-image">' . (! empty ( $event->picture_path ) ? '<img src="' . BD_PICTURES_PATH . $event->picture_path . '" alt="' . htmlspecialchars($event->name) . '">' : "") . '</div>' . '</div>';
			
			if (! empty ( $event->latitude ) && ! empty ( $event->longitude )) {
				$tmp ['title'] = htmlspecialchars($event->name);
				$tmp ['latitude'] = $event->latitude;
				$tmp ['longitude'] = $event->longitude;
				$tmp ['zIndex'] = 4;
				$tmp ['content'] = $contentString;
				$tmp [] = $index;
				$tmp ['marker'] = $marker;
				$tmp ['in_range'] = 1;


				$event_locations [] = $tmp;
			}
			
			$index ++;
		}

		return $event_locations;
	}

	/**
	 * Retrieve the request IP address
	 */

	public static function getRequestIP(){
		$ip = isset($_SERVER['HTTP_CLIENT_IP'])
				? $_SERVER['HTTP_CLIENT_IP'] 
				: (isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
						? $_SERVER['HTTP_X_FORWARDED_FOR'] 
						: $_SERVER['REMOTE_ADDR']);

		return $ip;
	}

	/**
	 * Retrieve the country based on IP
	 *
	 * @param [type] $ip
	 * @return void
	 */
	public static function getCountryByIp($ip){

		if(empty($ip)){
			return null;
		}

		// Initialize cURL.
		$ch = curl_init();

		// Set the URL that you want to GET by using the CURLOPT_URL option.ip_address=5.15.227.246
		curl_setopt($ch, CURLOPT_URL, 'https://ipgeolocation.abstractapi.com/v1/?api_key=9231f25d6f954bfdb31553756942ef26&ip_address='.$ip);

		// Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		// Execute the request.
		$data = curl_exec($ch);

		// Close the cURL handle.
		curl_close($ch);

		if(!empty($data)){
			$data = json_decode($data);
			$countryCode = $data->country_code;
			$country = JBusinessUtil::getCountryByCode($countryCode);

			return $country;
		}else{
			$country = JBusinessUtil::getCountryByCode("CA");
			return $country;
		}

		return null;
	}

	/**
	 * Retrieve the latitude/longitude based on IP address
	 *
	 * @param [type] $ip
	 * @return void
	 */
	public static function getLocationByIp($ip){

		if(empty($ip)){
			return null;
		}

		// Initialize cURL.
		$ch = curl_init();

		// Set the URL that you want to GET by using the CURLOPT_URL option.ip_address=5.15.227.246
		curl_setopt($ch, CURLOPT_URL, 'https://ipgeolocation.abstractapi.com/v1/?api_key=9231f25d6f954bfdb31553756942ef26&ip_address='.$ip);

		// Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		// Execute the request.
		$data = curl_exec($ch);

		$err = curl_error($ch);

		// Close the cURL handle.
		curl_close($ch);

		if(!empty($data)){
			$data = json_decode($data);
			$location = new stdClass;
			$location->longitude = $data->longitude;
			$location->latitude = $data->latitude;

			return $location;
		}

		return null;
	}

	public static function getNearbyCitiesByLocation($latitude, $longitue){

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://wft-geo-db.p.rapidapi.com/v1/geo/locations/33.832213-118.387099/nearbyCities?radius=100",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTPHEADER => [
				"X-RapidAPI-Host: wft-geo-db.p.rapidapi.com",
				"X-RapidAPI-Key: e3ce7ec592msh94219a502cfa59ap16da95jsne8936b12a5e4"
			],
		]);

		$data = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if(!empty($data)){
			$data = json_decode($data);
		}
	}

	/**
	 * Calculate the difference between a date in the future (by number of days) and current date
	 *
	 * @param $days
	 * @return stdClass
	 */
	public static function parseDays($days) {
		$date1 = time();
		$date2 = strtotime("+$days day");

		$diff = abs($date2 - $date1);

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			
		$result = new stdClass();

		$result->days = $days;
		$result->months = $months;
		$result->years = $years;

		return $result;
	}

	static function getPluginName() {
		return "wp-businessdirectory";
	}

	/**
	 * get component name
	 *
	 * @return mixed component name
	 * @throws Exception
	 */
	public static function getComponentName() {
		$componentname = "com_jbusinessdirectory";
		return $componentname;
	}

	/**
	 * make path file
	 * @param $path string path
	 * @return string|string[]
	 */
	public static function makePathFile($path) {
		$path_tmp = str_replace('\\', DIRECTORY_SEPARATOR, $path);
		$path_tmp = str_replace('/', DIRECTORY_SEPARATOR, $path_tmp);
		return $path_tmp;
	}

	/**
	 * Get the current time based on the time zone
	 *
	 * @param [type] $time_zone
	 * @return void
	 */
	public static function getCurrentTime($time_zone){
		$currentTime="";

		$time_zone=intval($time_zone);
		$offset= $time_zone * 60 * 60;
		$currentTime = gmdate("H:i:s", time()+$offset);

		return $currentTime;
	}
	
	/**
	 * Convert time to Mysql Time Format
	 *
	 * @param $time string time
	 * @return false|string
	 */
	public static function convertTimeToMysqlFormat($time) {
		if (empty($time)) {
			return "00:00:00";
		}
		
		if ($time == '12:00 AM') {
			return '24:00:00';
		}
		
		$strtotime = strtotime($time);
		$time = date('H:i:s', $strtotime);
		
		return $time;
	}

	/**
	 * Convert time to application settings format
	 *
	 * @param $time string time
	 * @return false|string|null time converted or null if no time is provided
	 */
	public static function convertTimeToFormat($time) {
		if (empty($time)) {
			return null;
		}
		$appSettings = JBusinessUtil::getApplicationSettings();
		$strtotime = strtotime($time);
		$time = date($appSettings->time_format, $strtotime);
		return $time;
	}

	/**
	 * Convert date to application settings format
	 *
	 * @param $date string date
	 * @return false|string|null date converted or null if no date is provided
	 */
	public static function convertToFormat($date) {
		if (isset($date) && strlen($date)>6 && $date!="0000-00-00" && $date!="00-00-0000") {
			try {
				$appSettings = JBusinessUtil::getApplicationSettings();
				$date = substr($date, 0, 10);
				list($yy, $mm, $dd)=explode("-", $date);
				if (is_numeric($yy) && is_numeric($mm) && is_numeric($dd)) {
					$date = date($appSettings->dateFormat, strtotime($date));
				} else {
					$date=null;
				}
			} catch (Exception $e) {
				$date="";
			}
		}
		return $date;
	}

	/**
	 * Convert date to mysql date format
	 *
	 * @param $date string date
	 * @return false|string|null date converted or null if it is empty
	 */
	public static function convertToMysqlFormat($date) {
		if (strpos((string)$date, "00-00-00")!==false) {
			return $date;
		} elseif (!empty($date) && strlen($date)>6) {
			$date = date("Y-m-d", strtotime($date));
		} else {
			$date = null;
		}
		return $date;
	}

	/**
	 * Covert a date to a general format
	 *
	 * @param $data string date
	 * @return string
	 */
	public static function getDateGeneralFormat($data) {
		$dateS="";
		if (isset($data) && strlen($data)>6  && $data!="0000-00-00" && $data != "current_timestamp()") {
			//$data =strtotime($data);
			//setlocale(LC_ALL, 'de_DE');
			//$dateS = strftime( '%e %B %Y', $data );
			$date = JFactory::getDate($data);
			$dateS = $date->format('j F Y');
			//$dateS = date( 'j F Y', $data );
		}
	
		return $dateS;
	}

	/**
	 * Get date in ISO format
	 *
	 * @param $data string date
	 * @return string date
	 */
	public static function getDateISOFormat($data, $time = "") {
		$dateS="";
		if (isset($data) && strlen($data)>6  && $data!="0000-00-00" && $data != "current_timestamp()") {
			//$data =strtotime($data);
			//setlocale(LC_ALL, 'de_DE');
			//$dateS = strftime( '%e %B %Y', $data );

			if(!empty($time)){
				$date = JFactory::getDate($data." ".$time);
				$dateS = $date->format('Y-m-dTH:m:s');
			}else{
				$date = JFactory::getDate($data);
				$dateS = $date->format('Y-m-d');
			}
		}
		
		return $dateS;
	}

	/**
	 * Convert a general date to a short format
	 *
	 * @param $data string date
	 * @return string
	 */
	public static function getDateGeneralShortFormat($data) {
		$dateS="";
		if (isset($data) && strlen($data)>6  && $data!="0000-00-00" && $data != "current_timestamp()") {
			//$data =strtotime($data);
			//$dateS = strftime( '%e %b %Y', $data );
			//$dateS = date( 'j M Y', $data );
			$date = JFactory::getDate($data);
			$dateS = $date->format('j M Y');
		}
	
		return $dateS;
	}

	/**
	 * Get date gemeral format with time
	 *
	 * @param $data string date
	 * @return string|null
	 */
	public static function getDateGeneralFormatWithTime($data) {
		if (empty($data) || $data == "current_timestamp()") {
			return null;
		}
		$date = JFactory::getDate($data);
		$dateS =  $date->format('j M Y | G:i:s');
	
		return $dateS;
	}


	/**
	 * Get short date if format 'M j'
	 *
	 * @param $data string date
	 * @return string|null
	 */
	public static function getShortDate($data) {
		if (empty($data) || $data == "current_timestamp()") {
			return null;
		}
		
		$date = JFactory::getDate($data);
		$dateS = $date->format('M j');
	
		return $dateS;
	}

	/**
	 * Get short week date
	 *
	 * @param $data string data
	 * @return string|null
	 */
	public static function getShortWeekDate($data) {
		if (empty($data) || $data == "current_timestamp()") {
			return null;
		}
		
		$date = JFactory::getDate($data);
		$dateS = $date->format('D, M j');
		
		return $dateS;
	}

	/**
	 * Covert time in g:iA format
	 *
	 * @param $time string time
	 * @return false|string
	 */
	public static function getTimeText($time) {
		$result = date('g:i A', strtotime($time));
		
		return $result;
	}

	/**
	 * Get the remaining time up to a specific date
	 *
	 * @param $date string date
	 * @return string return time
	 * @throws Exception
	 */
	public static function getRemainingTime($date, $onlyDays = false) {
		$now = new DateTime();
		$future_date = new DateTime($date);
		$timestamp = strtotime($date);
		$timestamp = strtotime('+1 day', $timestamp);

		if ($timestamp  < time()) {
			return "";
		}
		$interval = $future_date->diff($now);
		$result = JText::_("LNG_ENDS_IN");
		
		if($interval->format("%a")){
			if($onlyDays){
				$result = "";
			}
			$result .= " ".$interval->format("%a")." ".($interval->format("%a") == 1 ?strtolower(JText::_("LNG_DAY")): strtolower(JText::_("LNG_DAYS")));
			if($onlyDays){
				$result .= " ".JText::_("LNG_LEFT");
				return $result;
			}
		}
		if($interval->format("%h")){
			$result .= " ".$interval->format("%h")." ".($interval->format("%h") == 1 ?strtolower(JText::_("LNG_HOUR")): strtolower(JText::_("LNG_HOURS")));
		}
		if($interval->format("%i")){
			$result .= " ".$interval->format("%i")." ".($interval->format("%i") == 1 ?strtolower(JText::_("LNG_MIN")): strtolower(JText::_("LNG_MIN")));
		}
		
		
		return $result;
	}

	/**
	 * Substract time
	 * 
	 */
	public static function substractTime($time, $interval){
		$a = new DateTime($time);
		$b = new DateTime("00:$interval");
		$interval = $a->diff($b);
		
		$result = $interval->format("%H:%i");
		return $result;
	}

	/**
	 * Add time
	 * 
	 */
	public static function addTime($time, $interval){
		$a = new DateTime($time);
		$interval = "PT".$interval."M";
		$b = new DateInterval($interval);
		$interval = $a->add($b);

		$result = $interval->format("H:i");
		return $result;
	}

	/**
	 * Load modules that are available on a specific position
	 *
	 * @param $position string position
	 */
	public static function loadModules($position) {
		require_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'module'.DS.'helper.php');
		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('module');
		$db =JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__modules WHERE position='$position' AND published=1 ORDER BY ordering");
		$modules = $db->loadObjectList();
		if (count($modules) > 0) {
			foreach ($modules as $module) {
				//just to get rid of that stupid php warning
				$module->user = '';
				$params = array('style'=>'xhtml');
				echo $renderer->render($module, $params);
			}
		}
	}
	
	/**
	 * Get company details
	 * @param int $companyId company id
	 */
	public static function getCompany($companyId) {
		if (empty($companyId)) {
			return null;
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$companiesTable = JTable::getInstance("Company", "JTable");
		$company = $companiesTable->getCompany($companyId);
		
		return $company;
	}

		/**
	 * Get company details
	 * @param int $companyId company id
	 */
	public static function updateCompanyUser($companyId, $userId) {
		if (empty($companyId)) {
			return null;
		}
		
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$companiesTable = JTable::getInstance("Company", "JTable");
		$companiesTable->load($companyId);
		$companiesTable->userId = $userId;

		if($companiesTable->store()){
			return true;
		}
		
		return false;
	}

	
	/**
	 * Get event details
	 * @param int $eventId
	 */
	public static function getEvent($eventId) {
		if (empty($eventId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$eventTable = JTable::getInstance("Event", "JTable");
		$event = $eventTable->getEvent($eventId);
	
		return $event;
	}
	
	/**
	 * Get offer details
	 * @param int $offerId
	 */
	public static function getOffer($offerId) {
		if (empty($offerId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$offerTable = JTable::getInstance("Offer", "JTable");
		$offer = $offerTable->getOffer($offerId);
	
		return $offer;
	}

	/**
	 * Get conference details
	 * @param int $conferenceId
	 */
	public static function getConference($conferenceId) {
		if (empty($conferenceId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$conferenceTable = JTable::getInstance("Conference", "JTable");
		$conference = $conferenceTable->getConference($conferenceId);
	
		return $conference;
	}

	/**
	 * Get speaker details
	 * @param int $speakerId
	 */
	public static function getSpeaker($speakerId) {
		if (empty($speakerId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$speakerTable = JTable::getInstance("Speaker", "JTable");
		$speaker = $speakerTable->getSpeaker($speakerId);
	
		return $speaker;
	}

	/**
	 * Get video details
	 * @param int $videoId
	 */
	public static function getVideo($videoId) {
		if (empty($videoId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$videoTable = JTable::getInstance("Videos", "Table");
		$video = $videoTable->getVideo($videoId);
	
		return $video;
	}
	
	/**
	 * Get session location details
	 * @param int $sessionLocationId
	 */
	public static function getSessionLocation($sessionLocationId) {
		if (empty($sessionLocationId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$sessionLocationTable = JTable::getInstance("Sessionlocation", "JTable");
		$sessionLocation = $sessionLocationTable->getSessionLocation($sessionLocationId);
	
		return $sessionLocation;
	}

	/**
	 * Get session name
	 * @param int $sessionId
	 */
	public static function getSessionName($sessionId) {
		if (empty($sessionId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$sessionTable = JTable::getInstance("Sessions", "JTable");
		$session = $sessionTable->getSessionName($sessionId);
	
		return $session;
	}

	/**
	 * Get article title
	 * @param int $articleId
	 */
	public static function getArticleTitle($articleId) {
		if (empty($articleId)) {
			return null;
		}
	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$articleTable = JTable::getInstance("CompanyArticles", "Table");
		$article = $articleTable->getArticleTitle($articleId);
	
		return $article;
	}

	/**
	 * Get package by id
	 *
	 * @param $packageId int package id
	 * @return |null
	 */
	public static function getPackage($packageId) {
		if (empty($packageId)) {
			return null;
		}
		
		$packageTable = JTable::getInstance("Package", "JTable");
		$package = $packageTable->getPackage($packageId);
		
		$package->features = explode(",", (string)$package->featuresS);
		$package->features[]= "multiple_categories";
		
		if (self::getInstance()->getApplicationSettings()->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($package, PACKAGE_TRANSLATION);
		}
		
		return $package;
	}

	/**
	 * Get current packages
	 * @return array
	 */
	public static function getPackages($showAdmin = true, $showAll = false) {
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$packageTable = JTable::getInstance("Package", "JTable");
		$packages = $packageTable->getPackages($showAdmin, $showAll);
		
		$result = array();
		foreach ($packages as $package) {
			$result[$package->id] = $package;
		}
		
		return $result;
	}

	/**
	 * Get packages names
	 * @return array
	 */
	public static function getPackagesNames($packageIds) {
		
		$packageNames = array(); 
		$packages = self::getPackages();
		foreach($packageIds as $id){
			$packageNames[] = $packages[$id]->name;
		}

		return $packageNames;
	}

	/**
	 * Get user active packages
	 * @return array
	 */
	public static function getUserActivePackages($userId) {
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$packageTable = JTable::getInstance("Package", "JTable");
		$packages = $packageTable->getUserActivePackage($userId, PACKAGE_TYPE_BUSINESS);
		
		return $packages;
	}
	
	/**
	 * Get custom attributes and filter them based on package features
	 * @return array
	 */
	public static function getPackagesAttributes($packages) {
		$attributesTable = JTable::getInstance('Attribute', 'JTable');
		$attributes = $attributesTable->getActiveAttributes();
	
		if (!is_array($packages)) {
			$packages = array($packages);
		}
		
		$result = array();
		//check if the attribues are contained in at least one package. If not it will be removed.
		foreach ($attributes as $attribute) {
			$found = false;
			foreach ($packages as $package) {
				foreach ($package->features as $feature) {
					if ($feature == $attribute->code) {
						$found = true;
					}
				}
			}
				
			if ($found) {
				$result[] = $attribute;
			}
		}
	
		if (self::getInstance()->getApplicationSettings()->enable_multilingual) {
			JBusinessDirectoryTranslations::updateAttributesTranslation($result);
		}
	
		return $result;
	}


	/**
	 * Get the package type unit in php time format
	 */
	public static function getTimeUnit($unit){

		if($unit=="Y"){
			return "years";
		}

		if($unit=="M"){
			return "months";
		}

		if($unit=="W"){
			return "weeks";
		}

		if($unit=="D"){
			return "days";
		}

		return "";
	}

	public static function getTrialText($package, $showPrice = true){
		$text= "";
		if($package->expiration_type == 4){

			if($showPrice){
				if($package->trial_price == 0){
					$text .= JText::_("LNG_FREE")." ".JText::_("LNG_FOR")." ";
				}else{
					$text = JBusinessUtil::getPriceFormat($package->trial_price);
					$text .= " ".JText::_("LNG_FOR")." ";
				}
			}else{
				$text = JText::_("LNG_TRY");
				$text .= " ".JText::_("LNG_FOR")." ";
			}
			
			$timePeriod = "";
			switch ($package->trial_period_unit) {
				case "D":
					$timePeriod = $package->trial_period_amount . " " . JText::_('LNG_DAYS');
					break;
				case "W":
					$timePeriod = $package->trial_period_amount . " " . JText::_('LNG_WEEKS');
					break;
				case "M":
					$timePeriod = $package->trial_period_amount . " " . JText::_('LNG_MONTHS');
					break;
				case "Y":
					$timePeriod = $package->trial_period_amount . " " . JText::_('LNG_YEARS');
					break;
				default:
				$timePeriod = $package->trial_period_amount;
			}

			$text .= $timePeriod;

		}

		return $text;
	}

	/**
	 * Get the package duratino as String
	 */
	public static function getTrialPackageDuration($package){

		$result = $package->trial_period_amount . " ";
		$time_unit = JText::_('LNG_DAYS');
		switch($package->trial_period_unit){
			case "D":
				$time_unit = $package->trial_period_amount>1?JText::_('LNG_DAYS'):JText::_('LNG_DAY');
				break;
			case "W":
				$time_unit = $package->trial_period_amount>1?JText::_('LNG_WEEKS'):JText::_('LNG_WEEK');
				break;
			case "M":
				$time_unit = $package->trial_period_amount>1?JText::_('LNG_MONTHS'):JText::_('LNG_MONTH');
				break;
			case "Y":
				$time_unit = $package->trial_period_amount>1?JText::_('LNG_YEARS'):JText::_('LNG_YEAR');
				break;
		}
		$result .= $time_unit;

		return $result;
	}

	/**
	 * Get the package duratino as String
	 */
	public static function getPackageDuration($package, $showOriginal = false){

		$result = "";
		if($package->expiration_type == 1 || empty($package->expiration_type)){
			$result = JText::_('LNG_LIFE_TIME');
		}else{
			$result = $package->time_amount . " ";
			$time_unit = JText::_('LNG_DAYS');

			if($package->time_unit == "Y" && $package->time_amount == 1 && !empty($package->show_price_per_month) && !$showOriginal){
				$package->time_unit = "M";
			}

			switch($package->time_unit){
				case "D":
					$time_unit = $package->time_amount>1?JText::_('LNG_DAYS'):JText::_('LNG_DAY');
					break;
				case "W":
					$time_unit = $package->time_amount>1?JText::_('LNG_WEEKS'):JText::_('LNG_WEEK');
					break;
				case "M":
					$time_unit = $package->time_amount>1?JText::_('LNG_MONTHS'):JText::_('LNG_MONTH');
					break;
				case "Y":
					$time_unit = $package->time_amount>1?JText::_('LNG_YEARS'):JText::_('LNG_YEAR');
					break;
			}
			$result .= $time_unit;

		}

		return $result;
	}

	/**
	 * Update the order end date
	 *
	 */
	public static function updateOrderEndTime(){
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$packages = self::getPackages(false, true);
		$ordersTable = JTable::getInstance("Order");

		$start = 0;
		$limit = 200;
		while($orders = $ordersTable->getOrdersWithoutEndDate($start, $limit)){
			foreach($orders as $order){

				//get the order package
				if(!empty($packages[$order->package_id])){
					$package = $packages[$order->package_id];
					$timeUnit = self::getTimeUnit($package->time_unit);

					$order->end_date = date('Y-m-d', strtotime($order->start_date. " + $package->time_amount $timeUnit"));
				} else {
					$order->end_date = $order->start_date;
				}

				$ordersTable->bind($order);
				$ordersTable->store();
			}
			$start += $limit;
		}

		return true;
	}

	/**
	 * Process listing packages to establish the state and expiration date
	 *
	 * @param $items
	 * @return mixed
	 */
	public static function processPackages($items) {
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		if (empty($items)) {
			return $items;
		}
			
		$packagesTable =  JTable::getInstance("Package");
		$freePackage = $packagesTable->getFreePackage();
		$packages = self::getPackages(true, true);
		$active = false;
		
		foreach ($items as $item) {
			$packageStatus = array();
			if (!empty($item->orders)) {
				$active = false;
				$orders = explode("#|", $item->orders);
				foreach ($orders as $i=>$orderS) {
					$orderS = explode("|", $orderS);
				  
					$order = new stdClass();
					$order->start_date = $orderS[0];
					$order->start_trial_date = $orderS[1];
					$order->state = $orderS[2];
					$order->package_id = $orderS[3];
					$order->id = $orderS[4];
					$order->company_id = $orderS[5];
					$order->end_date = $orderS[6];
					$order->amount = $orderS[7];
					$order->trial_amount = $orderS[8];

					if (empty($order->package_id) || !isset($packages[$order->package_id])) {
						continue;
					}
					
					$package = $packages[$order->package_id];
					self::preparePackageText($package);

					$packgeInfo = new stdClass();
					$packgeInfo->name = $package->name;
					$packgeInfo->package_id = $package->id;
					$packgeInfo->expiration_type =  $package->expiration_type;
					//check if package is still active - between the start and end date
					$packgeInfo->active = ($package->price==0 || $package->expiration_type==1 || (time() > strtotime($order->start_date) && (time() < strtotime($order->end_date)) && $order->start_date!='0000-00-00')) ;
					$packgeInfo->price = $package->price;
					$packgeInfo->renewal_price = $package->renewal_price;
					$packgeInfo->details_text = $package->details_text;
					$packgeInfo->trial_period_unit = $package->trial_period_unit;
					$packgeInfo->trial_period_amount = $package->trial_period_amount;
					
					if ($packgeInfo->active) {
						$active = true;
					}
					$packgeInfo->state = $order->state;
					$packgeInfo->expirationDate = JBusinessUtil::getDateGeneralShortFormat($order->end_date);
					$packgeInfo->start_date = $order->start_date;
					$packgeInfo->future = (time() < strtotime($order->start_date) && $order->start_date!='0000-00-00');
					$packgeInfo->order_id = $order->id;
					$packgeInfo->company_id = $order->company_id;
					$packgeInfo->amount = $order->amount;
					$packgeInfo->trial_price = $order->trial_amount;

					if (!$packgeInfo->active && (count($orders) == 1 || $i >= (count($orders) - 3)) || ($packgeInfo->active || $packgeInfo->future)) {
						$packageStatus[] = $packgeInfo;
					}
				}
			}

			if (!empty($item->package_id) && isset($packages[$item->package_id])) {
				$currentPackage = $packages[$item->package_id];
				if ($currentPackage->price == 0) {
					$packgeInfo = new stdClass();
					$packgeInfo->name = $currentPackage->name;
					$packgeInfo->expiration_type =  $currentPackage->expiration_type;
					$packgeInfo->active = true;
					$packgeInfo->state = 1;
					$packgeInfo->future = false;
					if(!empty($package)){
						$packgeInfo->package_id = $package->id;
					}
					$packgeInfo->price = $currentPackage->price;
					$packgeInfo->trial_period_unit = $currentPackage->trial_period_unit;
					$packgeInfo->trial_period_amount = $currentPackage->trial_period_amount;
					$packageStatus[] = $packgeInfo;
				}
			}

			if (!empty($freePackage)) {
				$active = true;
			}
				
			$item->packgeInfo= $packageStatus;
			$item->active = $active;
		}
		
		return $items;
	}


	public static function preparePackageText(&$package){
		$text = '';

		$text = JBusinessUtil::getPackageDuration($package);

		if ($package->expiration_type == 3 || $package->expiration_type == 4) {
			if (!empty($package->recurrence_count)) {
				$text .= " | x" . $package->recurrence_count . " " . JText::_("LNG_OCCURANCES");
			}

			if ($package->expiration_type == 4) {
				$text .= " | trial: ";
				switch ($package->trial_period_unit) {
					case "D":
						$text .= $package->trial_period_amount . " " . JText::_('LNG_DAYS');
						break;
					case "W":
						$text .= $package->trial_period_amount . " " . JText::_('LNG_WEEKS');
						break;
					case "M":
						$text .= $package->trial_period_amount . " " . JText::_('LNG_MONTHS');
						break;
					case "Y":
						$text .= $package->trial_period_amount . " " . JText::_('LNG_YEARS');
						break;
					default:
						$text .= $package->trial_period_amount;
				}

				if (!empty($package->trial_price)) {
					$text .= " " . JBusinessUtil::getPriceFormat($package->trial_price);
				}
			}
		}
		
		$package->details_text = $text;
		$package->price_text = JBusinessUtil::getPriceFormat($package->price);
	}

	/**
	 * Set the active menu item on sessions
	 *
	 * @throws Exception
	 */
	public static function setMenuItemId() {
		$session = JFactory::getSession();
		
		$lang = JFactory::getLanguage();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$activeMenu = $app->getMenu()->getActive();
		
		$url = $_SERVER['REQUEST_URI'];
		$urlParts = parse_url($url);
		$menuId="";
		
		if ((!empty($activeMenu) && $menu->getActive() != $menu->getDefault($lang->getTag()))
			|| ($urlParts["path"]=='/' && empty($urlParts["query"]))) {
			$menuId = $activeMenu->id;
			$session->set('menuId', $menuId);
		}
	}

	/**
	 * Build the menu item URL parameter based on the active menu item
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function getItemIdS() {


		return "";
	}

	/**
	 * Get the current menu alias
	 *
	 * @return string
	 */
	public static function getCurrentMenuAlias() {
		$menualias =  "";
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		
		return $appSettings->url_menu_alias;
		/*
		$currentMenu = null;
		if(!empty($appSettings->menu_item_id)){
			$currentMenu = JFactory::getApplication()->getMenu()->getItem($appSettings->menu_item_id);
		}

		if(empty($currentMenu)){
			$currentMenu = JFactory::getApplication()->getMenu()->getActive();
		}

		if(!empty($currentMenu))
			$menualias = $currentMenu->alias;*/
		
		return $menualias;
	}

	/**
	 * Prevent the links to contain administrator keyword
	 *
	 * @param $url string url path
	 * @return string|string[]
	 */
	public static function processURL($url) {
		if (strpos($url, "/administrator/")!==false) {
			$url = str_replace("administrator/", "", $url);
		}
		
		return $url;
	}

	/**
	 * Retrieve the status of the url language code based on language filter plugin
	 * 
	 */
	public static function getURLLanguageCodeStatus(){
		// $languagePlugin = JPluginHelper::getPlugin('system', 'languagefilter');
		// if (empty($languagePlugin)) {
		// 	return false;
		// } else {
		// 	$params = new JRegistry($languagePlugin->params);
		// 	$removePrefix = $params->get('remove_default_prefix');
		// 	if(!$removePrefix){
		// 		return true;
		// 	}
		// }
		
		return false;
	}

	/**
	 * Generate the business listing link
	 *
	 * @param $company object company
	 * @param null $addIndex
	 * @return string|string[]
	 * @throws Exception
	 */
	public static function getCompanyLink($company, $addIndex = null) {
		$itemidS = self::getItemIdS();

		$companyAlias = trim($company->alias);
		$companyAlias = stripslashes(strtolower($companyAlias));
		$companyAlias = str_replace(" ", "-", $companyAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";

		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		$appSettings = JBusinessUtil::getApplicationSettings();

		if ($appSettings->open_business_website == "1" && !empty($company->website)) {
			JBusinessDirectoryTranslations::updateEntityTranslation($company, BUSSINESS_DESCRIPTION_TRANSLATION);
			return $company->website;
		} elseif (!$appSettings->enable_seo) {
			$companyLink = $company->id;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$companyLink = $company->id;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=companies&companyId=' . $companyLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$companyLink = $company->id . "-" . htmlentities(urlencode($companyAlias));
			} else {
				$companyLink = htmlentities(urlencode($companyAlias));
			}

			$urlParts = array();
			if (!empty($appSettings->url_fields)) {
				if (in_array("category", $appSettings->url_fields)) {
					$categoryPath = self::getBusinessCategoryPath($company);
					if (!empty($categoryPath)) {
						$path = "";
						foreach ($categoryPath as $cp) {
							if ($appSettings->enable_multilingual) {
								if (!class_exists('JBusinessDirectoryTranslations')) {
									require_once BD_HELPERS_PATH . '/translations.php';
								}

								JBusinessDirectoryTranslations::updateEntityTranslation($cp, CATEGORY_TRANSLATION);
							}
							$path = $path . JApplicationHelper::stringURLSafe($cp->name) . "/";
						}
						$path = substr($path, 0, -1);
						array_push($urlParts, strtolower($path));
					}
				}
				if (in_array("province", $appSettings->url_fields) && !empty($company->province)) {
					array_push($urlParts, strtolower($company->province));
				}
				if (in_array("region", $appSettings->url_fields) && !empty($company->county)) {
					array_push($urlParts, strtolower($company->county));
				}
				if (in_array("city", $appSettings->url_fields) && !empty($company->city)) {
					array_push($urlParts, strtolower($company->city));
				}

				array_push($urlParts, $companyLink);
				$urlParts = array_filter($urlParts);
				$companyLink = implode("/", $urlParts);
			} else {
				//$company->county = JApplicationHelper::stringURLSafe($company->county);
				//$company->city = JApplicationHelper::stringURLSafe($company->city);
				//$company->province = JApplicationHelper::stringURLSafe($company->province);

				if ($appSettings->listing_url_type == 2) {
					$categoryPath = self::getBusinessCategoryPath($company);
					$path = "";
					foreach ($categoryPath as $cp) {
						$path = $path . JApplicationHelper::stringURLSafe($cp->name) . "/";
					}
					$companyLink = strtolower($path) . $companyLink;
				} elseif ($appSettings->listing_url_type == 3) {
					$companyLink = strtolower($company->county) . "/" . strtolower($company->city) . "/" . $companyLink;
				} elseif ($appSettings->listing_url_type == 4) {
					$categoryPath = self::getBusinessCategoryPath($company);
					$path = "";
					foreach ($categoryPath as $cp) {
						$path = $path . JApplicationHelper::stringURLSafe($cp->name) . "/";
					}
					$companyLink = $path . strtolower($company->province) . "/" . $companyLink;
				} elseif ($appSettings->listing_url_type == 5) {
					$categoryPath = self::getBusinessCategoryPath($company);
					$path = "";
					if (!empty($categoryPath) && isset($categoryPath[0]->name)) {
						$path = JApplication::stringURLSafe($categoryPath[0]->name);
					}
					$companyLink = $path . "/" . strtolower($company->province) . "/" . $companyLink;
				} elseif ($appSettings->listing_url_type == 6) {
					$categoryPath = self::getBusinessCategoryPath($company);
					$path = JApplication::stringURLSafe($categoryPath[0]->name);
					$countryName = "";
					if (isset($company->countryName)) {
						$countryName = $company->countryName;
					} elseif (isset($company->country_name)) {
						$countryName = $company->country_name;
					}

					$countryName = JApplicationHelper::stringURLSafe($countryName);
					$companyLink = $path . "/" . strtolower($countryName) . "/" . $companyLink;
				}
			}

			$base = get_site_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}

			$url = $base . $companyLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $companyLink;
			}
		}

		$url = self::processURL($url);

		return $url;
	}

	/**
	 * Generate the business listing link only for type one (only name in the link)
	 *
	 * @param $companyId int company Id
	 * @param $companyAlias string company alias
	 * @param $addIndex boolean add index or no
	 * @return String $url
	 */
	public static function getCompanyDefaultLink($companyId, $addIndex = null) {
		$company = self::getCompany($companyId);
		$url = self::getCompanyLink($company, $addIndex);

		return $url;
	}

	/**
	 * Generate the link for categories
	 *
	 * @param $categoryId int category Id
	 * @param $categoryAlias string category alias
	 * @param $addIndex boolean true or false to add index
	 * @return string|string[]
	 * @throws Exception
	 */
	public static function getCategoryLink($categoryId, $categoryAlias, $addIndex = null) {
		$itemidS = self::getItemIdS();

		$appSettings = JBusinessUtil::getApplicationSettings();
		
		if(!empty($categoryAlias)){
			$categoryAlias = trim($categoryAlias);
			$categoryAlias = stripslashes(strtolower($categoryAlias));
			$categoryAlias = str_replace(" ", "-", $categoryAlias);
		}
	
		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		$categoryLink = $categoryId;

		if (!$appSettings->enable_seo) {
			$categoryLink = $categoryId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$categoryLink = $categoryId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=search&categoryId=' . $categoryLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$categoryLink = $categoryId . "-" . htmlentities(urlencode($categoryAlias));
			} else {
				$categoryLink = htmlentities(urlencode($categoryAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->category_url_type == 2) {
				$url = $base . $categoryLink;
				if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
					$url = $base . $menuAlias . "/" . $categoryLink;
				}
			} else {
				$url = $base . $appSettings->category_url_naming . "/" . $categoryLink;
				if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
					$url = $base . $menuAlias . "/" . $appSettings->category_url_naming . "/" . $categoryLink;
				}
			}
		}

		$url = self::processURL($url);

		return $url;
	}

	/**
	 *
	 * Generate the offer category link
	 *
	 * @param $categoryId int get categpry link
	 * @param $categoryAlias string category alias
	 * @param $addIndex boolean true or false to add index
	 * @return string|string[]
	 * @throws Exception
	 */
	public static function getOfferCategoryLink($categoryId, $categoryAlias, $addIndex = null) {
		$itemidS = self::getItemIdS();

		$appSettings = JBusinessUtil::getApplicationSettings();

		$categoryAlias = trim($categoryAlias);
		$categoryAlias = stripslashes(strtolower($categoryAlias));
		$categoryAlias = str_replace(" ", "-", $categoryAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		$offerCategoryLink = $categoryId;

		if (!$appSettings->enable_seo) {
			$offerCategoryLink = $categoryId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$categoryLink = $categoryId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=offers&offerCategoryId=' . $offerCategoryLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$offerCategoryLink = $categoryId . "-" . htmlentities(urlencode($categoryAlias));
			} else {
				$offerCategoryLink = htmlentities(urlencode($categoryAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}

			$url = $base . $appSettings->offer_category_url_naming . "/" . $offerCategoryLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->offer_category_url_naming . "/" . $offerCategoryLink;
			}
		}

		$url = self::processURL($url);

		return $url;
	}

	/**
	 * Generate the link for event categories
	 *
	 * @param $categoryId int category id
	 * @param $categoryAlias string category alias
	 * @param $addIndex boolean true or false to add index or not
	 */
	public static function getEventCategoryLink($categoryId, $categoryAlias, $addIndex = null) {
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		//		$menu = $app->getMenu();
		$itemid = "";
		//		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if (isset($activeMenu)) {
			$itemid = JFactory::getApplication()->getMenu()->getActive()->id;
		}

		//		if ($itemid == $menu->getDefault($lang->getTag())->id) {
		//			$itemid	= "";
		//		}

		$appSettings = JBusinessUtil::getApplicationSettings();

		$categoryAlias = trim($categoryAlias);
		$categoryAlias = stripslashes(strtolower($categoryAlias));
		$categoryAlias = str_replace(" ", "-", $categoryAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";

		if (!$appSettings->enable_seo) {
			$eventCategoryLink = $categoryId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$categoryLink = $categoryId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=events&eventCategoryId=' . $eventCategoryLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$eventCategoryLink = $categoryId . "-" . htmlentities(urlencode($categoryAlias));
			} else {
				$eventCategoryLink = htmlentities(urlencode($categoryAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}

			$url = $base . $appSettings->event_category_url_naming . "/" . $eventCategoryLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->event_category_url_naming . "/" . $eventCategoryLink;
			}
		}

		$url = self::processURL($url);

		return $url;
	}

	/**
	 * Generate the link for an offer
	 *
	 * @param $offerId int offer id
	 * @param $offerAlias string offer alias
	 * @param $addIndex boolean true or false to add index or not
	 */
	public static function getOfferLink($offerId, $offerAlias, $addIndex = null) {
		$itemidS = self::getItemIdS();

		$appSettings = JBusinessUtil::getApplicationSettings();

		$offerAlias = trim($offerAlias);
		$offerAlias = stripslashes(strtolower($offerAlias));
		$offerAlias = str_replace(" ", "-", $offerAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		$offerLink = $offerId;

		if (!$appSettings->enable_seo) {
			$offerLink = $offerId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$offerLink = $offerId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=offer&offerId=' . $offerLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$offerLink = $offerId . "-" . htmlentities(urlencode($offerAlias));
			} else {
				$offerLink = htmlentities(urlencode($offerAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}
			$url = $base . $appSettings->offer_url_naming . "/" . $offerLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->offer_url_naming . "/" . $offerLink;
			}
		}

		$url = self::processURL($url);

		return $url;
	}

	/**
	 * Generate the link for an event
	 *
	 * @param $eventId int event id
	 * @param $eventAlias string event alias
	 * @param $addIndex true or false add index
	 */
	public static function getEventLink($eventId, $eventAlias, $addIndex = null) {
		$itemidS = self::getItemIdS();

		$appSettings = JBusinessUtil::getApplicationSettings();

		$eventAlias = trim($eventAlias);
		$eventAlias = stripslashes(strtolower($eventAlias));
		$eventAlias = str_replace(" ", "-", $eventAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		if (!$appSettings->enable_seo) {
			$eventLink = $eventId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$categoryLink = $eventId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=event&eventId=' . $eventLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$eventLink = $eventId . "-" . htmlentities(urlencode($eventAlias));
			} else {
				$eventLink = htmlentities(urlencode($eventAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}

			$url = $base . $appSettings->event_url_naming . "/" . $eventLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->event_url_naming . "/" . $eventLink;
			}
		}

		$url = self::processURL($url);

		return $url;
	}

	/**
	 * Generate the link for a conference
	 *
	 * @param $conferenceId int conference id
	 * @param $conferenceAlias string conference alias
	 * @param $addIndex true or false add index
	 */
	public static function getConferenceLink($conferenceId, $conferenceAlias, $addIndex = null) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$itemid = JFactory::getApplication()->input->getInt('Itemid');

		$conferenceAlias = trim($conferenceAlias);
		$conferenceAlias = stripslashes(strtolower($conferenceAlias));
		$conferenceAlias = str_replace(" ", "-", $conferenceAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		if (!$appSettings->enable_seo) {
			$conferenceLink = $conferenceId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$conferenceLink = $conferenceId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=conference&conferenceId=' . $conferenceId, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$conferenceLink = $conferenceId . "-" . htmlentities(urlencode($conferenceAlias));
			} else {
				$conferenceLink = htmlentities(urlencode($conferenceAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}
			$url = $base . $appSettings->conference_url_naming . "/" . $conferenceLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->conference_url_naming . "/" . $conferenceLink;
			}
		}

		return $url;
	}

	/**
	 * Generate the link for a conference session
	 *
	 * @param $sessionId int session Id
	 * @param $sessionAlias string session alias
	 * @param $addIndex boolean true or false to add the index
	 * @return string
	 */
	public static function getConferenceSessionLink($sessionId, $sessionAlias, $addIndex = null) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$itemid = JFactory::getApplication()->input->getInt('Itemid');

		$sessionAlias = trim($sessionAlias);
		$sessionAlias = stripslashes(strtolower($sessionAlias));
		$sessionAlias = str_replace(" ", "-", $sessionAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		if (!$appSettings->enable_seo) {
			$sessionLink = $sessionId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$sessionLink = $sessionId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=conferencesession&cSessionId=' . $sessionLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$sessionLink = $sessionId . "-" . htmlentities(urlencode($sessionAlias));
			} else {
				$sessionLink = htmlentities(urlencode($sessionAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}

			$url = $base . $appSettings->conference_session_url_naming . "/" . $sessionLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->conference_session_url_naming . "/" . $sessionLink;
			}
		}

		return $url;
	}

	/**
	 * Generate the link for a speaker
	 *
	 * @param $speakerId int speaker id
	 * @param $speakerAlias string speaker alias
	 * @param $addIndex boolean true or false to add the index
	 * @return string
	 * @throws Exception
	 */
	public static function getSpeakerLink($speakerId, $speakerAlias, $addIndex = null) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$itemid = JFactory::getApplication()->input->getInt('Itemid');

		$speakerAlias = trim($speakerAlias);
		$speakerAlias = stripslashes(strtolower($speakerAlias));
		$speakerAlias = str_replace(" ", "-", $speakerAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index = "/";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index = "/index.php/";
		}

		if (!$appSettings->enable_seo) {
			$speakerLink = $speakerId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$speakerLink = $speakerId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=speaker&speakerId=' . $speakerLink, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$speakerLink = $speakerId . "-" . htmlentities(urlencode($speakerAlias));
			} else {
				$speakerLink = htmlentities(urlencode($speakerAlias));
			}

			$base = home_url() . $index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base .= $langTag . "/";
			}
			$url = $base . $appSettings->speaker_url_naming . "/" . $speakerLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base . $menuAlias . "/" . $appSettings->speaker_url_naming . "/" . $speakerLink;
			}
		}

		return $url;
	}

	/**
	 * Generate the link for a video
	 *
	 * @param $videoId int video id
	 * @param $videoAlias string video alias
	 * @param $addIndex boolean true or false to add the index
	 * @return string
	 * @throws Exception
	 */
	public static function getVideoLink($video, $addIndex=null) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$appSettings->video_url_naming = "video";

		$itemid = JFactory::getApplication()->input->getInt('Itemid');

		$videoAlias = trim($video->alias);
		$videoAlias = stripslashes(strtolower($videoAlias));
		$videoAlias = str_replace(" ", "-", $videoAlias);

		$conf = JBusinessUtil::getSiteConfig();
		$index ="";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index ="index.php/";
		}

		$url = "";
		$videoLink="";
		if (!$appSettings->enable_seo) {
			$videoLink = $video->id;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$videoLink = $video->id;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=video&videoId='.$videoLink, false, -1);
		} else {

			$category = self::getCategoryItem($video->main_subcategory);
			if(!empty($category)){
				$videoLink =  $category->alias."/";
			}

			if ($appSettings->add_url_id == 1) {
				$videoLink .= $video->id."-".htmlentities(urlencode($videoAlias));
			} else {
				$videoLink .= htmlentities(urlencode($videoAlias));
			}

			$base = JBusinessUtil::getWebsiteURL(true).$index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			$url = $base.$appSettings->video_url_naming."/".$videoLink;

			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base.$menuAlias."/".$appSettings->video_url_naming."/".$videoLink;
			}
		}

		return $url;
	}

	/**
	 * Generate the link for a trip
	 *
	 * @param $tripId int conference id
	 * @param $tripAlias string conference alias
	 * @param $addIndex true or false add index
	 */
	public static function getTripLink($tripId, $tripAlias, $addIndex=null) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$appSettings->trip_url_naming = "trip";
	
		$itemid = JFactory::getApplication()->input->getInt('Itemid');
	
		$tripAlias = trim($tripAlias);
		$tripAlias = stripslashes(strtolower($tripAlias));
		$tripAlias = str_replace(" ", "-", $tripAlias);
	
		$conf = JBusinessUtil::getSiteConfig();
		$index ="";
		if (!JBusinessUtil::getSiteConfig()->sef_rewrite) {
			$index ="index.php/";
		}
	
		if (!$appSettings->enable_seo) {
			$tripLink = $tripId;
			if (JBusinessUtil::getSiteConfig()->sef) {
				$tripLink = $tripId;
			}
			$url = JRoute::_('index.php?option=com_jbusinessdirectory&view=trip&tripId='.$tripId, false, -1);
		} else {
			if ($appSettings->add_url_id == 1) {
				$tripLink = $tripId."-".htmlentities(urlencode($tripAlias));
			} else {
				$tripLink = htmlentities(urlencode($tripAlias));
			}
			
			$base = JBusinessUtil::getWebsiteURL(true).$index;
			$urlLngCodeStatus = self::getURLLanguageCodeStatus();
			if ($appSettings->add_url_language || $urlLngCodeStatus) {
				$langTag = self::getCurrentLanguageCode();
				$base.= $langTag."/";
			}
			$url = $base.$appSettings->trip_url_naming."/".$tripLink;
			
			$menuAlias = self::getCurrentMenuAlias();
			if ($appSettings->enable_menu_alias_url && !empty($menuAlias)) {
				$url = $base.$menuAlias."/".$appSettings->trip_url_naming."/".$tripLink;
			}
		}
	
		return $url;
	}

	/**
	 * Check if joomla 3 is used
	 *
	 * @return bool
	 */
	public static function isJoomla3() {
		return true;
	}

    /**
     * Include color picker based on Joomla version.
     */
    public static function includeColorPicker(){

        if(self::isJoomla3()){
            JHTML::_('behavior.colorpicker');
        }else{
            $wa = JFactory::getApplication()->getDocument()->getWebAssetManager();
            $wa->usePreset('minicolors')->useScript('field.color-adv');
        }

    }

	/**
	 * Truncate html text
	 *
	 * @param $text string text
	 * @param $length int length
	 * @param string $ending ending of the text
	 * @param bool $considerHtml
	 * @param bool $exact
	 *
	 * @return false|string
	 */
	public static function truncate($text, $length, $ending = '&hellip;', $considerHtml = true, $exact = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag
					} elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag
					} elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if ($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if ($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}

	/**
	 * Generate the alias from title
	 *
	 * @param $title string title to get the alias
	 * @param $alias string alias
	 * @return string
	 */
	public static function getAlias($title, $alias) {
		if (empty($alias) || trim($alias) == '') {
			$alias = $title;
		}
		
		$alias = JApplicationHelper::stringURLSafe($alias);
		if (trim(str_replace('-', '', $alias)) == '') {
			$alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
		
		return $alias;
	}

	/**
	 * Compose address based on address and city
	 *
	 * @param $address string address
	 * @param $city string city
	 * @return string
	 */
	public static function composeAddress($address, $city) {
		$result ="";
		if (!empty($address)) {
			$result .=$address;
		}
		
		if (!empty($address) && !empty($city)) {
			$result .=", ";
		}
		
		if (!empty($city)) {
			$result .=$city;
		}
		
		return $result;
	}

	/**
	 * Generate the address text
	 *
	 * @param $company object company details
	 * @return string|null
	 */
	public static function getAddressText($company) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$customAddress = $appSettings->custom_address;
		$address="";

		if(empty($company)){
			return;
		}

		$company->street_number = isset($company->street_number) ? $company->street_number : '';
		$company->postalCode    = isset($company->postalCode) ? $company->postalCode : '';
		if(!empty($company->postal_code)){
            $company->postalCode = $company->postal_code;
        }

		$company->province      = isset($company->province) ? $company->province : '';
		$company->address       = isset($company->address) ? $company->address : '';
		$company->county        = isset($company->county) ? $company->county : '';

        if(!empty($company->region)){
            $company->county = $company->region;
        }
		$company->city          = isset($company->city) ? $company->city : '';
		
		if (isset($company->publish_only_city) && $company->publish_only_city) {
			$addressParts = array($company->city,$company->county);
			if (!empty($company->countryId) && $appSettings->add_country_address == 1) {
				$addressParts[] = self::getCountry($company->countryId)->country_name;
			}
			
			$addressParts = array_filter($addressParts);
			foreach ($addressParts as $key=>$add) {
				if ($add == " ") {
					unset($addressParts[$key]);
				}
			}
			
			$addressParts = array_map('trim', $addressParts);
			$addressParts = implode(', ', $addressParts);
			$addressParts = trim($addressParts);
			$address = $addressParts;
			return $address;
		}

		if (!isset($company->street_number)) {
			$company->street_number = "";
		}

		if (!isset($company->area)) {
			$company->area = "";
		}

		if ($appSettings->limit_cities_regions == 1) {
			if (!empty($address) || !empty($customAddress)) {
				if (!empty($company->regions)) {
					$company->county = $company->regions[0]->name;
				}

				if (!empty($company->cities)) {
					$company->city   = $company->cities[0]->name;
				}
			}
		}

		$addressParts = array();
		switch ($appSettings->address_format) {
			case 1:
				$addressParts = array($company->street_number." ".$company->address, $company->area,$company->city." ".$company->postalCode,$company->county, $company->province);
				break;
			case 2:
				$addressParts = array($company->address." ".$company->street_number, $company->area,$company->city." ".$company->postalCode,$company->county, $company->province);
				break;
			case 3:
				$addressParts = array($company->street_number." ".$company->address, $company->area,$company->city,$company->county ." ".$company->postalCode, $company->province);
				break;
			case 4:
				$addressParts = array($company->address." ".$company->street_number, $company->area,$company->city,$company->county ." ".$company->postalCode, $company->province);
				break;
			case 5:
				$addressParts = array($company->street_number." ".$company->address, $company->area,$company->postalCode." ".$company->city,$company->county, $company->province);
				break;
			case 6:
				$addressParts = array($company->address." ".$company->street_number, $company->area,$company->postalCode." ".$company->city,$company->county, $company->province);
				break;
			case 7:
				$addressParts = array($company->postalCode." ".$company->province, $company->city, $company->area, $company->street_number);
				break;
			case 8:
				$customAddress = str_replace(ADDRESS_ADDRESS, $company->address, $customAddress);
				$customAddress = str_replace(ADDRESS_AREA, $company->area, $customAddress);
				$customAddress = str_replace(ADDRESS_CITY, $company->city, $customAddress);
				$customAddress = str_replace(ADDRESS_POSTAL_CODE, $company->postalCode, $customAddress);
				$customAddress = str_replace(ADDRESS_PROVINCE, $company->province, $customAddress);
				$customAddress = str_replace(ADDRESS_REGION, $company->county, $customAddress);
				$customAddress = str_replace(ADDRESS_STREET_NUMBER, $company->street_number, $customAddress);
				
				//$customAddress = str_replace("<br/><br/>", "<br/>",$customAddress);
				if (empty($company->address) && empty($company->area) && empty($company->city) && empty($company->postalCode) && empty($company->province) && empty($company->county) && empty($company->street_number)) {
					$customAddress="";
				}
				
				break;
		}
		
	  
		$addressParts = array_filter($addressParts);
		foreach ($addressParts as $key=>$add) {
			if ($add == " ") {
				unset($addressParts[$key]);
			}
		}
	   
		$addressParts = array_map('trim', $addressParts);
		$addressParts = implode(', ', $addressParts);
		$addressParts = trim($addressParts);
		$address = $addressParts;

		$countryName = "";
		if ($appSettings->add_country_address == 1) {
			if (!empty($address) || !empty($customAddress)) {
				if (!empty($company->country_name)) {
					if (!empty($address)) {
						$address .= ", " . $company->country_name;
					} else {
						$address = $company->country_name;
					}
					$countryName = $company->country_name;
				} elseif (!empty($company->countryName)) {
					if (!empty($address)) {
						$address .= ", " . $company->countryName;
					} else {
						$address = $company->countryName;
					}
					$countryName = $company->countryName;
                } elseif (!empty($company->country)) {
                    if (!empty($address)) {
                        $address .= ", " . $company->country;
                    } else {
                        $address = $company->country;
                    }
				} elseif (!empty($company->countryId)) {
					$country = self::getCountry($company->countryId);
					$countryName = !empty($country)?$country->country_name:"";
					if (!empty($address)) {
						$address .= ", " . $countryName;
					} else {
						$address = $countryName;
						;
					}
				}
			}
		}
		
		if ($appSettings->address_format == 8) {
			$customAddress = str_replace(ADDRESS_COUNTRY, $countryName, $customAddress);
			$address = $customAddress;
		}

		$address = explode(',', $address);
		$address = array_filter($address);
		foreach ($address as $key=>$add) {
			if ($add == " ") {
				unset($address[$key]);
			}
		}
		$address = implode(',', $address);
		$address = trim($address);

		if (empty($address)) {
			return null;
		}

		return $address;
	}

	/**
	 * Generate address based on city and county
	 *
	 * @param $item object item to get the details
	 * @return string
	 */
	public static function getShortAddress($item) {
		$address="";
		$appSettings = JBusinessUtil::getApplicationSettings();

		if ($appSettings->limit_cities_regions == 1) {
			if (!empty($address) || !empty($customAddress)) {
				if (!empty($item->regions)) {
					$item->county = $item->regions[0]->name;
				}

				if (!empty($item->cities)) {
					$item->city   = $item->cities[0]->name;
				}
			}
		}

		$city = "";
		if(!empty($item->city)){
			$city = '<span itemprop="addressLocality">'.$item->city.'</span>';
		}

		$county = "";
		if(!empty($item->county)){
			$county = '<span itemprop="addressRegion">'.$item->county.'</span>';
		}

		$addressParts = array($city,$county);
		$addressParts = array_filter($addressParts);
		
		if (!empty($addressParts)) {
			$address = implode(", ", $addressParts);
		}
		
		return $address;
	}

	/**
	 * Generate an address based on provided params
	 *
	 * @param $street_number
	 * @param $address
	 * @param $area
	 * @param $city
	 * @param $county
	 * @param $province
	 * @param $postalCode
	 * @return string|null
	 */
	public static function getLocationAddressText($street_number, $address, $area, $city, $county, $province, $postalCode, $showOnlyCity = false) {
		$locationItem = new stdClass();
		$locationItem->street_number = $street_number;
		$locationItem->address = $address;
		$locationItem->city = $city;
		$locationItem->county = $county;
		$locationItem->postalCode = $postalCode;
		$locationItem->province = $province;
		$locationItem->area = $area;
		$locationItem->publish_only_city = $showOnlyCity;
		
		return self::getAddressText($locationItem);
	}
	
	/**
	 * Get business listing category
	 *
	 * @param $company boolean company data
	 * @return int|mixed
	 */
	public static function getBusinessListingCategory($company) {
		$categoryId = 0;
		if (!empty($company->mainSubcategory)) {
			$categoryId = $company->mainSubcategory;
		} else {
			if (!empty($company->categories)) {
				$categoryId = $company->categories[0];
			}
		}
		
		return $categoryId;
	}
	
	/**
	 * Get business listing main category and retrieve the category path
	 * @param object $company company data
	 */
	public static function getBusinessCategoryPath($company) {
		$categoryId = self::getBusinessListingCategory($company);
		return self::getCategoryPath($categoryId);
	}

	/**
	 * Get the category path as an array of categories
	 *
	 * @param $categoryId int category id
	 * @return array
	 */
	public static function getCategoryPath($categoryId) {
		if (empty($categoryId)) {
			return array();
		}
		
		$categories = self::getCategories();
	
		$category = self::getCategory($categories, $categoryId);
		$path=array();
		if (!empty($category)) {
			$path[]=$category;
		
			while ($category->parent_id != 1) {
				if (!$category->parent_id) {
					break;
				}
				$category=self::getCategory($categories, $category->parent_id);
				$path[] = $category;
			}
				
			$path = array_reverse($path);
		}
		
		return $path;
	}

	/**
	 * Retrieve the categories from database or from the present cache
	 * @return mixed
	 */
	public static function getCategories() {
		$instance = JBusinessUtil::getInstance();
		
		if (!isset($instance->categories)) {
			JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
			$categoryTable =JTable::getInstance("Category", "JBusinessTable");
			$categories = $categoryTable->getAllCategories(0,true);
			$instance->categories = $categories;
		}
		return $instance->categories;
	}

	/**
	 * Get a category from cache based on category id
	 *
	 * @param $categories array categories array
	 * @param $categoryId int category id
	 * @return mixed|null
	 */
	public static function getCategory($categories, $categoryId) {
		if (empty($categories) || empty($categoryId)) {
			return null;
		}
		
		foreach ($categories as $category) {
			if ($category->id == $categoryId) {
				return $category;
			}
		}
		return null;
	}

	/**
	  * Get a category from cache based on category id
	 *
	 * @param $categoryId
	 * @return mixed|null
	 */
	public static function getCategoryItem($categoryId) {
		$categories = self::getCategories();
		if (empty($categories)) {
			return null;
		}
		
		foreach ($categories as $category) {
			if ($category->id == $categoryId) {
				return $category;
			}
		}
		
		return null;
	}

	/**
	 * Get the available languages on the CMS
	 *
	 * @return array
	 */
	static function getAvailableLanguages() {
		$languages = array(
			"Chinese Simplified" => "zh-CN", "Ã�Â Ã‘Æ’Ã‘ï¿½Ã‘ï¿½Ã�ÂºÃ�Â¸Ã�Â¹" => "ru-RU", "Portuguese" => "pt-PT", "English" => "en-US", "English CA" => "en-CA", "Polish" => "pl-PL",
			"Arabic Unitag" => "ar-AA", "Finnish" => "fi-FI",
			"Dutch" => "nl-NL", "Czech" => "cs-CZ", "Hungarian" => "hu-HU", "Hebrew" => "he-IL",
			"Romanian" => "ro-RO", "Spanish" => "es-ES", "French" => "fr-FR",
			"Greek" => "el-GR", "Ukrainian" => "uk-UA", "German" => "de-DE", "PortuguÃƒÂªs do Brasil" => "pt-BR",
			"Italian" => "it-IT", "Swedish" => "sv-SE", "TÃƒÂ¼rkÃƒÂ§e" => "tr-TR"
		);
		asort($languages);

		return $languages;
	}

	static function getLanguages() {

		$appSettings = JBusinessUtil::getApplicationSettings();
		$languages = $appSettings->available_languages;
		$languages = explode(",", $languages);
		$availableLanguages = JBusinessUtil::getAvailableLanguages();

		$result = array();
		foreach ($availableLanguages as $key => $value) {
			if (in_array($value, $languages))
				$result[$key] = $value;
		}

		asort($result);
		return $result;
	}



	/**
	 * Get the current language tag
	 *
	 * @return string
	 */
	static function getLanguageTag() {

		$user = self::getUser();
		$lang =  get_locale();
		if (!empty($user->ID)) {
			$lang = get_user_locale($user->ID);
		}

		if ($lang == "ar") {
			$lang = "ar_AA";
		}

		$lang = str_replace("_", "-", $lang);

		//dump($lang);

		return $lang;
	}

	/**
	 * Get the current language code
	 *
	 * @return mixed
	 */
	public static function getCurrentLanguageCode() {
		$lang = get_locale();
		$lang = explode("_", $lang);

		return $lang[0];
	}

	/**
	 * Generate the list of available categories for drop down use
	 *
	 * @param $published
	 * @param null $type
	 * @param null $catId
	 * @param bool $showRoot
	 * @return mixed
	 */
	public static function getCategoriesOptions($published, $type = CATEGORY_TYPE_BUSINESS, $catId = null, $showRoot = false, $onlyMain = false) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('a.id AS value, a.name AS text, a.level, a.published, a.parent_id')
		->from('#__jbusinessdirectory_categories AS a')
		->join('LEFT', $db->quoteName('#__jbusinessdirectory_categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		
		if (!empty($catId)) {
			$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_categories') . ' AS p ON p.id = ' . (int) $catId)
			->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		}
		
		if (($published)) {
			$query->where('a.published = 1');
		}


		if (($onlyMain)) {
			$query->where('a.parent_id = 1');
		}

		if (($type)) {
			$query->where('(a.type IN (0,' . (int) $type.'))');
		}
		
		if (!$showRoot) {
			$query->where('a.id >1');
		}
		
		$query->group('a.id, a.name, a.level, a.lft, a.rgt, a.parent_id, a.published')
		->order('a.lft ASC');
		
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		$categoryTranslations = JBusinessDirectoryTranslations::getInstance()->getCategoriesTranslations();
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++) {
			if ($options[$i]->published == 1) {
				if (!empty($categoryTranslations[$options[$i]->value])) {
					$options[$i]->text = $categoryTranslations[$options[$i]->value]->name;
				}
				if ($showRoot) {
					$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
				} else {
					if($options[$i]->level > 1){
						$options[$i]->text = str_repeat('- ', $options[$i]->level-1) . $options[$i]->text;
					}
				}
			} else {
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' . $options[$i]->text . ']';
			}
		}
		
		return $options;
	}


	/**
	 * Retrive the ids of all container categories
	 */
	public static function getContainerCategories($type = CATEGORY_TYPE_BUSINESS) {
		$db =JFactory::getDBO();
		$query = "select id from #__jbusinessdirectory_categories where user_as_container = 1 and type = $type";
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		$result = array();
		if(!empty($categories)){
			foreach($categories as $category){
				$result[] = $category->id;
			}
		}

		return $result;
	}


	/**
	 * Check if category is leaf
	 */

	public static function getLeafs($categories){
		$leafIds = array();
		if(empty($categories)){
			return $leafIds;
		}
	
		$parents = array();
		foreach($categories as $cat){
			$parents[$cat->parent_id] = $cat->parent_id;
		}

		foreach($categories as $cat){
			if(!isset($parents[$cat->value])){
				$leafIds[] = $cat->value;
			}
		}

		return $leafIds;
	}


	/**
	  * Generate the list of available companies for drop down use
	 *
	 * @param null $companyId
	 * @param null $userId
	 * @return mixed
	 */
	public static function getCompaniesOptions($companyId=null, $userId=null) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('id, name')
		->from('#__jbusinessdirectory_companies as c')
		->group('c.id')
		->order('c.name ASC');

		if (!empty($companyId)) {
			$query->where("c.id != $companyId");
		}
		
		if (!empty($userId)) {
			$query->where("c.userId = $userId");
		}
		
		$query->where("c.state = 1");

		$db->setQuery($query, 0, 50);
		
		$options = $db->loadObjectList();
		
		return $options;
	}

	/**
	 *  Generate the list of available events for drop down use
	 *
	 * If eventId is set then the event with that id will not be retrieved
	 * If userId is set then the function will retrieve only the events belonging to that user
	 *
	 * @param null $eventId event id
	 * @param null $userId user id
	 * @return mixed all events returned
	 * @since 5.3.1
	 */
	public static function getEventsOptions($eventId=null, $userId=null) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, name')
			->from('#__jbusinessdirectory_company_events as c')
			->group('c.id')
			->order('c.name ASC');

		if (!empty($eventId)) {
			$query->where("c.id != $eventId");
		}

		if (!empty($userId)) {
			$query->where("c.user_id = $userId");
		}

		$db->setQuery($query, 0, 20);

		$options = $db->loadObjectList();

		return $options;
	}

	/**
	 * Generate the list of available speakers for drop down use
	 *
	 * @return list of speakers
	 */
	public static function getSpeakersOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_conference_speakers')
		->group('id')
		->order('title ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}

	/**
	 * Generate the list of available sessions for drop down use
	 *
	 * @return mixed
	 */
	public static function getSessionsOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_conference_sessions')
		->group('id')
		->order('name ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}

	/**
	 * Generate the list of available conferences for drop down use
	 *
	 * @return mixed
	 */
	public static function getConferenceOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('id AS value, name AS text')
		->from('#__jbusinessdirectory_conferences')
		->group('id')
		->order('name ASC');
		
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return $options;
	}
	
	/**
	 * Get review question types
	 */
	public static function getReviewQuestiosnTypes() {
		$types = array();
		$type = new stdClass();
		$type->value = 0;
		$type->text = JTEXT::_("LNG_TEXT");
		$types[] = $type;
		$type = new stdClass();
		$type->value = 1;
		$type->text = JTEXT::_("LNG_YES_NO_QUESTION");
		$types[] = $type;
		$type = new stdClass();
		$type->value = 2;
		$type->text = JTEXT::_("LNG_RATING");
		$types[] = $type;
	
		return $types;
	}



	/**
	 * Get payment occurances options
	 */
	public static function getPaymentOccurances() {
		$types = array();

		for($i=2;$i<53;$i++){
			$type = new stdClass();
			$type->value = $i;
			$type->text = "$i";
			$types[] = $type;
		}
		
		return $types;
	}

	/**
	 * Convert an amount to the current price format from general settings
	 *
	 * @param $amount double price format
	 * @param $currencyId int currency id
	 * @param bool $skip_formating
	 * @return string
	 */
	public static function getPriceFormat($amount, $currencyId = null, $skip_formating = false) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$dec_point=".";
		$thousands_sep = ",";
		
		if ($appSettings->amount_separator==2) {
			$dec_point=",";
			$thousands_sep = ".";
		}
		
		$currencyString = $appSettings->currency_name;
		if ($appSettings->currency_display==2) {
			$currencyString = $appSettings->currency_symbol;
		}
		
		$amountString = $amount;
		if (!$skip_formating && is_numeric($amount)) {
			$amountString = number_format($amount, $appSettings->number_of_decimals, $dec_point, $thousands_sep);
		}

		if (!empty($currencyId)) {
			$currency = self::getCurrency($currencyId);
			$currencyString = $currency->currency_name;
			if ($appSettings->currency_display==2) {
				$currencyString = $currency->currency_symbol;
			}
		}
		
		if ($appSettings->currency_location==1) {
			if($appSettings->currency_display==2){
				$result = $currencyString.$amountString;
			}else{
				$result = $currencyString." ".$amountString;
			}
		} else {
			$result = $amountString." ".$currencyString;
		}

		return $result;
	}


	/**
	 * Convert an amount to the current price format from general settings
	 *
	 * @param $amount double price format
	 * @param $currencyId int currency id
	 * @param bool $skip_formating
	 * @return string
	 */
	public static function getHtmlPriceFormat($amount, $currencyId = null, $skip_formating = false) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$dec_point=".";
		$thousands_sep = ",";

		if(empty($amount)){
			return $amount;
		}

		if ($appSettings->amount_separator==2) {
			$dec_point=",";
			$thousands_sep = ".";
		}
		
		$currencyString = $appSettings->currency_name;
		if ($appSettings->currency_display==2) {
			$currencyString = $appSettings->currency_symbol;
		}

		if (!empty($currencyId)) {
			$currency = self::getCurrency($currencyId);
			$currencyString = $currency->currency_name;
			if ($appSettings->currency_display==2) {
				$currencyString = $currency->currency_symbol;
			}
		}
		
		$amountString = $amount;
		if (!$skip_formating) {
			$num = strval(number_format((float)$amount, $appSettings->number_of_decimals, $dec_point, $thousands_sep));
			$numParts = explode($dec_point, $num);
			
			$whole = $numParts[0];
			$frac = "";
			if(count($numParts) == 2){
				$frac = $numParts[1];
			}
			
			$amountString = '<span class="price-whole">'.$whole.'</span>';
			//$amountString .= '<span class="price-dec">'.$dec_point.'</span>';
			if(!empty($frac)){
				$amountString .= '<span class="price-fraction">'.$frac.'</span>';
			}
		}
		
		if ($appSettings->currency_location==1) {
			if($appSettings->currency_display==2){
				$result = $amountString = '<span class="price-symbol">'.$currencyString.'</span>'.$amountString;
			}else{
				$result = '<span class="price-currency">'.$currencyString.'</span>'." ".$amountString;
			}
		} else {
			$result = $amountString." ".'<span class="price-currency">'.$currencyString.'</span>';
		}

		$formattedPrice = '<span class="price-formatted">'.$result.'</span>';
		
		$amountString = $amount;
		if (!$skip_formating) {
			$amountString = number_format((float)$amount, $appSettings->number_of_decimals, $dec_point, $thousands_sep);
		}

		if (!empty($currencyId)) {
			$currency = self::getCurrency($currencyId);
			$currencyString = $currency->currency_name;
			if ($appSettings->currency_display==2) {
				$currencyString = $currency->currency_symbol;
			}
		}
		
		if ($appSettings->currency_location==1) {
			if($appSettings->currency_display==2){
				$result = $currencyString.$amountString;
			}else{
				$result = $currencyString." ".$amountString;
			}
		} else {
			$result = $amountString." ".$currencyString;
		}

		$result = $formattedPrice . '<span class="price-original">'.$result.'</span>';
		return $result;
	}

	/**
	 * Get the currency data based on id
	 *
	 * @param $currencyId int currency Id
	 * @return mixed
	 */
	public static function getCurrency($currencyId) {
		$instance = JBusinessUtil::getInstance();
		$varName = "currency".$currencyId;
		if (isset($instance->$varName)) {
			return $instance->$varName;
		}
		
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$currencyTable = JTable::getInstance("Currency", "JTable");
		$instance->$varName = $currencyTable->getCurrencyById($currencyId);
		
		return $instance->$varName;
	}

	/**
	 * Get price discount percentage
	 *
	 * @param $specialPrice
	 * @param $price
	 * @return string
	 */
	public static function getPriceDiscount($specialPrice, $price) {
		$percentChange = (1 - $specialPrice / $price) * 100;
		$discount =  number_format((float)$percentChange,0);
		return $discount;
	}

	/**
	 * Convert price to mysql format
	 *
	 * @param $price string price
	 * @return string|string[]
	 */
	public static function convertPriceToMysql($price)
    {
        if (empty($price)) {
            return 0;
        }

        $appSettings = JBusinessUtil::getApplicationSettings();
        if ($appSettings->number_of_decimals == 0) {
            $price .=".00";
		}

		if (strpos($price, '.') && strpos($price, ',')) {
			if (strlen($price)>strrpos($price, ',') && strlen($price)>strrpos($price, '.')) {
				if (abs(strrpos($price, '.') - strrpos($price, ',')) == 4) {
					if (strrpos($price, '.') < strrpos($price, ',')) {
						$price = str_replace('.', '', $price);
						$pos = strrpos($price, ',');
						$price = substr_replace($price, '.', $pos, '1');
						$price = str_replace(',', '', $price);
					} else {
						$price = str_replace(',', '', $price);
						$pos = strrpos($price, '.');
						$price = substr_replace($price, ',', $pos, '1');
						$price = str_replace('.', '', $price);
						$price = str_replace(',', '.', $price);
					}
				} else {
					$price = str_replace('.', '', $price);
					$price = str_replace(',', '', $price);
				}
			} else {
				$price = str_replace('.', '', $price);
				$price = str_replace(',', '', $price);
			}
		} else {
			if (strlen($price) > (strrpos($price, ',')+1) || strlen($price) > (strrpos($price, '.')+1)) {
				if (substr_count($price, ',') > 1 || substr_count($price, '.') > 1) {
					if (strpos($price, '.')) {
						if (strlen($price) > (strrpos($price, '.')+1)) {
							$pos = strrpos($price, '.');
							$price = substr_replace($price, ',', $pos, '1');
							$price = str_replace('.', '', $price);
							$price = str_replace(',', '.', $price);
						} else {
							$price = str_replace('.', '', $price);
							$price = str_replace(',', '', $price);
						}
					} else {
						if (strlen($price) > (strrpos($price, ',')+1)) {
							$pos = strrpos($price, ',');
							$price = substr_replace($price, '.', $pos, '1');
							$price = str_replace(',', '', $price);
						} else {
							$price = str_replace('.', '', $price);
							$price = str_replace(',', '', $price);
						}
					}
				} else {
					$price = str_replace('.', '.', $price);
					$price = str_replace(',', '.', $price);
				}
			} else {
				$price = str_replace('.', '', $price);
				$price = str_replace(',', '', $price);
			}
		}
	  
		$price = str_replace(' ', '', $price);
		
		if (empty($price)) {
			$price = 0;
		} else {
			$price = doubleval($price);
		}

		return $price;
	}

	/**
	 * Convert price from mysql format
	 *
	 * @param $price string price
	 * @return string
	 */
	public static function convertPriceFromMysql($price) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$dec_point=".";
		$thousands_sep = ",";

		if ($appSettings->amount_separator==2) {
			$dec_point=",";
			$thousands_sep = ".";
		}

		$price = number_format((float)$price, $appSettings->number_of_decimals, $dec_point, $thousands_sep);

		return $price;
	}

	/**
	 * Load extension language file based on the current language tag for admin area
	 */
	public static function loadAdminLanguage() {
		$language = JFactory::getLanguage();
		$language_tag =  self::getLanguageTag();

		//dump($language_tag);

		$x = $language->load(
			'com_jbusinessdirectory',
			dirname(BD_LANGUAGE_FOLDER_PATH),
			$language_tag,
			true
		);


		$language_tag = str_replace("-", "_", $language->getTag());
		setlocale(LC_TIME, $language_tag . '.UTF-8');
	}

	/**
	 * Load extension language file based on the current language tag for front-end
	 */
	public static function loadSiteLanguage() {
		$language = JFactory::getLanguage();
		$language_tag =  self::getLanguageTag();

		$x = $language->load(
			'com_jbusinessdirectory',
			dirname(BD_LANGUAGE_FOLDER_PATH), 
			$language_tag,
			true
		);
		
		$language_tag = str_replace("-", "_", $language->getTag());
		setlocale(LC_TIME, $language_tag . '.UTF-8');
	}
	
	
	/**
	 * Remove a file directory
	 *
	 * @param $dir
	 */
	public static function removeDirectory($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") {
						rmdir($dir."/".$object);
					} else {
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	/**
	 * Removes a directory and all its content recursively
	 *
	 * @param $dir string path of the directory
	 *
	 * @return bool
	 *
	 * @since 5.1.5
	 */
	public static function recursiveRemoveDirectory($dir) {
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file") && !is_link("$dir/$file")) ? self::recursiveRemoveDirectory("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
	
	/**
	 * Convert a day to a String
	 * @param unknown_type $day
	 * @param unknown_type $abbr
	 */
	public static function dayToString($day, $abbr = false) {
		$date = new JDate();
		return addslashes($date->dayToString($day, $abbr));
	}
	
	/**
	 * Convert a month to a String
	 * @param unknown $month
	 * @param boolean $abbr
	 * @return string
	 */
	public static function monthToString($month, $abbr = false) {
		$date = new JDate();
		return addslashes($date->monthToString($month, $abbr));
	}
	
	
	/**
	 * Calculate the difference between 2 dates in days
	 *
	 * @param unknown $startData
	 * @param unknown $endDate
	 * @return number
	 */
	public static function getNumberOfDays($startData, $endDate) {
		$nrDays = floor((strtotime($endDate) - strtotime($startData)) / (60 * 60 * 24));
	
		return $nrDays;
	}
	
	/**
	 * Get the day of month from provided date
	 * @param unknown_type $date
	 */
	public static function getDayOfMonth($date) {
		if (empty($date)) {
			return "";
		}
		
		return date("j", strtotime($date));
	}

	/**
	 * Get the day of week from provided date
	 * @param unknown_type $date
	 */
	public static function getWeekDay($date) {
		if (empty($date)) {
			return "";
		}

		$date = JFactory::getDate($date);
		$dateS = $date->format('D');
		
		return $dateS;
	}

	/**
	 * Get month as string from provided date
	 * @param unknown_type $date
	 */
	public static function getMonth($date) {
		if (empty($date)) {
			return "";
		}
		$date = JFactory::getDate($date);
		return $date->format('M');
	}
	
	/**
	 * Get year from provided date
	 * @param unknown_type $date
	 * @return string
	 */
	public static function getYear($date) {
		if (empty($date)) {
			return "";
		}
		
		$date = JFactory::getDate($date);
		return $date->format('Y');
	}
	
	/**
	 * Include validation required files
	 */
	public static function includeValidation() {
		JBusinessUtil::enqueueStyle('libraries/validation-engine/validationEngine.jquery.css');
		$tag = JBusinessUtil::getCurrentLanguageCode();
		
		if (!file_exists(JPATH_COMPONENT_SITE.'/assets/libraries/validation-engine/jquery.validationEngine-'.$tag.'.js')) {
			$tag ="en";
		}
		
		JBusinessUtil::enqueueScript('libraries/validation-engine/jquery.validationEngine-'.$tag.'.js');
		JBusinessUtil::enqueueScript('libraries/validation-engine/jquery.validationEngine.js');
	}

	/**
	 * Calculate the elapsed time from a timestamp
	 *
	 * @param unknown_type $datetime
	 * @param unknown_type $full
	 * @return string
	 */
	public static function convertTimestampToAgo($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) {
			$string = array_slice($string, 0, 1);
		}
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	/**
	 * Remove files/images that are not linked anymore
	 * @param array $usedFiles
	 * @param unknown_type $rootFolder
	 * @param unknown_type $filesFolder
	 */
	public static function removeUnusedFiles(array $usedFiles, $rootFolder, $filesFolder) {
		
		$directoryPath = JBusinessUtil::makePathFile($rootFolder.$filesFolder);
		// dump($directoryPath);
		// $usedFiles -> array of the filename of the files
		// $filesFolder -> example: 'items/id/'
		// $rootFolder -> example: 'pictures'

		if(!empty($usedFiles)){
			$usedFiles = array_filter($usedFiles);
		}

		$usedFiles[]=JBusinessUtil::makePathFile($filesFolder)."index.html";
		
		//dump("used");
		//dump($usedFiles);
		foreach ($usedFiles as $file) {
			$file = JBusinessUtil::makePathFile($file);
		}
		
		$allFiles = array();
		if (file_exists($directoryPath)) {
			foreach (scandir($directoryPath, 1) as $singleFile) {
				//dump($singleFile);
				if($singleFile == "." || $singleFile == ".."){
					continue;
				}
				array_push($allFiles, JBusinessUtil::makePathFile($filesFolder.$singleFile));
			}
		}

		//dump("all");
		//dump($allFiles);
		$unusedFiles = array_diff($allFiles, $usedFiles);
		// dump("unused Files");
		//dump($unusedFiles);
		if(count($unusedFiles)>4){
			//exit;
		}
		foreach ($unusedFiles as $unusedFile) {

			// dump($unusedFile);
			if (is_file($rootFolder.$unusedFile)) {
				// dump("delete ".$rootFolder.$unusedFile);
				//unlink($rootFolder.$unusedFile);
			}
		}

		//exit;
		return true;
	}

	/**
	 * Move a file to a folder
	 *
	 * @param $picture_path string picture path
	 * @param $itemId int item id
	 * @param $oldId int old item id for the item
	 * @param $type int type of the file
	 * @return string|void
	 * @throws Exception
	 */
	public static function moveFile($picture_path, $itemId, $oldId, $type) {
		$path_new = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH .$type.($itemId)."/");
		
		//prepare photos
		$path_old = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH .$type.($oldId)."/");
		if (!empty($picture_path)) {
			$parts = explode("/", $picture_path);
			$oldId = $parts[2];
			$path_old = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH .$type.($oldId)."/");
		}
			
		$file_tmp = JBusinessUtil::makePathFile($path_old.basename($picture_path));
		//dump($file_tmp);
		if (!is_file($file_tmp)) {
			return;
		}
		//dump("is file");
		if (!is_dir($path_new)) {
			if (!@mkdir($path_new)) {
				throw( new Exception(JText::_("LNG_CANNOT_CREATE_DIRECTORY")) );
			}
		}
		
		// dbg(($path_old.basename($picture_path).",".$path_new.basename($picture_path)));
		//dump($path_old.basename($picture_path) != $path_new.basename($picture_path));
		// exit;
		if ($path_old.basename($picture_path) != $path_new.basename($picture_path)) {
			//dump("move files");
			if ($oldId==0) {
				if (@rename($path_old.basename($picture_path), $path_new.basename($picture_path))) {
					$picture_path	 = $type.($itemId).'/'.basename($picture_path);
				//@unlink($path_old.basename($pic->room_picture_path));
				} else {
					throw( new Exception(JText::_("LNG_CANNOT_CHANGE_NAME")) );
				}
			} else {
				if (@copy($path_old.basename($picture_path), $path_new.basename($picture_path))) {
					$picture_path	 = $type.($itemId).'/'.basename($picture_path);
				//@unlink($path_old.basename($pic->room_picture_path));
				} else {
					throw( new Exception(JText::_("LNG_CANNOT_COPY_ITEM")) );
				}
			}
		}
		
		return $picture_path;
	}
	

	/**
	 * Get the type and thumbnail of the sound based on the sound url (SoundCloud and Spotify supported).
	 *
	 * @param $url
	 * @return array()
	 */
	public static function getSoundDetails($url) {
		$data = array();

		// If it's a SoundCloud sound
		if (strpos($url, 'soundcloud') > 0) {
		
			//Get the JSON data of song details with embed code from SoundCloud oEmbed
			$getValues=file_get_contents('http://soundcloud.com/oembed?format=js&maxheight=300&url='.$url.'&iframe=true');
			//Clean the Json to decode
			$decodeiFrame=substr($getValues, 1, -2);
			//json decode to convert it as an array
			$jsonObj = json_decode($decodeiFrame);

			//$jsonObj->html = str_replace('height="400"', 'height="260"', $jsonObj->html);
			//$jsonObj->html = str_replace('visual=true', 'show_user=true', $jsonObj->html);

			$data = array(
				'iframe' => $jsonObj->html,
				'type' => 'soundcloud'
			);
		}
		// If it's a Spotify sound
		elseif (strpos($url, 'spotify') > 0) {
			if(strpos($url, '?') > 0) {
				$trackURL = substr($url, 0, strpos($url, '?'));
			} else {
				$trackURL = $url;
			}
			$trackURL = explode('/',$trackURL);
			$trackId = end($trackURL);

			if(strpos($url, 'track') > 0) {
				$type = 'track';
			} else if (strpos($url, 'episode') > 0) {
				$type = 'episode';
			} else if (strpos($url, 'playlist') > 0) {
				$type = 'playlist';
			}
			$iframe = '<iframe style="border-radius:5px" src="https://open.spotify.com/embed/'.$type.'/'.$trackId.'?utm_source=generator&theme=0" width="100%" height="360" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>';
			$data = array(
				'iframe' => $iframe,
				'type' => 'spotify'
			);
		}

		return $data;
	}

	/**
	 * Get the type and thumbnail of the video based on the video url (Youtube, Vimeo and Tiktok supported).
	 *
	 * @param $url
	 * @return array()
	 */
	public static function getVideoDetails($url) {
		$data = array();

		$iframe = strpos($url, 'iframe');
		if (!empty($iframe)) { //if the $url is an iframe
			preg_match('/src="([^"]+)"/', $url, $match);
			if (isset($match[1])) {
				$url = $match[1];
			}
		}

		// If it's a youtube video
		if (strpos($url, 'youtu') > 0) {
			preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);

			if (isset($matches[1])) {
				$id = $matches[1]; // We need the video ID to find the thumbnail
			} else {
				$id = "";
			}

			$thumbnail = 'https://img.youtube.com/vi/'.$id.'/0.jpg';
			
			$data = array(
				'url' => 'https://www.youtube-nocookie.com/embed/'.$id.'?rel=0',
				'type' => 'youtube',
				'video-id' => $id,
				'thumbnail' => $thumbnail
			);
		}
		// If it's a vimeo video
		elseif (strpos($url, 'vimeo') > 0) {
			preg_match("/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $url, $matches);
			$id = $matches[3];
			$hash = unserialize(self::getURLData("https://vimeo.com/api/v2/video/".$id.".php"));
			$thumbnail = $hash[0]['thumbnail_large'];

			$data = array(
				'url' => 'https://vimeo.com/'.$id,
				'type' => 'vimeo',
				'video-id' => $id,
				'thumbnail' => $thumbnail
			);
		}
		// If it's a tiktok video
		elseif (strpos($url, 'tiktok') > 0) {
			preg_match("/^(?:http(?:s)?:\/\/)?(?:(?:www)\.(?:tiktok\.com)(?:\/)(?!foryou)(@[a-zA-z0-9]+)(?:\/)(?:video)(?:\/)([\d]+)|(?:m)\.(?:tiktok\.com)(?:\/)(?!foryou)(?:v)(?:\/)?(?=([\d]+)\.html))/", $url, $matches);
			$userName = $matches[1];
			$id = $matches[2];
			$videoData = json_decode(self::getURLData("https://www.tiktok.com/oembed?url=https://www.tiktok.com/".$userName."/video/".$id));
			$thumbnail = $videoData->thumbnail_url;

			$data = array(
				'url' => 'https://tiktok.com/embed/v2'.'/'.$id,
				'type' => 'tiktok',
				'video-id' => $id,
				'thumbnail' => $thumbnail
			);
		}
		// If it's a facebook video
		elseif (strpos($url, 'facebook') > 0) {
			preg_match('/(?<=src=").*?(?=[\*"])/', $url, $matches);
			// preg_match('/(?<=width=").*?(?=[\*"])/', $url, $matchesWidth);
			// preg_match('/(?<=height=").*?(?=[\*"])/', $url, $matchesHeight);

			$videoUrl = $matches[0];
			// $videoWidth = $matchesWidth[0];
			// $videoHeight = $matchesHeight[0];
			// $thumbnail = $matches[0];

			$data = array(
				'url' => $videoUrl,
				'type' => 'facebook',
				// 'width' => $videoWidth,
				// 'height' => $videoHeight,
				'thumbnail' => $thumbnail
			);
		}
		// If it's not supported
		else {
			$data = array(
				'url' => 'https://www.youtube.com',
				'type' => 'unsupported',
				'thumbnail' => 'placehold.it/400x300?text=UNSUPPORTED+FORMAT'
			);
		}

		return $data;
	}
	
	/**
	 * Retrieve current version from manifest file
	 * @return  string versino number
	 */
	public static function getCurrentVersion() {
		return WP_BUSINESSDIRECTORY_VERSION_NUM;
	}

	/**
	 * Format the id of a booking to a specific format
	 *
	 * @param $bookingId
	 * @return string
	 */
	public static function formatBookingId($bookingId) {
		return str_pad($bookingId, LENGTH_ID_BOOKING, "0", STR_PAD_LEFT);
	}
	
	/**
	 * Method that gets the booking open date-times of a particular event, compares their values
	 * to the current time and returns true or false depending on whether the current date-time is
	 * considered as valid.
	 *
	 * @param $event	Object containing the booking dates and times
	 * @return bool		Boolean value
	 */
    public static function isBookingAvailable($event) {
        // Booking hours

        $startTime = $event->booking_open_time;
        $endTime = $event->booking_close_time;


		//set timezone and create a new object date time for that timezone
		$original = new DateTime("now", new DateTimeZone('GMT'));

		//TODO replace with JBusinessUtil::getCurrentTime
		$time_zone=intval($event->time_zone);
		$timezoneName = timezone_name_from_abbr("", $time_zone * 3600, false);
		$modified = $original->setTimezone(new DateTimezone($timezoneName));

		$currentTime = null;
		$currentTime = $modified->format('Y-m-d H:i:s');


        // Create Y-m-d H:i:s format if start/end hour is available, if not, 00:00:00 is set as start hour and 23:59:59 as end hour
        if (!empty($event->booking_open_time)) {
            $start = $event->booking_open_date . ' ' . $event->booking_open_time;
        } else {
            $start = $event->booking_open_date . ' 00:00:00';
        }

        if (!empty($event->booking_close_time)) {
            $end = $event->booking_close_date . ' ' . $event->booking_close_time;
        } else {
            $end = $event->booking_close_date . ' 23:59:59';
        }

        // If date is empty or set to 0, empty the $start and $end values
        if (self::emptyDate($event->booking_open_date)) {
            $start = '';
        }
        if (self::emptyDate($event->booking_close_date)) {
            $end = '';
        }

        if (!empty($start) || !empty($end)) {
            // Check the Booking Dates
            if (!empty($start) && !empty($end)) {

                if (strtotime($start) <= strtotime($currentTime) && strtotime($currentTime) < strtotime($end)) {
                    return true;
                } else {
                    return false;
                }
            } elseif (!empty($start)) {
                if (strtotime($start) <= strtotime($currentTime)) {
                    return true;
                } else {
                    return false;
                }
            } elseif (!empty($end)) {
                if (strtotime($end) > strtotime($currentTime)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return true;
    }


    /**
	 * Checks if a date is empty or equal to the date equivalent of zero, and if so, returns true.
	 *
	 * @param $date
	 * @return bool
	 */
	public static function emptyDate($date) {
		if (!empty($date)) {
			if ($date == '0000-00-00' || $date == '00-00-0000' || $date == '0000-00-00 00:00:00' || $date == '00-00-0000 00:00:00') {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Checks if the current date is inside the date interval determined by the $startDate and $endDate
	 *
	 * @param $startDate string containing the start Date of the interval
	 * @param $endDate	string containing the end Date of the interval
	 * @param $currentDate string containing the date that will be checked, null if the date that needs to be checked is the actual day
	 * @param bool|true $includeEndDate determines whether the end date
	 * will be included in the interval
	 * @param bool|true $allowEmptyBoundaries determines
	 * whether start or end date may be empty
	 * @return bool If date is in between the specified interval, true is returned.
	 * False is returned otherwise or if one of the boolean parameters condition is violated
	 *
	 * Note: if both start and end values are empty, and $allowEmptyBoundaries is set to true,
	 * true will be returned.
	 */
	public static function checkDateInterval($startDate, $endDate, $currentDate = null, $includeEndDate=true, $allowEmptyBoundaries=true) {
		if ($currentDate == null) {
			$currentDate = date("Y-m-d H:i:s");
		}

		if (!self::emptyDate($startDate) || $allowEmptyBoundaries) {
			$start = $startDate;
		} else {
			return false;
		}

		if (!self::emptyDate($endDate) || $allowEmptyBoundaries) {
			$end = $endDate;
		} else {
			return false;
		}

		if ($includeEndDate && !self::emptyDate($end)) {
			$end .= ' 23:59:59';
		}


		if (((strtotime($start) <= strtotime($currentDate)) || self::emptyDate($start))
			&& ((strtotime($currentDate) < strtotime($end)) || self::emptyDate($end))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method that returns all dates between a provided date interval
	 *
	 * @param $first string First date of interval
	 * @param $last string Last date of interval
	 * @param string $step The iteration step (in days) between the dates
	 * @param string $output_format The output format of the dates
	 * @return array An array containing all the dates between the interval
	 */
	public static function getAllDatesInInterval($first, $last, $step = "+1 day", $output_format = "d-m-Y") {
		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while ($current <= $last) {
			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}

	/**
	 * Get region id by country id and region name
	 */
	public static function getCityByNameAndRegion($regionId, $cityName){
		
		if(!empty($cityName)){
			$regionFilter = "";
			if(!empty($regionId)){
				$regionFilter = "and region_id = $regionId ";
			}

			$db =JFactory::getDBO();
			$query = "select * from #__jbusinessdirectory_cities where name='$cityName' $regionFilter";
			$db->setQuery($query);
			$city = $db->loadObject();

			return $city;
		}

		return null;
	}

	/**
     * Get city by id
     */
    public static function getCityById($cityId){
        
        if(!empty($cityId)){
            $db =JFactory::getDBO();
            $query = "select * from #__jbusinessdirectory_cities where id=$cityId";
            $db->setQuery($query);
            $city = $db->loadObject();

            return $city;
        }

        return null;
    }

	/**
	 * Get region id by country id and region name
	 */
	public static function getRegionByNameAndCountry($countryId, $regionName){
		
		if(!empty($regionName)){
			$countryFilter = "";
			if(!empty($countryId)){
				$countryFilter = "and country_id = $countryId";
			}
			$db =JFactory::getDBO();
			$query = "select * from #__jbusinessdirectory_regions where name='$regionName' $countryFilter";
			$db->setQuery($query);
			$region = $db->loadObject();

			return $region;
		}

		return null;
	}

	/**
     * Get region by id
     */
    public static function getRegionById($regionId){
        
        if(!empty($regionId)){
          
            $db =JFactory::getDBO();
            $query = "select * from #__jbusinessdirectory_regions where id=$regionId";
            $db->setQuery($query);
            $region = $db->loadObject();

            return $region;
        }

        return null;
    }

    /**
     * Get country based on id
     */
    public static function getCountryName($countryId){
        $instance = JBusinessUtil::getInstance();
        $countryName = "";

        if (empty($countryId)) {
            return "";
        }else{
            if(is_int($countryId)){
                $country =  $instance::getCountry($countryId);
                if(!empty($country)){
                    $countryName = $country->country_name;
                }
            }else{
                return "";
            }
        }

        return $countryName;
    }


   /**
	 * Get country based on id
	 */
	public static function getCountry($countryId) {
		$instance = JBusinessUtil::getInstance();

		if(empty($countryId)){
		    return null;
        }

		if(!isset($instance->countries)){
			$instance->countries = array();
		}

		if (!isset($instance->countries[$countryId])) {
			$db =JFactory::getDBO();
			$query = "select * from #__jbusinessdirectory_countries where id = $countryId ";
			$db->setQuery($query);
			$country = $db->loadObject();
			$instance->countries[$countryId] = $country;
		}
		
		return $instance->countries[$countryId];
	}

	/**
	 * Get country based on id
	 */
	public static function getCountryByCode($countryCode) {
		
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_countries where country_code = '$countryCode' ";
		$db->setQuery($query);
		$country = $db->loadObject();
		
		return $country;
	}

    /**
	 * Get country based on name
	 */
	public static function getCountryByName($country) {
		$instance = JBusinessUtil::getInstance();

		if(empty($country)){
		    return null;
        }
		
		if(!isset($instance->countries)){
			$instance->countries = array();
		}

		if (!isset($instance->countries[$country])) {
			$db =JFactory::getDBO();
			$query = "select * from #__jbusinessdirectory_countries where country_name = '$country' ";
			$db->setQuery($query);
			$countryObj = $db->loadObject();
			$instance->countries[$country] = $countryObj;
		}
		
		return $instance->countries[$country];
	}


	/**
	 * Get restricted countries
	 * @return array
	 */
	public static function getCountryRestriction() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$countries = $appSettings->country_ids;
		$countriesArray = array();
		if ($countries != '') {
			$countriesArray = explode(',', $countries);
		}

		$limitCountries = array();
		foreach ($countriesArray as $key => $value) {
			$country = JBusinessUtil::getCountry($value)->country_code;
			$limitCountries[] = $country;
		}
		return $limitCountries;
	}

	
	/**
	 * Get type name
	 */
	public static function getTypeName($typeId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_types where id = $typeId ";
		$db->setQuery($query);
		$type = $db->loadObject();
		
		return $type->name;
	}

	/**
	 * Validate order by field
	 * @return true if valid or false if not
	 */
	public static function validateOrderBy($orderBy, $allowedValues) {
		if(!empty($allowedValues)){
			foreach ($allowedValues as $item) {
				if ($orderBy == $item->value) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get file size info
	 *
	 * @param unknown_type $bytes
	 * @param unknown_type $decimals
	 */
	public static function getReadableFilesize($bytes, $decimals = 2) {
		$sz = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $sz[$factor];
	}


	/**
	 * convert all the following units ['B', 'KB', 'MB', 'GB', 'TB', 'PB'] to bytes
	 *
	 * @param $from string the value that will be converted to byte it will be of type 10B, 10MB etc
	 * @return float|int|null|string|string[]
	 * @since 4.9.5
	 */
	public static function convertToBytes($from) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$number = substr($from, 0, -2);
		$suffix = strtoupper(substr($from, -2));

		//B or no suffix
		if (is_numeric(substr($suffix, 0, 1))) {
			return preg_replace('/[^\d]/', '', $from);
		}

		$exponent = array_flip($units)[$suffix];
		if ($exponent === null) {
			return null;
		}

		return $number * (pow(1024, $exponent));
	}

	/**
	 * Get number of pdf pages
	 *
	 * @param  $pdfname
	 * @return number of pages
	 */
	public static function getNumberOfPdfPages($pdfname) {
        $pdftext = self::getURLData($pdfname);
		$num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
		return $num;
	}

	/**
	 * Retrieve attachment properties
	 * @param unknown_type $attach
	 */
	public static function getAttachProperties($attachment) {
		$attachmentProperties = new stdClass();
		$fileProperties = pathinfo(BD_ATTACHMENT_PATH . $attachment->path);
		if (!isset($fileProperties['extension'])) {
			$fileProperties['extension'] = "FILE";
		}
		$attachmentProperties->fileProperties = $fileProperties;
		
		
		if (!empty($attachment->path)) {
			$realfile = BD_ATTACHMENT_UPLOAD_PATH . $attachment->path;
			$fsize = filesize($realfile);
			$attachmentProperties->size = JBusinessUtil::getReadableFilesize($fsize, 2);
		}

		if (strcasecmp($fileProperties['extension'], 'pdf') == 0  && !empty($attachment->path)) {
			$attachmentProperties->nrPages = JBusinessUtil::getNumberOfPdfPages(BD_ATTACHMENT_PATH . $attachment->path);
		} else {
			$attachmentProperties->nrPages = "";
		}

		switch (strtoupper($fileProperties['extension'])) {
			case "PDF":
				$attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "pdf.png";
				break;
			case "BMP":
			case "GIF":
			case "JPEG":
			case "JPG":
			case "PNG":
				 $attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "photo.png";
				break;
			case "DOC":
			case "DOCM":
			case "DOCX":
			case "GDOC":
				$attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "doc.png";
				break;
			case "XLS":
			case "XLK":
			case "XLSX":
			case "XLR":
			case "XLT":
			case "XLW":
				 $attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "xls.png";
				break;
			case "TXT":
				$attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "txt.png";
				break;
			case "MP3":
			case "WMA":
			case "M4A":
			case "MP4A":
			case "AAC":
				$attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "sound.png";
				break;
			default:
				$attachmentProperties->icon = BD_ATTACHMENT_ICON_PATH . "file.png";
		}

		return $attachmentProperties;
	}
	
	/**
	 * Upload file in a specified folder
	 *
	 * @param unknown_type $fileName
	 * @param unknown_type $data
	 * @param unknown_type $dest
	 * @return boolean|string|NULL
	 */
	public static function uploadFile($fileName, &$data, $dest) {
		//Retrieve file details from uploaded file, sent from upload form

		$file  = JFactory::getApplication()->input->files->get($fileName);
		//$file = JFactory::getApplication()->input->get($fileName, null, 'files', 'array');

		if ($file['name']=='') {
			return false;
		}
			
		//Clean up filename to get rid of strange characters like spaces etc
		$fileNameSrc = JFile::makeSafe($file['name']);
		$data[$fileName] =  $fileNameSrc;
	
		$src = $file['tmp_name'];
		$dest = $dest."/".$fileNameSrc;
	
		$result =  JFile::upload($src, $dest);

		if ($result) {
			return $dest;
		}
	
		return null;
	}

	/**
	 * Generate the direction URL for two waypoints and for the specified type of map
	 *
	 * @param $origin
	 * @param $destination
	 * @param $mapType int type of the map, default google
	 *
	 * @return string
	 */
	public static function getDirectionURL($origin, $destination = null, $mapType = null, $justOrigin = false) {
		$db = JFactory::getDbo();

		if (empty($mapType)) {
			$appSettings = JBusinessUtil::getApplicationSettings();
			$mapType     = $appSettings->map_type;
		}

		$source    = '';
		$dest      = '';
		$delimiter = '';
		$baseUrl   = '';
		if ($mapType == MAP_TYPE_GOOGLE) {
			$source = "";
			if (!empty($origin) && isset($origin["latitude"]) && isset($origin["longitude"])) {
				$source = "saddr=" . $db->escape($origin["latitude"]) . "," . htmlspecialchars($origin["longitude"]);
			}

			$dest = "";
			if (!empty($destination) && isset($destination->latitude) && isset($destination->longitude)) {
				$dest = "daddr=" . $db->escape($destination->latitude) . "," . $db->escape($destination->longitude);
			}

			if (!empty($source) && !empty($dest)) {
				$delimiter = "&";
			}

			$baseUrl = "https://maps.google.com?";
		} elseif ($mapType == MAP_TYPE_BING) {
			if (!empty($origin) && isset($origin["latitude"]) && isset($origin["longitude"])) {
				$source = "pos." . $db->escape($origin["latitude"]) . "_" . $db->escape($origin["longitude"]);
			}

			if (!empty($destination) && isset($destination->latitude) && isset($destination->longitude)) {
				$dest = "pos." . $db->escape($destination->latitude) . "_" . $db->escape($destination->longitude);
			}

			$delimiter = "";
			if (!empty($source) && !empty($dest)) {
				$delimiter = "~";
			}

			$baseUrl = "https://www.bing.com/maps?rtp=";
		} elseif ($mapType == MAP_TYPE_OSM) {
			if (!empty($origin) && isset($origin["latitude"]) && isset($origin["longitude"])) {
				$source = "route=" . $db->escape($origin["latitude"]) . "%2C" . $db->escape($origin["longitude"]);
			}

			if (!empty($destination) &&((isset($destination->latitude) && isset($destination->longitude)) || (is_array($destination) && isset($destination["latitude"]) && isset($destination["longitude"])) )) {

				$latitude = "";
				$longitude = "";

				if(!empty($destination->latitude)){
	            	$latitude = $destination->latitude;
	            	$longitude = $destination->longitude;
				}

				if(is_array($destination) && isset($destination["latitude"])){
					$latitude = $destination["latitude"];
					$longitude = $destination["longitude"];
				}

			    if (empty($origin)) {
			        $dest = "to=";
                } else {
			        $dest = "to=";
                }
                
				$dest .= $db->escape($latitude) . "%2C" . $db->escape($longitude);
			    $dest .= '#map=7/'.$db->escape($latitude).'/'.$db->escape($longitude);
			}

			$delimiter = "";
			if (!empty($source) && !empty($dest)) {
				$delimiter = "&";
			}

			if ($mapType == MAP_TYPE_OSM) {
				$delimiter = "";
			}

			$baseUrl = "https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&";
		}

		$url = $baseUrl . $source . $delimiter . $dest;

		if ($justOrigin) {
			$url = $baseUrl . $source;
		}

		return $url;
	}

	/**
	 * Method that returns the translated name of the weekday for a
	 * given index that should range from 1-7
	 *
	 * @param $dayIndex int day of week index, should have values from 1 to 7
	 * @param $translate bool if false, days will not be translated (will stay in english)
	 * @return string the name of the weekday
	 * @return boolean false if the dayIndex is outside the required range
	 */
	public static function getWeekdayFromIndex($dayIndex, $translate=true) {
		if ($translate) {
			$days = array(
				1 => JText::_('LNG_MONDAY'),
				2 => JText::_('LNG_TUESDAY'),
				3 => JText::_('LNG_WEDNESDAY'),
				4 => JText::_('LNG_THURSDAY'),
				5 => JText::_('LNG_FRIDAY'),
				6 => JText::_('LNG_SATURDAY'),
				7 => JText::_('LNG_SUNDAY')
			);
		} else {
			$days = array(
				1 => 'Monday',
				2 => 'Tuesday',
				3 => 'Wednesday',
				4 => 'Thursday',
				5 => 'Friday',
				6 => 'Saturday',
				7 => 'Sunday'
			);
		}

		if ($dayIndex >= 1 && $dayIndex <= 7) {
			return $days[$dayIndex];
		} else {
			return false;
		}
	}

	/**
	 * Method that formats a timestamp duration or time period (minutes) into
	 * a better looking time text like: 'hh Hour(s) mm Minute(s) ss Second(s)'
	 *
	 * @param $time string containing the time in the hh:mm:ss format
	 * @param $timeFormat int 0-> 00:00:00, 1-> minutes
	 * @return string formatted time
	 */
	public static function formatTimePeriod($time, $timeFormat = 0) {

		if ($timeFormat == 0) {
			$temp = explode(':', $time);
			$hours = $temp[0];
			$minutes = $temp[1];
			$seconds = $temp[2];
		} elseif ($timeFormat == 1) {
			if ($time < 1) {
				return false;
			}

			$hours = floor($time / 60);
			$minutes = ($time % 60);
			$seconds = 0;
		}

		$resultTime = '';
		if (isset($hours) && $hours != '00' || $hours != '0') {
			$hoursText = (int)$hours > 1 ? JText::_('LNG_HOURS') : JText::_('LNG_HOUR');
			$resultTime .= (int)$hours . ' ' . $hoursText . ' ';
		}

		if (isset($minutes) && $minutes != '00' || $minutes != '0') {
			$minutesText = (int)$minutes > 1 ? JText::_('LNG_MINUTES') : JText::_('LNG_MINUTE');
			$resultTime .= (int)$minutes . ' ' . $minutesText . ' ';
		}

		if (isset($seconds) && $seconds != '00' || $seconds != '0') {
			$secondsText = (int)$seconds > 1 ? JText::_('LNG_SECONDS') : JText::_('LNG_SECOND');
			$resultTime .= (int)$seconds . ' ' . $secondsText . ' ';
		}

		return $resultTime;
	}


	/**
	 * Method that gets raw information about the vacations for a provider
	 * and organizes them into dates which will define the free days
	 *
	 * @param $vacationData object containing availability and break days information
	 *
	 * @return array organized array containing all the free days for a provider
	 */
	public static function processProviderVacationDays($vacationData) {
		$vacations    = array();
		$availability = explode(',', $vacationData->availability);
		$weekDays     = explode(',', $vacationData->breakDays);

		$availability = array_filter($availability);
		$weekDays = array_filter($weekDays);

		if (!empty($availability)) {
			$startDates = array();
			$endDates   = array();

			// get all start-end date pairs
			for ($i = 0; $i < count($availability); $i += 2) {
				$startDates[] = $availability[$i];
				$endDates[]   = $availability[$i + 1];
			}

			// get all dates between each startDate - endDate pair
			foreach ($startDates as $key => $val) {
				$dates     = self::getAllDatesInInterval($val, $endDates[$key], '+1 day', 'd-m-Y');
				$vacations = array_merge($vacations, $dates);
			}
		}

		$freeDays      = array();
		$addCurrentDay = false;
		foreach ($weekDays as $weekDay) {
			if (!empty($weekDay)) {
				// get the date of the nearest weekday at hand
				$day = date('d-m-Y', strtotime("next " . self::getWeekdayFromIndex($weekDay, false), strtotime(date('d-m-Y'))));

				if ($weekDay == date('N')) {
					$addCurrentDay = true;
				}

				// get all dates of the current weekday for the next 6 months
				$freeDays = array_merge($freeDays, self::getAllDatesInInterval($day, date('d-m-Y', strtotime("+6 months")), '+1 week', 'd-m-Y'));
			}
		}

		if ($addCurrentDay) {
			$freeDays[] = date('d-m-Y');
		}

		$vacations = array_merge($vacations, $freeDays);

		return $vacations;
	}

	/**
	 * Method that gets the raw information about the work and break hours for a provider and
	 * organizes them between the start and work hours (with an interval equal
	 * to the service duration) excluding the break hours intervals, into an array.
	 *
	 * @param $hours array containing all information about the work, break hours and the service duration
	 * @return array|bool organized array containing the available hours
	 */
	public static function processProviderAvailableHours($hours) {
		$workHours = array();
		$breakHours = array();
		foreach ($hours as $result) {
			if ($result->type == STAFF_WORK_HOURS) {
				$workHours["start_hour"] = $result->start_hour;
				$workHours["end_hour"] = $result->end_hour;
			} else {
				$breakHours["start_hour"][] =  $result->start_hour;
				$breakHours["end_hour"][] = $result->end_hour;
			}
		}

		if (!empty($breakHours)) {
			usort($breakHours["start_hour"], function ($a, $b) {
				return (strtotime($a) < strtotime($b)) ? -1 : 1;
			});
			usort($breakHours["end_hour"], function ($a, $b) {
				return (strtotime($a) < strtotime($b)) ? -1 : 1;
			});
		}

		$minutes = $hours[0]->duration;

		// if duration not available, default to 1 hour (60 minutes)
		if (empty($minutes)) {
			$minutes = 60;
		}

		if (!isset($workHours['start_hour'])) {
			return false;
		}

		$now = strtotime($workHours["start_hour"]);
		$end = strtotime($workHours["end_hour"]);

		$hours = array();
		$i = 0;
		while ($now < $end) {
			if (isset($breakHours["start_hour"][$i])) {
				if ($now >= strtotime($breakHours["start_hour"][$i]) && $now <= strtotime($breakHours["end_hour"][$i])) {
					$now = strtotime($breakHours["end_hour"][$i]);
					$i++;
				}
			}

			$hours[] = date('H:i:s', $now);
			$now = strtotime('+ '.$minutes.' minutes', $now);
		}

		$availableHours = array();

		foreach ($hours as $key=>$val) {
			if (isset($hours[$key])) {
				if (strtotime($val) < strtotime('12:00:00')) {
					$availableHours["morning"][] = $val;
				} elseif (strtotime($val) >= strtotime('12:00:00') && strtotime($val) < strtotime('18:00:00')) {
					$availableHours["afternoon"][] = $val;
				} elseif (strtotime($val) >= strtotime('18:00:00')) {
					$availableHours["evening"][] = $val;
				}
			}
		}

		return $availableHours;
	}

	/**
	 *
	 * Update company,offer or events data based on the default attributes
	 *
	 * @param $item object contain all the company data
	 * @param $defaultAttribute array with the application configuration
	 * @return mixed the updated object of company,offer or events
	 */
	public static function updateItemDefaultAtrributes(&$item, $defaultAttribute) {
		if (empty($item)) {
			return;
		}
		
		if (isset($item->website) && !empty($item->website)) {
			$item->website           = $defaultAttribute["website"]          !=ATTRIBUTE_NOT_SHOW?$item->website:"";
		}
		if (isset($item->keywords) && !empty($item->keywords)) {
			$item->keywords          = $defaultAttribute["keywords"]         !=ATTRIBUTE_NOT_SHOW?$item->keywords:"";
		}
		if (isset($item->categories) && !empty($item->categories)) {
			$item->categories        = $defaultAttribute["category"]         !=ATTRIBUTE_NOT_SHOW?$item->categories:"";
		}
		if (isset($item->logoLocation) && !empty($item->logoLocation)) {
			$item->logoLocation      = $defaultAttribute["logo"]             !=ATTRIBUTE_NOT_SHOW?$item->logoLocation:"";
		}
		if (isset($item->street_number) && !empty($item->street_number)) {
			$item->street_number     = $defaultAttribute["street_number"]    !=ATTRIBUTE_NOT_SHOW?$item->street_number:"";
		}
		if (isset($item->address) && !empty($item->address)) {
			$item->address           = $defaultAttribute["address"]          !=ATTRIBUTE_NOT_SHOW?$item->address:"";
		}
		if (isset($item->city) && !empty($item->city)) {
			$item->city              = $defaultAttribute["city"]             !=ATTRIBUTE_NOT_SHOW?$item->city:"";
		}
		if (isset($item->county) && !empty($item->county)) {
			$item->county            = $defaultAttribute["region"]           !=ATTRIBUTE_NOT_SHOW?$item->county:"";
		}
		if (isset($item->countryId) && !empty($item->countryId)) {
			$item->countryId         = $defaultAttribute["country"]          !=ATTRIBUTE_NOT_SHOW?$item->countryId:"";
		}
		if (isset($item->countryName) && !empty($item->countryName)) {
			$item->countryName       = $defaultAttribute["country"]          !=ATTRIBUTE_NOT_SHOW?$item->countryName:"";
		}
		if (isset($item->country_name) && !empty($item->country_name)) {
			$item->country_name      = $defaultAttribute["country"]          !=ATTRIBUTE_NOT_SHOW?$item->country_name:"";
		}
		if (isset($item->postalCode) && !empty($item->postalCode)) {
			$item->postalCode        = $defaultAttribute["postal_code"]      !=ATTRIBUTE_NOT_SHOW?$item->postalCode:"";
		}
		if (isset($item->phone) && !empty($item->phone)) {
			$item->phone             = $defaultAttribute["phone"]            !=ATTRIBUTE_NOT_SHOW?$item->phone:"";
		}
		if (isset($item->mobile) && !empty($item->mobile)) {
			$item->mobile            = $defaultAttribute["mobile_phone"]     !=ATTRIBUTE_NOT_SHOW?$item->mobile:"";
		}
		if (isset($item->fax) && !empty($item->fax)) {
			$item->fax               = $defaultAttribute["fax"]              !=ATTRIBUTE_NOT_SHOW?$item->fax:"";
		}
		if (isset($item->email) && !empty($item->email)) {
			$item->email             = $defaultAttribute["email"]            !=ATTRIBUTE_NOT_SHOW?$item->email:"";
		}
		if (isset($item->short_description) && !empty($item->short_description)) {
			$item->short_description = $defaultAttribute["short_description"]!=ATTRIBUTE_NOT_SHOW?$item->short_description:"";
		}
		if (isset($item->province) && !empty($item->province)) {
			$item->province          = $defaultAttribute["province"]         !=ATTRIBUTE_NOT_SHOW?$item->province:"";
		}
		if (isset($item->typeName) && !empty($item->typeName)) {
			$item->typeName          = $defaultAttribute["type"]             !=ATTRIBUTE_NOT_SHOW?$item->typeName:"";
		}
		if (isset($item->longitude) && !empty($item->longitude)) {
			$item->longitude         = $defaultAttribute["map"]              !=ATTRIBUTE_NOT_SHOW?$item->longitude:"";
		}
		if (isset($item->latitude) && !empty($item->latitude)) {
			$item->latitude          = $defaultAttribute["map"]              !=ATTRIBUTE_NOT_SHOW?$item->latitude:"";
		}
		if (isset($item->taxCode) && !empty($item->taxCode)) {
			$item->taxCode           = $defaultAttribute["tax_code"]         !=ATTRIBUTE_NOT_SHOW?$item->taxCode:"";
		}
		if (isset($item->comercialName) && !empty($item->comercialName)) {
			$item->comercialName     = $defaultAttribute["comercial_name"]   !=ATTRIBUTE_NOT_SHOW?$item->comercialName:"";
		}
		if (isset($item->slogan) && !empty($item->slogan)) {
			$item->slogan            = $defaultAttribute["slogan"]           !=ATTRIBUTE_NOT_SHOW?$item->slogan:"";
		}
		if (isset($item->description) && !empty($item->description)) {
			$item->description       = $defaultAttribute["description"]      !=ATTRIBUTE_NOT_SHOW?$item->description:"";
		}
		if (isset($item->pictures) && !empty($item->pictures)) {
			$item->pictures          = $defaultAttribute["pictures"]         !=ATTRIBUTE_NOT_SHOW?$item->pictures:array();
		}
		if (isset($item->videos) && !empty($item->videos)) {
			$item->videos            = $defaultAttribute["video"]            !=ATTRIBUTE_NOT_SHOW?$item->videos:array();
		}
		if (isset($item->facebook) && !empty($item->facebook)) {
			$item->facebook          = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["facebook"] !=ATTRIBUTE_NOT_SHOW?$item->facebook:"";
		}
		if (isset($item->twitter) && !empty($item->twitter)) {
			$item->twitter           = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["twitter"] !=ATTRIBUTE_NOT_SHOW?$item->twitter:"";
		}
		if (isset($item->googlep) && !empty($item->googlep)) {
			$item->googlep           = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW?$item->googlep:"";
		}
		if (isset($item->skype) && !empty($item->skype)) {
			$item->skype             = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["skype"] !=ATTRIBUTE_NOT_SHOW?$item->skype:"";
		}
		if (isset($item->linkedin) && !empty($item->linkedin)) {
			$item->linkedin          = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["linkedin"] !=ATTRIBUTE_NOT_SHOW?$item->linkedin:"";
		}
		if (isset($item->youtube) && !empty($item->youtube)) {
			$item->youtube           = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["youtube"] !=ATTRIBUTE_NOT_SHOW?$item->youtube:"";
		}
		if (isset($item->instagram) && !empty($item->instagram)) {
			$item->instagram         = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["instagram"] !=ATTRIBUTE_NOT_SHOW?$item->instagram:"";
		}
		if (isset($item->tiktok) && !empty($item->tiktok)) {
			$item->tiktok         = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["tiktok"] !=ATTRIBUTE_NOT_SHOW?$item->tiktok:"";
		}
		if (isset($item->pinterest) && !empty($item->pinterest)) {
			$item->pinterest         = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["pinterest"] !=ATTRIBUTE_NOT_SHOW?$item->pinterest:"";
		}
		if (isset($item->whatsapp) && !empty($item->whatsapp)) {
			$item->whatsapp          = $defaultAttribute["social_networks"]  !=ATTRIBUTE_NOT_SHOW && $defaultAttribute["whatsapp"] !=ATTRIBUTE_NOT_SHOW?$item->whatsapp:"";
		}
		if (isset($item->contacts) && !empty($item->contacts)) {
			$item->contacts          = $defaultAttribute["contact_person"]   !=ATTRIBUTE_NOT_SHOW?$item->contacts:array();
		}
		if (isset($item->attachments) && !empty($item->attachments)) {
			$item->attachments       = $defaultAttribute["attachments"]      !=ATTRIBUTE_NOT_SHOW?$item->attachments:array();
		}
		if (isset($item->custom_tab_name) && !empty($item->custom_tab_name)) {
			$item->custom_tab_name   = $defaultAttribute["custom_tab"]       !=ATTRIBUTE_NOT_SHOW?$item->custom_tab_name:"";
		}
		if (isset($item->custom_tab_content) && !empty($item->custom_tab_content)) {
			$item->custom_tab_content= $defaultAttribute["custom_tab"]       !=ATTRIBUTE_NOT_SHOW?$item->custom_tab_content:"";
		}
		if (isset($item->business_hours) && !empty($item->business_hours)) {
			$item->business_hours    = $defaultAttribute["opening_hours"]    !=ATTRIBUTE_NOT_SHOW?$item->business_hours:array();
		}
		if (isset($item->notes_hours) && !empty($item->notes_hours)) {
			$item->notes_hours       = $defaultAttribute["opening_hours"]    !=ATTRIBUTE_NOT_SHOW?$item->notes_hours:"";
		}
		if (isset($item->meta_title) && !empty($item->meta_title)) {
			$item->meta_title        = $defaultAttribute["metadata_information"]!=ATTRIBUTE_NOT_SHOW?$item->meta_title:"";
		}
		if (isset($item->meta_description) && !empty($item->meta_description)) {
			$item->meta_description  = $defaultAttribute["metadata_information"]!=ATTRIBUTE_NOT_SHOW?$item->meta_description:"";
		}
		if (isset($item->meta_keywords) && !empty($item->meta_keywords)) {
			$item->meta_keywords     = $defaultAttribute["metadata_information"]!=ATTRIBUTE_NOT_SHOW?$item->meta_keywords:"";
		}
		if (isset($item->publish_start_date) && !empty($item->publish_start_date)) {
			$item->publish_start_date= $defaultAttribute["publish_dates"]    !=ATTRIBUTE_NOT_SHOW?$item->publish_start_date:"";
		}
		if (isset($item->publish_end_date) && !empty($item->publish_end_date)) {
			$item->publish_end_date  = $defaultAttribute["publish_dates"]    !=ATTRIBUTE_NOT_SHOW?$item->publish_end_date:"";
		}
		if (isset($item->area) && !empty($item->area)) {
			$item->area              = $defaultAttribute["area"]             !=ATTRIBUTE_NOT_SHOW?$item->area:"";
		}
		if (isset($item->business_cover_image) && !empty($item->business_cover_image)) {
			$item->business_cover_image= $defaultAttribute["cover_image"]    !=ATTRIBUTE_NOT_SHOW?$item->business_cover_image:"";
		}
		if (isset($item->contact_phone) && !empty($item->contact_phone)) {
			$item->contact_phone     = $defaultAttribute["phone"]            !=ATTRIBUTE_NOT_SHOW?$item->contact_phone:"";
		}
		if (isset($item->contact_email) && !empty($item->contact_email)) {
			$item->contact_email     = $defaultAttribute["email"]            !=ATTRIBUTE_NOT_SHOW?$item->contact_email:"";
		}
		if (isset($item->establishment_year) && !empty($item->establishment_year)) {
			$item->establishment_year= $defaultAttribute["establishment_year"]!=ATTRIBUTE_NOT_SHOW?$item->establishment_year:"";
		}
		if (isset($item->employees) && !empty($item->employees)) {
			$item->employees         = $defaultAttribute["employees"]        !=ATTRIBUTE_NOT_SHOW?$item->employees:"";
		}
		if (isset($item->min_project_size) && !empty($item->min_project_size)) {
			$item->min_project_size         = $defaultAttribute["min_project_size"]        !=ATTRIBUTE_NOT_SHOW?$item->min_project_size:"";
		}
		if (isset($item->hourly_rate) && !empty($item->hourly_rate)) {
			$item->hourly_rate         = $defaultAttribute["hourly_rate"]        !=ATTRIBUTE_NOT_SHOW?$item->hourly_rate:"";
		}
		if (isset($item->ad_image) && !empty($item->ad_image)) {
			$item->ad_image          = $defaultAttribute["ad_images"]        !=ATTRIBUTE_NOT_SHOW?$item->ad_image:"";
		}
		if (isset($item->price_text) && !empty($item->price_text)) {
			$item->price_text        = $defaultAttribute["price_text"]       !=ATTRIBUTE_NOT_SHOW?$item->price_text:"";
		}
		if (isset($item->related_listing) && !empty($item->related_listing)) {
			$item->related_listing   = $defaultAttribute["related_listing"]  !=ATTRIBUTE_NOT_SHOW?$item->related_listing:"";
		}
		if (isset($item->price) && !empty($item->price)) {
			$item->price  			 = $defaultAttribute["price"] 			 !=ATTRIBUTE_NOT_SHOW?$item->price:"";
		}
		if (isset($item->enable_subscription) && !empty($item->enable_subscription)) {
			$item->enable_subscription = $defaultAttribute["enable_subscription"]  	 !=ATTRIBUTE_NOT_SHOW?$item->enable_subscription:"";
		}
		if (isset($item->total_tickets) && !empty($item->total_tickets)) {
			$item->total_tickets   	 = $defaultAttribute["total_tickets"]  	 !=ATTRIBUTE_NOT_SHOW?$item->total_tickets:"";
		}
		if (isset($item->ticket_url) && !empty($item->ticket_url)) {
			$item->ticket_url   	 = $defaultAttribute["ticket_url"]  	 !=ATTRIBUTE_NOT_SHOW?$item->ticket_url:"";
		}
		if (isset($item->time_zone) && !empty($item->time_zone)) {
			$item->time_zone   	     = $defaultAttribute["time_zone"]  	  	 !=ATTRIBUTE_NOT_SHOW?$item->time_zone:"";
		}
		if (isset($item->booking_open_date) && !empty($item->booking_open_date)) {
			$item->booking_open_date   	 = $defaultAttribute["booking_dates"]  	 !=ATTRIBUTE_NOT_SHOW?$item->booking_open_date:"";
		}
		if (isset($item->booking_close_date) && !empty($item->booking_close_date)) {
			$item->booking_close_date   	 = $defaultAttribute["booking_dates"]  	 !=ATTRIBUTE_NOT_SHOW?$item->booking_close_date:"";
		}

		return $item;
	}

	/**
	 * This function get all the default business attribute configuration on general settings
	 *
	 * @return array containing all the default attribute and their configuration
	 */
	public static function getAttributeConfiguration($attrType) {
		if (empty($attrType) || !isset($attrType)) {
			$attrType = DEFAULT_ATTRIBUTE_TYPE_LISTING;
		}
		
		$instance = JBusinessUtil::getInstance();
		$varName = "attributeConfiguration".$attrType;
		if (isset($instance->$varName)) {
			return $instance->$varName;
		}
		
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$defaultAttributesTable = JTable::getInstance('DefaultAttributes', 'Table');
		$attributesConfiguration = $defaultAttributesTable->getAttributesConfiguration($attrType);
		$defaultAtrributes= array();
		if (isset($attributesConfiguration) && count($attributesConfiguration)>0) {
			foreach ($attributesConfiguration as $attrConfig) {
				$defaultAtrributes[$attrConfig->name] = $attrConfig->config;
			}
		}
		
		$instance->$varName = $defaultAtrributes;

		return $defaultAtrributes;
	}

	/**
	 * Update the default attribute configuration with the simple form configuration
	 */
	public static function applySimpleFormConfiguration(&$defaultAtrributes){
		$appSettings = JBusinessUtil::getApplicationSettings();

		$simpleFormAttributes = array();
		if(!empty($appSettings->simple_form_fields)){
			$simpleFormAttributes = explode(",", $appSettings->simple_form_fields);
		}

		$simpleFormAttributes[] = "address_autocomplete";

		if(in_array("address",$simpleFormAttributes)){
			$simpleFormAttributes[]="street_number";
			$simpleFormAttributes[]="area";
			$simpleFormAttributes[]="city";
			$simpleFormAttributes[]="postal_code";
			$simpleFormAttributes[]="region";
			$simpleFormAttributes[]="province";
			$simpleFormAttributes[]="country";
		}

		foreach ($defaultAtrributes as $key=>$attrConfig) {
			if(!in_array($key, $simpleFormAttributes)){
				$defaultAtrributes[$key] = 3;
			}
		}

		if(in_array("custom_attributes", $simpleFormAttributes)){
			$defaultAtrributes["custom_attributes"] = 2 ;
		}else{
			$defaultAtrributes["custom_attributes"] = 3 ;
		}
	}


	//retrieve the current day based on the time zone
	public static function getCurrentDayIndex($time_zone) {

		//set timezone and create a new object date time for that timezone
		$original = new DateTime("now", new DateTimeZone('GMT'));
		
		//TODO replace with JBusinessUtil::getCurrentTime
		$time_zone=intval($time_zone);
		$timezoneName = timezone_name_from_abbr("", $time_zone * 3600, false);
		$modified = $original->setTimezone(new DateTimezone($timezoneName));

		$dayIndex = $modified->format('N');

		return $dayIndex;
	}
    /**
     * Method that returns a complex array organized in a way that it may
     * be simply used in a view to display the work and break hours.
     *
     * The main array will have 7 objects, one for each day of the week.
     * Each of these objects will contain the name of the day, and the
     * break and work hours for that particular day.
     *
     * @param $workHours array containing the work hours for all days of the week
     * @param $breakHours array containing the break hours for all days of the week
     *
     * @return array
     */
    public static function getWorkingDays($workHours, $breakHours, $startDate=null, $endDate=null) {
        $workingDays = array();

        if(empty($startDate)) {
            for ($i = 1; $i <= 7; $i++) {
                $day = self::getWorkingDay($workHours, $breakHours, $i);

                $day->name = self::getWeekdayFromIndex($i);
                $workingDays[$i] = $day;
            }
        }else{
            $date = strtotime($startDate);
            $index = 1;
            while($date <= strtotime($endDate)){
                $day = self::getWorkingDay($workHours, $breakHours, $index);

                $day->name = self::getDateGeneralFormat(date("Y-m-d",$date));
                $workingDays[$index] = $day;
                $index++;
                $date = strtotime("+1 day", $date);
            }
        }

        return $workingDays;
    }

    /**
     * Build the day working structure
     *
     * @param $workHours
     * @param $breakHours
     * @return stdClass
     */
    public static function getWorkingDay($workHours, $breakHours, $index){
        $day = new stdClass();

        $day->workHours['id'] = '';
        $day->workHours['status'] = '';
        $day->workHours['start_time'] = '';
        $day->workHours['end_time'] = '';
        if (!empty($workHours) || !empty($breakHours)) {
            // Arrange the working hours
            if (isset($workHours[$index - 1])) {
                $day->workHours['start_time'] = $workHours[$index - 1]->startHours;
                $day->workHours['end_time'] = $workHours[$index - 1]->endHours;
                $day->workHours['id'] = $workHours[$index - 1]->periodIds;
                $day->workHours['status'] = $workHours[$index - 1]->statuses;
            }

            // Arrange the break hours
            if (!empty($breakHours[$index])) {
                $day->breakHours = array();
                $startHours = explode(',', $breakHours[$index]->startHours);
                $endHours = explode(',', $breakHours[$index]->endHours);
                $breakIds = explode(',', $breakHours[$index]->periodIds);
                $n = count($startHours);
                if ($n > 0) {
                    for ($j = 0; $j < $n; $j++) {
                        $day->breakHours['start_time'][$j] = $startHours[$j];
                        $day->breakHours['end_time'][$j] = $endHours[$j];
                        $day->breakHours['id'][$j] = $breakIds[$j];
                    }
                } else {
                    $day->breakHours['start_time'][] = null;
                    $day->breakHours['end_time'][] = null;
                }
            }
        } else {
            $day->workHours['start_time'] = null;
            $day->workHours['end_time'] = null;
            $day->workHours['id'] = null;
            $day->breakHours[0]['start_time'] = null;
            $day->breakHours[0]['end_time'] = null;
        }

        return $day;
    }

	/**
	 * Render opening day
	 */
	public static function renderOpeningDay($day){
		ob_start(); 
		?>
			<div class="business-hour" itemprop="openingHours">
				<div class="day"><?php echo $day->name ?></div>
				<div class="business-hour-time">
					<?php if ($day->workHours['status']) { ?>
						<div class="business-hours-wrap">
							<span class="start">
								<?php echo JBusinessUtil::convertTimeToFormat($day->workHours["start_time"]) ?>
							</span>
							<?php if(isset($day->breakHours)) { ?>
									<span class="end">
										- <?php echo JBusinessUtil::convertTimeToFormat($day->breakHours["start_time"][0]) ?>
									</span>
								</div>
								<div class="business-hours-wrap">
									<span class="start">
										<?php echo JBusinessUtil::convertTimeToFormat($day->breakHours["end_time"][0]) ?>
									</span>
							<?php } ?>
							<span class="end">
								- <?php echo JBusinessUtil::convertTimeToFormat($day->workHours['end_time']) ?>
							</span>
						</div>
					<?php } else { ?>
						<span class="end"><?php echo JText::_('LNG_CLOSED'); ?></span>
					<?php } ?>
				</div>
			</div>
		<?php

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public static function getOpeningStatus($openingStatus){

		switch($openingStatus){
			case COMPANY_ALWAYS_OPEN:
				$status = JText::_('LNG_ALWAYS_OPEN');
				$class = 'success';
				break;
			case COMPANY_TEMP_CLOSED:
				$status = JText::_('LNG_TEMPORARILY_CLOSED');
				$class = 'danger';
				break;
			case COMPANY_OPEN_BY_APPOINTMENT:
				$status = JText::_('LNG_OPEN_BY_APPOINTMENT');
				$class = 'danger';
				break;
			case COMPANY_SEASON_CLOSED:
				$status = JText::_('LNG_COMPANY_SEASON_CLOSED');
				$class = 'danger';
				break;
			case COMPANY_PERMANENTLY_CLOSED:
				$status = JText::_('LNG_PERMANENTLY_CLOSED');
				$class = 'danger';
				break;
		}

		$statusInfo = new stdClass();
		$statusInfo->status = $status ?? '';
		$statusInfo->class = $class ?? '';

		return $statusInfo;
	}

	/**
	 * This function generate an array with all the timezones to be used for a good user experience
	 *
	 * @return array contain all the timezones
	 */
	public static function timeZonesList() {
		return $timezoneTable = array(
			"-11:00" => "(GMT -11:00) Midway Island, Samoa",
			"-10:00" => "(GMT -10:00) Hawaii",
			"-09:30" => "(GMT -9:30) Marquesas Islands",
			"-09:00" => "(GMT -9:00) Alaska",
			"-08:00" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
			"-07:00" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
			"-06:00" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
			"-05:00" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
			"-04:00" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
			"-03:30" => "(GMT -3:30) Newfoundland",
			"-03:00" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
			"-02:00" => "(GMT -2:00) Noronha, South Georgia",
			"-01:00" => "(GMT -1:00) Azores, Cape Verde Islands",
			"+00:00" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
			"+01:00" => "(GMT +1:00) BST, Brussels, Copenhagen, Madrid, Paris",
			"+02:00" => "(GMT +2:00) Kaliningrad, South Africa, Helsinki, Bucharest, Athens, Jerusalem",
			"+03:00" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
			"+04:00" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
			"+04:30" => "(GMT +4:30) Kabul",
			"+05:00" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
			"+05:30" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
			"+05:45" => "(GMT +5:45) Kathmandu",
			"+06:00" => "(GMT +6:00) Astana, Dhaka, Vostok",
			"+06:30" => "(GMT +6:30) Rangoon, Cocos Islands",
			"+07:00" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
			"+08:00" => "(GMT +8:00) Beijing, Perth, Hong Kong, Kuala Lumpur",
			"+08:45" => "(GMT +8:45) Eucla",
			"+09:00" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
			"+09:30" => "(GMT +9:30) Adelaide, Darwin",
			"+10:00" => "(GMT +10:00) Melbourne, Brisbane, Canberra, Guam, Vladivostok",
			"+10:30" => "(GMT +10:30) Lord Howe Island",
			"+11:00" => "(GMT +11:00) Sakhalin, Chokurdakh, Solomon Is. Island",
			"+12:00" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka",
			"+12:45" => "(GMT +12:45) Chatham Islands",
		);
	}

	/**
	 * Get all checklist elements organized in an array, each element
	 * having a name and status field. Status field is set to 0 by
	 * default.
	 *
	 * @param $type int business->1 offer->2 event->3
	 * @return array checklist array
	 */
	public static function getChecklist($type=1) {
		$checklist = array();
		$checklist['description'] = new stdClass();
		$checklist['address'] = new stdClass();

		// Business fields
		if ($type == 1) {
			$checklist['email'] = new stdClass();
			$checklist['keywords'] = new stdClass();
			$checklist['website'] = new stdClass();
			$checklist['logo'] = new stdClass();
			$checklist['cover_image'] = new stdClass();
			$checklist['social_networks'] = new stdClass();
		}
		// Offer fields
		elseif ($type == 2) {
			$checklist['duration'] = new stdClass();
			$checklist['pictures'] = new stdClass();
			$checklist['price'] = new stdClass();
		}
		// Event fields
		elseif ($type == 3) {
			$checklist['email'] = new stdClass();
			$checklist['duration'] = new stdClass();
			$checklist['phone'] = new stdClass();
			$checklist['pictures'] = new stdClass();
		}

		foreach ($checklist as $key=>$val) {
			$val->name = JText::_('LNG_'.strtoupper($key));
			$val->status = 0;
		}

		return $checklist;
	}

	/**
	 * Check each field of the item (business/offer/event) relative to the checklist, and
	 * change the status (to 1) if the respective field is already completed.
	 *
	 * @param $item array containing all relevant information about the company/offer/event
	 * @param $type int business->1 offer->2 event->3, default business(1)
	 * @return array checklist array with updated statuses
	 */
	public static function getCompletionProgress($item, $type) {
		$progress = self::getChecklist($type);

		$attributes = self::getAttributeConfiguration($type);
		$appSettings = JBusinessUtil::getApplicationSettings();

		$showAddress = $attributes["street_number"]!=ATTRIBUTE_NOT_SHOW || $attributes["address"]!=ATTRIBUTE_NOT_SHOW ||$attributes["area"]!=ATTRIBUTE_NOT_SHOW
			|| $attributes["country"]!=ATTRIBUTE_NOT_SHOW || $attributes["city"]!=ATTRIBUTE_NOT_SHOW
			|| $attributes["province"]!=ATTRIBUTE_NOT_SHOW || $attributes["region"]!=ATTRIBUTE_NOT_SHOW
			|| $attributes["postal_code"]!=ATTRIBUTE_NOT_SHOW || $attributes["map"]!=ATTRIBUTE_NOT_SHOW;

		if ($type == 1) {
			if (!empty($item->description)) {
				$progress['description']->status = 1;
			}
			if (!empty($item->email)) {
				$progress['email']->status = 1;
			}
			$address = self::getAddressText($item);
			if (!empty($address)) {
				$progress['address']->status = 1;
			}
			if (!empty($item->logoLocation)) {
				$progress['logo']->status = 1;
			}
			if (!empty($item->keywords)) {
				$progress['keywords']->status = 1;
			}
			if (!empty($item->website)) {
				$progress['website']->status = 1;
			}
			if (!empty($item->business_cover_image)) {
				$progress['cover_image']->status = 1;
			}
			if (!empty($item->facebook) || !empty($item->twitter) || !empty($item->googlep) || !empty($item->skype)
				|| !empty($item->linkedin) || !empty($item->youtube) || !empty($item->instagram) || !empty($item->pinterest)) {
				$progress['social_networks']->status = 1;
			}
		} elseif ($type == 2) {
			if (!empty($item->description)) {
				$progress['description']->status = 1;
			}
			if (!empty($item->price)) {
				$progress['price']->status = 1;
			}
			$address = self::getAddressText($item);
			if (!empty($address)) {
				$progress['address']->status = 1;
			}
			if (!empty($item->picture_path)) {
				$progress['pictures']->status = 1;
			}
			if (!self::emptyDate($item->startDate) || !self::emptyDate($item->endDate)) {
				$progress['duration']->status = 1;
			}
		} elseif ($type == 3) {
			if (!empty($item->description)) {
				$progress['description']->status = 1;
			}
			if (!empty($item->contact_email)) {
				$progress['email']->status = 1;
			}
			if (!empty($item->contact_phone)) {
				$progress['phone']->status = 1;
			}
			$address = self::getAddressText($item);
			if (!empty($address)) {
				$progress['address']->status = 1;
			}
			if (!empty($item->picture_path)) {
				$progress['pictures']->status = 1;
			}
			if (!self::emptyDate($item->start_date) || !self::emptyDate($item->end_date)) {
				$progress['duration']->status = 1;
			}
		}

		if (!$showAddress) {
			unset($progress['address']);
		}

		if ($type == 1) {
			$table = JTable::getInstance("Package", "JTable");
			$item->package = $table->getCurrentActivePackage($item->id);
		}

		foreach ($progress as $key=>$val) {
			if ($type == 1) {
				$checkFeature = false;
				if ($key == "logo") {
					$feature = "company_logo";
					$checkFeature = !(isset($item->package->features) && in_array($feature, $item->package->features) || !$appSettings->enable_packages);
				} elseif ($key == "website") {
					$feature = "website_address";
					$checkFeature = !(isset($item->package->features) && in_array($feature, $item->package->features) || !$appSettings->enable_packages);
				} elseif ($key == "description") {
					$checkFeature = !(isset($item->package->features) && in_array($key, $item->package->features) || !$appSettings->enable_packages);
				} elseif ($key == "social_networks") {
					$checkFeature = !(isset($item->package->features) && in_array($key, $item->package->features) || !$appSettings->enable_packages);
				}
				if ($attributes[$key] == ATTRIBUTE_NOT_SHOW || $checkFeature) {
					unset($progress[$key]);
				}
			} else {
				if (isset($attributes[$key]) &&  $attributes[$key] == ATTRIBUTE_NOT_SHOW) {
					unset($progress[$key]);
				}
			}
		}

		return $progress;
	}

	/**
	 * Creates an object containing all necessary fields that will be
	 * mostly used by js functions. These fields will be included in the
	 * JBDUtils object on js.
	 *
	 * @return stdClass
	 */
	public static function addJSSettings() {
		$jsSettings = new stdClass();
		$appSettings = JBusinessUtil::getApplicationSettings();

		if (! is_admin() && $appSettings->enable_multilingual) {
			$jsSettings->languageCode = self::getCurrentLanguageCode();
		}
		
		$jsSettings->baseUrl =  JRoute::_('index.php?option=com_jbusinessdirectory');
		$jsSettings->imageRepo = get_site_url() . 'components/com_jbusinessdirectory';
		$jsSettings->imageBaseUrl = (BD_PICTURES_PATH);
		$jsSettings->assetsUrl = (BD_ASSETS_FOLDER_PATH);
		$jsSettings->no_image = $appSettings->no_image;
		$jsSettings->maxFilenameLength = MAX_FILENAME_LENGTH;
		$jsSettings->siteRoot = get_site_url()."/";
		$jsSettings->componentName = JBusinessUtil::getComponentName();
		$jsSettings->timeFormat = $appSettings->time_format;
		$jsSettings->dateFormat = $appSettings->dateFormat;
		$jsSettings->mapType = $appSettings->map_type;
		$jsSettings->mapMarker = $appSettings->map_marker;
		$jsSettings->mapDefaultZoom = (int)$appSettings->map_zoom;
		$jsSettings->enable_attribute_category = $appSettings->enable_attribute_category;
		$jsSettings->enable_packages = $appSettings->enable_packages;
		$jsSettings->isMultilingual = $appSettings->enable_multilingual ? true : false;
		$jsSettings->validateRichTextEditors = false;
		$jsSettings->logo_width = $appSettings->logo_width;
		$jsSettings->logo_height = $appSettings->logo_height;
		$jsSettings->cover_width = $appSettings->cover_width;
		$jsSettings->cover_height = $appSettings->cover_height;
		$jsSettings->gallery_width = $appSettings->gallery_width;
		$jsSettings->gallery_height = $appSettings->gallery_height;
		$jsSettings->enable_crop = $appSettings->enable_crop ? true : false;
		$jsSettings->enable_resolution_check = $appSettings->enable_resolution_check ? true : false;
		$jsSettings->limit_cities_regions = $appSettings->limit_cities_regions ? true : false;
		$jsSettings->enable_map_gdpr = $appSettings->enable_map_gdpr ? true : false;
		$jsSettings->maxAttachments = $appSettings->max_attachments;
		$jsSettings->marker_size = $appSettings->marker_size;
        $jsSettings->month_names = implode(",",JBusinessUtil::getMonthNames());
        $jsSettings->month_names_short = implode(", ",JBusinessUtil::getShortMonthNames());
		$jsSettings->autocomplete_config = $appSettings->autocomplete_config;
		$jsSettings->enable_map_clustering = $appSettings->enable_map_clustering;
		$jsSettings->map_enable_auto_locate = $appSettings->map_enable_auto_locate;
		$jsSettings->projects_style = $appSettings->projects_style;
		$jsSettings->search_filter_items = $appSettings->search_filter_items;
		$jsSettings->event_search_filter_items = $appSettings->event_search_filter_items;
		$jsSettings->offer_search_filter_items = $appSettings->offer_search_filter_items;
		$jsSettings->search_filter_view = $appSettings->search_filter_view;
		$jsSettings->search_type = $appSettings->search_type;
		$jsSettings->event_search_type = $appSettings->event_search_type;
		$jsSettings->offer_search_type = $appSettings->offer_search_type;
		$jsSettings->metric = $appSettings->metric;
		$jsSettings->search_filter_type = $appSettings->search_filter_type;
		$jsSettings->offers_search_filter_type = $appSettings->offers_search_filter_type;
		$jsSettings->events_search_filter_type = $appSettings->events_search_filter_type;
		$jsSettings->speaker_img_width = $appSettings->speaker_img_width;
		$jsSettings->speaker_img_height = $appSettings->speaker_img_height;
		$jsSettings->location_map_marker = $appSettings->location_map_marker;
		$jsSettings->disable_cropping_types=array(PICTURE_TYPE_CATEGORY_ICON, PICTURE_TYPE_MARKER);
		$jsSettings->enable_ratings = $appSettings->enable_ratings;
		$jsSettings->edit_form_mode = $appSettings->edit_form_mode;
		$jsSettings->search_results_loading = $appSettings->search_results_loading;
		$jsSettings->google_map_key = $appSettings->google_map_key;
		$jsSettings->show_search_map = $appSettings->show_search_map;

		$langTab = JBusinessUtil::getLanguageTag();
		$langTab = str_replace("-", "_", $langTab);
		$jsSettings->langTab = $langTab;

		$jsSettings->defaultLang = JBusinessUtil::getLanguageTag();

		return $jsSettings;
	}

	/**
	 * Get available user groups
	 */
	public static function getUserGroups() {
		$roles = wp_roles()->roles;
		$options = array();
		foreach ($roles as $roleName => $role) {
			$option = new stdClass();
			$option->value = $roleName;
			$option->name = $role["name"];
			$options[] = $option;
		}

		return $options;
	}

		/**
	 * Get available user groups
	 */
	public static function getUserGroupCodes() {
		$roles = wp_roles()->roles;
		$options = array();
		foreach ($roles as $code => $role) {
			$options[] = $code;
		}

		return $options;
	}

	/**
	 *  Get all users on system
	 */
	public static function getAllUsers() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.display_name AS text, a.user_login AS display_name')
			->from($db->quoteName('#__users') . ' AS a')
			->order('a.display_name ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		foreach ($options as &$option) {
			$option->name = $option->text . "-" . $option->display_name;
		}

		return $options;
	}

	/**
	 * Method to retrieve the ID of an extension based by it's type and name
	 *
	 * @param $type string type of the extension
	 * @param $name string name of the extension
	 * @return mixed
	 */
	public static function getExtensionID($type, $name) {
		$db = JFactory::getDbo();

		$query = "select ext.extension_id 
                  from #__extensions as ext
                  where ext.type='$type' and ext.element = '$name'
                  ";

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->extension_id;
	}

	/**
	 *  Return the id of the country based on hic code
	 * @param $countryCode
	 * @return mixed
	 */
	public static function getCountryIDByCode($countryCode) {
		$db = JFactory::getDbo();

		$query = "select co.id 
                  from #__jbusinessdirectory_countries as co
                  where co.country_code = '$countryCode'
                  ";

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->id;
	}

	/**
	 * This function get the content as a string and removes all rel attributes on anchor links if there is any
	 *
	 * @param $string string content that will be analyzed and where the rel attribute will be removed
	 * @return mixed string content with rel attributes removed
	 */
	public static function removeRelAttribute($string) {
		$string = str_replace("rel=","",$string);
		return $string;
	}

	/**
	 * Method that groups all attributes together in an array based on the group name.
	 * The indexes of the final 2-dimensional array will correspond to the group, and all
	 * attributes belonging to that group will be inside an array in that particular index.
	 *
	 * An extra index called ungrouped is created for the attributes with no group.
	 *
	 * @param $attributes array containing all attributes with their relevant fields
	 *
	 * @return array
	 *
	 * @since 4.9.0
	 */
	public static function arrangeAttributesByGroup($attributes) {
		$groupedAttr = array();
		$groupedAttr['ungrouped'] = array();
		foreach ($attributes as $attribute) {
			if (!empty($attribute->group)) {
				if (!isset($groupedAttr[$attribute->group])) {
					$groupedAttr[$attribute->group] = array();
				}

				$groupedAttr[$attribute->group][] = $attribute;
			} else {
				$groupedAttr['ungrouped'][] = $attribute;
			}
		}

		return $groupedAttr;
	}
	
	/**
	 * Filter payment methods based on package type.
	 * If package is of type recurring and there is a recurring payment method available, only that method will be visible
	 *
	 * @param unknown $order
	 * @param unknown $paymentMethods
	 */
	public static function filterPaymentMethods($order, $paymentMethods) {
		$result = array();
		
		if (empty($order)) {
			return $paymentMethods;
		}
		
		if ($order->package->expiration_type == 3 || $order->package->expiration_type == 4) {
			foreach ($paymentMethods as $paymentMethod) {
				if (isset($paymentMethod->recurring) && $paymentMethod->recurring) {
					$result[] = $paymentMethod;
				}
			}
		}else{
			foreach ($paymentMethods as $paymentMethod) {
				if (!isset($paymentMethod->recurring) || !$paymentMethod->recurring) {
					$result[] = $paymentMethod;
				}
			}
		}
		
	
		return $result;
	}

	public static function getRazorpaySupportedCurrencies() {
		$currencies = array(
		'AED','ALL', 'AMD','ARS','AUD','AWG','BBD','BDT','BMD','BND','BOB','BSD','BWP','BZD','CAD','CHF','CNY','COP','CRC','CUP','CZK','DKK','DOP','DZD','EGP','ETB',
		'EUR','FJD','GBP','GHS','GIP','GMD','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','JMD','KES','KGS','KHR','KYD','KZT','LAK','LKR','LRD','LSL',
		'MAD','MDL','MKD','MMK','MNT','MOP','MUR','MVR','MWK','MXN','MYR','NAD','NGN','NIO','NOK','NPR','NZD','PEN','PGK','PHP','PKR','QAR','RUB','SAR','SCR','SEK',
		'SGD','SLL','SOS','SSP','SVC','SZL','THB','TTD','TZS','USD','UYU','UZS','YER','ZAR');

		return $currencies;
	}

	/**
	 * Loads the appropriate scripts depending on the selected map type on
	 * general settings or the one provided as a parameter.
	 * Will return also the value of the respective API key.
	 *
	 * @param null $mapType int type of the map
	 *
	 * @param bool $asyncDefer
	 *
	 * @return string
	 *
	 * @since 4.9.5
	 */
	public static function loadMapScripts($mapType = null, $asyncDefer = false) {
		$key         = "";
		$lang        = JBusinessUtil::getLanguageTag();
		$appSettings = JBusinessUtil::getApplicationSettings();

		if (empty($mapType)) {
			$mapType = $appSettings->map_type;
		}

		if ($mapType == MAP_TYPE_GOOGLE) {
			if (!empty($appSettings->google_map_key)) {
				$key = "&key=" . $appSettings->google_map_key;
			}
            if ($appSettings->enable_map_gdpr && !isset($_COOKIE['jbd_map_gdpr'])) {
                return false;
            } else {
						wp_enqueue_script('google-map', "https://maps.googleapis.com/maps/api/js?language=" . $lang . $key . "&libraries=geometry&libraries=places");
            }

			if ($appSettings->enable_map_clustering) {
				wp_enqueue_script('google-map-obj-front', "https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js");
			}
		} elseif ($mapType == MAP_TYPE_BING) {
			if (!empty($appSettings->bing_map_key)) {
				$key = $appSettings->bing_map_key;
			}

			wp_enqueue_script("https://www.bing.com/api/maps/mapcontrol?key=$key");
		} elseif ($mapType == MAP_TYPE_OSM) {
			JBusinessUtil::loadJQueryUI();

			JBusinessUtil::enqueueStyle('libraries/leaflet/leaflet.css');
			JBusinessUtil::enqueueStyle('libraries/leaflet/leaflet-search.css');
			JBusinessUtil::enqueueScript('libraries/leaflet/leaflet.js');
			JBusinessUtil::enqueueScript('libraries/leaflet/leaflet-search.js');

			JBusinessUtil::enqueueStyle('libraries/leaflet/leaflet.fullscreen.css');
			JBusinessUtil::enqueueScript('libraries/leaflet/leaflet.fullscreen.min.js');

			if ($appSettings->enable_map_clustering) {
				JBusinessUtil::enqueueScript('libraries/leaflet/leaflet.markercluster.js');
				JBusinessUtil::enqueueStyle('libraries/leaflet/MarkerCluster.css');
				JBusinessUtil::enqueueStyle('libraries/leaflet/MarkerCluster.Default.css');
			}
		}

		return $key;
	}

	/**
	 * Gets a static map image of the provided position (lat, long).
	 *
	 * @param float  $lat latitude of the position
	 * @param float  $long longitude of the position
	 * @param string $class css class of the image
	 * @param int    $width of the map
	 * @param int    $height of the map
	 * @param int    $zoom
	 * @param int    $mapType defaults to google map if not specified
	 *
	 * @return string
	 *
	 * @since 4.9.5
	 */
	public static function getStaticMap($lat, $long, $class = "company map", $width = 400, $height = 300, $zoom = 13, $mapType = null) {
		$db = JFactory::getDbo();
		$appSettings = JBusinessUtil::getApplicationSettings();

		$html = '';
		$src  = '';

		$lat   = $db->escape($lat);
		$long  = $db->escape($long);

		if (empty($mapType)) {
			$mapType = $appSettings->map_type;
		}

		if ($mapType == MAP_TYPE_GOOGLE) {
			$key = "&key=".$appSettings->google_map_key;

			$src = "https://maps.googleapis.com/maps/api/staticmap?center=";
			$src .= $lat.",".$long;
			$src .= "&zoom=".$zoom."&size=".$width."x".$height."&markers=color:blue|";
			$src .= $lat.",".$long;
			$src .= $key;
			$src .= "&sensor=false";
		} elseif ($mapType == MAP_TYPE_BING) {
			$key = "&key=".$appSettings->bing_map_key;

			$src = "https://dev.virtualearth.net/REST/v1/Imagery/Map/Road/";
			$src .= $lat.",".$long;
			$src .="/".$zoom;
			$src .= "?mapSize=".$width.",".$height;
			$src .= "&pp=".$lat.",".$long;
			$src .= $key;
		} elseif ($mapType == MAP_TYPE_OSM) {
			$src = get_site_url(). '/wp-content/plugins/wp-businessdirectory/site/libraries/staticmaplite/staticmap.php?';
			$src .= "center=" . $lat . "," . $long;
			$src .= "&zoom=" . $zoom;
			$src .= "&size=" . $width . "x" . $height;
			$src .= "&markers=" . $lat . "," . $long;
		}

		$html .= "<img alt='".$class."' ";
		$html .= "src='".$src."'";
		$html .= ">";

		return $html;
	}

	/**
	 * Loads all base scripts needed for all JBD
	 *
	 * @since 5.3.1
	 */
	public static function loadBaseScripts() {
        //JBusinessUtil::enqueueScript('libraries/react/development/react.development.js');
        //JBusinessUtil::enqueueScript('libraries/react/development/react-dom.development.js');
		JBusinessUtil::enqueueScript('libraries/react/production/react.production.min.js');
        JBusinessUtil::enqueueScript('libraries/react/production/react-dom.production.min.js');
        JBusinessUtil::enqueueScript('libraries/babel/babel.min.js');
		JBusinessUtil::enqueueScript('js/jbd-app.js');
	}

	/**
	 * get yelp data
	 * @param $id int listing id on yelp
	 * @param bool $getReviews get Reviews of listing or not
	 * @return mixed
	 */
	public static function getYelpData($id, $getReviews = false) {
		$access_token = 'V0RYaofP72UIt0j7ohSOU1OX2V_hfKPwRdk7WPrWCO-XcZPMjmhe-rzGyBDhrYki_izUfrBsCx8qyydyRcz1Sy7gDSU6GbZCxj-IYre_O6-Mqzsj6qG4ydNKGUVDWHYx';
		$url = 'https://api.yelp.com/v3/businesses/'.$id;

		if ($getReviews) {
			$url .= '/reviews';
		}

		//Initialize cURL.
		$ch = curl_init();

		//Set the URL that you want to GET by using the CURLOPT_URL option.
		curl_setopt($ch, CURLOPT_URL, $url);

		//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				'token_type: Bearer',
				'Authorization: Bearer '.$access_token,
			)
		);

		//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		//Execute the request.
		$data = curl_exec($ch);

		//Close the cURL handle.
		curl_close($ch);
		//dump(json_decode($data));exit;
		return json_decode($data);
	}

	/**
	 * Get more search filter options
	 *
	 * @return array
	 */
	public static function getMoreSearchFilterOptions() {
		$options = array();

		$option = new stdClass();
		$option->value = 'with_address';
		$option->text = JTEXT::_("LNG_WITH_ADDRESS");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_contact';
		$option->text = JTEXT::_("LNG_WITH_CONTACT");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_phone';
		$option->text = JTEXT::_("LNG_WITH_PHONE");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_email';
		$option->text = JTEXT::_("LNG_WITH_EMAIL");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_social_networks';
		$option->text = JTEXT::_("LNG_WITH_SOCIAL_NETWORKS");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_website';
		$option->text = JTEXT::_("LNG_WITH_WEBSITE");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_images';
		$option->text = JTEXT::_("LNG_WITH_IMAGES");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_videos';
		$option->text = JTEXT::_("LNG_WITH_VIDEOS");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_attached_file';
		$option->text = JTEXT::_("LNG_WITH_ATTACHED_FILE");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_description';
		$option->text = JTEXT::_("LNG_WITH_DESCRIPTION");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_products_offers';
		$option->text = JTEXT::_("LNG_WITH_PRODUCTS_OFFERS");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_events';
		$option->text = JTEXT::_("LNG_WITH_EVENTS");
		$options[] = $option;

		$option = new stdClass();
		$option->value = 'with_appointments';
		$option->text = JTEXT::_("LNG_WITH_APPOINTMENTS");
		$options[] = $option;

		return $options;
	}

	/**
	 * Returns the URL of the upload controller along with the specified
	 * controller function
	 *
	 * @param string $task name of the controller function
	 *
	 * @return string
	 */
	public static function getUploadUrl($task = 'upload') {
		return home_url() . '/index.php?directory=1&task=upload.' . $task;
	}
	
	public static function showMandatory($isMandatory) {
		$result = "";
		if ($isMandatory == ATTRIBUTE_MANDATORY) {
			$result = '<span class="star" aria-hidden="true">&nbsp;*</span>';
		}
		return $result;
	}

	/**
	 * Function that converts one currency to another by using the service provided by
	 * free.currencyconverterapi.com
	 *
	 * @param      $amount       float the amount to be converted
	 * @param      $fromCurrency int ID of the current currency
	 * @param null $toCurrency   int ID of the currency we want to convert to, defaults to app settings currency
	 *
	 * @return float|int
	 *
	 * @since 5.0.1
	 */
	public static function convertCurrency($amount, $fromCurrency, $toCurrency = null) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		if (empty($appSettings->currency_converter_api)) {
			return $amount;
		}

		if (empty($toCurrency)) {
			$toCurrency = $appSettings->currency_id;
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$currencyTable = JTable::getInstance("Currency", "JTable");
		$fromCurrency  = $currencyTable->getCurrencyById($fromCurrency)->currency_name;
		$toCurrency    = $currencyTable->getCurrencyById($toCurrency)->currency_name;

		if (function_exists('curl_init')) {
			$url = "https://free.currencyconverterapi.com/api/v6/convert?apiKey=" . $appSettings->currency_converter_api;
			$url .= "&q=" . $fromCurrency . "_" . $toCurrency;

			$ch      = curl_init();
			$timeout = 0;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$rawdata = curl_exec($ch);

			curl_close($ch);

			$data = json_decode($rawdata);
			if (empty($data) || $data->query->count == 0) {
				return $amount;
			} else {
				$field = $fromCurrency . "_" . $toCurrency;
				return $amount * $data->results->$field->val;
			}
		} else {
			return $amount;
		}
	}

	/**
	 * Checks if the provided JBD App is installed or not.
	 *
	 * @param $app int type of the JBD app
	 *
	 * @return bool
	 *
	 * @since 5.0.2
	 */
	public static function isAppInstalled($app) {
		$path = '';
		switch ($app) {
			case JBD_APP_APPOINTMENTS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/companyservice.php';
				break;
			case JBD_APP_SELL_OFFERS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/offerorder.php';
				break;
			case JBD_APP_EVENT_BOOKINGS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/eventreservation.php';
				break;
			case JBD_APP_EVENT_APPOINTMENTS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/eventappointment.php';
				break;
			case JBD_APP_CAMPAIGNS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/campaigns.php';
				break;
			case JBD_APP_QUOTE_REQUESTS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/requestquotes.php';
				break;
			case JBD_APP_STRIPE:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/StripeProcessor.php';
				break;
			case JBD_APP_STRIPE_SUBSCRIPTIONS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/StripeSubscriptions.php';
				break;
			case JBD_APP_PAYPAL_SUBSCRIPTIONS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/PaypalSubscriptions.php';
				break;
			case JBD_APP_PAYFAST_SUBSCRIPTIONS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/PayfastSubscriptions.php';
				break;
			case JBD_APP_AUTHORIZE:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/Authorize.php';
				break;
			case JBD_APP_AUTHORIZE_SUBSCRIPTIONS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/AuthorizeSubscriptions.php';
				break;
            case JBD_APP_MERCADO_PAGO:
                $path = JPATH_COMPONENT_SITE . '/classes/payment/processors/MercadoPagoProcessor.php';
				break;
			case JBD_APP_ELASTIC_SEARCH:
				$path = JPATH_COMPONENT_SITE . '/classes/elasticsearch/JBusinessElasticHelper.php';
				break;
			case JBD_APP_TRIPS:
				$path = JPATH_COMPONENT_SITE . '/controllers/trip.php';
				break;
			case JBD_APP_VIDEOS:
				$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/video.php';
				break;
			case JBD_APP_MOLLIE:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/Mollie.php';
				break;
			case JBD_APP_MOLLIE_SUBSCRIPTIONS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/MollieSubscriptions.php';
				break;
			case JBD_APP_CARDLINK:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/Cardlink.php';
				break;
			case JBD_APP_CARDLINK_SUBSCRIPTIONS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/CardlinkSubscriptions.php';
				break;
			case JBD_APP_RAZORPAY:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/Razorpay.php';
				break;
			case JBD_APP_ASAAS:
				$path = JPATH_COMPONENT_SITE . '/classes/payment/processors/Asaas.php';
				break;
			default:
				$path = '';
		}

		if (empty($path)) {
			return false;
		}

		return file_exists($path);
	}

	/**
	 * Increase the GROUP_CONTACT lenght to 10000 characters
	 *
	 * @return mixed
	 */
	public static function setGroupConcatLenght() {
		$instance = JBusinessUtil::getInstance();
		
		if (isset($instance->groupConcatLenghtSet)) {
			return $instance->groupConcatLenghtSet;
		}
		
		$db =JFactory::getDBO();
		
		$db->setQuery("SET SESSION group_concat_max_len = 10000");
		$instance->groupConcatLenghtSet = $db->execute();
		
		return $instance->groupConcatLenghtSet;
	}

	/**
	 * SET SQL_BIG_SELECTS=1
	 * @return mixed
	 */
	public static function setBigSqlSelects() {
		$instance = JBusinessUtil::getInstance();
		
		if (isset($instance->bigSqlSelects)) {
			return $instance->bigSqlSelects;
		}
		
		$db =JFactory::getDBO();
		
		$db->setQuery("SET SQL_BIG_SELECTS=1");
		$instance->bigSqlSelects = $db->execute();
		
		return $instance->bigSqlSelects;
	}

	/**
	 * Get announcements action types and default options
	 *
	 * @return array
	 */
	public static function getAnnouncementActions() {
		$options = array();

		$option = new stdClass();
		$option->id = '1';
		$option->name = JTEXT::_("LNG_ANNOUNCEMENTS");
		$option->title = JTEXT::_("LNG_ANNOUNCEMENT_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_ANNOUNCEMENT_ACTION_DESCRIPTION");
		$option->icon = 'icon bullhorn';
		$option->button_text = JTEXT::_("LNG_ANNOUNCEMENT_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_ANNOUNCEMENT_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '2';
		$option->name = JTEXT::_("LNG_CONTACT_US");
		$option->title = JTEXT::_("LNG_CONTACT_US_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_CONTACT_US_ACTION_DESCRIPTION");
		$option->icon = 'la la-envelope-o';
		$option->button_text = JTEXT::_("LNG_CONTACT_US_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_CONTACT_US_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '3';
		$option->name = JTEXT::_("LNG_BOOK_NOW");
		$option->title = JTEXT::_("LNG_BOOK_NOW_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_BOOK_NOW_ACTION_DESCRIPTION");
		$option->icon = 'la la-adjust';
		$option->button_text = JTEXT::_("LNG_BOOK_NOW_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_BOOK_NOW_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '4';
		$option->name = JTEXT::_("LNG_BUY_TICKETS");
		$option->title = JTEXT::_("LNG_BUY_TICKETS_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_BUY_TICKETS_ACTION_DESCRIPTION");
		$option->icon = 'la la-cart-plus';
		$option->button_text = JTEXT::_("LNG_BUY_TICKETS_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_BUY_TICKETS_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '5';
		$option->name = JTEXT::_("LNG_GET_OFFER");
		$option->title = JTEXT::_("LNG_GET_OFFER_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_GET_OFFER_ACTION_DESCRIPTION");
		$option->icon = 'la la-envelope-o';
		$option->button_text = JTEXT::_("LNG_GET_OFFER_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_GET_OFFER_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '6';
		$option->name = JTEXT::_("LNG_GET_QUOTE");
		$option->title = JTEXT::_("LNG_GET_QUOTE_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_GET_QUOTE_ACTION_DESCRIPTION");
		$option->icon = 'icon bullhorn';
		$option->button_text = JTEXT::_("LNG_GET_QUOTE_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_GET_QUOTE_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '7';
		$option->name = JTEXT::_("LNG_JOIN_NOW");
		$option->title = JTEXT::_("LNG_JOIN_NOW_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_JOIN_NOW_ACTION_DESCRIPTION");
		$option->icon = 'la la-adjust';
		$option->button_text = JTEXT::_("LNG_JOIN_NOW_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_JOIN_NOW_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		$option = new stdClass();
		$option->id = '8';
		$option->name = JTEXT::_("LNG_SCHEDULE_NOW");
		$option->title = JTEXT::_("LNG_SCHEDULE_NOW_ACTION_TITLE");
		$option->description = JTEXT::_("LNG_SCHEDULE_NOW_ACTION_DESCRIPTION");
		$option->icon = 'la la-calendar';
		$option->button_text = JTEXT::_("LNG_SCHEDULE_NOW_ACTION_BUTTON_TEXT");
		$option->button_link = JTEXT::_("LNG_SCHEDULE_NOW_ACTION_BUTTON_LINK");
		$options[$option->id] = $option;

		return $options;
	}

	/**
	 * Get Website URL
	 * @return string
	 */
	public static function getWebsiteURL() {
		$base = get_site_url()."/";

		return $base;
	}

	/**
	 * Get responsive content place holders
	 *
	 * @return array
	 */
	public static function getPlaceholders() {
		$placeholders = array(
			PLACEHOLDER_COMPANY_NAME => JText::_("LNG_PLACEHOLDER_COMPANY_NAME"),
			PLACEHOLDER_ADDRESS => JText::_("LNG_ADDRESS"),
			PLACEHOLDER_EMAIL => JText::_("LNG_EMAIL")
		);

		return $placeholders;
	}

	/**
	 * Process responsible content
	 * @param $company object company object
	 * @param $content string content to process
	 * @return string|string[]
	 */
	public static function processResponsibleCotent($company, $content) {
		$content = str_replace(PLACEHOLDER_COMPANY_NAME, $company->name, $content);
		$content = str_replace(PLACEHOLDER_ADDRESS, (string)self::getAddressText($company), $content);
		$content = str_replace(PLACEHOLDER_EMAIL, $company->email, $content);
		$content = str_replace(PLACEHOLDER_PHONE, $company->phone, $content);

		return $content;
	}

	/**
	 * Generate a message for pages with no items with a new item button
	 *
	 * @param $singleItemText string text for single item
	 * @param $multipleItemsText string text for multiple items
	 * @param $actionUrl string url for creating new item
	 * @return false|string
	 */
	public static function getNewItemMessageBlock($singleItemText, $multipleItemsText, $actionUrl) {
		$title = JText::sprintf('LNG_MESSAGE_CREATE_NEW_TITLE', strtolower($multipleItemsText));
		$text=JText::sprintf('LNG_MESSAGE_CREATE_NEW_TEXT', strtolower($singleItemText));
		$btnText = JText::sprintf('LNG_MESSAGE_BTN_CREATE_NEW', $singleItemText);
		ob_start(); ?>
        <div class="jbd-message-container">
        	<div>
        		<img class="message-image" alt="Create new"
        			src="data:image/png;base64,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">
        		<h2 class="message-header"><?php echo $title ?></h2>
        		<p class="messge-text">
        			<?php echo $text ?>
        		</p>
        		<p>
        			<a href="<?php echo $actionUrl?>" class="btn btn-success m-2 px-5 py-2" type="button">
        				<i class="la la-plus"></i> <?php echo ucwords($btnText) ?>
        			</a>
        		</p>
        	</div>
        </div>
        <?php
		
		// Done with the requested template; get the buffer and
		// clear it.
		$messageContent = ob_get_contents();
		ob_end_clean();
		
		return $messageContent;
	}

	/**
	 * Generate a message for pages with no items
	 *
	 * @param $singleItemText string text for single item
	 * @param $multipleItemsText string text for multiple items
	 * @return false|string
	 */
	public static function getNoItemMessageBlock($singleItemText, $multipleItemsText) {
		$title = JText::sprintf('LNG_MESSAGE_NO_ITEMS_TITLE', mb_strtolower($multipleItemsText));
		$text=JText::sprintf('LNG_MESSAGE_NO_ITEMS_TEXT', mb_strtolower($singleItemText));
		ob_start(); ?>
        <div class="jbd-message-container">
        	<div>	
        		<img class="message-image" alt="Create new"
        			src="data:image/png;base64,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">
        		<h3 class="message-header"><?php echo $title ?></h3>
        		<p class="messge-text">
        			<?php echo $text ?>
        		</p>
        	</div>
        </div>
        <?php
		
		// Done with the requested template; get the buffer and
		// clear it.
		$messageContent = ob_get_contents();
		ob_end_clean();
		
		return $messageContent;
	}

	/**
	 * Returns the HTMl for the Map GDPR Modal
	 *
	 * @return string HTML of the modal
	 *
	 * @since 5.2.0
	 */
	public static function getMapGDPRModal() {
		return '<div class="jmodal-sm" id="map-gdpr-modal" style="display:none;text-align:center;">
                <div class="jmodal-header">
                    <h3>'.JText::_("LNG_MAP_GDPR_TITLE").'</h3>
                    <hr/>
                </div>
                <div class="jmodal-body">
                    <div class="d-flex justify-content-between">
                        <img class="map-gdpr-img" />
                        <p>'.JText::_("LNG_MAP_GDPR_TEXT").'</p>
                    </div>
                    <br/>
                </div>
                <div class="jmodal-footer">
                    <div class="btn-group" role="group" aria-label="">
                        <button type="button" id="gdpr-deny-btn" class="jmodal-btn btn btn-secondary">'.JText::_("LNG_DENY").'</button>
                        <button type="button" id="gdpr-accept-btn" class="jmodal-btn btn btn-success">'.JText::_("LNG_ACCEPT").'</button>
                    </div>
                </div>
            </div>';
	}

	/**
	 * Retrieves companies based on search string
	 *
	 * @param      $str string search string
	 * @param null $userId int ID of the user
	 *
	 * @return array
	 *
	 * @since 5.2.0
	 */
	public static function getCompaniesByString($str, $userId = null) {
		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$companyTable = JTable::getInstance("Company", "JTable");

		$company = $companyTable->getCompaniesByString($str, $userId);

		$result = array();
		foreach ($company as $item) {
			$result[] = $item;
		}

		return $result;
	}

	/**
	 * Retrieves Events based on search string
	 *
	 * @param      $str string search string
	 * @param null $userId int ID of the user
	 *
	 * @return array
	 *
	 * @since 5.2.0
	 */
	public static function getEventsByString($str, $userId = null) {
		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$table = JTable::getInstance("Event", "JTable");

		$items = $table->getEventsByString($str, $userId);

		$result = array();
		foreach ($items as $item) {
			$result[] = $item;
		}

		return $result;
	}

	/**
	 * Retrieves Offers based on search string
	 *
	 * @param      $str string search string
	 * @param null $userId int ID of the user
	 *
	 * @return array
	 *
	 * @since 5.2.0
	 */
	public static function getOffersByString($str, $userId = null) {
		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$table = JTable::getInstance("Offer", "JTable");

		$items = $table->getOffersByString($str, $userId);

		$result = array();
		foreach ($items as $item) {
			$result[] = $item;
		}

		return $result;
	}

	/**
	 * Retrieves Offers based on search string
	 *
	 * @param      $str string search string
	 * @param null $userId int ID of the user
	 *
	 * @return array
	 *
	 * @since 5.2.0
	 */
	public static function getSessionLocationByString($str, $userId = null) {
		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$table = JTable::getInstance("SessionLocation", "JTable");

		$items = $table->getSessionLocationByString($str, $userId);

		$result = array();
		foreach ($items as $item) {
			$result[] = $item;
		}

		return $result;
	}


    /**
     * Get companies by user id
     *
     * @return array
     */
    public static function getCompaniesByUserId($userId = null, $idsOnly = false) {
        if (empty($userId)) {
            $user = self::getUser();
            $userId = $user->ID;
        }

        JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
        $companyTable = JTable::getInstance("Company", "JTable");

        $companies = $companyTable->getCompaniesByUserId($userId);

        if ($idsOnly) {
            $result = array();
            foreach ($companies as $company) {
                $result[] = $company->id;
            }
            $companies = $result;
        }

        return $companies;
    }

	/**
	 * Get user events
	 * @param $userId int user id by default is null
	 * @return array
	 */
	public static function getUserEvents($userId = null) {
		if (empty($userId)) {
			$user = self::getUser();
			$userId = $user->ID;
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$table = JTable::getInstance("Event", "JTable");

		$items =  $table->getUserEvents($userId);
		$result = array();
		foreach ($items as $item) {
			$result[] = $item->id;
		}
		return $result;
	}


	/**
	 * Subscract $subsctract array from source array
	 * 
	 */
	public static function substractArrays($source, $substract){
		$result = array();
		foreach($source as $src){
			$found = false;
			foreach($substract as $sub){
				if($src->id == $sub->id){
					$found = true;
					break;
				}
			}
			if(!$found){
				$result[] = $src;
			}
		}

		return $result;
	}

	/**
	 * Generate request summary for quote requests
	 *
	 * @param $summary object summary details
	 * @return string
	 */
	public static function generateRequestSummary($summary) {
		$questionAnsers = json_decode($summary);
		$text = '';
		foreach ($questionAnsers as $key => $option) {
			$nr = ++$key;
			$answers = explode('#', $option->answer);
			unset($answers[0]);
			$text .= "<div style='margin-bottom: 10px'>";
				$text .= "<div class='text-info cmall'><strong>Q$nr:</strong> " . $option->question . "</div>";
				$count = 1;
				foreach ($answers as $answer) {
					$text .= "<div class='small text-muted'>" . $answer . " </div>";
				}
			$text .= "</div>";
		}
		return $text;
	}

	/**
	 * Loads the upload script along with the required translations. Will also render the HTML needed by the JS on screen.
	 *
	 * @since 5.3.0
	 *
	 * @param bool $includeAttachments      boolean if true, will include the HTML for attachments
	 * @param bool $includeDropzonePictures boolean if true, will include the HTML for the dropzone images list
	 * @param bool $includeCropper  boolean if true, will include the cropper modal HTML
	 */
	public static function loadUploadScript($includeAttachments = false, $includeDropzonePictures = false, $includeCropper = true, $extraPictures = false) {
		JBusinessUtil::enqueueScript('libraries/jquery/jquery.upload.js');
		
		JText::script('LNG_IMAGE_SIZE_WARNING');
		JText::script('LNG_FILE_ALLREADY_ADDED');
		JText::script('LNG_ERROR_ADDING_FILE');
		JText::script('LNG_ERROR_RESIZING_FILE');
		JText::script('LNG_ERROR_REMOVING_FILE');
		JText::script('LNG_FILE_DOESNT_EXIST');
		JText::script('LNG_FILENAME_TOO_LONG');
		JText::script('LNG_MAX_ATTACHMENTS_ALLOWED');
		JText::script('LNG_CONFIRM_DELETE_ATTACHMENT');
		JText::script('LNG_CONFIRM_DELETE_PICTURE');

		$attachmentTemplate = '
        <div id="attachment-item-template" style="display:none;">
                <li class="jbd-item" id="jbd-item-{attachment_id}">
                    <div class="jupload-files">
                        <div class="jupload-files-img">
                            <i class="la la-file"></i>
                        </div>
                        <div class="jupload-files-info">
                            <div class="jupload-filename">
                                <p>{attachment_path}</p>
                                <input id="jupload-filename-{attachment_id}" type="text"
                                       name="attachment_name[]" value="{attachment_name}" maxlength="100">
                            </div>
                            <div class="jupload-actions jbd-item-actions">
                                <label for="jupload-filename-{attachment_id}">
                                    <i class="la la-pencil"></i>
                                </label>
            
                                <input type="hidden" name="attachment_status[]" id="attachment_status_{attachment_id}" value="{attachment_status}" />
                                <input type=\'hidden\' name=\'attachment_path[]\' id=\'attachment_path_{attachment_id}\' value=\'{attachment_full_path}\' />
                            </div>
                        </div>
                    </div>
                </li>
            </div>';

		$pictureTemplate = '
            <div id="picture-item-template" style="display:none;">
                <li class="jbd-item" id="jbd-item-{picture_id}">
                    <div class="jupload-files">
                        <div class="jupload-files-img">
                            {picture_link}
                        </div>
                        <div class="jupload-files-info">
                            <div class="jupload-filename">
                                <p>{picture_path}</p>
                                <input id="jupload-filename-{picture_id}" type="text"
                                       name="picture_title[]" maxlength="255" value="{picture_title}" placeholder="'.JText::_("LNG_TITLE").'">
                                <input id="jupload-filename-{picture_id}" type="text"
                                       name="picture_info[]" maxlength="255" value="{picture_info}" placeholder="'.JText::_("LNG_DESCRIPTION").'">
                            </div>
                            <div class="jupload-actions jbd-item-actions">
                                <label for="jupload-filename-{picture_id}">
                                    <i class="la la-pencil"></i>
                                </label>
            
                                <input type="hidden" name="picture_enable[]" id="picture_enable_{picture_id}" value="{picture_enable}" />
                                <input type=\'hidden\' name=\'picture_path[]\' id=\'picture_path_{picture_id}\' value=\'{picture_full_path}\' />
                            </div>
                        </div>
                    </div>
                </li>
            </div>';

			$extraPictureTemplate = '
            <div id="extra-picture-item-template" style="display:none;">
                <li class="jbd-item" id="jbd-item-{image_id}">
                    <div class="jupload-files">
                        <div class="jupload-files-img">
                            {image_link}
                        </div>
                        <div class="jupload-files-info">
                            <div class="jupload-filename">
                                <p>{image_path}</p>
                                <input id="jupload-filename-{image_id}" type="text"
                                       name="image_title[]" maxlength="255" value="{image_title}" placeholder="'.JText::_("LNG_TITLE").'">
                                <input id="jupload-filename-{image_id}" type="text"
                                       name="image_info[]" maxlength="255" value="{image_info}" placeholder="'.JText::_("LNG_DESCRIPTION").'">
                            </div>
                            <div class="jupload-actions jbd-item-actions">
                                <label for="jupload-filename-{image_id}">
                                    <i class="la la-pencil"></i>
                                </label>
            
                                <input type="hidden" name="image_enable[]" id="image_enable_{image_id}" value="{image_enable}" />
                                <input type=\'hidden\' name=\'image_path[]\' id=\'image_path_{picture_id}\' value=\'{image_full_path}\' />
                            </div>
                        </div>
                    </div>
                </li>
            </div>';

		$cropperModal = '
					<div id="cropper-modal" class="jbd-container" style="display: none">    
						<div class="jmodal-sm">
							<div class="jmodal-header">
								<p class="jmodal-header-title">'.JText::_("LNG_IMAGE_CROPPING").'</p>
								<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
							</div>
							<div class="jmodal-body">
                                <p>'.JText::_("LNG_IMAGE_CROPPING_DESCRIPTION").'</p>
                                <br/>
                                <div>
                                    <img id="cropper-image" src="" />
                                </div>
				
                                <div class="row-fluid">
                                    <button type="button" id="save-cropped" class="btn btn-success mt-2">
                                        <span class="ui-button-text"><i class="la la-pencil"></i>'.JText::_("LNG_SAVE").'</span>
                                    </button>
                                    <button type="button" class="btn btn-dark mt-2" onclick="jQuery.jbdModal.close()">
                                        <span class="ui-button-text"><i class="la la la-close"></i>'.JText::_("LNG_CANCEL").'</span>
                                    </button>
                                </div>
                            </div>
						</div>
					</div>';

		if ($includeAttachments) {
			echo $attachmentTemplate;
		}

		if ($includeDropzonePictures) {
			echo $pictureTemplate;
		}

		if ($extraPictures) {
			echo $extraPictureTemplate;
		}

		if ($includeCropper) {
			echo $cropperModal;
		}
	}
  
	/**
	 * Retrieve the trial end date based on trial period unit and trial period amount
	 *
	 * @param      $package object details of the package
	 * @param null $date string the date from when the calculation will be made from, defaults to current date
	 *
	 * @return false|string
	 *
	 * @since 5.3.0
	 */
	public static function getTrialEndDate($package, $date = null) {
		if (empty($date)) {
			$date = date('Y-m-d');
		}
	
		$cycle = 'days';
		if ($package->trial_period_unit == 'M') {
			$cycle = 'months';
		} elseif ($package->trial_period_unit == 'Y') {
			$cycle = 'years';
		} elseif ($package->trial_period_unit == 'W') {
			$cycle = 'weeks';
		}
	
		$end = date('Y-m-d', strtotime("+".$package->trial_period_amount." $cycle", strtotime($date)));
	
		return $end;
	}

	/**
	 * Checks if a language constant has a translation for the active language.
	 *
	 * @param $languageConstant string
	 *
	 * @return bool
	 * @since 5.3.1
	 */
	public static function hasTranslation($languageConstant) {
		$result = strcmp(JText::_($languageConstant), $languageConstant) != 0;

		return $result;
	}

	/**
	 * Logs all the search parameters and values searched for
	 *
	 * @param $searchDetails array containing all searched parameters and values searched for
	 * @param $type int type of item where the search is done
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function logSearch($searchDetails, $type) {
		$totalData = array();
		foreach ($searchDetails as $paramSearched => $searchedVal) {
			$dataToSave = array();
			if (strpos($paramSearched, 'keyword') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_KEYWORD;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'categoriesIds') !== false) {
				foreach ($searchedVal as $val) {
					$categories = explode(',', $val);
					$categories = array_filter($categories);
					foreach ($categories as $category) {
						$dataToSave['item_type'] = SEARCH_LOG_CATEGORY;
						$dataToSave['value'] = $category;
						$dataToSave['has_text'] = 0;
					}
				}
			} elseif (strpos($paramSearched, 'typeSearch') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_TYPE;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 0;
				}
			} elseif (strpos($paramSearched, 'latitude') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_LOCATION;
					$dataToSave['value'] = $searchedVal . "##" . $searchDetails['longitude'];
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'countrySearch') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_COUNTRY;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 0;
				}
			} elseif (strpos($paramSearched, 'provinceSearch') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_PROVINCE;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'regionSearch') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_REGION;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'citySearch') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_CITY;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'minprice') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_MIN_PRICE;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'maxprice') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_MAX_PRICE;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'startDate') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_START_DATE;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'endDate') !== false) {
				if (!empty($searchedVal)) {
					$dataToSave['item_type'] = SEARCH_LOG_END_DATE;
					$dataToSave['value'] = $searchedVal;
					$dataToSave['has_text'] = 1;
				}
			} elseif (strpos($paramSearched, 'customAttributes') !== false) {
				if (!empty($searchedVal)) {
					JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
					$attrTable = JTable::getInstance("Attribute", "JTable");
					foreach ($searchedVal as $attrId => $val) {
						$attribute = $attrTable->getAttributesConfiguration($attrId)[0];
						if ($attribute->attributeTypeCode == 'input' || $attribute->attributeTypeCode == 'textarea'
							|| $attribute->attributeTypeCode == 'link' || $attribute->attributeTypeCode == 'header') {
							$dataToSave['has_text'] = 1;
							$dataToSave['value'] = $val;
						} else {
							$dataToSave['has_text'] = 0;
							$dataToSave['value'] = $val;
						}
						$dataToSave['item_type'] = SEARCH_LOG_CUSTOM_ATTRIBUTE;
					}
				}
			}
			if (!empty($dataToSave)) {
				$totalData[] = $dataToSave;
			}
		}

		if (!empty($totalData)) {
			$db =JFactory::getDBO();
			$created = self::convertToMysqlFormat(date('Y-m-d'));
			foreach ($totalData as $data) {
				$query = "insert into  #__jbusinessdirectory_search_logs (item_type, object_type, date, value, has_text) values 
			('".$db->escape($data['item_type'])."','".$db->escape($type)."','".$db->escape($created)."','".$db->escape($data['value'])."','".$db->escape($data['has_text'])."')";
				$db->setQuery($query);
				$db->execute();
			}
		}
		return true;
	}


	/**
	 * Get default payment processors
	 *
	 * @param bool $restrict restrict default processors based on the general settings selection
	 * @return array
	 */
	public static function getDefaultPaymentProcessors($restrict = false) {
		$defaultProcessors = array();
		
		$defaultProcessors['cash'] = JText::_('LNG_CASH');
		$defaultProcessors['wiretransfer'] = JText::_('LNG_WIRE_TRANSFER');
		$defaultProcessors['paypal'] = JText::_('LNG_PROCESSOR_PAYPAL');
		//$defaultProcessors['twocheckout'] = JText::_('LNG_PROCESSOR_2CHECKOUT');
		//$defaultProcessors['buckaroo'] = JText::_('LNG_PROCESSOR_BUCKAROO');
		//$defaultProcessors['cardsave'] = JText::_('LNG_PROCESSOR_CARDSAVE');
		//$defaultProcessors['eway'] = JText::_('LNG_PROCESSOR_EWAY');
		$defaultProcessors['payfast'] = JText::_('LNG_PROCESSOR_PAYFAST');
		$defaultProcessors['touchpay'] = JText::_('LNG_PROCESSOR_TOUCHPAY');
		$defaultProcessors['payleo'] = JText::_('LNG_PROCESSOR_PAYLEO');
		if (self::isAppInstalled(JBD_APP_ASAAS)) {
			$defaultProcessors['asaas'] = JText::_('LNG_PROCESSOR_ASAAS');
			$defaultProcessors['asaassubscriptions'] = JText::_('LNG_PROCESSOR_ASAAS_SUBSCRIPTIONS');
		}

		if (self::isAppInstalled(JBD_APP_STRIPE)) {
			$defaultProcessors['stripeprocessor'] = JText::_('LNG_PROCESSOR_STRIPE');
		}
		
		if (self::isAppInstalled(JBD_APP_STRIPE_SUBSCRIPTIONS)) {
			$defaultProcessors['stripesubscriptions'] = JText::_('LNG_PROCESSOR_STRIPE_SUBSCRIPTIONS');
		}
		
		if (self::isAppInstalled(JBD_APP_AUTHORIZE)) {
			$defaultProcessors['authorize'] = JText::_('LNG_PROCESSOR_AUTHORIZE');
		}
		
		if (self::isAppInstalled(JBD_APP_AUTHORIZE_SUBSCRIPTIONS)) {
			$defaultProcessors['authorizesubscriptions'] = JText::_('LNG_PROCESSOR_AUTHORIZE_SUBSCRIPTIONS');
		}
		
		if (self::isAppInstalled(JBD_APP_PAYPAL_SUBSCRIPTIONS)) {
			$defaultProcessors['paypalsubscriptions'] = JText::_('LNG_PROCESSOR_PAYPAL_SUBSCRIPTIONS');
		}
		
		if (self::isAppInstalled(JBD_APP_PAYFAST_SUBSCRIPTIONS)) {
			$defaultProcessors['payfastsubscriptions'] = JText::_('LNG_PROCESSOR_PAYFAST_SUBSCRIPTIONS');
		}
		
		if (self::isAppInstalled(JBD_APP_MERCADO_PAGO)) {
            $defaultProcessors['mercadopagoprocessor'] = JText::_('LNG_PROCESSOR_MERCADO_PAGO');
        }
		
		if (self::isAppInstalled(JBD_APP_MOLLIE)) {
			$defaultProcessors['mollie'] = JText::_('LNG_PROCESSOR_MOLLIE');
        }
		
		if (self::isAppInstalled(JBD_APP_MOLLIE_SUBSCRIPTIONS)) {
			$defaultProcessors['molliesubscriptions'] = JText::_('LNG_PROCESSOR_MOLLIE_SUBSCRIPTION');
        }
		
		if (self::isAppInstalled(JBD_APP_CARDLINK)) {
            $defaultProcessors['cardlink'] = JText::_('LNG_PROCESSOR_CARDLINK');
        }
		
		if (self::isAppInstalled(JBD_APP_CARDLINK_SUBSCRIPTIONS)) {
            $defaultProcessors['cardlinksubscriptions'] = JText::_('LNG_PROCESSOR_CARDLINK_SUBSCRIPTIONS');
        }
		
		if (self::isAppInstalled(JBD_APP_RAZORPAY)) {
            $defaultProcessors['razorpay'] = JText::_('LNG_PROCESSOR_RAZORPAY');
        }

		$defaultProcessors['paymaya'] = JText::_('LNG_PROCESSOR_PAYMAYA');

		// restrict default processors based on the general settings selection
		if ($restrict) {
			$appSettings = JBusinessUtil::getApplicationSettings();
			$processorTypes = explode(',', $appSettings->default_processor_types);
			
			if(empty($processorTypes)){
				return array();
			} else {
				foreach ($defaultProcessors as $type=>$name) {
					if (!in_array($type, $processorTypes)) {
						unset($defaultProcessors[$type]);
					}
				}
			}
		}
		
		return $defaultProcessors;
	}

	/**
	 * Get  the default payment processor fields
	 *
	 * @return array payment processor fields
	 */
	public static function getPaymentProcessorFields() {
		$processorFieldsConfig =
		array(
				"stripeprocessor" => array(
						"name" => "Stripe",
						"type" => "stripeprocessor",
						"fields" => array(
								"secret_key"      => JText::_("LNG_SECRET_KEY"),
								"publishable_key" => JText::_("LNG_PUBLISHABLE_KEY")
						)
				),
				"stripesubscriptions" => array(
						"name" => "Stripe Subscriptions",
						"type" => "stripesubscriptions",
						"fields" => array(
								"secret_key"      => JText::_("LNG_SECRET_KEY"),
								"publishable_key" => JText::_("LNG_PUBLISHABLE_KEY")
						)
				),
				"paypal" => array(
						"name" => "Paypal",
						"type" => "paypal",
						"fields" => array(
								"paypal_email" => JText::_("LNG_PAYPAL_EMAIL")
						)
				),
				"paypalsubscriptions" => array(
						"name" => "Paypal Subscriptions",
						"type" => "paypalsubscriptions",
						"fields" => array(
								"paypal_email" => JText::_("LNG_PAYPAL_EMAIL")
						)
				),
				"payfast" => array(
						"name" => "Payfast",
						"type" => "payfast",
						"fields" => array(
								"merchant_id" => JText::_("LNG_MERCHANT_ID"),
								"merchant_key" => JText::_("LNG_MERCHANT_KEY"),
								"passphrase" => JText::_("LNG_PASSPHRASE")
						)
				),
				"payfastsubscriptions" => array(
						"name" => "Payfast Subscriptions",
						"type" => "payfastsubscriptions",
						"fields" => array(
								"merchant_id" => JText::_("LNG_MERCHANT_ID"),
								"merchant_key" => JText::_("LNG_MERCHANT_KEY"),
								"passphrase" => JText::_("LNG_PASSPHRASE")
						)
				),
				"authorize" => array(
						"name" => "Authorize",
						"type" => "authorize",
						"fields" => array(
								"transaction_key" => JText::_("LNG_TRANSACTION_KEY"),
								"api_login_id" => JText::_("LNG_API_LOGIN_ID")
						)
				),
				"authorizesubscriptions" => array(
						"name" => "Authorize Subscriptions",
						"type" => "authorizesubscriptions",
						"fields" => array(
								"transaction_key" => JText::_("LNG_TRANSACTION_KEY"),
								"api_login_id" => JText::_("LNG_API_LOGIN_ID")
						)
				),
				"wiretransfer" => array(
						"name" => "Bank Transfer",
						"type" => "wiretransfer",
						"fields" => array(
								"bank_name" => JText::_("LNG_BANK_NAME"),
								"bank_city" => JText::_("LNG_BANK_CITY"),
								"bank_address" => JText::_("LNG_BANK_ADDRESS"),
								"bank_country" => JText::_("LNG_BANK_COUNTRY"),
								"swift_code" => JText::_("LNG_SWIFT_CODE"),
								"iban" => JText::_("LNG_IBAN"),
								"bank_account_number" => JText::_("LNG_BANK_ACCOUNT_NUMBER"),
								"bank_holder_name" => JText::_("LNG_BANK_HOLDER_NAME"),
						)
				),
				"buckaroo" => array(
						"name" => "Buckaroo",
						"type" => "buckaroo",
						"fields" => array(
								"secretKey" => JText::_("LNG_SECRETKEY"),
								"merchantId" => JText::_("LNG_MERCHANT_ID"),
						)
				),
				"cardsave" => array(
						"name" => "Cardsave",
						"type" => "cardsave",
						"fields"  => array(
								"secretKey" => JText::_("LNG_SECRETKEY"),
								"preSharedKey" => JText::_("LNG_PRESHARED_KEY"),
								"password" => JText::_("LNG_PASSWORD"),
						)
				),
				"eway" => array(
						"name" => "Eway",
						"type" => "eway",
						"fields" => array(
								"user_name" => JText::_("LNG_USER_NAME"),
								"customer_id" => JText::_("LNG_CUSTOMER_ID")
						)
				),
				"twocheckout" => array(
						"name" => "2Checkout",
						"type" => "twocheckout",
						"fields" => array(
								"account_number" => JText::_("LNG_ACCOUNT_NUMBER"),
								"secret_word" => JText::_("LNG_SECRET_WORD")
						)
				),
				"mollie" => array(
						"name" => "Mollie",
						"type" => "mollie",
						"fields" => array(
								"api_key" => JText::_("LNG_API_KEY")
						)
				),
				"molliesubscriptions" => array(
					"name" => "Mollie Subscriptions",
					"type" => "molliesubscriptions",
					"fields" => array(
							"api_key" => JText::_("LNG_API_KEY")
					)
				),
				"touchpay" => array(
						"name" => "Touch Pay",
						"type" => "touchpay",
						"fields" => array(
							"agency_code" => JText::_('LNG_AGENCY_CODE'),
							"security_code" => JText::_('LNG_SECURITY_CODE')
						)
				),
				"payleo" => array(
					"name" => "Pay Leo",
					"type" => "payleo",
					"fields" => array(
						"consumer_key" => JText::_('LNG_CONSUMER_KEY'),
						"consumer_secret" => JText::_('LNG_CONSUMER_SECRET'),
						"customer_msisdn" => JText::_('LNG_CUSTOMER_MSISDN'),
						"merchant_code"  => JText::_('LNG_MERCHANT_CODE')
					)
				),
                "mercadopagoprocessor" => array(
                    "name" => "Mercado Pago",
                    "type" => "mercadopagoprocessor",
                    "fields" => array(
                        "public_key" => JText::_('LNG_PUBLIC_KEY'),
                        "access_token" => JText::_('LNG_ACCESS_TOKEN'),
                    )
				),
				"paymaya" => array(
					"name" => "PayMaya",
					"type" => "paymaya",
					"fields" => array(
							"secret_key"      => JText::_("LNG_SECRET_KEY"),
							"publishable_key" => JText::_("LNG_PUBLISHABLE_KEY")
					)
				),
				"razorpay" => array(
					"name" => "Razorpay",
					"type" => "razorpay",
					"fields" => array(
							"razorpay_key_id"      => JText::_("LNG_KEY_ID"),
							"razorpay_key_secret" => JText::_("LNG_KEY_SECRET")
					)
				),
				"cardlink" => array(
					"name" => "Cardlink",
					"type" => "cardlink",
					"fields" => array(
							"cardlink_merchant_id"      => JText::_("LNG_MERCHANT_ID"),
							"cardlink_shared_secret" => JText::_("LNG_SHARED_SECRET_KEY")
					)
				),
				"cardlinksubscriptions" => array(
					"name" => "Cardlink Subscriptions",
					"type" => "cardlinksubscriptions",
					"fields" => array(
							"cardlink_merchant_id"      => JText::_("LNG_MERCHANT_ID"),
							"cardlink_shared_secret" => JText::_("LNG_SHARED_SECRET_KEY")
					)
				),
				"asaas" => array(
					"name" => "Asaas",
					"type" => "asaas",
					"fields" => array(
							"asaas_api_key"      => JText::_("LNG_API_KEY"),
					)
				),
				"asaassubscriptions" => array(
					"name" => "Asaas Subscriptions",
					"type" => "asaassubscriptions",
					"fields" => array(
							"asaas_api_key"      => JText::_("LNG_API_KEY"),
					)
				),
		);
		
		return $processorFieldsConfig;
	}

	/**
	 * Check if payment processor should be available for assignment
	 *
	 * @return bool
	 */
	public static function canAssignPaymentProcessor($restrict = false) {
		if (self::isAppInstalled(JBD_APP_SELL_OFFERS) || self::isAppInstalled(JBD_APP_APPOINTMENTS) || self::isAppInstalled(JBD_APP_EVENT_BOOKINGS)) {
			$appSettings = JBusinessUtil::getApplicationSettings();
			
			$defaultProcessors = JBusinessUtil::getDefaultPaymentProcessors($restrict);

			if(empty($defaultProcessors)){
				return false;
			}

			if ($appSettings->enable_offer_selling || $appSettings->enable_event_reservation || $appSettings->enable_services) {
				return true;
			} else {
				return false;
			}
			return $filtersHidden;
		}
	}
	
	/**
	 * Set the filter visibility status.
	 * Check if any option is selected and if yes keep the filter open by default.
	 *
	 * @param $state
	 * @return int 1 if the filter need to be hidden or 0 if it need to be shown
	 *
	 * @since 5.3.3
	 */
	public static function setFilterVisibility($state) {
		$filtersHidden = 1; // by default keep the filter hidden

		//if any state is set than make it visible
		if ($state->get('filter.company_id') != ''      ||  $state->get('filter.status_id') != ''   ||
			$state->get('filter.published') != ''       ||  $state->get('filter.state_id') != ''    ||
			$state->get('filter.type_id') != ''         ||  $state->get('filter.category_id') != '' ||
			$state->get('filter.type') != ''            ||  $state->get('filter.package_id') != ''  ||
			$state->get('filter.start_date') != ''      ||  $state->get('filter.end_date') != ''    ||
			$state->get('filter.item_type') != ''       ||  $state->get('filter.object_type') != '' ||
			$state->get('filter.service_id') != ''      ||  $state->get('filter.location_id') != '' ||
			$state->get('filter.conference_id') != ''   ||  $state->get('filter.level_id') != ''    ||
			$state->get('filter.event_id') != ''        ||  $state->get('filter.status') != ''      ||
			$state->get('filter.attribute_type') != ''  ||  $state->get('filter.payment_status_id') != ''
		) {
			$filtersHidden = 0;
		}

		return $filtersHidden;
	}
	
	/**
	 * Convert a string date range to 2 dates
	 *
	 * @param unknown $dateRange
	 * @return stdClass
	 */
	public static function processDateRange($dateRange) {
		$dates = explode(':', $dateRange);

		$result = new stdClass();
		$result->startDate = isset($dates[0])?self::convertToMysqlFormat($dates[0]):"";
		$result->endDate = isset($dates[1])?self::convertToMysqlFormat($dates[1]):"";
		
		return $result;
	}

	/**
	 * Check if user has access to item edit view on CP front end. If the user has no rights to access the item he will
	 * be redirected to list view containing that item.
	 *
	 * @param $itemId integer Id of the item trying to be accessed
	 * @param $itemUserId integer Owner of item trying to be accessed
	 * @param $companyId integer Company if related to the item trying to be accessed
	 * @param $view string list view that contains the item
	 * @throws Exception
	 * @since 5.4.0
	 */
	public static function checkEditAccessForItem($itemId, $itemUserId, $companyId, $view) {
		$user = JBusinessUtil::getUser();
		$companies = self::getCompaniesByUserId();
		$found = false;

		if ($itemUserId == $user->ID) {
			$found = true;
		} else {
			foreach ($companies as $company) {
				if ($company->userId == $user->ID && $companyId == $company->id) {
					$found = true;
				}
			}
		}


		//redirect if the user has no access and the event appointment is not new
		if (!$found && $itemId != 0) {
			$msg = JText::_("LNG_ACCESS_RESTRICTED");
			$app = JFactory::getApplication();
			$app->enqueueMessage($msg);
			$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$view, false));
		}
	}


	/**
	 * Retrieve the list of available services
	 *
	 * @return array available services
	 */
	public static function getAvailablePayableServices($front=false) {
		$instance = JBusinessUtil::getInstance();
		
		if (!isset($instance->paidServices)) {
			$paidServices  = array();
			
			JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
			$directoryAppsTable = JTable::getInstance("DirectoryApps", "JTable");
			$services           = $directoryAppsTable->getDirectoryApps();
			
			$payableServices = array(JBD_APP_APPOINTMENTS, JBD_APP_EVENT_BOOKINGS, JBD_APP_SELL_OFFERS, JBD_APP_CAMPAIGNS, JBD_PACKAGES);
			if($front){
				$payableServices = array(JBD_APP_APPOINTMENTS, JBD_APP_EVENT_BOOKINGS, JBD_APP_SELL_OFFERS);
			}

			foreach ($services as $service) {
				if (JBusinessUtil::isAppInstalled($service->id) && in_array($service->id, $payableServices)) {
					$paidServices[] = $service;
				}
			}
			
			if(!$front){
				$packageOption = new stdClass();
				$packageOption->id = JBD_PACKAGES;
				$packageOption->name = JText::_("LNG_PACKAGES");
				$paidServices[] = $packageOption;
			}

			$instance->paidServices = $paidServices;
		}
		
		return $instance->paidServices;
	}
	
	/**
	 * Retrieve the app ids and return the app names
	 *
	 * @param array $appIds
	 */
	public static function getSelectedServicesNames($appIds) {
		$result = array();
		$apps = self::getAvailablePayableServices();
		foreach ($appIds as $id) {
			foreach ($apps as $app) {
				if ($app->id == $id) {
					$result[] = $app->name;
				}
			}
		}
		
		return $result;
	}

	/**
	 * Return All Listing Types
	 *
	 * @return mixed
	 * @since 5.4.0
	 */
	public static function getListingTypes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id AS value, name AS name')
			->from('#__jbusinessdirectory_company_types')
			->order('ordering ASC');

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * check if JCH plugin option for try_catch is yes or no
	 * @throws Exception
	 */
	public static function checkJCHPlugin() {
		
	}

	/**
	 * Get All payment statuses options
	 *
	 * @return array containing the payment statuses
	 *
	 * @since version
	 */
	public static function getPaymentStatuses(){
		$statuses      = array();
		$status        = new stdClass();
		$status->value = PAYMENT_STATUS_PAID;
		$status->text  = JText::_("LNG_PAYMENT_STATUS_PAID");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = PAYMENT_STATUS_NOT_PAID;
		$status->text  = JText::_("LNG_PAYMENT_STATUS_NOT_PAID");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = PAYMENT_STATUS_FAILURE;
		$status->text  = JText::_("LNG_PAYMENT_STATUS_FAILURE");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = PAYMENT_STATUS_CANCELED;
		$status->text  = JText::_("LNG_PAYMENT_STATUS_CANCELED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = PAYMENT_STATUS_WAITING;
		$status->text  = JText::_("LNG_PAYMENT_STATUS_WAITING");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = PAYMENT_STATUS_PENDING;
		$status->text  = JText::_("LNG_PAYMENT_STATUS_PENDING");
		$statuses[]    = $status;

		return $statuses;
    }

	/**
	 * Get Offer Order states
	 *
	 * @return array
	 *
	 * @since 5.0.0
	 */
	public static function getOfferOrderStates() {
		$statuses      = array();
		$status        = new stdClass();
		$status->value = OFFER_ORDER_CREATED;
		$status->text  = JText::_("LNG_CREATED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = OFFER_ORDER_CONFIRMED;
		$status->text  = JText::_("LNG_CONFIRMED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = OFFER_ORDER_SHIPPED;
		$status->text  = JText::_("LNG_SHIPPED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = OFFER_ORDER_COMPLETED;
		$status->text  = JText::_("LNG_COMPLETED");
		$statuses[]    = $status;

		return $statuses;
	}

	/**
	 * Get Event Reservation States options
	 *
	 * @return array
	 *
	 * @since version
	 */
	public static function getEventReservationStates()
	{
		$statuses = array();
		$status = new stdClass();
		$status->value = EVENT_BOOKING_CREATED;
		$status->text = JTEXT::_("LNG_CREATED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = EVENT_BOOKING_CONFIRMED;
		$status->text = JTEXT::_("LNG_CONFIRMED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = EVENT_BOOKING_CANCELED;
		$status->text = JTEXT::_("LNG_CANCELED");
		$statuses[] = $status;

		return $statuses;
	}

	/**
	 * Get all company Service bookings state options
	 * @return array
	 *
	 * @since version
	 */
	public static function getCompanyServiceReservationStates() {
		$statuses      = array();
		$status        = new stdClass();
		$status->value = SERVICE_BOOKING_CREATED;
		$status->text  = JText::_("LNG_CREATED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = SERVICE_BOOKING_CONFIRMED;
		$status->text  = JText::_("LNG_CONFIRMED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = SERVICE_BOOKING_CANCELED;
		$status->text  = JText::_("LNG_CANCELED");
		$statuses[]    = $status;

		return $statuses;
	}

    /**
     * Get Company Service types
     *
     * @return array
     *
     * @since version
     */
    public static function getServiceTypes()
    {
        $statuses = array();
        $status = new stdClass();
        $status->value = SERVICE_TYPE_LIVE;
        $status->text = JTEXT::_("LNG_LIVE");
        $statuses[] = $status;
        $status = new stdClass();
        $status->value = SERVICE_TYPE_VIRTUAL;
        $status->text = JTEXT::_("LNG_VIRTUAL");
        $statuses[] = $status;
        $status = new stdClass();
        $status->value = SERVICE_TYPE_MIXED;
        $status->text = JTEXT::_("LNG_MIXED");
        $statuses[] = $status;

        return $statuses;
    }

	/**
	 * Generate the review statistics by stars
	 * @param unknown $reviews
	 * @return array[]|unknown
	 */
	public static function getReviewsStatistics($reviews){
		$reviewsStatistics = array('1'=>array(),'2'=>array(),'3'=>array(),'4'=>array(),'5'=>array());
		foreach ($reviews as $review){
			$reviewsStatistics[round($review->rating)][] = $review;
		}
		return $reviewsStatistics;
	}
	
	/**
	 * Calculate the average rating
	 * 
	 * @param unknown $reviews
	 * @return array[]|unknown
	 */
	public static function getReviewsAverageScore($reviews){
		if(empty($reviews)){
			return 0;
		}
		
		$score = 0;
		foreach($reviews as $review){
			$score += $review->rating;
		}
		
		$score = $score*1.0/count($reviews);
			
		return $score;
			
	}

    /**
     *
     * Get the total unread messages for a user
     *
     * @param $userId
     * @return mixed
     */
	public static function getTotalUserMessages($userId, $onlyUnread = false){
        JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
        $messagesTable = JTable::getInstance("Messages", "JTable");

        $companiesIds = self::getCompaniesByUserId($userId);
        $companiesIds = array_column($companiesIds, 'id');

        $result = $messagesTable->getTotalMessages($userId, $companiesIds, 0,$onlyUnread);

		if(empty($result)){
			return 0;
		}

        return $result;
    }


    /**
     * Get Event attendance modes
     *
     * @return array
     *
     * @since version
     */
    public static function getAttendanceModes()
    {
        $statuses = array();
        $status = new stdClass();
        $status->value = MODE_LIVE;
        $status->text = JTEXT::_("LNG_LIVE");
        $statuses[] = $status;
        $status = new stdClass();
        $status->value = MODE_VIRTUAL;
        $status->text = JTEXT::_("LNG_VIRTUAL");
        $statuses[] = $status;
        $status = new stdClass();
        $status->value = MODE_MIXED;
        $status->text = JTEXT::_("LNG_MIXED");
        $statuses[] = $status;

        return $statuses;
    }

    /**
     * Get attendance mode text
     *
     * @param $mode
     */
    public static function getAttendanceModeText($mode){
        $modes = self::getAttendanceModes();
        foreach($modes as $md){
            if($md->value == $mode){
                return $md->text;
            }
        }

        return "";
    }

    /**
     * Get month names
     *
     */
    public static function getMonthNames(){
        $monthNames = array();
        for ($i = 1; $i <= 12; $i++) {
            $monthNames[] = JBusinessUtil::monthToString($i, false);
        }

        return $monthNames;
    }

    /**
     * Get short month names
     *
     */
    public static function getShortMonthNames(){
        $monthNamesShort = array();
        for ($i = 1; $i <= 12; $i++) {
            $monthNamesShort[] = JBusinessUtil::monthToString($i, true) ;
        }

        return $monthNamesShort;
    }

    /**
     * Formats response as json and prints it
     *
     * @param $response object
     *
     * @since 5.3.0
     */
    public static function sendJsonResponse($data, $status, $message="") {
        $response          = new stdClass();
        $response->data    = $data;
        $response->status  = $status;
        $response->message = $message;

	    if (ob_get_length() > 0 ) {
			ob_end_clean();
		}

		//dump($response);
		//exit;

        // Send as JSON
        header("Content-Type: application/json", true);
        echo json_encode($response);
		//echo json_last_error_msg();
        exit;
	}
	
	/**
     * Include jQuery UI based on Joomla version
     *
     */
	public static function loadJQueryUI () {	

		if (self::isJoomla3()) {			

			JHtml::_('jquery.ui', array('core', 'sortable'));
			if (!defined('J_JQUERY_UI_LOADED')) {
				JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
				JBusinessUtil::enqueueScript('libraries/jquery/jquery-ui.js');
			
				define('J_JQUERY_UI_LOADED', 1);
			}
		} else {
			JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');			
			JBusinessUtil::enqueueScript('libraries/jquery/jquery-ui.js');
		}

	}

	/**
     * Check if there is no other appointment booked at the same time for an user
     *
	 * @param $userId int id of user creating the booking
	 * @param $date string date of appointment
	 * @param $time string time of appointment
     */
	public static function checkAppointmentAvailability($userId, $date, $time, $serviceId, $providerId) {		
		$appointmentTable = JTable::getInstance("companyservicebookings", "JTable");
		$result = $appointmentTable->checkAvailability($userId, $date, $time, $serviceId, $providerId);
		
		return $result;
	}
    /* Fetches and returns all countries
     *
	 * @return array
     *
     * @since 5.5.0
	 */
	public static function getCountries() {
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$countriesTable = JTable::getInstance("Country", "JTable");
		$countries = $countriesTable->getCountries();

		return $countries;
	}
	
	/* Initialize chosen on Joomla3
     *
	 */
	public static function initializeChosen () {		
		if(self::isJoomla3()){
			JHtml::_('formbehavior.chosen', 'select');
		}		
    }

	/**
	 * Load the jquery chosen library
	 */
	public static function loadJQueryChosen() {
		JBusinessUtil::enqueueScript('libraries/chosen/chosen.jquery.min.js');
		JBusinessUtil::enqueueStyle('libraries/chosen/chosen.css');
	}
    /**
     *
     * Get the total unread quote requests for a user related companies
     *
     * @param $userId
     * @return mixed
     */
    public static function getUnreadUserQuotes($userId){
       JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
       $quoteTable = JTable::getInstance("requestquote", "JTable");

       $companyIds = self::getCompaniesByUserId($userId, true);
       
	   if(!empty($companyIds)){
			$companyIds = implode(',', $companyIds);
       		$result = $quoteTable->getTotalQuoteRequests($companyIds, true);
			return $result;
	   }

	   return null;
    }

	/**
     * Return packages prices with VAT included
     *
	 * @param $packages
     * @since 5.5.0
	 */
    public static function packagesPriceVat($packages){
        $appSettings = JBusinessUtil::getApplicationSettings();
        if($appSettings->packages_vat_apply) {
            foreach($packages as &$package) {
                $package->price += TaxService::getVatAmount($package->price);
            }
        }
    }

	/**
     * Retuns the HTML for the RSS Modal
     *
	 * @param $categoryOptions array of categories
	 *
	 * @return string
	 * @throws Exception
     *
     * @since 5.5.0
	 */
    public static function getRssModal($categoryOptions) {
        return '<div class="jbd-container" id="rss-model" style="display: none">
                <form action="'.JRoute::_("index.php?option=com_jbusinessdirectory&view=catalog").'" method="get" name="rssForm" id="rssForm">
                    <div class="jmodal-sm">
                        <div class="jmodal-header">
                            <p class="jmodal-header-title">'.JText::_("LNG_COMPANIES_RSS").'</p>
                            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                        </div>
                        <div class="jmodal-body">'.
                            // '<p>'.JText::_("LNG_CATEGORY_RSS_TEXT").'</p>
                            // <div class="row">
                            //     <div class="col-12">
                            //         <div class="jinput-outline jinput-hover">
                            //             <select name="category" id="category" class="form-control chosen-select">
                            //                 <option value="0">'.JText::_("LNG_ALL_CATEGORIES").'</option>
                            //                 '.JHtml::_("select.options", $categoryOptions, "value", "text", null).'
                            //             </select>
                            //         </div>
                            //     </div>
                            // </div>'.
                            '<input type="hidden" name="option"	value="'.JBusinessUtil::getComponentName().'" />
                            <input type="hidden" name="task" id="task" value="directoryrss.getCompaniesRss" />
                            '.JHTML::_( "form.token" ).'
                        </div>
                        <div class="jmodal-footer">
                            <div class="btn-group" role="group" aria-label="">
                                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()">'.JText::_("LNG_CANCEL").'</button>
                                <button type="submit" class="jmodal-btn">'.JText::_("LNG_GENERATE").'</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>';
    }

    /**
    *
	* @param $string
    * @return string Function to check and format the content to UTF-8 Encoding
    */
	public static function make_safe_for_utf8_use($string) {
		$encoding = mb_detect_encoding($string, "UTF-8,WINDOWS-1252");

		if ($encoding != 'UTF-8') {
			return iconv($encoding, 'UTF-8//TRANSLIT', $string);
		} else {
			return $string;
		}
	}

	/**
	 * Retrieve the unique sessios days
	 */
	public static function getSessionsDays($sessions){
		$result = array();

		if(empty($sessions)){
			return $result;
		}

		foreach($sessions as $session){
			if(!in_array($session->date, $result)){
				$result[]=$session->date;
			}
		}

		return $result;
	}

	public static function array_search_recursive($needle, $haystack) {
		foreach ($haystack as $value) {
			if (is_array($value) && self::array_search_recursive($needle, $value)) {
				return true;
			}
			else if ($value == $needle) {
				return true;
			}
		}
		return false;
	}

	public static function check_time_overlap($start_time1, $end_time1, $start_time2, $end_time2) {
		if ($start_time1 <= $end_time2 && $start_time2 < $end_time1) {
			return true;
		}
		return false;
	}

	public static function getOverlappingSessions($session) {

		$user = JBusinessUtil::getUser();
		$registersTable = JTable::getInstance("SessionRegister", "JTable");
		$userRegisteredSessions = $registersTable->getUserSessionRegistrations($user->ID);

		$overlaps = array();
		if(!empty($userRegisteredSessions)) {
			foreach($userRegisteredSessions as $registration) {
				if($registration->id != $session->id && $registration->date == $session->date) {
					$isOverlap = self::check_time_overlap($session->start_time, $session->end_time, $registration->start_time, $registration->end_time);
					if ($isOverlap) {
						$overlaps[] = $registration->id;
					}
				}
			}
		}
		return $overlaps;
	}


	/**
	 * Adds the possibility to sort items using drag&drop in the list views
	 *
	 *
	 */
	public static function addSorting($saveOrder=null, $listDirn=null) {

		$jinput = JFactory::getApplication()->input;
		$task = $jinput->get( 'view' );

		$saveOrderingUrl="";

		$listType = 'itemList';
		if($task == 'categories') {
			$listType = 'categoryList';
		}

		if ($saveOrder) {
			if (defined('JVERSION')) {
				$joomlaVersion = (int)JVERSION;
			} else {
				$j = new JVersion();
				$joomlaVersion = (int)$j->getShortVersion();
			}
			$saveOrderingUrl = 'index.php?option=com_jbusinessdirectory&task='.$task.'.saveOrderAjax&tmpl=component&' . JSession::getFormToken() . '=1';
			if ($joomlaVersion == 3) {
				JHtml::_('sortablelist.sortable', $listType , 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			} else {
				HTMLHelper::_('draggablelist.draggable');
			}
		}

		return $saveOrderingUrl;
	}

	public static function convertHexToRGB($color, $opacity=null, $pdf = false){
		list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
		if(!empty($opacity)){
			$result = "rgba($r,$g,$b,$opacity)";
		} elseif ($pdf) {
			$result = array($r,$g,$b);
		}else{
			$result = "rgba($r,$g,$b)";
		}

		return $result;
	}


	/**
	 * Generate the inline scripts for the plugin
	 * 
	 * 
	 * @return string
	 */
	public static function generateScriptDeclarations() {
		// Generate script declarations
		$defaultJsMimes         = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');
		$buffer = "";

		$document = JFactory::getDocument();
		$lnEnd        = $document->_getLineEnd();
		$tab          = $document->_getTab();
		$tagEnd       = ' />';


		$data = $document->getHeadData();
		$data = $document->mergeHeadData($data);

		// Generate scripts options
		$scriptOptions = $document->getScriptOptions();

		if (!empty($scriptOptions)) {
			$buffer .= '<script type="application/json" class="script-options new">';

			$prettyPrint = (JDEBUG && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false);
			$jsonOptions = json_encode($scriptOptions, $prettyPrint);
			$jsonOptions = $jsonOptions ? $jsonOptions : '{}';

			$buffer .= $jsonOptions;
			$buffer .= '</script>' . $lnEnd;
		}

		foreach ($data->_script as $type => $content) {
			$buffer .= '<script';

			if (!is_null($type) && in_array($type, $defaultJsMimes)) {
				$buffer .= ' type="' . $type . '"';
			}

			$buffer .= '>' . $lnEnd;
			$buffer .= $content . $lnEnd;
			$buffer .= '</script>' . $lnEnd;
		}

		return $buffer;
	}

	/**
	 * Retrieves remote file contents using WP functions
	 *
	 * @param $url string
	 *
	 * @return mixed
	 */
	public static function getRemoteFileContents($url) {
		$data = wp_remote_retrieve_body(JBusinessUtil::getURLData($url));

		return $data;
	}

	public static function renderCategoryIcon ($icon=null, $image=null, $attributes=null) {
		$appSettings = self::getApplicationSettings();

		$result = '';
		if (!empty($image) ) {
			$result = '<img src="'.BD_PICTURES_PATH.$image.'" alt="icon-'.$image.'" class="category-icon-image" >';
		} else {
			$result = '<i class="la la-'.$icon.' '.$attributes.'"></i>';
		}

		return $result;
	}

	/**
     *Returns the url for an article
     *@param $articleId id of the article
     *
     */
	public static function getArticleUrl($articleId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id, a.alias, a.catid, a.language');
		$query->from('#__content AS a')
			->where('a.id = ' . (int) $articleId);


		$db->setQuery($query);
		$data = $db->loadObject();

		$url="";
		if(!empty($data)) {
			//JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');
			//$url = JRoute::_(ContentHelperRoute::getArticleRoute($data->id, $data->catid));
			$url = JRoute::_('index.php?option=com_content&view=article&id=' . $data->id, false);

			return $url;
		}

	}

	/**
     *Returns the url for the terms and conditions article
     *You specify the id of article being retrieved in general settings
     *
     */
	public static function getTermsAndConditions ($type = 'general') {
		$instance = JBusinessUtil::getInstance();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$lang = '';

		if($appSettings->enable_multilingual) {
			$lang = '_'.self::getLanguageTag();
		}

		if ($type=='contact') {
			if (!isset($instance->contactTermsConditionsUrl)) {
				$articleId = (int)$appSettings->{"contact_terms_conditions_article_id$lang"};
				$instance->contactTermsConditionsUrl = self::getArticleUrl($articleId);
			}
			return $instance->contactTermsConditionsUrl;
		}elseif ($type=="reviews") {
			if (!isset($instance->reviewTermsConditionsUrl)) {
				$articleId = (int)$appSettings->{"reviews_terms_conditions_article_id$lang"};
			    $instance->reviewTermsConditionsUrl = self::getArticleUrl($articleId);
		   }
		   return $instance->reviewTermsConditionsUrl;
		}elseif ($type=="privacy") {
			if (!isset($instance->privacyPolicyUrl)) {
				$articleId = $appSettings->{"privacy_policy_article_id$lang"};
			    $instance->privacyPolicyUrl = self::getArticleUrl($articleId);
		   }
		   return $instance->privacyPolicyUrl;
		} else {
			if(!isset($instance->termsConditionsUrl)) {
				$articleId = (int)$appSettings->{"terms_conditions_article_id$lang"};
				$instance->termsConditionsUrl = self::getArticleUrl($articleId);
			}
			return $instance->termsConditionsUrl;
		}
	}

	/**
     *Returns the url to view the terms and conditions
     *If setting is enabled in general settings you view terms in article
     *otherwise they are displayed in termsconditions view
     */
	public static function getTermsUrl ($type = 'general') {
		$appSettings = JBusinessUtil::getApplicationSettings();

		if($appSettings->show_terms_conditions_article == 1) {
			$termsConditionsUrl = self::getTermsAndConditions($type);
		} else {
			$termsConditionsUrl = JRoute::_('index.php?option=com_jbusinessdirectory&view=termsconditions&type='.$type);
		}

		return $termsConditionsUrl;
	}

	/**
     *Renders the Terms and Conditions and Privacy policy html
     *@param $type  Type of the Terms and Conditions to retrieve
     *
     */
	public static function renderTermsAndConditions($type = 'general') {

		$appSettings = JBusinessUtil::getApplicationSettings();

		if($type=='reviews') {
			$termsText = JText::_('LNG_REVIEW_TERMS_AGREAMENT');
		} elseif($type=='contact') {
			$termsText = JText::_('LNG_CONTACT_TERMS_AGREAMENT');
		} else {
			$termsText = JText::_('LNG_TERMS_AGREAMENT');
		}

		$termsUrl = '<a target="_blank" href="'.JBusinessUtil::getTermsUrl($type).'" >'.JText::_('LNG_TERMS_AND_CONDITIONS').'</a>';
		$termsAgreement = str_replace("{terms_link}", $termsUrl, $termsText);
		$privacyUrl = '<a target="_blank" href="'.JBusinessUtil::getTermsUrl('privacy').'" >'.JText::_('LNG_PRIVACY_POLICY').'</a>';
		$privacyAgreement = str_replace("{privacy_link}", $privacyUrl, JText::_('LNG_PRIVACY_AGREEMENT'));

		$html = '<div id="term_conditions" class="jbd-terms-conditions">';
		$html .= $termsAgreement;
		if($appSettings->show_privacy == 1) {
			$html .= $privacyAgreement .' </p>';
		} else {
			$html .= '';
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render listing process steps
	 *
	 * @return void
	 */
	public static function renderProcessSteps($step){
		ob_start();
		?>
			<div class="jbd-container">
				<div id="process-container" class="process-container">
					<ol class="process-steps">
						<li class="inbox <?php echo $step == 1?"is-active":""?> <?php echo $step > 1?"is-complete":""?>" data-step="1">
							<p><?php echo JText::_("LNG_CHOOSE_PACKAGE")?></p>
						</li>
						<li class="user <?php echo $step == 2?"is-active":""?> <?php echo $step > 2?"is-complete":""?>" data-step="2">
							<p><?php echo JText::_("LNG_BASIC_INFO")?></p>
						</li>
						<li class="file-text <?php echo $step == 3?"is-active":""?> <?php echo $step > 3?"is-complete":""?>" data-step="3">
							<p><?php echo JFactory::getApplication()->input->get("packageType") == PACKAGE_TYPE_USER ? JText::_("LNG_ORDER_INFO") : JText::_("LNG_LISTING_INFO") ?></p>
						</li>
						<li class="payment <?php echo $step == 4?"is-active":""?> <?php echo $step > 4?"is-complete":""?>" data-step="4">
							<p><?php echo JText::_("LNG_PAYMENT")?></p>
						</li>
					</ol>
				</div>
			</div>
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Resize an image
	 *
	 */
	public static function resizeImage($imagePath, $maxWidth=500, $maxHeight=500){
		require_once BD_HELPERS_PATH.'/class.resizeImage.php';

		$image = new Resize_Image;
		$image->ratio = true;

		if (!empty($maxWidth)) {
			$image->new_width = $maxWidth;
		}else if(!empty($maxHeight)) {
			$image->new_height = $maxHeight;
		}

		$file_tmp      = JBusinessUtil::makePathFile($imagePath);

		$image->image_to_resize = $file_tmp;    // Full Path to the file

		$image->new_image_name = basename($file_tmp);
		$image->save_folder    = dirname($file_tmp) . DIRECTORY_SEPARATOR;

		$process = $image->resize();
		if ($process['result'] && $image->save_folder) {
			return true;
		}

		return false;
	}

	/**
	 * Get the thumnail file name from the original file
	 *
	 */
	public static function getThumbnailImage($image){
		$extension = pathinfo($image, PATHINFO_EXTENSION);
		$baseName = basename($image, ".{$extension}");
		$thumbImage =str_replace($baseName,"{$baseName}_thumb",$image);

		return $thumbImage;
	}

	public static function getLoginUrl($redirect = null, $amp=true) {

		if(!isset($redirect)) {
			$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
			$redirect = $base_url . $_SERVER["REQUEST_URI"];
		}

		$redirect = urlencode(JRoute::_($redirect));
		$result = WP_BUSINESS_DIRECTORY_BASE_URL.'/wp-login.php?redirect_to='.$redirect;

		return $result;
	}

	/**
	 * Retrieve the fist listing of the user when there are no orders generated
	 * This is used for showing an upgrade message on the front-end CP
	 *
	 */
	public static function getUpgradeListingID($userId){
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orders = $orderTable->getOrders($userId);

		if(empty($orders)){
			$listings = self::getCompaniesOptions(-1,$userId);
			if(!empty($listings)){
				return $listings[0]->id;
			}
		}

		return null;
	}

	public static function formatToDefaultDate($dateString) {
		$app = JBusinessUtil::getApplicationSettings();
		
		$dateFormat = $app->dateFormat;
		$formattedAvailableDates = '';
		if (isset($dateString) && !empty($dateString)) {
			$dates = explode(",", $dateString);
			$result = array();
			foreach ($dates as $date) {
				if ($date=="NaN-NaN-NaN") {
					continue;
				}
				$dateObj = new DateTime($date);
				$formatedDate = $dateObj->format($dateFormat);
				$result[] = $formatedDate;
			}
		
			$formattedAvailableDates = implode(",", $result);
		}
		
		return $formattedAvailableDates;
	}

	/**
	 * render user selection
	 *
	 * @return rendered content
	 */
	public static function renderUserSelection($userId, $vName){
		
		if(!JBusinessUtil::isJoomla3()){

			HTMLHelper::_('bootstrap.modal');
			Factory::getDocument()->getWebAssetManager()->useScript('webcomponent.field-media');
			Factory::getDocument()->getWebAssetManager()->useScript('webcomponent.field-user');
		}

		$companyOwner = JBusinessUtil::getUser($userId); 
		?>
			<label for="userId" class="w-100"><?php echo JText::_('LNG_USERID')?></label>
			<div class="input-group flex-nowrap">
				<input
					type="text" id="jform_created_by"
					value="<?php echo !empty($companyOwner)?$companyOwner->display_name:"" ?>"
					placeholder="Select a User."
					readonly
					class="field-user-input-name form-control"/>
				<div class="input-group-append">
					<a class="btn btn-primary button-select" title="Select User" onclick="jQuery('#user-model').jbdModal()"><span class="icon-user"></span></a>
				</div>
			</div>
			<input type="hidden" id="jform_created_by_id" name="userId" value="<?php echo $userId ?>"  class="field-user-input " data-onchange=""/>
			<div class="jbd-container jbd-edit-container" id="user-model" style="display: none">
				<div class="jmodal-sm">
					<div class="jmodal-header">
						<p class="jmodal-header-title"><?php echo JText::_('LNG_SELECT_USER') ?></p>
						<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
					</div>
					<div class="jmodal-body">
						<?php $users = get_users();?>
						<div class="mb-2">
							<a class="btn btn-primary"  href="javascript:selectUser('','')" /><?php echo JText::_("LNG_NO_USER")?></a>
						</div>
						<table class="wp-list-table widefat striped">
							<thead>
								<tr>
									<th class="nowrap">Name</th>
									<th class="nowrap">Username</th>
									<th class="nowrap">User Groups</th>
									<th class="nowrap">ID</th>
								</tr>
							</thead>
							<tbody>
								<?php 		
									usort($users, function($a, $b) {
										return strnatcasecmp($a->display_name, $b->display_name);
									});
								?>
								<?php foreach($users as $usr){?>
									<tr class="row0">
										<td>
											<a class="" href="javascript:selectUser('<?php echo $usr->display_name ?>',<?php echo $usr->ID ?>)"><?php echo $usr->display_name?></a>
										</td>
										<td>
											<?php echo $usr->user_login?>						
										</td>
										<td>
											<?php $user_meta=get_userdata($usr->ID);
												if(!empty($user_meta->roles)){
													echo implode(",",$user_meta->roles);
												}
											?>				
										</td>
										<td>
											<?php echo $usr->ID?>
										</td>
									</tr>
								<?php } ?>						
							</tbody>
						</table>
					</div>
					<div class="jmodal-footer">
						<div class="btn-group" role="group" aria-label="">
							<button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	/**
     * Check if there is no other trip booked at the same time for an user
     *
	 * @param $userId int id of user creating the booking
	 * @param $date string date of appointment
	 * @param $time string time of appointment
     */
	public static function checkTripBookingAvailability ($userId, $date, $orderId) {		
		$bookingsTable = JTable::getInstance("TripBookings", "JTable");
		$result = $bookingsTable->checkAvailability($userId, $date, $orderId);
		
		return $result;
	}

	public static function checkUserBookingCapability($userId = null) {
		
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');
		$packagesTable = JTable::getInstance("Package", "JTable");
		$bookingsTable = JTable::getInstance("TripBookings", "JTable");

		if(!isset($userId)) {
			$userId = JBusinessUtil::getUser()->ID;
		}

		$packages = $packagesTable->getUserActivePackages($userId);

		$tripsCount = 0;
		$startDates = array();		
		$endDates = array();		
		foreach($packages as $package) {
			if(in_array(TRIPS, $package->features)) {
				$tripsCount += $package->max_trips;
				$startDates[] =  $package->start_date;
				$endDates[] =  $package->end_date;
			}
		}

		usort($startDates, function($a, $b) {
			return strtotime($a) > strtotime($b);
		});
		usort($endDates, function($a, $b) {
			return strtotime($a) < strtotime($b);
		});
		
		$bookedTripsCount = 0;
		if(!empty($startDates) && !empty($endDates)) {
			$startDate = $startDates[0];
			$endDate = $endDates[0];
			$bookedTripsCount = $bookingsTable->getTotalBookedTrips($userId,$startDate, $endDate);
		}


		if($bookedTripsCount >= $tripsCount) {
			return false;
		}
		
		return true;
	}

	/**
	 * Get Trip Booking states
	 *
	 * @return array
	 *
	 * @since 5.7.0
	 */
	public static function getTripBookingStates() {
		$statuses      = array();
		$status        = new stdClass();
		$status->value = TRIP_BOOKING_CREATED;
		$status->text  = JText::_("LNG_CREATED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = TRIP_BOOKING_CONFIRMED;
		$status->text  = JText::_("LNG_CONFIRMED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = TRIP_BOOKING_CANCELED;
		$status->text  = JText::_("LNG_CANCELED");
		$statuses[]    = $status;

		return $statuses;
	}

	public static function formatHyperlinks($content){
         preg_match_all('/<a ((?!target)[^>])+?>/', $content, $href_matches); 

        foreach ($href_matches[0] as $key => $value) {
        	$orig_link = $value; 
        	if (!preg_match('/target="_blank"/',$orig_link)){
        		$new_link = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $orig_link); 
        		$content = str_replace($orig_link, $new_link, $content);
        	}
        }
        return $content;
    }

	public static function getForActionURL($country, $region, $city, $category, $useCurrent = false){
		$urlParmas = "";

		if($useCurrent){
			$url = "/";
		}

		if(!empty($country)){
			$urlParmas .= "&countrySearch=$country";
		}

		if(!empty($region)){
			$urlParmas .= "&regionSearch=$region";
		}

		if(!empty($city)){
			$urlParmas .= "&citySearch=$city";
		}

		if(!empty($category)){
			$urlParmas .= "&categoryId=$category";
		}

		//retrieve the current menu item id
		$menuItemId = JBusinessUtil::getActiveMenuItem();

		$url = JRoute::_("index.php?option=com_jbusinessdirectory&view=search".$urlParmas.$menuItemId);

		return $url;
	}

	/**
	 * Logs an action in the database
	 */
	public static function logAction($itemId, $itemType, $action, $userId){

		$db =JFactory::getDBO();
		$query = "insert into  #__jbusinessdirectory_logs (item_id, item_type, action, user_id) values 
					($itemId, $itemType, $action, $userId)";
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Logs an action in the database
	 */
	public static function getItemLogs($itemId, $itemType){
		
		$db =JFactory::getDBO();
		$query = "select l.*, u.display_name from #__jbusinessdirectory_logs l
				  left join #__users u on l.user_id = u.id
		 		  where item_id = $itemId and item_type=$itemType
				  order by l.date desc";
		
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Render the action log badge
	 */
	public static function renderLogAction($action){
		switch($action) {
			case ITEM_CREATED:
				echo '<div class="status-badge badge-primary">'.JText::_("LNG_CREATED").'</div>';
				break;
			case ITEM_UPDATED:
				echo '<div class="status-badge badge-success">'.JText::_("LNG_UPDATED").'</div>';
				break;
			case ITEM_DELETED:
				echo '<div class="status-badge badge-danger">'.JText::_("LNG_DELETED").'</div>';
				break;
		}
	}


	public static function getAcuityCalendars(){
		
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://acuityscheduling.com/api/v1/calendars",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => [
			"accept: application/json",
			"authorization: Basic MjgwNjE3OTU6ODNkZWYzMjM5MGM0ZDQ3MzkyZmJmZjVlZjI3MGVjMWM="
		  ],
		]);
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		$result = array();
		if (!$err) {
			$calendars = json_decode($response);
			if(!empty($calendars)){
				foreach($calendars as $calendar){
					$result[$calendar->id] = $calendar;
				}
			}
		}
		
		return $result;
	}

	public static function getAcuityAppointmentTypes(){

		$curl = curl_init();

		curl_setopt_array($curl, [
		CURLOPT_URL => "https://acuityscheduling.com/api/v1/appointment-types?includeDeleted=false",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"accept: application/json",
			"authorization: Basic MjgwNjE3OTU6ODNkZWYzMjM5MGM0ZDQ3MzkyZmJmZjVlZjI3MGVjMWM="
		],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);

		$result = array();
		if (!$err) {
			$result = json_decode($response);
		}else{
			dump($err);
		}

		return $result;
	}

	public static function getAcuityAvailableDates($caledarId, $appointmentTypeId, $month){

		$curl = curl_init();

		curl_setopt_array($curl, [
		CURLOPT_URL => "https://acuityscheduling.com/api/v1/availability/dates?month=$month&appointmentTypeID=$appointmentTypeId&calendarID=$caledarId",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"accept: application/json",
			"authorization: Basic MjgwNjE3OTU6ODNkZWYzMjM5MGM0ZDQ3MzkyZmJmZjVlZjI3MGVjMWM="
		],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		$result = null;
		if (!$err) {
			$result = json_decode($response);
		}

		return $result;
	}

	public static function getAvailableMembers($month){

		//get available appointmentTypes
		$appointmentTypes = self::getAcuityAppointmentTypes();
		
		$calendars = self::getAcuityCalendars();
		$members = array();

		if(!empty($appointmentTypes)){
			foreach($appointmentTypes as $type){
				$calendarIds = $type->calendarIDs;
				//dump($type->name);
				foreach($calendarIds as $calendarId){
					$availableDates = self::getAcuityAvailableDates($calendarId,$type->id,$month);
					$calendar = $calendars[$calendarId];
					//dump("available dates for $calendar->name");
					//dump($availableDates);
					if(!empty($availableDates)){
						$members[] = $calendar->name;
					}
				}
			}
		}

		return $members;
	}

	public static function updateItemsFeaturedStatus($items){

		if(empty($items)){
			return;
		}

		$items = array_filter($items);
		$names = implode("','", $items);
		
		$db	= JFactory::getDBO();

		$query = "update #__jbusinessdirectory_companies set featured =0 where id > 0"; 
		//dump($query);
		$db->setQuery($query);
		
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		} 

		$query = "update #__jbusinessdirectory_companies set featured =1 where name in ('$names')"; 
		//dump($query);
		$db->setQuery($query);
		
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		} 

		return true;
	}

	/**
	 * Get the mobile app general settings from the database
	 *
	 * @return stdClass app settings
	 */
	private static function getMobileAppConfig() {
		$db		= JFactory::getDBO();
		$query	= "	SELECT *
	                  FROM #__jbusinessdirectory_mobile_app_config";

		//dump($query);
		$db->setQuery($query);
		$mobileAppSettings =  $db->loadObjectList();
		
		$app = new stdClass();
		foreach ($mobileAppSettings as $setting) {
			$app->{$setting->name} = $setting->value;
		}

		return $app;
	}

	/**
	 * Retrieve the application setting instance
	 *
	 * @return stdClass application settings
	 */
	public static function getMobileAppSettings() {
		$instance = JBusinessUtil::getInstance();

		if (!isset($instance->mobileAppConfig)) {
			$instance->mobileAppConfig = self::getMobileAppConfig();
		}
		return $instance->mobileAppConfig;
	}

	public static function endsWith($haystack, $needle ){
		$len = strlen($needle);
		if ($len == 0) {
			return true;
		}
		return (substr($haystack, -$len) === $needle);
	}

	/**
	 *  Count all users on system
	 */
	public static function countAllUsers() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__users'));
		$db->setQuery($query);
		$count = $db->loadResult();
	
		return $count;
	}

	public static function isUserBlocked($self, $userId) {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__jbusinessdirectory_blocked_users'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($self->id))
			->where($db->quoteName('blocked_id') . ' = ' . $db->quote($userId));
		
		$db->setQuery($query);
		$result = $db->loadResult() > 0;

		return $result;
	}

	public static function isUserBlocker($self, $userId) {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__jbusinessdirectory_blocked_users'))
			->where($db->quoteName('blocked_id') . ' = ' . $db->quote($self->id))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($userId));
		
		$db->setQuery($query);
		$result = $db->loadResult() > 0;

		return $result;
	}

}

/**
 * Utility function for displaying debug messages
 *
 */
if (!function_exists('dump')) {
	function dump() {
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';
		//exit;
	}
}

/**
 * Utility function for displaying debug messages
 *
 */
if (!function_exists('dbg')) {
	function dbg($text) {
		echo "<pre>";
		var_dump($text);
		echo "</pre>";
	}
}

if (!function_exists('dd')) {
	function dd() {
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';
		exit;
	}
}
