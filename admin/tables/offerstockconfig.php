<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableOfferStockConfig extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_offer_stock_config', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function deleteConfigurationsByStockId($id){
		$db = JFactory::getDBO();
		$query = " delete from #__jbusinessdirectory_offer_stock_config WHERE stock_id in ($id)";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getRelatedOfferStock($offerId, $stockCombinations, $emptyStockQty = true){
		$havingStockCond = '';
		if (!empty($stockCombinations)) {
			foreach ($stockCombinations as $combination) {
				if (!empty($combination)) {
					$havingStockCond .= " and find_in_set('$combination',attrCombinations)";
				}
			}
		}

		$whereStockQtyCond = '';
		if ($emptyStockQty == false) {
			$whereStockQtyCond = " and os.qty > '0' ";
		}

		$query = "SELECT os.*, GROUP_CONCAT(DISTINCT osc.attribute_id,'_',osc.attribute_value) as attrCombinations, co.min_purchase as min_sale, co.max_purchase as max_sale, co.notify_offer_quantity, co.use_stock_price
					from #__jbusinessdirectory_offer_stock_config as osc
					left join #__jbusinessdirectory_offer_stock os on osc.stock_id = os.id
					left join #__jbusinessdirectory_company_offers as co on co.id = os.offer_id
					where os.offer_id = $offerId
					group by osc.stock_id $whereStockQtyCond
					having 1 $havingStockCond
		";
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function checkUsageOfAttribute($attrId){
		$query = "SELECT count(osc.attribute_id) as nrUsed
					from #__jbusinessdirectory_offer_stock_config as osc
					where osc.attribute_id = $attrId
		";
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
}
