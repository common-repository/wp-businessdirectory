JBDUtils = function () {
    this.baseUrl = null;
    this.imageRepo = null;
    this.imageBaseUrl = null;
    this.siteRoot = null;
    this.componentName = null;
    this.timeFormat = null;
    this.dateFormat = null;
    this.mapType = null;
    this.mapMarker = null;
    this.isProfile = null;
    this.isMultilingual = null;
    this.langTab = null;
    this.url = null;
    this.componentImagePath = null;
    this.enable_attribute_category = null;
    this.enable_packages = null;
    this.logo_width = null;
    this.logo_height = null;
    this.cover_width = null;
    this.cover_height = null;
    this.gallery_width = null;
    this.gallery_height = null;
    this.enable_crop = null;
    this.enable_resolution_check = null;
    this.limit_cities_regions = null;

    this.construct = function (settings) {
        var self = this;
        jQuery.each(settings, function (key, value) {
            self[key] = value;
        });
        var tmp = '';
        var separator = '?';
        if (this.isProfile == 0) {
            tmp = '/wp-admin/admin.php?page=jbd_businessdirectory';
            separator = '&';
        }
        this.url = this.baseUrl;
    };

    this.getDateWithFormat = function (date) {
        var format = this.dateFormat;
        var delimiter = '-';

        if (format.indexOf('/') > -1)
            delimiter = '/';
        else if (format.indexOf('\\') > -1)
            delimiter = '\\';

        var tmp = format.split(delimiter);

        var newDate = '';
        for (var i = 0; i < 3; i++) {
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
};

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
}

function registerAction(itemId, type, articleId) {
    var urlWebsiteCount = jbdUtils.url + '&task=companies.increaseStatisticCount&companyId=' + itemId + '&type=' + type + '&articleId=' + articleId;

    jQuery.ajax({
        type: 'GET',
        url: urlWebsiteCount,
        success: function () {
        }
    });
}

function increaseShareClicks(itemId, itemType) {
    var urlShareCount = jbdUtils.url + '&task=companies.increaseShareCount&itemId=' + itemId + '&itemType=' + itemType;

    jQuery.ajax({
        type: 'GET',
        url: urlShareCount,
        success: function () {
        }
    });
}

function addCoordinatesToUrl(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    var newURLString = window.location.href;
    newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
    newURLString += "geo-latitude=" + latitude;
    newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
    newURLString += "geo-longitude=" + longitude;

    window.location.href = newURLString;    // The page will redirect instantly
}

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value)
        + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

jQuery.fn.center = function () {
    this.css("left", (jQuery(window).width() - this.width()) / 2 + jQuery(window).scrollLeft() + "px");
    return this;
};

