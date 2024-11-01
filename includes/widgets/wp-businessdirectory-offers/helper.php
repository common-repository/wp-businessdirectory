<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


abstract class modJBusinessOffersHelper {
	public static function getList($params) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$searchDetails = array();

		$categoriesIds = $params->get('categoryIds');
		if (isset($categoriesIds) && count($categoriesIds)>0 && $categoriesIds[0]!= 0 && $categoriesIds[0]!= "") {
			$searchDetails["categoriesIds"] = $categoriesIds;
		}

		$featured = $params->get('only_featured');
		if (isset($featured)) {
			$searchDetails["featured"] = $featured;
		}

		$packages = $params->get('packages');
		if (!empty($packages)) {
			$searchDetails["packages"] = $packages;
		}

		$ordering = $params->get('order');
		if ($ordering == 1) {
			$orderBy ="co.created desc";
		} elseif ($ordering == 2) {
			$orderBy ="co.id desc";
		} elseif ($ordering == 3) {
			$orderBy ="co.subject asc";
		} else {
			$orderBy = " rand() ";
		}

		if(isset($_REQUEST["offer-data"]) &&  $params->get('category_link')){
			$offer = $_REQUEST["offer-data"];
			$searchDetails["relatedCategoryId"] = $offer->main_subcategory;
			$searchDetails["offerId"] = $offer->id;
		}

		$nrResults = $params->get('count');

		$searchDetails["enablePackages"] = $appSettings->enable_packages;
		$searchDetails["showPendingApproval"] = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$searchDetails["orderBy"] = $orderBy;
		$searchDetails["citySearch"] = $params->get('citySearch');
		$searchDetails["regionSearch"] = $params->get('regionSearch');
		$searchDetails["featured"] = $params->get('only_featured');
		$searchDetails["typeSearch"] = $params->get('type');
		$searchDetails["item_type"] = $params->get('itemType');

		$jinput = JFactory::getApplication()->input;
		
		$latitude = $jinput->get("latitude");
		$longitude = $jinput->get("longitude");

		$searchDetails["radius"] = $params->get('radius');

		if ($params->get('geo_location')) {
			$searchDetails["latitude"] = $latitude;
			$searchDetails["longitude"] = $longitude;
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$offersTable = JTable::getInstance("Offer", "JTable");
		$offers =  $offersTable->getOffersByCategories($searchDetails, 0, $nrResults);

		foreach ($offers as $offer) {
			$offer->picture_path = $offer->picture_path ? str_replace(" ", "%20", $offer->picture_path): "";
			switch ($offer->view_type) {
				case 1:
					$offer->link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
					break;
				case 2:
					$itemId = $jinput->get('Itemid');
					$offer->link = JRoute::_("index.php?option=com_content&view=article&Itemid=$itemId&id=".$offer->article_id);
					break;
				case 3:
					$offer->link = $offer->url;
					break;
				default:
					$offer->link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
			}
		
			$offer->logoLocation = $offer->picture_path;

			$offer->specialPrice = (float)$offer->specialPrice;
			$offer->price = (float)$offer->price;
		}
		
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateOffersTranslation($offers);
			JBusinessDirectoryTranslations::updateOfferTypesTranslation($offers);
		}
		return $offers;
	}
}
