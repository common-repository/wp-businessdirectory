<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JBusinessUtil::loadJQueryUI();

// following translations will be used in js
JText::script('LNG_SELECT_SHIPPING_METHOD_FOR_ALL');

class JBusinessDirectoryViewCart extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		
		$this->cart = OfferSellingService::getCartDetails();
		$this->shippingMethods = $this->get('ShippingMethods');
		$this->shippingCosts = $this->get('ShippingCosts');
		
		//check if all offers are coupon sale
		$disableShipping = true;
        if (!empty($this->cart->listingItems)) {
            foreach ($this->cart->listingItems as $item) {
                foreach ($item["items"] as $itm) {
                    if ($itm->enable_offer_selling == OFFER_SELLING_REGULAR) {
                        $disableShipping = false;
                    }
                }
            }
        }
		
		if($disableShipping){
			$this->appSettings->enable_shipping = false;
		}
		
		parent::display($tpl);
	}
}
