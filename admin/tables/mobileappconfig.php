<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableMobileAppConfig extends JTable {
	public $app_id		= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_mobile_app_config', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function getMobileAppConfig() {
		$db    = JFactory::getDbo();
		$query = "SELECT * FROM #__jbusinessdirectory_mobile_app_config";
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	public function updateMobileAppConfig($data) {
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_mobile_app_config(name, value) values ";
		foreach ($data as $key => $item) {
			//this are part of fields that are included during import of other files like it is uploader.php that contains forms for uploads.
			if (in_array($key, array('picture_info','picture_enable','picture_path','uploadfile','option','task'))) {
				continue;
			}
			
			if (is_numeric($key[0])) {
				continue;
			}
			
			if (is_array($item)) {
				$item = implode(',', $item);
			}

			$item = $db->escape($item);
			$query = $query."('".$key."','".$item."'),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE value=values(value) ";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}


	public function resetConfigurations() {
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__jbusinessdirectory_mobile_app_config` (`name`, `value`) 
		VALUES 
			('androidOrderId', ''),
			('androidOrderEmail', ''),
			('iosOrderId', ''),
			('iosOrderEmail', ''),
			('baseUrl', ''),
			('user_email', ''),
			('mapsApiKey', ''),
			('firebase_server_key', ''),
			('primaryColor', '#ff7142'),
			('backgroundColor', '#ffffff'),
			('textPrimary', '#212e3e'),
			('genericText', '#0a713b'),
			('iconColor', '#666666'),
			('isLocationMandatory', '0'),
			('showLatestListings', '0'),
			('showFeaturedListings', '0'),
			('showFeaturedOffers', '0'),
			('showFeaturedEvents', '0'),
			('showOffers', '0'),
			('showEvents', '0'),
			('enableReviews', '0'),
			('showMessages', '0'),
			('isJoomla', '0'),
			('mobile_only_featured_listings', '0'),
			('mobile_only_featured_offers', '0'),
			('mobile_only_featured_events', '0'),
			('mobile_list_limit', '5'),
			('mobile_business_img', ''),
			('mobile_offer_img', ''),
			('mobile_event_img', ''),
			('app_id', ''),
			('client_configs', 'client_configs.xml'),
			('language_keys', ''),
			('last_updated', '1679428992'),
			('mobile_company_categories_filter', ''),
			('mobile_offer_categories_filter', ''),
			('mobile_event_categories_filter', ''),
			('logo_ios_nb', 'jbd-mobile-logo.png'),
			('logo_android_nb', 'jbd-mobile-logo.png')
		ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";

		$db->setQuery($query);
		$result = $db->execute();
	
		return $result;
	}
	
}
