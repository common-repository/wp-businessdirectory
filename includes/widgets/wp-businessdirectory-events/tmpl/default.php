<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$showLocation = isset($showLocation)?$showLocation:1;
?>

<div class="jbd-container events<?php echo $moduleclass_sfx; ?> jbd-grid-container">
    <?php $index = 0;?>
    <div class="row has-flex-columns">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<?php $index ++; ?>
        		<div class="<?php echo $span?> my-3">
            		<div class="jitem-card jitem-date-right my-3"  style="<?php echo $borderCss?>">
                        <?php if (!JBusinessUtil::emptyDate($item->start_date)){ ?>
                            <div class="jitem-date-wrap  bg-dark">
                                <p><?php echo JBusinessUtil::getDayOfMonth($item->start_date) ?></p>
                                <p><?php echo JBusinessUtil::getMonth($item->start_date) ?> <?php echo JBusinessUtil::getYear($item->start_date) ?></p>
                            </div>
                        <?php } ?>
            			<div class="jitem-img-wrap">
            				<a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
    						 <?php if(isset($item->logoLocation) && $item->logoLocation!='') { ?>
    							<img src="<?php echo BD_PICTURES_PATH.$item->logoLocation ?>"  title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
    						<?php } else { ?>
    							<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo $item->name ?>">
    						<?php } ?>
    						<div class="card-hoverable">
                                <a class="hoverable h-100 w-100" href="<?php echo htmlspecialchars($item->link, ENT_QUOTES) ?>"></a>
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
                					<?php if($item->show_start_time && !empty($item->start_time)){?>
    									<div class="pt-2">
    										<i class="icon clock"></i> <?php echo ($item->show_start_time?JBusinessUtil::convertTimeToFormat($item->start_time):"")." ".(!empty($item->end_time) && $item->show_end_time?"-":"")." ".($item->show_end_time?JBusinessUtil::convertTimeToFormat($item->end_time):""); ?>
    									</div>                				
    								<?php }?>
    								<div class="pt-2">
                                    	<?php
                                            if(!empty($item->slogan)) {
                                                echo $item->slogan;
                                            } else if(!empty($item->short_description)) {
                                                echo JBusinessUtil::truncate($item->short_description, 200);
                                            } else if(!empty($item->description)) {
                                                echo JBusinessUtil::truncate($item->description, 200);
                                            }
                                        ?>
                                    </div>
                				</div>
            				</div>
            				<div class="jitem-bottom text-center">
                				<div style="<?php echo $backgroundCss?>" class="p-3 w-100">
                                	<?php  
                                    $address = JBusinessUtil::getShortAddress($item);
                                    if($showLocation && !empty($address)) {?>
                						<div class="item-address mb-2">
                							 <i class="icon map-marker"></i> <a class="location" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=events&citySearch='.$item->city."&Itemid=".$menuItemId); ?>"><?php echo $address; ?></a>
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
		        <?php if($index%4 == 0 && count($items)>$index){ ?>
                    </div>
                    <div class="row has-flex-columns">
                <?php }?>
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
        jQuery(".full-width-logo").each(function(){
        });

        <?php
        $load = JFactory::getApplication()->input->get("geo-latitude");
        if($params->get('geo_location') && empty($load)){ ?>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(jbdUtils.addCoordinatesToUrl);
        }
        <?php } ?>
    });

</script>