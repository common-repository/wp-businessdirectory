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
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task == 'report.cancel' || !jbdUtils.validateCmpForm(false, false)) {
			JBD.submitform(task, document.getElementById('item-form'));
		}
    }
});
</script>

<?php 
	$appSetings = JBusinessUtil::getApplicationSettings();
?>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <div class="">
                    <fieldset class="boxed">
                        <h2> <?php echo JText::_('LNG_REPORT_DETAILS');?></h2>
                        <p><?php echo JText::_('LNG_REPORT_INFO_TXT');?></p>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="name"><?php echo JText::_('LNG_NAME')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <input type="text"	name="name" id="name" class="form-control validate[required]" value="<?php echo $this->escape($this->item->name) ?>"  maxLength="100">
                            </div>
                            <div class="form-group">
                                <label for="description"><?php echo JText::_('LNG_DESCRIPTION')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?></label>
                                <textarea name="description" id="description" class="form-control h-auto validate[required]" cols="75" rows="5" ><?php echo $this->item->description ?></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="boxed">
                        <h2> <?php echo JText::_('LNG_REPORT_TYPE');?></h2>
                        <p> <?php echo JText::_('LNG_REPORT_TYPE_INFORMATION_TEXT');?>.</p>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_TYPE')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="type" name="type" class="validate[required] chosen-select">
                                        <option value='0' <?php echo $this->item->type=="0"? "selected" : ""?>><?php echo JText::_('LNG_COMPANY')?></option>
                                        <option value='1' <?php echo $this->item->type=="1"? "selected" : ""?>><?php echo JText::_('LNG_CONFERENCE')?></option>
                                        <option value='2' <?php echo $this->item->type=="2"? "selected" : ""?>><?php echo JText::_('LNG_OFFER')?></option>
                                </select>
                            </div>
                        </div>

                        <h2> <?php echo JText::_('LNG_DISPLAY_INACTIVE_ITEMS');?></h2>
                        <p> <?php echo JText::_('LNG_DISPLAY_INACTIVE_ITEMS_TEXT');?>.</p>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_ITEM')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="listing_status" name="listing_status" class="validate[required] chosen-select">
                                    <option value='2' <?php echo 2 == $this->item->listing_status ? "selected":"" ; ?>><?php echo JText::_('LNG_ALL')?></option>
                                    <option value='1' <?php echo 1 == $this->item->listing_status ? "selected":"" ; ?>><?php echo JText::_('LNG_ACTIVE')?></option>
                                    <option value='0' <?php echo 0 == $this->item->listing_status ? "selected":"" ; ?>><?php echo JText::_('LNG_INACTIVE')?></option>
                                </select>
                            </div>
                        </div>

                        <h2> <?php echo JText::_('LNG_STATISTICS_DATE_RANGE');?></h2>
                        <p> <?php echo JText::_('LNG_STATISTICS_DATE_RANGE_TEXT');?>.</p>
                        <div class="form-container label-w-100">
                            <div class=" align-items-center py-2">
                                <div class="has-jicon-left">
                                    <input type='text' class="inputbox front-calendar pull-right" name='startEndDate' id="startEndDate" placeholder="<?php echo JText::_("LNG_PICK_A_DATE")?>">
                                    <i class="la la-calendar"></i>
                                    <input type='hidden' name='dateRange' id="dateRange" >
                                </div>
                            </div>
                            <a href="javascript:clearDate()"><?php echo JText::_("LNG_CLEAR_DATE")?></a>
                        </div>
                    </fieldset>

                    <fieldset id="business_params" class="boxed">
                        <h2> <?php echo JText::_('LNG_REPORT_PARAMS');?></h2>
                        <p> <?php echo JText::_('LNG_REPORT_PARAMS_INFORMATION_TEXT');?>.</p>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_PARAMS')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="features" class="multiselect chosen" multiple="multiple" name="selected_params[]" size="10">
                                    <?php
                                    foreach($this->params as $key=>$param){
                                        if(in_array($key, $this->item->selected_params)>0)
                                            $selected = "selected='selected'";
                                        else
                                            $selected = "";
                                        echo "<option value='$key' $selected> ".JText::_($param)."</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_CUSTOM_PARAMS')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="features" class="multiselect" multiple="multiple" name="custom_params[]" size="10">
                                    <?php
                                    foreach($this->customFeatures as $feature){
                                        if(in_array($feature->code,$this->item->custom_params)>0)
                                            $selected = "selected='selected'";
                                        else
                                            $selected = "";
                                        echo "<option value='$feature->code' $selected>$feature->name</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset id="conference_params" class="boxed">
                        <h2> <?php echo JText::_('LNG_REPORT_PARAMS');?></h2>
                        <p> <?php echo JText::_('LNG_REPORT_PARAMS_INFORMATION_TEXT');?>.</p>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_PARAMS')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="features" class="multiselect" multiple="multiple" name="selected_conference_params[]" size="10">
                                    <?php
                                    foreach($this->conferenceParams as $key=>$conferenceParam){
                                        if(in_array($key, $this->item->selected_params)>0)
                                            $selected = "selected='selected'";
                                        else
                                            $selected = "";
                                        echo "<option value='$key' $selected> ".JText::_($conferenceParam)."</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset id="offer_params" class="boxed">
                        <h2> <?php echo JText::_('LNG_REPORT_PARAMS');?></h2>
                        <p> <?php echo JText::_('LNG_REPORT_PARAMS_INFORMATION_TEXT');?>.</p>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_PARAMS')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="features" class="multiselect" multiple="multiple" name="selected_offer_params[]" size="10">
                                    <?php
                                    foreach($this->offerParams as $key=>$offerParam){
                                        if(in_array($key, $this->item->selected_params)>0)
                                            $selected = "selected='selected'";
                                        else
                                            $selected = "";
                                        echo "<option value='$key' $selected> ".JText::_($offerParam)."</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-container label-w-100">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_CUSTOM_PARAMS')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                <select id="features" class="multiselect" multiple="multiple" name="custom_params[]" size="10">
                                    <?php
                                    foreach($this->offerCustomFeatures as $feature){
                                        if(in_array($feature->code,$this->item->custom_params)>0)
                                            $selected = "selected='selected'";
                                        else
                                            $selected = "";
                                        echo "<option value='$feature->code' $selected>$feature->name</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

		<script  type="text/javascript">
			function save() {
				if(jbdUtils.validateCmpForm(false, false))
					return false;
				jQuery("#item-form #task").val('report.save');
				var form = document.adminForm;
				form.submit();
			}

			function cancel() {
                if (confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_COUNTRIES_CONFIRM_CANCEL', true);?>'))
                {
                    jQuery("#item-form #task").val('report.cancel');
                    var form = document.adminForm;
                    form.submit();
                }else{
                    setInterval(function(){
                        jQuery(".button-cancel").removeClass("loader");
                    }
                    ,500);
                }
			}

			window.addEventListener('load', function() {
                jQuery('.multiselect').multi({
                    'enable_search': true,
                    'search_placeholder': '<?php echo JText::_("LNG_SEARCH") . "...";?>'
                });

				jQuery('#type').on('change', function() {
					if ( this.value == '1') {
						jQuery("select[name='selected_params']").empty();
						jQuery("select[name='custom_params']").empty();
						jQuery("select[name='selected_offer_params']").empty();
						jQuery("#business_params").hide();
						jQuery("#offer_params").hide();
						jQuery("#conference_params").show();
					} else if(this.value == '2'){
                        jQuery("select[name='selected_params']").empty();
						jQuery("select[name='custom_params']").empty();
						jQuery("select[name='selected_conference_params']").empty();
						jQuery("#conference_params").hide();
						jQuery("#business_params").hide();
						jQuery("#offer_params").show();
					} else {
						jQuery("select[name='selected_conference_params']").empty();
						jQuery("select[name='selected_offer_params']").empty();
						jQuery("#conference_params").hide();
						jQuery("#offer_params").hide();
						jQuery("#business_params").show();
					}
				}).trigger('change');

                jQuery(".chosen-select").chosen({placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
			});
		</script>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
		<input type="hidden" name="task" id="task" value="" /> 
		<input type="hidden" name="id" value="<?php echo $this->item->id ?>" /> 
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>

<script type="text/javascript">
    function clearDate(){
        jQuery('#startEndDate').val('');
        jQuery('#dateRange').val('');
    }

    window.addEventListener('load', function() {
        <?php if(!empty($this->item->start_date) && $this->item->start_date != '0000-00-00'){ ?>
            var savedStartDate = '<?php echo $this->item->start_date ?>';
            var savedEndDate = '<?php echo $this->item->end_date ?>';
            var start = moment(savedStartDate);
            var end = moment(savedEndDate);
        <?php }else{ ?>
             var start = moment().subtract(29, 'days');
             var end = moment();
        <?php } ?>

        jQuery('#startEndDate').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                "<?php echo JText::_("LNG_TODAY")?>": [moment(), moment()],
                "<?php echo JText::_("LNG_YESTERDAY")?>": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "<?php echo JText::_("LNG_LAST_7_DAYS")?>": [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
                "<?php echo JText::_("LNG_LAST_30_DAYS")?>": [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
                "<?php echo JText::_("LNG_THIS_MONTH")?>": [moment().startOf('month'), moment().endOf('month')]
            },
            locale:{
                applyLabel: "<?php echo JText::_("LNG_APPLY")?>",
                cancelLabel: "<?php echo JText::_("LNG_CLEAR")?>",
                fromLabel: "<?php echo JText::_("LNG_FROM")?>",
                toLabel: "<?php echo JText::_("LNG_TO")?>",
                customRangeLabel: "<?php echo JText::_("LNG_CUSTOM_RANGE")?>",
                separator:"-",
                format:'DD/MM/YYYY'
            }
        });

        jQuery('#dateRange').val(start.format('DD-MM-YYYY') + ':' + end.format('DD-MM-YYYY'));
        jQuery('#startEndDate').val(start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY'));

        jQuery('input[name="startEndDate"]').bind("keypress focusout", function(e) {
            if (e.keyCode === 13 || e.type=='focusout') {
                jQuery('.daterangepicker .applyBtn').click();
            }
        });

        jQuery('input[name="startEndDate"]').on('apply.daterangepicker', function(ev, picker) {
            jQuery(this).val(picker.startDate.format('DD/MM/YYYY') + picker.locale.separator + picker.endDate.format('DD/MM/YYYY'));
            jQuery('#dateRange').val(picker.startDate.format('DD-MM-YYYY') + ':' + picker.endDate.format('DD-MM-YYYY'));
            jQuery('#startEndDate').show();
        });

	    <?php if(!(!empty($this->item->start_date) && $this->item->start_date != '0000-00-00')){ ?>
            clearDate();
        <?php }?>
    });
</script>