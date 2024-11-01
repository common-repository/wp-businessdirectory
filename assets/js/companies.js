//******************************************************//
//*********************** ADMIN ************************//
//******************************************************//

var editUrl;
var editUrlFront;
var deleteUrl;
var deleteUrlFront;

function setLocationUrl(eurl, eurlf, durl, durlf) {
    editUrl = eurl;
    editUrlFront = eurlf;
    deleteUrl = durl;
    deleteUrlFront = durlf;
}

//************--Company Section--*****************//
function uncheckAll() {
    jQuery('#related-listings option').attr('selected', false);
    jQuery('#related-listings').trigger('chosen:updated');
}

function extendPeriod(packageId) {
    if (jbdUtils.isProfile != 0) {
        jQuery("#task").val("managecompany.extendPeriod");
    } else {
        jQuery("#extend_package_id").val(packageId);
        jQuery("#task").val("company.extendPeriod");
    }

    jQuery("#extend_package_id").val(packageId);
    jQuery("#item-form").submit();
}

function checkAllActivityCities() {
    uncheckAllActivityCities();
    jQuery(".cities_ids-select option").each(function () {
        if (jQuery(this).val() != "") {
            activityCitiesList.add(jQuery(this));
        }
    });
    jQuery("#activity_cities option").each(function () {
        jQuery(this).attr("selected", "selected");
    });
}

function uncheckAllCategories() {
    jQuery('#selectedSubcategories option').attr('selected', false);
    jQuery('#mainSubcategory').empty();
    jQuery('#mainSubcategory option').trigger('chosen:updated');
    jQuery('#mainSubcategory option').trigger("chosen:updated");
    jQuery('#selectedSubcategories').trigger('chosen:updated');
    jQuery('#selectedSubcategories').trigger("chosen:updated");

    if (jbdUtils.enable_attribute_category == 1) {
        resetAttributes();
    }
}

function uncheckAllTypes() {
    jQuery('#companyTypes option').attr('selected', false);
    jQuery('#companyTypes').trigger('chosen:updated');
    jQuery('#companyTypes').trigger("chosen:updated");
}

function uncheckAllMemberships() {
    jQuery('#selectedMemberships option').attr('selected', false);
    jQuery('#selectedMemberships').trigger('chosen:updated');
}

function uncheckAllActivityCities() {
    jQuery("#activity_cities option").each(function () {
        jQuery(this).removeAttr("selected");
    });
    activityCitiesList.remove();
}

function editLocation(locationId, identifier) {
    var baseUrl = editUrl;
    if (jbdUtils.isProfile == 1) {
        baseUrl = editUrlFront;
    }
    baseUrl = baseUrl + "&locationId=" + locationId + "&identifier=" + identifier;
    jQuery("#location-frame").attr("src", baseUrl);
    
	var options = {
     	    modalClass: "jbd-modal jbd-location"
    }
    
    jQuery('#location-dialog').jbdModal(options);
    
}

function deleteLocation(locationId) {
    if (!confirm(JBD.JText._("LNG_DELETE_LOCATION_CONF"))) {
        return;
    }
    var baseUrl = deleteUrl;
    if (jbdUtils.isProfile == 1) {
        baseUrl = deleteUrlFront;
    }

    var postData = "&locationId=" + locationId;
    jQuery.post(baseUrl, postData, processDeleteLocationResult);
}

function processDeleteLocationResult(response) {
    var xml = response;
    jQuery(xml).find('answer').each(function () {
        if (jQuery(this).attr('error') == '1')
            jQuery("#location-box-" + jQuery(this).attr('locationId')).remove();
        else {
        	
        	var html= '<h3>' + JBD.JText._("LNG_LOCATION_DELETE_FAILED") + '</h3>';
        	jQuery(html).appendTo('body').jbdModal();
            setTimeout(function(){jQuery.jbdModal.close()}, 2000);
        }
    });
}

function updateLocation(id, name, streetNumber, address, city, county, country) {
    if (jQuery("#location-0").length > 0) {
        jQuery("#location-0").html(name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + ", " + country);
        jQuery("#location-0").attr("id", "#location-" + id);
    } else if (jQuery("#location-" + id).length > 0) {
        jQuery("#location-" + id).html(name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + ", " + country);
    }
    else {
        var locationContainer = '<div id="location-box-' + id + '" class="detail_box">';
        locationContainer += '<div id="location-' + id + '">' + name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + " ," + country + '</div>';
        locationContainer += '</div>';
        jQuery("#company-locations").append(locationContainer);
    }
}

function closeLocationDialog() {
    jQuery.jbdModal.close();
}

var currentTab = 1;
var currentTabIndex = 0;
var maxTabs = 6;
var tabMapInitialized = 0;

function setMaxTabs(max_tabs) {
    maxTabs = max_tabs;
}

function openTab(tab) {
    if (jbdUtils.isMultilingual) {
        jQuery(".tab-" + jbdUtils.defaultLang).each(function () {
            jQuery(this).click();
        });
    }

    jQuery("#item-form").validationEngine('detach');
    if (jbdUtils.validateRichTextEditors) {
        validateRichTextEditors();
    }

    validateMultiSelects();
    var validationResult = jQuery("#item-form").validationEngine('validate');

    if (!validationResult) {
        return;
    }

    showEditTab(tab);
}

function showEditTab(tab) {
    jQuery(".edit-tab").each(function () {
        jQuery(this).hide();
    });

    jQuery(".process-step").each(function () {
        jQuery(this).hide();
        jQuery(this).removeClass("active");

    });

    jQuery(".process-tab").each(function () {
        jQuery(this).removeClass("active");
    });

    if (currentTabIndex == 0) {
        jQuery("#prev-btn").hide();
    }
    else {
        jQuery("#prev-btn").show();
    }

    if ((currentTabIndex + 1) == maxTabs) {
        jQuery("#next-btn").hide();
        jQuery("#save-btn").show();
        jQuery("#term_conditions").show();
    }
    else {
        jQuery("#next-btn").show();
        jQuery("#save-btn").hide();
        jQuery("#term_conditions").hide();
    }

    jQuery("#edit-tab" + tab).show();
    jQuery("#step" + tab).show();

    if (tab != 1) {
        var scrollTopOfset = jQuery("#tab" + tab).offset().top - 150;
        jQuery('html,body').animate({scrollTop: scrollTopOfset}, 'slow');
    } else {
        jQuery(window).scrollTop(10);
    }

    jQuery("#step" + tab).addClass("active");
    jQuery("#tab" + tab).addClass("active");
    jQuery("#active-step-number").html(tab);
    if (tab == 3 && tabMapInitialized == 0) {
        initializeMap();
        tabMapInitialized = 1;
    }
}

function nextTab() {
    if (jbdUtils.isMultilingual) {
        jQuery(".tab-" + jbdUtils.defaultLang).each(function () {
            jQuery(this).click();
        });
    }

    if (jbdUtils.validateRichTextEditors) {
        validateRichTextEditors();
    }

    var validationResult = jQuery("#item-form").validationEngine('validate');
    if (validationResult) {
        if (currentTabIndex < presentTabs.length - 1) {
            currentTabIndex++;
            currentTab = presentTabs[currentTabIndex];
        }
        showEditTab(currentTab);
    }
}

function previousTab() {
    if (currentTabIndex > 0) {
        currentTabIndex--;
        currentTab = presentTabs[currentTabIndex];
    }

    showEditTab(currentTab);
}

function addNewContact(index) {
    var newIndex = parseInt(index) + 1;
    jQuery('#contact-form-box1').clone().prop('id', 'contact-form-box' + newIndex).appendTo('#contact_details');
    jQuery("#contact-form-box" + newIndex).find('h3').text(JBD.JText._('LNG_CONTACT') + ' ' + newIndex);
    jQuery('#contact-form-box' + newIndex + ' input').each(function () {
        jQuery(this).val('');
    });

    jQuery("#contact-form-box" + newIndex + " .remove-contact").attr('href', 'javascript:removeContact(\'' + newIndex + '\')').show();
    jQuery('#add_contact').attr('onclick', 'addNewContact(\'' + newIndex + '\')');

}

function removeContact(index) {
    if (index < 2)
        return;

    index = parseInt(index);
    jQuery('#contact-form-box' + index).remove();
}

