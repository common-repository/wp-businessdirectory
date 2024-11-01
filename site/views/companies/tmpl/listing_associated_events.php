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
<style>
    .ui-timepicker-wrapper {
        z-index: 999999 !important;
    }
</style>

<?php if(!empty($this->associatedEvents)){ ?>
    <div class="row has-flex-columns">
    	<?php 
        $index = 0;
        foreach ($this->associatedEvents as $event){
            $index++;
        	$dateError = (strtotime($event->end_date) < strtotime($event->start_date))?true:false;
            ?>
            <div class="col-lg-4 col-sm-6 col-12">
        		<div class="jitem-card my-3">
        			<div class="jitem-date-wrap bg-dark">
        				<p><?php echo JBusinessUtil::getDayOfMonth($event->start_date) ?></p>
        				<p><?php echo JBusinessUtil::getMonth($event->start_date) ?> <?php echo JBusinessUtil::getYear($event->start_date) ?></p>
        			</div>
        			<div class="jitem-img-wrap small">
        				<a href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>" target="_blank"></a>
                        <?php if(!empty($event->picture_path) ){?>
                            <img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo BD_PICTURES_PATH.$event->picture_path ?>" >
                        <?php }else{ ?>
                            <img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" >
                        <?php } ?>
        			</div>
        			<div class="jitem-body">
            			<div class="jitem-body-content">
            				<div class="jitem-title">
            					<a href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><?php echo $this->escape($event->name) ?></a>
            				</div>
            				<div class="jitem-desc text-small">
            				 	<?php if($event->show_start_time && !empty($event->start_time)){?>
    								<div>
    									<i class="icon clock"></i> <?php echo ($event->show_start_time?JBusinessUtil::convertTimeToFormat($event->start_time):"")." ".(!empty($event->end_time) && $event->show_end_time?"-":"")." ".($event->show_end_time?JBusinessUtil::convertTimeToFormat($event->end_time):""); ?>
    								</div>                				
    							<?php }?>
            				
                                <?php $address = JBusinessUtil::getShortAddress($event);
                                if (!empty($address)){?>
            					<div><i class="icon map-marker"></i> <?php echo $address ?></div>
            					<?php }?>
            				</div>
            			</div>
                        <?php if($this->appSettings->enable_event_appointments && !$dateError && (!JBusinessUtil::emptyDate($event->start_date) || !JBusinessUtil::emptyDate($event->end_date))
                                && (isset($this->package->features) && in_array(EVENT_APPOINTMENT,$this->package->features) || !$appSettings->enable_packages)) { ?>
                        	<div class="jitem-bottom justify-content-center pb-3">
        						<a href="javascript:void(0)" class="btn btn-success" onclick="jbdListings.makeAppointment(<?php echo $event->id; ?>, '<?php echo $event->start_date; ?>', '<?php echo $event->end_date; ?>')">
        							<?php echo JText::_('LNG_BOOK_APPOINTMENT'); ?>
        						</a>
        					</div>
    			    	<?php } ?>
            		</div>	
        		</div>
			</div>
		    <?php if($index%3==0){?>
        		</div>
        		<div class="row has-flex-columns">
      		<?php } ?>
  	  <?php } ?>
	</div>
<?php } ?>

<?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$this->appSettings->enable_packages)
&& isset($this->associatedEvents) && count($this->associatedEvents) && $this->appSettings->enable_events){ ?>
	<div id="event-appointment" class="jbd-container" style="display:none">
        <form id="leaveAppointmentFrm" name ="leaveAppointmentFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post">
            <div class="jmodal-sm">
                <div class="jmodal-header">
                    <p class="jmodal-header-title"><?php echo JText::_('LNG_BOOK_APPOINTMENT') ?></p>
                    <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                </div>

                <div class="jmodal-body">
                    <p>
                        <?php echo JText::_('LNG_BOOK_APPOINTMENT_TEXT') ?>
                    </p>

                    <div class="row">
                        <div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="first_name" id="first_name" class="validate[required]" required="">
                                <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="last_name" id="last_name" class="validate[required]" required="">
                                <label for="last_name"><?php echo JText::_('LNG_LAST_NAME') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="company_name" id="app_company_name" required="">
                                <label for="app_company_name"><?php echo JText::_('LNG_COMPANY_NAME') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="phone" id="app_phone" class="input_txt  validate[required]" required="">
                                <label for="app_phone"><?php echo JText::_('LNG_PHONE') ?></label>
                            </div>
                        </div>

                        <div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="email" id="app_email" class="input_txt validate[required,custom[email]]" required="">
                                <label for="app_email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md">
                            <div class="jinput-outline">
                                <select name="date" id="date-appoint" class="inputbox validate[required]" required="">
                                </select>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="time" id="app_time" class="input_txt timepicker ui-timepicker-input" required="">
                                <label for="app_time"><?php echo JText::_('LNG_PREFERRED_TIME') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <textarea rows="5" name="remarks" id="app_remarks" class="form-control" required=""></textarea>
                                <label for="app_remarks"><?php echo JText::_('LNG_REMARKS')?>:</label>
                            </div>
                        </div>
                    </div>

                    <?php if($this->appSettings->captcha){?>
                        <div class="form-item">
                            <?php
                            $namespace="jbusinessdirectory.contact";
                            $class=" required";

                            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                            if(!empty($captcha)){
                                echo $captcha->display("captcha", "captcha-div-appointment", $class);
                            }
                            ?>
                        </div>
                    <?php } ?>

                    <input type='hidden' name='task' id="leave_appointment_task" value='companies.leaveAppointment'/>
                    <input type='hidden' name='userId' value='<?php echo $user->ID?>'/>
                    <input type='hidden' name='controller' value='companies' />
                    <input type='hidden' name='view' value='companies' />
                    <input type="hidden" name="company_id" value="<?php echo $this->company->id?>" />
                    <input type="hidden" name="event_id" id="eventId-appoint" value="" />
                </div>
                <div class="jmodal-footer">
                    <div class="btn-group" role="group" aria-label="">
                        <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                        <button type="button" class="jmodal-btn jbd-commit" onclick="jbdUtils.saveForm('leaveAppointmentFrm' , document.getElementById('leave_appointment_task').value )"><?php echo JText::_("LNG_BOOK_APPOINTMENT")?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } ?>