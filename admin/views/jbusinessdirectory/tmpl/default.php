<?php 
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>

<div id="jbd-container" class="jbd-container jdb-dashboard">
    <span class="jbd-app-messages"></span>
	<div class="row justify-content-center">

        <?php if (!empty($this->databaseDifferences) && $this->appSettings->last_schema_check_version != $this->schemaVersion) {?>
            <div class="col-12">
                <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory');?>" method="post" name="dbForm" id="dbForm" >
                    <div class="card jbox-card border border-bottom-warning">
                        <div class="jbox-header">
                            <h5><?php echo JText::_("LNG_DATABASE_OUT_OF_SYNC");?></h5>
                        </div>
                        <div class="jbox-body d-flex pt-0" style="min-height: 0">
                            <div>
                                <small><?php echo JText::_("LNG_DATABASE_OUT_OF_SYNC_DESC");?></small>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary"><?php echo JText::_("LNG_UPDATE_DATABASE");?></button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                    <input type="hidden" name="task" id="task" value="database.fix" />
                    <?php echo JHTML::_( 'form.token' ); ?>
                </form>
            </div>
        <?php } ?>           

		<div class="col-6 col-lg-2">
			<div class="card jbox-card h-100 border">
				<div class="jbox-header">
                    <h5><?php echo JText::_("LNG_BUSINESS_LISTINGS");?></h5>
				</div>
				<div class="jbox-body">
                    <div>
                        <h1 class="mb-2"><?php echo number_format((float)$this->statistics->totalListings,0) ?></h1>
                        <small><?php echo JText::_("LNG_THIS_MONTH");?></small>
                    </div>
                    <div class="stat-percent text-success">
						<?php echo number_format((float)$this->statistics->month) ?>
                    </div>
                </div>
			</div>
		</div>
		<div class="col-6 col-lg-2">
			<div class="card jbox-card h-100">
				<div class="jbox-header">
                    <h5><?php echo JText::_("LNG_OFFERS");?></h5>
				</div>
				<div class="jbox-body">
                    <div>
                        <h1 class="mb-2"><?php echo number_format((float)$this->statistics->totalOffers,0) ?></h1>
                        <small><?php echo JText::_("LNG_ACTIVE");?></small>
                    </div>
                    <div class="stat-percent text-success">
						<?php echo $this->statistics->totalOffers>0 ? round($this->statistics->activeOffers*100/$this->statistics->totalOffers,2):0 ?>%
                    </div>
                </div>
			</div>
		</div>
		<div class="col-6 col-lg-2">
			<div class="card jbox-card h-100">
				<div class="jbox-header">
                    <h5><?php echo JText::_("LNG_EVENTS");?></h5>
					<span class="dir-label dir-label-primary pull-right"></span>
				</div>
				<div class="jbox-body">
                    <div>
                        <h1 class="mb-2"><?php echo number_format((float)$this->statistics->totalEvents) ?></h1>
                        <small><?php echo JText::_("LNG_ACTIVE");?></small>
                    </div>
                    <div class="stat-percent text-success">
						<?php echo $this->statistics->totalEvents>0 ? round($this->statistics->activeEvents*100/$this->statistics->totalEvents,2):0 ?>%
                    </div>
				</div>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="card jbox-card h-100">
				<div class="jbox-header">
                    <h5><?php echo JText::_("LNG_INCOME");?></h5>
				</div>
				<div class="jbox-body">
                    <div>
                        <h1 class="mb-2"><?php echo number_format((float)$this->income->total) ?></h1>
                        <small><?php echo JText::_("LNG_THIS_MONTH");?></small>
                    </div>
                    <div class="stat-percent text-success">
						<?php echo number_format((float)$this->income->month) ?>
                    </div>
				</div>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="card jbox-card jbox-card-style-center h-100">
				<div class="jbox-header">
					<h5><?php echo JText::_("LNG_VERSION_STATUS");?></h5>
				</div>
				<div class="jbox-body">
					<div id="update-status">
						<img class="loading" src="<?php echo BD_ASSETS_FOLDER_PATH."images/loader.gif"?>" />
					</div>
					<div class="">
						<div class="mt-2">
							<div class="stat-percent"> <span class="dir-label dir-label-info" id="current-version"><?php echo JBusinessUtil::getCurrentVersion()?></span> </div>
							<span class="mr-1"><?php echo JText::_("LNG_EXTENSION_VERSION");?></span>
						</div>
						<div class="d-none" id="update-version-holder">
							<div class="stat-percent">
                                <small><?php echo JText::_("LNG_UPDATE_VERSION");?></small>
                                <span class="badge badge-primary" id="update-version"></span>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="row">
		<div class="col-lg-6">
            <div class="card-content row">
                <div class="col-12 pt-0">
                       <div class="card jbox-card">
                           <div class="jbox-header">
                               <h2><?php echo JText::_("LNG_TOTAL_VIEWS")?></h2> <h3 class="btn-secondary jbtn-round ml-2 px-3 py-1"><?php echo number_format((float)$this->statistics->totalViews) ?></h3>
                           </div>
                           <div class="jbox-body">
                                <ul class="stat-list row justify-content-around w-100 py-4">
                                    <li class="col pl-0">
                                        <h2 class="pb-2 m-0"><?php echo number_format((float)$this->statistics->listingsTotalViews) ?></h2> <?php echo JText::_("LNG_BUSINESS_LISTING_VIEWS")?>
                                        <div class="stat-percent">
                                            <?php echo $this->statistics->totalViews> 0 ? round($this->statistics->listingsTotalViews * 100/$this->statistics->totalViews): 0 ?>%
                                        </div>
                                        <div class="dir-progress progress-mini">
                                            <div class="dir-progress-bar" style="width: <?php echo  $this->statistics->totalViews> 0 ? round($this->statistics->listingsTotalViews * 100/$this->statistics->totalViews) : 0?>%;"></div>
                                        </div>
                                    </li>
                                    <li class="col">
                                        <h2 class="pb-2 m-0 "><?php echo number_format((float)$this->statistics->offersTotalViews) ?></h2><?php echo JText::_("LNG_OFFER_VIEWS")?>
                                        <div class="stat-percent">
                                            <?php echo $this->statistics->totalViews > 0 ? round($this->statistics->offersTotalViews * 100/$this->statistics->totalViews): 0?>%
                                        </div>
                                        <div class="dir-progress progress-mini">
                                            <div class="dir-progress-bar" style="width: <?php echo $this->statistics->totalViews>0 ? round($this->statistics->offersTotalViews * 100/$this->statistics->totalViews) : 0?>%;"></div>
                                        </div>
                                    </li>
                                    <li class="col">
                                        <h2 class="pb-2 m-0"><?php echo number_format((float)$this->statistics->eventsTotalViews) ?></h2><?php echo JText::_("LNG_EVENT_VIEWS")?>
                                        <div class="stat-percent">
                                            <?php echo $this->statistics->totalViews > 0 ?round($this->statistics->eventsTotalViews * 100/$this->statistics->totalViews): 0?>%
                                        </div>
                                        <div class="dir-progress progress-mini">
                                            <div class="dir-progress-bar" style="width: <?php echo $this->statistics->totalViews > 0 ? round($this->statistics->eventsTotalViews * 100/$this->statistics->totalViews): 0?>%;"></div>
                                        </div>
                                    </li>
                                </ul>
                           </div>
                       </div>
                   </div>
					<?php $days_ago = 70; ?>
                    <?php 
						$date = JFactory::getDate(strtotime($days_ago.' days ago'));
						$time = $date->format('Y-m-d'); 
					?>
                <div class="col-12 pb-0" id="dir-dashboard-calendar-form">
					<div class="card jbox-card container-fluid  py-5">
						<div class="w-100 py-2" id="tabs">
								<div id="dir-dashboard-tabs" class="row">
									<div class="col-12 order-2" id="dir-dashboard-tabs-col">
										<ul class="d-flex justify-content-center">
											<li><a href="#newCompaniesAjax"><?php echo JText::_("LNG_BUSINESS_LISTINGS");?></a></li>
                                            <?php if($this->appSettings->enable_offers) { ?>
											    <li><a href="#newOffersAjax"><?php echo JText::_("LNG_OFFERS");?></a></li>
                                            <?php } ?>
                                            <?php if($this->appSettings->enable_events) { ?>
											    <li><a href="#newEventsAjax"><?php echo JText::_("LNG_EVENTS");?></a></li>
                                            <?php } ?>
											<li><a href="#incomeAjax"><?php echo JText::_("LNG_INCOME");?></a></li>
										</ul>
									</div>
									<div class="col-12 order-1" id="dir-dashboard-tabs-col">
											<div class="detail_box d-flex justify-content-center align-items-center py-2">
                                                <div class="has-jicon-left">
                                                    <input type='text' class="inputbox calendar-date front-calendar" style="height: auto; margin-bottom: auto;" name='startEndDate' id="startEndDate" placeholder="<?php echo JText::_("LNG_PICK_A_DATE")?>">
                                                    <input type='hidden' name='dateRange' id="dateRange" >
                                                    <i class="la la-calendar"></i>
                                                </div>
											</div>
										<div class="clear"></div>
									</div>
								</div>
								<div id="newCompaniesAjax">
									<div id="graph"></div>
								</div>
								<div id="newOffersAjax">
								</div>
								<div id="newEventsAjax">
								</div>
								<div id="incomeAjax">
								</div>
						</div>
					</div>
                   </div>
		    </div>
            <div class="custom-banner pt-4">
                <a href="https://www.cmsjunkie.com/services"><img src="<?php echo BD_PICTURES_PATH.'/custom-work-banner.jpg'?>" title="CMSjunkie Custom Work"/></a>
            </div> 
        </div>
        <div class="col-lg-6">
            <div class="card jbox-card h-100">
                <div class="jbox-header">
                    <h4><?php echo JText::_('LNG_DIRECTORY_APPS') ?>
                        <a style="float:right;text-decoration:none;cursor:pointer;"
                           href="http://cmsjunkie.com/docs/jbusinessdirectory/index.html" target="_blank">
                            <i class="ml-2 la la-info-circle la-lg"></i>
                        </a>
                    </h4>
                </div>
                <div class="jbox-body">
                    <div class="jbd-apps-container row">
                        <?php foreach($this->directoryApps as $app) { ?>
                            <?php 
                                if($app->name == "JBD Mollie Subscriptions"){
                                    continue;
                                }
                            ?>
                            <div class="col-xl-4 col-lg-6 col-sm-4 col-12">
                                <div class="jbd-app">
                                    <div class="pb-2">
                                        <img src="<?php echo BD_ASSETS_FOLDER_PATH."images/".$app->icon ?>"
                                            class="rounded-circle" style="width:45px;height:45px;" data-toggle="tooltip" title="<?php echo $app->description ?>"/>
                                    </div>
                                    <div class="pb-2">
                                        <span data-toggle="tooltip" title="<?php echo $app->description ?>"> <?php echo $app->name ?></span>
                                    </div>
                                    <div class="nowrap">
                                        <?php if ($this->appStatuses[$app->id] != DIRECTORY_APP_UNINSTALLED) { ?>
                                            <?php if ($this->appStatuses[$app->id] == DIRECTORY_APP_UPDATE) { ?>
                                                <div class="badge badge-pill badge-warning" data-toggle="tooltip" title="<?php echo JText::_('LNG_NEWER_VERSION_AVAILABLE') ?>">
                                                    <i class="la la-warning"></i>
                                                </div>
                                            <?php }else{ ?>
                                                <div class="badge badge-pill badge-success" data-toggle="tooltip" title="<?php echo JText::_('LNG_INSTALLED') ?>">
                                                    <i class="la la-check-circle"></i>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <div class="badge badge-pill badge-light" data-toggle="tooltip" title="<?php echo JText::_('LNG_NOT_INSTALLED') ?>">
                                                <i class="la la-ban"></i>
                                            </div>
                                        <?php } ?>
                                    
                                        <a class="badge badge-pill badge-primary " data-toggle="tooltip" title="<?php echo JText::_('LNG_STORE') ?>"
                                        target="_blank" href="<?php echo $app->store_link ?>">
                                            <i class="la la-shopping-cart"></i>
                                        </a>
                                        <a class="badge badge-pill badge-info" data-toggle="tooltip" title="<?php echo JText::_('LNG_MANUAL') ?>"
                                        target="_blank" href="<?php echo $app->doc_link ?>">
                                            <i class="la la-book"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="d-flex justify-content-center py-2 w-100">
                        <a href="javascript:void(0)" onclick="installApp()" class="btn btn-success"><?php echo JText::_('LNG_INSTALL') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-12 col-lg-3">
            <div class="card jbox-card mb-2">
                <div class="jbox-header justify-content-left">
                    <h4><?php echo JText::_('LNG_STATUS_CHECK') ?>
                        <a style="float:right;text-decoration:none;cursor:pointer;"
                           href="http://cmsjunkie.com/docs/jbusinessdirectory/index.html" target="_blank">
                            <i class="ml-2 la la-info-circle la-lg"></i>
                        </a>
                    </h4>
                </div>
                <div class="jbox-body">
					<?php foreach($this->actions as $action) { ?>
                        <div class="d-flex w-100 py-2">
                            <div class="d-flex align-items-center px-2">
                                <i class='la la-fw la-<?php echo $action->status?'check-circle text-success':'warning text-warning'; ?> la-3x'></i>
                            </div>
                            <div class="d-flex align-items-center">
                                <a style="text-decoration:none;" href="<?php echo $action->link ?>" target="_blank">
                                    <p class="m-0"><?php echo $action->text ?></p>
                                </a>
                            </div>
                        </div>
					<?php } ?>
                </div>
            </div>

            <div class="card jbox-card mb-2">
                <div class="jbox-header">
                    <h5>Custom Services</h5>
                </div>
                <div class="jbox-body">
                    <p>
                        We do offer <strong>custom development</strong>. If you are
                        interested to contract us to perform some customizations, please
                        feel free to <a href="http://www.cmsjunkie.com/contacts/" title="Contact CMS Junkie">contact us</a>!
                    </p>
                </div>
            </div>
            <div class="card jbox-card mb-2">
                <div class="jbox-header">
                    <h5>Support & Documentation</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"> <i class="la la-angle-up la-fw"></i></a>
                        <a class="close-link"> <i class="la la-close"></i></a>
                    </div>
                </div>
                <div class="jbox-body">
                    <div class="d-flex w-100 pb-2">
                        <div class="d-flex align-items-center px-2">
                            <i class="la la-life-bouy la-3x text-info la-fw"></i>
                        </div>
                        <div>
                            <a href="http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1">Community forum</a>
                            <p class="m-0">Get in touch with our community to find the best solutions</p>
                        </div>
                    </div>
                    <div class="d-flex w-100 py-2">
                        <div class="d-flex align-items-center px-2">
                            <i class="la la-book la-3x text-success la-fw"></i>
                        </div>
                        <div>
                            <a href="http://www.cmsjunkie.com/docs/jbusinessdirectory/businessdiradmin.html">Online documentation</a>
                            <p class="m-0">Find details about the extension features & functionality</p>
                        </div>
                    </div>
                    <div class="d-flex w-100 py-2">
                        <div class="d-flex align-items-center px-2">
                            <i class="la la-ticket la-3x text-warning la-fw"></i>
                        </div>
                        <div>
                            <a href="https://www.cmsjunkie.com/helpdesk/customer/index/">Support Ticket</a>
                            <p class="m-0">Could not found a solution to your issue? Post a ticket.</p>
                        </div>
                    </div>
                    <div class="d-flex w-100 pt-2">
                        <div class="d-flex align-items-center px-2">
                            <i class="la la-stack-exchange la-3x text-primary la-fw"></i>
                        </div>
                        <div>
                            <a href="http://www.cmsjunkie.com/contacts/">Contact us</a>
                            <p class="m-0">Post a sales question</p>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<div class="col-12 col-lg-3">
        <?php if ($this->appSettings->enable_item_moderation){
                if (!empty($this->pendingListings)){ ?>
                    <div class="card jbox-card mb-2">
                        <div class="jbox-header justify-content-left">
                            <h4><?php echo JText::_('LNG_PENDING_LISTINGS') ?></h4>
                        </div>
                        <div class="jbox-body">
                            <?php foreach($this->pendingListings as $key => $listing) {
                                if ($key==5){
                                    break;
                                }
                                ?>
                                <div class="d-flex w-100 py-2">
                                    <div class="d-flex align-items-center px-2">
                                        <i class='la la-fw la-exclamation-circle text-warning la-2x'></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a style="text-decoration:none;" href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.edit&id='. $listing->id )?>" target="_blank">
                                            <p class="m-0"><?php echo $listing->name ?></p>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (count($this->pendingListings)>TOTAL_PENDING_ITEMS_DISPLAYED){?>
                            	<div class="text-center w-100">
	                                <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&view=companies&filter_status_id='. COMPANY_STATUS_CREATED )?>" target="_blank">
	                                   <?php echo JText::_("LNG_VIEW_MORE")?>
	                                </a>
	                            </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } if (!empty($this->pendingClaimApproval)){ ?>
                    <div class="card jbox-card mb-2">
                        <div class="jbox-header justify-content-left">
                            <h4><?php echo JText::_('LNG_PENDING_CLAIM_APPROVAL') ?> </h4>
                        </div>
                        <div class="jbox-body">
				            <?php foreach($this->pendingClaimApproval as $key => $listing) {
					            if ($key==5){
						            break;
					            }
					            ?>
                                <div class="d-flex w-100 py-2">
                                    <div class="d-flex align-items-center px-2">
                                        <i class='la la-fw la-exclamation-circle text-warning la-2x'></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a style="text-decoration:none;" href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.edit&id='. $listing->id )?>" target="_blank">
                                            <p class="m-0"><?php echo $listing->name ?></p>
                                        </a>
                                    </div>
                                </div>
				            <?php } ?>
  			                <?php if (count($this->pendingClaimApproval)>TOTAL_PENDING_ITEMS_DISPLAYED){?>
  			                	<div class="text-center w-100">
	                                <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&view=companies&filter_status_id='. COMPANY_STATUS_CLAIMED )?>" target="_blank">
	                                    <?php echo JText::_("LNG_VIEW_MORE")?>
	                                </a>
	                            </div>
				            <?php } ?>
                        </div>
                    </div>
	            <?php }
                if (!empty($this->pendingOffers)){ ?>
                    <div class="card jbox-card mb-2">
                        <div class="jbox-header justify-content-left">
                            <h4><?php echo JText::_('LNG_PENDING_OFFERS') ?></h4>
                        </div>
                        <div class="jbox-body">
			                <?php foreach($this->pendingOffers as $key => $offer) {
				                if ($key==5){
					                break;
				                }
				                ?>
                                <div class="d-flex w-100 py-2">
                                    <div class="d-flex align-items-center px-2">
                                        <i class='la la-fw la-exclamation-circle text-warning la-2x'></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a style="text-decoration:none;" href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=offer.edit&id='. $offer->id )?>" target="_blank">
                                            <p class="m-0"><?php echo $offer->subject ?></p>
                                        </a>
                                    </div>
                                </div>
			                <?php } ?>
			                <?php if (count($this->pendingOffers)>TOTAL_PENDING_ITEMS_DISPLAYED){?>
			                	<div class="text-center w-100">
	                                <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&view=offers&filter_status_id=0')?>" target="_blank">
	                                    <?php echo JText::_("LNG_VIEW_MORE")?>
	                                </a>
                                </div>
			                <?php } ?>
                        </div>
                    </div>
                <?php }
                if (!empty($this->pendingEvents)){ ?>
                    <div class="card jbox-card mb-2">
                        <div class="jbox-header justify-content-left">
                            <h4><?php echo JText::_('LNG_PENDING_EVENTS') ?></h4>
                        </div>
                        <div class="jbox-body">
			                <?php foreach($this->pendingEvents as $key => $event) {
				                if ($key==5){
					                break;
				                }
				                ?>
                                <div class="d-flex w-100 py-2">
                                    <div class="d-flex align-items-center px-2">
                                        <i class='la la-fw la-exclamation-circle text-warning la-2x'></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a style="text-decoration:none;" href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=event.edit&id='. $event->id )?>" target="_blank">
                                            <p class="m-0"><?php echo $event->name ?></p>
                                        </a>
                                    </div>
                                </div>
			                <?php } ?>
			                <?php if (count($this->pendingEvents)>TOTAL_PENDING_ITEMS_DISPLAYED){?>
			                	<div class="text-center w-100">
	                                <a  href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&view=events&filter_status_id=0')?>" target="_blank">
	                                    <?php echo JText::_("LNG_VIEW_MORE")?>
	                                </a>
	                            </div>
			                <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php }
            if (!empty($this->pendingReviews)){ ?>
                <div class="card jbox-card mb-2">
                    <div class="jbox-header justify-content-left">
                        <h4><?php echo JText::_('LNG_PENDING_REVIEWS') ?></h4>
                    </div>
                    <div class="jbox-body">
			            <?php foreach($this->pendingReviews as $key => $review) {
				            if ($key==5){
					            break;
				            }
				            ?>
                            <div class="d-flex w-100 py-2">
                                <div class="d-flex align-items-center px-2">
                                    <i class='la la-fw la-exclamation-circle text-warning la-2x'></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a style="text-decoration:none;" href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=review.edit&id='. $review->id )?>" target="_blank">
                                        <p class="m-0"><?php echo JBUsinessUtil::truncate( $review->name ." - ".$review->subject, 55) ?></p>
                                    </a>
                                </div>
                            </div>
                           
			            <?php } ?>
			            <?php if (count($this->pendingReviews)>TOTAL_PENDING_ITEMS_DISPLAYED){?>
			            	<div class="text-center w-100">
	                            <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&view=reviews&filter_status_id='.REVIEW_STATUS_CREATED)?>" target="_blank">
	                                <?php echo JText::_("LNG_VIEW_MORE")?>
	                            </a>
                            </div>
			            <?php } ?>
                    </div>
                </div>
            <?php } ?>
		</div>
		<div class="col-12 col-lg-6">
            <div class="card jbox-card mb-2">
				<div class="jbox-header">
					<h5>Latest news</h5>
					<div class="card-tools">
                        <a class="collapse-link"> <i class="la la-angle-up"></i></a>
                        <a class="close-link"> <i class="la la-close"></i></a>
					</div>
				</div>
				<div class="jbox-body">
					<div class="feed-activity-list">
						<?php if(!empty($this->news)){?>
							<?php foreach($this->news as $news) { ?>
								<div class="feed-element">
									<div>
										<small class="pull-right text-navy"><?php echo  $news->publish_ago; ?></small> 
										<?php 
											if($news->new) { ?>
											<span class="dir-label dir-label-warning pull-left"><?php echo JText::_("LNG_NEW")?></span>&nbsp;
										<?php } ?>
										<a target="_blank" href="<?php echo $news->link; ?>">
											<strong><?php echo $news->title; ?></strong>
										</a>
										<div><?php echo $news->description; ?></div>
										<small class="text-muted"><?php echo $news->publishDateS; ?></small>
									</div>
								</div>
							<?php } ?>
						<?php }else{ ?>
							<p>
								<?php echo JText::_("LNG_RETRIEVING_REFRESH_PAGE");?>
							</p>
						<?php } ?>
						<a href="https://www.cmsjunkie.com/news" target="_blank" class="pull-right btn btn-info btn-sm mt-2"><?php echo JText::_("LNG_VIEW_ALL_NEWS")?></a>
					</div>
				</div>
			</div>
            
			<div class="card jbox-card mb-2">
				<div class="jbox-header">
					<h5>About CMS Junkie</h5>
					<div class="card-tools">
                        <a class="collapse-link"> <i class="la la-angle-up"></i></a>
                        <a class="close-link"> <i class="la la-close"></i></a>
					</div>
				</div>
				<div class="jbox-body">
					<p>
						CMSJunkie offers <strong>top quality</strong> commercial CMS products: extensions,
						templates, themes, modules for open sources content management
						systems. All products are completely customizable and ready to be
						used as a basis for a clean and high-quality website. We are now
						working with following CMS systems: Magento, Wordpress, JBD. <br />
					</p>
					<p>The CMSJunkie Store team can answer your questions about
						purchasing, usage of our products, returns, and more. Our aim is to
						<strong> keep every one of our customers happy</strong> and we are not just saying
						that. We understand the importance of deadlines to our clients and
						we deliver on time and keep everything on schedule.</p>
				</div>
            </div>
            <div class="card jbox-card">
                <div class="jbox-header">
                    <h5>Connect with us</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"> <i class="la la-angle-up"></i></a>
                        <a class="close-link"> <i class="la la-close"></i></a>
                    </div>
                </div>
                <div class="jbox-body jsocial-icons">
                    <a target="social" href="http://twitter.com/cmsjunkie" class="la la-twitter la-3x"></a>
                    <a target="social" href="http://facebook.com/cmsjunkie" class="la la-thumbs-up la-3x"></a>
                    <a href="mailto:info@cmsjunkie.com" class="la la-at la-3x"></a>
                </div>
            </div>

		</div>
	</div>
    <div class="row">
        <div class="col-lg-4 ">

        </div>
    </div>
</div>



<div id="install-dialog" class="jbd-container" style="display: none">    
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INSTALL') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
        <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post" enctype="multipart/form-data">
            <div class="custom-file">
                <label class="custom-file-label w-100" for="install_package"><?php echo JText::_('LNG_PLEASE_CHOOSE_A_FILE'); ?></label>
                <input type="file" class="d-none custom-file-input" name="install_package" id="install_package">
            </div>

            <div class="dropzone dropzone-previews container-fluid" id="file-upload">
                <div id="actions" style="margin-left:-15px;" class="row">
                    <div class="col d-flex justify-content-center">
                        <!-- The fileinput-button span is used to style the file input field as button -->
                        <span class="btn btn-success fileinput-button dz-clickable mr-1">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span><?php echo JText::_('LNG_ADD_FILES'); ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-1">
                <button type="submit" value="<?php echo JText::_('LNG_INSTALL'); ?>" id="install_btn" class="btn btn-primary"><?php echo JText::_('LNG_INSTALL'); ?></button>
            </div>

            <input type="hidden" name="view" id="view" value="jbusinessdirectory" />
            <input type="hidden" name="task" id="task" value="jbusinessdirectory.installApp" />
        </form>                       
    </div>
</div>


<script type="text/javascript">
    var chart;
    window.addEventListener('load', function() {
        jQuery(function() {
            var start = moment().subtract(29, 'days');
            var end = moment();

            jQuery('#startEndDate').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    "<?php echo JText::_("LNG_TODAY")?>": [moment(), moment()],
                    "<?php echo JText::_("LNG_TOMORROW")?>": [moment().add(1, 'days'), moment().add(1, 'days')],
                    "<?php echo JText::_("LNG_NEXT_7_DAYS")?>": [moment().add(1, 'days'), moment().add(7, 'days')],
                    "<?php echo JText::_("LNG_NEXT_30_DAYS")?>": [moment().add(1, 'days'), moment().add(30, 'days')],
                    "<?php echo JText::_("LNG_THIS_MONTH")?>": [moment(), moment().endOf('month')]
                },
                locale:{
            		applyLabel: "<?php echo JText::_("LNG_APPLY")?>",
            		cancelLabel: "<?php echo JText::_("LNG_CANCEL")?>",
            		fromLabel: "<?php echo JText::_("LNG_FROM")?>",
            		toLabel: "<?php echo JText::_("LNG_TO")?>",
            		customRangeLabel: "<?php echo JText::_("LNG_CUSTOM_RANGE")?>"
                },
                autoUpdateInput: false
            });


            jQuery('#dateRange').val(start.format('DD-MM-YYYY') + ':' + end.format('DD-MM-YYYY'));
            jQuery('#startEndDate').val(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));

            jQuery('input[name="startEndDate"]').on('apply.daterangepicker', function(ev, picker) {
                jQuery(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                jQuery('#dateRange').val(picker.startDate.format('DD-MM-YYYY') + ':' + picker.endDate.format('DD-MM-YYYY'));
                calendarChange();
            });

            jQuery("#file-upload").dropzone({ 
                acceptedFiles: ".zip,.rar",
                url: jbdUtils.getAjaxUrl('installApp', 'jbusinessdirectory'),
                clickable: ".fileinput-button",
                dictDefaultLanguage: '<?php echo JText::_('LNG_DRAG_N_DROP',true); ?>',
                autoProcessQueue: false,
                uploadMultiple: true,
                addRemoveLinks: true,
                parallelUploads: 10,
                init: function() {
                    let myDropzone = this;
                    jQuery("#install_btn").click(function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        myDropzone.processQueue();
                    });
                },
                success: function (file, response) {
                    jQuery.jbdModal.close();

                    for (let i in response) {
                        let html = '';
                        let css = "alert-success";

                        if (response[i].status == 0) {
                            css = "alert-danger";
                        }

                        if (jQuery('div[id="' + response[i].file.name + '"]').length == 0) {
                            html += '<div class="alert ' + css + ' " role="alert" id="' + response[i].file.name +'" >';
                            html +=     response[i].file.name + ": " + response[i].message;
                            html +=     '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                            html +=         '<span aria-hidden="true">&times;</span>';
                            html +=     '</button>'
                            html += '</div>';

                            jQuery('.jbd-app-messages').append(html);
                        }

                        this.removeAllFiles();
                    }
                }
            });
        });

        let urlArchiveStats = jbdUtils.getAjaxUrl('archiveStatisticsAjax', 'jbusinessdirectory');
        jQuery.ajax({
            type: "GET",
            url: urlArchiveStats,
            data: {isAjax:true},
            success: function () {
            }
        });

        let urlArchiveSearchLogs = jbdUtils.getAjaxUrl('archiveSearchLogsAjax', 'jbusinessdirectory');
        jQuery.ajax({
            type: "GET",
            url: urlArchiveSearchLogs,
            success: function () {
            }
        });

        chart = Morris.Area({
            element: 'graph',
            data: [{date: '<?php echo date("d-m-Y"); ?>', value: 0}],
            fillOpacity: 0.6,
            hideHover: 'auto',
            behaveLikeLine: true,
            resize: true,
            lineColors: ['#54cdb4'],
            xkey: 'date',
            ykeys: ['value'],
            labels: ['Total'],
            xLabelFormat: function(d) {
                return jbdUtils.getDateWithFormat(d);
            },
            dateFormat: function(unixTime) {
                var d = new Date(unixTime);
                return jbdUtils.getDateWithFormat(d);
            }
        });

      //retrieve current version status;
        let versionCheckTask = jbdUtils.getAjaxUrl('getVersionStatusAjax', 'updates');
    	jQuery.ajax({
    		url: versionCheckTask,
    		dataType: 'json',
    		type: 'GET',
    		success: function(data){
    				
                    if(jbdUtils.compareVersions(data.currentVersion,data.updateVersion)){
                 	  	jQuery("#update-status").html("<span class='text-success'><?php echo JText::_("LNG_UP_TO_DATE")?></span>");	
                    }else{
                    	jQuery("#update-status").html("<span class='text-danger'><?php echo JText::_("LNG_OUT_OF_DATE")?></span>");	
                    	jQuery("#update-version").html(data.updateVersion);
                 	  	jQuery("#update-version-holder").show();
                 	  	jQuery("#current-version").removeClass("dir-label-info");
                 	  	jQuery("#current-version").addClass("dir-label-warning");
                    }

                    if(data.message && data.message.indexOf("Please enter your order details")>0){
                    	jQuery("#update-status").html(data.message);
                    }  	
            }
    	});

		setTimeout(function(){
    	        var dateRange = jQuery("#dateRange").val();
    	        var data = dateRange.split(":");
    	        var start_date = data[0];
    	        var end_date = data[1];

    	        let urlReport = jbdUtils.getAjaxUrl('newCompaniesAjax', 'jbusinessdirectory');
    	        requestData(urlReport, start_date, end_date, chart);
    	    }, 500);

        jQuery("#tabs").tabs();


        jQuery("#tabs").click(function(e) {
            e.preventDefault();
            calendarChange();
        });

        jQuery("#start_date, #end_date").bind("paste keyup", function(e) {
            e.preventDefault();
            calendarChange();
        });


        var curTab = jQuery("#tabs").tabs('option', 'active');

        let urlNews = jbdUtils.getAjaxUrl('getLatestServerNewsAjax', 'jbusinessdirectory');
        let urlReport = jbdUtils.getAjaxUrl('newCompaniesAjax', 'jbusinessdirectory');

        //retrieve the latest news
        jQuery.ajax({
            url: urlNews,
            type: 'GET'
        });

        jQuery("#install_package").change(function(){
            jQuery('.custom-file-label').text(this.value.split("\\").pop());
        });
    });

	function requestData(urlReport, start_date, end_date, chart) {
		jQuery.ajax({
			url: urlReport,
			dataType: 'json',
			type: 'GET',
			data: { start_date: start_date, end_date: end_date },
		})
		.done(function(data) {
			//console.log(JSON.stringify(data));
			chart.setData(data);
		})
		.fail(function(data) {
			console.log("Error");
			console.log(JSON.stringify(data));
		});
	}

	function calendarChange() {
		var curTab = jQuery("#tabs .ui-tabs-panel:visible").attr("id");
        var dateRange = jQuery("#dateRange").val();
        var data = dateRange.split(":");
        var start_date = data[0];
        var end_date = data[1];
        let urlReport = jbdUtils.getAjaxUrl(curTab, 'jbusinessdirectory');
		jQuery("#graph").appendTo("#"+curTab);
		requestData(urlReport, start_date, end_date, chart);
	}

    function installApp(){
    	jQuery('#install-dialog').jbdModal();
    }
</script>