function addNewTestimonial(index) {
    var newIndex = parseInt(index) + 1;
    jQuery('#testimonial-form-box1').clone().prop('id', 'testimonial-form-box' + newIndex).appendTo('#testimonial_details');
    jQuery("#testimonial-form-box" + newIndex).find('h3').text(JBD.JText._('LNG_TESTIMONIAL') + ' ' + newIndex);
    jQuery('#testimonial-form-box' + newIndex + ' input').each(function () {
        jQuery(this).val('');
    });

    jQuery('#testimonial-form-box' + newIndex + ' textarea').each(function () {
        jQuery(this).html('');
    });
    jQuery('#testimonial-form-box' + newIndex + ' textarea').val('');

    jQuery("#testimonial-form-box" + newIndex + " .remove-testimonial").attr('href', 'javascript:removeTestimonial(\'' + newIndex + '\')').show();
    jQuery('#add_testimonial').attr('onclick', 'addNewTestimonial(\'' + newIndex + '\')');

}

function removeTestimonial(index) {
    if (index < 2)
        return;

    index = parseInt(index);
    jQuery('#testimonial-form-box' + index).remove();
}

function addNewService(index, ServiceFolder, ServiceFolderPath) {
    var newIndex = parseInt(index) + 1;
    jQuery('#service-form-box1').clone().prop('id', 'service-form-box' + newIndex).appendTo('#service_details');
    jQuery("#service-form-box" + newIndex).find('h3').text(JBD.JText._('LNG_SERVICE') + ' ' + newIndex);
    jQuery('#service-form-box' + newIndex + ' input').each(function () {
        jQuery(this).val('');
    });
    jQuery('#service-form-box' + newIndex + ' textarea').each(function () {
        jQuery(this).html('');
    });
    jQuery('#service-form-box' + newIndex + ' textarea').val('');
    jQuery('#service-form-box' + newIndex + ' .input-imageLocation').prop('id', 'service-imageLocation' + newIndex);
    jQuery('#service-form-box' + newIndex + ' .input-imageLocationSize').prop('id', 'service-imageUploader' + newIndex);

    jQuery('#service-form-box' + newIndex + ' .services').prop('id', 'service-picture-preview' + newIndex);
    jQuery('#service-picture-preview' + newIndex).html('<i class="la la-image"></i>');

    jQuery('#service-form-box' + newIndex + ' #imageSelection2').find('a').prop('href', 'javascript:removeServiceLogo(' + newIndex + ')');
    jQuery('#service-form-box' + newIndex + ' #imageSelection2').find('label').prop('for', 'service-imageUploader' + newIndex);
    jQuery('#service-form-box' + newIndex + ' #imageSelection1').find('#service-imageUploader1').prop('id', 'service-imageUploader' + newIndex);
    jQuery('#service-form-box' + newIndex + '').find('#service-imageLocation1').prop('id', 'service-imageLocation' + newIndex);

    jQuery("#service-form-box" + newIndex + " .remove-service").attr('href', 'javascript:removeService(\'' + newIndex + '\')').show();
    jQuery('#add_service').attr('onclick', 'addNewService(\'' + newIndex + '\',\'' + ServiceFolder + '\',\'' + ServiceFolderPath + '\')');

    imageUploader(ServiceFolder, ServiceFolderPath, 'service-', newIndex);

}

function removeService(index) {
    if (index < 2)
        return;

    index = parseInt(index);
    jQuery('#service-form-box' + index).remove();
}

function removeServiceLogo(id) {
    jQuery('#service-imageLocation' + id).val("");
    jQuery('#service-picture-preview' + id).html("<i class='la la-image'></i>");
    jQuery('#service-imageUploader' + id).val("");
}

function disableWorkPeriod(day, mandatory, multiple) {
    if (typeof multiple === 'undefined') {
        multiple = true;
    }

    var status;
    var checked = jQuery('#work_status_check_' + day).is(":checked");
    var button;
    var dayRow = jQuery('#js_workhours_'+day);
    if (multiple) {
        dayRow = jQuery('#break_period_'+day);
    }

    if (checked) {
        status = 1;
        jQuery('#work_start_hour_' + day).prop('readonly', false);
        if (mandatory) {
            jQuery('#work_start_hour_' + day).addClass('validate[required]');
            jQuery('#work_end_hour_' + day).addClass('validate[required]');
        }
        jQuery('#work_end_hour_' + day).prop('readonly', false);

        button = dayRow.find('.js_action_buttons').find('a');
        button.text(JBD.JText._('LNG_ADD_BREAK'));
        button.attr('onclick', 'addBreak(' + day + ', ' + multiple + ')');
        button.attr('disabled', false);
        button.addClass('btn-success');
        button.removeClass('btn-outline-danger');
    }
    else {
        status = 0;
        jQuery('#work_start_hour_' + day).prop('readonly', true);
        if (mandatory) {
            jQuery('#work_start_hour_' + day).removeClass('validate[required]');
            jQuery('#work_end_hour_' + day).removeClass('validate[required]');
        }
        jQuery('#work_end_hour_' + day).prop('readonly', true);

        button = dayRow.find('.js_action_buttons').find('a');
        button.attr('disabled', true);
        button.text(JBD.JText._('LNG_CLOSED'));
        button.attr('onclick', '');
        button.removeClass('btn-success');
        button.addClass('btn-outline-danger');

        jQuery('#break_hours_day_' + day).empty();
        jQuery('#break_periods_count_' + day).val(0);
    }

    jQuery('#work_status_' + day).val(status);
}

function addBreak(day, multiple) {
    var id = parseInt(jQuery('#break_periods_count_' + day).val());
    var newId = id + 1;

    var dayRow = jQuery('#js_workhours_'+day);
    var button = dayRow.find('.js_action_buttons').find('a');

    if (typeof button.attr('disabled') !== 'undefined') {
        console.log("cannot add more than 1 break period");
        return false;
    }

    var html = '';
    html += '<div class="row align-items-center" id="break_hour_period_' + day + '_' + newId + '">';
    html += '<div class="col-1 p-1"></div>';
    html += '<div class="col-4 p-1"><input type="text" name="break_start_hour[]" class="form-control p-1 text-center timepicker" value="01:00 PM" /></div>';
    html += '<div class="col-4 p-1"><input type="text" name="break_end_hour[]" class="form-control p-1 text-center timepicker" value="02:00 PM" /></div>';
    if (multiple) {
        html += '<div class="col-2"><a href="javascript:void(0)" class="btn btn-danger btn-panel" onclick="deleteBreak(' + day + ', ' + newId + ')">';
    }
    else {
        html += '<div class="col-2"><a href="javascript:void(0)" class="btn btn-danger btn-panel" onclick="deleteCompanyBreak(' + day + ', ' + newId + ')">';
        button.attr('disabled', true);
    }
    html += '<i class="la la-trash"></i>';
    html += '</a></div>';
    html += '<input type="hidden" name="break_ids[]" id="break_ids" value="" />';
    html += '</div>';

    jQuery('#break_hours_day_' + day).append(html);
    jQuery('#break_periods_count_' + day).val(newId);
    if (!multiple) {
        jQuery('#break_period_' + day).find('.break-button').attr('disabled', true);
        jQuery('#break_period_' + day).find('.break-button').attr('onclick', '');
    }

    jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.timeFormat, 'minTime': '6:00am',});
}

function deleteCompanyBreak(day, id) {
    jQuery('#break_hour_period_' + day + '_' + id).remove();

    var dayRow = jQuery('#js_workhours_'+day);
    var button = dayRow.find('.js_action_buttons').find('a');

    button.attr('disabled', false);

    jQuery('#break_period_' + day).find('.break-button').removeAttr('disabled');
    var count = jQuery('#break_periods_count_' + day).val();
    if (count > 0) {
        count--;
    }
    jQuery('#break_periods_count_' + day).val(count);
    jQuery('#break_period_' + day).find('.break-button').attr('onclick', 'addBreak(' + day + ', false)');
}

function deleteBreak(day, id) {
    jQuery('#break_hour_period_' + day + '_' + id).empty();
    var count = parseInt(jQuery('#break_periods_count_' + day).val());
    var newCount = count - 1;

    jQuery('#break_periods_count_' + day).val(newCount);
}

function updateAttributes(categoryId, companyId) {
    if (jbdUtils.isProfile != 1) {
        var attributesUrl = jbdUtils.url + '&task=company.getAttributesAjax';
    } else {
        var attributesUrl = jbdUtils.url + '&task=managecompany.getAttributesAjax';
    }

    var packageId = 0;
    if (jbdUtils.enable_packages == 1)
        packageId = jQuery('#filter_package_select').val();

    jQuery.ajax({
        type: 'GET',
        url: attributesUrl,
        data: {categoryId: categoryId, companyId: companyId, packageId: packageId},
        dataType: 'json',
        success: function (data) {
            jQuery('#customFieldsContent').html(data);
            jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true});
        }
    });
}

