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

<div class="jbd-container offers<?php echo $moduleclass_sfx; ?> mod-items-list container">
	<div class="row">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<div class="col-12 my-1">
            		<div class="text-center row list-item"  style="<?php echo $borderCss?>">
            			<div class="col-md-3 p-0">
	            			<div class="jitem-img-wrap">
	            				<a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    						 <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
    							<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>"  title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
    						<?php } else { ?>
    							<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->subject ?>">
    						<?php } ?>
	    						<div class="card-hoverable">
	                            </div>
	            			</div>
            			</div>
            			<div class="col-md-5 py-3">
            				<div class="jitem-body pb-2">
	            				<div class="jitem-body-content">
	                				<div class="item-name text-left">
	                					<a class="item-name" href="<?php echo $item->link ?>" >
	                                    	<?php echo $item->subject; ?>
	                                	</a>
	                				</div>
	            				</div>
	            				<div class="jitem-bottom text-left">
	                				<div class="pt-2 w-100">
	                				 	<?php if ($showListingName && !empty($item->company_id)){ ?>
		    	                            <div class="listing-name mb-2">
                                                <i class="icon business"></i> <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->company_name; ?> </a>
		            	                    </div>
		                                <?php } ?>
	                                	<?php  
	                                    $address = JBusinessUtil::getShortAddress($item);
	                                    if($showLocation && !empty($address)) {?>
	                						<div class="item-address mb-2">
	                							 <i class="icon map-marker"></i> <?php echo $address; ?>
	                						</div>
	                                    <?php } ?>
	                                </div>
	                			</div>
	            			</div>
				            <?php //echo OfferSellingService::getAddToCartBtn($item, 'w-25') ?>
                        </div>
            			<div class="col-md-4 py-3">
            				<div class="offer-price text-right">
                        		<?php if(!empty($item->price)){ ?>
                        			<div class="<?php echo $item->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($item->price, $item->currencyId) ?></div>
                        		<?php } ?>
                        		<?php if(!empty($item->specialPrice)){?>
                        			<div class="price"><?php echo JBusinessUtil::getPriceFormat($item->specialPrice, $item->currencyId); ?></div>
                        		<?php }?>
                                <?php if ($attributeConfig["price_text"]!=ATTRIBUTE_NOT_SHOW) { ?>
                                	<?php if (!empty($item->price_text)) { ?>
                                        <div  class="price"><?php echo $item->price_text ?></div>
                                     <?php }elseif (empty($item->price) && empty($item->specialPrice) && ($showFreeText)){ ?>
                                         <div class="price"><?php echo JText::_('LNG_FREE') ?></div>
                                     <?php } ?>
                                <?php } ?>
                                
                                 <a class="btn btn-success" href="<?php echo $item->link ?>">
                                        <?php echo JText::_("LNG_VIEW_DETAILS")?>
                                    </a>
                       			</div>
	            			</div>
	            		</div>
	                </div>
	           <?php } ?>
		<?php } ?>
	</div>
    <?php if(!empty($params) && $params->get('showviewall')){?>
        <div class="view-all-offers">
            <a href="<?php echo $viewAllLink ?>"><?php echo JText::_("LNG_VIEW_ALL_OFFERS")?></a>
        </div>
    <?php } ?>
</div>

<script>
    window.addEventListener('load', function(){
        <?php
            $load = JFactory::getApplication()->input->get("geo-latitude");
            if($params->get('geo_location') && empty($load)){ ?>
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(jbdUtils.addCoordinatesToUrl);
                }
        <?php } ?>
    });
</script>