<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableDiscount extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_discounts', 'id', $db);
	}

	
	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function getDiscount($discountCode, $orderId) {
		$db =JFactory::getDBO();
		
		$query = "SELECT count(*) as discount_used FROM #__jbusinessdirectory_orders  WHERE id<>$orderId and discount_code = '$discountCode'";
		$db->setQuery($query);
		$result = $db->loadObject();
		$discountUsed = $result->discount_used;
		
		$countFilter = "";
		$countFilter = "and uses_per_coupon > $discountUsed";
		
		$query = "SELECT * FROM #__jbusinessdirectory_discounts  WHERE code = '$discountCode' $countFilter and state=1 and DATE(now())>= start_date and DATE(now())<=end_date";
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;
	}
	
	public function increaseUse($discountCode) {
		$db =JFactory::getDBO();
		$query = "update  #__jbusinessdirectory_discounts set coupon_used = coupon_used + 1 where  code = '$discountCode'";
		// 		dump($query);
		$db->setQuery($query);
		return $db->execute();
	}
	
	
	public function changeState($id) {
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_discounts SET state = IF(state, 0, 1) WHERE id = ".$id ;
		$db->setQuery($query);
	
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	
	public function getDiscountsForExport() {
		$db =JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_discounts";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}

	public function getDiscountByCode($discountCode) {
		$db =JFactory::getDbo();
		$discountCode = $db->escape($discountCode);
		$query = "SELECT * FROM #__jbusinessdirectory_discounts  WHERE code = '$discountCode'";
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}
}
