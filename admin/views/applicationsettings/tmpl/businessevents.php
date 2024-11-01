<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<div class="app_tab" id="panel_9">

<div class="row panel_9_content">
	<div class="col-md-6 general-settings">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_EVENTS'); ?></legend>
            <div class="form-container">
    			<div class="control-group">
    				<div class="control-label"><label id="enable_events-lbl" for="enable_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_EVENTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_EVENTS'); ?></label></div>
    				<div class="controls">
    					<fieldset id="enable_events_fld" class="radio btn-group btn-group-yesno">
    						<input type="radio"  name="enable_events" id="enable_events1" value="1" <?php echo $this->item->enable_events==true? 'checked="checked"' :""?> />
    						<label class="btn" for="enable_events1"><?php echo JText::_('LNG_YES')?></label> 
    						<input type="radio"  name="enable_events" id="enable_events0" value="0" <?php echo $this->item->enable_events==false? 'checked="checked"' :""?> />
    						<label class="btn" for="enable_events0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
    				</div>
    			</div>
    			
    			<div class="control-group">
    				<div class="control-label"><label id="max_events-lbl" for="max_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_EVENTS');?></strong><br/><?php echo JText::_('LNG_MAX_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_EVENTS'); ?></label></div>
    				<div class="controls">
    					<input type="text" size="40" maxlength="20"  id="max_events" name="max_events" value="<?php echo $this->item->max_events?>">
                    </div>
    			</div>
    			
    			<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/views/event/tmpl/edit_recurring.php')) {?>
					<div class="control-group">
						<div class="control-label"><label id="enable_recurring_events-lbl" for="enable_recurring_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_RECURRING_EVENTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_RECURRING_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_RECURRING_EVENTS'); ?></label></div>
						<div class="controls">
							<fieldset id="enable_recurring_events_fld" class="radio btn-group btn-group-yesno">
								<input type="radio"  name="enable_recurring_events" id="enable_recurring_events1" value="1" <?php echo $this->item->enable_recurring_events==true? 'checked="checked"' :""?> />
								<label class="btn" for="enable_recurring_events1"><?php echo JText::_('LNG_YES')?></label> 
								<input type="radio"  name="enable_recurring_events" id="enable_recurring_events0" value="0" <?php echo $this->item->enable_recurring_events==false? 'checked="checked"' :""?> />
								<label class="btn" for="enable_recurring_events0"><?php echo JText::_('LNG_NO')?></label>
							</fieldset>
						</div>
					</div>
    			<?php } ?>
    			
    			<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventreservation.php')) {?>
    				<div class="control-group">
    					<div class="control-label"><label id="enable_event_reservation-lbl" for="enable_event_reservation" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_EVENT_RESERVATION');?></strong><br/><?php echo JText::_('LNG_ENABLE_EVENT_RESERVATION_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_ENABLE_EVENT_RESERVATION"); ?></label></div>
    					<div class="controls">
    						<fieldset id="enable_event_reservation_fld" class="radio btn-group btn-group-yesno">
    							<input type="radio"  name="enable_event_reservation" id="enable_event_reservation1" value="1" <?php echo $this->item->enable_event_reservation==1? 'checked="checked"' :""?> />
    							<label class="btn" for="enable_event_reservation1"><?php echo JText::_('LNG_YES')?></label>
    							<input type="radio"  name="enable_event_reservation" id="enable_event_reservation0" value="0" <?php echo $this->item->enable_event_reservation==0? 'checked="checked"' :""?> />
    							<label class="btn" for="enable_event_reservation0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
    					</div>
    				</div>
    			<?php } ?>
    
                <div class="control-group">
                    <div class="control-label"><label id="event_booking_timeout-lbl" for="event_booking_timeout" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENT_BOOKING_TIMEOUT');?></strong><br/><?php echo JText::_('LNG_EVENT_BOOKING_TIMEOUT_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_EVENT_BOOKING_TIMEOUT"); ?></label></div>
                    <div class="controls">
                        <input type="text" size="40" maxlength="20" id="event_booking_timeout" name="event_booking_timeout" value="<?php echo $this->item->event_booking_timeout ?>">
                    </div>
                </div>
    			
    			<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventappointment.php')) {?>
    				<div class="control-group">
    					<div class="control-label"><label id="enable_event_appointments-lbl" for="enable_event_appointments" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_EVENT_APPOINTMENTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_EVENT_APPOINTMENTS_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_ENABLE_EVENT_APPOINTMENTS"); ?></label></div>
    					<div class="controls">
    						<fieldset id="enable_event_appointments_fld" class="radio btn-group btn-group-yesno">
    							<input type="radio"  name="enable_event_appointments" id="enable_event_appointments1" value="1" <?php echo $this->item->enable_event_appointments==1? 'checked="checked"' :""?> />
    							<label class="btn" for="enable_event_appointments1"><?php echo JText::_('LNG_YES')?></label>
    							<input type="radio"  name="enable_event_appointments" id="enable_event_appointments0" value="0" <?php echo $this->item->enable_event_appointments==0? 'checked="checked"' :""?> />
    							<label class="btn" for="enable_event_appointments0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
    					</div>
    				</div>
    			<?php } ?>
    			
    			<div class="control-group">
    				<div class="control-label"><label id="enable_event_appointments-lbl" for="enable_event_appointments" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_EVENT_SUBSCRIPTION');?></strong><br/><?php echo JText::_('LNG_ENABLE_EVENT_SUBSCRIPTION_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_ENABLE_EVENT_SUBSCRIPTION"); ?></label></div>
    				<div class="controls">
    					<fieldset id="enable_event_appointments_fld" class="radio btn-group btn-group-yesno">
    						<input type="radio"  name="enable_event_subscription" id="enable_event_subscription1" value="1" <?php echo $this->item->enable_event_subscription==1? 'checked="checked"' :""?> />
    						<label class="btn" for="enable_event_subscription1"><?php echo JText::_('LNG_YES')?></label>
    						<input type="radio"  name="enable_event_subscription" id="enable_event_subscription0" value="0" <?php echo $this->item->enable_event_subscription==0? 'checked="checked"' :""?> />
    						<label class="btn" for="enable_event_subscription0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
    				</div>
    			</div>
    			
    			 <div class="control-group">
                    <div class="control-label"><label id="max_listing_events_display-lbl" for="max_listing_events_display" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_LISTING_EVENTS_DISPLAY');?></strong><br/><?php echo JText::_('LNG_MAX_LISTING_EVENTS_DISPLAY_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_MAX_LISTING_EVENTS_DISPLAY"); ?></label></div>
                    <div class="controls">
                        <input type="text" size="40" maxlength="20" id="max_listing_events_display" name="max_listing_events_display" value="<?php echo $this->item->max_listing_events_display ?>">
                    </div>
                </div>
            </div>
		</fieldset>
	</div>
