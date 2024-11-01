<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<!-- layout -->
<div class="jbd-grid-container list-grid-2">
	<div id="jbd-results-grid-container" class="row">
		<?php
			if (isset($this->companies)) {
				$index = 0;
				foreach ($this->companies as $index => $company) {
					$index++; 
		?>
				<div class="col-lg-4 col-sm-6 col-12">
					<div class="jitem-card <?php echo isset($company->featured) && $company->featured == 1 ? "featured" : "" ?>">
						<div class="jitem-img-wrap">
							<a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"> </a>
							<?php if (!empty($company->logoLocation) &&
									(isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$appSettings->enable_packages)) { ?>
								<img title="<?php echo $this->escape($company->name) ?>"
									alt="<?php echo $this->escape($company->name) ?>"
									src="<?php echo BD_PICTURES_PATH . $company->logoLocation ?>">
							<?php } else { ?>
								<img title="<?php echo $this->escape($company->name) ?>"
									alt="<?php echo $this->escape($company->name) ?>"
									src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>">
							<?php } ?>
							
							<div class="jitem-body-content">
								<?php if (!empty($company->mainCategory)) { ?>
									<div class="post-category">
										<a href="<?php echo JBusinessUtil::getCategoryLink($company->mainCategoryId, $company->mainCategoryAlias) ?>"><?php echo $company->mainCategory ?> </a>
									</div>
								<?php } ?>
								<div class="jitem-title">
									<a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><span><?php echo $company->name ?></span></a>
								</div>
								<?php if ($appSettings->enable_ratings && false) {?>
									<div class="jitem-rating">
										<span title="<?php echo $company->review_score ?>" class="rating-review"></span> 
										<?php if ($company->nr_reviews > 0){ ?>
											<div class="jitem-rating-count"><?php echo $company->nr_reviews ?></div>
										<?php } ?>
									</div>
								<?php } ?>

								<?php
									$address = JBusinessUtil::getShortAddress($company);
									if (!empty($company->bestMatchLocation)) {
										$address = $company->bestMatchLocation;
									} 
								?>
								<?php if (!empty($address)) {?>
									<div class="company-address">
										<span><i class="icon map-marker"></i>&nbsp;<?php echo $address?></span>
									</div>
								<?php } ?>
									
							</div>
						</div>
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