<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

JBusinessUtil::checkPermissions("directory.access.trips", "manageusertripbookings");
$isProfile = true;
$filterState = $this->state->get('filter.state_id');
?>
<script>
    var isProfile = true;
</script>
<style>
    #header-box, #control-panel-link {
        display: none;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageusertripbookings');?>" method="post" name="adminForm" id="adminForm">
    <div id="editcell">
        <?php if(empty($this->items) && empty($filterState) && $filterState != '0'){
            echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_BOOKING"),JText::_("LNG_BOOKINGS"));
        ?>
            </div>
            <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
            <input type="hidden" name="task" id="task" value="" />
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="Itemid" id="Itemid" />
            <?php echo JHTML::_('form.token'); ?>
        </form>
        <?php
        return;
        } ?>
        
        <div class="row align-items-center">
            <div class="col-md-4">
                <select name="filter_state_id" id="filter_state_id" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('LNG_JOPTION_SELECT_STATUS');?></option>
			        <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $filterState);?>
                </select>
            </div>
        </div>

        <?php if (empty($this->items)) { ?>
            <div style="margin: 20px 0;" class="alert alert-warning">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php } else { ?>
            <table class="dir-panel-table responsive-simple">
                <thead>
                <tr>
                    <th width="10%"><?php echo JText::_('LNG_BOOKING_ID'); ?></th>
                    <th><?php echo JText::_('LNG_TRIP'); ?></th>
                    <th width="20%"><?php echo JText::_('LNG_GUEST_DETAILS'); ?></th>
                    <th width="10%"><?php echo JText::_('LNG_CREATED'); ?></th>
                    <th width="5%"><?php echo JText::_('LNG_STATUS');  ?></th>
                    <th width="10%"><?php echo JText::_('LNG_ACTION');  ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $nrcrt = 1;
                if(!empty($this->items)){
                	foreach($this->items as $item) {?>
                        <tr class="jtable-body-row row<?php echo $nrcrt%2 ?>">
                            <td align="left" nowrap="nowrap">
                                <strong><?php echo JBusinessUtil::formatBookingId($item->id); ?></strong>
                            </td>
                            <td>
                                <a href="<?php echo JBusinessUtil::getTripLink($item->trip_id, $item->tripAlias)?>"><?php echo $item->tripName ?></a><br/>
                                <div>
                                	<i class="la la-calendar"></i> <?php echo !empty( $item->trip_date)? JBusinessUtil::getDateGeneralFormat($item->trip_date):"" ?>
                                    <br/>
                                	<i class="la la-clock-o"></i> <?php echo !empty( $item->trip_time)? JBusinessUtil::getTimeText($item->trip_time):"" ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo $item->first_name; ?> <?php echo $item->last_name; ?></strong> <br/>
                                <?php echo $item->email?></br>
                                <?php echo $item->phone?>
                            </td>
                            <td>
                               <?php echo JBusinessUtil::getDateGeneralShortFormat($item->created) ?>
                            </td>
                            <td valign="top" align="center">
                                <?php
                                switch($item->status) {
                                    case TRIP_BOOKING_CREATED:
                                        echo '<span class="status-badge badge-primary">'.JText::_("LNG_CREATED").'</span>';
                                        break;
                                    case TRIP_BOOKING_CONFIRMED:
                                        echo '<span class="status-badge badge-success">'.JText::_("LNG_CONFIRMED").'</span>';
                                        break;
                                    case TRIP_BOOKING_CANCELED:
                                        echo '<span class="status-badge badge-danger">'.JText::_("LNG_CANCELED").'</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td valign="top" align="center">
                                <?php if($item->status == TRIP_BOOKING_CREATED) { 
                                    echo '<a class="btn btn-sm btn-success" href="'.JRoute::_('index.php?option=com_jbusinessdirectory&view=tripBooking&orderId='.$item->id).'">'.JText::_("LNG_CONFIRM").'</a>';
                                 } ?>
                            </td>
                        </tr>
                    <?php }
                }
                ?>
                </tbody>
            </table>
        <?php } ?>

        <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
            <?php echo $this->pagination->getListFooter(); ?>
            <div class="clear"></div>
        </div>
    </div>
    <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="Itemid" id="Itemid" />
    <?php echo JHTML::_('form.token'); ?>
</form>
<div class="clear"></div>