<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');

require_once BD_CLASSES_PATH.'/traits/OfferSellingSummary.php';

class OfferSellingService {

	use OfferSellingSummary;

	/**
	 * Method to initialize or retrieve the cartData in the session. If cartData is initialized, it will
	 * return the data itself, and if not, it will initialize the data in the session and return an empty array.
	 *
	 * @return array|null
	 * @since 5.0.0
	 */
	public static function initializeCartData() {
		$cartData = isset($_SESSION['cartData']) ? $_SESSION['cartData'] : null;
		if (!isset($cartData)) {
			$cartData             = array();
			$_SESSION['cartData'] = $cartData;
		}

		return $cartData;
	}

	/**
	 * Get's the offer id and the quantity for the offer that is being added to the cart, retrieves all the data
	 * and creates a new object containing all the offer fields needed to be rendered on the cart view.
	 *
	 * After creating the object, it checks if the current offer is already on the session. If not, it creates the offer
	 * in the session.
	 * If the offer is already present, it updates the quantity of that offer by adding to it the new quantity.
	 *
	 * @param $data  array contains the cart data
	 *
	 * @return array|null
	 * @since 5.0.0
	 */
	public static function addToCart($data) {

		$offerId = $data['offerId'];
		$quantity = $data['selectedData']['quantity'];
		unset($data['selectedData']['quantity']);
		$offerTable = JTable::getInstance('Offer', 'JTable', array());
		$offer      = $offerTable->getOffer($offerId);
		$attrTable = JTable::getInstance('OfferAttributes', 'JTable', array());
		$attributeParent = null;
		$attributeChild = null;

		$optionCombinedId = implode('_',$data['selectedData']);
		$productOrderVal = array();

		foreach ($data['selectedData'] as $key => $value) {
			if ($key == 'quantity') {
				continue;
			}
			$explodeValues = explode('_', $value);
			$attribute = $attrTable->getAttributeById($offerId, $explodeValues[0], $explodeValues[1])[0];
			$data['attributeDetails'][] = $attribute;
			$productOrderVal[] = $attribute->name.' => '.$attribute->options;
		}

		$searchDetails['offerId'] = $offerId;
		$table = JTable::getInstance("OfferStockConfig");
		$relatedStocks =  $table->getRelatedOfferStock($data['offerId'],$data['selectedData']);
		$offer->quantity = !empty($relatedStocks)?$relatedStocks[0]->qty:$offer->quantity;

		$cartData   = self::initializeCartData();

		if (!isset($cartData['items'][$offerId.'_'.$optionCombinedId])) {
			$item                 = new stdClass();
			$item->name           = $offer->subject;
			$item->description    = $offer->short_description;
			$item->price          = $offer->price;
			$item->quantity       = $quantity;
			$item->total_quantity = $offer->quantity;
			$item->selected_details = $data['selectedData'];
			$item->combinedIds = $optionCombinedId;
			$item->attributeDetails = isset($data['attributeDetails'])?$data['attributeDetails']:array();

			$item->productOrderIds = implode(',',$data['selectedData']);
			$item->combination_values = implode('##',$productOrderVal);

			$item->enable_offer_selling      = $offer->enable_offer_selling;

			if (!empty((float)$offer->specialPrice)) {
				$item->price = $offer->specialPrice;
			}


			if($offer->use_stock_price && $offer->enable_offer_selling != OFFER_SELLING_COUPON && is_array($relatedStocks) && !empty($relatedStocks[0])){
				$item->price = $relatedStocks[0]->price;
			}

			$item->id        = $offer->id;
			$item->companyId = $offer->companyId;
			$item->companyName = $offer->company_name;
			$item->link      = JBusinessUtil::getOfferLink($offer->id, $offer->alias);

			$sameCurrency = true;
			if(!empty($cartData['items'])){
				foreach ($cartData['items'] as $cartItem) {
					if ($cartItem->currencyId != $offer->currencyId) {
						$sameCurrency = false;
						break;
					}
				}
			}

			if($sameCurrency){
				$cartData["currencyId"] = $offer->currencyId;
			}else{
				$cartData = self::updateCartPrices($cartData);
				$cartData["currencyId"] = JBusinessUtil::getApplicationSettings()->currency_id;
			}

			// convert currency only if items on cart are of different currencies
			$item->currencyId = $offer->currencyId;
			if (!$sameCurrency) {
				$item->price      = JBusinessUtil::convertCurrency($item->price, $offer->currencyId);
				$item->currencyId = JBusinessUtil::getApplicationSettings()->currency_id;
			}

			$offerPictures = $offerTable->getOfferPictures($offerId);
			$item->picture = '/no_image.jpg';
			if (!empty($offerPictures)) {
				$item->picture = $offerPictures[0]->picture_path;
			}

			$item->min_purchase = $offer->min_purchase;
			$item->max_purchase = $offer->max_purchase;

			$checkItems = array();
			if(isset($cartData['items'])){
				$checkItems = $cartData['items'];
			}
			$checkItems[$offerId.'_'.$optionCombinedId] = $item;
			
			// check if the cart has multiple payment methods. 
			// multiple payment methods are not allowed for the moment.
			if(!self::hasMultiplePaymentMethods($checkItems)){
				//dump("add to cart");
				$cartData['items'][$offerId.'_'.$optionCombinedId] = $item;
			}else{
				return false;
			}
		} else {
			
			if($offer->enable_offer_selling == OFFER_SELLING_COUPON){
				$quantity = 0;
			}
			
			$newQuantity = $cartData['items'][$offerId.'_'.$optionCombinedId]->quantity + $quantity;

			// do not allow the user to add more items to the cart than the actual limit
			$maxAllowedQuantity = $offer->max_purchase < $offer->quantity ? $offer->max_purchase : $offer->quantity;
			if ($newQuantity <= $maxAllowedQuantity) {
				$cartData['items'][$offerId.'_'.$optionCombinedId]->quantity = $newQuantity;
			}
			// if exceeded, the item quantity will be set equal to the limit, and the user cannot
			// add more than that
			else {
				$cartData['items'][$offerId.'_'.$optionCombinedId]->quantity = $maxAllowedQuantity;
			}
		}

		$appSettings = JBusinessUtil::getApplicationSettings();

		$cartData = self::saveToSession($cartData);
		if ($appSettings->enable_shipping) {
			$cartData = self::calculateShippingCosts($cartData);
			$cartData = self::saveToSession($cartData);
		}
		return true;
	}

