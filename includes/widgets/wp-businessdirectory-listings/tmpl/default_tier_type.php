<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');$max_numbers_row = $params->get('max-numbers-row');
$showHeader = $params->get('show-header');
$imageSize = $params->get('image_size');
$span = $params->get('phoneGridOption').' '.$params->get('tabletGridOption').' '.$params->get('desktopGridOption');

$imageClass = '';
if ($imageSize == 1) {
    $imageClass = 'small-image';
} else if ($imageSize == 2) {
    $imageClass = 'medium-image';
}

if(!empty($max_numbers_row) && is_numeric($max_numbers_row)) {
	$percent = 100/$max_numbers_row;
	$width = $percent.'%'; ?>
	<style type="text/css">
		.dynamic-col {height: auto; float: left}
	</style>
<?php } else { ?>
	<style type="text/css">
		.dynamic-col {height: auto; float: left}
	</style>
<?php } ?>
<style type="text/css">
	@media (max-width: 970px) { .dynamic-col {width: 25%;} }
	@media (max-width: 767px) { .dynamic-col {width: 33.3333%;} }
	@media (max-width: 599px) { .dynamic-col {width: 50%;} }
</style>

<div class="jbd-container">
    <div class="row grid-divider">
    	<div id="latestbusiness" class="latestbusiness<?php echo $moduleclass_sfx; ?>" >
    		<div class="bussiness responsive" itemscope itemtype="http://schema.org/ItemList">
    			<?php if(!empty($items)) { ?>
    				<?php
    				$types = array();
    				foreach ($items as $company) {
    					array_push($types, $company->typeName);
    				} ?>
    
    				<?php 
    				$types = array_unique($types);
    				sort($types);
    				foreach (array_unique($types) as $typeName) { ?>
    				<?php if($showHeader){?>
    						<div class="<?php echo $span?>">
    							<h1><?php echo $typeName; ?></h1>
    						</div>
    					<?php } ?>
    					<div class="row">
    						<div class="<?php echo $span?>">
    							<div class="business-row-container">
    								<ul>
    									<?php foreach ($items as $company) { ?>
    										<?php if ($company->typeName == $typeName) { ?>
    											<li itemscope itemprop="itemListElement" itemtype="http://schema.org/LocalBusiness">
    												<div class="company-box dynamic-col remove-padding <?php echo $imageClass ?>">
    													<div class="full-width-logo slider-item">
    														<div class="offer-overlay">
    															<div class="offer-vertical-middle">
    																<div> 
    																	<a <?php echo $newTab; ?> href="<?php echo $company->link?>" data-companyId="<?php echo $company->id ?>" class="btn-view <?php echo $campaignCallClass; ?>"><span itemprop="name"><?php echo $company->companyName; ?></span></a>
    																</div>
    															</div>
    														</div>
    														<a <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $company->id ?>" href="<?php echo $company->link?>" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
    															<?php if(!empty($company->logoLocation)) { ?>
    																<img title="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl">
    															<?php } else { ?>
    																<img title="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
    															<?php } ?>
    														</a>
    													</div>
    												</div>
    											</li>
    										<?php } ?>
    									<?php } ?>
    								</ul>
    							</div>
    						</div>
    					</div>
    				<?php } ?>
    			<?php } ?>
    		</div>
            <?php if(!empty($params) && $params->get('showviewall')){?>
                <br/>
                <div class="view-all-items">
                    <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
                </div>
            <?php }?>
    	</div>
    </div>
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