<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JTableCurrency extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_currencies', 'currency_id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function getCurrencies() {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_currencies order by currency_name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCurrencyById($currencyId) {
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_currencies where currency_id='$currencyId'";
		$db->setQuery($query);
		return $db->loadObject();
	}
}
