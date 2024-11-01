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

<div class="jbd-container jbd-edit-container" id="import-model" style="display: none">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=categories');?>" method="post" name="importForm" id="importForm" enctype="multipart/form-data">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_CATEGORIES_IMPORT_FROM_CSV') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <p><?php echo JText::_('LNG_CATEGORIES_IMPORT_FROM_CSV_TEXT'); ?></p>
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline ">
                            <select id="formId" onchange="openImportForm()">
                                <option value="1"><?php echo JText::_("LNG_IMPORT_FROM_CSV") ?></option>
                                <option value="2"><?php echo JText::_("LNG_IMPORT_FROM_TXT_AREA") ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="import-from-file" class="form-group row" style="display: block">
                    <div class="col-12">
                        <div class="form-upload">
                            <label class="optional" for="csvFile"><?php echo JText::_("LNG_SELECT_CSV_FILE") ?>.</label>
                            <input type="file" id="csvFile" name="csvFile" size="50">
                        </div>
                    </div>
                </div>
                <div id="import-from-txt-area" class="form-group row" style="display: none">
                    <div class="col-12">
                        <div class="jinput-outline ">
                            <textarea rows="15" style="width: 90%" id="companyData_ForImport" name="companyData_ForImport" placeholder="<?php echo JText::_("LNG_ENTER_DATA_HERE") ?>"></textarea>
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
                                <!--option value="|"><?php echo JText::_('LNG_VERTICAL_LINE')." (just for the custom work)"?></option-->
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                <div id="insert_task">
                    <input type="hidden" name="task" id="task" value="categories.importCategoriesFromCsv" />
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
	function openImportForm() {
		var option = document.getElementById('formId');
		if (option.value == 1) {
			jQuery("#import-from-txt-area").slideUp(500);
			jQuery("#import-from-file").slideDown(500);
			jQuery('#insert_task').html('');
			jQuery('#insert_task').html('<input type="hidden" name="task" id="task" value="categories.importCategoriesFromCsv" />');
			var txtAreaVal = document.getElementById('categoryData_ForImport');
			txtAreaVal.value = null;
		} else if (option.value == 2) {
			jQuery("#import-from-file").slideUp(500);
			jQuery("#import-from-txt-area").slideDown(500);
			jQuery('#insert_task').html('');
			jQuery('#insert_task').html('<input type="hidden" name="task" id="task" value="categories.importCategoriesFromTxtArea" />');
			var file = document.getElementById('csvFile');
			file.value = null;
		}
	}
    
    jQuery("#importForm").submit(function() {        
        jQuery(".jmodal-btn-submit").addClass("loader").attr("disabled", true)
    })

</script>