<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>
<div class="latestbusiness<?php echo $moduleclass_sfx; ?> list-view" itemscope itemtype="http://schema.org/ItemList">
	<ul>
		<?php foreach($items as $company){ ?>
			<li itemscope itemprop="itemListElement" itemtype="http://schema.org/LocalBusiness">
				<div class="business-logo" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
					<a class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $company->id ?>" <?php echo $newTab; ?> href="<?php echo $company->link?>">
						<?php if(!empty($company->logoLocation)) { ?>
							<img title="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" itemprop="contentUrl">
						<?php } else { ?>
							<img title="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($company->name, ENT_QUOTES) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
						<?php } ?>
					</a>
				</div>
				<div class="company-info">				
					<a class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $company->id ?>" class="company-name" <?php echo $newTab; ?> href="<?php echo $company->link ?>">
						<span itemprop="name"><?php echo $company->name; ?></span>
					</a>
					<div class="company-address" itemprop="address">
                        <?php $address = JBusinessUtil::getShortAddress($company);
                        if(!empty($address)) { ?>
						    <span><i class="icon map-marker"></i> <?php echo $address?></span>
                        <?php } ?>
					</div>				
				</div>
			</li>
		<?php } ?>
	</ul>
	<div class="clear"></div>
    <?php if(!empty($params) && $params->get('showviewall')){?>
        <div class="view-all-items">
            <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
        </div>
    <?php }?>
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