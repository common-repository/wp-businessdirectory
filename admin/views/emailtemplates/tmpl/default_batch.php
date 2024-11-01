<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_content
 *
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

$published = $this->state->get('filter.published');
?>
<div class="jbd-container jbd-edit-container" id="collapseModal" style="display: none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('COM_JBUSINESSDIRECTORY_BATCH_OPTIONS') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p><?php echo JText::_('COM_JBUSINESSDIRECTORY_BATCH_TIP'); ?></p>
            <div class="row">
                <div class="col-12">
                    <div class="jinput-outline ">
                        <label title="" class="modalTooltip" for="batch-status_id" id="batch-status-lbl" data-original-title="&lt;strong&gt;Set Status&lt;/strong&gt;&lt;br /&gt;Not making a selection will keep the original status when processing."><?php echo JText::_("LNG_SET_STATUS")?></label>
                        <select id="batch-status_id" class="inputbox form-control" name="batch[status_id]">
                            <option value=""><?php echo JText::_("LNG_KEEP_ORIGINAL_STATUS")?></option>
                            <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.status_id'));?>
                        </select>
                    </div>
                </div>
            </div>            
        </div>
        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="document.getElementById('batch-status_id').value='';jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                <button type="button" class="jmodal-btn" onclick="JBD.submitbutton('emailtemplate.batch');"><?php echo JText::_("JGLOBAL_BATCH_PROCESS")?></button>
            </div>
        </div>
    </div>
</div>
