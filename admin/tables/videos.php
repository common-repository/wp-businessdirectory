<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

class TableVideos extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_videos', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getVideos() {
		$db = JFactory::getDBO();
		
		$query = "select v.*, cp.name as category_name 
				  from #__jbusinessdirectory_videos v
				  left join #__jbusinessdirectory_video_category cvc on cvc.category_id=v.main_subcategory and cvc.video_id=v.id
				  left join  #__jbusinessdirectory_categories cp on cp.id=cvc.category_id";
				 

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTotalVideosByCategories($searchDetails) {
		$startTime = microtime(true); // Gets
		$db = JFactory::getDBO();

		$query = $this->getVideosByCategoriesSql($searchDetails);

		$db->setQuery($query);
		$db->execute();
		$result = $db->getNumRows();
		$endTime = microtime(true) - $startTime; // And this at the end of your code

		//echo PHP_EOL . 'Total by cat script took ' . round($endTime, 4) . ' seconds to run. <br/>';

		return $result;
	}

	public function getVideosByCategories($searchDetails, $limitstart = 0, $limit = 0) {
		$startTime = microtime(true); // Gets
		$db =JFactory::getDBO();

		$query = $this->getVideosByCategoriesSql($searchDetails);

		//echo($query);
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();
		$endTime = microtime(true) - $startTime; // And this at the end of your code

		//echo PHP_EOL . 'Search script took ' . round($endTime, 4) . ' seconds to run. <br/>';
		return $result;
	}

	public function getVideosByCategoriesSql($searchDetails, $totalCategories = false) {
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
		$facetedSearch = isset($searchDetails["facetedSearch"])?$searchDetails["facetedSearch"]:null;
		$orderBy = isset($searchDetails["orderBy"])?$searchDetails["orderBy"]:null;

		$whereCatCond = '';
		if (!empty($categoriesIDs) && count($categoriesIDs)>0) {
			$whereCatCond .= " and cc.category_id in (";
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
						$whereCatCond .= " or cg.id REGEXP '[[:<:]]".$value."[[:>:]]' ";
					}
					$whereCatCond .= ' ) ';
				}
			}
		}

		$whereNameCond='';
		if (!empty($keyword)) {
			$keywords = explode(" ", $keyword);
			$fields= array("vd.video_name","vd.video_description");

			$sqlFilter="";
			foreach ($fields as $field) {
				if ($field=="vd.name") {
					continue;
				}
				$sqlFilter .= "("."$field LIKE '%".implode("%' and $field LIKE '%", $keywords) . "%') OR ";
			}

			$whereNameCond=" and ($sqlFilter  vd.video_name like '%$keyword%') ";
		}

		if (empty($asc_desc)) {
			$asc_desc = "";
		}

		if ($orderBy=="rand()" || empty($orderBy)) {
			$orderBy = "vd.id";
			$asc_desc = "desc";
			$asc_desc = "desc";
		}

		$query = " 
			select vd.*, bcm.name as mainSubcategoryName, bcm.color as cat_color"
			.(!empty($whereCatCond)?", GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color  ORDER BY cg.name separator '#|') as categories":"")."
			from #__jbusinessdirectory_videos as vd "
			.(!empty($whereCatCond)?"
			left join #__jbusinessdirectory_video_category cc on vd.id=cc.video_id
			left join #__jbusinessdirectory_categories cg on cg.id=cc.category_id and cg.published=1":"")." 
			left join #__jbusinessdirectory_categories bcm on bcm.id=vd.main_subcategory and bcm.published=1
			where 1 $whereNameCond $whereCatCond
			group by vd.id
			order by $orderBy $asc_desc
		";

		return $query;
	}

	public function getVideo($videoId) {
		$videoId = (int) $videoId;
		$db = JFactory::getDBO();

		$query = "select vd.*,
					GROUP_CONCAT( DISTINCT cg.id,'|',cg.name,'|',cg.alias,'|',cg.icon,'|',cg.color  ORDER BY cg.name separator '#|') as categories
					from #__jbusinessdirectory_videos as vd
					left join #__jbusinessdirectory_video_category cc on vd.id=cc.video_id
					left join #__jbusinessdirectory_categories cg on cg.id=cc.category_id and cg.published=1 
					where vd.id='$videoId'";

		$db->setQuery($query);
		return $db->loadObject();
	}
}