</div>

<div class="row">
    <div class="col-md-12 general-settings">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_SEARCH'); ?></legend>
			<div class="row">
                <div class="col-md-6 general-settings">
                    <div class="form-container">
                        <div class="control-group">
                            <div class="control-label"><label id="event_submit_method-lbl" for="event_submit_method" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SUBMIT_METHOD');?></strong><br/><?php echo JText::_('LNG_SUBMIT_METHOD_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SUBMIT_METHOD'); ?></label></div>
                            <div class="controls">
                                <fieldset id="event_submit_method_fld" class="radio btn-group btn-group-yesno">
                                    <input type="radio"  name="event_submit_method" id="event_submit_method1" value="post" <?php echo $this->item->event_submit_method=="post"? 'checked="checked"' :""?> />
                                    <label class="btn" for="event_submit_method1"><?php echo JText::_('LNG_POST')?></label>
                                    <input type="radio"  name="event_submit_method" id="event_submit_method2" value="get" <?php echo $this->item->event_submit_method=="get"? 'checked="checked"' :""?> />
                                    <label class="btn" for="event_submit_method2"><?php echo JText::_('LNG_GET')?></label>
                                </fieldset>
                            </div>
                        </div>
                        
                       
            			<div class="control-group">
            				<div class="control-label"><label id="order_search_events-lbl" for="order_search_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ORDER_SEARCH_EVENTS');?></strong><br/><?php echo JText::_('LNG_ORDER_SEARCH_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ORDER_SEARCH_EVENTS'); ?></label></div>
            				<div class="controls">
            					<select name="order_search_events" id="order_search_events_fld" class="chosen-select">
            						<?php foreach( $this->item->orderSearchEvents as $key=>$orderSearchEvent){?>
            							<option value="<?php echo $key ?>" <?php echo $key == $this->item->order_search_events ? "selected":"" ; ?>><?php echo JText::_($orderSearchEvent)  ?></option>
            						<?php } ?>
            					</select>
                            </div>
            			</div>
            			
            			<div class="control-group">
		                    <div class="control-label"><label id="mix_results_events" for="mix_results_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MIX_RESULTS');?></strong><br/><?php echo JText::_('LNG_MIX_RESULTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MIX_RESULTS'); ?></label></div>
                            <div class="controls">
                                <fieldset id="mix_results_events_fld" class="radio btn-group btn-group-yesno">
                                    <input type="radio"  name="mix_results_events" id="mix_results_events1" value="1" <?php echo $this->item->mix_results_events==true? 'checked="checked"' :""?> />
                                    <label class="btn" for="mix_results_events1"><?php echo JText::_('LNG_YES')?></label>
                                    <input type="radio"  name="mix_results_events" id="mix_results_events0" value="0" <?php echo $this->item->mix_results_events==false? 'checked="checked"' :""?> />
                                    <label class="btn" for="mix_results_events0"><?php echo JText::_('LNG_NO')?></label>
                                </fieldset>
                            </div>
                        </div>
            			
            			
            			<div class="control-group">
            				<div class="control-label"><label id="events_search_view-lbl" for="events_search_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DEFAULT_EVENTS_VIEW');?></strong><br/><?php echo JText::_('LNG_DEFAULT_EVENTS_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_DEFAULT_EVENTS_VIEW"); ?></label></div>
            				<div class="controls">
            					<fieldset id="events_search_view_fld" class="radio btn-group btn-group-yesno">
            						<input type="radio"  name="events_search_view" id="events_search_view1" value="1" <?php echo $this->item->events_search_view==1? 'checked="checked"' :""?> />
            						<label class="btn" for="events_search_view1"><?php echo JText::_('LNG_GRID')?></label> 
            						<input type="radio"  name="events_search_view" id="events_search_view0" value="2" <?php echo $this->item->events_search_view==2? 'checked="checked"' :""?> />
            						<label class="btn" for="events_search_view0"><?php echo JText::_('LNG_LIST')?></label>
                                </fieldset>
            				</div>
            			</div>
            			<div class="control-group">
            				<div class="control-label"><label id="events_search_results_list_view-lbl" for="events_search_results_list_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENTS_SEARCH_RESULT_LIST_VIEW');?></strong><br/><?php echo JText::_('LNG_EVENTS_SEARCH_RESULT_LIST_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_EVENTS_SEARCH_RESULT_LIST_VIEW'); ?></label></div>
           				    <div class="controls">
                                <select name="events_search_results_list_view" id="events_search_results_list_view_fld" class="chosen-select">
                                    <?php foreach( $this->item->eventSearchResultViews as $key=>$searchResultView){?>
                                        <option value="<?php echo $key ?>" <?php echo $key == $this->item->events_search_results_list_view ? "selected":"" ; ?>><?php echo JText::_($searchResultView)  ?></option>
                                    <?php } ?>
                                </select>
                            </div>
            			</div>
            			
            			<div class="control-group">
            				<div class="control-label"><label id="events_search_results_grid_view-lbl" for="events_search_results_grid_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENTS_SEARCH_RESULT_GRID_VIEW');?></strong><br/><?php echo JText::_('LNG_EVENTS_SEARCH_RESULT_GRID_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_EVENTS_SEARCH_RESULT_GRID_VIEW'); ?></label></div>
            				<div class="controls">
            					<fieldset id="events_search_results_grid_view_fld" class="radio btn-group btn-group-yesno">
            						<input type="radio"  name="events_search_results_grid_view" id="events_search_results_grid_view1" value="1" <?php echo $this->item->events_search_results_grid_view==1? 'checked="checked"' :""?> />
            						<label class="btn" for="events_search_results_grid_view1"><?php echo JText::_('LNG_CALENDAR')?></label>
            						<input type="radio"  name="events_search_results_grid_view" id="events_search_results_grid_view2" value="2" <?php echo $this->item->events_search_results_grid_view==2? 'checked="checked"' :""?> />
            						<label class="btn" for="events_search_results_grid_view2"><?php echo JText::_('LNG_STYLE_1')?></label>
                                </fieldset>
            				</div>
            			</div>
            		</div>
            	</div>
        		<div class="col-md-6 general-settings">
                	<div class="form-container">
                		<div class="control-group">
            				<div class="control-label"><label id="enable_search_filter_events-lbl" for="enable_search_filter_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_SEARCH_FILTER_EVENTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_SEARCH_FILTER_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_SEARCH_FILTER_EVENTS'); ?></label></div>
            				<div class="controls">
            					<fieldset id="enable_search_filter_events_fld" class="radio btn-group btn-group-yesno">
            						<input type="radio"  name="enable_search_filter_events" id="enable_search_filter_events1" value="1" <?php echo $this->item->enable_search_filter_events==true? 'checked="checked"' :""?> />
            						<label class="btn" for="enable_search_filter_events1"><?php echo JText::_('LNG_YES')?></label> 
            						<input type="radio"  name="enable_search_filter_events" id="enable_search_filter_events0" value="0" <?php echo $this->item->enable_search_filter_events==false? 'checked="checked"' :""?> />
            						<label class="btn" for="enable_search_filter_events0"><?php echo JText::_('LNG_NO')?></label>
                                </fieldset>
            				</div>
            			</div>
						<div id="event-search-filter-settings" style="<?php echo $this->item->enable_search_filter_events == 0? "display:none" :"" ?>">
							<div class="control-group">
									<div class="control-label"><label id="events_search_filter_type-lbl" for="events_search_filter_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENTS_SEARCH_FILTER_TYPE');?></strong><br/><?php echo JText::_('LNG_EVENTS_SEARCH_FILTER_TYPE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_EVENTS_SEARCH_FILTER_TYPE"); ?></label></div>
									<div class="controls">
										<fieldset id="events_search_filter_type_fld" class="radio btn-group btn-group-yesno">
											<input type="radio"  name="events_search_filter_type" id="events_search_filter_type1" value="1" <?php echo $this->item->events_search_filter_type==1? 'checked="checked"' :""?> />
											<label class="btn" for="events_search_filter_type1"><?php echo JText::_('LNG_HORIZONTAL')?></label>
											<input type="radio"  name="events_search_filter_type" id="events_search_filter_type2" value="2" <?php echo $this->item->events_search_filter_type==2? 'checked="checked"' :""?> />
											<label class="btn" for="events_search_filter_type2"><?php echo JText::_('LNG_VERTICAL')?></label>
										</fieldset>
									</div>
								</div>
							
							<div class="control-group">
								<div class="control-label"><label id="search_filter_view_events-lbl" for="search_filter_view_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER_VIEW_EVENTS');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_VIEW_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER_VIEW_EVENTS"); ?></label></div>
								<div class="controls">
									<fieldset id="search_filter_view_events_fld" class="radio btn-group btn-group-yesno">
										<input type="radio"  name="search_filter_view_events" id="search_filter_view_events1" value="1" <?php echo $this->item->search_filter_view_events==1? 'checked="checked"' :""?> />
										<label class="btn" for="search_filter_view_events1"><?php echo JText::_('LNG_STYLE_1')?></label>
										<input type="radio"  name="search_filter_view_events" id="search_filter_view_events2" value="2" <?php echo $this->item->search_filter_view_events==2? 'checked="checked"' :""?> />
										<label class="btn" for="search_filter_view_events2"><?php echo JText::_('LNG_STYLE_2')?></label>
									</fieldset>
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label"><label id="event_search_filter_items-lbl" for="event_search_filter_items" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER_ITEM');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_ITEM_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SEARCH_FILTER_ITEM'); ?></label></div>
								<div class="controls">
									<input type="text" size=40 maxlength=20  id="event_search_filter_items" name="event_search_filter_items" value="<?php echo $this->item->event_search_filter_items?>">
								</div>
							</div>
							<div class="control-group">
								<div class="control-label"><label id="event_search_filter_fields-lbl" for="event_search_filter_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_FILTER_FIELDS'); ?></label></div>
								<div class="controls">
									<select	id="event_search_filter_fields[]" name="event_search_filter_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
										<?php
										foreach($this->eventSearchFilterFields as $field) {
											$selected = "";
											if (!empty($this->item->event_search_filter_fields)) {
												if (in_array($field->value, $this->item->event_search_filter_fields))
													$selected = "selected";
											} ?>
											<option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label"><label id="event_search_type-lbl" for="event_search_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER"); ?></label></div>
								<div class="controls">
									<fieldset id="event_search_type_fld" class="radio btn-group btn-group-yesno">
										<!--input type="radio"  name="event_search_type" id="event_search_type1" value="1" <?php echo $this->item->event_search_type==true? 'checked="checked"' :""?> />
										<label class="btn" for="event_search_type1"><?php echo JText::_('LNG_FACETED')?></label-->
										<input type="radio"  name="event_search_type" id="event_search_type0" value="0" <?php echo $this->item->event_search_type==false || true? 'checked="checked"' :""?> />
										<label class="btn" for="event_search_type0"><?php echo JText::_('LNG_FILTER_REGULAR')?></label>
									</fieldset>
								</div>
							</div>
                		</div>
                	</div>
                </div>
             </div>
		</fieldset>
	</div>
</div>

<div class="row">
	<div class="col-md-6 general-settings">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_EVENT_DETAILS'); ?></legend>
            <div class="form-container">
    			<div class="control-group">
    				<div class="control-label"><label id="event_view-lbl" for="event_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENT_VIEW');?></strong><br/><?php echo JText::_('LNG_EVENT_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_EVENT_VIEW'); ?></label></div>
    				<div class="controls">
    					<fieldset id="event_view_fld" class="radio btn-group btn-group-yesno">
    						<input type="radio"  name="event_view" id="event_view1" value="1" <?php echo $this->item->event_view==1? 'checked="checked"' :""?> />
    						<label class="btn" for="event_view1"><?php echo JText::_('LNG_STYLE_1')?></label>
                        </fieldset>
    				</div>
    			</div>
            </div>
		</fieldset>
	</div>
</div>

</div>

<script>
    window.addEventListener('load', function() {
        // Hide settings not taken into consideration  
        jQuery("#enable_search_filter_events1").click(function(){
            jQuery("#event-search-filter-settings").show(300);
        });
        jQuery("#enable_search_filter_events0").click(function(){
            jQuery("#event-search-filter-settings").hide(300);
        });
    });

</script>