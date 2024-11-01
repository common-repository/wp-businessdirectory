<?php
/**
 * @package    WPBusinessDirectory
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

<div class="jbd-container listings<?php echo $moduleclass_sfx; ?>" >
    <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
    <div class="bussiness-slider responsive slider" id="slider-<?php echo $sliderId ?>">
        <?php if(!empty($items)) ?>
        <?php foreach ($items as $item) {?>
            <div>
                <div class="slider-item">
                    <div class="slider-content" id="slider-content-<?php echo $sliderId ?>" style="<?php echo $backgroundCss?> <?php echo $borderCss?>">
    					<div class="card place-card h-100">
    						<div class="place-card-body">
    							<a <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    							 <?php if(!empty($item->logoLocation)) { ?>
    								<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" alt="<?php echo $item->name ?>">
    							<?php } else { ?>
    								<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
    							<?php } ?>
    							
    							<div class="card-hoverable">
    								<h3><?php echo $item->name ?></h3>
                                    <div class="" >
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
                                            <a <?php echo $followLink ?> itemprop="url" title="<?php echo $db->escape($item->name);?> Website" onclick="jbdUtils.registerStatAction(<?php echo $item->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>);event.stopPropagation();" href="<?php echo $db->escape($item->website) ?>"><i class="la la-lg la-globe"></i> <?php echo $db->escape($item->website) ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($item->customAttributes)) { ?>
                                        <div class="attribute-icon-container-slider">
                                            <?php foreach($item->customAttributes as $attribute) {
                                               // $icons = AttributeService::getAttributeIcons($attribute, $appSettings->enable_packages, $item->packageFeatures);
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
                                    <a class="item-title" <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>">
                                        <?php echo $item->name ?>
                                    </a>
    								 <?php if(isset($item->review_score) && $appSettings->enable_ratings){ ?>
                                        <span title="<?php echo $item->review_score ?>" class="rating-review-<?php echo $idnt ?>"></span>
                                    <?php } ?>
    							</div>
    							<?php if((!empty($item->mainCategoryIcon) && $item->mainCategoryIcon!='None') || !empty($item->categoryIconImage)){ ?>
    							<?php $attributes="pull-right  la la-custom rounded-circle bg-warning"; ?>
                                    <a href="<?php echo $item->mainCategoryLink .$geoLocationParams ?>"><?php echo JBusinessUtil::renderCategoryIcon($item->mainCategoryIcon, $item->categoryIconImage, $attributes) ?></a>
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