function displaySubcategories(id, level, maxLevel) {
    var categoryId = jQuery("#" + id).val();

    if (!categoryId)
        categoryId = 0;
    //invalidate subcategories level
    for (var i = level + 1; i <= maxLevel; i++) {
        jQuery("#company_categories-level-" + i).html('');
    }
    jQuery("#company_categories-level-" + (level + 1)).html("<div style='width:20px;margin: 0 auto;'><img align='center' src='" + jbdUtils.imageRepo + "/assets/images/loading.gif'  /></div>");

    var postParameters = '';

    postParameters += "&categoryId=" + categoryId;

    var postData = '';
    if (jbdUtils.isProfile == 0)
        postData = '&option=com_jbusinessdirectory&task=company.getSubcategories' + postParameters;
    else
        postData = '&option=com_jbusinessdirectory&task=managecompany.getSubcategories' + postParameters;
    jQuery.post(jbdUtils.baseUrl, postData, processDisplaySubcategoriesResponse);
    //jQuery('#frmFacilitiesFormSubmitWait').show();
}

function processDisplaySubcategoriesResponse(responce) {
    var xml = responce;
    //jQuery('#frmFacilitiesFormSubmitWait').hide();
    jQuery(xml).find('answer').each(function () {
        if (jQuery(this).attr('error') == '1') {
            jQuery('#frm_error_msg_facility').className = 'text_error';
            jQuery('#frm_error_msg_facility').html(jQuery(this).attr('errorMessage'));
            jQuery('#frm_error_msg_facility').show();

        }
        else if (jQuery(this).attr('error') == '0') {

            jQuery("#subcategories").html(jQuery(this).attr('content_categories'));
            removeSelectedCategories();
            //clear current level
            jQuery("#company_categories-level-" + jQuery(this).attr('category-level')).html('');
            //clear next level
            level = 1 + parseInt(jQuery(this).attr('category-level'));
            jQuery("#company_categories-level-" + level).html('');
            if (jQuery(this).attr('isLastLevel') != '1') {
                jQuery("#company_categories-level-" + jQuery(this).attr('category-level')).html(jQuery(this).attr('content_select_categories'));

            }
        }
    });
}

function removeSelectedCategories() {
    jQuery("#mainSubcategory > option").each(function () {
        jQuery("#subcategories option[value=" + jQuery(this).val() + "]").remove();
    });
}

function hideDisapprovalBox() {
    jQuery("#disapprovalBox").hide();
}

function showDisapprovalBox() {
    jQuery("#disapprovalBox").show();
}

//*******************--End Company Section--************************//

//************--Company Service Provider Section--*****************//
function addVacation(id) {
    id = parseInt(id);
    var newId = id + 1;

    var deleteButton = '';
    deleteButton += '<br/>';
    deleteButton += '<a href="javascript:void(0)" class="btn btn-danger btn-panel mb-1" onclick="deleteVacation(' + newId + ')">';
    deleteButton += '<i class="la la-trash"></i>';
    deleteButton += '</a>';

    jQuery('#vacation_0').clone().prop('id', 'vacation_' + newId).insertAfter('#vacation_' + id);
    jQuery('#vacation_' + newId + ' #start_calendar_0').find('input').attr('id', 'start_date_' + newId);
    jQuery('#vacation_' + newId + ' #start_calendar_0').find('input').val('');
    jQuery('#vacation_' + newId + ' #start_calendar_0').find('button').attr('id', 'start_date_' + newId + '_img');
    jQuery('#vacation_' + newId + ' #end_calendar_0').find('input').attr('id', 'end_date_' + newId);
    jQuery('#vacation_' + newId + ' #end_calendar_0').find('input').val('');
    jQuery('#vacation_' + newId + ' #end_calendar_0').find('button').attr('id', 'end_date_' + newId + '_img');
    jQuery('#vacation_' + newId + ' #delete_vacation_0').prop('id', 'delete_vacation_' + newId);
    jQuery('#add_vacation').find('a').attr('onclick', 'addVacation(\'' + newId + '\')');
    jQuery('#delete_vacation_' + newId).html(deleteButton);

    initCalendar(newId);
    jQuery('#add_vacation_' + parseInt(id)).remove();
}

function deleteVacation(id) {
    var html = '';
    jQuery('#vacation_' + id).empty();
}

function updateServices() {
    var companyId = jQuery('#company_id').find(":selected").val();
    if (jbdUtils.isProfile == 0) {
        var urlGetServices = jbdUtils.url + '&task=companyserviceprovider.getServicesAjax';
    } else {
        var urlGetServices = jbdUtils.url + '&task=managecompanyserviceprovider.getServicesAjax';
    }

    jQuery.ajax({
        type: 'GET',
        url: urlGetServices,
        data: {companyId: companyId},
        dataType: 'json',
        success: function (data) {
            jQuery('#services').html(data);
            jQuery("#services").trigger("chosen:updated");
            jQuery("#services").trigger("chosen:updated");
        }
    });
}

function initCalendar(id) {
    Calendar._DN = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    Calendar._SDN = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    Calendar._FD = 0;
    Calendar._MN = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    Calendar._SMN = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    Calendar._TT = {
        "INFO": "About the Calendar",
        "ABOUT": "DHTML Date\/Time Selector\n(c) dynarch.com 2002-2005 \/ Author: Mihai Bazon\nFor latest version visit: http:\/\/www.dynarch.com\/projects\/calendar\/\nDistributed under GNU LGPL.  See http:\/\/gnu.org\/licenses\/lgpl.html for details.\n\nDate selection:\n- Use the \u00ab and \u00bb buttons to select year\n- Use the < and > buttons to select month\n- Hold mouse button on any of the buttons above for faster selection.",
        "ABOUT_TIME": "\n\nTime selection:\n- Click on any of the time parts to increase it\n- or Shift-click to decrease it\n- or click and drag for faster selection.",
        "PREV_YEAR": "Select to move to the previous year. Select and hold for a list of years.",
        "PREV_MONTH": "Select to move to the previous month. Select and hold for a list of the months.",
        "GO_TODAY": "Go to today",
        "NEXT_MONTH": "Select to move to the next month. Select and hold for a list of the months.",
        "SEL_DATE": "Select a date.",
        "DRAG_TO_MOVE": "Drag to move.",
        "PART_TODAY": " Today ",
        "DAY_FIRST": "Display %s first",
        "WEEKEND": "0,6",
        "CLOSE": "Close",
        "TODAY": "Today",
        "TIME_PART": "(Shift-)Select or Drag to change the value.",
        "DEF_DATE_FORMAT": "%Y-%m-%d",
        "TT_DATE_FORMAT": "%a, %b %e",
        "WK": "wk",
        "TIME": "Time:"
    };
    Calendar.setup({
        // Id of the input field
        inputField: "start_date_" + id,
        // Format of the input field
        ifFormat: "%d-%m-%Y",
        // Trigger for the calendar (button ID)
        button: "start_date_" + id + "_img",
        // Alignment (defaults to "Bl")
        align: "Tl",
        singleClick: true,
        firstDay: 0,
        defaultDate: new Date()
    });
    Calendar.setup({
        // Id of the input field
        inputField: "end_date_" + id,
        // Format of the input field
        ifFormat: "%d-%m-%Y",
        // Trigger for the calendar (button ID)
        button: "end_date_" + id + "_img",
        // Alignment (defaults to "Bl")
        align: "Tl",
        singleClick: true,
        firstDay: 0,
        defaultDate: new Date()
    });
}

//************--End Company Service Provider Section--*****************//

//************--Company Service Reservation Section--*****************//
var todayDate = new Date();
var maxDate = new Date();
maxDate.setMonth(todayDate.getMonth() + 6);

function updateProviders() {
    var serviceId = jQuery('#service_id').find(":selected").val();
    var urlGetProviders = jbdUtils.url + '&task=companyservicereservation.getProvidersAjax';

    jQuery('#time-text').empty();
    jQuery('#date-text').empty();

    jQuery.ajax({
        type: 'GET',
        url: urlGetProviders,
        data: {serviceId: serviceId},
        dataType: 'json',
        success: function (data) {
            jQuery('#provider_id').html(data);
        }
    });
}

