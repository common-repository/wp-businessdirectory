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

require_once BD_CLASSES_PATH . '/attributes/attributeservice.php';
?>

<div class="jbd-container listings<?php echo $moduleclass_sfx; ?> jbd-grid-container ">
    <?php $index = 0;?>
    <div class="row">
        <?php if(!empty($items)){?>
       		<?php foreach ($items as $item) { ?>
        		<?php $index ++; ?>
        		
        		<div class="<?php echo $span?> my-3">
            		<div class="jitem-card"">
            			 <?php if(!empty($item->review_score) && $appSettings->enable_ratings){ ?>
                			<div class="jitem-text-wrap">
    							<?php echo $item->review_score ?> / 5
    						</div>
    					<?php } ?>
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
                				<div class="jitem-title mb-2">
                					<a class="item-name <?php echo $campaignCallClass; ?>" data-companyId="<?php echo $item->id ?>" <?php echo $newTab; ?> href="<?php echo $item->link ?>" >
                                    	<?php echo $item->name; ?>
                                	</a>
                				</div>
                				<div class="jitem-desc">
                					<div class="jitem-desc-content">
        								<p>
                                            <?php
                                                if(!empty($item->slogan)) {
                                                    echo $item->slogan;
                                                }
                                            ?>
                                        </p>
                                        
                                        <?php  
                                        	$address = JBusinessUtil::getShortAddress($item);
                                        	if($showLocation && !empty($address)) {?>
                    							<p><i class="icon map-marker"></i><?php echo $address; ?></p>
    	                                <?php } ?>
    	                                <?php if(!empty($item->phone)) { ?>
        									<p><i class="icon phone"></i><?php echo $item->phone ?></p>
        								<?php }?>

										<?php if (!empty($item->customAttributes)) {?>
											<div class="listing-custom-attributes">
												<?php
													$renderedContent = AttributeService::renderAttributesFront($item->customAttributes, $appSettings->enable_packages, $item->package, true);
													echo $renderedContent;
												?>
											</div>
										<?php } ?>
                					</div>
                				</div>
								<div class="jitem-info" style="margin-top:15px;">
									<?php if(!empty($item->mainCategory)) { ?>
										<?php $attributes = "bg-dark rounded-circle p-2"; ?>
											<div class="jitem-icon-box">
												<a href="<?php echo $item->mainCategoryLink .$geoLocationParams ?>">
													<?php if((!empty($item->mainCategoryIcon) && $item->mainCategoryIcon!='None') || !empty($item->categoryIconImage)) { 
														echo JBusinessUtil::renderCategoryIcon($item->mainCategoryIcon, $item->categoryIconImage, $attributes); 
													} ?> 
													<?php echo $item->mainCategory ?>
												</a>
											</div>
									<?php } ?>
									<?php
										if ($appSettings->show_open_status  && ($item->enableWorkingStatus || $item->opening_status != COMPANY_OPEN_BY_TIMETABLE)) {
											if ($item->enableWorkingStatus) { 
												if ($item->workingStatus) { ?>
													<div class="badge badge-success"><span><?php echo JText::_("LNG_OPEN") ?></span></div>
												<?php } else { ?>
													<div class="badge badge-danger"><span><?php echo JText::_("LNG_CLOSED") ?></span></div>
												<?php } ?>
										<?php } else {
												$statusInfo = JBusinessUtil::getOpeningStatus($item->opening_status); ?>
												<div class="badge badge-<?php echo $statusInfo->class ?>"><span><?php echo $statusInfo->status ?></span></div>
										<?php } ?>
									<?php } ?>
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