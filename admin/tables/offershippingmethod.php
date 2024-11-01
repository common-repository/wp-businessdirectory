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
 * Class JTableOfferShippingMethod
 *
 * @since 5.1.0
 */
class JTableOfferShippingMethod extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 *
	 * @since 5.1.0
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_offer_shipping_methods', 'id', $db);
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
}
