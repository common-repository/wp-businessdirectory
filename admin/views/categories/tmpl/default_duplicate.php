<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

?>

<div class="jbd-container jbd-edit-container" id="dublicate-model" style="display: none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('COM_JBUSINESSDIRECTORY_BATCH_OPTIONS') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p><?php echo JText::_('LNG_CATEGORIES_DUPLICATE_TEXT'); ?></p>
            <div class="row">
                <div class="col-12">
                    <div class="jinput-outline ">
                        <label title="" class="modalTooltip" for="batch-catType" id="batch-catType-lbl"><?php echo JText::_("LNG_TYPE")?></label>
                        <select id="batch-catType-id" class="inputbox" name="catType">
                            <?php foreach ($this->types as $type) { ?>
                                <option value="<?php echo $type->value ?>"><?php echo $type->text ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="document.getElementById('batch-catType-id').value='';jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                <button type="button" class="jmodal-btn" onclick="document.adminForm.catType.value=jQuery('#batch-catType-id').val();JBD.submitbutton('categories.duplicateCategories');"><?php echo JText::_("JGLOBAL_BATCH_PROCESS")?></button>
            </div>
        </div>
    </div>
</div>