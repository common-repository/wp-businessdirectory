<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableOfferCoupon extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_offer_coupons', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCoupon($couponId) {
		$db = JFactory::getDBO();
		$query = "select ofc.*, co.name as company, co.phone as phone, off.subject as offer, off.description as offer_description,
                   off.address as offer_address, off.city as offer_city, off.endDate as expiration_time, off.price as offerPrice,
                   off.specialPrice as offerSpecialPrice, off.price_text as priceText, off.currencyId as offerCurrencyId
					from #__jbusinessdirectory_company_offer_coupons ofc
					left join #__jbusinessdirectory_company_offers off on off.id=ofc.offer_id
					left join #__jbusinessdirectory_companies co on co.id=off.companyId
					where ofc.id='$couponId'";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getCoupons($filter, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDBO();
		$query = "select ofc.*, co.id as company_id, co.name as company, co.phone as phone, off.subject as offer, off.endDate as expiration_time
					from #__jbusinessdirectory_company_offer_coupons ofc
					left join #__jbusinessdirectory_company_offers off on off.id=ofc.offer_id
					left join #__jbusinessdirectory_companies co on co.id=off.companyId
					$filter";
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function checkCoupon($code) {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_offer_coupons where code='$code'";
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if ($num_rows>0) {
			return true;
		}
		return false;
	}

	public function saveCoupon($userId, $offerId, $orderId, $code) {
		$db = JFactory::getDBO();
		$code = $db->escape($code);

		$query = "insert into #__jbusinessdirectory_company_offer_coupons (user_id, offer_id, order_id, code, generated_time) VALUES ('$userId', '$offerId', $orderId, '$code', NOW())";
		$db->setQuery($query);
		
		if (!$db->execute()) {
			return null;
		};
		
		return $db->insertid();
	}

	public function getCouponsByUserId($userId, $limitstart = 0, $limit = 0) {
		$db = JFactory::getDBO();
		$query = "select ofc.*, co.id as company_id, co.name as company, co.phone as phone, off.subject as offer, off.endDate as expiration_time
					from #__jbusinessdirectory_company_offer_coupons ofc
					left join #__jbusinessdirectory_company_offers off on off.id=ofc.offer_id
					left join #__jbusinessdirectory_companies co on co.id=off.companyId
					where off.user_id='$userId' or ofc.user_id = '$userId'";
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getUserCoupon($offerId, $orderId, $userId) {
		$db = JFactory::getDBO();
		$query = "select ofc.*
					from #__jbusinessdirectory_company_offer_coupons ofc
					where ofc.offer_id='$offerId' and ofc.order_id = $orderId and ofc.user_id = '$userId'";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getLastOfferCoupon($offerId) {
		$db = JFactory::getDBO();
		$query = "select ofc.*
					from #__jbusinessdirectory_company_offer_coupons ofc
					where ofc.offer_id='$offerId' 
					order by ofc.id desc
					limit 1
					";
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getTotalOfferCoupons($offerId) {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_offer_coupons where offer_id='$offerId'";
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getTotalUserOfferCoupons($userId) {
		$db = JFactory::getDBO();
		$query = "select ofc.*, co.id as company_id, co.name as company, co.phone as phone, off.subject as offer, off.endDate as expiration_time
					from #__jbusinessdirectory_company_offer_coupons ofc
					left join #__jbusinessdirectory_company_offers off on off.id=ofc.offer_id
					left join #__jbusinessdirectory_companies co on co.id=off.companyId
					where off.user_id='$userId' or ofc.user_id = '$userId'
                    group by ofc.id";
		$db->setQuery($query);
		$db->execute();
		$result = $db->getNumRows();
		
		return $result;
	}

	public function checkOffer($offerId) {
		$totalCoupons = $this->getTotalOfferCoupons($offerId);
		
		$db = JFactory::getDBO();
		$query = "select total_coupons, endDate from #__jbusinessdirectory_company_offers where id='$offerId'";
		$db->setQuery($query);
		$offer = $db->loadObject();

		$today      = strtotime(date("Y-m-d"));
		$endOffer   = strtotime($offer->endDate);

		// If the total coupons available is not reached and the offer has not expired
		if (((int)$offer->total_coupons > $totalCoupons) && ($endOffer >= $today || $offer->endDate=="0000-00-00")) {
			return true;
		}

		return false;
	}
}