	/**
	 * Update all prices to the same currency
	 *
	 * @return void
	 */
	public static function updateCartPrices($cartData){
		if(!empty($cartData['items'])){
			foreach ($cartData['items'] as &$cartItem) {
				$cartItem->price      = JBusinessUtil::convertCurrency($cartItem->price, $cartItem->currencyId);
				$cartItem->currencyId = JBusinessUtil::getApplicationSettings()->currency_id;
			}
		}

		return $cartData;
	}

	/**
	 * Method that removes the offer from the cartSession based on the offerId it receives.
	 *
	 * @param $offerId int Id of the offer that will be removed
	 *
	 * @return array|null
	 * @since 5.0.0
	 */
	public static function removeFromCart($offerId) {
		$cartData = self::initializeCartData();
		
		if(!empty($cartData['items'])){
			foreach($cartData['items'] as $key=>$value){
				if(strpos($key,$offerId)=== 0){
					unset($cartData['items'][$key]);
				}
			}
		}

		$appSettings = JBusinessUtil::getApplicationSettings();
		if (!empty($cartData['items']) && $appSettings->enable_shipping) {
			$cartData = self::calculateShippingCosts($cartData);
		} else {
			if (isset($cartData['shippingCosts'])) {
				unset($cartData['shippingCosts']);
			}
		}
		$cartData = self::saveToSession($cartData);

		return $cartData;
	}

	/**
	 * Method that updates the quantity of an offer that is already in the session. Retrieves the offerId and quantity,
	 * and updates the quantity for that particular offer
	 *
	 * @param $offerId  int ID of the offer
	 * @param $quantity int New quantity value that will be updated in the session
	 *
	 * @return array|null
	 * @since 5.0.0
	 */
	public static function editCartItem($offerId, $quantity) {
		$cartData = self::initializeCartData();
		$cartData['items'][$offerId]->quantity = $quantity;

		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_shipping) {
			$cartData = self::calculateShippingCosts($cartData);
		}
		$cartData = self::saveToSession($cartData);

