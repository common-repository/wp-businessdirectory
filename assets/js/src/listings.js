/**
 * JBD Listing javascript class
 */
class JBDListings {

    /**
     * Constructor
     */
    constructor() {
    	this.issetCategory = false;
   	    
        this.todayDate = new Date();
        this.maxDate = new Date();
        this.maxDate.setMonth(this.todayDate.getMonth() + 6);

        this.availableHoursCallType = "site";
    }

    /**
     * Set edit and delete location url for front and back end
     *
     * @param eurl string edit url on back end
     * @param eurlf string edit url on front end
     * @param durl string delete url on back end
     * @param durlf string delete url on front end
     */
    setLocationUrl(eurl, eurlf, durl, durlf) {
        this.editUrl = eurl;
        this.editUrlFront = eurlf;
        this.deleteUrl = durl;
        this.deleteUrlFront = durlf;
    }

    /**
     * Based on the fact if it is back end or front end this function set the task value and submits the form with
     * package id passed as parameter
     *
     * @param packageId int package id
     */
    extendPeriod(packageId) {
        if (jbdUtils.getProperty("isProfile") != JBDConstants.IS_ADMIN) {
            jQuery("#item-form #task").val("managecompany.extendPeriod");
        } else {
            jQuery("#extend_package_id").val(packageId);
            jQuery("#item-form #task").val("company.extendPeriod");
        }

        jQuery("#extend_package_id").val(packageId);
        jQuery("#item-form").submit();
    }

    //TODO do we still need this?
    /**
     * Check all activity cities from select box
     */
    checkAllActivityCities() {
        this.uncheckAllActivityCities();
        jQuery(".cities_ids-select option").each(function () {
            if (jQuery(this).val() != "") {
                activityCitiesList.add(jQuery(this));
            }
        });
        jQuery("#activity_cities option").each(function () {
            jQuery(this).attr("selected", "selected");
        });
    }

    //TODO do we still need this?
    /**
     * Uncheck all activity cities from select box
     */
    uncheckAllActivityCities() {
        jQuery("#activity_cities option").each(function () {
            jQuery(this).removeAttr("selected");
        });
        activityCitiesList.remove();
    }

    /**
     * Based on the check if it is back or front end open the modal with the options passed on parameter
     *
     * @param locationId int location Id
     * @param identifier string identifier
     */
    editLocation(locationId, identifier) {
        let baseUrl = this.editUrl;
        if (jbdUtils.getProperty("isProfile") == JBDConstants.IS_FRONT) {
            baseUrl = this.editUrlFront;
        }
        baseUrl = baseUrl + "&locationId=" + locationId + "&identifier=" + identifier;
        jQuery("#location-frame").attr("src", baseUrl);

        let options = {
            modalClass: "jbd-modal jbd-location"
        };

        jQuery('#location-dialog').jbdModal(options);
    }

    /**
     * Delete secondary location
     *
     * @param locationId int location ID
     */
    deleteLocation(locationId) {
        if (!confirm(JBD.JText._("LNG_DELETE_LOCATION_CONF"))) {
            return;
        }
        let baseUrl = this.deleteUrl;
        if (jbdUtils.getProperty("isProfile") == JBDConstants.IS_FRONT) {
            baseUrl = this.deleteUrlFront;
        }

        let postData = "&locationId=" + locationId;
        jQuery.post(baseUrl, postData, this.processDeleteLocationResult);
    }

    /**
     * Process the response from deleteLocation function
     *
     * @param response object response
     */
    processDeleteLocationResult(response) {
        let xml = response;
        jQuery(xml).find('answer').each(function () {
            if (jQuery(this).attr('error') == '1')
                jQuery("#location-box-" + jQuery(this).attr('locationId')).remove();
            else {

                let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('LNG_LOCATION_DELETE_FAILED') +'</p>'
    			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div></div></div>';

                jQuery(html).appendTo('body').jbdModal();
                setTimeout(function () {
                    jQuery.jbdModal.close()
                }, 2000);
            }
        });
    }

    /**
     * Add new contact div to add a new contact person for the listing
     *
     * @param index int last index of the contacts
     */
    addNewContact(index) {
        let newIndex = parseInt(index) + 1;
        jQuery('#contact-form-box1').clone().prop('id', 'contact-form-box' + newIndex).appendTo('#contact_details');
        jQuery("#contact-form-box" + newIndex).find('h3').text(JBD.JText._('LNG_CONTACT') + ' ' + newIndex);
        jQuery('#contact-form-box' + newIndex + ' input').each(function () {
            jQuery(this).val('');
        });

        jQuery("#contact-form-box" + newIndex + " .remove-contact").attr('href', 'javascript:jbdListings.removeContact(\'' + newIndex + '\')').show();
        jQuery('#add_contact').attr('onclick', 'jbdListings.addNewContact(\'' + newIndex + '\')');

    }

    /**
     * Remove a contact person from listing contacts
     *
     * @param index int index of the contact
     */
    removeContact(index) {
        if (index < 2) {
            return;
        }

        index = parseInt(index);
        jQuery('#contact-form-box' + index).remove();
    }

    /**
     * Add new testimonial div to add a new testimonial for the listing
     *
     * @param index int last index of the contacts
     */
    addNewTestimonial(index) {
        let newIndex = parseInt(index) + 1;
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
        jQuery('#add_testimonial').attr('onclick', 'jbdListings.addNewTestimonial(\'' + newIndex + '\')');
    }

    /**
     * Remove a testimonial from listing
     *
     * @param index int index of the testimonial
     */
    removeTestimonial(index) {
        if (index < 2) {
            return;
        }

        index = parseInt(index);
        jQuery('#testimonial-form-box' + index).remove();
    }

    /**
     * Add new service div to add a new service for the listing
     *
     * @param index int last index of the service
     * @param ServiceFolder string name of the folder where the image of the service will be added
     * @param ServiceFolderPath string url path of the service image
     */
    addNewService(index, ServiceFolder, ServiceFolderPath) {
        let newIndex = parseInt(index) + 1;
        jQuery('#service-form-box1').clone().prop('id', 'service-form-box' + newIndex).appendTo('#service_details');
        jQuery("#service-form-box" + newIndex).find('h5').text(JBD.JText._('LNG_ITEM') + ' ' + newIndex);
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
        jQuery('#add_service').attr('onclick', 'jbdListings.addNewService(\'' + newIndex + '\',\'' + ServiceFolder + '\',\'' + ServiceFolderPath + '\')');

        let uploadInstance = JBDUploadHelper.getUploadInstance();
        uploadInstance.imageUploader(ServiceFolder, ServiceFolderPath, 'service-', newIndex);
    }

    /**
     * Remove a service from listing
     *
     * @param index int index of the service
     */
    removeService(index) {
        if (index < 2) {
            return;
        }

        index = parseInt(index);
        jQuery('#service-form-box' + index).remove();
    }

