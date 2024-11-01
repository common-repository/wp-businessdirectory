<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableTaxes extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_taxes', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getTaxes($type = JBD_PACKAGES, $countryId = null) {
		$db = JFactory::getDBO();

		$whereCountry = "";
		$leftJoinTaxCountries = "";
		$selectTaxCountries = "";
		if (!empty($countryId)) {
			$whereCountry = " and tc.country_id = $countryId";
			$leftJoinTaxCountries = " left join #__jbusinessdirectory_tax_countries as tc on tc.tax_id = t.id ";
			$selectTaxCountries = ", tc.amount as country_amount, tc.country_id ";
		}

		$query = "select t.* $selectTaxCountries
			      from #__jbusinessdirectory_taxes t
				  left join #__jbusinessdirectory_tax_services as ts on ts.tax_id = t.id
				  $leftJoinTaxCountries
				  where ts.app_id = $type $whereCountry
				  order by ordering, tax_name";

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
