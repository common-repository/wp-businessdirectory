class JBDUtils{
    /**
     * Sets the main Utils properties. Should be set before trying to call other utility functions.
     *
     * @param properties array
     */
    setProperties(properties) {
        let tmp = '';

        if (properties["languageCode"]) {
            tmp = properties["languageCode"] + '/';
        }

        if (properties["isProfile"] == JBDConstants.IS_ADMIN) {
            tmp = 'wp-admin/?page=jbd_businessdirectory&';
        }

        properties.url = properties.siteRoot + tmp + 'index.php?directory=1&option=com_jbusinessdirectory';

        this.properties = properties;
        this.properties["month_names"] = this.properties["month_names"].split(",");
    }

    /**
     * Get's a property value by its name.
     *
     * @param property string name of the property
     * @returns {*}
     */
    getProperty(property) {
        if (typeof this.properties === 'undefined') {
            console.warn('JBDUtils properties not set!');
        } else {
            if (typeof this.properties[property] === 'undefined') {
                console.warn('JBDUtils property: ' + property + ' not found!');
            } else {
                return this.properties[property];
            }
        }

        return null;
    }

    /**
     * Get's a property value by its name.
     *
     * @param property string name of the property
     * @param value
     * @returns {*}
     */
    setProperty(property, value) {

        this.properties[property] = value;

        return true;
    }

    /**
     * Generates URL for Ajax Calls. If frontController is specified, it will be used instead of controller
     * when the call is initiated from site (not admin).
     *
     * @param task string name of the task
     * @param controller string name of the controller
     * @param frontController string optional, name of the front controller
     * @returns {string}
     */
    getAjaxUrl(task, controller, frontController = '') {
        let url = '';

        if (typeof controller === 'undefined' || controller.length === 0) {
            console.warn('Controller cannot be empty!');
            return url;
        }

        if (typeof task === 'undefined' || task.length === 0) {
            console.warn('Task cannot be empty!');
            return url;
        }

        let cntrl = controller;

        let isProfile = jbdUtils.getProperty('isProfile');
        if (isProfile === JBDConstants.IS_FRONT && frontController.length > 0) {
            cntrl = frontController;
        }

        let baseUrl = jbdUtils.getProperty('url');
        url += baseUrl + '&task=' + cntrl + '.' + task;

        return url;
    }

    /**
     * Convert given date to the date format set on general settings
     *
     * @param date string date
     * @returns {string}
     */
    getDateWithFormat(date) {
        let format = this.getProperty('dateFormat');
        let delimiter = '-';

        if (format.indexOf('/') > -1) {
            delimiter = '/';
        } else if (format.indexOf('\\') > -1) {
            delimiter = '\\';
        }

        let tmp = format.split(delimiter);

        let newDate = '';
        for (let i = 0; i < 3; i++) {
            if (tmp[i] === 'd')
                newDate += ("0" + date.getDate()).slice(-2);
            else if (tmp[i] === 'm')
                newDate += ("0" + (date.getMonth() + 1)).slice(-2);
            else if (tmp[i] === 'y' || tmp[i] === 'Y')
                newDate += date.getFullYear();

            if (i < 2)
                newDate += delimiter;
        }

        return newDate;
    }

    /**
     * Sleep function. Stop any process for an amount of milliseconds
     *
     * @param milliseconds int the amount of milliseconds to pause the process
     */
    sleep(milliseconds) {
        let start = new Date().getTime();
        for (let i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds) {
                break;
            }
        }
    }

    /**
     * Increase the statistic for the item. All the info is passed through the parameters
     *
     * @param itemId int item id for the item that we will save the statistics
     * @param type int type of statistic
     * @param articleId int if there is a statistic for an article of any listing then we will need to store also the id
     * of the article
     */
    registerAction(itemId, type, articleId) {
        let urlWebsiteCount = jbdUtils.getAjaxUrl('increaseStatisticCountAjax', 'companies');

        jQuery.ajax({
            type: "GET",
            data: {
                companyId: itemId,
                type: type,
                articleId: articleId
            },
            url: urlWebsiteCount,
            success: function () {
            }
        });
    }

    /**
     * Increase the share clicks statistics for an item(listing, offer, event)
     *
     * @param itemId int item Id for the object who is being shared
     * @param itemType int the type of the object
     */
    increaseShareClicks(itemId, itemType) {
        let urlShareCount = jbdUtils.getAjaxUrl('increaseShareCountAjax', 'companies');

        jQuery.ajax({
            type: "GET",
            data: {
                itemId: itemId,
                itemType: itemType
            },
            url: urlShareCount,
            success: function () {
            }
        });
    }

    /**
     * Update the current url with the coordinates passed through parameters
     *
     * @param position object contain the latitude and logitude to pass to the url
     */
    addCoordinatesToUrl(position) {
        let latitude = position.coords.latitude;
        let longitude = position.coords.longitude;

        let newURLString = window.location.href;
        newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
        newURLString += "geo-latitude=" + latitude;
        newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
        newURLString += "geo-longitude=" + longitude;

        window.location.href = newURLString;    // The page will redirect instantly
    }

    /**
     * Retrieve the cookie searched for by the parameters
     *
     * @param c_name string cookie we are searching for
     * @returns {string}
     */
    getCookie(c_name) {
        let i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++) {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            if (x == c_name) {
                return unescape(y);
            }
        }
    }

    /**
     * Set the cookie based on the parameters sent to it
     *
     * @param c_name string the name of the cookie
     * @param value int the value of the cookie
     * @param exdays int the expiration days for that cookie
     */
    setCookie(c_name, value, exdays) {
        let exdate = new Date();
        exdate.setDate(exdate.getDate() + exdays);
        let c_value = escape(value)
            + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
        document.cookie = c_name + "=" + c_value;
    }

    /**
     * Whenever this function is called it will render the Radio buttons on that page.
     */
    renderRadioButtons() {
        //Turn radios into btn-group
        jQuery('.radio.btn-group label').addClass('btn');
        jQuery('.btn-group label:not(.active)').click(function () {
            let label = jQuery(this);
            let input = jQuery('#' + label.attr('for'));

            if (!input.prop('checked')) {
                label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');
                if (input.val() == '') {
                    label.addClass('active btn-primary');
                } else if (input.val() == 0) {
                    label.addClass('active btn-danger');
                } else {
                    label.addClass('active btn-success');
                }
                input.prop('checked', true);
            }
        });

        jQuery('.btn-group input[checked=checked]').each(function () {
            if (jQuery(this).val() == '') {
                jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-primary');
            } else if (jQuery(this).val() == 0) {
                jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-danger');
            } else {
                jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-success');
            }
        });
    }

    //************--Edit Views--*****************//

    /**
     * Initialize the Autocomplete. All the component that will need to be filled will be passed through the params.
     *
     * @param preventSubmit boolean true or false if true the form will be submitted
     * @param componentForm array components that will be filled when the address is filled
     */
    initializeAdminAutocomplete(preventSubmit, componentForm) {
        let element = document.getElementById('autocomplete');

        JBDMapHelper.initAutocomplete(element, function (place) {
            jbdUtils.fillInAddress(place, componentForm);
        }, preventSubmit, true, null, componentForm);
    }

    /**
     * When a map is clicked an event is initialized and the marker will be set on map and also the coordinates will
     * be updated
     */
    addAdminMapClickAction() {
        if (typeof jbdAdminMapInstance !== 'undefined') {
            jbdAdminMapInstance.addMapListener('click', function (location) {
                jbdAdminMapInstance.clearMarkers();
                jbdAdminMapInstance.addMarker(location, function () {
                    document.getElementById("latitude").value = location.latitude;
                    document.getElementById("longitude").value = location.longitude;
                });
            });
        }
    }

    /**
     * Fill all the elements of the address form based on the information passed from autocomplete
     *
     * @param place array the details of the place that was selected from autocomplete and will be used to update the
     * elements in the address form
     * @param component_form array elements from form
     */
    fillInAddress(place, component_form) {
        if (typeof component_form === 'undefined') {
            component_form = jbdUtils.getComponentForm();
        }

        for (let component in component_form) {
            let obj = document.getElementById(component);
            if (typeof maybeObject != "undefined") {
                document.getElementById(component).value = "";
                document.getElementById(component).disabled = false;
            }
        }
        for (let j in place) {
            let att = j;
            if (component_form[att]) {
                let val = place[j];
                jQuery("#" + att).val(val);
                if (att == 'country') {
                    jQuery('#country option').filter(function () {
                        return jQuery(this).text() === val;
                    }).attr('selected', true);
                    if (jbdUtils.getProperty('limit_cities_regions')) {
                        //TODO global reference (defined in edit views)
                        updateRegions(function () {
                            if (att == 'administrative_area_level_1') {
                                let regionsElement = jQuery('#activity_regions option');
                                let regionsId = "#activity_regions";

                                if (!regionsElement.length) {
                                    regionsElement = jQuery('#administrative_area_level_1 option');
                                    regionsId = "#administrative_area_level_1";
                                }

                                regionsElement.filter(function () {
                                    return jQuery(this).text() === val;
                                }).attr('selected', true);
                                jbdUtils.updateChosenSelect(regionsId);
                            }

                            if (att == 'locality') {
                                //TODO global reference (defined in edit views)
                                updateCities(function () {
                                    let citiesElement = jQuery('#activity_cities option');
                                    let citiesId = "activity_cities";

                                    if (!citiesElement.length) {
                                        citiesElement = jQuery('#locality option');
                                        citiesId = "#locality";
                                    }

                                    citiesElement.filter(function () {
                                        return jQuery(this).text() === val;
                                    }).attr('selected', true);
                                    jbdUtils.updateChosenSelect(citiesId);
                                });
                            }
                        });
                    }
                }
            }
        }
        jbdUtils.updateChosenSelect('#country');
    }

    /**
     * Gets the component form needed to format the geolocation response
     *
     * @returns {{street_number: string, route: string, locality: string, administrative_area_level_1: string, country: string, postal_code: string, administrative_area_level_2: string, latitude: string, longitude: string, area_id: string}}
     */
    getComponentForm() {
        return {
            'street_number': 'short_name',
            'route': 'long_name',
            'locality': 'long_name',
            'administrative_area_level_1': 'long_name',
            'country': 'long_name',
            'postal_code': 'short_name',
            'administrative_area_level_2': 'long_name',
            'latitude': 'long',
            'longitude': 'long',
            'area_id': 'short_name'
        };
    }

    /**
     * Validates the Application Settings section on submit button
     *
     * @param validateNonVisible boolean validates the hidden form elements
     * @param validateRichText boolean validates the inputs that contain rich text
     * @param formName string name of the form that will be validated
     * @returns {jQuery}
     */
    validateAppSettingsForm(validateNonVisible, validateRichText, formName) {
        formName = formName || "item-form";
        let validationResult = false;

        jQuery("#" + formName).validationEngine('attach', {
            validateNonVisibleFields: validateNonVisible,
        });

        validationResult = jQuery("#" + formName).validationEngine('validate');

        if (!validationResult) {
            jbdUtils.showSettingsValidationError(formName);
        }

        return validationResult;
    }

    /**
     * Validates the form elements based on the parameters passed to the function
     *
     * @param validateNonVisible boolean validates the hidden form elements
     * @param validateRichText boolean validates the inputs that contain rich text
     * @param customRuleHook string custom function to run if it is needed
     * @returns {boolean}
     */
    validateCmpForm(validateNonVisible, validateRichText, task = null, customRuleHook) {
        if (typeof customRuleHook !== "undefined") {
            customRuleHook();
        }

        if (validateRichText) {
            jbdUtils.validateRichTextEditors();
        }

        jbdUtils.validateMultiSelects();
        jbdUtils.validateSingleSelects();
        let isError = jQuery("#item-form").validationEngine('validate', {validateNonVisibleFields: validateNonVisible});
        if(isError) {            
            if(task == "apply") {
                jQuery(".button-save").addClass("loader")
                jQuery(".button-save, .button-close , .button-cancel").attr("disabled", true)
            } 
            if(task == "save") {
                jQuery(".button-close").addClass("loader")
                jQuery(".button-save, .button-close , .button-cancel").attr("disabled", true)

            }
        }
        return !isError;
    }

    /**
     * Show the validation error to the form which is passed on parameters.
     *
     * @param formName string name of the form which has been checked for validation
     */
    showValidationError(formName) {
        formName = formName || "item-form";
        jQuery("#validation-error").show(500);

        setTimeout(function () {
            jQuery("#validation-error").hide(700);
        }, 5000);

        setTimeout(function () {
            for (let i = 1; i <= 6; i++) {
                if (jQuery("#edit-tab" + i + " div.formError").length) {
                    console.log("show tab " + i);
                    jbdTabs.openTab(i);
                    break;
                }
            }
            setTimeout(function () {
                jQuery('#' + formName).validationEngine('updatePromptsPosition');
            }, 500);
        }, 1500);
    }

    /**
     * Show the validation error to general settings section after it is been checked
     *
     * @param formName string name of the form which has been checked for validation
     */
    showSettingsValidationError(formName) {
        formName = formName || "item-form";
        jQuery("#validation-error").show(500);

        setTimeout(function () {
            jQuery("#validation-error").hide(700);
        }, 5000);
        let numItems = jQuery('.focus_validate').length;
        setTimeout(function () {
            for (let i = 1; i <= numItems; i++) {
                if (jQuery(".panel_" + i + "_content div.formError").length) {
                    console.log("show tab " + i);
                    jbdUtils.openSettingsTab(i);
                    break;
                }
            }
            setTimeout(function () {
                jQuery('#' + formName).validationEngine('updatePromptsPosition');
            }, 500);
        }, 1500);
    }

    /**
     * Open the corresponding tab after it has been validated and there is an error.
     *
     * @param tab int id of the tab that will be opened
     */
    openSettingsTab(tab) {
        jQuery("#adminForm").validationEngine('detach');
        let validationResult = jQuery("#adminForm").validationEngine('validate');

        if (!validationResult) {
            return;
        }

        jQuery('.panel_' + tab).click();
    }

    /**
     * Validates form tabs of the form with id item-form and displays the validations for it.
     *
     * @param validateNonVisible boolean validates the hidden form elements
     * @param validateRichText boolean validates the inputs that contain rich text
     * @returns {jQuery}
     */
    validateTabs(validateNonVisible, validateRichText, task = null) {
        let validationResult = false;

        if (jbdUtils.getProperty('isMultilingual')) {
            jQuery(".tab-" + jbdUtils.getProperty("defaultLang")).each(function () {
                jQuery(this).click();
            });
        }

        if (validateRichText) {
            this.validateRichTextEditors();
        }

        jbdUtils.validateMultiSelects();
        jQuery("#item-form").validationEngine('attach', {
            validateNonVisibleFields: validateNonVisible,
        });

        validationResult = jQuery("#item-form").validationEngine('validate');

        if (!validationResult) {
            this.showValidationError();
        } else { 
            if(task == "apply") {
                jQuery(".button-save").addClass("loader")
                jQuery(".button-save , .button-close , .button-cancel").attr("disabled", true)
            } 
            if(task == "save") {
                jQuery(".button-close").addClass("loader")
                jQuery(".button-save , .button-close, .button-cancel").attr("disabled", true)

            }            
        }

        return validationResult;
    }

    /**
     * Validates all rich text editors on the form. Mostly called on javascript functions
     */
    validateRichTextEditors() {
        let lang = '';
        
        if (jbdUtils.getProperty('isMultilingual')) {
            lang += '_' + jbdUtils.getProperty('langTab');
            jQuery(".tab-" + jbdUtils.getProperty('defaultLang')).each(function () {
                jQuery(this).click();
            });

            jQuery(".tab_description_" + jbdUtils.getProperty('defaultLang')).click();
        }

        jQuery(".js-editor-tinymce").each(function () {
            let textarea = jQuery(this).find('textarea');
            tinyMCE.triggerSave();
            if (textarea.attr('id') == 'description' + lang) {
                if (jQuery.trim(textarea.val()).length > 0) {
                    if (jQuery(this).hasClass("validate[required]"))
                        jQuery(this).removeClass("validate[required]");
                }
                else {
                    if (!jQuery(this).hasClass("validate[required]"))
                        jQuery(this).addClass("validate[required]");
                }
            }
        });
    }

    /**
     * Opens a link in new tab. Link is passed through function parameters
     *
     * @param link string is the link that will need to be accessed
     */
    showItem(link) {
        let win = window.open(link, '_blank');
        win.focus();
    }

    /**
     * When called opens the rest of the text that is hidden.
     */
    readMore() {
        jQuery("#general-info").removeClass("collapsed");
        jQuery(".read-more").hide();
    }

    /**
     * Is called for the show more or show less on the search filter on search page. It displays the rest of the filters
     * when clicked show more or hide them when clicked show less
     */
    applyReadMore() {
        let showChar = 70;  // How many characters are shown by default
        let ellipsestext = "...";
        let moretext = "Show more";
        let lesstext = "Show less";

        jQuery('.read-more').each(function () {
            let content = jQuery(this).html();

            if (content.length > showChar) {
                let c = content.substr(0, showChar);
                let h = content.substr(showChar, content.length - showChar);

                let html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="morelink" >' + moretext + '</a></span>';

                jQuery(this).html(html);
            }
        });

        jQuery(".morelink").click(function () {
            if (jQuery(this).hasClass("less")) {
                jQuery(this).removeClass("less");
                jQuery(this).html(moretext);
            } else {
                jQuery(this).addClass("less");
                jQuery(this).html(lesstext);
            }
            jQuery(this).parent().prev().toggle();
            jQuery(this).prev().toggle();
            return false;
        });
    }

    /**
     * Calcualates the length of the text in text area. Is used more for the calculations of the descriptions
     */
    calculateLength() {
        let obj = jQuery("#description");

        let max = parseInt(obj.attr('maxlength'));
        if (obj.val().length > max) {
            obj.val(obj.val().substr(0, obj.attr('maxlength')));
        }

        jQuery("#descriptionCounter").val((max - obj.val().length));
    }

    /**
     * Calcualates the length of the text in text area. Is used more for the calculations of the short descriptions
     */
    calculateLengthShort() {
        let obj = jQuery("#short_description");

        let max = parseInt(obj.attr('maxlength'));
        if (obj.val().length > max) {
            obj.val(obj.val().substr(0, obj.attr('maxlength')));
        }

        jQuery("#descriptionCounterShort").val((max - obj.val().length));
    }

    /**
     * Validates chosen multi select. Called on the javascript functions
     */
    validateMultiSelects() {
        let chosenSelector = '.chzn-container-multi';
        if (!jQuery(chosenSelector).length) {
            chosenSelector = '.chosen-container-multi';
        }

        jQuery(chosenSelector).each(function () {
            let id = jQuery(this).attr('id');
            let selectId = id.substr(0, id.lastIndexOf("_"));

            if (jQuery('#' + selectId).hasClass('validate[required]') || jQuery('#' + id).hasClass('validate[required]')) {
                let values = jQuery('#' + selectId).chosen().val();
                if (typeof values === 'undefined' || values == null || values.length == 0) {
                    if (!jQuery('#' + id).hasClass('validate[required]')) {
                        jQuery('#' + id).addClass('validate[required]');
                    }
                    jQuery('#' + selectId).removeClass('validate[required]');
                }
                else {
                    jQuery('#' + id).removeClass('validate[required]');
                    jQuery('#' + selectId).addClass('validate[required]');
                }
            }
        });
    }

    /**
     * Validates chosen select. Called on the javascript functions
     */
    validateSingleSelects() {
        let chosenSelector = '.chzn-container-single';
        if (!jQuery(chosenSelector).length) {
            chosenSelector = '.chosen-container-single';
        }

        jQuery(chosenSelector).each(function () {
            let id = jQuery(this).attr('id');
            let selectId = id.substr(0, id.lastIndexOf("_"));

            if (jQuery('#' + selectId).hasClass('validate[required]') || jQuery('#' + id).hasClass('validate[required]')) {
                let values = jQuery('#' + selectId).chosen().val();
                if (typeof values === 'undefined' || values == null || values.length == 0) {
                    if (!jQuery('#' + id).hasClass('validate[required]')) {
                        jQuery('#' + id).addClass('validate[required]');
                    }
                    jQuery('#' + selectId).removeClass('validate[required]');
                }
                else {
                    jQuery('#' + id).removeClass('validate[required]');
                    jQuery('#' + selectId).addClass('validate[required]');
                }
            }
        });
    }

    //************--End Edit Views--***********************//


    //***************--Front End Views--*******************//

    /**
     * Validates the form given by the parameter. Mostly the forms on the component has id = item-form and after that
     * it submits the form.
     *
     * @param formId string the id of the form
     */
    saveForm(formId = 'item-form') {
        let isError = true;
        jQuery('#' + formId).validationEngine('detach');

        isError = jQuery('#' + formId).validationEngine('validate');
        jQuery('#' + formId).validationEngine('attach');
        if (!isError) {
            return;
        }

        jQuery("#" + formId + " button").each(function () {
            jQuery(this).attr("disabled", "disabled");
        });

        //loading button animation if validation is OK
        jQuery("#" + formId + " .jbd-commit").addClass("loader");

        document.getElementById(formId).submit();
    }

    /**
     * Opens login modal.
     */
    showLoginNotice() {
        jQuery('#login-notice').jbdModal();
    }

    /**
     * When called it displays or hides the map in the map container on the search listing
     * @param display
     */
    showMap(display) {
        if (!jQuery("#map-link").hasClass("active")) {
            jQuery("#companies-map-container").show();
            jQuery("#map-link").addClass("active");
        } else {
            jQuery("#companies-map-container").hide();
            jQuery("#map-link").removeClass("active");
        }

        mapInstance.initialize();
    }

    /**
     * Hides the map on listing detail view
     */
    hideMap() {
        jQuery("#company-map-holder").hide();
    }

    /**
     * Initialize the map autocomplete on search view. When a location is searched and selected it fills the lat and long
     * on the form parameters
     *
     * @param token string token to check and identify which module is used on a form
     * @param preventSubmit boolean prevents the form submit if true
     * @param limitedCountries json it contains the countries that are selected on which the search can be done
     */
    initializeSearchAutocomplete(token, preventSubmit, limitedCountries) {
        let element = document.getElementsByClassName('zipcode-' + token)[0];

        jQuery(document).ready( function () {
            JBDMapHelper.initAutocomplete(element, function (place) {
                jQuery("#geo-latitude").val(place['latitude']);
                jQuery("#geo-longitude").val(place['longitude']);
                jQuery("#location-detected").val("1");
            }, preventSubmit, false, limitedCountries);
        });
    }

    /**
     * Used when filter rule is called to save the category selected on the form parameters. The selected category will
     * be set or replaced based on the category selected
     *
     * @param categorySet boolean if true get the category selected on the select box otherwise get the one on the parameters
     * @param categId int category id passed on the parameters that needs to be saved
     */
    saveSelectedCategory(categorySet, categId) {
        let catId;
        let checked = jQuery("#search-filter input[type='checkbox']:checked");
        catId = checked.attr('id');

        if (categorySet) {
            catId = categId;
        }

        jQuery("#adminForm #categoryId").val(catId);
        jQuery("#adminForm input[name=limitstart]").val(0);
    }

    /**
     * Add the selected category to categoryId field on the form
     *
     * @param categoryId int category id that will be saved
     */
    chooseCategory(categoryId) {
        if (categoryId.toString().substring(0, 3) == "chk") {
            categoryId = categoryId.substring(3);
        }

        categoryId = categoryId.toString().replace(";", "");
        jQuery("#adminForm #categoryId").val(categoryId);
        jQuery("#adminForm input[name=limitstart]").val(0);
        jQuery("#adminForm").submit();
    }

    /**
     * When a rule is removed from the search filter on search view then it will call this function to remove the rule
     * from the form hidden field
     *
     * @param type string the type of the rule removed
     */
    removeSearchRule(type) {
        if (type == "province")
            jQuery("#adminForm #province-search").val("");
        if (type == "type")
            jQuery("#adminForm #type-search").val("");
        if (type == "zipcode") {
            jQuery("#adminForm #zipcode").val("");
            jQuery("#adminForm #radius").val("");
        }
        if (type == "location") {
            jQuery("#adminForm #geo-latitude").val("");
            jQuery("#adminForm #geo-longitude").val("");
        }
        if (type == "age") {
            jQuery("#adminForm #age").val("");
        }
        if (type == "start-time") {
            jQuery("#adminForm #start_time").val("");
        }
        if (type == "end-time") {
            jQuery("#adminForm #end_time").val("");
        }
        if (type == "country")
            jQuery("#adminForm #country-search").val("");
        if (type == "region")
            jQuery("#adminForm #region-search").val("");
        if (type == "city")
            jQuery("#adminForm #city-search").val("");
        if (type == "keyword")
            jQuery("#adminForm #searchkeyword").val("");
        if (type == "startDate")
            jQuery("#adminForm #startDate").val("");
        if (type == "endDate")
            jQuery("#adminForm #endDate").val("");
        if (type == "minprice")
            jQuery("#adminForm #minprice-search").val("");
        if (type == "maxprice")
            jQuery("#adminForm #maxprice-search").val("");

        jQuery("#adminForm").submit();
    }

    /**
     * Its called on the horizontal search filter and removed the attribute condition from filter
     * After removing it then submits the form.
     *
     * @param attributeId int attribute ID
     */
    removeAttrCond(attributeId) {
        jQuery("input[name=attribute_" + attributeId + "]").remove();
        jQuery("#attribute_" + attributeId).val("");
        jQuery("#adminForm").submit();
    }

    /**
     * After an element from search filter is selected then it calls this function to save the rule and submits the form
     *
     * @param type string type of the rule
     * @param id int the selected value of the rule
     * @param categorySet boolean if its set to yes get the category from module
     * @param categId int category Id that is selected
     */
    addFilterRule(type, id, categorySet, categId) {
        let val = type + '=' + id + ';';
        console.debug(val);
        if (jQuery("#selectedParams").val().length > 0) {
            jQuery("#selectedParams").val(jQuery("#selectedParams").val() + val);
        } else {
            jQuery("#selectedParams").val(val);
        }
        console.debug(jQuery("#selectedParams").val());

        if (categorySet) {
            jQuery("#filter_active").val("1");
        }

        jQuery("#adminForm input[name=limitstart]").val(0);
        //jbdUtils.saveSelectedCategory(categorySet, categId);
        jQuery("#adminForm").submit();
    }

    /**
     * Remove the selected search filter option and submits the form
     *
     * @param type string type of the rule
     * @param id int the selected value of the rule
     * @param categorySet boolean if its set to yes get the category from module
     * @param categId int category Id that is selected
     */
    removeFilterRule(type, id, categorySet, categId) {
        let val = type + '=' + id + ';';
        let str = jQuery("#selectedParams").val();
        jQuery("#selectedParams").val((str.replace(val, "")));
        jQuery("#filter_active").val("1");
        //jbdUtils.saveSelectedCategory(categorySet, categId);

        if (type == "city")
            jQuery("#adminForm #city-search").val("");
        if (type == "region")
            jQuery("#adminForm #region-search").val("");
        if (type == "country")
            jQuery("#adminForm #country-search").val("");
        if (type == "type")
            jQuery("#adminForm #type-search").val("");

        jQuery("#adminForm").submit();
    }

    /**
     * Reset the search filter on search Results
     *
     * @param resetCategories boolean reset also category selection if it is true
     * @param isCompany boolean set true if we are on companies search view.
     */
    resetFilters(resetCategories, isCompany = false) {
        jQuery("#selectedParams").val("");
        
        if (resetCategories) {
            jQuery("#categories-filter").val("");
        } else {
            if (!isCompany) {
                this.saveSelectedCategory();
            } else {
                jbdListings.saveSelectedCategory();
            }
        }
        jQuery("#adminForm #categoryId").val("");

        jQuery("#adminForm #searchkeyword").val("");
        jQuery("#adminForm #zipcode").val("");
        jQuery("#adminForm #city-search").val("");
        jQuery("#adminForm #region-search").val("");
        jQuery("#adminForm #province-search").val("");
        jQuery("#adminForm #age").val("");
        jQuery("#adminForm #start_time").val("");
        jQuery("#adminForm #end_time").val("");
        jQuery("#adminForm #minprice-search").val("");
        jQuery("#adminForm #maxprice-search").val("");
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

    /**
     * When a value is clicked from more filters then this function is called to add or remove the rule
     *
     * @param val string the filter value that is checked
     */
    checkMoreFilterRule(val) {
        let selected = jQuery('input[name=' + val + ']:checked').length;

        if (selected > 0) {
            jbdUtils.addMoreFilter(val);
        } else {
            jbdUtils.removeMoreFilter(val);
        }
    }

    /**
     * If the value from more filter is checked then it will be added for the search params
     *
     * @param val string the value that is selected
     */
    addMoreFilter(val) {
        val = val + ';';
   
        if (jQuery("#moreParams").val().length > 0) {
            jQuery("#moreParams").val(jQuery("#moreParams").val() + val);
        } else {
            jQuery("#moreParams").val(val);
        }

        jQuery("#adminForm input[name=limitstart]").val(0);
    }

    /**
     * If the value from more filter is checked then it will be removed for the search params
     *
     * @param val string the value that is selected
     */
    removeMoreFilter(val) {
        val = val + ';';
        let str = jQuery("#adminForm #moreParams").val();
        jQuery("#adminForm #moreParams").val((str.replace(val, "")));
    }

    /**
     * Empty the search params related to the more filter
     */
    resetMoreFilter() {
        jQuery("#adminForm #moreParams").val("");
        jQuery("#adminForm").submit();
    }

    /**
     * Add selected category to search params on listing search view
     *
     * @param catId int category id selected
     */
    addFilterRuleCategory(catId) {
        catId = catId + ";";
        if (jQuery("#categories-filter").val().length > 0) {
            jQuery("#categories-filter").val(jQuery("#categories-filter").val() + catId);
        } else {
            jQuery("#categories-filter").val(catId);
        }
        jQuery("#filter_active").val("1");
        jQuery("#adminForm input[name=limitstart]").val(0);
        jbdUtils.chooseCategory(catId);
    }

    /**
     * Removes the selected category from search params on listing search view
     *
     * @param catId int category id selected
     */
    removeFilterRuleCategory(catId) {
        let categoryId = catId + ";";
        let str = jQuery("#categories-filter").val();
        jQuery("#categories-filter").val((str.replace(categoryId, "")));

        jQuery("#categorySearch").val("");
        jQuery("input[name='categorySearch']").each(function () {
            jQuery(this).val("");
        });

        jQuery("#filter_active").val("1");
        let checked = jQuery("#filterCategoryItems input[type='checkbox']:checked");

        if (checked.length > 0) {
            checked.each(function () {
                let id = jQuery(this).attr('id');
                if (id != catId) {
                    jbdUtils.chooseCategory(id);
                    return false;
                }
            });
        } else if (checked.length == 0) {
            let categoryIds = jQuery("#categories-filter").val();
            categoryId = categoryIds.slice(0, categoryIds.length - 1);
            let start = categoryId.lastIndexOf(';') + 1;
            if (start == -1) {
                start = 0;
            }

            categoryId = categoryId.slice(start, categoryId.length);
            jbdUtils.chooseCategory(categoryId);
        }
    }

    /**
     * Shows more params of search filter when more is selected
     *
     * @param div string div name
     * @param lessButton string button id
     */
    showMoreParams(div, lessButton) {
        div = "#" + div;
        let less = "#" + lessButton;
        jQuery(div).removeAttr("style");
        jQuery(less).css("display", "none");
    }

    /**
     * Shows less params of search filter when less is selected
     *
     * @param div string div name
     * @param moreButton string button id
     */
    showLessParams(div, moreButton) {
        div = "#" + div;
        let more = "#" + moreButton;
        jQuery(div).css("display", "none");
        jQuery(more).removeAttr("style");
    }

    /**
     * Initialize image slider for the listing details view
     *
     * @param params array handles all the settings for the slider
     */
    initSlider(params) {
        let sliderId = '#slider';
        let sliderContentId = '#slider-content';
        if (typeof params['sliderId'] !== 'undefined') {
            sliderId += '-' + params['sliderId'];
            sliderContentId += '-' + params['sliderId'];
        }

        let autoplay = false;
        if (typeof params['autoplay'] !== 'undefined') {
            autoplay = params['autoplay'];
        }

        let autoplaySpeed = 0;
        if (typeof params['autoplaySpeed'] !== 'undefined') {
            autoplaySpeed = params['autoplaySpeed'];
        }

        let nrVisibleItems = 0;
        if (typeof params['nrVisibleItems'] !== 'undefined') {
            nrVisibleItems = parseInt(params['nrVisibleItems']);
        }

        let nrVisibleItems1024 = 3;
        if (nrVisibleItems1024 > nrVisibleItems) {
            nrVisibleItems1024 = nrVisibleItems;
        }

        let nrVisibleItems600 = 2;
        if (nrVisibleItems600 > nrVisibleItems) {
            nrVisibleItems600 = nrVisibleItems;
        }

        let nrItemsToScrool = 0;
        if (typeof params['nrItemsToScrool'] !== 'undefined') {
            nrItemsToScrool = parseInt(params['nrItemsToScrool']);
        }

        let rtl = false;
        if (typeof params['rtl'] !== 'undefined') {
            rtl = params['rtl'];
        }

        let infinite = true;
        //if (typeof params['infinite'] !== 'undefined')
        //	infinite = params['infinite'];

        let centerMode = false;
        if (typeof params['centerMode'] !== 'undefined') {
            centerMode = params['centerMode'];
        }

        let variableWidth = false;
        if (typeof params['variableWidth'] !== 'undefined') {
            variableWidth = params['variableWidth'];
        }

        jQuery(sliderId).slick({
            dots: false,
            prevArrow: '<a class="controller-prev" href="javascript:;"><span><i class="la la-angle-left"></i></span></a>',
            nextArrow: '<a class="controller-next" href="javascript:;"><span><i class="la la-angle-right"></i></span></a>',
            customPaging: function (slider, i) {
                return '<a class="controller-dot" href="javascript:;"><span><i class="la la-circle"></i></span></a>';
            },
            autoplay: autoplay,
            autoplaySpeed: autoplaySpeed,
            speed: 300,
            slidesToShow: nrVisibleItems,
            slidesToScroll: nrItemsToScrool,
            infinite: infinite,
            centerMode: centerMode,
            variableWidth: variableWidth,
            rtl: rtl,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: nrVisibleItems1024,
                        slidesToScroll: nrVisibleItems1024,
                        infinite: true,
                        dots: false
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: nrVisibleItems600,
                        slidesToScroll: nrVisibleItems600
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }]
        });
    }

    /**
     * Shows add bookmark modal
     *
     * @param requiresLogin boolean if not logged in the log in popup will be shown
     * @param customId string modal id that will be open
     */
    showAddBookmark(requiresLogin, itemId, itemType) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            this.showBookmarkDialog(itemId, itemType);
        }
    }

    /**
     * Show the corresponding bookmark dialog based on the bookmark status
     *
     */
    showBookmarkDialog(itemId, itemType){
        let url = jbdUtils.getAjaxUrl('getBookmarkAjax', 'bookmark', 'bookmark');

        jQuery.ajax({
            type: "GET",
            data: {
                user_id:  jQuery('#update-bookmark #user_id').val(),
                item_id:  itemId,
                item_type:  itemType,
            },
            url: url,
            cache: false,
            success: function (response) {
                if( response.data.bookmark && response.data.bookmark.id > 0){
                    jQuery('#update-bookmark #item_id').val(itemId);
                    jQuery('#update-bookmark #item_type').val(itemType);
                    jQuery('#update-bookmark #note').val(response.data.bookmark.note);
                    jQuery('#update-bookmark').jbdModal();
                }else{
                    jQuery('#add-bookmark #item_id').val(itemId);
                    jQuery('#add-bookmark #item_type').val(itemType);
                    jQuery('#add-bookmark #note').val("");
                    jQuery('#add-bookmark').jbdModal();
                }
            }
        });

    }

    /**
     * Add bookmark item
     *
     * @returns {boolean}
     */
    addBookmark(){
        let url = jbdUtils.getAjaxUrl('addBookmarkAjax', 'bookmark', 'bookmark');

        jQuery.ajax({
            type: "GET",
            data: {
                item_id:  jQuery('#add-bookmark #item_id').val(),
                item_type:  jQuery('#add-bookmark #item_type').val(),
                note: jQuery('#add-bookmark #note').val()
            },
            url: url,
            cache: false,
            success: function (response) {
                let itemId = jQuery('#add-bookmark #item_id').val();
                jQuery("#bookmark-"+itemId +" i").toggleClass("la la-heart");
                jQuery("#bookmark-"+itemId +" i").toggleClass("la la-heart-o");
                jbdUtils.showMessage(response.data.response_message);
            }
        });
    }
    /**
     * Shows update bookmark modal
     *
     * @param requiresLogin boolean if not logged in the log in popup will be shown
     * @param customId string modal id that will be open
     */
    showUpdateBookmarkDialog(requiresLogin, itemId, itemType) {
        if (requiresLogin) {
            jbdUtils.showLoginNotice();
        } else {
            this.showBookmarkDialog(itemId, itemType);
        }
    }

    /**
     * Update bookmark item
     *
     * @returns {boolean}
     */
    updateBookmark(){
        let url = jbdUtils.getAjaxUrl('updateBookmarkAjax', 'bookmark', 'bookmark');

        jQuery.ajax({
            type: "GET",
            data: {
                user_id:  jQuery('#update-bookmark #user_id').val(),
                item_id:  jQuery('#update-bookmark #item_id').val(),
                item_type:  jQuery('#update-bookmark #item_type').val(),
                note: jQuery('#update-bookmark #note').val()
            },
            url: url,
            cache: false,
            success: function (response) {
                jbdUtils.showMessage(response.data.response_message);
            }
        });
    }

    /**
     * Removes bookmark
     *
     * @param type string type of bookmark
     */
    removeBookmark(type) {
        let url = jbdUtils.getAjaxUrl('removeBookmarkAjax', 'bookmark', 'bookmark');

        jQuery.ajax({
            type: "GET",
            data: {
                user_id:  jQuery('#update-bookmark #user_id').val(),
                item_id:  jQuery('#update-bookmark #item_id').val(),
                item_type:  jQuery('#update-bookmark #item_type').val()
            },
            url: url,
            cache: false,
            success: function (response) {
                let itemId = jQuery('#update-bookmark #item_id').val();
                jQuery("#bookmark-"+itemId +" i").toggleClass("la la-heart");
                jQuery("#bookmark-"+itemId +" i").toggleClass("la la-heart-o");
                jbdUtils.showMessage(response.data.response_message);
            }
        });
    }

    /**
     * Shows a dialog message
     *
     * @param message
     */
    showMessage(message){
        let html = '<div class="jbd-container" style="display:none"><div class="jmodal-sm"><div class="jmodal-header"><p class="jmodal-header-title">' + message +'</p>'
            +'<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>  </div></div></div>';

        jQuery(html).appendTo('body').jbdModal();
        setTimeout(function () {
            jQuery.jbdModal.close()
        }, 2000);
    }

    /**
     * Shows terms and condition modal
     */
    showTerms() {
        jQuery('#conditions').jbdModal();
    }

    /**
     * Shows Privacy policy modal
     */    
    showPolicy() {
        jQuery('#policy').jbdModal();
    }
    //************--End Front End Views--*****************//

    /**
     * Returns the base name from the path
     *
     * @param path string path of the file
     * @returns {T}
     */
    basename(path) {
        return path.split('/').reverse()[0];
    }

    /**
     * Used to submit the form on Manage Company Announcement. Add an Announcement
     */
    addDirAnnouncement() {
        jQuery("#id").val(0);
        jQuery("#task").val("managecompanyannouncement.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Used to submit the form on Manage Company Announcement. Removes an Announcement
     */
    deleteDirAnnouncement(id) {
        jQuery("#id").val(id);

        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_ANNOUNCEMENT_CONFIRM_DELETE'))) {
            jQuery("#id").val(id);
            jQuery("#task").val("managecompanyannouncements.deleteAnnouncement");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Used to submit the form on Manage Company Price List. Adds a Price List
     */
    addDirPriceList() {
        jQuery("#id").val(0);
        jQuery("#task").val("managecompanypricelist.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Used to submit the form on Manage Company Price List. Removes a Price List
     */
    deleteDirPriceList(id) {
        jQuery("#id").val(id);

        if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_PRICE_LIST_CONFIRM_DELETE'))) {
            jQuery("#id").val(id);
            jQuery("#task").val("managecompanypricelists.deletePriceList");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Used to submit the form on Manage Payment Processor. Adds a processor
     */
    addPaymentProcessor() {
        jQuery("#id").val(0);
        jQuery("#task").val("managepaymentprocessor.add");
        jQuery("#adminForm").submit();
    }

    /**
     * Used to submit the form on Manage Payment Processor. Removes a processor
     */
    deletePaymentProcessor(processorId) {
        if (confirm(JBD.JText._("COM_JBUSINESS_DIRECTORY_PAYMENT_PROCESSOR_CONFIRM_DELETE"))) {
            jQuery("#id").val(processorId);
            jQuery("#task").val("managepaymentprocessors.delete");
            jQuery("#adminForm").submit();
        }
    }

    /**
     * Called to handle ajax call for approving or publishing an item by ajax on list views.
     *
     * When this function is called it changes the value on the db and then by the response it get changes also the
     * color on the start of the row for that items.
     *
     * @param cid int id of the the row which have item
     * @param itemId int id of the item with the status that will be changed
     * @param task string task name
     */
    listItemTaskAjax(cid, itemId, task) {

        let iState = jQuery("#" + itemId).prop('checked');

        let cidArray = [];
        cidArray[0] = jQuery("#" + cid).val();

        let url = jbdUtils.getProperty('url') + '&task=' + task;
        jQuery.ajax({
            type: "GET",
            url: url,
            data: {
                cid: cidArray,
                id: cidArray[0],
                itemId: itemId,
                ajax: true,
                status: iState,
            },
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    jQuery("#" + itemId).prop('checked', !iState);
                    return;
                }

                if (!Array.isArray(data.cid)) {
                    let cida = [data.cid];
                    data.cid = cida;
                }

                if (task == 'categories.unpublish') {
                    for (let i = 0; i < data.cid.length; i++) {
                        jQuery("#jtoggle-state-" + data.cid[i]).prop('checked', false);
                        let text = document.getElementById('jtoggle-state-' + data.cid[i]).getAttribute('onclick');
                        let res = text.replace("unpublish", "publish");
                        document.getElementById('jtoggle-state-' + data.cid[i]).setAttribute('onclick', res);
                    }
                } else if (task == 'categories.publish') {
                    for (let i = 0; i < data.cid.length; i++) {
                        jQuery("#jtoggle-state-" + data.cid[i]).prop('checked', true);
                        let text = document.getElementById('jtoggle-state-' + data.cid[i]).getAttribute('onclick');
                        let res = text.replace("publish", "unpublish");
                        document.getElementById('jtoggle-state-' + data.cid[i]).setAttribute('onclick', res);
                    }
                }

                for (let i = 0; i < data.cid.length; i++) {
                    if (document.getElementById('jtoggle-state-' + data.cid[i]) && document.getElementById('jtoggle-approval-' + data.cid[i])) {
                        if (jQuery("#jtoggle-state-" + data.cid[i]).prop('checked') && jQuery("#jtoggle-approval-" + data.cid[i]).prop('checked')) {
                            jQuery("#item-status-" + data.cid[i]).removeClass("bg-danger");
                            jQuery("#item-status-" + data.cid[i]).addClass("bg-success");
                        } else {
                            jQuery("#item-status-" + data.cid[i]).addClass("bg-danger");
                            jQuery("#item-status-" + data.cid[i]).removeClass("bg-success");
                        }

                    } else if (document.getElementById('jtoggle-state-' + data.cid[i])) {
                        if (jQuery("#jtoggle-state-" + data.cid[i]).prop('checked')) {
                            jQuery("#item-status-" + data.cid[i]).removeClass("bg-danger");
                            jQuery("#item-status-" + data.cid[i]).addClass("bg-success");
                        } else {
                            jQuery("#item-status-" + data.cid[i]).addClass("bg-danger");
                            jQuery("#item-status-" + data.cid[i]).removeClass("bg-success");
                        }
                    }else if(document.getElementById('jtoggle-approval-' + data.cid[i])){
                        if (jQuery("#jtoggle-approval-" + data.cid[i]).prop('checked')) {
                            jQuery("#item-status-" + data.cid[i]).removeClass("bg-danger");
                            jQuery("#item-status-" + data.cid[i]).addClass("bg-success");
                        } else {
                            jQuery("#item-status-" + data.cid[i]).addClass("bg-danger");
                            jQuery("#item-status-" + data.cid[i]).removeClass("bg-success");
                        }
                    }
                }
            }
        });
    }

    /**
     * check if the string passed is empty
     *
     * @param str string text that will be checked
     * @returns {boolean}
     */
    isBlank(str) {
        return (!str || /^\s*$/.test(str));
    }

    /**
     * Check all options in a select
     *
     * @param elementId string id of the select box
     */
    checkAll(elementId) {
        jQuery('#'+elementId+' option').prop('selected', true);
        jbdUtils.updateChosenSelect('#'+elementId+' option');
    }

    /**
     * Unchecks all options of a jquery multiselect
     *
     * @param elementId string ID of the element
     */
    uncheckAll(elementId) {
        jQuery('#'+elementId+' option').prop('selected', false);
        jbdUtils.updateChosenSelect('#'+elementId+' option');
    }

    /**
     * Unchecks all categories for edit views
     *
     * @param mainCategoryId string ID of the mainCategory input
     * @param subCategoriesId string ID of the subCategories input
     */
    uncheckAllCategories(mainCategoryId = 'main_subcategory', subCategoriesId = 'categories') {
        let mainCatElement = '#'+mainCategoryId;
        jQuery(mainCatElement).empty();
        jbdUtils.updateChosenSelect(mainCatElement+' option');

        let subCatElement = '#'+subCategoriesId;
        jQuery(subCatElement+' option').prop('selected', false);
        jbdUtils.updateChosenSelect(subCatElement);

        //TODO do we still need this?
        if (jbdUtils.getProperty("enable_attribute_category") == 1) {
            resetAttributes();
        }
    }

    /**
     * Truncate text with javascript by the limit given to parameters
     *
     * @param text string text that will be truncated
     * @param id int id for the show more text for a particular text
     * @param limit int limit of chars that will be shown
     * @returns {string|*}
     */
    truncate(text, id, limit) {
        let truncatedText;

        if (id.length > 10) {
            id = id.slice(15, id.length);
        }

        if (text.length <= limit) {
            return text;
        } else if (text.length > limit) {
            truncatedText = text.slice(0, limit) + '<span>...</span>';
            truncatedText += '<a href="javascript:void(0)" onClick=\'jbdUtils.showFullText("' + id + '")\' class="more" id="more' + id + '">' + JBD.JText._("LNG_READ_MORE") + '</a>';
            truncatedText += '<span style="display:none;" id="more-text">' + text.slice(limit, text.length) + '</span>';

            return truncatedText;
        }
    }

    /**
     * Print a windows page based on the url and settings given.
     *
     * @param url string url that will be printed
     * @param params array settings for the function
     */
    printItem(url, params = null) {
        let width = '1050';
        let height = '700';
        let timeout = 2000;

        if (params != null) {
            if (typeof params['width'] !== 'undefined') {
                width = params['width'];
            }

            if (typeof params['height'] !== 'undefined') {
                height = params['height'];
            }

            if (typeof params['timeout'] !== 'undefined') {
                timeout = params['timeout'];
            }
        }

        let winref = window.open(url, 'windowName', 'width='+width+',height='+height);
        winref.window.focus();
        setTimeout(function () {
            winref.window.print();
        }, timeout);
    }

    /**
     * Show full text from the truncated one
     *
     * @param id string id of he elemnt with the hidden text
     */
    showFullText(id) {
        jQuery('#more' + id).next().show();
        jQuery('#more' + id).prev().remove();
        jQuery('#more' + id).remove();
    }

    /**
     * Change order of the items shown
     *
     * @param orderField int value of the order field as how they will be ordered on front
     */
    changeOrder(orderField) {
        jQuery("#orderBy").val(orderField);
        jQuery("#adminForm").submit();
    }

    /**
     * Change search Radius on search view
     *
     * @param radius double new radius which will be used to search
     */
    changeRadius(radius) {
        jQuery("#radius").val(radius);
        jQuery("#adminForm").submit();
    }

    /**
     * Compare versions of component installed and required.
     *
     * @param installed duoble version installed already
     * @param required double version that is up to date
     * @returns {boolean}
     */
    compareVersions (installed, required) {
        let a = installed.split('.');
        let b = required.split('.');

        for (let i = 0; i < a.length; ++i) {
            a[i] = Number(a[i]);
        }
        for (let i = 0; i < b.length; ++i) {
            b[i] = Number(b[i]);
        }
        if (a.length == 2) {
            a[2] = 0;
        }

        if (a[0] > b[0]) return true;
        if (a[0] < b[0]) return false;

        if (a[1] > b[1]) return true;
        if (a[1] < b[1]) return false;

        if (a[2] > b[2]) return true;
        if (a[2] < b[2]) return false;

        return true;
    }

    /**
     * Remove Offer selling field option
     *
     * @param index string index of the div that will be removed
     */
    deleteOfferSellingOption(index) {
        var count = jQuery('.selling-option-fields');

        if (count.length > 1) {
            jQuery('#' + index).remove();
        } else {
            jQuery('#customOptionsContent #' + index).find(':input').each(function() {
                jQuery(this).val('');
            });
        }
    }

    /**
     * Remove Payment Processor field option
     *
     * @param id string id of the div that will be removed
     */
    deleteProcessorOption(id) {
        var count = jQuery('.processor-fields');

        if (count.length > 1) {
            jQuery('#processor_field_' + id).remove();
        } else {
            var nameInput = jQuery('#processor_field_' + id + ' #column_name').find('input');
            nameInput.val('');
            nameInput.attr('type', 'text');
            nameInput.attr('placeholder', JBD.JText._("LNG_COLUMN_NAME"));
            jQuery('#processor_field_' + id + ' .hasTooltip').empty();

            var valueInput = jQuery('#processor_field_' + id + ' #column_value').find('input');
            valueInput.val('');
            valueInput.attr('placeholder', JBD.JText._("LNG_COLUMN_VALUE"));

            jQuery('#processor_field_' + id + ' #delete_processor_field_' + id).empty();
        }
    }

    /**
     * Adds a new Offer Selling option
     *
     * @param currentDivClass string class of the last div after which the new one will be added
     */
    addOfferSellingOption() {
        var lastElement = jQuery('#customOptionsContent div.selling-option-fields:last');
        var lastElementId = jQuery('#customOptionsContent div.selling-option-fields:last').attr('id');
        var lastIndex = lastElementId.split('-')[1];
        var newIdex = lastIndex + 1;

        var deleteButton = '';
        deleteButton += "<div id=\"delete_offer_selling_option\">\n" +
            "                <a href=\"javascript:void(0)\" class=\"btn btn-xs btn-danger btn-panel\" onclick=\"jbdUtils.deleteOfferSellingOption('options-"+newIdex+"')\">\n" +
            "                    <i class=\"la la-trash\"></i>\n" +
            "                </a>\n" +
            "            </div>";


        jQuery('#'+lastElementId).clone().prop('id', 'options-' + newIdex).insertAfter('#'+lastElementId);
        jQuery('#options-' + newIdex).find(':input').each(function() {
            if (this.type != 'hidden'){
                jQuery(this).val('');
            }
        });

        jQuery('#options-' + newIdex + ' .deleteButton').html(deleteButton);
    }

    /**
     * Adds a new Payment Processor field option
     *
     * @param id string id of the last div after which the new one will be added
     */
    addProcessorOption(id) {
        id = parseInt(id);
        var newId = id + 1;

        var deleteButton = '';
        deleteButton += '<a href="javascript:void(0)" class="btn btn-xs btn-danger btn-panel" onclick="jbdUtils.deleteProcessorOption(' + newId + ')">';
        deleteButton += '<i class="la la-trash"></i>';
        deleteButton += '</a>';

        var fields = jQuery('.processor-fields');
        var lastId = jQuery(fields[fields.length - 1]).attr('id').slice(-1);

        jQuery('#processor_field_'+lastId).clone().prop('id', 'processor_field_' + newId).insertAfter('#processor_field_' + lastId);
        jQuery('#processor_field_'+newId).addClass('new-field');
        var newNameField = jQuery('#processor_field_' + newId + ' #column_name').find('input');
        newNameField.attr('id', 'column_name_' + newId);
        newNameField.attr('type', 'text');
        newNameField.val('');
        newNameField.attr('placeholder', JBD.JText._("LNG_COLUMN_NAME"));

        jQuery('#processor_field_' + newId + ' .hasTooltip').remove();
        jQuery('#processor_field_' + newId + ' #column_name_'+lastId).find('button').attr('id', 'column_name_' + newId + '_img');
        var newValueField = jQuery('#processor_field_' + newId + ' #column_value').find('input');
        newValueField.attr('id', 'column_value_' + newId);
        newValueField.attr('placeholder', JBD.JText._("LNG_COLUMN_VALUE"));
        newValueField.val('');
        jQuery('#processor_field_' + newId + ' #column_value_'+lastId).find('button').attr('id', 'column_value_' + newId + '_img');
        jQuery('#processor_field_' + newId + ' #delete_processor_field_'+lastId).prop('id', 'delete_processor_field_' + newId);
        jQuery('#add_processor_field').find('a').attr('onclick', 'jbdUtils.addProcessorOption(\'' + newId + '\')');
        jQuery('#delete_processor_field_' + newId).html(deleteButton);

        jQuery('#add_processor_field' + parseInt(id)).remove();
    }

    /**
     * Resize iframe given by parameter to the size of content so it looses the scroll option
     *
     * @param iframe string frame that will be resized
     */
    resizeIframe(iframe) {
        iframe.height = iframe.contentWindow.document.body.scrollHeight + "px";
    }

    /**
     * Adds payment processor fields by selected type
     *
     * @returns {boolean}
     */
    addProcessorFieldsHTML(){
        let container = jQuery('#payment_processor_fields');
        let url = jbdUtils.getAjaxUrl('getPaymentProcessorFields', 'paymentprocessor', 'managepaymentprocessor');
        let processorType = jQuery('#type').find('option:selected').val();

        if (typeof processorType === 'undefined' || processorType.length == 0) {
            console.warn('Processor type should be defined first!');
            return false;
        }

        container.empty();

        jQuery.ajax({
            type: "GET",
            data: {
                processor_type: processorType
            },
            url: url,
            cache: false,
            success: function (data) {
                if (data.status == 1) {
                    container.append(data.data);
                } else {
                    console.warn(data.data);
                }
            }
        });

    }

    /**
     * Update the chosen selects after changes done on them
     *
     * @param item string the chosen select
     */
    updateChosenSelect(item){
        jQuery(item).trigger("liszt:updated");
        jQuery(item).trigger("chosen:updated");
    }

    /**
     * Render average rating on listing details view
     *
     * @param averageRating int rating that will be displayed (listing average rating)
     */
    renderReviewAverageRating(averageRating) {
        jQuery('.rating-average-review').rating({
            min: 0,
            max: 5,
            step: 0.5,
            stars: 5,
            size: 'sm',
            showCaption: false,
            rtl: false,
            displayOnly: true,
        });
        jQuery('.rating-average-review').rating('update', averageRating);
    }

    /**
     * Toggle the search filter
     */
    toggleFilter () {
        jQuery("#search-filter").toggleClass("open");
        jQuery("#search-filter").slideToggle(500);   
        if (jQuery('#search-filter').height()<10){
            jQuery(this).html(JBD.JText._('LNG_HIDE_FILTER'));
        }else{
            jQuery(this).html(JBD.JText._('LNG_SHOW_FILTER'));
        }
    }

}

jQuery.fn.center = function () {
    this.css("left", (jQuery(window).width() - this.width()) / 2 + jQuery(window).scrollLeft() + "px");
    return this;
};

//TODO change to ES6 and move to separate file
let JBDItemList = function () {
    this.element = null;
    this.status = true;
    this.delete = true;
    this.statusSelector = null;
    this.enabled = 1;
    this.disabled = 0;
    this.statusCallback = function () {
    };
    this.deleteCallback = function () {
    };
    this.deleteMsg = "Are you sure you want to delete this?";

    this.construct = function (element, params) {
        this.element = element;

        if (typeof params !== 'undefined') {
            if (typeof params['status'] !== 'undefined') {
                this.status = params['status'];
            }

            if (typeof params['delete'] !== 'undefined') {
                this.delete = params['delete'];
            }

            if (typeof params['statusSelector'] !== 'undefined') {
                this.statusSelector = params['statusSelector'];
            }

            if (typeof params['statusCallback'] !== 'undefined') {
                this.statusCallback = params['statusCallback'];
            }

            if (typeof params['deleteCallback'] !== 'undefined') {
                this.deleteCallback = params['deleteCallback'];
            }

            if (typeof params['enabled'] !== 'undefined') {
                this.enabled = params['enabled'];
            }

            if (typeof params['disabled'] !== 'undefined') {
                this.disabled = params['disabled'];
            }

            if (typeof params['deleteMsg'] !== 'undefined') {
                this.deleteMsg = params['deleteMsg'];
            }
        }

        this.init();
    };

    this.init = function () {
        var root = this.element;
        var list = root.find('.jbd-item-list');
        var items = list.find('.jbd-item');

        var self = this;
        items.each(function () {
            var actionDiv = jQuery(this).find('.jbd-item-actions');
            var id = self.getId(this);

            if (self.status) {
                var statusValue = self.getStatusValue(id);
                var statusBtn = self.getStatusButton(statusValue);

                if (!actionDiv.find('.jbd-item-status').length) {
                    actionDiv.append(statusBtn);
                    actionDiv.find('.jbd-item-status').click(function () {
                        self.changeStatus(this, id);
                    });
                }
            }

            if (self.delete) {
                var deleteBtn = self.getDeleteButton();

                if (!actionDiv.find('.jbd-item-delete').length) {
                    actionDiv.append(deleteBtn);
                    actionDiv.find('.jbd-item-delete').click(function () {
                        self.deleteItem(id)
                    });
                }
            }
        })
    };

    this.changeStatus = function (element, id) {
        var oldClass = 'la-check-circle';
        var newClass = 'la-ban';

        var oldVal = this.getStatusValue(id);
        if (oldVal == this.disabled) {
            oldClass = 'la-ban';
            newClass = 'la-check-circle';
        }

        jQuery(element).find('.la').removeClass(oldClass).addClass(newClass);

        this.statusCallback(id, oldVal);
    };

    this.getStatusButton = function (val) {
        var html = '';

        html += '<a href="javascript:void(0)" class="jbd-item-status">';
        if (val == this.enabled) {
            html += '  <i class="la la-check-circle"></i>';
        } else {
            html += '  <i class="la la-ban"></i>';
        }
        html += '</a>';

        return html;
    };

    this.deleteItem = function (id) {
        if (confirm(this.deleteMsg)) {
            jQuery('#jbd-item-' + id).remove();
        } else {
            return false;
        }

        this.deleteCallback(id);
    };

    this.getDeleteButton = function () {
        var html = '';
        html += '<a href="javascript:void(0)" class="jbd-item-delete">';
        html += '  <i class="la la-trash"></i>';
        html += '</a>';

        return html;
    };

    this.getId = function (element) {
        var item = jQuery(element).closest('.jbd-item').attr('id');

        return item.substr(9);
    };

    this.getStatusValue = function (id) {
        var statusElement;
        if (this.statusSelector.indexOf('#') > -1) {
            statusElement = jQuery(this.statusSelector + id);
        } else {
            statusElement = jQuery('#' + this.statusSelector + id);
        }

        return statusElement.val();
    };
};

jQuery.fn.jbdList = function (options) {
    let list = new JBDItemList();
    list.construct(this, options);
};

String.prototype.replaceAll = function (search, replacement) {
    let target = this;
    return target.split(search).join(replacement);
};

let jbdUtils = new JBDUtils();