function renderRadioButtons() {
    //Turn radios into btn-group
    jQuery('.radio.btn-group label').addClass('btn');
    jQuery('.btn-group label:not(.active)').click(function () {
        var label = jQuery(this);
        var input = jQuery('#' + label.attr('for'));

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
var mapInstance;
var component_form;

function initializeAdminMap(params) {
    var locations = [];
    var location = {};
    location.latitude = params['map_latitude'];
    location.longitude = params['map_longitude'];
    locations.push(location);

    params["map_div"] = "company_map";
    jbdMap.construct(locations, params);
    if (typeof mapInstance === 'undefined') {
        mapInstance = jbdMap.loadMapScript();
    }

    if (typeof params['no_map'] == 'undefined') {
        mapInstance.addMapListener('click', function (location) {
            mapInstance.clearMarkers();
            mapInstance.addMarker(location, function () {
                document.getElementById("latitude").value = location.latitude;
                document.getElementById("longitude").value = location.longitude;
            });
        });
    }
}

function updateMap(params) {
    if (typeof mapInstance === 'undefined') {
        initializeAdminMap(params);
    } else {
        if (mapInstance.hasMap()) {
            mapInstance.clearMarkers();
            mapInstance.addMarker(params, mapInstance.moveToLocation(params));
        }
    }
}

function loadAdminMapScript(showMap, params) {
    if (showMap == 1) {
        initializeAdminMap(params);
    } else {
        var noMapParams = [];
        noMapParams['no_map'] = 1;
        initializeAdminMap(noMapParams);
    }
}

function initializeAutocomplete(preventSubmit, componentForm) {
    var element = document.getElementById('autocomplete');

    if (typeof mapInstance === 'undefined') {
        var noMapParams = [];
        noMapParams['no_map'] = 1;
        initializeAdminMap(noMapParams);
    }

    mapInstance.initAutocomplete(element, function (place) {
        fillInAddress(place);
    }, preventSubmit, true);

    component_form = componentForm;
}

function fillInAddress(place) {
    for (var component in component_form) {
        var obj = document.getElementById(component);
        if (typeof maybeObject != "undefined") {
            document.getElementById(component).value = "";
            document.getElementById(component).disabled = false;
        }
    }
    for (var j in place) {
        var att = j;
        if (component_form[att]) {
            var val = place[j];
            jQuery("#" + att).val(val);
            if (att == 'country') {
                jQuery('#country option').filter(function () {
                    return jQuery(this).text() === val;
                }).attr('selected', true);
                if (jbdUtils.limit_cities_regions) {
                    updateRegions();
                    updateCities();
                }
            }

            if (jbdUtils.limit_cities_regions) {
                updateRegions(function () {
                    if (att == 'administrative_area_level_1') {
                        jQuery('#activity_regions option').filter(function () {
                            return jQuery(this).text() === val;
                        }).attr('selected', true);
                        jQuery('#activity_regions').trigger("liszt:updated");
                        jQuery('#activity_regions').trigger("chosen:updated");
                    }

                    if (att == 'locality') {
                        updateCities(function () {
                            jQuery('#activity_cities option').filter(function () {
                                return jQuery(this).text() === val;
                            }).attr('selected', true);
                            jQuery('#activity_cities').trigger("liszt:updated");
                            jQuery('#activity_cities').trigger("chosen:updated");
                        });
                    }
                });
            }
        }
    }
}

function validateAppSettingsForm(validateNonVisible, validateRichText,formName) {
    formName = formName || "item-form";
    var validationResult = false;

    jQuery("#"+formName).validationEngine('attach', {
        validateNonVisibleFields: validateNonVisible,
    });

    validationResult = jQuery("#"+formName).validationEngine('validate');

    if (!validationResult) {
        showSettingsValidationError(formName);
    }

    return validationResult;
}

function validateCmpForm(validateNonVisible, validateRichText, customRuleHook) {
    if (typeof customRuleHook !== "undefined") {
        customRuleHook();
    }

    if (validateRichText) {
        validateRichTextEditors();
    }

    validateMultiSelects();
    validateSingleSelects();
    var isError = jQuery("#item-form").validationEngine('validate', {validateNonVisibleFields: validateNonVisible});
    return !isError;
}

function showValidationError(formName) {
    formName = formName || "item-form";
    jQuery("#validation-error").show(500);

    setTimeout(function () {
        jQuery("#validation-error").hide(700);
    }, 5000);

    setTimeout(function () {
        for (var i = 1; i <= 6; i++) {
            if (jQuery("#edit-tab" + i + " div.formError").length) {
                console.log("show tab " + i);
                openTab(i);
                break;
            }
        }
        setTimeout(function () {
            jQuery('#'+formName).validationEngine('updatePromptsPosition');
        }, 500);
    }, 1500);
}

function showSettingsValidationError(formName) {
    formName = formName || "item-form";
    jQuery("#validation-error").show(500);

    setTimeout(function () {
        jQuery("#validation-error").hide(700);
    }, 5000);
    var numItems = jQuery('.focus_validate').length;
    setTimeout(function () {
        for (var i = 1; i <= numItems; i++) {
            if (jQuery(".panel_" + i + "_content div.formError").length) {
                console.log("show tab " + i);
                openSettingsTab(i);
                break;
            }
        }
        setTimeout(function () {
            jQuery('#'+formName).validationEngine('updatePromptsPosition');
        }, 500);
    }, 1500);
}

function openSettingsTab(tab) {
    jQuery("#adminForm").validationEngine('detach');
    var validationResult = jQuery("#adminForm").validationEngine('validate');

    if (!validationResult) {
        return;
    }

    jQuery('.panel_'+tab).click();
}

function validateTabs(validateNonVisible, validateRichText) {
    var validationResult = false;
    if (jbdUtils.isMultilingual) {
        jQuery(".tab-" + jbdUtils.defaultLang).each(function () {
            jQuery(this).click();
        });
    }

    if (validateRichText) {
        validateRichTextEditors();
    }

    validateMultiSelects();
    jQuery("#item-form").validationEngine('attach', {
        validateNonVisibleFields: validateNonVisible,
    });

    validationResult = jQuery("#item-form").validationEngine('validate');

    if (!validationResult) {
        showValidationError();
    }

    return validationResult;
}

function validateRichTextEditors() {
    var lang = '';
    if (jbdUtils.isMultilingual) {
        lang += '_' + jbdUtils.langTab;
        jQuery(".tab-" + jbdUtils.defaultLang).each(function () {
            jQuery(this).click();
        });

        jQuery(".tab_description_" + jbdUtils.defaultLang).click();
    }

    jQuery(".editor").each(function () {
        var textarea = jQuery(this).find('textarea');
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

function showItem(link) {
    var win = window.open(link, '_blank');
    win.focus();
}

function applyReadMore() {
    var showChar = 70;  // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Show more";
    var lesstext = "Show less";

    jQuery('.read-more').each(function () {
        var content = jQuery(this).html();

        if (content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="morelink" >' + moretext + '</a></span>';

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

function addVideo() {
    var count = jQuery("#video-container").children().length + 1;
    id = 0;
    var outerDiv = document.createElement('div');
    outerDiv.setAttribute('class', 'video-item');
    outerDiv.setAttribute('id', 'detailBox' + count);

    var newLabel = document.createElement('label');
    newLabel.setAttribute("for", id);
    newLabel.innerHTML = JBD.JText._('LNG_VIDEO');

    var cointainerDiv = document.createElement('div');
    cointainerDiv.setAttribute('class', 'input-group');
    
    var newInput = document.createElement('input');
    newInput.setAttribute('name', 'videos[]');
    newInput.setAttribute('id', id);
    newInput.setAttribute('class', 'form-control');

    var appendDiv = document.createElement('div');
    appendDiv.setAttribute('class', 'input-group-append');
    
    var newButton = document.createElement('button');
    newButton.setAttribute('class', 'input-group-text btn-secondary');
    newButton.setAttribute('onclick', 'removeRow("detailBox' + count + '");checkNumberOfVideos();');

    var newIcon = document.createElement('i');

    newIcon.setAttribute('class', 'la la-lg la-remove');
   
    newButton.appendChild(newIcon);
    appendDiv.appendChild(newButton);
    
    cointainerDiv.appendChild(newInput);
    cointainerDiv.appendChild(appendDiv);
    
    outerDiv.appendChild(newLabel);
    outerDiv.appendChild(cointainerDiv);
    

    var facilityContainer = jQuery("#video-container");
    facilityContainer.append(outerDiv);
    
    checkNumberOfVideos();
}

function removeRow(id) {
    jQuery('#' + id).remove();
    checkNumberOfVideos();
    checkNumberOfSounds();
}

function checkNumberOfVideos() {
    var nrVideos = jQuery('input[name*="videos[]"]').length;

    if (nrVideos < maxVideos) {
        jQuery("#add-video").show();
    }
    else {
        jQuery("#add-video").hide();
    }
}

function addSound() {
    var count = jQuery("#sound-container").children().length + 1;
    id = 0;
    var outerDiv = document.createElement('div');
    outerDiv.setAttribute('class', 'detail_box');
    outerDiv.setAttribute('id', 'soundDetailBox' + count);

    var newLabel = document.createElement('label');
    newLabel.setAttribute("for", id);
    newLabel.innerHTML = JBD.JText._('LNG_SOUND');

    var newInput = document.createElement('textarea');
    newInput.setAttribute('name', 'sounds[]');
    newInput.setAttribute('id', id);
    newInput.setAttribute('class', 'input_txt');
    newInput.setAttribute('rows', '3');

    var img_del = document.createElement('img');
    img_del.setAttribute('src', jbdUtils.imageRepo + "/assets/images/del_icon.png");
    img_del.setAttribute('alt', 'Delete option');
    img_del.setAttribute('height', '12px');
    img_del.setAttribute('width', '12px');
    img_del.setAttribute('align', 'left');
    img_del.setAttribute('onclick', 'removeRow("soundDetailBox' + count + '")');
    img_del.setAttribute('style', "cursor: pointer; margin:3px;");

    var clearDiv = document.createElement('div');
    clearDiv.setAttribute('class', 'clear');

    outerDiv.appendChild(newLabel);
    outerDiv.appendChild(newInput);
    outerDiv.appendChild(img_del);
    outerDiv.appendChild(clearDiv);

    var facilityContainer = jQuery("#sound-container");
    facilityContainer.append(outerDiv);

    checkNumberOfSounds();
}

function checkNumberOfSounds() {
    var nrVideos = jQuery('textarea[name*="sounds[]"]').length;
    if (nrVideos < 15) {
        jQuery("#add-sound").show();
    }
    else {
        jQuery("#add-sound").hide();
    }
}

function calculateLenght() {
    var obj = jQuery("#description");

    var max = parseInt(obj.attr('maxlength'));
    if (obj.val().length > max) {
        obj.val(obj.val().substr(0, obj.attr('maxlength')));
    }

    jQuery("#descriptionCounter").val((max - obj.val().length));
}

function calculateLenghtShort() {
    var obj = jQuery("#short_description");

    var max = parseInt(obj.attr('maxlength'));
    if (obj.val().length > max) {
        obj.val(obj.val().substr(0, obj.attr('maxlength')));
    }

    jQuery("#descriptionCounterShort").val((max - obj.val().length));
}

var cropper;

function showCropper(dataUri, type, picId) {
    if (typeof cropper !== 'undefined')
        cropper.destroy();
    cropped = false;

    if (picId === undefined || picId === null)
        picId = '';

    
    jQuery('#cropper-modal').jbdModal();
    
    jQuery('#cropper-image').attr('src', '');
    jQuery('#cropper-image').attr('src', dataUri);
    jQuery('#save-cropped').attr('onclick', 'saveCropped("' + type + '", "' + picId + '")');

    var width;
    var height;
    if (type.length == 0) {
        removeLogo();
        width = jbdUtils.logo_width;
        height = jbdUtils.logo_height;
    }
    else if (type === 'cover-') {
        width = jbdUtils.cover_width;
        height = jbdUtils.cover_height;
        removeCoverImage();
    }
    else if (type === 'service-') {
        width = jbdUtils.gallery_width;
        height = jbdUtils.gallery_height;
        removeServiceLogo(picId);
    }
    else {
        width = jbdUtils.gallery_width;
        height = jbdUtils.gallery_height;
    }

    var image = document.getElementById('cropper-image');
    cropper = new Cropper(image, {
        aspectRatio: width / height,
        cropBoxResizable: false,
        dragMode: 'move',
        scalable: true,
        crop: function (e) {
        },
        ready: function () {
            cropper.setCropBoxData({left: 0, top: 0, width: parseInt(width), height: parseInt(height)});
        }
    });
}

function saveCropped(type, picId) {
    cropper.getCroppedCanvas().toBlob(function (blob) {
        var formData = new FormData();
        blob['name'] = 'cropped.' + blob['type'].substr(blob['type'].indexOf('/') + 1, blob.type.length);
        formData.append('croppedimage', blob);

        var submitPath = '';
        if (type.length == 0)
            submitPath = companyFolderPath;
        else if (type === 'cover-')
            submitPath = companyFolderPathCover;
        else
            submitPath = companyFolderPathGallery;

        submitPath += '&crop=1';
        jQuery.ajax(submitPath, {
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (xml) {
                jQuery(xml).find("picture").each(function () {
                    if (jQuery(this).attr("error") == 0) {
                        setUpImage(
                            companyFolder + jQuery(this).attr("path"),
                            jQuery(this).attr("name"),
                            type,
                            picId
                        );
                        jQuery("#remove-image-loading").remove();
                    }
                    else if (jQuery(this).attr("error") == 1)
                        alert(JBD.JText._('LNG_FILE_ALLREADY_ADDED'));
                    else if (jQuery(this).attr("error") == 2)
                        alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                    else if (jQuery(this).attr("error") == 3)
                        alert(JBD.JText._('LNG_ERROR_GD_LIBRARY'));
                    else if (jQuery(this).attr("error") == 4)
                        alert(JBD.JText._('LNG_ERROR_RESIZING_FILE'));
                });

                jQuery.jbdModal.close();
            },
            error: function () {
                console.log('Upload error');
            }
        });
    },"image/jpeg", 0.8);
}

function validateMultiSelects() {
    var chosenSelector = '.chzn-container-multi';
    if (!jQuery(chosenSelector).length) {
        chosenSelector = '.chosen-container-multi';
    }

    jQuery(chosenSelector).each(function () {
        var id = jQuery(this).attr('id');
        var selectId = id.substr(0, id.lastIndexOf("_"));

        if (jQuery('#' + selectId).hasClass('validate[required]') || jQuery('#' + id).hasClass('validate[required]')) {
            var values = jQuery('#' + selectId).chosen().val();
            if (typeof values === 'undefined' || values == null || values.length == 0) {
                if (!jQuery('#' + id).hasClass('validate[required]'))
                    jQuery('#' + id).addClass('validate[required]');
                jQuery('#' + selectId).removeClass('validate[required]');
            }
            else {
                jQuery('#' + id).removeClass('validate[required]');
                jQuery('#' + selectId).addClass('validate[required]');
            }
        }

    });
}

function validateSingleSelects() {
    var chosenSelector = '.chzn-container-single';
    if (!jQuery(chosenSelector).length) {
        chosenSelector = '.chosen-container-single';
    }

    jQuery(chosenSelector).each(function () {
        var id = jQuery(this).attr('id');
        var selectId = id.substr(0, id.lastIndexOf("_"));

        if (jQuery('#' + selectId).hasClass('validate[required]') || jQuery('#' + id).hasClass('validate[required]')) {
            var values = jQuery('#' + selectId).chosen().val();
            if (typeof values === 'undefined' || values == null || values.length == 0) {
                if (!jQuery('#' + id).hasClass('validate[required]'))
                    jQuery('#' + id).addClass('validate[required]');
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
function saveForm(formId) {
    var isError = true;
    jQuery('#' + formId).validationEngine('detach');

    isError = jQuery('#' + formId).validationEngine('validate');
    jQuery('#' + formId).validationEngine('attach');
    if (!isError)
        return;

    jQuery("#"+formId + " button").each(function(){
    	jQuery(this).attr("disabled","disabled");
    });
    
    document.getElementById(formId).submit();
}

function showLoginNotice() {
	jQuery('#login-notice').jbdModal();
}

function showMap(display) {
    if (!jQuery("#map-link").hasClass("active")) {
        jQuery("#companies-map-container").show();
        jbdMap.loadMapScript();
        jQuery("#map-link").addClass("active");
    } else {
        jQuery("#companies-map-container").hide();
        jQuery("#map-link").removeClass("active");
    }

    if (typeof mapInstance === 'undefined') {
        mapInstance = jbdMap.loadMapScript();
    }
    mapInstance.initialize();
}

function loadMapCallback() {
    jbdMap.loadMapScriptCallback();
}

function initializeNoMapAutocomplete(token, preventSubmit, limitedCountries) {
    jbdMap.construct([], {
        no_map: true
    });
    var map_instance = jbdMap.loadMapScript();

    var element = document.getElementsByClassName('zipcode-' + token)[0];
    map_instance.initAutocomplete(element, function (place) {
        jQuery("#geo-latitude").val(place['latitude']);
        jQuery("#geo-longitude").val(place['longitude']);
        jQuery("#location-detected").val("1");
    }, preventSubmit, false, limitedCountries);
}

function saveSelectedCategory(categorySet, categId) {
    var catId;
    var checked = jQuery("#search-filter input[type='checkbox']:checked");
    catId = checked.attr('id');

    if (categorySet) {
        catId = categId;
    }

    jQuery("#adminForm #categoryId").val(catId);
    jQuery("#adminForm input[name=limitstart]").val(0);
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

function removeSearchRule(type) {
    if (type == "province")
        jQuery("#adminForm #province-search").val("");
    if (type == "type")
        jQuery("#adminForm #type-search").val("");
    if (type == "zipcode") {
        jQuery("#adminForm #zipcode").val("");
        jQuery("#adminForm #radius").val("");
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

function removeAttrCond(attributeId) {
    jQuery("input[name=attribute_" + attributeId + "]").remove();
    jQuery("#attribute_" + attributeId).val("");
    jQuery("#adminForm").submit();
}

function addFilterRule(type, id, categorySet, categId) {
    var val = type + '=' + id + ';';
    if (jQuery("#selectedParams").val().length > 0) {
        jQuery("#selectedParams").val(jQuery("#selectedParams").val() + val);
    } else {
        jQuery("#selectedParams").val(val);
    }

    if (categorySet) {
        jQuery("#filter_active").val("1");
    }

    jQuery("#adminForm input[name=limitstart]").val(0);
    saveSelectedCategory(categorySet, categId);
    jQuery("#adminForm").submit();
}

function removeFilterRule(type, id, categorySet, categId) {
    var val = type + '=' + id + ';';
    var str = jQuery("#selectedParams").val();
    jQuery("#selectedParams").val((str.replace(val, "")));
    jQuery("#filter_active").val("1");
    saveSelectedCategory(categorySet, categId);

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

function resetFilters(resetCategories, categorySet, categId) {
    jQuery("#selectedParams").val("");
    if (resetCategories)
        jQuery("#categories-filter").val("");
    else
        saveSelectedCategory(categorySet, categId);
    jQuery("#adminForm #categoryId").val("");

    jQuery("#adminForm #searchkeyword").val("");
    jQuery("#adminForm #zipcode").val("");
    jQuery("#adminForm #city-search").val("");
    jQuery("#adminForm #region-search").val("");
    jQuery("#adminForm #province-search").val("");
    jQuery("#adminForm #minprice-search").val("");
    jQuery("#adminForm #maxprice-search").val("");
    jQuery("#adminForm #country-search").val("");
    jQuery("#adminForm #type-search").val("");
    jQuery("#adminForm #radius").val("");
    jQuery("#adminForm #startDate").val("");
    jQuery("#adminForm #endDate").val("");
    jQuery("#adminForm #filter-by-fav").val("");
    jQuery("#adminForm #resetSearch").val("1");

    jQuery("#adminForm").submit();
}

function checkMoreFilterRule(val) {
    var selected = jQuery('input[name=' + val + ']:checked').length;

    if (selected > 0) {
        addMoreFilter(val);
    } else {
        removeMoreFilter(val);
    }
}

function addMoreFilter(val) {
    val = val + ';';
    if (jQuery("#moreParams").val().length > 0) {
        jQuery("#moreParams").val(jQuery("#moreParams").val() + val);
    } else {
        jQuery("#moreParams").val(val);
    }

    jQuery("#adminForm input[name=limitstart]").val(0);
}

function removeMoreFilter(val) {
    val = val + ';';
    var str = jQuery("#moreParams").val();
    jQuery("#moreParams").val((str.replace(val, "")));
}

function resetMoreFilter() {
    jQuery("#moreParams").val("");
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

function initSlider(params) {
    var sliderId = '#slider';
    var sliderContentId = '#slider-content';
    if (typeof params['sliderId'] !== 'undefined') {
        sliderId += '-' + params['sliderId'];
        sliderContentId += '-' + params['sliderId'];
    }

    var autoplay = false;
    if (typeof params['autoplay'] !== 'undefined')
        autoplay = params['autoplay'];

    var autoplaySpeed = 0;
    if (typeof params['autoplaySpeed'] !== 'undefined')
        autoplaySpeed = params['autoplaySpeed'];

    var nrVisibleItems = 0;
    if (typeof params['nrVisibleItems'] !== 'undefined')
        nrVisibleItems = parseInt(params['nrVisibleItems']);

    var nrVisibleItems1024 = 3;
    if (nrVisibleItems1024 > nrVisibleItems)
        nrVisibleItems1024 = nrVisibleItems;
    var nrVisibleItems600 = 2;
    if (nrVisibleItems600 > nrVisibleItems)
        nrVisibleItems600 = nrVisibleItems;

    var nrItemsToScrool = 0;
    if (typeof params['nrItemsToScrool'] !== 'undefined')
        nrItemsToScrool = parseInt(params['nrItemsToScrool']);

    var rtl = false;
    if (typeof params['rtl'] !== 'undefined')
        rtl = params['rtl'];

    var centerMode = false;
    if (typeof params['centerMode'] !== 'undefined')
    	centerMode = params['centerMode'];
    
    var variableWidth = false;
    if (typeof params['variableWidth'] !== 'undefined')
    	variableWidth = params['variableWidth'];


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
        infinite: true,
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

//************--End Front End Views--*****************//

function addBookmark(requiresLogin, customId) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        var id = 'add-bookmark';
        if (typeof customId != 'undefined')
            id = customId;

        jQuery('#' + id).jbdModal();
    }
}

function showUpdateBookmarkDialog(requiresLogin, customId) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        var id = 'update-bookmark';
        if (typeof customId != 'undefined')
            id = customId;

        jQuery('#' + id).jbdModal();
    }
}

function removeBookmark(type) {
    jQuery("#updateBookmarkFrm #task").val(type + ".removeBookmark");
    jQuery("#updateBookmarkFrm").submit();
}

function showTerms() {
	jQuery('#conditions').jbdModal();
}

//************--End Front End Views--*****************//

JBDItemList = function() {
    this.element = null;
    this.status = true;
    this.delete = true;
    this.statusSelector = null;
    this.enabled = 1;
    this.disabled = 0;
    this.statusCallback = function() {};
    this.deleteCallback = function() {};
    this.deleteMsg = "Are you sure you want to delete this?";

    this.construct = function(element, params) {
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

    this.init = function() {
        var root = this.element;
        var list = root.find('.jbd-item-list');
        var items = list.find('.jbd-item');

        var self = this;
        items.each(function() {
            var actionDiv = jQuery(this).find('.jbd-item-actions');
            var id = self.getId(this);

            if (self.status) {
                var statusValue = self.getStatusValue(id);
                var statusBtn = self.getStatusButton(statusValue);

                if (!actionDiv.find('.jbd-item-status').length) {
                    actionDiv.append(statusBtn);
                    actionDiv.find('.jbd-item-status').click(function() {
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

    this.changeStatus = function(element, id) {
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

    this.getStatusButton = function(val) {
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

    this.deleteItem = function(id) {
        if(confirm(this.deleteMsg)) {
            jQuery('#jbd-item-'+id).remove();
        } else {
            return false;
        }

        this.deleteCallback(id);
    };

    this.getDeleteButton = function() {
        var html = '';
        html += '<a href="javascript:void(0)" class="jbd-item-delete">';
        html += '  <i class="la la-trash"></i>';
        html += '</a>';

        return html;
    };

    this.getId = function(element) {
        var item = jQuery(element).closest('.jbd-item').attr('id');

        return item.substr(9);
    };

    this.getStatusValue = function(id) {
        var statusElement;
        if (this.statusSelector.indexOf('#') > -1) {
            statusElement = jQuery(this.statusSelector+id);
        } else {
            statusElement = jQuery('#'+this.statusSelector+id);
        }

        return statusElement.val();
    };
};

jQuery.fn.jbdList = function(options) {
    var list = new JBDItemList();
    list.construct(this, options);
};

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

function basename(path) {
    return path.split('/').reverse()[0];
}

function addDirAnnouncement(){
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyannouncement.add");
    jQuery("#adminForm").submit();
}

function deleteDirAnnouncement(id) {
    jQuery("#id").val(id);

    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_ANNOUNCEMENT_CONFIRM_DELETE'))) {
        jQuery("#id").val(id);
        jQuery("#task").val("managecompanyannouncements.deleteAnnouncement");
        jQuery("#adminForm").submit();
    }
}


function addDirPriceList(){
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanypricelist.add");
    jQuery("#adminForm").submit();
}

function deleteDirPriceList(id) {
    jQuery("#id").val(id);

    if (confirm(JBD.JText._('COM_JBUSINESS_DIRECTORY_PRICE_LIST_CONFIRM_DELETE'))) {
        jQuery("#id").val(id);
        jQuery("#task").val("managecompanypricelists.deletePriceList");
        jQuery("#adminForm").submit();
    }
}

function listItemTaskAjax(cid, itemId, task){

	iState = jQuery("#"+itemId).prop('checked');
	
 	cidArray = [];
 	cidArray[0] = jQuery("#"+cid).val();
 	
	var url = jbdUtils.url + '&task=' + task;
	jQuery.ajax({
        type: 'GET',
        url: url,
        data: {
        	cid:cidArray,
        	id:cidArray[0],
            itemId: itemId,
            ajax: true,
            status: iState,
        },
        dataType: 'json',
        success: function (data) {
            if(data.error){
                jQuery("#"+itemId).prop('checked', !iState);
                return;
            }

            if(!Array.isArray(data.cid)){
            	var cida = [data.cid]; 
            	data.cid = cida;
            }
            
            if (task == 'categories.unpublish'){
                for (var i=0;i < data.cid.length; i++){
                    jQuery("#jtoggle-state-"+data.cid[i]).prop('checked',false);
                    var text = document.getElementById('jtoggle-state-'+data.cid[i]).getAttribute('onclick');
                    var res = text.replace("unpublish", "publish");
                    document.getElementById('jtoggle-state-'+data.cid[i]).setAttribute('onclick',res);
                }
            }else if(task == 'categories.publish') {
                for (var i=0;i < data.cid.length; i++){
                    jQuery("#jtoggle-state-"+data.cid[i]).prop('checked',true);
                    var text = document.getElementById('jtoggle-state-'+data.cid[i]).getAttribute('onclick');
                    var res = text.replace("publish", "unpublish");
                    document.getElementById('jtoggle-state-'+data.cid[i]).setAttribute('onclick',res);
                }
            }

            for (var i=0;i < data.cid.length; i++) {
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
                }
            }
        }
    });
}

function setupNav(){
	if (jQuery("#jdb-wrapper").width() < 769 && jQuery("#jdb-wrapper").width() > 480  ) {
		jQuery('#jdb-wrapper').addClass('mini-navbar')
	} else {
		jQuery('#jdb-wrapper').removeClass('mini-navbar')
	} 
}

function SmoothlyMenu() {
	if (!jQuery('#side-menu').hasClass('mini-navbar') || jQuery('body').hasClass('body-small')) {
	    // Hide menu in order to smoothly turn on when maximize menu
	    jQuery('#side-menu').hide();
	    // For smoothly turn on menu
	    setTimeout(
	        function () {
	            jQuery('#side-menu').fadeIn(500);
	        }, 100);
	} else {
	    // Remove all inline style from jquery fadeIn function to reset menu state
	    jQuery('#side-menu').removeAttr('style');
	}
}

function selectUser(name,id){
	jQuery("#jform_created_by_id").val(id);
	jQuery("#jform_created_by").val(name);
	jQuery.jbdModal.close();	
}