		return $cartData;
	}

	/**
	 * Method to reset the session data for the cart items. Used for the empty cart functionality.
	 *
	 * @since 5.0.0
	 */
	public static function resetSession() {
		unset($_SESSION['cartData']);
		unset($_SESSION['offerUserData']);
	}

	/**
	 * Method that retrieves the cart data from the session.
	 *
	 * @return null
	 * @since 5.0.0
	 */
	public static function getCartData() {
		$cartData = isset($_SESSION['cartData']) ? $_SESSION['cartData'] : null;

		return $cartData;
	}

	/**
	 * Sets the selected shipping method for an offer group
	 *
	 * @param $companyId int ID of the company
	 * @param $methodId  int ID of the shipping method
	 *
	 * @since 5.1.0
	 */
	public static function setGroupShippingMethod($companyId, $methodId) {
		$cartData = self::initializeCartData();
		if (!isset($cartData['shippingMethods'])) {
			$cartData['shippingMethods'] = array();
		}
		$cartData['shippingMethods'][$companyId] = $methodId;
		$cartData                                = self::calculateShippingCosts($cartData);

		self::saveToSession($cartData);
	}

	public static function calculateShippingCosts($cartData = null) {
		if (empty($cartData)) {
			$cartData = self::initializeCartData();
		}

		if (isset($cartData['shippingMethods']) && !empty($cartData['shippingMethods'])) {
			$cartData['shippingCosts'] = array();

			$items = self::getItemsGroupedBySeller($cartData);

			if (empty($items)) {
				return $cartData;
			}

			$shippingMethods = self::getShippingMethodsGroupedBySeller($items);

			if(empty($shippingMethods)){
				return ;
			}
			$checkedItems = array();
			$totalShipping = 0;
			foreach ($cartData['shippingMethods'] as $companyId => $methodId) {
				foreach ($shippingMethods[$companyId] as $method) {
					if ($methodId == $method->id) {
						$tmp             = array();
						$tmp['totPrice'] = $method->base_price;
						foreach ($items as $offer1) {
							$offer = $offer1[0];
							if (!isset($offer->uniqueId)){
								$offer->uniqueId = $offer->id.'_'.implode('_',$offer->selected_details);
							}
							if (in_array($offer->companyId.'_'.$offer->uniqueId, $checkedItems) || $companyId != $offer->companyId) {
								continue;
							}
							$checkedItems[] = $offer->uniqueId;
							// skip array keys that are not offers
							if (is_object($offer)) {
								$extraShipping = 0;
								// if offers are associated with the shipping method and have extra cost
								if (!empty($method->offerIds)) {
									// calculate the extra cost for each associated offer
									foreach ($method->offer_ids as $key => $offerId) {
										// skip cart offers that have no extra cost
										if ($offerId == $offer->id) {
											// if more than one item per offer, multiply cost
											$extraShipping = $method->offer_prices[$offer->id] * $offer->quantity;

											$val                     = new stdClass();
											$val->offerId            = $offerId;
											$val->offerName          = $offer->name;
											$val->shippingMethodId   = $method->id;
											$val->shippingMethod     = $method->name;
											$val->shippingBasePrice  = $method->base_price;
											$val->offerShippingPrice = $method->offer_prices[$offer->id] * $offer->quantity;
											$tmp[$offerId]           = $val;

											break;
										}
									}
								} else {
									$val                     = new stdClass();
									$val->offerId            = $offer->id;
									$val->offerName          = $offer->name;
									$val->shippingMethodId   = $method->id;
									$val->shippingMethod     = $method->name;
									$val->shippingBasePrice  = $method->base_price;
									$val->offerShippingPrice = $method->base_price;
									$tmp[$offer->id]         = $val;
								}

								$tmp['totPrice']         += $extraShipping;
								$tmp['shippingMethodId'] = $method->id;
								
								// if total shipping price exceeds shipping method threshold, then shipping is free
								if ($cartData['totPrice'] >= $method->threshold && !empty($method->threshold)) {
									$tmp['totPrice'] = 0;
								}

								$cartData['shippingCosts'][$companyId]['totPrice']         = $tmp['totPrice'];
								$cartData['shippingCosts'][$companyId]['shippingMethodId'] = $tmp['shippingMethodId'];
								$cartData['shippingCosts'][$companyId][$offer->uniqueId] = $extraShipping;
								$cartData['shippingCosts'][$companyId]['basePrice'] = $method->base_price;
							}
						}
					}
				}
			}

			foreach ($cartData['shippingCosts'] as $companyId => $data) {
				$totalShipping += $data['totPrice'];
			}
			$cartData['shippingCosts']['totPrice'] = $totalShipping;
		}

		return $cartData;
	}

	/**
	 * Method that retrieves the cartData after changes have been performed onto them, calculates global properties for
	 * all the items (and for each of them) like totalPrice, and saves them in the session
	 *
	 * @param $cartData array of objects that holds all cart data
	 *
	 * @return mixed
	 * @since 5.0.0
	 */
	private static function saveToSession($cartData) {
		$totalPrice = 0;
		$currencyId = 0;
		foreach ($cartData['items'] as $item) {
			$totPrice = $item->price * $item->quantity;

			$item->subtotal   = $totPrice;
			$item->totalPrice = JBusinessUtil::getPriceFormat($totPrice, $item->currencyId);
			$item->totPrice   = $totPrice;

			$totalPrice += $totPrice;
			$currencyId = $item->currencyId;
		}

		$cartData["totalPrice"] = JBusinessUtil::getPriceFormat($totalPrice, $currencyId);
		$cartData["totPrice"]   = $totalPrice;
		$_SESSION['cartData']   = $cartData;

		return $cartData;
	}

	/**
	 * Check if the products are comming from multiple providers with multiple payment methods 
	 * 
	 * @param $purchasedItems
	 */
	public static function hasMultiplePaymentMethods($purchasedItems){
		
		if(empty($purchasedItems)){
			return false;
		}
		
		$companiesIds = array();
		foreach ($purchasedItems as $purchasedItem){
			$companiesIds[] = $purchasedItem->companyId;
		}
		
		$companiesIds = array_filter($companiesIds);
		
		$hasCustom = false;
		$hasDefault = false;
		//check the payment methods for the currect companies
		$pcp = array();
		
		foreach($companiesIds as $companyId){
			$paymentMethods = PaymentService::getPaymentProcessors(true, JBD_APP_SELL_OFFERS, $companyId);
			
			if(empty($paymentMethods) || $paymentMethods[0]->company_id == -1){
				$hasDefault = true;
			}else{
				$pcp[] = $paymentMethods[0]->company_id;
				$hasCustom = true;
			}
			
			$pcp = array_unique($pcp);
			
			if( $paymentMethods[0]->company_id != -1 && in_array($paymentMethods[0]->company_id, $pcp) && count($pcp)>1){
				 return true;
			}
			
			if($hasDefault && $hasCustom){
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Creates one or multiple orders based on the items on the cart
	 *
	 * @param $data
	 *
	 * @return bool|string
	 *
	 * @since 5.0.0
	 * @throws Exception
	 */
	public static function createOrders($data) {
		$cartData = self::getCartData();
		$items    = self::getItemsGroupedBySeller();

		$offerOrdersTable = JTable::getInstance('OfferOrders', 'JTable');

		$orders = array();
		if($data->buyerDetails->orderId){
			$orders = $offerOrdersTable->getSellersByOrderToken($data->buyerDetails->orderId);
		}

		$orderIds = array();
		// create only one order per company/seller
		foreach ($items as $companyId => $item) {
			$companyId = explode('_', $companyId)[0];

			if(!empty($orders)){
				foreach($orders as $order){
					if($order->id == $companyId){
						$data->orderId = $order->order_id;
					}
				}
			}

			$details                = $data;
			$details->reservedItems = $item;
			if(!empty($cartData['shippingCosts'])){
				$details->shipping      =  $cartData['shippingCosts'][$companyId]['basePrice']/(count($cartData['shippingCosts'][$companyId])-3) + $cartData['shippingCosts'][$companyId][$item[0]->uniqueId];
				$details->shipping_method = $cartData['shippingCosts'][$companyId]['shippingMethodId'];
			}
			$details->currency_id = $cartData['currencyId'];

			$orderIds[] = self::saveOrder($details);
		}

		// generate unique token for all orders based on their ids
		$token      = implode(',', $orderIds);
		$orderToken = md5($token);

		//update all orders with the generated token
		foreach ($orderIds as $orderId) {
			$offerOrdersTable->id    = $orderId;
			$offerOrdersTable->token = $orderToken;

			if (!$offerOrdersTable->store()) {
				$application = JFactory::getApplication();
				$application->enqueueMessage($offerOrdersTable->getError(), 'error');
				return false;
			}
		}

		$cartData["token"]    = $orderToken;
		$cartData["orderIds"] = $orderIds;

		self::saveToSession($cartData);

		return $orderToken;
	}

	/**
	 * Method that saves all the order details and information in the database
	 *
	 * @param $orderDetails object containing all order details
	 *
	 * @return bool|int false if there's an error saving the data, otherwise it returns the id of the order that was saved
	 * @throws Exception
	 * @since 5.0.0
	 */
	public static function saveOrder($orderDetails) {
		$offerOrdersTable = JTable::getInstance('OfferOrders', 'JTable');

		// Create an order record on the table
		$offerOrdersTable->id          = 0;

		if (!empty($orderDetails->orderId)) {
			$offerOrdersTable->id = (int) $orderDetails->orderId;
		}

		$offerOrdersTable->first_name  = $orderDetails->buyerDetails->first_name;
		$offerOrdersTable->last_name   = $orderDetails->buyerDetails->last_name;
		$offerOrdersTable->address     = $orderDetails->buyerDetails->address;
		$offerOrdersTable->city        = $orderDetails->buyerDetails->city;
		$offerOrdersTable->region      = $orderDetails->buyerDetails->county;
		$offerOrdersTable->country_id  = $orderDetails->buyerDetails->country;
		$offerOrdersTable->postal_code = $orderDetails->buyerDetails->postalCode;
		$offerOrdersTable->phone       = $orderDetails->buyerDetails->phone;
		$offerOrdersTable->email       = $orderDetails->buyerDetails->email;
		$offerOrdersTable->shipping    = $orderDetails->shipping;
		$offerOrdersTable->currency_id = $orderDetails->currency_id;
        $offerOrdersTable->shipping_method = $orderDetails->shipping_method;
		$offerOrdersTable->user_id     = isset($orderDetails->buyerDetails->user_id)?$orderDetails->buyerDetails->user_id:0;// JBusinessUtil::getUser()->ID;
		$offerOrdersTable->initial_amount = floatval($orderDetails->reservedItems["totalPrice"]);

		//applying taxes on order
		$taxObject = TaxService::calculateTaxes($offerOrdersTable->initial_amount, JBD_APP_SELL_OFFERS, $orderDetails->buyerDetails->country->id);
		if (empty($taxObject->taxes)) {
			$taxObject = TaxService::calculateTaxes($offerOrdersTable->initial_amount, JBD_APP_SELL_OFFERS);
		}

		$vatObject = TaxService::getVat($offerOrdersTable->initial_amount, $orderDetails->buyerDetails->country->id);
		$offerOrdersTable->vat_amount = $vatObject->vat_amount;
		$offerOrdersTable->vat = $vatObject->vat;

		$totalPrice = floatval((float)$offerOrdersTable->initial_amount + (float)$taxObject->tax_amount + (float)$vatObject->vat_amount);

		$totalPrice = $totalPrice + $orderDetails->shipping;
		$offerOrdersTable->amount         = $totalPrice;
		$offerOrdersTable->status         = OFFER_ORDER_CREATED;

		if (!$offerOrdersTable->store()) {
			$application = JFactory::getApplication();
			$application->enqueueMessage($offerOrdersTable->getError(), 'error');
			return false;
		}
		$orderId = $offerOrdersTable->id;

		$orderTable = JTable::getInstance('Order', 'JTable');
		$orderTable->deleteOrderTaxes($orderId, JBD_APP_SELL_OFFERS);
		$orderTable->createOrderTax($orderId, JBD_APP_SELL_OFFERS, $taxObject->taxes);

		// Save all the purchased offers data
		$orderProductsTable = JTable::getInstance('OfferOrderProducts', 'JTable');

		if(empty($orderDetails->orderId)){
			// iterate for each order product
			foreach ($orderDetails->reservedItems as $key => $item) {
				// skip 'totalPrice' array index, iterate only over the items
				if (is_numeric($key)) {
					$orderProductsTable->offer_id   = $item->id;
					$orderProductsTable->order_id   = $orderId;
					$orderProductsTable->quantity   = $item->quantity;
					$orderProductsTable->price      = $item->price;
					$orderProductsTable->currencyId = $item->currencyId;
					$orderProductsTable->combination_ids = $item->productOrderIds;
					$orderProductsTable->combination_values = $item->combination_values;

					if (!$orderProductsTable->store()) {
						$application = JFactory::getApplication();
						$application->enqueueMessage($orderProductsTable->getError(), 'error');
						return false;
					}
				}
			}
		}

		return $orderId;
	}

	/**
	 * Method that groups all cart items by their company, organizes them in an array and returns it.
	 *
	 * @param null $cartData array
	 *
	 * @return array arranged array of cart items
	 * @since 5.0.0
	 */
	public static function getItemsGroupedBySeller($cartData = null) {
		$arrangedItems = array();
		if (empty($cartData)) {
			$data = self::getCartData();
		} else {
			$data = $cartData;
		}
		$items = $data['items'];

		foreach ($items as $item) {
			$arrangedItems[$item->companyId.'_'.$item->combinedIds][] = $item;
			if (!isset($arrangedItems[$item->companyId.'_'.$item->combinedIds]['totalPrice'])) {
				$arrangedItems[$item->companyId.'_'.$item->combinedIds]['totalPrice'] = 0;
			}
			$arrangedItems[$item->companyId.'_'.$item->combinedIds]['totalPrice'] += (float) $item->totPrice;
		}

		return $arrangedItems;
	}


	/**
	 * Get's the data from the session by calling the method in the OfferSellingService, and returns them to be renderd on the
	 * cart view.
	 *
	 * @return null
	 *
	 * @since 5.0.0
	 */
	public static function getCartDetails() {
		$result = new stdClass;
		$result->base_amount = 0;
		$result->total_amount = 0;

		$listingItems = array();
		$cartData = OfferSellingService::getCartData();

		$appSettings = JBusinessUtil::getApplicationSettings();
        if ($appSettings->enable_multilingual) {
            JBusinessDirectoryTranslations::updateCartTranslation($cartData["items"], OFFER_DESCRIPTION_TRANSLATION);
        }

		if (empty($cartData['items'])) {
			return array();
		}

		foreach ($cartData['items'] as $offerId => $item) {
			$item->uniqueId = $offerId;

			if (!isset($listingItems[$item->companyId])) {
				$listingItems[$item->companyId] = array();
				$listingItems[$item->companyId]['items'] = array();
				$listingItems[$item->companyId]['total_price'] = 0.0;
			}

			if(!empty($item->combination_values)){
				$attrValues = explode("##",$item->combination_values);
				$item->attValues = array();
				foreach($attrValues as $val){
					$val = explode("=>",$val);
					$item->attValues[$val[0]]= $val[1];
				}
			}

			$listingItems[$item->companyId]['items'][$offerId] = $item;

			$listingItems[$item->companyId]['total_price'] += $item->totPrice;

			$result->base_amount += $item->totPrice;
		}

		$result->listingItems = $listingItems;


		$taxObject= TaxService::calculateTaxes($result->base_amount, JBD_APP_SELL_OFFERS);
		$result->taxes = $taxObject->taxes;

		//dump($taxObject);

		$vatObject = TaxService::getVat($result->base_amount);
		$result->vat_amount = $vatObject->vat_amount;
		$result->vat = $vatObject->vat;
		$result->shipping = 0;
		if(!empty($cartData["shippingCosts"])){
			$result->shipping = $cartData["shippingCosts"]["totPrice"];
		}
		
		$result->total_amount = $result->base_amount + $result->shipping + $vatObject->vat_amount + $taxObject->tax_amount;


		$result->currency_id = $cartData["currencyId"];
		
		return $result;
	}


	public static function getShippingMethodsGroupedBySeller($items = null) {
		$table                  = JTable::getInstance('ShippingMethod', 'JTable');
		$groupedShippingMethods = array();

		if (empty($items)) {
			$items = self::getItemsGroupedBySeller();
		}

		foreach ($items as $companyId => $item) {
			$newCompanyId = explode('_',$companyId)[0];
			$shippingMethods = $table->getShippingMethodsByCompany($newCompanyId);

			if (empty($shippingMethods)) {
				$shippingMethods = $table->getDefaultShippingMethods();
			}

			foreach ($shippingMethods as $method) {
				$method->offer_ids    = explode(',', $method->offerIds);
				$offerPrices = explode(',', $method->offerPrices);
				$priceArray = array();
				foreach ($offerPrices as $key => $value){
					$priceArray[$method->offer_ids[$key]] = $value;
				}
				$method->offer_prices = $priceArray;
			}

			$groupedShippingMethods[$newCompanyId] = $shippingMethods;
		}

		return $groupedShippingMethods;
	}


	/**
	 * Checks if given offer can be added to cart and returns the add to cart button HTML.
	 *
	 * @param        $offer object
	 * @param string $class string custom CSS class that can be added to the button
	 *
	 * @return string
	 *
	 * @since 5.5.0
	 */
	public static function getAddToCartBtn($offer, $class = "") {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$btn = "";
		if ($appSettings->enable_offer_selling && $offer->enable_offer_selling == OFFER_SELLING_REGULAR && JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate) && $offer->quantity > 0
			&& (isset($offer->featuresS) && in_array(SELL_OFFERS, explode(',', $offer->featuresS)) || !$appSettings->enable_packages)) {
			$btn = "
			<div class='cart-btn-list $class' onclick='jbdOffers.addToCart($offer->id, 1)'>
	          <a id='addToCartButton' href='javascript:void(0);'><i class='la la-shopping-cart'></i></a>
	        </div>
		    ";
		}

		return $btn;
	}

	/**
	 * Returns the HTML for the cart success and error modals.
	 *
	 * @return string
	 *
	 * @since 5.5.0
	 */
	public static function getCartModal() {

		JText::script('LNG_ADDING_PRODUCT_TO_SHOPPING_CART');

		// success modal
		$html = '
		<div id="cart-dialog" class="jbd-container" style="display:none">
			<div class="jmodal-sm">
		        <div class="jmodal-header">
		            <p class="jmodal-header-title"><i class="la la-check-circle" style="color:green; font-size: 28px;"></i>'.JText::_('LNG_ITEM_ADDED_TO_CART_TITLE').'</p>
		            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
		        </div>
		        <div class="jmodal-body">
		            <div>
		                <p>'.JText::_('LNG_ITEM_ADDED_TO_CART').'</p>
			            <a class="btn btn-primary" href="'.JRoute::_("index.php?option=com_jbusinessdirectory&view=cart").'">
							'.JText::_('LNG_VIEW_SHOPPING_CART').'
						</a>
					</div>
		        </div>
		        <div class="jmodal-footer">
		            <div class="btn-group" role="group" aria-label="">
		                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()">'.JText::_("LNG_CLOSE").'</button>
		            </div>
		        </div>
		    </div>
		</div>
		';

		// error modal
		$html .= '<div id="cart-error" class="jbd-container" style="display:none">
					<div class="jmodal-sm">
				        <div class="jmodal-header">
				            <p class="jmodal-header-title"><i class="la la-times-circle" style="color:red; font-size: 28px;"></i> <?php echo JText::_(\'LNG_CREATE_NEW_ORDER\'); ?></p>
				            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
				        </div>
				        <div class="jmodal-body">
				            <p>
				                '.JText::_('LNG_ITEM_PRESENT_ON_CART').'
				            </p>
				        </div>
				        <div class="jmodal-footer">
				            <div class="btn-group" role="group" aria-label="">
				                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CLOSE")?></button>
				                <button type="button" class="jmodal-btn jbd-commit" onclick="jbdOffers.resetCartAndAdd(<?php echo $this->offer->id; ?>)"><?php echo JText::_("LNG_NEW_ORDER")?></button>
				            </div>
				        </div>
				    </div>
				</div>';


		return $html;
	}

	/**
	 * Create user data for storing order details
	 *
	 * @param $data
	 * @param $userData
	 * @return stdClass
	 */
	public static function createUserData() {
		$userData = new stdClass();
		$userData->first_name =  '';
		$userData->last_name =  '';
		$userData->address =  '';
		$userData->city =  '';
		$userData->state_name = '';
		$userData->country =  '';
		$userData->postal_code =  '';
		$userData->phone =  '';
		$userData->email =  '';
		$userData->conf_email =  '';

		return $userData;
	}

	/**
	 * Method to initialize Offer User Data
	 *
	 * @param bool|false $resetUserData
	 * @return null|stdClass
	 */
	public static function initializeUserData($resetUserData = false) {
		$get = JFactory::getApplication()->input->get->getArray();
		$data = JFactory::getApplication()->input->post->getArray();
		if (count($data) == 0) {
			$data = $get;
		}

		$userData = isset($_SESSION['offerUserData']) ? $_SESSION['offerUserData'] : null;
		if (!isset($userData) || $resetUserData) {
			$userData = self::createUserData();
			$_SESSION['offerUserData'] = $userData;
		}

		if (!isset($userData->buyerDetails)) {
			$guestDtls = new stdClass();
			$guestDtls->first_name = "";
			$guestDtls->last_name = "";
			$guestDtls->address = "";
			$guestDtls->city = "";
			$guestDtls->county = "";
			$guestDtls->country_name = "";
			$guestDtls->postalCode = "";
			$guestDtls->phone = "";
			$guestDtls->email = "";
			$userData->buyerDetails = $guestDtls;
		}

		$_SESSION['offerUserData'] = $userData;
		return $userData;
	}

	/**
	 * Method to get user data object created from session data
	 *
	 * @return mixed|null|stdClass
	 */
	public static function getUserData() {
		
		$userData = isset($_SESSION['offerUserData']) ? $_SESSION['offerUserData'] : null;
		if (!isset($userData)) {
			$userData = self::initializeUserData();
			$_SESSION['offerUserData'] = $userData;
		}

		return $userData;
	}

	/**
	 * Adds the buyer details and saves them in the session
	 *
	 * @param $buyerDetails array containing the buyer details
	 */
	public static function addBuyerDetails($buyerDetails) {
		$userData = self::getUserData();
		$buyerDtls = new stdClass();
		$buyerDtls->first_name = ucfirst($buyerDetails["first_name"]);
		$buyerDtls->last_name = ucfirst($buyerDetails["last_name"]);
		$buyerDtls->address = ucfirst($buyerDetails["address"]);
		$buyerDtls->city = $buyerDetails["city"];
		$buyerDtls->county = $buyerDetails["region"];

		if(!is_object($buyerDetails["country"])){
			$buyerDetails["country"] = JBusinessUtil::getCountry($buyerDetails["country"]);
		}

		$buyerDtls->country = $buyerDetails["country"];
		$buyerDtls->country_name = !empty($buyerDetails["country"])?$buyerDetails["country"]->country_name:"";
		$buyerDtls->postalCode = strtoupper($buyerDetails["postal_code"]);
		$buyerDtls->phone = $buyerDetails["phone"];
		$buyerDtls->email = $buyerDetails["email"];
		$buyerDtls->user_id = $buyerDetails["user_id"];

		$orderId = JFactory::getApplication()->input->get("orderId", null);
		$buyerDtls->orderId = $orderId;
		$userData->buyerDetails = $buyerDtls;

		$_SESSION['offerUserData'] = $userData;

	}

	/**
	 * Method to get the current Joomla Session
	 *
	 * @return JSession
	 * @throws Exception
	 */
	private static function getJoomlaSession() {
		$session = JFactory::getSession();
		if ($session->getState() !== 'active') {
			$app = JFactory::getApplication();
			$msg = "Your session has expired";
			$app->redirect('index.php?option=' . JBusinessUtil::getComponentName() . '&view=offers', $msg);
			$app->enqueueMessage("Your session has expired", 'warning');
		} else {
			return $session;
		}
	}
}
