<?php 
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
 JBusinessUtil::initializeChosen();
?>

<div id="jbd-container" class="jbd-container jbd-edit-container container-fluid statistics-dashboard">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=statistics');?>" method="post" name="adminForm" id="adminForm">
		<div class="card-content row">
			<div class="col-12 pb-0">
				<div class="card jbox-card container-fluid  py-3">
					<div class="w-100 py-2" id="tabs">
						<div id="dir-dashboard-tabs" >
							<div class="row" id="dir-dashboard-tabs-col">
								<div class="col-lg-3 col-md-6 d-flex justify-content-center align-items-center py-2">
									<select onchange="updateCategories();requestData();" name="object_type" id="object_type" class="form-control chosen-select validate[required]">
										<option value="<?php echo STATISTIC_ITEM_BUSINESS ?>"><?php echo JText::_('LNG_BUSINESS_LISTINGS');?></option>
										<?php if($this->appSettings->enable_offers) { ?>
											<option value="<?php echo STATISTIC_ITEM_OFFER ?>"><?php echo JText::_('LNG_OFFERS');?></option>
										<?php } ?>
										<?php if($this->appSettings->enable_events) { ?>
											<option value="<?php echo STATISTIC_ITEM_EVENT ?>"><?php echo JText::_('LNG_EVENTS');?></option>
										<?php } ?>
										<?php if(file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/conference.php')) { ?>
											<option value="<?php echo STATISTIC_ITEM_SESSION_LOCATION ?>"><?php echo JText::_('LNG_SESSION_LOCATIONS');?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-3 col-md-6 d-flex py-2">
									<div class="has-jicon-left align-items-center w-100">
										<input autocomplete="off" type="text" class="inputbox search-field" id="item_name" style="height: auto; margin-bottom: auto;" placeholder="<?php echo JText::_('LNG_ITEM_NAME') ?>" />
										<i class="la la-search"></i>
									</div>
								</div>

								<div class="col-lg-6 col-md-12 d-flex justify-content-start align-items-center py-2">
									<div class="has-jicon-left">
										<input type='text' class="inputbox calendar-date front-calendar pull-right" style="height: auto; margin-bottom: auto;" name='startEndDate' id="startEndDate" placeholder="<?php echo JText::_("LNG_PICK_A_DATE")?>">
										<i class="la la-calendar"></i>
										<input type='hidden' name='dateRange' id="dateRange" >
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3 col-md-6 d-flex justify-content-center align-items-center py-2">
									<select onchange="requestData()" name="action_type" id="action_type" class="form-control chosen-select validate[required]">
										<option value="<?php echo STATISTIC_TYPE_VIEW ?>"><?php echo JText::_('LNG_VIEW');?></option>
										<option value="<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>"><?php echo JText::_('LNG_WEBSITE_CLICKS');?></option>
										<option value="<?php echo STATISTIC_TYPE_CONTACT ?>"><?php echo JText::_('LNG_CONTACTS');?></option>
										<option value="<?php echo STATISTIC_TYPE_SHARE ?>"><?php echo JText::_('LNG_SHARE');?></option>
										<!-- <option value="<?php echo STATISTIC_TYPE_ARTICLE_CLICK ?>"><?php echo JText::_('LNG_ARTICLE_CLICK');?></option> -->
									</select>
								</div>

								<div class="col-lg-3 col-md-6 d-flex justify-content-center align-items-center py-2">
									<select onchange="requestData()" name="category_id" id="category_id" data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="form-control chosen-select validate[required]">
										<option value=""><?php echo JTEXT::_("LNG_ALL_CATEGORIES"); ?></option>
										<?php echo JHtml::_('select.options', $this->listingCategoryOptions, 'value', 'text', '');?>
									</select>
								</div>

								<div class="col-lg-3  col-md-12 d-flex justify-content-center align-items-center py-2">
									<select onchange="requestData()" name="group_by" id="group_by" class="form-control chosen-select validate[required]">
										<option value="<?php echo STATISTIC_ORDER_BY_DAY ?>"><?php echo JText::_('LNG_GROUP_BY_DAY');?></option>
										<option value="<?php echo STATISTIC_ORDER_BY_MONTH ?>"><?php echo JText::_('LNG_GROUP_BY_MONTH');?></option>
										<option value="<?php echo STATISTIC_ORDER_BY_YEAR ?>"><?php echo JText::_('LNG_GROUP_BY_YEAR');?></option>
									</select>
								</div>
								<input type="hidden" id="itemId" name="itemId" value="" />
							</div>
							<div id="statisticsTab">
								<div id="graph"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php echo $this->loadTemplate('export'); ?>

<script type="text/javascript">

	var filters = null;
	var chart = null;
	window.addEventListener('load', function() {
		jQuery("#item_name").bind("focusout", function(e) {
		if (!jQuery('#item_name').val()) {
			filters.itemId = null;
			jQuery('#itemId').val('');
			requestData();
		}
	});

	

        initDatePicker();

		filters = {
			  object_type:null,
			  action_type:null,
			  category_id:null,
			  group_by:null,
			  itemId:null,
			  start_date:null,
			  end_date:null
		};

		chart = Morris.Area({
			element: 'graph',
		  	data: [{date: '<?php echo date("d-m-Y"); ?>', value: 0}],
		  	fillOpacity: 0.6,
		  	hideHover: 'auto',
		  	behaveLikeLine: true,
		  	resize: true,
		  	lineColors: ['#54cdb4'],
		  	xkey: 'date',
		  	ykeys: ['value'],
		  	labels: ['Total'],
		  	xLabelFormat: function(d) {
				return jbdUtils.getDateWithFormat(d);
		  	},
		  	dateFormat: function(unixTime) {
				var d = new Date(unixTime);
			  	return jbdUtils.getDateWithFormat(d);
		  	}
		});

        let url = jbdUtils.getAjaxUrl('getItemByStringAjax', 'statistics', 'managestatistics');

        if(jbdUtils.getProperty("isProfile") != 1) {
            url = url + '&objectType='+jQuery('#object_type').val()
        }else{
            url = url + '&isProfile=1&objectType='+jQuery('#object_type').val();
        }

		jQuery("#item_name").autocomplete({
			source: url,
			minLength: 1,
			select: function (event, ui) {
				jQuery('#itemId').val(ui.item.id);
				keyword = ui.item.label;
				jQuery(this).val(ui.item.name);
				requestData();
				return false;
			}

		}).data('ui-autocomplete')._renderItem = function(ul, item) {
			return jQuery('<li>').append("<div><i class='la la-building'></i> "+item.name+"</div>").appendTo(ul);
		};

		if(jbdUtils.getProperty("isProfile") != 1) {
			jQuery(".chosen-select").chosen({width: "95%", search_contains: false, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
		}

		setTimeout(function(){
			requestData();
		}, 500);
	});

   	function requestData() {
		applyFilter();

        let urlStatistics = jbdUtils.getAjaxUrl('getStatistics', 'statistics', 'managestatistics');

		jQuery.ajax({
			url: urlStatistics,
			dataType: 'json',
			type: 'GET',
			data: {
                isProfile: jbdUtils.getProperty("isProfile"),
				object_type:filters.object_type,
				action_type:filters.action_type,
				category_id:filters.category_id,
				group_by:filters.group_by,
				itemId:filters.itemId,
				start_date:filters.start_date,
				end_date:filters.end_date
			}
		})
		.done(function(data) {
			chart.setData(data);
		})
		.fail(function(data) {
			console.log("Error");
			console.log(JSON.stringify(data));
		});
	}

	function applyFilter(){
		filters.object_type = jQuery('#object_type').val();
        let url = jbdUtils.getAjaxUrl('getItemByStringAjax', 'statistics', 'managestatistics');
		if(jbdUtils.getProperty("isProfile") != 1) {
			jQuery("#item_name").autocomplete('option', 'source', url + '&objectType='+filters.object_type);
		}else{
			jQuery("#item_name").autocomplete('option', 'source', url + '&isProfile=1&objectType='+filters.object_type);
		}
		filters.action_type = jQuery('#action_type').val();
		filters.category_id = jQuery('#category_id').val();
		filters.group_by = jQuery('#group_by').val();
		if (jQuery('#item_name').val() != '') {
			filters.itemId = jQuery('#itemId').val();
		}else{
			filters.itemId = null;
		}

		var dateRange = jQuery("#dateRange").val();
		var data = dateRange.split(":");
		var start_date = data[0];
		var end_date = data[1];
		filters.start_date = start_date;
		filters.end_date = end_date;
	}

	function updateCategories() {
		var listingType = '<?php echo STATISTIC_ITEM_BUSINESS ?>';
		var offerType = '<?php echo STATISTIC_ITEM_OFFER ?>';
		var defaultText = '<?php echo JTEXT::_("LNG_ALL_CATEGORIES") ?>';
		var type = jQuery('#object_type').val();
		if (type == listingType) {
			var categories = <?php echo json_encode($this->listingCategoryOptions) ?>;
		} else if (type == offerType) {
			var categories = <?php echo json_encode($this->offerCategoryOptions) ?>;
		} else {
			var categories = <?php echo json_encode($this->eventCategoryOptions) ?>;
		}
		jQuery('#category_id').empty().append('<option selected="selected" value="">' + defaultText + '</option>');

		jQuery.each(categories, function (i, val) {
			jQuery('#category_id').append('<option value="' + val.value + '">' + val.text + '</option>');
		});
		if (jbdUtils.getProperty("isProfile") != 1) {
            jbdUtils.updateChosenSelect('#category_id');
		}
	}

	function initDatePicker() {
        var start = moment().subtract(29, 'days');
        var end = moment();

        jQuery('#startEndDate').daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'center',
            ranges: {
                "<?php echo JText::_("LNG_TODAY")?>": [moment(), moment()],
                "<?php echo JText::_("LNG_YESTERDAY")?>": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "<?php echo JText::_("LNG_LAST_7_DAYS")?>": [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
                "<?php echo JText::_("LNG_LAST_30_DAYS")?>": [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
                "<?php echo JText::_("LNG_THIS_MONTH")?>": [moment().startOf('month'), moment().endOf('month')]
            },
            locale:{
                applyLabel: "<?php echo JText::_("LNG_APPLY")?>",
                cancelLabel: "<?php echo JText::_("LNG_CANCEL")?>",
                fromLabel: "<?php echo JText::_("LNG_FROM")?>",
                toLabel: "<?php echo JText::_("LNG_TO")?>",
                customRangeLabel: "<?php echo JText::_("LNG_CUSTOM_RANGE")?>",
                separator:"-",
                format:'DD/MM/YYYY'
            }
        });


        jQuery('#dateRange').val(start.format('DD-MM-YYYY') + ':' + end.format('DD-MM-YYYY'));
        jQuery('#startEndDate').val(start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY'));

        jQuery('input[name="startEndDate"]').bind("keypress", function(e) {
            if (e.keyCode === 13) {
                jQuery('.daterangepicker .applyBtn').click();
            }
        });

        jQuery('input[name="startEndDate"]').on('apply.daterangepicker', function(ev, picker) {
            jQuery(this).val(picker.startDate.format('DD/MM/YYYY') + picker.locale.separator + picker.endDate.format('DD/MM/YYYY'));
            jQuery('#dateRange').val(picker.startDate.format('DD-MM-YYYY') + ':' + picker.endDate.format('DD-MM-YYYY'));
            requestData();
        });
    }

</script>