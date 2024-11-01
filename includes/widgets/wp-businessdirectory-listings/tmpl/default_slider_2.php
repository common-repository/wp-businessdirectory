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
$menuItemId = JBusinessUtil::getActiveMenuItem();
?>

<div class="jbd-container listings<?php echo $moduleclass_sfx; ?>">
    <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
    <div class="bussiness-slider responsive slider list-grid-3" id="slider-<?php echo $sliderId ?>">
        <?php if(!empty($items)) ?>
        <?php foreach ($items as $item) { ?>
            <div>
                <div class="slider-item">
                    <div class="slider-content" id="slider-content-<?php echo $sliderId ?>">
                        <div class="my-5">
                            <div class="jitem-card card-shadow h-100">
                                <div class="jitem-img-wrap">
                                    <a <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
                                    <?php if((!$enablePackages || (isset($item->package) && in_array(SHOW_COMPANY_LOGO, $item->package))) && !empty($item->business_cover_image)) { ?>
                                        <img src="<?php echo file_exists(JBusinessUtil::getThumbnailImage(BD_PICTURES_UPLOAD_PATH.$item->business_cover_image))?JBusinessUtil::getThumbnailImage(BD_PICTURES_PATH.$item->business_cover_image):BD_PICTURES_PATH.$item->business_cover_image ?>" alt="<?php echo $item->name ?>">
                                        <?php } else { ?>
                                            <img src="<?php echo !empty($appSettings->default_bg_listing) ?  BD_PICTURES_PATH. $appSettings->default_bg_listing :  BD_PICTURES_PATH. '/app/default_bg.jpg' ?>" alt="<?php echo $item->name ?>">
                                        <?php } ?>
                                    <div class="jitem-logo">
                                        <?php if((!$enablePackages || (isset($item->package) && in_array(SHOW_COMPANY_LOGO, $item->package))) && !empty($item->logoLocation)) { ?>
                                            <img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" alt="<?php echo $item->name ?>">
                                        <?php } else { ?>
                                            <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <div class="jitem-body">
                                    <div class="jitem-rating">
                                        <?php if(!empty($item->review_score) && $appSettings->enable_ratings){ ?>
                                            <span title="<?php echo $item->review_score ?>" class="rating-review"></span>
                                            <?php if (!empty($item->nr_reviews)){ ?>
                                                <div class="jitem-rating-count"><?php echo $item->nr_reviews." ".JText::_("LNG_REVIEWS") ?> </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    
                                    <div class="jitem-body-content">
                                        <div class="jitem-title text-bold">
                                            <a class="item-name" data-companyId="<?php echo $item->id ?>" <?php echo $newTab; ?> href="<?php echo $item->link?>" >
                                                <span><?php echo $item->name; ?></span>
                                            </a>
                                        </div>
                                        <div class="jitem-desc">
                                            <?php
                                                if(!empty($item->slogan)) {
                                                    echo JBusinessUtil::truncate($item->slogan, 75);
                                                } else if(!empty($item->short_description)) {
                                                    echo JBusinessUtil::truncate($item->short_description, 75);
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="jitem-bottom bottom-border">
                                        <?php if(!empty($item->mainCategory)) { ?>
                                            <a href="<?php echo JBusinessUtil::getCategoryLink($item->mainCategoryId,$item->mainCategoryAlias) ?>" class="item-category bottom-item">
                                                <?php if((!empty($item->mainCategoryIcon) && $item->mainCategoryIcon!='None') || !empty($item->categoryIconImage)) { 
                                                    echo JBusinessUtil::renderCategoryIcon($item->mainCategoryIcon, $item->categoryIconImage);
                                                } ?> 
                                                &nbsp;<?php echo $item->mainCategory ?>
                                            </a>
                                        <?php } ?>
                                    
                                        <?php if ($showData && (isset($item->packageFeatures) && in_array(PHONE, $item->packageFeatures) || !$enablePackages)) { ?>
                                            <?php if (!empty($item->phone)) { ?>
                                                <div class="horizontal-element phone" itemprop="telephone">
                                                    <a href="tel:<?php echo ($item->phone); ?>"><i class="icon phone-circle"></i></a>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($showData && (isset($item->packageFeatures) && in_array(CONTACT_FORM, $item->packageFeatures) || !$enablePackages)) { ?>
                                            <?php if ($appSettings->show_contact_form) { ?>
                                                <div class="horizontal-element">
                                                    <a href="javascript:jbdListings.showContactCompanyList(<?php echo $item->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $item->name ?>', '<?php echo $item->logoLocation ?>',  '<?php echo $item->business_cover_image ?>', <?php echo $item->review_score ?>)"><i class="icon envelope-circle"></i></a>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                    </div>
                                </div>
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

<?php require_once 'listing_utils.php'; ?>

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
