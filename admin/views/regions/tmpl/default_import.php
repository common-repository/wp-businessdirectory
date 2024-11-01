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
JHtml::_('behavior.multiselect');
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function(task)
    {
        if (task != 'regions.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_REGIONS_CONFIRM_DELETE', true);?>'))
        {
            JBD.submitform(task);
        }
    }
});
</script>

<div class="jbd-container jbd-edit-container" id="import-model" style="display: none">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=regions');?>" method="post" name="importForm" id="importForm" enctype="multipart/form-data">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_IMPORT_REGIONS_AND_CITIES_CSV') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <div id="import-from-file" class="form-group row" style="display: block">
                    <div class="col-12">
                        <div class="form-upload">
                            <label class="optional" for="csvFile"><?php echo JText::_("LNG_SELECT_CSV_FILE") ?>.</label>
                            <input type="file" id="csvFile" name="csvFile" size="50">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline ">
                            <label for="delimiter"><?php echo JText::_('LNG_DELIMITER')?> </label>
                            <select name="delimiter">
                                <option value=";"><?php echo JText::_('LNG_SEMICOLON')?></option>
                                <option value=","><?php echo JText::_('LNG_COMMA')?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                <div id="insert_task">
                    <input type="hidden" name="task" id="task" value="regions.importRegionsAndCitiesFromCsv" />
                </div>
                <?php echo JHTML::_( 'form.token' ); ?>
            </div>
            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="submit" class="jmodal-btn jmodal-btn-submit"><?php echo JText::_("LNG_IMPORT")?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

    jQuery("#importForm").submit(function() {        
        jQuery(".jmodal-btn-submit").addClass("loader").attr("disabled", true)
    })

</script>