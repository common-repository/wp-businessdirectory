<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
var uploadInstance;
window.addEventListener('load', function() {
    JBD.submitbutton = function(task) {
        jQuery("#adminForm").validationEngine('detach');
        if (task == 'applicationsettings.cancel' || task == 'database.fix' || jbdUtils.validateAppSettingsForm(true, false,'adminForm')) {
            if (task == 'language.delete'){
                if (confirm('<?php echo JText::_("LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE", true,true);?>')) {
                    JBD.submitform(task);
                }else{
                    return;
                }
            }
            JBD.submitform(task, document.getElementById('adminForm'));
        }
        jQuery("#adminForm").validationEngine('detach');
    };

    uploadInstance = JBDUploadHelper.getUploadInstance({
      'removePath': removePath
    });
});
</script>

<style>
.jbd-container #content-wrapper{
    background: none;
    padding: 5px;
}

.jbd-container dl.tabs dt h3 {
	margin: 0;
	padding: 0;
	font-size: 1em;
	font-weight: normal;
	line-height: 25px;
}

.jbd-container dl.tabs dt h3 a:link {
	color:#333333;
	outline:medium none;
	text-decoration:none;
}
.jbd-container dl.tabs {
    float: none;
	display: flex;
    justify-content: space-between;
	margin: 10px 0 15px 0;
	z-index: 10;
	border-radius: 5px;
    box-shadow: 0px 0px 9px -5px rgba(0, 0, 0, .75);
    overflow: hidden;
}

.jbd-container dl.tabs dt {
    margin: 0;
    float: none;
	padding: 9px 15px;
	border: 0px;
	border-right: 1px solid rgb(206, 206, 206);
	background: #fff;
	color: #5A5A5A;
	flex-grow: 1;
    text-align: left;
    transition: all ease .3s
}

.jbd-container dl.tabs dt:hover{
	background: #23B26B;
}

.jbd-container dl.tabs dt:hover h3 a:link{
	color: #fff;
}

.jbd-container dl.tabs dt.open {
	background: #23B26B;
	z-index: 10;
	color: #fff;
	border-bottom: 0px;
}

.jbd-container dl.tabs dt.open h3 a:link{
	color: #fff;
} 

.jbd-container div.current {
	clear: both;
	border: 1px solid #ccc;
	padding: 10px 10px;
	box-shadow: 0px 0px 9px -5px rgba(0, 0, 0, .75);
   	background: white;
}

.jbd-container div.current dd {
	padding: 0;
	margin: 0;
}

.jbd-container dl#content-pane.tabs {
	margin: 1px 0 0 0;
}


.tooltip {
  position: absolute;
  z-index: 1080;
  display: block;
  margin: 0;
  font-style: normal;
  font-weight: 400;
  line-height: 1.2;
  text-align: left;
  text-align: start;
  text-decoration: none;
  text-shadow: none;
  text-transform: none;
  letter-spacing: normal;
  word-break: normal;
  word-spacing: normal;
  white-space: normal;
  line-break: auto;
  font-size: 13px;
  word-wrap: break-word;
  opacity: 0;
}
.tooltip strong{
	padding-bottom: 15px !important;
	margin-bottom: 15px !important;
}
.tooltip.show {
  opacity: 0.9;
}
.tooltip .tooltip-arrow {
  position: absolute;
  display: block;
  width: 0.8rem;
  height: 0.4rem;
}
.tooltip .tooltip-arrow::before {
  position: absolute;
  content: "";
  border-color: transparent;
  border-style: solid;
}

.bs-tooltip-top, .bs-tooltip-auto[data-popper-placement^=top] {
  padding: 0.4rem 0;
}
.bs-tooltip-top .tooltip-arrow, .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow {
  bottom: 0;
}
.bs-tooltip-top .tooltip-arrow::before, .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before {
  top: -1px;
  border-width: 0.4rem 0.4rem 0;
  border-top-color: #000;
}

.bs-tooltip-end, .bs-tooltip-auto[data-popper-placement^=right] {
  padding: 0 0.4rem;
}
.bs-tooltip-end .tooltip-arrow, .bs-tooltip-auto[data-popper-placement^=right] .tooltip-arrow {
  left: 0;
  width: 0.4rem;
  height: 0.8rem;
}
.bs-tooltip-end .tooltip-arrow::before, .bs-tooltip-auto[data-popper-placement^=right] .tooltip-arrow::before {
  right: -1px;
  border-width: 0.4rem 0.4rem 0.4rem 0;
  border-right-color: #000;
}

