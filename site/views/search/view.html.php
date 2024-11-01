<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::loadJQueryChosen();

$appSettings = JBusinessUtil::getApplicationSettings();
if ($appSettings->enable_ratings) {
	JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
	JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
}

// following translations will be used in js
JText::script('LNG_BAD');
JText::script('LNG_POOR');
JText::script('LNG_REGULAR');
JText::script('LNG_GOOD');
JText::script('LNG_GORGEOUS');
JText::script('LNG_NOT_RATED_YET');
JText::script('COM_JBUSINESS_ERROR');
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED');

JText::script('LNG_SHOW_FILTER');
JText::script('LNG_HIDE_FILTER');

JText::script('LNG_DISTANCE');
JText::script('LNG_MILES');
JText::script('LNG_KM');
JText::script('LNG_CATEGORIES');
JText::script('LNG_MORE');
JText::script('LNG_LESS');
JText::script('LNG_ALL');
JText::script('LNG_STARS');
JText::script('LNG_STAR_RATING');
JText::script('LNG_TYPES');
JText::script('LNG_COUNTRIES');
JText::script('LNG_REGIONS');
JText::script('LNG_CITIES');
JText::script('LNG_AREA');
JText::script('LNG_PROVINCE');
JText::script('LNG_CATEGORY');
JText::script('LNG_SELECT_OPTION');
JText::script('LNG_SELECT_RATING');
JText::script('LNG_SELECT_TYPE');
JText::script('LNG_SELECT_MEMBERSHIP');
JText::script('LNG_SELECT_COUNTRY');
JText::script('LNG_SELECT_REGION');
JText::script('LNG_SELECT_CITY');
JText::script('LNG_SELECT_AREA');
JText::script('LNG_SELECT_PROVINCE');
JText::script('LNG_PACKAGE');
JText::script('LNG_RADIUS');
JText::script('LNG_CLEAR');
JText::script('LNG_FILTERS');
JText::script('LNG_SHOW_ONLY_LOCAL');
JText::script('LNG_CLEAR_ALL_FILTERS');
JText::script('LNG_APPLIED_FILTERS');
JText::script('LNG_GEO_LOCATION');

JBusinessUtil::includeValidation();
class JBusinessDirectoryViewSearch extends JViewLegacy{
	
	public function __construct(){
		parent::__construct();
	}


	public function display($tpl = null){
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);

		if(!$this->appSettings->search_results_loading){
			$this->companies = $this->get('Items');
			$this->totalCompanies = $this->get("Total");

			if (empty($this->companies) &&  !empty($this->appSettings->search_redirect_url)) {
				$app->redirect(JRoute::_($this->appSettings->search_redirect_url));
			}
		}

		//$this->viewType = $jinput->get("view-type", LIST_VIEW);
		//$this->searchFilter = $this->get("SearchFilter");

		$categoryId = $this->get('CategoryId');
		$this->categoryId = $this->get('CategoryId');
		$this->filterActive = $jinput->get("filter_active");
		if (!$this->appSettings->enable_search_filter || !empty($categoryId) && $this->appSettings->search_type != 1 || (!empty($categoryId) && $this->appSettings->search_filter_type == 1)) {
			$this->categoryId = $categoryId;
			$this->category = $this->get('Category');
		}

		$this->selectedCategories =  $this->get("SelectedCategories");
		$this->selectedParams = $this->get('SelectedParams');

		$this->country = $this->get('Country');
		$this->region  = $this->get('Region');
		$this->city    = $this->get('City');

		$searchkeyword = $session->get('searchkeyword');
		$this->categorySuggestion = $jinput->get('categorySuggestion');
		if (isset($searchkeyword) && empty($this->categorySuggestion)) {
			$this->searchkeyword =  $searchkeyword;
		}

		$this->location = $this->get("Location");

		$this->radius = $session->get('radius');
		$this->onlyLocal = $session->get('onlyLocal');
		$this->customAtrributes = $session->get('customAtrributes');
		$this->customAtrributesValues = $this->get("CustomAttributeValues");

		$this->categories = implode(";", $this->selectedCategories);
		if (!empty($this->categories)) {
			$this->categories .= ";";
		}

