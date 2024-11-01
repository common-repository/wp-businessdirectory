<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jbusinessdirectory/tables');

class JBusinessDirectoryModelDirectoryRSS extends JModelList {
	public function __construct() {
		parent::__construct();
	
		$this->appSettings = JBusinessUtil::getApplicationSettings();
	}
	
	/**
	 * Declare RSS information including the Title, URL and the Meta Description of the site.
	 */
	public function getHeaderRSS() {
		$config = JBusinessUtil::getSiteConfig();

		header('Cache-Control: no-cache, must-revalidate');
		header('content-type: text/xml');

		echo '<?xml version="1.0" encoding="utf-8"?>';
		echo '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">';
		echo '<channel>';
		echo '<title>'.htmlspecialchars($config->sitename).'</title>';
		echo '<link>'.JBusinessUtil::getWebsiteUrl(true).'</link>';
		echo '<description>'.htmlspecialchars($config->MetaDesc).'</description>';
	}

	public function getSearchParams(){
		$searchDetails = array();
		$categoryService = new JBusinessDirectorCategoryLib();
		$selectedCat = JFactory::getApplication()->input->getInt("category");
		$categoriesLevel= array();
		$cats = $categoryService->getCategoryLeafs($selectedCat, CATEGORY_TYPE_BUSINESS);
		if (isset($cats)) {
			$categoriesLevel = array_merge($categoriesLevel, $cats);
		}
		$categoriesLevel[] = $selectedCat;
		$categoriesIds[] = implode(",", $categoriesLevel);

		$searchDetails['categoriesIds'] = $categoriesIds;
		return $searchDetails;
	}

	/**
	 * Get the RSS Feeds of the companies.
	 */
	public function getCompaniesRSS() {
		$companiesTable = JTable::getInstance("Company", "JTable");
		$searchDetails = $this->getSearchParams();
		$companies = $companiesTable->getCompaniesRSS($searchDetails);
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($companies);
			JBusinessDirectoryTranslations::updateBusinessListingsSloganTranslation($companies);
		}
		
		$this->getHeaderRSS();

		foreach ($companies as $company) {
			if (empty($company->logoLocation)) {
				$company->logoLocation = DS.'no_image.jpg';
			} // If there isn't any image display the default image.

			$campanyLogo = BD_PICTURES_PATH.$company->logoLocation;

			
			if (!empty($company->categories)) {
				$company->categories = explode('#|', $company->categories);
				foreach ($company->categories as $k=>&$category) {
					$category = explode("|", $category);
				}
			}
			
			$maxCategories = !empty($company->categories)?count($company->categories):0;
			if ($this->appSettings->enable_packages) {
				$table = JTable::getInstance("Package", "JTable");
				;
				$package = $table->getCurrentActivePackage($company->id);
				if (!empty($package->max_categories) && $maxCategories > (int)$package->max_categories) {
					$maxCategories = (int)$package->max_categories;
				}
			} elseif (!empty($this->appSettings->max_categories)) {
				$maxCategories = $this->appSettings->max_categories;
			}
			
			if (!empty($company->categories)) {
				$company->categories = array_slice($company->categories, 0, $maxCategories);
			}
			
			
			echo '<item>';
			echo '<title>';
			echo '<![CDATA['.$company->name.']]>';
			echo '</title>';
			echo '<link>'.JBusinessUtil::getCompanyLink($company).'</link>';
			echo '<pubDate>';
			echo date(DATE_RSS, strtotime($company->creationDate));
			echo '</pubDate>';
			echo '<description>';
			echo '<![CDATA[<img src="'.$campanyLogo.'" width="150" /><br>'.strip_tags($company->description).']]>';
			echo '</description>';
			echo '</item>';
		}

		echo '</channel>';
		echo '</rss>';
	}

	/**
	 * Get the RSS Feeds of the offers.
	 */
	public function getOffersRSS() {
		$offersTable = JTable::getInstance("Offer", "JTable");
		$searchDetails = $this->getSearchParams();
		$offers = $offersTable->getOffersRSS($searchDetails);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateOffersTranslation($offers);
		}
		
		$this->getHeaderRSS();

		foreach ($offers as $offer) {
			if (empty($offer->picture_path)) {
				$offer->picture_path = DS.'no_image.jpg';
			}

			$offerLogo = BD_PICTURES_PATH.$offer->picture_path;

			echo '<item>';
			echo '<title>';
			echo '<![CDATA['.$offer->subject.']]>';
			echo '</title>';
			echo '<link>'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'</link>';
			echo '<pubDate>';
			echo date(DATE_RSS, strtotime($offer->created));
			echo '</pubDate>';
			echo '<description>';
			echo '<![CDATA[<img src="'.$offerLogo.'" width="150" /><br>'.strip_tags($offer->description).']]>';
			echo '</description>';
			echo '</item>';
		}

		echo '</channel>';
		echo '</rss>';
	}

	/**
	 * Get the RSS Feeds of the events.
	 */
	public function getEventsRSS() {
		$eventsTable = JTable::getInstance("Event", "JTable");
		$searchDetails = $this->getSearchParams();
		$events = $eventsTable->getEventsRSS($searchDetails);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEventsTranslation($events);
		}
		
		$this->getHeaderRSS();

		foreach ($events as $event) {
			if (empty($event->picture_path)) {
				$event->picture_path = DS.'no_image.jpg';
			}

			$eventLogo = BD_PICTURES_PATH.$event->picture_path;

			echo '<item>';
			echo '<title>';
			echo '<![CDATA['.$event->name.']]>';
			echo '</title>';
			echo '<link>'.JBusinessUtil::getEventLink($event->id, $event->alias).'</link>';
			echo '<pubDate>';
			echo date(DATE_RSS, strtotime($event->created));
			echo '</pubDate>';
			echo '<description>';
			echo '<![CDATA[<img src="'.$eventLogo.'" width="150" /><br>'.strip_tags($event->description).']]>';
			echo '</description>';
			echo '</item>';
		}

		echo '</channel>';
		echo '</rss>';
	}
}
