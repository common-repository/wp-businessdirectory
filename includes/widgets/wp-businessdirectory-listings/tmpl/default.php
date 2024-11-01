<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$showLocation = isset($showLocation)?$showLocation:1;
?>

<div class="jbd-container listings<?php echo $moduleclass_sfx; ?> jbd-grid-container">
    <?php $index = 0;?>
    <div class="row">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<?php $index ++; ?>
        		<div class="<?php echo $span?> my-2">
            		<div class="jitem-card text-center" style="<?php echo $borderCss?>">
            			<div class="jitem-img-wrap">
            				<a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    						 <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
    							<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>" alt="<?php echo $item->name ?>">
    						<?php } else { ?>
    							<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
    						<?php } ?>
    						<div class="card-hoverable">
                            </div>
            			</div>
            			<div class="jitem-body">
            				<div class="jitem-body-content">
            				<div class="jitem-title">
            					<a class="item-name" href="<?php echo $item->link ?>" >
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
        						<?php if(isset($item->mainCategoryLink)) { ?>
                                    <div class="dir-category">
                                            <a href="<?php echo $item->mainCategoryLink .$geoLocationParams ?>"><i class="la la-<?php echo $item->mainCategoryIcon ?>"></i> <?php echo $item->mainCategory ?></a>
                                    </div>
                                <?php } ?>
        					
                            	<?php  
                                    $address = JBusinessUtil::getShortAddress($item);
                                    if($showLocation && !empty($address)) {?>
                						<div class="item-address mb-2">
                							 <i class="la la-map-marker"></i> <?php echo $address; ?>
                						</div>
                                <?php } ?>
                                <a class="btn btn-success" href="<?php echo $item->link ?>">
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
    jQuery(document).ready(function(){
        <?php
            $load = JFactory::getApplication()->input->get("latitude");
            if($params->get('geo_location') && empty($load)){ ?>
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(addCoordinatesToUrl);
                }
        <?php } ?>
    });

    function addCoordinatesToUrl(position){

        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        var newURLString = window.location.href;
        newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
        newURLString += "latitude="+latitude;
        newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
        newURLString += "longitude="+longitude;

        window.location.href = newURLString;    // The page will redirect instantly

    }
</script>