<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
?>

<div class="offers-price-list" id="offers-price-list">
    <?php foreach ($this->offers as $offer) { ?>
    <?php $quantity = !empty($offer->stockQuantity) ? $offer->stockQuantity : $offer->quantity ?>
    <?php if  ($offer->add_to_price_list == 1) { ?>
        <div class="list-item row">
            <div class="offer-img col-md-3">
            <?php if (!empty($offer->picture_path) ) { ?>
                <img title="<?php echo $this->escape($offer->subject) ?>" alt="<?php echo $this->escape($offer->subject) ?>" src="<?php echo BD_PICTURES_PATH.$offer->picture_path ?>" >
            <?php } else { ?>
                <img title="<?php echo $this->escape($offer->subject) ?>" alt="<?php echo $this->escape($offer->subject) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" >
            <?php } ?>
            </div>

            <div class="offer-body col-md-9">
                <div class="offer-header">
                    <a href="<?php echo $offer->link ?>" class="title"><?php echo $offer->subject ?></a>
                    <a href="<?php echo $offer->link ?>" class="description"><?php echo $offer->short_description ?></a>
                </div>

                <div class="offer-details">
                    <div class="offer-price">
                    <?php $price = !empty($offer->specialPrice) ? $offer->specialPrice : $offer->price; ?>
                        <p>
                            <?php echo JText::_('LNG_PRICE') ?> 
                        </p>
                        <span class="price-text"><?php echo JBusinessUtil::getPriceFormat($price, $offer->currencyId) ?></span>
                    </div>

                    <div class="offer-quantity">
                        <?php if ($this->appSettings->enable_packages && isset($this->package) && in_array(SELL_OFFERS,$this->package->features) &&  $this->appSettings->enable_offer_selling && $offer->enable_offer_selling == OFFER_SELLING_REGULAR && JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate)
                                        || !$this->appSettings->enable_packages && $this->appSettings->enable_offer_selling && $offer->enable_offer_selling == OFFER_SELLING_REGULAR && JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate)) { ?>
                            <p>
                                <?php echo JText::_('LNG_QUANTITY') ?> 
                            </p>
                            <select id="offer-quantity-<?php echo $offer->id ?>">
                                <?php
                                    if ($offer->min_purchase > 0) {
                                        echo '<option value="0">0</option>';
                                    }

                                    $maximum = $offer->max_purchase;
                                    if ($quantity < $offer->max_purchase) {
                                        $maximum = $quantity;
                                    }
                                    $maximum = ($maximum < MAXIMUM_OFFER_QUANTITY_SELLING) ? $maximum : MAXIMUM_OFFER_QUANTITY_SELLING;
                                    for ($i = $offer->min_purchase; $i <= $maximum; $i++) {
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                    } 
                                ?>
                            </select>
                        <?php } ?>
                    </div>

                    <div class="offer-actions">
                        <?php if ($this->appSettings->enable_packages && isset($this->package) && in_array(SELL_OFFERS,$this->package->features) &&  $this->appSettings->enable_offer_selling && $offer->enable_offer_selling == OFFER_SELLING_REGULAR && JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate) && $quantity > 0
                                     || !$this->appSettings->enable_packages && $this->appSettings->enable_offer_selling && $offer->enable_offer_selling == OFFER_SELLING_REGULAR && JBusinessUtil::checkDateInterval($offer->startDate, $offer->endDate) && $quantity > 0) { ?>
                                <div class="btn-add-to-cart" onclick="jbdOffers.addToCart(<?php echo $offer->id; ?>, jQuery('#offer-quantity-<?php echo $offer->id ?> :selected').val())">                    
                                    <i class="la la-shopping-cart"></i>
                                    <a href="javascript:void(0)">
                                        <?php echo JText::_('LNG_ADD_TO_CART')?>
                                    </a>
                                </div>                       
                                <?php if(!empty($this->cart["items"]) && in_array($offer->id , array_column($this->cart["items"], 'id'))) {?>
                                    <div class="btn-remove-from-cart" onclick="jbdOffers.removeFromCart('<?php echo $offer->id; ?>_')">
                                     <i class="icon arrow-bin"></i>
                                    </div>
                                <?php } ?>
                        <?php } ?>
                        <?php if ($quantity == 0) { ?>
                            <div class="no-quantity-text">
                                <?php echo JText::_('LNG_OUT_OF_STOCK') ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php } ?>
</div>

<?php echo OfferSellingService::getCartModal() ?>