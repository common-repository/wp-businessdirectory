<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');
require_once(BD_HELPERS_PATH.'/category_lib.php');

class JBusinessDirectoryModelVideos extends JModelList {

	public function __construct() {
		parent::__construct();

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$jinput            = JFactory::getApplication()->input;

		$this->searchFilter = array();

		$this->keyword      = $jinput->getString('searchkeyword');
		$categorySuggestion = $jinput->getInt('categorySuggestion');
		$suggestionType     = $jinput->getInt('suggestionType');
		$this->orderBy 		= $jinput->getString("orderBy", '');

		if (!empty($categorySuggestion)) {
			if ($suggestionType == SUGGESTION_TYPE_CATEGORY) {
				$this->keyword        = '';
				$this->categorySearch = $categorySuggestion;
			}
		}

		$this->categoryId = $jinput->getInt('categoryId',null);
		if(empty($this->categoryId)){
			$catId = $jinput->getInt('offerCategoryId',null);
			if(!empty($catId)){
				$this->categoryId = $catId;
			}
		}

		if (empty($this->categorySearch)){
			$this->categorySearch = $jinput->getId('categorySearch');
		}

		$this->menuCategoryId = $jinput->getString('menuCategoryId',null);

		$session = JFactory::getSession();
		$resetSearch = $jinput->getInt('resetSearch', null);
		$session->set('vd-categorySearch', '');

		if (isset($this->categorySearch) && empty($this->categoryId)) {
			$this->categoryId = $this->categorySearch;
		}

		if (!empty($this->menuCategoryId) && empty($this->categoryId) && !isset($this->preserve)) {
			$this->categoryId = $this->menuCategoryId;
		}

		if (isset($this->categoryId)) {
			$this->categoryId = intval($this->categoryId);
		}

		$reload = $jinput->getInt('reload', null);
		if (empty($reload)) {
			if (isset($this->categoryId) || !empty($resetSearch)) {
				$session->set('vd-categorySearch', $this->categoryId);
				$session->set('vd-searchkeyword', "");
			}

			if (!empty($resetSearch)) {
				$session->set('vd-categoryId', $this->categoryId);
			}

			if (isset($this->categoryId)) {
				$this->categoryId = intval($this->categoryId);
				$session->set('vd-categoryId', $this->categoryId);
			}

			if (isset($this->keyword)) {
				$this->keyword = trim($this->keyword);
				$session->set('vd-searchkeyword', $this->keyword);
			}
		}

		$this->categoryId = $session->get('vd-categoryId');
		$this->keyword = $session->get('vd-searchkeyword');

		$mainframe = JFactory::getApplication();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$app = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $appSettings->dir_list_limit, 'int');
		$limitstart = $app->input->getInt('limitstart', 0, 'uint');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}

	public function getSearchParameters() {
		$categories = $this->getSelectedCategories();
		$categoryService = new JBusinessDirectorCategoryLib();

		$categoriesIds = array();

		if (!empty($categories)) {
			foreach ($categories as $category) {
				$categoriesLevel= array();
				$cats = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_BUSINESS);

				if (isset($cats)) {
					$categoriesLevel = array_merge($categoriesLevel, $cats);
				}
				$categoriesLevel[] = $category;
				$categoriesIds[] = implode(",", $categoriesLevel);
			}
		} elseif (!empty($this->categoryId)) {
			$categoriesIds = $categoryService->getCategoryLeafs($this->categoryId, CATEGORY_TYPE_BUSINESS);

			if (isset($this->categoryId) && $this->categoryId !=0) {
				if (isset($categoriesIds) && count($categoriesIds) > 0) {
					$categoriesIds[] = $this->categoryId;
				} else {
					$categoriesIds = array($this->categoryId);
				}
			}
			$categoriesIds = array_filter($categoriesIds);
			$categoriesIds = array(implode(",", $categoriesIds));
		}

		$params = $this->getSelectedParams();

		$searchDetails = array();
		$searchDetails["keyword"] = $this->keyword;
		$searchDetails["categoriesIds"] = $categoriesIds;
		$searchDetails["orderBy"] = $this->orderBy;
		
		return $searchDetails;
	}

	public function getSeachFilter() {
		return null; //TODO implement this
	}

	/**
	 * Method to get a cache id based on the search results.
	 *
	 * This is necessary because the different search parameters are used
	 *
	 * @param   string  $id  An identifier string to generate the cache id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.2
	 */
	protected function getCacheId($params, $id = '') {
		if (!empty($params)) {
			$params = array_filter($params);
			foreach ($params as $param) {
				if (is_array($param)) {
					$id .= ':'.implode(",", $param);
				} else {
					$id .= ':'.$param;
				}
			}
		}
		// Add the list state to the store id.
		$id .= ':'.$this->getState('list.start');
		$id .= ':'.$this->getState('list.limit');
		$id .= ':'.$this->getState('list.ordering');
		$id .= ':'.$this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	public function getSelectedParams() {
		$session = JFactory::getSession();
		$params = array();
		$values = array();
		$this->letter = JFactory::getApplication()->input->getString("letter");
		$selectedParams = JFactory::getApplication()->input->getString("selectedParams");

		if (empty($selectedParams)) {
			$selectedParams = $session->get("vd-filterParams");
		}

		if (!empty($selectedParams)) {
			$session->set("vd-filterParams", $selectedParams);
			$values = explode(";", $selectedParams);
		}

		foreach ($values as $val) {
			$temp = explode("=", $val);
			if (!isset($params[$temp[0]])) {
				$params[$temp[0]] = array();
			}

			if (!empty($temp[0])) {
				array_push($params[$temp[0]], $temp[1]);
			}
		}

		if (!empty($this->categoryId) && !isset($params["category"])) {
			$params["category"][] = $this->categoryId;
		}

		foreach ($params as $param) {
			if (in_array('', $param)) {
				unset($param[array_search('', $param)]);
			}
		}

		if (in_array('', $params)) {
			unset($params[array_search('', $params)]);
		}

		$params["selectedParams"] = $selectedParams;

		return $params;
	}

	public function getSelectedCategories() {
		$categories = array();
		$selectedCat = JFactory::getApplication()->input->getString("categories");
		if (!empty($selectedCat)) {
			$categories = explode(";", $selectedCat);
		}

		if (!empty($this->categoryId) && !isset($selectedCat)) {
			$categories[]=$this->categoryId;
		}

		if (in_array('', $categories)) {
			unset($categories[array_search('', $categories)]);
		}

		return $categories;
	}

	public function getItems() {
		$searchDetails = $this->getSearchParameters();
		JBusinessUtil::logSearch($searchDetails, SEARCH_LOG_TYPE_VIDEO);
		$videosTable = JTable::getInstance("Videos", "Table");

		$videos = array();
		if ($this->appSettings->enable_cache) {
			$cacheIdentifier = $this->getCacheId($searchDetails, "Items");
			try {
				$cache = JCache::getInstance();
				$videos = $cache->get($cacheIdentifier);
				if (empty($videos)) {
					$videos =  $videosTable->getVideosByCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
					$cache->store($videos, $cacheIdentifier);
				}
			} catch (RuntimeException $e) {
				$this->setError($e->getMessage());
				return null;
			}
		}

		if (empty($videos)) {
			$videos =  $videosTable->getVideosByCategories($searchDetails, $this->getState('limitstart'), $this->getState('limit'));
		}

		foreach ($videos as $video) {
			if (!empty($video->categories)) {
				$video->categories = explode('#|', $video->categories);
				foreach ($video->categories as &$category) {
					$category = explode("|", $category);
					if (empty($category[0])) {
						unset($category[0]);
						$category = array_values($category);
					}
				}

				$maxCategories = !empty($video->categories)?count($video->categories):0;
				if (!empty($this->appSettings->max_categories)) {
					$maxCategories = $this->appSettings->max_categories;
				}

				if (!empty($video->categories)) {
					$video->categories = array_slice($video->categories, 0, $maxCategories);
				}
			}
		}

		if ($searchDetails["orderBy"] == "") {
			$videos = $this->shuffleVideos($videos);
		}

		JFactory::getApplication()->input->set("videos-search-results", $videos);

		return $videos;
	}

	public function getCategoryId() {
		return $this->categoryId;
	}

	public function getCategory() {
		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$category = $categoryTable->getCategoryById($this->categoryId);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($category, CATEGORY_TRANSLATION);
		}

		return $category;
	}

	public function getCategories() {
		$categoryService = new JBusinessDirectorCategoryLib();
		return $categoryService->getCategories();
	}

	public function getSortByConfiguration() {
		$states = array();
		$state = new stdClass();
		$state->value = '';
		$state->text = JTEXT::_("LNG_RELEVANCE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'vd.id desc';
		$state->text = JTEXT::_("LNG_LAST_ADDED");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'vd.id asc';
		$state->text = JTEXT::_("LNG_FIRST_ADDED");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 'vd.name';
		$state->text = JTEXT::_("LNG_NAME");
		$states[] = $state;


		return $states;
	}

	/**
	 * Change the order the videos within their interval
	 *
	 * @param array $items
	 */
	public function shuffleVideos($items){
		$result = array();
		$iResult = array();
		$featured = false;

//		foreach($items as $item){
//			if($item->featured !=$featured ){
//				$featured = $item->featured;
//
//				shuffle($iResult);
//				$result = array_merge($result, $iResult);
//
//				//reset the temporary array
//				$iResult = array();
//			}
//			$iResult[] = $item;
//		}

		shuffle($items);
		$result = array_merge($result, $items);

		return $result;
	}

	public function getTotal() {
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$searchDetails = $this->getSearchParameters();
			$videosTable = JTable::getInstance("Videos", "Table");


			if ($this->appSettings->enable_cache) {
				$cacheIdentifier = $this->getCacheId($searchDetails, "getTotal");
				try {
					$cache = JCache::getInstance();
					$this->_total = $cache->get($cacheIdentifier);
					if (empty($this->_total)) {
						$this->_total = $videosTable->getTotalVideosByCategories($searchDetails);
						$cache->store($this->_total, $cacheIdentifier);
					}
				} catch (RuntimeException $e) {
					$this->setError($e->getMessage());
					return null;
					;
				}
			}

			if (empty($this->_total)) {
				$this->_total = $videosTable->getTotalVideosByCategories($searchDetails);
			}
		}
		return $this->_total;
	}
}