    /**
     * Disable work period for a specific day but do not remove it
     *
     * @param day int day number
     * @param mandatory bool is it mandatory or not
     * @param multiple
     */
    disableWorkPeriod(day, mandatory, multiple) {
        if (typeof multiple === 'undefined') {
            multiple = true;
        }

        let status;
        let checked = jQuery('#work_status_check_' + day).is(":checked");
        let button;
        let dayRow = jQuery('#js_workhours_' + day);
        if (multiple) {
            dayRow = jQuery('#break_period_' + day);
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
            button.attr('onclick', 'jbdListings.addBreak(' + day + ', ' + multiple + ')');
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

    /**
     * Add hour breaks for the listing
     *
     * @param day int day number
     * @param multiple bool can be added multiple break hours
     * @returns {boolean}
     */
    addBreak(day, multiple) {
        let id = parseInt(jQuery('#break_periods_count_' + day).val());
        let newId = id + 1;

        let dayRow = jQuery('#js_workhours_' + day);
        let button = dayRow.find('.js_action_buttons').find('a');

        if (typeof button.attr('disabled') !== 'undefined') {
            console.log("cannot add more than 1 break period");
            return false;
        }

        let html = '';
        html += '<div class="row align-items-center" id="break_hour_period_' + day + '_' + newId + '">';
        html += '<div class="col-1 p-1"></div>';
        html += '<div class="col-4"><input type="text" name="break_start_hour[]" class="form-control p-1 text-center timepicker" value="01:00 PM" /></div>';
        html += '<div class="col-4"><input type="text" name="break_end_hour[]" class="form-control p-1 text-center timepicker" value="02:00 PM" /></div>';
        if (multiple) {
            html += '<div class="col-2"><a href="javascript:void(0)" class="btn btn-sm btn-danger btn-panel" onclick="jbdListings.deleteBreak(' + day + ', ' + newId + ')">';
        }
        else {
            html += '<div class="col-2"><a href="javascript:void(0)" class="btn btn-sm btn-danger btn-panel" onclick="jbdListings.deleteCompanyBreak(' + day + ', ' + newId + ')">';
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

        jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.getProperty("timeFormat"), 'minTime': '6:00am',});
    }

    /**
     * Delete company break hour
     *
     * @param day int day nr
     * @param id int break ID
     */
    deleteCompanyBreak(day, id) {
        jQuery('#break_hour_period_' + day + '_' + id).remove();

        let dayRow = jQuery('#js_workhours_' + day);
        let button = dayRow.find('.js_action_buttons').find('a');

        button.attr('disabled', false);

        jQuery('#break_period_' + day).find('.break-button').removeAttr('disabled');
        let count = jQuery('#break_periods_count_' + day).val();
        if (count > 0) {
            count--;
        }
        jQuery('#break_periods_count_' + day).val(count);
        jQuery('#break_period_' + day).find('.break-button').attr('onclick', 'jbdListings.addBreak(' + day + ', false)');
    }

    /**
     * Delete break hour period on trail weeks
     *
     * @param day string day
     * @param id int ID
     */
    deleteBreak(day, id) {
        jQuery('#break_hour_period_' + day + '_' + id).empty();
        let count = parseInt(jQuery('#break_periods_count_' + day).val());
        let newCount = count - 1;

        jQuery('#break_periods_count_' + day).val(newCount);
    }

    /**
     * Delete a trail period on a given date
     *
     * @param date string date
     */
    disableTrailPeriod(date) {
        let status = jQuery('#trail_status_' + date).val();
        let dateRow = jQuery('.js_trailhours_' + date);
        let button = dateRow.find('.js_action_buttons').find('a');

        let checked = jQuery('#trail_status_checkbox_' + date).is(":checked");

        if (!checked) {
            jQuery('#trail_status_' + date).val(0);
            jQuery('.js_trail_row_' + date).find('.js_trail_hours').attr('disabled', true);

            button.attr('disabled', true);
            button.text(JBD.JText._('LNG_CLOSED'));
            button.attr('onclick', '');
            button.removeClass('btn-success');
            button.addClass('btn-outline-danger');
        } else {
            jQuery('#trail_status_' + date).val(1);
            jQuery('.js_trail_row_' + date).find('.js_trail_hours').attr('disabled', false);

            button.text(JBD.JText._('LNG_ADD_BREAK'));
            button.attr('onclick', 'jbdListings.addTrailBreak("' + date + '")');
            button.attr('disabled', false);
            button.addClass('btn-success');
            button.removeClass('btn-outline-danger');
        }
    }

    /**
     * Add new trail break for trail weeks
     *
     * @param date string date
     * @returns {boolean}
     */
    addTrailBreak(date) {
        let dateRow = jQuery('.js_trailhours_' + date);
        let button = dateRow.find('.js_action_buttons').find('a');

        if (typeof button.attr('disabled') !== 'undefined') {
            console.log("cannot add more than 1 break period");
            return false;
        }
        button.attr('disabled', true);

        jQuery('#trail_breaks_status_' + date).val(1);
        jQuery('#trail_break_hours_' + date).show();

        jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.getProperty("timeFormat"), 'minTime': '6:00am',});
    }

    /**
     * Delete break hour for trail weeks
     *
     * @param date string date
     */
    deleteTrailBreak(date) {
        let dateRow = jQuery('.js_trailhours_' + date);
        let button = dateRow.find('.js_action_buttons').find('a');

        jQuery('#trail_breaks_status_' + date).val(0);
        jQuery('#trail_break_hours_' + date).hide();

        button.attr('disabled', false);
    }

    /**
     * Update the attributes on category selection. If linking of categories and attributes is enabled then the attributes
     * will be shown based on the category selection for the item
     *
     * @param categoryId int category id
     * @param companyId int company id
     */
    updateAttributes(categoryId, companyId) {
        let attributesUrl = jbdUtils.getAjaxUrl('getAttributesAjax', 'company', 'managecompany');

        let packageId = 0;
        if (jbdUtils.getProperty('enable_packages') == 1) {
            packageId = jQuery('#filter_package_select').val();
            if(!packageId){
                packageId = jQuery('#filter_package').val();
            }
        }

        jQuery.ajax({
            type: "GET",
            url: attributesUrl,
            data: {categoryId: categoryId, companyId: companyId, packageId: packageId},
            dataType: 'json',
            cache:false,
            success: function (data) {

            	if(data!== null && data !== 'undefined'){
            		 jQuery('#custom-attributes-section').show();
            	}else{
            		jQuery('#custom-attributes-section').hide();
            	}

                jQuery('#customFieldsContent').html(data);
                jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: JBD.JText._('LNG_SELECT_OPTION'), placeholder_text_multiple: JBD.JText._('LNG_SELECT_OPTION')});
            }
        });
    }

    /**
     * Display subcategories by category selection
     *
     * @param id int category ID
     * @param level int depth level of category
     * @param maxLevel int max level of category
     */
    displaySubcategories(id, level, maxLevel) {
        let categoryId = jQuery("#" + id).val();

        if (!categoryId) {
            categoryId = 0;
        }
        //invalidate subcategories level
        for (let i = level + 1; i <= maxLevel; i++) {
            jQuery("#company_categories-level-" + i).html('');
        }
        jQuery("#company_categories-level-" + (level + 1)).html("<div style='width:20px;margin: 0 auto;'><img align='center' src='" + jbdUtils.getProperty("imageRepo") + "/assets/images/loading.gif'  /></div>");

        let postParameters = '';

        postParameters += "&categoryId=" + categoryId;

        let postData = '';
        if (jbdUtils.getProperty("isProfile") == JBDConstants.IS_ADMIN)
            postData = '&option=com_jbusinessdirectory&task=company.getSubcategories' + postParameters;
        else
            postData = '&option=com_jbusinessdirectory&task=managecompany.getSubcategories' + postParameters;
        jQuery.post(jbdUtils.baseUrl, postData, this.processDisplaySubcategoriesResponse);
        //jQuery('#frmFacilitiesFormSubmitWait').show();
    }

    /**
     * manage response for displaySubcategories function
     *
     * @param response object response
     */
    processDisplaySubcategoriesResponse(response) {
        let xml = response;
        let self = this;
        //jQuery('#frmFacilitiesFormSubmitWait').hide();
        jQuery(xml).find('answer').each(function () {
            if (jQuery(this).attr('error') == '1') {
                jQuery('#frm_error_msg_facility').className = 'text_error';
                jQuery('#frm_error_msg_facility').html(jQuery(this).attr('errorMessage'));
                jQuery('#frm_error_msg_facility').show();

            }
            else if (jQuery(this).attr('error') == '0') {

                jQuery("#subcategories").html(jQuery(this).attr('content_categories'));
                self.removeSelectedCategories();
                //clear current level
                jQuery("#company_categories-level-" + jQuery(this).attr('category-level')).html('');
                //clear next level
                let level = 1 + parseInt(jQuery(this).attr('category-level'));
                jQuery("#company_categories-level-" + level).html('');
                if (jQuery(this).attr('isLastLevel') != '1') {
                    jQuery("#company_categories-level-" + jQuery(this).attr('category-level')).html(jQuery(this).attr('content_select_categories'));

                }
            }
        });
    }

    /**
     * Remove all selected categories from company edit view
     */
    removeSelectedCategories() {
        jQuery("#mainSubcategory > option").each(function () {
            jQuery("#subcategories option[value=" + jQuery(this).val() + "]").remove();
        });
    }

    /**
     * Hide company disapprove div
     */
    hideDisapprovalBox() {
        jQuery("#disapprovalBox").hide();
    }

    /**
     * Show company disapprove div
     */
    showDisapprovalBox() {
        jQuery("#disapprovalBox").show();
    }

    /**
     * Add provider vacation
     *
     * @param id int provider ID
     */
    addVacation(id) {
        id = parseInt(id);
        let newId = id + 1;

        let deleteButton = '';
        deleteButton += '<br/>';
        deleteButton += '<a href="javascript:void(0)" class="btn btn-sm btn-danger btn-panel mb-1" onclick="deleteVacation(' + newId + ')">';
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

        this.initCalendar(newId);
        jQuery('#add_vacation_' + parseInt(id)).remove();
    }

    /**
     * Delete vacation of the provider
     *
     * @param id in provider
     */
    deleteVacation(id) {
        jQuery('#vacation_' + id).empty();
    }

    /**
     * Display company services after the company is selected
     */
    updateServices() {
        let companyId = jQuery('#company_id').find(":selected").val();
        let urlGetServices = jbdUtils.getAjaxUrl('getServicesAjax', 'companyserviceprovider', 'managecompanyserviceprovider');

        jQuery.ajax({
            type: "GET",
            url: urlGetServices,
            data: {companyId: companyId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery('#services').html(data);
                jbdUtils.updateChosenSelect('#services');
            }
        });
    }

    /**
     * Initiate calendar when selecting the vacation for a provider
     *
     * @param id int provider id
     */
    initCalendar(id) {
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

    /**
     * Update providers after selecting a service
     */
    updateProviders() {
        let serviceId = jQuery('#service_id').find(":selected").val();
        let urlGetProviders = jbdUtils.getAjaxUrl('getProvidersAjax', 'companyservicereservation');

        jQuery('#time-text').empty();
        jQuery('#date-text').empty();

        jQuery.ajax({
            type: "GET",
            url: urlGetProviders,
            data: {serviceId: serviceId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery('#provider_id').html(data);
            }
        });
    }

    /**
     * Select hour on admin for services
     *
     * @param time string time
     */
    selectHourAdmin(time) {
        jQuery('#time').val(time);
        jQuery('#time-text').html(time);
    }

    /**
     * Shows event appointment modal
     */
    showAppointmentDialog() {
        jQuery('#event-appointment').jbdModal();
        jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.getProperty("timeFormat"), 'minTime': '6:00am'});
    }

    /**
     * Shows event appointment modal and display the available dates
     *
     * @param eventId int event ID
     * @param eventStartDate string event start date
     * @param eventEndDate string event end date
     */
    makeAppointment(eventId, eventStartDate, eventEndDate) {
        this.showAppointmentDialog();
        jQuery('#eventId-appoint').val(eventId);
        this.listAvailableDates(eventStartDate, eventEndDate);
    }

    /**
     * List available dates for the appointment that is booked for the event
     *
     * @param eventStartDate string event start date
     * @param eventEndDate string event end date
     */
    listAvailableDates(eventStartDate, eventEndDate) {
        let dStart;
        let dEnd;

        if (eventStartDate.length === 0 || eventStartDate == null || eventStartDate === "0000-00-00") {
            dStart = new Date();
        } else {
            dStart = new Date(eventStartDate);
        }

        if (eventEndDate.length === 0 || eventEndDate == null || eventEndDate === "0000-00-00") {
            dEnd = new Date();
            dEnd.setDate(dStart.getDate() + 20);
        } else {
            dEnd = new Date(eventEndDate);
        }

        let dNow = new Date();
        if (dNow > dStart && dNow < dEnd) {
            dStart = dNow;
        }

        let select = document.getElementById("date-appoint");

        let i = 0;
        while (dStart <= dEnd && i <= 20) {
            let opt = document.createElement('option');
            opt.value = dStart.toDateString();
            opt.innerHTML = dStart.toDateString();
            select.appendChild(opt);

            dStart.setDate(dStart.getDate() + 1);
            i++;
        }
    }

    /**
     * Select a service and go to the next step on service selection
     *
     * @param id int service ID
     */
    selectService(id) {
        serviceId = id;
        providerId = 0;

        wizard.steps("next");
    }

    /**
     * After selecting a service provider go to the next step on service selection
     *
     * @param id int provider ID
     */
    selectProvider(id, type) {
        providerId = id;
        providerType = type;
        providerName = jQuery('#provider-' + id).text();
        serviceName = jQuery('.selected-service').text();
        jQuery('#morning').empty();
        jQuery('#afternoon').empty();
        jQuery('#evening').empty();

        wizard.steps("next");
    }

    /**
     * Add the selected hour and go the finish step on the service selection
     *
     * @param hour string hour
     */
    selectHour(hour) {
        selectedHour = hour;

        wizard.steps("finish");
    }

    /**
     * Render providers with ajax and add them to providers-content div
     */
    renderProviders() {
        let urlGetProviders = jbdUtils.getAjaxUrl('getServiceProvidersAjax', 'companyservicereservation', 'managecompanyservicereservation');

        jQuery.ajax({
            type: "GET",
            url: urlGetProviders,
            data: {serviceId: serviceId},
            dataType: 'json',
            cache: false,
            success: function (data) {
                jQuery('#providers-content').html(data);
            }
        });
    }

    createProviderCalendarAdmin() {
        let providerId = jQuery('#provider_id').find(":selected").val();
        let providerType = jQuery('#provider_type').val();
        
        let urlGetDays = jbdUtils.getAjaxUrl('getVacationDaysAjax', 'companyservicereservation', 'managecompanyservicereservation');
        if(providerType == 2){
            urlGetDays = jbdUtils.getAjaxUrl('getWorkingDaysAjax', 'companyservicereservation', 'managecompanyservicereservation');
        }

        this.availableHoursCallType = "admin";
        this.createProviderCalendar(providerId,providerType);
    }

    createProviderCalendarFront() {
        jQuery('.selected-provider-service').text(serviceName);
        jQuery('.selected-provider').text(providerName);

        this.availableHoursCallType = "site";
        this.createProviderCalendar(providerId,providerType);
    }

    /**
     * Get vacation days for a service provider by ajax
     */
    createProviderCalendar(providerId, providerType) {
        let urlGetDays = jbdUtils.getAjaxUrl('getVacationDaysAjax', 'companyservicereservation', 'managecompanyservicereservation');
        if(providerType == 2){
            urlGetDays = jbdUtils.getAjaxUrl('getWorkingDaysAjax', 'companyservicereservation', 'managecompanyservicereservation');
        }

        let self = this;
        let hourCall = self.getAvailableHoursFront;
        if(self.availableHoursCallType == "admin"){
            hourCall = self.getAvailableHoursAdmin;
        }

        jQuery.ajax({
            type: "GET",
            url: urlGetDays,
            data: {providerId: providerId},
            dataType: 'json',
            cache: false,
            success: function (data) {
                jQuery('#datepicker').datepicker('destroy');
                jQuery('#datepicker').datepicker({
                    beforeShowDay: function (date) {
                        let string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                        if(providerType == 2){
                            return [data.indexOf(string) != -1]
                        }else{
                            return [data.indexOf(string) == -1]
                        }
                    },
                    onSelect: hourCall,
                    minDate: self.todayDate,
                    maxDate: self.maxDate
                });

                let todayString = jQuery.datepicker.formatDate('dd-mm-yy', self.todayDate);
                if ((data.indexOf(todayString) == -1 && providerType == 1) || (data.indexOf(todayString) != -1 && providerType == 2)) {
                    if(self.availableHoursCallType == "admin"){
                        self.getAvailableHoursAdmin(self.todayDate);
                    }else {
                        self.getAvailableHoursFront(self.todayDate);
                    }
                }
            }
        });
    }

    /**
     * Get services available hours on admin with ajax. Same function used also on front end
     *
     * @param date string date
     */
    getAvailableHoursAdmin(date) {
        let serviceId = jQuery('#service_id').find(":selected").val();
        let providerId = jQuery('#provider_id').find(":selected").val();
        let providerType = jQuery('#provider_type').find(":selected").val();

        jQuery('#date').val(date);
        jQuery('#date-text').html(date);
        jQuery('#time-text').empty();

        jbdListings.getAvailableHours(date, serviceId, providerId, providerType);
    }

    /**
     * get available hours of listing on a date given in front-end
     */
    getAvailableHoursFront(date){
        jbdListings.getAvailableHours(date, serviceId, providerId, providerType);
    }

    /**
     * get available hours of listing on a date given
     *
     * @param date string date
     */
    getAvailableHours(date, serviceId, providerId, providerType) {
        let urlGetHours = jbdUtils.getAjaxUrl('getAvailableHoursAjax', 'companyservicereservation', 'managecompanyservicereservation');

        let hoursD = new Date(date);
        let hoursDate = jQuery.datepicker.formatDate('dd-mm-yy', hoursD);
        let self = this;

        jQuery.ajax({
            type: "GET",
            url: urlGetHours,
            data: {serviceId: serviceId, providerId: providerId, providerType: providerType, type: this.availableHoursCallType, date: hoursDate},
            dataType: 'json',
            cache: false,
            success: function (data) {
                jQuery('#morning').empty();
                jQuery('#afternoon').empty();
                jQuery('#evening').empty();

                jQuery('#morning').append(data.morning);
                jQuery('#afternoon').append(data.afternoon);
                jQuery('#evening').append(data.evening);

                //TODO global reference
                if(self.availableHoursCallType == "site") {
                    selectedDate = hoursDate;
                }else{
                    jQuery("#date").val(hoursDate);
                }
            }
        });
    }

    /**
     * Initiate tabs on list detail view 1 and 2
     *
     * @param tabId int tab ID
     */
    initTabs(tabId) {
        jQuery("#tabs").tabs();

        jQuery("#dir-tab-2").click(function () {
            //TODO global reference
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

    /**
     * Open Map on details view of the listing
     */
    showCompanyMap() {
        jQuery("#company-map-holder").show();
        //TODO global reference
        loadDetailsViewMap();
    }

    /**
     * Used on listing style 4,6 and 8 to navigate through the tabs
     *
     * @param identifier string tab name (identifier)
     */
    showDetails(identifier) {
        let ids = ["company-details", "company-announcements", "company-gallery", "company-videos", "company-sounds", "company-offers", "company-products", "company-related", "company-services", "company-associated", "company-events", "events-associated", "company-testimonials", "company-price-list", "company-projects", "company-memberships","listing-articles", "company-team", "company-reviews"];

        let pos = ids.indexOf(identifier);

        jQuery(".company-menu a").each(function () {
            jQuery(this).removeClass("active");
        });

        //if (identifier === "company-projects") {
        this.returnToProjects();
        //}

        let linkIdentifier = identifier.substring(identifier.indexOf("-") + 1, identifier.length);
        jQuery("#" + linkIdentifier + "-link").addClass("active");

        for (let i = 0; i < pos; i++) {
            jQuery("#" + ids[i]).slideUp();
        }

        for (let i = pos; i < ids.length; i++) {
            jQuery("#" + ids[i]).slideDown();
        }
    }

    /**
     * Used on listing details view 6 to navigate through the tabs
     *
     * @param identifier string tab name (identifier)
     */
    showTabContent(identifier) {
        let ids = ["company-details", "company-gmap", "company-testimonials", "company-services", "company-price-list", "company-projects", "company-announcements", "company-products", "company-team", "company-reviews"];

        jQuery(".company-menu a").each(function () {
            jQuery(this).removeClass("active");
        });

        let linkIdentifier = identifier.substring(identifier.indexOf("-") + 1, identifier.length);
        jQuery("#" + linkIdentifier + "-link").addClass("active");

        jQuery("#" + identifier).show();
        for (let i = 0; i < ids.length; i++) {
            if (ids[i] !== identifier)
                jQuery("#" + ids[i]).hide();
        }

        if (identifier === "company-projects") {
            this.returnToProjects();
        }

        if (identifier === "company-gmap") {
            //TODO global reference
            loadDetailsViewMap();
        }

        if (identifier === "company-products") {
            this.goBack();
        }

        if (identifier === "company-offers") {
            jQuery('.offers-container').removeAttr("style");
        }
    }

    /**
     * Render user average rating
     *
     * @param averageRating double average rating
     * @param companyId int company ID
     * @param showNotice boolean show login notice if user not logged in
     */
    renderUserAverageRating(averageRating, companyId, showNotice) {
        let self = this;

        jQuery('.user-rating-avg').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
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

        jQuery('.user-rating-avg').on('rating:change', function (event, value, caption) {
            if (showNotice == 1) {
                jQuery(this).rating('update', jQuery(this).attr('title'));
                jbdUtils.showLoginNotice();
            }
            else {
                self.updateCompanyRate(companyId, value);
            }
        });

        jQuery('.user-rating-avg').on('rating:clear', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });

        jQuery('.user-rating-avg').on('rating:reset', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });
    }

    /**
     * Open review form for a new review. Checks also if user needs to be logged in to leave a review
     *
     * @param requiresLogin boolean true if user needs to be logged in to review the company and currently he is
     *                         false if user needs to be logged in to review the company but he is not currently logged in
     */
    showReviewForm(requiresLogin) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            jQuery("#add-review").slideDown(500);
        }
    }

    /**
     * Render rating criteria for reviews while adding a new review
     *
     * @param calculate_review_criterias boolean to include the review criterias or not
     * @param companyId int company ID
     */
    renderRatingCriteria(calculate_review_criterias, companyId) {
        let self = this;

        jQuery('.rating-criteria').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
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

        jQuery('.rating-criteria').on('rating:change', function (event, value, caption) {
            jQuery(this).parent().parent().parent().children("input").val(value);
            if (calculate_review_criterias == 1) {
                let total = 0;
                let count = 0;
                jQuery(".review-criterias").each(function () {
                    count++;
                    total += parseFloat(jQuery(this).val());
                });
                if (!isNaN(total)) {
                    value = total * 1.0 / count;
                }
            }
            self.updateCompanyRate(companyId, value);
        });

        jQuery('.rating-criteria').on('rating:clear', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });

        jQuery('.rating-criteria').on('rating:reset', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });
    }

    /**
     * Render review questions on adding review section.
     */
    renderRatingQuestions() {
        jQuery('.rating-question').rating({
            min: 0,
            max: 10,
            step: 0.5,
            stars: 10,
            size: 'sm',
            showCaption: false,
            hoverEnabled: true,
            hoverChangeCaption: false,
            hoverChangeStars: true,
            rtl: false,
            displayOnly: false,
        });

        jQuery('.rating-question').on('rating:change', function (event, value, caption) {
            jQuery(this).parent().parent().parent().children("input").val(value);
        });

        jQuery('.rating-question').on('rating:clear', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });

        jQuery('.rating-question').on('rating:reset', function (event) {
            jQuery(this).parent().parent().parent().children("input").val(0);
        });
    }

    /**
     * Display review questions
     *
     * @param reviewId int review question Id
     */
    showReviewQuestions(reviewId) {
        let self = this;
        let maxLength = 100;
        jQuery("#show-questions" + reviewId).text(JBD.JText._('LNG_HIDE_REVIEW_QUESTIONS'));
        jQuery("#show-questions" + reviewId).attr('onclick', 'hideReviewQuestions("' + reviewId + '")');
        jQuery("#review-questions" + reviewId).slideDown(500);
        jQuery('#review-questions' + reviewId).children('.review-question-answer').each(function () {
            if (jQuery(this).hasClass('star-rating')) {
                self.showStarRating(jQuery(this).attr('id'));
            } else {
                jQuery(this).html(jbdUtils.truncate(jQuery(this).text(), jQuery(this).attr('id'), maxLength));
            }
        });
    }

    /**
     * Hide review Questions
     *
     * @param reviewId int review ID
     */
    hideReviewQuestions(reviewId) {
        jQuery("#show-questions" + reviewId).text(JBD.JText._('LNG_SHOW_REVIEW_QUESTIONS'));
        jQuery("#show-questions" + reviewId).attr('onclick', 'jbdListings.showReviewQuestions("' + reviewId + '")');
        jQuery("#review-questions" + reviewId).slideUp(500);
    }

    /**
     * Show start rating for listing
     *
     * @param answerId int answerId
     */
    showStarRating(answerId) {
        let id = answerId.slice(15, answerId.length);
        jQuery('#' + answerId).empty();
        jQuery('#' + answerId).rating({
            min: 0,
            max: 10,
            step: 0.5,
            stars: 10,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('#' + answerId).rating('update', jQuery('#star-rating-score' + id).val());
    }

    /**
     * After the review question answers are submitted they have the option to be edited and when it is enabled open the possibility
     * to change the answer
     *
     * @param answerId int answer ID
     * @param answerType int answer Type
     */
    editAnswer(answerId, answerType) {
        let answerDiv = jQuery("#question-answer" + answerId);
        let answer = answerDiv.text();
        let data;
        let score;

        let self = this;

        if (answerType == 0) {
            jbdUtils.showFullText(answerId);
            answer = answerDiv.text();
            data = '<textarea style="width:100%;" name="answer-' + answerId + '" id="answer-' + answerId + '" onblur="jbdListings.saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" >' + answer + '</textarea>';
        } else if (answerType == 1) {
            let yes = answer == JBD.JText._('LNG_YES') ? 'checked="checked"' : "";
            let no = answer == JBD.JText._('LNG_NO') ? 'checked="checked"' : "";
            data = '<input type="radio" id="answer-' + answerId + '" value="1" onclick="jbdListings.saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" name="answer-' + answerId + '"' + yes + '>' + JBD.JText._("LNG_YES") + '</input>';
            data += ' <input type="radio" id="answer-' + answerId + '" value="0" onclick="jbdListings.saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" name="answer-' + answerId + '"' + no + '>' + JBD.JText._("LNG_NO") + '</input>';
        } else if (answerType == 2) {
            data = '<div class="rating-answer"></div>';
            score = parseFloat(answer);
        }

        jQuery("#question-answer" + answerId).attr('class', '');
        answerDiv.html(data);

        if (answerType == 2) {
            jQuery('.rating-answer').rating({
                min: 0,
                max: 10,
                step: 0.5,
                stars: 10,
                size: 'sm',
                showCaption: false,
                hoverEnabled: true,
                hoverChangeCaption: false,
                hoverChangeStars: true,
                rtl: false,
                displayOnly: false,
            });

            jQuery('.rating-answer').on('rating:change', function (event, value, caption) {
                jQuery(this).parent().parent().parent().children("input").val(value);
                document.getElementById('star-rating-score' + answerId).value = value;
                self.saveAnswer(answerId, answerType);
            });

            jQuery('.rating-answer').on('rating:clear', function (event) {
                jQuery(this).parent().parent().parent().children("input").val(0);
            });

            jQuery('.rating-answer').on('rating:reset', function (event) {
                jQuery(this).parent().parent().parent().children("input").val(0);
            });
        }
    }

    /**
     * Save question answer on reviews section with ajax after editing it
     *
     * @param answerId int answer ID
     * @param answerType int answer Type
     */
    saveAnswer(answerId, answerType) {
        let data;
        let self = this;

        if (answerType == 0) {
            data = jQuery("#answer-" + answerId).val();
        } else if (answerType == 1) {
            data = jQuery("input[name='answer-" + answerId + "']:checked").val();
        } else if (answerType == 2) {
            data = jQuery("#star-rating-score" + answerId).val();
        }

        let urlSaveAnswerAjax = jbdUtils.getAjaxUrl('saveAnswerAjax', 'companies');
        jQuery.ajax({
            type: "GET",
            url: urlSaveAnswerAjax,
            data: {answer: data, answerId: answerId},
            dataType: 'json',
            cache:false,
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
                    self.showStarRating('question-answer' + answerId);
                }
            }
        });

        if (answerType != 2) {
            jQuery("#question-answer" + answerId).attr('class', 'answer question-answer');
        } else {
            jQuery("#question-answer" + answerId).attr('class', 'answer star-rating');
        }
    }

    /**
     * When an image is clicked magnify it
     *
     * @param htmlClass string image gallery class
     */
    magnifyImages(htmlClass) {
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

    /**
     * Render average rating on listing details view
     *
     * @param averageRating int rating that will be displayed (listing average rating)
     */
    renderAverageRating(averageRating) {
        jQuery('#rating-average').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('#rating-average').rating('update', averageRating);
    }

    /**
     * Render review rating on listing detail view
     */
    renderReviewRating() {
        jQuery('.rating-review').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('.rating-review').each(function () {
            jQuery(this).rating('update', this.title);
        });
    }

    /**
     * Open tab with id given to the parameters
     *
     * @param tabId int tab ID
     */
    showTab(tabId) {
        jQuery("#tabId").val(tabId);
        jQuery("#tabsForm").submit();
    }

    /**
     * Used to display the company claim modal. Checks if user is needed to be logged in then if he is show
     * the dialog otherwise show the login notice
     *
     * @param requiresLogin boolean true if user needs to be logged in to claim the company and currently he is
     *                         false if user needs to be logged in to claim the company but he is not currently logged in
     */
    claimCompany(requiresLogin) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            jQuery(".error_msg").each(function () {
                jQuery(this).hide();
            });
            this.showClaimDialog();
        }
    }

    /**
     * Display claim dialog modal
     */
    showClaimDialog() {
        jQuery('#company-claim').jbdModal();
    }

    /**
     * Used on listing detail view style one and two to go through the tabs of the view
     *
     * @param tab string tab id that need to be opened
     */
    showDirTab(tab) {
        jQuery(".dir-tab").each(function () {
            jQuery(this).hide();
        });

        jQuery(tab).show();
        jQuery(".track-business-details").each(function () {
            jQuery(this).parent().removeClass("active");
        });

        let number = tab.substr(tab.indexOf("-") + 1, tab.length);
        jQuery("#dir-tab-" + number).parent().addClass("active");

        this.returnToProjects();
    }

    /**
     * Change the company owner with ajax
     *
     * @param companyId int company ID
     * @param userId int new owner ID
     */
    updateCompanyOwner(companyId, userId) {
        let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">Please wait...</p>'
		+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div></div></div>';

        jQuery(html).appendTo('body').jbdModal();

        jQuery("#company-claim button").each(function () {
            jQuery(this).attr("disabled", "disabled");
        });

        let form = document.reportAbuse;
        let postParameters = '';
        postParameters += "&companyId=" + companyId;
        postParameters += "&userId=" + userId;
        let postData = '&controller=companies&task=companies.updateCompanyOwner' + postParameters;
        jQuery.post(jbdUtils.baseUrl, postData, this.processUpdateCompanyOwner);
    }

    /**
     * Handle response from updateCompanyOwner ajax function
     *
     * @param response object response
     */
    processUpdateCompanyOwner(response) {
        let xml = response;
        jQuery(xml).find('answer').each(function () {
            let message = '';
            if (jQuery(this).attr('result') == true) {
                message = JBD.JText._('LNG_CLAIM_SUCCESSFULLY');
                jQuery("#claim-container").hide();
            } else {
                message = JBD.JText._('LNG_ERROR_CLAIMING_COMPANY');
                //alert('notsaved');
            }

            let html = '<span class="loading-message">' + message + '</span>';
            jQuery(html).appendTo('body').jbdModal();
            setTimeout(function () {
                jQuery.jbdModal.close()
            }, 2000);
        });
    }

    /**
     * Displays the report abuse modal
     */
    showReportAbuse() {
        jQuery('#reportAbuseEmail').jbdModal();
    }

    /**
     * Render the review rating for grid view on the listings
     *
     * @param id int review ID
     */
    renderGridReviewRating(id) {

        jQuery('.rating-review-' + id).rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('.rating-review-' + id).each(function () {
            jQuery(this).rating('update', this.title);
        });
    }

    /**
     * Displayes the company quote modal
     *
     * @param companyId int company ID
     */
    showQuoteCompanyForm(companyId) {
        jQuery("#company-quote #companyId").val(companyId);
        jQuery('#company-quote').jbdModal();
    }

    /**
     * Used to display the company request quote modal on list view. Checks if user is needed to be logged in then if he is show
     * the dialog otherwise show the login notice
     *
     * @param companyId int company ID
     * @param showData boolean true if user needs to be logged in to request quote to the company and currently he is
     *                         false if user needs to be logged in to request quote to the company but he is not currently logged in
     */
    showQuoteCompany(companyId, showData) {
        if (showData == 0) {
            jbdUtils.showLoginNotice();
        } else {
            jQuery(".error_msg").each(function () {
                jQuery(this).hide();
            });
            this.showQuoteCompanyForm(companyId);
        }
    }

    /**
     * Used to display the company contact modal on list view. Checks if user is needed to be logged in then if he is show
     * the dialog otherwise show the login notice
     *
     * @param companyId int company ID
     * @param showData boolean true if user needs to be logged in to contact the company and currently he is
     *                         false if user needs to be logged in to contact the company but he is not currently logged in
     */
    showContactCompanyList(companyId, showData) {
        if (showData == 0) {
            jbdUtils.showLoginNotice();
        } else {
            jQuery(".error_msg").each(function () {
                jQuery(this).hide();
            });
            jQuery("#company-contact #companyId").val(companyId);
            this.showContactCompany();
        }
    }

    /**
     * Validates the company request quote form on the modal and if everything is OK then submit a ajax call to request the
     * quote
     */
    requestQuoteCompany() {
        let baseurl = jbdUtils.getAjaxUrl('requestQuoteCompanyAjax', 'companies');

        let isError = jQuery("#quoteCompanyFrm").validationEngine('validate');
        if (!isError)
            return;

        jQuery("#company-quote button").each(function () {
            jQuery(this).attr("disabled", "disabled");
        });

        //loading button animation if validation is OK
        jQuery(".quote-submit-button").addClass("loader");

        let postData = "";
        postData += "&firstName=" + jQuery("#company-quote #firstName-quote").val();
        postData += "&lastName=" + jQuery("#company-quote #lastName-quote").val();
        postData += "&email=" + jQuery("#company-quote #email-quote").val();
        postData += "&description=" + jQuery("#company-quote #description-quote").val();
        postData += "&companyId=" + jQuery("#company-quote #companyId").val();
        postData += "&category=" + jQuery("#company-quote #category").val();
        postData += "&g-recaptcha-response=" + jQuery("#company-quote .g-recaptcha-response").val();

        jQuery("#quoteCompanyFrm .jbd-commit").addClass("loader");

        jQuery.post(baseurl, postData, this.processQuoteCompanyResult);
    }

    /**
     * Validates the company contact form on the modal and if everything is OK then submit a ajax call to contact the
     * company
     */
    contactCompanyList() {
        let isError = jQuery("#contactCompanyFrm").validationEngine('validate');
        if (!isError)
            return;

        jQuery("#contactCompanyFrm button").each(function () {
            jQuery(this).attr("disabled", "disabled");
        });

        //loading button animation if validation is OK
        jQuery(".contact-submit-button").addClass("loader");

        let postData = "";
        postData += "&firstName=" + jQuery("#company-contact #firstName").val();
        postData += "&lastName=" + jQuery("#company-contact #lastName").val();
        postData += "&email=" + jQuery("#company-contact #email").val();
        postData += "&description=" + jQuery("#company-contact #description").val();
        postData += "&companyId=" + jQuery("#company-contact #companyId").val();
        postData += "&recaptcha_response_field=" + jQuery("#captcha-div-contact #recaptcha_response_field").val();
        postData += "&g-recaptcha-response=" + jQuery("#captcha-div-contact #g-recaptcha-response").val();

        jQuery.post(contactListUrl, postData, this.processContactCompanyResult);
    }

    /**
     * Show company contact modal
     */
    showContactCompany() {
        jQuery('#company-contact').jbdModal();
    }

    /**
     * If showData is false it means that the user need to be logged in to view the data and currently he is not so display
     * the login notice otherwise open the company contact modal
     *
     * @param showData boolean true if user needs to be logged in to contact the company and currently he is
     *                         false if user needs to be logged in to contact the company but he is not currently logged in
     */
    contactCompany(showData) {
        if (showData == 0) {
            jbdUtils.showLoginNotice();
        } else {
            jQuery(".error_msg").each(function () {
                jQuery(this).hide();
            });
            this.showContactCompany();
        }
    }

    /**
     * Ajax function to create registering request (join request) for the listings selected on the popup.
     * It sends a request with all the data and based on the results it get displays a success message or an error one if
     * the registration request failed
     *
     * @param $companyId int company ID
     * @param multipleListings boolean true if user owns more than one listing
     */
    joinCompany($companyId,multipleListings) {
        let baseUrl = jbdUtils.getAjaxUrl('joinCompany', 'companies');
        let selectedValues = jQuery('#userAssociatedCompanies').val();
        let companyIds = '';
        if (multipleListings && Array.isArray(selectedValues)) {
            companyIds = selectedValues.join();
        }
        jQuery(".associated-buttons button").each(function () {
            jQuery(this).attr("disabled", "disabled");
        });

        //loading button animation if validation is OK
        jQuery(".jbd-commit-associated").addClass("loader");

        jQuery.ajax({
            type: "GET",
            url: baseUrl,
            cache:false,
            data: {
                companyIds: companyIds,
                multipleListings: multipleListings,
                mainCompanyId:$companyId
            },
            dataType: 'json',
            success: function (data) {
                if (data.error == '1') {
                    
                    let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('COM_JBUSINESS_ERROR') +'</p>'
        			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div> <div class="jmodal-body"><p>' + data.message + '</p></div> </div></div>';
                    
                    jQuery(html).appendTo('body').jbdModal();
                    setTimeout(function () {
                        jQuery.jbdModal.close();
                        location.reload(true);
                    }, 1200);
                } else {
                    let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('COM_JBUSINESS_DIRECTORY_COMPANY_REGISTERED') +'</p>'
        			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div></div></div>';
                    jQuery(html).appendTo('body').jbdModal();
                    setTimeout(function () {
                        jQuery.jbdModal.close();
                        location.reload(true);
                    }, 1200);
                }
            }
        });
    }

    /**
     * If user owns more that one listing or if he already has joined then display the join popup. Otherwise proceed right
     * away with listing join
     *
     * @param $companyId int company ID
     * @param multipleListings boolean true if user owns multiplelistings
     * @param nrJoined int number of listings already joined
     */
    showCompanyListDialog($companyId,multipleListings,nrJoined) {
        if (multipleListings || nrJoined > 0) {
            jQuery('#company-list').jbdModal();
        } else {
            jbdListings.joinCompany($companyId,multipleListings);
        }
    }

    /**
     * Checks if user is logged in and if yes it proceeds with listing join otherwise shows the login notice
     *
     * @param nrJoined int number of listings that are already joined
     * @param $companyId int company ID
     * @param userId int user ID
     * @param multipleListings boolean treu if user owns more than one listing
     */
    joinListing(nrJoined,$companyId,userId,multipleListings) {
        if (userId == 0) {
            jbdUtils.showLoginNotice();
        } else {
            this.showCompanyListDialog($companyId,multipleListings,nrJoined);
        }
    }

    /**
     * Handle the response from contactCompany and requestQuoteCompany functions
     *
     * @param responce object response
     */
    processContactCompanyResult(response) {
        //remove loader
        jQuery("#quoteCompanyFrm .jbd-commit").removeClass("loader");

        //enable the buttons
        jQuery("#quoteCompanyFrm button").each(function () {
            jQuery(this).removeAttr('disabled');
        });

        let xml = response;
        jQuery(xml).find('answer').each(function () {
            if (jQuery(this).attr('error') == '1') {
                let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('COM_JBUSINESS_ERROR') +'</p>'
    			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div> <div class="jmodal-body"><p>' +jQuery(this).attr('errorMessage') + '</p></div> </div></div>';

                jQuery(html).appendTo('body').jbdModal();
                setTimeout(function () {
                    jQuery.jbdModal.close()
                }, 2000);
            } else {
                let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED') +'</p>'
    			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div></div></div>';

                jQuery(html).appendTo('body').jbdModal();
                setTimeout(function () {
                    jQuery.jbdModal.close()
                }, 2000);

                if ((typeof  jQuery(this).attr('redirect_url') !== 'undefined') && (jQuery(this).attr('redirect_url') != "")) {
                    window.location.href = jQuery(this).attr('redirect_url');
                }
            }
        });
    }

    /**
     * Render listings rating for the search view
     */
    renderSearchAverageRating() {
        jQuery('.rating-average').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('.rating-average').each(function () {
            jQuery(this).rating('update', this.title);
        });
    }

    /**
     * Set the instance category status and category ID
     *
     * @param status int category status
     * @param categoryId int category ID
     */
    setCategoryStatus(status, categoryId) {
        this.issetCategory = status;
        this.cat_id = categoryId;
    }

    /**
     * Set the selected category on the search fields and submits the form
     */
    saveSelectedCategory() {
        let catId;
        let checked = jQuery("#filterCategoryItems input[type='checkbox']:checked");
        catId = checked.attr('id');

        if (this.issetCategory) {
            catId = this.cat_id;
        }

        jQuery("#adminForm #categoryId").val(catId);
        jQuery("#adminForm input[name=limitstart]").val(0);
    }

    /**
     * Show list view mode on listing search view
     */
    showList() {
        jQuery("#jbd-results-container").show();
        jQuery("#jbd-grid-view").hide();

        jQuery("#grid-view-link").removeClass("active");
        jQuery("#list-view-link").addClass("active");
    }

    /**
     * Show grid mode for listing on search view
     */
    showGrid() {
        jQuery("#jbd-results-container").hide();
        jQuery("#jbd-grid-view").show();
        jQuery(window).resize();

        jQuery("#grid-view-link").addClass("active");
        jQuery("#list-view-link").removeClass("active");
    }

    /**
     * Add selected category to search params on listing search view
     *
     * @param type string type of rule
     * @param id int id of the rule selected
     */
    addFilterRule(type, id) {
        let val = type + '=' + id + ';';
        if (jQuery("#selectedParams").val().length > 0) {
            jQuery("#selectedParams").val(jQuery("#selectedParams").val() + val);
        } else {
            jQuery("#selectedParams").val(val);
        }
        if (!this.issetCategory) {
            jQuery("#filter_active").val("1");
        }
        jQuery("#adminForm input[name=limitstart]").val(0);
        this.saveSelectedCategory();
        jQuery("#adminForm").submit();
    }

    /**
     * Remove the selected search filter option and submits the form
     *
     * @param type string type of the rule
     * @param id int the selected value of the rule
     */
    removeFilterRule(type, id) {
        let val = type + '=' + id + ';';
        let str = jQuery("#selectedParams").val();
        jQuery("#selectedParams").val((str.replace(val, "")));
        jQuery("#filter_active").val("1");
        this.saveSelectedCategory();

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

    /**
     * Set radius for search and submits the form
     *
     * @param radius int radius search
     */
    setRadius(radius) {
        jQuery("#adminForm > #radius").val(radius);
        jQuery("#adminForm input[name=limitstart]").val(0);
        jQuery("#adminForm").submit();
    }

    /**
     * Filter by fav items on search view.
     *
     * @param requiresLogin
     */
    filterByFavorites(requiresLogin) {
        if (!requiresLogin) {
            jQuery("#adminForm #filter-by-fav").val("1");
            jQuery("#adminForm").submit();
        } else {
            jbdUtils.showLoginNotice();
        }
    }

    /**
     * Initialize an event that when the button with class accordionCollapse is clicked then the search filter will be
     * collapsed or shown
     */
    collapseSearchFilter() {
        //searchFilter collapse
        let headers = ["H1", "H2", "H3", "H4", "H5", "H6"];

        jQuery(".accordionCollapse").click(function (e) {
            let target = e.target,
                name = target.nodeName.toUpperCase();

            if (jQuery.inArray(name, headers) > -1) {
                let subItem = jQuery(target).next();

                //slideUp all elements (except target) at current depth or greater
                let depth = jQuery(subItem).parents().length;
                let allAtDepth = jQuery(".accordion li, .accordion div").filter(function () {
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

    /**
     * Delete listing on front end CP
     *
     * @param id int id of the company
     */
    deleteDirListing(id) {
        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_COMPANIES_CONFIRM_DELETE'))) {
            jQuery("#cid").val(id);
            jQuery("#adminForm #task").val("managecompanies.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Delete message on front end CP
     *
     * @param id int ID of the message
     */
    deleteMessage(id) {
        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_COMPANY_MESSAGE_CONFIRM_DELETE'))) {
            jQuery("#id").val(id);
            jQuery("#adminForm #task").val("managemessages.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Add Service on front end CP
     */
    addService() {
        jQuery("#id").val(0);
        jQuery("#adminForm #task").val("managecompanyservice.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete Service on front end CP
     *
     * @param serviceId int service ID
     */
    deleteService(serviceId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_SERVICE_CONFIRM_DELETE"))) {
            jQuery("#id").val(serviceId);
            jQuery("#adminForm #task").val("managecompanyservices.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Duplicate Service on front end CP
     *
     * @param serviceId int service id
     */
    duplicateService(serviceId) {
        jQuery("#id").val(serviceId);
        jQuery("#adminForm #task").val("managecompanyservice.duplicate");
        jQuery("#adminForm").submit();
    }

    /**
     * Submits form on service provider selection
     */
    selectServiceProviders() {
        jQuery("#adminForm").submit();
    }

    /**
     * Add new service provider on front end CP
     */
    addServiceProvider() {
        jQuery("#id").val(0);
        jQuery("#adminForm #task").val("managecompanyserviceprovider.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete Service Provider on front end CP
     *
     * @param serviceId int service ID
     */
    deleteServiceProvider(serviceId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_SERVICE_PROVIDER_CONFIRM_DELETE"))) {
            jQuery("#adminForm #id").val(serviceId);
            jQuery("#adminForm #task").val("managecompanyserviceproviders.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Submits on reservation selection
     */
    selectReservation() {
        jQuery("#adminForm").submit();
    }

    /**
     * Delete reservation on front end CP
     *
     * @param bookingId int booking ID
     */
    deleteReservation(bookingId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_SERVICE_RESERVATION_CONFIRM_DELETE"))) {
            jQuery("#adminForm #id").val(bookingId);
            jQuery("#adminForm #task").val("managecompanyservicereservations.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Edit project on front end CP
     *
     * @param projectId int project id
     */
    editProject(projectId) {
        jQuery("#adminForm #id").val(projectId);
        jQuery("#adminForm #task").val("managecompanyproject.edit");
        jQuery("#adminForm").submit();
    }

    /**
     * Add new project on front end CP
     */
    addProject() {
        jQuery("#adminForm #id").val(0);
        jQuery("#adminForm #task").val("managecompanyproject.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Delete project on front end CP
     *
     * @param projectId int project ID
     */
    deleteProject(projectId) {
        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_PROJECT_CONFIRM_DELETE'))) {
            jQuery("#adminForm #id").val(projectId);
            jQuery("#adminForm #task").val("managecompanyprojects.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Retrieve the project details with ajax and display them
     *
     * @param project int project ID
     */
    /**
     * Retrieve the project details with ajax and display them
     *
     * @param project int project ID
     */
    showProjectDetail(project) {
        let self = this;
        let baseUrl = jbdUtils.getAjaxUrl('getProjectDetailsAjax', 'companies');
        jQuery.ajax({
            type: "GET",
            url: baseUrl,
            data: {projectId: project},
            dataType: 'json',
            cache:false,
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

                const projectsStyle = jbdUtils.getProperty('projects_style');
                if (projectsStyle == 1) {
                    self.applyLighSlider();
                } else if (projectsStyle == 2) {
                    unitegalleryprojects = null;
                }
            }
        });
    }

    applySwiperSlider() {
        var mySwiper = new Swiper('.swiper-container', {
            // Optional parameters
            slidesPerView: 1.1,
            centeredSlides: true,
            spaceBetween: 5,
            paginationClickable: true,
            observer: true,
            observeParents: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }

    /**
     * Apply unitegallery fullscreen
     */
    applyLighSliderFullscreen() {
        if (unitegalleryprojects == null) {
            unitegalleryprojects = jQuery('#projectImageGallery').unitegallery({
                gallery_theme: "default",
                gallery_height: 550,
                theme_enable_text_panel: true,
                slider_control_zoom: false,
                slider_enable_zoom_panel: false,
                thumb_fixed_size: false
            });
        }

        jQuery('#projectImageGallery').hide();

        document.addEventListener("fullscreenchange", jbdListings.onFullScreenChange, false);
        document.addEventListener("webkitfullscreenchange", jbdListings.onFullScreenChange, false);
        document.addEventListener("mozfullscreenchange", jbdListings.onFullScreenChange, false);

        unitegalleryprojects.toggleFullscreen();
    }

    onFullScreenChange() {
        let fullscreenElement = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement;

        // if in fullscreen mode fullscreenElement won't be null
        if (fullscreenElement == null) {
            jQuery('#projectImageGallery').hide();
        } else {
            jQuery('#projectImageGallery').show();
        }
    }

    /**
     * Apply unitegallery after 2 seconds
     */
    applyLighSlider() {
        setTimeout(() => {
            let unitegallery = jQuery('#projectImageGallery').unitegallery({
                gallery_theme: "default",
                gallery_height: 550,
                theme_enable_text_panel: true,
                slider_control_zoom: false,
                slider_enable_zoom_panel: false,
                thumb_fixed_size: false
            });

        }, 2000);
    }

    /**
     * Return to projects on business details
     */
    returnToProjects() {
        jQuery("#project-details").hide(500);
        jQuery("#company-projects-container").show(500);
    }

    /**
     * Update company rate with ajax
     *
     * @param companyId int company ID
     * @param rateScore double rate score
     */
    updateCompanyRate(companyId, rateScore) {
        let postParameters = "";
        let ratingId = this.getRatingId(companyId);
        if (typeof ratingId === 'undefined') {
            ratingId = 0;
        }
        postParameters += "&companyId=" + companyId;
        postParameters += "&rating=" + rateScore;
        postParameters += "&ratingId=" + ratingId;

        let postData = '&task=companies.updateRating' + postParameters;
        jQuery.post(jbdUtils.baseUrl, postData, this.processRateResult);
    }

    /**
     * Process response from updateCompanyRate function
     *
     * @param response object response
     */
    processRateResult(response) {
        let self = this;
        let xml = response;

        jQuery(xml).find('answer').each(
            function () {
                jQuery("#rateNumber" + jQuery(this).attr('id')).html(
                    jQuery(this).attr('nrRatings'));
                jQuery("#rateNumber" + jQuery(this).attr('id'))
                    .parent().show();
                jQuery('#rating-average').rating('update', jQuery(this).attr('averageRating'));
                self.saveCookieRating(jQuery(this).attr('id'), jQuery(this).attr('ratingId'));
            });
    }

    /**
     * Retrieve rating id from cookie
     *
     * @param companyId int company ID
     * @returns {string}
     */
    getRatingId(companyId) {
        let ratings = jbdUtils.getCookie("companyRatingIds");
        if (typeof ratings === 'undefined') {
            return;
        }

        let ratingsIds = ratings.split('#');
        for (let i = 0; i < ratingsIds.length; i++) {
            let temp = ratingsIds[i].split(',');
            if (temp[0] == companyId)
                return temp[1];
        }
    }

    /**
     * Save cookie for rating of the company. So the same pc dont rate twice
     *
     * @param companyId int company ID
     * @param reviewId int review ID
     */
    saveCookieRating(companyId, reviewId) {
        let ratings = jbdUtils.getCookie("companyRatingIds");
        if (ratings == undefined)
            ratings = companyId + ',' + reviewId + '#';

        let ratingsIds = ratings.split('#');
        let found = false;
        for (let i = 0; i < ratingsIds.length; i++) {
            let temp = ratingsIds[i].split(',');
            if (temp[0] == companyId)
                found = true;
        }
        if (!found) {
            ratings = ratings + companyId + ',' + reviewId + '#';
        }
        jbdUtils.setCookie("companyRatingIds", ratings, 60);
    }

    /**
     * Display product categories
     *
     * @param parentId int parent ID
     */
    showProductCategories(parentId) {
        jQuery('.categories-level-1').hide(500);
        jQuery('.categories-level-' + parentId).show(500);
        let parent = jQuery('#parent-category-' + parentId + ' .post-title').text();
        parent = parent + ' - ' + JBD.JText._('LNG_SUBCATEGORIES');
        jQuery('#sub-categories').html('<a href="javascript:void(0);" onclick="this.showProductCategories(' + parentId + ')">' + parent + '</a>&raquo;');
        jQuery('#category-products').empty();
        jQuery('#product-details').empty();
        jQuery('#product-details-content').empty();
        jQuery('#product-list-content').empty();
    }

    /**
     * Go back to all categories on products section on company details
     */
    goBack() {
        jQuery('.grid4').hide(500);
        jQuery('.categories-level-1').show(500);
        jQuery('#sub-categories').empty();
        jQuery('#category-products').empty();
        jQuery('#product-details').empty();
        jQuery('#product-details-content').empty();
        jQuery('#product-list-content').empty();
    }

    /**
     * Show products after selecting a category on company details
     *
     * @param catId int category ID
     * @param companyId int company ID
     */
    showProducts(catId, companyId) {
        jQuery('.categories-level-1').hide(500);
        jQuery('.grid4').hide(500);
        console.log(jbdUtils.getProperty("baseUrl"));
        console.log(jbdUtils.getProperty('url'));
        jQuery('#product-list-content').html('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>').load(jbdUtils.getProperty("url") + '&view=companyproducts #grid-content', {
            categoryId: catId,
            companyId: companyId
        }, function () {
            let categoryName = jQuery('#product-category h1').text();
            jQuery('#category-products').html('<a href="javascript:void(0);" onclick="jbdListings.goBackToProducts(' + catId + ', ' + companyId + ')">' + categoryName + '</a>&raquo;');
        });
        jQuery('#company-products-title').text(JBD.JText._('LNG_PRODUCTS'));
        jQuery('#product-list-content').attr('style', 'display: block;');
        jQuery('#category-products').empty();
        jQuery('#product-details').empty();
        jQuery('#product-details-content').empty();
    }

    /**
     * Open quote modal for product on company details
     *
     * @param companyId int company ID
     */
    showQuoteCompanyProduct(companyId) {
        var path_part1 = jQuery("#all-categories-path").text();
        var path_part2 = jQuery("#sub-categories").text();
        var path_part3 = jQuery("#category-products").text();
        var path_part4 = jQuery("#product-details").text();

        var path = path_part1;
        if (path_part2!=''){
            path += ' -> '+path_part2;
            if(path_part3 != ''){
                path += ' -> '+path_part3;
                if (path_part4 != ''){
                    path += ' -> '+path_part4;
                }
            }
        }

        jQuery("#company-quote-product #path").val(path);
        jQuery("#company-quote-product #companyId").val(companyId);
        jQuery('#company-quote-product').jbdModal();
    }

    /**
     * Request Quote for product on company details page
     *
     * @param baseurl url of the path where quote is placed
     */
    requestQuoteCompanyProduct(baseurl) {
        var isError = jQuery("#quoteCompanyProductFrm").validationEngine('validate');
        if (!isError)
            return;

        var postData = "";
        postData += "&firstName=" + jQuery("#company-quote-product #firstName-quote").val();
        postData += "&lastName=" + jQuery("#company-quote-product #lastName-quote").val();
        postData += "&email=" + jQuery("#company-quote-product #email-quote").val();
        postData += "&description=" + jQuery("#company-quote-product #description-quote").val();
        postData += "&companyId=" + jQuery("#company-quote-product #companyId").val();
        var productId = jQuery("#company-quote-product #productId").val();
        if (productId != '') {
            postData += "&productId=" + productId;
        }else{
            postData += "&productId=0";
        }
        var productAlias = jQuery("#company-quote-product #productAlias").val();
        if (productAlias != '') {
            postData += "&productAlias=" + productAlias;
        }else{
            postData += "&productAlias=0";
        }
        var productSubject = jQuery("#company-quote-product #productSubject").val();
        if (productSubject != '') {
            postData += "&productSubject=" + productSubject;
        }else{
            postData += "&productSubject=0";
        }
        postData += "&path=" + jQuery("#company-quote-product #path").val();
        postData += "&recaptcha_response_field=" + jQuery("#company-quote-product #recaptcha_response_field").val();
        postData += "&g-recaptcha-response=" + jQuery("#company-quote-product #g-recaptcha-response-1").val();

        jQuery.post(baseurl, postData, this.processContactCompanyResult);
    }

    /**
     * Handle the response from requestQuoteCompanyProduct function
     *
     * @param response object response
     */
    processQuoteCompanyResult(response) {
        var xml = response;
        jQuery(xml).find('answer').each(function () {
            jQuery(".quote-submit-button").removeClass("loader");
            
            let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + JBD.JText._('COM_JBUSINESS_ERROR') +'</p>'
			+'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div> <div class="jmodal-body"><p>' +jQuery(this).attr('errorMessage') + '</p></div> </div></div>';
            
            jQuery(html).appendTo('body').jbdModal();
            setTimeout(function () {
                jQuery.jbdModal.close()
            }, 2000);
            
        });
    }

    /**
     * Get back to categories on products section
     *
     * @param catId int cateogry ID
     */
    goBackToCategories(catId) {
        jQuery('#product-list-content').empty();
        jQuery('#subcategory-' + catId).closest('.grid4').show(500);
        jQuery('#company-products-title').text(JBD.JText._('LNG_PRODUCT_CATEGORIES'));
    }

    /**
     * Open the product details on front end section on company detail view
     *
     * @param productId int product id
     * @param catId int category ID
     * @param companyId int company ID
     * @param productAlias string product alias
     * @param productSubject string product subject
     */
    showProductDetails(productId, catId, companyId, productAlias, productSubject) {
        jQuery('#product-list-content').hide(500);
        jQuery('#product-details-content').html('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>').load(jbdUtils.getProperty("url") + '&view=companyproducts #product-details', {
            productId: productId,
            categoryId: catId
        }, function () {
            let productName = jQuery('#product-name h2').text();
            jQuery('#product-details').html('<a style="color:black;">' + productName + '</a>');
            jQuery("#company-quote-product #productId").val(productId);
            jQuery("#company-quote-product #productAlias").val(productAlias);
            jQuery("#company-quote-product #productSubject").val(productSubject);
            jQuery('#product-quote-request').html();
            jQuery('#product-quote-request').html('<a href="javascript:void(0);" class="btn btn-primary" style="float: right" onclick="jbdListings.showQuoteCompanyProduct('+companyId+')">'+JBD.JText._('LNG_QUOTE')+'</a>');
        });
        jQuery('#company-products-title').text(JBD.JText._('LNG_PRODUCT_DETAILS'));
        jQuery('#product-details-content').show(500);
    }

    /**
     * Go back to product section
     *
     * @param catId int category ID
     * @param companyId int company ID
     */
    goBackToProducts(catId, companyId) {
        jQuery('#product-details-content').hide(500);
        jQuery('#product-details-content').empty();
        this.showProducts(catId, companyId);
        jQuery('#product-list-content').show(500);
        jQuery('#product-list-content').attr('style', 'display: block;');
    }
    /** 
     * Add new team member
     * 
    */
    addNewMember(index,memberFolder,memberFolderPath) {
        var newMemId = parseInt(index) + 1;
        jQuery('#member-form-box1').clone().prop('id', 'member-form-box' + newMemId).appendTo('#member_details');
        jQuery("#member-form-box" + newMemId).find('h3').text(JBD.JText._('LNG_MEMBER') + ' ' + newMemId);
        jQuery('#member-form-box' + newMemId + ' input').each(function () {
            jQuery(this).val('');
        });
        jQuery('#member-form-box' + newMemId + ' textarea').each(function () {
            jQuery(this).html('');
        });
    
        jQuery('#member-form-box' + newMemId + ' textarea').val('');
        jQuery('#member-form-box' + newMemId + ' .input-imageLocation').prop('id', 'member-imageLocation' + newMemId);
        jQuery('#member-form-box' + newMemId + ' .input-imageLocationSize').prop('id', 'member-imageUploader' + newMemId);
    
        var options = jQuery("#member-form-box1 #member_type_1").clone();
        jQuery("#member-form-box"+newMemId+" #member_type1_chosen").remove();
        jQuery("#member-form-box"+newMemId+" #member_type_select").append(options);
        jQuery("#member-form-box"+newMemId+" #member_type1").removeAttr('onchange');
        jQuery('.chosen-select').chosen({disable_search_threshold: 5,search_contains:false});
    
        jQuery('#member-form-box' + newMemId + ' .services').prop('id', 'member-picture-preview' + newMemId);
        jQuery('#member-picture-preview' + newMemId).html('');
    
        jQuery('#member-form-box' + newMemId + ' #memberImageSection').find('a').prop('href', 'javascript:jbdListings.removeMemberLogo('+ newMemId +')');
    
        jQuery("#member-form-box" + newMemId + " .remove-member").attr('href', 'javascript:jbdListings.removeMember(\'' + newMemId + '\')').show();
        jQuery('#add_member').attr('onclick', 'jbdListings.addNewMember(\'' + newMemId + '\',\'' + memberFolder + '\',\'' + memberFolderPath + '\' )');
    
        console.debug(memberFolder);
        console.debug(memberFolderPath);

        uploadInstance.imageUploader(memberFolder, memberFolderPath, 'member-',newMemId);
        
    }
    
    /**
     * Remove memeber
     * 
     * @param {*} index 
     */
    removeMember(index) {
        if (index < 2)
            return;
    
        index = parseInt(index);
        jQuery('#member-form-box' + index).remove();
    }
    
    /**
     * Remove member logo
     * 
     * @param {*} id 
     */
    removeMemberLogo(id) {
        jQuery('#member-imageLocation'+id).val("");
        jQuery('#member-picture-preview'+id).html("");
        jQuery('#member-imageUploader'+id).val("");
    }
}

let jbdListings = new JBDListings();