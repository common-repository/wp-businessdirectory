<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class JTableTaxCountries
 */
class JTableTaxCountries extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_tax_countries', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getTaxCountries($taxId) {
		if (empty($taxId)) {
			$taxId = 0;
		}
		$db    = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_tax_countries where tax_id = $taxId";

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
