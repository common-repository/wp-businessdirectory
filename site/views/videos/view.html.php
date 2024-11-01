<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::enqueueStyle('libraries/chosen/chosen.css');
JBusinessUtil::enqueueScript('libraries/chosen/chosen.jquery.min.js');

// following translations will be used in js
JText::script('LNG_SHOW_FILTER');
JText::script('LNG_DISTANCE');
JText::script('LNG_MILES');
JText::script('LNG_KM');
JText::script('LNG_CATEGORIES');
JText::script('LNG_MORE');
JText::script('LNG_LESS');
JText::script('LNG_TYPES');
JText::script('LNG_COUNTRIES');
JText::script('LNG_REGIONS');
JText::script('LNG_CITIES');
JText::script('LNG_AREA');
JText::script('LNG_PROVINCE');

class JBusinessDirectoryViewVideos extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$session = JFactory::getSession();
		$this->appSettings =  JBusinessUtil::getApplicationSettings();
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");

		$this->videos = $this->get('Items');
		$jinput = JFactory::getApplication()->input;

		$this->orderBy = $jinput->getString("orderBy", "");
		$this->categoryOptions = JBusinessUtil::getCategoriesOptions(true, CATEGORY_TYPE_BUSINESS);

		$this->categorySearch = $jinput->getInt('categorySearch', null);

		$searchkeyword = $jinput->getString('searchkeyword');
		if (isset($searchkeyword)) {
			$this->searchkeyword=  $searchkeyword;
		}
		$this->selectedParams = $this->get('SelectedParams');
		$this->categories = implode(";", $this->get("SelectedCategories"));
		if (!empty($this->categories)) {
			$this->categories.=";";
		}

		$this->selectedCategories = $this->get("SelectedCategories");
		$this->preserve = $jinput->get('preserve', null);

		$categoryId= $this->get('CategoryId');
		if (!empty($categoryId) && $this->appSettings->offer_search_type != 1) {
			$this->categoryId=$categoryId;
			$this->category = $this->get('Category');
		}

		if ($this->appSettings->enable_search_filter_offers) {
			$this->searchFilter = $this->get('SeachFilter');
		}

		$this->pagination = $this->get('Pagination');
		$this->sortByOptions = $this->get('SortByConfiguration');

		parent::display($tpl);
	}
}
