<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableOffer extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_offers', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function changeStateOfferOfTheDay($offerId) {
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_company_offers SET offerOfTheDay = IF(offerOfTheDay, 0, 1) WHERE id = ".$offerId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getOffer($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = "select off.*,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color  ORDER BY cg.name separator '#|') as categories,
					c.name as company_name, c.email
					from #__jbusinessdirectory_company_offers off
					left join #__jbusinessdirectory_companies c on c.id=off.companyId
					left join #__jbusinessdirectory_company_offer_category cc on off.id=cc.offerId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1 
					where off.id='$offerId'";
		$db->setQuery($query);
		return $db->loadObject();
	}

	
	public function getActiveOffer($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$appSettings = JBusinessUtil::getApplicationSettings();
		
		$packageFilter = '';
		if ($appSettings->enable_packages && !$appSettings->item_decouple) {
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
									) and pf.feature='company_offers' ";
		}

		$companyStatusFilter="and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$companyStatusFilter = "and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}
		
		if ($appSettings->item_decouple) {
			$companyStatusFilter = "";
		}
		
		$offerStatusFilter ="and (co.approved = ".OFFER_APPROVED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$offerStatusFilter = "and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}
		
		$publishFilter='';
		$publishFilter = "and ((co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00' or co.publish_start_date is null) and ((co.publish_start_date=DATE(now()) and time(co.publish_start_time)<=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_start_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_start_time='00:00:00' or co.publish_start_time is null))
		      and ((co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00' or co.publish_end_date is null ) and ((co.publish_end_date=DATE(now()) and time(co.publish_end_time)>=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_end_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_end_time='00:00:00' or co.publish_end_time is null) )";
		$query = "select co.*, et.name as offerType, bcm.markerLocation as categoryMarker,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias  ORDER BY cg.name separator '#|') as categories  
					from #__jbusinessdirectory_company_offers co
					left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId
					left join #__jbusinessdirectory_companies cp on co.companyId = cp.id
					left join  #__jbusinessdirectory_company_offer_types et on co.offer_type=et.id
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1 
					left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
					left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
					left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
					left join #__jbusinessdirectory_categories bcm on bcm.id=co.main_subcategory and bcm.published=1
					where co.id='$offerId' $packageFilter $companyStatusFilter $offerStatusFilter $publishFilter
					group by co.id
					";
		$db->setQuery($query);

		$result = $db->loadObject();
		return $result;
	}
	
	public function getOffers($filter, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$query = "select co.*,cp.name as companyName from #__jbusinessdirectory_company_offers co
				left join  #__jbusinessdirectory_companies cp on cp.id=co.companyId
		$filter";
		// 		dump($query);
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getTotalOffers() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_offers";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getTotalOffersByCompany($companyId, $offerCountType) {
		$db =JFactory::getDBO();
		$activeFilter = "";
		$companyId = $db->escape($companyId);
		
		$activeFilter = "";
		if ($offerCountType=="2") { //retrieve active offers only
			$activeFilter = " and state=1 and CURDATE() <= endDate";
		}
			
		$query = "select * from #__jbusinessdirectory_company_offers where companyId = $companyId $activeFilter";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function changeState($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = "UPDATE #__jbusinessdirectory_company_offers SET state = IF(state, 0, 1) WHERE id = ".$offerId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function changeStateFeatured($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = 	" UPDATE #__jbusinessdirectory_company_offers SET featured = IF(featured, 0, 1) WHERE id = ".$offerId ;
		$db->setQuery($query);
	
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	

	public function getCompanyOffers($companyId, $limitstart = 0, $limit = 0, $type = OFFER_TYPE_OFFER) {
		$db =JFactory::getDBO();
		$orderByClause= "order by co.id desc";
		
		if (!empty($orderBy)) {
			$orderByClause = "order by $orderBy";
		}

		$appSettings =  JBusinessUtil::getApplicationSettings();
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$approvalFilter="and (co.approved = ".OFFER_APPROVED.")";
		if ($showPendingApproval) {
			$approvalFilter = "and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}

		
		$publishFilter = "and ((co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00' or co.publish_start_date is null) and ((co.publish_start_date=DATE(now()) and time(co.publish_start_time)<=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_start_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_start_time='00:00:00' or co.publish_start_time is null))
						 and ((co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00' or co.publish_end_date is null ) and ((co.publish_end_date=DATE(now()) and time(co.publish_end_time)>=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_end_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_end_time='00:00:00' or co.publish_end_time is null) )";

		$whereTypeCond = ' and co.item_type = '.$type;
		
		$query = " select co.id, co.price , co.specialPrice, co.subject, co.short_description, co.address, co.city, co.county, co.province, co.area, co.latitude, co.longitude, co.startDate, co.endDate, co.alias, co.view_type, co.article_id, co.url, co.quantity, co.min_purchase, co.max_purchase,
					op.picture_info,op.picture_path,op.picture_title, co.currencyId, co.price_text,co.time_zone, co.add_to_price_list, co.enable_offer_selling,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.name separator '#|') as categories,
					GROUP_CONCAT(DISTINCT cg.id) as categoryIds,
					sum(os.qty) as stock_quantity
					from
					#__jbusinessdirectory_company_offers co
					left join  #__jbusinessdirectory_company_offer_pictures op on co.id=op.offerId
					and
						(op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_offers co1
							left join  #__jbusinessdirectory_company_offer_pictures op1 on co1.id=op1.offerId
							where op1.picture_enable=1
							group by co1.id
						)
					)
					left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					left join #__jbusinessdirectory_offer_stock as os on os.offer_id = co.id
					where co.companyId=$companyId and co.state=1 and (co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00' or co.publish_start_date is null) and  (co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00'or co.publish_end_date is null) 
					$approvalFilter $whereTypeCond $publishFilter
					group by co.id, op.id
					$orderByClause ";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	public function getTotalCompanyOffers($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_offers where companyId='$companyId'";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}
	
	public function getUserOffers($userId, $companyIds, $itemType = null, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		if (empty($companyIds) && empty($userId)) {
			return null;
		}
		
		if (empty($companyIds)) {
			$companyIds = array(-1);
		}

		$whereItemType = '';
		if (!empty($itemType)) {
			$whereItemType = " and co.item_type = $itemType";
		}
		
		$companyIds = implode(",", $companyIds);
		
		$query = "select co.*, op.picture_path from #__jbusinessdirectory_company_offers co
                    left join #__jbusinessdirectory_companies cp on cp.id = co.companyId
					left join  #__jbusinessdirectory_company_offer_pictures op on co.id=op.offerId
					and
						(op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_offers co1
							left join  #__jbusinessdirectory_company_offer_pictures op1 on co1.id=op1.offerId
							where op1.picture_enable=1
							group by co1.id
						)
					)
					where ( companyId in ($companyIds) or cp.userId = $userId or user_id=$userId ) $whereItemType
					group by co.id	
					order by co.id desc";
		
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	public function getTotalUserOffers($companyIds, $userId, $approved = false) {
		$db =JFactory::getDBO();

		$comCond = "";
		if (count($companyIds)>0) {
			$companyIds = implode(",", $companyIds);
			$comCond = " and co.companyId in ($companyIds)";
		}

		$approvedCond = "";
		if ($approved) {
			$approvedCond = " and co.approved='1' ";
		}

		$query = "select * from #__jbusinessdirectory_company_offers co
                    left join #__jbusinessdirectory_companies cp on cp.id = co.companyId
                     where 1  $comCond  or cp.userId = $userId or co.user_id=$userId $approvedCond ";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getOfferPictures($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = "select * from #__jbusinessdirectory_company_offer_pictures where offerId='$offerId' and picture_enable=1 order by id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getOffersByCategoriesSql($searchDetails, $totalCategories = false) {
		$db =JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
		
		foreach ($searchDetails as &$searchDetail) {
			if (!empty($searchDetail) && !is_array($searchDetail)) {
				$searchDetail = stripslashes($searchDetail);
				$searchDetail = $db->escape($searchDetail);
			}
		}
		
		$keyword = isset($searchDetails['keyword'])?$searchDetails['keyword']:null;
		$categoriesIDs = isset($searchDetails["categoriesIds"])?$searchDetails["categoriesIds"]:null;
		$latitude = isset($searchDetails["latitude"])?$searchDetails["latitude"]:null;
		$longitude = isset($searchDetails["longitude"])?$searchDetails["longitude"]:null;
		$radius = isset($searchDetails["radius"])?$searchDetails["radius"]:null;
		$city = isset($searchDetails["citySearch"])?$searchDetails["citySearch"]:null;
		$region = isset($searchDetails["regionSearch"])?$searchDetails["regionSearch"]:null;
		$enablePackage = isset($searchDetails["enablePackages"])?$searchDetails["enablePackages"]:null;
		$showPendingApproval = isset($searchDetails["showPendingApproval"])?$searchDetails["showPendingApproval"]:null;
		$orderBy = isset($searchDetails["orderBy"])?$searchDetails["orderBy"]:null;
		$featured = isset($searchDetails["featured"])?$searchDetails["featured"]:null;
		$asc_desc = isset($searchDetails["asc_desc"])?$searchDetails["asc_desc"]:null;
		$multilingual = isset($searchDetails["multilingual"])?$searchDetails["multilingual"]:null;
		$area = isset($searchDetails["areaSearch"])?$searchDetails["areaSearch"]:null;
		$facetedSearch = isset($searchDetails["facetedSearch"])?$searchDetails["facetedSearch"]:null;
		$offerType = isset($searchDetails["type"])?$searchDetails["type"]:null;
		$company = isset($searchDetails["companyId"])?$searchDetails["companyId"]:null;
		$minPrice = isset($searchDetails["minprice"])?$searchDetails["minprice"]:null;
		$maxPrice = isset($searchDetails["maxprice"])?$searchDetails["maxprice"]:null;
		$customAttributes = isset($searchDetails["customAttributes"])?$searchDetails["customAttributes"]:null;
		$countryId = isset($searchDetails["countrySearch"])?$searchDetails["countrySearch"]:null;
		$province = isset($searchDetails["provinceSearch"])?$searchDetails["provinceSearch"]:null;
		$type = isset($searchDetails["typeSearch"])?$searchDetails["typeSearch"]:null;
		$itemType = isset($searchDetails["item_type"])?$searchDetails["item_type"]:null;
		$filterByFav = isset($searchDetails["filter-by-fav"])?$searchDetails["filter-by-fav"]:null;
		$relatedCategoryId = isset($searchDetails["relatedCategoryId"])?$searchDetails["relatedCategoryId"]:null;
		$offer = isset($searchDetails["offerId"])?$searchDetails["offerId"]:null;
		$packages = isset($searchDetails["packages"])?$searchDetails["packages"]:null;
		$offerIds = isset($searchDetails["offer-ids"])?$searchDetails["offer-ids"]:null;
		$showCountryFlag = isset($searchDetails["showCountryFlag"])?$searchDetails["showCountryFlag"]:null;

		$user = JBusinessUtil::getUser();

		$whereRelatedCatCond = '';
		if(!empty($relatedCategoryId)) {
			$whereRelatedCatCond = " and co.main_subcategory = $relatedCategoryId and co.id != $offer";
		}
		
		$whereCatCond = '';
		if (!empty($categoriesIDs) && count($categoriesIDs)>0) {
			$whereCatCond .= " and cc.categoryId in (";
			$categoryIds = implode(", ", $categoriesIDs);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}

		if ($facetedSearch == 1) {
			if (!empty($categoriesIDs)) {
				//dump($categoriesIDs);
				foreach ($categoriesIDs as $categoryId) {
					$values = explode(",", $categoryId);
					$whereCatCond .= ' or (0  ';
					foreach ($values as $value) {
						$whereCatCond .= " or cg.id REGEXP '\\\\b".$value."\\\\b' ";
					}
					$whereCatCond .= ' ) ';
				}
			}
		}

		//custom work - show offers only from a specific category when the user is not login
		$whereUserCatCond = "";
		if(empty($user->ID)){
			//$whereUserCatCond = " and cc.categoryId not in (516) ";
		}

		$whereNameCond='';
		if (!empty($keyword)) {
			$translationCondition = '';
			if ($multilingual) {
				$translationCondition = " or t.name like '%$keyword%'";
			}
				
			$keywords = explode(" ", $keyword);
			$fields= array("co.subject","cg.name","co.meta_keywords","co.meta_description","co.short_description","co.address","co.city","co.county");
			if (!$appSettings->item_decouple) {
				$fields[] = "cp.name";
			}
				
			$sqlFilter="";
			foreach ($fields as $field) {
				if ($field=="cg.name") {
					continue;
				}
				$sqlFilter .= "("."$field LIKE '%".implode("%' and $field LIKE '%", $keywords) . "%') OR ";
			}
				
			$whereNameCond=" and ($sqlFilter  co.subject like '%$keyword%' $translationCondition) ";
		}
		
		$whereCityCond='';
		if (!empty($city)) {
			$whereCityCond=" and co.city = '".$city."' ";
		}

		$whereOfferTypeCond='';
		if (!empty($type)) {
			$whereOfferTypeCond=" and co.offer_type = $type";
		}
		
		$whereRegionCond='';
		if (!empty($region)) {
			$whereRegionCond=" and co.county = '".$region."' ";
		}

		$whereAreaCond='';
		if (!empty($area)) {
			$whereAreaCond=" and co.area = '".$area."' ";
		}

		$whereProvinceCond='';
		if (!empty($province)) {
			$whereProvinceCond=" and co.province = '".$province."' ";
		}

		$whereTypeCond = '';
		if (!empty($offerType)) {
			$whereTypeCond = ' and co.type = '.$offerType;
		}

		$whereItemType = '';
		if (!empty($itemType)) {
			$whereItemType = ' and co.item_type = '.$itemType;
		}

		$whereCompanyCond = '';
		if (!empty($company)) {
			$whereCompanyCond=" and co.companyId = '".$company."' ";
		}
		
		$wherePriceCond="";
		if (!empty($minPrice) && !empty($maxPrice)) {
			$wherePriceCond="and ((co.price between '$minPrice' and '$maxPrice') or (co.specialPrice between '$minPrice' and '$maxPrice'))";
		} elseif (!empty($minPrice)) {
			$wherePriceCond="and (co.price >='$minPrice' or co.specialPrice >='$minPrice')";
		} elseif (!empty($maxPrice)) {
			$wherePriceCond="and (co.price <='$maxPrice' or co.specialPrice <='$maxPrice')";
		}
		
		if ($orderBy == "distance asc" && (empty($latitude) || empty($longitude))) {
			$orderBy ="";
		}
		
		$distanceQuery = "";
		if (($orderBy == "distance asc" || $radius>0) && !empty($latitude) && !empty($longitude)) {
			$distanceQuery = ", 3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( co.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( co.latitude) *  pi()/180) * POWER(SIN(($longitude -  co.longitude) *  pi()/180 / 2), 2) )) as distance";
		}
			
		$having = "having 1";
		if (!empty($latitude) && !empty($longitude) && $radius>0) {
			if (empty($orderBy)) {
				$orderBy = "distance";
			}
				
			if ($radius>0) {
				$having .= " and distance < $radius ";
			}
		}

		//dump($customAttributes);
		$customAttrFilter="";
		$customAttrIdFilter="";
		if (!empty($customAttributes)) {
			$customAttrFilterS="";
			$index=0;
			foreach ($customAttributes as $key => $value) {
				$index++;
				$values = explode(",", $value);
				$filter = "";
				$j=0;
				foreach ($values as $value2) {
					$value2 = $db->escape($value2);
					if (is_numeric($value2)) {
						if ($j>0) {
							$filter .=" and ";
						}
						$filter.=" (ca.value like '%$value2%' or ca.option_id = $value2) ";
					} else {
						if ($j>0) {
							$filter .=" and ";
						}
						$filter.=" (ca.value like '%$value2%')";
					}
					$j++;
				}
				if ($index>1) {
					$customAttrFilterS .=" or ";
				}

				if (!empty($filter)) {
					$customAttrFilterS .=" (ca.attribute_id = $key  and $filter )";
				}
			}
			$customAttrIds = array_keys($customAttributes);
			sort($customAttrIds);
			if (!empty($customAttrFilterS)) {
				$customAttrIdFilter = " and attributeSelection ='".implode(",", $customAttrIds)."'";
				$customAttrFilter=" and ($customAttrFilterS) ";
				$having .= $customAttrIdFilter;
			}
		}

		$featuredFilter = "";
		if ($featured) {
			$featuredFilter = " and co.featured = 1";
		}
		
		$packageFilter = '';
		$featuredSelect = " co.featured as featured ";
		if ($enablePackage && !$appSettings->item_decouple) {
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
								) and (pf.feature='company_offers' or pf.feature='featured_offers') ";

			$featuredSelect = " GREATEST(if(FIND_IN_SET('featured_offers',GROUP_CONCAT(DISTINCT pf.feature)) ,1,0), co.featured) as featured ";

			if ($featured) {
				$having .= " and featured = 1 ";
				$featuredFilter = '';
			}
		}
		
		$companyStatusFilter="and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$companyStatusFilter = "and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}
		
		if ($appSettings->item_decouple) {
			$companyStatusFilter = "";
		}
		
		$offerStatusFilter ="and (co.approved = ".OFFER_APPROVED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$offerStatusFilter = "and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}

		$whereCountryCond='';
		if (!empty($countryId)) {
			$whereCountryCond=" and ( co.countryId =". $countryId ." ) ";
		}

		$publishFilter = "and ((co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00' or co.publish_start_date is null) and ((co.publish_start_date=DATE(now()) and time(co.publish_start_time)<=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_start_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_start_time='00:00:00' or co.publish_start_time is null))
		      and ((co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00' or co.publish_end_date is null ) and ((co.publish_end_date=DATE(now()) and time(co.publish_end_time)>=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_end_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_end_time='00:00:00' or co.publish_end_time is null) )";
		
		if (empty($asc_desc)) {
			$asc_desc = "";
		}

		$wherePackageCond = '';
		if (!empty($packages) && $enablePackage && !$appSettings->item_decouple) {
			$packagesIds = implode(",", $packages);
			$wherePackageCond = " and inv.package_id in ($packagesIds) ";
		}

		if ($orderBy=="rand()" || empty($orderBy)) {
			$orderBy = "co.id";
			$asc_desc = "desc";
			$asc_desc = "desc";
		}

		$reviewFilter = " and cr.review_type = ".REVIEW_TYPE_OFFER ." and cr.approved=".REVIEW_STATUS_APPROVED." ";
		if ($appSettings->show_pending_review) {
			$reviewFilter = " and cr.review_type = ".REVIEW_TYPE_OFFER ." and (cr.approved=".REVIEW_STATUS_APPROVED." OR cr.approved=".REVIEW_STATUS_CREATED.")";
		}

		$selectFields = " co.id, co.subject, co.short_description, co.address, co.main_subcategory, co.city, co.county, co.province, co.area, co.street_number, co.postalCode, co.latitude, 
			co.longitude, co.startDate, co.endDate, co.alias, co.view_type, co.article_id, co.url, co.show_time, co.created, co.type, co.companyId, co.time_zone,
			co.price, co.specialPrice, co.currencyId, co.countryId, co.price_text, 
			co.quantity, co.enable_offer_selling,co.review_score, count(distinct cr.id) as nr_reviews,
			et.name as offerType, et.id as typeId, bcm.markerLocation as categoryMaker,"
			.($enablePackage && !$appSettings->item_decouple?"group_concat(pf.feature) as featuresS,":"") 
			.($showCountryFlag?"cn.logo as country_flag,":"") 
			.(!empty($customAttrFilter)?" GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection, ":"")."
			op.picture_info, op.picture_path, op.picture_title,"
			.(!empty($whereCatCond)?"GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color  ORDER BY cg.name separator '#|') as categories,":"")."
			$featuredSelect  
			,cp.phone, cp.name as company_name, cp.id as company_id, cp.alias as company_alias, cp.logoLocation ";
		
		if(empty($offerIds)){
			$selectfields = "co.id";
		}

		$whereConditions = " co.state=1 and co.approved !=-1 
			$whereNameCond $whereCityCond $whereRegionCond $whereAreaCond $featuredFilter $publishFilter $whereCatCond $packageFilter $companyStatusFilter $offerStatusFilter
			$whereTypeCond $whereItemType $whereCompanyCond $wherePriceCond $whereOfferTypeCond $customAttrFilter $whereCountryCond $whereProvinceCond $whereRelatedCatCond
			$whereUserCatCond $wherePackageCond";

		if(!empty($offerIds)){
			$offerIds = implode(",",$offerIds);
			$whereConditions = " co.id in ($offerIds)";
			$customAttrIdFilter="";
			$whereCatCondFaceted="";
			$having="";
		}

		$query = " select 
					$selectFields 
					$distanceQuery
					from
					#__jbusinessdirectory_company_offers co
					left join #__jbusinessdirectory_company_reviews cr on cr.itemId = co.id $reviewFilter
					left join  #__jbusinessdirectory_company_offer_pictures op on co.id=op.offerId
					and
						(op.id in (
								select  min(op1.id) as min from #__jbusinessdirectory_company_offers co1
								left join  #__jbusinessdirectory_company_offer_pictures op1 on co1.id=op1.offerId
								where op1.picture_enable=1
								group by co1.id
							)
						)
                    "
					.(!empty($whereCatCond)?"
					left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1":"")."
                    left join  #__jbusinessdirectory_company_offer_types et on co.offer_type=et.id ".
					"left join #__jbusinessdirectory_categories bcm on bcm.id=co.main_subcategory and bcm.published=1 
					left join #__jbusinessdirectory_companies cp on co.companyId = cp.id ".

					(!empty($whereCountryCond) || $showCountryFlag?" left join #__jbusinessdirectory_countries cn on co.countryId=cn.id ":"").
					(!empty($customAttrFilter)?"
                        left join #__jbusinessdirectory_offer_attributes AS ca on ca.offer_id=co.id
					    left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
					    left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
                        ":"")

					.($enablePackage && !$appSettings->item_decouple?"
                        left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
                        left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
                        left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
                        ":"").
					(!empty($multilingual)?" left join #__jbusinessdirectory_language_translations t on co.id = t.object_id and t.type = ".OFFER_DESCRIPTION_TRANSLATION:"").
					(!empty($filterByFav)?"
					inner join #__jbusinessdirectory_bookmarks bk on bk.item_id = co.id and bk.user_id = $user->id and bk.item_type = ".BOOKMARK_TYPE_OFFER:"").
					" where 1 and $whereConditions
					group by co.id
					$having
					order by featured desc, $orderBy $asc_desc
					";
		
		//echo($query);
		//exit;

		if ($totalCategories) {
			$parentId = 1;
			if (!empty($categoriesIDs)) {
				$categoriesIDs= explode(",", $categoriesIDs[0]);
				$parentId = end($categoriesIDs);
			}
			$objectType = '';
			if (isset($searchDetails["object_type"])) {
				$objectType = $searchDetails["object_type"];
			}

			$enableCustomAttr = false;
			$whereAttrCond="";
			if($objectType == "attributes"){
				$enableCustomAttr = true;
			}

			$selectFields = '';
			$groupBy = '';
			$whereCatCountCond = $whereCatCond;
			switch ($objectType) {
				case 'city':
					$selectFields = 'co.city as cityName';
					$groupBy = 'group by co.city';
					$orderBy = 'order by co.city';
					break;
				case 'region':
					$selectFields = 'co.county as regionName';
					$groupBy = 'group by co.county';
					$orderBy = 'order by co.county';
					break;
				case 'country':
					$selectFields = 'cn.country_name as countryName, co.countryId';
					$groupBy = 'group by cn.country_name';
					$orderBy = 'order by cn.country_name';
					break;
				case 'area':
					$selectFields = 'co.area as areaName';
					$groupBy = 'group by co.area';
					$orderBy = 'order by co.area';
					break;
				case 'type':
					$selectFields = 'et.name as typeName, co.offer_type as typeId';
					$groupBy = 'group by et.id';
					$orderBy = 'order by et.ordering';
					break;
				case 'attributes':
					$selectFields = 'a2.name, a2.id, ao2.name as optionName, ca2.value';
					$groupBy = 'group by a2.id, ca2.value, ao2.name';
					$orderBy = 'order by a2.ordering';
					$whereAttrCond = 'and (ca2.value!="" or ca2.option_id > 0)';
					break;
				default:
					$selectFields = 'cg1.name, cg1.id';
					$groupBy = 'group by cg1.id';
					$whereCatCountCond = " and cg1.parent_id = $parentId ";
					$orderBy = 'order by cg1.name';
			}

			$query = " select count(distinct co.id) as nr_offers, ".(!empty($customAttrFilter)?" GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection, ":"")."
                        $featuredSelect , $selectFields $distanceQuery  $distanceQuery
					from
					#__jbusinessdirectory_company_offers co
                    left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId 
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					left join #__jbusinessdirectory_categories cg1 ON cg1.id = cg.parent_id or cg1.id=cg.id
					
					left join  #__jbusinessdirectory_company_offer_types et on co.offer_type=et.id "
					.(!empty($whereCountryCond) || ($objectType == 'country')?"
							left join #__jbusinessdirectory_countries cn on co.countryId=cn.id ":"")
					.(!empty($customAttrFilter) || $enableCustomAttr?"
					left join #__jbusinessdirectory_offer_attributes AS ca on ca.offer_id=co.id
					left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
					left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id and ca.value = ao.id
					
					left join #__jbusinessdirectory_offer_attributes AS ca2 on ca2.offer_id=co.id
					left join #__jbusinessdirectory_attributes a2 on ca2.attribute_id = a2.id
					left join #__jbusinessdirectory_attribute_options as ao2 on ao2.attribute_id = a2.id and ca2.value = ao2.id
					":"").

					(!$appSettings->item_decouple?
						"left join #__jbusinessdirectory_companies cp on co.companyId = cp.id ":"").

					($enablePackage && !$appSettings->item_decouple?"
                        left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
                        left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
                        left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id ":"").
					(!empty($filterByFav)?"
					    inner join #__jbusinessdirectory_bookmarks bk on bk.item_id = co.id and bk.user_id = $user->ID and bk.item_type = ".BOOKMARK_TYPE_OFFER:"").
					(!empty($multilingual) ?
						"left join #__jbusinessdirectory_language_translations t on co.id = t.object_id and t.type = " . OFFER_DESCRIPTION_TRANSLATION : "") .
					" where co.state=1 and co.approved !=-1 
					$whereNameCond $customAttrFilter $whereCityCond $whereRegionCond $whereProvinceCond $whereOfferTypeCond $whereCountryCond $whereAreaCond $featuredFilter $publishFilter $whereCatCountCond $packageFilter $companyStatusFilter $whereRelatedCatCond
					$whereUserCatCond $wherePackageCond $whereAttrCond
					$groupBy
					$having
					$orderBy
					";
		}
		return $query;
	}

	public function getTotalOffersByObject($searchDetails, $objectType) {
		$startTime = microtime(true); // Gets current microtime as one long string
		$db =JFactory::getDBO();

		//        $db->setQuery("SET OPTION SQL_BIG_SELECTS=1 ");
		//$db->execute();

		JBusinessUtil::setGroupConcatLenght();

		$searchDetails["object_type"] = $objectType;

		$query = $this->getOffersByCategoriesSql($searchDetails, true);

		$db->setQuery($query);

		$result =  $db->loadObjectList();
		//dump($result);

		$endTime = microtime(true) - $startTime; // And this at the end of your code

		//echo PHP_EOL . "Total by object $objectType script took " . round($endTime, 4) . ' seconds to run. <br/>';
		return $result;
	}
	
	public function getOffersByCategories($searchDetails, $limitstart = 0, $limit = 0) {
		$startTime = microtime(true); // Gets
		$db =JFactory::getDBO();

		$query = $this->getOffersByCategoriesSql($searchDetails, false);
		$db->setQuery($query, $limitstart, $limit);

		$items =  $db->loadObjectList();
		
		$ids = array_map(function ($c) {
			return $c->id;
		},  $items);		
		
		//retrieve the offers with their data
		$searchDetails["offer-ids"] = $ids;
		$query = $this->getOffersByCategoriesSql($searchDetails, false);

		//echo($query);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$endTime = microtime(true) - $startTime; // And this at the end of your code
		
		// 		dump($result);
		
		//echo PHP_EOL . 'Search script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		return $result;
	}
 
	public function getTotalOffersByCategories($searchDetails) {
		$startTime = microtime(true); // Gets
		$db =JFactory::getDBO();
		
		$query = $this->getOffersByCategoriesSql($searchDetails);
		
		$db->setQuery($query);
		$db->execute();
		$result = $db->getNumRows();
		$endTime = microtime(true) - $startTime; // And this at the end of your code
		
		// echo PHP_EOL . 'Total by cat script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		
		return $result;
	}

	public function changeAprovalState($offerId, $state) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = " UPDATE #__jbusinessdirectory_company_offers SET approved=$state WHERE id = ".$offerId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function increaseViewCount($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = "update  #__jbusinessdirectory_company_offers set viewCount = viewCount + 1 where id='$offerId'";
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function getTotalNumberOfOffers($userId = null) {
		$db =JFactory::getDBO();
		
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_company_offers o ".
				(empty($userId)?"":"inner join  #__jbusinessdirectory_companies c on c.id = o.companyId 
				  					where c.userId =  $userId");
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->nr;
	}
	
	public function getOffersViews($userId = null) {
		$db =JFactory::getDBO();
		$query = "SELECT sum(o.viewCount) as nr FROM #__jbusinessdirectory_company_offers o ".
				(empty($userId)?"":"inner join  #__jbusinessdirectory_companies c on c.id = o.companyId
									where c.userId =  $userId");
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->nr;
	}
	
	public function getTotalActiveOffers() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_company_offers where state =1 and endDate>now()";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->nr;
	}
	
	public function getOfferForExport($limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();

		$query = "select co.*, GROUP_CONCAT( DISTINCT CONCAT_WS('#', op.picture_path, op.picture_title, op.picture_info)) as pictures, cn.country_name as countryName,
					GROUP_CONCAT(DISTINCT cg.name) as categories, GROUP_CONCAT(DISTINCT cg.id) as categoriesIds
					from
					#__jbusinessdirectory_company_offers co
					left join  #__jbusinessdirectory_company_offer_pictures op on co.id=op.offerId
					left join #__jbusinessdirectory_countries cn on co.countryId=cn.id	
					left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId		
					group by co.id
					order by co.id desc";
		

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	public function checkAlias($id, $alias) {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_company_offers  WHERE alias='$alias' and id<>$id";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
	}

	public function getNewOffers($start_date, $end_date) {
		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(created, '%Y-%m-%d') as date, count(*) as value 
					from #__jbusinessdirectory_company_offers
					where (CAST(created AS DATE) between '$start_date' and '$end_date')
					group by date
					having date IS NOT NULL
					order by date asc";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
	
	public function getOffersRSS($searchDetails) {
		$db = JFactory::getDBO();
		$orderByClause= "order by co.id desc";
		
		if (!empty($orderBy)) {
			$orderByClause = "order by $orderBy";
		}

		$categoriesIDs = isset($searchDetails["categoriesIds"])?$searchDetails["categoriesIds"]:null;
		$whereCatCond = '';
		if (!empty($categoriesIDs) && count($categoriesIDs)>0 && !empty($categoriesIDs[0])) {
			$whereCatCond .= " and cc.categoryId in (";
			$categoryIds = implode(", ", $categoriesIDs);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}

		$offers = "select co.id, co.specialPrice, co.subject, co.description, co.startDate, co.endDate, co.alias, co.featured, co.created,
					op.picture_path
					from #__jbusinessdirectory_company_offers co
					left join  #__jbusinessdirectory_company_offer_pictures op on co.id=op.offerId
					left join #__jbusinessdirectory_company_offer_category cc on co.id=cc.offerId
					and ( op.id in (
							select min(op1.id) as min from #__jbusinessdirectory_company_offers co1
							left join  #__jbusinessdirectory_company_offer_pictures op1 on co1.id=op1.offerId
							where op1.picture_enable=1
							group by co1.id ) )
					inner join #__jbusinessdirectory_companies cp on co.companyId = cp.id
					where co.state=1 
					and co.approved !=-1 
					and cp.state=1
					and cp.approved !=-1
					$whereCatCond
					group by co.id
					order by featured desc, co.created desc, subject asc";
		$db->setQuery($offers, 0, 10);
		return $db->loadObjectList();
	}

	public function getOffersAboutToExpire($nrDays) {
		$db =JFactory::getDBO();
		$expirationFilter = '';
		$expirationFilter = " and ((CURDATE() + INTERVAL $nrDays DAY) > (co.endDate)) ";

		$appSettings =  JBusinessUtil::getApplicationSettings();
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$approvalFilter="and (co.approved = ".OFFER_APPROVED.")";
		if ($showPendingApproval) {
			$approvalFilter = "and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}

		$query = "select co.*, cp.email as companyEmail
				from #__jbusinessdirectory_company_offers co
				left join #__jbusinessdirectory_companies cp on cp.id = co.companyId
				where 1 $expirationFilter $approvalFilter and co.state=1 and co.expiration_email_date is null
				group by co.id ";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function updateExpirationEmailDate($offerId) {
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = "update  #__jbusinessdirectory_company_offers set expiration_email_date = now() where id=$offerId";
		$db->setQuery($query);
		return $db->execute();
	}

	public function getOfferByName($offerName) {
		$db =JFactory::getDBO();
		$offerName = $db->escape($offerName);
		$query = "select * from #__jbusinessdirectory_company_offers where subject = '$offerName' ";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getNewOffersViews($start_date, $end_date, $userId = null) {
		$whereFilter = " and st.item_type=2 and st.type=0 ";
		if (!empty($userId)) {
			$whereFilter = " and (off.user_id=$userId  or c.userId = $userId)";
		}

		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(st.date, '%Y-%m-%d') as date, sum(st.item_count) as value 
					from #__jbusinessdirectory_statistics_archive st
					LEFT JOIN #__jbusinessdirectory_company_offers off on off.id = st.item_id
                    left JOIN #__jbusinessdirectory_companies c on off.companyId =c.id 
					where (CAST(st.date AS DATE) between '$start_date' and '$end_date') $whereFilter 
					group by date
					having date IS NOT NULL
					order by date asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getOfferViewsOnFront($userId = null) {
		$whereFilter = "";
		if (!empty($userId)) {
			$whereFilter = " and (offer.user_id =  $userId or c.userId = $userId)";
		}

		$db =JFactory::getDBO();
		$query = "SELECT sum(offer.viewCount) as nr 
                    FROM #__jbusinessdirectory_company_offers offer
                    left JOIN #__jbusinessdirectory_companies c on offer.companyId =c.id 
                    where 1 $whereFilter ";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
	}

	public function getProductCategories($categoryIds = null, $companyId = null, $level = 2, $type = OFFER_TYPE_PRODUCT) {
		$db =JFactory::getDbo();

		$whereTypeCond = ' and co.item_type = '.$type;

		$whereCatCond = '';
		if (!empty($categoryIds) && count($categoryIds)>0) {
			$whereCatCond .= " and cg.id in (";
			$categoryIds = implode(", ", $categoryIds);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}

		$whereCompany = '';
		if (!empty($companyId)) {
			$whereCompany = " and co.companyId = $companyId";
		}

		$whereCatLevelCond = ' and cg1.level <= '.$level;

		$query = "select cg1.*, group_concat(co.id) as offerIds
                  from #__jbusinessdirectory_categories as cg
                  right join #__jbusinessdirectory_categories as cg1 on cg1.id = cg.parent_id or cg.id = cg1.id
                  left join #__jbusinessdirectory_company_offer_category as cgo on cgo.categoryId = cg1.id
                  left join #__jbusinessdirectory_company_offers as co on co.id = cgo.offerId $whereTypeCond $whereCompany
                  where 1 and cg1.published = 1 and cg1.level > 0 $whereCatLevelCond $whereCatCond
                  group by cg1.id
                  order by cg1.id";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getSuggestions($keyword, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDbo();
		$keyword = $db->escape($keyword);

		$appSettings = JBusinessUtil::getApplicationSettings();

		$companyStatusFilter="and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$companyStatusFilter = "and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}
		
		if ($appSettings->item_decouple) {
			$companyStatusFilter = "";
		}
		
		$offerStatusFilter ="and (co.approved = ".OFFER_APPROVED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$offerStatusFilter = "and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}

		$publishFilter = "and ((co.publish_start_date<=DATE(now()) or co.publish_start_date='0000-00-00' or co.publish_start_date is null) and ((co.publish_start_date=DATE(now()) and time(co.publish_start_time)<=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_start_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_start_time='00:00:00' or co.publish_start_time is null))
		      and ((co.publish_end_date>=DATE(now()) or co.publish_end_date='0000-00-00' or co.publish_end_date is null ) and ((co.publish_end_date=DATE(now()) and time(co.publish_end_time)>=time(convert_tz(now(),@@session.time_zone,co.time_zone)) or IFNULL(UNIX_TIMESTAMP(co.publish_end_date),0)!=UNIX_TIMESTAMP(DATE(now()))) or co.publish_end_time='00:00:00' or co.publish_end_time is null) )";
		
		$query = "select co.id as suggestionId, co.subject as name 
				 from #__jbusinessdirectory_company_offers co ".
				 (!$appSettings->item_decouple?
						"left join #__jbusinessdirectory_companies cp on co.companyId = cp.id ":"").
				 "where subject like '%$keyword%' $companyStatusFilter $offerStatusFilter $publishFilter";

		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();

		return $result;
	}

	public function deleteAllDependencies($itemId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_company_offer_pictures where offerId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_offer_coupons where offer_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_offer_category where offerId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_offer_attributes where offer_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_messages where item_id = $itemId and type = ".MESSAGE_TYPE_OFFER;
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_offer_videos where offerId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_attachments WHERE type=2 and object_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		return true;
	}

	public function getOffersByString($str, $userId = null) {
		$db = JFactory::getDBO();
		$whereUser = "";
		if (!empty($userId)) {
			$whereUser = " and bc.user_id='$userId'";
		}

		$query = "select  bc.id, bc.subject as name 
						   from #__jbusinessdirectory_company_offers bc 
						   where bc.subject like '%$str%' $whereUser 
						   order by bc.subject";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getOffersByStatus($status, $itemNumber = null){
		$limitCondition = '';
		if (!empty($itemNumber)) {
			$limitCondition = ' LIMIT '.$itemNumber;
		}
		$db = JFactory::getDBO();
		$query = "select  bc.*
						   from #__jbusinessdirectory_company_offers bc 
						   where bc.approved = $status and bc.item_type = ".OFFER_TYPE_OFFER."
						   order by bc.created DESC
						   $limitCondition";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function updateOfferStock($offerId, $qtySold){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_company_offers SET quantity = quantity - $qtySold WHERE id = ".$offerId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	
	public function getOfferCurrency($offerId){
		$db =JFactory::getDBO();
		$offerId = $db->escape($offerId);
		$query = "select cr.*
					from #__jbusinessdirectory_company_offers ofr
					left join  #__jbusinessdirectory_currencies cr on ofr.currencyId = cr.currency_id
					where ofr.id=".$offerId;
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}
}
