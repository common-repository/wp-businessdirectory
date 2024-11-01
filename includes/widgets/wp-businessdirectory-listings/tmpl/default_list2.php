
<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="latestbusiness<?php echo $moduleclass_sfx; ?> list-view2" itemscope itemtype="http://schema.org/ItemList">
	<div class="row">
		<?php $index = 0;?>
		<?php foreach ($items as $item) : ?>
			<?php $index ++; ?>
			<div class="<?php echo $span?>">
				<div class="item-container row" itemscope itemprop="itemListElement" itemtype="http://schema.org/LocalBusiness">
					<div class="business-logo col-md-6" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
						<a <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo $item->link?>">
							<?php if(!empty($item->logoLocation)) { ?>
								<img title="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" itemprop="contentUrl">
							<?php } else { ?>
								<img title="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($item->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
							<?php } ?>
						</a>
					</div>
					<div class="company-info col-md-6">	
						<h3>	
							<a <?php echo $newTab; ?> class="company-name <?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo $item->link ?>">
								<span itemprop="name"><?php echo $item->name; ?></span>
							</a>
						</h3>
						<div class="listing-description" >
							<?php echo $item->short_description; ?>
						</div>		
						<a <?php echo $newTab; ?> class="mt-3 btn btn-success  <?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo $item->link ?>">
							<?php echo JText::_("LNG_VIEW_DETAILS")?>
						</a>					
					</div>
					<div class="clear"></div> 
				</div>
			</div>
		<?php endforeach; ?>
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
