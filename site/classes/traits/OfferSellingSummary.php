<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait OfferSellingSummary
{
	
	/**
	 * Create the purchased items summary
	 *
	 * @param      $purchasedItems array containing the items that have been bought
	 *
	 * @param null $shipping       float total shipping cost
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public static function getOrderSummary($cartDetails) {
		$enableShipping = file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/shippingmethod.php') && JBusinessUtil::getApplicationSettings()->enable_shipping;

		// Need to recalculate the total from $cartDetails based on country selection tax
		$totPrice = $cartDetails->initial_amount ?? $cartDetails->base_amount;
		if ($cartDetails->vat_amount > 0) {
			$totPrice += $cartDetails->vat_amount;
		}

		if (!empty($cartDetails->taxes)) {
			foreach ($cartDetails->taxes as $tax) {
				$totPrice += $tax->tax_calc_amount;
			}
		}

		if($enableShipping) { 
			$totPrice += $cartDetails->shipping;
		}

		ob_start();
		?>
			<div class="order-items">
				<?php foreach($cartDetails->listingItems as $companyId => $companyItems) { ?>
					<?php foreach ($companyItems['items'] as $item) { ?>
						<div class="order-item-section">
							<div class="order-item-image">
								<img class="w-100" alt="<?php echo $item->name; ?>" src="<?php echo BD_PICTURES_PATH.$item->picture ?>">
							</div>
							<div class="order-item-cnt">
								<div class="order-service">
									<?php echo $item->name ?>
								</div>
								
								<div class="order-details">
									<?php echo JText::_("LNG_SOLD_BY").': '. $item->companyName ?>
								</div>

								<div class="item-properties">
									<?php if (!empty($item->attValues)) { ?>
										<?php foreach ($item->attValues as $name=>$val) { ?>
											<div class="item-property">
												<div class="item-property-title"><?php echo $name?></div> 
												<div class="item-property-content"><?php echo $val?></div>
											</div>
										<?php } ?>
									<?php } ?>

									<?php if (false  && !empty($item->attributeDetails)) { ?>
										<?php foreach ($item->attributeDetails as $attributeDetail) { ?>
											<div class="item-property">
												<div class="item-property-title"><?php echo $attributeDetail->name?></div> 
												<div class="item-property-content"><?php echo $attributeDetail->options?></div>
											</div>
										<?php } ?>
									<?php } ?>

									<div class="item-property">
										<div class="item-property-title"><?php echo JText::_('LNG_QTY'); ?></div>
										<div class="item-property-content"><?php echo $item->quantity; ?></div>
									</div>
									<div class="item-property">
										<div class="item-property-title"><?php echo JText::_('LNG_PRICE'); ?></div>
										<div class="item-property-content"><?php echo JBusinessUtil::getPriceFormat( $item->price, $item->currencyId); ?></div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>

			
				<div class="order-spacer">
				</div>
	
				<div class="order-section">
					<div class="order-item-title"><?php echo JText::_("LNG_ORDER_INFORMATION") ?> </div>
				</div>

				<div class="order-section">
					<div class="order-item-title"><?php echo JText::_('LNG_PRODUCTS'); ?> </div>
					<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat( $cartDetails->base_amount, $cartDetails->currency_id); ?></div>
				</div>

				<?php if ($cartDetails->vat_amount > 0) { ?>
					<div class="order-section">
						<div class="order-item-title"><?php echo JText::_("LNG_VAT") ?> <span class="text-small">(<?php echo $cartDetails->vat . "%" ?>)</span></div>
						<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($cartDetails->vat_amount, $cartDetails->currency_id) ?></div>
					</div>
				<?php } ?>
	
				<?php
					if (!empty($cartDetails->taxes)) {
						foreach ($cartDetails->taxes as $tax) {
				?>
						<div class="order-section">
							<div class="order-item-title"><?php echo $tax->tax_name ?> <span class="text-small"><?php echo ($tax->tax_type == 2) ? "( " . $tax->tax_amount . " %)" : "" ?></span></div>
							<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($tax->tax_calc_amount, $cartDetails->currency_id) ?></div>
						</div>
					<?php } ?>
				<?php } ?>

				<?php if($enableShipping) { ?>
					<div class="order-section">
						<div class="order-item-title">
							<?php echo JText::_('LNG_SHIPPING'); ?>
						</div>
						<div class="order-item-desc">
							<span id="cart-item-subtotal-<?php echo $companyId; ?>"><?php echo JBusinessUtil::getPriceFormat($cartDetails->shipping, $cartDetails->currency_id); ?></span>
						</div>
					</div>
				<?php } ?>

				<div class="order-section">
					<div class="order-item-title">
						<?php echo JText::_('LNG_TOTAL'); ?> 
					</div>
					<div class="order-item-desc">
						<span id="cart-item-subtotal-<?php echo $companyId; ?>"><?php echo JBusinessUtil::getPriceFormat( $totPrice, $cartDetails->currency_id); ?></span>
					</div>
				</div>

			</div>
	
		<?php

		$result = ob_get_contents();
		ob_end_clean();
	
		return $result;
	//}


		/*$result = "";
		$result .= "<div class=\"offer-order-items\">";

		$result .= "<table style='padding:3px;width:100%;'>";
		$result .= "<tr class='title'><td colspan=2><strong>" . JText::_("LNG_ITEMS_DETAILS") . "<strong></td></tr>";
		$total  = 0;
		
		$cartData     = self::getCartData();
		foreach ($purchasedItems as $item) {
			$boughtItemDetails = '';
			$attributeDetails = explode('##',$item->combination_values);
			$attributeDetails = array_values(array_filter($attributeDetails));
			if (!empty($attributeDetails)) {
				foreach ($attributeDetails as $attributeDetail) {
					$boughtItemDetails .= '<div class="item-quantity"><strong>' . explode(' => ', $attributeDetail)[0] . ':</strong> ' . explode(' => ', $attributeDetail)[1] . '</div>';
				}
			}

			$result     .= '<tr>';
			$result     .= '<td colspan="3">
								<div style="display: flex; margin-bottom: 20px;">
									<div class="item-image text-center" style="margin-right: 10px;"><img style="width: 100px; border-radius: 3px;" src="'. BD_PICTURES_PATH. $item->picture.'" class="img-circle"/> </div>
									<div class="item-name text-left">
										<div class="item-title"><strong>'. $item->name .'</strong></div>
										<div class="item-quantity"><strong>'. JText::_("LNG_QUANTITY").': '.$item->quantity .'</strong></div>
										'.$boughtItemDetails .'
										<div class="item-supplier" style="color:#888;font-size: 12px;">'.JText::_("LNG_SOLD_BY").': '. $item->companyName .'</div>';
			if($item->enable_offer_selling == OFFER_SELLING_COUPON && $isPaid){
				$result     .='				<div style="margin-top: 5px"><a href="'.JRoute::_('index.php?option=com_jbusinessdirectory&task=offer.generateCoupon&id='.$item->offer_id.'&orderId='.$orderId).'" class="btn btn-success btn-sm">'.JText::_("LNG_GET_COUPON").'<a></div>';
			}
			$result     .='			<div>
								</div>
							 </td>';
			$result     .= '</tr>';
			$result     .= '<tr>';
			$result     .= '<td colspan=2>';
			$result     .= JText::_("LNG_SUBTOTAL");
			$result     .= '</td>';
			$result     .= "<td width='100px' style='vertical-align:top'>" . JBusinessUtil::getPriceFormat($item->quantity * $item->price, $item->currencyId) . "</td>";
			$result     .= '</tr>';
			$basePrice   = $item->quantity * $item->price;
			$initialAmount = $basePrice;

			//applying taxes on order
			$taxes = null;
			
			if(!empty($orderId) && empty($cartData)){
				$cartData['orderIds'] = array($orderId);
			}

			if (isset($cartData['orderIds'])) {
				$orderTable = JTable::getInstance("Order", "JTable", array());
				if (!isset($item->id)){
					$item->id = $item->offer_id;
				}
				$taxes = $orderTable->getOfferOrderTaxes($cartData['orderIds'], JBD_APP_SELL_OFFERS, $item->id);
			}
			
			if (empty($taxes)) {
				$taxesTable = JTable::getInstance("Taxes", "Table", array());
				$taxes = $taxesTable->getTaxes(JBD_APP_SELL_OFFERS);
			}else{
				foreach ($taxes as $key => $value) {
					if (!isset($item->uniqueId)) {
						$item->uniqueId = $item->id.'_'.implode('_',explode(',',$item->combination_ids));
					}
					if ($item->uniqueId != $value->offerId.'_'.implode('_',explode(',',$value->combination_ids))) {
						unset($taxes[$key]);
					}
				}
			}

			if (!empty($taxes)) {
				foreach ($taxes as &$tax) {
					if ($tax->tax_type==1) {
						$result .=      "<tr>";
						$result .=          "
											 <td>";
						$result .=              "<div>";
						$result .=                  $tax->tax_name;
						$result .=              "</div>";
						$result .=          "</td><td></td>";
						$result .=          "<td nowrap=\"nowrap\">".JBusinessUtil::getPriceFormat($tax->tax_amount, $item->currencyId)."</td>";
						$result .=      "</tr>";
						$basePrice += $tax->tax_amount;
					} else {
						$taxAmount = $tax->tax_amount*$initialAmount/100;
						$result .=      "<tr>";
						$result .=          "
											 <td>";
						$result .=              "<div>";
						$result .=                  $tax->tax_name." ";
						$result .=                  "(".$tax->tax_amount."%)";
						$result .=              "</div>";
						$result .=          "</td><td></td>";
						$result .=          "<td nowrap=\"nowrap\">".JBusinessUtil::getPriceFormat($taxAmount, $item->currencyId)."</td>";
						$result .=      "</tr>";
						$basePrice += $taxAmount;
					}
				}
			}

			$appSettings = JBusinessUtil::getApplicationSettings();
			$currencyId = $appSettings->currency_id;
			
			if (isset($item->currencyId)) {
				$currencyId = $item->currencyId;
			}

			$vat = !empty($item->vat) ? $item->vat : $appSettings->vat;
			$vatAmount = !empty($item->vat_amount) ? $item->vat_amount : TaxService::getVatAmount($initialAmount);
			if (!empty($vat)) {
				$vatText = !empty($vat) ? " ($vat%)" : "";
				
				$result .= "<tr><td><div>" . JText::_("LNG_VAT") . "$vatText</div></td><td></td><td nowrap='nowrap'>" . JBusinessUtil::getPriceFormat($vatAmount, $currencyId) . "</td></tr>";
				$total += $vatAmount;
			}

			$total      += $basePrice;
		}

		$shippingCost = 0;
		if (isset($cartData["shippingCosts"]) || !empty($shipping)) {
			$shippingCost = !empty($shipping) ? $shipping : $cartData['shippingCosts']['totPrice'];

			$result .= "<tr style='height:20px;'></tr>";
			$result .= "<tr><td><b>" . JText::_('LNG_SUBTOTAL') . "</b></td><td></td><td>" . JBusinessUtil::getPriceFormat($total, $currencyId) . "</td></tr>";
			$result .= "<tr></tr>";
			$result .= "<tr><td><b>" . JText::_('LNG_SHIPPING') . "</b></td><td></td><td>" . JBusinessUtil::getPriceFormat($shippingCost, $currencyId) . "</td></tr>";
		}
		$result .= "<tr style='height:10px;'></tr>";
		$result .= "<tr><td><b>" . JText::_('LNG_TOTAL') . "</b></td><td></td><td>" . JBusinessUtil::getPriceFormat(($total + $shippingCost), $currencyId) . "</td></tr>";

		$result .= "</table>";
		$result .= "</div>";

		return $result;*/
	}

	/**
	 * Create the buyer details summary
	 *
	 * @param $buyerDetails object containing the buyer details
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public static function getBuyerDetailsSummary($buyerDetails) {
		ob_start();

	?>
		<div class="billing-details">
			<div class="title"><?php echo JText::_("LNG_BUYER_DETAILS") ?></div>
			<div class="detail-spacer"></div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_FULL_NAME") ?></div>
				<div class="billing-item-desc"><?php echo $buyerDetails->first_name . " " . $buyerDetails->last_name ?></div>
			</div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_ADDRESS") ?></div>
				<div class="billing-item-desc">
					<?php echo JBusinessUtil::getAddressText($buyerDetails) ?>
				</div>
			</div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_EMAIL") ?></div>
				<div class="billing-item-desc">
					<?php echo $buyerDetails->email ?>
				</div>
			</div>
			<div class="billing-item">
				<div class="billing-item-title"><?php echo JText::_("LNG_PHONE") ?></div>
				<div class="billing-item-desc"><?php echo $buyerDetails->phone ?></div>
			</div>
		</div>
		<?php
		
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

}