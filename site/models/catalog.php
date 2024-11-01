<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

class JBusinessDirectoryModelCatalog extends JModelList {
	public function __construct() {
		parent::__construct();
		$jinput = JFactory::getApplication()->input;
		$mainframe = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$app = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $appSettings->dir_list_limit, 'int');
		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->enablePackages = $appSettings->enable_packages;
		$this->showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		
		$session = JFactory::getSession();
		$this->letter = $jinput->getString('letter');
		if (isset($this->letter)) {
			$session->set('letter', $this->letter);
		}
		
		$this->letter = $session->get('letter');
		$session->set("lSearchType", 2);
		$session->set("listing-search", true);
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if (isset($activeMenu)) {
			$session->set("menuItemId", $activeMenu->id);
		}
	}

	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Companies', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function &getCompanies() {
		return $this->companies;
	}
	
	public function getLetter() {
		return $this->letter;
	}
	
	public function getUsedLetter() {
		$companiesTable = $this->getTable("Company");
		
		$letters =  $companiesTable->getUsedLetters();
		$result = array();
		foreach ($letters as $letter) {
			$result[$letter->letter]=$letter->letter;
		}
		
		return $result;
	}
	
	public function getCompaniesByLetter() {
		$companiesTable = $this->getTable("Company");
		$categoryId = JFactory::getApplication()->input->get('categoryId');
	
		$companies =  $companiesTable->getCompaniesByLetter($this->letter, $this->enablePackages, $this->showPendingApproval, $this->getState('limitstart'), $this->getState('limit'));
		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($companies);
			JBusinessDirectoryTranslations::updateBusinessListingsSloganTranslation($companies);
		}
		
		foreach ($companies as $company) {

			if (!empty($company->locations)) {
				$locations = explode("#", $company->locations);
				$distances = isset($company->secondaryDistances) ? explode(',', $company->secondaryDistances) : array();
				if (!empty($distances)) {
					$distance =  floatval($distances[0]);
					foreach ($locations as $k => $location) {
						$tmp = array();
						$loc = explode("|", $location);
						$address = JBusinessUtil::getLocationAddressText($loc[2], $loc[3], $loc[9], $loc[4], $loc[5], $loc[8], $loc[6]);
						if (floatval($distances[$k]) < floatval($company->distance) && floatval($distances[$k]) <= floatval($distance)) {
							$company->bestMatchLocation = $address;
						}
					}
				}
			}

			JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jbusinessdirectory/models', 'Companies');
			$comanyModel = JModelLegacy::getInstance('Companies', 'JBusinessDirectoryModel', array('ignore_request' => true));

			$company->business_hours = $comanyModel->getWorkingDays($company);


			if(empty($company->time_zone)){
				$company->time_zone = $this->appSettings->default_time_zone;
			}

			$company->enableWorkingStatus = false;
			if (!empty($company->business_hours) && $company->opening_status == COMPANY_OPEN_BY_TIMETABLE) {
				foreach ($company->business_hours as $day) {
					if ($day->workHours["status"] == '1') {
						$company->enableWorkingStatus = true;
					}
				}
			}

			if ($company->enableWorkingStatus) {
				$company->workingStatus = $comanyModel->getWorkingStatus($company->business_hours, $company->time_zone, $company->opening_status);
			} else {
				$company->workingStatus = false;
			}

			$company->packageFeatures = array();
			if(!empty($company->features)){
				$company->packageFeatures = explode(",", $company->features);
			}
			
			$attributesTable = $this->getTable('CompanyAttributes');
			$company->customAttributes = $attributesTable->getCompanyAttributes($company->id);
			$company = JBusinessUtil::updateItemDefaultAtrributes($company, $attributeConfig);
			
			if (!empty($company->categories)) {
				$company->categories = explode('#|', $company->categories);
				foreach ($company->categories as $k=>&$category) {
					$category = explode("|", $category);
				}
			}
			
			$maxCategories = !empty($company->categories)?count($company->categories):0;
			if ($this->appSettings->enable_packages) {
				$table = $this->getTable("Package");
				$package = $table->getCurrentActivePackage($company->id);
				if (!empty($package->max_categories) && $maxCategories > (int)$package->max_categories) {
					$maxCategories = (int)$package->max_categories;
				}
			} elseif (!empty($this->appSettings->max_categories)) {
				$maxCategories = $this->appSettings->max_categories;
			}
			
			if ($this->appSettings->search_result_view == 6 && !empty($company->categories)) {
				foreach ($company->categories as $k2=>$category2) {
					if (empty($category2[3]) || $category2[3] == "None") {
						unset($company->categories[$k2]);
					}
				}
			}
			
			if (!empty($company->categories)) {
				$company->categories = array_slice($company->categories, 0, $maxCategories);
			}
			
			if (!empty($company->pictures)) {
				$pictures = [];

				$tmpPictures = explode(',', $company->pictures);
				foreach ($tmpPictures as $key=>$val) {
					$picture = new stdClass();

					$tmpPicture = explode('#', $val);
					$picture->picture_path = $tmpPicture[0];
					$picture->picture_title = !empty($tmpPicture[1]) ? $tmpPicture[1] : "";
					$picture->picture_info = !empty($tmpPicture[2]) ? $tmpPicture[2] : "";

					$pictures[] = $picture;
				}

				$company->pictures = $pictures;
			}
			
			if ($this->appSettings->limit_cities_regions) {
				$table = $this->getTable('Company');
				$company->regions = $table->getCompanyRegions($company->id);
				$company->cities = $table->getCompanyCities($company->id);
			}
		}
		
		$_REQUEST["search-results"] = $companies;
		
		return $companies;
	}
	
	public function getTotalCompaniesByLetter() {
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$categoryId= JFactory::getApplication()->input->get('categoryId');
			$companiesTable = $this->getTable("Company");
			$this->_total = $companiesTable->getTotalCompaniesByLetter($this->letter, $this->enablePackages, $this->showPendingApproval);
		}
		return $this->_total;
	}
	
	
	public function getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			require_once(BD_HELPERS_PATH.'/dirpagination.php');
			$this->_pagination = new JBusinessDirectoryPagination($this->getTotalCompaniesByLetter(), $this->getState('limitstart'), $this->getState('limit'));
			$this->_pagination->setAdditionalUrlParam('option', 'com_jbusinessdirectory');
			$this->_pagination->setAdditionalUrlParam('controller', 'catalog');
			$this->_pagination->setAdditionalUrlParam('view', 'catalog');
		}
		return $this->_pagination;
	}
	
	public function getCategory() {
		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$categoryId = JFactory::getApplication()->input->get('categoryId');
		return  $categoryTable->getCategoryById($categoryId);
	}
	
	/**
	 * Get current user location
	 */
	public function getLocation() {
		$session = JFactory::getSession();
		$location= $session->get("location");
		return $location;
	}
}
