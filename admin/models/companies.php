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
require_once(BD_HELPERS_PATH.'/category_lib.php');
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');
require_once(BD_HELPERS_PATH.'/fpdf_helper.php');


/**
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelCompanies extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'bc.id',
				'name', 'bc.name',
				'display_name', 'u.display_name',
				'websiteCount', 'bc.websiteCount','category_name',
				'email', 'bc.email',
				'address', 'bc.address',
				'type', 'ct.name',
				'package', 'bc.package_id',
				'subscription', 'sb.status',
				'eventCount', 'COUNT(DISTINCT ev.id)',
				'offerCount', 'COUNT(DISTINCT o.id)',
				'reviewCount', 'COUNT(DISTINCT re.id)',
				'viewCount', 'bc.viewCount',
				'ordering', 'bc.ordering',
				'contactCount', 'bc.contactCount',
				'state', 'bc.state','bc.modified','bc.featured',
				'approved', 'bc.approved','p.name','active'
			);
		}

		parent::__construct($config);
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
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		$items = JBusinessUtil::processPackages($items);
		$items = SubscriptionService::processSubscriptions($items);

		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		}
		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery($total = false) {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();

		// Create a new query object.
		$db = $this->getDbo();

		$typeSelect = '';
		$typeJoin = '';
		if ($this->getState('filter.type_id')) {
			$typeSelect = ' ,GROUP_CONCAT(distinct ct.name) as typeName';
			$typeJoin = ' LEFT JOIN `#__jbusinessdirectory_company_types` AS ct ON find_in_set(ct.id,bc.typeId)';
		}
		
		$query = "SELECT bc.* $typeSelect ,GROUP_CONCAT( DISTINCT cpt.picture_path) as pictures, clm.id as claimId, u.display_name as username, cat.name as category_name, cnt.contact_name,";
		if($total){
			$query = "SELECT bc.id";
		}

		if(!$total){
			$query .= "GROUP_CONCAT(distinct inv.start_date,'|',IFNULL(inv.start_trial_date, ''),'|',inv.state,'|',inv.package_id,'|',inv.id,'|',inv.company_id,'|',inv.end_date,'|',inv.amount,'|',IFNULL(inv.trial_amount, '')  separator '#|') as orders";
		}
		
		if ($this->getState('list.show_advanced_list') && !$total) {
			$query .= ",COUNT(DISTINCT ev.id) as eventCount, COUNT(DISTINCT o.id) as offerCount, COUNT(DISTINCT re.id) as reviewCount";

			$query .= "
					,websiteCnt.websiteCounts
					";
		}

		if ($appSettings->enable_packages && !$total) {
			$query .= " ,sb.id as sub_id, sb.status as sub_status, sb.subscription_id as sub_subscription_id, sb.time_unit as sub_time_unit, sb.time_amount as sub_time_amount, sb.processor_type as sub_processor_type, sb.created as sub_created,
						ord.payment_date as sub_payment_date, ord.payment_status as sub_payment_status, ord.order_id as sub_order_id";
		}
				
		$query .= "	FROM `#__jbusinessdirectory_companies` AS bc
					$typeJoin
					LEFT JOIN `#__jbusinessdirectory_company_claim` AS clm ON bc.id=clm.companyId
					LEFT JOIN `#__users` u on bc.userId=u.id
					left join `#__jbusinessdirectory_orders` inv on inv.company_id = bc.id 
                    ";

		if(!$total){
			$query .= "	left join `#__jbusinessdirectory_categories` cat on bc.mainSubcategory=cat.id ";
			$query .= "	left join #__jbusinessdirectory_company_contact cnt on bc.id=cnt.companyId  ";
			$query .= "	left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=bc.id and cpt.picture_enable = 1 ";
		}

		if ($this->getState('filter.category_id')) {
			$query .= "	left join `#__jbusinessdirectory_company_category` cc on bc.id=cc.companyId ";
		}
		
		if ($this->getState('list.show_advanced_list') && !$total) {
			$query .= "	LEFT JOIN `#__jbusinessdirectory_company_events` ev on ev.company_id=bc.id
                		LEFT JOIN `#__jbusinessdirectory_company_offers` o on o.companyId=bc.id
                		LEFT JOIN `#__jbusinessdirectory_company_reviews` re on re.itemId=bc.id ";
			$query .= "

						left join 
						( select sum(starchWebsite.item_count) as websiteCounts, item_id
							from `#__jbusinessdirectory_statistics_archive` starchWebsite where starchWebsite.item_type = '".STATISTIC_ITEM_BUSINESS."' and starchWebsite.type='".STATISTIC_TYPE_WEBSITE_CLICK."'   group by item_id
						) as websiteCnt on websiteCnt.item_id = bc.id 
					";
		}

		if ($appSettings->enable_packages && !$total) {
			$query .= " LEFT JOIN (
						select * 
						from #__jbusinessdirectory_subscriptions
						where status != ".SUBSCRIPTION_STATUS_CANCELED."
						order by id desc
						) as sb on sb.company_id = bc.id ";

			$query .= " LEFT JOIN (
						  select inv.*, p.payment_date, p.payment_status
						  from #__jbusinessdirectory_orders as inv
						  LEFT JOIN (
							  select p.*
							  from #__jbusinessdirectory_payments as p
							  where p.payment_status = ".PAYMENT_STATUS_PAID."
							  order by p.payment_id desc
						  ) as p on p.order_id = inv.id
						  order by inv.id desc
					   ) as ord on ord.subscription_id = sb.id ";
		}
		
		$where = " where 1 ";
		// Filter by search in title.
		$search = $this->getState('filter.search');
		$search = trim((string)$search);
		
		$keywords = array();
		if (strpos($search, '"') === false) {
			$keyword = $db->escape($search);
			$keywords = explode(" ", $keyword);
		} else {
			$keyword = trim($search, '"');
			$keyword = $db->escape($keyword);
			$keywords = array($keyword);
		}
		
		if (!empty($search)) {
			if (stripos($search, 'r:') === 0) {
				$where.=' and bc.registrationCode = '.(int) substr($search, 2);
			} elseif (stripos($search, 'u:') === 0) {
				$search = str_replace("u:", "", $search);
				$where.=" and u.display_name = '".$db->escape($search)."'";
			} elseif (stripos($search, 'id:') === 0) {
				$search = str_replace("id:", "", $search);
				$where.=" and bc.id = '".$db->escape($search)."'";
			} else {
				$where.=" and (bc.name LIKE '%". implode("%' and bc.name LIKE '%", $keywords) ."%' or
							bc.city LIKE '%". implode("%' and bc.city LIKE '%", $keywords) ."%' )";
			}
		}
		
		$typeId = $this->getState('filter.type_id');
		if (is_numeric($typeId)) {
			$where.=" and find_in_set(".$typeId.",bc.typeId)>0";
		}

		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$categoryService = new JBusinessDirectorCategoryLib();
			$categoriesIds = $categoryService->getCategoryLeafs($categoryId, CATEGORY_TYPE_BUSINESS);

			if (!empty($categoriesIds)) {
				$categoriesIds[] = $categoryId;
			} else {
				$categoriesIds = array($categoryId);
			}
			
			$categoriesIds = implode(",", $categoriesIds);
				
			$where.=" and cc.categoryId in ($categoriesIds)";
		}
		
		$statusId = $this->getState('filter.status_id');
		if ($statusId == COMPANY_STATUS_CLAIMED_APPROVED) {
			$statusId = COMPANY_STATUS_APPROVED;
			$where.=" and clm.id is not null";
		}

		if (is_numeric($statusId)) {
			$where.=" and bc.approved =".(int) $statusId;
		}
		
		$stateId = $this->getState('filter.state_id');
		if (is_numeric($stateId)) {
			$where.=' and bc.state ='.(int) $stateId;
		}

		if ($appSettings->enable_packages && !$total){
			$where .= ' and (sb.end_date IS NULL or sb.end_date <= CURDATE())';
		}

		// $actions = JBusinessDirectoryHelper::getActions();
		// if($actions->get('directory.access.listing.onlyuser')){
		// 	$userId = JBusinessUtil::getUser()->ID;
		// 	$where .= " and (bc.created_by = $userId && bc.approved = ".COMPANY_STATUS_CREATED.")";
		// }

		$groupBy = " group by bc.id";

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'bc.ordering');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$orderBy = " order by ". $db->escape($orderCol) . ' ' . $db->escape($orderDirn);

		$query = $query.$where;
		$query = $query.$groupBy;
		$query = $query.$orderBy;

		//dump($query);
		//exit;

		return $query;
	}


	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since   1.6
	 */
	public function getTotal()
	{
		
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		try
		{
			// Load the total and add the total to the internal cache.
			$this->cache[$store] = (int) $this->_getListCount($this->getListQuery(true));
			
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = "bc.id", $direction = "desc") {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$typeId = $app->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
		$this->setState('filter.type_id', $typeId);
		
		$statusId = $app->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
		$this->setState('filter.status_id', $statusId);

		$stateId = $app->getUserStateFromRequest($this->context.'.filter.state_id', 'filter_state_id');
		$this->setState('filter.state_id', $stateId);

		//Category filter
		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);
				
		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	public function getCompanyTypes() {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyTypes();
	}
		
	public function getStates() {
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
	
	public function getStatuses() {
		$statuses = array();
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CLAIMED;
		$status->text = JTEXT::_("LNG_NEEDS_CLAIM_APROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CREATED;
		$status->text = JTEXT::_("LNG_NEEDS_CREATION_APPROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_DISAPPROVED;
		$status->text = JTEXT::_("LNG_DISAPPROVED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_APPROVED;
		$status->text = JTEXT::_("LNG_APPROVED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CLAIMED_APPROVED;
		$status->text = JTEXT::_("LNG_CLAIM_APPROVED");
		$statuses[] = $status;
	
		return $statuses;
	}
	
	
	public function exportCompaniesCSV() {
		
		$fileName = "jbusinessdirectory_business_listing";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$fileName.".csv");

		$this->sendCompaniesCSV();
	}
	
	public function sendCompaniesCSV() {
		$jinput    = JFactory::getApplication()->input;
		$delimiter = $jinput->getString("delimiter", ",");
		$category  = $jinput->post->getArray()['category'];
		$languages = JBusinessUtil::getLanguages();
		$includeId = $jinput->get("include_id");

		$companyTable = $this->getTable();
		$categoriesIds="";

		if (!empty($category)) {
			$categoriesIds = array();
			$categoryService = new JBusinessDirectorCategoryLib();
			$categoriesIds = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_BUSINESS);
			
			if (!empty($category)) {
				if (isset($categoriesIds) && count($categoriesIds) > 0) {
					$categoriesIds[] = $category;
				} else {
					$categoriesIds = array($category);
				}
			}
			$categoriesIds = implode(",", $categoriesIds);
		}
		
		$companyZipcodesTable = $this->getTable('CompanyZipcode', "Table");
		$attributesTable = JTable::getInstance("Attribute", "JTable");
		$companyAttributesTable = JTable::getInstance("CompanyAttributes", "JTable");
		$contactTable = $this->getTable('CompanyContact', "Table");
		$maxContacts = $contactTable->getMaxContactNumberOnCompany();
		$locationTable = $this->getTable('CompanyLocations', "JTable");
		$maxLocations = $locationTable->getMaxLocationNumberOnCompany();
		$attributes = $attributesTable->getAttributes(ATTRIBUTE_TYPE_BUSINESS);
		$companyModel = JModelLegacy::getInstance('Company', 'JBusinessDirectoryModel', array('ignore_request' => true));

		$csv_output = "";
		$name = "name".$delimiter;
		$slogan = "slogan".$delimiter;
		$shortDesc = "short_description".$delimiter;
		$desc = "description".$delimiter;
		$metaName = "meta_title".$delimiter;
		$metaDesc = "meta_description".$delimiter;
		foreach ($languages as $lng) {
			$name.="name_$lng".$delimiter;
			$slogan.="slogan_$lng".$delimiter;
			$shortDesc.="short_description_$lng".$delimiter;
			$desc.="description_$lng".$delimiter;
			$metaName.="meta_title_$lng".$delimiter;
			$metaDesc.="meta_description_$lng".$delimiter;
		}
		$metaDesc = rtrim($metaDesc, $delimiter);

		if (isset($includeId)) {
			$csv_output .= "id".$delimiter; 
		}

		$csv_output .= $name . "alias".$delimiter."commercial_name".$delimiter."tax_code".$delimiter."registration_code".$delimiter
			."establishment_year".$delimiter."employees".$delimiter."min_project_size".$delimiter."hourly_rate".$delimiter."website".$delimiter."website_count".$delimiter."type".$delimiter."keywords".$delimiter. $slogan . $shortDesc .$desc ."categories".$delimiter ."main_subcategory".$delimiter ."time_zone".$delimiter
			."work_status".$delimiter."work_start_hour".$delimiter."work_end_hour".$delimiter."break_start_hour".$delimiter."break_end_hour".$delimiter."break_ids".$delimiter."breaks_count".$delimiter."work_ids".$delimiter
			."street_number".$delimiter."address".$delimiter."area".$delimiter."country".$delimiter."city".$delimiter
			."province".$delimiter."region".$delimiter."postal_code".$delimiter."publish_only_city".$delimiter."latitude".$delimiter
			."longitude".$delimiter."activity_radius".$delimiter."recommended".$delimiter;

		for ($i=1; $i<= $maxLocations; $i++) {
			$csv_output = $csv_output."location_name_".$i.$delimiter."location_street_number_".$i.$delimiter."location_address_".$i.$delimiter
				."location_city_".$i.$delimiter."location_region_".$i.$delimiter."location_postalCode_".$i.$delimiter."location_countryId_".$i.$delimiter
				."location_latitude_".$i.$delimiter."location_longitude_".$i.$delimiter."location_phone_".$i.$delimiter."location_province_".$i.$delimiter
				."location_area_".$i.$delimiter;
		}

		for ($i=1; $i<= $maxContacts; $i++) {
			$csv_output = $csv_output."contact_department_".$i.$delimiter."contact_name_".$i.$delimiter."contact_email_".$i.$delimiter."contact_phone_".$i.$delimiter
				."contact_fax_".$i.$delimiter;
		}

		$csv_output = $csv_output."phone".$delimiter."mobile".$delimiter."email".$delimiter."fax".$delimiter."facebook".$delimiter."twitter".$delimiter
			."googlep".$delimiter."skype".$delimiter."linkedin".$delimiter."youtube".$delimiter."instagram".$delimiter."pinterest".$delimiter."whatsapp".$delimiter
			."logo_location".$delimiter."business_cover".$delimiter."business_ad".$delimiter."pictures".$delimiter."user".$delimiter."review_score".$delimiter."views".$delimiter
			."contact_count".$delimiter."package".$delimiter."custom_tab_name".$delimiter."custom_tab_content".$delimiter."state".$delimiter."approved".$delimiter
			."modified".$delimiter."publish_start_date".$delimiter."publish_end_date".$delimiter."created".$delimiter."featured".$delimiter . $metaName . $metaDesc . $delimiter. "zipcodes";

		if (!empty($attributes)) {
			foreach ($attributes as $attribute) {
				$csv_output = $csv_output.$delimiter."attribute_".$attribute->name;
			}
		}

		$csv_output = $csv_output."\n";

		$categoryTable = JTable::getInstance("Category", "JBusinessTable");

		$appSettings = JBusinessUtil::getApplicationSettings();

		$start = 0;
		$batch = ITEMS_BATCH_SIZE;
		do {
			$companies = $companyTable->getCompaniesForExport($categoriesIds, $start, $batch);
			if (count($companies) > 0) {
				foreach ($companies as $company) {
					$weekDays = $companyModel->getWorkingDays($company->id);

					if ($appSettings->limit_cities_regions) {
						$company->city   = $company->cities;
						$company->county = $company->regions;
					}

					$company->contact = $contactTable->getAllCompanyContacts($company->id);
					$company->secLocations = $locationTable->getAllCompanyLocations($company->id);
					if (!empty($company->mainSubcategory)) {
						$subcategory = $categoryTable->getCategoryById($company->mainSubcategory);
					}
					$company->subcategory = "";
					if (isset($subcategory)) {
						$company->subcategory = $subcategory->name;
					}
					$company->slogan = str_replace(array("\r\n", "\r", "\n"), '<br />', $company->slogan);
					$company->slogan = str_replace('"', '""', $company->slogan);
					$company->short_description = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->short_description);
					$company->short_description = str_replace('"', '""', $company->short_description);
					$company->description = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->description);
					$company->description = str_replace('"', '""', $company->description);
					$company->custom_tab_name = str_replace(array("\r\n", "\r", "\n"), "<br />", (string)$company->custom_tab_name);
					$company->custom_tab_name = str_replace('"', '""', $company->custom_tab_name);
					$company->custom_tab_content = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->custom_tab_content);
					$company->custom_tab_content = str_replace('"', '""', $company->custom_tab_content);
					$company->meta_title = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->meta_title);
					$company->meta_title = str_replace('"', '""', $company->meta_title);
					$company->meta_description = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->meta_description);
					$company->meta_description = str_replace('"', '""', $company->meta_description);

					$translations = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_DESCRIPTION_TRANSLATION, $company->id);
					$translationsSlogan = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_SLOGAN_TRANSLATION, $company->id);
					$translationsMeta = JBusinessDirectoryTranslations::getAllTranslations(BUSINESS_META_TRANSLATION, $company->id);

					$name = "\"$company->name\"" . $delimiter;
					$slogan = "\"$company->slogan\"" . $delimiter;
					$shortDesc = "\"$company->short_description\"" . $delimiter;
					$desc = "\"$company->description\"" . $delimiter;
					$metaName = "\"$company->meta_title\"" . $delimiter;
					$metaDesc = "\"$company->meta_description\"" . $delimiter;
					foreach ($languages as $lng) {
						$langContentName = isset($translations[$lng . "_name"]) ? $translations[$lng . "_name"] : "";
						$langContentName = str_replace(array("\r\n", "\r", "\n"), '<br />', $langContentName);
						$langContentName = str_replace('"', '""', $langContentName);
						
						$langContentSlogan = isset($translationsSlogan[$lng]) ? $translationsSlogan[$lng] : "";
						$langContentSlogan = str_replace(array("\r\n", "\r", "\n"), '<br />', $langContentSlogan);
						$langContentSlogan = str_replace('"', '""', $langContentSlogan);
						
						$langContentShort = isset($translations[$lng . "_short"]) ? $translations[$lng . "_short"] : "";
						$langContentShort = str_replace(array("\r\n", "\r", "\n"), '<br />', $langContentShort);
						$langContentShort = str_replace('"', '""', $langContentShort);
						
						$langContentDesc = isset($translations[$lng]) ? $translations[$lng] : "";
						$langContentDesc = str_replace(array("\r\n", "\r", "\n"), '<br />', $langContentDesc);
						$langContentDesc = str_replace('"', '""', $langContentDesc);
						
						$contentNameMeta = isset($translationsMeta[$lng . "_name"]) ? $translationsMeta[$lng . "_name"] : "";
						$contentNameMeta = str_replace(array("\r\n", "\r", "\n"), '<br />', $contentNameMeta);
						$contentNameMeta = str_replace('"', '""', $contentNameMeta);
						
						$contentDescMeta = isset($translationsMeta[$lng]) ? $translationsMeta[$lng] : "";
						$contentDescMeta = str_replace(array("\r\n", "\r", "\n"), '<br />', $contentDescMeta);
						$contentDescMeta = str_replace('"', '""', $contentDescMeta);
						
						$name .= "\"".$langContentName."\"".$delimiter;
						$slogan .= "\"".$langContentSlogan."\"".$delimiter;
						$shortDesc .= "\"".$langContentShort."\"".$delimiter;
						$desc .= "\"".$langContentDesc."\"".$delimiter;
						$metaName .= "\"".$contentNameMeta."\"".$delimiter;
						$metaDesc .= "\"".$contentDescMeta."\"".$delimiter;
					}
					$metaDesc = rtrim($metaDesc, $delimiter);

					if (isset($includeId)) {
						$csv_output .= "\"$company->id\"" . $delimiter; 
					}

					$csv_output .= $name . "\"$company->alias\"" . $delimiter . "\"$company->comercialName\"" . $delimiter
						. "\"$company->taxCode\"" . $delimiter . "\"$company->registrationCode\"" . $delimiter . "\"$company->establishment_year\"" . $delimiter
						. "\"$company->employees\"" . $delimiter . "\"$company->min_project_size\"" . $delimiter . "\"$company->hourly_rate\"" . $delimiter 
						. "\"$company->website\"" . $delimiter . "\"$company->websiteCount\"" . $delimiter
						. "\"$company->typeName\"" . $delimiter . "\"$company->keywords\"" . $delimiter . $slogan . $shortDesc . $desc . "\"$company->categoryNames\"" . $delimiter
						. "\"$company->subcategory\"" . $delimiter . "\"$company->time_zone\"" . $delimiter ;

					$workingHours['work_status'] = array();
					$workingHours['work_ids'] = array();
					$workingHours['work_start_hour'] = array();
					$workingHours['work_end_hour'] = array();

					$workingHours['break_ids'] = array();
					$workingHours['break_start_hour'] = array();
					$workingHours['break_end_hour'] = array();
					$workingHours['breaks_count'] = array();
					foreach ($weekDays as $key => $day) {
						$workingHours['work_ids'][] = null;
						$workingHours['work_status'][] = $day->workHours['status'];
						$workingHours['work_start_hour'][] = JBusinessUtil::convertTimeToFormat($day->workHours['start_time']);
						$workingHours['work_end_hour'][] = JBusinessUtil::convertTimeToFormat($day->workHours['end_time']);
						if (isset($day->breakHours) && isset($day->breakHours['start_time'])) {
							$workingHours['breaks_count'][] = count($day->breakHours['start_time']);
							for ($i=0; $i<count($day->breakHours['start_time']); $i++) {
								$workingHours['break_ids'][] = null;
								$workingHours['break_start_hour'][] =  JBusinessUtil::convertTimeToFormat($day->breakHours['start_time'][$i]);
								$workingHours['break_end_hour'][] = JBusinessUtil::convertTimeToFormat($day->breakHours['end_time'][$i]);
							}
						} else {
							$workingHours['breaks_count'][] = 0;
						}
					}

					$workStatus = implode('##', $workingHours['work_status']);
					$workStartHour = implode('##', $workingHours['work_start_hour']);
					$workEndHour = implode('##', $workingHours['work_end_hour']);
					$breakStartHour = implode('##', $workingHours['break_start_hour']);
					$breakEndHour = implode('##', $workingHours['break_end_hour']);
					$breakIds = implode('##', $workingHours['break_ids']);
					$breakCount = implode('##', $workingHours['breaks_count']);
					$workIds = implode('##', $workingHours['work_ids']);

					$csv_output .= "\"$workStatus\"" . $delimiter . "\"$workStartHour\"" . $delimiter . "\"$workEndHour\"" . $delimiter
						. "\"$breakStartHour\"" . $delimiter . "\"$breakEndHour\"" . $delimiter . "\"$breakIds\"" . $delimiter
						. "\"$breakCount\"" . $delimiter . "\"$workIds\"" . $delimiter . "\"$company->street_number\"" . $delimiter
						. "\"$company->address\"" . $delimiter . "\"$company->area\"" . $delimiter . "\"$company->countryName\"" . $delimiter
						. "\"$company->city\"" . $delimiter . "\"$company->province\"" . $delimiter . "\"$company->county\"" . $delimiter . "\"$company->postalCode\"" . $delimiter
						. "\"$company->publish_only_city\"" . $delimiter . "\"$company->latitude\"" . $delimiter . "\"$company->longitude\"" . $delimiter . "\"$company->activity_radius\"" . $delimiter. "\"$company->recommended\"" . $delimiter;

					for ($i = 0; $i < $maxLocations; $i++) {
						if (isset($company->secLocations[$i])) {
							$secLocation = $company->secLocations[$i];
							$csv_output .= "\"$secLocation->name\"" . $delimiter . "\"$secLocation->street_number\"" . $delimiter . "\"$secLocation->address\""
								. $delimiter . "\"$secLocation->city\"" . $delimiter . "\"$secLocation->county\"" . $delimiter . "\"$secLocation->postalCode\"" . $delimiter
								. "\"$secLocation->countryId\"" . $delimiter . "\"$secLocation->latitude\"" . $delimiter . "\"$secLocation->longitude\"" . $delimiter
								. "\"$secLocation->phone\"" . $delimiter . "\"$secLocation->province\"" . $delimiter . "\"$secLocation->area\"" . $delimiter;
						} else {
							$csv_output .= "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter
								. "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter;
						}
					}

					for ($i = 0; $i < $maxContacts; $i++) {
						if (isset($company->contact[$i])) {
							$companyContact = $company->contact[$i];
							$csv_output .= "\"$companyContact->contact_department\"" . $delimiter . "\"$companyContact->contact_name\"" . $delimiter . "\"$companyContact->contact_email\"" . $delimiter
								. "\"$companyContact->contact_phone\"" . $delimiter . "\"$companyContact->contact_fax\"" . $delimiter;
						} else {
							$csv_output .= "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter . "" . $delimiter;
						}
					}

					$csv_output .= "\"$company->phone\"" . $delimiter . "\"$company->mobile\"" . $delimiter . "\"$company->email\"" . $delimiter . "\"$company->fax\"" . $delimiter
						. "\"$company->facebook\"" . $delimiter . "\"$company->twitter\"" . $delimiter . "\"$company->googlep\"" . $delimiter . "\"$company->skype\"" . $delimiter
						. "\"$company->linkedin\"" . $delimiter . "\"$company->youtube\"" . $delimiter . "\"$company->instagram\"" . $delimiter . "\"$company->pinterest\"" . $delimiter
						. "\"$company->whatsapp\"" . $delimiter . "\"$company->logoLocation\"" . $delimiter . "\"$company->business_cover_image\"" . $delimiter. "\"$company->ad_image\"" . $delimiter
						. "\"$company->pictures\"" . $delimiter . "\"$company->userId\"" . $delimiter . "\"$company->review_score\"" . $delimiter . "\"$company->viewCount\"" . $delimiter
						. "\"$company->contactCount\"" . $delimiter . "\"$company->packageName\"" . $delimiter . "\"$company->custom_tab_name\"" . $delimiter
						. "\"$company->custom_tab_content\"" . $delimiter . "\"$company->state\"" . $delimiter . "\"$company->approved\"" . $delimiter
						. "\"$company->modified\"" . $delimiter . "\"$company->publish_start_date\"" . $delimiter . "\"$company->publish_end_date\"" . $delimiter
						. "\"$company->creationDate\"" . $delimiter . "\"$company->featured\"" . $delimiter . $metaName . $metaDesc ;

						$companyZipcodes = $companyZipcodesTable->getAllCompanyZipcodes($company->id);
						$companyZips = array();
					foreach ($companyZipcodes as $key => $companyZip) {
						$zip = array();

						$zip[] = $companyZip->zip_code;
						$zip[] = $companyZip->latitude;
						$zip[] = $companyZip->longitude;

						$companyZips[] = implode("#", $zip);
					}
						
						$coordinates = implode($delimiter, $companyZips);
						$csv_output .=$delimiter . "\"$coordinates\"";
					
					$companyAttributes = $companyAttributesTable->getCompanyAttributes($company->id);
					foreach ($attributes as $attribute) {
						$found = false;
						foreach ($companyAttributes as $key => $companyAttribute) {
							if ($attribute->code == $companyAttribute->code) {
								$attributeValue = AttributeService::getAttributeValues($companyAttribute);
								$csv_output .= $delimiter . "\"$attributeValue\"";
								$found = true;
								unset($companyAttributes[$key]);
								break;
							}
						}
						if (!$found) {
							$csv_output .= $delimiter . "\"\"";
						}
					}

					
						
					$csv_output .= "\n";
				}
				
			}
			print $csv_output;
			$csv_output="";
			$start += $batch;
		} while (count($companies) == $batch);
		
	}
	
	public function getCompaniesWithTranslationCSV() {
		$jinput    = JFactory::getApplication()->input;
		$delimiter = $jinput->getString("delimiter", ",");
		$category  = $jinput->post->getArray()['category'];
	
		$companyTable = $this->getTable();
	
		$categoriesIds="";
	
		if (!empty($category)) {
			$categoriesIds = array();
			$categoryService = new JBusinessDirectorCategoryLib();
			$categoriesIds = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_BUSINESS);
				
			if (!empty($category)) {
				if (isset($categoriesIds) && count($categoriesIds) > 0) {
					$categoriesIds[] = $category;
				} else {
					$categoriesIds = array($category);
				}
			}
			$categoriesIds = implode(",", $categoriesIds);
		}
	
		$languages = JBusinessUtil::getLanguages();

		$attributesTable = JTable::getInstance("Attribute", "JTable");
		$companyAttributesTable = JTable::getInstance("CompanyAttributes", "JTable");
	
		$attributes = $attributesTable->getAttributes();
	
		$csv_output = "id".$delimiter."name".$delimiter."commercial name".$delimiter."website".$delimiter."main category id".$delimiter;
		foreach ($languages as $language) {
			$csv_output.="main category $language".$delimiter;
		}
		
		$csv_output.= "categories_ids".$delimiter;
		foreach ($languages as $language) {
			$csv_output.="categories $language".$delimiter;
		}
		
		$csv_output.="registration code".$delimiter."type".$delimiter."slogan".$delimiter;
		foreach ($languages as $language) {
			$csv_output.="description $language".$delimiter;
		}
		$csv_output.= "short description".$delimiter."full address".$delimiter."street number".$delimiter."address".$delimiter."city".$delimiter."region".$delimiter."country".$delimiter."website".$delimiter."keywords".$delimiter."phone".$delimiter."mobile".$delimiter."email".$delimiter."fax".$delimiter."latitude".$delimiter."longitude".$delimiter."user".$delimiter."review_score".$delimiter."views".$delimiter."featured".$delimiter."facebook".$delimiter."twitter".$delimiter."googlep".$delimiter."postal code".$delimiter."state".$delimiter."approved".$delimiter."contact_name".$delimiter."contact_email".$delimiter."contact_phone".$delimiter."contact_fax".$delimiter."logo".$delimiter."pictures";
		if (!empty($attributes)) {
			foreach ($attributes as $attribute) {
				$csv_output = $csv_output.$delimiter.$attribute->name;
			}
		}
		$csv_output = $csv_output."\n";

		$categoryTable = JTable::getInstance("Category", "JBusinessTable");

		$start = 0;
		$batch = ITEMS_BATCH_SIZE;
		do {
			$companies =  $companyTable->getCompaniesForExport($categoriesIds, $start, $batch);
			if (count($companies)>0) {
				foreach ($companies as $company) {
					$contactTable = $this->getTable('CompanyContact', "Table");
					$company->contact = $contactTable->getCompanyContact($company->id);

					$translations = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_DESCRIPTION_TRANSLATION, $company->id);
					if (!empty($company->mainSubcategory)) {
						$subcategory = $categoryTable->getCategoryById($company->mainSubcategory);
					}

					$categoryIds = array();
					if (!empty($company->categoryIds)) {
						$categoryIds = explode(",", $company->categoryIds);
					}

					$company->subcategory = "";
					if (isset($subcategory)) {
						$company->subcategory= $subcategory->name;
					}
					$company->short_description = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->short_description);
					$company->description = str_replace(array("\r\n", "\r", "\n"), "<br />", $company->description);
					$company->description = str_replace('"', '""', $company->description);
					$csv_output .= $company->id.$delimiter."\"$company->name\"".$delimiter."\"$company->comercialName\"".$delimiter."\"$company->website\"".$delimiter."\"$company->mainSubcategory\"".$delimiter;

					foreach ($languages as $lng) {
						$ct = array();
						$translation = JBusinessDirectoryTranslations::getObjectTranslation(CATEGORY_TRANSLATION, $company->mainSubcategory, $lng);
						$ct[] = empty($translation)?"":$translation->name;
						if (empty($ct)) {
							$csv_output.= "".$delimiter;
						} else {
							$csv_output.= "\"".implode(",", $ct)."\"".$delimiter;
						}
					}


					$csv_output .="\"$company->categoryIds\"".$delimiter;

					foreach ($languages as $lng) {
						$ct = array();
						foreach ($categoryIds as $cId) {
							$translation = JBusinessDirectoryTranslations::getObjectTranslation(CATEGORY_TRANSLATION, $cId, $lng);
							$ct[] = empty($translation)?"":$translation->name;
						}
						if (empty($ct)) {
							$csv_output.= "".$delimiter;
						} else {
							$csv_output.= "\"".implode(",", $ct)."\"".$delimiter;
						}
					}

					$csv_output .= "$company->registrationCode".$delimiter."\"$company->type\"".$delimiter."\"$company->slogan\"".$delimiter;
					foreach ($languages as $language) {
						if (!empty($translations[$language])) {
							$translations[$language] = str_replace(array("\r\n", "\r", "\n"), "<br />", $translations[$language]);
							$csv_output.= "\"$translations[$language]\"".$delimiter;
						} else {
							$csv_output.= $delimiter;
						}
					}
					$csv_output .="\"$company->short_description\"".$delimiter;
					$csv_output .="\"$company->street_number, $company->address, $company->city, $company->county, $company->countryName\"".$delimiter;
					$csv_output .="$company->street_number".$delimiter."\"$company->address\"".$delimiter."\"$company->city\"".$delimiter."\"$company->county\"".$delimiter."\"$company->countryName\"".$delimiter."\"$company->website\"".$delimiter."\"$company->keywords\"".$delimiter."\"$company->phone\"".$delimiter."\"$company->mobile\"".$delimiter."$company->email".$delimiter."$company->fax".$delimiter."$company->latitude".$delimiter."$company->longitude".$delimiter."$company->userId".$delimiter."$company->review_score".$delimiter."$company->viewCount".$delimiter."$company->featured".$delimiter."$company->facebook".$delimiter."$company->twitter".$delimiter."$company->googlep".$delimiter."\"$company->postalCode\"".$delimiter."$company->state".$delimiter."$company->approved".$delimiter."\"".$company->contact->contact_name."\"".$delimiter."\"".$company->contact->contact_email."\"".$delimiter."\"".$company->contact->contact_phone."\"".$delimiter."\"".$company->contact->contact_fax."\"".$delimiter;

					$csv_output .="\"".(BD_PICTURES_PATH.$company->logoLocation)."\"".$delimiter;
					$pictures = explode(",", $company->pictures);
					foreach ($pictures as &$picture) {
						$picturesArray = explode('#', $picture);
						$picturePath = $picturesArray[0];
						$picturePath = BD_PICTURES_PATH.$picturePath;
						$picturesArray[0] = $picturePath;
						$picture = implode("#", $picturesArray);
					}
					$pictures = implode(",", $pictures);
					$csv_output .="\"".$pictures."\"";

					$companyAttributes = $companyAttributesTable->getCompanyAttributes($company->id);

					foreach ($attributes as $attribute) {
						foreach ($companyAttributes as $companyAttribute) {
							if ($attribute->code == $companyAttribute->code) {
								$csv_output .= $delimiter."\"".AttributeService::getAttributeValues($companyAttribute)."\"";
							}
						}
					}

					$csv_output .= "\n";
				}
			}
			$start += $batch;
		} while (count($companies) == $batch);


		return $csv_output;
	}

	public function generateListingsPDF() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$companies = self::getItems();
		$categoryTable = JTable::getInstance("Category", "JBusinessTable");	
		$companyModel = JModelLegacy::getInstance('Company', 'JBusinessDirectoryModel', array('ignore_request' => true));	

		if (empty($companies)) {
			die(JText::_('LNG_COMPANY_PDF_EXPORT_FAIL'));
		}

		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="companies.pdf"');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
 		define("_SYSTEM_TTFONTS", FPDF_FONTPATH);

		$pdf = new FPDF_HELPER ();

		$coverImg = BD_PICTURES_UPLOAD_PATH."/pdf_home_bg.jpg";

		$pdf->AddPage();
		$pdf->Image($coverImg, 0, 0, 210, 297);
		$pdf->SetTextColor(255,255,255); 
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed-Bold.ttf',true);

		$pdf->SetFont("DejaVu", "", "30");
		$pdf->Cell(0, 20, utf8_decode((JText::_('LNG_MEMBER_DIRECTORY'))), 0, 0, 'C');
		$pdf->centreImage(BD_PICTURES_PATH .$appSettings->logo);
		$pdf->SetXY(0,260);		
		$pdf->SetFont("DejaVu", "", "35");
		$pdf->Cell(0, 0, strtoupper($appSettings->company_name), 0, 0, 'C');
		$pdf->SetXY(0,275);		
		$pdf->SetFont("DejaVu", "", "18");
		$pdf->Cell(0, 0, JText::_('LNG_PDF_COVER_DESC'), 0, 0, 'C');
		$pdf->SetTextColor(0); 

		foreach ($companies as $company) {
		  if ($company->state == 1 && $company->approved == 2) {
			
			$companyCategory = $categoryTable->getCategoryById($company->mainSubcategory);
			$workingDays = $companyModel->getWorkingDays($company->id);

			$translations = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_DESCRIPTION_TRANSLATION, $company->id);
			$translationsSlogan = JBusinessDirectoryTranslations::getAllTranslations(BUSSINESS_SLOGAN_TRANSLATION, $company->id);			
			$lng = JBusinessUtil::getLanguageTag();

			if ($appSettings->enable_multilingual) {
				$description = isset($translations[$lng]) ? strip_tags(html_entity_decode($translations[$lng])) : "";
				$slogan = isset($translationsSlogan[$lng]) ? strip_tags(html_entity_decode($translationsSlogan[$lng])) : "";
				if (empty($description))
					$description = strip_tags(html_entity_decode($company->description));				
				if(empty($slogan))
					$slogan = strip_tags(html_entity_decode($company->slogan));	
			} else {
				$description = strip_tags(html_entity_decode($company->description));
				$slogan = strip_tags(html_entity_decode($company->slogan));
			}

			$pdf->AddPage();
			$pdf->Cell(0, 10, "", 0, 1, "L");
			$pdf->SetFont("DejaVu", "", "18");

			//IMAGES SECTION	
			$companyPictures = [];
			if(!empty($company->pictures)){
				$company->pictures = explode("," , $company->pictures);
				$companyPictures = $company->pictures;
				if(count($companyPictures) > 3 ){
					$companyPictures = array_splice($company->pictures, 0, 3);
				}					
				while(count($companyPictures) < 3)
					array_push($companyPictures, $appSettings->no_image);				
			} else {
				$companyPictures = array($appSettings->no_image, $appSettings->no_image, $appSettings->no_image);					
			}	

			$pdf->SetXY(0,0);
			$index = 0;	
			foreach($companyPictures as $pic) {
				if(!empty($pic) && file_exists(BD_PICTURES_UPLOAD_PATH.$pic)){
					$pdf->Image(BD_PICTURES_PATH.$pic, $index*70, 0, $index < 2 ? 69.5 : 70 , 46);
				} else {
					$pdf->Image(BD_PICTURES_PATH.$appSettings->no_image, $index*70, 0, $index < 2 ? 69.5 : 70 , 46);
				}
				$index++;
			}			
			
			//HEADER
			if (!empty($company->logoLocation) && file_exists(BD_PICTURES_UPLOAD_PATH.$company->logoLocation)){ 			
				$companyImg = BD_PICTURES_PATH . $company->logoLocation;
			} else {
				$companyImg = BD_PICTURES_PATH . $appSettings->no_image;
			}

			if(!empty($appSettings->logo) && file_exists(BD_PICTURES_UPLOAD_PATH.$appSettings->logo)) {
				$siteLogo = BD_PICTURES_PATH . $appSettings->logo;
			} else {
				$siteLogo = BD_PICTURES_PATH . $appSettings->no_image;	
			}			

			$pdf->Cell(1, 40, $pdf->Image($companyImg, 10, 55, 25), 10, 0, 'L', false );   

			$pdf->SetXY(39, 20);
			$pdf->Cell(0, 83,utf8_decode($company->name), 0, 0, "L");
			$pdf->SetFont("DejaVu", "", "12");
			
			$pdf->SetTextColor(160,160,176);
			$pdf->SetXY(39, 66);
			$pdf->MultiCell(0, 4, utf8_decode(JBusinessUtil::truncate($slogan, 180, '')));
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 80);
			
			$pdf->SetDrawColor(220,220,220);
			$pdf->SetLineWidth(".4");
			$pdf->Rect(10, 80, 107,200, "L");
			$pdf->Text(16,88, JText::_('LNG_DESCRIPTION'));
			$pdf->SetTextColor(100,100,106);
			$pdf->Cell(0, 0, "", 20, 5, "L");		
		
			$pdf->SetTextColor(0,0,0);

			// CATEGORY SECTION 
			if(!empty($companyCategory->color)) {
				$color = JBusinessUtil::convertHexToRGB($companyCategory->color,null, true);
				$pdf->SetFillColor($color[0],$color[1],$color[2]);
			} else {
				$pdf->SetFillColor(100,100,106); 
			}
				
			$pdf->Rect(123, 80, 90, 18, "F");
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont("DejaVu", "", "15");
			$pdf->SetXY(145,83);
			$pdf->MultiCell(60, 6, !empty($companyCategory->name) ? utf8_decode($companyCategory->name) : "", 0, "L");
			$pdf->SetTextColor(0);
			$pdf->Image($siteLogo, 125, 81, 17, 15);

			$pdf->SetFillColor(255,255,255);

			// CONTACT BOX
			$pdf->Rect(123, 103, 78, 85, "L");
			$pdf->SetFont("DejaVu", "", "12");
			$pdf->SetXY(130,110);
			$pdf->Write(0, JText::_('LNG_COMPANY_INFO'));
			$pdf->SetFont("DejaVu", "", "8");
			$pdf->SetTextColor(50,50,50);			
			
			$mailIcon = BD_PICTURES_PATH . "/mailicon.png";
			$webIcon = BD_PICTURES_PATH . "/webicon.png";
			$phoneIcon = BD_PICTURES_PATH . "/phoneicon.png";
			$locationIcon = BD_PICTURES_PATH . "/locationicon.png";

			
			$websiteBreak = 40;
			$website = implode(PHP_EOL, str_split($company->website, $websiteBreak));

			$pdf->Image($mailIcon, 130, 116.5, 7, 7);
			$pdf->SetXY(140,120);
			$pdf->Write(0, !empty($company->email) ? $company->email : JText::_('LNG_NOT_PROVIDED'));
			$pdf->Image($phoneIcon, 130, 126.5, 7, 7);
			$pdf->SetXY(140,130);
			$pdf->Write(0, !empty($company->phone) ? $company->phone : JText::_('LNG_NOT_PROVIDED'));
			$pdf->Image($webIcon, 130, 136.5, 7, 7);
			$pdf->SetXY(140,138);
			$pdf->MultiCell(0, 3, !empty($company->website) ? $website : JText::_('LNG_NOT_PROVIDED'), 0, "L");
			
			$pdf->Image($locationIcon, 130, 149, 7, 7);
			$pdf->SetXY(140,150);
			$listingAddress = $company->address." ".$company->city." ".$company->county." ".$company->postalCode ;
			$pdf->MultiCell(0, 5, !empty(trim($listingAddress)) ? utf8_decode($listingAddress) : JText::_('LNG_NOT_PROVIDED'), 0, "L");
			$pdf->Line(130,165, 195,165);
			$pdf->SetXY(130,172);
			$pdf->SetFont("DejaVu", "", "12");
			$pdf->SetTextColor(0);

			if(!empty($company->contact_name)) {
				$pdf->Write(0, utf8_decode(JText::_('LNG_CONTACT_PERSON')));
				$pdf->SetXY(130, 180);
				$pdf->SetTextColor(50,50,50);
				$pdf->SetFont("DejaVu", "", "9");
				$pdf->Write(0, utf8_decode($company->contact_name));
			}

			// Opening hours Box
			$pdf->Rect(123, 193, 78, 87, "B");
			$pdf->SetTextColor(0);
			$pdf->SetXY(130,200);
			$pdf->SetFont("DejaVu", "", "12");

			$pdf->Write(0, utf8_decode(JText::_('LNG_OPENING_TIME')));
			$pdf->SetTextColor(50,50,50);
			$pdf->SetFont("DejaVu", "", "9");

			$dayIndex = 1;
			foreach($workingDays as $day) {
				$pdf->SetXY(130,$dayIndex*10+200);
				$pdf->Write(0, ucfirst($day->name));

				if($day->workHours["status"] == 0) {
					$pdf->SetXY(185,$dayIndex*10+200);

					$pdf->Write(0, utf8_decode(JText::_('LNG_CLOSED')));
				} 
				elseif ($day->workHours["status"] != 0 && isset($day->breakHours)) {
					$pdf->SetXY(176.5,$dayIndex*10+200);
					$pdf->Write(0, date('H:i', strtotime($day->workHours["start_time"]))." - ". date('H:i', strtotime($day->breakHours["start_time"][0])));
					$pdf->SetXY(176.5,$dayIndex*10+203);
					$pdf->Write(0, date('H:i', strtotime($day->breakHours["end_time"][0]))." - ". date('H:i', strtotime($day->workHours["end_time"])));
				} 
				else {
					$pdf->SetXY(176.5, $dayIndex*10+200);
					$pdf->Write(0,	date('H:i', strtotime($day->workHours["start_time"]))." - ". date('H:i', strtotime($day->workHours["end_time"])));					
				}

				$dayIndex++;
			}
			//DESCRIPTION TEXT
			$pdf->SetXY(15,94);
			$pdf->SetFont("DejaVu", "", "10");
			$pdf->MultiCell(98, 5, utf8_decode(preg_replace('/\s+/', ' ', JBusinessUtil::truncate($description, 1800, ''))));
		  }
		}
			// LAST PAGE
			$pdf->AddPage();
			$pdf->SetXY(0,0);
			$pdf->SetFillColor(50); 		
			$pdf->Rect(0, 0, 210, 297, "F");
			$pdf->SetTextColor(255, 255, 255); 
			$pdf->SetFont("DejaVu", "", "30");
			$pdf->Cell(0, 30, utf8_decode(JText::_('LNG_PDF_GOODBYE_TEXT')), 0, 0, 'C');
			$pdf->centreImage(BD_PICTURES_PATH .$appSettings->logo);

			$addressText = $appSettings->company_name. " - " .$appSettings->invoice_company_address. " - " . $appSettings->invoice_company_phone ;
			$pdf->SetFont("DejaVu", "", "12");
			$pdf->SetTextColor(255, 255, 255); 
			$pdf->SetXY(0,275);			
			$pdf->Cell(0, 0, utf8_decode($addressText), 0, 0, 'C');

			$pdf->RotatedImage(BD_PICTURES_PATH .$appSettings->no_image,200, 280, 10, 10, 90);
			$pdf->SetFont("DejaVu", "", "9");
			$pdf->RotatedText(206,267,$appSettings->company_name." - ".JBusinessUtil::getWebsiteUrl(), 90);
		$pdf->Output('companies.pdf', 'I');
		exit;		
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param    int        The ID of the primary key to move.
	 * @param    integer    Increment, usually +1 or -1
	 *
	 * @return    boolean    False on failure or error, true otherwise.
	 */
	public function reorder($pk, $direction = 0) {
		// Sanitize the id and adjustment.
		$pk   = (!empty($pk)) ? $pk : (int) $this->getState('company.id');
		// Get an instance of the record's table.
		$table = JTable::getInstance('Company');

		// Load the row.
		if (!$table->load($pk)) {
			$this->setError($table->getError());
			return false;
		}

		// Access checks.
		$allow = true; //$user->authorise('core.edit.state', 'com_users');

		if (!$allow) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			return false;
		}

		// Move the row.
		// TODO: Where clause to restrict category.
		$table->move($pk);

		return true;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param    array    An array of primary key ids.
	 * @param    int        +/-1
	 *
	 * @return bool
	 */
	public function saveorder($pks, $order) {
		// Initialise variables.
		$table      = JTable::getInstance('Company');
		$conditions = array();

		if (empty($pks)) {
			return JFactory::getApplication()->enqueueMessage(JText::_('COM_USERS_ERROR_LEVELS_NOLEVELS_SELECTED'), 'warning');
		}

		// update ordering values
		foreach ($pks as $i => $pk) {
			$table->load((int) $pk);

			// Access checks.
			$allow = true;//$user->authorise('core.edit.state', 'com_users');

			if (!$allow) {
				// Prune items that you can't change.
				unset($pks[$i]);
				JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'warning');
			} elseif ($table->ordering != $order[$i]) {
				$table->ordering = $order[$i];
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond) {
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		return true;
	}

}