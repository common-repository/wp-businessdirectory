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
$user = JBusinessUtil::getUser();
?>

<div class="jbd-container offers<?php echo $moduleclass_sfx; ?> jbd-grid-container offers-simple">
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
                                    
                			<div class="jitem-body">
                				<div class="jitem-body-content">
                                    <div class="pb-3 country-flag-container">
                                        <img class="country_flag" style="height:20px;" src="<?php echo BD_PICTURES_PATH . $item->country_flag ?>" />
                                    </div>
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

                                        <?php if(!empty($item->short_description)){ ?>
                                            <div class="company-info short-description" itemprop="description">
                                                <?php  echo JBusinessUtil::truncate( $item->short_description , 150 ) ?>
                                            </div>
                                        <?php }?>
                                        <?php if(!empty($item->startDate)){?>
                                            <div class="py-2 date-container">
                                                <i class="icon calendar"></i> <?php echo JBusinessUtil::getShortWeekDate($item->startDate) ?>
                                            </div>                                    
                                        <?php } ?>
                    				</div>
                                    <div class="jitem-bottom justify-content-between">
                                        <div class="offer-price">
            								<?php if(!empty($item->price)){ ?>
            									<span class="<?php echo $item->specialPrice>0 ?"old-price":"" ?>"><?php echo JBusinessUtil::getPriceFormat($item->price, $item->currencyId) ?></span><br/>
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
                                        <div class="pt-3">
                                            <a class="btn btn-success" href="<?php echo $item->link ?>">
                                                <?php echo JText::_("LNG_VIEW_DETAILS")?>
                                            </a>
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

<div id="login-notice" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INFO') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p>
                <?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
            </p>
            <p>
                <a href="<?php echo JBusinessUtil::getLoginUrl($url); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
            </p>
        </div>
    </div>
</div>

<div id="package-notice" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INFO') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p id="package-text">
            
            </p>
            <p>
                <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=packages'); ?>"><?php echo JText::_('LNG_VIEW_PACKAGES') ?></a>
            </p>
        </div>
    </div>
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

    function showPackageNotice(text){
        jQuery("#package-text").html(text);
        jQuery('#package-notice').jbdModal();                     
    }
</script>