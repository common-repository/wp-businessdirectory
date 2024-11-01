<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';

$appSettings = JBusinessUtil::getApplicationSettings();
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;
$enableNumbering = $appSettings->enable_numbering;
$user = JBusinessUtil::getUser();

$limitStart = JFactory::getApplication()->input->getInt('limitstart', 0);
if (empty($limitStart)) {
	$limitStart  = JFactory::getApplication()->input->get('start', 0);
}

if(!$appSettings->search_results_loading) {
    $total_page_string = $this->pagination->getPagesCounter();
    $current_page = substr((string)$total_page_string, 5, 1);
    if (($current_page === 1) || $total_page_string==null) {
        $limitStart = 0;
    }
}

$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
?>
<div id="jbd-results-list-container" itemscope itemtype="http://schema.org/ItemList" class="list-style-7" <?php echo $appSettings->search_view_mode?'style="display: none"':'' ?>>
<?php
		if (!empty($this->companies)) {
			$itemCount = 1;
			foreach ($this->companies as $index=>$company) {
				?>
		<?php
		  if (!empty($searchModules) && isset($searchModules[$index])) {
		  	foreach ($searchModules[$index] as $module) {
		  		?>
		          <div class="search-result-module">
		          	<?php echo JModuleHelper::renderModule($module, array("style"=>"xhtml")); ?>
		          </div>
		          <?php
		  	}
		  } ?>
		<div class="result-item <?php echo isset($company->featured) && $company->featured==1?"featured":"" ?>" style="<?php echo !empty($company->featured) && !empty($appSettings->listing_featured_bg)?"background-color: $appSettings->listing_featured_bg":"" ?>">
			<div itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
                <div itemscope itemprop="item" itemtype="http://schema.org/LocalBusiness">
                    <div class="row">
						<?php if (isset($company->packageFeatures) && in_array(IMAGE_UPLOAD, $company->packageFeatures) || !$enablePackages) { ?>
							<?php if (!empty($company->pictures)) {?>
								<div class="col-lg-5 pr-lg-0">
									<div class="company-gallery">
										<a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
											<?php foreach ($company->pictures as $picture) {?>
												<img src="<?php echo BD_PICTURES_PATH.$picture->picture_path?>" title="<?php echo $picture->picture_title ?>" alt="<?php echo $picture->picture_info ?>" style="display:none">
											<?php } ?>
										</a>
									</div>
								</div>
							<?php }?>
						<?php } ?>
	 					<div class="col-lg">
	 						<div class="result-content"> 
   								<div class="result-content-top">
									<?php if (isset($company->featured) && $company->featured==1) { ?>
										<div class="featured-text">
											<?php echo JText::_("LNG_FEATURED")?>
										</div>
									<?php } ?>
									<div class="result-header">
										<?php if ($this->defaultAttributes["logo"]!=ATTRIBUTE_NOT_SHOW && (isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$enablePackages)) { ?>
											<div class="company-logo" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
												<a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>">
													<?php if (!empty($company->logoLocation)) {?>
														<img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl" />
													<?php } else { ?>
														<img title="<?php echo $company->name?>" alt="<?php echo $company->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl" />
													<?php } ?>
												</a>
											</div>
										<?php } ?>
										<div class="busienss-name-info">									
											<h3 class="business-name">
												<a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company)?>" ><?php echo $enableNumbering? "<span>".($index + $limitStart + 1).". </span>":""?><span itemprop="name"> <?php echo $company->name?> </span></a>
												<?php if (!empty($company->recommended) && $appSettings->show_recommended) { ?>
													<div class="recommended"><i class="la la-star"></i></div>
												<?php } ?>
											</h3>
											<div class="company-rating" <?php echo !$enableRatings? 'style="display:none"':'' ?>>
												<?php if ($appSettings->enable_ratings) { ?>
														<div class="rating">
															<p class="rating-average" title="<?php echo $company->review_score?>" id="<?php echo $company->id?>" style="display: block;"></p>
														</div>
														<?php if (!empty($this->reviews) > 0) { ?>
														<div class="review-count" <?php echo $company->review_score == 0 ? 'style="display:none"':'' ?>>
														<span> <?php echo $company->nr_reviews." ".JText::_("LNG_REVIEWS");?></span>
														</div>
													<?php } ?>
												<?php } ?>
											</div>
											<span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>
										</div>
									</div>

									<?php if (!empty($company->short_description)) {?>
										<div class="company-intro">
											<?php echo JBusinessUtil::truncate($company->short_description, 200) ?>
											<a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><?php echo JText::_('LNG_MORE_INFO') ?></a>
										</div>
									<?php } ?>

									<div class="item-options">
										<?php if (!empty($company->customAttributes) && $appSettings->show_custom_attributes) { ?>
											<?php
												$renderedContent = AttributeService::renderAttributesSearchResults($company->customAttributes, $enablePackages, $company->packageFeatures);
												echo $renderedContent;
											?>
										<?php } ?>
										<?php
											$address = JBusinessUtil::getShortAddress($company);
											if (!empty($company->bestMatchLocation)) {
												$address = $company->bestMatchLocation;
												$address .= " <a target=\"_blank\" href=\"" . JBusinessUtil::getCompanyLink($company) . "\"> (" . JText::_("LNG_VIEW_ALL_LOCATIONS") . ")</a>";
											}
										?>
										<?php if (!empty($address)) { ?>
											<div class="item-option">
												<span itemprop="address"><i class="icon map-marker"></i>&nbsp;<?php echo $address ?></span>

												<?php if (!empty($company->distance)) { ?>
													<span>
														<?php echo JText::_("LNG_DISTANCE") . ": " . round($company->distance, 1) . " " . ($appSettings->metric == 1 ? JText::_("LNG_MILES") : JText::_("LNG_KM")) ?>
													</span>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
    							</div>
    							<div class="result-content-bottom">
									<div class="row">
										<div class="col-lg-7">
											<?php require "listings_info_section.php"; ?>
										</div>
										<div class="col-lg-5">
											<?php require "listings_actions.php"; ?>
										</div>
									</div>
								</div>
    						</div>
						</div>
					</div>
                </div>
			</div>
		</div>
		<?php $itemCount++; ?>
     	<?php
			} ?>
    <?php
		} ?>
</div>

<script>
	window.addEventListener('load', function() {
		renderListGallery()
	});
	
	function renderListGallery() {
		jQuery(".company-gallery").each(function(){
			jQuery(this).children().children().first().show();
		});

		var visibleChild = 1;
		
		jQuery(".company-gallery").mousemove(function( event ) {
			var nrChildren = jQuery(this).children().children().size();
			var offsetX = parseInt(jQuery(this).offset().left);
			var width = parseInt(jQuery(this).width());
			var currentChild = parseInt((event.pageX - offsetX) / (width/nrChildren));
			if((currentChild+1)>=nrChildren){
				currentChild = nrChildren-1;
			}
			if(currentChild!=visibleChild){
				jQuery(this).children().children().hide();
				jQuery(this).children().children().eq(currentChild).show();
				visibleChild = currentChild;
			}
		});
	}
</script>