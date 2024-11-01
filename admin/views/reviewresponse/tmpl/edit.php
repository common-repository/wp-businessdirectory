<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior.
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function(task) {
        jQuery("#item-form").validationEngine('detach');
        if (jbdUtils.getProperty("isMultilingual")) {
            jQuery(".tab-"+jbdUtils.getProperty("defaultLang")).each(function(){
                jQuery(this).click();
            });
        }

        if (task == 'company.cancel' || task == 'company.aproveClaim' || task == 'company.disaproveClaim' || jbdUtils.validateTabs(true, <?php echo $attributeConfig["description"] == ATTRIBUTE_MANDATORY?'true':'false' ?>)){
            JBD.submitform(task, document.getElementById('item-form'));
        }
        jQuery("#item-form").validationEngine('attach');
    }
});
</script>
<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
        jQuery("#item-form").validationEngine('detach');
        if (task == 'reviewresponse.cancel' || jbdUtils.validateTabs(true, false)){
            JBD.submitform(task, document.getElementById('item-form'));
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
                    <div class="col-md-12">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_EDIT_REVIEW_RESPONSE');?></h2>
                            <div class="form-container">
                                <div class="form-group">
                                    <label for="response_id"><?php echo JText::_('LNG_ID')?> </label>
                                    <input type="text"
                                           name="response_id" id="response_id_fld" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->item->id ?>" maxlength="4" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="review_name"><?php echo JText::_('LNG_REVIEW_NAME')?> </label>
                                    <input type="text"
                                           name="review_name" id="review_name_fld" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->escape($this->item->subject) ?>" maxlength="4" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="firstName"><?php echo JText::_('LNG_FIRSTNAME')?> </label>
                                    <input type="text"
                                           name="firstName" id="firstName" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->escape($this->item->firstName) ?>" maxlength="45" size="50">
                                </div>

                                <div class="form-group">
                                    <label for="lastName"><?php echo JText::_('LNG_LASTNAME')?> </label>
                                    <input type="text"
                                           name="lastName" id="lastName" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->escape($this->item->lastName) ?>" maxlength="45" size="50">
                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo JText::_('LNG_EMAIL')?> </label>
                                    <input type="text"
                                           name="email" id="email" class="control-label form-control hasTooltip validate[custom[email]]" data-toggle="tooltip" value="<?php echo $this->escape($this->item->email) ?>" maxlength="45" size="50">
                                </div>

                                <div class="form-group">
                                    <label for="response"><?php echo JText::_('LNG_RESPONSE')?> </label>
                                    <input type="text"
                                           name="response" id="response" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->escape($this->item->response) ?>" maxlength="45" size="50">
                                </div>

                                <div class="form-group">
                                    <label for="state"><?php echo JText::_('LNG_STATE')?> </label>
                                    <select data-placeholder="<?php echo JText::_("LNG_JOPTION_SELECT_STATE") ?>" class="inputbox form-control input-medium chosen-select" name="state" id="state">
                                        <option value=""><?php echo JText::_("LNG_JOPTION_SELECT_STATE") ?></option>
                                        <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->item->state);?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>

		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />

		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>