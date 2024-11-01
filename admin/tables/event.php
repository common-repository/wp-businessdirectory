<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableEvent extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_events', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function getEventsByCategoriesSql($searchDetails, $totalCategories = false) {
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
		$area = isset($searchDetails["areaSearch"])?$searchDetails["areaSearch"]:null;
		$facetedSearch = isset($searchDetails["facetedSearch"])?$searchDetails["facetedSearch"]:null;
		
		$enablePackage = isset($searchDetails["enablePackages"])?$searchDetails["enablePackages"]:null;
		$showPendingApproval = isset($searchDetails["showPendingApproval"])?$searchDetails["showPendingApproval"]:null;
		$startDate = isset($searchDetails["startDate"])?$searchDetails["startDate"]:null;
		$endDate = isset($searchDetails["endDate"])?$searchDetails["endDate"]:null;
		$type = isset($searchDetails["typeSearch"])?$searchDetails["typeSearch"]:null;
		$companyId = isset($searchDetails["companyId"])?$searchDetails["companyId"]:null;
		$orderBy = isset($searchDetails["orderBy"])?$searchDetails["orderBy"]:null;
		$featured = isset($searchDetails["featured"])?$searchDetails["featured"]:null;
		$asc_desc = isset($searchDetails["asc_desc"])?$searchDetails["asc_desc"]:null;
		$multilingual = false;// isset($searchDetails["multilingual"])?$searchDetails["multilingual"]:null;
		$countryId = isset($searchDetails["countrySearch"])?$searchDetails["countrySearch"]:null;
		$province = isset($searchDetails["provinceSearch"])?$searchDetails["provinceSearch"]:null;
		$age = isset($searchDetails["age"])?$searchDetails["age"]:null;

		$startTime = isset($searchDetails["startTime"])?$searchDetails["startTime"]:null;
		$startTime = JBusinessUtil::convertTimeToMysqlFormat($startTime);
		$startTime = $startTime!="00:00:00"?$startTime:"";
		$endTime = isset($searchDetails["endTime"])?$searchDetails["endTime"]:null;
		$endTime = JBusinessUtil::convertTimeToMysqlFormat($endTime);
		$endTime = $endTime!="00:00:00"?$endTime:"";

		$customAttributes = isset($searchDetails["customAttributes"])?$searchDetails["customAttributes"]:null;
		
		$filterByFav = isset($searchDetails["filter-by-fav"])?$searchDetails["filter-by-fav"]:null;
		$excludedEvent = isset($searchDetails["excluded_event"])?$searchDetails["excluded_event"]:null;
		$packages = isset($searchDetails["packages"])?$searchDetails["packages"]:null;
		$eventIds = isset($searchDetails["event-ids"])?$searchDetails["event-ids"]:null;

		$user = JBusinessUtil::getUser();

		$whereCatCond = '';
        if (!empty($categoriesIDs)) {
            if (!empty($categoriesIDs)) {
				$whereCatCond .= " and ( cc.categoryId in (";
				$categoryIds = implode(", ", $categoriesIDs);
				$whereCatCond .= $categoryIds;
				$whereCatCond .= ")";
			}

			if ($facetedSearch == 1) {
				foreach ($categoriesIDs as $categoryId) {
					$values = explode(",", $categoryId);
					$whereCatCond .= ' or (0  ';
					foreach ($values as $value) {
						$whereCatCond .= " or cg.id REGEXP '\\\\b".$value."\\\\b' ";
					}
					$whereCatCond .= ' ) ';
				}
			}

			$whereCatCond .= ")";
		}

		$whereDateCond="";
		if (!empty($startDate) && !empty($endDate)) {
			$whereDateCond.=" and (co.start_date<='$endDate' and co.end_date>='$startDate')";
		} elseif (!empty($startDate)) {
			$whereDateCond.=" and co.end_date>='$startDate'";
		} elseif (!empty($endDate)) {
			$whereDateCond.=" and co.start_date<='$endDate'";
		} else {
			$whereDateCond.=" and (co.end_date>DATE(NOW()) or co.end_date='0000-00-00' or co.end_date is null or (co.end_date=DATE(NOW())))";
		}

		$whereTimeCond="";
		if (!empty($startTime) && !empty($endTime)) {
			$whereTimeCond.=" and (co.start_time >= '".JBusinessUtil::substractTime($startTime, 15) ."' and co.end_time<='".JBusinessUtil::addTime($endTime, 15)."')";
		} elseif (!empty($startTime)) {
			$whereTimeCond.=" and (co.start_time between '".JBusinessUtil::substractTime($startTime, 15) ."' and '".JBusinessUtil::addTime($startTime, 15)."' )";
		} elseif (!empty($endTime)) {
			$whereTimeCond.=" and (co.end_time between '".JBusinessUtil::substractTime($endTime, 15) ."' and '".JBusinessUtil::addTime($endTime, 15)."' )";
		}
		
		if (empty($keyword)) {
			$multilingual = false;
		}
		
		$translationCondition = '';
		if ($multilingual) {
			$translationCondition = " or t.name like '%$keyword%'";
		}

		$whereNameCond='';
		if (!empty($keyword)) {
			$translationCondition = '';
			if ($multilingual) {
				$translationCondition = " or t.name like '%$keyword%'";
			}
			
			$keywords = explode(" ", $keyword);
			$fields= array("co.name","cg.name","co.meta_keywords","co.meta_description","co.short_description","co.address","co.city","co.city","co.county");

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
				
			$whereNameCond=" and ($sqlFilter (co.name like '%$keyword%') $translationCondition) ";
		}
		
		$whereTypeCond='';
		if (!empty($type)) {
			$whereTypeCond=" and co.type = $type";
		}
		
		$whereCompanyIdCond='';
		if (!empty($companyId)) {
			$whereCompanyIdCond=" and cp.id = $companyId";
		}

		$whereAge = "";
		if (!empty($age)) {
			$whereAge = " and (co.min_age <= $age and co.max_age>=$age ) ";
		}

		$whereCityCond='';
		if (!empty($city)) {
			$whereCityCond=" and co.city = '".$city."' ";
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
		
		if ($orderBy == "distance asc" && (empty($latitude) || empty($longitude))) {
			$orderBy ="";
		}
		
		
		$distanceQuery = "";
		if (($orderBy == "distance asc" || $radius>0) && !empty($latitude) && !empty($longitude)) {
			$distanceQuery = ", 3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( co.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( co.latitude) *  pi()/180) * POWER(SIN(($longitude -  co.longitude) *  pi()/180 / 2), 2) )) as distance";
		}
				
		$having = " having 1";
		if (!empty($latitude) && !empty($longitude) && $radius>0) {
			$distanceQuery = ", 3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -abs( co.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( co.latitude) *  pi()/180) * POWER(SIN(($longitude -  co.longitude) *  pi()/180 / 2), 2) )) as distance";
		
			if (empty($orderBy)) {
				$orderBy = "distance";
			}
		
			if ($radius>0) {
				$having .= " and distance < $radius ";
			}
		}
		
		$featuredFilter = "";
		if ($featured) {
			$featuredFilter = " and co.featured = 1";
		}

		$customAttrFilter="";
		$customAttrIdFilter="";
		// dump($customAttributes);
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
		
		$packageFilter = '';
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

								)and pf.feature='company_events'";
		}
		
		$companyStatusFilter="and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$companyStatusFilter = "and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}

		if ($appSettings->item_decouple) {
			$companyStatusFilter = "";
		}
		
		$eventStatusFilter ="and (co.approved = ".EVENT_APPROVED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$eventStatusFilter = "and (co.approved = ".EVENT_CREATED." or co.approved = ".EVENT_APPROVED.") ";
		}

		$whereCountryCond='';
		if (!empty($countryId)) {
			$whereCountryCond=" and ( co.countryId =". $countryId ." ) ";
		}

		if (empty($asc_desc)) {
			$asc_desc = "";
		}

		if ($orderBy=="rand()" || empty($orderBy)) {
			$orderBy = "co.id";
			$asc_desc = "desc";
		}

		$whereExcluded = "";
		if(!empty($excludedEvent)){
			$whereExcluded = " and co.id != $excludedEvent";
		}

		$wherePackageCond = '';
		if (!empty($packages) && $enablePackage && !$appSettings->item_decouple) {
			$packagesIds = implode(",", $packages);
			$wherePackageCond = " and inv.package_id in ($packagesIds) ";
		}

		$selectFields = " co.id, co.name, co.short_description, co.main_subcategory, co.address, co.street_number, co.city, co.county, co.province, co.area, co.postalCode, co.latitude, co.longitude, co.start_date, co.end_date, co.start_time, co.end_time,
			co.alias, co.featured, co.created, co.show_end_date, co.show_start_time, co.show_end_time, co.company_id,co.countryId, co.contact_phone, "
			.(!empty($customAttrFilter)?" GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection, ":"")."
			op.picture_info,op.picture_path,op.picture_title,  et.id as type, et.name as eventType , 
			cp.name as companyName,cp.phone, cp.name as company_name, cp.id as company_id, cp.alias as company_alias, cp.logoLocation,
			cg.markerLocation as categoryMaker , co.featured, co.created,
			GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.name separator '#|') as categories ";

		if(empty($eventIds)){
			$selectfields = "co.id";
		}

		$whereConditions = " 
			co.state=1
			$whereDateCond $whereCompanyIdCond
			$whereCatCond $packageFilter $companyStatusFilter $eventStatusFilter $customAttrFilter
			$whereNameCond $whereTypeCond  $whereCityCond $whereRegionCond $whereAreaCond $featuredFilter 
			$whereCountryCond $whereProvinceCond $whereAge $whereTimeCond $whereExcluded
			$wherePackageCond ";

		if(!empty($eventIds)){
			$eventIds = implode(",",$eventIds);
			$whereConditions = " co.id in ($eventIds)";
			$customAttrIdFilter="";
			$whereCatCond="";
			$having="";
		}

		$query = " select 
					$selectFields
                    $distanceQuery
                    from
					#__jbusinessdirectory_company_events co
					left join  #__jbusinessdirectory_company_event_pictures op on co.id=op.eventId and
					(op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_events co1
							left join  #__jbusinessdirectory_company_event_pictures op1 on co1.id=op1.eventId
							where op1.picture_enable=1
							group by co1.id
						)
					)
					left join  #__jbusinessdirectory_company_event_types et on co.type=et.id
					left join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					left join #__jbusinessdirectory_companies cp on co.company_id = cp.id 
					".(!empty($whereCountryCond)?"left join #__jbusinessdirectory_countries cn on co.countryId=cn.id ":"").
					(!empty($customAttrFilter)?"
                        left join #__jbusinessdirectory_event_attributes AS ca on ca.event_id=co.id
                        left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
                        left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
                    ":"")
					.($enablePackage && !$appSettings->item_decouple?"
					left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
					left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
					left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id":"").
					 (!empty($filterByFav)?"
					inner join #__jbusinessdirectory_bookmarks bk on bk.item_id = co.id and bk.user_id = $user->ID and bk.item_type = ".BOOKMARK_TYPE_EVENT:"").
					(!empty($multilingual)?"
					left join #__jbusinessdirectory_language_translations t on co.id = t.object_id and t.type = ".EVENT_DESCRIPTION_TRANSLATION:"").
					" where 1 and $whereConditions
					group by co.id
					$having
					order by featured desc, $orderBy $asc_desc
					";
		//printf($query);exit;
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
			$joinCondition = '';
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
				case 'type':
					$selectFields = 'et.name as typeName, co.type as typeId';
					$groupBy = 'group by et.id';
					$orderBy = 'order by et.ordering';
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
					$joinCondition = "left join #__jbusinessdirectory_categories cg1 ON cg1.id = cg.parent_id or cg1.id=cg.id";
			}
			
			$query = " select count(distinct co.id) as nr_events, ".(!empty($customAttrFilter)?" GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection, ":"")."
                        $selectFields $distanceQuery
					from
					#__jbusinessdirectory_company_events co
					left join  #__jbusinessdirectory_company_event_types et on co.type=et.id
					left join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId 
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					
					$joinCondition
					".(!empty($whereCountryCond) || ($objectType == 'country')?"left join #__jbusinessdirectory_countries cn on co.countryId=cn.id ":"")

					.(!$appSettings->item_decouple?
					"left join #__jbusinessdirectory_companies cp on co.company_id = cp.id ":"")

					.(!empty($customAttrFilter) || $enableCustomAttr?"
					left join #__jbusinessdirectory_event_attributes AS ca on ca.event_id=co.id
					left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
					left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id and ca.value = ao.id
					
					left join #__jbusinessdirectory_event_attributes AS ca2 on ca2.event_id=co.id
					left join #__jbusinessdirectory_attributes a2 on ca2.attribute_id = a2.id
					left join #__jbusinessdirectory_attribute_options as ao2 on ao2.attribute_id = a2.id and ca2.value = ao2.id
					":"")

					.($enablePackage && !$appSettings->item_decouple?"
					left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
					left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
					left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id":"").
					(!empty($filterByFav)?"
					inner join #__jbusinessdirectory_bookmarks bk on bk.item_id = co.id and bk.user_id = $user->ID and bk.item_type = ".BOOKMARK_TYPE_EVENT:"").
					(!empty($multilingual)?"
					left join #__jbusinessdirectory_language_translations t on co.id = t.object_id and t.type = ".EVENT_DESCRIPTION_TRANSLATION:"").
					" where co.state=1
					$whereDateCond $whereCompanyIdCond
					$packageFilter $companyStatusFilter $customAttrFilter 
					$whereNameCond $whereTypeCond  $whereCityCond $whereRegionCond $whereAreaCond $whereProvinceCond 
					$featuredFilter $whereCatCountCond $whereCountryCond $companyStatusFilter $whereAge $whereAttrCond
					$wherePackageCond
					$groupBy
					$having
					$orderBy
					";
		}

		return $query;
	}

	public function getEventsByCategories($searchDetails, $limitstart = 0, $limit = 0) {
		$startTime = microtime(true); // Gets current microtime as one long string

		$db =JFactory::getDBO();
	
		$query = $this->getEventsByCategoriesSql($searchDetails, false);
		$db->setQuery($query, $limitstart, $limit);

		$items =  $db->loadObjectList();
		
		$ids = array_map(function ($c) {
			return $c->id;
		},  $items);		
		
		//retrieve the offers with their data
		$searchDetails["event-ids"] = $ids;
		$query = $this->getEventsByCategoriesSql($searchDetails, false);

		//echo($query);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$endTime = microtime(true) - $startTime; // And this at the end of your code

		// echo PHP_EOL . 'getEventsByCategories script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		
		return $result;
	}

	public function getTotalEventsByObject($searchDetails, $objectType) {
		$startTime = microtime(true); // Gets current microtime as one long string
		$db =JFactory::getDBO();

		//$db->setQuery("SET OPTION SQL_BIG_SELECTS=1 ");
		//$db->execute();

		JBusinessUtil::setGroupConcatLenght();

		$searchDetails["object_type"] = $objectType;

		$query = $this->getEventsByCategoriesSql($searchDetails, true);

		$db->setQuery($query);

		$result =  $db->loadObjectList();
		//dump($result);

		$endTime = microtime(true) - $startTime; // And this at the end of your code

		//echo PHP_EOL . 'Total by object script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		return $result;
	}
	
	public function getTotalEventsByCategories($searchDetails) {
		$db =JFactory::getDBO();
		
		$query = $this->getEventsByCategoriesSql($searchDetails);
	
		//dump($query);
		//echo $query;
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function changeAprovalState($eventId, $state) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = " UPDATE #__jbusinessdirectory_company_events SET approved=$state WHERE id = ".$eventId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}


	public function increaseViewCount($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = "update  #__jbusinessdirectory_company_events set view_count = view_count + 1 where id=$eventId";
		$db->setQuery($query);
		return $db->execute();
	}

	public function getEvent($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = "select e.*, et.name as eventType, et.id as eventTypeId, cu.currency_name as currencyName, cp.email as companyEmail, cp.phone as companyPhone,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.name separator '#|') as categories
					from #__jbusinessdirectory_company_events e
					left join  #__jbusinessdirectory_company_event_types et on e.type=et.id
					left join  #__jbusinessdirectory_company_event_category ec on ec.eventId=e.id
					left join  #__jbusinessdirectory_categories cg on cg.id=ec.categoryId and cg.published=1
					left join  #__jbusinessdirectory_currencies cu on e.currency_id = cu.currency_id
					left join  #__jbusinessdirectory_companies cp on e.company_id = cp.id
					where e.id=".$eventId;
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}

	
	public function getActiveEvent($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
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
								) and pf.feature='company_events' ";
		}
				
		$companyStatusFilter="and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$companyStatusFilter = "and cp.state=1 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") and (co.approved = ".OFFER_CREATED." or co.approved = ".OFFER_APPROVED.") ";
		}
		
		if ($appSettings->item_decouple) {
			$companyStatusFilter = "";
		}
		
		$eventStatusFilter ="and (co.approved = ".EVENT_APPROVED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$eventStatusFilter = "and (co.approved = ".EVENT_CREATED." or co.approved = ".EVENT_APPROVED.") ";
		}
		
		$query = "select co.*, et.name as eventType, et.id as eventTypeId, cu.currency_name as currencyName, bcm.markerLocation as categoryMarker,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias ORDER BY cg.name separator '#|') as categories
					from #__jbusinessdirectory_company_events co
					left join  #__jbusinessdirectory_company_event_types et on co.type=et.id
					left join  #__jbusinessdirectory_company_event_category ec on ec.eventId=co.id
					left join  #__jbusinessdirectory_categories cg on cg.id=ec.categoryId and cg.published=1  
					left join  #__jbusinessdirectory_currencies cu on co.currency_id = cu.currency_id
					left join #__jbusinessdirectory_companies cp on co.company_id = cp.id
					left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
					left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1) or (p.price=0 and p.status=1)
					left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
					left join #__jbusinessdirectory_categories bcm on bcm.id=co.main_subcategory and bcm.published=1
					where co.id='$eventId' $packageFilter $companyStatusFilter $eventStatusFilter
					group by co.id
					";
		$db->setQuery($query);

		$result = $db->loadObject();
		return $result;
	}
	
	public function getEvents($filter, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$query = "select co.*,cp.name as companyName 
				  from #__jbusinessdirectory_company_events co
				  left join  #__jbusinessdirectory_companies cp on cp.id=co.company_id
				  left join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId
				  left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1 
				  group by co.id
				  order by co.name
				  $filter";
		// 		dump($query);
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getTotalEvents() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_events";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getTotalEventsByCompany($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = "select * from #__jbusinessdirectory_company_events where company_id =".$companyId;
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function changeState($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = 	" UPDATE #__jbusinessdirectory_company_events SET state = IF(state, 0, 1) WHERE id = ".$eventId ;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function changeStateFeatured($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = 	" UPDATE #__jbusinessdirectory_company_events SET featured = IF(featured, 0, 1) WHERE id = ".$eventId ;
		$db->setQuery($query);
	
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getCompanyEvents($companyId, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$appSettings =  JBusinessUtil::getApplicationSettings();
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$approvalFilter="and (co.approved = ".EVENT_APPROVED.")";
		if ($showPendingApproval) {
			$approvalFilter = "and (co.approved = ".EVENT_CREATED." or co.approved = ".EVENT_APPROVED.") ";
		}
		
		$query = "select co.*, op.picture_path, et.name as eventType, cg.markerLocation as categoryMarker
					from #__jbusinessdirectory_company_events co
					left join  #__jbusinessdirectory_company_event_pictures op on co.id=op.eventId
					and (op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_events co1
							left join  #__jbusinessdirectory_company_event_pictures op1 on co1.id=op1.eventId
							where op1.picture_enable=1 and company_id=$companyId
							group by co1.id))
					left join  #__jbusinessdirectory_company_event_types et on co.type=et.id
					left join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1
					where co.state=1 and (co.end_date>=DATE(now()) or co.end_date='0000-00-00') and company_id=$companyId $approvalFilter
					group by co.id	
					order by co.start_date";
			
		//echo($query);
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();
		//dump($result);
		//dump($this->_db->getError());
		return $result;
	}

	public function getUserEvents($userId, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();

		if (empty($userId)) {
			return null;
		}

		$query = "select co.*, cp.name as companyName, op.picture_path 
					from 
					#__jbusinessdirectory_company_events co
					left join #__jbusinessdirectory_companies cp on cp.id = co.company_id
					left join  #__jbusinessdirectory_company_event_pictures op on co.id=op.eventId and
					(op.id in (
							select  min(op1.id) as min from #__jbusinessdirectory_company_events co1
							left join  #__jbusinessdirectory_company_event_pictures op1 on co1.id=op1.eventId
							where op1.picture_enable=1
							group by co1.id
						)
					)
					where (co.user_id= $userId || cp.userId=$userId)
					group by co.id	
					order by co.id desc ";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getTotalUserEvents($companyIds, $userId, $approved = false) {
		$db =JFactory::getDBO();

		$comCond = "";
		if (count($companyIds)>0) {
			$companyIds = implode(",", $companyIds);
			$comCond = " and co.company_id in ($companyIds)";
		}

		$approvedCond = "";
		if ($approved) {
			$approvedCond = " and co.approved='1' ";
		}

		$query = "select * from #__jbusinessdirectory_company_events co
					left join #__jbusinessdirectory_companies cp on cp.id = co.company_id 
					where 1 $comCond or cp.userId = $userId or co.user_id= $userId $approvedCond";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getTotalCompanyEvents($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_events where company_id=$companyId";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getEventPictures($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = "select * from #__jbusinessdirectory_company_event_pictures where eventId=$eventId and picture_enable=1 order by id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTotalNumberOfEvents($userId = null) {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_company_events ev ".
				(empty($userId)?"":"inner join  #__jbusinessdirectory_companies c on c.id = ev.company_id
									where c.userId =  $userId");
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->nr;
	}

	public function getEventsViews($userId = null) {
		$db =JFactory::getDBO();
		$query = "SELECT sum(view_count) as nr FROM #__jbusinessdirectory_company_events ev ".
				(empty($userId)?"":"inner join  #__jbusinessdirectory_companies c on c.id = ev.company_id
									where c.userId =  $userId");
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result->nr;
	}
	public function getTotalActiveEvents() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_company_events where state =1 and end_date>now()";
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->nr;
	}
	
	public function getEventsForExport($limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();

		$query = "select co.*, GROUP_CONCAT( DISTINCT CONCAT_WS('#', op.picture_path, op.picture_title, op.picture_info)) as pictures, et.name as eventType, cn.country_name as countryName, c.name as company, GROUP_CONCAT(DISTINCT cg.name) as categories, GROUP_CONCAT(DISTINCT cg.id) as categoriesIds
					from #__jbusinessdirectory_company_events co
					left join #__jbusinessdirectory_company_event_category cc on co.id=cc.eventId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId	
					left join #__jbusinessdirectory_countries cn on co.countryId=cn.id	
					left join  #__jbusinessdirectory_company_event_pictures op on co.id=op.eventId
					left join  #__jbusinessdirectory_company_event_types et on co.type=et.id
					left join  #__jbusinessdirectory_companies c on co.company_id=c.id
					group by co.id	
					order by co.id ";
			
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();

		return $result;
	}
	
	public function checkAlias($id, $alias) {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_company_events  WHERE alias='$alias' and id<>$id";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
	}
	
	public function getNextEventsIds($id, $reccuring_id) {
		$db =JFactory::getDBO();
		$query = "select id from #__jbusinessdirectory_company_events where id>=$id and recurring_id=$reccuring_id";
		$db->setQuery($query);
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$result = array();
		
		foreach ($items as $item) {
			$result[] = $item->id;
		}
		
		return $result;
	}
	
	public function getAllSeriesEventsIds($reccuring_id) {
		$db =JFactory::getDBO();
		$query = "select id from #__jbusinessdirectory_company_events where recurring_id=$reccuring_id or id = $reccuring_id";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$result = array();
		
		foreach ($items as $item) {
			$result[] = $item->id;
		}
		
		return $result;
	}
	
	public function deleteReccuringEvents($reccuring_id) {
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_events where recurring_id=$reccuring_id and id != $reccuring_id";
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function getReccuringEvents($id, $reccuring_id) {
	}

	public function getNewEvents($start_date, $end_date) {
		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(created, '%Y-%m-%d') as date, count(*) as value 
					from #__jbusinessdirectory_company_events
					where (CAST(created AS DATE) between '$start_date' and '$end_date') 
					group by date
					having date IS NOT NULL
					order by date asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getEventsRSS($searchDetails) {
		$categoriesIDs = isset($searchDetails["categoriesIds"])?$searchDetails["categoriesIds"]:null;
		$whereCatCond = '';
		if (!empty($categoriesIDs) && count($categoriesIDs)>0 && !empty($categoriesIDs[0])) {
			$whereCatCond .= " and cc.categoryId in (";
			$categoryIds = implode(", ", $categoriesIDs);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}
		$db = JFactory::getDBO();
		$query = "select ce.id, ce.alias, ce.name, ce.description, ce.featured, ce.created,
					op.picture_path, cp.name as companyName
					from #__jbusinessdirectory_company_events ce
					left join #__jbusinessdirectory_company_event_category cc on ce.id=cc.eventId
					left join #__jbusinessdirectory_company_event_pictures op on ce.id=op.eventId
					and ( op.id in (
						select  min(op1.id) as min from #__jbusinessdirectory_company_events co1
						left join  #__jbusinessdirectory_company_event_pictures op1 on co1.id=op1.eventId
						where op1.picture_enable=1
						group by co1.id ) )
					inner join #__jbusinessdirectory_companies cp on ce.company_id = cp.id
					where ce.state=1 
					and ce.approved !=-1
					and cp.state=1
					and cp.approved !=-1
					$whereCatCond
					group by ce.id
					order by featured desc, ce.created desc, name asc";
		$db->setQuery($query, 0, 10);
		return $db->loadObjectList();
	}

	public function getEventsAboutToExpire($nrDays) {
		$db =JFactory::getDBO();
		$appSettings =  JBusinessUtil::getApplicationSettings()->show_pending_approval;
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$approvalFilter="and (ce.approved = ".EVENT_APPROVED.")";
		if ($showPendingApproval) {
			$approvalFilter = "and (ce.approved = ".EVENT_CREATED." or ce.approved = ".EVENT_APPROVED.") ";
		}

		$expirationFilter = '';
		$expirationFilter = " and ((CURDATE() + INTERVAL $nrDays DAY) > (ce.end_date)) ";

		$query = "select ce.*
				from #__jbusinessdirectory_company_events ce
				where 1 and ce.state=1 and ce.expiration_email_date is null $expirationFilter $approvalFilter
				group by ce.id ";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function deleteExpiredEvents() {
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_events
                    where UNIX_TIMESTAMP(concat(ifnull(end_date,'0000-00-00'),' ',ifnull(end_time,'00:00:00'))) < UNIX_TIMESTAMP(DATE(NOW()))
                          and UNIX_TIMESTAMP(ifnull(end_date,0))>0";
		$db->setQuery($query);
		return $db->execute();
	}

	public function updateExpirationEmailDate($eventId) {
		$db =JFactory::getDBO();
		$eventId = $db->escape($eventId);
		$query = "update  #__jbusinessdirectory_company_events set expiration_email_date = now() where id=$eventId";
		$db->setQuery($query);
		return $db->execute();
	}

	public function getEventsByIds($eventIds) {
		$db =JFactory::getDBO();
		$query = "select ce.*
				from #__jbusinessdirectory_company_events ce
				where ce.id in ($eventIds)
				group by ce.id ";
		$db->setQuery($query);
		return $db->loadObjectList();
		;
	}

	public function storeAppointment($data) {
		$db =JFactory::getDBO();
		$user = JBusinessUtil::getUser();
		$query = "insert into  #__jbusinessdirectory_company_event_appointments (first_name, last_name, company_name, email, phone, date, time, status, remarks, event_id, company_id,user_id) values 
			('".$db->escape($data['first_name'])."','".$db->escape($data['last_name'])."','".$db->escape($data['company_name'])."','".$db->escape($data['email'])."','".$db->escape($data['phone'])."','".$db->escape($data['date'])."','".$db->escape($data['time'])."', 0 ,'".$db->escape($data['remarks'])."', ".$db->escape($data['event_id']).",".$db->escape($data['company_id']).",".$db->escape($user->ID).")";

		$db->setQuery($query);
		return $db->execute();
	}

	public function getEventByName($eventName) {
		$db =JFactory::getDBO();
		$eventName = $db->escape($eventName);
		$query = "select * from #__jbusinessdirectory_company_events where name = '$eventName' ";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getNewEventsViews($start_date, $end_date, $userId = null) {
		$whereFilter = " and st.item_type=3 and st.type=0 ";
		if (!empty($userId)) {
			$whereFilter = " and (ev.user_id=$userId or c.userId = $userId )";
		}

		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(st.date, '%Y-%m-%d') as date, sum(st.item_count) as value 
					from #__jbusinessdirectory_statistics_archive st
					LEFT JOIN #__jbusinessdirectory_company_events ev on ev.id = st.item_id
                    Left JOIN #__jbusinessdirectory_companies c on ev.company_id =c.id 
					where (CAST(st.date AS DATE) between '$start_date' and '$end_date') $whereFilter 
					group by date
					having date IS NOT NULL
					order by date asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getEventViewsOnFront($userId = null) {
		$whereFilter = "";
		if (!empty($userId)) {
			$whereFilter = " and (ev.user_id =  $userId or c.userId = $userId)";
		}

		$db =JFactory::getDBO();
		$query = "SELECT sum(ev.view_count) as nr 
                  FROM #__jbusinessdirectory_company_events ev
                  Left JOIN #__jbusinessdirectory_companies c on ev.company_id =c.id 
                  where 1 $whereFilter ";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
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
		
		$eventStatusFilter ="and (co.approved = ".EVENT_APPROVED.")";
		if (($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'))) {
			$eventStatusFilter = "and (co.approved = ".EVENT_CREATED." or co.approved = ".EVENT_APPROVED.") ";
		}

		$query = "select co.id as suggestionId, co.name from 
				  #__jbusinessdirectory_company_events co
				  left join #__jbusinessdirectory_companies cp on co.company_id = cp.id
				  where co.name like '%$keyword%' $companyStatusFilter $eventStatusFilter";

		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();

		return $result;
	}

	public function deleteAllDependencies($itemId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_event_videos where eventId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_event_associated_items where event_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_event_pictures where eventId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_event_category where eventId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_event_category where eventId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_attachments WHERE type=3 and object_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_messages where item_id = $itemId and type =".MESSAGE_TYPE_EVENT;
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_event_attributes where event_id = $itemId";
		$db->setQuery($sql);
		$db->execute();

		return true;
	}

	public function getEventsByString($str, $userId = null) {
		$db = JFactory::getDBO();
		$whereUser = "";
		if (!empty($userId)) {
			$whereUser = " and bc.user_id='$userId'";
		}

		$query = "select  bc.id, bc.name 
						   from #__jbusinessdirectory_company_events bc 
						   where bc.name like '%$str%' $whereUser 
						   order by bc.name";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getEventsByStatus($status, $itemNumber = null){
		$limitCondition = '';
		if (!empty($itemNumber)) {
			$limitCondition = ' LIMIT '.$itemNumber;
		}
		$db = JFactory::getDBO();
		$query = "select  bc.*
						   from #__jbusinessdirectory_company_events bc 
						   where bc.approved = $status 
						   order by bc.created DESC
						   $limitCondition";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