var jbdFrontCP = false;
function updateDatesAdmin(url) {
    var urlGetDays = jbdUtils.url + '&task=companyservicereservation.getVacationDaysAjax';

    if (typeof url !== 'undefined') {
        urlGetDays = url;
        jbdFrontCP = true;
    }

    var providerId = jQuery('#provider_id').find(":selected").val();

    jQuery.ajax({
        type: 'GET',
        url: urlGetDays,
        data: {providerId: providerId},
        dataType: 'json',
        success: function (data) {
            jQuery('#datepicker').datepicker('destroy');
            jQuery('#datepicker').datepicker({
                beforeShowDay: function (date) {
                    var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                    return [data.indexOf(string) == -1]
                },
                onSelect: getAvailableHoursAdmin,
                minDate: todayDate,
                maxDate: maxDate
            });
        }
    });
}

function getAvailableHoursAdmin(date) {
    var urlGetHours = jbdUtils.url + '&task=companyservicereservation.getAvailableHoursAjax';

    if (jbdFrontCP) {
        urlGetHours = jbdUtils.url + '&task=managecompanyservicereservation.getAvailableHoursAjax';
    }

    var serviceId = jQuery('#service_id').find(":selected").val();
    var providerId = jQuery('#provider_id').find(":selected").val();

    jQuery('#date').val(date);
    jQuery('#date-text').html(date);
    jQuery('#time-text').empty();

    jQuery.ajax({
        type: 'GET',
        url: urlGetHours,
        data: {serviceId: serviceId, providerId: providerId, date: date},
        dataType: 'json',
        success: function (data) {
            jQuery('#morning').empty();
            jQuery('#afternoon').empty();
            jQuery('#evening').empty();

            jQuery('#morning').append(data.morning);
            jQuery('#afternoon').append(data.afternoon);
            jQuery('#evening').append(data.evening);

            selectedDate = date;
        }
    });
}

function selectHourAdmin(time) {
    jQuery('#time').val(time);
    jQuery('#time-text').html(time);
}

//************--End Company Service Reservation Section--*****************//




//*****************************************************//
//*********************** SITE ************************//
//*****************************************************//
jQuery(document).ready(function () {
    if (jQuery("#descriptionCounter").val())
        jQuery("#descriptionCounter").val(parseInt(jQuery("#description").attr('maxlength')) - jQuery("#description").val().length);
    if (jQuery("#descriptionCounterShort").val())
        jQuery("#descriptionCounterShort").val(parseInt(jQuery("#short_description").attr('maxlength')) - jQuery("#short_description").val().length);
});

//************--Associated Events Section--*****************//
function showAppointmentDialog() {
	
	jQuery('#event-appointment').jbdModal();
    jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.timeFormat, 'minTime': '6:00am'});
}

function makeAppointment(eventId, eventStartDate, eventEndDate) {
    showAppointmentDialog();
    jQuery('#eventId-appoint').val(eventId);
    listAvailableDates(eventStartDate, eventEndDate);
}

function listAvailableDates(eventStartDate, eventEndDate) {
    var dStart;
    var dEnd;

    if (eventStartDate.length === 0 || eventStartDate == null || eventStartDate === "0000-00-00")
        dStart = new Date();
    else
        dStart = new Date(eventStartDate);

    if (eventEndDate.length === 0 || eventEndDate == null || eventEndDate === "0000-00-00") {
        dEnd = new Date();
        dEnd.setDate(dStart.getDate() + 20);
    }
    else
        dEnd = new Date(eventEndDate);

    var dNow = new Date();

    if (dNow > dStart && dNow < dEnd)
        dStart = dNow;

    var select = document.getElementById("date-appoint");

    var i = 0;
    while (dStart <= dEnd && i <= 20) {
        var opt = document.createElement('option');
        opt.value = dStart.toDateString();
        opt.innerHTML = dStart.toDateString();
        select.appendChild(opt);

        dStart.setDate(dStart.getDate() + 1);
        i++;
    }
}

//************--End Associated Events Section--*************//

//************--Company Services Section--*****************//
function selectService(id) {
    serviceId = id;
    providerId = 0;

    wizard.steps("next");
}

function selectProvider(id) {
    providerId = id;
    providerName = jQuery('#provider-'+id).text();
    serviceName = jQuery('.selected-service').text();
    jQuery('#morning').empty();
    jQuery('#afternoon').empty();
    jQuery('#evening').empty();

    wizard.steps("next");
}

function selectHour(hour) {
    selectedHour = hour;

    wizard.steps("finish");
}

function renderProviders() {
    var urlGetProviders = jbdUtils.url + '&task=companies.getServiceProvidersAjax';

    jQuery.ajax({
        type: "GET",
        url: urlGetProviders,
        data: {serviceId: serviceId},
        dataType: 'json',
        success: function (data) {
            jQuery('#providers-content').html(data);
        }
    });
}

function getVacationDates() {
    var urlGetDays = jbdUtils.url + '&task=companies.getVacationDaysAjax';

    jQuery('.selected-provider-service').text(serviceName);
    jQuery('.selected-provider').text(providerName);

    jQuery.ajax({
        type: "GET",
        url: urlGetDays,
        data: {providerId: providerId},
        dataType: 'json',
        success: function (data) {
            jQuery('#datepicker').datepicker('destroy');
            jQuery('#datepicker').datepicker({
                beforeShowDay: function (date) {
                    var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                    return [data.indexOf(string) == -1]
                },
                onSelect: getAvailableHours,
                minDate: todayDate,
                maxDate: maxDate
            });

            var todayString = jQuery.datepicker.formatDate('dd-mm-yy', todayDate);
            if (data.indexOf(todayString) == -1) {
                getAvailableHours(todayDate);
            }
        }
    });
}

function getAvailableHours(date) {
    var urlGetHours = jbdUtils.url + '&task=companies.getAvailableHoursAjax';

    var hoursD = new Date(date);
    var hoursDate = jQuery.datepicker.formatDate('dd-mm-yy', hoursD);
    jQuery.ajax({
        type: "GET",
        url: urlGetHours,
        data: {serviceId: serviceId, providerId: providerId, date: hoursDate},
        dataType: 'json',
        success: function (data) {
            jQuery('#morning').empty();
            jQuery('#afternoon').empty();
            jQuery('#evening').empty();

            jQuery('#morning').append(data.morning);
            jQuery('#afternoon').append(data.afternoon);
            jQuery('#evening').append(data.evening);

            selectedDate = hoursDate;
        }
    });
}

//************--End Company Services Section--*************//

//************--Company Layouts Section--*****************//
function initTabs(tabId) {
    jQuery("#tabs").tabs();

    jQuery("#dir-tab-2").click(function () {
        loadDetailsViewMap();
    });

    jQuery(".dir-tabs-options").click(function () {
        jQuery(".dir-tabs-options").each(function () {
            jQuery(this).removeClass("ui-state-active");
        });
        jQuery(this).addClass("ui-state-active");
    });

    jQuery("#dir-tab-" + tabId).click();
}

function showCompanyMap() {
    jQuery("#company-map-holder").show();
    loadDetailsViewMap();
}

function hideMap() {
    jQuery("#company-map-holder").hide();
}

function readMore() {
    jQuery("#general-info").removeClass("collapsed");
    jQuery(".read-more").hide();
}

function showDetails(identifier) {
    var ids = ["company-details", "company-gallery", "company-videos", "company-sounds", "company-offers", "company-related", "company-services", "company-events", "events-associated", "company-testimonials", "company-price-list", "company-projects", "company-announcements","company-memberships","company-reviews"];

    var pos = ids.indexOf(identifier);

    jQuery(".company-menu a").each(function () {
        jQuery(this).removeClass("active");
    });

    //if (identifier === "company-projects") {
       returnToProjects();
    //}

    var linkIdentifier = identifier.substring(identifier.indexOf("-") + 1, identifier.length);
    jQuery("#" + linkIdentifier + "-link").addClass("active");

    for (var i = 0; i < pos; i++) {
        jQuery("#" + ids[i]).slideUp();
    }

    for (var i = pos; i < ids.length; i++) {
        jQuery("#" + ids[i]).slideDown();
    }
}