.bs-tooltip-bottom, .bs-tooltip-auto[data-popper-placement^=bottom] {
  padding: 0.4rem 0;
}
.bs-tooltip-bottom .tooltip-arrow, .bs-tooltip-auto[data-popper-placement^=bottom] .tooltip-arrow {
  top: 0;
}
.bs-tooltip-bottom .tooltip-arrow::before, .bs-tooltip-auto[data-popper-placement^=bottom] .tooltip-arrow::before {
  bottom: -1px;
  border-width: 0 0.4rem 0.4rem;
  border-bottom-color: #000;
}

.bs-tooltip-start, .bs-tooltip-auto[data-popper-placement^=left] {
  padding: 0 0.4rem;
}
.bs-tooltip-start .tooltip-arrow, .bs-tooltip-auto[data-popper-placement^=left] .tooltip-arrow {
  right: 0;
  width: 0.4rem;
  height: 0.8rem;
}
.bs-tooltip-start .tooltip-arrow::before, .bs-tooltip-auto[data-popper-placement^=left] .tooltip-arrow::before {
  left: -1px;
  border-width: 0.4rem 0 0.4rem 0.4rem;
  border-left-color: #000;
}

.tooltip-inner {
  max-width: 200px;
  padding: 0.25rem 0.5rem;
  color: #fff;
  text-align: left;
  background-color: #000;
  border-radius: 0.25rem;
}

.tooltip-inner strong{
	padding-bottom: 5px;
}

</style>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=applicationsettings');?>" method="post" name="adminForm" id="adminForm">

        <div class="appsettings-search w-25 has-jicon-left">
            <i class="la la-search"></i>
            <input type="text" class="form-control" id="settings-search" placeholder="<?php echo JText::_('LNG_SEARCH_SETTING') ?>" />
        </div>

        <div id="validation-error" class="validation-error" style="display:none">
            <i class="la la-warning"></i> <?php echo JText::_("LNG_VALIDATION_ERROR")?>
        </div>
    	<div class="col100">
    		<?php 	
				$options = array(
						    'onActive' => 'function(title, description){
						        description.setStyle("display", "block");
						        title.addClass("open").removeClass("closed");
						    }',
						    'onBackground' => 'function(title, description){
						        description.setStyle("display", "none");
						        title.addClass("closed").removeClass("open");
						    }',
						    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
						    'useCookie' => true, // this must not be a string. Don't use quotes.
				);

                $jbdTabs = new JBDTabs();
                $jbdTabs->setOptions($options);

				echo $jbdTabs->startTabSet('tab_general_id');
				
				echo $jbdTabs->addTab('tab_general_id','panel_1', JText::_('LNG_GENERAL_SETTINGS'));
				require_once 'general.php';
                echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_2', JText::_('LNG_COMPANY_DETAILS'));
				require_once 'businessdetails.php';
                echo $jbdTabs->endTab();
				
				echo $jbdTabs->addTab('tab_general_id','panel_3', JText::_('LNG_SEO'));
				require_once 'seo.php';
				echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_4', JText::_('LNG_METADATA_SETTINGS'));
				require_once 'metadata.php';
                echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_5', JText::_('LNG_FRONT_END'));
				require_once 'frontend.php';
                echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_6', JText::_('LNG_BUSINESS_LISTINGS'));
				require_once 'businesslistings.php';
                echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_7', JText::_('LNG_ATTRIBUTES_CONFIG'));
				require_once 'defaultattributes.php';
				echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_8', JText::_('LNG_OFFERS'));
				require_once 'businessoffers.php';
                echo $jbdTabs->endTab();
				
				echo $jbdTabs->addTab('tab_general_id','panel_9', JText::_('LNG_EVENTS'));
				require_once 'businessevents.php';
                echo $jbdTabs->endTab();

				echo $jbdTabs->addTab('tab_general_id','panel_10', JText::_('LNG_LANGUAGES'));
				require_once 'languages.php';
                echo $jbdTabs->endTab();


				 if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/conferences.php')){
				    echo $jbdTabs->addTab('tab_general_id','panel_11', JText::_('LNG_CONFERENCES'));
				    require_once 'conferences.php';
				    echo $jbdTabs->endTab();
				} 

				echo $jbdTabs->addTab('tab_general_id','panel_12', JText::_('LNG_CUSTOM_CSS'));
				require_once 'customcss.php';
                echo $jbdTabs->endTab();

				echo $jbdTabs->endTabSet();
			?>

    		</div>
    	<input type="hidden" name="sendmail_from" value="<?php echo $this->item->sendmail_from?>" />
    	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    	<input type="hidden" name="task" value="" />
    	<input type="hidden" name="applicationsettings_id" value="<?php echo $this->item->applicationsettings_id?>" />
    	<?php echo JHtml::_('form.token'); ?>
    </form>
</div>

