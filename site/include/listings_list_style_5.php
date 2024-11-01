<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$user = JBusinessUtil::getUser();
$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
?>
<style>
	#sp-main-body {
		padding: 0;
		margin: 0;
	}

	.container, #sp-main-body>div {
		margin: 0;
		width: auto;
		max-width: none;
        padding:0;
	}

    .site-main{
        padding: 0;
    }

    #content{
        padding: 0;
    }


</style>

<div id="map-view-container" class="list-style-5">
    <div class="row m-0">
        <div id="map-listing-container" class="col-lg-6" itemscope itemtype="http://schema.org/ItemList">
			<div class="search-header">
                <div class="jbd-search-statistics">
            		<div class="search-keyword">
                        <div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div>
                    </div>
                    <div>
                        <div class="search-options">
                            <div class="jbd-select-box">
                                <i class="la la-sort"></i>
                                <select name="orderBy" class="chosen jbd-select" onchange="jbdUtils.changeOrder(this.value)">
                                    <?php echo JHtml::_('select.options', $this->sortByOptions, 'value', 'text', $this->orderBy);?>
                                </select>
                            </div>
                            
                            <?php if($this->appSettings->enable_rss == 1) { ?>
                                <div class="view-mode">
                                    <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=directoryrss.getCompaniesRss') ?>" target="_blank">
                                        <i class="la la-rss"></i>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <div id="search-filters-react">
                </div>
            </div>
            
            <div class="list-grid-3">
                <div id="jbd-results-list-container" class="row">
                    <?php
                        if (isset($this->companies)) {
                            $index = 0;
                            foreach ($this->companies as $index => $company) {
                                $index++; 
                                $showOnMap = (!empty($company->latitude) && !empty($company->longitude) && $showData && (isset($company->packageFeatures) && in_array(GOOGLE_MAP, $company->packageFeatures) || !$appSettings->enable_packages)); 
                    ?>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div id="company<?php echo $company->id ?>" class="grid-item-holder" itemscope  itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                                <span style="display:none;" itemprop="position"><?php echo $index ?></span>
                                <span itemscope itemprop="item" itemtype="http://schema.org/Organization">
                                    <div class="jitem-card card-shadow">
                                        <div class="jitem-img-wrap">
                                            <a <?php echo $newTab; ?> data-companyId="<?php echo $company->id ?>" href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"></a>
                                            <?php if ((isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$enablePackages) 
                                                        && $this->defaultAttributes['logo'] != ATTRIBUTE_NOT_SHOW && !empty($company->business_cover_image))  { ?>
                                                    <img src="<?php echo file_exists(JBusinessUtil::getThumbnailImage(BD_PICTURES_UPLOAD_PATH.$company->business_cover_image))?JBusinessUtil::getThumbnailImage(BD_PICTURES_PATH.$company->business_cover_image):BD_PICTURES_PATH.$company->business_cover_image ?>" alt="<?php echo $company->name ?>">
                                                <?php } else { ?>
                                                    <img src="<?php echo !empty($appSettings->default_bg_listing) ?  BD_PICTURES_PATH. $appSettings->default_bg_listing :  BD_PICTURES_PATH. '/app/default_bg.jpg' ?>" alt="<?php echo $company->name ?>">
                                                <?php } ?>
                                            <div class="jitem-logo">
                                            <?php if ((isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$enablePackages) 
                                                        && $this->defaultAttributes['logo'] != ATTRIBUTE_NOT_SHOW && !empty($company->logoLocation))  { ?>
                                                    <img src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" alt="<?php echo $company->name ?>">
                                                <?php } else { ?>
                                                    <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $company->name ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        
                                        <div class="jitem-body">
                                            <div class="jitem-rating">
                                                <?php if(!empty($company->review_score) && $appSettings->enable_ratings){ ?>
                                                    <span title="<?php echo $company->review_score ?>" class="rating-review"></span>
                                                    <?php if ($company->nr_reviews > 0){ ?>
                                                        <div class="jitem-rating-count"><?php echo $company->nr_reviews." ".JText::_("LNG_REVIEWS") ?> </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            
                                            <div class="jitem-body-content">
                                                <div class="jitem-title text-bold">
                                                    <a class="item-name" data-companyId="<?php echo $company->id ?>" <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>" >
                                                        <span><?php echo $company->name; ?></span>
                                                    </a>
                                                </div>
                                                <div class="jitem-desc">
                                                    <?php
                                                        if(!empty($company->slogan)) {
                                                            echo JBusinessUtil::truncate($company->slogan, 75);
                                                        } else if(!empty($company->short_description)) {
                                                            echo JBusinessUtil::truncate($company->short_description, 75);
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="jitem-bottom bottom-border">
                                                <?php if(!empty($company->mainCategoryIcon) || !empty($company->categoryIconImage)) { ?>
                                                    <a href="<?php echo JBusinessUtil::getCategoryLink($company->mainCategoryId,$company->mainCategoryAlias) ?>" class="item-category bottom-item">
                                                        <?php echo JBusinessUtil::renderCategoryIcon($company->mainCategoryIcon, $company->categoryIconImage) ?>&nbsp;<?php echo $company->mainCategory ?>
                                                    </a>
                                                <?php } ?>
                                            
                                                <?php if ($showData && (isset($company->packageFeatures) && in_array(CONTACT_FORM, $company->packageFeatures) || !$enablePackages)) { ?>
                                                    <?php if ($appSettings->show_contact_form) { ?>
                                                        <div class="horizontal-element">
                                                            <a href="javascript:jbdListings.showContactCompanyList(<?php echo $company->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $company->name ?>', '<?php echo $company->logoLocation ?>',  '<?php echo $company->business_cover_image ?>', <?php echo $company->review_score ?>)"><i class="icon envelope-circle"></i></a>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php if ($showOnMap) { ?>
                                                    <a href="javascript:void(0)"
                                                        class="show-marker"><i class="icon map-marker-circle"></i></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span style="display:none;" itemprop="position"><?php echo $index ?></span>
                                </span>
                            </div>
                        </div>
                        <?php } ?>  
                    <?php } ?>
                </div>
            </div>
            <div id="load-more-btn" class="row" style="display:none">
                <div class="col-12 text-center mt-4 btn-container">
                    <button type="button" class="load-more-js btn btn-outline-primary" onclick="jbdUtils.loadMoreItems()" ><?php echo JText::_("LNG_LOAD_MORE")?></button>
                </div>
            </div>
        </div>
    
        <div class="col-lg-6 p-lg-0 sticky-map-container">
            <div id="style5-map-container">
                <?php require JPATH_COMPONENT_SITE . '/include/search-map.php' ?>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function() {
        
    <?php if ($appSettings->enable_ratings) {?>
        jbdListings.renderGridReviewRating();
    <?php } ?>

    let viewportHeight = window.innerHeight;
    
    jQuery(".sticky-map-container").height(viewportHeight);
    var mapHeight = viewportHeight;//jQuery("#map-listing-container").height();
    let initParams = <?php echo json_encode($initparams) ?>;
    
    initParams["map_height"] = mapHeight+"px";
    jbdUtils.setProperty('map_enable_auto_locate', 0);
    jbdUtils.setProperty('enable_map_clustering', 0)

    mapInstance =  JBDMapHelper.loadMapScript(
        <?php echo json_encode($company_locations) ?>,
        initParams,
        <?php echo $appSettings->map_type ?>
    );

    mapInstance.initialize();
    mapInstance.mapInitialized = true;

});
</script>