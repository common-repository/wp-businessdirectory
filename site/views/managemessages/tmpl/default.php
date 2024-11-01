<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$menuItemId = JBusinessUtil::getActiveMenuItem();

JBusinessUtil::checkPermissions("directory.access.messages", "managemessages");

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= true;
$isProfile = true;
$filterType = $this->state->get('filter.type');
?>
<script>
    var isProfile = true;
</script>
<style>
    #header-box, #control-panel-link {
        display: none;
    }
    #message-container .full-text {
        display: none;
    }
    #message-container.open .full-text {
        display: block;
    }
    #message-container.open .intro-text {
        display: none;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managemessages'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">
    <?php if(empty($this->items) && empty($filterType)) {
        echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_MESSAGE"), JText::_("LNG_MESSAGES"));
    ?>
    </form>
    <?php  return; } ?>

	<div class="row">
		<div class="col-md-3">
            <select name="filter_type" id="filter_type" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('LNG_JOPTION_ALL_MESSAGES');?></option>
                <?php echo JHtml::_('select.options', $this->messageTypes, 'id', 'name', $filterType);?>
            </select>
		</div>
	</div>

    <?php if (empty($this->items)) { ?>
        <div style="margin: 20px 0;" class="alert alert-warning">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php } else { ?>
        <div class="dir-table dir-panel-table messages-table responsive-simple" id="itemList">
           <div class="dir-table-body">
            <?php if(!empty($this->items)) : ?>
                <?php foreach($this->items as $i=>$item) : ?>
                    <?php
                            $itemType = '';
                            $itemName = '';
                            switch ($item->type){
                                case MESSAGE_TYPE_BUSINESS:
                                    $itemName= $item->companyName;
                                    $itemType = JText::_('LNG_COMPANY');
                                    break;
                                case MESSAGE_TYPE_OFFER:
                                    $itemName= $item->offerName;
                                    $itemType = JText::_('LNG_OFFER');
                                    break;
                                case MESSAGE_TYPE_EVENT:
                                    $itemName= $item->eventName;
                                    $itemType = JText::_('LNG_EVENT');
                                    break;
                            }
                            ?>
                    <div id="message-<?php echo $item->id ?>" class="<?php echo ($item->read)?'read-message':'unread-message' ?> dir-table-row">
                        <div class="row align-items-center justify-content-between">
                            <div onclick="readMessage('<?php echo $item->id ?>')" class="col-lg-5 dir-table-cell jtable-body-row-data">
                                <div class="item-title p-0">
                                    <?php echo $item->name." ".$item->surname; ?>
                                </div>  
                                <?php echo $item->email ?> 
                                <div class="jtable-body-row-data-allias">
                                    <?php echo JBusinessUtil::getDateGeneralFormatWithTime($item->date); ?>                                
                                </div>
                            </div>       
                            <div onclick="readMessage('<?php echo $item->id ?>')"  class="col-lg-3 dir-table-cell jtable-body-row-data">
                                <div class="item-label">
                                    <?php echo $itemType ?>
                                </div>
                                <div class="">
                                    <?php echo $itemName ?>
                                </div>
                            </div>
                            <?php if ($filterType == MESSAGE_TYPE_BUSINESS || empty($filterType)){ ?>
                                <div onclick="readMessage('<?php echo $item->id ?>')"  class="col-lg dir-table-cell jtable-body-row-data">
                                <div class="">                                
                                    <?php echo $item->contactName ?>
                                </div>
                                <div class="">
                                    <?php echo !empty($item->contactEmail)?' ('.$item->contactEmail.')':''; ?>
                                </div>
                                </div>
                            <?php } ?>
                            <div class="col-lg jtable-body-row-data">
                                <div class="item-actions" >
                                    <a class="jtable-btn" onclick="window.location.replace('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managemessages.changeSMSStatus&id='. $item->id. '&currentStatus='.$item->read )?> ')"
                                        <?php
                                        if($item->read==0)
                                            echo 'title="'.JText::_('LNG_MARK_AS_READ').'"';
                                        else
                                            echo 'title="'.JText::_('LNG_MARK_AS_UNREAD').'"';
                                        ?>
                                    >
                                    <?php if($item->read==0) {?>
                                            <i class="la la-file-text"></i>
                                        <?php } else { ?>
                                            <i class="la la-envelope"></i>
                                    <?php } ?>
                                    </a>
                                    <a  href="javascript:jbdListings.deleteMessage(<?php echo $item->id; ?>)"
                                        title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="jtable-btn">
                                        <i class="la la-trash"></i>
                                    </a>
                                </div>                            
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 dir-table-cell jtable-body-row-data d-flex justify-content-between" id="message-container" onclick="readMessage('<?php echo $item->id ?>')">
                                <div class="intro-text">
                                    <?php echo JBusinessUtil::truncate(JHTML::_("content.prepare", $item->message),100) ?>
                                </div>
                                <div class="full-text">
                                    <?php echo JHTML::_("content.prepare", $item->message) ?>
                                </div>
                                <?php if(strlen(strip_tags($item->message))>strlen(strip_tags(JBusinessUtil::truncate(JHTML::_("content.prepare", $item->message),100)))){?>
                                    <a id="expand-link-<?php echo $item->id ?>" href="javascript:void(0)">
                                    <i class="la la-chevron-down" id="expand-icon-<?php echo $item->id ?>"></i></a>
                                <?php } ?>
                            </div>                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    <?php } ?>
    <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
        <?php echo $this->pagination->getListFooter(); ?>
        <div class="clear"></div>
    </div>
    <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="currentStatus" id="currentStatus" value="">
    <input type="hidden" name="type" id="type" value="<?php echo $filterType ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>

<script>
    var nrUnreadMessages = '<?php echo $this->nrUnreadMessages ?>';

    window.addEventListener("load", function () {
        jQuery('#message-unreaded').html('('+nrUnreadMessages+')');
    });

    function readMessage(id){

        jQuery("#expand-link-"+id).parent().toggleClass('open')
        jQuery('#expand-icon-'+id).toggleClass('la-chevron-down la-chevron-up')
        
        let url = jbdUtils.getAjaxUrl('readMessageAjax', 'managemessages', 'managemessages');
        jQuery.ajax({
            type: 'GET',
            url: url,
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                if (data && !jQuery("#message-" + id).hasClass( "read-message" )) {
                    jQuery("#message-" + id).toggleClass('unread-message read-message');
                    nrUnreadMessages = nrUnreadMessages - 1;
                    jQuery('#message-unreaded').html();
                    jQuery('#message-unreaded').html('('+nrUnreadMessages+')');
                }
            }
        });
    }

    // function changeSMSStatus(id,currentStatus){
    //     jQuery("#id").val(id);
    //     jQuery("#currentStatus").val(currentStatus);
    //     jQuery("#task").val('managemessages.chageState');
    //
    //
    //     var form = document.adminForm;
    //
    //     form.submit();
    // }
</script>