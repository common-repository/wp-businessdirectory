<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

if (!defined('BD_COMPONENT_IMAGE_PATH')) {
	define("BD_COMPONENT_IMAGE_PATH", BD_ASSETS_FOLDER_PATH."images/");
}

$appSettings = JBusinessUtil::getApplicationSettings();
$lang        = JBusinessUtil::getLanguageTag();
$key         = JBusinessUtil::loadMapScripts();

$map_latitude  = $appSettings->map_latitude;
$map_longitude = $appSettings->map_longitude;
$map_zoom      = (int) $appSettings->map_zoom;
$user = JBusinessUtil::getUser();

if ((empty($map_latitude)) || (!is_numeric($map_latitude))) {
	$map_latitude = "43.679549569435906";
}

if ((empty($map_longitude)) || (!is_numeric($map_longitude))) {
	$map_longitude = "-80.28390466016782";
}

if ((empty($map_zoom)) || (!is_numeric($map_zoom))) {
	$map_zoom = 3;
}

if ($appSettings->map_apply_search != '1') {
	$map_latitude  =  "43.679549569435906";
	$map_longitude = "-80.28390466016782";
	$map_zoom      = 3;
}

// if selected the Style 5 layout from General settings
$layout_style_5 = false;
if ($appSettings->search_result_view == 5 && (empty($params) || empty($params->get('showMap')))) {
	$layout_style_5 = true;
}

$layout_style_9 = false;
if ($appSettings->search_result_view == 9 && (empty($params) || empty($params->get('showMap')))) {
	$layout_style_9 = true;
}

$mapId = rand(1000, 10000);
if ($appSettings->enable_map_clustering) {
	JBusinessUtil::enqueueScript('libraries/map/markercluster.js');
}

$width  = "100%";
$height = "450px";

if (isset($mapHeight)) {
	$height = $mapHeight;
}
if (isset($mapWidth)) {
	$width = $mapWidth;
}

$session = JFactory::getSession();
if (empty($radius)) {
	$radius = $session->get("radius");
}

if ($appSettings->metric == 0) {
	$radius = $radius * 0.621371;
}

$company_locations = array();
if (!isset($companies) && !empty($this->companies)) {
	$companies = $this->companies;
}

if(isset($this) && isset($this->allCompanies)){
	$companies = $this->allCompanies;
}

if(!empty($companies)){
	$company_locations = JBusinessUtil::prepareCompaniesMapLocations($companies);
}

// the params array that will be used to initialize the map
$initparams                   = array();
$initparams["tmapId"]         = $mapId;
$initparams["default_marker"] = $mapId;
$initparams["map_div"]        = 'companies-map-';
$initparams["map_style"]      = 'search';
$initparams["map_latitude"]   = $map_latitude;
$initparams["map_longitude"]  = $map_longitude;
$initparams["map_width"]      = $width;
$initparams["map_height"]     = $height;
$initparams["map_zoom"]       = $map_zoom;
$initparams["isLayout"]       = ($layout_style_5 || $layout_style_9) ? 1 : 0;
$initparams["markerButton"]   = ($layout_style_5 || $layout_style_9) ? 1 : 0;
$initparams["imagePath"]      = BD_COMPONENT_IMAGE_PATH;
$initparams["has_location"]   = (isset($this) && !empty($this->location["latitude"])) ? 1 : 0;
$initparams ["radius"] 		  = !empty($radius) && isset($this) && !empty($this->location["latitude"]) ? $radius : 0;
$initparams["longitude"]      = '';
$initparams["latitude"]       = '';

if (isset($this) && !empty($this->location["latitude"])) {
	$initparams["longitude"] = $this->location["longitude"];
	$initparams["latitude"]  = $this->location["latitude"];
}

if ($appSettings->map_type == MAP_TYPE_BING) {
	$initparams["key"] = $key;
}
?>

<div id="companies-map-<?php echo $mapId ?>" style="position: relative;" class="search-map-container"></div>

<?php echo JBusinessUtil::getMapGDPRModal(); ?>

<script>
    var mapInstance;

    window.addEventListener('load', function() {
		<?php if(!isset($isFixedMapLayout) || !$isFixedMapLayout){?>
			mapInstance = JBDMapHelper.loadMapScript(
				<?php echo json_encode($company_locations) ?>,
				<?php echo json_encode($initparams) ?>,
				<?php echo $appSettings->map_type ?>
			);
		<?php } ?>
    });
</script>