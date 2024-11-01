<?php

/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');

$idnt = rand(500, 1500);
$sliderId = rand(1000,10000);

$sliderParams = array();
$sliderParams['sliderId'] = $sliderId;
$sliderParams['autoplay'] = $params->get('autoplay') ? true : false;
$sliderParams['autoplaySpeed'] = $params->get('autoplaySpeed');
$sliderParams['nrVisibleItems'] = $params->get('nrVisibleItems');
$sliderParams['nrItemsToScrool'] = $params->get('nrItemsToScrool');
$sliderParams['rtl'] = $dir ? true : false;
$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
?>

<div class="jbd-container offers<?php echo $moduleclass_sfx; ?>" >
    <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
    <?php $index = 0; ?>
    <div class="bussiness-slider responsive slider" id="slider-<?php echo $sliderId ?>">
        <?php if(!empty($items)) ?>
        <?php foreach ($items as $item) {?>
            <?php $index ++; ?>
            <div class="slider-item">
                <div class="slider-content" id="slider-content-<?php echo $sliderId ?>" style="<?php echo $backgroundCss?> <?php echo $borderCss?>">
                    <div class="card place-card h-100">
                        <div class="place-card-body">
                            <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
                                <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
                                <img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
                            <?php } else { ?>
                                <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->subject ?>">
                            <?php } ?>
                            
                            <div class="card-hoverable">
                                <strong><?php echo $item->subject ?></strong>
                                <?php $address = JBusinessUtil::getShortAddress($item);
                                    if($showLocation && !empty($address)) { ?>
                                        <div>
                                            <i class="icon map-marker"></i> <?php echo $address; ?>
                                        </div>
                                <?php }?>
                                <?php if(!empty($item->phone)) { ?>
                                <div>
                                    <i class="icon phone"></i> <?php echo $item->phone ?>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                        
                        <div class="place-card-info">
                            <div class="place-card-info-title">
                                <a class="item-title" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"><?php echo $item->subject ?></a>
                                <?php if ($showListingName && !empty($item->company_id)){ ?>
                                    <div>
                                        <i class="icon business"></i> <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->company_name; ?></a>
                                    </div>
                                <?php } ?>
                                <?php if(isset($item->review_score) && $appSettings->enable_ratings){ ?>
                                    <span title="<?php echo $item->review_score ?>" class="rating-review-<?php echo $idnt ?>"></span>
                                <?php } ?>
                                <div class="offer-price">
                                    <?php if(!empty($item->price)){ ?>
                                        <span class="<?php echo $item->specialPrice>0 ?"old-price":"price" ?>"><?php echo JBusinessUtil::getPriceFormat($item->price, $item->currencyId) ?></span>
                                    <?php } ?>
                                    <?php if(!empty($item->specialPrice)){?>
                                        <span class="price red"><?php echo JBusinessUtil::getPriceFormat($item->specialPrice, $item->currencyId); ?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <?php //echo OfferSellingService::getAddToCartBtn($item) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if(!empty($params) && $params->get('showviewall')){?>
        <div class="view-all-offers">
            <a href="<?php echo $viewAllLink ?>"><?php echo JText::_("LNG_VIEW_ALL_OFFERS")?></a>
        </div>
    <?php } ?>
</div>

<script>
    window.addEventListener('load', function() {
        jbdUtils.initSlider(<?php echo json_encode($sliderParams) ?>);

        <?php if($appSettings->enable_ratings) { ?>
        jQuery('.rating-review-<?php echo $idnt ?>').rating({
            min:0,
            max:5,
            step:0.5,
            stars:5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('.rating-review-<?php echo $idnt ?>').each(function() {
            jQuery(this).rating('update',this.title);
        });
        <?php } ?>

        <?php
        $load = JFactory::getApplication()->input->get("geo-latitude");
        if($params->get('geo_location') && empty($load)){ ?>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(jbdUtils.addCoordinatesToUrl);
        }
        <?php } ?>
    });

</script>
