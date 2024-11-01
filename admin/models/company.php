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
require_once BD_CLASSES_PATH.'/services/UserService.php';
require_once(BD_HELPERS_PATH.'/category_lib.php');
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';

use MVC\Utilities\ArrayHelper;
use MVC\String\StringHelper;

/**
 * Company Model for Companies.
 *
 */
class JBusinessDirectoryModelCompany extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_COMPANY';
	protected $header;
	protected $headerDifferences = array();
	protected $newCategoryCount = 0;
	protected $failedCompanies = array();
	protected $newSubcategoryCount = 0;
	protected $error_row = 0;
	protected $newTypesCount = 0;
	protected $newCompaniesCount = 0;
	protected $mainSubcategory = 0;
	protected $tableheader;
	protected $categories;
	protected $companyTypes;
	protected $packages;
	protected $countries;
	protected $languages;
	protected $savedCategories = array();

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jbusinessdirectory.company';

	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();
        $this->populateState();
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	*/
	public function getTable($type = 'Company', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->getInt('id');
		if (isset($id)) {
			$this->setState('company.id', $id);
		}

		$packageId = $jinput->getInt('filter_package');
		if (isset($packageId)) {
			$this->setState('company.packageId', $packageId);
		}
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param
	 *            integer The id of the menu item to get.
	 *
	 * @return mixed Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('company.id');
		if (empty($data['id'])) {
			$data['id'] = 0;
		}
		$false = false;

		// Get a menu item row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$result = $table->load($itemId);

		// Check for a table object error.
		if ($result === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');

		if ($value->id == 0) {
			$value->time_zone = $this->appSettings->default_time_zone;
		}

		$value->pictures = $this->getCompanyPictures($itemId);
		$value->extra_pictures = $this->getCompanyExtraPictures($itemId);
		
		// dbg($this->_data->pictures);
		$value->videos = $this->getCompanyVideos($itemId);

		$value->sounds = $this->getCompanySounds($itemId);

		$value->companyViews    = array();
		$value->companyViews[0] = 'LNG_DEFAULT';
		$value->companyViews[1] = 'LNG_STYLE_1';
		$value->companyViews[2] = 'LNG_STYLE_2';
		$value->companyViews[3] = 'LNG_STYLE_3';
		$value->companyViews[4] = 'LNG_STYLE_4';
		$value->companyViews[5] = 'LNG_STYLE_5';
		$value->companyViews[6] = 'LNG_STYLE_6';
		$value->companyViews[7] = 'LNG_STYLE_7';
		$value->companyViews[8] = 'LNG_STYLE_8';
		//$value->companyViews[9] = 'LNG_STYLE_9';

		$zipCodesTable = $this->getTable('companyzipcode', "Table");
		$value->zipcodes = $zipCodesTable->getAllCompanyZipcodes($itemId);
		if (empty($value->zipcodes)) {
			$zipcode = new stdClass();
			$zipcode->zip_code = "";
			$zipcode->latitude  = "";
			$zipcode->longitude  = "";
			$zipcode->id = "";
			$value->zipcodes = array(
				$zipcode
			);
		}

		$activityCitiesTable = $this->getTable('CompanyActivityCity');
		$value->activityCities = $activityCitiesTable->getActivityCities($itemId);
		
		$activityRegionsTable = $this->getTable('CompanyActivityRegion');
		$value->activityRegions = $activityRegionsTable->getActivityRegions($itemId);

		$activityCountriesTable = $this->getTable('CompanyActivityCountry');
		$value->activityCountries = $activityCountriesTable->getActivityCountries($itemId);
		
		$contactTable = $this->getTable('CompanyContact', "Table");
		$value->contacts = $contactTable->getAllCompanyContacts($itemId);
		if (empty($value->contacts)) {
			$contact = new stdClass();
			$contact->contact_department = "";
			$contact->contact_job_title = "";
			$contact->contact_name = "";
			$contact->contact_email = "";
			$contact->contact_fax = "";
			$contact->contact_phone = "";
			$contact->id = "";
			$value->contacts = array(
				$contact
			);
		}

		$editorTable = $this->getTable('CompanyEditor', "Table");
		$value->editors = $editorTable->getCompanyEditors($itemId);
		$value->editorOptions = JBusinessUtil::getAllUsers();

		$testimonialTable = $this->getTable('CompanyTestimonials', "Table");
		$value->testimonials = $testimonialTable->getAllCompanyTestimonials($itemId);
		if (empty($value->testimonials)) {
			$testimonial = new stdClass();
			$testimonial->testimonial_title = "";
			$testimonial->testimonial_name = "";
			$testimonial->testimonial_description = "";
			$testimonial->id = "";
			$value->testimonials = array(
				$testimonial
			);
		}

		$countriesTable = $this->getTable('Country');
		$value->countries = $countriesTable->getCountries();
		JBusinessDirectoryTranslations::updateCountriesTranslation($value->countries);

		//get countries with regions
		$value->acountries = $countriesTable->getCountriesWithRegions();
		JBusinessDirectoryTranslations::updateCountriesTranslation($value->acountries);

		$typesTable = $this->getTable('CompanyTypes');
		$value->types = $typesTable->getCompanyTypes();
		$value->typeId = explode(',', (string)$value->typeId);
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateTypesTranslation($value->types);
		}

		$cityTable = $this->getTable('City');
		$value->cities = $cityTable->getCities();

		$regionTable = $this->getTable('Region');
		$value->regions = $regionTable->getRegions();

		$companyLocationsTable = $this->getTable('CompanyLocations');
		$value->locations = $companyLocationsTable->getCompanyLocations($itemId);

		// Get selected Companies and Related to fill the dropdown
		$companyTable = $this->getTable('Company');
		if (!empty($itemId)) {
			$value->selectedCompanies = $companyTable->getSelectedCompaniesList($itemId);
		} else {
			$value->selectedCompanies = array();
		}

		if (!empty($value->selectedCompanies)) {
			$value->companyRelatedOptions = $companyTable->getCompanyRelatedOptions($itemId);
		}

		$companyCategoryTable = $this->getTable('CompanyCategory');
		if (!empty($itemId)) {
			$value->selCats = $companyCategoryTable->getSelectedCategoriesList($itemId);
		} else {
			$value->selCats = array();
		}

		$companyMembershipTable = $this->getTable('Memberships', 'Table');
		if (!empty($itemId)) {
			$value->selMembership = $companyMembershipTable->getSelectedMembershipsList($itemId);
		} else {
			$value->selMembership = array();
		}

		$companyCategoryTable = $this->getTable('CompanyCategory');
		$value->selectedCategories = $companyCategoryTable->getSelectedCategories($itemId);
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateCategoriesTranslation($value->selectedCategories);
		}

		foreach ($value->selectedCategories as $cat) {
			$cat->name = str_repeat('- ', $cat->level - 1) . $cat->name;
		}

		$packageId = $this->getState('company.packageId');

		if ($packageId == 0) {
			$this->setState('company.packageId', $value->package_id);
			$packageId = $value->package_id;
		}

		$value->defaultAtrributes = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);

		if ($this->appSettings->enable_packages) {
			if ($packageId != 0) {
				$value->package = $this->getPackage($packageId);
			} else {
				$value->package = $this->getDefaultPackage();
			}

			if (count($value->selCats) > $value->package->max_categories) {
				$diff = count($value->selCats) - $value->package->max_categories;
				$companyCategoryTable->updateCategoriesNumber($itemId, $diff);
				if (!empty($itemId)) {
					$value->selCats = $companyCategoryTable->getSelectedCategoriesList($itemId);
				} else {
					$value->selCats = array();
				}
			}

			if ($this->getState('company.id') > 0 && !empty($value->package->id)) {
				$value->paidPackage = $this->getPackagePayment($this->getState('company.id'), $value->package->id);
				$value->lastActivePackage = $this->getLastActivePackage($this->getState('company.id'));
				$value->lastPaidPackage = $this->getLastPackage($this->getState('company.id'));

				$value->statusTxt= $this->checkBusinessListing($value->package, $value->lastActivePackage, $value->paidPackage);
			}

			if ($this->appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateEntityTranslation($value->package, PACKAGE_TRANSLATION);
			}
		}

		$categoryId = $this->appSettings->enable_attribute_category ? $value->mainSubcategory : null;
		$attributesTable = $this->getTable('CompanyAttributes');
		$value->customFields = $attributesTable->getCompanyAttributes($itemId, $categoryId);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateAttributesTranslation($value->customFields);
		}

		// check if custom fields are contained on packages
		$value->containsCustomFields = false;
		if ($this->appSettings->enable_packages) {
			foreach ($value->customFields as $attribute) {
				if (!empty($value->package->features) && in_array($attribute->code, $value->package->features)) {
					$value->containsCustomFields = true;
					break;
				}
			}
		} else {
			$value->containsCustomFields = true;
		}

		$value->attachments = JBusinessDirectoryAttachments::getAttachments(BUSSINESS_ATTACHMENTS, $itemId);
		if (!empty($value->business_hours)) {
			$value->business_hours = explode(",", $value->business_hours);
		}

		if ($value->publish_start_date == '0000-00-00' || empty($value->publish_start_date)) {
			$value->publish_start_date = null;
		}
		if ($value->publish_end_date == '0000-00-00' || empty($value->publish_end_date)) {
			$value->publish_end_date = null;
		}

		$view = JFactory::getApplication()->input->get("view");
		if ($view == "managecompany" && $value->approved == COMPANY_STATUS_DISAPPROVED) {
			if (!empty($value->disapproval_text)) {
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_BUSINESS_NOT_APPROVED')." ".JText::_('LNG_DISAPPROVAL_REASON').": ".$value->disapproval_text, 'Warning');
			}
		}


		$servicesTable = $this->getTable('CompanyMembers', "Table");
		$value->members = $servicesTable->getCompanyMembers($itemId);
		if (empty($value->members)) {
			$member = new stdClass();
			$member->name="";
			$member->type="";
			$member->title="";
			$member->description="";
			$member->image="";
			$member->id="";
			$value->members = array($member);
		}

		$value->logs = JBusinessUtil::getItemLogs($itemId, ITEM_TYPE_BUSINESS);

		/*   $post = JFactory::getApplication()->input->post->getArray();;
		  if (! empty($post)) {
			  $company = json_decode(json_encode($post));
			  if ($this->appSettings->enable_multilingual) {
				  $lng = JBusinessUtil::getLanguageTag();
				  $name = "name_" . $lng;
				  $slogan = "slogan_" . $lng;
				  $shortDesc = "short_description_" . $lng;
				  $description = "description_" . $lng;
				  $company->name = $company->$name;
				  $company->slogan = $company->$slogan;
				  $company->short_description = $company->$shortDesc;
				  if (! empty($company->$description))
					  $company->description = $company->$description;
			  }
			  foreach ($company as $key => $compVal) {
				  $value->$key = $compVal;
			  }
			  if (isset($post['related-listings'])) {
				  $relatedListings = $post['related-listings'];
				  $companyRelatedOptions = array();
				  $selectedCompanies = array();
				  if (! empty($relatedListings)) {
					  $companyTable = JTable::getInstance("Company", "JTable");
					  foreach ($relatedListings as $index => $related) {
						  $companyDetail = $companyTable->getCompany($related);
						  $object = new stdClass();
						  $object->value = $related;
						  $object->text = $companyDetail->name;
						  $companyRelatedOptions[] = $object;
						  $selectedCompanies[] = $object->value;
					  }
				  }
				  $value->selectedCompanies = $selectedCompanies;
				  $value->companyRelatedOptions = $companyRelatedOptions;
			  }

			  if (isset($company->selectedMemberships) && ! empty($company->selectedMemberships)) {
				  $value->selMembership = $company->selectedMemberships;
			  } else {
				  $value->selMembership = array();
			  }
			  if (isset($value->videos) && ! empty($value->videos)) {
				  foreach ($value->videos as $key => $video) {
					  $object = new stdClass();
					  $object->id = 0;
					  $object->url = $video;
					  $value->videos[$key] = $object;
				  }
			  }

			  if (isset($value->sounds) && ! empty($value->sounds)) {
				  foreach ($value->sounds as $key => $sounds) {
					  $object = new stdClass();
					  $object->id = 0;
					  $object->url = $sounds;
					  $value->sounds[$key] = $object;
				  }
			  }
			  $value->preserved = 1;

			  if (isset($company->contact_id) && ! empty($company->contact_id)) {
				  foreach ($post['contact_id'] as $key => $val) {
					  $companyContact = new stdClass();
					  $companyContact->id = 0;
					  $companyContact->contact_name = $post['contact_name'][$key];
					  $companyContact->contact_email = $post['contact_email'][$key];
					  $companyContact->contact_phone = $post['contact_phone'][$key];
					  $companyContact->contact_fax = $post['contact_fax'][$key];
					  $companyContact->contact_department = $post['contact_department'][$key];
					  $value->contacts[$key] = $companyContact;
				  }
			  }

			  // dump($company);
		  }*/
		// dump($value);

		if (!empty($this->appSettings->trail_weeks_dates)) {
			$trailHours = array();

			$dateRange = JBusinessUtil::processDateRange($this->appSettings->trail_weeks_dates);
			$dates     = JBusinessUtil::getAllDatesInInterval($dateRange->startDate, $dateRange->endDate);

			if (!empty($value->trail_weeks_hours)) {
				$trailHours = json_decode($value->trail_weeks_hours, true);
					
				foreach ($trailHours as $date => &$values) {
					if (!in_array($date, $dates)) {
						unset($trailHours[$date]);
					} else {
						$values = (object) $values;
					}
				}
			}

			foreach ($dates as $date) {
				if (!isset($trailHours[$date])) {
					$trailDate = new stdClass();
					$trailDate->status = 0;
					$trailDate->startHour = '';
					$trailDate->endHour = '';
					$trailDate->breakStatus = 0;
					$trailDate->breakStartHour = '';
					$trailDate->breakEndHour = '';

					$trailHours[$date] = $trailDate;
				}
			}

			uksort($trailHours, function ($a, $b) {
				return strtotime($a) < strtotime($b) ? -1 : 1;
			});

			$value->trailHours = $trailHours;
		}

		foreach ($value->selectedCategories as $ky => $selectedCategory) {
			if (!in_array($selectedCategory->categoryId, $value->selCats)) {
				unset($value->selectedCategories[$ky]);
			}
		}

		return $value;
	}

	
	public function getPackage($packageId) {
		$packageTable = $this->getTable("Package");
		$packageTable->load($packageId);
		$properties = $packageTable->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');
		
		$packageTable = $this->getTable("Package");
		$value->features = $packageTable->getSelectedFeaturesAsString($packageId);
		
		if (isset($value->features)) {
			$value->features = explode(",", $value->features);
		}

		if (!is_array($value->features)) {
			$value->features = array($value->features);
		}
		
		return $value;
	}
	
	public function getDefaultPackage() {
		$packageTable = $this->getTable("Package");
		$package = $packageTable->getDefaultPackage();
		
		if (empty($package)) {
			$package = new stdClass();
			$package->name = JText::_("LNG_NO_ACTIVE_PACKAGE");
			$package->max_attachments=0;
			$package->max_pictures=0;
			$package->max_categories=0;
			$package->max_videos=0;
			$package->max_sounds=0;
			$package->price = 0;
			$package->features = array();
			return $package;
		}
		
		$packageTable = $this->getTable("Package");
		$package->features = $packageTable->getSelectedFeaturesAsString($package->id);
	
		if (isset($package->features)) {
			$package->features = explode(",", $package->features);
		}
	
		if (!is_array($package->features)) {
			$package->features = array($package->features);
		}
		
		return $package;
	}
	
	public function getPackagePayment($companyId, $packageId) {
		$packageTable = $this->getTable("Package");
		$package = $packageTable->getPackagePayment($companyId, $packageId);

		if (!$package) {
			return null;
		}
		$package->expirationDate = $package->end_date;
		
		if ($package->expiration_type==1) {
			$package->expired = false;
		} else {
			$package->expired = strtotime($package->expirationDate) <= time();
		}
		
		return $package;
	}
	
	public function getLastActivePackage($companyId) {
		$packageTable = $this->getTable("Package");
		$package = $packageTable->getLastActivePackage($companyId);
		
		if (!$package) {
			return null;
		}

		$package->expirationDate = $package->end_date;
		if ($package->expiration_type==1) {
			$package->expired = false;
		} else {
			$package->expired = strtotime($package->expirationDate) <= time();
		}
		
		return $package;
	}
	
	public function getLastPackage($companyId) {
		$packageTable = $this->getTable("Package");
		$package = $packageTable->getLastPackage($companyId);
	
		if (!$package) {
			return null;
		}
	
		$package->expirationDate = JBusinessUtil::getDateGeneralShortFormat($package->end_date);
		
		if ($package->expiration_type==1) {
			$package->expired = false;
		} else {
			$package->expired = strtotime($package->expirationDate) <= time();
		}

		return $package;
	}
	
	
	public function extendPeriod($data) {
		return $this->createOrder($data["id"], $data["extend_package_id"], UPDATE_TYPE_EXTEND);
	}
	
	public function getPackages() {
		$packageTable = $this->getTable("Package");
		$packages = $packageTable->getPackages(true, false , true);
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updatePackagesTranslation($packages);
		}

		return $packages;
	}
	
	public function checkBusinessListing($currentPackage, $lastPackage, $packageP) {
		$packages = $this->getPackages();
		$freePackage = null;
		$text = "";
		foreach ($packages as $package) {
			if ($package->price == 0) {
				$freePackage = $package;
			}
		}

		if (!isset($freePackage) && isset($lastPackage) && $lastPackage->expired) {
			$text = JText::_('LNG_BUSINESS_NOT_SHOWN');
		}
		
		if (!isset($packageP) && $currentPackage->price>0) {
			$text = JText::_('LNG_BUSINESS_FEATURES_NOT_SHOWN');
		}

		return $text;
	}
	
	
	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		//exit;
		// The folder and element vars are passed when saving the form.
		if (empty($data)) {
			$item		= $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jbusinessdirectory.company', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form)) {
			return false;
		}
		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.company.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getClaimDetails() {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getClaimDetails((int) $this->getState('company.id'));
	}
	
	/**
	 * Retrive the pictures for a business listing
	 * @param unknown_type $companyId
	 * @return multitype:multitype:NULL
	 */
	public function getCompanyPictures($companyId) {
		$query = "SELECT * FROM #__jbusinessdirectory_company_pictures
				WHERE companyId =".$companyId ."
				ORDER BY id ";
		$files =  $this->_getList($query);
		$pictures = array();
		foreach ($files as $value) {
			$pictures[] = array(
				'id' => $value->id,
				'picture_title' => $value->picture_title,
				'picture_info' => $value->picture_info,
				'picture_path' => $value->picture_path,
				'picture_enable' => $value->picture_enable,
			);
		}
	
		return $pictures;
	}
	
	public function getCompanyVideos($companyId) {
		$query = "SELECT * FROM #__jbusinessdirectory_company_videos
					WHERE companyId =".$companyId ."
					ORDER BY id "
					;
		
		$files =  $this->_getList($query);
		return $files;
	}
	
	public function getCompanySounds($companyId) {
		$query = "SELECT * FROM #__jbusinessdirectory_company_sounds
		WHERE company_id =".$companyId ."
		ORDER BY id ";
	
		$files =  $this->_getList($query);
		return $files;
	}
	
	public function deleteCompany() {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->deteleCompany((int) $this->getState('company.id'));
	}
	
	/**
	 * Check for duplicate alias and generate a new alias
	 * @param unknown_type $busienssId
	 * @param unknown_type $alias
	 */
	public function checkAlias($busienssId, $alias) {
		$companiesTable = $this->getTable();
		while ($companiesTable->checkIfAliasExists($busienssId, $alias)) {
			$alias = StringHelper::increment($alias, 'dash');
		}

		return $alias;
	}

	
	public function parsePlaceholders(&$data) {
		
		$typesTable = $this->getTable("companytypes");
		$companyType = $typesTable->getCompanyType($data["typeId"]);

		$companyAttributeTable = $this->getTable('CompanyAttributes');
		$properties = $companyAttributeTable->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');
		$categoryId = 0;
		if (!empty($value->mainSubcategory)) {
			$categoryId = $this->appSettings->enable_attribute_category ? $value->mainSubcategory : null;
		}
		$value->customFields = $companyAttributeTable->getCompanyAttributes($data["id"], $categoryId);
		
		foreach ($value->customFields as $attribute) {
			$attributeValue = AttributeService::getAttributeValues($attribute);
			if (isset($data["attribute_".$attribute->id]) && is_array($data["attribute_".$attribute->id])) {
				$attributeValue = reset($data["attribute_".$attribute->id]);
			}
				
			$data["description"] 		= str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $data["description"]);
			$data["meta_description"]   = str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $data["meta_description"]);
			$data["meta_title"]   		= str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $data["meta_title"]);
			$data["slogan"]	  		  	= str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $data["slogan"]);
			$data["short_description"]	= str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $data["short_description"]);
			
			if(!empty($data["pictures"])){
				foreach ($data["pictures"] as &$pic) {
					$pic["picture_title"] = str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $pic["picture_title"]);
					$pic["picture_info"] = str_ireplace("{attribute_".$attribute->name."}", $attributeValue, $pic["picture_info"]);
				}
			}
		}

		if (!empty($data["name"])) {
			$data["meta_title"] 	  = str_ireplace("{name}", $data["name"], $data["meta_title"]);
			$data["meta_description"] = str_ireplace("{name}", $data["name"], $data["meta_description"]);
			$data["description"]	  = str_ireplace("{name}", $data["name"], $data["description"]);
			$data["slogan"]	  		  = str_ireplace("{name}", $data["name"], $data["slogan"]);
			$data["short_description"]= str_ireplace("{name}", $data["name"], $data["short_description"]);

			if(!empty($data["pictures"])){
				foreach ($data["pictures"] as &$pic) {
					$pic["picture_title"] =  str_ireplace("{name}", $data["name"], $pic["picture_title"]);
					$pic["picture_info"] =  str_ireplace("{name}", $data["name"], $pic["picture_info"]);
				}
			}
		}

		if (!empty($data["city"])) {
			$data["meta_title"] 	  = str_ireplace("{city}", $data["city"], $data["meta_title"]);
			$data["meta_description"] = str_ireplace("{city}", $data["city"], $data["meta_description"]);
			$data["description"] 	  = str_ireplace("{city}", $data["city"], $data["description"]);
			$data["slogan"]	  		  = str_ireplace("{city}", $data["city"], $data["slogan"]);
			$data["short_description"]= str_ireplace("{city}", $data["city"], $data["short_description"]);

			if(!empty($data["pictures"])){
				foreach ($data["pictures"] as &$pic) {
					$pic["picture_title"] =  str_ireplace("{city}", $data["city"], $pic["picture_title"]);
					$pic["picture_info"] =  str_ireplace("{city}", $data["city"], $pic["picture_info"]);
				}
			}
		}

		if (!empty($data["postalCode"])) {
			$data["meta_title"] 	  = str_ireplace("{zipcode}", $data["postalCode"], $data["meta_title"]);
			$data["meta_description"] = str_ireplace("{zipcode}", $data["postalCode"], $data["meta_description"]);
			$data["description"] 	  = str_ireplace("{zipcode}", $data["postalCode"], $data["description"]);
			$data["slogan"]	  		  = str_ireplace("{zipcode}", $data["postalCode"], $data["slogan"]);
			$data["short_description"]= str_ireplace("{zipcode}", $data["postalCode"], $data["short_description"]);
			
			if(!empty($data["pictures"])){
				foreach ($data["pictures"] as &$pic) {
					$pic["picture_title"] =  str_ireplace("{zipcode}", $data["postalCode"], $pic["picture_title"]);
					$pic["picture_info"] =  str_ireplace("{zipcode}", $data["postalCode"], $pic["picture_info"]);
				}
			}
		}

		if (!empty($companyType)) {
			$data["meta_title"] 	  = str_ireplace("{type}", $companyType->name, $data["meta_title"]);
			$data["meta_description"] = str_ireplace("{type}", $companyType->name, $data["meta_description"]);
			$data["description"]	  = str_ireplace("{type}", $companyType->name, $data["description"]);
			$data["slogan"]	  		  = str_ireplace("{type}", $companyType->name, $data["slogan"]);
			$data["short_description"]= str_ireplace("{type}", $companyType->name, $data["short_description"]);

			foreach ($data["pictures"] as &$pic) {
				$pic["picture_title"] =  str_ireplace("{type}", $companyType->name, $pic["picture_title"]);
				$pic["picture_info"] =  str_ireplace("{type}", $companyType->name, $pic["picture_info"]);
			}
		}
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data) {
		$companiesTable = $this->getTable("Company");
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('company.id');
		$data["id"] = $id;

		$isNew = true;
		$createOrder = true;
		$imported = isset($data['imported'])?true:false;

		//trim space for the submitted values
		foreach ($data as &$item) {
			if (!is_array($item)) {
				$item = trim($item);
			}
		}
		
		$controller = "";
		if(isset($data["task"])){
			$controller = substr($data["task"], 0, strpos($data["task"], "."));
		}
		$this->processLinks($data);

		//do not create user if admin and user registration is not allowed
		if ($this->appSettings->allow_user_creation == 1 && empty($data["userId"]) && $controller == "managecompany" && empty($data["only_contribute"])) {
			$userId = UserService::addUser($data);
			if (empty($userId)) {
				return false;
			}

			$this->setState('created.user.id', $userId);
			$data["userId"] = $userId;
		}
		
		if ($data["userId"]!=0 && $controller == "managecompany") {
			if ($this->appSettings->enable_item_moderation == '0') {
				$data["approved"] = COMPANY_STATUS_APPROVED;
			} else {
				//if number of threshold is 0 then approve else check the number of approved items of this user
				if ($this->appSettings->enable_automated_moderation) {
					if ($this->appSettings->moderate_threshold == '0') {
						$data["approved"] = COMPANY_STATUS_APPROVED;
					} else {
						$table = $this->getTable();
						$totalApprovedUserItems = $table->getCompaniesByUserId($data["userId"], true);
						if (count($totalApprovedUserItems) >= $this->appSettings->moderate_threshold) {
							$data["approved"] = COMPANY_STATUS_APPROVED;
						}
					}
				}
			}
		}

		$data["modified"]=date("Y-m-d H:i:s");
		if (empty($data["publish_only_city"])) {
			$data["publish_only_city"]= 0;
		}

		if (isset($data['approved']) && $data['approved'] == COMPANY_STATUS_APPROVED) {
			$data['disapproval_text']="";
		}

		if (!empty($data["business_hours"][0])
			|| !empty($data["business_hours"][1])
			|| !empty($data["business_hours"][2])
			|| !empty($data["business_hours"][3])
			|| !empty($data["business_hours"][4])
			|| !empty($data["business_hours"][5])
			|| !empty($data["business_hours"][6])
		) {
			$data["business_hours"] = implode(",", $data["business_hours"]);
		} else {
			$data["business_hours"]="";
		}

		$defaultLng  = JBusinessUtil::getLanguageTag();
		$jinput      = JFactory::getApplication()->input;
		$description = $jinput->getString('description_' . $defaultLng, '', 'RAW');
		$name        = $jinput->getString('name_' . $defaultLng, '', 'RAW');
		$meta_title  = $jinput->getString('meta_title_' . $defaultLng, '', 'RAW');
		$meta_desc   = $jinput->getString('meta_description_' . $defaultLng, '', 'RAW');

		if ((!empty($meta_title) && empty($data["meta_title"])) || !isset($data["meta_title"])) {
			$data["meta_title"] = $meta_title;
		}

		if ((!empty($meta_desc) && empty($data["meta_description"])) || !isset($data["meta_description"])) {
			$data["meta_description"] = $meta_desc;
		}
		
		if (!empty($name) && empty($data["name"])) {
			$data["name"] = $name;
		}

		if (!empty($description) && empty($data["description"])) {
			$data["description"] = $description;
		}

		$shortDescription = $jinput->get('short_description_' . $defaultLng, '', 'RAW');
		if (empty($data["short_description"])) {
			$data["short_description"] = $shortDescription;
		}

		$slogan = $jinput->get('slogan_' . $defaultLng, '', 'RAW');
		if (empty($data["slogan"])) {
			$data["slogan"] = $slogan;
		}

		$customTitle   = $jinput->get('custom_tab_name_' . $defaultLng, '', 'RAW');
		if (!empty($customTitle) && empty($data["custom_tab_name"])) {
			$data["custom_tab_name"] = $customTitle;
		}

		$customDesc   = $jinput->get('custom_tab_content_' . $defaultLng, '', 'RAW');
		if (!empty($customDesc) && empty($data["custom_tab_content"])) {
			$data["custom_tab_content"] = $customDesc;
		}

		$website = $jinput->get('additional_description_'.$defaultLng, '', 'RAW');
		if ((!empty($website) && empty($data["website"])) || !isset($data["website"])) {
			$data["website"] = $website;
		}

		$data["alias"] = !empty($data["alias"])?$data["alias"]:"";
		$data["alias"] = JBusinessUtil::getAlias($data["name"], $data["alias"]);
		$data["alias"] = $this->checkAlias($id, $data["alias"]);

		if (!empty($data["latitude"])) {
			$data["latitude"] = filter_var($data['latitude'], FILTER_SANITIZE_NUMBER_FLOAT, array('flags' => FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND));
		}

		if (!empty($data["longitude"])) {
			$data["longitude"] = filter_var($data['longitude'], FILTER_SANITIZE_NUMBER_FLOAT, array('flags' => FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND));
		}


		//set the cover image path based on listing id
		if (!empty($data['business_cover_image']) && !empty($id)) {
			$data['business_cover_image'] = JBusinessUtil::moveFile($data['business_cover_image'], $id, 0, COMPANY_PICTURES_PATH);
			$this->storeCoverImageThumbnail($data);
		}

		if (isset($data["publish_start_date"]) && !JBusinessUtil::emptyDate($data["publish_start_date"]) && !JBusinessUtil::emptyDate($data["publish_end_date"])) {
			if (strtotime($data["publish_start_date"]) > strtotime($data["publish_end_date"])) {
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_END_DATE_LOWER_THAN_START_DATE'), 'warning');
				$data["publish_end_date"] = $data["publish_start_date"];
			}
		}

		if (!empty($data["publish_start_date"])) {
			$data["publish_start_date"] = JBusinessUtil::convertToMysqlFormat($data["publish_start_date"]);
		} else {
			$data["publish_start_date"] = '0000-00-00 00:00:00';
		}

		if (!empty($data["publish_end_date"])) {
			$data["publish_end_date"] = JBusinessUtil::convertToMysqlFormat($data["publish_end_date"]);
		} else {
			$data["publish_end_date"] = '0000-00-00 00:00:00';
		}

		if(empty($data["review_score"])){
			$data["review_score"] = null;
		}

		if(empty($data["recommended"])){
			$data["recommended"] = null;
		}

		// delete main category if not present in data
		if (!isset($data["mainSubcategory"])) {
			$data["mainSubcategory"] = 0;
		}

		if (!isset($data['typeId']) && empty($data['admin_types'])) {
			$data['typeId'] = '';
		} else {
			if(!empty($data['typeId'])){
				$data['typeId'] = implode(',', $data['typeId']);
			}
			if (!empty($data['admin_types'])) {
				$data['typeId'] .= "," . $data['admin_types'];
			}
		}
		
		if (!empty($data["keywords"])) {
			$data["keywords"] = implode(", ", array_map('trim', explode(",", $data["keywords"])));
		}

		$data["description"]        = JBusinessUtil::removeRelAttribute($data["description"]);
		$data["custom_tab_content"] = JBusinessUtil::removeRelAttribute($data["custom_tab_content"]);

		if (isset($data["trail_weeks_status"]) && $data["trail_weeks_status"] == 1) {
			$data["trail_weeks_hours"] = $this->processTrailHours($data);
		}

		// Get a row instance.
		$table = $this->getTable();
		
		// Load the row if saving an existing item.
		$approvedStatus = -1;
		if ($id > 0) {
			$table->load($id);
			$approvedStatus = $table->approved;
			$isNew = false;
		}

		$user = JBusinessUtil::getUser();
		if(!empty($user->ID) && $isNew){
			$data["created_by"] = $user->ID;
		}

		$this->setState('company.isNew', $isNew);

		if ($controller == "managecompany" && $approvedStatus == 1) {
			$data["approved"] = COMPANY_STATUS_CREATED;
		}

		if (!empty($data["filter_package"])) {
			$data["package_id"] = $data["filter_package"];
		}

		if (empty($data["employees"])) {
			$data["employees"] = "";
		}

		if (empty($data["countryId"])) {
			$data["countryId"] = null;
		}

		if (empty($data["activity_radius"])) {
			$data["activity_radius"] = "0";
		}

		if (empty($data["address"])) {
			$data["address"] = "";
		}

		if ($isNew) {
			if (!empty($data['edit_attributes'])) {
				$this->storeAttributes($this->getState('company.id'), $data);
			}
		}

		$this->parsePlaceholders($data);

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}
		
		$id =  $table->id;
		$this->setState('company.id', $table->id);


		$logUserId= $data["userId"];
		if(empty($logUserId)){
			$logUserId = $user->ID;
		}

		if($isNew){
			JBusinessUtil::logAction($table->id, ITEM_TYPE_BUSINESS, ITEM_CREATED, $logUserId);
		}else{
			JBusinessUtil::logAction($table->id, ITEM_TYPE_BUSINESS, ITEM_UPDATED, $logUserId);
		}

		//set the logo path based on listing id and update the database again
		if (!empty($data['logoLocation']) && !empty($id) && $isNew) {
			if (!empty($data['logoLocation'])) {
				$data['logoLocation'] = JBusinessUtil::moveFile($data['logoLocation'], $id, 0, COMPANY_PICTURES_PATH);
			}

			if (!empty($data['ad_image'])) {
				$data['ad_image'] = JBusinessUtil::moveFile($data['ad_image'], $id, 0, COMPANY_PICTURES_PATH);
			}

			if (!empty($data['business_cover_image'])) {
				$data['business_cover_image'] = JBusinessUtil::moveFile($data['business_cover_image'], $id, 0, COMPANY_PICTURES_PATH);
			}

			$data['id'] = $table->id;

			// Bind the data.
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				dump($table->getError());
				return false;
			}
			
			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
				dump($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				dump($table->getError());
				return false;
			}
		}

		
		// Clean the cache
		$this->cleanCache();
		$properties = $table->getProperties(1);
		$company = ArrayHelper::toObject($properties, 'JObject');

		$lastPackage = $this->getLastPackage($company->id);

		$app = JFactory::getApplication();
		$app->setUserState('user.company_id',$company->id);

		if ($this->appSettings->enable_packages && !$imported && ($isNew || $createOrder || (!empty($lastPackage) && $lastPackage->expired))) {
			$type = $isNew? UPDATE_TYPE_NEW: UPDATE_TYPE_UPGRADE;
			$orderId= $this->createOrder($company->id, $company->package_id, $type);
			if (!empty($orderId)) {
				$this->setState('company.redirect.payment', 1);
				$this->setState('company.redirect.orderId', $orderId);
				$this->setState('company.redirect.companyId', $company->id);

								
				$app->setUserState('user.order_id',$orderId);
				
			}else{
				$this->setState('company.redirect.payment', 0);
			}
		}

		JBusinessDirectoryTranslations::saveTranslations(BUSSINESS_DESCRIPTION_TRANSLATION, $table->id, 'description_', false, $imported, $data);
		JBusinessDirectoryTranslations::saveTranslations(BUSSINESS_SLOGAN_TRANSLATION, $table->id, 'slogan_', false, $imported, $data);
		JBusinessDirectoryTranslations::saveTranslations(BUSINESS_META_TRANSLATION, $table->id, '', true, $imported, $data);
		JBusinessDirectoryTranslations::saveCustomTabTranslations($table->id, $imported, $data);
		JBusinessDirectoryAttachments::saveAttachments(BUSSINESS_ATTACHMENTS, BUSINESS_ATTACHMENTS_PATH, $table->id, $data, $id);

		// if no category is selected, create a dummy relation with categoryId = -1 so that
		// the insertRelations function deletes all other existing relations
		if (!isset($data['selectedSubcategories'])) {
			$data['selectedSubcategories'] = array(-1);
		}

		//save in companycategory table
		$table = $this->getTable('CompanyCategory');
		if (!empty($data["selectedSubcategories"])) {
			$table->insertRelations($this->getState('company.id'), $data["selectedSubcategories"]);
		}

		//save in companymembership table
		$tableMembership = $this->getTable('Memberships', 'Table');
		$memberships = !empty($data["selectedMemberships"])?$data["selectedMemberships"]:null;
		$tableMembership->insertRelations($this->getState('company.id'), $memberships);

		//save in related company
		$tableCompany = $this->getTable('Company');
		$relatedListings = isset($data["related-listings"])?$data["related-listings"]:null;
		$tableCompany->insertRelations($this->getState('company.id'), $relatedListings);
		
		try {
			if (isset($data["activity_cities"])) {
				$this->storeActivityCities($this->getState('company.id'), $data["activity_cities"]);
			}

			if (isset($data["activity_regions"])) {
				$this->storeActivityRegions($this->getState('company.id'), $data["activity_regions"]);
			}

			if (isset($data["activity_countries"])) {
				$this->storeActivityCountries($this->getState('company.id'), $data["activity_countries"]);
			}

			if (isset($data['images_included']) || (is_array($data['pictures']) && count($data['pictures'])>0) || (is_array($data['extra_pictures']) && count($data['extra_pictures'])>0) || (!empty($data['deleted'])) || true) {
				$oldId = $isNew?0:$id;
				$data['logoLocation'] = $company->logoLocation;
				$data['business_cover_image']= $company->business_cover_image;
				$data['ad_image']= $company->ad_image;
				$this->storePictures($data, $this->getState('company.id'), $oldId);
			}

			if (isset($data['videos-included'])) {
				$this->storeVideos($data, $this->getState('company.id'));
			}

			if (isset($data['sounds-included'])) {
				$this->storeSounds($data, $this->getState('company.id'));
			}

			if ($isNew) {
				$this->updateLocationId($this->getState('company.id'), $data["identifier"]);
			}

			if (!$data['edit_attributes']) {
				$this->storeAttributes($this->getState('company.id'), $data);
			}
			$this->parsePlaceholders($data);

			// Store the contact details data
			if (!$this->saveContactDetails($data, $this->getState('company.id'))) {
				return false;
			}

			// Store the contact details data
			if (isset($data['companyEditors']) || !empty($data["contribute_editor"]) || $controller == "company") {
				if (!$this->saveEditors($data, $this->getState('company.id'))) {
					return false;
				}
			}

			//save member details
			if (!$this->saveMembersDetails($data, $this->getState('company.id'), $oldId)) {
				return false;
			}

			// Store company zip codes data
			if (!$this->saveCompanyZipcodes($data, $this->getState('company.id'))) {
				return false;
			}
			
			// Store the testimonials details data
			if (!$this->saveTestimonialsDetails($data, $this->getState('company.id'))) {
				return false;
			}
		} catch (Exception $ex) {
			$this->setError($ex->getMessage());
		}
	   
		// save the break and working hours for the company
		try {
			$this->storeWorkingHours($this->getState('company.id'), $data);
			$this->storeBreakingHours($this->getState('company.id'), $data);
		} catch (Exception $ex) {
			$this->setError($ex->getMessage());
		}
		
		//$company=  $this->getItem($company->id);
		if ($data["userId"]==0) {
			if ($approvedStatus == COMPANY_STATUS_CREATED) {
				$send = true;
			} else {
				$send = false;
			}
			//$this->changeClaimAprovalState("-1", $send);
		}

		if ($isNew && empty($data["no-email"])) {
			$companyCategoryTable = $this->getTable('CompanyCategory');
			$company->selectedCategories = $companyCategoryTable->getSelectedCategories($company->id);
			
			$contactTable = $this->getTable('CompanyContact', "Table");
			$company->contacts = $contactTable->getAllCompanyContacts($company->id);
			
			if ($controller == "managecompany") {
				EmailService::sendNewCompanyNotificationEmailToAdmin($company);
				EmailService::sendNewCompanyNotificationEmailToOwner($company);
			} else {
				EmailService::sendNewCompanyNotificationEmailForClaimToOwner($company);
			}
		}
		
		//send approval email if approval status is changed from created status
		if ($approvedStatus == COMPANY_STATUS_CREATED && !$isNew && !$imported) {
			if (isset($data['approved']) && $data['approved'] == COMPANY_STATUS_APPROVED) {
				EmailService::sendApprovalEmail($company);
			} elseif (isset($data['approved']) && $data['approved'] == COMPANY_STATUS_DISAPPROVED) {
				EmailService::sendDisapprovalEmail($company);
			}
		}
		
		// check if changes are made on control panel on front end
		// check if the business is an existing one,
		// check the difference between last time of notification and now is bigger than 6h
		//  if all are true send the email
		if ($this->appSettings->business_update_notification == 1 && !isset($data["autosave"])) {
			$elapsedTime = EMAIL_NOTIFICATION_PERIOD;
			if (!empty($company->notified_date)) {
				$elapsedTime = round((abs(strtotime($company->notified_date) - strtotime(date('Y-m-d H:i:s', time()))) / 60) / 60);
			}
				
			if (($controller == "managecompany") && (!$isNew) && ($elapsedTime >= EMAIL_NOTIFICATION_PERIOD)) {
				if (EmailService::sendUpdateCompanyNotificationEmailToAdmin($company) == true) {
					$companiesTable->updateLastUpdateNotification($company->id);
				}
			}
		}
		
		if (!JBusinessUtil::emptyDate($data["publish_start_date"]) && !JBusinessUtil::emptyDate($data["publish_end_date"])) {
			if (strtotime($data["publish_start_date"]) > strtotime($data["publish_end_date"])) {
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_END_DATE_LOWER_THAN_START_DATE'), 'warning');
			}
		}

		//add the user to the business user group - if one is defined
		if ($isNew && $controller == "managecompany" && !empty($this->appSettings->business_usergroup)) {
			if (!empty($data["userId"]) && !$this->associateUserGroup($data["userId"], $this->appSettings->business_usergroup)) {
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_USER_NOT_ASSOCIATED_WITH_GROUP'), 'warning');
			}
		}

		if ($controller != "managecompany" && $data['current_user_id'] != $data['userId'] && !empty($data['current_user_id']) && !empty($data['userId'])) {
			EmailService::sendListingUserChangeNotification($company, $data);
		}

		JFactory::getApplication()->triggerEvent('onAfterJBDSaveListing', array($company,$isNew));
		return $id;
	}

	public function saveEditors($data, $companyId) {
		$editors = isset($data["companyEditors"])?$data["companyEditors"]:array();
		$table = $this->getTable("CompanyEditor", "Table");
		if (!$table->deleteCompanyEditors($companyId)) {
			return false;
		}

		if (!empty($data["contribute_editor"])) {
			$editors[] = $data["contribute_editor"];
		}

		$editors = array_filter($editors);
		if (!empty($editors)) {
			foreach ($editors as $key => $editor) {
				$table->company_id = $companyId;
				$table->editor_id = $editor;

				if (!$table->store()) {
					$application = JFactory::getApplication();
					$application->enqueueMessage($this->_db->getError(), 'error');
					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Retrieves the contacts data and their company ID, and makes the appropriate
	 * changes in the database
	 * @param $data
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function saveContactDetails($data, $companyId) {
		$contactsName = isset($data["contact_name"])?$data["contact_name"]:array();
		$contactsEmail = isset($data["contact_email"])?$data["contact_email"]:array();
		$contactsPhone = isset($data["contact_phone"])?$data["contact_phone"]:array();
		$contactsFax = isset($data["contact_fax"])?$data["contact_fax"]:array();
		$contactsDepartment = isset($data["contact_department"])?$data["contact_department"]:array();
		$contactsJobtitle = isset($data["contact_job_title"])?$data["contact_job_title"]:array();
		$contactsId = isset($data["contact_id"])?$data["contact_id"]:array();

		$remainingIds = array();
		if (!empty($contactsId)) {
			foreach ($contactsId as $key => $value) {
				if (!empty($contactsName[$key]) || !empty($contactsEmail[$key]) || !empty($contactsPhone[$key]) || !empty($contactsFax[$key]) || !empty($contactsDepartment[$key])
				|| !empty($contactsJobtitle[$key])) {
					$remainingIds[] = $value;
				}
			}
		}
		
		if (!$this->deleteCompanyContacts($remainingIds, $companyId)) {
			return false;
		}

		if (!empty($contactsId)) {
			$companyContactTable = $this->getTable('CompanyContact', 'Table');
			foreach ($contactsId as $key => $value) {
				if (empty($contactsName[$key]) && empty($contactsEmail[$key]) && empty($contactsPhone[$key]) && empty($contactsFax[$key]) && empty($contactsDepartment[$key]) 
					&& empty($contactsJobtitle[$key])) {
					continue;
				} else {
					$companyContactTable->id = 0;
					if (!empty($value)) {
						$companyContactTable->id = $value;
					}
					$companyContactTable->contact_name = isset($contactsName[$key])?$contactsName[$key]:null;
					$companyContactTable->contact_email = isset($contactsEmail[$key])?$contactsEmail[$key]:null;
					$companyContactTable->contact_phone = isset($contactsPhone[$key])?$contactsPhone[$key]:null;
					$companyContactTable->contact_fax = isset($contactsFax[$key])?$contactsFax[$key]:null;
					$companyContactTable->contact_department = isset($contactsDepartment[$key])?$contactsDepartment[$key]:null;
					$companyContactTable->contact_job_title = isset($contactsJobtitle[$key])?$contactsJobtitle[$key]:null;

					$companyContactTable->companyId = $companyId;
					if (!$companyContactTable->store()) {
						$application = JFactory::getApplication();
						$application->enqueueMessage($companyContactTable->getError(), 'error');
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Retrieves the testimonials data and their company ID, and makes the appropriate
	 * changes in the database
	 * @param $data
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function saveTestimonialsDetails($data, $companyId) {
		$testimonialsTitle = isset($data["testimonial_title"])?$data["testimonial_title"]:array();
		$testimonialsName = isset($data["testimonial_name"])?$data["testimonial_name"]:array();
		$testimonialsDesc = isset($data["testimonial_description"])?$data["testimonial_description"]:array();
		$testimonialsId = isset($data["testimonial_id"])?$data["testimonial_id"]:array();

		if (!$this->deleteCompanyTestimonials($testimonialsId, $companyId)) {
			return false;
		}

		if (!empty($testimonialsId)) {
			$companyTestimonialsTable = $this->getTable('CompanyTestimonials', 'Table');
			foreach ($testimonialsId as $key => $value) {
				$companyTestimonialsTable->id = 0;
				if (!empty($value)) {
					$companyTestimonialsTable->id = $value;
				}
				$companyTestimonialsTable->testimonial_title = $testimonialsTitle[$key];
				$companyTestimonialsTable->testimonial_name = $testimonialsName[$key];
				$companyTestimonialsTable->testimonial_description = $testimonialsDesc[$key];

				$companyTestimonialsTable->companyId = $companyId;
				if (!$companyTestimonialsTable->store()) {
					$application = JFactory::getApplication();
					$application->enqueueMessage($companyTestimonialsTable->getError(), 'error');
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * Retrieves the zip codes and lat/long data and their company ID, and makes the appropriate
	 * changes in the database
	 * @param $data
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function saveCompanyZipcodes($data, $companyId) {
		
		$companyZipcode = isset($data["zip_code"])?$data["zip_code"]:array();
		$companyLatitude = isset($data["latitudes"])?$data["latitudes"]:array();
		$companyLongitude = isset($data["longitudes"])?$data["longitudes"]:array();
		$zipcodeId = isset($data["zip_code_id"])?$data["zip_code_id"]:array();

		if (!$this->deleteCompanyZipcodes($zipcodeId, $companyId)) {
			return false;
		}
		
		if (!empty($zipcodeId)) {
			$table = $this->getTable('CompanyZipcode', 'Table');
			
			foreach ($zipcodeId as $key => $value) {
				$data = [];
				$data["id"] = 0;
				if (!empty($value)) {
					$data["id"] = $value;
				}
				
				if (empty($data["latitudes"]) && empty($data["longitudes"]) && !empty($data["zip_code"])) {
					$location = JBusinessUtil::getCoordinates($data["zip_code"]);
					$data["latitudes"] = $location["latitude"];
					$data["longitudes"] = $location["longitude"];
				}
				
				$data["zip_code"] = $companyZipcode[$key];
				$data["latitude"] = $companyLatitude[$key];
				$data["longitude"] = $companyLongitude[$key];
				$data["company_id"] = $companyId;

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
	 * @param $zipcodeId
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function deleteCompanyZipcodes($zipcodeId, $companyId) {
		if(empty($zipcodeId)){
			return true;
		}
		$ids = implode(',', array_filter($zipcodeId));

		$rowOpt = $this->getTable("CompanyZipcode", "Table");

		if ($rowOpt->deleteCompanyZipcodes($companyId, $ids)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($rowOpt->getError(), 'error');
			return false;
		}
		return true;
	}

	/**
	 * Processes the input data into the appropriate json structure for trail week hours.
	 *
	 * @param $data array input
	 *
	 * @return string json
	 */
	function processTrailHours($data) {
		if (empty($this->appSettings->trail_weeks_dates)) {
			return '';
		}
		
		$dateRange = JBusinessUtil::processDateRange($this->appSettings->trail_weeks_dates);
		$dates     = JBusinessUtil::getAllDatesInInterval($dateRange->startDate, $dateRange->endDate);

		$result = array();
		foreach ($dates as $date) {
			$trailDate = new stdClass();
			$trailDate->status = isset($data['trail_status_'.$date]) ? $data['trail_status_'.$date] : 0;

			if ($trailDate->status == 1) {
				$trailDate->startHour = isset($data['trail_start_hour_'.$date]) ? $data['trail_start_hour_'.$date] : 0;
				$trailDate->endHour = isset($data['trail_end_hour_'.$date]) ? $data['trail_end_hour_'.$date] : 0;
				
				$trailDate->breakStatus = isset($data['trail_breaks_status_'.$date]) ? $data['trail_breaks_status_'.$date] : 0;
				$trailDate->breakStartHour = '';
				$trailDate->breakEndHour = '';
	
				if ($trailDate->breakStatus) {
					$trailDate->breakStartHour = isset($data['trail_break_start_hour_'.$date]) ? $data['trail_break_start_hour_'.$date] : '';
					$trailDate->breakEndHour = isset($data['trail_break_end_hour_'.$date]) ? $data['trail_break_end_hour_'.$date] : '';
				}
	
				$result[$date] = $trailDate;
			}
		}

		$result = json_encode($result);

		return $result;
	}

	/**
	 * Deletes all company contacts whose id is not present in the $contactIds list. If this list is
	 * empty, then all contacts that have the same company id as $companyId will be deleted
	 * @param $contactIds
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function deleteCompanyContacts($contactIds, $companyId) {
		$ids = implode(',', array_filter($contactIds));

		$rowOpt = $this->getTable("CompanyContact", "Table");

		if ($rowOpt->deleteCompanyContacts($companyId, $ids)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($rowOpt->getError(), 'error');
			return false;
		}
		
		return true;
	}

	/**
	 * Deletes all company testimonials whose id is not present in the $contactIds list. If this list is
	 * empty, then all contacts that have the same company id as $companyId will be deleted
	 * @param $contactIds
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	public function deleteCompanyTestimonials($testimonialsId, $companyId) {
		$ids = implode(',', array_filter($testimonialsId));

		$rowOpt = $this->getTable("CompanyTestimonials", "Table");

		if ($rowOpt->deleteCompanyTestimonials($companyId, $ids)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($rowOpt->getError(), 'error');
			return false;
		}
		return true;
	}
	
	/**
	 * Create the order for the packages. Order is created now also if the price is 0 and package type is different than lifetime.
	 * The free orders should not be marked automatically as paid and they should be confirmed by the end user.
	 * 
	 * We can have the following cases
	 * 
	 * 1. Fixed - the order is created with start and end date. 
	 * 2. Recurring - the order is created with start and edit date and also a subscription is created on payment time.
	 * 3. Recurring with trial - the order is created with start and end date for the trial period. (trial_start_date and trial_end_date will be ignored)
	 * 
	 * When a package is upgraded, if the package expiration type is fixed, the new package can start right away with the price difference, otherwise it will start when the current package expires.
	 * When a package is downgraded, if the package expiration type is fixed, the new package can start right away with the price difference, otherwise it will start when the current package will end
	 * 
	 */

	public function createOrder($companyId, $packageId, $type, $getPackageFromCompany = true) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$companyTable = $this->getTable("Company");
		$company = $companyTable->getCompany($companyId);
		
		$packageTable = $this->getTable("Package");
		$package = $packageTable->getPackage($packageId);
		
		if (empty($package) || ($package->expiration_type == 1 && $package->price == 0 )) {
			return false;
		}
		
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($package, PACKAGE_TRANSLATION);
		}
		
		$orderId = $package->name;
		
		// if ($type == UPDATE_TYPE_NEW) {
		// 	$orderId = JText::_("LNG_NEW_LISTING")." - ".$package->name;
		// 	$description = JText::_("LNG_NEW_LISTING")." $company->name"." - ".$package->name;
		// } elseif ($type == UPDATE_TYPE_EXTEND) {
		// 	$orderId = JText::_("LNG_RENEW")." - ".$package->name;
		// 	$description = JText::_("LNG_RENEW")." $company->name"." - ".$package->name;
		// }
		
		$appSettings->vat = floatval($appSettings->vat);
		
		$lastPaidPackage = $packageTable->getLastActivePackage($company->id);
		$start_date = date("Y-m-d");
		$remainingAmount = 0;

		//the start date will be calculated based on the last paid package end date.
		//for fixed packages we will substract the remaining period.
		if (!empty($lastPaidPackage)) {
			//the same upgrade package as paid package
			
			if ($lastPaidPackage->package_id == $packageId && strtotime(date("Y-m-d")) <= strtotime($lastPaidPackage->end_date) && $type != UPDATE_TYPE_EXTEND) {
				return $lastPaidPackage->id;
			}

			if($lastPaidPackage->price > $package->price){
				// $orderId = JText::_("LNG_DOWNGRADE")." - ".$package->name;
				// $description = JText::_("LNG_DOWNGRADE")." $company->name"." - ".$package->name;
			}

			//when previous package was a fixed package the start date will be the current date
			if (strtotime(date("Y-m-d")) <= strtotime($lastPaidPackage->end_date) && $lastPaidPackage->expiration_type != 1) {
				$start_date = $lastPaidPackage->end_date;
			} else {
				$start_date = date("Y-m-d");
			}
				
			if ($type == UPDATE_TYPE_UPGRADE && $lastPaidPackage->expiration_type == 2 && $package->expiration_type == 2 && $lastPaidPackage->price <= $package->price && strtotime(date("Y-m-d"))<=strtotime($lastPaidPackage->end_date)) {
				$start_date = date("Y-m-d");

				$remainingDays = floor((strtotime($lastPaidPackage->end_date) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
				// dump($remainingDays);
				if ($lastPaidPackage->expiration_type == 1) {
					$remainingDays = 0;
					$lastPaidPackage->days = 0;
				}
				
				if ($remainingDays>0) {
					$remainingAmount = $lastPaidPackage->price/$lastPaidPackage->days * $remainingDays;
				}
			}
		}
		// dump($lastPaidPackage->price);
		// dump($lastPaidPackage->days);
		// dump($remainingAmount);
		// exit;
		$user = JBusinessUtil::getUser($company->userId);
		$table= $this->getTable("Order");
		$lastUnpaidOrder = $table->getLastUnpaidOrder($company->id, $lastPaidPackage);

		//order has already been generated
		if(!empty($lastUnpaidOrder) && $lastUnpaidOrder->package_id == $package->id){
			return $lastUnpaidOrder->id;
		}

		$data = array();
		$data["order_id"] = $orderId;
		$data["company_id"] = $company->id;
		if ($getPackageFromCompany) {
			$data["package_id"] = $company->package_id;
		} else {
			$data["package_id"] = $packageId;
		}
		
		$data["vat"] = $appSettings->vat;

		if (!empty($lastUnpaidOrder)) {
			$data["id"] = $lastUnpaidOrder->id;
			$table->deleteOrderDetails($lastUnpaidOrder->id);
		}

		if ($type == UPDATE_TYPE_UPGRADE) {
			$data["initial_amount"] = $package->price - $remainingAmount;
			//when we a trial recurring package we will take into consideration the trial price
		}else if ($type == UPDATE_TYPE_EXTEND && !empty(floatval($package->renewal_price))) {
			$data["initial_amount"] = $package->renewal_price;
		} else {
			$data["initial_amount"] = $package->price;
			//dump($lastPaidPackage);
			if($lastPaidPackage->package_id == $package->id && !empty($lastPaidPackage->discount_code)){
				$discountCode = $lastPaidPackage->discount_code;
				//dump($discountCode);
				$discountTable = JTable::getInstance("Discount", "JTable", array());
				$discount = $discountTable->getDiscount($discountCode, -1);
				//dump($discount);
				if (!empty($discount)) {
					$discount->package_ids = explode(",", $discount->package_ids);
					if (in_array($package->id, $discount->package_ids)) {
						$data["discount_code"] = $discount->code;
						if ($discount->price_type==1) {
							$data["discount_amount"] = $discount->value;
						} else {
							$data["discount_amount"] = $data["initial_amount"] * $discount->value/100;
						}
					}
				}else{
					$data["discount_amount"]  = 0;
				}
			}else{
				$data["discount_code"]    = null;
				$data["discount_amount"]  = 0;
			}
		}

		JModelLegacy::addIncludePath(JPATH_COMPONENT_SITE . '/models', 'BillingDetails');
		$model = JModelLegacy::getInstance('BillingDetails', 'JBusinessDirectoryModel', array('ignore_request' => true));

		$billingDetails = $model->getItem();
		$countryId = (!empty($billingDetails) && !empty($billingDetails->id) && !empty($billingDetails->country)) ? $billingDetails->country->id : null;

		if($package->expiration_type == 4){
			$data["trial_initial_amount"] = floatval($package->trial_price);
			$data["trial_amount"] = $data["trial_initial_amount"] + $appSettings->vat * $data["trial_initial_amount"] / 100;
			$taxObject = TaxService::calculateTaxes($data["trial_initial_amount"], JBD_PACKAGES, $countryId);
			$data["trial_amount"] += (float) $taxObject->tax_amount;
		}

		$initialAmount = $data["initial_amount"];
		if(!empty($data["discount_amount"])){
			$initialAmount = $initialAmount - floatval($data["discount_amount"]);
		}
		$data["vat_amount"]     = $initialAmount * $appSettings->vat / 100;
		$data["amount"]         = $initialAmount + $data["vat_amount"];

		$taxObject = TaxService::calculateTaxes($initialAmount, JBD_PACKAGES, $countryId);
		$data["amount"] += (float) $taxObject->tax_amount;

		$timeUnit = JBusinessUtil::getTimeUnit($package->time_unit);
		$endDate = date('Y-m-d', strtotime($start_date. " + $package->time_amount $timeUnit"));
		
		if ($package->expiration_type == 1) {
			$endDate = date('Y-m-d', strtotime($start_date. " + 100 years"));
		}

		//invalidate price per month to display the full time period
		$package->show_price_per_month = 0;
		$serviceName = $package->name." - ".$company->name;
		$description = JText::sprintf("LNG_ORDER_DESCIPTION", $package->name, $appSettings->company_name, JBusinessUtil::getPackageDuration($package), JBusinessUtil::getDateGeneralShortFormat($start_date));

		$data["state"]            = 0;
		$data["start_date"]       = $start_date;
		$data["end_date"]         = $endDate;
				
		$data["user_name"]        = $user->name;
		$data["user_id"]          = $user->ID;
		$data["service"]          = $serviceName;
		$data["description"]      = $description;
		$data["type"]             = $type;
		$data["currency"]         = $appSettings->currency_name;

		$isNew = $this->getState('company.isNew');
        if($isNew){
            $data["state"] = 0;
            //$data["only_trial"] = $package->allow_free_trial;
        }

		// dump($data);
		// exit;
		// Bind the data.
		if (!$table->bind($data)) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			return false;
		}
		
		// Check the data.
		if (!$table->check()) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			return false;
		}
		
		// Store the data.
		if (!$table->store()) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			return false;
		}
		
		$table->createOrderDetails($table->id, $company->package_id);
		$table->createOrderTax($table->id, JBD_PACKAGES, $taxObject->taxes);

		return $table->id;
	}

	public function storeAttributes($companyId, $data) {

		if (empty($companyId)) {
			return;
		}

		#delete all ad attributes
		$attrTable =$this->getTable('CompanyAttributes');
		
		$attrIds=array();
		foreach ($data as $key => $value) {
			#save ad attributes
			if (strpos($key, "attribute")===0) {
				$attributeArr = explode("_", $key);
				if (!empty($attributeArr[1])) {
					$attrIds[]=$attributeArr[1];
				}
			}
			if (strpos($key, "delete_attribute")===0) {
				$attributeArr = explode("_", $key);
				if (!empty($attributeArr[2])) {
					$attrIds[]=$attributeArr[2];
				}
			}
		}
		$attrIds = array_unique($attrIds);
		try {
			if (!empty($attrIds) && !$attrTable->deleteCompanyAttributes($companyId, $attrIds)) {
				$this->setError(JText::_("LNG_ERROR_DELETING_AD_ATTRIBUTES").$attrTable->getError());
			}
			//exit;
			foreach ($data as $key => $value) {

				if(empty($value)){
					continue;
				}

				#save ad attributes
				if (strpos($key, "attribute")===0) {
					$attributeArr = explode("_", $key);
					//dump($attributeArr);
					$companyAttributeTable =$this->getTable('CompanyAttributes');
					$companyAttributeTable->company_id= $companyId;
					if(is_int($value)){
						$companyAttributeTable->option_id= $value;
					}
					$companyAttributeTable->value= $value;
					$companyAttributeTable->attribute_id= $attributeArr[1];
					
					if (!is_array($companyAttributeTable->value)) {
						$companyAttributeTable->value = array($companyAttributeTable->value);
					}
					
					$values = $companyAttributeTable->value;
					foreach($values  as $val){
						$companyAttributeTable->id = null;
						$companyAttributeTable->value = $val;
						if (!$companyAttributeTable->store()) {
							$this->setError(JText::_("LNG_ERROR_SAVING_AD_ATTRIBUTES").$companyAttributeTable->getError());
						}
					}
				}
			}
		} catch (Exception $e) {
			dump($e);
		}
	}
	
	public function storeCompanyContact($data, $companyId) {
		$row = $this->getTable('CompanyContact', "Table");
		$data["companyId"]= $companyId;
		$key = array("companyId"=>$companyId);
		$data["id"]=null;
		$row->load($key, true);
		
		if (!$row->bind($data)) {
			throw( new Exception($row->getError()) );
			$this->setError($row->getError());
		}
		// Make sure the record is valid
		if (!$row->check()) {
			throw( new Exception($row->getError()) );
			$this->setError($row->getError());
		}

		// Store the web link table to the database
		if (!$row->store()) {
			throw( new Exception($this->_db->getErrorNum()) );
			$this->setError($row->getError());
		}
	}
	
	public function storeActivityCities($companyId, $cities, $forceDelete=false) {
		if(is_array($cities)){
			$cities = array_filter($cities);
		}

		if (empty($cities) && !$forceDelete) {
			return;
		}

		$companyActivityCity = $this->getTable('CompanyActivityCity', "JTable");
		if (!is_array($cities) && !empty($cities)) {
			$cities = array($cities);
		}
		$companyActivityCity->deleteNotContainedCities($companyId, $cities);

		if ($cities[0] == '-1') {
			return;
		}
		
		foreach ($cities as $city) {
			$row = $this->getTable('CompanyActivityCity', "JTable");
			
			$obj = $row->getActivityCity($companyId, $city);
			
			if (!empty($obj)) {
				continue;
			}
			$obj = new stdClass();
			$obj->company_id = $companyId;
			$obj->city_id = $city;
			
			if (!$row->bind($obj)) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
			// Make sure the record is valid
			if (!$row->check()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
	
			// Store the web link table to the database
			if (!$row->store(true)) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
		}
	}

	public function storeActivityRegions($companyId, $regions, $forceDelete=false) {

		if(is_array($regions)){
			$regions = array_filter($regions);
		}
		
		if (empty($regions) && !$forceDelete) {
			return;
		}

		if (!is_array($regions) && !empty($regions)) {
			$regions = array($regions);
		}

		$companyActivityRegion = $this->getTable('CompanyActivityRegion', "JTable");
		$companyActivityRegion->deleteNotContainedRegions($companyId, $regions);

		if(empty($regions)){
			return;
		}

		if ($regions[0] == '-1') {
			return;
		}

		foreach ($regions as $region) {
			$row = $this->getTable('CompanyActivityRegion', "JTable");

			$obj = $row->getActivityRegion($companyId, $region);
			if (!empty($obj)) {
				continue;
			}

			$obj = new stdClass();
			$obj->company_id = $companyId;
			$obj->region_id = $region;

			if (!$row->bind($obj)) {
				throw (new Exception($row->getError()));
				$this->setError($row->getError());
			}

			// Make sure the record is valid
			if (!$row->check()) {
				throw (new Exception($row->getError()));
				$this->setError($row->getError());
			}

			// Store the web link table to the database
			if (!$row->store(true)) {
				throw (new Exception($row->getError()));
				$this->setError($row->getError());
			}
		}
	}

	
	public function storeActivityCountries($companyId, $countries, $forceDelete = false ) {

		if(is_array($countries)){
			$countries = array_filter($countries);
		}

		if (empty($countries) && !$forceDelete) {
			return;
		}

		$companyActivityCountry = $this->getTable('CompanyActivityCountry', "JTable");
		if (!is_array($countries) && !empty($countries)) {
			$countries = array($countries);
		}
		
		$companyActivityCountry = $this->getTable('CompanyActivityCountry', "JTable");
		$companyActivityCountry->deleteNotContainedCountries($companyId, $countries);

		if ($countries[0] == '-1') {
			return;
		}

		foreach ($countries as $country) {
			$row = $this->getTable('CompanyActivityCountry', "JTable");

			$obj = $row->getActivityCountry($companyId, $country);
			if (!empty($obj)) {
				continue;
			}

			$obj = new stdClass();
			$obj->company_id = $companyId;
			$obj->country_id = $country;

			if (!$row->bind($obj)) {
				throw (new Exception($row->getError()));
				$this->setError($row->getError());
			}

			// Make sure the record is valid
			if (!$row->check()) {
				throw (new Exception($row->getError()));
				$this->setError($row->getError());
			}

			// Store the web link table to the database
			if (!$row->store(true)) {
				throw (new Exception($row->getError()));
				$this->setError($row->getError());
			}
		}
	}


	public function storeVideos($data, $companyId) {
		$table = $this->getTable('CompanyVideos');
		$table->deleteAllForCompany($companyId);
		$cmpVideoTitle = isset($data["title"])?$data["title"]:array();

		foreach ($data['videos'] as $key => $value) {
			$data["title"] = $cmpVideoTitle[$key];

			if (empty($value)) {
				continue;
			}
			
			$row = $this->getTable('CompanyVideos');
				
			$video = new stdClass();
			$video->id =0;
			$video->companyId = $companyId;
			$video->url = $value;
			$video->title = $data["title"];

			if (!$row->bind($video)) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
			// Make sure the record is valid
			if (!$row->check()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
				
			// Store the web link table to the database
			if (!$row->store()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
		}
	}
	
	/**
	 * Creates the cover thumbnail image from the uploaded cover image
	 * 
	 */
	public function storeCoverImageThumbnail(&$data){
		
		if (!empty($data['business_cover_image'])) {
			$coverImage =  $data['business_cover_image'];
			$coverImageThumb = JBusinessUtil::getThumbnailImage($coverImage); 
			$data['business_cover_image_thumb'] = $coverImageThumb;

			$coverImage = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH).$coverImage;
			$coverImageThumb = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH).$coverImageThumb;

			if (!@copy($coverImage, $coverImageThumb)) {
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_COULD_NOT_CREATE_COVER_THUMB'), 'warning');
			}

			if(!JBusinessUtil::resizeImage($coverImageThumb)){
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_COULD_NOT_RESIZE_COVER_THUMB'), 'warning');
			}

		}
	}

	/**
	 * Store listings pictures and remove unused files
	 * 
	 */
	public function storePictures($data, $companyId, $oldId) {
		$usedFiles = array();
		if (!empty($data['pictures'])) {
			foreach ($data['pictures'] as $value) {
				array_push($usedFiles, $value["picture_path"]);
			}
		}

		if (!empty($data['extra_pictures'])) {
			foreach ($data['extra_pictures'] as $value) {
				array_push($usedFiles, $value["image_path"]);
			}
		}

		if (!empty($data['logoLocation'])) {
			array_push($usedFiles, $data['logoLocation']);
		}

		if (!empty($data['business_cover_image'])) {
			array_push($usedFiles, $data['business_cover_image']);
		}

		if (!empty($data['business_cover_image_thumb'])) {
			array_push($usedFiles, $data['business_cover_image_thumb']);
		}

		if (!empty($data['ad_image'])) {
			array_push($usedFiles, $data['ad_image']);
		}
		
		if (!empty($data['member_image'])) {
			foreach ($data['member_image'] as $img) {
				array_push($usedFiles, $img);
			}
		}

		$pictures_path = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH);
		$company_pictures_path = JBusinessUtil::makePathFile(COMPANY_PICTURES_PATH.($companyId)."/");
		JBusinessUtil::removeUnusedFiles($usedFiles, $pictures_path, $company_pictures_path);
		
		$picture_ids 	= array();
		foreach ($data['pictures'] as $value) {
			$row = $this->getTable('CompanyPictures');
	
			$pic 				= new stdClass();
			$pic->id		= 0;
			$pic->companyId 	= $companyId;
			$pic->picture_title	= $value['picture_title'];
			$pic->picture_info	= $value['picture_info'];
			$pic->picture_path	= $value['picture_path'];
			$pic->picture_enable= $value['picture_enable'];
			
			$pic->picture_path = JBusinessUtil::moveFile($pic->picture_path, $companyId, $oldId, COMPANY_PICTURES_PATH);

			//dump("save");
			//dbg($pic);
			//exit;
			if (!$row->bind($pic)) {
				$this->setError($row->getError());
			}
			// Make sure the record is valid
			if (!$row->check()) {
				$this->setError($row->getError());
			}
	
			// Store the web link table to the database
			if (!$row->store()) {
				$this->setError($row->getError());
			}
	
			$picture_ids[] = $this->_db->insertid();
		}
	
	
		$query = " DELETE FROM #__jbusinessdirectory_company_pictures
				WHERE companyId = '".$companyId."'
				".(count($picture_ids)> 0 ? " AND id NOT IN (".implode(',', $picture_ids).")" : "");
				
		$this->_db->setQuery($query);
		if (!$this->_db->execute()) {
			throw( new Exception("Error executing query") );
		}
	
		$pictureIds 	= array();
		foreach ($data['extra_pictures'] as $value) {
			$table = $this->getTable('CompanyPicturesExtra');

			$img 				= new stdClass();
			$img->id			= 0;
			$img->companyId 	= $companyId;
			$img->image_title	= $value['image_title'];
			$img->image_info	= $value['image_info'];
			$img->image_path	= $value['image_path'];
			$img->image_enable	= $value['image_enable'];
			
			$img->image_path = JBusinessUtil::moveFile($img->image_path, $companyId, $oldId, COMPANY_PICTURES_PATH);

			if (!$table->bind($img)) {
				throw( new Exception($table->getError()) );
				$this->setError($table->getError());
			}
			// Make sure the record is valid
			if (!$table->check()) {
				throw( new Exception($table->getError()) );
				$this->setError($table->getError());
			}

			// Store the web link table to the database
			if (!$table->store()) {
				throw( new Exception($table->getError()) );
				$this->setError($table->getError());
			}

			$pictureIds[] = $this->_db->insertid();
		}

		$query = " DELETE FROM #__jbusinessdirectory_company_pictures_extra
			WHERE companyId = '".$companyId."'
			".(count($pictureIds)> 0 ? " AND id NOT IN (".implode(',', $pictureIds).")" : "");

		$this->_db->setQuery($query);

		try {
			$this->_db->execute();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	
	public function storeSounds($data, $companyId) {
		$table = $this->getTable('CompanySounds');
		$table->deleteAllForCompany($companyId);
	
		$data['sounds'] = $_REQUEST["sounds"];

		foreach ($data['sounds'] as $value) {
			if (empty($value)) {
				continue;
			}
				
			$row = $this->getTable('CompanySounds');
	
			$video = new stdClass();
			$video->id =0;
			$video->company_id = $companyId;
			$video->url = $value;
	
			if (!$row->bind($video)) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
			// Make sure the record is valid
			if (!$row->check()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
	
			// Store the web link table to the database
			if (!$row->store()) {
				throw( new Exception($row->getError()) );
				$this->setError($row->getError());
			}
		}
	}


	/**
	 * Save members into the database
	 *
	 * @param $data object all company detail
	 * @param $companyId int company Id
	 * @param $oldId int old company Id
	 * @return bool
	 * @throws Exception
	 */
	function saveMembersDetails($data, $companyId, $oldId){
		$memberName = $data["member_name"];
		$memberType = $data["member_type"];
		$memberTitle = $data["member_title"];
		$memberDescription = $data["member_description"];
		$memberImage = $data["member_image"];
		$memberIds = $data["member_id"];

		if (!$this->deleteCompanyMembers($memberIds, $companyId)) {
			return false;
		}

		$companyMemberTable = $this->getTable('CompanyMembers', 'Table');
	
		if(!empty($memberIds)){
			foreach ($memberIds as $key => $value) {
				if (!empty($memberName[$key])) {
					if (!empty($memberImage[$key])) {
						$memberImage[$key] = JBusinessUtil::moveFile($memberImage[$key], $companyId, $oldId, COMPANY_PICTURES_PATH);
					}
					$companyMemberTable->id = 0;
					$companyMemberTable->name = $memberName[$key];
					$companyMemberTable->type = $memberType[$key];
					$companyMemberTable->title = $memberTitle[$key];
					$companyMemberTable->description = $memberDescription[$key];
					$companyMemberTable->image = $memberImage[$key];
					$companyMemberTable->company_id = $companyId;

					if (!$companyMemberTable->store()) {
						$application = JFactory::getApplication();
						$application->enqueueMessage($companyMemberTable->getError(), 'error');
						return false;
					}
				}
			}
		}

		return true;
	}
	
	/**
	 * Delete company members that are not present on save
	 *
	 * @param $membersIds
	 * @param $companyId
	 * @return bool
	 * @throws Exception
	 */
	function deleteCompanyMembers($membersIds, $companyId){
		if(empty($membersIds)){
			return true;
		}

		$ids = implode(',', array_filter($membersIds));

		$membersTable = $this->getTable("CompanyMembers", "Table");

		if ($membersTable->deleteCompanyMembers($companyId, $ids)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($membersTable->getError(), 'error');
			return false;
		}
		return true;
	}

	public function changeState($id = null) {
		if (empty($id)) {
			$id = JFactory::getApplication()->input->getInt('id');
		}
		
		if (empty($id)) {
			return false;
		}
		
		$companiesTable = $this->getTable("Company");
		return $companiesTable->changeState($id);
	}
	
	public function changeFeaturedState($id) {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->changeFeaturedState($id);
	}

	public function changeRecommendedState($id) {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->changeRecommendedState($id);
	}

	public function changeAprovalState($state, $disapprovalText = null) {
		$this->populateState();
		$companiesTable = $this->getTable("Company");
		return $companiesTable->changeAprovalState($this->getState('company.id'), $state, $disapprovalText);
	}

	public function changeClaimAprovalState($state, $sendDisapproval = true) {
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->getInt('id');
		if (!empty($id)) {
			$this->setState('company.id', $id);
		}

		$companiesTable = $this->getTable("Company");
		$claimDetails   = $companiesTable->getClaimDetails($this->getState('company.id'));
		$companiesTable->changeClaimState($this->getState('company.id'), $state);

		if ($state == -1) {
			if ($sendDisapproval) {
				$this->sendNegativeClaimResponseEmail($this->getState('company.id'), $claimDetails);
			}
			$companiesTable->resetCompanyOwner($this->getState('company.id'));
		} else if($state == 0){
			$companiesTable->resetCompanyOwner($this->getState('company.id'));
		} else {
			//add the user to the business user group - if one is defined
			if (!empty($this->appSettings->business_usergroup)) {
				$userId = $jinput->getInt('userId');
				if (!$this->associateUserGroup($userId, $this->appSettings->business_usergroup)) {
					JFactory::getApplication()->enqueueMessage(JText::_('LNG_USER_NOT_ASSOCIATED_WITH_GROUP'), 'warning');
				}
			}
			$this->sendClaimResponseEmail($this->getState('company.id'), $claimDetails);
		}
	}
	
	/**
	 * Prepare & Send positive claim response email
	 * @param $companyId
	 * @param $claimDetails
	 */
	public function sendClaimResponseEmail($companyId, $claimDetails) {
		$companyTable = $this->getTable("Company");
		$company = $companyTable->getCompany($companyId);
	
		$result = EmailService::sendClaimResponseEmail($company, $claimDetails, "Claim Response Email");
		return $result;
	}
	
	/**
	 * Prepare & Send negative claim response email
	 *
	 * @param $companyId
	 * @param  $claimDetails
	 * @return unknown
	 */
	public function sendNegativeClaimResponseEmail($companyId, $claimDetails) {
		$companyTable = $this->getTable("Company");
		$company = $companyTable->getCompany($companyId);
	
		$result = EmailService::sendClaimResponseEmail($company, $claimDetails, "Claim Negative Response Email");

		return $result;
	}
	
	/**
	 * Check if the same company name exists
	 * @param $companyName
	 */
	public function checkCompanyName($companyName) {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->checkCompanyName($companyName);
	}
	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);

		// Get a group row instance.
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}

			$user = JBusinessUtil::getUser();
			JBusinessUtil::logAction($table->id, ITEM_TYPE_BUSINESS, ITEM_DELETED, $user->ID);
			
			if (!$this->deleteFiles($itemId)) {
				$this->setError("Could not delete files");
				return false;
			}
			
			if (!$table->deleteAllDependencies($itemId)) {
				$this->setError($table->getError());
				return false;
			}
			JBusinessDirectoryTranslations::deleteTranslationsForObject(BUSSINESS_DESCRIPTION_TRANSLATION, $itemId);
			JBusinessDirectoryTranslations::deleteTranslationsForObject(BUSSINESS_SLOGAN_TRANSLATION, $itemId);
		}
		
		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	/**
	 * Delete business listing files
	 * @param $itemId
	 * @return boolean
	 */
	public function deleteFiles($itemId) {
		$imagesDir = BD_PICTURES_UPLOAD_PATH .COMPANY_PICTURES_PATH.($itemId);
		JBusinessUtil::removeDirectory($imagesDir);
		
		$attachmentDir = BD_ATTACHMENT_UPLOAD_PATH .BUSINESS_ATTACHMENTS_PATH.$itemId;
		JBusinessUtil::removeDirectory($attachmentDir);

		return true;
	}


	/**
	 * Prepare details for importing
	 *
	 */
	public function prepareImport() {
	
		// Get the importer state.
		$state = $this->getBatchState();
		$filePath = $state->filePath;
		$delimiter = $state->delimiter;

		// Get the number of content items.
		$total =0;
		//ini_set("auto_detect_line_endings", "1");
		
		if (($handle = fopen($filePath, "r")) !== false) {
			while (($data = fgetcsv($handle, 9000, $delimiter)) !== false) {
				$total ++;
			}
		}

		// Add the content count to the total number of items.
		$state->totalItems = $total-1;
		$state->offset = 0;
		// Set the importer state.
		$this->setBatchState($state);
	}


	/**
	 * Index a batch of items.
	 *
	 * @return  boolean  True on success.
	 */
	public function startImport()
	{
		// Get the batch state.
		$state = $this->getBatchState();

		// Get the batch offset and size.
		
		$limit = (int) ($state->batchSize - $state->batchOffset);
	
		// Get the content items to index.
		$this->importCompaniesFromCSV($state->filePath, $state->delimiter, $state->offset, $limit);

	
		return true;
	}

	/**
	 * Get the importer state.
	 *
	 */
	public function getBatchState() {
		
		// If we couldn't load from the internal state, try the session.
		$session = JFactory::getSession();
		$data = $session->get('jbd-listings-import1.state', null);

		// If the state is empty, load the values for the first time.
		if (empty($data)) {
			$data = new JObject;

			//list of types will be run incremental update
			$data->lastRun = '';

			// Set the current time as the start time.
			$data->startTime = JFactory::getDate()->toSql();

			// Set the remaining default values.
			$data->batchSize   = 100;
			$data->batchOffset = 0;
			$data->totalItems  = 0;
		}

		return $data;
	}

	/**
	 * Set the batch state.
	 *
	 */
	public function setBatchState($data) {
		// Check the state object.
		if (empty($data) || !$data instanceof JObject) {
			return false;
		}

		// Set the new session state.
		JFactory::getSession()->set('jbd-listings-import1.state', $data);

		return true;
	}


	/**
	 * Prepare the file content to be imported
	 * @param string $filePath
	 * @param string $delimiter
	 */
	public function importCompaniesFromCSV($filePath, $delimiter, $start = 0, $limit = 0) {
		$row = 1;
		$this->initializeImport();
	
		// Get the batch state.
		$state = $this->getBatchState();

		//ini_set("auto_detect_line_endings", "1");
		if (($handle = fopen($filePath, "r")) !== false) {
			while (($data = fgetcsv($handle, 9000, $delimiter)) !== false && $row<($start+$limit)) {
				$companyData = array();
				
				if ($row == 1) {
					$this->header = $data;
					$this->checkHeaders();
					$row ++;
					continue;
				}
				$num = count($data);
	
				//echo "<p> $num fields in line $row: <br /></p>\n";
				for ($c = 0; $c < $num; $c++) {
					$companyData[strtolower($this->header[$c])] = $data[$c];
				}
	
				if ($row >= $start && $row<($start+$limit) || ($start == 0 && $limit ==0)) {
					$this->importCompany($companyData, $delimiter);

					// Adjust the offsets.
					$state->offset++;
					$state->batchOffset++;
					$state->totalItems--;
				}

				$row++;
			}
		}

		$this->setBatchState($state);
	}
	
	/**
	 * Prepare the text area content to be imported
	 * @param array $csvContent
	 * @param string $delimiter
	 */
	public function importCompaniesFromTextArea($csvContent, $delimiter) {
		$row = 1;
		$this->initializeImport();
		foreach ($csvContent as $key => $content) {
			$data = str_getcsv($content, $delimiter);
	
			if ($row == 1) {
				$this->header = $data;
				$this->checkHeaders();
				$row ++;
				continue;
			}
			$num = count($data);
			for ($c = 0; $c < $num; $c++) {
				$companyData[strtolower($this->header[$c])] = $data[$c];
			}
	
			$this->importCompany($companyData, $delimiter);
			$row++;
		}
	}
	
	/**
	 * Fill the Categories, CompanyTypes, Packages and Countries
	 */
	public function initializeImport() {
		$this->categories = $this->getCategories();
		$this->companyTypes = $this->getCompanyTypes();
		$this->packages = $this->getPackagesByName();
		$this->countries = $this->getCountries();
		$this->languages = JBusinessUtil::getLanguages();
	}

	/**
	 * Check if headers on the file that will be imported are OK
	 * @return bool
	 */
	public function checkHeaders() {
		$attributesTable = JTable::getInstance("Attribute", "JTable");
		$attributes = $attributesTable->getAttributes();
		$this->tableheader = array("id", "name", "alias", "commercial_name", "website","main_subcategory","categories","registration_code", "tax_code", "type", "slogan","description",
				"work_status", "work_start_hour", "work_end_hour", "break_start_hour", "break_end_hour", "break_ids", "breaks_count", "work_ids",
				"short_description", "street_number", "address", "time_zone", "city", "region", "country","activity_radius", "website", "keywords", "phone", "mobile", "email", "fax",
				"latitude", "longitude","user", "review_score", "views","website_count", "contact_count", "package", "featured", "facebook", "twitter","recommended",
				"googlep", "skype", "linkedin", "youtube", "instagram", "pinterest", "establishment_year","employees", "min_project_size", "hourly_rate", "whatsapp", "business_hours", "custom_tab_name", "custom_tab_content", "publish_only_city",
				"postal_code", "state", "approved","contact_department", "contact_name", "contact_email", "contact_phone", "contact_fax", "logo_location","business_cover","business_ad",
				"pictures" , "meta_title", "meta_description","publish_start_date","publish_end_date","created","modified","area","province", "zipcodes");
		if (GET_DATA_FROM_YELP) {
			array_push($this->tableheader, 'yelp_id');
		}

		if (!empty($attributes)) {
			foreach ($attributes as $attribute) {
				array_push($this->tableheader, 'attribute_'.$attribute->name);
			}
		}

		foreach ($this->languages as $lng) {
			array_push($this->tableheader, "name_$lng");
			array_push($this->tableheader, "slogan_$lng");
			array_push($this->tableheader, "short_description_$lng");
			array_push($this->tableheader, "description_$lng");
			array_push($this->tableheader, "meta_title_$lng");
			array_push($this->tableheader, "meta_description_$lng");
		}

		$headerDifferences = array_diff($this->header, $this->tableheader);
		if ($headerDifferences != null) {
			foreach ($headerDifferences as $index => $diff) {
				if (strpos($diff, 'location_name_') !== false) {
					for ($i = 0; $i < 12; $i++) { //because there are 12 location fields retrieved from database on export
						unset($headerDifferences[$index + $i]);
					}
				} elseif (strpos($diff, 'contact_department_') !== false) {
					for ($i = 0; $i < 5; $i++) { //because there are 5 location fields retrieved from database on export
						unset($headerDifferences[$index + $i]);
					}
				}
			}

			if (!empty($headerDifferences)) {
				$this->headerDifferences = $headerDifferences;
				return false;
			}
		}
		return true;
	}

	public function importCategories($categories) {
		$categoryNames = array();
		$groupedCategories = explode('|', $categories);
		$model = JModelLegacy::getInstance('Category', 'JBusinessDirectoryModel', array('ignore_request' => true));
		foreach ($groupedCategories as $group) {
			$parentId = 1;
			$level = 1;
			$relatedCategories = explode('>>', $group);
			foreach ($relatedCategories as $key => $category) {
				$name = trim($category);
				$cat = $this->getCategoryByName($this->categories, $name);

				if (empty($cat) && !in_array($name, $this->savedCategories)) {
					$data = array();
					$data['id'] = 0;
					$data['name'] = $name;
					$data['alias'] = '';
					$data['parent_id'] = $parentId;
					$data['published'] = '1';
					$data['level'] = $level++;
					$data['type'] = CATEGORY_TYPE_BUSINESS;

					$result = $model->save($data);
					if ($key == count($relatedCategories)-1) {
						$categoryNames[] = $name;
					}
					$parentId = $result;
					$this->savedCategories[$result] = $name;
				} else {
					if ($key == count($relatedCategories)-1) {
						$categoryNames[] = $name;
					}

					$parentId = array_search($name, $this->savedCategories);
				}
			}
		}

		return $categoryNames;
	}

	/**
	 * prepare the data and import business listing
	 * @param $company
	 */
	public function importCompany($company, $delimiter) {
		if (isset($company["name"])) {
			$updateExisting = $this->getBatchState()->update_existing;
			if(!isset($updateExisting)){
                $updateExisting = JFactory::getApplication()->input->get("update_existing");
            }
			$categoryIds = array();
			if (!empty($company["categories"])) {
				if ($delimiter == '|') {
					$categoriesNames = $this->importCategories($company["categories"]);
				} else {
					$categoriesNames = explode(",", $company["categories"]);
				}

				foreach ($categoriesNames as $category) {
					if (empty($category)) {
						continue;
					}

					$cat = $this->getCategoryByName($this->categories, $category);
					if (!empty($cat)) {
						$categoryIds[] = $cat[0]->id;
					} elseif (in_array($category, $this->savedCategories)) {
						$categoryIds[] = array_search($category, $this->savedCategories);
					} else {
						continue;
					}

					if (!empty($company["main_subcategory"]) && $company["main_subcategory"] == $category) {
						$this->mainSubcategory = $cat[0]->id;
					}
				}
			}

			$typeId = array();
			if (!empty($company["type"])) {
				$compayTypeImport = explode(',', $company["type"]);
				foreach ($compayTypeImport as $type) {
					if (!isset($this->companyTypes[$type])) {
						$this->addCompanyType($type, count($this->companyTypes));
						$this->companyTypes = $this->getCompanyTypes();
						$this->newTypesCount++;
					}
					$typeId[] = $this->companyTypes[$type]->id;
				}
			}

			$package_id = 0;
			if (isset($company["package"])) {
				if (isset($this->packages[$company["package"]])) {
					$package_id = $this->packages[$company["package"]]->id;
				}
			}

			$countryId = 0;
			if (isset($company["country"])) {
				if (isset($this->countries[$company["country"]])) {
					$countryId = $this->countries[$company["country"]]->id;
				}
			}
			$categoryData = array();
			$categoryData["id"] = isset($company["id"])  ? $company["id"] : NULL;
			if (isset($updateExisting)) {
				$result = $this->getCompanyByName($company["name"]);
				if (isset($result)) {
					$categoryData["id"] = $result->id;
				}
			}
			$categoryData["name"] = isset($company["name"]) ? $company["name"] : NULL;
			$company["alias"] = !empty($company["alias"]) ? $company["alias"] : NULL;
			$categoryData["alias"] = JBusinessUtil::getAlias($company["name"], $company["alias"]);
			$categoryData["comercialName"] = isset($company["commercial_name"]) ? $company["commercial_name"] : null;
			$categoryData["registrationCode"] = isset($company["registration_code"]) ? $company["registration_code"] : null;
			$categoryData["taxCode"] = isset($company["tax_code"]) ? $company["tax_code"] : null;
			$categoryData["slogan"] = isset($company["slogan"]) ? $company["slogan"] : null;
			$categoryData["description"] = isset($company["description"]) ? $company["description"] : null;
			$categoryData["short_description"] = isset($company["short_description"]) ? $company["short_description"] : null;
			$categoryData["street_number"] = isset($company["street_number"]) ? $company["street_number"] : null;
			$categoryData["address"] = isset($company["address"]) ? $company["address"] : null;
			if (isset($company["address 2"])) {
				$categoryData["address"] = $categoryData["address"] . ', ' . $company["address 2"];
			}
			$categoryData["time_zone"] = isset($company["time_zone"]) ? $company["time_zone"] : null;
			$categoryData["city"] = isset($company["city"]) ? $company["city"] : null;
			$categoryData["county"] = isset($company["region"]) ? $company["region"] : null;
			$categoryData["province"] = isset($company["province"]) ? $company["province"] : null;
			$categoryData["area"] = isset($company["area"]) ? $company["area"] : null;
			$categoryData["countryId"] = $countryId;
			$categoryData["activity_radius"] = isset($company["activity_radius"]) ? $company["activity_radius"] : null;

			$categoryData["website"] = isset($company["website"]) ? $company["website"] : null;
			$categoryData["publish_start_date"] = isset($company["publish_start_date"]) ? $company["publish_start_date"] : null;
			$categoryData["publish_end_date"] = isset($company["publish_end_date"]) ? $company["publish_end_date"] : null;

			$categoryData["keywords"] = isset($company["keywords"]) ? $company["keywords"] : null;
			$categoryData["phone"] = isset($company["phone"]) ? $company["phone"] : null;
			$categoryData["mobile"] = isset($company["mobile"]) ? $company["mobile"] : null;
			$categoryData["email"] = isset($company["email"]) ? $company["email"] : null;
			$categoryData["fax"] = isset($company["fax"]) ? $company["fax"] : null;
			$categoryData["typeId"] = $typeId;
			$categoryData["mainSubcategory"] = $this->mainSubcategory;
			$categoryData["latitude"] = isset($company["latitude"]) ? $company["latitude"] : null;
			$categoryData["longitude"] = isset($company["longitude"]) ? $company["longitude"] : null;

			$categoryData["userId"] = isset($company["user"]) ? $company["user"] : null;
			$categoryData["review_score"] = isset($company["review_score"]) ? $company["review_score"] : null;
			$categoryData["viewCount"] = isset($company["views"]) ? $company["views"] : null;
			$categoryData["websiteCount"] = isset($company["website_count"]) ? $company["website_count"] : null;
			$categoryData["contactCount"] = isset($company["contact_count"]) ? $company["contact_count"] : null;
			$categoryData["filter_package"] = $package_id;
			$categoryData["facebook"] = isset($company["facebook"]) ? $company["facebook"] : null;
			$categoryData["twitter"] = isset($company["twitter"]) ? $company["twitter"] : null;
			$categoryData["googlep"] = isset($company["googlep"]) ? $company["googlep"] : null;
			$categoryData["skype"] = isset($company["skype"]) ? $company["skype"] : null;
			$categoryData["linkedin"] = isset($company["linkedin"]) ? $company["linkedin"] : null;
			$categoryData["youtube"] = isset($company["youtube"]) ? $company["youtube"] : null;
			$categoryData["instagram"] = isset($company["instagram"]) ? $company["instagram"] : null;
			$categoryData["pinterest"] = isset($company["pinterest"]) ? $company["pinterest"] : null;
			$categoryData["featured"] = isset($company["featured"]) ? $company["featured"] : null;
			$categoryData["whatsapp"] = isset($company["whatsapp"]) ? $company["whatsapp"] : null;
			$categoryData["meta_title"] = isset($company["meta_title"]) ? $company["meta_title"] : null;
			$categoryData["meta_description"] = isset($company["meta_description"]) ? $company["meta_description"] : null;
			$categoryData["establishment_year"] = isset($company["establishment_year"]) ? $company["establishment_year"] : null;
			$categoryData["employees"] = isset($company["employees"]) ? $company["employees"] : null;
			$categoryData["min_project_size"] = isset($company["min_project_size"]) ? $company["min_project_size"] : null;
			$categoryData["hourly_rate"] = isset($company["hourly_rate"]) ? $company["hourly_rate"] : null;

			$categoryData["meta_title"] = isset($company["meta_title"]) ? $company["meta_title"] : null;
			$categoryData["meta_description"] = isset($company["meta_description"]) ? $company["meta_description"] : null;

			$this->addURLHttp($categoryData);

			$categoryData["work_status"] = isset($company["work_status"]) ? explode('##', $company["work_status"]) : array();
			$categoryData["work_start_hour"] = isset($company["work_start_hour"]) ? explode('##', $company["work_start_hour"]) : array();
			$categoryData["work_end_hour"] = isset($company["work_end_hour"]) ? explode('##', $company["work_end_hour"]) : array();
			$categoryData["break_start_hour"] = isset($company["break_start_hour"]) ? explode('##', $company["break_start_hour"]) : array();
			$categoryData["break_end_hour"] = isset($company["break_end_hour"]) ? explode('##', $company["break_end_hour"]) : array();
			$categoryData["break_ids"] = isset($company["break_ids"]) ? explode('##', $company["break_ids"]) : array();
			$categoryData["breaks_count"] = isset($company["breaks_count"]) ? explode('##', $company["breaks_count"]) : array();
			$categoryData["work_ids"] = isset($company["work_ids"]) ? explode('##', $company["work_ids"]) : array();

			$categoryData["custom_tab_name"] = isset($company["custom_tab_name"]) ? $company["custom_tab_name"] : null;
			$categoryData["custom_tab_content"] = isset($company["custom_tab_content"]) ? $company["custom_tab_content"] : null;
			$categoryData["publish_only_city"] = isset($company["publish_only_city"]) ? $company["publish_only_city"] : null;

			$categoryData["postalCode"] = !empty($company["postal_code"]) ? $company["postal_code"] : null;
			$categoryData["logoLocation"] = !empty($company["logo_location"]) ? $company["logo_location"] : null;
			$categoryData["business_cover_image"] = !empty($company["business_cover"]) ? $company["business_cover"] : null;
			$categoryData["ad_image"] = !empty($company["business_ad"]) ? $company["business_ad"] : null;
			$categoryData["pictures"] = !empty($company["pictures"]) ? $company["pictures"] : null;
			$categoryData["zipcodes"] = !empty($company["zipcodes"]) ? $company["zipcodes"] : null;
			$categoryData["recommended"] = !empty($company["recommended"]) ? $company["recommended"] : null;

			if(!empty($company["created"])){
				$categoryData["creationDate"] =  $company["created"];
			}
			if(!empty($company["modified"])){
				$categoryData["modified"] =  $company["modified"];
			}

			$categoryData["yelp_id"] = !empty($company["yelp_id"]) ? $company["yelp_id"] : null;

			foreach ($this->languages as $lng) {
				$categoryData["name_".strtolower($lng)] = isset($company["name_".strtolower($lng)]) ? $company["name_".strtolower($lng)] : null;
				$categoryData["slogan_".strtolower($lng)] = isset($company["slogan_".strtolower($lng)]) ? $company["slogan_".strtolower($lng)] : null;
				$categoryData["short_description_".strtolower($lng)] = isset($company["short_description_".strtolower($lng)]) ? $company["short_description_".strtolower($lng)] : null;
				$categoryData["description_".strtolower($lng)] = isset($company["description_".strtolower($lng)]) ? $company["description_".strtolower($lng)] : null;
				$categoryData["meta_title_".strtolower($lng)] = isset($company["meta_title_".strtolower($lng)]) ? $company["meta_title_".strtolower($lng)] : null;
				$categoryData["meta_description_".strtolower($lng)] = isset($company["meta_description_".strtolower($lng)]) ? $company["meta_description_".strtolower($lng)] : null;
			}
			$categoryData["imported"] = true;

			if (!empty($categoryData["pictures"])) {
				$categoryData["pictures"] = explode(",", $categoryData["pictures"]);
				$pictures = array();

				foreach ($categoryData["pictures"] as $key => $picture) {
					$picturesData = explode('#', $picture);

					$picTmp = [];
					$picTmp['picture_title'] = $picturesData[1];
					$picTmp['picture_info'] = $picturesData[2];
					$picTmp['picture_path'] = $picturesData[0];
					$picTmp['picture_enable'] = 1;

					$pictures[] = $picTmp;
				}
				$categoryData["pictures"] = $pictures;
				$categoryData['images_included'] = 1;
			}

			if (!empty($categoryData["zipcodes"])) {
				$categoryData["zipcodes"] = explode($delimiter, $categoryData["zipcodes"]);
				$categoryData["zip_code"] = [];
				$categoryData["latitudes"] = [];
				$categoryData["longitudes"] = [];
				$categoryData["zip_code_id"] = [];
				foreach ($categoryData["zipcodes"] as $zipcode) {
					$tmp = explode('#', $zipcode);

					$categoryData["zip_code"][] = $tmp[0];
					$categoryData["latitudes"][] = $tmp[1];
					$categoryData["longitudes"][] = $tmp[2];
					$categoryData["zip_code_id"][] = "";
				}
			}

			$categoryData["contact_id"] = array();
			$categoryData["contact_department"] = array();
			$categoryData["contact_name"] = array();
			$categoryData["contact_email"] = array();
			$categoryData["contact_phone"] = array();
			$categoryData["contact_fax"] = array();

			$all_keys = array_keys($company);
			foreach ($all_keys as $index => $key) {
				if (strpos($key, 'contact_department_') !== false) {
					$contact_dep = isset($company[$key]) ? $company[$key] : null;
					$contact_nam = isset($company[$all_keys[$index+1]]) ? $company[$all_keys[$index+1]] : null;
					$contact_mail = isset($company[$all_keys[$index+2]]) ? $company[$all_keys[$index+2]] : null;
					$contact_phone = isset($company[$all_keys[$index+3]]) ? $company[$all_keys[$index+3]] : null;
					$contact_fax = isset($company[$all_keys[$index+4]]) ? $company[$all_keys[$index+4]] : null;

					$contact_dep = trim($contact_dep);
					$contact_nam = trim($contact_nam);
					$contact_mail = trim($contact_mail);
					$contact_phone = trim($contact_phone);
					$contact_fax = trim($contact_fax);

					if (!empty($contact_nam) || !empty($contact_dep) || !empty($contact_mail) || !empty($contact_phone) || !empty($contact_fax)) {
						array_push($categoryData["contact_id"], "");
						array_push($categoryData["contact_department"],$contact_dep);
						array_push($categoryData["contact_name"], $contact_nam);
						array_push($categoryData["contact_email"], $contact_mail);
						array_push($categoryData["contact_phone"], $contact_phone);
						array_push($categoryData["contact_fax"], $contact_fax);
					}
				}
			}

			$dataLocation = array();
			foreach ($all_keys as $index => $key) {
				if (strpos($key, 'location_name_') !== false) {
					$location_name = isset($company[$key]) ? $company[$key] : null;
					$location_street_number = isset($company[$all_keys[$index + 1]]) ? $company[$all_keys[$index + 1]] : null;
					$location_address = isset($company[$all_keys[$index + 2]]) ? $company[$all_keys[$index + 2]] : null;
					$location_city = isset($company[$all_keys[$index + 3]]) ? $company[$all_keys[$index + 3]] : null;
					$location_county = isset($company[$all_keys[$index + 4]]) ? $company[$all_keys[$index + 4]] : null;
					$location_postalCode = isset($company[$all_keys[$index + 5]]) ? $company[$all_keys[$index + 5]] : null;
					$location_countryId = isset($company[$all_keys[$index + 6]]) ? $company[$all_keys[$index + 6]] : null;
					$location_latitude = isset($company[$all_keys[$index + 7]]) ? $company[$all_keys[$index + 7]] : null;
					$location_longitude = isset($company[$all_keys[$index + 8]]) ? $company[$all_keys[$index + 8]] : null;
					$location_phone = isset($company[$all_keys[$index + 9]]) ? $company[$all_keys[$index + 9]] : null;
					$location_province = isset($company[$all_keys[$index + 10]]) ? $company[$all_keys[$index + 10]] : null;
					$location_area = isset($company[$all_keys[$index + 11]]) ? $company[$all_keys[$index + 11]] : null;
					if (!empty($location_name) || !empty($location_street_number) || !empty($location_address) || !empty($location_city)
							|| !empty($location_county) || !empty($location_postalCode) || !empty($location_countryId) || !empty($location_latitude)
							|| !empty($location_longitude) || !empty($location_phone) || !empty($location_province) || !empty($location_area)
					) {
						$secLocations = array();
						$secLocations["locationId"]='0';
						$secLocations["name"]= $location_name;
						$secLocations["street_number"]= $location_street_number;
						$secLocations["address"]= $location_address;
						$secLocations["city"]=$location_city;
						$secLocations["county"]=$location_county;
						$secLocations["postalCode"]=$location_postalCode;
						$secLocations["countryId"]=$location_countryId;
						$secLocations["latitude"]=$location_latitude;
						$secLocations["longitude"]=$location_longitude;
						$secLocations["phone"]=$location_phone;
						$secLocations["province"]= $location_province;
						$secLocations["area"]= $location_area;
						$secLocations["imported"] = '1';
						if (!empty($categoryData['id'])) {
							$locationTable = $this->getTable('CompanyLocations', "JTable");
							$secLocations['company_id'] = $categoryData['id'];
							$locationTable->deleteAllCompanyLocations($categoryData['id']);
						}
						$dataLocation[] = $secLocations;
					}
				}
			}

			$appSettings = JBusinessUtil::getApplicationSettings();
			if ($appSettings->limit_cities_regions == 1) {
				if (!empty($company["region"])) {
					$regionTable = $this->getTable('Region');
					$regions = explode(',', $company['region']);

					$regionIds = array();
					foreach ($regions as $region) {
						$rg = $regionTable->getRegionByName($region);
						if ($rg) {
							$regionIds[] = $rg->id;
						}
					}

					$categoryData['region_ids'] = $regionIds;
				}

				if (!empty($company["city"])) {
					$cityTable = $this->getTable('City');
					$cities = explode(',', $company['city']);

					$cityIds = array();
					foreach ($cities as $city) {
						$ct = $cityTable->getCityByName($city);
						if ($ct) {
							$cityIds[] = $ct->id;
						}
					}

					$categoryData['city_ids'] = $cityIds;
				}
			}

			$categoryData["state"] = isset($company["state"]) ? $company["state"] : 1;
			$categoryData["approved"] = isset($company["approved"]) ? $company["approved"] : 2;
			$categoryData["selectedSubcategories"] = $categoryIds;

			if (empty($categoryData["latitude"]) && empty($categoryData["longitude"]) && !empty($categoryData["postalCode"])) {
				$location = JBusinessUtil::getCoordinates($categoryData["postalCode"]);
				$categoryData["latitude"] = $location["latitude"];
				$categoryData["longitude"] = $location["longitude"];
			}

			$categoryData["no-email"] = 1;

			//load custom attributes
			$attributesTable = JTable::getInstance("Attribute", "JTable");
			$attributes = $attributesTable->getAttributesWithTypes();

			$attributesTable = JTable::getInstance("AttributeOptions", "JTable");
			$attributeOptions = $attributesTable->getAllAttributesWithOptions();
			$appSettings = JBusinessUtil::getApplicationSettings();
			foreach ($attributes as $attribute) {
				$attribute->name = 'attribute_'.strtolower($attribute->name);

				if (!empty($company[$attribute->name])) {
					$attrValues = $company[$attribute->name];
					$attrValues = explode(",", $attrValues);
					foreach ($attrValues as $value) {
						if ($attribute->attr_type == "input" || $attribute->attr_type == "textarea" || $attribute->attr_type == "link") {
							$categoryData["attribute_" . $attribute->id][] = $company[$attribute->name];
							break;
						} else {
							foreach ($attributeOptions as $attributeOption) {
								if ($appSettings->enable_multilingual) {
									$attributeOption->name = JBusinessDirectoryTranslations::getTranslatedItemName($attributeOption->name);
								}
								if ($attributeOption->attr_id == $attribute->id && $attributeOption->name == $value) {
									$categoryData["attribute_" . $attribute->id][] = $attributeOption->id;
								}
							}
						}
					}
				}
			}
			try {
				$this->setState('company.id', 0);
				$this->error_row++;
				if ($this->save($categoryData)) {
					$locationNumber = count($dataLocation);
					for ($i=0; $i<$locationNumber; $i++) {
						$this->saveLocation($dataLocation[$i]);
					}

					$companyId = !empty($categoryData['id']) ? $categoryData['id'] : $this->getState('company.id');
					if (isset($categoryData['region_ids'])) {
						foreach ($categoryData['region_ids'] as $regionId) {
							$this->storeActivityRegions($companyId, $regionId);
						}
					}

					if (isset($categoryData['city_ids'])) {
						foreach ($categoryData['city_ids'] as $cityId) {
							$this->storeActivityCities($companyId, $cityId);
						}
					}

					$this->newCompaniesCount++;
				} else {
					$failedCompany = new stdClass();
					$failedCompany->name = $categoryData["name"];
					$failedCompany->row = $this->error_row;
					array_push($this->failedCompanies, $failedCompany);
				}
			} catch (Exception $e) {
				dump($e);
			}
		} else {
			$failedCompany = new stdClass();
			$this->error_row++;
			$failedCompany->name = JText::_('LNG_UNKNOWN');
			
			$failedCompany->row = $this->error_row;
			array_push($this->failedCompanies, $failedCompany);
		}
	}

	public function importTranscripts($filePath, $delimiter) {
		 
		// Get the applicaiton settings
		$appSettings = JBusinessUtil::getApplicationSettings();
		ini_set("auto_detect_line_endings", "1");
		$count = 0;
		$row = 1;
		$data = array();
		//dump($filePath);
		if ((($handle = fopen($filePath, "r")) !== false)) {
			$data = null;
	
			while (($data = fgetcsv($handle, 9000, $delimiter)) !== false) {
				if ($row==1) {
					$header = $data;
					$row++;
					continue;
				}
				$num = count($data);
				//dump($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c=0; $c < $num; $c++) {
					$data[strtolower($header[$c])]= $data[$c];
				}
	
				
				try {
					$userId = JUserHelper::getUserId($data["userid"]);
					
					$table = $this->getTable();
					$companies = $table->getCompaniesByUserId($userId);
					if (!empty($companies)) {
						$company = $companies[0];
						if (empty($company->custom_tab_content)) {
							$company->custom_tab_content = "";
						}
						$company->custom_tab_name="Transcripts";
						$company->custom_tab_content = $company->custom_tab_content.$data["seminar"]." ".$data["created"]."\n";
						
						// Bind the data.
						if (!$table->bind($company)) {
							$this->setError($table->getError());
							dump($table->getError());
							return false;
						}
						
						// Check the data.
						if (!$table->check()) {
							$this->setError($table->getError());
							dump($table->getError());
							return false;
						}
						
						// Store the data.
						if (!$table->store()) {
							$this->setError($table->getError());
							dump($table->getError());
							return false;
						}
					}
				} catch (RuntimeException $e) {
					dump($e->getMessage());
					return false;
				}
			}
		}
	}
	
	
	public function importUsers($filePath, $delimiter) {
		
		// Get the applicaiton settings
		$appSettings = JBusinessUtil::getApplicationSettings();
		ini_set("auto_detect_line_endings", "1");
		$count = 0;
		$row = 1;
		$data = array();
		if ((($handle = fopen($filePath, "r")) !== false)) {
			$data = null;
	
			while (($data = fgetcsv($handle, 9000, $delimiter)) !== false) {
				if ($row==1) {
					$header = $data;
					$row++;
					continue;
				}
				$num = count($data);
				//dump($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c=0; $c < $num; $c++) {
					$data[strtolower($header[$c])]= $data[$c];
				}
								
				// "generate" a new JUser Object
				$user = JBusinessUtil::getUser(0); // it's important to set the "0" otherwise your admin user information will be loaded

				jimport('joomla.application.component.helper');
				$usersParams = JComponentHelper::getParams('com_users'); // load the Params
					
				$userdata = array(); // place user data in an array for storing.
				$userdata['name'] = $data["last_name"]." ".$data["first_name"];
				$userdata['email'] = $data["e-mail"];
				$userdata['display_name'] = $data["display_name"];
				//$userdata['registerDate']= strtotime("y-m-d", strtotime($data["created"]));
				//set password
				$userdata['password'] ="ooKish16@@";
				$userdata['password2'] = "ooKish16@@";
					
				//set default group.
				$usertype = $appSettings->usergroup;
				if (!$usertype) {
					$usertype = 2;  // 'Registered' ID in usergroup table is 2
				}
					
				//default to defaultUserGroup i.e.,Registered
				$userdata['groups']=array($usertype);
				$useractivation = $usersParams->get('useractivation'); 					// in this example, we load the config-setting
				
				$userdata['block'] = 0; // don't block the user
				//now to add the new user to the dtabase.
				//dump($userdata);
				try {
					if (!$user->bind($userdata)) {
						JFactory::getApplication()->enqueueMessage(JText::_($user->getError()), 'warning'); // something went wrong!!
					}
					if (!$user->save()) {
						// now check if the new user is saved
						JFactory::getApplication()->enqueueMessage(JText::_($user->getError()), 'warning'); // something went wrong!!
					}
				
					// Sanitize the date
				
					$db = JFactory::getDbo();
				
					$fields = array(
							'address1'=>$data["address"],
							'address2'=>"",
							'city'=>"",
							'region'=>"",
							'country'=>"",
							'postal_code'=>$data["postal_code"],
							'phone'=>$data["mobile_phone"],
							'website'=>"",
							'favoritebook'=>"",
							'aboutme'=>"",
							'dob'=>"",
							'tos'=>"",
					);
					
					$tuples = array();
					$order = 1;
				
					foreach ($fields as $k => $v) {
						$tuples[] = '(' . $user->ID . ', ' . $db->quote('profile.' . $k) . ', ' . $db->quote(json_encode($v)) . ', ' . ($order++) . ')';
					}
					//dump($tuples);
					$db->setQuery('INSERT INTO #__user_profiles VALUES ' . implode(', ', $tuples));
					$db->execute();
					exit;
				} catch (RuntimeException $e) {
					dump($e->getMessage());
					return false;
				}
			}
		}
	}
	
	public function getCompanyByName($companyName) {
		$companyTable = $this->getTable("Company", "JTable");
		$company = $companyTable->getCompanyByName($companyName);

		return $company;
	}
	
	public function getCategories() {
		$categoryService = new JBusinessDirectorCategoryLib();
		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$categories = $categoryTable->getAllCategories();
		$categories = $categoryService->processCategoriesByName($categories);
		return $categories;
	}
	
	public function getCategoryByName($categories, $categoryName) {
		$categoryService = new JBusinessDirectorCategoryLib();
		$cat = null;
		$category = $categoryService->findCategoryByName($categories, $cat, $categoryName);
	
		return $category;
	}
	
	public function addCompanyType($name, $ordering) {
		$table = $this->getTable("CompanyType");
	
		$type = array();
		$type["name"] = $name;
		$type["ordering"] = $ordering;
	
		if (!$table->bind($type)) {
			throw( new Exception($table->getError()) );
			$this->setError($table->getError());
		}
		// Make sure the record is valid
		if (!$table->check()) {
			throw( new Exception($table->getError()) );
			$this->setError($table->getError());
		}
	
		// Store the web link table to the database
		if (!$table->store()) {
			throw( new Exception($table->getError()) );
			$this->setError($table->getError());
		}
	
		return $table->id;
	}
	
	public function getCompanyTypes() {
		$result = array();
		$companyTypesTable = $this->getTable("CompanyTypes");
		$companyTypes = $companyTypesTable->getCompanyTypes();
		foreach ($companyTypes as $companyType) {
			$result[$companyType->name] = $companyType;
		}
	
		return $result;
	}
	
	public function getCountries() {
		$result = array();
		$countriesTable = $this->getTable("Country");
		$countries = $countriesTable->getCountries();
		foreach ($countries as $country) {
			$result[$country->country_name] = $country;
		}
		
		return $result;
	}

	public function getRegions() {
		$result = array();
		$regionsTable = $this->getTable("Region");
		$regions = $regionsTable->getRegions();
		foreach ($regions as $region) {
			$result[$region->name] = $region;
		}

		return $result;
	}
	
	public function getPackagesByName() {
		$result = array();
		$packageTable = $this->getTable("Package");
		$packages = $packageTable->getPackages();
	
		foreach ($packages as $package) {
			$result[$package->name] = $package;
		}
	
		return $result;
	}

	
	public function getLocation() {
		$locationId = JFactory::getApplication()->input->get("locationId", 0);
		// Get a menu item row instance.
		$table = $this->getTable("CompanyLocations");
		
		// Attempt to load the row.
		$return = $table->load($locationId);
		
		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}
		
		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');
		
		return $value;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function saveLocation($data) {
		if (isset($data['imported'])) {
			$id = '0';
			$data['company_id'] = isset($data["company_id"])?$data["company_id"]:(int) $this->getState('company.id');
		} else {
			$id = $data['locationId'];
		}

		if (empty($data['countryId'])) {
			$data['countryId'] = 0;
		}

		// Get a row instance.
		$table = $this->getTable("CompanyLocations");
	
		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
		}
		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}
	
		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}
	
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}
	
		return $table->id;
	}

	public function updateLocationId($companyId, $identifier) {
		$table = $this->getTable("CompanyLocations");
		return $table->updateCompanyLocations($companyId, $identifier);
	}

	public function deleteSecondaryLocation($identifier) {
		$table = $this->getTable("CompanyLocations");
		return $table->deleteCompanyLocations($identifier);
	}
	
	public function deleteLocation($locationId) {
		$table = $this->getTable("CompanyLocations");
		return $table->delete($locationId);
	}
	
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($vars, $pks, $contexts) {
		// Sanitize ids.
		$pks = array_unique($pks);
		ArrayHelper::toInteger($pks);
	
		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}
	
		if (empty($pks)) {
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
	
			return false;
		}
	
		$done = false;
	
		// Set some needed variables.
		$this->user = JBusinessUtil::getUser();
		$this->table = $this->getTable();
		$this->tableClassName = get_class($this->table);
		$this->batchSet = true;
		// Parent exists so let's proceed
		while (!empty($pks)) {
			// Pop the first ID off the stack
			$pk = array_shift($pks);
		
			$this->table->reset();
		
			// Check that the row actually exists
			if (!$this->table->load($pk)) {
				if ($error = $this->table->getError()) {
					// Fatal error
					$this->setError($error);
		
					return false;
				} else {
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}
		
			// set new approval state
			if ($vars["approval_status_id"]!="") {
				$this->table->approved = $vars["approval_status_id"];
			}
			
			// set new approval state
			if ($vars["featured_status_id"]!="") {
				$this->table->featured = $vars["featured_status_id"];
			}
			
			// set new approval state
			if ($vars["state_id"]!="") {
				$this->table->state = $vars["state_id"];
			}
		
			// Check the row.
			if (!$this->table->check()) {
				$this->setError($this->table->getError());
		
				return false;
			}
		
			// Store the row.
			if (!$this->table->store()) {
				$this->setError($this->table->getError());
		
				return false;
			}
		}
		
		// Clean the cache
		$this->cleanCache();
		
		return true;
	}
	
	/**
	 * Add http prefix if it does not exists
	 * @param unknown_type $data
	 */
	private function addURLHttp(&$data) {
		if (!empty($data['website'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['website'])) {
				$data['website'] = "https://" . $data['website'];
			}
		}
		if (!empty($data['facebook'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['facebook'])) {
				$data['facebook'] = "https://" . $data['facebook'];
			}
		}
		if (!empty($data['twitter'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['twitter'])) {
				$data['twitter'] = "https://" . $data['twitter'];
			}
		}
		if (!empty($data['googlep'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['googlep'])) {
				$data['googlep'] = "https://" . $data['googlep'];
			}
		}
		if (!empty($data['linkedin'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['linkedin'])) {
				$data['linkedin'] = "https://" . $data['linkedin'];
			}
		}
		
		if (!empty($data['youtube'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['youtube'])) {
				$data['youtube'] = "https://" . $data['youtube'];
			}
		}
		if (!empty($data['instagram'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['instagram'])) {
				$data['instagram'] = "https://" . $data['instagram'];
			}
		}
		if (!empty($data['pinterest'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['pinterest'])) {
				$data['pinterest'] = "https://" . $data['pinterest'];
			}
		}
	}


	public function associateUserGroup($user_id, $group_id) {
		return JUserHelper::addUserToGroup($user_id, $group_id);
	}

	/**
	 *  This function collect all the data results for the import process
	 * @return stdClass The status for the import
	 */
	public function getImportStatus() {
		$result = new stdClass();
		$result->differences = $this->headerDifferences;
		$result->correctHeader = $this->tableheader;
		$result->failedCompanies = $this->failedCompanies;
		$result->newSubCategories = $this->newSubcategoryCount;
		$result->newTypes = $this->newTypesCount;
		$result->newCompanies = $this->newCompaniesCount;
		$result->statusTitle = "";
		$result->statusMessage = "";
		$result->error = false;
 
		if ($result->newSubCategories) {
			$message = JText::plural('COM_JBUSINESS_DIRECTORY_N_SUB_CATEGORIES_IMPORTED', $result->newSubCategories);
			JFactory::getApplication()->enqueueMessage($message, 'success');
			$result->statusMessage = $message."<br/>";
			$result->error = false;
		}
		if ($result->newTypes) {
			$message = JText::plural('COM_JBUSINESS_DIRECTORY_N_TYPES_IMPORTED', $result->newTypes);
			JFactory::getApplication()->enqueueMessage($message, 'success');
			$result->statusMessage = $message."<br/>";
			$result->error = false;

		}

		if (!empty($result->differences)) {
			$msg = JText::_('LNG_NOT_RECOGNIZED_HEADERS')."<br />".implode(", ", $result->differences);
			JFactory::getApplication()->enqueueMessage($msg, 'warning');
			
			$result->statusMessage = $msg."<br/><br/>";
			$result->error = true;

			$msg = JText::_('LNG_ALLOWED_HEADER')."<br />".implode(", ", $result->correctHeader);
			$msg .= "<br /><br />";
			JFactory::getApplication()->enqueueMessage($msg, 'success');
			

		}

		if (!empty($result->failedCompanies)) {
			$msg = JText::_('LNG_IMPORT_FAILED_FOR');
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_IMPORT_FAILED_FOR'), 'warning');
			foreach ($result->failedCompanies as $item) {
				$msg = $msg.JFactory::getApplication()->enqueueMessage(JText::_('LNG_ROW') . " " . $item->row . "  " . JText::_('LNG_COMPANY_NAME') . " " . $item->name, 'warning');
			}
			$result->statusMessage .= $msg."<br/>";
			$result->error = true;
		}

		if ($result->newCompanies) {
			$message = JText::plural('COM_JBUSINESS_DIRECTORY_N_COMPANIES_IMPORTED', $result->newCompanies);
			JFactory::getApplication()->enqueueMessage($message, 'success');
			$result->statusMessage = $message."<br/>";
		}

		return $result;
	}

	/**
	 * Method that stores in the database the working hours assigned for
	 * a certain business.
	 *
	 * @param $companyId int ID of the company
	 * @param $data array array containing all the post data including the working hours info
	 * @return bool false if there's an error while storing the info, true otherwise
	 */
	public function storeWorkingHours($companyId, $data) {
		$workStartHours = isset($data["work_start_hour"])?$data["work_start_hour"]:array();
		$workEndHours = isset($data["work_end_hour"])?$data["work_end_hour"]:array();
		$workIds = isset($data["work_ids"])?$data["work_ids"]:array();
		$workStatuses = isset($data["work_status"])?$data["work_status"]:array();

		$hasStartHours = false;
		foreach ($workStartHours as $hour) {
			if (!empty($hour)) {
				$hasStartHours = true;
			}
		}

		$hasEndHours = false;
		foreach ($workEndHours as $hour) {
			if (!empty($hour)) {
				$hasEndHours = true;
			}
		}

		if (!$hasStartHours && !$hasEndHours) {
			$workIds = array();
		}

		if (!$this->deleteTimePeriods($workIds, $companyId, STAFF_WORK_HOURS)) {
			return false;
		}

		if (!empty($workIds)) {
			$table = $this->getTable('CompanyServiceProviderHours');
			foreach ($workIds as $key => $value) {
				$table->id = 0;
				if (!empty($workIds[$key])) {
					$table->id = $workIds[$key];
				}
				$table->start_hour = JBusinessUtil::convertTimeToMysqlFormat($workStartHours[$key]);
				$table->end_hour = JBusinessUtil::convertTimeToMysqlFormat($workEndHours[$key]);
				$table->type = STAFF_WORK_HOURS;
				$table->item_type = BUSINESS_HOURS;
				$table->weekday = $key + 1;
				$table->status = isset($workStatuses[$key]) ? (int)$workStatuses[$key] : 0;
				$table->provider_id = $companyId;
				if (!$table->store()) {
					$application = JFactory::getApplication();
					$application->enqueueMessage($table->getError(), 'error');
					return false;
				}

				// check if there are any time periods where the end time is lower than the start time
				if (!empty($workStartHours[$key]) || !empty($workEndHours[$key])) {
					if (strtotime($workStartHours[$key]) >= strtotime($workEndHours[$key])) {
						$this->work_hours_errors = true;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Method that stores in the database the break hours assigned for
	 * a certain company.
	 *
	 * @param $companyId int ID of the company
	 * @param $data array array containing all the post data including the break hours info
	 * @return bool false if there's an error while storing the info, true otherwise
	 */
	public function storeBreakingHours($companyId, $data) {
		$breakStartHours = isset($data["break_start_hour"])?$data["break_start_hour"]:array();
		$breakEndHours = isset($data["break_end_hour"])?$data["break_end_hour"]:array();
		$breakIds = isset($data["break_ids"])?$data["break_ids"]:array();
		$breakCount = isset($data["breaks_count"])?$data["breaks_count"]:array();

		if (!$this->deleteTimePeriods($breakIds, $companyId, STAFF_BREAK_HOURS)) {
			return false;
		}

		$table = $this->getTable('CompanyServiceProviderHours');

		$i = 1;
		$weekday = 0;
		if (!empty($breakStartHours) && !empty($breakStartHours[0])) {
			foreach ($breakStartHours as $key => $value) {
				$table->id = 0;
				if (!empty($breakIds[$key])) {
					$table->id = $breakIds[$key];
				}
				$table->start_hour = JBusinessUtil::convertTimeToMysqlFormat($breakStartHours[$key]);
				$table->end_hour = JBusinessUtil::convertTimeToMysqlFormat($breakEndHours[$key]);
				$table->type = STAFF_BREAK_HOURS;
				$table->item_type = BUSINESS_HOURS;
	
				// if there are multiple break periods for a day, assign it to that day
				if ($i <= $breakCount[$weekday]) {
					$table->weekday = $weekday + 1;
					$i++;
				}
				// if the break count for a particular day is exceeded, search for the next day with
				// at least one break and assign the period to that day
				else {
					// reset the $i counter and increment the weekday to represent the next day
					$i = 1;
					$weekday++;
	
					// search for the next day with at least one break in it
					while ($i > $breakCount[$weekday]) {
						$weekday++;
					}
	
					// assign the day to the table (+1 cause the weekday index varies from 0-6, and we need it from 1-7 in the DB)
					$table->weekday = $weekday + 1;
					$i++;
				}
				// if the work day is disabled, disable the break periods also
				$table->status = $data['work_status'][$weekday];
				$table->provider_id = $companyId;
				if (!$table->store()) {
					$application = JFactory::getApplication();
					$application->enqueueMessage($table->getError(), 'error');
					return false;
				}
	
				// check if there are any time periods where the end time is lower than the start time
				if (!empty($breakStartHours[$key]) || !empty($breakEndHours[$key])) {
					if (strtotime($breakStartHours[$key]) >= strtotime($breakEndHours[$key])) {
						$this->break_hours_errors = true;
					}
				}
			}
		}
		
		return true;
	}

	/**
	 * Method that deletes a set of time period Ids form the database that belong to
	 * a particular company.
	 *
	 * @param $periodIds string concatenated values of the period ids
	 * @param $companyId int company Id
	 * @param $type int period type, work or break hours
	 * @return bool false if there's an error while deleting the records, true otherwise
	 */
	public function deleteTimePeriods($periodIds, $companyId, $type) {
		if (is_array($periodIds)) {
			$periodIds = array_filter($periodIds);
		}
		if (!empty($periodIds)) {
			$ids = implode(',', $periodIds);
		} else {
			$ids="";
		}

		$rowOpt = $this->getTable('CompanyServiceProviderHours', 'JTable');

		if ($rowOpt->deleteTimePeriods($companyId, $ids, $type, BUSINESS_HOURS)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($rowOpt->getError(), 'error');
			return false;
		}
		return true;
	}

	/**
	 * Method that returns the working periods. If no working hours are present on the
	 * hours table, it will check the companies table, retrieve the hours from there and
	 * convert them to the new format.
	 *
	 * @return mixed
	 */
	private function getWorkingHours($companyId = null) {
		$table = $this->getTable('CompanyServiceProviders', 'JTable');

		$companyId = !empty($companyId)?$companyId:$this->getState('company.id');

		if (empty($companyId)) {
			return array();
		}

		$workingHours = $table->getStaffTimetable($companyId, STAFF_WORK_HOURS, BUSINESS_HOURS);

		// if no working hours are set, check the old business hours
		if (empty($workingHours)) {
			$companyId = $companyId;
			if (empty($companyId)) {
				return $workingHours;
			}

			$companyTable = $this->getTable("Company");
			$company = $companyTable->getCompany($companyId);

			// convert the old business hours to the new format
			if (!empty($company->business_hours)) {
				$openingHours = explode(",", $company->business_hours);

				for ($i=0; $i<7; $i++) {
					$tmp = new stdClass();
					$tmp->startHours = $openingHours[$i*2];
					$tmp->endHours = $openingHours[$i*2+1];
					$tmp->statuses = 1;
					$tmp->periodIds = '';

					if ($tmp->startHours == "closed" || $tmp->endHours == "closed") {
						$tmp->startHours = '';
						$tmp->endHours = '';
					}

					$workingHours[$i] = $tmp;
				}
			}
		}

		return $workingHours;
	}

	/**
	 * Method that returns the break periods
	 *
	 * @return mixed
	 */
	private function getBreakHours($companyId = null) {
		$table = $this->getTable('CompanyServiceProviders', 'JTable');

		$companyId = !empty($companyId)?$companyId:$this->getState('company.id');

		if (empty($companyId)) {
			return array();
		}

		$result = $table->getStaffTimetable($companyId, STAFF_BREAK_HOURS, BUSINESS_HOURS);
		$breakHours = array();
		foreach ($result as $hours) {
			$breakHours[$hours->weekday] = $hours;
		}

		return $breakHours;
	}

	/**
	 * Method that returns a complex array organized in a way that it may
	 * be simply used in the view.
	 *
	 * @return array
	 */
	public function getWorkingDays($companyId = null) {
		$workHours = $this->getWorkingHours($companyId);
		$breakHours = $this->getBreakHours($companyId);
		$workingDays = JBusinessUtil::getWorkingDays($workHours, $breakHours);

		return $workingDays;
	}

	public function getOpeningStatusOptions() {

		$options       = array();
		$option        = new stdClass();
		$option->value = COMPANY_OPEN_BY_TIMETABLE;
		$option->text  = JTEXT::_("LNG_BY_TIMETABLE");
		$options[]     = $option;
		$option        = new stdClass();
		$option->value = COMPANY_ALWAYS_OPEN;
		$option->text  = JTEXT::_("LNG_ALWAYS_OPEN");
		$options[]     = $option;
		$option        = new stdClass();
		$option->value = COMPANY_TEMP_CLOSED;
		$option->text  = JTEXT::_("LNG_TEMPORARILY_CLOSED");
		$options[]     = $option;
		$option        = new stdClass();
		$option->value = COMPANY_OPEN_BY_APPOINTMENT;
		$option->text  = JTEXT::_("LNG_OPEN_BY_APPOINTMENT");
		$options[]     = $option;
		$option        = new stdClass();
		$option->value = COMPANY_SEASON_CLOSED;
		$option->text  = JTEXT::_("LNG_COMPANY_SEASON_CLOSED");
		$options[]     = $option;
		$option        = new stdClass();
		$option->value = COMPANY_PERMANENTLY_CLOSED;
		$option->text  = JTEXT::_("LNG_PERMANENTLY_CLOSED");
		$options[]     = $option;
		
		return $options;
	}

	/**
	 * Method that returns a complex array organized in a way that it may
	 * be simply used in the view.
	 *
	 * @return array
	 */
	public function getMembershipOptions() {
		$table = $this->getTable('Memberships', 'Table');
		$result = $table->getAllMemberships();

		return $result;
	}

	/**
	 * Method that retrieves custom attributes based on a certain category in order to be
	 * rendered in HTML. If packages are enabled, it will retrieve the current company package
	 * to check whether a certain attribute belongs to the package feature.
	 * If multilingual is also enabled, it will translate the attributes.
	 *
	 * @param $categoryId int ID of the category
	 * @param $companyId int ID of the company
	 * @param $packageId int ID of the package
	 *
	 * @return string html output
	 */
	public function getAttributesAjax($categoryId, $companyId, $packageId) {
		$attributesTable = $this->getTable('Attribute');
		$customFields = $attributesTable->getAttributesByCategory($categoryId, ATTRIBUTE_TYPE_BUSINESS, $companyId);

		foreach ($customFields as $val) {
			if (!isset($val->attributeValue)) {
				$val->attributeValue = '';
			}
		}

		if (!empty($customFields)) {
			// get the package of the company in order to render only the allowed attributes
			if ($this->appSettings->enable_packages) {
				if ($packageId != 0) {
					$package = $this->getPackage($packageId);
				} else {
					$package = $this->getDefaultPackage();
				}

			}

			if ($this->appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateAttributesTranslation($customFields);
			}

			$packageFeatures = !empty($package->features) ? $package->features : null;
			$renderedContent = AttributeService::renderAttributes($customFields, $this->appSettings->enable_packages, $packageFeatures);
		} else {
			$renderedContent = null;
		}


		return $renderedContent;
	}
	
	/**
	 * Send creation notification email for selected business listings.
	 */
	public function sendNotificationEmail(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);
		
		// Get a group row instance.
		$table = $this->getTable();
		
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			$table->load($itemId);
			$properties = $table->getProperties(1);
			$company = ArrayHelper::toObject($properties, 'JObject');
			if (!EmailService::sendNewCompanyNotificationEmailToOwner($company)) {
				$this->setError("Internal error!");
				return false;
			}
		}
		
		return true;
	}
	/**
	 * Add missing link prefix
	 *
	 * @param unknown $data
	 */
	public static function processLinks(&$data) {
		if (!empty($data['website'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['website'])) {
				$data['website'] = "http://" . $data['website'];
			}
			$data['website'] =  str_replace(array('\'', '"'), '', $data['website']);
		}
		if (!empty($data['facebook'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['facebook'])) {
				$data['facebook'] = "http://" . $data['facebook'];
			}
			$data['facebook'] =  str_replace(array('\'', '"'), '', $data['facebook']);
		}
		if (!empty($data['twitter'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['twitter'])) {
				$data['twitter'] = "http://" . $data['twitter'];
			}
			$data['twitter'] =  str_replace(array('\'', '"'), '', $data['twitter']);
		}
		if (!empty($data['googlep'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['googlep'])) {
				$data['googlep'] = "http://" . $data['googlep'];
			}
			$data['googlep'] =  str_replace(array('\'', '"'), '', $data['googlep']);
		}
		if (!empty($data['linkedin'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['linkedin'])) {
				$data['linkedin'] = "http://" . $data['linkedin'];
			}
			$data['linkedin'] =  str_replace(array('\'', '"'), '', $data['linkedin']);
		}
		
		if (!empty($data['youtube'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['youtube'])) {
				$data['youtube'] = "http://" . $data['youtube'];
			}
			$data['youtube'] =  str_replace(array('\'', '"'), '', $data['youtube']);
		}
		if (!empty($data['instagram'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['instagram'])) {
				$data['instagram'] = "http://" . $data['instagram'];
			}
			$data['instagram'] =  str_replace(array('\'', '"'), '', $data['instagram']);
		}
		if (!empty($data['pinterest'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['pinterest'])) {
				$data['pinterest'] = "http://" . $data['pinterest'];
			}
			$data['pinterest'] =  str_replace(array('\'', '"'), '', $data['pinterest']);
		}
	}

	/**
	 * Set the package id to a company
	 *
	 * @param unknown $companyId
	 * @param unknown $packageId
	 */
	public function setPackageId($companyId, $packageId) {
		$table = $this->getTable();
		$result = $table->setPackageId($companyId, $packageId);
		
		return $result;
	}

	/**
	 * Retrive the extra pictures for a business listing
	 * @param unknown_type $companyId
	 * @return multitype:multitype:NULL
	 */
	public function getCompanyExtraPictures($companyId) {
		$query = "SELECT * FROM #__jbusinessdirectory_company_pictures_extra
				WHERE companyId =".$companyId ."
				ORDER BY id ";
		$files =  $this->_getList($query);
		$pictures = array();
		foreach ($files as $value) {
			$pictures[] = array(
				'id' => $value->id,
				'image_title' => $value->image_title,
				'image_info' => $value->image_info,
				'image_path' => $value->image_path,
				'image_enable' => $value->image_enable,
			);
		}
	
		return $pictures;
	}

	/**
	 * Submit review for the profile
	 */
	function submitReview(){
		$companyId = $this->getState('company.id');
		$table = $this->getTable();
		$table->load($companyId);

		$properties = $table->getProperties(1);
		$company = ArrayHelper::toObject($properties, 'JObject');
		
		$user = JBusinessUtil::getUser($table->userId);
		
		$password = JUserHelper::genRandomPassword(8);
		$user->password = JUserHelper::hashPassword($password);
		$user->block = 0;
		$user->save();

		$user->password = $password;

		$orderTable= $this->getTable("Order");
		$lastUnpaidOrder = $orderTable->getLastUnpaidOrder($companyId, null);
		
		$packageTable = $this->getTable("Package");
		if(!empty($lastUnpaidOrder)){
			$package = $packageTable->getPackage($lastUnpaidOrder->package_id);	
		}else{
			$package = $this->getDefaultPackage();
		}

		// if($package->allow_free_trial){
		// 	if(!empty($lastUnpaidOrder)){
		// 		$lastUnpaidOrder->only_trial = 1;
		// 		$orderTable->bind($lastUnpaidOrder);
		// 		$orderTable->store();
		// 	}
		// 	EmailService::sendListingReviewResultFreeTrialEmail($user, $company);
		// }else{
		// 	EmailService::sendListingReviewResultEmail($user, $company);
		// }

		//change review status
		$table->review_status = 1;
		$table->store();

		return true;
	}
}
