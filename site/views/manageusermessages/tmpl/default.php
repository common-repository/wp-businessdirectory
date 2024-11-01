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

JBusinessUtil::checkPermissions("directory.access.messages", "manageusermessages");

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
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageusermessages'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">

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
        <table class="dir-panel-table messages-table responsive-simple" id="itemList">
            <thead>
            <tr>
                <th class="hidden-xs hidden-phone" width="1%" >#</th>
                <th width="5%"><?php echo JText::_('LNG_NAME') ?></th>
                <th width="1%" class="hidden-xs hidden-phone"><?php echo JText::_('LNG_EMAIL') ?></th>
                <th width="10%" class="nowrap hidden-phone"><?php echo JText::_('LNG_ITEM_NAME'); ?></th>
                <th width="5%" class="hidden-xs hidden-phone"><?php echo JText::_('LNG_TYPE') ?></th>
                <th width="5%"><?php echo JText::_('LNG_DATE') ?></th>
                <?php if ($filterType == MESSAGE_TYPE_BUSINESS || empty($filterType)){ ?>
                    <th width="10%" class="hidden-xs hidden-phone"><?php echo JText::_('LNG_CONTACT_NAME') ?></th>
                <?php } ?>
                <th width="5%"></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($this->items)) : ?>
                <?php foreach($this->items as $i=>$item) : ?>
                    <tr id="message-<?php echo $item->id ?>" class="<?php echo ($item->read)?'read-message':'unread-message' ?>">
                        <td onclick="readMessage('<?php echo $item->id ?>')">
                            <?php echo $this->pagination->getRowOffset($i); ?>
                        </td>
                        <td data-title="<?php echo JText::_('LNG_NAME'); ?>" onclick="readMessage('<?php echo $item->id ?>')" class="has-title">
                            <?php echo $item->name." ".$item->surname; ?>
                        </td>
                        <td data-title="<?php echo JText::_('LNG_EMAIL'); ?>" onclick="readMessage('<?php echo $item->id ?>')"  class="has-title">
                            <?php echo $item->email; ?>
                        </td>
                        <td data-title="<?php echo JText::_('LNG_ITEM_NAME'); ?>" onclick="readMessage('<?php echo $item->id ?>')"  class="has-title">
                            <?php
                                $itemType = '';
                                switch ($item->type){
                                    case MESSAGE_TYPE_BUSINESS:
                                        echo $item->companyName;
                                        $itemType = JText::_('LNG_COMPANY');
                                        break;
                                    case MESSAGE_TYPE_OFFER:
                                        echo $item->offerName;
                                        $itemType = JText::_('LNG_OFFER');
                                        break;
                                    case MESSAGE_TYPE_EVENT:
                                        echo $item->eventName;
                                        $itemType = JText::_('LNG_EVENT');
                                        break;
                                }
                            ?>
                        </td>
                        <td data-title="<?php echo JText::_('LNG_TYPE'); ?>" onclick="readMessage('<?php echo $item->id ?>')"  class="has-title">
                            <?php echo $itemType; ?>
                        </td>
                        <td data-title="<?php echo JText::_('LNG_DATE'); ?>" onclick="readMessage('<?php echo $item->id ?>')"  class="has-title">
                            <?php echo JBusinessUtil::getDateGeneralFormatWithTime($item->date); ?>
                        </td>
                        <?php if ($filterType == MESSAGE_TYPE_BUSINESS || empty($filterType)){ ?>
                            <td data-title="<?php echo JText::_('LNG_CONTACT_NAME'); ?>" onclick="readMessage('<?php echo $item->id ?>')"  class="has-title">
                                <?php echo $item->contactName ?>
                                <?php echo !empty($item->contactEmail)?' ('.$item->contactEmail.')':''; ?>
                            </td>
                        <?php } ?>
                        <td class="">
                            <a  href="javascript:jbdListings.deleteMessage(<?php echo $item->id; ?>)"
                                title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>">
                                <i class="la la-trash la-large">&nbsp;</i>
                            </a>
                            <a onclick="window.location.replace('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=manageusermessages.changeSMSStatus&id='. $item->id. '&currentStatus='.$item->read )?> ')"
                                <?php
                                if($item->read==0)
                                    echo 'title="'.JText::_('LNG_MARK_AS_READ').'"';
                                else
                                    echo 'title="'.JText::_('LNG_MARK_AS_UNREAD').'"';
                                ?>
                            >
                                <i
                                    <?php
                                    if($item->read==0)
                                        echo 'class="la la-envelope la-large';
                                    else
                                        echo 'class="la la-folder-open la-large"';
                                    ?>>
                                </i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
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

<div id="message-modal" class="jbd-container message-modal" style="display:none">
	<div class="jmodal">
		<div class="jmodal-header">
			<p class="jmodal-header-title"><?php echo JText::_('LNG_MESSAGE')?></p>
			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
		</div>
		<div class="jmodal-body">
			<div><span><?php echo JText::_('LNG_FROM') ?>:</span> <span id="message-name"></span></div>
			<div><span><?php echo JText::_('LNG_EMAIL') ?>: </span> <span id="message-email"></span></div>
			<p id="message-message" class="message-text"></p>
		</div>	
		<div class="jmodal-footer">
			<div class="btn-group" role="group" aria-label="">
				<button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CLOSE")?></button>
			</div>
		</div>
	</div>
</div>

<script>
    function readMessage(id){
        jQuery('#message-modal #message-name').html();
        jQuery('#message-modal #message-email').html();
        jQuery('#message-modal #message-message').html();
        <?php foreach ($this->items as $item) { ?>
        var val = '<?php echo $item->id ?>';
        if (id == val) {
            var name = '<?php echo $item->name." ".$item->surname ?>';
            jQuery('#message-modal #message-name').html(name);
            var email = '<?php echo $item->email ?>';
            jQuery('#message-modal #message-email').html(email);
            var message = "<?php echo  str_replace( array( "\n", "\r" ), array( "<br/>", "\\r" ), $item->message ); ?>";
            jQuery('#message-modal #message-message').html(message);
        }
        <?php } ?>
        jQuery('#message-modal').jbdModal();
    }
</script>