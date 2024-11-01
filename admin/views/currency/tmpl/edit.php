<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
// Load the tooltip behavior.
JBusinessUtil::loadJQueryChosen();
$item = $this->item;
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		jQuery("#item-form").validationEngine('detach');
		if (task == 'currency.cancel' || !jbdUtils.validateCmpForm(false, false)) {
			JBD.submitform(task, document.getElementById('item-form'));
		}
		jQuery("#item-form").validationEngine('attach');
    }
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $item->currency_id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <fieldset class="boxed">
                    <h2> <?php echo JText::_('LNG_CURRENCY'); ?></h2>
                    <p> <?php echo JText::_('LNG_CURRENCY_INFORMATION_TEXT'); ?></p>
                    <div id="currency-details">
                        <div class="form-container label-w-100" id="currency-form-box">
                            <div class="form-group">
                                <label for="currency_name"><?php echo JText::_('LNG_NAME') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?></label>                                        
                                    <input type="text" name="currency_name" id="currency_name" class="input_txt form-control validate[required]" value="<?php echo $this->escape($item->currency_name) ?>"  maxLength="255">                                        
                            </div>

                            <div class="form-group">
                                <label for="currency_description"><?php echo JText::_('LNG_DESCRIPTION') ?></label>                                        
                                    <textarea name="currency_description" id="currency_description" class="input_txt form-control"  cols="75" rows="5"  maxLength="255"><?php echo $item->currency_description ?></textarea>                                        
                            </div>

                            <div class="form-group">
                                <label for="currency_symbol"><?php echo JText::_('LNG_SYMBOL') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?></label>                                        
                                    <input type="text" name="currency_symbol" id="currency_symbol" class="input_txt form-control validate[required]" maxLength="255" value="<?php echo $this->escape($item->currency_symbol) ?>">                                     
                            </div>
                        
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="currency_id" value="<?php echo $item->currency_id ?>" />
        <input type="hidden" name="view" id="view" value="currency" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>

