<?php
if (isset($isProfile)) {
    echo JBusinessUtil::getMapGDPRModal();
}

JBusinessUtil::loadUploadScript(true, true, true, true);

$map_latitude = $this->appSettings->map_latitude;
if ((empty($map_latitude)) || (!is_numeric($map_latitude)))
    $map_latitude = 0;

$map_longitude = $this->appSettings->map_longitude;
if ((empty($map_longitude)) || (!is_numeric($map_longitude)))
    $map_longitude = 0;

$map_zoom = $this->appSettings->map_zoom;
if ((empty($map_zoom)) || (!is_numeric($map_zoom)))
    $map_zoom = 10;

$latitude = !empty($this->item->latitude) ? $this->item->latitude : $map_latitude;
$longitude = !empty($this->item->longitude) ? $this->item->longitude : $map_longitude;

$initparams =  array();
$initparams['map_latitude'] = $latitude;
$initparams['map_longitude'] = $longitude;
$initparams['map_zoom'] = $map_zoom;
$initparams['longitude'] = $this->item->longitude;
$initparams['latitude'] = $this->item->latitude;
$initparams['is_admin'] = true;
$initparams["map_height"] = "300px";
if ($this->appSettings->map_type == MAP_TYPE_BING) {
    $initparams["key"] = $mapKey;
}

$initLocation = new stdClass();
$initLocation->longitude = $longitude;
$initLocation->latitude = $latitude;
$initLocations = array($initLocation);
?>

