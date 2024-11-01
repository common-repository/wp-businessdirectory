<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

//retrieving current menu item parameters
$menuItemId = JBusinessUtil::getActiveMenuItem();

$jinput = JFactory::getApplication()->input;

$startDate = $jinput->get('startDate');
$endDate = $jinput->get('endDate');
if(empty($startDate) && empty($endDate)){
    $startDate = date('d-m-Y');
    $endDate = date('d-m-Y');
}
?>
<div class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post" name="adminForm" id="adminForm"  >
        <div class="row">
            <div class="col-md-3">
                <?php echo JHTML::_('calendar', $startDate, 'startDate', 'startDate', $this->appSettings->calendarFormat, array('style'=>'display:inline;width:100px;','class'=>'inputbox calendar-date', 'size'=>'10', 'maxlength'=>'10', 'placeholder'=>JText::_('LNG_FROM'))); ?>
            </div>
            <div class="col-md-3">
                <?php echo JHTML::_('calendar', $endDate, 'endDate', 'endDate', $this->appSettings->calendarFormat, array('style'=>'display:inline;width:100px','class'=>'inputbox calendar-date', 'size'=>'10', 'maxlength'=>'10', 'placeholder'=>JText::_('LNG_TO'))); ?>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-success" onclick="this.form.submit();">
                    <span class="ui-button-text"><i class="la la-pencil"></i> <?php echo JText::_("LNG_SEARCH")?></span>
                </button>
            </div>
        </div>
    </form>
    <br/>

    <?php if(!empty($this->items)) { ?>
    <div class="row">
        <div class="col-md-3">
            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=activityitinerary&layout=print&selected=0&startDate='.$startDate.'&endDate='.$endDate) ?>" class="btn btn-sm btn-success btn-panel">
                <?php echo JText::_('LNG_PRINT_ALL_ACTIVITIES'); ?>
            </a>
        </div>
    </div>
    <br/>

    <!-- My Itinerary Section -->
    <div class="activity_itinerary_table">
        <div class="activity_itinerary_header">
            <h3>
                <?php echo JText::_('LNG_MY_ITINERARY'); ?>
            </h3>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p><b>&nbsp;&nbsp;<?php echo JBusinessUtil::getDateGeneralFormat($startDate) ?> - <?php echo JBusinessUtil::getDateGeneralFormat($endDate) ?></b></p>
            </div>
            <div class="col-md-3">
                <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=activityitinerary&layout=print&selected=1') ?>" class="btn btn-sm btn-primary btn-panel">
                    <?php echo JText::_('LNG_PRINT_MY_ITINERARY'); ?>
                </a>&nbsp;&nbsp;
                <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=activityitinerary&task=activityitinerary.reset') ?>" class="btn btn-sm btn-danger btn-panel">
                    <?php echo JText::_('LNG_RESET'); ?>
                </a>
            </div>
        </div><br/>
        <table style="margin-left:10px;">
            <tbody>
            <?php foreach($this->days as $day) { ?>
                <tr>
                    <td>
                        <?php echo JBusinessUtil::getDateGeneralFormat($day) ?>
                    </td>
                </tr>
                <tr id="selected_activities_<?php echo $day; ?>" class="<?php echo empty($this->activities[$day])?"no-activities":"selected-activities"; ?>">
                    <?php if(empty($this->activities[$day])) { ?>
                        <td>
                            <i><?php echo JText::_('LNG_NO_ACTIVITIES_ADDED') ?></i>
                        </td>
                    <?php }
                    else { ?>
                        <td>
                        <?php foreach($this->activities[$day] as $activity) { ?>
                                <a href="<?php echo $activity->link ?>"><?php echo $activity->name ?></a>&nbsp;&nbsp;&nbsp;(<?php echo $activity->hours ?>)
                                <br/>
                                <?php echo $activity->address ?>
                                <br/>
                                <?php echo $activity->contact ?>
                            <hr/>
                        <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <br/>

        <!-- Dates Container -->
        <?php foreach($this->days as $day) { ?>
        <div class="activity_itinerary_table">
            <div class="activity_itinerary_header">
                <h3>
                    <?php echo JBusinessUtil::getDateGeneralFormat($day) ?>
                </h3>
            </div>
            <!-- Categories Container -->
            <div>
            <?php if(!empty($this->items[$day])) { ?>
                <?php $i=0; ?>
                <?php foreach($this->items[$day] as $key=>$val) { ?>
                <table class="table">
                    <thead>
                    <tr>
                        <td>
                            <h4><?php echo $key ?></h4>
                        </td>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td></td>
                        <td align="center">
                            <?php echo $key ?>
                        </td>
                        <td align="left">
                            <?php echo JText::_('LNG_TIME'); ?>
                        </td>
                    </tr>
                    <?php foreach($val as $item) { ?>
                    <tr class="row<?php echo $i%2; ?>">
                        <td align="left" width="15%">
                            <?php $selected = false; ?>
                            <?php if(isset($this->selectedActivities[$day][$item->id][$item->type])){$selected = true; } ?>
                            <a href="javascript:void(0)" id="<?php echo $day ?>_<?php echo $item->id ?>_<?php echo $item->type ?>" onclick="<?php echo (!$selected)?"addToTrip('".$day."', ".$item->id.", ".$item->type.")":"return false;"; ?>" class="btn btn-sm <?php echo (!$selected)?"btn-success":"btn-disabled"; ?> btn-panel">
                                <?php echo JText::_('LNG_ADD_TO_TRIP'); ?>
                            </a>
                            <?php if($selected) { ?>
                            <a href="javascript:void(0)" id="<?php echo $day ?>_<?php echo $item->id ?>_<?php echo $item->type ?>_remove" onclick="removeFromTrip('<?php echo $day ?>', <?php echo $item->id ?>, <?php echo $item->type ?>)" class="btn btn-sm btn-danger btn-panel">
                                x
                            </a>
                            <?php } ?>
                        </td>
                        <td align="center" width="30%">
                            <a href="<?php echo $item->link; ?>">
                                <?php echo $item->name ?>
                            </a>
                        </td>
                        <td align="left" width="10%">
                            <?php echo $item->hours ?>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php $i++; } ?>
                <?php }
                else { ?>
                    <div>
                        <p><i>&nbsp;&nbsp;<?php echo JText::_('LNG_NO_ACTIVITIES_FOR_THIS_DAY'); ?></i></p>
                    </div>
                <?php } ?>
            </div>
        </div><br/>
        <?php } ?>
    <?php }
    else { ?>
        <p><?php echo JText::_('LNG_SELECT_DATE_INTERVAL'); ?></p>
    <?php } ?>
</div>

<script>

    function addToTrip(day, id, type){
        let urlAddToTrip = jbdUtils.getAjaxUrl('addToTripAjax', 'activityitinerary');

        jQuery.ajax({
            type:"GET",
            url: urlAddToTrip,
            data: { day: day, id: id, type: type },
            dataType: 'json',
            success: function(data){
                jQuery('#'+day+'_'+id+'_'+type).attr('class', 'btn btn-sm btn-disabled btn-panel');
                jQuery('#'+day+'_'+id+'_'+type).attr("onclick", "return false;");
                addRemoveButton(day, id, type);
                jQuery('#selected_activities_'+day).html(data);
            }
        });
    }

    function addRemoveButton(day, id, type){
        let removeButton = "<a href='javascript:void(0);' id='"+day+"_"+id+"_"+type+"_remove' class='btn btn-sm btn-danger btn-panel'>x</a>";
        jQuery('#'+day+"_"+id+"_"+type).parent().append(removeButton);
        jQuery('#'+day+'_'+id+'_'+type+'_remove').attr("onclick", "removeFromTrip('"+day+"',"+id+","+type+")");
    }

    function removeFromTrip(day, id, type){
        let urlRemoveFromTrip = jbdUtils.getAjaxUrl('removeFromTripAjax', 'activityitinerary');
        jQuery.ajax({
            type:"GET",
            url: urlRemoveFromTrip,
            data: { day: day, id: id, type: type },
            dataType: 'json',
            success: function(data){
                jQuery('#'+day+'_'+id+'_'+type).attr('class', 'btn btn-sm btn-success btn-panel');
                jQuery('#'+day+'_'+id+'_'+type).attr("onclick", "addToTrip('"+day+"',"+id+","+type+")");
                jQuery('#'+day+'_'+id+'_'+type+'_remove').remove();
                jQuery('#selected_activities_'+day).html(data);
            }
        });
    }


</script>