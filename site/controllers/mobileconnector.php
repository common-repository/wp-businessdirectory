<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once BD_CLASSES_PATH . '/attributes/attributeservice.php';
jimport('joomla.application.component.model');
use MVC\Plugin\PluginHelper;
use MVC\Factory;

class JBusinessDirectoryControllerMobileConnector extends JControllerLegacy {
	private $appSettings;

	/**
	 * Constructor (registers additional tasks to methods)
	 * @return void
	 * @since 5.0.0
	 */
	public function __construct() {
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		parent::__construct();
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string $name   The model name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   5.0.0
	 */
	public function getModel($name = 'MobileConnector', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Get's search parameters and searches based on the item type. Prints the result
	 * as JSON.
	 *
	 * @since 5.0.0
	 */
	public function getFeaturedItems() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$appSettings = JBusinessUtil::getApplicationSettings();
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		JFactory::getApplication()->input->set("limit", $mobileAppSettings->mobile_list_limit);


		switch ($itemType) {
			case ITEM_TYPE_EVENT:
				if($mobileAppSettings->mobile_only_featured_events) {
					JFactory::getApplication()->input->set("featured", 1);
				}
				if($mobileAppSettings->mobile_event_categories_filter) {
					JFactory::getApplication()->input->set("mobileCategoriesFilter", 1);
				}
				$model = $this->getModel('Events');
				$items = $model->getItems();
				foreach ($items as $item) {
					$item->logoLocation = !empty($item->picture_path) ? $item->picture_path : $mobileAppSettings->mobile_event_img;
					$item->itemType = ITEM_TYPE_EVENT;
					$item->id            = (int) $item->id;
					$item->longitude     = (float) $item->longitude;
					$item->latitude      = (float) $item->latitude;
					$item->review_score  = (float) $item->review_score;
					$item->averageRating = (float) $item->averageRating;
					$item->viewCount     = (int) $item->viewCount;
					$item->userId        = (int) $item->userId;
					$item->company_id    = (int) $item->company_id;

				}
				break;
			case ITEM_TYPE_OFFER:
                //reset itemType param, same param used in offers structure
                JFactory::getApplication()->input->set("itemType",null);
				if($mobileAppSettings->mobile_only_featured_offers) {
					JFactory::getApplication()->input->set("featured", 1);
				}
				if($mobileAppSettings->mobile_offer_categories_filter) {
					JFactory::getApplication()->input->set("mobileCategoriesFilter", 1);
				}
                $model = $this->getModel('Offers');

                $items = $model->getItems();
				foreach ($items as $item) {
					$item->name         = $item->subject;
					$item->logoLocation = !empty($item->picture_path) ? $item->picture_path : $mobileAppSettings->mobile_offer_img;
					$item->itemType = ITEM_TYPE_OFFER;
					$item->currency = JBusinessUtil::getCurrency($item->currencyId)->currency_name;
					$item->currencySymbol = JBusinessUtil::getCurrency($item->currencyId)->currency_symbol;
					$item->id            = (int) $item->id;
					$item->longitude     = (float) $item->longitude;
					$item->latitude      = (float) $item->latitude;
					$item->review_score  = (float) $item->review_score;
					$item->averageRating = (float) $item->averageRating;
					$item->viewCount     = (int) $item->viewCount;
					$item->userId        = (int) $item->userId;
					$item->companyId     = (int) $item->companyId;
					$item->start_date    =  $item->startDate;
					$item->end_date      =  $item->endDate;

				}
				break;
			default:
				if($mobileAppSettings->mobile_only_featured_listings) {
					JFactory::getApplication()->input->set("featured", 1);
				}
				if(!empty($mobileAppSettings->mobile_company_categories_filter)) {
					JFactory::getApplication()->input->set("mobileCategoriesFilter", 1);
				}
				$model = $this->getModel('Search');
				$items = $model->getItems();
				foreach ($items as $item) {
					$item->itemType = ITEM_TYPE_BUSINESS;
					$item->id            = (int) $item->id;
					$item->longitude     = (float) $item->longitude;
					$item->latitude      = (float) $item->latitude;
					$item->review_score  = (float) $item->review_score;
					$item->averageRating = (float) $item->averageRating;
					$item->viewCount     = (int) $item->viewCount;
					$item->userId        = (int) $item->userId;
					$item->business_hours = array();

					if(empty($item->logoLocation)) {
						$item->logoLocation = $mobileAppSettings->mobile_business_img;
					}
					if(empty($item->categories)) {
						$item->categories =  array();
					}
					if(empty($item->images)) {
						$item->images =  array();
					}
				}
		}

		$this->sendResponse($items);
	}

	public function getLatestListings() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

 		JFactory::getApplication()->input->set("orderBy", 'id desc');

		$model = $this->getModel('Search');
		$items = $model->getItems();

		foreach ($items as $item) {
			$item->itemType = ITEM_TYPE_BUSINESS;
			$item->id            = (int) $item->id;
			$item->longitude     = (float) $item->longitude;
			$item->latitude      = (float) $item->latitude;
			$item->review_score  = (float) $item->review_score;
			$item->averageRating = (float) $item->averageRating;
			$item->viewCount     = (int) $item->viewCount;
			$item->userId        = (int) $item->userId;
			$item->business_hours = array();

			if(empty($item->logoLocation)) {
				$item->logoLocation = $mobileAppSettings->mobile_business_img;
			}
			if(empty($item->categories)) {
				$item->categories =  array();
			}
			if(empty($item->images)) {
				$item->images =  array();
			}
		}

		$this->sendResponse($items);
	}

	/**
	 * Get's search parameters and searches based on the item type. Prints the result
	 * as JSON. Filters the result for all 3 types to create a consistent response structure.
	 *
	 * @since 5.0.0
	 */
	public function getSearchResults() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$itemType = empty($itemType) ? ITEM_TYPE_BUSINESS : $itemType;

		$limitStart = (int) JFactory::getApplication()->input->get("limitstart");
		$limit      = (int) JFactory::getApplication()->input->get("limit");

		if ($limitStart < $limit && $limitStart != 0) {
			$limitStart = $limit;
			JFactory::getApplication()->input->set("limitstart", $limitStart);
		}

		$nameField         = 'name';
		$logoField         = 'logoLocation';
		$mainCategoryField = 'mainSubcategory';
		switch ($itemType) {
			case ITEM_TYPE_EVENT:
				$model             = $this->getModel('Events');
				$items             = $model->getItems();
				$logoField         = 'picture_path';
				$mainCategoryField = 'main_subcategory';
				break;
			case ITEM_TYPE_OFFER:
                //reset itemType param, same param used in offers structure
                JFactory::getApplication()->input->set("itemType",null);
				$model             = $this->getModel('Offers');
				$items             = $model->getItems();
				$nameField         = 'subject';
				$logoField         = 'picture_path';
				$mainCategoryField = 'main_subcategory';
				break;
			default:
				$model = $this->getModel('Search');
				$items = $model->getItems();
		}

		$results = array();
		foreach ($items as $item) {
			$tmp                    = new stdClass();
			$tmp->id                = (int) $item->id;
			$tmp->name              = $item->$nameField;
			$tmp->logoLocation      = $item->$logoField;
			$tmp->short_description = $item->short_description;
			$tmp->longitude         = (float) $item->longitude;
			$tmp->latitude          = (float) $item->latitude;

			$phone = 'N/A';
			if (!empty($item->contact_phone)) {
				$phone = $item->contact_phone;
			} elseif (!empty($item->phone)) {
				$phone = $item->phone;
			} elseif (!empty($item->mobile)) {
				$phone = $item->mobile;
			}

			$tmp->review_score  = (float) $item->review_score;
			$tmp->phone         = $phone;
			$tmp->address       = JBusinessUtil::getAddressText($item);
			$tmp->distance      = (float) (isset($item->distance) ? $item->distance : 0);
			$tmp->main_category = (int) $item->$mainCategoryField;

			$tmp->categories = array();
			if (isset($item->categories)) {
				$tmp->categories = $item->categories;
			}

			if ($itemType == ITEM_TYPE_EVENT) {
				$tmp->start_date = $item->start_date;
				$tmp->end_date   = $item->end_date;
				if(empty($tmp->logoLocation)) {
					$tmp->logoLocation = $mobileAppSettings->mobile_event_img;
				}
				$tmp->company_name = $item->company_name;

			} elseif ($itemType == ITEM_TYPE_OFFER) {
				$tmp->startDate = $item->startDate;
				$tmp->endDate   = $item->endDate;
				$tmp->start_date = $item->startDate;
				$tmp->end_date   = $item->endDate;
				if(empty($tmp->logoLocation)) {
					$tmp->logoLocation = $mobileAppSettings->mobile_offer_img;
				}
				$tmp->company_name = $item->company_name;
				$tmp->price        = (float) $item->price;
				$tmp->specialPrice  = (float) $item->specialPrice;
				$tmp->currencySymbol = JBusinessUtil::getCurrency($item->currencyId)->currency_symbol;
				$tmp->currency = JBusinessUtil::getCurrency($item->currencyId)->currency_name;
			} else {
				$item->business_hours = array();
				if(empty($tmp->logoLocation)) {
					$tmp->logoLocation = $mobileAppSettings->mobile_business_img;
				}
			}

			if ($itemType == ITEM_TYPE_BUSINESS && !empty($tmp->categories) && !is_array($tmp->categories)) {
				$categories = explode("#|", $tmp->categories);
			} else {
				$categories = !empty($tmp->categories) ? $tmp->categories : array();
			}

			$tmp->categories = $categories;

			$tmp->item_type = $itemType;
			$results[]      = $tmp;
		}

