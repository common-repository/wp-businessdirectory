<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompany extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	/**
		 * @param	JDatabase	A database connector object
		 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_companies', 'id', $db);
	}
	
	
	public function delete($pk = null, $children = false) {
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__jbusinessdirectory_companies");
		$query->where('id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		return parent::delete($pk, $children);
	}
	
	/**
	 * Method to run an update query and check for a database error
	 *
	 * @param   string  $query         The query.
	 * @param   string  $errorMessage  Unused.
	 *
	 * @return  boolean  False on exception
	 *
	 * @since   11.1
	 */
	protected function _runQuery($query, $errorMessage) {
		$db =JFactory::getDBO();
		$db->setQuery($query);

		// Check for a database error.
		if (!$db->execute()) {
			$e = new Exception($this->_db->getError());
			$this->setError($e);
			$this->_unlock();
			return false;
		}
		if (isset($this->_debug)) {
			$this->_logtable();
		}
	}
	
	public function getPlainCompany($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$companyId = (int)$companyId;
		$query = "select bc.*
				from #__jbusinessdirectory_companies bc
				where bc.id=$companyId ";
		$db->setQuery($query);
		$result =  $db->loadObject();
		
		return $result;
	}
	
	public function getCompanies($searchFilter, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$companiesQuery = "select bc.*, GROUP_CONCAT(distinct ct.name) as typeName 
							from #__jbusinessdirectory_companies bc 
							left join #__jbusinessdirectory_company_types ct  on find_in_set(ct.id,bc.typeId)  $searchFilter
							
							GROUP BY bc.id";
		//dump($companiesQuery);
		$db->setQuery($companiesQuery, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	public function getAllCompanies() {
		$db =JFactory::getDBO();
		$companiesQuery = "select bc.id, bc.name, bc.alias, GROUP_CONCAT(distinct ct.name) as typeName 
						    from #__jbusinessdirectory_companies bc 
							left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,bc.typeId) 
							group by bc.id
							order by bc.name";
		$db->setQuery($companiesQuery);
		return $db->loadObjectList();
	}
	
	public function getTotalCompanies($searchFilter) {
		$db =JFactory::getDBO();
		$companiesQuery = "select bc.* from #__jbusinessdirectory_companies bc   $searchFilter";
		$db->setQuery($companiesQuery);
		$db->execute();
		return $db->getNumRows();
	}
	
	public function getCompany($companyId) {
		JBusinessUtil::setGroupConcatLenght();
		
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$companyId = (int)$companyId;
		$query = "select bc.*, GROUP_CONCAT(DISTINCT ct.name separator ', ') as typeName, GROUP_CONCAT(DISTINCT ct.company_view separator ',') as companyView, cr.country_name,
				GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',IFNULL(cg.icon,''),'|', IFNULL(cg.color,'') ORDER BY cg.lft separator '#|') as categories,  
				cr.country_name,GROUP_CONCAT( DISTINCT cpt.picture_path) as pictures,
				ccm.name as mainCategory, ccm.alias as mainCategoryAlias, ccm.markerLocation as categoryMarker, ccm.id as mainCategoryId
				from #__jbusinessdirectory_companies bc 
				left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,bc.typeId)
				left join #__jbusinessdirectory_company_category cc on bc.id=cc.companyId 
				left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId 
				left join #__jbusinessdirectory_categories ccm on ccm.id=bc.mainSubcategory and ccm.published=1
				left join #__jbusinessdirectory_countries as cr on cr.id = bc.countryId 
				left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=bc.id and cpt.picture_enable = 1
				where bc.id=".$companyId." group by bc.id order by name";
		$db->setQuery($query);
		$result =  $db->loadObject();
		
		return $result;
	}
	
	/**
	 * Set the package id for a company
	 * @param unknown $companyId
	 * @param unknown $packageId
	 * @return boolean
	 */
	public function setPackageId($companyId, $packageId) {
		$db =JFactory::getDBO();
		$companyId = intval($companyId);
		$packageId = intval($packageId);
		
		$query = " UPDATE #__jbusinessdirectory_companies SET package_id = $packageId WHERE id = $companyId ";
		$db->setQuery($query);
		
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function getCompaniesByLetterSql($letter, $enablePackage, $showPendingApproval) {
		$db =JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$showCustomMarkers = 1;// $appSettings->show_custom_markers;
		$searchImages = $appSettings->search_result_view==7;
		
		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}
		
		$packageFilter = '';
		if ($enablePackage) {
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
		
		$publishFilter = '';
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";
		
		
		if ($letter=="[x]") {
			$letter = "#";
		}
		
		$letter = $db->escape($letter);

		$language = JBusinessUtil::getLanguageTag();
		$whereName = "  and (cp.name like '#lowerLetter%' or cp.name like '#upperLetter%' )";
		if ($appSettings->enable_multilingual) {
			$whereName = "and ((cp.name like '#lowerLetter%' or cp.name like '#upperLetter%' ) or
		                    (ltra.name like '#lowerLetter%' or ltra.name like '#upperLetter%' ))";
		}

		$reviewFilter = " and cr.review_type = ".REVIEW_TYPE_BUSINESS ." and cr.approved=".REVIEW_STATUS_APPROVED." ";
		if ($appSettings->show_pending_review) {
			$reviewFilter = " and cr.review_type = ".REVIEW_TYPE_BUSINESS ." and (cr.approved=".REVIEW_STATUS_APPROVED." OR cr.approved=".REVIEW_STATUS_CREATED.")";
		}
		
		$query = "select slec.*, ".(($showCustomMarkers==1)?" mainCategory, mainCategoryId, ":"")." companyName, companyId1, features, GREATEST(if(FIND_IN_SET('featured_companies',features) ,1,0), featured) as featured
				 from (
				 select cp.name as companyName, cp.id as companyId1, cp.ad_image, cp.yelp_id, cp.viewCount, cp.recommended,
				 cp.id, cp.name, cp.alias, cp.short_description, cp.meta_description, cp.street_number, cp.address, cp.city, cp.county, cp.province, cp.area, cp.website, cp.phone, cp.mobile, 
				 cp.email, cp.state, cp.fax, cp.opening_status, cp.time_zone,
				 cp.employees, cp.hourly_rate, cp.min_project_size, cp.facebook, cp.twitter, cp.whatsapp, cp.instagram, cp.pinterest, cp.youtube, cp.linkedin,
				 cp.averageRating, cp.slogan,cp.logoLocation, cp.activity_radius, cp. review_score, cp.creationDate, cp.package_id,
				 cp.featured,cp.publish_only_city,cp.userId, cp.business_cover_image,
				 cp.latitude, cp.longitude, cp.keywords,cp.approved, cp.mainSubcategory, cp.countryId,cp.typeId, cp.postalCode, cp.publish_start_date, cp.publish_end_date,cp.ordering,
				 count(distinct cr.id) as nr_reviews,
			 	 ".($searchImages?" GROUP_CONCAT( DISTINCT CONCAT_WS('#', cpt.picture_path, cpt.picture_title, cpt.picture_info)) as pictures, ":"")."
				 GROUP_CONCAT(DISTINCT pf.feature) as features
				 ".(($showCustomMarkers==1)?" ,bc.name as mainCategory, bc.alias as mainCategoryAlias, bc.markerLocation as categoryMaker, bc.id as mainCategoryId ":"")."
				 from #__jbusinessdirectory_companies cp 
				 ".(($appSettings->enable_multilingual==1)?" left join #__jbusinessdirectory_language_translations ltra on ltra.object_id=cp.id and ltra.type=".BUSSINESS_DESCRIPTION_TRANSLATION." and ltra.language_tag = '$language' ":"")."
				".(($showCustomMarkers==1)?" left join #__jbusinessdirectory_categories bc on bc.id=cp.mainSubcategory and bc.published=1 ":"")."
                ".($searchImages?" left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=cp.id and cpt.picture_enable = 1 ":"")."
				left join #__jbusinessdirectory_company_reviews cr on cr.itemId = cp.id $reviewFilter
				left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id 
				left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,cp.typeId)
				left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
				left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
				left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id
				where 1 $whereName $packageFilter and cp.state=1  $companyStatusFilter $publishFilter
				group by cp.id
				 order by cp.name
				  ) as slec 
				group by companyId1 
				order by companyName";

		if ($letter=="[0-9]") {
			$query = str_replace("like '#lowerLetter%'", " REGEXP '^[0-9]'", $query);
			$query = str_replace("like '#upperLetter%'", " REGEXP '^[0-9]'", $query);
		} else {
			$query = str_replace("#lowerLetter", strtolower($letter), $query);
			$query = str_replace("#upperLetter", strtoupper($letter), $query);
		}
		return $query;
	}
	
	public function getCompaniesByLetter($letter, $enablePackage, $showPendingApproval, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$query = $this->getCompaniesByLetterSql($letter, $enablePackage, $showPendingApproval);
		//echo $query;
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	public function getTotalCompaniesByLetter($letter, $enablePackage, $showPendingApproval) {
		$db =JFactory::getDBO();
		$query = $this->getCompaniesByLetterSql($letter, $enablePackage, $showPendingApproval);
	
		$db->setQuery($query);
		$db->execute();
		
		return $db->getNumRows();
	}
	
	public function getCompaniesByNameAndCategoriesSql($searchDetails, $totalCategories = false) {
		$db =JFactory::getDBO();
		$appSettings = JBusinessUtil::getApplicationSettings();
		$language = JBusinessUtil::getLanguageTag();

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
		$type = isset($searchDetails["typeSearch"])?$searchDetails["typeSearch"]:null;
		$city = isset($searchDetails["citySearch"])?$searchDetails["citySearch"]:null;
		$membership = isset($searchDetails["membershipSearch"])?$searchDetails["membershipSearch"]:null;
		$package = isset($searchDetails["packageSearch"])?$searchDetails["packageSearch"]:null;
		$displayMembership = isset($searchDetails["displayMembership"])?$searchDetails["displayMembership"]:null;
		$starRating = isset($searchDetails["starRating"])?$searchDetails["starRating"]:null;
		$region = isset($searchDetails["regionSearch"])?$searchDetails["regionSearch"]:null;
		$countryId = isset($searchDetails["countrySearch"])?$searchDetails["countrySearch"]:null;
		$enablePackage = isset($searchDetails["enablePackages"])?$searchDetails["enablePackages"]:null;
		$showPendingApproval = isset($searchDetails["showPendingApproval"])?$searchDetails["showPendingApproval"]:null;
		$orderBy = isset($searchDetails["orderBy"])?$searchDetails["orderBy"]:null;
		$facetedSearch = isset($searchDetails["facetedSearch"])?$searchDetails["facetedSearch"]:null;
		$zipCodeSearch = isset($searchDetails["zipcCodeSearch"])?$searchDetails["zipcCodeSearch"]:null;
		$zipCode = isset($searchDetails["zip-code"])?$searchDetails["zip-code"]:null;
		$limitCities = isset($searchDetails["limit_cities_regions"])?$searchDetails["limit_cities_regions"]:null;
		$customAttributes = isset($searchDetails["customAttributes"])?$searchDetails["customAttributes"]:null;
		$asc_desc = isset($searchDetails["asc_desc"])?$searchDetails["asc_desc"]:null;
		$featured = isset($searchDetails["featured"])?$searchDetails["featured"]:null;
		$keywordLocation = isset($searchDetails['keywordLocation'])?$searchDetails['keywordLocation']:null;
		$showLocations = isset($searchDetails["showSecondayLocationsMap"])?$searchDetails["showSecondayLocationsMap"]:null;
		$multilingual = isset($searchDetails["multilingual"])?$searchDetails["multilingual"]:null;
		$letter = isset($searchDetails["letter"])?$searchDetails["letter"]:null;
		$userId= isset($searchDetails["userId"])?$searchDetails["userId"]:null;
		$area = isset($searchDetails["areaSearch"])?$searchDetails["areaSearch"]:null;
		$bookmarkIds = isset($searchDetails["bookmarks"])?$searchDetails["bookmarks"]:null;
		$province = isset($searchDetails["provinceSearch"])?$searchDetails["provinceSearch"]:null;
		$searchImages = isset($searchDetails["searchImages"])?$searchDetails["searchImages"]:null;
		$showCustomMarkers = isset($searchDetails["showCustomMarkers"])?$searchDetails["showCustomMarkers"]:null;
		$getMonthlyStatistics = isset($searchDetails["getMonthlyStatistics"])?$searchDetails["getMonthlyStatistics"]:null;
		$requestQuoteListings = isset($searchDetails["request_quote_listings"])?$searchDetails["request_quote_listings"]:null;
		$withAddress = isset($searchDetails["with_address"])?$searchDetails["with_address"]:null;
		$withContact = isset($searchDetails["with_contact"])?$searchDetails["with_contact"]:null;
		$withEmail = isset($searchDetails["with_email"])?$searchDetails["with_email"]:null;
		$withWebsite = isset($searchDetails["with_website"])?$searchDetails["with_website"]:null;
		$withVideos = isset($searchDetails["with_videos"])?$searchDetails["with_videos"]:null;
		$withDescription = isset($searchDetails["with_description"])?$searchDetails["with_description"]:null;
		$withEvents = isset($searchDetails["with_events"])?$searchDetails["with_events"]:null;
		$withProductsOffers = isset($searchDetails["with_products_offers"])?$searchDetails["with_products_offers"]:null;
		$withAttachedFile = isset($searchDetails["with_attached_file"])?$searchDetails["with_attached_file"]:null;
		$withImages = isset($searchDetails["with_images"])?$searchDetails["with_images"]:null;
		$withSocialNetworks = isset($searchDetails["with_social_networks"])?$searchDetails["with_social_networks"]:null;
		$withPhone = isset($searchDetails["with_phone"])?$searchDetails["with_phone"]:null;
		$withAppointments = isset($searchDetails["with_appointments"])?$searchDetails["with_appointments"]:null;
		$filterByFav = isset($searchDetails["filter-by-fav"])?$searchDetails["filter-by-fav"]:null;
		$companyIds = isset($searchDetails["company-ids"])?$searchDetails["company-ids"]:null;
	
		$user = JBusinessUtil::getUser();

		if (!empty($keywordLocation)) {
			$keyword = $keywordLocation;
		}
		
		$searchForCategory = false;
		$whereCatCond = '';
		$whereCatCondFaceted ="";
		//dump($categoriesIDs);
		if ($facetedSearch == 1) {
			if (!empty($categoriesIDs)) {
				//dump($categoriesIDs);
				foreach ($categoriesIDs as $categoryId) {
					$values = explode(",", $categoryId);
					$whereCatCondFaceted .= ' and (0  ';
					foreach ($values as $value) {
						$whereCatCondFaceted .= " or categoryIds REGEXP '\\\\b".$value."\\\\b' ";
					}
					$whereCatCondFaceted .= ' ) ';
				}
				$searchForCategory = true;
			}
		}

		if (!empty($categoriesIDs) && count($categoriesIDs)>0 && !empty($categoriesIDs[0])) {
			//dump($categoriesIDs);
			$whereCatCond .= " and cc.categoryId in (";
			$categoryIds = implode(", ", $categoriesIDs);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}
		
		$bookmarkFilter = "";
		if (!empty($bookmarkIds)) {
			$bookmarkFilter = " and cp.id in (".implode(",", $bookmarkIds).")";
		}

		$distanceQuery = "";
		if (($radius>0 || $zipCodeSearch != SEARCH_BY_EXACT_CODE) && ($orderBy == "distance asc" || ($radius>0 || $zipCodeSearch != SEARCH_BY_EXACT_CODE)) && !empty($latitude) && !empty($longitude)) {
			$distanceQuery = ", 3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -( cp.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( cp.latitude) *  pi()/180) * POWER(SIN(($longitude - cp.longitude) *  pi()/180 / 2), 2) )) as distance ";
			$distanceQuery.= $showLocations?" , min(3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -( l.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( l.latitude) *  pi()/180) * POWER(SIN(($longitude -  l.longitude) *  pi()/180 / 2), 2) ))) as secondaryDistance ":" ";
			$distanceQuery.= $showLocations?" , GROUP_CONCAT(DISTINCT(3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -( l.latitude)) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( l.latitude) *  pi()/180) * POWER(SIN(($longitude -  l.longitude) *  pi()/180 / 2), 2) )))) as secondaryDistances ":" ";
		}

		// $zipAreaQuery = "";
		// if (($orderBy == "distance asc" || ($radius>0 || $zipCodeSearch != SEARCH_BY_EXACT_CODE)) && !empty($latitude) && !empty($longitude)) {
		// 	$zipAreaQuery = ", min(3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -( czc.latitude )) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( czc.latitude ) *  pi()/180) * POWER(SIN(($longitude -  czc.longitude) *  pi()/180 / 2), 2) ))) as zipDistance , GROUP_CONCAT(DISTINCT(3956 * 2 * ASIN(SQRT( POWER(SIN(($latitude -( czc.latitude )) * pi()/180 / 2),2) + COS($latitude * pi()/180 ) * COS( abs( czc.latitude ) *  pi()/180) * POWER(SIN(($longitude -  czc.longitude) *  pi()/180 / 2), 2) )))) as zipDistances";
		// }
		
		$having = "having 1 ";
		if (!empty($latitude) && !empty($longitude) && ($radius>0 || $zipCodeSearch != SEARCH_BY_EXACT_CODE)) {
			//$orderBy = "distance, ".$orderBy;
			if ($zipCodeSearch == SEARCH_BY_DISTNACE) {
				if($radius > 0 && !empty($latitude) && !empty($longitude) ){
					$having .= " and ( distance < $radius ";
					$having .= $showLocations?" or secondaryDistance < $radius )":" ) ";
					//$having .= " or zipDistance < $radius ) " ;
				}
			} else if ($zipCodeSearch == SEARCH_BY_ACTIVITY_RADIUS) {
				if ($appSettings->metric==0) {
					$having .= " and ( distance < (activity_radius * 0.621371) ";
					$having .= $showLocations?" or secondaryDistance < (activity_radius* 0.621371) ) ":" ) ";
					//$having .= "or zipDistance < (activity_radius* 0.621371)";
				} else {
					$having .= " and ( distance < activity_radius ";
					$having .= $showLocations?" or secondaryDistance < activity_radius ) ":" ) ";
					//$having .= " or  zipDistance < activity_radius ";
				}
			}
		}

		$zipCodeFilter = "";
		if($zipCodeSearch == SEARCH_BY_EXACT_CODE && !empty($zipCode)){
			$zipCodeFilter = " and (cp.postalCode ='$zipCode')";
		}

		if (empty($asc_desc)) {
			$asc_desc = "";
		}

		if ($orderBy == "distance asc" && (empty($latitude) || empty($longitude))) {
			$orderBy ="packageOrder desc";
		}

		if (strpos((string)$orderBy, "packageOrder") !== false && !$enablePackage) {
			$orderBy="";
		}


		$orderByCond = " order by";
		if(empty($orderBy) || $orderBy == "packageOrder desc" || $orderBy == "rand()") {
			$orderByCond .= " featured desc";
		}

		$secondOrderByCond = "";
		if(!empty($appSettings->second_order_search_listings)){
			$secondOrderByCond =  ', ' . $appSettings->second_order_search_listings;
		}

		if (!empty($orderBy) && $orderBy!="rand()") {
			if($orderBy == 'packageOrder desc') {
				$orderBy .= $secondOrderByCond;
				$orderByCond .= ',';
			}
			$orderByCond .=" $orderBy $asc_desc";
		}

		if ($featured) {
			$having .= " and featured = 1 ";
		}

		
		$whereNameCond='';
		
		$fields = explode(",", $appSettings->search_fields);
		$fields = array_filter($fields);
		
		if (empty($fields)) {
			$fields = array("cp.name");
		}
		
		if (in_array("cg.name", $fields)) {
			$searchForCategory = true;
		}
		
		if (!empty($keyword)) {
			$keywords = explode(" ", $keyword);
			
			if (!$multilingual) {
				$pos = array_search("t.name", $fields);
				if ($pos!==false) {
					unset($fields[$pos]);
				}
			}
			
			$sqlFilter = "";
			if (!empty($fields)) {
				foreach ($fields as $field) {
					$sqlFilter .= "(" . "$field LIKE '%" . implode("%' and $field LIKE '%", $keywords) . "%') OR ";
				}
			}
					
			$whereNameCond=" and ($sqlFilter  LOCATE('$keyword', cp.keywords)>0) ";
		}
		
		$leftJoinCompanyMembership = "";
		$leftJoinMemberships = "";
		$selectMemberhisps = "";
		$whereMembershipCondition = "";
		if (!empty($membership) || $displayMembership) {

			$membership = implode(",", array_filter(explode(",",$membership)));

			$leftJoinCompanyMembership = " left join #__jbusinessdirectory_company_membership as cmb on cmb.company_id = cp.id  ";
			$leftJoinMemberships = " left join #__jbusinessdirectory_memberships as mmb on cmb.membership_id = mmb.id and mmb.status = 1";
			$selectMemberhisps=", GROUP_CONCAT( DISTINCT mmb.id,'|',mmb.name,'|',mmb.type,'|',mmb.logo_location separator '#|') as memberships";
			if (!empty($membership)){
				$whereMembershipCondition = "and mmb.id in ( $membership ) ";
			}
		}

		$whereRatingCond='';
		if (!empty($starRating)) {
			$whereRatingCond =" and ( round(cp.review_score) = '".$starRating."' ) ";
		}

		$whereCityCond='';
		if (!empty($city)) {
			$whereCityCond =" and ( cp.city = \"".$city."\"";
			$whereCityCond.=$showLocations?" or l.city = '".$city."' ) ":") ";

			if ($limitCities) {
				$whereCityCond = " and cty.id = '".$city."'";
			}
		}

		$whereRegionCond='';
		if (!empty($region)) {
			$whereRegionCond=" and ( cp.county = '$region' ";
			$whereRegionCond.=$showLocations?"  or l.county ='$region' ) ":") ";

			if ($limitCities) {
				$whereRegionCond = " and rg.id = '".$region."'";
			}
		}
		
		$whereAreaCond='';
		if (!empty($area)) {
			$whereAreaCond=" and ( cp.area = '$area' ";
			$whereAreaCond.=$showLocations?" or l.area ='".$area."') ":") ";
		}

		$whereProvinceCond='';
		if (!empty($province)) {
			$whereProvinceCond=" and ( cp.province = '$province'";
			$whereProvinceCond.=$showLocations?" or l.province ='".$province."') ":") ";
		}
		
		$whereCountryCond='';
		if (!empty($countryId)) {
			$whereCountryCond=" and ( cp.countryId = $countryId";
			$whereCountryCond.=$showLocations?" or l.countryId = $countryId )":") ";
		}

		$whereActivityCondition = "";
		if ($limitCities && $zipCodeSearch == SEARCH_BY_ACTIVITY_AREA) {
			$whereCityCond = "";
			$whereRegionCond = "";
			$whereCountryCond = "";
			
			$parts = array();
			if(!empty($city)){
				$parts[] ="cty.id = $city";
			}
			if(!empty($region)){
				$parts[] ="rg.id = $region";
			}
			if(!empty($countryId)){
				$parts[] ="cntr.id = $countryId";
			}
	
			if(!empty($parts)){
				$whereActivityCondition = " and ( ".implode(" or ",$parts)." )";
			}
		}

		$whereLetterCondition = '';
		if (!empty($letter)) {
			if ($letter == 'all') {
				$letter = '%';
			}
			if ($letter != '[0-9]') {
				$upletter = strtolower($letter);
				$lowletter = strtoupper($letter);
				$whereLetterCondition = " and ( ";
				$whereLetterCondition .= " (cp.name like '$upletter%' or cp.name like '$lowletter%')";
				if ($appSettings->enable_multilingual) {
					$whereLetterCondition .= " or (t.name like '$upletter%' or t.name like '$lowletter%')";
				}
				$whereLetterCondition .= " ) ";
			} else {
				$whereLetterCondition = " and ( ";
				$whereLetterCondition .= " (cp.name REGEXP '^[0-9]')";
				if ($appSettings->enable_multilingual) {
					$whereLetterCondition .= " or (t.name REGEXP '^[0-9]')";
				}
				$whereLetterCondition .= " ) ";
			}
		}
		
		$whereTypeCond='';
		if (!empty($type)) {
			$whereTypeCond=" and find_in_set($type,cp.typeId) ";
		}
		
		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}
		
		$packageFilter = '';
		if ($enablePackage) {
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
									(p.expiration_type=1 and p.price=0) or (p.id = cp.package_id and p.price=0))
							)";		
		}


		$wherePackageCond="";
		if(!empty($package)){
			$wherePackageCond = " and p.id = $package";
		}

		$publishFilter = '';
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";
		
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
							$filter .=" or ";
						}
						$filter.=" (ca.value like '%$value2%' or ca.option_id = $value2) ";
					} else {
						if ($j>0) {
							$filter .=" or ";
						}
						$filter.=" (ca.value like '%$value2%')";
					}
					$j++;
				}
				if ($index>1) {
					$customAttrFilterS .=" or ";
				}
				
				if (!empty($filter)) {
					$customAttrFilterS .=" (ca.attribute_id = $key and ($filter))";
				}
			}
			$customAttrIds = array_keys($customAttributes);
			sort($customAttrIds);
			if (!empty($customAttrFilterS)) {
				$customAttrIdFilter = " and attributeSelection ='".implode(",", $customAttrIds)."'";
				$customAttrFilter=" and ($customAttrFilterS) ";
			}
		}

		//dump($customAttrFilter);

		$userIdFilter="";
		if (!empty($userId)) {
			$userIdFilter=" and cp.userId=$userId";
		}
		
		$reviewFilter = " and cr.review_type = ".REVIEW_TYPE_BUSINESS ." and cr.approved=".REVIEW_STATUS_APPROVED." ";
		if ($appSettings->show_pending_review) {
			$reviewFilter = " and cr.review_type = ".REVIEW_TYPE_BUSINESS ." and (cr.approved=".REVIEW_STATUS_APPROVED." OR cr.approved=".REVIEW_STATUS_CREATED.")";
		}


		$whereHasAddress = "";
		if (!empty($withAddress)) {
			$whereHasAddress = " and (cp.city <> '' or cp.street_number <> '' or cp.county <> '' or cp.province <> '' or cp.area <> '' or cp. countryId <> '' or cp.latitude <> '' or cp.longitude <> '' )";
		}

		$whereHasEmail = "";
		if (!empty($withEmail)) {
			$whereHasEmail = " and cp.email <> ''";
		}

		$whereHasWebsite = "";
		if (!empty($withWebsite)) {
			$whereHasWebsite = " and cp.website <> ''";
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('website_address',features) ";
			}
		}

		$whereHasDescription = "";
		if (!empty($withDescription)) {
			$whereHasDescription = " and cp.description <> ''";
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('description',features) ";
			}
		}

		$whereHasPhone = "";
		if (!empty($withPhone)) {
			$whereHasPhone = " and cp.phone <> ''";
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('phone',features) ";
			}
		}

		$whereHasSocialNetworks = "";
		if (!empty($withSocialNetworks)) {
			$whereHasSocialNetworks = " and ( cp.facebook <> '' or cp.twitter <> '' or cp.skype <> '' or cp.linkedin <> '' or cp.youtube <> '' or cp.instagram <> '' or cp.pinterest <> '' or cp.whatsapp <> '' ) ";
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('social_networks',features) ";
			}
		}

		$whereQuoteRequestListings = '';
		if ($requestQuoteListings) {
			$whereQuoteRequestListings = ' and cp.enable_request_quote = 1';
		}

		$selectMore = "";
		if ($withContact) {
			$selectMore .= " count(cpcon.id) as contactsNumber, ";
			$having .= " and contactsNumber > 0 ";
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('contact_form',features) ";
			}
		}

		if ($withAttachedFile) {
			$selectMore .= " count(attach.id) as attachmentsNumber, ";
			$having .= ' and attachmentsNumber > 0 ';
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('attachments',features) ";
			}
		}

		if ($withImages) {
			$selectMore .= " count(cpimages.id) as imagesNumber, ";
			$having .= ' and imagesNumber > 0 ';
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('attachments',features) ";
			}
		}

		if ($withEvents) {
			$selectMore .= " count(cpevents.id) as eventsNumber, ";
			$having .= ' and eventsNumber > 0 ';
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('company_events',features) ";
			}
		}

		if ($withProductsOffers) {
			$selectMore .= " count(cpoffers.id) as offersNumber, ";
			$having .= ' and offersNumber > 0 ';
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('company_offers',features) ";
			}
		}

		if ($withVideos) {
			$selectMore .= " count(cpvideos.id) as videosNumber, ";
			$having .= ' and videosNumber > 0 ';
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('videos',features) ";
			}
		}
		if (!empty($withAppointments)) {
			$selectMore .= " count(cps.id) as services, ";
			$having .= ' and services > 0 ';
			if ($enablePackage) {
				$having .= " and FIND_IN_SET('company_services',features) ";
			}
		}

		$getBookmark = true;
		// if ($appSettings->search_result_view == 8) {
		// 	$getBookmark = true;
		// }

		$whereOnlyFavorites = "";
		if ($filterByFav) {
			$whereOnlyFavorites = " and bk.id is not null ";
		}

		if(!$searchForCategory){
			$whereCatCondFaceted = "";
		}

		$selectfields = " cp.name as companyName, cp.id as companyId1, cp.ad_image, cp.yelp_id, cp.viewCount, cp.recommended,
					cp.id, cp.name, cp.alias, cp.short_description, cp.meta_description, cp.street_number, cp.address, cp.city, cp.county, cp.province, cp.area, cp.website, cp.phone, cp.mobile, 
				cp.email, cp.state, cp.fax, cp.opening_status, cp.time_zone,
				cp.employees, cp.hourly_rate, cp.min_project_size, cp.facebook, cp.twitter, cp.whatsapp, cp.instagram, cp.pinterest, cp.youtube, cp.linkedin,
				cp.averageRating, cp.slogan,cp.logoLocation, cp.activity_radius, cp. review_score, cp.creationDate, cp.package_id,
				cp.featured,cp.publish_only_city,cp.userId, cp.business_cover_image,
				cp.latitude, cp.longitude, cp.keywords,cp.approved, cp.mainSubcategory, cp.countryId,cp.typeId, cp.postalCode, cp.publish_start_date, cp.publish_end_date,cp.ordering,
				$selectMore
				".(($getMonthlyStatistics==1)?" GROUP_CONCAT(DISTINCT stat.id,'-',stat.type,'-',stat.item_count separator '##') as statistics,  ":"")."
				
				".($searchForCategory?"
				GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.lft separator '#|') as categories,
				GROUP_CONCAT( DISTINCT cg.id) as categoryIds,":"")."
					
				".($searchImages?" GROUP_CONCAT( DISTINCT CONCAT_WS('#', cpt.picture_path, cpt.picture_title, cpt.picture_info) ORDER BY cpt.id) as pictures, ":"")."
				".(($showCustomMarkers==1)?" bcm.name as mainCategory, bcm.alias as mainCategoryAlias ,bcm.markerLocation as categoryMaker,bcm.icon as mainCategoryIcon, bcm.iconImgLocation as categoryIconImage, bcm.id as mainCategoryId, ":"")."
				
				".($whereTypeCond || true ?" GROUP_CONCAT(DISTINCT ct.name) as typeName,":"")." 
				".($getBookmark?" bk.id as bookmark,":"")." 
				count(distinct cr.id) as nr_reviews,
				cn.country_name as countryName
				".($showLocations?", GROUP_CONCAT(DISTINCT l.latitude,'|',l.longitude,'|',l.street_number,'|',l.address,'|',l.city,'|',l.county,'|',l.postalCode,'|',l.phone,'|',l.province,'|',l.area separator '#') as locations ":"")
				.(!empty($customAttrFilter)?", GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection ":"")."
				$selectMemberhisps";

		$primarySelect = ", companyName ";
		if(empty($companyIds)){
			$selectfields = "cp.id, cp.featured, cp.name as companyName, cp.city, cp.review_score, cp.viewCount, cp.activity_radius, count(distinct cr.id) as nr_reviews, cp.ordering"
							.(!empty($customAttrFilter)?", GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection ":"")
							.($searchForCategory?"
								,GROUP_CONCAT( DISTINCT cg.id) as categoryIds":"");
		}

		$whereConditions = " cp.state=1 $whereMembershipCondition $whereNameCond $whereCatCond $whereTypeCond $userIdFilter $whereLetterCondition $packageFilter 
		$whereActivityCondition $whereCityCond $whereRatingCond $whereRegionCond $whereAreaCond $whereCountryCond $customAttrFilter $companyStatusFilter $publishFilter $bookmarkFilter 
		$whereProvinceCond $whereHasAddress $whereHasEmail $whereHasWebsite $whereHasDescription $whereHasPhone $whereHasSocialNetworks $whereQuoteRequestListings 
		$whereOnlyFavorites $wherePackageCond $zipCodeFilter";
		
		if(!empty($companyIds)){
			$companyIds = implode(",",$companyIds);
			$whereConditions = " cp.id in ($companyIds)";
			$customAttrIdFilter="";
			$whereCatCondFaceted="";
			$having="";
		}

		$searchForCategory = $searchForCategory || !empty($whereCatCond);
		$query = "select slec.* $primarySelect".
					($enablePackage?" ,features, GREATEST(if(FIND_IN_SET('featured_companies',features) ,1,0), featured) as featured":", featured")
					.(!empty($customAttrFilter)?", attributeSelection":"")."
				 from (
					select 
					$selectfields
					$distanceQuery 
					".($enablePackage?" ,GROUP_CONCAT(DISTINCT pf.feature) as features, max(p.ordering) as packageOrder, p.name as package_name":"")." 
					from #__jbusinessdirectory_companies cp
                    ".($searchForCategory?"
					left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId 
			   	    left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1":"")."

			   	    ".(($showCustomMarkers==1)?" left join #__jbusinessdirectory_categories bcm on bcm.id=cp.mainSubcategory and bcm.published=1 ":"")."
				    ".($showLocations?"left join #__jbusinessdirectory_company_locations l on cp.id = l.company_id ":"")."
					left join #__jbusinessdirectory_company_reviews cr on cr.itemId = cp.id $reviewFilter 
					".($searchImages?" left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=cp.id and cpt.picture_enable = 1 ":"")."
					".($getMonthlyStatistics?" left join #__jbusinessdirectory_statistics_archive stat on stat.item_id=cp.id and stat.date > DATE_SUB(DATE(NOW()), INTERVAL 1 MONTH) ":"")."
					".((!empty($city) && $limitCities)?"
					left join #__jbusinessdirectory_company_activity_city cat on cat.company_id=cp.id
					left join #__jbusinessdirectory_cities cty on cty.id=cat.city_id":"").
					((!empty($region) && $limitCities)?"
					left join #__jbusinessdirectory_company_activity_region car on car.company_id=cp.id
					left join #__jbusinessdirectory_regions rg on rg.id=car.region_id":"").
					((!empty($countryId) && $limitCities)?"
					left join #__jbusinessdirectory_company_activity_country cac on cac.company_id=cp.id
					left join #__jbusinessdirectory_countries cntr on cntr.id=cac.country_id":"").
					
					($withContact?"left join #__jbusinessdirectory_company_contact cpcon on cp.id = cpcon.companyId ":"")."
					".($withAttachedFile?"left join #__jbusinessdirectory_company_attachments attach on cp.id = attach.object_id and attach.type = '".BUSSINESS_ATTACHMENTS."' ":"")."
					".($withImages?"left join #__jbusinessdirectory_company_pictures cpimages on cp.id = cpimages.companyId ":"")."
					".($withEvents?"left join #__jbusinessdirectory_company_events cpevents on cp.id = cpevents.company_id ":"")."
					".($withProductsOffers?"left join #__jbusinessdirectory_company_offers cpoffers on cp.id = cpoffers.companyId ":"")."
					".($withVideos?"left join #__jbusinessdirectory_company_videos cpvideos on cp.id = cpvideos.companyId ":"")."
					".($withAppointments?"left join #__jbusinessdirectory_company_services cps on cp.id = cps.company_id and cps.published = 1 ":"")."
					left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id

                    ".($whereTypeCond || true?"left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,cp.typeId)":"")."
                    
                    ".($enablePackage?"
    					left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
    					left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and inv.state= ".PAYMENT_STATUS_PAID." and (NOW() between inv.start_date and inv.end_date) and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
    					left join #__jbusinessdirectory_package_fields pf on p.id=pf.package_id":"")."
					
                    ".(!empty($customAttrFilter)?"
					left join #__jbusinessdirectory_company_attributes AS ca on ca.company_id=cp.id
					left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
					left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id and ca.value = ao.id":"").
					(!empty($filterByFav || $getBookmark)?"
					left join #__jbusinessdirectory_bookmarks bk on bk.item_id = cp.id and bk.user_id = $user->ID and bk.item_type = ".BOOKMARK_TYPE_BUSINESS:"").
					
					(!empty($multilingual) && !empty($whereNameCond)?"
					left join #__jbusinessdirectory_language_translations t on cp.id = t.object_id and t.language_tag = '$language' and t.type = ".BUSSINESS_DESCRIPTION_TRANSLATION:"").
					
					"
					$leftJoinCompanyMembership
					$leftJoinMemberships
					
                    where 1 and $whereConditions
					group by cp.id 
				) as slec 
				where 1 $customAttrIdFilter $whereCatCondFaceted
				$having
                $orderByCond";
		
				
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
			$membershipActive = false;
			
			switch ($objectType) {
				case 'total':
						$selectFields = 'cp.id';
						$groupBy = 'group by cp.id';
						$orderBy = '';
					break;
				case 'city':
					$selectFields = 'cp.city as city, cp.city as cityName';
					$selectFields .= $showLocations? ", GROUP_CONCAT(distinct l.city) as secCityNames":"";
					$groupBy = 'group by cp.city';
					$orderBy = 'order by cp.city';

					if ($limitCities) {
						$selectFields = 'cty.id as city, cty.name as cityName';
						$groupBy = 'group by cty.name';
						$orderBy = 'order by cty.name';
					}
					break;
				case 'starRating':
					$selectFields = 'round (cp.review_score) as reviewScore';
					$groupBy = 'group by cp.review_score ';
					$orderBy = 'order by cp.review_score ';
					break;
				case 'region':
					$selectFields = 'cp.county as region, cp.county as regionName';
					$selectFields .= $showLocations? ", GROUP_CONCAT(distinct l.county) as secRegionNames":"";
					$groupBy = 'group by cp.county';
					$orderBy = 'order by cp.county';

					if ($limitCities) {
						$selectFields = 'rg.id as region, rg.name as regionName';
						$groupBy = 'group by rg.name';
						$orderBy = 'order by rg.name';
					}
					break;
				case 'country':
					$selectFields = 'cn.country_name as countryName, cp.countryId';
					$selectFields .= $showLocations? ", GROUP_CONCAT(DISTINCT l.countryId) as secCountryNames":"";
					$groupBy = 'group by cn.country_name';
					$orderBy = 'order by cn.country_name';
					break;
				case 'type':
					$selectFields = 'GROUP_CONCAT(DISTINCT ct.name) as typeName, GROUP_CONCAT(DISTINCT cp.typeId) as typeId';
					$groupBy = 'group by cp.typeId';
					$orderBy = 'order by ct.ordering';
					break;
				case 'letter':
					if ($letter=='all') {
						$letter = '%';
					}
					if ($letter != '[0-9]') {
						$upletter = strtolower($letter);
						$lowletter = strtoupper($letter);
						$whereLetterCondition = " and ( ";
						$whereLetterCondition .= " (cp.name like '$upletter%' or cp.name like '$lowletter%')";
						if ($appSettings->enable_multilingual) {
							$whereLetterCondition .= " or (t.name like '$upletter%' or t.name like '$lowletter%')";
						}
						$whereLetterCondition .= " ) ";
					} else {
						$whereLetterCondition = " and ( ";
						$whereLetterCondition .= " (cp.name REGEXP '^[0-9]')";
						if ($appSettings->enable_multilingual) {
							$whereLetterCondition .= " or (t.name REGEXP '^[0-9]')";
						}
						$whereLetterCondition .= " ) ";
					}
					break;
				case 'area':
					$selectFields = 'cp.area as areaName';
					$selectFields .= $showLocations? ", GROUP_CONCAT(distinct l.area) as secAreaNames":"";
					$groupBy = 'group by cp.area';
					$orderBy = 'order by cp.area';
					break;
				case 'province':
					$selectFields = 'cp.province as provinceName';
					$selectFields .= $showLocations? ", GROUP_CONCAT(distinct l.province) as secProvinceNames":"";
					$groupBy = 'group by cp.province';
					$orderBy = 'order by cp.province';
					break;
				case 'memberships':
					$membershipActive = true;
					$selectFields = 'mmb.name as membership_name, mmb.id as membership_id';
					$groupBy = 'group by mmb.id';
					$orderBy = 'order by mmb.name';
					break;
				case 'packages':
					$selectFields = 'p.name as package_name, p.id as package_id';
					$groupBy = 'group by p.id';
					$orderBy = 'order by p.ordering';
					break;
				case 'attributes':
					$selectFields = 'a2.name, a2.id, ao2.name as optionName, ca2.value';
					$groupBy = 'group by a2.id, ca2.value, ao2.name';
					$orderBy = 'order by a2.ordering';
					$whereAttrCond = 'and (ca2.value!="" or ca2.option_id > 0)';
					break;
				default:
					$selectFields = 'cg.name, cg.id';
					$groupBy = 'group by cg.id';
					$whereCatCountCond = "" ;
					$orderBy = 'order by cg.lft';
			}
			
			$query = "
					select * from 
					(select count(distinct cp.id) as nr_listings, cp.activity_radius,"
					.(!empty($customAttrFilter)?" GROUP_CONCAT(DISTINCT a.id ORDER BY a.id) as attributeSelection, ":"")
					.($enablePackage?" GROUP_CONCAT(DISTINCT pf.feature) as features, 
					GREATEST(if(FIND_IN_SET('featured_companies',GROUP_CONCAT(DISTINCT pf.feature)),1,0), featured) as featured,":"cp.featured,")."
					$selectMore
					$selectFields
					$distanceQuery 

					from #__jbusinessdirectory_companies cp

					".((!empty($whereCatCond) || (!empty($whereNameCond) && $searchForCategory ) || $objectType == "category") ?"
					left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId 
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1 ":""
					)

					.($showLocations?"left join #__jbusinessdirectory_company_locations l on cp.id = l.company_id ":"")
					
					.(($limitCities)?"
					left join #__jbusinessdirectory_company_activity_city cat on cat.company_id=cp.id
					left join #__jbusinessdirectory_cities cty on cty.id=cat.city_id":"")."
					
					".(($limitCities)?"
					left join #__jbusinessdirectory_company_activity_region car on car.company_id=cp.id
					left join #__jbusinessdirectory_regions rg on rg.id=car.region_id":"").

					((!empty($limitCities))?"
					left join #__jbusinessdirectory_company_activity_country cac on cac.company_id=cp.id
					left join #__jbusinessdirectory_countries cntr on cntr.id=cac.country_id":"").

					($withContact?"left join #__jbusinessdirectory_company_contact cpcon on cp.id = cpcon.companyId ":"")."
					".($withAttachedFile?"left join #__jbusinessdirectory_company_attachments attach on cp.id = attach.object_id and attach.type = '".BUSSINESS_ATTACHMENTS."' ":"")."
					".($withImages?"left join #__jbusinessdirectory_company_pictures cpimages on cp.id = cpimages.companyId ":"")."
					".($withEvents?"left join #__jbusinessdirectory_company_events cpevents on cp.id = cpevents.company_id ":"")."
					".($withProductsOffers?"left join #__jbusinessdirectory_company_offers cpoffers on cp.id = cpoffers.companyId ":"")."
					".($withVideos?"left join #__jbusinessdirectory_company_videos cpvideos on cp.id = cpvideos.companyId ":"")."
					".($withAppointments?"left join #__jbusinessdirectory_company_services cps on cp.id = cps.company_id and cps.published = 1 ":"")."
					
					".(!empty($whereCountryCond) || ($objectType == 'country')?"left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id":"")."
					
					".(($whereTypeCond || $objectType == "type")?"left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,cp.typeId)":"").


					($membershipActive || !empty($whereMembershipCondition)?" left join #__jbusinessdirectory_company_membership as cmb on cmb.company_id = cp.id 
					  left join #__jbusinessdirectory_memberships as mmb on cmb.membership_id = mmb.id and mmb.status = 1":"").
					
					($enablePackage?"
					left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id 
					left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and inv.state= ".PAYMENT_STATUS_PAID." and (NOW() between inv.start_date and inv.end_date) and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
					left join #__jbusinessdirectory_package_fields pf on p.id= pf.package_id":"")

					.(!empty($customAttrFilter) || $enableCustomAttr?"
					left join #__jbusinessdirectory_company_attributes AS ca on ca.company_id=cp.id
					left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
					left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id and ca.value = ao.id
					
					left join #__jbusinessdirectory_company_attributes AS ca2 on ca2.company_id=cp.id
					left join #__jbusinessdirectory_attributes a2 on ca2.attribute_id = a2.id
					left join #__jbusinessdirectory_attribute_options as ao2 on ao2.attribute_id = a2.id and ca2.value = ao2.id
					":"").

					(!empty($filterByFav)?"
					left join #__jbusinessdirectory_bookmarks bk on bk.item_id = cp.id and bk.user_id = $user->ID and bk.item_type = ".BOOKMARK_TYPE_BUSINESS:"").

					(!empty($multilingual) && !empty($whereNameCond)?"
					left join #__jbusinessdirectory_language_translations t on cp.id = t.object_id and t.language_tag = '$language' and t.type = ".BUSSINESS_DESCRIPTION_TRANSLATION:"").

					" 
					where 1 $whereNameCond $whereCatCountCond $whereTypeCond $whereLetterCondition $packageFilter $whereCityCond $whereRatingCond $whereRegionCond 
					$whereActivityCondition $whereAreaCond $whereCountryCond $whereProvinceCond $customAttrFilter $whereAttrCond and cp.state=1 $companyStatusFilter $publishFilter 
					$whereHasAddress $whereHasEmail $whereHasWebsite $whereHasDescription $whereHasPhone $whereHasSocialNetworks $whereQuoteRequestListings $zipCodeFilter $whereMembershipCondition $wherePackageCond
					$groupBy
					$having
					) s
					where 1 $customAttrIdFilter $whereCatCondFaceted
			";

			 if($objectType == "city"){
			 	//print_r($query);
			 	//exit;
			 }
		}
		return $query;
	}
	
	public function getCompaniesByNameAndCategories($searchDetails, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		
		JBusinessUtil::setGroupConcatLenght();
		JBusinessUtil::setBigSqlSelects();
		
		$startTime = microtime(true); // Gets current microtime as one long string

		//retrieve the company ids
		$query = $this->getCompaniesByNameAndCategoriesSql($searchDetails, false);
		// echo $query;
		// exit;
		$db->setQuery($query, $limitstart, $limit);
		$items =  $db->loadObjectList();
		
		$ids = array_map(function ($c) {
			return $c->id;
		},  $items);		
		
		//dump($ids);
		//retrieve the listings with their data
		$searchDetails["company-ids"] = $ids;
		$query = $this->getCompaniesByNameAndCategoriesSql($searchDetails, false);
	
		//echo($query);
		$db->setQuery($query);
		$result =  $db->loadObjectList();
		//dump($this->_db->getError());
		//dump($result);
		$endTime = microtime(true) - $startTime; // And this at the end of your code
			
		//echo PHP_EOL . 'Search script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		//exit;
		return $result;
	}
	
	public function getTotalCompaniesByNameAndCategories($searchDetails) {
		$startTime = microtime(true); // Gets current microtime as one long string
		$db =JFactory::getDBO();
		
		JBusinessUtil::setBigSqlSelects();
		
		$searchDetails["object_type"] = "total";
		$query = $this->getCompaniesByNameAndCategoriesSql($searchDetails, true);
		//echo $query;
		//exit;
		$db->setQuery($query);
		if (!$db->execute()) {
			return 0;
		}
		
		$endTime = microtime(true) - $startTime; // And this at the end of your code
			
		//echo PHP_EOL . 'Total script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		//exit;
		return $db->getNumRows();
	}

	public function getTotalCompaniesByObject($searchDetails, $objectType) {
		$startTime = microtime(true); // Gets current microtime as one long string
		$db =JFactory::getDBO();

		JBusinessUtil::setBigSqlSelects();

		JBusinessUtil::setGroupConcatLenght();

		$searchDetails["object_type"] = $objectType;

		$query = $this->getCompaniesByNameAndCategoriesSql($searchDetails, true);
		$db->setQuery($query);
		$result =  $db->loadObjectList();
		//dump($result);

		$endTime = microtime(true) - $startTime; // And this at the end of your code

		//echo PHP_EOL . 'Total by object script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		return $result;
	}

	public function getCompanyCategories($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		
		$query = "select GROUP_CONCAT(c.name) as categories from #__jbusinessdirectory_categories c
				  left join #__jbusinessdirectory_company_category cc on c.id=cc.categoryId
				  where companyId=".$companyId." 
  				  group by companyId
				 order by c.name";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->categories;
	}
	
	public function deteleCompany($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$sql = "delete from #__jbusinessdirectory_companies where id=".$companyId;
		$db->setQuery($sql);

		return $db->execute();
	}
	
	public function changeState($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = 	" UPDATE #__jbusinessdirectory_companies SET state = IF(state, 0, 1) WHERE id = ".$companyId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}

		return true;
	}
	
	/**
	 * Change featured state
	 *
	 * @param [type] $companyId
	 * @return void
	 */
	public function changeFeaturedState($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = 	" UPDATE #__jbusinessdirectory_companies SET featured = IF(featured, 0, 1) WHERE id = ".$companyId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	/**
	 * Change the recommended state
	 *
	 * @param [type] $companyId
	 * @return void
	 */
	public function changeRecommendedState($companyId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = 	" UPDATE #__jbusinessdirectory_companies SET recommended = IF(recommended, 0, 1) WHERE id = ".$companyId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	/**
	 * Change approval state
	 *
	 * @param [type] $companyId
	 * @param [type] $state
	 * @return void
	 */
	
	public function changeAprovalState($companyId, $state, $disapprovalText) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$disapprovalText = $db->escape($disapprovalText);

		$query = " UPDATE #__jbusinessdirectory_companies SET approved=$state, disapproval_text='$disapprovalText' WHERE id = ".$companyId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function changeClaimState($companyId, $state) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = " UPDATE #__jbusinessdirectory_company_claim SET status=$state WHERE companyId = ".$companyId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function getCompaniesByUserId($userId, $approved = false) {
		$approvedCond = "";
		if ($approved) {
			$approvedCond = " and bc.approved='2' ";
		}
		$db =JFactory::getDBO();
		$userId = $db->escape($userId);
		$query = "select * from #__jbusinessdirectory_companies bc  where bc.userId='$userId' $approvedCond ";
		$db->setQuery($query, 0, 1000);
		return $db->loadObjectList();
	}
	
	public function getCompanyTypes() {
		$db =JFactory::getDBO();
		$query = "select id as value, name as text from #__jbusinessdirectory_company_types order by name";
		// 		dump($query);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function increaseViewCount($id) {
		$db =JFactory::getDBO();
		$id = $db->escape($id);
		$query = "update  #__jbusinessdirectory_companies set viewCount = viewCount + 1 where id=$id";
		// 		dump($query);
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function increaseWebsiteCount($id) {
		$db =JFactory::getDBO();
		$id = $db->escape($id);

		$query = "update  #__jbusinessdirectory_companies set websiteCount = websiteCount + 1 where id=$id";
		// 		dump($query);
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function increaseContactsNumber($id) {
		$db =JFactory::getDBO();
		$id = $db->escape($id);
		
		$query = "update  #__jbusinessdirectory_companies set contactCount = contactCount + 1 where id=$id";
		//dump($query);
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function resetCompanyOwner($companyId) {
		$db = JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$query = "update  #__jbusinessdirectory_companies set userId = 0 where id=$companyId";
		//dump($query);
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function updateCompanyOwner($companyId, $userId) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);
		$userId = $db->escape($userId);
		$query = "update  #__jbusinessdirectory_companies set userId = $userId, approved=-1  where id=$companyId";
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function claimCompany($data) {
		$db =JFactory::getDBO();
		$query = "insert into  #__jbusinessdirectory_company_claim (`companyId`, `firstName`, `lastName`, `function`, `phone`, `email`, `status`) values 
			(".$db->escape($data['companyId']).",'".$db->escape($data['firstName'])."','".$db->escape($data['lastName'])."','".$db->escape($data['function'])."','".$db->escape($data['phone'])."','".$db->escape($data['email'])."',0)";
		//dump($query);
		//exit;
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function getClaimDetails($companyId) {
		$db =JFactory::getDBO();
		$companyId= $db->escape($companyId);
		$query = "select * from #__jbusinessdirectory_company_claim where companyId = $companyId and status <> -1 order by id desc";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function checkCompanyName($companyName) {
		$db =JFactory::getDBO();
		$companyName= $db->escape($companyName);
		$query = "select * from #__jbusinessdirectory_companies where name = '$companyName' ";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}
	
	public function getCompanyByName($companyName) {
		$db =JFactory::getDBO();
		$companyName = $db->escape($companyName);
		$query = "select * from #__jbusinessdirectory_companies where name = '$companyName' ";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCompaniesByCityName($cityName) {
		$db =JFactory::getDBO();
		$cityName = $db->escape($cityName);
		$query = "select bc.*, GROUP_CONCAT(distinct ct.name) as typeName,
				GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.name separator '#|') as categories,  
				cr.country_name,
				ccm.name as mainCategory, ccm.alias as mainCategoryAlias, ccm.markerLocation as categoryMarker, ccm.id as mainCategoryId
				from #__jbusinessdirectory_companies bc 
				left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,bc.typeId)
				left join #__jbusinessdirectory_company_category cc on bc.id=cc.companyId 
				left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId 
				left join #__jbusinessdirectory_categories ccm on ccm.id=bc.mainSubcategory and ccm.published=1
				left join #__jbusinessdirectory_countries as cr on cr.id = bc.countryId 
				where bc.city='".$cityName."' group by bc.id order by name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getBusinessAboutToExpire($nrDays) {
		$db =JFactory::getDBO();
		$appSettings =  JBusinessUtil::getApplicationSettings();
		
		$packageFilter = "and inv.state= ".PAYMENT_STATUS_PAID." and p.expiration_type=2 and inv.end_date>CURDATE()";

		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}

		if (empty($nrDays)) {
			$nrDays = 3;
		}

		$query = "  select cp.*, inv.id as orderId, DATEDIFF(max(inv.end_date),CURDATE()) as expiration_days
				from #__jbusinessdirectory_companies cp 
				left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id and inv.state= ".PAYMENT_STATUS_PAID."
				left join #__jbusinessdirectory_packages p on inv.package_id=p.id and p.expiration_type=2
				where 1  $companyStatusFilter $packageFilter and cp.state=1 
				group by cp.id
                having expiration_days in ($nrDays)
        ";

		echo $query;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
	
	public function getCompaniesForExport($categories, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDbo();

		$appSettings = JBusinessUtil::getApplicationSettings();
		$limitCities = $appSettings->limit_cities_regions;
		
		$whereCatCond="";
		if (!empty($categories)) {
			$whereCatCond .= " and cc.categoryId in ($categories)";
		}
		
		$query = "select slec.*, companyName, companyId1,
				GROUP_CONCAT(cph.weekday) as weekdays, GROUP_CONCAT(IFNULL(cph.start_hour, '#')) as start_hours, GROUP_CONCAT(IFNULL(cph.end_hour, '#')) as end_hours
				from (
					select cp.*, cp.name as companyName,cp.id as companyId1, cn.country_name as countryName, GROUP_CONCAT(DISTINCT ct.name) as typeName,cnt.contact_name,
					GROUP_CONCAT(DISTINCT t.name) as type,
					p.name as packageName,
					GROUP_CONCAT( DISTINCT cg.id) as categoryIds, GROUP_CONCAT(DISTINCT cg.name separator ',') as categoryNames, GROUP_CONCAT( DISTINCT CONCAT_WS('#', cpt.picture_path, cpt.picture_title, cpt.picture_info)) as pictures
					".(($limitCities) ? ",GROUP_CONCAT(DISTINCT cty.name separator ',')  as cities, GROUP_CONCAT(DISTINCT rg.name separator ',') as regions" : "") ."
					from #__jbusinessdirectory_companies cp
					left join #__jbusinessdirectory_company_pictures cpt on cpt.companyId=cp.id
					left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId
					left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id
					left join #__jbusinessdirectory_company_contact cnt on cp.id=cnt.companyId 
					left join #__jbusinessdirectory_company_types t on t.id=cp.typeId
					left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,cp.typeId)
					left join #__jbusinessdirectory_packages p on p.id=cp.package_id
					".
						(($limitCities) ? "
						left join #__jbusinessdirectory_company_activity_city cat on cat.company_id=cp.id
						left join #__jbusinessdirectory_cities cty on cty.id=cat.city_id
						left join #__jbusinessdirectory_company_activity_region car on car.company_id=cp.id
						left join #__jbusinessdirectory_regions rg on rg.id=car.region_id"
						: "")
					."
					where 1 $whereCatCond
					group by cp.id order by cp.name  )
				 as slec
				left join #__jbusinessdirectory_company_provider_hours as cph on cph.provider_id = companyId1 and cph.item_type = ".BUSINESS_HOURS." and cph.type = ".STAFF_WORK_HOURS." and cph.status = 1
				group by companyId1
				order by companyName";
		
		$db->setQuery($query, $limitstart, $limit);
		$result =  $db->loadObjectList();
		return $result;
	}

	public function getListingMap(){
		$db = JFactory::getDbo();

		$whereCatCond="";
		if (!empty($categories)) {
			$whereCatCond .= " and cc.categoryId in ($categories)";
		}

		$query = " select cp.city, cp.county, cp.countryId, cn.country_name,
				  GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.lft separator '#') as categories
				from #__jbusinessdirectory_companies cp 
                left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId 
			   	left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1 
				left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id
				where cp.city != ''
				group by cp.city
				order by cn.country_name, cp.county, cp.city";

		$db->setQuery($query);
		$result =  $db->loadObjectList();

		return $result;
	}
	
	public function getUsedLetters() {
		$db =JFactory::getDBO();
		$query = "SELECT DISTINCT UPPER(LEFT(name, 1)) as letter FROM #__jbusinessdirectory_companies ORDER BY letter";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getUsedLettersForActiveBusiness() {
		$db =JFactory::getDBO();
		$query = "SELECT DISTINCT UPPER(LEFT(name, 1)) as letter FROM #__jbusinessdirectory_companies WHERE approved=2 ORDER BY letter";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getTotalListings($userId = null) {
		$whereFilter = "";
		if (!empty($userId)) {
			$whereFilter = "where userId =  $userId";
		}
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_companies $whereFilter";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->nr;
	}
	
	public function getTodayListings() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_companies where  DATE(`creationDate`) = CURDATE() ";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->nr;
	}
	
	public function getWeekListings() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_companies  WHERE WEEKOFYEAR(creationDate)=WEEKOFYEAR(NOW())";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->nr;
	}
	
	public function getMonthListings() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_companies WHERE MONTH(creationDate)=MONTH(NOW())";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->nr;
	}
	
	public function getYearListings() {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_companies  WHERE YEAR(creationDate)=YEAR(NOW())";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->nr;
	}

	public function checkIfAliasExists($busienssId, $alias) {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as nr FROM #__jbusinessdirectory_companies  WHERE alias='$alias' and id<>$busienssId";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
	}
	
	public function getListingsViews($userId = null) {
		$whereFilter = "";
		if (!empty($userId)) {
			$whereFilter = "where userId =  $userId";
		}
	
		$db =JFactory::getDBO();
		$query = "SELECT sum(viewCount) as nr FROM #__jbusinessdirectory_companies $whereFilter";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nr;
	}
	
	public function getLastAlias($alias) {
		$db =JFactory::getDBO();
		$query = "SELECT alias FROM #__jbusinessdirectory_companies  WHERE alias like'$alias%'order by alias desc";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
	
	public function deleteAllDependencies($itemId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_company_activity_city where company_id= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_attachments WHERE type=1 and object_id = $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_attributes WHERE company_id = $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_category where companyId= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_contact where companyId= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_locations where company_id= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_pictures where companyId= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_videos where companyId= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete t1, t2 from #__jbusinessdirectory_company_reviews t1 
			inner join #__jbusinessdirectory_company_reviews_user_criteria t2 on t1.id = t2.review_id where t1.itemId= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_company_ratings where companyId= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_language_translations where type=".BUSSINESS_DESCRIPTION_TRANSLATION." and object_id= $itemId";
		$db->setQuery($sql);
		$db->execute();
		
		$sql = "delete from #__jbusinessdirectory_language_translations where type=".BUSSINESS_SLOGAN_TRANSLATION." and object_id= $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_claim where companyId= $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_membership where company_id= $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_messages where item_id= $itemId and type=".MESSAGE_TYPE_BUSINESS;
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_related where company_id= $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_reviews where itemId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_services_list where companyId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__jbusinessdirectory_company_testimonials where companyId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		return true;
	}

	public function getNewCompanies($start_date, $end_date) {
		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(creationDate, '%Y-%m-%d') as date, count(*) as value 
					from #__jbusinessdirectory_companies
					where (CAST(creationDate AS DATE) between '$start_date' and '$end_date')
					group by date
					having date IS NOT NULL
					order by date asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getCompaniesRSS($searchDetails) {

		$categoriesIDs = isset($searchDetails["categoriesIds"])?$searchDetails["categoriesIds"]:null;
		$whereCatCond = '';
		if (!empty($categoriesIDs) && count($categoriesIDs)>0 && !empty($categoriesIDs[0])) {
			$whereCatCond .= " and cc.categoryId in (";
			$categoryIds = implode(", ", $categoriesIDs);
			$whereCatCond .= $categoryIds;
			$whereCatCond .= ")";
		}

		$db =JFactory::getDBO();
		$query = "select 
                 cp.name as companyName, cp.id as companyId1, cp.ad_image, cp.yelp_id, cp.viewCount,
			  	 cp.id, cp.name, cp.alias, cp.short_description, cp.meta_description, cp.street_number, cp.address, cp.city, cp.county, cp.province, cp.area, cp.website, cp.phone, cp.mobile, cp.email, cp.state, cp.fax, 
				 cp.averageRating, cp.slogan,cp.logoLocation, cp.activity_radius, cp. review_score, cp.creationDate,
				 cp.featured,cp.publish_only_city,cp.userId, cp.description,
				 cp.latitude, cp.longitude, cp.keywords,cp.approved, cp.mainSubcategory, cp.countryId,cp.typeId, cp.postalCode, cp.publish_start_date, cp.publish_end_date,
                 GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color ORDER BY cg.lft separator '#|') as categories,
			 	 GROUP_CONCAT( DISTINCT cg.id) as categoryIds
                 from #__jbusinessdirectory_companies cp 
                 left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId 
			   	 left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId and cg.published=1 
				 where state=1  $whereCatCond
				 and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.")
				 group by id
				 order by featured desc, creationDate desc, name asc";

		$db->setQuery($query, 0, 10);
		return $db->loadObjectList();
	}

	public function getCompaniesAndEventsCategorically($startDate, $searchDetails) {
		$db = JFactory::getDbo();
		$enablePackage = isset($searchDetails["enablePackages"])?$searchDetails["enablePackages"]:null;
		$showPendingApproval = isset($searchDetails["showPendingApproval"])?$searchDetails["showPendingApproval"]:null;

		$packageFilter = '';
		if ($enablePackage) {
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

		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}

		$eventStatusFilter="and (ce.approved = ".EVENT_APPROVED.")";
		if ($showPendingApproval) {
			$eventStatusFilter = "and (ce.approved = ".EVENT_CREATED." or ce.approved = ".EVENT_APPROVED.") ";
		}

		$publishFilter = '';
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";

		$query = "select cp.id, cp.name, cp.alias, cp.business_hours, cp.address, cg.name as categoryName, GROUP_CONCAT(distinct(ce.id)) as eventIds
                  from #__jbusinessdirectory_companies cp
                  left join #__jbusinessdirectory_categories cg on cp.mainSubcategory = cg.id
                  left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
				  left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
                  left join (
                    select ce.id,ce.name, ce.alias, ce.start_date, ce.end_date, ce.start_time, ce.end_time, ce.company_id
                    from #__jbusinessdirectory_company_events ce
                    where 1 and ce.state =  1 and ce.end_date > $startDate $eventStatusFilter
                  ) as ce on ce.company_id = cp.id
                  where 1 and cp.state = 1 $companyStatusFilter $packageFilter $publishFilter
                  group by cp.id, cp.name, cp.alias, cp.business_hours, cp.address, categoryName
                  order by categoryName";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	public function updateLastUpdateNotification($companyId) {
		$db = JFactory::getDbo();
		$time = date('Y-m-d H:i:s', time());
		$query = "update #__jbusinessdirectory_companies set notified_date = '$time' where id=$companyId";

		$db->setQuery($query);
		$db->execute();

		return true;
	}
	
	/**
	 * Get all valid Related Companies for business listing to show on frontend
	 * @param $companyId
	 * @return array|void
	 */
	public function getRelatedCompanies($companyId) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$enablePackage = $appSettings->enable_packages;
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));

		$packageFilter = '';
		if ($enablePackage) {
			$packageFilter = " and (
									  (
										(inv.state= ".PAYMENT_STATUS_PAID." and (
											(
												(now() > (inv.start_date) and (now() < ( inv.start_date + INTERVAL p.days DAY) or p.expiration_type=1))
									 			or
												(now() > (inv.start_trial_date) and now() < ( inv.start_trial_date + INTERVAL p.trial_days DAY)))
									 ))
									)
									or (
										((p.expiration_type=1 and p.price=0) or (p.id = cp.package_id and p.price=0)))
								)";
		}

		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}

		$publishFilter= " ";
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";

		$state =" and cp.state = 1 ";

		$db =JFactory::getDBO();
		$query = "select DISTINCT cp.* from #__jbusinessdirectory_companies cp 
                    left join #__jbusinessdirectory_orders inv on inv.company_id=cp.id
                    left join #__jbusinessdirectory_packages p on (inv.package_id=p.id and p.status=1 and $enablePackage) or (p.price=0 and (p.expiration_type=1 or cp.package_id = p.id) and p.status=1 and $enablePackage)
                    left join #__jbusinessdirectory_company_related rc on rc.related_company_id=cp.id  
                    where rc.company_id=$companyId  $packageFilter $companyStatusFilter $publishFilter $state ";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$comp = array();
		foreach ($list as $item) {
			if($appSettings->limit_cities_regions){
				$item->regions = $this->getCompanyRegions($item->id);
				if (!empty($item->regions)) {
					$item->county = $item->regions[0]->name;
				}
				$item->cities = $this->getCompanyCities($item->id);
				if (!empty($item->cities)) {
					$item->city = $item->cities[0]->name;
				}
			}
			$comp[] = $item;
		}
		return $comp;
	}

	/**
	 * Add related Companies in database
	 * @param $companyId
	 * @param $relatedCompanyIds
	 * @return bool|void
	 */
	public function insertRelations($companyId, $relatedCompanyIds) {
		$db =JFactory::getDBO();
		if (empty($relatedCompanyIds)) {
			$query = "delete from #__jbusinessdirectory_company_related where company_id =$companyId";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
			return true;
		}

		$query = "insert into #__jbusinessdirectory_company_related(company_id, related_company_id) values ";
		foreach ($relatedCompanyIds as $relcompanyId) {
			$query = $query."(".$companyId.",".$relcompanyId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE company_id=values(company_id), related_company_id=values(related_company_id) ";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		$filter ="(";
		foreach ($relatedCompanyIds as $relcompanyId) {
			$filter = $filter.$relcompanyId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_company_related where company_id =$companyId and related_company_id not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}

	/**
	 * Get All Related Companies that are selected on the backend
	 * @param $companyId
	 * @return array
	 */
	public function getSelectedCompaniesList($companyId) {
		$db =JFactory::getDBO();
		$companyId = intval($companyId);
		$query = "select related_company_id
                  from #__jbusinessdirectory_company_related rc  
                  LEFT join #__jbusinessdirectory_companies c  on rc.company_id=c.id  
                  where rc.company_id=".$companyId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->related_company_id;
		}

		return $result;
	}

	/**
	 * Get related Companies as option to fill the dropdown
	 * @param $companyId
	 * @return mixed
	 */
	public function getCompanyRelatedOptions($companyId) {
		$db = JFactory::getDbo();
		$companyId = intval($companyId);
		$ids = $this->getSelectedCompaniesList($companyId);
		$comp_id = implode(',', $ids);
		$query ="select c.id as value, c.name as text
                from #__jbusinessdirectory_companies c 
                LEFT join  #__jbusinessdirectory_company_related rc on rc.related_company_id=c.id 
                where c.id IN ($comp_id) and rc.company_id=$companyId";
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

	public function getNewCompanyViews($start_date, $end_date, $userId = null) {
		$whereFilter = " and st.item_type=1 and st.type=0 ";
		if (!empty($userId)) {
			$whereFilter = " and cp.userId=$userId ";
		}

		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(st.date, '%Y-%m-%d') as date, sum(st.item_count) as value 
					from #__jbusinessdirectory_statistics_archive st
					LEFT JOIN #__jbusinessdirectory_companies cp on cp.id = st.item_id
					where (CAST(st.date AS DATE) between '$start_date' and '$end_date') $whereFilter 
					group by date
					having date IS NOT NULL
					order by date asc";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getListingsViewsOnFront($userId = null) {
		$whereFilter = "";
		if (!empty($userId)) {
			$whereFilter = " and cp.userId = $userId";
		}

		$db =JFactory::getDBO();
		$query = "SELECT sum(cp.viewCount) as nr 
                    FROM #__jbusinessdirectory_companies cp
                     where 1 $whereFilter ";

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->nr;
	}

	public function getPackageUseStatus($packageId) {
		$db =JFactory::getDBO();
		$query = "SELECT count(*) as number
                    FROM #__jbusinessdirectory_companies
                    WHERE package_id=".$packageId;
		$db->setQuery($query);
		$result = $db->loadObject();

		$query = "SELECT count(*) as number
                    FROM #__jbusinessdirectory_orders ord 
                    INNER JOIN #__jbusinessdirectory_companies co ON co.id = ord.company_id
                    WHERE ord.package_id=".$packageId;
		$db->setQuery($query);
		$result2 = $db->loadObject();

		$totalUse = $result->number + $result2->number;

		return $totalUse;
	}

	public function getRegionByName($region) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$db = JFactory::getDBO();
		$region = $db->escape($region);
		$query = "select distinct county as regionName 
				  from #__jbusinessdirectory_companies
				  where state = 1 and county = '$region'
                    ".(($appSettings->show_secondary_locations)? "
                  union
                  select distinct county as regionName from #__jbusinessdirectory_company_locations
                    ":"")."
                  order by regionName asc";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCityByName($city) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$db = JFactory::getDBO();
		
		$city = $db->escape($city);
		
		$query = "select distinct city as cityName 
				  from #__jbusinessdirectory_companies
				  where state = 1 and city = '$city'
                    ".(($appSettings->show_secondary_locations)? "
                  union
                  select distinct city as cityName from #__jbusinessdirectory_company_locations
                    ":"")."
                  order by cityName asc";

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCompanyRegions($companyId) {
		$db    = JFactory::getDbo();
		$companyId = intval($companyId);
		$query = "select rg.id, rg.name
				  from #__jbusinessdirectory_company_activity_region as car
				  left join #__jbusinessdirectory_regions as rg on rg.id = car.region_id
				  where car.company_id = $companyId
				  order by rg.name";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getCompanyCities($companyId) {
		$db    = JFactory::getDbo();
		$companyId = intval($companyId);
		$query = "select cty.id, cty.name
				  from #__jbusinessdirectory_company_activity_city as cac
				  left join #__jbusinessdirectory_cities as cty on cty.id = cac.city_id
				  where cac.company_id = $companyId
				  order by cty.name";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getSuggestions($keyword, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDbo();
		$keyword = $db->escape($keyword);

		$appSettings = JBusinessUtil::getApplicationSettings();
		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		
		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}
		
		$publishFilter = '';
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";
		
		$query = "select id as suggestionId, name from #__jbusinessdirectory_companies cp where name like '%$keyword%' $companyStatusFilter $publishFilter and cp.state=1";

		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Get Companies based on the string that admin site will enter on search field
	 *
	 * @param      $str
	 * @param null $userId
	 *
	 * @return mixed
	 */
	public function getCompaniesByString($str, $userId = null) {
		$db = JFactory::getDBO();
		$whereUser = "";
		if (!empty($userId)) {
			$whereUser = " and bc.userId = $userId";
		}

		$companiesQuery = "select  bc.id, bc.name 
						   from #__jbusinessdirectory_companies bc 
						   where bc.state =1 and bc.name like '%$str%' $whereUser 
						   order by bc.name";
		$db->setQuery($companiesQuery);
		$result = $db->loadObjectList();

		return $result;
	}
	
	
	/**
	 * Retrieve the companies that do not have a recurring payment activated
	 *
	 * @return list of companies
	 */
	public function getNonActiveSubscriptionsCompanies() {
		$db =JFactory::getDBO();
		
		$query = "select cp.*
				from #__jbusinessdirectory_companies cp 
                left join #__jbusinessdirectory_orders inv on cp.id=inv.company_id
                left join #__jbusinessdirectory_packages p on p.id=inv.package_id
				where (p.expiration_type < 3 or p.id is null) or (p.expiration_type > 2 and (inv.state <> 1 or inv.id is null))
				group by cp.id
				";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getListingsByStatus($status, $itemNumber = null){
		$limitCondition = '';
		if (!empty($itemNumber)) {
			$limitCondition = ' LIMIT '.$itemNumber;
		}
		$db = JFactory::getDBO();
		$companiesQuery = "select  bc.*
						   from #__jbusinessdirectory_companies bc 
						   where bc.approved = $status
						   order by bc.creationDate DESC
						   $limitCondition ";
		$db->setQuery($companiesQuery);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getCompaniesKeywords($start, $limit) {
		$db = JFactory::getDBO();

		$publishFilter = '';
		$publishFilter = " and (cp.publish_start_date<=DATE(now()) or cp.publish_start_date='0000-00-00' or cp.publish_start_date is null) and (cp.publish_end_date>=DATE(now()) or cp.publish_end_date='0000-00-00' or cp.publish_end_date is null) ";
		$appSettings = JBusinessUtil::getApplicationSettings();

		$showPendingApproval = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$companyStatusFilter="and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED.") ";
		if ($showPendingApproval) {
			$companyStatusFilter = "and (cp.approved = ".COMPANY_STATUS_APPROVED." or cp.approved= ".COMPANY_STATUS_CLAIMED." or cp.approved= ".COMPANY_STATUS_CREATED.") ";
		}

		$keywordsQuery = "select DISTINCT keywords from #__jbusinessdirectory_companies cp where state =1 $companyStatusFilter $publishFilter  limit $start, $limit";
		$db->setQuery($keywordsQuery);
		$result = $db->loadObjectList();

		return $result;
	}
}