function showTabContent(identifier) {
    var ids = ["company-details", "company-gmap", "company-testimonials", "company-services", "company-price-list", "company-projects", "company-announcements", "company-products", "company-reviews"];

    jQuery(".company-menu a").each(function () {
        jQuery(this).removeClass("active");
    });

    var linkIdentifier = identifier.substring(identifier.indexOf("-") + 1, identifier.length);
    jQuery("#" + linkIdentifier + "-link").addClass("active");

    jQuery("#" + identifier).show();
    for (var i = 0; i < ids.length; i++) {
        if (ids[i] !== identifier)
            jQuery("#" + ids[i]).hide();
    }

    if (identifier === "company-projects") {
        returnToProjects();
    }

    if (identifier === "company-gmap") {
        loadDetailsViewMap();
    }

    if (identifier === "company-products") {
        goBack();
    }

    if (identifier === "company-offers") {
        jQuery('.offers-container').removeAttr("style");
    }
}

function renderUserAverageRating(averageRating, companyId, showNotice) {
    jQuery('.user-rating-avg').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        starCaptions: {
            0.5: JBD.JText._('LNG_BAD'),
            1: JBD.JText._('LNG_BAD'),
            1.5: JBD.JText._('LNG_POOR'),
            2: JBD.JText._('LNG_POOR'),
            2.5: JBD.JText._('LNG_REGULAR'),
            3: JBD.JText._('LNG_REGULAR'),
            3.5: JBD.JText._('LNG_GOOD'),
            4: JBD.JText._('LNG_GOOD'),
            4.5: JBD.JText._('LNG_GORGEOUS'),
            5: JBD.JText._('LNG_GORGEOUS')
        },
        size: 'sm',
        showCaption: false,
        starCaptionClasses: {
            0.5: 'badge badge-danger',
            1: 'badge badge-danger',
            1.5: 'badge badge-warning',
            2: 'badge badge-warning',
            2.5: 'badge badge-info',
            3: 'badge badge-info',
            3.5: 'badge badge-primary',
            4: 'badge badge-primary',
            4.5: 'badge badge-success',
            5: 'badge badge-success'
        },
        clearCaption: JBD.JText._('LNG_NOT_RATED_YET'),
        clearCaptionClass: 'badge badge-default',
        hoverEnabled: false,
        hoverChangeCaption: false,
        hoverChangeStars: false,
        rtl: false,
        displayOnly: true,
    });

    jQuery('.user-rating-avg').on('rating:change', function(event, value, caption) {
        if (showNotice == 1) {
            jQuery(this).rating('update', jQuery(this).attr('title'));
            showLoginNotice();
        }
        else {
            updateCompanyRate(companyId, value);
        }
    });

    jQuery('.user-rating-avg').on('rating:clear', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });

    jQuery('.user-rating-avg').on('rating:reset', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });
}

//************--End Company Layouts Section--**************//

//************--Company Reviews Section--*****************//
function showReviewForm(requiresLogin) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        jQuery("#add-review").slideDown(500);
    }
}

function renderRatingCriteria(calculate_review_criterias, companyId) {
    jQuery('.rating-criteria').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        starCaptions: {
            0.5: JBD.JText._('LNG_BAD'),
            1: JBD.JText._('LNG_BAD'),
            1.5: JBD.JText._('LNG_POOR'),
            2: JBD.JText._('LNG_POOR'),
            2.5: JBD.JText._('LNG_REGULAR'),
            3: JBD.JText._('LNG_REGULAR'),
            3.5: JBD.JText._('LNG_GOOD'),
            4: JBD.JText._('LNG_GOOD'),
            4.5: JBD.JText._('LNG_GORGEOUS'),
            5: JBD.JText._('LNG_GORGEOUS')
        },
        size: 'sm',
        showCaption: true,
        starCaptionClasses: {
            0.5: 'badge badge-danger',
            1: 'badge badge-danger',
            1.5: 'badge badge-warning',
            2: 'badge badge-warning',
            2.5: 'badge badge-info',
            3: 'badge badge-info',
            3.5: 'badge badge-primary',
            4: 'badge badge-primary',
            4.5: 'badge badge-success',
            5: 'badge badge-success'
        },
        clearCaption: JBD.JText._('LNG_NOT_RATED_YET'),
        clearCaptionClass: 'badge badge-default',
        hoverEnabled: true,
        hoverChangeCaption: true,
        hoverChangeStars: true,
        rtl: false,
    });

    jQuery('.rating-criteria').on('rating:change', function(event, value, caption) {
        jQuery(this).parent().parent().parent().children("input").val(value);
        if (calculate_review_criterias == 1) {
            var total = 0;
            var count = 0;
            jQuery(".review-criterias").each(function () {
                count++;
                total += parseFloat(jQuery(this).val());
            });
            if (!isNaN(total)) {
                value = total * 1.0 / count;
            }
        }
        updateCompanyRate(companyId, value);
    });

    jQuery('.rating-criteria').on('rating:clear', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });

    jQuery('.rating-criteria').on('rating:reset', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });
}

function renderRatingQuestions() {
    jQuery('.rating-question').rating({
        min:0,
        max:10,
        step:0.5,
        stars:10,
        size: 'sm',
        showCaption: false,
        hoverEnabled: true,
        hoverChangeCaption: false,
        hoverChangeStars: true,
        rtl: false,
        displayOnly: false,
    });

    jQuery('.rating-question').on('rating:change', function(event, value, caption) {
        jQuery(this).parent().parent().parent().children("input").val(value);
    });

    jQuery('.rating-question').on('rating:clear', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });

    jQuery('.rating-question').on('rating:reset', function(event) {
        jQuery(this).parent().parent().parent().children("input").val(0);
    });
}

//************--End Company Reviews Section--***************//

//************--Review Questions Section--*****************//
function showReviewQuestions(reviewId) {
    var maxLength = 100;
    jQuery("#show-questions" + reviewId).text(JBD.JText._('LNG_HIDE_REVIEW_QUESTIONS'));
    jQuery("#show-questions" + reviewId).attr('onclick', 'hideReviewQuestions("' + reviewId + '")');
    jQuery("#review-questions" + reviewId).slideDown(500);
    jQuery('#review-questions' + reviewId).children('.review-question-answer').each(function () {
        if (jQuery(this).hasClass('star-rating'))
            showStarRating(jQuery(this).attr('id'));
        else
            jQuery(this).html(truncate(jQuery(this).text(), jQuery(this).attr('id'), maxLength));
    });
}

function hideReviewQuestions(reviewId) {
    jQuery("#show-questions" + reviewId).text(JBD.JText._('LNG_SHOW_REVIEW_QUESTIONS'));
    jQuery("#show-questions" + reviewId).attr('onclick', 'showReviewQuestions("' + reviewId + '")');
    jQuery("#review-questions" + reviewId).slideUp(500);
}

function showStarRating(answerId) {
    var id = answerId.slice(15, answerId.length);
    jQuery('#' + answerId).empty();
    jQuery('#' + answerId).rating({
        min:0,
        max:10,
        step:0.5,
        stars:10,
        size: 'sm',
        showCaption: false,
        rtl: false,
        displayOnly: true,
    });
    jQuery('#' + answerId).rating('update',jQuery('#star-rating-score' + id).val());
}

function editAnswer(answerId, answerType) {
    var answerDiv = jQuery("#question-answer" + answerId);
    var answer = answerDiv.text();
    var data;
    var score;

    if (answerType == 0) {
        showFullText(answerId);
        answer = answerDiv.text();
        data = '<textarea style="width:100%;" name="answer-' + answerId + '" id="answer-' + answerId + '" onblur="saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" >' + answer + '</textarea>';
    }
    else if (answerType == 1) {
        var yes = answer == JBD.JText._('LNG_YES') ? 'checked="checked"' : "";
        var no = answer == JBD.JText._('LNG_NO') ? 'checked="checked"' : "";
        data = '<input type="radio" id="answer-' + answerId + '" value="1" onclick="saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" name="answer-' + answerId + '"' + yes + '>' + JBD.JText._("LNG_YES") + '</input>';
        data += ' <input type="radio" id="answer-' + answerId + '" value="0" onclick="saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" name="answer-' + answerId + '"' + no + '>' + JBD.JText._("LNG_NO") + '</input>';
    }
    else if (answerType == 2) {
        data = '<div class="rating-answer"></div>';
        score = parseFloat(answer);
    }
    jQuery("#question-answer" + answerId).attr('class', '');
    answerDiv.html(data);

    if (answerType == 2) {
        jQuery('.rating-answer').rating({
            min:0,
            max:10,
            step:0.5,
            stars:10,
            size: 'sm',
            showCaption: false,
            hoverEnabled: true,
            hoverChangeCaption: false,
            hoverChangeStars: true,
            rtl: false,
            displayOnly: false,
        });

        jQuery('.rating-answer').on('rating:change', function(event, value, caption) {
            jQuery(this).parent().parent().parent().children("input").val(value);
            document.getElementById('star-rating-score' + answerId).value = value;
            saveAnswer(answerId, answerType);
        });

        jQuery('.rating-answer').on('rating:clear', function(event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });

        jQuery('.rating-answer').on('rating:reset', function(event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });
    }
}

