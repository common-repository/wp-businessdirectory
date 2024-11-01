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
    <?php $index = 0;?>
    <div class="row">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<?php $index ++; ?>
        		<div class="<?php echo $span?> my-3">
            		<div class="jitem-card"  style="<?php echo $borderCss?>">
            			<div class="jitem-img-wrap">
            				<a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    						 <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
    							<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
    						<?php } else { ?>
    							<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->subject ?>">
    						<?php } ?>
    						<div class="card-hoverable">
                                <a class="hoverable h-100 w-100" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
                            </div>
            			</div>
            			<div class="jitem-body">
            				<div class="jitem-body-content">
                				<div class="jitem-title">
                					<a class="item-name" href="<?php echo $item->link ?>" >
                                    	<?php echo $item->subject; ?>
                                	</a>
                				</div>
                				<div class="jitem-desc">
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
                                        <?php if ($showListingName && !empty($item->company_id)){ ?>
                                        <p>
                                           <i class="icon business"></i>
                                            <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->company_name; ?></a>
                                        </p>
                                        <?php } ?>
                        			</div>
            					</div>
            				</div>
            				<div class="jitem-bottom text-center">
                				<div style="<?php echo $backgroundCss?>" class="p-3 w-100">
                                	<?php  
                                    $address = JBusinessUtil::getShortAddress($item);
                                    if($showLocation && !empty($address)) {?>
                						<div class="item-address mb-2">
                							 <i class="icon map-marker"></i> <?php echo $address; ?>
                						</div>
                                    <?php } ?>
                                    <a class="btn btn-success" href="<?php echo $item->link ?>">
                                        <?php echo JText::_("LNG_VIEW_DETAILS")?>
                                    </a>
                                </div>
                			</div>
            			</div>
            			
            		</div>
            	</div>
		        <?php if($index%4 == 0 && count($items)>$index){ ?>
                    </div>
                    <div class="row">
                <?php }?>
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