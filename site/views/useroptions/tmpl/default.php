<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();

$title = JText::_("LNG_CONTROL_PANEL").' | '.$config->sitename;
JBusinessUtil::setMetaData($title, "", "", true);
$menuItemId = JBusinessUtil::getActiveMenuItem();

JBusinessUtil::checkPermissions("directory.access.controlpanel", "useroptions");

$appSettings =  JBusinessUtil::getApplicationSettings();
$enablePackages = $appSettings->enable_packages;
$enableOffers = $appSettings->enable_offers;
$hasBusiness = isset($this->companies) && count($this->companies)>0;
?>

<style>
#content-wrapper{
	margin: 20px;
	padding: 0px;
}

.tooltip {
	border-style:none !important;
}
</style>

<div id="jbd-container" class="jbd-container jdb-dashboard">
    <div id="user-options">
    	<?php if($this->actions->get('directory.access.controlpanel') || !$appSettings->front_end_acl){ ?>
    
    		<div class="row">
    			<?php if($this->actions->get('directory.access.listings')|| !$appSettings->front_end_acl){?>
    				<div class="col-lg">
    					<div class="card jbox-card h-100 border clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies') ?>')">
    						<div class="jbox-header">
    							<p><?php echo JTEXT::_("LNG_ADD_MODIFY_COMPANY_DATA") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_ADD_MODIFY_COMPANY_DATA_INFO") ?></span>
								</p>
    							<div class="badge transparent"><i class="la la-4x la-tasks text-success"></i></div>
    						</div>
    						<div class="jbox-body pb-0">
                                <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_LISTINGS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->totalListings ?></h4>
                                </div>
                                <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4><?php echo $this->statistics->listingsTotalViews?></h4>
                                </div>
                            </div>
    					</div>
    				</div>
    			<?php }?>
    			
    			<?php if($enableOffers && ($this->actions->get('directory.access.offers')|| !$appSettings->front_end_acl) && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/offers.php')){?>
    				<div class="col-lg">
    					<div class="card jbox-card h-100 border clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers') ?>')">
    						<div class="jbox-header">
    							<p>
    								<?php echo JTEXT::_("LNG_ADD_MODIFY_OFFERS") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_ADD_MODIFY_OFFERS_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-certificate text-info"></i></div>
    						</div>
    						<div class="jbox-body pb-0">
                                <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_OFFERS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->totalOffers ?></h4>
                                </div>
                                <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4><?php echo $this->statistics->offersTotalViews?></h4>
                                </div>
    						</div>
    					</div>
    				</div>
    			<?php }?>
    			
    			<?php if($appSettings->enable_events && ($this->actions->get('directory.access.events')|| !$appSettings->front_end_acl) && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/events.php')){?>		
    				<div class="col-lg">
    					<div class="card jbox-card h-100 border clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents') ?>')">
    						<div class="jbox-header">
    							<p>
    								<?php echo JTEXT::_("LNG_MANAGE_YOUR_EVENTS") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_EVENTS_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-calendar text-warning"></i></div>
    						</div>
    						<div class="jbox-body pb-0">
    							 <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_EVENTS")?></small>
                                    <h4 class="text-success"><?php echo $this->statistics->totalEvents ?></h4>
                                </div>
                                <div>
                                    <small class="stats-label"><?php echo JText::_("LNG_VIEW_NUMBER")?></small>
                                    <h4><?php echo $this->statistics->eventsTotalViews?></h4>
                                </div>
    						</div>
    					</div>
    				</div>
    			<?php } ?>
    		</div>
    
    		<div class="row">
    			<div class="col-lg-8">
        			<div class="card jbox-card h-100">
        				<div class="jbox-body d-block pt-3">
							<div>
								<?php $days_ago = 70; ?>
								<?php 
								$date = JFactory::getDate(strtotime($days_ago.' days ago'));
								$time = $date->format('Y-m-d'); 
								?>
								<div id="dir-dashboard-calendar-form">
									<div class="detail_box d-flex justify-content-center align-items-center">
										<div class="has-jicon-left">
											<input type='text' class="inputbox calendar-date front-calendar" name='startEndDate' id="startEndDate" placeholder="<?php echo JText::_("LNG_PICK_A_DATE")?>">
											<input type='hidden' name='dateRange' id="dateRange" >
											<i class="la la-calendar"></i>
										</div>
										<div class="clear"></div>
									</div>
									<div class="row">
										<div class="col-12">
											<div id="tabs">
												<div id="dir-dashboard-tabs" class="row">
													<div class="col-md" id="dir-dashboard-tabs-col">
														<ul>
															<li id="tab-newCompaniesAjax" class="tab-item active" onclick=""><a href="javascript:showTab('newCompaniesAjax')"><?php echo JText::_("LNG_BUSINESS_LISTINGS");?></a></li>
															<?php if($enableOffers){?><li id="tab-newOffersAjax" class="tab-item"><a href="javascript:showTab('newOffersAjax')"><?php echo JText::_("LNG_OFFERS");?></a></li> <?php }?>
															<?php if($appSettings->enable_events){?><li id="tab-newEventsAjax" class="tab-item"><a href="javascript:showTab('newEventsAjax')"><?php echo JText::_("LNG_EVENTS");?></a></li> <?php }?>
														</ul>
														<div class="clear"></div>
													</div>
												</div>
												<div id="newCompaniesAjax" class="tab-content">
													<div id="jbd-graph" style="height: 250px;"> </div>
												</div>
												<div id="newOffersAjax" class="tab-content">
												</div>
												<div id="newEventsAjax" class="tab-content">
												</div>
											</div>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card jbox-card h-100">
        				<div class="jbox-body d-block pt-3">
							<div>
								<div class="total-statistics">
									<div class=""><?php echo JText::_("LNG_TOTAL_VIEWS")?></div>
									<h3 class="total py-5"><?php echo $this->statistics->totalViews?></h3>
								</div>
								
								<ul class="stat-list">
									<li>
										<h2 class="no-margins"><?php echo $this->statistics->listingsTotalViews ?></h2>
										<div>
											<?php echo JText::_("LNG_BUSINESS_LISTING_VIEWS")?>
											<span class="stat-percent">
												<?php echo $this->statistics->totalViews> 0 ? round($this->statistics->listingsTotalViews * 100/$this->statistics->totalViews): 0 ?>%
											</span>
										</div>
										<div class="dir-progress progress-mini">
											<div class="dir-progress-bar" style="width: <?php echo  $this->statistics->totalViews>0?round($this->statistics->listingsTotalViews * 100/$this->statistics->totalViews):0?>%;"></div>
										</div>
									</li>
									<?php if($enableOffers){?>
										<li>
											<h2 class="no-margins "><?php echo $this->statistics->offersTotalViews ?></h2>
											<div>
												<?php echo JText::_("LNG_OFFER_VIEWS")?>
												<span class="stat-percent">
													<?php echo $this->statistics->totalViews>0 ? round($this->statistics->offersTotalViews * 100/$this->statistics->totalViews): 0?>%
												</span>
											</div>
											<div class="dir-progress progress-mini">
												<div class="dir-progress-bar" style="width: <?php echo  $this->statistics->totalViews>0 ?round($this->statistics->offersTotalViews * 100/$this->statistics->totalViews):0?>%;"></div>
											</div>
										</li>
									<?php } ?>
									<?php if($appSettings->enable_events){?>
										<li>
											<h2 class="no-margins "><?php echo $this->statistics->eventsTotalViews ?></h2>
											<div>
												<?php echo JText::_("LNG_EVENT_VIEWS")?>
												<span class="stat-percent">
													<?php echo $this->statistics->totalViews > 0 ?round($this->statistics->eventsTotalViews * 100/$this->statistics->totalViews): 0?>%
												</span>
											</div>
											<div class="dir-progress progress-mini">
												<div class="dir-progress-bar" style="width: <?php echo $this->statistics->totalViews > 0 ? round($this->statistics->eventsTotalViews * 100/$this->statistics->totalViews): 0?>%;"></div>
											</div>
										</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
        		</div>
    		</div>    
    		<div class="row">
    			<?php if($appSettings->enable_packages && $this->actions->get('directory.access.orders')) { ?>
    				<div class="col-lg">
    					<div class="card jbox-card h-100 clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=billingoverview') ?>')">
    						<div class="jbox-header pb-3">
    							<p>
    								<?php echo JTEXT::_("LNG_MANAGE_YOUR_ORDERS") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_ORDERS_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-cog text-success"></i></div>
    						</div>
    					
    					</div>
    				</div>
    			<?php } ?>
    			
    			<?php if($appSettings->enable_bookmarks && ($this->actions->get('directory.access.bookmarks') || !$appSettings->front_end_acl)) { ?>
    				<div class="col-lg">
    					<div class="card jbox-card h-100 clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks') ?>')">
    						<div class="jbox-header pb-3">
    							<p>
    								<?php echo JTEXT::_("LNG_MANAGE_YOUR_BOOKMARKS") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_BOOKMARKS_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-bookmark text-danger"></i></div>
    						</div>
    						
    					</div>
    				</div>
    			<?php } ?>
    			
    			<?php if($appSettings->enable_packages){?>
    				<div class="col-lg">
    					<div class="card jbox-card h-100 clickable" onclick="openLink('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit') ?>')">
    						<div class="jbox-header pb-3">
    							<p>
    								<?php echo JTEXT::_("LNG_BILLING_DETAILS") ?>
    								<span class="d-block small pt-1"><?php echo JTEXT::_("LNG_BILLING_DETAILS_INFO") ?></span>
    							</p>
    							<div class="badge transparent"><i class="la la-4x la-list-alt text-primary"></i></div>
    						</div>
    						
    					</div>
    				</div>
    			<?php } ?>
    		</div>
    	<?php } else {
    			echo JText::_("LNG_NOT_AUTHORIZED");
    		}
    	?>
    </div>
</div>

<script>

	var chart = null;

    window.addEventListener('load', function() {
       
        var start = moment().subtract(29, 'days');
        var end = moment();

        chart = Morris.Area({
        	element: 'jbd-graph',
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


    	jQuery("#start_date, #end_date").bind("paste keyup change", function(e) {
    		calendarChange();
    	});

    	setTimeout(function(){
            var dateRange = jQuery("#dateRange").val();
            var data = dateRange.split(":");
            var start_date = data[0];
            var end_date = data[1];
            var urlReport = jbdUtils.getAjaxUrl('newCompaniesAjax', 'useroptions', 'useroptions');
            requestData(urlReport, start_date, end_date, chart);
        }, 500);
    });


    function openLink(link){
    	document.location.href=link;
    }
    
    function requestData(urlReport, start_date, end_date, chart) {
	
        
    	jQuery.ajax({
    		url: urlReport,
    		dataType: 'json',
    		type: 'GET',
    		data: { start_date: start_date, end_date: end_date }
    	})
    	.done(function(data) {
    		console.log(JSON.stringify(data));
    		chart.setData(data);
    	})
    	.fail(function(data) {
    		console.log("Error");
    		console.log(JSON.stringify(data));
    	});
    }

	function showTab(ident){

		jQuery(".tab-item").each(function(){
			jQuery(this).removeClass("active");
		});

		jQuery("#tab-"+ident).addClass("active");
		
		jQuery(".tab-content").each(function(){
			jQuery(this).hide();
		});

		jQuery("#"+ident).show();
		
		calendarChange();
	}
    
    function calendarChange() {
    	//console.debug("calendar change");
    	var curTab = jQuery("#tabs .tab-content:visible").attr("id");
    	var dateRange = jQuery("#dateRange").val();
        var data = dateRange.split(":");
        var start_date = data[0];
        var end_date = data[1];
    	var urlReport = jbdUtils.getAjaxUrl(curTab, 'useroptions', 'useroptions');
    	jQuery("#jbd-graph").appendTo("#"+curTab);
    	requestData(urlReport, start_date, end_date, chart);
    }

</script>
