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
		if (task != 'companies.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_COMPANIES_CONFIRM_DELETE', true);?>'))
		{
			JBD.submitform(task);
		}
	}
});
</script>

<div class="jbd-container jbd-edit-container" id="import-model" style="display: none">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=companies');?>" method="post" name="importForm" id="importForm" enctype="multipart/form-data">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_IMPORT_FROM_CSV') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <p><?php echo JText::_('LNG_IMPORT_FROM_CSV_TEXT'); ?></p>
                <span><?php echo JText::_('LNG_IMPORT_FROM_CSV_HINT'); ?><a target="_blank" href="http://cmsjunkie.com/docs/jbusinessdirectory/businessdiradmin.html?highlight=csv%20import#id9"> <?php echo JText::_('LNG_USER_MANUAL') ?></a></span>
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline">
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
                        <div class="jinput-outline">
                            <textarea rows="15" style="width: 90%" id="companyData_ForImport" name="companyData_ForImport" placeholder="<?php echo JText::_("LNG_ENTER_DATA_HERE") ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="jbd-checkbox justify-content-end">
                            <label for="update_existing"><?php echo JText::_("LNG_UPDATE_EXISTING_LISTINGS") ?></label>
                            <input type="checkbox" id="update_existing" name="update_existing">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline">
                            <label for="delimiter"><?php echo JText::_('LNG_DELIMITER')?> </label>
                            <select name="delimiter">
                                <option value=";"><?php echo JText::_('LNG_SEMICOLON')?></option>
                                <option value=","><?php echo JText::_('LNG_COMMA')?></option>
<!--                                <option value="|">--><?php //echo JText::_('LNG_VERTICAL_LINE')." (just for the custom work)"?><!--</option>-->
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                <input type="hidden" name="task" id="task" value="companies.importCompaniesFromCsv" />
                
                <?php echo JHTML::_( 'form.token' ); ?>
            </div>
            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="button" class="jmodal-btn jmodal-btn-submit" onclick="startImport()"><?php echo JText::_("LNG_IMPORT")?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="jbd-container" id="batch-model" style="display: none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title" id="jbd-progress-title"><?php echo JText::_('LNG_MESSAGE_IMPORT_INIT') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
           
            <p id="jbd-progress-message"><?php echo JText::_('LNG_MESSAGE_IMPORT_INIT_MESSAGE'); ?></p>

            <div id="progress" class="progress progress-striped active">
                <div id="progress-bar" class="bar bar-success" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <input id="jbd-indexer-token" type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />
        </div>
    </div>
</div>

<script>

   function openImportForm() {
		var option = document.getElementById('formId');
		if (option.value == 1) {
			jQuery("#import-from-txt-area").slideUp(500);
			jQuery("#import-from-file").slideDown(500);
            jQuery('#task').val('companies.prepareImport');
            jQuery('#companyData_ForImport').val('');
		} else if (option.value == 2) {
			jQuery("#import-from-file").slideUp(500);
            jQuery("#import-from-txt-area").slideDown(500);
            jQuery('#task').val('companies.importCompaniesFromTxtArea');
			var file = document.getElementById('csvFile');
			file.value = null;
		}
	}
	
    function startImport(){
        var option = document.getElementById('formId');
        if(option.value == 2){
            jQuery("#importForm").submit();
        }else{
            // jQuery("#importForm").submit(function(e) {
            //     e.preventDefault();    
            //     var formData = new FormData(this);
            //     jQuery(".jmodal-btn-submit").addClass("loader").attr("disabled", true)
            //     jQuery.ajax({
            //         url: window.location.pathname,
            //         type: 'POST',
            //         data: formData,
            //         cache: false,
            //         processData: false,
            //         contentType: false,

            //         success: function (data) {
            //             console.debug(data);
            //             jQuery.jbdModal.close();
            //             jQuery("#batch-model").jbdModal();
            //             jbdBatch.initialize('companies','startImportingAjax',0);
            //             jQuery(".jmodal-btn-submit").removeClass("loader").attr("disabled", false)
            //         },
            //         error: function (xhr, desc, err)
            //         {
            //             console.log("error");

            //         }
            //     });                
            // });
            jQuery("#importForm").submit();            
        }
    }

</script>