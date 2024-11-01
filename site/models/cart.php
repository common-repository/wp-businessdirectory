<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

/**
 * Class JBusinessDirectoryModelCart
 *
 * @since 5.0.0
 */
class JBusinessDirectoryModelCart extends JModelList {

	/**
	 * constructor method
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->appSettings = JBusinessUtil::getApplicationSettings();
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array   Configuration array for model. Optional.
	 * @return  JTable    A database object
	 *
	 * @since 5.0.0
	 */
	public function getTable($type = 'Offer', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Gets the offerId and quantity and calls the respective method from the OfferSellingService class.
	 *
	 * @param $data array contains the cart info for selected items
	 * @return array|null
	 *
	 * @since 5.0.0
	 */
	public function addToCartAjax($data) {
		OfferSellingService::initializeCartData();
		$result = OfferSellingService::addToCart($data);

		return $result;
	}

	/**
	 * Gets the offerId and quantity and calls the respective method from the OfferSellingService class.
	 *
	 * @param $offerId int Id of the offer
	 * @param $quantity int Quantity of the offer
	 * @return array|null
	 *
	 * @since 5.0.0
	 */
	public function editCartItemAjax($offerId, $quantity) {
		$cartData = OfferSellingService::initializeCartData();
		$result = OfferSellingService::editCartItem($offerId, $quantity);

		return $result;
	}

	/**
	 * Gets the offerId and calls the respective method from the OfferSellingService class.
	 *
	 * @param $offerId int Id of the offer
	 * @return array|null
	 *
	 * @since 5.0.0
	 */
	public function removeCartItemAjax($offerId) {
		$cartData = OfferSellingService::initializeCartData();
		$result = OfferSellingService::removeFromCart($offerId);

		return $result;
	}

	/**
	 * Calculates the shipping price for each offer on the cart belonging to a company group, for the
	 * selected shipping method.
	 *
	 * @param $companyId int ID of the company
	 * @param $methodId int ID of the shipping method
	 *
	 * @since 5.1.0
	 */
	public function selectShippingMethod($companyId, $methodId) {
		$companyId = (int)$companyId;
		$methodId  = (int)$methodId;

		OfferSellingService::setGroupShippingMethod($companyId, $methodId);
	}

	/**
	 * Get shipping methods for each offer group present on the cart
	 *
	 * @since 5.1.0
	 */
	public function getShippingMethods() {
		if (!$this->appSettings->enable_shipping) {
			return array();
		}

		$table = $this->getTable('ShippingMethod');
		$groupedShippingMethods = array();
		$cart = OfferSellingService::getCartDetails();

		if(!empty($cart->listingItems)){
			foreach ($cart->listingItems as $companyId => $item) {

				$shippingMethods = $table->getShippingMethodsByCompany($companyId);
				
				if (empty($shippingMethods)) {
					$shippingMethods = $table->getDefaultShippingMethods();
				}

				foreach ($shippingMethods as $method) {
					$method->offer_ids    = explode(',', $method->offerIds);
					$method->offer_prices = explode(',', $method->offerPrices);
				}

				$groupedShippingMethods[$companyId] = $shippingMethods;
			}
		}

		return $groupedShippingMethods;
	}

	/**
	 * Returns the shipping costs organized by company->offer
	 *
	 * @return array
	 *
	 * @since 5.1.0
	 */
	public function getShippingCosts() {
		$cart = OfferSellingService::getCartDetails();
		$session = OfferSellingService::getCartData();
		$shippingCost = isset($session['shippingCosts']) ? $session['shippingCosts'] : null;

        if (!empty($cart->listingItems)) {
            foreach ($cart->listingItems as $companyId => $item) {
                if (!isset($shippingCost[$companyId])) {
                    $shippingCost[$companyId] = array();
                    $shippingCost[$companyId]["notSelected"] = true;
                    $shippingCost[$companyId]["totPrice"] = 0.0;
                }
            }
        }

		return $shippingCost;
	}
	
	
	/**
	 * Retrieve 
	 * 
	 */
	public function getTaxes(){
		$taxesTable = JTable::getInstance("Taxes", "Table", array());
		$taxes = $taxesTable->getTaxes(JBD_APP_SELL_OFFERS);

		return $taxes;
	}
}