<script type="text/javascript">
    var maxPictures = '<?php echo isset($this->item->package) ? $this->item->package->max_pictures : $this->appSettings->max_pictures ?>';
    var maxVideos = <?php echo isset($this->item->package) ? $this->item->package->max_videos : $this->appSettings->max_video ?>;
    var maxSounds = <?php echo isset($this->item->package)?$this->item->package->max_sounds :$this->appSettings->max_sound ?>;
    var maxAttachments = '<?php echo (isset($this->item->package) &&  isset($this->item->package->max_attachments)) ? $this->item->package->max_attachments : $this->appSettings->max_attachments ?>';
    var maxZipcodes = '<?php echo (isset($this->item->package) &&  isset($this->item->package->max_zipcodes)) ? $this->item->package->max_zipcodes : 0 ?>';
    var maxLocations = '<?php echo (isset($this->item->package) &&  isset($this->item->package->max_locations)) ? $this->item->package->max_locations : 100 ?>';

    var companyFolder = '<?php echo COMPANY_PICTURES_PATH . ($this->item->id) . "/" ?>';
    var companyFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now") ?>&picture_type=<?php echo PICTURE_TYPE_LOGO ?>&_path_type=1&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") ?>&croppable=1';
    var companyFolderPathCover = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now") ?>&picture_type=<?php echo PICTURE_TYPE_COMPANY ?>&_path_type=1&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") ?>&croppable=1';
    var companyFolderPathAd = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now") ?>&picture_type=<?php echo PICTURE_TYPE_LOGO ?>&_path_type=1&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") ?>&croppable=1';
    var companyFolderPathGallery = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now") ?>&picture_type=<?php echo PICTURE_TYPE_GALLERY ?>&_path_type=1&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") ?>&croppable=1';
    var companyAttachFolderPath = '<?php echo JBusinessUtil::getUploadUrl('uploadFile') ?>&t=<?php echo strtotime("now") ?>&_path_type=4&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ((int)$this->item->id) . "/") ?>';
    var removePath = '<?php echo JBusinessUtil::getUploadUrl('remove') ?>&_path_type=2&_filename=';

    var closeLocationOnupdate = false;

    var maxActivityCities = 0<?php //echo $maxActivityCities?>;
    var maxActivityRegions = 0<?php //echo $maxActivityRegions ?>;
    var maxActivityCountries = 0<?php //echo $maxActivityCountries ?>;

    var jbdAdminMapInstance;
    var uploadInstance;
    <?php $loadMap = (!isset($editSimple) && (!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP, $this->item->package->features)) && $attributeConfig["map"] != ATTRIBUTE_NOT_SHOW); ?>

    //if form is basic we should not validate hidden fields
    let validateHidden = <?php echo $showSteps ? 'false' : 'true' ?>;


    function initializeMap() {
            jbdAdminMapInstance = JBDMapHelper.loadMapScript(
                <?php echo json_encode($initLocations) ?>,
                <?php echo json_encode($initparams) ?>
            );

            jbdUtils.addAdminMapClickAction();
    }

    window.addEventListener('load', function() {

        uploadInstance = JBDUploadHelper.getUploadInstance({
            'maxPictures': maxPictures,
            'maxVideos': maxVideos,
            'maxSounds': maxSounds,
            'maxAttachments': maxAttachments,
            'removePath': removePath
        });

        var approved = '<?php echo $this->item->approved; ?>';
        if (approved == 1) {
            jQuery("#disapprovalBox").show();
        } else {
            jQuery("#disapprovalBox").hide();
        }

        if (jQuery("#descriptionCounter").val()){
            jQuery("#descriptionCounter").val(parseInt(jQuery("#description").attr('maxlength')) - jQuery("#description").val().length);
        }
        
        <?php if (!$this->appSettings->enable_multilingual == 1) { ?>
            if (jQuery("#descriptionCounterShort").length){
                jbdUtils.calculateLengthShort();
            }
            if (jQuery("#descriptionCounterSlogan").length){
                jbdUtils.calculateLengthSlogan();
            }
        <?php } ?>

        <?php if(!empty($this->containerCategories)){ ?>
            var containerCats = ["<?php echo implode("\",\"",$this->containerCategories);?>"];
            jQuery("#selectedSubcategories  > option").each(function(){
                if(containerCats.includes(jQuery(this).val())){
                    jQuery(this).addClass("primary result-selected");
                    jQuery(this).attr("disabled","disabled");
                }
            });
        <?php } ?>

        jQuery('select#selectedSubcategories').on('change', function() {
            var selected = jQuery('#mainSubcategory option:selected').val();
            jQuery('select#mainSubcategory').find('option').remove();
            jQuery('select#selectedSubcategories option:selected').each(function() {
                if (jQuery(this).length) {
                    var selCategoryOption = jQuery(this).clone();
                    selCategoryOption.removeAttr('selected');
                    jQuery('select#mainSubcategory').append(selCategoryOption);
                    if (selCategoryOption.val() === selected) {
                        jQuery('select#mainSubcategory').find('option').attr('selected', 'selected');
                    }
                    jbdUtils.updateChosenSelect('select#mainSubcategory');
                }
            });

            var catId = jQuery('#mainSubcategory option:selected').val();
            if (catId === 0 || typeof catId === "undefined") {
                catId = -1;
            }

            <?php if ($this->appSettings->enable_attribute_category) { ?>
                jbdListings.updateAttributes(catId, '<?php echo $this->item->id ?>');
            <?php } ?>
        });

        jQuery('#pictures-list').jbdList({
            statusCallback: uploadInstance.changePictureStatus,
            deleteCallback: uploadInstance.deletePicture,
            statusSelector: 'picture_enable_',
            deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE', true) ?>"
        });

        jQuery('#attachment-list').jbdList({
            statusCallback: uploadInstance.changeAttachmentStatus,
            deleteCallback: uploadInstance.deleteAttachment,
            statusSelector: 'attachment_status_',
            deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_ATTACHMENT', true) ?>"
        });

        uploadInstance.checkNumberOfPictures();
        jQuery("#sortable, #extra").sortable();
        jQuery("#sortable-attachment").sortable();
        jQuery("#sortable-attachment").disableSelection();
        jQuery("#sortable, #extra").disableSelection();

        uploadInstance.imageUploaderDropzone(
            "#file-upload",
            '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now") ?>&picture_type=<?php echo PICTURE_TYPE_GALLERY ?>&_path_type=1&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") ?>', ".fileinput-button", "<?php echo JText::_('LNG_DRAG_N_DROP', true); ?>",
            companyFolder,
            <?php echo $allowedNr ?>,
            "addPicture"
        );
        uploadInstance.imageUploaderDropzone(
            "#image-upload",
            '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now") ?>&picture_type=<?php echo PICTURE_TYPE_GALLERY ?>&_path_type=1&_target=<?php echo urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") ?>', ".fileinput-btn", "<?php echo JText::_('LNG_DRAG_N_DROP', true); ?>",
            companyFolder,
            <?php echo $allowedNr ?>,
            "addExtraPicture"
        );

        jQuery('#extra-pictures-list').jbdList({
            statusCallback: uploadInstance.changeExtraPictureStatus,
            deleteCallback: uploadInstance.deleteExtraPicture,
            statusSelector: 'image_enable_',
            deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE', true) ?>"
        });
        jQuery("#imageLocation").val('<?php echo $this->item->logoLocation ?>');

        jbdListings.setLocationUrl(
            "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=company&tmpl=component&layout=locations&id=' . $this->item->id, false); ?>",
            "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompany&tmpl=component&layout=locations&id=' . $this->item->id, false); ?>",
            "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=company.deleteLocation', false); ?>",
            "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.deleteLocation', false); ?>"
        );

        <?php if (!empty($this->company->id)) { ?>
            jQuery(".process-tab").each(function() {
                jQuery(this).addClass("editable");
            });
        <?php } ?>


        <?php if ($lockAttributes) { ?>
            jQuery("#customFieldsContent input").each(function() {
                jQuery(this).prop("disabled", true);
            });

            jQuery("#customFieldsContent select").each(function() {
                jQuery(this).prop("disabled", true);
            });

            jQuery("#customFieldsContent textarea").each(function() {
                jQuery(this).prop("disabled", true);
            });

        <?php } ?>

        jQuery('.bootstrap-tagsinput input').on('keypress', function(e) {
            if (e.keyCode == 13) {
                e.keyCode = 188;
                e.preventDefault();
            }
        });

        jQuery("#opening_status").change(function() {
            if (jQuery(this).val() == 0) {
                jQuery(".business-schedule").slideDown(500);
            } else {
                jQuery(".business-schedule").slideUp(500);
            }
        });

        uploadInstance.imageUploader(companyFolder, companyFolderPath);
        uploadInstance.imageUploader(companyFolder, companyFolderPathCover, 'cover-');
        uploadInstance.imageUploader(companyFolder, companyFolderPathAd, 'ad-');
        uploadInstance.multiFileUploader(companyFolder, companyAttachFolderPath);
        uploadInstance.btn_removefile();
        uploadInstance.btn_removefile_at();

        uploadInstance.checkNumberOfVideos();
        uploadInstance.checkNumberOfPictures();
        uploadInstance.checkNumberOfSounds();

        var totalMembers = '<?php echo count($this->item->members) ?>';
        var countMember = 0;
        for (countMember = 0; countMember < totalMembers; countMember++) {
            var memPicId = countMember + 1;
            uploadInstance.imageUploader(companyFolder, companyFolderPathGallery, 'member-', memPicId);
        }
    });


    var maxCategories = <?php echo isset($this->item->package) ? $this->item->package->max_categories : $this->appSettings->max_categories ?>;
    var maxMemberships = <?php echo MAX_MEMBERSHIPS ?>

    window.addEventListener('load', function(evt) {

        jQuery("#item-form").validationEngine('attach');

        jbdTabs.setPresentTabs(<?php echo json_encode($presentTabs) ?>);

        jQuery(".chosen-select").chosen({
            width: "95%",
            disable_search_threshold: 5,
            search_contains: true,
            placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>",
            placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"
        });
        jQuery(".chosen-select-categories").chosen({
            width: "95%",
            max_selected_options: maxCategories,
            search_contains: true
        });
        jQuery(".chosen-select-membership").chosen({
            width: "95%",
            max_selected_options: maxMemberships,
            search_contains: true
        });

        jQuery(".ajax-chosen-select").chosen({
            width: "95%",
            disable_search_threshold: 5,
            placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>",
            placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"
        });
        //		jQuery(".fieldset-business_hours").click(function(){
        //			jQuery(this).toggleClass("open");
        //		});
        jQuery(".fieldset-business_hours > .field").click(function(event) {
            event.stopPropagation();
        });
        jQuery('.timepicker').timepicker({
            'timeFormat': jbdUtils.getProperty("timeFormat"),
            'minTime': '6:00am',
        });

        //		jQuery('select#selectedSubcategories').on('change', function() {
        //			mainCat = jQuery('#mainSubcategory').val();
        //			jQuery('select#mainSubcategory').find('option').remove();
        //			jQuery('select#selectedSubcategories option:selected').each(function () {
        //				if (jQuery(this).length) {
        //					var selCategoryOption = jQuery(this).clone();
        //                  jbdUtils.updateChosenSelect('select#mainSubcategory');
        //				}
        //			});
        //
        //			if(mainCat){
        //				jQuery('#mainSubcategory').val(mainCat);
        //			}else{
        //				updateAttributes(jQuery('#mainSubcategory').val());
        //			}
        //		});

        <?php if ($loadMap) { ?>
            initializeMap();
        <?php } ?>

        <?php if ($showLocation && $attributeConfig["address_autocomplete"] != ATTRIBUTE_NOT_SHOW) { ?>
            jbdUtils.initializeAdminAutocomplete(true);
        <?php } ?>

        <?php if ($showTabs && $this->appSettings->edit_form_mode != 3) { ?>
            jbdTabs.showEditTab(1);
        <?php } ?>

        <?php if ($attributeConfig["description"] == ATTRIBUTE_MANDATORY) { ?>
            jbdUtils.setProperty("validateRichTextEditors", true);
        <?php } ?>

        <?php if (!isset($isProfile)) { ?>
            jQuery(".form-control").removeClass("validate[required]");
            jQuery(".form-control").removeClass("validate[required,custom[email]]");
            jbdUtils.setProperty("validateRichTextEditors", false);
        <?php } ?>

        jQuery('#userModal_jform_created_by').on('show', function() {
            var modalBodyHeight = jQuery(window).height() - 147;
            jQuery('.modal-body').css('max-height', modalBodyHeight);
            jQuery('body').addClass('modal-open');
        }).on('hide', function() {
            jQuery('body').removeClass('modal-open');
        });

        if (document.getElementById('related-listings')) {

            if (jbdUtils.getProperty("isProfile") != 1) {
                var isProfile = false;
                var userId = 0;
            } else {
                var userId = '<?php echo $user->ID ?>';
            }

            let urlGetCompanies = jbdUtils.getAjaxUrl('getCompaniesByStringAjax', 'companies', 'managecompanies');

            jQuery("#related-listings").ajaxChosen({
                    type: 'GET',
                    url: urlGetCompanies,
                    data: {
                        isProfile: isProfile,
                        userId: userId
                    },
                    dataType: 'json',
                    cache: false,
                },
                function(data) {
                    var results = [];
                    jQuery.each(data, function(i, val) {
                        results.push({
                            value: val.id,
                            text: val.name
                        });
                    });
                    return results;
                }, {
                    // CHOSEN OPTIONS
                    disable_search_threshold: -1
                }
            );
        }

        jQuery('#add-zip-field').on('click', function() {
            let id = Math.random();
            let html = jQuery('#zipTemplate').innerHTML;
            jQuery('#zip-box-container').append(html);

            let zipCodeFields = document.getElementById('zip-autocomplete-' + id);

            JBDMapHelper.initAutocomplete(zipCodeFields, function(data) {
                document.getElementById('zip_input_' + id).value = data.postal_code
                document.getElementById('lat_input_' + id).value = data.latitude
                document.getElementById('long_input_' + id).value = data.longitude
            }, false, false, false);
        })

        if (maxZipcodes > 0 && jQuery("div[id='zip-box-container']").children().length >= maxZipcodes) {
            jQuery('#add-zip-field').hide()
        }

        //TODO change to mutation
        jQuery("div[id='zip-box-container']").bind('DOMSubtreeModified', function() {
            if (maxZipcodes > 0 && jQuery("div[id='zip-box-container']").children().length >= maxZipcodes) {
                jQuery('#add-zip-field').hide()
            } else {
                jQuery('#add-zip-field').show()
            }
        })

        if (jQuery("div[id='company-locations']").children().length >= maxLocations) {
            jQuery('#add-location').hide()
        }

        const targetNode = document.getElementById("company-locations");
        const config = {
            childList: true,
            subtree: true
        };

        const callbackDom = function(mutationsList, observer) {
            for (let mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    if (jQuery("div[id='company-locations']").children().length >= maxLocations) {
                        jQuery('#add-location').hide()
                    } else {
                        jQuery('#add-location').show()

                    }
                }
            }
        };

        const observer = new MutationObserver(callbackDom);
        if(targetNode){
            observer.observe(targetNode, config);
        }

        <?php if ($this->appSettings->limit_cities_regions == 1) { ?>
            //updateRegions("#country","#activity_regions","#activity_cities");
            //updateCities("#activity_regions","#activity_cities");
        <?php } ?>


        <?php //$leafIds = JBusinessUtil::getLeafs($this->categoryOptions) 
        ?>

        <?php if (!empty($leafIds)) { ?>
            let leafs = [<?php echo implode(",", $leafIds) ?>];
            jQuery("#selectedSubcategories option").each(function() {
                if (!leafs.includes(parseInt(jQuery(this).val()))) {
                    jQuery(this).attr("disabled", "disabled");
                }
            });
            jbdUtils.updateChosenSelect('#selectedSubcategories')
        <?php } ?>

        initializeCharCount();
     
        jQuery('#tab_group_description').on('click', function() {
            initializeCharCount();
        });


      function initializeCharCount() {
        var lng = "";
        var lngUnderscore = ""
        <?php if ($this->appSettings->enable_multilingual == 1) { ?>
            lng = getLanguageTag();
            lngUnderscore = lng.replace("-","_");
        <?php } ?>

        var maxchar = <?php echo $maxDescriptionLength ?>;
        var iframe = document.getElementById('description'+ lngUnderscore +'_ifr');
        if(iframe) {
            var innerDoc = (iframe.contentDocument) ? iframe.contentDocument : iframe.contentWindow.document;
            var c = document.getElementById("count" + lng);

            var nrChars = jbdUtils.getEditorStats('description' + lngUnderscore).chars;
            c.innerHTML = maxchar - nrChars;
            checkCount(evt);

            innerDoc.addEventListener("keydown",checkCount);
            innerDoc.addEventListener('paste', handlePaste);

        }
      }

        jQuery("#activity_radius").change(function(){
            let val = jQuery(this).val();
            let index = <?php echo $this->appSettings->metric==1?1.6:0.621371 ?>;
            val = jbdUtils.convertMetric(val, index);
            jQuery("#converted-value").html(val);
        });

        jQuery("#activity_radius").trigger("change");

        jQuery("#activity-container #activity_countries").on('change', function() {
            jQuery(this).find('option').not(':selected').remove();
            if(jQuery("#activity-container #activity_countries option").length < maxActivityCountries){
                jQuery("#activity-container .js-add-country").attr("disabled", false);
            }
            jbdUtils.updateChosenSelect("#activity-container #activity_countries");
        });

        jQuery("#activity-container #activity_regions").on('change', function() {
            jQuery(this).find('option').not(':selected').remove();
            if(jQuery("#activity-container #activity_regions option").length < maxActivityRegions){
                jQuery("#activity-container .js-add-region").attr("disabled",false);
            }
            jbdUtils.updateChosenSelect("#activity-container #activity_regions");
        });

        jQuery("#activity-container select#activity_cities").on('change', function() {
            jQuery(this).find('option').not(':selected').remove();
            if(jQuery("#activity-container #activity_cities option").length < maxActivityCities){
                jQuery("#activity-container .js-add-city").attr("disabled",false);
            }
            jbdUtils.updateChosenSelect("#activity-container #activity_cities");
        });

        //check the maximum number of countries
        if(jQuery("#activity-container #activity_countries option").length >= maxActivityCountries){
                jQuery("#activity-container .js-add-country").attr("disabled", true);
        }

        //check the maximum number of regions
        if(jQuery("#activity-container #activity_regions option").length >= maxActivityRegions){
            jQuery("#activity-container .js-add-region").attr("disabled",true);
        }

        //check the maximum number of cities
        if(jQuery("#activity-container #activity_cities option").length >= maxActivityCities){
            jQuery("#activity-container .js-add-city").attr("disabled",true);
        }

        jQuery(".section-header").on('click', function(){
            let tab = jQuery(this).attr("data-tab");
            
            if(jQuery("#tab" + tab).hasClass("active")){
                let modified = jQuery("#field_modified").val();
                if(modified == 1){
                    if(confirm("Do you want to close the section withouth saving the changes?")){
                        jbdTabs.openTab(tab);
                    }
                }else{
                    jbdTabs.openTab(tab);
                }
            }else{
                jQuery("#field_modified").val(0);
                jbdTabs.openTab(tab);
            }
        });
    });

    function resetHours() {
        jQuery('.work-hours-input').val('');
        var i;
        for (i = 1; i <= 7; i++) {
            jQuery('#work_status_check_' + i).removeAttr('checked');
            jbdListings.disableWorkPeriod(i, true, false);
        }
    }

    <?php if ($this->appSettings->listing_auto_save && !empty($user->ID) && !empty($this->item->id)) { ?>
        var autosaveOn = false;

        function listingAutoSave() {

            <?php if (isset($isProfile)) { ?>
                jQuery("#item-form #task").val("managecompany.listingautosave");
            <?php } else { ?>
                jQuery("#item-form #task").val("company.listingautosave");
            <?php } ?>
            autosaveOn = false;

            var itemName = "";
            jQuery('#item-form .form-item-name').each(function() {
                var value = jQuery(this).val();
                if (value.length > 0) {
                    itemName = value;
                }
            });

            var d = new Date();
            var timezone = d.getTimezoneOffset() / 60;
            jQuery("#item-form #user-timezone").val(timezone);

            if (!autosaveOn && itemName.length != 0) {
                autosaveOn = true;
                jQuery.ajax({
                    type: 'GET',
                    url: jbdUtils.getProperty("baseUrl"),
                    data: jQuery("#item-form").serialize(),
                    dataType: 'json',
                    success: function(response) {
                        jQuery("#last_saved").html(response.time);
                        jQuery("#item-form #company_id").val(response.company_id);
                    }
                });
            }
        }

        setInterval(listingAutoSave, <?php echo !empty($this->appSettings->auto_save_interval) ? $this->appSettings->auto_save_interval : "12000" ?>);
    <?php } ?>

    <?php if ($this->appSettings->limit_cities_regions == 1) { ?>
        var showCountryAttr = <?php echo $attributeConfig["country"] != ATTRIBUTE_NOT_SHOW ? 'true' : 'false'; ?>

        function updateCities(regionsId, citiesId, callback) {
            var regionIds = jQuery(regionsId).val();

            var dataSelected = [];
            jQuery(citiesId).find('option:selected').each(function() {
                dataSelected.push(jQuery(this).val());
            });

            if (regionIds == null) {
                jQuery(citiesId).empty();
                jbdUtils.updateChosenSelect(citiesId);
                return;
            }
            let url = jbdUtils.getAjaxUrl('getCitiesByRegionsAjax', 'cities', 'managecities');
            jQuery.ajax({
                type: 'GET',
                url: url,
                data: {
                    regionParam: regionIds
                },
                cache:false,
                dataType: 'json',
                success: function(data) {
                    var selectedVar;
                    jQuery(citiesId).empty();

                    var tmp = [];
                    tmp.id = "";
                    tmp.name = '<?php echo JText::_('LNG_SELECT_CITY') ?>';
                    data.unshift(tmp);

                    jQuery.each(data, function(i, val) {
                        selectedVar = "";
                        if (jQuery.inArray(val.id, dataSelected) != '-1') {
                            selectedVar = "selected";
                        }
                        jQuery(citiesId).append('<option ' + selectedVar + ' value="' + val.id + '">' + val.name + '</option>');
                    });
                    jbdUtils.updateChosenSelect(citiesId);

                    if (typeof callback !== "undefined") {
                        callback();
                    }
                }
            });
        }

        function updateRegions(countriesId, regionsId, citiesId, callback) {
            var countryId = jQuery(countriesId).val();

            
            var dataSelected = [];
            jQuery(regionsId).find('option:selected').each(function() {
                dataSelected.push(jQuery(this).val());
            });

            if (showCountryAttr && countryId == null) {
                jQuery(regionsId).empty();
                jbdUtils.updateChosenSelect('#regions');
                return;
            }

            let url = jbdUtils.getAjaxUrl('getRegionsByCountryAjax', 'regions', 'manageregions');
            jQuery.ajax({
                type: 'GET',
                url: url,
                data: {
                    countryId: countryId
                },
                dataType: 'json',
                cache:false,
                success: function(data) {
                    var selectedVar;
					jQuery('#administrative_area_level_1').empty();
					jQuery('#administrative_area_level_1').append('<option value=""><?php echo JText::_('LNG_SELECT_REGION') ?></option>');
					jQuery.each(data, function (i, val) {
						selectedVar = "";
						if (jQuery.inArray(val.name, dataSelected) != '-1' && dataSelected.length > 0) {
							selectedVar = "selected";
						}
						jQuery('#administrative_area_level_1').append('<option ' + selectedVar + ' value="' + val.name + '">' + val.name + '</option>');
					});

					if (jQuery('#administrative_area_level_1').val().length > 0) {
						updateCities();
					} else {
						updateCities(function(){
							jQuery('#locality').empty();
							jQuery('#locality').append('<option value=""><?php echo JText::_('LNG_SELECT_CITY') ?></option>');
						});
					}

					if (typeof callback !== "undefined") {
						callback();
					}
                }
            });
        }


       

        function addActivityCountry(){
            let text = jQuery("#activity-container #activity-country option:selected").text();
            let value = jQuery("#activity-container #activity-country option:selected").val();
            
            if(value.length > 0 && jQuery("#activity-container #activity_countries option[value='"+value+"']").length == 0){
                jQuery("#activity-container #activity_countries").append(new Option(text, value));
            }

            jQuery("#activity-container #activity_countries option").prop('selected', true);
            jbdUtils.updateChosenSelect("#activity-container #activity_countries");
            
            if(jQuery("#activity-container #activity_countries option").length>= maxActivityCountries){
                jQuery("#activity-container .js-add-country").attr("disabled", true);
            }
        }

        function addActivityRegion(){
            let text = jQuery("#activity-container #regions option:selected").text();
            let value = jQuery("#activity-container #regions option:selected").val();

            if(value.length > 0 && jQuery("#activity-container #activity_regions option[value='"+value+"']").length == 0){
                jQuery("#activity-container #activity_regions").append(new Option(text, value));
            }

            jQuery("#activity-container #activity_regions option").prop('selected', true);
            jbdUtils.updateChosenSelect("#activity-container #activity_regions");

            if(jQuery("#activity-container #activity_regions option").length >= maxActivityRegions){
                jQuery("#activity-container .js-add-region").attr("disabled",true);
            }
        }

        function addActivityCity(){
            let text = jQuery("#activity-container #cities option:selected").text();
            let value = jQuery("#activity-container #cities option:selected").val();

            if(value.length > 0 && jQuery("#activity-container #activity_cities option[value='"+value+"']").length == 0){
                jQuery("#activity-container #activity_cities").append(new Option(text, value));
            }

            jQuery("#activity-container #activity_cities option").prop('selected', true);
            jbdUtils.updateChosenSelect("#activity-container #activity_cities");

            if(jQuery("#activity-container #activity_cities option").length>= maxActivityCities){
                jQuery("#activity-container .js-add-city").attr("disabled", true);
            }
        }
    <?php } ?>

    jQuery(".button-cancel").click(function() {
        jQuery(this).addClass("loader")
    });

    function saveCompanyInformation(task) {

        let validateHidden = true;

        let activeClass =  jQuery("#form-mode-container").attr('class');
        if(activeClass == "basic"){
            validateHidden = false;
        }

        if (!jbdUtils.validateTabs(validateHidden, <?php echo $attributeConfig["description"] && isset($isProfile) == ATTRIBUTE_MANDATORY ? 'true' : 'false' ?>, task))
            return false;

        //&& empty($this->item->id)
        <?php if (isset($isProfile)  && $this->appSettings->allow_contribute == 1 && $this->item->id == 0) { ?>
            jQuery("#item-form #task").val(task);
            jQuery('#business-owner-dlg').jbdModal();
            jQuery("#business-owner-dlg").on('click', '.owner-confirm-btn', function() {
                jQuery(this).addClass("loader")
            })
        <?php } else { ?>
            saveListing(task);
        <?php } ?>
    }

    function switchFormView(type) {
        if (type == "basic") {
            jQuery("#form-mode-container").removeClass("advanced");
            jQuery("#form-mode-container").addClass("basic");

            <?php if ($showTabs) { ?>
                jQuery("#prev-btn").hide();
                jQuery("#next-btn").hide();
                jQuery("#save-btn").show();
                jQuery(".jbd-terms-conditions").show();
                jQuery("#privacy_policy").show();
                jQuery(".edit-tab").each(function () {
                    jQuery(this).hide();
                });

                jQuery("#edit-tab1").show();
            <?php } ?>

        } else {
            jQuery("#form-mode-container").addClass("advanced");
            jQuery("#form-mode-container").removeClass("basic");

            <?php if ($showTabs) { ?>
                jbdTabs.showEditTab(1);
            <?php } ?>
        }
    }

    function resetOwner() {
        jQuery("#item-form #contribute_editor").val(jQuery("#item-form #userId").val());
        jQuery("#item-form #userId").val("0");
        jQuery("#item-form #only_contribute").val("1");

        var task = jQuery("#item-form #task").val();

        saveListing(task);
    }

    function saveListing(task) {
        jQuery("#item-form #task").val('managecompany.' + task);
        var form = document.adminForm;
        form.submit();
    }

    function cancel() {
        if (confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_COUNTRIES_CONFIRM_CANCEL', true);?>'))
		{
            jQuery("#item-form #task").val('managecompany.cancel');
            var form = document.adminForm;
            form.submit();
		}else{
            setInterval(function(){
                jQuery(".button-cancel").removeClass("loader");
            }
            ,500);
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
    
    function getLanguageTag() {
        if((jQuery('#tab_group_description').find('dt.open').attr('class')) !==undefined){
            value = jQuery('#tab_group_description').find('dt.open').attr('class').split(/\s+/)[1].split('tabdesc')[1].replace(/^\-/, "_");
        } else {
            value = jQuery('#tab_group_description').find('[aria-expanded=true]').attr('aria-controls').split('tabdesc')[1].replace(/^\-/, "_");
        }
        return value;
      }

    function checkCount(e) {
        var maxchar = <?php echo $maxDescriptionLength ?>;
        var lng = "";
        var lngUnderscore = "";
        <?php if ($this->appSettings->enable_multilingual == 1) { ?>
            lng = getLanguageTag();
            lngUnderscore = lng.replace("-","_");
        <?php } ?>
        
        var counter = document.getElementById("count" + lng);
        var nrChars = jbdUtils.getEditorStats('description' + lngUnderscore).chars;
        var remaining = maxchar - nrChars;
        
        if(remaining < 0){
            remaining = 0;
        }
        counter.innerHTML = remaining;

        if (nrChars >= maxchar) {
            jQuery("#count-warning" + lng).text('<?php echo JText::_("LNG_DESCRIPTION_LIMIT_EXCEEDED")?>');
            if (e.keyCode != 8 || e.keyCode == 32) {
                e.preventDefault();
            }
        } else {
            jQuery("#count-warning" + lng).text('')
        }
    }

    function handlePaste(event) {
        var clipboardData, pastedData;
        var lng = ""
        <?php if ($this->appSettings->enable_multilingual == 1) { ?>
            lng = getLanguageTag();
        <?php } ?>

        // Stop data actually being pasted into div
        event.stopPropagation();
        event.preventDefault();
        // Get pasted data via clipboard API
        clipboardData = event.clipboardData || window.clipboardData;
        pastedData = clipboardData.getData('Text');
        var c = document.getElementById("count" + lng);
        let remaining = parseInt(c.innerHTML);
        if( remaining <0 ){
            remaining = 0;
        }
        if (pastedData.length > remaining) {
            pastedData = pastedData.substring(0, remaining);
            event.target.textContent = pastedData;
            event.target.innerText = pastedData;
            alert('Pasted text contains more characters than allowed limit!')
            event.preventDefault();
        }

        checkCount(event)
    }

    function selectUser(name,id){
        jQuery("#jform_created_by_id").val(id);
        jQuery("#jform_created_by").val(name);
        jQuery.jbdModal.close();	
    }
</script>