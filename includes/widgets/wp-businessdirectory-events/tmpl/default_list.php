<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_OFFER);
?>

<div class="jbd-container events<?php echo $moduleclass_sfx; ?> mod-items-list events-container container">
	<div class="row">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<div class="col-12 my-1">
            		<div class="text-center row list-item"  style="<?php echo $borderCss?>">
            			<div class="col-md-3 p-0">
	            			<div class="jitem-img-wrap">
	            				<a href="<?php echo JBusinessUtil::getEventLink($item->id, $item->alias) ?>"></a>
	        					<?php if(!empty($item->picture_path)){?>
	        						<img src="<?php echo BD_PICTURES_PATH.$item->picture_path ?>"  title="<?php echo $item->picture_title ?>" alt="<?php echo $item->picture_info ?>">
								<?php } else { ?>
									<img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" alt="<?php echo stripslashes($item->name)?>">
								<?php } ?>
	            			</div>
            			</div>
            			<div class="col-md-5">
            				<div class="jitem-body py-2">
	            				<div class="jitem-body-content">
	                				<div class="item-name text-left">
	                					<a class="item-name" href="<?php echo $item->link ?>" >
	                                    	<?php echo $item->name; ?>
	                                	</a>
	                				</div>
	            				</div>
	            				<div class="jitem-bottom text-left">
	                				<div class="pt-2 w-100">
	                					<?php if ($showListingName && !empty($item->company_id)){ ?>
	        								<div class="listing-name">
	        	                                <i class="icon business"></i> <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>"><?php echo $item->companyName; ?></a>
	        	                            </div>
	        							<?php } ?>
	                				 	
										<?php
	                                        $dates = JBusinessUtil::getDateGeneralShortFormat($item->start_date);
	                                        if(!empty($dates)) { ?>
	                                        	<div class="pt-1">
	                                            	<i class="icon calendar"></i>
		                                            <?php echo $dates;
		                                            if ($item->show_start_time && !empty($item->start_time)) {
		                                                ?> /
		                                                <i class="icon clock"></i> <?php echo($item->show_start_time ? JBusinessUtil::convertTimeToFormat($item->start_time) : "") ?>
		                                                <?php
		                                            }?>
		                                     	</div>       
	                                     <?php } ?>
	                					
	                                	<?php $address = JBusinessUtil::getShortAddress($item);
                                        if($showLocation && !empty($address)) { ?>
                                        	<div class="pt-1">
                                            	<i class="icon map-marker"></i> <?php echo $address; ?>
                                            </div>
                                        <?php }?>
	                                </div>
	                			</div>
	            			</div>
            			</div>
            			<div class="col-md-4 d-flex align-items-center justify-content-end">
            				<div class="text-right">
                                 <a class="btn btn-success" href="<?php echo $item->link ?>">
                                    <?php echo JText::_("LNG_VIEW_DETAILS")?>
                                 </a>
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