<script>

    var appImgFolder = '<?php echo APP_PICTURES_PATH ?>';
    var appImgFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_COMPANY_LOGO?>&_path_type=1&_target=<?php echo urlencode(APP_PICTURES_PATH)?>';
	var removePath = '<?php echo JBusinessUtil::getUploadUrl('remove') ?>&_path_type=2&_filename=';

    window.addEventListener('load', function() {
		jQuery("#enable_packages1").click(function(){
			jQuery("#assign-packages").show();
		});

		jQuery("#enable_packages2").click(function(){
			jQuery("#assign-packages").hide();
		});

		jbdTabs.setPresentTabs([1,2,3,4,5,6]);
		
		jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});

        jQuery('select').chosen();
		jQuery('[data-toggle="tooltip"]').each(function(){
			jQuery(this).attr("data-bs-original-title", jQuery(this).attr("data-original-title"));
		});
		jQuery('[data-toggle="tooltip"]').tooltip();

        jbdUtils.setProperty("enable_crop", false);
        var settingUrl = jbdUtils.getAjaxUrl('searchSettingAjax', 'applicationsettings', 'applicationsettings');
        jQuery('#settings-search').keyup(function (e) {
            jQuery('#settings-search').autocomplete({
                source: function (request, response) {
                    jQuery.ajax({
                        type: "GET",
                        url: settingUrl,
                        dataType: 'json',
                        data: {keyword: jQuery('#settings-search').val()},
                        success: function(data){
                            response(jQuery.map(data, function (item) {
                                return {
                                    label: item.text,
                                    description: item.description,
                                    value: item.text,
                                    field: item.name
                                };
                            }));
                        }
                    });
                },
                appendTo: "#jbd-container",
                select: function (event, ui) {
                    var field = jQuery('[name="'+ui.item.field+'"]');
                    if (field.length === 0) {
                        field = jQuery('[name="'+ui.item.field+'[]"]');
                    }

                    var tabId = field.closest('.app_tab').attr('id');
                    jQuery('.'+tabId).click();
                    jQuery('html, body').animate({ scrollTop: field.closest('.control-group').offset().top - 150}, 'slow');
                    field.closest('.control-group').effect("highlight", {}, 1500);
                }
            }).autocomplete().data("uiAutocomplete")._renderItem = function(ul, item) {
                return jQuery('<li>').append("<div><p>"+item.label+"<br/><small class='text-muted'>"+item.description+"</small></p></div>").appendTo(ul);
            };
		});

		let startDate = moment();
		let endDate = moment();
		<?php 
			if (!empty($this->item->trail_weeks_dates)) {
				$dateRange = JBusinessUtil::processDateRange($this->item->trail_weeks_dates); 

				echo "startDate = moment('$dateRange->startDate');";
				echo "endDate = moment('$dateRange->endDate');";
				echo "jQuery('#trailDates').val(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));";
			}
		?> 

		jQuery('#trailDates').daterangepicker({
				startDate: startDate,
				endDate: endDate,
				locale:{
					applyLabel: "<?php echo JText::_("LNG_APPLY")?>",
					cancelLabel: "<?php echo JText::_("LNG_CANCEL")?>",
					fromLabel: "<?php echo JText::_("LNG_FROM")?>",
					toLabel: "<?php echo JText::_("LNG_TO")?>",
					customRangeLabel: "<?php echo JText::_("LNG_CUSTOM_RANGE")?>"
				},
				autoUpdateInput: false,
				autoclose: false
		});

		jQuery('#trailDates').on('cancel.daterangepicker', function(ev, picker) {
			jQuery(this).val('');
			jQuery('#trail_weeks_dates').val('');
			jQuery('#trailDates').show();
		});

		jQuery('#trailDates').on('apply.daterangepicker', function(ev, picker) {
			jQuery(this).val(picker.startDate.format('DD MMM, YYYY') + ' - ' + picker.endDate.format('DD MMM, YYYY'));
			jQuery('#trail_weeks_dates').val(picker.startDate.format('DD-MM-YYYY') + ':' + picker.endDate.format('DD-MM-YYYY'));
			jQuery('#trailDates').show();
		});

		jQuery('#trailDates').on('hide.daterangepicker', function(ev, picker) {
			console.log('hide');
			jQuery('#trailDates').show();
		});

		jQuery(document).on('click',function (e) {
			jQuery('#trailDates').show();
		});
	});

	function resetTrailDates() {
		jQuery('#trailDates').val('');
		jQuery('#trail_weeks_dates').val('');
	}

	function clearColor() {
		jQuery("#colorpicker").val("");
		jQuery(".minicolors-swatch").html("");
	}

</script>

<?php JBusinessUtil::loadUploadScript(); ?>