function saveAnswer(answerId, answerType) {
    var data;
    if (answerType == 0)
        data = jQuery("#answer-" + answerId).val();
    else if (answerType == 1)
        data = jQuery("input[name='answer-" + answerId + "']:checked").val();
    else if (answerType == 2)
        data = jQuery("#star-rating-score" + answerId).val();

    var urlSaveAnswerAjax = jbdUtils.url + '&task=companies.saveAnswerAjax';
    jQuery.ajax({
        type: 'GET',
        url: urlSaveAnswerAjax,
        data: {answer: data, answerId: answerId},
        dataType: 'json',
        success: function () {
            jQuery("#question-answer" + answerId).empty();
            if (answerType == 1) {
                if (data == 0)
                    data = JBD.JText._('LNG_NO');
                else if (data == 1)
                    data = JBD.JText._('LNG_YES');
            }
            if (answerType != 2)
                jQuery("#question-answer" + answerId).text(data);
            else {
                showStarRating('question-answer' + answerId);
            }
        }
    });
    if (answerType != 2)
        jQuery("#question-answer" + answerId).attr('class', 'answer question-answer');
    else
        jQuery("#question-answer" + answerId).attr('class', 'answer star-rating');
}

function truncate(text, id, limit) {
    var truncatedText;

    if (id.length > 10)
        id = id.slice(15, id.length);

    if (text.length <= limit) {
        return text;
    }
    else if (text.length > limit) {
        truncatedText = text.slice(0, limit) + '<span>...</span>';
        truncatedText += '<a href="javascript:void(0)" onClick=\'showFullText("' + id + '")\' class="more" id="more' + id + '">' + JBD.JText._("LNG_READ_MORE") + '</a>';
        truncatedText += '<span style="display:none;" id="more-text">' + text.slice(limit, text.length) + '</span>';

        return truncatedText;
    }
}

function showFullText(id) {
    jQuery('#more' + id).next().show();
    jQuery('#more' + id).prev().remove();
    jQuery('#more' + id).remove();
}

//********--End Review Questions Section--*********//

//************--Gallery Section--*****************//
function magnifyImages(htmlClass) {
    jQuery('.' + htmlClass).magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 2] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                return item.el.attr('title');
            }
        }
    });
}

//************--End Gallery Section--*******************//

//************--Company Util Section--*****************//
function printCompany(url) {
    var winref = window.open(url, 'windowName', 'width=1050,height=700');
    winref.window.focus();
    setTimeout(function () {
        winref.window.print();
    }, 1000);
}

function renderAverageRating(averageRating) {
    jQuery('#rating-average').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        size: 'sm',
        showCaption: false,
        rtl: false,
        displayOnly: true,
    });
    jQuery('#rating-average').rating('update',averageRating);
}

function renderReviewRating() {
    jQuery('.rating-review').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        size: 'sm',
        showCaption: false,
        rtl: false,
        displayOnly: true,
    });
    jQuery('.rating-review').each(function() {
        jQuery(this).rating('update',this.title);
    });
}

function showTab(tabId) {
    jQuery("#tabId").val(tabId);
    jQuery("#tabsForm").submit();
}

function claimCompany(requiresLogin) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        showClaimDialog();
    }
}

function showClaimDialog() {
	jQuery('#company-claim').jbdModal();
}

function showDirTab(tab) {
    jQuery(".dir-tab").each(function () {
        jQuery(this).hide();
    });

    jQuery(tab).show();
    jQuery(".track-business-details").each(function () {
        jQuery(this).parent().removeClass("active");
    });

    var number = tab.substr(tab.indexOf("-") + 1, tab.length);
    jQuery("#dir-tab-" + number).parent().addClass("active");

    returnToProjects();
}

function updateCompanyOwner(companyId, userId) {
	var html= '<span class="loading-message"> Please wait...</span>';
	jQuery(html).appendTo('body').jbdModal();
	
    jQuery("#company-claim button").each(function(){
    	jQuery(this).attr("disabled","disabled");
    });

    var form = document.reportAbuse;
    var postParameters = '';
    postParameters += "&companyId=" + companyId;
    postParameters += "&userId=" + userId;
    var postData = '&controller=companies&task=companies.updateCompanyOwner' + postParameters;
    jQuery.post(jbdUtils.baseUrl, postData, processUpdateCompanyOwner);
}

function processUpdateCompanyOwner(responce) {
    var xml = responce;
    jQuery(xml).find('answer').each(function () {
        var message = '';
        if (jQuery(this).attr('result') == true) {
            message = JBD.JText._('LNG_CLAIM_SUCCESSFULLY');
            jQuery("#claim-container").hide();
        } else {
            message = JBD.JText._('LNG_ERROR_CLAIMING_COMPANY');
            //alert('notsaved');
        }
        
    	var html= '<span class="loading-message">' + message + '</span>';
    	jQuery(html).appendTo('body').jbdModal();
    	setTimeout(function(){jQuery.jbdModal.close()}, 2000);
    });
}

function showReportAbuse() {
	jQuery('#reportAbuseEmail').jbdModal();
}

//************--End Company Util Section--*****************//

//************--Companies/Search Section--*****************//
function renderGridReviewRating(id) {

    jQuery('.rating-review-' + id).rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        size: 'sm',
        showCaption: false,
        rtl: false,
        displayOnly: true,
    });
    jQuery('.rating-review-' + id).each(function() {
        jQuery(this).rating('update',this.title);
    });
}

function showQuoteCompanyForm(companyId) {
    jQuery("#company-quote #companyId").val(companyId);
    jQuery('#company-quote').jbdModal();
}

function showQuoteCompany(companyId, showData) {
    if (showData == 0) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        showQuoteCompanyForm(companyId);
    }
}

function showContactCompanyList(companyId, showData) {
    if (showData == 0) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        jQuery("#company-contact #companyId").val(companyId);
        showContactCompany();
    }
}

function requestQuoteCompany(baseurl) {
    var isError = jQuery("#quoteCompanyFrm").validationEngine('validate');
    if (!isError)
        return;

    jQuery("#company-quote button").each(function(){
    	jQuery(this).attr("disabled","disabled");
    });
    
    var postData = "";
    postData += "&firstName=" + jQuery("#company-quote #firstName-quote").val();
    postData += "&lastName=" + jQuery("#company-quote #lastName-quote").val();
    postData += "&email=" + jQuery("#company-quote #email-quote").val();
    postData += "&description=" + jQuery("#company-quote #description-quote").val();
    postData += "&companyId=" + jQuery("#company-quote #companyId").val();
    postData += "&category=" + jQuery("#company-quote #category").val();
    postData += "&recaptcha_response_field=" + jQuery("#company-quote #recaptcha_response_field").val();
    postData += "&g-recaptcha-response=" + jQuery("#company-quote #g-recaptcha-response-1").val();

    jQuery.post(baseurl, postData, processContactCompanyResult);
}

function contactCompanyList() {
    var isError = jQuery("#contactCompanyFrm").validationEngine('validate');
    if (!isError)
        return;
    
    jQuery("#company-contact button").each(function(){
    	jQuery(this).attr("disabled","disabled");
    });

    var postData = "";
    postData += "&firstName=" + jQuery("#company-contact #firstName").val();
    postData += "&lastName=" + jQuery("#company-contact #lastName").val();
    postData += "&email=" + jQuery("#company-contact #email").val();
    postData += "&description=" + jQuery("#company-contact #description").val();
    postData += "&companyId=" + jQuery("#company-contact #companyId").val();
    postData += "&recaptcha_response_field=" + jQuery("#captcha-div-contact #recaptcha_response_field").val();
    postData += "&g-recaptcha-response=" + jQuery("#captcha-div-contact #g-recaptcha-response").val();

    jQuery.post(contactListUrl, postData, processContactCompanyResult);
}

function showContactCompany() {
    
	jQuery('#company-contact').jbdModal();
}

function contactCompany(showData) {
    if (showData == 0) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        showContactCompany();
    }
}

