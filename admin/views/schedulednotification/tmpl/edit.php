<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JBusinessUtil::loadJQueryChosen();

$this->item=$this->item;

$options = array(
        'onActive' => 'function(title, description) {
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
        'onBackground' => 'function(title, description) {
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
        'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
        'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<script type="text/javascript">
    window.addEventListener('load', function() {
        Joomla.submitbutton = function (task) {

            jQuery("#item-form").validationEngine('detach');
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("click", true, true);
           
            if (task == 'notification.cancel' || !jbdUtils.validateCmpForm(true, false)) {
                Joomla.submitform(task, document.getElementById('item-form'));
            }
            jQuery("#item-form").validationEngine('attach');
        }
    });
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_NOTIFICATION'); ?></h2>
                            <p> <?php echo JText::_('LNG_NOTIFICATIONS_INFORMATION_TEXT'); ?></p>
                            <div id="notification-details">
                                <div class="form-container label-w-100" id="notification-form-box">
                                    <div class="form-group">
                                        <label for="name"><?php echo JText::_('LNG_NAME') ?></label>                                        
                                            <input type="text" name="name" id="name" class="input_txt form-control" value="<?php echo $this->escape($this->item->name) ?>"  maxLength="255">                                        
                                    </div>

                                    <div class="form-group">
                                        <label for="title"><?php echo JText::_('LNG_TITLE')?> </label>
                                        <input type="text"	name="title" id="title" class="input_txt form-control text-input" value="<?php echo $this->escape($this->item->title) ?>"  maxLength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="body"><?php echo JText::_('LNG_DESCRIPTION') ?></label>                                        
                                            <textarea name="body" id="body" class="input_txt form-control h-auto"  cols="75" rows="3"  maxLength="255"><?php echo $this->item->body ?></textarea>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="frequency-lbl" for="frequency" class="hasTooltip"  data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_FREQUENCY');?></strong><br/><?php echo JText::_('LNG_FREQUENCY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_FREQUENCY'); ?></label></div>
                                <div class="controls">
                                    <select id="frequency" name="frequency[]" multiple="multiple" class="chosen chosen-select">
                                        <?php for($i=1;$i<31;$i++){?>
                                            <option value="<?php echo $i?>" <?php echo in_array($i, $this->item->frequency)?'selected="selected"':'' ?>><?php echo $i?></option>                		
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                        </fieldset>
                        
                        <input type="hidden" name="id" id="id" value="<?php echo $this->item->id ?>"/>
                        <hr/>
                       
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <fieldset class="boxed approved-label">
                    <div class="control-group">
                        <div class="control-label">
                            <label id="status-lbl" for="status" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_STATUS');?></strong><br/><?php echo JText::_('LNG_STATUS_DESC');?>" title=""><?php echo JText::_('LNG_STATUS'); ?></label>
                        </div>
                        <div class="controls">
                           <fieldset id="events_search_filter_type_fld" class="radio btn-group btn-group-yesno">
                                <label class="btn" id="label_status1" for="status1"><?php echo JTEXT::_("LNG_ACTIVE") ?></label>
                                <input type="radio" class="" onclick="" name="status" id="status1" value="<?php echo NOTIFICATION_STATUS_ACTIVE  ?>" <?php echo $this->item->status == NOTIFICATION_STATUS_ACTIVE ? 'checked="checked"' : "" ?> />
                                <input type="radio" class="" onclick="" name="status" id="status0" value="<?php echo NOTIFICATION_STATUS_INACTIVE  ?>" <?php echo $this->item->status == NOTIFICATION_STATUS_INACTIVE ? 'checked="checked"' : "" ?> />
                                <label class="btn <?php 'btn-danger' ?>" id="label_status2" for="status0"><?php echo JText::_('LNG_INACTIVE') ?></label>
                            </fieldset>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
        <input type="hidden" name="view" id="view" value="schedulednotification" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>

<script type="text/javascript">


window.addEventListener('load', function() {
    jQuery("#item-form").validationEngine('attach');
    jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5,search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
})    
</script>

