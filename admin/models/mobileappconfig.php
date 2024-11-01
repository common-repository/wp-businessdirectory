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

class JBusinessDirectoryModelMobileAppConfig extends JModelAdmin {
	public static $appConfig;

	public function __construct() {
		parent::__construct();
		$id = JFactory::getApplication()->input->get('app_id', 0);
		$this->setId((int) $id);
	}

	public function setId($app_id) {
		// Set id and wipe data
		$this->_app_id = $app_id;
		$this->_data                   = null;
	}

	public function getForm($data = array(), $loadData = true) {
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
	public function getTable($type = 'MobileAppConfig', $prefix = 'Table', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get MobileAppConfig
	 * @return object with data
	 */
	public function &getData() {
		$appTable    = $this->getTable('MobileAppConfig');
		$appConfig = $appTable->getMobileAppConfig();
		$this->_data = new stdClass();
		foreach ($appConfig as $key => $value) {
			$this->_data->{$value->name} = $value->value;
		}

		if (!$this->_data) {
			$this->_data                            = new stdClass();
			$this->_data->app_id    = null;
		}

		if (!empty($this->_data->language_keys)) {
			$this->_data->language_keys = explode(",", $this->_data->language_keys);
		}

		if (!empty($this->_data->mobile_company_categories_filter)) {
			$this->_data->mobile_company_categories_filter = explode(",", $this->_data->mobile_company_categories_filter);
		}

		if (!empty($this->_data->mobile_offer_categories_filter)) {
			$this->_data->mobile_offer_categories_filter = explode(",", $this->_data->mobile_offer_categories_filter);
		}

		if (!empty($this->_data->mobile_event_categories_filter)) {
			$this->_data->mobile_event_categories_filter = explode(",", $this->_data->mobile_event_categories_filter);
		}

		$menusTable = $this->getTable('MobileAppMenus');
		$this->_data->menus = $menusTable->getMobileAppMenus();

		return $this->_data;
	}

	public function store($data) {
		$row        = $this->getTable('MobileAppConfig');
		
		if (empty($data["language_keys"])) {
			$data["language_keys"] = "";
		} else {
			$data["language_keys"] = implode(",", $data["language_keys"]);
		}

		if(empty($data["last_updated"]))	 {
			$data["last_updated"] = $_SERVER['REQUEST_TIME'];
		}

		if (empty($data["mobile_company_categories_filter"])) {
			$data["mobile_company_categories_filter"] = "";
		} else {
			$data["mobile_company_categories_filter"] = implode(",", $data["mobile_company_categories_filter"]);
		}

		if (empty($data["mobile_offer_categories_filter"])) {
			$data["mobile_offer_categories_filter"] = "";
		} else {
			$data["mobile_offer_categories_filter"] = implode(",", $data["mobile_offer_categories_filter"]);
		}

		if (empty($data["mobile_event_categories_filter"])) {
			$data["mobile_event_categories_filter"] = "";
		} else {
			$data["mobile_event_categories_filter"] = implode(",", $data["mobile_event_categories_filter"]);
		}

		$row->updateMobileAppConfig($data);
		$this->saveMobileAppMenus($data);

		return true;
	}


	public function resetConfigurations() {

		$table = $this->getTable();
		$result = $table->resetConfigurations();

		return $result;
	}

	/**
	 * Retrieves the zip codes and lat/long data and their company ID, and makes the appropriate
	 * changes in the database
	 * @param $data
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function saveMobileAppMenus($data) {
		
		$menuTitle = isset($data["title"])?$data["title"]:array();
		$menuUrl = isset($data["urls"])?$data["urls"]:array();
		$menuIcon = isset($data["icons"])?$data["icons"]:array();
		$menuGroup = isset($data["groups"])?$data["groups"]:array();
		$menuPosition = isset($data["positions"])?$data["positions"]:array();
		$menuType = isset($data["types"])?$data["types"]:array();
		$menuLang = isset($data["langs"])?$data["langs"]:array();

		$menuId = isset($data["menu_id"])?$data["menu_id"]:array();

		if (!$this->deleteMobileAppMenus($menuId)) {
			return false;
		}
		
		if (!empty($menuId)) {
			$table = $this->getTable('MobileAppMenus');
			
			foreach ($menuId as $key => $value) {
				$data = [];
				$data["id"] = 0;
				if (!empty($value)) {
					$data["id"] = $value;
				}
				
				$data["title"] = $menuTitle[$key];
				$data["url"] = $menuUrl[$key];
				$data["icon"] = $menuIcon[$key];
				$data["group"] = $menuGroup[$key];
				$data["position"] = $menuPosition[$key];
				$data["type"] = $menuType[$key];
				$data["lang"] = $menuLang[$key];

				// Bind the data.
				if (!$table->bind($data)) {
					JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
					return false;
				}

				if (!$table->store()) {
					$application = JFactory::getApplication();
					$application->enqueueMessage($table->getError(), 'error');
					return false;
				}
			}
		}

		return true;
	}
	
	/**
	 * Deletes all company zipcodes whose id is not present
	 * @param $menuId
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function deleteMobileAppMenus($menuId) {
		$ids = implode(',', array_filter($menuId));

		$rowOpt = $this->getTable("MobileAppMenus");

		if ($rowOpt->deleteMobileAppMenus($ids)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($rowOpt->getError(), 'error');
			return false;
		}
		return true;
	}


}