function processContactCompanyResult(responce) {
    var xml = responce;
    jQuery(xml).find('answer').each(function () {
    	console.debug("process response");
        if (jQuery(this).attr('error') == '1') {
        	jQuery.jbdModal.close()
        	var html= '<div><strong>' + JBD.JText._("COM_JBUSINESS_ERROR") + '</strong><br/><br/><p>' + jQuery(this).attr('errorMessage') + '</p></div>';
        	jQuery(html).appendTo('body').jbdModal();
        	setTimeout(function(){jQuery.jbdModal.close()}, 3000);
        } else {
        	jQuery.jbdModal.close()
        	var html= '<div><p>' + JBD.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED")  + '</p></div>';
        	jQuery(html).appendTo('body').jbdModal();
        	setTimeout(function(){jQuery.jbdModal.close()}, 3000);
        	
            if ((typeof  jQuery(this).attr('redirect_url') !== 'undefined') && (jQuery(this).attr('redirect_url') != "")) {
                window.location.href = jQuery(this).attr('redirect_url');
            }
        }
    });
}

function renderSearchAverageRating() {
    jQuery('.rating-average').rating({
        min:0,
        max:5,
        step:0.5,
        stars:5,
        size: 'sm',
        showCaption: false,
        rtl: false,
        displayOnly: true,
    });
    jQuery('.rating-average').each(function() {
        jQuery(this).rating('update',this.title);
    });
}

var issetCategory = false;
var cat_id;

function setCategoryStatus(status, categoryId) {
    issetCategory = status;
    cat_id = categoryId;
}

function saveSelectedCategory() {
    var catId;
    var checked = jQuery("#filterCategoryItems input[type='checkbox']:checked");
    catId = checked.attr('id');

    if (issetCategory)
        catId = cat_id;

    jQuery("#adminForm #categoryId").val(catId);
    jQuery("#adminForm input[name=limitstart]").val(0);
}

function changeOrder(orderField) {
    jQuery("#orderBy").val(orderField);
    jQuery("#adminForm").submit();
}

function showList() {
    jQuery("#jbd-results-container").show();
    jQuery("#jbd-grid-view").hide();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link").addClass("active");
}

function showGrid() {
    jQuery("#jbd-results-container").hide();
    jQuery("#jbd-grid-view").show();
    jQuery(window).resize();

    jQuery("#grid-view-link").addClass("active");
    jQuery("#list-view-link").removeClass("active");
}

function chooseCategory(categoryId) {
    if (categoryId.toString().substring(0, 3) == "chk") {
        categoryId = categoryId.substring(3);
    }
    categoryId = categoryId.toString().replace(";", "");
    jQuery("#adminForm #categoryId").val(categoryId);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}

function addFilterRule(type, id) {
    var val = type + '=' + id + ';';
    if (jQuery("#selectedParams").val().length > 0) {
        jQuery("#selectedParams").val(jQuery("#selectedParams").val() + val);
    } else {
        jQuery("#selectedParams").val(val);
    }
    if (!issetCategory) {
        jQuery("#filter_active").val("1");
    }
    jQuery("#adminForm input[name=limitstart]").val(0);
    saveSelectedCategory();
    jQuery("#adminForm").submit();
}

function removeFilterRule(type, id) {
    var val = type + '=' + id + ';';
    var str = jQuery("#selectedParams").val();
    jQuery("#selectedParams").val((str.replace(val, "")));
    jQuery("#filter_active").val("1");
    saveSelectedCategory();

    if (type == "city")
        jQuery("#adminForm #city-search").val("");
    if (type == "region")
        jQuery("#adminForm #region-search").val("");
    if (type == "country")
        jQuery("#adminForm #country-search").val("");
    if (type == "type")
        jQuery("#adminForm #type-search").val("");
    if (type == "province")
        jQuery("#adminForm #province-search").val("");

    jQuery("#adminForm").submit();

}

function resetFilters(resetCategories) {
    jQuery("#selectedParams").val("");
    if (resetCategories)
        jQuery("#categories-filter").val("");
    else
        saveSelectedCategory();
    jQuery("#adminForm #categoryId").val("");

    jQuery("#adminForm #searchkeyword").val("");
    jQuery("#adminForm #zipcode").val("");
    jQuery("#adminForm #city-search").val("");
    jQuery("#adminForm #region-search").val("");
    jQuery("#adminForm #country-search").val("");
    jQuery("#adminForm #type-search").val("");
    jQuery("#adminForm #province-search").val("");
    jQuery("#adminForm #radius").val("");
    jQuery("#adminForm #startDate").val("");
    jQuery("#adminForm #endDate").val("");
    jQuery("#adminForm #filter-by-fav").val("");
    jQuery("#adminForm #resetSearch").val("1");
    jQuery("input[id*='attribute_']").val("");
    jQuery("input[name*='attribute_']").remove();

    jQuery("#adminForm").submit();
}

function addFilterRuleCategory(catId) {
    catId = catId + ";";
    if (jQuery("#categories-filter").val().length > 0) {
        jQuery("#categories-filter").val(jQuery("#categories-filter").val() + catId);
    } else {
        jQuery("#categories-filter").val(catId);
    }
    jQuery("#filter_active").val("1");
    jQuery("#adminForm input[name=limitstart]").val(0);
    chooseCategory(catId);
}

function removeFilterRuleCategory(catId) {
    var categoryId = catId + ";";
    var str = jQuery("#categories-filter").val();
    jQuery("#categories-filter").val((str.replace(categoryId, "")));

    jQuery("#categorySearch").val("");
    jQuery("input[name='categorySearch']").each(function() {
        jQuery(this).val("");
    });

    jQuery("#filter_active").val("1");
    var checked = jQuery("#filterCategoryItems input[type='checkbox']:checked");
    if (checked.length > 0) {
        checked.each(function () {
            var id = jQuery(this).attr('id');
            if (id != catId) {
                chooseCategory(id);
                return false;
            }
        });
    }
    else if (checked.length == 0) {
        var categoryIds = jQuery("#categories-filter").val();
        var categoryId = categoryIds.slice(0, categoryIds.length - 1);
        var start = categoryId.lastIndexOf(';') + 1;
        if (start == -1)
            start = 0;

        categoryId = categoryId.slice(start, categoryId.length);
        chooseCategory(categoryId);
    }
}

function setRadius(radius) {
    jQuery("#adminForm > #radius").val(radius);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}

function showMoreParams(div, lessButton) {
    var div = "#" + div;
    var less = "#" + lessButton;
    jQuery(div).removeAttr("style");
    jQuery(less).css("display", "none");
}

function showLessParams(div, moreButton) {
    var div = "#" + div;
    var more = "#" + moreButton;
    jQuery(div).css("display", "none");
    jQuery(more).removeAttr("style");
}

function filterByFavorites(requiresLogin) {
    if (!requiresLogin) {
        jQuery("#adminForm #filter-by-fav").val("1");
        jQuery("#adminForm").submit();
    } else {
        showLoginNotice();
    }
}

function collapseSearchFilter() {
    //searchFilter collapse
    var headers = ["H1", "H2", "H3", "H4", "H5", "H6"];

    jQuery(".accordionCollapse").click(function (e) {
        var target = e.target,
            name = target.nodeName.toUpperCase();

        if (jQuery.inArray(name, headers) > -1) {
            var subItem = jQuery(target).next();

            //slideUp all elements (except target) at current depth or greater
            var depth = jQuery(subItem).parents().length;
            var allAtDepth = jQuery(".accordion li, .accordion div").filter(function () {
                if (jQuery(this).parents().length >= depth && this !== subItem.get(0)) {
                    return true;
                }
            });
            jQuery(allAtDepth).slideUp("fast");

            //slideToggle target content and adjust bottom border if necessary
            subItem.slideToggle("fast", function () {
                jQuery(".accordionCollapse :visible:last").css("border-radius", "0 0 10px 10px");
            });
            jQuery(target).css({"border-bottom-right-radius": "0", "border-bottom-left-radius": "0"});
        }
    });
}

//************--End Companies/Search Section--**************//

//************--Manage Companies Section--*****************//
function deleteDirListing(id) {
    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_COMPANIES_CONFIRM_DELETE'))) {
        jQuery("#cid").val(id);
        jQuery("#task").val("managecompanies.delete");
        jQuery("#adminForm").submit();
    }
}

//***********--End Manage Companies Section--***************//

