<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableOfferStock extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_offer_stock', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getOfferStocks($offerId, $categoryId = null, $emptyStockQty = true){
		$whereStockCond = '';
		if (!empty($categoryId)) {
			$whereStockCond = " and stock_main_category = $categoryId";
		}

		$whereStockQtyCond = '';
		if ($emptyStockQty == false) {
			$whereStockQtyCond = " and os.qty > '0' ";
		}

		$query = "select os.*,GROUP_CONCAT(DISTINCT osc.attribute_id,'_',osc.attribute_value SEPARATOR '##') as attributes
			from #__jbusinessdirectory_offer_stock as os
			left join #__jbusinessdirectory_offer_stock_config as osc on osc.stock_id = os.id
			where os.offer_id=$offerId $whereStockCond $whereStockQtyCond
			group by os.id
		";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getOfferQuantity($offerId, $categoryId = null){
		$whereStockCond = '';
		if (!empty($categoryId)) {
			$whereStockCond = " and stock_main_category = $categoryId";
		}
		$query = "select sum(os.qty) as quantity
					from #__jbusinessdirectory_offer_stock as os
					where os.offer_id=$offerId $whereStockCond
				";
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}


	public function getOfferStocksConfig($offerId, $stockId, $categoryId = null){
		if (empty($categoryId)) {
			$categoryId = '-1';
		}
		if (is_array($stockId)) {
			$whereStockCond = " and osc.stock_id in (".implode(',', $stockId).")";
		} else {
			$whereStockCond = " and osc.stock_id = $stockId";
		}
		$query = "select a.*,
			    GROUP_CONCAT(DISTINCT ao.name ORDER BY ao.ordering asc SEPARATOR '|#')  options,
			    GROUP_CONCAT(DISTINCT ao.id ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIDS,
			    GROUP_CONCAT(ao.icon ORDER BY ao.ordering asc SEPARATOR '|#')  optionsIcons,
			    osc.attribute_value as attributeValue
				from #__jbusinessdirectory_offer_stock as os
			    left join #__jbusinessdirectory_offer_stock_config as osc on osc.stock_id = os.id
			    left join #__jbusinessdirectory_attributes a on a.id = osc.attribute_id
			    left join #__jbusinessdirectory_attribute_options as ao on ao.attribute_id = a.id
			    right join #__jbusinessdirectory_attribute_category as cag on cag.attributeId = a.id and cag.categoryId in ($categoryId, -1)
			where os.offer_id=$offerId and a.status = 1 and a.attribute_type = ".ATTRIBUTE_TYPE_OFFER." and a.use_attribute_for_selling='1' $whereStockCond
			group by a.id
			order by a.ordering
		";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function deleteStockByOfferId($offerId){
		$db = JFactory::getDBO();
		$query = " delete from #__jbusinessdirectory_offer_stock WHERE offer_id = $offerId";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function updateStock($stockId, $qtySold){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__jbusinessdirectory_offer_stock SET qty = qty-$qtySold WHERE id = ".$stockId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
