<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class JTableShippingMethod
 *
 * @since 5.1.0
 */
class JTableShippingMethod extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 *
	 * @since 5.1.0
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_shipping_methods', 'id', $db);
	}

	/**
	 * Sets key
	 *
	 * @param $k
	 *
	 * @since 5.1.0
	 */
	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	/**
	 * Changes the published state for a shipping method
	 *
	 * @param $id int ID of the shipping method
	 *
	 * @return bool
	 *
	 * @since 5.1.0
	 */
	public function changeState($id) {
		$db    = JFactory::getDbo();
		$id    = $db->escape($id);
		$query = " UPDATE #__jbusinessdirectory_shipping_methods SET published = IF(published, 0, 1) WHERE id = " . $id;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	/**
	 * Retrieves all shipping methods
	 *
	 * @return mixed
	 *
	 * @since 5.1.0
	 */
	public function getShippingMethods($userId = null) {
		$db    = JFactory::getDBO();
		
		$whereUser = "";
		if (!empty($userId)) {
			$whereUser  = " and user_id = $userId";
		}
		
		$query = "SELECT * FROM #__jbusinessdirectory_shipping_methods WHERE published = 1 $whereUser";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Retrieves all shipping methods belonging to an offer
	 *
	 * @param null $offerId int ID of the offer
	 *
	 * @return array
	 *
	 * @since 5.1.0
	 */
	public function getOfferShippingMethods($offerId = null) {
		$offerId = (int) $offerId;
		if (empty($offerId)) {
			return array();
		}

		$db    = JFactory::getDBO();
		$query = "select sp.*, sp.price as base_price, osp.price as price
				  from #__jbusinessdirectory_shipping_methods as sp
				  left join #__jbusinessdirectory_offer_shipping_methods as osp on osp.shipping_method_id = sp.id
			      where osp.offer_id = $offerId and sp.published = 1
			      order by osp.id asc";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getShippingMethodsByCompany($companyId = null) {
		$companyId = (int) $companyId;
		if (empty($companyId)) {
			return array();
		}

		$db    = JFactory::getDBO();
		$query = "select sp.*, sp.price as base_price, GROUP_CONCAT(osp.price) as offerPrices, GROUP_CONCAT(osp.offer_id) as offerIds
				  from #__jbusinessdirectory_shipping_methods as sp
				  left join #__jbusinessdirectory_companies as cp on cp.userId = sp.user_id
				  left join #__jbusinessdirectory_company_offers as co on co.companyId = cp.id
				  left join #__jbusinessdirectory_offer_shipping_methods as osp on osp.shipping_method_id = sp.id and osp.offer_id = co.id
				  where cp.id = $companyId and sp.published = 1
				  group by sp.id
				  order by osp.id asc";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getDefaultShippingMethods() {
		$db    = JFactory::getDBO();
		$query = "select sp.*, sp.price as base_price, GROUP_CONCAT(osp.price) as offerPrices, GROUP_CONCAT(osp.offer_id) as offerIds
				  from #__jbusinessdirectory_shipping_methods as sp
				  left join #__jbusinessdirectory_offer_shipping_methods as osp on osp.shipping_method_id = sp.id
				  where sp.default = 1 and sp.published = 1
				  group by sp.id
				  order by osp.id asc";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Deletes offer-shipping method relations based on offer ID
	 *
	 * @param $offerId int ID of the offer
	 *
	 * @return bool
	 *
	 * @since 5.1.0
	 */
	public function deleteOfferShippingMethods($offerId) {
		$offerId = (int) $offerId;

		$db    = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_offer_shipping_methods WHERE offer_id = $offerId";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getTotalShippingMethodsByUser($userId) {
		$db     = JFactory::getDbo();
		$userId = (int) $userId;

		$query = "select sm.*
				  from #__jbusinessdirectory_shipping_methods as sm
				  where sm.user_id = $userId";

		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getShippingMethod($id){
		$db     = JFactory::getDbo();

		if (empty($id)) {
			return null;
		}

		$query = "select *
				  from #__jbusinessdirectory_shipping_methods
				  where id = $id";

		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
}
