<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');
$showLocation = isset($showLocation)?$showLocation:1;
$appSettings = JBusinessUtil::getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$idnt = rand(500, 1500);
$sliderId = rand(1000,10000);

$sliderParams = array();
$sliderParams['sliderId'] = $sliderId;
$sliderParams['autoplay'] = $params->get('autoplay') ? true : false;
$sliderParams['autoplaySpeed'] = $params->get('autoplaySpeed');
$sliderParams['nrVisibleItems'] = $params->get('nrVisibleItems');
$sliderParams['nrItemsToScrool'] = $params->get('nrItemsToScrool');
$sliderParams['rtl'] = $dir ? true : false;

$user = JBusinessUtil::getUser();
$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
$db = JFactory::getDBO();
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
?>

<div class="jbd-container events<?php echo $moduleclass_sfx; ?>" >
    <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
    <?php $index = 0; ?>
    <div class="bussiness-slider responsive slider" id="slider-<?php echo $sliderId ?>">
        <?php if(!empty($items)) ?>
        <?php foreach ($items as $item) {?>
            <?php $index ++; ?>
            <div>
                <div class="slider-item">
                    <div class="slider-content" id="slider-content-<?php echo $sliderId ?>" style="<?php echo $backgroundCss?> <?php echo $borderCss?>">
    					<div class="card place-card h-100">
    						<div class="place-card-body">
    							<a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    							 <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
    								<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
    							<?php } else { ?>
    								<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
    							<?php } ?>
    							
    							<div class="card-hoverable">
    								<?php if ($showListingName && !empty($item->company_id) && !empty($item->companyName)){ ?>
                                        <h5 class="company-info icon business white">
                                            <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->companyName; ?></a>
                                        </h5>
                                    <?php } ?>
    								
                                    <div >
                                        <?php $address = JBusinessUtil::getShortAddress($item);
                                        if($showLocation && !empty($address)) { ?>
                                            <i class="icon map-marker white"></i> <?php echo $address; ?>
                                        <?php }?>
                                    </div>
    
                                    <?php if(!empty($item->phone) && $showData && (isset($item->packageFeatures) && in_array(PHONE,$item->packageFeatures) || !$enablePackages)) { ?>
                                        <div>
                                            <i class="icon phone white"></i> <?php echo htmlspecialchars($item->phone, ENT_QUOTES) ?>
                                        </div>
                                    <?php } ?>
                                    <?php if($showData && !empty($item->website) && (isset($item->packageFeatures) && in_array(WEBSITE_ADDRESS,$item->packageFeatures) || !$enablePackages)){
                                        if ($appSettings->enable_link_following){
                                            $followLink = (isset($item->packageFeatures) && in_array(LINK_FOLLOW,$item->packageFeatures) && $enablePackages)?'rel="follow noopener"' : 'rel="nofollow noopener"';
                                        }else{
                                            $followLink ='rel="noopener"';
                                        }?>
                                        <div onclick="this.event.stopPropagation()">
                                            <a <?php echo $followLink ?> itemprop="url" title="<?php echo $db->escape($item->name);?> Website" onclick="jbdUtils.registerStatAction(<?php echo $item->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>);event.stopPropagation();" href="<?php echo $db->escape($item->website) ?>"><i class="la la-globe"></i> <?php echo $db->escape($item->website) ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($item->customAttributes)) { ?>
                                        <div class="attribute-icon-container-slider">
                                            <?php foreach($item->customAttributes as $attribute) {
                                                $icons = AttributeService::getAttributeIcons($attribute, $appSettings->enable_packages, $item->packageFeatures);
                                                $color = !empty($attribute->color)?$attribute->color:'';
                                                if(!empty($icons)) {
                                                    foreach($icons as $icon)
                                                        echo '<i class="'.$icon.' attribute-icon" style="color:'.$color.';"></i>';
                                                }
                                            }?>
                                        </div>
                                    <?php } ?>
    							</div>
    						</div>
    						<div class="place-card-info">
    							<div class="place-card-info-title">
    								<a class="item-title" title ="<?php echo $db->escape($item->name);?>" href="<?php echo $item->link ?>"><?php echo $item->name ?></a>
                                    <div class="item-desc">
                                        <?php
                                            $dates = JBusinessUtil::getDateGeneralShortFormat($item->start_date);
                                            if(!empty($dates)) { ?>
                                                <span><i class="icon calendar"></i>
                                                <?php echo $dates;
                                                if ($item->show_start_time && !empty($item->start_time)) {
                                                    ?> /
                                                    <i class="icon clock"></i> <?php echo($item->show_start_time ? JBusinessUtil::convertTimeToFormat($item->start_time) : "") ?>
                                                    <?php
                                                }?>
                                            </span>       
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <?php if(!empty($item->mainCategoryIcon)){ ?>
    								 <a href="<?php echo $item->mainCategoryLink ?>">
                                        <i class="pull-right la la-custom rounded-circle la la-bg-grey la la-<?php echo $item->mainCategoryIcon ?>"></i>
                                    </a>
                                <?php } ?>
    						</div>
                            
    					</div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if(!empty($params) && $params->get('showviewall')){?>
        <div class="view-all-items">
            <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
        </div>
    <?php }?>
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
