<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableReport extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_reports', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getReport($reportId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_reports where id=".$reportId;
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getReportData($report, $orderBy, $displayActive) {
		JBusinessUtil::setBigSqlSelects();
		$reportColumns = $report->selected_params;

		if (!empty($report->start_date) && $report->start_date != '0000-00-00') {
			$joinStatistics = "
			left join `#__jbusinessdirectory_statistics_archive` starchView on starchView.item_id = cp.id and starchView.item_type = '".STATISTIC_ITEM_BUSINESS."' and (CAST(starchView.date AS DATE) between '$report->start_date' and '$report->end_date') and starchView.type='".STATISTIC_TYPE_VIEW."' 
			left join `#__jbusinessdirectory_statistics_archive` starchContact on starchContact.item_id = cp.id and starchContact.item_type = '".STATISTIC_ITEM_BUSINESS."' and (CAST(starchContact.date AS DATE) between '$report->start_date' and '$report->end_date') and starchContact.type='".STATISTIC_TYPE_CONTACT."' 
			left join `#__jbusinessdirectory_statistics_archive` starchWebsite on starchWebsite.item_id = cp.id and starchWebsite.item_type = '".STATISTIC_ITEM_BUSINESS."' and (CAST(starchWebsite.date AS DATE) between '$report->start_date' and '$report->end_date') and starchWebsite.type='".STATISTIC_TYPE_WEBSITE_CLICK."' 
			left join `#__jbusinessdirectory_statistics_archive` statsWhatsup on statsWhatsup.item_id = cp.id and statsWhatsup.item_type = '".STATISTIC_ITEM_BUSINESS."' and (CAST(statsWhatsup.date AS DATE) between '$report->start_date' and '$report->end_date') and statsWhatsup.type='".STATISTIC_TYPE_SHARE."' 
			";
		} else {
			$joinStatistics = "
			left join `#__jbusinessdirectory_statistics_archive` starchView on starchView.item_id = cp.id and starchView.item_type = '".STATISTIC_ITEM_BUSINESS."' and starchView.type='".STATISTIC_TYPE_VIEW."' 
			left join `#__jbusinessdirectory_statistics_archive` starchContact on starchContact.item_id = cp.id and starchContact.item_type = '".STATISTIC_ITEM_BUSINESS."' and starchContact.type='".STATISTIC_TYPE_CONTACT."' 
			left join `#__jbusinessdirectory_statistics_archive` starchWebsite on starchWebsite.item_id = cp.id and starchWebsite.item_type = '".STATISTIC_ITEM_BUSINESS."' and starchWebsite.type='".STATISTIC_TYPE_WEBSITE_CLICK."' 
			left join `#__jbusinessdirectory_statistics_archive` statsWhatsup on statsWhatsup.item_id = cp.id and statsWhatsup.item_type = '".STATISTIC_ITEM_BUSINESS."' and statsWhatsup.type='".STATISTIC_TYPE_SHARE."' 
			";
		}

		$db =JFactory::getDBO();
		$reportColumns = explode(',', $reportColumns);
		foreach ($reportColumns as $key => $col) {
			if ($col == 'start_package' || $col == 'expire_package' || $col=="whatsupCount") {
				unset($reportColumns[$key]);
			}
		}
		
		$reportColumns = implode(',', $reportColumns);
		$activeCond='';
		$displayActive = (int)$displayActive;
		if ($displayActive != 2) {
			$activeCond = ' and cp.state ="'.$displayActive.'" ';
		}

		$query = "select $reportColumns, jb.custom_attributes,jb.orders
					#contactCounts,websiteCounts,viewCounts, whatsupCounts 
					from (
					select cp.*, cn.country_name as countryName, u.display_name as userName, p.name as package ,GROUP_CONCAT(distinct ct.name) as type,
					       #group_concat(DISTINCT starchView.id,'-',starchView.item_count separator '##') as viewCounts,
                           #group_concat(DISTINCT starchContact.id,'-',starchContact.item_count separator '##') as contactCounts,
                           #group_concat(DISTINCT starchWebsite.id,'-',starchWebsite.item_count separator '##') as websiteCounts,
						   #roup_concat(DISTINCT statsWhatsup.id,'-',statsWhatsup.item_count separator '##') as whatsupCounts,
					cnt.contact_name, cnt.contact_email, cnt.contact_phone, cnt.contact_fax, cp.business_hours as opening_hours,
					sb.status as subscription_status,
                    GROUP_CONCAT(DISTINCT inv.start_date,'|',IFNULL(inv.start_trial_date, ''),'|',inv.state,'|',inv.package_id,'|',inv.id,'|',inv.company_id,'|',inv.end_date  SEPARATOR '#|') AS orders,
					GROUP_CONCAT(distinct a.name,'||', a.code,'||', at.code,'||', ca.value,'||',a.id  separator '#') as custom_attributes

					from #__jbusinessdirectory_companies as cp
					left join #__jbusinessdirectory_company_category cc on cp.id=cc.companyId 
					left join #__jbusinessdirectory_categories cg on cg.id=cc.categoryId 
					left join #__jbusinessdirectory_categories bc on bc.id=cp.mainSubcategory
					left join #__jbusinessdirectory_company_contact cnt on cp.id=cnt.companyId 
					left join #__jbusinessdirectory_countries cn on cp.countryId=cn.id 
					left join #__jbusinessdirectory_company_types ct on find_in_set(ct.id,cp.typeId)
					left join #__jbusinessdirectory_packages p on p.id=cp.package_id
					left join `#__jbusinessdirectory_orders` inv on inv.company_id = cp.id
					LEFT JOIN `#__jbusinessdirectory_subscriptions` as sb on sb.company_id = cp.id and ( sb.status = ".SUBSCRIPTION_STATUS_ACTIVE." or sb.status = ".SUBSCRIPTION_STATUS_CANCELED." )
					left join #__users as u on u.id = cp.userId
					
					left join #__jbusinessdirectory_company_attributes AS ca on ca.company_id = cp.id 
					left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
					left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type
					
					
					where 1 $activeCond
					group by cp.id 
					order by $orderBy
				) as jb
				";

		$db->setQuery($query);
		$reportData = $db->loadObjectList();

		return $reportData;
	}

	public function getConferenceReportData($reportColumns) {
		$from = array("conferenceName", "c_viewCount", "sessionName", "cs_viewCount", "speakerName", "csp_viewCount", "categoryName", "cg_clickCount", "cst_clickCount");
		$to = array("c.name as conferenceName", "c.viewCount as c_viewCount", "cs.name as sessionName", "cs.viewCount as cs_viewCount", "cp.name as speakerName", "cp.viewCount as csp_viewCount", "cg.name as categoryName", "cg.clickCount as cg_clickCount", "cst.clickCount as cst_clickCount");
		$reportColumns = str_replace($from, $to, $reportColumns);
		$first = current(explode(" ", $reportColumns));

		if (($first == 'c.name') || ($first == 'c.viewCount')) {
			$query = "select $reportColumns
						from #__jbusinessdirectory_conferences as c 
						left join #__jbusinessdirectory_conference_sessions cs on c.id=cs.conferenceId 
						left join #__jbusinessdirectory_conference_session_types cst on cs.sessiontypeId=cst.id
						left join #__jbusinessdirectory_conference_session_categories csc on cs.id=csc.sessionId 
						left join #__jbusinessdirectory_categories cg on csc.categoryId=cg.id
						left join #__jbusinessdirectory_conference_session_speakers csp on csp.sessionId=cs.id
						left join #__jbusinessdirectory_conference_speakers cp on cp.id=csp.speakerId 
						group by c.id 
						order by c.viewCount desc";
		} elseif (($first == 'cs.name') || ($first == 'cs.viewCount')) {
			$query = "select $reportColumns
						from #__jbusinessdirectory_conference_sessions as cs
						left join #__jbusinessdirectory_conferences c on cs.conferenceId=c.id
						left join #__jbusinessdirectory_conference_session_types cst on cs.sessiontypeId=cst.id
						left join #__jbusinessdirectory_conference_session_categories csc on cs.id=csc.sessionId 
						left join #__jbusinessdirectory_categories cg on csc.categoryId=cg.id
						left join #__jbusinessdirectory_conference_session_speakers csp on csp.sessionId=cs.id
						left join #__jbusinessdirectory_conference_speakers cp on cp.id=csp.speakerId 
						group by cs.id 
						order by cs.viewCount desc";
		} elseif (($first == 'cp.name') || ($first == 'cp.viewCount')) {
			$query = "select $reportColumns
						from #__jbusinessdirectory_conference_speakers as cp
						left join #__jbusinessdirectory_conference_session_speakers csp on csp.speakerId=cp.id
						left join #__jbusinessdirectory_conference_sessions cs on csp.sessionId=cs.id
						left join #__jbusinessdirectory_conference_session_types cst on cs.sessiontypeId=cst.id
						left join #__jbusinessdirectory_conference_session_categories csc on cs.id=csc.sessionId 
						left join #__jbusinessdirectory_categories cg on csc.categoryId=cg.id
						left join #__jbusinessdirectory_conferences c on cs.conferenceId=c.id
						group by cp.id 
						order by cp.viewCount desc";
		} elseif (($first == 'cg.name') || ($first == 'cg.clickCount')) {
			$query = "select $reportColumns
						from #__jbusinessdirectory_categories as cg
						left join #__jbusinessdirectory_conference_session_categories csc on cg.id=csc.categoryId
						left join #__jbusinessdirectory_conference_sessions cs on csc.sessionId=cs.id
						left join #__jbusinessdirectory_conference_session_types cst on cs.sessiontypeId=cst.id
						left join #__jbusinessdirectory_conferences c on cs.conferenceId=c.id
						left join #__jbusinessdirectory_conference_session_speakers csp on csp.sessionId=cs.id
						left join #__jbusinessdirectory_conference_speakers cp on cp.id=csp.speakerId 
						group by cg.id 
						order by cg.clickCount desc";
		} else {
			$query = "select $reportColumns
						from #__jbusinessdirectory_conference_session_types as cst
						left join #__jbusinessdirectory_conference_sessions cs on cst.id=cs.sessiontypeId
						left join #__jbusinessdirectory_conference_session_categories csc on cs.id=csc.sessionId
						left join #__jbusinessdirectory_categories cg on csc.categoryId=cg.id
						left join #__jbusinessdirectory_conferences c on cs.conferenceId=c.id
						left join #__jbusinessdirectory_conference_session_speakers csp on csp.sessionId=cs.id
						left join #__jbusinessdirectory_conference_speakers cp on cp.id=csp.speakerId 
						group by cst.id 
						order by $first";
		}
		
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$reportData = $db->loadObjectList();
		return $reportData;
	}

	public function getReports() {
		$db =JFactory::getDBO();
		$query = "select p.*
					from #__jbusinessdirectory_reports p
					group by p.id
					order by p.name asc";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function insertRelations($reportId, $features) {
		$db =JFactory::getDBO();
		
		$query = "delete from #__jbusinessdirectory_report_fields where report_id = $reportId";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
			
		$query = "insert into #__jbusinessdirectory_report_fields(report_id, feature) values ";
		foreach ($features as $feature) {
			$query = $query."(".$reportId.",'".$db->escape($feature)."'),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE report_id=values(report_id), feature=values(feature) ";
	
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
	}

	public function getPaymentReport($searchDetails = array(), $limitstart = 0, $limit = 0) {
		$db = JFactory::getDbo();

		$query = $this->getPaymentReportSql($searchDetails);
		$db->setQuery($query, $limitstart, $limit);
		$results = $db->loadObjectList();

		return $results;
	}

	public function getTotalPaymentReport($searchDetails = array()) {
		$db    = JFactory::getDbo();
		$query = $this->getPaymentReportSql($searchDetails);

		$db->setQuery($query);
		if (!$db->execute()) {
			return 0;
		}

		return $db->getNumRows();
	}

	public function getPaymentReportSql($searchDetails) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$paymentStatus  = isset($searchDetails["payment_status"]) ? $searchDetails["payment_status"] : null;
		$currency       = isset($searchDetails["currency"]) ? $searchDetails["currency"] : null;
		$keywordSearch  = isset($searchDetails['keywordSearch']) ? $searchDetails['keywordSearch'] : null;
		$startDate      = isset($searchDetails["start_date"]) ? $searchDetails["start_date"] : null;
		$endDate        = isset($searchDetails["end_date"]) ? $searchDetails["end_date"] : null;
		$itemType       = isset($searchDetails["item_type"]) ? $searchDetails["item_type"] : null;
		$order          = isset($searchDetails["order"]) ? $searchDetails["order"] : null;
		$user          = isset($searchDetails["user"]) ? $searchDetails["user"] : null;
		$dir            = isset($searchDetails["dir"]) ? $searchDetails["dir"] : null;

		$whereDate = "";
		if (!empty($startDate) && !empty($endDate)) {
			$whereDate = " and (pm.created>='$startDate' and pm.created<='$endDate')";
		} elseif (!empty($startDate)) {
			$whereDate = " and pm.created>='$startDate'";
		} elseif (!empty($endDate)) {
			$whereDate = " and pm.created<='$endDate'";
		}

		$orderBy ='';
		if (!empty($order)) {
			$orderDir = '';
			if (!empty($dir)) {
				$orderDir = $dir;
			}

			$orderBy = 'order by ' . $order . ' ' . $orderDir;
		}

		$whereStatus = '';
		if (!empty($paymentStatus) || $paymentStatus == "0") {
			$whereStatus = " and pm.payment_status='$paymentStatus'";
		}

		$whereType = '';
		if (!empty($itemType)) {
			$whereType = " and pm.type='$itemType'";
		}

		$whereCurrency = '';
		if (!empty($currency)) {
			$whereCurrency = " and pm.currency LIKE '%$currency%'";
		}

		$havingCond= '';
        if (!empty($keywordSearch) || !empty($user)){
            $havingCond = " having 1 ";
        }

		$selectFields = " ";
		$leftJoin = ' ';
		$whereCond = "";
		$having = '';

		$queries = array();
		$queries[PAYMENT_TYPE_PACKAGE] = array();
		$queries[PAYMENT_TYPE_PACKAGE]["select"] = ' ord.description as name, cmp.userId as cp_user_id,';
		$queries[PAYMENT_TYPE_PACKAGE]["join"] = ' left join #__jbusinessdirectory_orders as ord on ord.id = pm.order_id';
		$queries[PAYMENT_TYPE_PACKAGE]["join"] .= ' left join #__jbusinessdirectory_companies as cmp on cmp.id = ord.company_id';
		$queries[PAYMENT_TYPE_PACKAGE]["where"] = " cp_user_id = $user";
		$queries[PAYMENT_TYPE_PACKAGE]["having"] = " name LIKE %$keywordSearch%";
		

		$queries[PAYMENT_TYPE_SERVICE] = array();
		$queries[PAYMENT_TYPE_SERVICE]["select"] = 'CONCAT_WS(" ", csb.first_name," ",csb.last_name, " - ",cs.name) as sname , cmps.userId as s_user_id,';
		$queries[PAYMENT_TYPE_SERVICE]["join"] = ' left join #__jbusinessdirectory_company_service_bookings as csb on csb.id = pm.order_id';
		$queries[PAYMENT_TYPE_SERVICE]["join"] .= ' left join #__jbusinessdirectory_company_services as cs on cs.id = csb.service_id';
		$queries[PAYMENT_TYPE_SERVICE]["join"] .= ' left join #__jbusinessdirectory_companies AS cmps ON cmps.id=cs.company_id';
		$queries[PAYMENT_TYPE_SERVICE]["where"] = " s_user_id = $user";
		$queries[PAYMENT_TYPE_SERVICE]["having"] = " sname LIKE %$keywordSearch%";

		$queries[PAYMENT_TYPE_EVENT] = array();
		$queries[PAYMENT_TYPE_EVENT]["select"] = 'CONCAT_WS(" ", ceb.first_name," ",ceb.last_name, " - ",ce.name) as ename, ce.user_id as e_user_id,';
		$queries[PAYMENT_TYPE_EVENT]["join"] = ' left join #__jbusinessdirectory_company_event_bookings as ceb on ceb.id = pm.order_id';
		$queries[PAYMENT_TYPE_EVENT]["join"] .= ' left join #__jbusinessdirectory_company_events as ce on ce.id = ceb.event_id';
		$queries[PAYMENT_TYPE_EVENT]["where"] = " e_user_id = $user";
		$queries[PAYMENT_TYPE_EVENT]["having"] = " ename LIKE %$keywordSearch%";

		$queries[PAYMENT_TYPE_OFFER] = array();
		$queries[PAYMENT_TYPE_OFFER]["select"] = 'CONCAT_WS(" ", coo.first_name," ",coo.last_name, " - ",co.subject) as oname, co.user_id as o_user_id,';
		$queries[PAYMENT_TYPE_OFFER]["join"] = ' left join #__jbusinessdirectory_company_offer_orders as coo on coo.id = pm.order_id';
		$queries[PAYMENT_TYPE_OFFER]["join"] .= ' left join #__jbusinessdirectory_company_offer_order_products as oop on oop.order_id = coo.id';
		$queries[PAYMENT_TYPE_OFFER]["join"] .= ' left join #__jbusinessdirectory_company_offers as co on co.id = oop.offer_id';
		$queries[PAYMENT_TYPE_OFFER]["where"] = "  o_user_id = $user";
		$queries[PAYMENT_TYPE_OFFER]["having"] = " oname LIKE %$keywordSearch%";

		$queries[PAYMENT_TYPE_CAMPAIGN] = array();
		$queries[PAYMENT_TYPE_CAMPAIGN]["select"] = ' CONCAT_WS(" ", ca.name,"-",cmpc.name) as cname, cmpc.userId as c_user_id,';
		$queries[PAYMENT_TYPE_CAMPAIGN]["join"] = ' left join #__jbusinessdirectory_campaigns as ca on ca.id = pm.order_id';
		$queries[PAYMENT_TYPE_CAMPAIGN]["join"] .= ' left join #__jbusinessdirectory_companies as cmpc on cmpc.id = ca.company_id';
		$queries[PAYMENT_TYPE_CAMPAIGN]["where"] = " c_user_id = $user";
		$queries[PAYMENT_TYPE_CAMPAIGN]["having"] = " cname LIKE %$keywordSearch%";

		switch ($itemType) {
			case PAYMENT_TYPE_PACKAGE:
				$selectFields .= $queries[PAYMENT_TYPE_PACKAGE]["select"];
				$leftJoin .= $queries[PAYMENT_TYPE_PACKAGE]["join"];
				$whereCond = $queries[PAYMENT_TYPE_PACKAGE]["where"];
				$having .= $queries[PAYMENT_TYPE_PACKAGE]["having"];
				break;
			case PAYMENT_TYPE_SERVICE:
				$selectFields .= $queries[PAYMENT_TYPE_SERVICE]["select"];
				$leftJoin .= $queries[PAYMENT_TYPE_SERVICE]["join"];
				$whereCond = $queries[PAYMENT_TYPE_SERVICE]["where"];
				$having .= $queries[PAYMENT_TYPE_SERVICE]["having"];
				break;
			case PAYMENT_TYPE_EVENT:
				$selectFields .= $queries[PAYMENT_TYPE_EVENT]["select"];
				$leftJoin .= $queries[PAYMENT_TYPE_EVENT]["join"];
				$whereCond = $queries[PAYMENT_TYPE_EVENT]["where"];
				$having .= $queries[PAYMENT_TYPE_EVENT]["having"];
				break;
			case PAYMENT_TYPE_OFFER:
				$selectFields .= $queries[PAYMENT_TYPE_OFFER]["select"];
				$leftJoin .= $queries[PAYMENT_TYPE_OFFER]["join"];
				$whereCond = $queries[PAYMENT_TYPE_OFFER]["where"];
				$having .= $queries[PAYMENT_TYPE_OFFER]["having"];
				break;
			case PAYMENT_TYPE_CAMPAIGN:
				$selectFields .= $queries[PAYMENT_TYPE_CAMPAIGN]["select"];
				$leftJoin .= $queries[PAYMENT_TYPE_CAMPAIGN]["join"];
				$whereCond = $queries[PAYMENT_TYPE_CAMPAIGN]["where"];
				$having .= $queries[PAYMENT_TYPE_CAMPAIGN]["having"];
				break;
			default:
				$w = array();
				$h = array();
				foreach($queries as $query){
					$selectFields .= $query["select"];
					$leftJoin .= $query["join"];
					$w[]= $query["where"];
					$h[]=$query["having"];
				}
				if (!empty($user)) {
					$whereCond .= " (".implode(" or",$w).")";
				}
				
				if (!empty($desciption)) {
					$having .= " (".implode(" or",$w).")";
				}

			break;
		}

		if(empty($user)){
			$whereCond = "";
		}

		if(empty($keywordSearch)){
			$having = "";
		}

		if (!empty($whereCond)) {
			$havingCond .= " and $whereCond";
		}

		if (!empty($having)) {
			$havingCond .= " and $having";
		}

		$query = "select pm.*, $selectFields concat(pm.amount,' (',pm.currency,') ') as order_amount
				  from #__jbusinessdirectory_payments as pm
				  ".$leftJoin."
				  
				  where 1 and (pm.order_id > 0) $whereDate $whereStatus $whereType $whereCurrency 
				  group by pm.payment_id,pm.order_id
				  $havingCond
				  $orderBy
		
				  ";

		//echo $query;
		//exit;
		return $query;
	}

	public function getOfferReportData($report, $displayActive) {
		$reportColumns = $report->selected_params;
		$activeCond='';
		$displayActive = (int)$displayActive;
		if ($displayActive != 2) {
			$activeCond = ' and o.state ="'.$displayActive.'" ';
		}
		
		$query = "select $reportColumns, jbo.custom_attributes
		from (
			select o.*, o.subject as name, cn.country_name as countryName, u.display_name as userName, cot.name as typeName, bc.name as categoryName, o.enable_offer_selling as item_selling_type,
			GROUP_CONCAT(distinct a.name,'||', a.code,'||', at.code,'||', ca.value,'||',a.id  separator '#') as custom_attributes
			from  #__jbusinessdirectory_company_offers o

			left join #__jbusinessdirectory_company_offer_category coc on o.id=coc.offerId 
			left join #__jbusinessdirectory_categories cg on cg.id=coc.categoryId 
			left join #__jbusinessdirectory_categories bc on bc.id=o.main_subcategory
			left join #__jbusinessdirectory_countries cn on o.countryId=cn.id 
			left join #__jbusinessdirectory_company_offer_types cot on cot.id=o.offer_type
			left join #__users as u on u.id = o.user_id

			left join #__jbusinessdirectory_offer_attributes AS ca on ca.offer_id = o.id 
			left join #__jbusinessdirectory_attributes a on ca.attribute_id = a.id
			left join #__jbusinessdirectory_attribute_types AS at on at.id=a.type

			where 1 $activeCond
			group by o.id 
			order by o.id
		) as jbo";		

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$reportData = $db->loadObjectList();

		return $reportData;
	}
}
