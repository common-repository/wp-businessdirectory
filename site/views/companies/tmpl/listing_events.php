<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

//$calendarSource = html_entity_decode(JRoute::_('index.php?option=com_jbusinessdirectory&task=events.getCalendarEvents&companyId='.$this->company->id));
//require_once JPATH_COMPONENT_SITE.'/libraries/calendar/calendar.php';
?>

<?php if(!empty($this->events)){ ?>
    <div>
    	<ul class="event-list">
    			<?php foreach ($this->events as $event){ ?>
    				<li>
    					<div class="row">
    						<div class="col-md-4">
    							<a href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>">
    								<?php if(!empty($event->picture_path)){?>
    									<img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo BD_PICTURES_PATH.$event->picture_path?>">
    								<?php }else{ ?>
    									<img title="<?php echo $this->escape($event->name) ?>" alt="<?php echo $this->escape($event->name) ?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>">
    								<?php } ?>
    							</a>
    						</div>
    						<div class="col-md-8">
    							<div class="event-subject">
    								<a title="<?php echo $this->escape($event->name) ?>" href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>"><?php echo $this->escape($event->name) ?></a>
    							</div>
    							<?php $address = JBusinessUtil::getShortAddress($event); ?>
                          	    <?php if(!empty($address)) { ?>
        							<div class="event-location">
        								<i class="icon map-marker"></i>&nbsp;<span itemprop="name"><?php echo $address ?></span>
        							</div>
    							<?php } ?>
    
                                <?php if ($event->start_date != '0000-00-00'){ ?>
                                    <div class="event-date">
                                        <i class="icon calendar"></i>
                                        <?php $dates = JBusinessUtil::getDateGeneralFormat($event->start_date).(!empty($event->start_date) && $event->start_date!=$event->end_date && $event->show_end_date?" - ".JBusinessUtil::getDateGeneralFormat($event->end_date):""); echo $dates; ?>
                                        <?php echo (empty($dates) || ($event->show_start_time==0  && $event->show_end_time==0))?"":"," ?>
                                        <?php echo ($event->show_start_time?JBusinessUtil::convertTimeToFormat($event->start_time):"")." ".(!empty($event->end_time)&&$event->show_end_time?JText::_("LNG_UNTIL"):"")." ".($event->show_end_time?JBusinessUtil::convertTimeToFormat($event->end_time):""); ?>
                                    </div>
                                <?php } ?>
                                
                                <?php if($event->show_start_time && !empty($event->start_time)){?>
									<div>
										<i class="icon clock"></i> <?php echo ($event->show_start_time?JBusinessUtil::convertTimeToFormat($event->start_time):"")." ".(!empty($event->end_time) && $event->show_end_time?"-":"")." ".($event->show_end_time?JBusinessUtil::convertTimeToFormat($event->end_time):""); ?>
									</div>                				
								<?php }?>
    
    							<div class="event-type">
    								<?php echo JText::_("LNG_TYPE")?>:  <?php echo $this->escape($event->eventType) ?>
    							</div>
    							<div class="event-desciption">
    								<?php echo $this->escape($event->short_description) ?>
    							</div>
    						</div>
    					</div>
    				</li>
    			<?php } ?>
    	</ul>
    </div>
<?php } ?>		
	