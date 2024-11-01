<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 

defined('_JEXEC') or die('Restricted access');

$title = JBusinessUtil::getPageTitle("");
JBusinessUtil::setMetaData($title, "", "", true);
$cartData = OfferSellingService::getCartData();
?>

<style>
	.price-info .price-txt {
		display:block;
	}
</style>

<div class="jbd-container">
	<div class="cart-container">
		<?php if(!empty($this->cart->listingItems)) { ?>
			<h1>
				<?php echo JText::_('LNG_YOUR_SHOPPING_CART'); ?>
				<a class="btn btn-sm btn-warning" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=cart.emptyCart') ?>"><?php echo JText::_('LNG_EMPTY_CART'); ?></a>
			</h1>
			<form method="post" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=offerbuyerdetails.checkBillingDetails&'.JSession::getFormToken().'=1') ?>" id="cart-form">
				<div class="row">
					<div class="col-lg-8">
						<div id="cart-wrapper" class="jbd-wrapper-front">
							<?php foreach($this->cart->listingItems as $companyId => $companyItems) { ?>
								<div id="cart-item-grou-<?php echo $companyId ?>" class="cart-item">
									<table class="cart-item-table responsive-simple">
										<thead>
											<tr>
												<th colspan="2" class="cart-item-header"></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ($companyItems['items'] as $item) {
											$boughtItemDetails = '';
											foreach ($item->attributeDetails as $attributeDetail){
												$boughtItemDetails .= '<div class="item-attribute"><strong>'. $attributeDetail->name.':</strong> '. $attributeDetail->options .'</div>';
											}
											?>
											<tr id="cart-item-<?php echo $item->uniqueId ?>">
												<td data-title="">
													<div class="cart-item-pic">
														<a target="_blank" href="<?php echo $item->link; ?>">
															<img alt="<?php echo $item->name; ?>"
																src="<?php echo BD_PICTURES_PATH.$item->picture ?>">
														</a>
													</div>
												</td>
												<td>
													<div class="cart-item-description">
														<span class="item-description"><strong><?php echo $item->name?></strong><br/> <?php echo $item->description; ?><br/>
															<div class="cart-item-attributes">
																<?php echo $boughtItemDetails; ?>
															</div>
														</span>

														<div class="row mt-3">
															<div class="col-md-4">
																<?php echo JText::_('LNG_QUANTITY'); ?>
																<strong>
																	<span id="cart-item-quantity-<?php echo $item->uniqueId ?>"><?php echo $item->quantity; ?></span>
																</strong>
															</div>
															<div class="col-md-4">
																<?php echo JText::_('LNG_PRICE'); ?>
																<strong><?php echo JBusinessUtil::getPriceFormat($item->price, $item->currencyId); ?></strong>
															</div>
															<div class="col-md-4 text-right">
																<span>
																	<?php $maxItems = $item->max_purchase;
																	if($maxItems > $item->total_quantity)
																		$maxItems = $item->total_quantity;
																	?>
																	<?php if($item->enable_offer_selling != OFFER_SELLING_COUPON){ ?>
																	<a href="javascript:void(0)" class="btn btn-sm btn-primary btn-panel" onclick="editCartItem('<?php echo $item->uniqueId."',".$item->min_purchase.",".$maxItems ?>)"><i class="la la-pencil"></i> <?php echo JText::_('LNG_EDIT'); ?></a>
																	<?php } ?> 
																	<a href="javascript:void(0)" class="btn btn-sm btn-danger btn-panel" onclick="removeCartItem('<?php echo $item->uniqueId ?>')"><i class="la la-trash"></i></a>
																</span>
															</div>
														</div>
													</div>
												</td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
									
									<div class="row">
										<div class="col-md-4">
											<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php') && $this->appSettings->enable_shipping) { ?>
												<select id="shipping_method_<?php echo $companyId ?>" class="form-control shipping-method" onchange="selectShippingMethod('<?php echo $companyId ?>')">
													<option value="-1"><?php echo JText::_('LNG_SELECT_SHIPPING_METHOD') ?></option>
														<?php foreach ($this->shippingMethods[$companyId] as $method) {
															$selected = '';
															if (!isset($this->shippingCosts[$companyId]["notSelected"])) {
																if ($method->id == $this->shippingCosts[$companyId]["shippingMethodId"]) {
																	$selected = "selected";
																}
															}
															?>
														<option value="<?php echo $method->id ?>" <?php echo $selected ?>><?php echo $method->name ?></option>
													<?php } ?>
												</select>
											<?php } ?>
										</div>

										<div class="col-md-8">
											<div class="price-info">
												<div class="price-item">
													<div class="price-text">
														<?php echo JText::_('LNG_PRODUCTS'); ?>
													</div>
													<div class="price-value">
														<?php echo JBusinessUtil::getPriceFormat( $companyItems['total_price'], $this->cart->currency_id) ?>
													</div>
												</div>
											
												<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php') && $this->appSettings->enable_shipping) { ?>
													<div class="price-item">
														<div class="price-text">
															<?php echo JText::_('LNG_SHIPPING'); ?>
														</div>
														<div class="price-value">
															<span id="cart-item-subtotal-<?php echo $companyId; ?>"><?php echo JBusinessUtil::getPriceFormat($this->shippingCosts[$companyId]['totPrice'], $this->cart->currency_id); ?></span>
														</div>
													</div>
												<?php } ?>
												
												<!-- <div class="price-item">
													<div class="price-text">
														<?php echo JText::_('LNG_TOTAL'); ?>
													</div>
													<div class="price-value">
														<span id="cart-item-total-<?php echo $companyId; ?>"><?php echo JBusinessUtil::getPriceFormat($companyItems['total_price'] + $this->shippingCosts[$companyId]['totPrice'], $this->cart->currency_id) ?></span>
													</div>
												</div> -->
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="cart-container">
							<div class="order-items">

								<div class="order-section">
									<div class="order-item-title"><?php echo JText::_('LNG_PRODUCTS'); ?> </div>
									<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat( $this->cart->base_amount, $this->cart->currency_id); ?></div>
								</div>

								<?php if ($this->cart->vat_amount > 0) { ?>
									<div class="order-section">
										<div class="order-item-title"><?php echo JText::_("LNG_VAT") ?> <span class="text-small">(<?php echo $this->cart->vat . "%" ?>)</span></div>
										<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($this->cart->vat_amount, $this->cart->currency_id) ?></div>
									</div>
								<?php } ?>
					
								<?php
									if (!empty($this->cart->taxes)) {
										foreach ($this->cart->taxes as $tax) {
								?>
										<div class="order-section">
											<div class="order-item-title"><?php echo $tax->tax_name ?> <span class="text-small"><?php echo ($tax->tax_type == 2) ? "( " . $tax->tax_amount . " %)" : "" ?></span></div>
											<div class="order-item-desc"><?php echo JBusinessUtil::getPriceFormat($tax->tax_calc_amount, $this->cart->currency_id) ?></div>
										</div>
									<?php } ?>
								<?php } ?>

								<!--<h3><?php echo JText::_('LNG_ALL_TOTAL'); ?>: <b><span style="margin-right:10px;" id="cart-total-price"><?php echo $this->items['totalPrice']; ?></span></b></h3>-->
					

								<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php') && $this->appSettings->enable_shipping) { ?>
									<div class="order-section">
										<div class="order-item-title">
											<?php echo JText::_('LNG_SHIPPING'); ?>
										</div>
										<div class="order-item-desc">
											<span id="cart-item-subtotal-<?php echo $companyId; ?>"><?php echo JBusinessUtil::getPriceFormat($this->shippingCosts[$companyId]['totPrice'], $this->cart->currency_id); ?></span>
										</div>
									</div>
								<?php } ?>

								<div class="order-section">
									<div class="order-item-desc">
										<?php echo JText::_('LNG_TOTAL'); ?> 
									</div>
									<div class="order-item-desc">
										<span id="cart-item-subtotal-<?php echo $companyId; ?>"><?php echo JBusinessUtil::getPriceFormat($this->cart->total_amount, $this->cart->currency_id); ?></span>
									</div>
								</div>

								<div class="order-section justify-content-end">
									<a class="btn btn-success px-3" href="javascript:checkoutCart()">
										<?php echo JText::_('LNG_CHECKOUT'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		<?php } else { ?>
			<div class="empty-cart">
				<img src="<?php echo BD_PICTURES_PATH.'/empty-cart.svg' ?>">
				<div class="title"><?php echo JText::_('LNG_NO_ITEMS_IN_CART'); ?></div>
				<a class="btn btn-success" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offers') ?>"><?php echo JText::_('LNG_CONTINUE_BROWSING'); ?> <i class="la la-arrow-right"></i></a>
			</div>
		<?php } ?>
	</div>
