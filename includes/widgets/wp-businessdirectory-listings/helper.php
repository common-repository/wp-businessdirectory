<?php
/*------------------------------------------------------------------------
 # JBusinessDirectory
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');

abstract class modJBusinessListingsHelper {
	public static function getList($params) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$enablePackage = $appSettings->enable_packages;
		// Get the database object.
		$db = JFactory::getDBO();

		$catNameFilter = "";
		if(isset($_REQUEST["video_category"])){
			$cat = $_REQUEST["video_category"];
			$catNameFilter = " and cg.name like '%$cat->name%'";
		}
		
		if(isset($_REQUEST["current_listing"]) && $params->get('listing_link')){
			$listing = $_REQUEST["current_listing"];
			$catId = $listing->mainSubcategory;
			$itemId = $listing->id;
			$catNameFilter = " and cg.id = $catId and cp.id!= $itemId";
		}

		$whereCatCond = '';
		
		$categoriesIds = $params->get('categoryIds');
        if(!is_array($categoriesIds)){
        	$categoriesIds = explode(",",$categoriesIds);
        }

		if (isset($categoriesIds) && count($categoriesIds)>0 && $categoriesIds[0]!= 0 && $categoriesIds[0]!= "") {
			$categoriesIDs = implode(",", $categoriesIds);
			$whereCatCond = " and cc.categoryId in ($categoriesIDs)";
		}

		$companyStatusFilter="and cp.approved = ".COMPANY_STATUS_APPROVED;
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}

		$packageFilter = '';
		if ($appSettings->enable_packages) {
			$packageFilter = " and (
									  (
										(inv.state= ".PAYMENT_STATUS_PAID." and (
											(
												(( now() between inv.start_date and inv.end_date) or p.expiration_type=1)
									 			or
												(now() between inv.start_trial_date and inv.end_trial_date)
											)
									 ))
									)
									or (
										((p.expiration_type=1 and p.price=0) or (p.id = cp.package_id and p.price=0)))
								)";
		}

		$only_with_logo = $params->get('only_with_logo');
		$whereLogoCond='';
		if (!empty($only_with_logo)) {
			$whereLogoCond=" and cp.logoLocation != '' ";
		}

		$city = $params->get('city');
		$whereCityCond='';
		if (!empty($city)) {
			if ($appSettings->limit_cities_regions) {
				$whereCityCond = " and cty.id = '".$city."'";
			} else {
				$whereCityCond=" and cp.city = '".$db->escape($city)."' ";
			}
		}

		$region = $params->get('region');
		$whereRegionCond='';
		if (!empty($region)) {
			if ($appSettings->limit_cities_regions) {
				$whereRegionCond = " and rg.id = '".$region."'";
			} else {
				$whereRegionCond=" and cp.county = '".$db->escape($region)."' ";
			}
		}

		$countryId = $params->get('country');
		$whereCountryCond='';
		if (!empty($countryId)) {
			$whereCountryCond=" and cp.countryId = ".$db->escape($countryId);
		}

		$type = $params->get('type');
		$whereTypeCond='';
		if (!empty($type)) {
			$type = $db->escape($type);
			$whereTypeCond=" and find_in_set($type,cp.typeId) ";
		}

		$membership = $params->get('membership');
		$leftJoinCompanyMembership = "";
		$leftJoinMemberships = "";
		$whereMembershipCondition = "";
		if (!empty($membership)) {
			$leftJoinMemberships = " left join #__jbusinessdirectory_memberships as mmb on mmb.id = $membership";
			$leftJoinCompanyMembership = " left join #__jbusinessdirectory_company_membership as cmb on cmb.company_id = cp.id";
			$whereMembershipCondition = "and cmb.membership_id = mmb.id";
		}

		$plan = $params->get('plan');
		$onlyCampaigns = $params->get('only_campaign');
		$wherePlanCondition = "";
		$leftJoinCampaign = "";
		$leftJoinCampaignHasPlans = "";
		$leftJoinCampaignPlans = "";
		if (!empty($plan) && !empty($onlyCampaigns) && JBusinessUtil::isAppInstalled(JBD_APP_CAMPAIGNS)) {
			$wherePlanCondition = " and cmp.id = $plan and cmg.published = 1 and cmg.status = ".CAMPAIGN_STATUS_PAID." and cmg.budget > 0";

			$leftJoinCampaign = " left join #__jbusinessdirectory_campaigns as cmg on cmg.company_id = cp.id";
			$leftJoinCampaignHasPlans = " left join #__jbusinessdirectory_campaign_has_plans as chp on chp.campaign_id = cmg.id";
			$leftJoinCampaignPlans = " left join #__jbusinessdirectory_campaign_plans as cmp on cmp.id = chp.campaign_plan_id";
		}

		$ordering = $params->get('order');
		if ($ordering == 1) {
			$orderBy ="slec.creationDate desc";
		} elseif ($ordering == 2) {
			$orderBy ="slec.id asc";
		} elseif ($ordering == 3) {
			$orderBy ="slec.name asc";
		} elseif ($ordering == 4) {
			$orderBy = "slec.review_score desc";
		} else {
			$orderBy = " rand() ";
		}
		$nrResults = $params->get('count');

		$radius = $params->get('radius');
		if(!empty($radius)){
			$radius = $db->escape($radius);
		}
		$jinput = JFactory::getApplication()->input;
		$latitude = $jinput->get("geo-latitude");
		if(!empty($latitude)){
			$latitude = $db->escape($latitude);
		}
		$longitude = $jinput->get("geo-longitude");
		if(!empty($longitude)){
			$longitude = $db->escape($longitude);
		}

		$enableNearby = $params->get('nearby');
		$whereCompanyCond = '';
		if ($enableNearby) {
			$companyId = $jinput->get('companyId');
			if (!empty($companyId)) {
				$company = self::getCompany($companyId);
				$latitude = $company->latitude;
				$longitude = $company->longitude;

				//if the module is in a company details page, exclude that company from the nearby list
				$whereCompanyCond = ' and cp.id not in ('.$companyId.')';


				// if company location is not set while enable nearby is active,
				// the results should be empty
				if (empty($latitude) && empty($longitude)) {
					return null;
				}
			}
		}

		$distance = "";
		if (!empty($latitude) && !empty($longitude) && $radius>0 && ($params->get('geo_location') || $enableNearby)) {
			$distance = ", 3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -( slec.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( slec.latitude) *  pi()/180) * POWER(SIN(($longitude -  slec.longitude) *  pi()/180 / 2), 2) )) as distance";
		}

		$publishFilter = '';
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";

		$having = "";
		if (!empty($latitude) && !empty($longitude) && $radius>0 && ($params->get('geo_location') || $enableNearby)) {
			$having .= " and distance < $radius";
		} else {
			$having = "";
		}
		
		$whereFeatured = '';
		$featured  = $params->get('only_featured');
		if ($featured) {
			$whereFeatured = ' and featuredListing = 1';
		}

		$searchImages = false;
		if ($params->get('viewtype')=="simple_3") {
			$searchImages = true;
		}

		$wherePackageCond = '';
		$packages  = $params->get('packages');
		if (!empty($packages) && $enablePackage) {
			$packagesIds = implode(",", $packages);
			$wherePackageCond = " and (inv.package_id in ($packagesIds) or p.id in ($packagesIds)) ";
		}

		$weekday = date("N");
		
		
		$whereListingsCond = '';
		if ($params->get('show_recently_viewed') && isset($_COOKIE['jbdViewedListings'])) {
			$listingIds = explode(",", $_COOKIE['jbdViewedListings']);
			$listingIds = array_filter($listingIds);
			if(!empty($listingIds)){
				$listingIds = implode(",",$listingIds);
				$whereListingsCond = "and cp.id in ($listingIds)";
			}
		}
		
		$query = "select slec.*, mainCategory, mainCategoryId, companyName, companyId1".
				($enablePackage?"
                ,features, GREATEST(if(FIND_IN_SET('featured_companies',features) ,1,0), featured) as featuredListing":" ,featured as featuredListing ").
				"
                $distance
				from (
				select  cp.id, cp.name, cp.alias, cp.short_description, cp.description, cp.street_number, cp.area, cp.address, cp.city, cp.county, cp.province, 
				cp.postalCode, cp.website, cp.phone, cp.email, cp.state, cp.fax, cp.business_cover_image,
				cp.averageRating, cp.slogan, cp.logoLocation, cp.creationDate, cp.latitude, cp.longitude,cp.mainSubcategory, cp.publish_start_date, cp.publish_end_date, cp.review_score, cp.yelp_id,
				cp.featured,cp.publish_only_city, cp.name as companyName,cp.id as companyId1, cn.country_name as countryName,GROUP_CONCAT(distinct ct.name) as typeName,".
				
				($appSettings->show_open_status?"
                cph.start_hour,cph.end_hour,":"").
				
				($searchImages?" GROUP_CONCAT( DISTINCT CONCAT_WS('#', cpt.picture_path, cpt.picture_title, cpt.picture_info)) as pictures, ":"").

				(!empty($whereCatCond)?"
				GROUP_CONCAT( DISTINCT cg.id) as categoryIds, GROUP_CONCAT(DISTINCT cg.name separator '#') as categoryNames,
				GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias ORDER BY cg.name separator '#') as categories,
                ":"").
				($enablePackage?"
				GROUP_CONCAT(DISTINCT pf.feature) as features,":"").
				"    
				bc.name as mainCategory,  bc.id as mainCategoryId, bc.icon as mainCategoryIcon, bc.iconImgLocation as categoryIconImage,  bc.alias as mainCategoryAlias, bc.color as mainCategoryColor
				from #__jbusinessdirectory_companies cp".
				
				(!empty($whereCatCond) || !empty($catNameFilter) ?"
				left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId
				left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId
				":"").
				"
                left join #__jbusinessdirectory_categories bc on bc.id=cp.mainSubcategory
				left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id".
				($appSettings->show_open_status?"
                left join #__jbusinessdirectory_company_provider_hours cph on cp.id = cph.provider_id and cph.item_type = ".BUSINESS_HOURS." and cph.type = ".STAFF_WORK_HOURS." and cph.status = 1 and cph.weekday = $weekday
				":"").
				"
				left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,cp.typeId)".

				($searchImages?" left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=cp.id and cpt.picture_enable = 1 ":"").

				($enablePackage?"
				left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
				left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
				left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
                ":"").
				(($appSettings->limit_cities_regions) ? "
				left join #__jbusinessdirectory_company_activity_city cat on cat.company_id=cp.id
				left join #__jbusinessdirectory_cities cty on cty.id=cat.city_id
				left join #__jbusinessdirectory_company_activity_region car on car.company_id=cp.id
				left join #__jbusinessdirectory_regions rg on rg.id=car.region_id"
					: "")
				."
                $leftJoinCampaign
				$leftJoinCampaignHasPlans
				$leftJoinCampaignPlans
				$leftJoinMemberships
				$leftJoinCompanyMembership
				where 1  $wherePlanCondition $whereCatCond $packageFilter $whereMembershipCondition and cp.state=1 $companyStatusFilter $whereCityCond $whereRegionCond $whereTypeCond 
						 $whereCountryCond $whereLogoCond $publishFilter $whereCompanyCond
						 $catNameFilter $wherePackageCond $whereListingsCond
				group by cp.id order by cp.name) as slec
				group by companyId1 
				having 1 $having $whereFeatured
				order by $orderBy";

		// Set the query and get the result list.
		$db->setQuery($query, 0, $nrResults);
		$items = $db->loadObjectlist();

		$link_target = $params->get('link_target');
		$selAttributes = $params->get('customAttributes');
		if(!empty($selAttributes)){
			$selAttributes = array_filter($selAttributes);
		}

		foreach ($items as $key => $item) {
			if (!empty($item->features)) {
				$item->package = explode(",", $item->features);
			}else{
				$item->package = array();
			}
			
			if (!empty($item->website) && $link_target == 2) {
				$item->link = $item->website;
			} else {
				$item->link = JBusinessUtil::getCompanyLink($item, true);
			}
				
			$item->mainCategoryLink = JBusinessUtil::getCategoryLink($item->mainCategoryId, $item->mainCategoryAlias);
			if ((!$enablePackage || isset($item->package) && in_array(SHOW_COMPANY_LOGO, $item->package))) {
				$item->logoLocation = 	str_replace(" ", "%20", $item->logoLocation);
			} else {
				// $item->logoLocation = "";
				// if ($only_with_logo) {
				// 	//unset($items[$key]);
				// }
			}

			$categoryId = null;
			if ($appSettings->enable_attribute_category) {
				$categoryId = -1;
				if (!empty($item->mainCategoryId)) {
					$categoryId = $item->mainCategoryId;
				}
			}

			if ($appSettings->limit_cities_regions) {
				$companiesTable = JTable::getInstance('Company');
				$item->regions = $companiesTable->getCompanyRegions($item->id);
				if (!empty($item->regions)) {
					$item->county = $item->regions[0]->name;
				}

				$item->cities  = $companiesTable->getCompanyCities($item->id);
				if (!empty($item->cities)) {
					$item->city = $item->cities[0]->name;
				}
			}
			
			if ($appSettings->show_open_status) {
				$startTime = date("Y-m-d") . " " . $item->start_hour;
				$endTime = date("Y-m-d") . " " . $item->end_hour;
				$currentTime = date('Y-m-d h:i A');
				$item->openStatus =  JBusinessUtil::checkDateInterval($startTime, $endTime, $currentTime, false, true);
			}
			
			$item->enableWorkingStatus = false;
			if (!empty($item->start_hour)) {
				$item->enableWorkingStatus = true;
			}
			
			if(!empty($selAttributes)){
				$attributesTable = JTable::getInstance('CompanyAttributes');
				$customAttributes = $attributesTable->getCompanyAttributes($item->id, $categoryId);
				
				$attributes = array();
				foreach ($customAttributes as $attr){
					if (in_array($attr->id, $selAttributes)){
						$attributes[]=$attr;
					}
				}

				$item->customAttributes = $attributes;
			}
		}

		$listings = array_values($items);

		return $listings;
	}
	
	public static function getCompany($companyId) {
		$db =JFactory::getDbo();
		$query = "select bc.*
		from #__jbusinessdirectory_companies bc
		where bc.id=".$companyId;
	
		$db->setQuery($query);
		$result =  $db->loadObject();
	
		return $result;
	}

}