		$this->sendResponse($results);
	}

	/**
	 * Provides a suggestion list based on the itemType and searchkeyword parameters.
	 *
	 * @since 5.0.0
	 */
	public function getSuggestions() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$keyword  = JFactory::getApplication()->input->get("searchkeyword");

		if (empty($itemType)) {
			$itemType = ITEM_TYPE_BUSINESS;
		}

		$model      = $this->getModel();
		$categories = $model->getCategorySuggestions($itemType, $keyword);
		$results    = $model->getSuggestions($itemType, $keyword);

		$items = array();
		foreach ($categories as $category) {
			$category->suggestionType = 2;
			$category->itemType       = $itemType;
			$items[]                  = $category;
		}

		foreach ($results as $result) {
			$result->suggestionType = 1;
			$result->itemType       = $itemType;
			$items[]                = $result;
		}

		$this->sendResponse($items);
	}

	/**
	 * Retrieves company details based on the companyId parameter.
	 *
	 * @since 5.0.0
	 */
	public function getCompany() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		$token       = JFactory::getApplication()->input->get("token");

		$user     = null;
		$apiModel = $this->getModel();
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		}

		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);
		$showAddress     = $attributeConfig["street_number"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["address"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["area"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["country"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["city"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["province"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["region"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["map"] != ATTRIBUTE_NOT_SHOW;

		$model            = $this->getModel('Companies');
		$company          = $model->getCompany();
		$company->package = $model->getPackage($company->id);

		$model->increaseViewCount($company->id);

		$images = array();
		if ($attributeConfig["pictures"] != ATTRIBUTE_NOT_SHOW) {
			$images = $model->getCompanyImages();
		}

		$company->videos = array();
		if (isset($attributeConfig["videos"]) && $attributeConfig["videos"] != ATTRIBUTE_NOT_SHOW) {
			$company->videos = $model->getCompanyVideos($company->id);
		}

		$company->announcements = array();
		if ($appSettings->enable_announcements) {
			$company->announcements = $model->getCompanyAnnouncements($company->id);
		}

		$company->images = array();
		foreach ($images as $image) {
			if(!empty($image->picture_path)) {
				$company->images[] = $image->picture_path;
			}
		}

		$company->id            = (int) $company->id;
		$company->longitude     = (float) $company->longitude;
		$company->latitude      = (float) $company->latitude;
		$company->review_score  = (float) $company->review_score;
		$company->averageRating = (float) $company->averageRating;
		$company->viewCount     = (int) $company->viewCount;
		$company->userId        = (int) $company->userId;
		$company->reviews       = $model->getReviews();

		$company->reviewStatistics = array();
		$company->reviewStatistics[] = count(JBusinessUtil::getReviewsStatistics($company->reviews)[0] ?? []);
		$company->reviewStatistics[] = count(JBusinessUtil::getReviewsStatistics($company->reviews)[1] ?? []);
		$company->reviewStatistics[] = count(JBusinessUtil::getReviewsStatistics($company->reviews)[2] ?? []);
		$company->reviewStatistics[] = count(JBusinessUtil::getReviewsStatistics($company->reviews)[3] ?? []);
		$company->reviewStatistics[] = count(JBusinessUtil::getReviewsStatistics($company->reviews)[4] ?? []);
		$company->reviewStatistics[] = count(JBusinessUtil::getReviewsStatistics($company->reviews)[5] ?? []);

		$company->reviews_count = count($company->reviews);
		if(empty($company->logoLocation)){
			$company->logoLocation = $mobileAppSettings->mobile_business_img;
		}
		if(empty($company->business_cover_image)){
			$company->business_cover_image = $mobileAppSettings->mobile_business_img;
		}
		if(empty($company->categories)){
			$company->categories = array();
		}

		$address          = JBusinessUtil::getAddressText($company);
		$company->address = !empty($address) ? $address : null;
		if (!$showAddress) {
			$company->address = null;
		}

		$phone = $company->phone;
		if (empty($phone)) {
			$phone = $company->mobile;
			if (empty($phone)) {
				$contacts = $model->getCompanyContacts();

				if (!empty($contacts) && !empty($contacts[0]->contact_phone)) {
					$phone = $contacts[0]->contact_phone;
				}
			}
		}

		$company->phone    = $phone;
		$company->shareUrl = JBusinessUtil::getCompanyLink($company);

		$hours             = array();
		$businessHours     = $company->business_hours;
		$company->isOpened = false;
		$dayCount          = 1;
		$hasHours          = false;
		foreach ($businessHours as $day) {
			$tmp                      = new stdClass();
			$tmp->workHours           = $day->workHours;
			$tmp->workHours['status'] = (int) $tmp->workHours['status'];
			$tmp->workHours['id']     = (int) $tmp->workHours['id'];

			// Add break hours if they exist
			if (isset($day->breakHours)) {
				$tmp->breakHours = $day->breakHours;
			}
			$curDay = date('N');
			if ($dayCount == $curDay) {
				if ($tmp->workHours['status'] == 1) {
					if (empty($tmp->workHours['start_time']) && empty($tmp->workHours['end_time'])) {
						$company->isOpened = true;
					} else {
						$start = strtotime(date('Y-m-d') . ' ' . $tmp->workHours['start_time']);
						$end   = strtotime(date('Y-m-d') . ' ' . $tmp->workHours['end_time']);
						$now   = strtotime(date('Y-m-d H:i:s'));

						if ($start <= $now && $now <= $end) {
							$company->isOpened = true;
						}
					}
				}
			}

			if (!empty($tmp->workHours['start_time']) || !empty($tmp->workHours['end_time'])) {
				$hasHours = true;
			}

			$tmp->workHours['start_time'] = !empty($tmp->workHours['start_time']) && $tmp->workHours['status'] == 1 ? JBusinessUtil::convertTimeToFormat($tmp->workHours['start_time']) : "";
			$tmp->workHours['end_time']   = !empty($tmp->workHours['end_time']) && $tmp->workHours['status'] == 1 ? JBusinessUtil::convertTimeToFormat($tmp->workHours['end_time']) : "";
			$tmp->name                    = $day->name;
			$tmp->workHours['break_start_time']   = !empty($tmp->breakHours['start_time']) && $tmp->workHours['status'] == 1 ? date("h:i A", strtotime($tmp->breakHours['start_time'][0])) : "";
			$tmp->workHours['break_end_time']   = !empty($tmp->breakHours['end_time']) && $tmp->workHours['status'] == 1 ? date("h:i A", strtotime($tmp->breakHours['end_time'][0])) : "";

			$hours[] = $tmp;
		}
		
		$company->business_hours = ($hasHours) ? $hours : array();

		$company->projects = $model->getCompanyProjects();

		foreach($company->projects as $project) {
			$imgUrls = array();
			if(!empty($project->pictures)) {
				foreach($project->pictures as $picture){
					$imgUrls[] = $picture[3];
				}
			}
			$project->pictures = $imgUrls;

			if(empty($project->picture_path)) {
				$project->picture_path = $mobileAppSettings->mobile_business_img;
			}
		}

		foreach ($company->reviews as $review) {
			$review->id = (int) $review->id;
			$review->userId = (int) $review->userId;
			$review->rating = (float) $review->rating;
			$pics = array();
			foreach($review->pictures as $pic) {
				$pics[] = $pic->picture_path;
			}
			$review->pictures = $pics;
		}

		$company->isBookmarked = false;
		if (!empty($user)) {
			$company->isBookmarked = $apiModel->isBookmarked($user->ID, $company->id);
		}

		$company->isOpened  = true;
		$company->hasDevice = false;
		$userId             = $company->userId;
		if (!empty($userId)) {
			$apiModel = $this->getModel();
			$devices  = $apiModel->getDevicesByUser($userId);
			if (count($devices) > 0) {
				$company->hasDevice = true;
			}
		}

		$company->contact_link = JBusinessUtil::getCompanyLink($company);

		$this->package = $model->getPackage();

		$company->attributes = $model->getCompanyAttributes();
		if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)) {
			$company->services = $model->getServicesList();
		}
		$company->appointmentServices = $model->getServices() ?? [];
		foreach($company->appointmentServices as $service) {
			$service->show_duration = (bool) $service->show_duration;
			$service->price = (float) $service->price;
			$service->currencySymbol = JBusinessUtil::getCurrency($service->currency_id)->currency_symbol;
		}

		$company->description_truncated = JBusinessUtil::truncate($company->description, 300);

		$company->relatedCompanies = $model->getRelatedCompanies();

		foreach($company->relatedCompanies as $item) {
			$item->itemType = ITEM_TYPE_BUSINESS;
			$item->id            = (int) $item->id;
			$item->longitude     = (float) $item->longitude;
			$item->latitude      = (float) $item->latitude;
			$item->review_score  = (float) $item->review_score;
			$item->averageRating = (float) $item->averageRating;
			$item->viewCount     = (int) $item->viewCount;
			$item->userId        = (int) $item->userId;
			$item->business_hours = array();

			if(empty($item->logoLocation)) {
				$item->logoLocation = $mobileAppSettings->mobile_business_img;
			}
			if(empty($item->categories)) {
				$item->categories =  array();
			}
			if(empty($item->images)) {
				$item->images =  array();
			}
		}

		$offers = self::getCompanyOffers();
		$company->offers = array();
		foreach($offers as $offer) {
			if($offer->add_to_price_list == 1) {
				$company->offers[] = $offer;
			}
		}

		$this->sendResponse($company);
	}

	public function getServiceProviders() {
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'CompanyServiceReservation');

		$serviceId = JFactory::getApplication()->input->get('serviceId');
		$model = JModelLegacy::getInstance('CompanyServiceReservation', 'JBusinessDirectoryModel');

		$providers = $model->getProviders($serviceId);

		$this->sendResponse($providers);
	}

	public function getProviderWorkingDays() {
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'CompanyServiceReservation');

		$providerId = JFactory::getApplication()->input->get('providerId');
		$providerType = JFactory::getApplication()->input->get('providerType');
		$model = JModelLegacy::getInstance('CompanyServiceReservation', 'JBusinessDirectoryModel');

		if($providerType == 1) {
			$vacations = $model->getVacationDaysAjax($providerId);
			$today = new DateTime();
			$sixMonthsFromToday = clone $today;
			$sixMonthsFromToday->add(new DateInterval('P6M'));

			$dates = [];
			$currentDate = clone $today;
			while($currentDate <= $sixMonthsFromToday) {
				if (!in_array($currentDate->format('d-m-Y'), $vacations)) {
					$dates[] = $currentDate->format('d-m-Y');
				}
				$currentDate->add(new DateInterval('P1D'));
			}
		} else {
			$dates = $model->getWorkingDaysAjax($providerId);
		}

		$this->sendResponse($dates);
	}

	public function getProviderWorkingHours() {
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'CompanyServiceReservation');

		$jinput = JFactory::getApplication()->input;
        $providerId = $jinput->get('providerId');
        $serviceId = $jinput->get('serviceId');
        $date = $jinput->get('date');

		$model = $this->getModel();
        $hours = $model->getProviderWorkingHours($serviceId, $providerId, $date);

		$this->sendResponse($hours);
	}

	/**
	 * Retrieves events for a certain company by companyId
	 *
	 * @since 5.1.3
	 */
	public function getCompanyEvents() {
		$model  = $this->getModel('Companies');
		$events = $model->getCompanyEvents();

		foreach ($events as $item) {
			$item->logoLocation = $item->picture_path;
		}


		$this->sendResponse($events);
	}

	/**
	 * Retrieves offers for a certain company by companyId
	 *
	 * @since 5.1.3
	 */
	public function getCompanyOffers() {
		$model  = $this->getModel('Companies');
		$offers = $model->getCompanyOffers();
		$companyId = JFactory::getApplication()->input->get('companyId',0);

		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
		$showAddress     = $attributeConfig["street_number"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["address"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["area"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["country"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["city"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["province"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["region"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["map"] != ATTRIBUTE_NOT_SHOW;

		foreach ($offers as $offer) {
			$offer->id           = (int) $offer->id;
			$offer->name         = $offer->subject;
			$offer->logoLocation = isset($offer->images[0]) ? $offer->images[0] : $offer->picture_path;
			$offer->price        = (float) $offer->price;
			$offer->specialPrice  = (float) $offer->specialPrice;
			if (empty($offer->price)) {
				$offer->priceText = JText::_('LNG_FREE');
			} else {
				$offer->priceText    = JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId);
			}
			$offer->longitude    = (float) $offer->longitude;
			$offer->latitude     = (float) $offer->latitude;
			$offer->userId       = (int) ($offer->user_id ?? 0);
			$offer->viewCount    = (int) ($offer->viewCount ?? 0);
			$offer->start_date    = isset($offer->startDate) ? $offer->startDate : '';
			$offer->end_date      = isset($offer->endDate) ? $offer->endDate : '';
			$offer->currency = JBusinessUtil::getCurrency($offer->currencyId)->currency_name;
			$offer->currencySymbol = JBusinessUtil::getCurrency($offer->currencyId)->currency_symbol;
			$offer->companyId      = (int) $companyId;
			$startDate = null;
			if (!JBusinessUtil::emptyDate($offer->startDate)) {
				$startDate = JBusinessUtil::getDateGeneralFormat(date('Y-m-d', strtotime($offer->startDate)));
			}

			$endDate = null;
			if (!JBusinessUtil::emptyDate($offer->endDate)) {
				$endDate = JBusinessUtil::getDateGeneralFormat(date('Y-m-d', strtotime($offer->endDate)));
			}

			$offer->startDate = $startDate;
			$offer->endDate   = $endDate;

			$address        = JBusinessUtil::getAddressText($offer);
			$offer->address = !empty($address) ? $address : null;
			if (!$showAddress) {
				$offer->address = null;
			}

			$offer->reviews = $model->getReviews();

			$totScore            = 0.0;
			$offer->review_score = 0.0;
			if (count($offer->reviews) > 0) {
				foreach ($offer->reviews as $review) {
					$totScore += (float) $review->rating;
				}

				$offer->review_score = (float) ($totScore / (float) count($offer->reviews));
			}

			$offer->alias    = isset($offer->alias) ? $offer->alias : '';
			$offer->shareUrl = JBusinessUtil::getOfferLink($offer->id, $offer->alias);

			$offer->isBookmarked = false;
			if (!empty($user)) {
				$offer->isBookmarked = $apiModel->isBookmarked($user->ID, $offer->id, BOOKMARK_TYPE_OFFER);
			}

			$offer->isOpened = true;
			if (!empty($offer->startDate) && !empty($offer->endDate)) {
				if (!JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate)) {
					$offer->isOpened = false;
				}
			} elseif (!empty($offer->endDate)) {
				if (strtotime(date('Y-m-d')) > strtotime($offer->endDate)) {
					$offer->isOpened = false;
				}
			} elseif (!empty($offer->startDate)) {
				if (strtotime(date('Y-m-d')) < strtotime($offer->startDate)) {
					$offer->isOpened = false;
				}
			}

			$offer->hasDevice = false;
			$userId           = !empty($offer->user_id) ? $offer->user_id : (isset($offer->company) ? $offer->company->userId : 0);
			if (!empty($userId)) {
				$apiModel = $this->getModel();
				$devices  = $apiModel->getDevicesByUser($userId);
				if (count($devices) > 0) {
					$offer->hasDevice = true;
				}
			}

			if(isset($offer->company)) {
				$offer->website      = $offer->company->website ?? '';
				$offer->phone        = $offer->company->phone;
				$offer->company_name = $offer->company->name;
				$offer->company_review_score   = (float) $offer->company->review_score;
				$offer->companyLogo    = $offer->company->logoLocation;
				$offer->companyEmail   = $offer->company->email;
			}
			
			$offer->contact_link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
			$offer->description_truncated = JBusinessUtil::truncate($offer->description ?? '', 300);
		}

		return $offers;
	}

	/**
	 * Retrieves event details based on the eventId parameter.
	 *
	 * @since 5.0.0
	 */
	public function getEvent() {
		$model = $this->getModel('Event');
		$event = $model->getEvent();

		$model->increaseViewCount($event->id);

		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_EVENT);
		$showAddress     = $attributeConfig["street_number"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["address"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["area"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["country"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["city"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["province"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["region"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["map"] != ATTRIBUTE_NOT_SHOW;

		$event->images = array();
		foreach ($event->pictures as $picture) {
			$event->images[] = $picture->picture_path;
		}

		if (count($event->images) == 0) {
			$event->images[] = '/no_image.jpg';
		}

		$event->id           = (int) $event->id;
		$event->logoLocation = isset($event->images[0]) ? $event->images[0] : '';
		$event->phone        = !empty($event->contact_phone) ? $event->contact_phone : $event->company->phone;
		$event->longitude    = (float) $event->longitude;
		$event->latitude     = (float) $event->latitude;
		$event->userId       = !empty($event->user_id) ? $event->user_id : $event->company->userId;
		$event->viewCount    = (int) $event->view_count;
		$event->price        = (float) $event->price;
		$event->companyLogo  = $event->company->logoLocation;
		$event->companyEmail = $event->company->email;
		$event->currencySymbol = JBusinessUtil::getCurrency($event->currency_id)->currency_symbol;
		$event->review_score = (float) $event->review_score;
		$event->company_review_score   = (float) $event->company->review_score;

		$startDate = null;
		if (!JBusinessUtil::emptyDate($event->start_date)) {
			$startDate = JBusinessUtil::getDateGeneralFormat(date('Y-m-d', strtotime($event->start_date)));
		}

		$endDate = null;
		if (!JBusinessUtil::emptyDate($event->end_date) && $event->show_end_date) {
			$endDate = JBusinessUtil::getDateGeneralFormat(date('Y-m-d', strtotime($event->end_date)));
		}

		$event->startDate = $startDate;
		$event->endDate   = $endDate;
		$event->website   = $event->company->website;

		$event->startTime = (!empty($event->start_time) && $event->show_start_time) ? $event->start_time : null;
		$event->endTime   = (!empty($event->end_time) && $event->show_end_time) ? $event->end_time : null;

		$address        = JBusinessUtil::getAddressText($event);
		$event->address = !empty($address) ? $address : null;
		if (!$showAddress) {
			$event->address = null;
		}

		$event->alias    = isset($event->alias) ? $event->alias : '';
		$event->shareUrl = JBusinessUtil::getEventLink($event->id, $event->alias);

		$event->isOpened  = true;
		$event->hasDevice = false;
		$userId           = !empty($event->user_id) ? $event->user_id : $event->company->userId;
		if (!empty($userId)) {
			$apiModel = $this->getModel();
			$devices  = $apiModel->getDevicesByUser($userId);
			if (count($devices) > 0) {
				$event->hasDevice = true;
			}
		}

		$event->company_name = $event->company->name;
		$event->company_id   = (int) $event->company_id;
		$event->contact_link = JBusinessUtil::getEventLink($event->id, $event->alias);
		$event->description_truncated = JBusinessUtil::truncate($event->description, 300);

		$this->sendResponse($event);
	}

	/**
	 * Retrieves offer details based on the offerId parameter.
	 *
	 * @since 5.0.0
	 */
	public function getOffer() {
		$token = JFactory::getApplication()->input->get("token");
		$appSettings = JBusinessUtil::getApplicationSettings();

		$user     = null;
		$apiModel = $this->getModel();
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		}

		$model = $this->getModel('Offer');
		$offer = $model->getOffer();

		$model->increaseViewCount($offer->id);

		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
		$showAddress     = $attributeConfig["street_number"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["address"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["area"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["country"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["city"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["province"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["region"] != ATTRIBUTE_NOT_SHOW
			|| $attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["map"] != ATTRIBUTE_NOT_SHOW;


		$offer->images = array();
		foreach ($offer->pictures as $picture) {
			$offer->images[] = $picture->picture_path;
		}

		if (count($offer->images) == 0) {
			$offer->images[] = '/no_image.jpg';
		}

		$offer->id           = (int) $offer->id;
		$offer->name         = $offer->subject;
		$offer->logoLocation = isset($offer->images[0]) ? $offer->images[0] : '';
		$offer->price        = (float) $offer->price;
		$offer->specialPrice  = (float) $offer->specialPrice;
		if (empty($offer->price)) {
			$offer->priceText = JText::_('LNG_FREE');
		} else {
			$offer->priceText    = JBusinessUtil::getPriceFormat($offer->price, $offer->currencyId);
		}
		$offer->longitude    = (float) $offer->longitude;
		$offer->latitude     = (float) $offer->latitude;
		$offer->userId       = !empty($offer->user_id) ? $offer->user_id : $offer->company->userId;
		$offer->viewCount    = (int) $offer->viewCount;
		$offer->website      = $offer->company->website;
		$offer->phone        = $offer->company->phone;
		$offer->start_date    = isset($offer->startDate) ? $offer->startDate : '';
		$offer->end_date      = isset($offer->endDate) ? $offer->endDate : '';
		$offer->currency = JBusinessUtil::getCurrency($offer->currencyId)->currency_name;
		$offer->currencySymbol = JBusinessUtil::getCurrency($offer->currencyId)->currency_symbol;
		$offer->companyLogo    = $offer->company->logoLocation;
		$offer->companyEmail   = $offer->company->email;
		$offer->companyId      = (int) $offer->companyId;
		$offer->enable_offer_selling = $appSettings->enable_offer_selling ? $offer->enable_offer_selling : "0";
		$offer->typeName = $offer->typeName ?? '';

		$startDate = null;
		if (!JBusinessUtil::emptyDate($offer->startDate)) {
			$startDate = JBusinessUtil::getDateGeneralFormat(date('Y-m-d', strtotime($offer->startDate)));
		}

		$endDate = null;
		if (!JBusinessUtil::emptyDate($offer->endDate)) {
			$endDate = JBusinessUtil::getDateGeneralFormat(date('Y-m-d', strtotime($offer->endDate)));
		}

		$offer->startDate = $startDate;
		$offer->endDate   = $endDate;

		$address        = JBusinessUtil::getAddressText($offer);
		$offer->address = !empty($address) ? $address : null;
		if (!$showAddress) {
			$offer->address = null;
		}

		$offer->reviews = $model->getReviews();

		$totScore            = 0.0;
		$offer->review_score = 0.0;
		if (count($offer->reviews) > 0) {
			foreach ($offer->reviews as $review) {
				$totScore += (float) $review->rating;
			}

			$offer->review_score = (float) ($totScore / (float) count($offer->reviews));
		}

		$offer->alias    = isset($offer->alias) ? $offer->alias : '';
		$offer->shareUrl = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
		foreach($offer->sellingOptions as $option) {
			$option->options = explode('|#', $option->options);
			$option->optionsIDS = explode('|#', $option->optionsIDS);
		}

		$offer->isBookmarked = false;
		if (!empty($user)) {
			$offer->isBookmarked = $apiModel->isBookmarked($user->ID, $offer->id, BOOKMARK_TYPE_OFFER);
		}

		$offer->isOpened = true;
		if (!empty($offer->startDate) && !empty($offer->endDate)) {
			if (!JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate)) {
				$offer->isOpened = false;
			}
		} elseif (!empty($offer->endDate)) {
			if (strtotime(date('Y-m-d')) > strtotime($offer->endDate)) {
				$offer->isOpened = false;
			}
		} elseif (!empty($offer->startDate)) {
			if (strtotime(date('Y-m-d')) < strtotime($offer->startDate)) {
				$offer->isOpened = false;
			}
		}

		$offer->hasDevice = false;
		$userId           = !empty($offer->user_id) ? $offer->user_id : $offer->company->userId;
		if (!empty($userId)) {
			$apiModel = $this->getModel();
			$devices  = $apiModel->getDevicesByUser($userId);
			if (count($devices) > 0) {
				$offer->hasDevice = true;
			}
		}

		$offer->company_name = $offer->company->name;
		$offer->company_review_score   = (float) $offer->company->review_score;
		$offer->contact_link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
		$offer->description_truncated = JBusinessUtil::truncate($offer->description, 300);

		$this->sendResponse($offer);
	}

	/**
	 * Retrieves the company/event types and prints them as JSON.
	 *
	 * @since 5.0.0
	 */
	public function getTypes() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");

		$model = $this->getModel();
		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				$items = $model->getOfferTypes();
				break;
			case ITEM_TYPE_EVENT:
				$items = $model->getEventTypes();
				break;
			default:
				$items = $model->getCompanyTypes();
		}

		$tmp       = new stdClass();
		$tmp->id   = '0';
		$tmp->name = JText::_('LNG_ALL_TYPES');
		array_unshift($items, $tmp);

		$this->sendResponse($items);
	}

	/**
	 * Retrieves the company/event/offer categories and prints them as JSON.
	 *
	 * @since 5.0.0
	 */
	public function getCategories() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$lang = JFactory::getApplication()->input->get('mobileLanguage');

		$model = $this->getModel();
		$items = $model->getCategories($lang, $itemType);

		$this->sendResponse($items);
	}

	/**
	 * Retrieves the sort by configuration based on item type.
	 *
	 * @since 5.0.0
	 */
	public function getSortByConfiguration() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");

		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				$model = $this->getModel('Offers');
				$items = $model->getSortByConfiguration();
				break;
			case ITEM_TYPE_EVENT:
				$model = $this->getModel('Events');
				$items = $model->getSortByConfiguration();
				break;
			default:
				$model = $this->getModel('Search');
				$items = $model->getSortByConfiguration();
		}

		$this->sendResponse($items);
	}

	/**
	 * Retrieves reviews belonging to a certain company or offer (defined by itemType).
	 *
	 * @since 5.0.0
	 */
	public function getReviews() {
		$itemId   = (int) JFactory::getApplication()->input->get("itemId");
		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$limitstart = (int) JFactory::getApplication()->input->get("limitstart", 0);
		$limit = (int) JFactory::getApplication()->input->get("limit", 0);

		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				JFactory::getApplication()->input->set("offerId", $itemId);
				$model = $this->getModel('Offer');
				$items = $model->getReviews(null, $limitstart, $limit);
				foreach ($items as $item) {
					if (isset($item->criteriaIds)) {
						$item->criterias = $model->getReviewCriterias();
					}

					$item->id = (int) $item->id;
					$item->userId = (int) $item->userId;
					$item->rating = (float) $item->rating;
					$pics = array();
					foreach($item->pictures as $pic) {
						$pics[] = $pic->picture_path;
					}
					$item->pictures = $pics;
				}
				break;
			default:
				JFactory::getApplication()->input->set("companyId", $itemId);
				$model = $this->getModel('Companies');
				$items = $model->getReviews(null, $limitstart, $limit);
				foreach ($items as $item) {
					if (isset($item->criteriaIds)) {
						$item->criterias = $model->getReviewCriterias();
					}

					$item->id = (int) $item->id;
					$item->userId = (int) $item->userId;
					$item->rating = (float) $item->rating;
					$pics = array();
					foreach($item->pictures as $pic) {
						$pics[] = $pic->picture_path;
					}
					$item->pictures = $pics;
				}
		}

		$this->sendResponse($items);
	}

	public function likeReview() {
		$reviewId = (int) JFactory::getApplication()->input->get("reviewId");
		$model = $this->getModel('companies');
		$result = $model->increaseReviewLikeCount($reviewId);

		$this->sendResponse($result);
	}

	public function reportReview() {
		$reviewId = (int) JFactory::getApplication()->input->get("reviewId");
		$companyId = (int) JFactory::getApplication()->input->get("companyId");
		$email =  JFactory::getApplication()->input->get("email");
		$description = JFactory::getApplication()->input->get("content", '', 'RAW');
		$model = $this->getModel('companies');

		$data = array();
		$data['companyId'] = $companyId;
		$data['reviewId'] = $reviewId;
		$data['email'] = $email;
		$data['description'] = $description;

		$result = $model->reportAbuse($data);

		$this->sendResponse($result);
	}

	public function reportListing() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		
		$itemId = JFactory::getApplication()->input->get('itemId');
		$message = JFactory::getApplication()->input->getString('abuseMessage');
		$email = JFactory::getApplication()->input->getString('reporterEmail');
		$reportCause = JFactory::getApplication()->input->getString('reportCause');
		$itemType = JFactory::getApplication()->input->getString('itemType');

		$model = $this->getModel();
		$result = $model->reportListing($itemId, $message, $email, $reportCause, $itemType);
		
		$this->sendResponse($result);
	}


	/**
	 * Retrieves parameters and creates a new review
	 *
	 * @since 5.0.0
	 */
	public function addReview() {
		$itemType = (int) JFactory::getApplication()->input->get("itemType");
		$data     = JFactory::getApplication()->input->getArray();

		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				$model               = $this->getModel('Offer');
				$data["review_type"] = REVIEW_TYPE_OFFER;
				break;
			default:
				$model               = $this->getModel('Companies');
				$data["review_type"] = REVIEW_TYPE_BUSINESS;
		}

		$userId = $data["userId"];
		$itemId = $data["itemId"];

		if ($data['itemUserId'] == $data["userId"]) {
			$result = array(0 => JText::_('LNG_NO_REVIEW_FROM_ITEM_OWNER'));
			$this->sendResponse($result, RESPONSE_STATUS_ERROR);
			return;
		}

		if ($userId != 0 && $model->checkUserReviews($userId, $itemId)) {
			$result = array(0 => JText::_('LNG_NO_MORE_THAN_ONE_REVIEW'));
			$this->sendResponse($result, RESPONSE_STATUS_ERROR);
			return;
		}

		if (!isset($data["rating"])) {
			$data["rating"] = $data['review'];
		}
		
		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				$result = $model->saveReview($data);
				break;
			default:
				$companyId = JFactory::getApplication()->input->get('itemId');
				$company = $model->getPlainCompany($itemId);
				$result = $model->saveReview($data,$company);
		}
		$reviewId = $model->getState('review.id');

		if($result) {
			$this->sendResponse($reviewId , RESPONSE_STATUS_SUCCESS);
		}

		$this->sendResponse($result , RESPONSE_STATUS_SUCCESS);
	}

	public function storeReviewPictures() {
		$jinput = JFactory::getApplication()->input;
		$reviewId = (int) $jinput->get('reviewId');
		$files = $jinput->files->get('files', null, 'raw');
		$model = $this->getModel();

		$review_pictures_path = JBusinessUtil::makePathFile(REVIEW_BD_PICTURES_PATH.($reviewId)."/");

		$data = array();
		$picArray = array();
		foreach($files as $file) {
			jimport('joomla.filesystem.file');
				
			$fileNameSrc = JFile::makeSafe($file['name']);
			$picArray['picture_path'] =  $review_pictures_path.$fileNameSrc;
		
			$src = $file['tmp_name'];
			$dest = BD_PICTURES_UPLOAD_PATH .$review_pictures_path.$fileNameSrc;
		
			$result =  JFile::upload($src, $dest);
			if ($result) {
				$data['pictures'][] = $picArray;
			}
		}

		$result = $model->storeReviewsPictures($data, $reviewId);

		$this->sendResponse($result , RESPONSE_STATUS_SUCCESS);
	}

	/**
	 * Adds a new bookmark for a certain company/offer based on
	 * item_id and item_type.
	 *
	 * @since 5.0.0
	 */
	public function addBookmark() {
		$data = JFactory::getApplication()->input->getArray(
			array(
				'item_id'   => 'int',
				'user_id'   => 'int',
				'note'      => 'raw',
				'item_type' => 'int'
			)
		);

		$missingParams = array();
		foreach($data as $param => $val) {
			if(empty($val) && $param != 'note') {
				array_push($missingParams, $param);
			}
		}

		if(!empty($missingParams)) {
			$text = implode(',', $missingParams);
			$this->sendResponse("$text parameters are required", RESPONSE_STATUS_ERROR);
			return;
		}

		$itemType = $data["item_type"];
		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				$model    = $this->getModel('Offer');
				$response = $model->addBookmark($data);
				break;
			default:
				$model    = $this->getModel('Companies');
				$response = $model->addBookmark($data);
		}

		// NotificationService::sendBookmarkNotification($data["item_id"], $data["item_type"]);
		$this->sendResponse($response);
	}

	/**
	 * Updates a bookmark based on it's id.
	 *
	 * @since 5.0.0
	 */
	public function updateBookmark() {
		$data = JFactory::getApplication()->input->getArray(
			array(
				'id'        => 'int',
				'note'      => 'raw',
				'item_type' => 'int'
			)
		);

		$itemType = $data["item_type"];
		switch ($itemType) {
			case ITEM_TYPE_OFFER:
				$model    = $this->getModel('Offer');
				$response = $model->updateBookmark($data);
				break;
			default:
				$model    = $this->getModel('Companies');
				$response = $model->updateBookmark($data);
		}

		$this->sendResponse($response);
	}

	/**
	 * Removes a bookmark based on the submitted id.
	 *
	 * @since 5.0.0
	 */
	public function removeBookmark() {
		$data = JFactory::getApplication()->input->getArray(
			array(
				'item_id'   => 'int',
				'item_type' => 'int',
				'user_id'   => 'int'
			)
		);

		$model  = $this->getModel();
		$result = $model->removeBookmark($data);

		$this->sendResponse($result);
	}

	public function removeReview() {
		$data = JFactory::getApplication()->input->getArray(
			array(
				'itemId'   => 'int',
			)
		);

		$model  = $this->getModel();
		$result = $model->removeReview($data);

		$this->sendResponse($result);
	}

	/**
	 * Retrieves the user bookmarks and prints them as JSON.
	 *
	 * @since 5.0.0
	 */
	public function getUserBookmarks() {
		$userId = (int) JFactory::getApplication()->input->get("userId");
		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$model = $this->getModel();
		$items = $model->getUserBookmarks($userId);

		$this->sendResponse($items);
	}

	/**
	 * Creates and returns the user object corresponding to an id.
	 *
	 * @param $id
	 *
	 * @return mixed
	 *
	 * @since 5.8.0
	 */
	public function getUserById() {
		$userId = (int) JFactory::getApplication()->input->get("id");
		$token =  JFactory::getApplication()->input->get("token");

		$model                = $this->getModel();
		$self                 = $model->getUserByToken($token);

		$user = JBusinessUtil::getUser($userId);

		$user->ID = (int) $user->ID;

		$relationship = '';

		if(JBusinessUtil::isUserBlocked($self, $userId)) {
			$relationship .= 'blocked';
		}
	
		if (JBusinessUtil::isUserBlocker($self, $userId)) {
			if ($relationship !== '') {
				$relationship .= ' and ';
			}
			$relationship .= 'blocker';
		}
	
		$user->relationship = $relationship;

		$this->sendResponse($user);
	}

	/**
	 * Retrieves the user bookmarks and prints them as JSON.
	 *
	 * @since 5.0.0
	 */
	public function getUserReviews() {
		$userId = (int) JFactory::getApplication()->input->get("userId");
		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$appSettings = JBusinessUtil::getApplicationSettings();
		$items       = array();
		if ($appSettings->enable_reviews) {
			$model = $this->getModel();
			$items = $model->getUserReviews($userId);

			foreach($items as $review) {
				$review->rating = (float) $review->rating;
				$review->userId = (int) $review->userId;
				$review->id = (int) $review->id;
			}




		}

		$this->sendResponse($items);
	}

	/**
	 * Enables/disables push notification setting of a certain device (token) based on the
	 * value of the enable parameter.
	 *
	 * @since 5.0.0
	 */
	public function setPushNotifications() {
		$enable = (int) JFactory::getApplication()->input->get("enable");
		$token  = JFactory::getApplication()->input->get("token");

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$model  = $this->getModel();
		$result = $model->setPushNotifications($enable, $token);

		$this->sendResponse($result);
	}

	/**
	 * Sets the firebase registration token for a specific device based on its session token.
	 *
	 * @since 5.0.0
	 */
	public function setFirebaseToken() {
		$token    = JFactory::getApplication()->input->get("token", '', 'RAW');
		$firebase = JFactory::getApplication()->input->get("firebase_token", '', 'RAW');

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$model  = $this->getModel();
		$result = $model->setFirebaseToken($token, $firebase);

		$this->sendResponse($result);
	}

	/**
	 * Retrieves the display_name and password from the request, log's the user in and
	 * creates a record for the user and device and returns the session token.
	 *
	 * @since 5.0.0
	 */
	public function logIn() {
		$username = JFactory::getApplication()->input->get("username", '', 'USERNAME');
		$password = JFactory::getApplication()->input->get("password", '', 'RAW');
		$deviceId = JFactory::getApplication()->input->get("deviceId", '', 'RAW');
	
		// Check if the provided username is an email address
		if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('username'))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('email') . ' = ' . $db->quote($username));
			$db->setQuery($query);
			$result = $db->loadResult();
	
			if ($result) {
				$username = $result;
			}
		}
	
		$credentials             = array();
		$credentials['user_login'] = $username;
		$credentials['log'] = $username;
		$credentials['user_password'] = $password;
		$credentials['remember'] = true;

		$app    = JFactory::getApplication();
		$result = wp_signon($credentials);

		// login failed
		if (!$result) {
			$result = array(0 => JText::_('LNG_LOGIN_FAILED'));
			$this->sendResponse($result, RESPONSE_STATUS_ERROR);
		} else {
			$userId = $result->data->ID;

			$model = $this->getModel();
			$token = $model->saveMobileUser($userId, $deviceId);

			$tempUsr                 = $result->data;


			$user = new stdClass();
			$user->id 		    = (int) $tempUsr->ID;
			$user->name 		= $tempUsr->display_name;
			$user->username 	= $tempUsr->user_nicename;
			$user->email 		= $tempUsr->user_email;
			$user->password 	= $tempUsr->user_pass;
			$user->password_clear = $password;
			$user->token          = $token;
			// $user->groups 		= $tempUsr->roles;
			
			// $groups = $tempUsr->roles;
			$user->businessUser = 0;
			// if(in_array($this->appSettings->business_usergroup,$groups)){
			// 	$user->businessUser = 1;
			// }
			$user->id             = (int) $user->id;
			$groups               = $user->get('groups');
			$user->businessUser   = 0;
			if (in_array($this->appSettings->business_usergroup, $groups)) {
				$user->businessUser = 1;
			}
			// if false, than error has occurred
			if (!$token) {
				$this->sendResponse($token, RESPONSE_STATUS_ERROR);
			}

			// send token
			$this->sendResponse($user);
		}
	}
	
	
	

	/**
	 * Registers new user based on the request parameters. Returns user ID on success.
	 *
	 * @since 5.0.0
	 */
	public function register() {
		$name     = JFactory::getApplication()->input->get("name", '', 'USERNAME');
		$username = JFactory::getApplication()->input->get("username", '', 'USERNAME');
		$password = JFactory::getApplication()->input->get("password", '', 'RAW');
		$email    = JFactory::getApplication()->input->get("email", '', 'RAW');
		$businessUser     = JFactory::getApplication()->input->get("businessUser", '0', '0');

		$data = array(
			"display_name"      => $name,
			"user_login"  => $username,
			"first_name" => $name,
			"last_name" => '',
			"user_pass"  => $password,
			"businessUser" => $businessUser,
			'role' => 'editor'
		);

		$user_id = wp_insert_user($data);

		if (!is_numeric($user_id)) {
			$this->sendResponse(reset($user_id->errors)[0], RESPONSE_STATUS_ERROR);
		}


		// try {
		// 	if(!empty($this->appSettings->mobile_usergroup)){
		// 		JUserHelper::addUserToGroup($user->ID, $this->appSettings->mobile_usergroup);
		// 	}
		// 	if(!empty($businessUser)) {
		// 		JUserHelper::addUserToGroup($user->id, $this->appSettings->business_usergroup);
		// 	}
		// } catch (Exception $e) {
		// 	$this->sendResponse($e->getMessage(), RESPONSE_STATUS_ERROR);
		// }

		$this->logIn();
	}

	/**
	 * Logs out a device by deleting its record from the database based on the token
	 * received.
	 *
	 * @since 5.0.0
	 */
	public function logOut() {
		$model  = $this->getModel();
		$result = $model->logOut($token);

		$this->sendResponse($result);
	}
	

	public function forgotPassword()
	{

		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$data    = array('email' => JFactory::getApplication()->input->get("email", '', 'RAW'));

		// Submit the password reset request.
		$return	= $model->processResetRequest($data);

		// Check for a hard error.
		if ($return instanceof Exception)
		{
			$message = $return->getMessage() ?? JText::_('COM_USERS_RESET_REQUEST_ERROR');

			// Go back to the request form.
			$this->sendResponse($message, RESPONSE_STATUS_ERROR);
		}
		elseif ($return === false)
		{
			// The request failed.
			// Go back to the request form.
			$message = JText::_('COM_USERS_RESET_REQUEST_FAILED');
			$this->sendResponse($message, RESPONSE_STATUS_ERROR);
		}
		else
		{
			// The request succeeded.
			// Proceed to step two.
			$message = JText::_('COM_USERS_RESET_REQUEST_SUCCESS');
			$this->sendResponse($message);
		}
	}

	/**
	 * Updates the user (identified by the token) with new values
	 *
	 * @since 5.0.0
	 */
	public function updateProfile() {
		$field     = JFactory::getApplication()->input->get("field", '', 'RAW'); // name, display_name, email, password 
		$fieldVal     = JFactory::getApplication()->input->get($field, '', 'RAW'); 
		$password_clear     = JFactory::getApplication()->input->get('passwordClear', '', 'RAW'); 
		$token    = JFactory::getApplication()->input->get("token");

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$data = array(
			$field      => $fieldVal,
			"block"     => 0,
		);

		if($field == 'password') {
			$data['password2'] = $fieldVal;
		}
	
		$model                = $this->getModel();
		$user                 = $model->getUserByToken($token);
		$user->token          = $token;
		$user->password_clear = $password_clear; // change on cases when change password
		$groups = JUserHelper::getUserGroups($user->id);
			$user->businessUser = 0;
		if(in_array($this->appSettings->business_usergroup,$groups)){
			$user->businessUser = 1;
		}

		$credentials             = array();
		$credentials['display_name'] = $user->display_name;
		$credentials['password'] = $password_clear;
		$app    = JFactory::getApplication();
		$result = $app->login($credentials);

		if (!$result) {
			$this->sendResponse(JText::_('LNG_PASSWORD_NOT_CORRECT'), RESPONSE_STATUS_ERROR);
		}

		$groups         = JUserHelper::getUserGroups($user->ID);
		$data['groups'] = $groups;

		if (!$user->bind($data)) {
			$this->sendResponse($user->getError(), RESPONSE_STATUS_ERROR);
		}
		if (!$user->save()) {
			$this->sendResponse($user->getError(), RESPONSE_STATUS_ERROR);
		}

		$this->sendResponse($user, RESPONSE_STATUS_SUCCESS);
	}

	/**
	 * Logs the user and loads the control panel screen if login is successful.
	 *
	 * @since 5.0.0
	 */
	public function showControlPanel() {
		$token = JFactory::getApplication()->input->get("token");
		JFactory::getSession()->set('mobileApp', true);
		$lang = JFactory::getApplication()->input->get('mobileLanguage');
		$langParam = substr($lang, 0, 2);

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$model   = $this->getModel();
		$userObj = $model->getUserByToken($token);

		$user             = array();
		$user['id']       = $userObj->id;
		$user['name']     = $userObj->name;
		$user['display_name'] = $userObj->display_name;
		$user['password'] = $userObj->password;

		// Initiate log in
		UserService::loginUser($userObj->id);
		$user = JBusinessUtil::getUser();
		$groups = $user->roles;
		$view = 'useroptions';
		if(!in_array($this->appSettings->business_usergroup, $groups)) {
			$view = 'userdashboard';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=$view&lang='.$lang, false));
			
        
	}

	public function showServiceBookingWeb() {
		JFactory::getSession()->set('mobileApp', true);
		$lang = JFactory::getApplication()->input->get('mobileLanguage');
		$langParam = substr($lang, 0, 2);
		
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$token = $jinput->get("token");
		$serviceId = $jinput->get('serviceId');
		$providerId = $jinput->get('providerId');
		$companyId = $jinput->get('companyId');
		$date = $jinput->get('date');
		$hour = $jinput->get('hour');
		$currency_id = $jinput->get('currency_id');

		if (!$this->validateToken()) {
			$app->logout();
			$this->setRedirect(JRoute::_("index.php?option=com_jbusinessdirectory&task=serviceguestdetails.checkBillingDetails&isMobile=1&lang=$langParam&companyId=$companyId&serviceId=$serviceId&providerId=$providerId&date=$date&hour=$hour&currency_id=$currency_id&".JSession::getFormToken().'=1&isJoomla=1' , false));
			return;
		}

		$model   = $this->getModel();
		$userObj = $model->getUserByToken($token);

		$user             = array();
		$user['id']       = $userObj->id;
		$user['name']     = $userObj->name;
		$user['display_name'] = $userObj->display_name;
		$user['password'] = $userObj->password;

		UserService::loginUser($userObj->id);
		$this->setRedirect(JRoute::_("index.php?option=com_jbusinessdirectory&task=serviceguestdetails.checkBillingDetails&isMobile=1&companyId=$companyId&serviceId=$serviceId&providerId=$providerId&date=$date&hour=$hour&currency_id=$currency_id&".JSession::getFormToken().'=1&isJoomla=1' , false));
		
	}


    /**
     * Logs the user and loads the control panel screen if login is successful.
     *
     * @since 5.0.0
     */
    public function showUserPackages() {
		JFactory::getSession()->set('mobileApp', true);
        $token = JFactory::getApplication()->input->get("token");

        if (!$this->validateToken()) {
            $this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
        }

        $model   = $this->getModel();
        $userObj = $model->getUserByToken($token);

        $user             = array();
        $user['id']       = $userObj->id;
        $user['name']     = $userObj->name;
        $user['display_name'] = $userObj->display_name;
        $user['password'] = $userObj->password;

        JPluginHelper::importPlugin('user');
        // Initiate log in
		UserService::loginUser($userObj->id);
        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=packages&packageType=2', false));   
    }

	/**
	 * Returns the terms and conditions text defined in general settings
	 *
	 * @since 5.0.0
	 */
	public function getTermsAndConditions() {
		JFactory::getSession()->set('mobileApp', true);
		$type = JFactory::getApplication()->input->get('type', 'general');
		$lang = JFactory::getApplication()->input->get('mobileLanguage');
		$langParam = substr($lang, 0, 2);

		$termsUrl = JBusinessUtil::getTermsUrl($type);
		$termsUrl .= '?tmpl=component';
		$termsUrl .= "&lang=$langParam";

		$this->setRedirect($termsUrl, false);
	}

	/**
	 * Retrieves ID of the message sender, ID of the receiver, subject and body of message, and sends
	 * a notification to the receiver's device about the message.
	 *
	 * @since 5.0.0
	 */
	public function sendMessageNotification() {
		$senderId   = JFactory::getApplication()->input->getInt("senderId");
		$receiverId = JFactory::getApplication()->input->getInt("receiverId");
		$subject    = JFactory::getApplication()->input->get("subject", '', 'RAW');
		$body       = JFactory::getApplication()->input->get("body", '', 'RAW');

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$response = NotificationService::sendMessageNotification($senderId, $receiverId, $subject, $body);

		$status = RESPONSE_STATUS_SUCCESS;
		if (!$response) {
			$status = RESPONSE_STATUS_ERROR;
		}

		$this->sendResponse($response, $status);
	}


	public function getTrip() {
		$model = $this->getModel('Trip');
		$trip = $model->getTripMobile();

		foreach ($trip->pictures as $picture) {
			$trip->images[] = $picture->picture_path;
		}

		if (count($trip->images) == 0) {
			$trip->images[] = '/no_image.jpg';
		}

		$trip->id       = (int) $trip->id;
		$trip->alias    = isset($trip->alias) ? $trip->alias : '';
		// $trip->dates 	= $model->getTripAvailableDates($trip->id);

		$this->sendResponse($trip);
	}

	public function getTrips() {

		$limitStart = (int) JFactory::getApplication()->input->get("limitstart");
		$limit      = (int) JFactory::getApplication()->input->get("limit");

		if ($limitStart < $limit && $limitStart != 0) {
			$limitStart = $limit;
			JFactory::getApplication()->input->set("limitstart", $limitStart);
		}
		
		$model = $this->getModel('Trips');
		$items = $model->getItemsMobile();

		foreach($items as $item) {
			$item->pictures = explode(',' , $item->pictures);
			$item->logo = $item->pictures[0];
		}

		$this->sendResponse($items);
	}

	public function getGuestDetails() {
        $apiModel = $this->getModel();
        $token 		 = JFactory::getApplication()->input->get("token");

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}
        $user = $apiModel->getUserByToken($token);

		$model = $this->getModel('BillingDetails');
		$details = $model->getBillingDetails($user->ID);

		$this->sendResponse($details);
	}

	public function updateGuestDetails() {
        $data = JFactory::getApplication()->input->getArray();
        $apiModel = $this->getModel();
        $token 		 = $data["token"];

		if (!$this->validateToken()) {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}
        
        $user = $apiModel->getUserByToken($token);
        

		if (empty($user->ID)) {
			$result = array(0 => JText::_('LNG_USER_DOESNT_EXIST'));
			$this->sendResponse($result, RESPONSE_STATUS_ERROR);
			return;
		}

		$data['user_id'] = $user->ID;

		$model = $this->getModel('BillingDetails');
		$result = $model->updateBillingDetails($data);
		
		$this->sendResponse($result, RESPONSE_STATUS_SUCCESS);
	}

	public function checkUserBookCapability() { 
		$token 		 = JFactory::getApplication()->input->get("token");

		$user     = null;
		$apiModel = $this->getModel();
		
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		}

		$result = array();
		if (empty($user)) { 
			$result[] = array('capability' => 2, 'message' => JText::_('LNG_TRIP_BOOKING_NOT_LOGGED_IN')); 
			$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
			return; 
		}

		if(!JBusinessUtil::checkUserBookingCapability($user->ID)) {
			$result[] = array('capability' => 3, 'message' => JText::_('LNG_TRIP_BOOKING_NO_ACTIVE_PACKAGE')); 
			$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
			return; 
		}

		$result[] = array('capability' => 1, 'message' => JText::_('LNG_TRIP_CAN_BOOK')); 
		$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
	}

	public function checkTripDateAvailability() { 
		$token  = JFactory::getApplication()->input->get("token");
		$date 	=  JFactory::getApplication()->input->get("date");

		$user     = null;
		$apiModel = $this->getModel();
		
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		}

		$date = strtotime($date);
		$formattedDate = date('Y-m-d', $date);

		$result = array();
		if(!JBusinessUtil::checkTripBookingAvailability(998, $formattedDate, null)) {
			$result[] = array("availability" => 2, 'message' => JText::_('LNG_TRIP_BOOKING_NOT_AVAILABLE')); 
			$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
			return; 
		}

		$result[] = array("availability" => 1, 'message' => JText::_('LNG_TRIP_CAN_BOOK')); 
		$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
	}

	public function createTripBooking() { 
		$data     	 = JFactory::getApplication()->input->getArray(); 
		$data 		 = (object) $data;
		$token 		 = JFactory::getApplication()->input->get("token");

		$user     = null;
		$apiModel = $this->getModel();
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		}

		$tripId   = $data->tripId; 
		$tripDate = $data->tripDate;  

		$serviceDetails = new stdClass();
		$serviceDetails->tripId =  $data->tripId; 
		$serviceDetails->tripDate =  $data->tripDate; 

		$data->serviceDetails = $serviceDetails;
		
		if(!JBusinessUtil::checkUserBookingCapability($user->ID)) {
			$result = array(0 => JText::_('LNG_TRIP_BOOKING_NO_ACTIVE_PACKAGE')); 
			$this->sendResponse($result, RESPONSE_STATUS_ERROR); 
			return; 
		}

		$model = $this->getModel('Trip');
		$trip = $model->getTripMobile();
		
		$trip->dates = array();
		foreach($trip->occurrences as $occurrence) {
			$trip->dates[] = $occurrence->start_date;
		}
		
		if(!in_array($tripDate, $trip->dates)){ 
			$result = array(0 => JText::_('LNG_TRIP_BOOKING_DATE_NOT_AVAILABLE')); 
			$this->sendResponse($result, RESPONSE_STATUS_ERROR); 
			return; 
		}

		$detailsModel = $this->getModel('TripBookingDetails');
		$guestDetails = $detailsModel->getGuestDetails($user->ID);

		if(!empty($guestDetails->id)) {
			$data->guestDetails = $guestDetails;
		}

		$bookingService = new TripBookingService();
		$result = $bookingService->saveBooking($data);
 
		$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
	} 


	//TODO implement token security mechanism
	public function validateToken() {
		$token = JFactory::getApplication()->input->get("token");

		$model  = $this->getModel();
		$device = $model->getDeviceByToken($token);

		if (!empty($device)) {
			return true;
		} else {
			return false;
		}
	}

	public function checkUserToken() {
		$token = JFactory::getApplication()->input->get("token");
		$userId = JFactory::getApplication()->input->get("userId");
		$deviceId = JFactory::getApplication()->input->get("deviceId");

		if(!$this->validateToken()) {

			$model = $this->getModel();
			$newToken = $model->saveMobileUser($userId, $deviceId);

			$user                 = JBusinessUtil::getUser($userId);
			$user->token          = $newToken;
			$user->password_clear = $password;
			$user->ID 		      = (int) $user->ID;

			$this->sendResponse($user, RESPONSE_STATUS_SUCCESS); 
		}

		$result = 'OK';
		$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
	}

	/**
	 * Get's the data and status, checks if there are any errors, and
	 * prints the data and status as JSON.
	 *
	 * @param array|object $data
	 * @param int          $status
	 *
	 * @since 5.0.0
	 */
	public function sendResponse($data, $status = RESPONSE_STATUS_SUCCESS) {
//		if (!empty(error_get_last())) {
//			$status = RESPONSE_STATUS_ERROR;
//			dump(error_get_last());
//		}

        if (ob_get_length() > 0 ) {
            ob_end_clean();
        }

		$tmp = array($data);
		if (is_array($data)) {
			$tmp = $data;
		}

		$data           = array();
		$data['status'] = $status;

		if ($status == RESPONSE_STATUS_SUCCESS) {
			$data['data'] = $tmp;
		} else {
			$data['error'] = $tmp;
		}

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
		exit;
	}

	public function sendSubscriptionEmail() {
		$data  = JFactory::getApplication()->input->json->getArray();
		$response = EmailService::sendSubscriptionEmail($data);

		if($response) {
			$status = RESPONSE_STATUS_SUCCESS;
		} else {
			$status = RESPONSE_STATUS_ERROR;
		}

		$this->sendResponse($response, $status);
	}

	/**
	 * Claim a listing function
	 */
	public function claimListing() {
		$app = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$model = $this->getModel('companies');
		
		$post = JFactory::getApplication()->input->getArray(
			array(
				'companyId'   => 'String',
				'firstName'   => 'String',
				'lastName'   => 'String',
				'phone'   => 'String',
				'email'   => 'String',
			)
		);

		$token       = JFactory::getApplication()->input->get("token");
		$user     = null;
		$apiModel = $this->getModel();
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		}

		$company = $model->getPlainCompany($companyId);
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$post["ipAddress"] = $ipAddress;
		$post["userId"] = $user->ID;
		
		
		if ($model->claimCompany($post)) {
			$this->sendResponse(JText::_('LNG_CLAIM_SUCCESSFULLY'), RESPONSE_STATUS_SUCCESS);
			EmailService::sendClaimEmail($company, $post);
			
			if (!empty($appSettings->business_usergroup)) {
				$userId =$post['userId'];
				if (!JUserHelper::addUserToGroup($userId, $appSettings->business_usergroup)) {
					JFactory::getApplication()->enqueueMessage(JText::_('LNG_USER_NOT_ASSOCIATED_WITH_GROUP'), 'warning');
				}
			}
		} else {
			$this->sendResponse(JText::_('LNG_ERROR_CLAIMING_COMPANY'), RESPONSE_STATUS_ERROR);
		}

	}

	public function addToCart() {
		JFactory::getSession()->set('mobileApp', true);
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$token = $jinput->get("token");
		$cartItemsJson = $jinput->getString('json');
		$cartItemsJson = str_replace('#', '"', $cartItemsJson); // issue getting json, force double quotes
		$cartItems = json_decode($cartItemsJson, true);

		$user     = null;
		$apiModel = $this->getModel();
		
		if ($this->validateToken() && !empty($token)) {
			$userObj = $apiModel->getUserByToken($token);
			$user             = array();
			$user['id']       = $userObj->id;
			$user['name']     = $userObj->name;
			$user['display_name'] = $userObj->display_name;
			$user['password'] = $userObj->password;

			UserService::loginUser($userObj->id);
		}

		OfferSellingService::resetSession();

		foreach ($cartItems as $data) {
			OfferSellingService::initializeCartData();
			OfferSellingService::addToCart($data);
		}

		$this->setRedirect(JRoute::_("index.php?option=com_jbusinessdirectory&view=cart" , false));
	}

	public function generateOfferCoupon() {
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$token = $jinput->get("token");
		$offerId = $jinput->get("offerId");

		$user     = null;
		$apiModel = $this->getModel();
		
		if ($this->validateToken() && !empty($token)) {
			$userObj = $apiModel->getUserByToken($token);
			$user             = array();
			$user['id']       = $userObj->id;
			$user['name']     = $userObj->name;
			$user['display_name'] = $userObj->display_name;
			$user['password'] = $userObj->password;
			JPluginHelper::importPlugin('user');
			$dispatcher = JDispatcher::getInstance();
	
			// Initiate log in
			$options = array('action' => 'core.login.site', 'remember' => false);
			$dispatcher->trigger('onUserLogin', array($user, $options));
		}

		$this->setRedirect(JRoute::_("index.php?option=com_jbusinessdirectory&task=offer.generateCoupon&id=$offerId&directory=1'" , false));
	}

	public function getSellingAttributeQuantity()
	{ 
		
		$jinput = JFactory::getApplication()->input;
		$offerId = $jinput->get('offerId');
		$mainCatId = $jinput->get('mainCatId');
		$oldVal = $jinput->get('oldVal');
		$newValue = $jinput->get('newValue');

		$selectedValuesJson = $jinput->getString('selectedValues');
		$selectedValuesJson = str_replace('#', '"', $selectedValuesJson); // issue getting json, force double quotes
		$selectedValues = json_decode($selectedValuesJson, true);

		$data = array();
		$data['offerId'] = $offerId;
		$data['mainCatId'] = $mainCatId;
		$data['oldVal'] = $oldVal;
		$data['newValue'] = $newValue;
		$data['selectedValues'] = $selectedValues;


		$model = $this->getModel();
		$result = $model->updateQuantity($data);

		$this->sendResponse($result, RESPONSE_STATUS_SUCCESS); 
	}

	public function oAuthLogin() {
		// $userData, $token, $provider
		$jinput = JFactory::getApplication()->input;
		$name =  $jinput->getString('name');
		$email =  $jinput->getString('email');
		$token =  $jinput->getString('token');
		$deviceId =  $jinput->getString('deviceId');

		$userData = array();
		$userData['name'] = $name;
		$userData['email'] = $email;

		$model = $this->getModel();
		$user = $model->getUser($email);

		if (empty($user)) {
			try {
				$user = $model->createUser($userData, $token, $provider);
				$user = $model->getUser($user->email);
			} catch (Exception $e) {
				throw $e;
			}
		}

		$user = json_decode(json_encode($user), true); //convert to array
		PluginHelper::importPlugin('user');

		// Initiate log in
		$options = array('action' => 'core.login.site', 'remember' => false);
		$result = Factory::getApplication()->triggerEvent('onUserLogin', array($user, $options))[0];

		// login failed
		if (!$result) {
			$result = array(0 => JText::_('LNG_LOGIN_FAILED'));
			$this->sendResponse($result, RESPONSE_STATUS_ERROR);
		} else {
			$userId = JBusinessUtil::getUser()->ID;

			$model = $this->getModel();
			$token = $model->saveMobileUser($userId, $deviceId);

			$user                 = JBusinessUtil::getUser();
			$user->token          = $token;
			$user->password_clear = '';
			$user->ID 		      = (int) $user->ID;

			// if false, than error has occurred
			if (!$token) {
				$this->sendResponse($token, RESPONSE_STATUS_ERROR);
			}

			// send token
			$this->sendResponse($user);
		}

	}


	public function getCustomMenu() 
	{
		$language = JFactory::getApplication()->input->get('mobileLanguage');
		$model = $this->getModel();
		$results = $model->getCustomMenu($language);
		foreach($results as $menu) {
			if ($menu->type == 'url' && !preg_match('~^(?:f|ht)tps?://~i', $menu->url)) {
				$menu->url = 'http://' . $menu->url;
			}
		}
		$this->sendResponse($results);
	}


	public function contactCompany() 
	{
		$data = JFactory::getApplication()->input->getArray(
			array(
				'firstName'   => 'raw',
				'lastName'   => 'raw',
				'email'   => 'raw',
				'phone'      => 'raw',
				'description' => 'raw',
				'itemId' => 'int',
				'contact_id' => 'int',
				'itemType'	 => 'int',
			)
		);

		if($data['itemType'] == ITEM_TYPE_BUSINESS) {
			$data['companyId'] = $data['itemId'];
			$companyModel = $this->getModel('Companies');
			$result = $companyModel->contactCompany($data);
		} else if ($data['itemType'] == ITEM_TYPE_OFFER) {
			$data['offer_id'] = $data['itemId'];
			$offerModel = $this->getModel('Offer');
			$offer = JBusinessUtil::getOffer($data['offer_id']);
			$data['companyId'] = $offer->companyId;
			$data['offer_name'] = $offer->subject;
			$data['offer_alias'] = $offer->alias;
			$result = $offerModel->contactOfferCompany($data, $offer);
		} else {
			$data['event_id'] = $data['itemId'];
			$eventModel = $this->getModel('Event');
			$event = JBusinessUtil::getEvent($data['event_id']);
			$data['companyId'] = $event->company_id;
			$data['event_name'] = $event->subject;
			$data['event_alias'] = $event->alias;
			$result = $eventModel->contactEventCompany($data, $event);

		}

		$this->sendResponse($result);
	}

	/**
	 * Retrieves the user's notifications.
	 *
	 */
	public function getUserNotifications() {
		$limitstart = (int) JFactory::getApplication()->input->getInt('limitstart', 0);
		$limit = (int) JFactory::getApplication()->input->getInt('limit', 0);
		$token = JFactory::getApplication()->input->get("token");
		$user = null;
		$apiModel = $this->getModel();
		
		// Validate token and retrieve user
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		} else {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}

		$items = $apiModel->getUserNotifications($user->id, $limitstart, $limit);
		
		$this->sendResponse($items);
	}

	/**
	 * Connects to the website URL.
	 *
	 */
	public function connectWebsiteUrl() {
		$result = true;
		$this->sendResponse($result);
	}

	/**
	 * Marks a notification as read.
	 *
	 */
	public function readNotification() {
		$token = JFactory::getApplication()->input->get("token");
		$notificationId = JFactory::getApplication()->input->get("notificationId");
		
		$user = null;
		$apiModel = $this->getModel();
		
		// Validate token and retrieve user
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		} else {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}
		
		// Mark notification as read
		$result = $apiModel->readNotification($notificationId);
		
		$this->sendResponse($result);
	}

	/**
	 * Deletes a notification.
	 *
	 */
	public function deleteNotification() {
		$token = JFactory::getApplication()->input->get("token");
		$notificationId = JFactory::getApplication()->input->get("notificationId");
		
		$user = null;
		$apiModel = $this->getModel();
		
		// Validate token and retrieve user
		if ($this->validateToken() && !empty($token)) {
			$user = $apiModel->getUserByToken($token);
		} else {
			$this->sendResponse(JText::_('LNG_INVALID_TOKEN'), RESPONSE_STATUS_INVALID_TOKEN);
		}
		
		// Delete notification
		$result = $apiModel->deleteNotification($notificationId);
		
		$this->sendResponse($result);
	}

	/**
	 * Blocks a user in the chat system.
	 *
	 */
	public function blockChatUser() {
		$blocked_id = JFactory::getApplication()->input->get("blocked_id");
		$token = JFactory::getApplication()->input->get("token");

		$apiModel = $this->getModel();

		// Validate token and retrieve user
		if ($this->validateToken() && !empty($token)) {
			$user_id = $apiModel->getUserByToken($token)->id;
		} else {
			$this->sendResponse(false, RESPONSE_STATUS_INVALID_TOKEN);
		}
		
		// Block chat user
		$result = $apiModel->blockChatUser($user_id, $blocked_id);
		$this->sendResponse($result);
	}

	/**
	 * Unblocks a user in the chat system.
	 *
	 */
	public function unblockChatUser() {
		$blocked_id = JFactory::getApplication()->input->get("blocked_id");
		$token = JFactory::getApplication()->input->get("token");
		$apiModel = $this->getModel();

		// Validate token and retrieve user
		if ($this->validateToken() && !empty($token)) {
			$user_id = $apiModel->getUserByToken($token)->id;
		} else {
			$this->sendResponse(false, RESPONSE_STATUS_INVALID_TOKEN);
		}
		
		// Unblock chat user
		$result = $apiModel->unblockChatUser($user_id, $blocked_id);
		$this->sendResponse($result);
	}


	public function deleteAccount() {
		$token = JFactory::getApplication()->input->get("token");

		if ($this->validateToken() && !empty($token)) {
			$apiModel = $this->getModel();
			$userId = $apiModel->getUserByToken($token)->id;
		} else {
			$this->sendResponse(false, RESPONSE_STATUS_INVALID_TOKEN);
		}


		$user = JBusinessUtil::getUser($userId);
		$result = $user->delete();

		$this->sendResponse($result);
	}

}