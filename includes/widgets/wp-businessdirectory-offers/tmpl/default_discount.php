<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
?>

<div class="jbd-container offers<?php echo $moduleclass_sfx; ?> jbd-grid-container">
	<div class="row">
		<?php if(isset($items)){ ?>
			<?php $counter = 0; ?>
			<?php foreach($items as $i=>$item){ ?>
            	<?php $counter++?>
            		<div class="<?php echo $span?> my-3">
                		<div class="jitem-card">
                    		<?php 
        						$discount = 0;
        						if(!empty($item->price) && $item->specialPrice>0){
        							$discount = round((($item->price -$item->specialPrice) * 100)/$item->price ,0);
        						}
        					?>
							<?php if(!empty($discount)){?>
        						<div class="jitem-discount-wrap bg-primary">
                    				<p><?php echo JText::_("LNG_DISCOUNT") ." ".$discount?> %</p>
                    			</div>
							<?php }?>
                			
                			<div class="jitem-img-wrap">
                				<a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
        						<?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
        							<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>"  title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
        						<?php } else { ?>
        							<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->subject ?>">
        						<?php } ?>
                			</div>
                			<div class="jitem-body">
                				<div class="jitem-body-content">
                    				<div class="jitem-title d-flex justify-content-between">
                    					<a href="<?php echo  $item->link ?>"><?php echo stripslashes($item->subject)?></a>
					                    <?php //echo OfferSellingService::getAddToCartBtn($item) ?>
                                    </div>
                    				<div class="jitem-desc">
                                		<?php if ($showListingName && !empty($item->company_id)){ ?>
            								<span class="company-info">
            	                                <i class="icon business"></i> <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->company_name; ?></a>
            	                            </span>
            							<?php }
            							$address = JBusinessUtil::getShortAddress($item);
            							if($showLocation && !empty($address)) { ?>
                							<div class="offer-city">
                								<i class="icon map-marker"></i> <?php echo $address;?>
                							</div>
            							<?php }?>
            							<div class="offer-price">
            								<?php if(!empty($item->price)){ ?>
            									<span class="<?php echo $item->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($item->price, $item->currencyId) ?></span>
            								<?php } ?>
            								<?php if(!empty($item->specialPrice)){?>
            									<span class="price red"><?php echo JBusinessUtil::getPriceFormat($item->specialPrice, $item->currencyId); ?></span>
            								<?php }?>
                                            <?php if ($attributeConfig["price_text"]!=ATTRIBUTE_NOT_SHOW) { ?>
                                                <?php if (!empty($item->price_text)) { ?>
                                                    <br/>
                                                    <span  class="price red"><?php echo $item->price_text ?></span>
                                                <?php }elseif (empty($item->price) && empty($item->specialPrice) && ($showFreeText)){ ?>
                                                    <span class="price red"><?php echo JText::_('LNG_FREE') ?></span>
                                                <?php } ?>
                                            <?php } ?>
            							</div>
                    				</div>
                    			</div>
                    		</div>
                		</div>
            		</div>
			<?php }?>
		<?php }?>	
	</div>

	<?php if(!empty($params) && $params->get('showviewall')){?>
		<div class="view-all-offers">
			<a href="<?php echo $viewAllLink ?>"><?php echo JText::_("LNG_VIEW_ALL_OFFERS")?></a>
		</div>
	<?php } ?>
</div>

<script>
    window.addEventListener('load', function(){
        jQuery(".full-width-logo").each(function(){
        });

        <?php
        $load = JFactory::getApplication()->input->get("geo-latitude");
        if($params->get('geo_location') && empty($load)){ ?>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(jbdUtils.addCoordinatesToUrl);
        }
        <?php } ?>
    });
</script>