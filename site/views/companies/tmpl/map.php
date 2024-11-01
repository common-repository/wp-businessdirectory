<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$lang = JBusinessUtil::getLanguageTag();
$key  = JBusinessUtil::loadMapScripts();

/**
 * Data for the markers consisting of a name, a LatLng and a zIndex for
 * the order in which these markers should display on top of each
 * other.
 */

$marker = 0;

if ($this->company->featured) {
	if (!empty($appSettings->feature_map_marker)) {
		$marker = BD_PICTURES_PATH. $appSettings->feature_map_marker;
	} 
} else if(!empty($this->company->categoryMarker) && $this->appSettings->show_custom_markers) {
    $marker = BD_PICTURES_PATH.$this->company->categoryMarker;
}

$contentPhone = (!empty($this->company->phone) && (isset($this->package->features) && in_array(PHONE,$this->package->features) || !$appSettings->enable_packages)) ? '<div class="info-phone"><i class="la la-phone"></i> '.htmlspecialchars($this->company->phone, ENT_QUOTES).'</div>':"";
$contentString =
                '<div class="info-box">'.
                '<div class="title">'.htmlspecialchars($this->company->name).'</div>'.
                '<div class="info-box-content">'.
                '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">'.
                JBusinessUtil::getAddressText($this->company).'</div>'.$contentPhone.
                '</div>'.
                '<div class="info-box-image">'.
                    (!empty($this->company->logoLocation)?'<img src="'. BD_PICTURES_PATH.(htmlspecialchars($this->company->logoLocation, ENT_QUOTES)).'" alt="'.htmlspecialchars($this->company->name).'">':"").
                '</div>'.
                '</div>';

$itemLocations = array();
$tmp = array();
if(!empty($this->company->latitude) && !empty($this->company->longitude) && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages)) {
    $tmp['latitude'] = $this->company->latitude;
    $tmp['longitude'] = $this->company->longitude;
    $tmp['activity_radius'] = $this->company->activity_radius;
    $tmp['marker'] = $marker;
    $tmp['content'] = $contentString;
}

$itemLocations[] = $tmp;

$index = 1;
foreach($this->company->locations as $location) {
    $tmp = array();
    $contentPhoneLocation = (!empty($location->phone) && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) ? '<div class="info-phone"><i class="la la-phone"></i> ' . htmlspecialchars($location->phone, ENT_QUOTES) . '</div>' : "";
    $address = JBusinessUtil::getAddressText($location);
    $locationName = !empty($location->name) ? '<div class="name" itemprop="name">' . htmlspecialchars($location->name, ENT_QUOTES) . '</div>' : "";

    $contentStringLocation =
        '<div class="info-box">' .
        '<div class="title">' . htmlspecialchars($this->company->name) . '</div>' .
        '<div class="info-box-content">' .
        $locationName .
        '<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . $address . '</div>' .
        $contentPhoneLocation .
        '</div>' .
        '<div class="info-box-image">' .
        (!empty($this->company->logoLocation) ? '<img src="' . BD_PICTURES_PATH . (htmlspecialchars($this->company->logoLocation, ENT_QUOTES)) . '" alt="' .htmlspecialchars($this->company->name). '">' : "") .
        '</div>' .
        '</div>';

    if (!empty($location->latitude) && !empty($location->longitude) && (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)) {
        $tmp['latitude'] = $location->latitude;
        $tmp['longitude'] = $location->longitude;
        $tmp['activity_radius'] = $location->activity_radius;
        $tmp['marker'] = $marker;
        $tmp['content'] = $contentStringLocation;
    }

    $itemLocations[] = $tmp;
    $index++;
}

$params = array();
$params['map_latitude'] = $itemLocations[0]['latitude'];
$params['map_longitude'] = $itemLocations[0]['longitude'];
$params['map_div'] = 'company-map';
$params["imagePath"] = BD_COMPONENT_IMAGE_PATH;

$params['panorama'] = 1;
//$params['map_zoom'] = 15;

if(isset($disableMapDefaultUI)){
    $params['disableDefaultUI'] = 1;
}

if (isset($mapHeight)) {
    $params["map_height"] = $mapHeight;
}

$params['map_enable_auto_locate'] = 0;
if (count($this->company->locations) > 0) {
    $params['map_enable_auto_locate'] = 1;
}

if ($appSettings->map_type == MAP_TYPE_BING) {
	$params["key"] = $key;
}
?>

<?php if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
		&& isset($this->company->latitude) && isset($this->company->longitude)) {
?>

    <div class="map-container">
        <?php if(!isset($hideMapDirections)){?>
            <a class="pl-3 map-directions" target="_blank" href="<?php echo JBusinessUtil::getDirectionURL($this->location, $this->company) ?>"><?php echo JText::_("LNG_GET_MAP_DIRECTIONS")?></a>
        
            <?php if($appSettings->map_type == MAP_TYPE_GOOGLE) { ?>
                <div id="map-street-view-panel">
                    <input type="button" value="<?php echo JText::_("LNG_TOGGLE_STREET_VIEW")?>" onclick="toggleStreetView();" />
                </div>
            <?php } ?>
        <?php } ?>

        <div id="company-map" style="position:relative;">
        </div>
    </div>

	<script>
        var map_instance_details;

        <?php
	    if($this->tabId == 2) {
	        echo "window.onload = loadDetailsViewMap();";
	    }
	    ?>

        function loadDetailsViewMap() {
            map_instance_details = JBDMapHelper.loadMapScript(
                <?php echo json_encode($itemLocations) ?>,
                <?php echo json_encode($params) ?>,
                <?php echo $appSettings->map_type ?>
            );
        }

        function toggleStreetView() {
            map_instance_details.toggleStreetView();
        }

	</script>
<?php }?>

<?php echo JBusinessUtil::getMapGDPRModal(); ?>