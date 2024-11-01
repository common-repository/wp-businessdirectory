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
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;

?>

<!-- layout -->
<div class="jbd-grid-container list-grid-3">
	<div id="jbd-results-grid-container" class="row">
		<?php
			if (isset($this->companies)) {
				$index = 0;
				foreach ($this->companies as $index => $company) {
					$index++; 
		?>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="grid-item-holder" itemscope  itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                        <span style="display:none;" itemprop="position"><?php echo $index ?></span>
                        <span itemscope itemprop="item" itemtype="http://schema.org/LocalBusiness">
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
                                    
                                        <?php if ($showData && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$enablePackages)) { ?>
                                            <?php if (!empty($company->phone)) { ?>
                                                <div class="horizontal-element phone" itemprop="telephone">
                                                    <a href="tel:<?php echo $this->escape($company->phone); ?>"><i class="icon phone-circle"></i></a>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($showData && (isset($company->packageFeatures) && in_array(CONTACT_FORM, $company->packageFeatures) || !$enablePackages)) { ?>
                                            <?php if ($appSettings->show_contact_form) { ?>
                                                <div class="horizontal-element">
                                                    <a href="javascript:jbdListings.showContactCompanyList(<?php echo $company->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $company->name ?>', '<?php echo $company->logoLocation ?>',  '<?php echo $company->business_cover_image ?>', <?php echo $company->review_score ?>)"><i class="icon envelope-circle"></i></a>
                                                </div>
                                            <?php } ?>
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
<script>
	window.addEventListener('load', function(){
		<?php if ($appSettings->enable_ratings) {?>
			jbdListings.renderGridReviewRating();
		<?php } ?>
	});
</script>