</div>
<script>

	function checkoutCart() {
		let selectAll = true;
		<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php') && $this->appSettings->enable_shipping) { ?>
		jQuery('.shipping-method').each(function () {
			let val = jQuery(this).val();
			if (val == -1) {
				selectAll = false;
			}
		});
		<?php } ?>

		if (selectAll) {
			jQuery('#cart-form').submit();
		} else {
			alert(JBD.JText._("LNG_SELECT_SHIPPING_METHOD_FOR_ALL"));
		}
	}


	function editCartItem(itemId, min, max) {
		let quantity = jQuery('#cart-item-quantity-' + itemId).html();

		let selectHtml = '';
		selectHtml = '<select id="cart-item-quantity-' + itemId + '" onchange="saveChanges(\'' + itemId + '\')" style="width:70px;">';
		if (min == max) {
			selectHtml = '<select id="cart-item-quantity-' + itemId + '" onclick="saveChanges(\'' + itemId + '\')" style="width:70px;">';
		}
		for (let i = min; i <= max; i++) {
			if (i != quantity)
				selectHtml += '<option value="' + i + '">' + i + '</option>';
			else
				selectHtml += '<option value="' + i + '" selected>' + i + '</option>';
		}
		selectHtml += '</select>';

		jQuery('#cart-item-quantity-' + itemId).html(selectHtml);
	}

	function saveChanges(itemId) {
		let urlEditCartItem = jbdUtils.getAjaxUrl('editCartItemAjax', 'cart');
		let quantity = jQuery('#cart-item-quantity-' + itemId + ' :selected').text();

		jQuery.ajax({
			type: "GET",
			url: urlEditCartItem,
			data: {offerId: itemId, quantity: quantity},
			dataType: 'json',
			success: function (data) {
				// jQuery('#cart-item-quantity-'+itemId).empty();
				// jQuery('#cart-item-quantity-'+itemId).text(quantity);
				// var itemTotalPrice = data['items'][itemId].totalPrice;
				// var totalPrice = data['totalPrice'];
				//
				// jQuery('#cart-item-total-'+itemId).text(itemTotalPrice);
//			jQuery('#cart-total-price').text(totalPrice);
				location.reload();
			}
		});
	}

	function removeCartItem(itemId) {
		let urlRemoveCartItem = jbdUtils.getAjaxUrl('removeCartItemAjax', 'cart');

		jQuery.ajax({
			type: "GET",
			url: urlRemoveCartItem,
			data: {offerId: itemId},
			dataType: 'json',
			success: function (data) {
				// jQuery('#cart-item-'+itemId).fadeOut("normal", function(){
				//	 jQuery(this).remove();
				// });
				//
				// if(data['items'].length == 0)
				//	 location.reload();
				// else
				// {
				//	 var totalPrice = data['totalPrice'];
				//	 //jQuery('#cart-total-price').text(totalPrice);
				// }

				location.reload();
			}
		});
	}

<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php') && $this->appSettings->enable_shipping) { ?>
	function selectShippingMethod(companyId) {
		let urlSelectShippingMethod = jbdUtils.getAjaxUrl('selectShippingMethodAjax', 'cart');
		let methodId = jQuery('select#shipping_method_' + companyId + ' option:selected').val();

		if (methodId == -1) {
			alert("<?php echo JText::_('LNG_SELECT_SHIPPING_METHOD') ?>");
			return false;
		}

		jQuery.ajax({
			type: "GET",
			url: urlSelectShippingMethod,
			data: {companyId: companyId, methodId: methodId},
			dataType: 'json',
			success: function (data) {
				location.reload();
			}
		});
	}
<?php } ?>
</script>
