<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<div class="jbd-container events<?php echo $moduleclass_sfx; ?> jbd-grid-container">
	<div class="row has-flex-columns">
		<?php if(isset($items)){ ?>
			<?php $counter = 0; ?>
			<?php foreach($items as $i=>$item){ $counter++?>
				<div class="<?php echo $span?> mb-3">
					<div class="jitem-card">
                        <?php if (!JBusinessUtil::emptyDate($item->start_date)){ ?>
                            <div class="jitem-date-wrap bg-primary">
                                <p><?php echo JBusinessUtil::getDayOfMonth($item->start_date) ?></p>
                                <p><?php echo JBusinessUtil::getMonth($item->start_date) ?> <?php echo JBusinessUtil::getYear($item->start_date) ?></p>
                            </div>
                        <?php } ?>
            			<div class="jitem-img-wrap">
            				<a href="<?php echo JBusinessUtil::getEventLink($item->id, $item->alias) ?>"></a>
        					<?php if(!empty($item->picture_path)){?>
        						<img src="<?php echo BD_PICTURES_PATH.$item->picture_path ?>"  title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
							<?php } else { ?>
								<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo stripslashes($item->name)?>">
							<?php } ?>
							<div class="card-hoverable">
                            	<a href="<?php echo $item->link ?>" class="btn btn-outline-success btn-sm w-auto"><?php echo JText::_("LNG_VIEW")?></a>
                            </div>
            			</div>
            			<div class="jitem-body">
            				<div class="jitem-body-content">
                				<div class="jitem-title">
                					<a href="<?php echo JBusinessUtil::getEventLink($item->id, $item->alias) ?>"><?php echo stripslashes($item->name)?></a>
                				</div>
                				<div class="jitem-desc">
                					<?php if($item->show_start_time && !empty($item->start_time)){?>
    									<div class="pt-3">
    										<i class="icon clock"></i> <?php echo ($item->show_start_time?JBusinessUtil::convertTimeToFormat($item->start_time):"")." ".(!empty($item->end_time) && $item->show_end_time?"-":"")." ".($item->show_end_time?JBusinessUtil::convertTimeToFormat($item->end_time):""); ?>
    									</div>                				
    								<?php }?>
                					<?php if (!$appSettings->item_decouple && $showListingName && !empty($item->company_id)){?>
        								<span class="company-info">
											<i class="icon business"></i> <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->companyName; ?></a>
        	                            </span>
        							<?php } ?>
        							<div><?php echo $item->short_description ?></div>
                				</div>
                			</div>
                			<div class="jitem-bottom justify-content-center py-2">
                				<?php if($showLocation && !empty($item->city)) { ?>
    								<div class="item-location">
    									<i class="icon map-marker"></i> <a class="location" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=events&citySearch='.$item->city."&Itemid=".$menuItemId); ?>"><?php echo $item->city ?></a>
    								</div>
    							<?php } ?>
                			</div>
            			</div>
					</div>
				</div>
    			<?php if( $counter%4==0 && $counter>3){?>
					</div>
					<div class="row has-flex-columns">
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</div>	
	
	<?php if(!empty($params) && $params->get('showviewall')){?>
		<div class="view-all-items">
			<a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
		</div>
	<?php }?>
</div>