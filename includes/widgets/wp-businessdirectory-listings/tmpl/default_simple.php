<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
defined('_JEXEC') or die('Restricted access');
$showLocation = isset($showLocation)?$showLocation:1;
?>

<div class="jbd-container listings<?php echo $moduleclass_sfx; ?> jbd-grid-container module-style-simple-1">
    <?php $index = 0;?>
    <div class="row">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<?php $index ++; ?>
        		<div class="<?php echo $span?> my-2">
            		<div class="jitem-card text-center"  style="<?php echo $borderCss?>">
            			<div class="jitem-img-wrap">
            				<a <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    						 <?php if(!empty($item->logoLocation)) { ?>
    							<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" alt="<?php echo $item->name ?>">
    						<?php } else { ?>
    							<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
    						<?php } ?>
    						<div class="card-hoverable">
    							<a <?php echo $newTab; ?> class="<?php echo $campaignCallClass; ?> hoverable h-100 w-100" data-companyId="<?php echo $item->id ?>" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
                            </div>
            			</div>
            			<div class="jitem-body">
            				<div class="jitem-body-content">
                				<div class="jitem-title text-center">
                					<a class="item-name <?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" <?php echo $newTab; ?> href="<?php echo $item->link ?>" >
                                    	<?php echo $item->name; ?>
                                	</a>
                				</div>
                				<div class="jitem-desc">
    								 <p style="padding-top: 6px !important;">
                                        <?php
											if(!empty($item->slogan)) {
												echo $item->slogan;
											} else if(!empty($item->short_description)) {
												echo JBusinessUtil::truncate($item->short_description, 200);
											} else if(!empty($item->description)) {
												echo JBusinessUtil::truncate($item->description, 200);
											}
                                        ?>
                                    </p>
                				</div>
            				</div>
            				<div class="jitem-bottom">
            	     			<div style="<?php echo $backgroundCss?>" class="p-3 w-100">
            						
                                	<?php  
                                        $address = JBusinessUtil::getShortAddress($item);
                                        if($showLocation && !empty($address)) {?>
                    						<div class="item-address mb-2">
                    							 <i class="icon map-marker"></i> <?php echo $address; ?>
                    						</div>
                                    <?php } ?>
                                    <a class="btn btn-success <?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" <?php echo $newTab; ?> href="<?php echo $item->link ?>">
                                        <?php echo JText::_("LNG_VIEW_DETAILS")?>
                                    </a>
                                </div>
        	                </div>
            			</div>
                	</div>
            	</div>
   			 <?php } ?>
       	 <?php } ?>
    </div>

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