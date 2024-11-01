<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Factory;
use JeroenDesloovere\VCard\VCard;

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

require_once BD_LIBRARIES_PATH."/phpqrcode/qrlib.php";
require_once BD_LIBRARIES_PATH."/vcard/vcard.php";

class JBusinessDirectoryModelCompanies extends JModelLegacy {
	public $company = null;

	public function __construct() {
		parent::__construct();
		$this->context="com_jbusinessdirectory.listing.details";
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		$this->companyId = JFactory::getApplication()->input->get('companyId');
		$this->companyId = (int)$this->companyId;
	}

	/**
	 * Method to get a cache id based on the listing id.
	 *
	 * @param unknown $params
	 * @param string $id
	 * @return string
	 */
	protected function getCacheId($id) {
		return md5($this->context . ':' . $id);
	}
	
	public function getCompany($cmpId=null) {
		$companyId = $this->companyId;

		if (!empty($companyId)) {
			$companyId = str_replace(".html", "", $companyId);
		}

		if (isset($cmpId)) {
			$companyId = $cmpId;
		}

		if (empty($companyId)) {
			return;
		}
		
		$companyData = null;
		$cacheIdentifier = $this->getCacheId($companyId);
		try {
			if ($this->appSettings->enable_cache) {
				$cache = JCache::getInstance();
				$companyData = $cache->get($cacheIdentifier);
				if (empty($companyData)) {
					$companyData = $this->loadCompanyData($companyId);
					$cache->store($companyData, $cacheIdentifier);
				}
			} else {
				$companyData = $this->loadCompanyData($companyId);
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return null;
		}
		
		if(!empty($companyData->mainSubcategory)){
			$_REQUEST["current_listing"] = $companyData;
		}

		return $companyData;
	}

			
	public function loadCompanyData($companyId) {
		$companiesTable = $this->getTable("Company");
		$company = $companiesTable->getCompany($companyId);
		$this->company = $company;

		if (empty($this->company)) {
			return;
		}

		$package = $this->getPackage($companyId);
		$company->package = $package;

		$company->business_hours = $this->getWorkingDays($company);

		$company->enableWorkingStatus = false;
        if (!empty($company->business_hours) && $company->opening_status == COMPANY_OPEN_BY_TIMETABLE) {
            foreach ($company->business_hours as $day) {
                if ($day->workHours["status"] == '1') {
                    $company->enableWorkingStatus = true;
                }
            }
        }

        if ($company->enableWorkingStatus) {
            $company->workingStatus = $this->getWorkingStatus($company->business_hours, $company->time_zone, $company->opening_status);
        } else {
            $company->workingStatus = false;
        }
		
		$categoryTable = $this->getTable("Category", "JBusinessTable");

		$category = null;
		if (!empty($company->mainSubcategory)) {
			$category = $categoryTable->getCategoryById($company->mainSubcategory);
		} else {
			if (!empty($company->categories)) {
				$categories = explode('#|', $company->categories);
				$category = explode("|", $categories[0]);
				$category = $categoryTable->getCategoryById($category[0]);
			}
		}

		$path=array();
		if (!empty($category)) {
			$path[]=$category;
			while (!empty($category) && $category->parent_id != 1 && !empty($category->parent_id)) {
				$category= $categoryTable->getCategoryById($category->parent_id);
				$path[] = $category;
			}
			$path = array_reverse($path);
			$company->path=$path;
		}

		$company->locations = array();
		if ($this->appSettings->show_secondary_locations && (isset($package->features) && in_array(SECONDARY_LOCATIONS, $package->features) || !$this->appSettings->enable_packages)) {
			$companyLocationsTable = $this->getTable('CompanyLocations');
			$company->locations = $companyLocationsTable->getCompanyLocations($companyId);
		}
		if (isset($package->features) && in_array(FEATURED_COMPANIES, $package->features) && $this->appSettings->enable_packages) {
			$company->featured = 1;
		}

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::getInstance()->updateEntityTranslation($company, BUSSINESS_DESCRIPTION_TRANSLATION);
			JBusinessDirectoryTranslations::getInstance()->updateCategoriesTranslation($company->path);
			JBusinessDirectoryTranslations::getInstance()->updateMetaDataTranslation($company, BUSINESS_META_TRANSLATION);
		}

		if (!empty($company->description) && $company->description==strip_tags($company->description)) {
			$company->description = str_replace("\n", "<br/>", $company->description);
		}

		if (!empty($company->description)) {
			$company->description = JBusinessUtil::formatHyperlinks($company->description);
		}

		$userId = JBusinessUtil::getUser()->ID;
		$company->isBookmarked = false;
		if (!empty($userId)) {
			$bookmarkTable = $this->getTable('Bookmark');
			$company->bookmark = $bookmarkTable->getBookmark($companyId, $userId);
		}

		$company->attachments = JBusinessDirectoryAttachments::getAttachments(BUSSINESS_ATTACHMENTS, $companyId, true);
		if (!empty($company->attachments)) {
			foreach ($company->attachments as $attach) {
				$attach->properties = JBusinessUtil::getAttachProperties($attach);
			}
		}
		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);
		$company = JBusinessUtil::updateItemDefaultAtrributes($company, $attributeConfig);

		if ($this->appSettings->enable_packages && !empty($company->description) && !empty($package->max_description_length)) {
			$company->description = JBusinessUtil::truncate($company->description, $package->max_description_length, "", true);
		}

		if (!empty($company->categories)) {
			$company->categories = explode('#|', $company->categories);
			foreach ($company->categories as $k=>&$category) {
				$company->categories[$k] = explode("|", $category);
			}
		}

		$maxCategories = !empty($company->categories)?count($company->categories):0;
		if ($this->appSettings->enable_packages) {
			if (!empty($package->max_categories) && $maxCategories > (int)$package->max_categories) {
				$maxCategories = (int)$package->max_categories;
			}
		} elseif (!empty($this->appSettings->max_categories)) {
			$maxCategories = $this->appSettings->max_categories;
		}

		if (!empty($company->categories)) {
			$company->categories = array_slice($company->categories, 0, $maxCategories);
		}

		//group the categories by their main category
		if ($this->appSettings->listing_category_display == 2) {
			$categories = array();
			$companyCat = $company->categories;
			if (!empty($companyCat)) {
				foreach ($companyCat as $key => $category1) {
					//get all details about a category
					$path = JBusinessUtil::getCategoryPath($category1[0]);

					if ($this->appSettings->enable_multilingual) {
						JBusinessDirectoryTranslations::getInstance()->updateCategoriesTranslation($path);
					}

					$this->createCategoryStructure($categories, $path);
				}
			}
		}else{
			$categories = $company->categories;
		}

		$company->categoriesDetails = $categories;

		if ($this->appSettings->limit_cities_regions) {
			$table = $this->getTable('Company');
			$company->regions = $table->getCompanyRegions($company->id);
			$company->cities  = $table->getCompanyCities($company->id);
			//$company->countries  = $table->getCompanyCountries($company->id);
		}