		// if (!empty($this->categories)) {
		// 	$this->categories = explode(";", $this->categories);
		// 	$result = array();
		// 	if (!empty($this->searchFilter["categories"])) {
		// 		foreach ($this->searchFilter["categories"] as $category) {
		// 			$found = false;
		// 			if (in_array($category[0][0]->id, $this->categories)) {
		// 				$found = true;
		// 			}

		// 			if ($found) {
		// 				$result[] = $category[0][0]->id;
		// 			}
		// 		}
		// 	}
		// 	$this->categories = implode(";", $result);
		// 	if (!empty($this->categories)) {
		// 		$this->categories.=";";
		// 	}
		// }

		$this->pagination = $this->get('Pagination');

		$this->letters = $this->get('UsedLetter');
		$this->letter = $jinput->get('letter', null);
		$this->categorySearch = $jinput->getInt('categorySearch', null);
		$this->citySearch = $jinput->getString('citySearch', null);
		$this->membershipSearch = $jinput->getString('membershipSearch', null);
		$this->packageSearch = $jinput->getString('packageSearch', null);
		$this->regionSearch = $jinput->getString('regionSearch', null);
		$this->areaSearch = $jinput->getString('areaSearch', null);
		$this->provinceSearch = $jinput->getString('provinceSearch', null);
		$this->zipCode = $jinput->getString('zipcode');
		$this->typeSearch = $jinput->getInt('typeSearch', null);
		if (!empty($this->typeSearch)) {
			$this->typeSearchName = $this->get('Type')->name;
		}
		$this->type = $this->get("CompanyType");
		$this->countrySearch = $jinput->getInt('countrySearch', null);

		//creates performance issues
		//$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);

		$this->filterByFav = $jinput->get('filter-by-fav', null);
		$this->featured = $jinput->get('featured', null);
		$this->sortByOptions = $this->get('SortByConfiguration');
		$this->form_submited = $jinput->get('form_submited', null);
		$this->orderBy = $jinput->getString("orderBy", $this->appSettings->order_search_listings);
		$this->preserve = $jinput->get('preserve', null);

		$this->location = $session->get('location');
		$this->moreFilters = $session->get("moreFilters");

		$this->geoLatitude = $session->get('geo-latitude');
		$this->state = $this->get('State');

		if (!empty($this->citySearch) || !empty($city)) {
			$this->allCompanies = $this->get('allItems');
		}

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, CONTACT_TERMS_CONDITIONS_TRANSLATION);
		}

		$session->set("lSearchType", 1);

		// if($this->appSettings->redirect_to_listing && count($this->companies) == 1) {
		// 	$app->redirect(JBusinessUtil::getCompanyLink($this->companies[0]));
		// }

		parent::display($tpl);
	}


	public function displayCompanies($companies, $grid = false){
		$this->companies = $companies;
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);
		$user = JBusinessUtil::getUser();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$newTab = ($appSettings->open_listing_on_new_tab) ? " target='_blank'" : "";
		$showData = !($user->ID == 0 && $appSettings->show_details_user == 1);
		$input = JFactory::getApplication()->input;
		$menu_list_layout = $input->getInt("list_layout");
		$menu_grid_layout = $input->getInt("grid_layout");
		
		if ($grid) {
			switch ($menu_grid_layout) {
				case 1:
					require_once JPATH_COMPONENT_SITE . '/include/listings_grid_style_1.php';
					break;
				case 2:
					require_once JPATH_COMPONENT_SITE . '/include/listings_grid_style_2.php';
					break;
				case 3:
					require_once JPATH_COMPONENT_SITE . '/include/listings_grid_style_3.php';
					break;
				default:
					require_once JPATH_COMPONENT_SITE . '/include/listings_grid_style_1.php';
					break;
			}
		} else {
			switch ($menu_list_layout) {
				case 1:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_1.php';
					break;
				case 2:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_2.php';
					break;
				case 3:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_3.php';
					break;
				case 4:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_4.php';
					break;
				case 5:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_5.php';
					break;
				case 6:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_6.php';
					break;
				case 7:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_7.php';
					break;
				case 8:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_8.php';
					break;
				case 9:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_9.php';
					break;
				default:
					require_once JPATH_COMPONENT_SITE . '/include/listings_list_style_1.php';
					break;
			}
		}
	}
}