//************--Manage Company Messages Section--**********//
function deleteMessage(id) {
    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_COMPANY_MESSAGE_CONFIRM_DELETE'))) {
        jQuery("#id").val(id);
        jQuery("#task").val("managemessages.delete");
        jQuery("#adminForm").submit();
    }
}

//***********--End Manage Messages Section--**************//

//***********--Manage Company Services Section--*********//
function addService() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyservice.add");
    jQuery("#adminForm").submit();
}

function deleteService(serviceId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_SERVICE_CONFIRM_DELETE"))) {
        jQuery("#id").val(serviceId);
        jQuery("#task").val("managecompanyservices.delete");
        jQuery("#adminForm").submit();
    }
}

function duplicateService(serviceId) {
    jQuery("#id").val(serviceId);
    jQuery("#task").val("managecompanyservice.duplicate");
    jQuery("#adminForm").submit();
}

//***********--End Manage Company Services Section--************************//

//************--Manage Company Service Providers Section--*****************//
function selectServiceProviders() {
    jQuery("#adminForm").submit();
}

function addServiceProvider() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyserviceprovider.add");
    jQuery("#adminForm").submit();
}

function deleteServiceProvider(serviceId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_SERVICE_PROVIDER_CONFIRM_DELETE"))) {
        jQuery("#id").val(serviceId);
        jQuery("#task").val("managecompanyserviceproviders.delete");
        jQuery("#adminForm").submit();
    }
}

//************--End Manage Company Service Providers Section--****************//

//************--Manage Company Service Reservations Section--****************//
function selectReservation() {
    jQuery("#adminForm").submit();
}

function deleteReservation(bookingId) {
    if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_SERVICE_RESERVATION_CONFIRM_DELETE"))) {
        jQuery("#id").val(bookingId);
        jQuery("#task").val("managecompanyservicereservations.delete");
        jQuery("#adminForm").submit();
    }
}

//************--End Manage Company Service Reservations Section--**************//

//************--Manage Project Section--*****************//
function editProject(projectId) {
    jQuery("#id").val(projectId);
    jQuery("#task").val("managecompanyproject.edit");
    jQuery("#adminForm").submit();
}

function addProject() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyproject.add");
    jQuery("#adminForm").submit();
}

function deleteProject(projectId) {
    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_PROJECT_CONFIRM_DELETE'))) {
        jQuery("#id").val(projectId);
        jQuery("#task").val("managecompanyprojects.delete");
        jQuery("#adminForm").submit();
    }
}

function showProjectDetail(project) {

    var baseUrl = jbdUtils.url + '&task=companies.getProjectDetailsAjax';
    baseUrl = baseUrl + "&projectId=" + project;
    jQuery.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'json',
        success: function (data) {
            jQuery('#project-name').html(data.name);
            jQuery('#project-name-link').html(data.breadCrumbsName);
            jQuery('#project-description').html(data.description);
            jQuery('#project-gallery').html(data.projectGalleryImages);
            if (data.nrPhotos === 0) {
                jQuery('#project-image-container').css("display", "none");
            } else {
                jQuery('#project-image-container').css("display", "");
            }
            jQuery("#company-projects-container").hide(500);
            jQuery("#project-details").show(500);
            applyLighSlider();
        }
    });
}

function applyLighSlider() {
    setTimeout(function () {
        jQuery('#projectImageGallery').unitegallery({
            gallery_theme: "default",
            gallery_height: 550,
            theme_enable_text_panel: true,
            slider_control_zoom: false,
            slider_enable_zoom_panel: false,
            thumb_fixed_size: false
        });
    }, 2000);
}

function returnToProjects() {
    jQuery("#project-details").hide(500);
    jQuery("#company-projects-container").show(500);
}

//***************--End Manage Project Section--******************//


//***************-- Company Rating --***************************//

function updateCompanyRate(companyId, rateScore) {
    var postParameters = "";
    var ratingId = getRatingId(companyId);
    if (ratingId == undefined) {
        ratingId = 0;
    }
    postParameters += "&companyId=" + companyId;
    postParameters += "&rating=" + rateScore;
    postParameters += "&ratingId=" + ratingId;

    var postData = '&task=companies.updateRating' + postParameters;
    jQuery.post(jbdUtils.baseUrl, postData, processRateResult);
}

function processRateResult(responce) {
    var xml = responce;

    jQuery(xml).find('answer')
    .each(
        function () {
            jQuery("#rateNumber" + jQuery(this).attr('id')).html(
                jQuery(this).attr('nrRatings'));
            jQuery("#rateNumber" + jQuery(this).attr('id'))
                .parent().show();
            jQuery('#rating-average').rating('update', jQuery(this).attr('averageRating'));
            saveCookieRating(jQuery(this).attr('id'), jQuery(this).attr('ratingId'));
        });
}

function getRatingId(companyId) {
    var ratings = getCookie("companyRatingIds");
    if (ratings == undefined)
        return;
    var ratingsIds = ratings.split('#');
    for (var i = 0; i < ratingsIds.length; i++) {
        var temp = ratingsIds[i].split(',');
        if (temp[0] == companyId)
            return temp[1];
    }
}

function saveCookieRating(companyId, reviewId) {
    var ratings = getCookie("companyRatingIds");
    if (ratings == undefined)
        ratings = companyId + ',' + reviewId + '#';

    var ratingsIds = ratings.split('#');
    var found = false;
    for (var i = 0; i < ratingsIds.length; i++) {
        var temp = ratingsIds[i].split(',');
        if (temp[0] == companyId)
            found = true;
    }
    if (!found) {
        ratings = ratings + companyId + ',' + reviewId + '#';
    }
    setCookie("companyRatingIds", ratings, 60);
}

//***************-- End Company Rating --***************************//


//*******************-- Listing Products --*************************//

function showProductCategories(parentId) {
    jQuery('.categories-level-1').hide(500);
    jQuery('.categories-level-'+parentId).show(500);
    var parent = jQuery('#parent-category-'+parentId+' h1').text();
    parent = parent+' - '+JBD.JText._('LNG_SUBCATEGORIES');
    jQuery('#sub-categories').html('<a href="javascript:void(0);" onclick="showProductCategories('+parentId+')">'+parent+'</a>&raquo;');
    jQuery('#category-products').empty();
    jQuery('#product-details').empty();
    jQuery('#product-details-content').empty();
    jQuery('#product-list-content').empty();
}

function goBack() {
    jQuery('.grid4').hide(500);
    jQuery('.categories-level-1').show(500);
    jQuery('#sub-categories').empty();
    jQuery('#category-products').empty();
    jQuery('#product-details').empty();
    jQuery('#product-details-content').empty();
    jQuery('#product-list-content').empty();
}

function showProducts(catId, companyId) {
    jQuery('.grid4').hide(500);
    jQuery('#product-list-content').html('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>').load(jbdUtils.url+'&view=companyproducts #grid-content', {categoryId: catId, companyId: companyId}, function() {
        var categoryName = jQuery('#product-category h1').text();
        jQuery('#category-products').html('<a href="javascript:void(0);" onclick="goBackToProducts('+catId+', '+companyId+')">'+categoryName+'</a>&raquo;');
    });
    jQuery('#company-products-title').text(JBD.JText._('LNG_PRODUCTS'));
    jQuery('#product-list-content').attr('style', 'display: block;');
    jQuery('#category-products').empty();
    jQuery('#product-details').empty();
    jQuery('#product-details-content').empty();
}

function goBackToCategories(catId) {
    jQuery('#product-list-content').empty();
    jQuery('#subcategory-'+catId).closest('.grid4').show(500);
    jQuery('#company-products-title').text(JBD.JText._('LNG_PRODUCT_CATEGORIES'));
}

function showProductDetails(productId, catId) {
    jQuery('#product-list-content').hide(500);
    jQuery('#product-details-content').html('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>').load(jbdUtils.url+'&view=companyproducts #product-details', {productId: productId, categoryId: catId}, function() {
        var productName = jQuery('#product-name h2').text();
        jQuery('#product-details').html('<a style="color:black;">'+productName+'</a>');

    });
    jQuery('#company-products-title').text(JBD.JText._('LNG_PRODUCT_DETAILS'));
    jQuery('#product-details-content').show(500);
}

function goBackToProducts(catId, companyId) {
    jQuery('#product-details-content').hide(500);
    jQuery('#product-details-content').empty();
    showProducts(catId, companyId);
    jQuery('#product-list-content').show(500);
    jQuery('#product-list-content').attr('style', 'display: block;');
}

//******************-- End Listing Products --***********************//