		if (!empty($this->appSettings->trail_weeks_dates) && !empty($company->trail_weeks_hours) && $company->trail_weeks_status == 1) {
			$trailHours = json_decode($company->trail_weeks_hours, true);
			foreach ($trailHours as &$val) {
				$val = (object) $val;
			}

			$company->trailHours = $trailHours;
		}

		$_REQUEST["business-data"] = $company;

		//dispatch load listing
		Factory::getApplication()->triggerEvent('onAfterJBDLoadListing', array($company));

		return $company;
	}
	
	
	/**
	 * Parse the path and add the categories if they do not exists
	 *
	 * @param unknown $categories
	 * @param unknown $path
	 */
	public function createCategoryStructure(&$categories, $path) {
		if (empty($path)) {
			return;
		}
		
		//dump($categories);
		$cat = array_shift($path);
		//dump($cat);
		
		if (!isset($categories[$cat->id])) {
			$categories[$cat->id] = array("cat"=>$cat,"subcategories"=>array());
		}
		
		$this->createCategoryStructure($categories[$cat->id]["subcategories"], $path);
	}

	public function checkUserReviews($userId, $itemId) {
		$table = $this->getTable("Review");
		$reviews = $table->getUserReviews($userId, true, $itemId, REVIEW_TYPE_BUSINESS);

		if (count($reviews)==0) {
			return false;
		}
		return true;
	}

	public function getServicesList() {
		$hasValidService = false;
		$servicesTable = $this->getTable('CompanyServicesList', "JTable");
		$services = $servicesTable->getPriceList($this->companyId);
		foreach ($services as $service) {
			if (!empty($service->service_section)) {
				$hasValidService = true;
			}
		}
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::getInstance()->updateCompanyServiceListsTranslation($services);
		}
		//if ($hasValidService) {
			return $services;
		// } else {
		// 	return array();
		// }
	}

	public function getPlainCompany($companyId) {
		if (empty($companyId)) {
			return null;
		}

		$companiesTable = $this->getTable("Company");
		$company = $companiesTable->getCompany($companyId);
		return $company;
	}

	public function getUserRating() {
		//dump($_COOKIE['companyRatingIds']);
		$companyRatingIds=array();
		if (isset($_COOKIE['companyRatingIds'])) {
			$companyRatingIds = explode("#", $_COOKIE['companyRatingIds']);
		}

		//dump($companyRatingIds);
		$ratingId =0;
		foreach ($companyRatingIds as $companyRatingId) {
			$temp = explode(",", $companyRatingId);
			if (strcmp($temp[0], $this->companyId)==0) {
				$ratingId = $temp[1];
			}
		}

		$ratingTable = $this->getTable("Rating");
		$rating = $ratingTable->getRating($ratingId);
		//dump($rating);

		//exit;
		return $rating;
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

	public function getReviews($reviewId = null, $limitstart = 0, $limit = REVIEWS_LIMIT, $companyId = null) {
		$reviewsTable = $this->getTable("Review");

		if(empty($companyId)) {
			$companyId = $this->companyId;
		}
		
		$session = JFactory::getSession();
		$yelp_id = $session->get('company_yelp_id');
		if (GET_DATA_FROM_YELP) {
			$reviews = JBusinessUtil::getYelpData($yelp_id, true);
		} else {
			$reviews = $reviewsTable->getReviews($companyId, $this->appSettings->show_pending_review, REVIEW_TYPE_BUSINESS, $reviewId,$limitstart,$limit);
		}

		if (!empty($reviews) && !GET_DATA_FROM_YELP) {
			foreach ($reviews as $review) {
				$review->responses =  $reviewsTable->getCompanyReviewResponse($review->id);
				if (isset($review->scores)) {
					$review->scores = explode(",", $review->scores);
				}
				if (isset($review->criteria_ids)) {
					$review->criteriaIds = explode(",", $review->criteria_ids);
				}
				if (isset($review->answer_ids)) {
					$review->answerIds = explode(",", $review->answer_ids);
				}
				if (isset($review->question_ids)) {
					$review->questionIds = explode(",", $review->question_ids);

					$temp = array();
					$i = 0;
					foreach ($review->questionIds as $val) {
						$temp[$val] = $review->answerIds[$i];
						$i++;
					}
					$review->answerIds = $temp;
				}
				$review->pictures = $reviewsTable->getReviewPictures($review->id);
			}
		}

		return $reviews;
	}

	public function getTotalReviews() {
		$reviewsTable = $this->getTable("Review");

		$totalReviews = $reviewsTable->getTotalReviews($this->companyId, $this->appSettings->show_pending_review, REVIEW_TYPE_BUSINESS);

		return $totalReviews;
	}	

	public function getReviewCriterias() {
		$reviewsCriteriaTable = $this->getTable("ReviewCriteria");

		if (!$this->appSettings->enable_criteria_category) {
			$criterias = $reviewsCriteriaTable->getCriterias();
		} else {
			$criterias = $reviewsCriteriaTable->getCriteriasByCategory($this->companyId);
		}

		$result = array();
		foreach ($criterias as $criteria) {
			$result[$criteria->id]=$criteria;
		}
		$criterias = $result;

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::getInstance()->updateReviewCriteriaTranslation($criterias);
		}

		return $criterias;
	}

	public function getReviewQuestions() {
		$reviewQuestionsTable = $this->getTable("ReviewQuestion");
		$questions = $reviewQuestionsTable->getQuestions();

		$result = array();
		foreach ($questions as $question) {
			$result[$question->id]=$question;
		}
		$questions = $result;

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::getInstance()->updateReviewQuestionTranslation($questions);
		}

		return $questions;
	}

	public function getReviewQuestionAnswers() {
		$reviewAnswersTable = $this->getTable("ReviewQuestionAnswer");
		$answers = $reviewAnswersTable->getAnswersByCompany($this->companyId);

		$result = array();
		foreach ($answers as $answer) {
			$result[$answer->id]=$answer;
		}
		$answers = $result;

		return $answers;
	}

	public function getCompanyImages() {
		$query = "SELECT *
				FROM #__jbusinessdirectory_company_pictures
				WHERE picture_enable =1 and companyId =".$this->companyId ."
				ORDER BY id ";

		$pictures =  $this->_getList($query);

		return $pictures;
	}

	public function getCompanyVideos() {
		$table = $this->getTable("companyvideos");
		$videos = $table->getCompanyVideos($this->companyId);

		if (!empty($videos)) {
			foreach ($videos as $video) {
				$data = JBusinessUtil::getVideoDetails($video->url);
				$video->url = $data['url'];
				$video->videoType = $data['type'];
				$video->videoThumbnail = $data['thumbnail'];
				// $video->width = $data['width'];
				// $video->height = $data['height'];
			}
		}

		return $videos;
	}

	public function getCompanySounds() {
		$table = $this->getTable("companysounds");
		$sounds = $table->getCompanySounds($this->companyId);

		if (!empty($sounds)) {
			foreach ($sounds as &$sound) {
				$data = JBusinessUtil::getSoundDetails($sound->url);
				if(!empty($data)){
					$sound->iframe = $data['iframe'];
					$sound->type = $data['type'];
				}
			}
		}

		return $sounds;
	}


	public function getCompanyAttributes() {
		$attributesTable = $this->getTable('CompanyAttributes');
		$categoryId = null;
		if ($this->appSettings->enable_attribute_category) {
			$categoryId = -1;
			if (!empty($this->company->mainSubcategory)) {
				$categoryId= $this->company->mainSubcategory;
			}
		}

		$result = $attributesTable->getCompanyAttributes($this->companyId, $categoryId);

		return $result;
	}

	/**
	 * Method that retrieves the contacts belonging to a certain company.
	 * checks if at least one of them has an email. If so, it returns the contacts,
	 * otherwise, if none of the contact has an email, return null
	 *
	 * @return bool|array
	 */
	public function getCompanyContacts() {
		$companyContactTable = $this->getTable('CompanyContact', 'Table');
		$contacts = $companyContactTable->getAllCompanyContacts($this->companyId);

		return $contacts;
	}

	/**
	 * Method that retrieves the testimonials belonging to a certain company.
	 * checks if at least one of them has an title and a name. If so, it returns the testimonials,
	 * otherwise, if none of the testimonials has an email, return null
	 *
	 * @return bool|array
	 */
	public function getCompanyTestimonials() {
		$companyTestimonialsTable = $this->getTable('CompanyTestimonials', 'Table');
		$testimonials = $companyTestimonialsTable->getAllCompanyTestimonials($this->companyId);
		$hasValidTestimonial = false;
		foreach ($testimonials as $testimonial) {
			if (!empty($testimonial->testimonial_title) && !empty($testimonial->testimonial_name)) {
				$hasValidTestimonial = true;
			}
		}
		if ($hasValidTestimonial) {
			return $testimonials;
		} else {
			return array();
		}
	}

	/**
	 * Retrieve the current articles associated with a company
	 *
	 */
	public function getCompanyArticles() {
		$table = $this->getTable('CompanyArticles', 'Table');
		$articles = $table->getCompanyArticles($this->companyId);

		return $articles;
	}

	/**
	 * Retrieve all contacts with email address
	 */
	public function getCompanyContactsWithEmail() {
		$companyContactTable = $this->getTable('CompanyContact', 'Table');
		$contacts = $companyContactTable->getAllCompanyContacts($this->companyId);

		$result = array();
		foreach ($contacts as $contact) {
			if (!empty($contact->contact_email)) {
				$result[] = $contact;
			}
		}

		return $result;
	}

	public function getCompanyDepartments() {
		$companyContactTable = $this->getTable('CompanyContact', 'Table');
		return $companyContactTable->getAllCompanyContactsDepartment($this->companyId);
	}

	/**
	 * Get the offers for the current company
	 *
	 * @return unknown
	 */
	public function getCompanyOffers() {
		$table = $this->getTable("Offer");
		$offers = $table->getCompanyOffers($this->companyId);
		
		if (!empty($offers)) {
			JBusinessDirectoryTranslations::getInstance()->updateOffersTranslation($offers);
			$offerAttributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
			foreach ($offers as $offer) {
				switch ($offer->view_type) {
					case 1:
						$offer->link = JBusinessUtil::getofferLink($offer->id, $offer->alias);
						break;
					case 2:
						$offer->link = JRoute::_('index.php?option=com_content&view=article&id=' . $offer->article_id);
						break;
					case 3:
						$offer->link = $offer->url;
						break;
					default:
						$offer->link = JBusinessUtil::getofferLink($offer->id, $offer->alias);
				}

				if (!empty($offer->categories)) {
					$offer->categories = explode('#|', $offer->categories);
					foreach ($offer->categories as &$category) {
						$category = explode("|", $category);
					}
				}

				$offer->price = (float)$offer->price;
				$offer->specialPrice = (float)$offer->specialPrice;

				$userId = JBusinessUtil::getUser()->ID;
				$offer->isBookmarked = false;
				if (!empty($userId)) {
					$bookmarkTable = $this->getTable('Bookmark');
					$offer->bookmark = $bookmarkTable->getBookmark($offer->id, $userId, BOOKMARK_TYPE_OFFER);
				}
				$offer = JBusinessUtil::updateItemDefaultAtrributes($offer, $offerAttributeConfig);
			}
		}
		return $offers;
	}
	
	/**
	 * Get the products for the current company
	 * 
	 * @return unknown
	 */
	public function getCompanyProducts() {
		$table = $this->getTable("Offer");
		$products = $table->getCompanyOffers($this->companyId,0,0,OFFER_TYPE_PRODUCT);
		
		if (!empty($products)) {
			JBusinessDirectoryTranslations::getInstance()->updateOffersTranslation($products);
			$offerAttributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
			foreach ($products as $product) {
				
				switch ($product->view_type) {
					case 1:
						$product->link = JBusinessUtil::getofferLink($product->id, $product->alias);
						break;
					case 2:
						$product->link = JRoute::_('index.php?option=com_content&view=article&id=' . $product->article_id);
						break;
					case 3:
						$product->link = $product->url;
						break;
					default:
						$product->link = JBusinessUtil::getofferLink($product->id, $product->alias);
				}
				
				if (!empty($product->categories)) {
					$product->categories = explode('#|', $product->categories);
					foreach ($product->categories as &$category) {
						$category = explode("|", $category);
					}
				}
				
				$userId = JBusinessUtil::getUser()->ID;
				$product->isBookmarked = false;
				if (!empty($userId)) {
					$bookmarkTable = $this->getTable('Bookmark');
					$product->bookmark = $bookmarkTable->getBookmark($product->id, $userId, BOOKMARK_TYPE_OFFER);
				}
				$product = JBusinessUtil::updateItemDefaultAtrributes($product, $offerAttributeConfig);
			}
		}
		return $products;
	}

	public function getCompanyEvents() {
		$table = $this->getTable("Event");
		$events = $table->getCompanyEvents($this->companyId, 0, $this->appSettings->max_listing_events_display);
		if (!empty($events) && $this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::getInstance()->updateEventsTranslation($events);
		}
		$eventAttributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_EVENT);
		$recurringEvents = array();
		foreach ($events as $key => $event) {
			$event = JBusinessUtil::updateItemDefaultAtrributes($event, $eventAttributeConfig);
			if ($event->recurring_id > 0) {
				if (in_array($event->recurring_id, $recurringEvents)) {
					unset($events[$key]);
				} else {
					$recurringEvents[] = $event->recurring_id;
				}
				$recurringEvents[] = $event->recurring_id;
			}
		}
		return $events;
	}

	/*
	 * Retrieve the currect active package for a listing
	 */
	public function getPackage($companyId=null) {
		if (empty($companyId)) {
			$companyId = $this->companyId;
		}
		$table = $this->getTable("Package");
		$package = $table->getCurrentActivePackage($companyId);

		return $package;
	}

	public function claimCompany($data) {
		$companiesTable = $this->getTable("Company");
		$companyId = $this->companyId;

		if ($companiesTable->claimCompany($data)) {
			return $this->updateCompanyOwner($data['companyId'], $data['userId']);
		}
		return false;
	}

	public function saveReview($data, $company) {
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('review.id');
		$isNew = true;

		$itemId = $data['itemId'];
		$criterias = array();
		$questions = array();
		foreach ($data as $key=>$value) {
			if (strpos($key, "criteria")===0) {
				$key = str_replace("criteria-", "", $key);
				$criterias[$key]=$value;
			} elseif (strpos($key, "question")===0) {
				$key = str_replace("question-", "", $key);
				$questions[$key]=$value;
			}
		}

		$rating = 0;
		if (isset($data["review"])) {
			$rating = $data["review"];
		}
		if (!empty($criterias)) {
			$score = 0;
			foreach ($criterias as $key=>$value) {
				$score += $value;
			}
			$rating = $score/count($criterias);
			$data["rating"] = number_format($rating, 2);
		}

		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}
		$data["ip_address"] = $ipAddress;
		
		$table = $this->getTable("Review");

		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
		}

		$this->setState('review.id', $table->id);

		if ($this->appSettings->show_pending_review) {
			$table->updateReviewScore($data['itemId'], REVIEW_TYPE_BUSINESS);
		}

		$reviewId = $table->id;
		foreach ($criterias as $key=>$score) {
			$table = $this->getTable("ReviewUserCriteria");

			$criteriaObj = array();
			$criteriaObj["review_id"]= $reviewId;
			$criteriaObj["criteria_id"]= $key;
			$criteriaObj["score"]= $score;
			// Bind the data.
			//dump($criteriaObj);
			if (!$table->bind($criteriaObj)) {
				$this->setError($table->getError());
			}

			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
			}

			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
			}
		}

		foreach ($questions as $key=>$value) {
			$table = $this->getTable("ReviewQuestionAnswer");

			$questionObj = array();
			$questionObj["review_id"] = $reviewId;
			$questionObj["question_id"] = $key;
			$questionObj["answer"] = $value;
			$questionObj["user_id"] = $data["user_id"];

			// Bind the data.
			if (!$table->bind($questionObj)) {
				$this->setError($table->getError());
			}

			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
			}

			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
			}
		}

		if (isset($data['pictures']) && count($data['pictures'])>0) {
			$oldId = $isNew?0:$id;
			$this->storePictures($data, $reviewId, $oldId);
		}

		
		EmailService::sendNewReviewNotification($company, $data);
		
		NotificationService::sendReviewNotification($itemId);

		return true;
	}

	public function saveRating($data) {
		$table = $this->getTable("Rating");
		$ratingId = $table->saveRating($data);
		$table->updateCompanyRating($data['companyId']);

		return $ratingId;
	}

	public function getRatingsCount() {
		$companyId = $this->companyId;
		$table = $this->getTable("Rating");
		return $table->getNumberOfRatings($companyId);
	}

	public function reportAbuse($data) {

		if(!$this->appSettings->enable_reporting){
			return;
		}

		$data["state"]=1;
		$row = $this->getTable("reviewabuses");

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		$reviewsTable = $this->getTable("Review");
		$review = $reviewsTable->getReview($data["reviewId"]);
		$company=$this->getCompany($data["companyId"]);
		$ret = EmailService::sendReportAbuseEmail($data, $review, $company);

		return $ret;
	}

	public function saveReviewResponse($data) {
		//save in banners table
		$row = $this->getTable("reviewresponses");
		$data["state"]=1;

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			dump($row->getError());
			$this->setError($row->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			dump($row->getError());
			$this->setError($row->getError());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			dump($row->getError());
			$this->setError($row->getError());
			return false;
		}

		$company=$this->getCompany($data["companyId"]);
		$ret = EmailService::sendReviewResponseEmail($company, $data);

		return true;
	}

	/**
	 * Saves a single Review Question Answer
	 * @param $data
	 * @return bool
	 */
	public function saveAnswerAjax($data) {
		//save in banners table
		$row = $this->getTable("ReviewQuestionAnswer");

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			dump($row->getError());
			$this->setError($row->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			dump($row->getError());
			$this->setError($row->getError());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			dump($row->getError());
			$this->setError($row->getError());
			return false;
		}

		return true;
	}

	public function updateCompanyOwner($companyId, $userId) {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->updateCompanyOwner($companyId, $userId);
	}

	public function getCompanyByName($companyName) {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyByName($companyName);
	}

	public function contactCompany($data) {
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		if(!$applicationSettings->show_contact_form){
			return;
		}

		$company = $this->getTable("Company");
		$company->load((int)$data['companyId']);
		
		if (!empty($data['contact_id'])) {
			$contactTable = $this->getTable("CompanyContact", "Table");
			$contact = $contactTable->load(intval($data['contact_id']));

			// if the contact has no email, keep the default company email
			if (!empty($contact->contact_email)) {
				$company->email = $contact->contact_email;
			}
		}

		$data["description"] = nl2br(htmlspecialchars($data["description"], ENT_QUOTES));
		
		$package = $this->getPackage($data['companyId']);
		$company->increaseContactsNumber(intval($data['companyId']));
		$this->increaseContactsNumber((int)$data['companyId']);

		if(!empty($company->userId)){
			$ret = EmailService::sendContactCompanyEmail($company, $data);
		}else{
			$ret = EmailService::sendRequestMoreInfoEmail($company, $data);
		}

		if (isset($package->features) && in_array(SEND_EMAIL_ON_CONTACT_BUSINESS, $package->features) || !$applicationSettings->enable_packages) {
			// EmailService::sendOnContactCompanySMS($company, $data);
		}

		return $ret;
	}


	public function increaseContactsNumber($companyId) {
		// prepare the array with the table fields
		$data = array();
		$data["id"] = 0;
		$data["item_id"] = $companyId;
		$data["item_type"] = STATISTIC_ITEM_BUSINESS;
		$data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
		$data["type"] = STATISTIC_TYPE_CONTACT;
		$statisticsTable = $this->getTable("Statistics", "JTable");
		if (!$statisticsTable->save($data)) {
			return false;
		}
		return true;
	}

	public function requestQuoteCompany($data) {
		$company = $this->getTable("Company");
		$data['companyId'] = intval($data['companyId']);

		if (empty($data['companyId'])) {
			return null;
		}

		$company->load($data['companyId']);

		$company->increaseContactsNumber($data['companyId']);
		$ret = EmailService::sendRequestQuoteEmail($data, $company);

		return $ret;
	}

	/**
	 * Get the listings that are about to expire and send an email to business owners
	 */
	public function checkBusinessAboutToExpire() {
		echo "Retrieving listings ... <br/><br/>";
		$companyTable = $this->getTable("Company");
		$orderTable = $this->getTable("Order");
		$appSettings = JBusinessUtil::getApplicationSettings();
		$nrDays = $appSettings->expiration_day_notice;
		if(empty($nrDays)){
			echo '<span">Expiration notification days are not set in the directory general settings! The expiration days will be set to 3.</span>';
		}else {
			echo "Expiration days notification setup: " . $nrDays;
		}

		echo "<br/><br/>";
		$companies = $companyTable->getBusinessAboutToExpire($nrDays);
		if(!empty($companies)) {
			foreach ($companies as $company) {
				echo "sending expiration e-mail to: <strong>" . $company->name . "</strong><br/>";
				$result = EmailService::sendExpirationEmail($company, $nrDays);
				if ($result) {
					$orderTable->updateExpirationEmailDate($company->orderId);
				}
			}
		}else{
			echo "There are no expiring listings.";
		}
		
		exit;
	}

	/**
	 * Get the listings that are about to expire and send an email to business owners
	 */
	public function processExpiredOrders() {
		echo "Retrieving expired orders that have not been processed ... <br/><br/>";
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models');
		$model = JModelLegacy::getInstance('Company', 'JBusinessDirectoryModel', array('ignore_request' => true));

		$orderTable = $this->getTable("Order");

		$orders = $orderTable->getExpiredOrders();
		if(!empty($orders)){
			echo "Process expiration for: " . count($orders);
			foreach($orders as $order){
				$companyId = $order->company_id;
				$countryId = $order->countryId;
				$region = $order->region;
				$city = $order->city;
				if(!is_numeric($region)){
					$region = JBusinessUtil::getRegionByNameAndCountry($countryId,$region);
					if(!empty($region)){
						$region = $region->id;
					}
				}
				
				if(!is_numeric($this->citySearch)){
					$city = JBusinessUtil::getCityByNameAndRegion($region, $city);
					if(!empty($city)){
						$city = $city->id;
					}
				}

				$model->storeActivityCities($companyId, array($city), true);
				$model->storeActivityRegions($companyId, array(), true);
				$model->storeActivityCountries($companyId, array(), true);
				
				$orderTable->orderProcessed($order->id);
			}
		}

		exit;
	}

	public function getClaimDetails() {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getClaimDetails((int) $this->companyId);
	}

	public function increaseReviewLikeCount($reviewId) {
		$table = $this->getTable("Review");
		return $table->increaseReviewLike($reviewId);
	}

	public function increaseReviewDislikeCount($reviewId) {
		$table = $this->getTable("Review");
		return $table->increaseReviewDislike($reviewId);
	}

	public function increaseReviewLoveCount($reviewId) {
		$table = $this->getTable("Review");
		return $table->increaseReviewLove($reviewId);
	}


	public function increaseViewCount() {
		$companiesTable = $this->getTable("Company");
		if (!$companiesTable->increaseViewCount($this->companyId)) {
			return false;
		}

		// prepare the array with the table fields
		$data = array();
		$data["id"] = 0;
		$data["item_id"] = $this->companyId;
		$data["item_type"] = STATISTIC_ITEM_BUSINESS;
		$data["date"] = date('Y-m-d H:i:s');
		$data["type"] = STATISTIC_TYPE_VIEW;
		$statisticsTable = $this->getTable("Statistics", "JTable");
		if (!$statisticsTable->save($data)) {
			return false;
		}

		return true;
	}

	public function getViewCount() {
		return $this->increaseViewCount();
	}

	public function saveCompanyMessages() {
		$jinput = JFactory::getApplication()->input;

		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}

		$data = array();
		$data["name"] = $jinput->getString('firstName');
		$data["surname"] = $jinput->getString('lastName');
		$data["email"] = $jinput->getString('email');
		$data["message"] = $jinput->getString('description');
		$data["item_id"] = $this->companyId;
		$data["contact_id"] = $jinput->getInt('contact_id');
		$data["user_id"] = JBusinessUtil::getUser()->ID;
		$data["type"] = MESSAGE_TYPE_BUSINESS;
		$data["read"] = '0';
		$data["ip_address"] = $ipAddress;

		
		$table = $this->getTable("Messages");

		$data["message"] = htmlspecialchars($data["message"]);

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

		return true;
	}

	public function reportListing() {
		$jinput = JFactory::getApplication()->input;
		$applicationSettings = JBusinessUtil::getApplicationSettings();
		$companiesTable = $this->getTable("Company");
		$companyId = $this->companyId;
		$company = $companiesTable->getCompany($companyId);
		$message = $jinput->getString('abuseMessage', null);
		$email = $jinput->getString('reporterEmail', null);
		$reportCause = $jinput->getString('report-cause', null);

		$result = EmailService::sendAbuseEmail($company, $email, $message, $reportCause);

		return $result;
	}

	/**
	 * Return related companies based on the id of the actual company
	 *
	 * @return mixed
	 */
	public function getRelatedCompanies() {
		$table = $this->getTable("Company");
		$companies = $table->getRelatedCompanies($this->companyId);
		return $companies;
	}

	/**
	 * Return company memberships based on the id of the actual company
	 *
	 * @return mixed
	 */
	public function getCompanyMemberships() {
		$table = $this->getTable("Memberships", 'Table');
		$memberships = $table->getCompanyMemberships($this->companyId);
		if(!empty($memberships)){
			foreach ($memberships as $membership) {
				if (empty($membership->logo_location)) {
					$membership->logo_location = '/no_image.jpg';
				}
			}
		}
		return $memberships;
	}

	/**
	 * Method to retrieve all associated events to a particular company
	 *
	 * @return mixed
	 */
	public function getAssociatedEvents() {
		$table = $this->getTable("EventAssociatedCompanies");

		$events = $table->getAssociatedEventsDetails($this->companyId, 0, $this->appSettings->max_listing_events_display);
		$eventAttributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);

		$eventTable = $this->getTable('Event');
		foreach ($events as $event) {
			$event->pictures = $eventTable->getEventPictures($event->id);
			if (!empty($event->pictures[0])) {
				$event->picture_path = $event->pictures[0]->picture_path;
			} else {
				$event->picture_path = '';
			}
			$event = JBusinessUtil::updateItemDefaultAtrributes($event, $eventAttributeConfig);
		}

		return $events;
	}

	/**
	 * Method to retrieve all services belonging to a particular company
	 *
	 * @return mixed
	 *
	 * @since 5.0.0
	 */
	public function getServices() {
		if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/tables/companyservices.php')) {
			return null;
		}

		$table = $this->getTable("CompanyServices");

		$searchDetails              = array();
		$searchDetails["companyId"] = $this->companyId;
		$services                   = $table->getServices($searchDetails);
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::getInstance()->updateCompanyServicesTranslation($services);
		}

		return $services;
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

	/**
	 * Method that returns the working periods. If no working hours are present on the
	 * hours table, it will check the companies table, retrieve the hours from there and
	 * convert them to the new format.
	 *
	 * @param $companyId int ID of the company
	 * @return mixed
	 */
	private function getWorkingHours($company) {
		$table = $this->getTable('CompanyServiceProviders', 'JTable');
		$workingHours = $table->getStaffTimetable($company->id, STAFF_WORK_HOURS, BUSINESS_HOURS);

		// if no working hours are set, check the old business hours
		if (empty($workingHours)) {
			if (empty($companyId)) {
				return $workingHours;
			}

			// convert the old business hours to the new format
			if (!empty($company->business_hours)) {
				$openingHours = explode(",", $company->business_hours);

				for ($i=0;$i<7;$i++) {
					$tmp = new stdClass();
					$tmp->startHours = $openingHours[$i*2];
					$tmp->endHours = $openingHours[$i*2+1];
					$tmp->statuses = 1;
					$tmp->periodIds = '';

					if ($tmp->startHours == "closed") {
						$tmp->startHours = '';
					}
					if ($tmp->endHours == "closed") {
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
	 * @param $companyId int ID of the company
	 * @return mixed
	 */
	private function getBreakHours($company) {
		$table = $this->getTable('CompanyServiceProviders', 'JTable');
		$result = $table->getStaffTimetable($company->id, STAFF_BREAK_HOURS, BUSINESS_HOURS);

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
	 * @param $companyId int ID of the company
	 * @return array
	 */
	public function getWorkingDays($company) {
		$workHours = $this->getWorkingHours($company);
		$breakHours = $this->getBreakHours($company);
		$workingDays = JBusinessUtil::getWorkingDays($workHours, $breakHours);

		return $workingDays;
	}

	/**
	 *  This function take the business working hours and the time zone set`s these on the edit view and set the status
	 *  of the business if it is open or closed on different times.
	 *
	 * @param $business_hours array here are all the business hours of the business
	 * @param $time_zone string here is the offset of the time_zone, taken from the database
	 * @return bool
	 */
	public function getWorkingStatus($business_hours, $time_zone, $opening_status) {
		if ($opening_status == COMPANY_OPEN_BY_TIMETABLE && !empty($business_hours)) {

			//set timezone and create a new object date time for that timezone
			$original = new DateTime("now", new DateTimeZone('GMT'));

			//TODO replace with JBusinessUtil::getCurrentTime
			$time_zone=intval($time_zone);
			$timezoneName = timezone_name_from_abbr("", $time_zone * 3600, false);
			$modified = $original->setTimezone(new DateTimezone($timezoneName));

			$currentTime = $modified->format('H:i:s');
			$dayIndex = $modified->format('N');


			if (empty($dayIndex)) {
				$currentTime = date('Y-m-d h:i A', strtotime($modified->format('Y-m-d h:i A')));
				$dayIndex = date('N', strtotime($modified->format('Y-m-d')));
			}
			
			//check if the day index is a working day or not and if not set workingStatus on false
			if (isset($business_hours[$dayIndex]) && !empty($business_hours[$dayIndex]) && $business_hours[$dayIndex]->workHours["status"]==1) {
				$day = $business_hours[$dayIndex];

				//check if start time and end time of the working day is set.. if they are left empty than the business is
				//supposed to be open and the workingStatus will be set open
				if ((isset($day->workHours["start_time"]) && !empty($day->workHours["start_time"])) && (isset($day->workHours["end_time"]) && !empty($day->workHours["end_time"]))) {
					$startTime = date("Y-m-d",strtotime($currentTime)) . " " . $day->workHours["start_time"];
					$endTime = date("Y-m-d",strtotime($currentTime)) . " " . $day->workHours["end_time"];

					if (strtotime($endTime) < strtotime($startTime)) {
						$endTime = date("Y-m-d", strtotime($endTime. " +1 day")) . " " . $day->workHours["end_time"];
					}

					//check if there is a break on the working day or not and if not than
					//will be processed only the working hours
					if ((isset($day->breakHours["start_time"][0]) && !empty($day->breakHours["start_time"][0])) && (isset($day->breakHours["end_time"][0]) && !empty($day->breakHours["end_time"][0]))) {
						$startBreakTime = date("Y-m-d") . " " . $day->breakHours["start_time"][0];
						$endBreakTime = date("Y-m-d") . " " . $day->breakHours["end_time"][0];

						if (JBusinessUtil::checkDateInterval($startTime, $startBreakTime, $currentTime, false, true) ||
								JBusinessUtil::checkDateInterval($endBreakTime, $endTime, $currentTime, false, true)) {
							return true;
						} else {
							if ($dayIndex == 1) {
								$previousDay = $business_hours[7];
							} else {
								$previousDay = $business_hours[$dayIndex-1];
							}

							$startTime = date("Y-m-d") . " " . $previousDay->workHours["start_time"];
							$endTime = date("Y-m-d") . " " . $previousDay->workHours["end_time"];

							if (strtotime($endTime) < strtotime($startTime)) {
								return (strtotime(date('Y-m-d')) <= strtotime($endTime));
							}

							return false;
						}
					} elseif (JBusinessUtil::checkDateInterval($startTime, $endTime, $currentTime, false, true)) {
						return true;
					} else {
						if ($dayIndex == 1) {
							$previousDay = $business_hours[7];
						} else {
							$previousDay = $business_hours[$dayIndex-1];
						}

						if($previousDay->workHours["status"] == 0 ){
							return false;
						}

						$startTime = date("Y-m-d") . " " . $previousDay->workHours["start_time"];
						$endTime = date("Y-m-d") . " " . $previousDay->workHours["end_time"];

						if (strtotime($endTime) < strtotime($startTime)) {
							return (strtotime(date('Y-m-d')) <= strtotime($endTime));
						} else if (strtotime($endTime) == strtotime($startTime)) {
							return true;
						}

						return false;
					}
				} else {
					return true;
				}
			} else {
				return false;
			}
		} else if($opening_status == COMPANY_ALWAYS_OPEN) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Increase the share count of a business, offer or event, depending
	 * on the itemType given.
	 *
	 * @param $id int ID of the item
	 * @param $itemType int type of the item, business(1), offer(2) or event(3)
	 * @return bool
	 */
	public function increaseShareCount($id, $itemType) {
		// prepare the array with the table fields
		$data = array();
		$data["id"] = 0;
		$data["item_id"] = $id;
		$data["item_type"] = $itemType;
		$data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
		$data["type"] = STATISTIC_TYPE_SHARE;
		$statisticsTable = $this->getTable("Statistics", "JTable");
		if (!$statisticsTable->save($data)) {
			return false;
		}

		return true;
	}


	public function storePictures($data, $reviewId, $oldId) {
		$usedFiles = array();
		if (!empty($data['pictures'])) {
			foreach ($data['pictures'] as $value) {
				array_push($usedFiles, $value["picture_path"]);
			}
		}

		$pictures_path = JBusinessUtil::makePathFile(BD_PICTURES_UPLOAD_PATH);
		$review_pictures_path = JBusinessUtil::makePathFile(BD_REVIEW_PICTURES_PATH.($reviewId)."/");
		JBusinessUtil::removeUnusedFiles($usedFiles, $pictures_path, $review_pictures_path);

		$picture_ids 	= array();
		foreach ($data['pictures'] as $value) {
			$row = $this->getTable('ReviewPictures');

			$pic = new stdClass();
			$pic->id = 0;
			$pic->reviewId = $reviewId;
			$pic->picture_info = $value['picture_info'];
			$pic->picture_path = $value['picture_path'];
			$pic->picture_enable = $value['picture_enable'];

			$pic->picture_path = JBusinessUtil::moveFile($pic->picture_path, $reviewId, $oldId, BD_REVIEW_PICTURES_PATH);

			//dump("save");
			//dbg($pic);
			//exit;
			if (!$row->bind($pic)) {
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

			$picture_ids[] = $this->_db->insertid();
		}


		$query = " DELETE FROM #__jbusinessdirectory_review_pictures
				WHERE reviewId = '".$reviewId."'
				".(count($picture_ids)> 0 ? " AND id NOT IN (".implode(',', $picture_ids).")" : "");

		//dbg($query);
		//exit;
		$this->_db->setQuery($query);
		try {
			$this->_db->execute();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}
		//~prepare photos
		//exit;
	}

	public function getCompanyProjects() {
		$companyProjectsTable = $this->getTable('Projects', 'Table');
		$projects = $companyProjectsTable->getCompanyProjects($this->companyId);

		foreach ($projects as &$project) {
			if (!empty($project->pictures)) {
				$project->pictures = explode('#|', $project->pictures);
				$project->nrPhotos = count($project->pictures);
				foreach ($project->pictures as &$picture) {
					$picture = explode("|", $picture);
				}
				$project->picture_path = $project->pictures[0][3];
			} else {
				$project->nrPhotos = 0;
				$project->picture_path = "";
			}
		}
		return $projects;
	}

	public function getCompanyAnnouncements() {
		$table = $this->getTable('Announcements', 'Table');
		$items = $table->getCompanyAnnouncements($this->companyId);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateAnnouncementsTranslation($items);
		}

		return $items;
	}

	public function getProjectDetails($projectId) {
		$projectTable = JTable::getInstance("Projects", "Table", array());
		$project = $projectTable->getProject($projectId);


		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($project, PROJECT_DESCRIPTION_TRANSLATION);
		}

		if (!empty($project->pictures)) {
			$project->pictures = explode('#|', $project->pictures);
			$project->nrPhotos = count($project->pictures);
			foreach ($project->pictures as $index => $picture) {
				$project->pictures[$index] = explode("|", $picture);
			}
			$project->picture_path = $project->pictures[0][3];
		} else {
			$project->nrPhotos = 0;
			$project->picture_path = "";
		}


		$projectGalleryImages = "";
		if ($this->appSettings->projects_style == 2) {
			if (!empty($project->pictures)) {
				foreach ($project->pictures as $picture) {
					$itemClass = "col-lg-4 col-sm-6 col-12 mb-2";
					$itemClass = "";
					$projectGalleryImages .= "
					<div class='gallery gallery-projects $itemClass'>
						<a  href='".BD_PICTURES_PATH.$picture[3]."' title='". $picture[2] ."'>
							<div class='card jitem-card'>
								<div class='jitem-img-wrap'>
									<img class='project-picture' itemprop='image' src='".BD_PICTURES_PATH.$picture[3]."' alt='". $picture[2] ."' data-image='".BD_PICTURES_PATH.$picture[3]."'  data-description='". $picture[2] ."' />								
									<div class='card-hoverable'>
									</div>
								</div>
							</div>
						</a>
	                </div>
					";
				}
			} else {
				$projectGalleryImages .= JText::_("LNG_NO_IMAGES");
			}
		}else{
			$projectGalleryImages .= '<div id="projectImageGallery" style="display:none;">';

			if (!empty($project->pictures)) {
				foreach ($project->pictures as $picture) {
					if(isset($picture[2])){
						$projectGalleryImages .= '<img src="'.BD_PICTURES_PATH.$picture[3].'" alt="'. $picture[2] .'"  data-image="'.BD_PICTURES_PATH.$picture[3].'"  data-description="'. $picture[2] .'"/>';
					}
				}
			} else {
				$projectGalleryImages .= JText::_("LNG_NO_IMAGES");
			}
			$projectGalleryImages .= '</div>';
		}

		$project->projectGalleryImages = $projectGalleryImages;
		$project->breadCrumbsName = $project->name;

		return $project;
	}

	/**
	 * Method that retrieves all category ids related to the products of the company
	 *
	 * @return array containing ids of the categories
	 *
	 * @since 4.9.0
	 */
	private function getProductCategoriesIds() {
		$table = $this->getTable("Offer");
		$offers = $table->getCompanyOffers($this->companyId, 0, 0, OFFER_TYPE_PRODUCT);

		$categoryIds = array();
		foreach ($offers as $offer) {
			$catIds = explode(',', $offer->categoryIds);

			$categoryIds = array_unique(array_merge($categoryIds, $catIds));
		}

		return $categoryIds;
	}

	/**
	 * Method that retrieves all categories(only 2 levels) and their parent categories(only if they are lvl2 categories)
	 * from a pre existing array of category ids. It rearranges them into a multi dimensional array. The first element
	 * of the array(itself an array) will contain the parent categories. The other element, will contain all the children categories
	 * of the parents, grouped by the parent category ID as array index.
	 *
	 * (Note: Only the lvl2 categories that have products will be retrieved, lvl1 categories may or may not
	 * have products directly associated to them)
	 *
	 * @return array|null
	 *
	 * @since 4.9.0
	 */
	public function getProductCategories() {
		$table = $this->getTable("Offer");
		$catIds = $this->getProductCategoriesIds();

		$catIds = array_filter($catIds);
		if (empty($catIds)) {
			return null;
		}

		$categories = $table->getProductCategories($catIds, $this->companyId);

		$productCategories = array();
		$productCategories[1] = array();

		// include only the parent categories (lvl1)
		foreach ($categories as $cat) {
			if ($cat->level == 1) {
				$productCategories[1][] = $cat;
			}
		}

		$productCategories[2] = array();
		foreach ($productCategories[1] as $category) {
			$productCategories[2][$category->id] = new stdClass();
			$productCategories[2][$category->id]->parent = $category->name;

			// include the parent category in the children's array, if there's a product
			// associated directly to that parent category
			if (!empty($category->offerIds)) {
				$productCategories[2][$category->id]->categories[] = $category;
			}

			// include all children categories (lvl2) that belong to this specific parent
			foreach ($categories as $cat) {
				if ($cat->parent_id == $category->id && !empty($cat->offerIds)) {
					$productCategories[2][$category->id]->categories[] = $cat;
				}
			}
		}

		return $productCategories;
	}

	public function generateQrCode($itemId) {
		//set it to writable location, a place for temp generated PNG files
		$TEMP_DIR = BD_PICTURES_UPLOAD_PATH . DS . 'listingQr';

		//we need rights to create temp dir
		if (!file_exists($TEMP_DIR)) {
			mkdir($TEMP_DIR);
		}

		$PNG_TEMP_DIR = BD_PICTURES_UPLOAD_PATH . DS . 'listingQr' . DS . $itemId . DS;

		if (!file_exists($PNG_TEMP_DIR)) {
			mkdir($PNG_TEMP_DIR);
		}

		$filename = $PNG_TEMP_DIR . 'test.png';

		//options
		$errorCorrectionLevel = 'L';
		$matrixPointSize = 5;

		if ($itemId) {
			//it's very important!
			if (trim($itemId) == '') {
				die('data cannot be empty!');
			}

			$filename = $PNG_TEMP_DIR . md5($itemId) . '.png';
			$data = $this->generateVCard($itemId, true);

			//print the qrcode
			echo '<img src="http://chart.apis.google.com/chart?chs=500x500&cht=qr&chld=H&chl=' . urlencode($data) . '"/>';exit;

			//download the image
			QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2, true);
		} else {
			//default data
			echo 'You can provide data in GET parameter';
			QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		}

		// Process download
		if (file_exists($filename)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			flush(); // Flush system output buffer
			readfile($filename);
			exit;
		}
	}

	public function generateVCard($itemId, $callFromQrCode = false) {
		// define vcard
		$vcard = new VCard();
		// define variables
		$lastname = '';
		$additional = '';
		$prefix = '';
		$suffix = '';
		$company = $this->getCompany($itemId);

		// add personal data
		$vcard->addName($lastname, $company->name, $additional, $prefix, $suffix);

		if (!empty($company->email)) {
			$vcard->addEmail($company->email);
		}

		if (!empty($company->phone)) {
			$vcard->addPhoneNumber($company->phone, 'WORK');
		}

		if (!empty($company->mobile)) {
			$vcard->addPhoneNumber($company->mobile, 'PREF;WORK');
		}

		$address = JBusinessUtil::getAddressText($company);
		if (!empty($address)) {
			$vcard->addAddress($address);
		}

		if (!empty($company->website)) {
			$vcard->addURL($company->website);
		}

		if (!$callFromQrCode) {
			if (!empty($company->logoLocation)) {
				$vcard->addPhoto(BD_PICTURES_UPLOAD_PATH . $company->logoLocation);
			} else {
				$vcard->addPhoto(BD_PICTURES_UPLOAD_PATH . '/no_image.jpg');
			}
			return $vcard->download();
		} else {
			return $vcard->getOutput();
		}

		// return vcard as a string
		//return $vcard->getOutput();

		// return vcard as a download
		// return $vcard->download();

		// save the card in file in the current folder
		// return $vcard->save();
	}

	/**
	 * Process the link request from the user.
	 * Get the main company Id and the company Ids and link them
	 *
	 * @param $companyId int Id of the main company that is requested for link
	 * @param $companies array IDs of the companies trying to be linked with the main one
	 * @return bool
	 */
	public function joinCompany($companyId, $companies) {
		$table = $this->getTable('RegisteredCompany');
		$user = JBusinessUtil::getUser();

		$table->deleteNotUsedUserCompanies($companies, $companyId, $user->ID);
		$existingCompanies = $table->getExistingUserCompanies($companyId, $user->ID);
		$existingCompArray = array();
		foreach ($existingCompanies as $company) {
			$existingCompArray[] = $company->companyId;
		}

		$companies = array_diff($companies, $existingCompArray);

		if ($table->joinCompany($companyId, $companies) && count($companies)>0) {
			$table = $this->getTable('Company');
			$company = $table->getPlainCompany($companyId);
			EmailService::sendCompanyJoiningNotification($company);
		}

		return true;
	}

	/**
	 * Get companies that are linked/ joined with company with id = $companyId
	 *
	 * @param null $companyId int Id of the company
	 * @return |null
	 */
	public function getRegisteredCompanies($companyId=null) {
		$user = JBusinessUtil::getUser();
		if ($user->ID == 0) {
			return array();
		}

		$table = $this->getTable("RegisteredCompany");
		if (empty($companyId)) {
			$companyId = $this->companyId;
		}
		$result = $table->getRegisteredCompanies($companyId);
		if (empty($result)) {
			return array();
		}
		return $result;
	}

	/**
	 * get associated companies (linked and approved) to be shown on front
	 *
	 * @return mixed
	 */
	public function getAssociatedCompanies() {
		$table = $this->getTable("RegisteredCompany");

		$companies = $table->getAssociatedCompaniesDetails($this->companyId);
		return $companies;
	}

	/**
	 * Get team members
	 *
	 * @return void
	 */
	function getTeamMembers(){
	    $team = array();
	    $table = $this->getTable('CompanyMembers',"Table");
	    $members = $table->getCompanyMembers($this->companyId);
	    
	    if (!empty($members)){
	        $leadership = array();
	        $teamMembers = array();
	        foreach ($members as $member){
	            if ($member->type == MEMBER_TYPE_LEADERSHIP){
	                array_push($leadership,$member);
	            }else{
	                array_push($teamMembers,$member);
	            }
	        }
	        $team['leadership'] = $leadership;
	        $team['team'] = $teamMembers;
	    }
	    return $team;
	}

	public function getCompanyExtraImages() {
		$query = "SELECT *
				FROM #__jbusinessdirectory_company_pictures_extra
				WHERE image_enable =1 and companyId =".$this->companyId ."
				ORDER BY id ";

		$pictures =  $this->_getList($query);

		return $pictures;
	}


    public function addBookmark($data) {
        //save in banners table
        $row = $this->getTable("Bookmark");

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            dump($row->getError());
            $this->setError($row->getError());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            dump($row->getError());
            $this->setError($row->getError());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            dump($row->getError());
            $this->setError($row->getError());
            return false;
        }

        return true;
    }

    public function updateBookmark($data) {
        //save in banners table
        $row = $this->getTable("Bookmark");

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            dump($row->getError());
            $this->setError($row->getError());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            dump($row->getError());
            $this->setError($row->getError());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            dump($row->getError());
            $this->setError($row->getError());
            return false;
        }

        return true;
    }

    public function removeBookmark($data) {
        $row = $this->getTable("Bookmark");
        return $row->delete($data["id"]);
    }

}
