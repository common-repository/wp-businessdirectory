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
JHtml::addIncludePath(BD_HELPERS_PATH.'html');
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';

// Load the tooltip behavior.

$attributeConfig = $this->item->defaultAtrributes;
$enablePackages = $this->appSettings->enable_packages;
$identifier = JFactory::getApplication()->input->get('identifier');
JBusinessUtil::loadBaseScripts();
$mapKey  = JBusinessUtil::loadMapScripts();
$this->location->countries = $this->item->countries;
?>

<style>
    html.wp-toolbar {
        padding: 0;
        background: #fff;
    }
</style>
<script type="text/javascript">
	window.addEventListener('load', function() {
		jQuery("body").html(jQuery("#jbd-container").parent().html());
        jQuery(".page-loader").hide();
	});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container location-container" >
    <div class="page-loader">
        <div class="loader"></div>
    </div>
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&tmpl=component&layout=locations'); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
    	<div class="">
            <div class="button-row">
                <button type="button" class="btn btn-success" onclick="saveLocation();">
                        <span class="ui-button-text"><?php echo JText::_("LNG_SAVE")?></span>
                </button>
                <button type="button" class="btn btn-success" onclick="saveLocation(true);">
                    <span class="ui-button-text"><?php echo JText::_("LNG_SAVE_AND_CLOSE")?></span>
                </button>
                <button type="button" class="btn btn-danger" onclick="parent.jQuery.jbdModal.close();">
                        <span class="ui-button-text"><?php echo JText::_("LNG_CLOSE")?></span>
                </button>
            </div>

            <fieldset class="pr-3 py-3">
                <p><?php echo JText::_('LNG_COMPANY_DETAILS_TXT');?></p>
                <p><?php echo JText::_('LNG_ADDRESS_SUGESTION');?></p>
                
                <div class="form-box">
                    <div>                       
                        <div class="form-group">
                            <label for="name"><?php echo JText::_('LNG_LOCATION_NAME')?></label>
                            <input type="text" name="name" id="name" class="input_txt form-control text-input" value="<?php echo $this->location->name ?>">
                        </div>

                        <?php FormService::renderAddressFields($attributeConfig, $this->location); ?>

                        <?php if($attributeConfig["phone"]!=ATTRIBUTE_NOT_SHOW) { ?>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-group">
                                        <label for="phone"><?php echo JText::_('LNG_TELEPHONE')?><?php echo JBusinessUtil::showMandatory($attributeConfig["phone"])?></label>
                                        <input type="text"	name="phone" id="phone" class="input_txt form-control <?php echo $attributeConfig["phone"] == ATTRIBUTE_MANDATORY?"validate[required]":""?> text-input"
                                            value="<?php echo $this->location->phone ?>">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($attributeConfig["radius"] != ATTRIBUTE_NOT_SHOW) { ?>
                            <div class="row">
                                <div class="col-md">
                                    <div>
                                        <label for="district_id"><?php echo JText::_('LNG_ACTIVITY_RADIUS') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["radius"]) ?> </label>
                                        <p class="small"><?php echo JText::_('LNG_ACTIVITY_RADIUS_INFO') ?></p>
                                        <div class="activity-radius-container">
                                            <input class="form-control <?php echo $attributeConfig["radius"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="activity_radius" id="sec_activity_radius" value="<?php echo $this->location->activity_radius ?>" />
                                            (<?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?>) -&nbsp;<span id="sec-converted-value"></span>&nbsp;(<?php echo $this->appSettings->metric!=1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?>) 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if(!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP,$this->item->package->features)){ ?>
                            <?php if($attributeConfig["map"]!=ATTRIBUTE_NOT_SHOW){?>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="latitude"><?php echo JText::_('LNG_LATITUDE')?><?php echo JBusinessUtil::showMandatory($attributeConfig["map"])?></label>
                                            <p class="small"><?php echo JText::_('LNG_MAP_INFO')?></p>
                                            <input class="input_txt form-control <?php echo $attributeConfig["map"] == ATTRIBUTE_MANDATORY?"validate[required]":""?>" type="text" name="latitude" id="latitude" value="<?php echo $this->location->latitude ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="longitude"><?php echo JText::_('LNG_LONGITUDE')?><?php echo JBusinessUtil::showMandatory($attributeConfig["map"])?></label>
                                            <p class="small"><?php echo JText::_('LNG_MAP_INFO')?></p>
                                            <input class="input_txt form-control <?php echo $attributeConfig["map"] == ATTRIBUTE_MANDATORY?"validate[required]":""?>" type="text" name="longitude" id="longitude" value="<?php echo $this->location->longitude ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if(!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP,$this->item->package->features)){ ?>
                        <?php if($attributeConfig["map"]!=ATTRIBUTE_NOT_SHOW){?>
                            <div id="map-container">
                                <div id="company-map">
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </fieldset>
            
            <div class="button-row">
                <button type="button" class="btn btn-success" onclick="saveLocation();">
                    <span class="ui-button-text"><?php echo JText::_("LNG_SAVE")?></span>
                </button>
                <button type="button" class="btn btn-success" onclick="saveLocation(true);">
                    <span class="ui-button-text"><?php echo JText::_("LNG_SAVE_AND_CLOSE")?></span>
                </button>
                <button type="button" class="btn btn-danger" onclick="parent.jQuery.jbdModal.close();">
                    <span class="ui-button-text"><?php echo JText::_("LNG_CLOSE")?></span>
                </button>
            </div>
        </div>
    	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" /> 
    	<input type="hidden" name="company_id" value="<?php echo !empty($this->item->id)?$this->item->id:$this->location->company_id ?>" /> 
    	<input type="hidden" name="locationId" id="locationId" value="<?php echo isset($this->location->id)?$this->location->id:"0" ?>" />
    	<input type="hidden" name="identifier" id="identifier" value="<?php echo $identifier; ?>" />
    	
    	
    	<?php if(isset($isProfile)){ ?>
    		<input type="hidden" name="task" id="task" value="managecompany.saveLocation" />
    		<input type="hidden" name="view" id="view" value="managecompany" /> 
    	<?php }else{ ?>
    		<input type="hidden" name="task" id="task" value="company.saveLocation" />
    		<input type="hidden" name="view" id="view" value="company" />
    	<?php }?>
    	<?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>

<?php

if (isset($isProfile)) {
    echo JBusinessUtil::getMapGDPRModal();
}

JBusinessUtil::loadUploadScript(true, true,true,true);

$latitude = isset($this->location->latitude) && strlen($this->location->latitude)>0?$this->location->latitude:"0";
$longitude = isset($this->location->longitude) && strlen($this->location->longitude)>0?$this->location->longitude:"0";

$map_zoom = $this->appSettings->map_zoom;
if ((empty($map_zoom)) || (!is_numeric($map_zoom))) {
	$map_zoom = 10;
}

$initparams =  array();
$initparams['map_latitude'] = $latitude;
$initparams['map_longitude'] = $longitude;
$initparams['map_height'] = "250px";
$initparams['map_zoom'] = empty($this->item->latitude)?(int)$map_zoom:15;
$initparams['longitude'] = $this->item->longitude;
$initparams['latitude'] = $this->item->latitude;
$initparams['is_admin'] = true;
if ($this->appSettings->map_type == MAP_TYPE_BING) {
	$initparams["key"] = $mapKey;
}

$initLocation = new stdClass();
$initLocation->longitude = $longitude;
$initLocation->latitude = $latitude;
$initLocations = array($initLocation);
?>

<script>
var jbdAdminMapInstance;
var closeOpdate;

function initialize() {
    <?php if ($attributeConfig["map"] != ATTRIBUTE_NOT_SHOW) { ?>
        jbdAdminMapInstance  = JBDMapHelper.loadMapScript(
            <?php echo json_encode($initLocations) ?>,
            <?php echo json_encode($initparams)?>
        );
        jbdUtils.addAdminMapClickAction();
    <?php } ?>
}

function saveLocation(clsOnUpdt){
	if(!jbdUtils.validateCmpForm(false, false)){
        jQuery(".button-save").addClass("loader")
        jQuery(".button-save, .button-close , .button-cancel").attr("disabled", true)

		jQuery("#item-form").submit();
	}

    parent.closeOpdate = clsOnUpdt;
    closeOpdate = clsOnUpdt;
}

function updateLocation(id, name, streetNumber, address, city, county, country) {
    console.debug(id);
    console.debug(name);
    console.debug(streetNumber);
    console.debug(address);
    console.debug(city);
    console.debug(county);
    console.debug(country);
	if (parent.jQuery("#location-" + id).length > 0) {
        parent.jQuery("#location-" + id).html(name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + ", " + country);
    }
    else {
        let locationContainer = '<div id="location-box-' + id + '" class="detail_box">';
        locationContainer += '<div id="location-' + id + '">' + name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + " ," + country + '</div>';
        locationContainer += '<a href = "javascript:jbdListings.editLocation('+id+')"><?php echo JText::_("LNG_EDIT") ?></a>';
        locationContainer += ' | ';
        locationContainer += '<a href = "javascript:jbdListings.deleteLocation('+id+')" ><?php echo JText::_("LNG_DELETE") ?></a>';
        locationContainer += '</div>';
        parent.jQuery("#company-locations").append(locationContainer);
    }

    if(parent.closeOpdate){
        parent.jQuery.jbdModal.close();
    }

    parent.closeOpdate = false;
}

window.addEventListener('load', function(){
	jQuery("#item-form").validationEngine('attach');
	<?php if(!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP,$this->item->package->features)){ ?>
    initialize();
	<?php }?>
    jbdUtils.initializeAdminAutocomplete(true);

	if(jQuery("#locationId").val()>0){
        updateLocation(jQuery("#locationId").val(), jQuery("#name").val(), jQuery("#street_number").val(),jQuery("#route").val(),jQuery("#locality").val(),jQuery("#administrative_area_level_1").val(),jQuery("#country :selected").text());
	}

    let metricIndex = <?php echo $this->appSettings->metric==1?1.6:0.621371 ?>;

    jQuery("#sec_activity_radius").change(function(){
        let val = jQuery(this).val();
        
        val = jbdUtils.convertMetric(val, metricIndex);
        jQuery("#sec-converted-value").html(val);
    });

    jQuery("#sec_activity_radius").trigger("change");
});

<?php if ($this->appSettings->limit_cities_regions == 1) { ?>
    var showCountryAttr = <?php echo $attributeConfig["country"]!=ATTRIBUTE_NOT_SHOW ?'true':'false'; ?>

    function updateCities(callback)
    {
        var regionIds = jQuery('#activity_regions').val();

        var dataSelected = [];
        jQuery('#activity_cities').find('option:selected').each(function(){
            dataSelected.push(jQuery(this).val());
        });

        if (regionIds == null) {
            jQuery('#activity_cities').empty();
            jbdUtils.updateChosenSelect('#activity_cities');
            return;
        }
        let url = jbdUtils.getAjaxUrl('getCitiesByRegionsAjax', 'cities');
        jQuery.ajax({
            type: 'GET',
            url: url,
            data: {regionParam: regionIds},
            dataType: 'json',
            cache: false,
            success: function (data) {
                var selectedVar;
                jQuery('#activity_cities').empty();

                var tmp = [];
                tmp.id = -1;
                tmp.name = '<?php echo JText::_('LNG_SELECT_CITY') ?>';
                data.unshift(tmp);

                jQuery.each(data, function (i, val) {
                    selectedVar = "";
                    if (jQuery.inArray(val.id, dataSelected) != '-1') {
                        selectedVar = "selected";
                    }
                    jQuery('#activity_cities').append('<option ' + selectedVar + ' value="' + val.id + '">' + val.name + '</option>');
                });
                jbdUtils.updateChosenSelect('#activity_cities');

                if (typeof callback !== "undefined") {
                    callback();
                }
            }
        });
    }

    function updateRegions(callback)
    {
        var countryId = jQuery('#country').val();

        var dataSelected = [];
        jQuery('#activity_regions').find('option:selected').each(function(){
            dataSelected.push(jQuery(this).val());
        });

        if (showCountryAttr && countryId == null) {
            jQuery('#activity_regions').empty();
            jbdUtils.updateChosenSelect('#activity_regions');
            return;
        }
        let url = jbdUtils.getAjaxUrl('getRegionsByCountryAjax', 'regions');
        jQuery.ajax({
            type: 'GET',
            url: url,
            data: {countryId: countryId},
            dataType: 'json',
            cache:false,
            success: function (data) {
                var selectedVar;
                jQuery('#activity_regions').empty();

                var tmp = [];
                tmp.id = -1;
                tmp.name = '<?php echo JText::_('LNG_SELECT_REGION') ?>';
                data.unshift(tmp);

                jQuery.each(data, function (i, val) {
                    selectedVar = "";
                    if (jQuery.inArray(val.id, dataSelected) != '-1') {
                        selectedVar = "selected";
                    }
                    jQuery('#activity_regions').append('<option ' + selectedVar + ' value="' + val.id + '">' + val.name + '</option>');
                });
                jbdUtils.updateChosenSelect('#activity_regions');

                updateCities();

                if (typeof callback !== "undefined") {
                    callback();
                }
            }
        });
    }

   
<?php } ?>
</script>