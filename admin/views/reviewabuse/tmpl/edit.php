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
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		JBD.submitform(task, document.getElementById('item-form'));
    }
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="form-horizontal boxed adminform">
                            <h2> <?php echo JText::_('LNG_EDIT_REVIEW_ABUSE');?></h2>
                            <div class="form-container">
                                <div class="form-group">
                                    <label for="review"><?php echo JText::_('LNG_REVIEW_NAME')?> </label>
                                    <input type="text"	name="review_id" id="review_id" class="form-control" disabled value="<?php echo $this->item->subject ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="email_id"><?php echo JText::_('LNG_EMAIL')?> </label>
                                    <input type="text"	name="email_id" id="email_id" class="form-control" disabled value="<?php echo $this->item->email ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="description_id"><?php echo JText::_('LNG_DESCRIPTION')?></label>
                                    <textarea name="description_id" id="description_id" class="form-control h-auto" disabled cols="75" rows="10" ><?php echo $this->item->description ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="state"><?php echo JText::_('LNG_STATE')?></label>
                                    <select class="form-control input-medium validate[required]" name="state" id="state">
                                        <?php foreach ($this->states as $allstates){?>
                                            <option value = '<?php echo $allstates->value?>' <?php echo $allstates->value==$this->item->state? "selected" : ""?>> <?php echo $allstates->text?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_id"><?php echo JText::_('LNG_DESCRIPTION')?></label>
                                    <textarea name="id_id" id="id_id" class="form-control" cols="75" rows="10" disabled ><?php echo $this->item->id ?></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="id" value="<?php echo $this->item->id ?>" />

        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>
