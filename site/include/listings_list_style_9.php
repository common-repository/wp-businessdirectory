<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');$idnt = rand(500, 1500);
$appSettings = JBusinessUtil::getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$user = JBusinessUtil::getUser();
$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
$mapHeight = "100%";
?>
<style>
    #content .container:first-child{
        max-width: none !important;
    }
</style>

<div class="jbd-container listings jbd-grid-container list-style-9">

    <div class="row">
        <div class="col-12 col-md-5">

	        <?php if(!empty($this->category->name)){ ?>
                <div class="row">
                    <div class="col">
                        <h1 class="search-category-name"><?php echo $this->category->name ?></h1>
                    </div>
                </div>
	        <?php } ?>

            <div class="ml-4">
                <?php
                    jimport('joomla.application.module.helper');
                    // this is where you want to load your module position
                    $modules = JModuleHelper::getModules("inside-search");
                ?>
                    <?php if(isset($modules) && count($modules)>0) { ?>
                        <div class="inside-search">
                            <?php
                            $fullWidth = false;
                            foreach($modules as $module) {
                                echo JModuleHelper::renderModule($module);
                            } ?>
                            <div class="clear"></div>
                        </div>
                <?php } ?>

                <div class="search-header-list-9">
                    <div class="search-header">
                        <div class="jbd-search-statistics">
                            <div class="search-keyword">
                                <div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div>
                            </div>
                            <div>
                                <div class="search-options">
                                    <div>
                                        <div class="jbd-select-box">
                                            <i class="la la-sort"></i>
                                            <select name="orderBy" class="chosen jbd-select" onchange="jbdUtils.changeOrder(this.value)">
                                                <?php echo JHtml::_('select.options', $this->sortByOptions, 'value', 'text', $this->orderBy);?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if ($this->appSettings->show_grid_list_option == 1) { ?>
                                        <div class="view-mode">
                                            <a id="grid-view-link-s9" class="grid active" href="javascript:void(0)"><i class="icon grid"></i></a>
                                            <a id="list-view-link-s9" class="list" href="javascript:void(0)"><i class="icon list-view"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <?php require_once "listings_grid_style_3.php" ?>

                    <div id="listings_list_style_9" style="display:none;">
                        <?php $index = 0;?>
                        <?php foreach ($this->companies as $item) { ?>
                        <?php $index ++; ?>
                            <div class="jitem-card card-horizontal card-shadow grid-item-holder mb-5" id="company<?php echo $item->id ?>">
                                <div class="jitem-img-wrap">
                                    <div class="img-holder">
                                        <a <?php echo $newTab; ?> data-companyId="<?php echo $item->id ?>" href="<?php echo JBusinessUtil::getCompanyLink($item) ?>"></a>
                                        <?php if ((isset($item->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $item->packageFeatures) || !$enablePackages) 
                                                    && $this->defaultAttributes['logo'] != ATTRIBUTE_NOT_SHOW && !empty($item->business_cover_image))  { ?>
                                                <img src="<?php echo file_exists(JBusinessUtil::getThumbnailImage(BD_PICTURES_UPLOAD_PATH.$item->business_cover_image))?JBusinessUtil::getThumbnailImage(BD_PICTURES_PATH.$item->business_cover_image):BD_PICTURES_PATH.$item->business_cover_image ?>" alt="<?php echo $item->name ?>">
                                            <?php } else { ?>
                                                <img src="<?php echo !empty($appSettings->default_bg_listing) ?  BD_PICTURES_PATH. $appSettings->default_bg_listing :  BD_PICTURES_PATH. '/app/default_bg.jpg' ?>" class="" alt="<?php echo $item->name ?>">
                                            <?php } ?>
                                    </div>
                                    <div class="jitem-logo">
                                        <?php if ((isset($item->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $item->packageFeatures) || !$enablePackages) 
                                                && $this->defaultAttributes['logo'] != ATTRIBUTE_NOT_SHOW && !empty($item->logoLocation))  { ?>
                                            <img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" alt="<?php echo $item->name ?>">
                                        <?php } else { ?>
                                            <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="jitem-body">
                                                                        
                                    <div class="jitem-body-content">
                                        <div class="jitem-title text-bold">
                                            <a class="item-name" data-companyId="<?php echo $item->id ?>" <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($item)?>" >
                                                <h3><?php echo $item->name; ?></h3>
                                            </a>
                                        </div>
                                        <div class="jitem-desc">
                                            <?php
                                                if(!empty($item->short_description)) {
                                                    echo JBusinessUtil::truncate($item->short_description, 60);
                                                } else if(!empty($item->description)) {
                                                    echo JBusinessUtil::truncate($item->description, 60);
                                                }
                                            ?>
                                        </div>

                                        <div class="jitem-rating">
                                            <?php if(!empty($item->review_score) && $appSettings->enable_ratings){ ?>
                                                <span title="<?php echo $item->review_score ?>" class="rating-review-<?php echo $idnt ?>"></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="jitem-bottom bottom-border">
                                        <?php if(!empty($item->mainCategoryId)) { ?>
                                            <a href="<?php echo JBusinessUtil::getCategoryLink($item->mainCategoryId,$item->mainCategoryAlias) ?>" class="item-category bottom-item">
                                            <?php echo JBusinessUtil::renderCategoryIcon($item->mainCategoryIcon, $item->categoryIconImage) ?>&nbsp;<?php echo $item->mainCategory ?>
                                            </a>
                                        <?php } ?>
                                        <?php if(!empty($item->email)){?>
                                            <a href="javascript:jbdListings.showContactCompanyList(<?php echo $item->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $item->name ?>', '<?php echo $item->logoLocation ?>',  '<?php echo $item->business_cover_image ?>', <?php echo $item->review_score ?>)" class="bottom-item">
                                                <i class="icon envelope"></i>
                                            </a>
                                        <?php } ?>
                                        <a href="javascript:void(0)" class="bottom-item show-marker">
                                            <i class="icon map-marker"></i>
                                        </a>
                                    </div>
                                </div>
                                
                            </div>
                    <?php } ?>
                </div>
            </div>
            <div class="pagination">
                <?php echo $this->pagination->getListFooter(); ?>
                <div class="clear"></div>
            </div>
        </div>

        <div class="col-12 col-md-7">
            <div id="style9-map-container" class="style9-map-container">
                <?php require JPATH_COMPONENT_SITE . '/include/search-map.php' ?>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        jQuery('#sp-main-body .container').removeClass('container').addClass('container-fluid');
        jQuery('#sp-bottom').css('position', 'relative');
        jQuery('#sp-bottom').css('z-index', '10');
        jQuery('#sp-footer').css('position', 'relative');
        jQuery('#sp-footer').css('z-index', '10');
        jQuery('#sp-header').css('z-index', '10');

        jQuery('.search-options-item').attr("style", "margin-right:0px !important");

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

        jQuery('#grid-view-link-s9').click(() => {
            jQuery('#listings_list_style_9').hide();
            jQuery('#listings_grid_style_3').show();
            jQuery('#grid-view-link-s9').toggleClass("active");
            jQuery('#list-view-link-s9').toggleClass("active");
        });

        jQuery('#list-view-link-s9').click(() => {
            jQuery('#listings_grid_style_3').hide();
            jQuery('#listings_list_style_9').show();
            jQuery('#grid-view-link-s9').toggleClass("active");
            jQuery('#list-view-link-s9').toggleClass("active");
        });
    });
</script>