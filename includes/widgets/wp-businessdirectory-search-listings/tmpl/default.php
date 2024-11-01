<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$preserve = $params->get('preserve');
$categorySearch = $session->get('categorySearch');
$minLetters = $params->get('minLetters');
$minLetters = is_numeric($minLetters)?$minLetters:3;
$token = rand(10, 1000);
JBusinessUtil::loadMapScripts();
$limitCountries = JBusinessUtil::getCountryRestriction();
$user = JBusinessUtil::getUser();

$zipCode = $params->get('defaultLocationName');
if($preserve && !empty($session->get('zipcode'))){
    $zipCode = $session->get('zipcode');
}
$geoLatitude = $params->get('defaultLatitude');
if($preserve && !empty($session->get('geo-latitude'))){
    $geoLongitude = $session->get('geo-longitude');
}
$geoLongitude = $params->get('defaultLongitude');
if($preserve && !empty($session->get('geo-longitude'))){
    $geoLongitude = $session->get('geo-longitude');
}

$locationDetected = $session->get('location-detected');
if(!empty($geoLongitude) && !empty($geoLongitude)){
    $locationDetected = 1 ;
}

?>
<div class="search-background-overlay"></div>
<div class="module-search-map jbd-container">
	<?php
		if($params->get('showMap')) {
			require JPATH_COMPONENT_SITE.'/include/search-map.php';
		}
	?>
</div>

<?php if(!$params->get('showOnlyMap')) { ?>
    <div id="companies-search" class="jbd-container business-directory<?php echo $moduleclass_sfx ?> <?php echo $layoutType?> <?php echo $styleClass ?>" style="<?php echo $bgStyle ?>">
        <div id="dir-container-<?php echo $token ?>">
            <div id="searchform" class="listing-search-form">
                <?php $title = $params->get('title'); ?>
                <?php if(!empty($title)){ ?>
                    <h1><?php echo $title ?></h1>
                <?php } ?>
                <?php $description = $params->get('description'); ?>
                <?php if(!empty($description)){ ?>
                    <p class="search-description"><?php echo $description ?></p>
                <?php } ?>

                <div class="jbd-search-bar">
                    <form action="<?php echo $formAction ?>"  method="<?php echo $appSettings->submit_method?>" name="keywordSearch" id="keywordSearch">
                        <div class="form-container">
                            <div class="fields-container">
                                <?php if($params->get('showKeyword')){ ?>
                                    <div class="form-field search">
                                        <div class="<?php echo $params->get('fieldLabel')==1?"has-jicon-left":"has-text-left" ?> ">
                                            <?php if($params->get('fieldLabel')==2){ ?>
                                                <label class="text-label"> <?php echo JText::_("LNG_WHAT")?></label>
                                            <?php } ?>
                                            <input autocomplete="off" class="search-field checkNumberChars-<?php echo $token?>" type="text" placeholder="<?php echo JText::_("LNG_SEARCH_BY_KEYWORD")?>" name="searchkeyword" id="searchkeyword" value="<?php  echo $preserve?$session->get('searchkeyword'):"";?>" />
                                            <?php if($params->get('fieldLabel')==1){ ?>
                                                <i class="la la-search"></i>
                                            <?php }?>
                                            <div class="search-dropdown validation keyword-search-validation-<?php echo $token ?>" style="display:none">
                                               <?php echo JText::_("LNG_ENTER_KEYWORD") ?>
                                            </div>
                                            <?php if(!empty($suggestedCategories)) { ?>
                                                <div id="keyword-suggestions" class="search-dropdown categories-suggestion-<?php echo $token?>" style="display:none">
                                                    <ul>
                                                        <li class="small"><?php echo JText::_("LNG_SUGGESTED_SERVICES")?></li>
                                                        <?php foreach($suggestedCategories as $category){?>
                                                            <li><a href="javascript:setCategorySuggestion(<?php echo $category->id?>,'<?php echo $category->name?>')"><?php echo $category->name?></a></li>
                                                            <?php if(!empty($category->subcategories) && !$separateCategories){?>
                                                                <?php foreach($category->subcategories as $subCat){?>
                                                                    <li><a href="javascript:setCategorySuggestion(<?php echo $subCat->id?>, '<?php echo $subCat->name?>')"><?php echo $subCat->name?></a></li>
                                                                <?php }?>
                                                            <?php }?>
                                                        <?php }?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if($params->get('showKeyword') && false){ ?>
                                    <div class="form-field" id="search-field-container-<?php echo $token ?>">
                                        <i class="la la-map-marker"></i>
                                        <input autocomplete="off" class="search-field" placeholder="<?php echo JText::_("LNG_LOCATION")?>" type="text" name="searchkeywordLocation" id="searchkeywordLocation" value="<?php  echo $preserve?$session->get('searchkeywordLocation'):"";?>" />
                                    </div>
                                <?php } ?>

                                <?php if($params->get('showCategories')){ ?>
                                <div id="categories-container-<?php echo $token?>" class="form-field">
                                    <div class="categories-form-field" id="1">
                                        <select id="categories-1" onchange="showCategorySelect<?php echo $token ?>(1)">
                                            <option value="0"><?php echo JText::_("LNG_ALL_CATEGORIES") ?></option>
                                            <?php foreach($categories as $category){?>
                                                <option value="<?php echo $category->id?>" <?php echo $categorySearch==$category->id && $preserve?" selected ":"" ?> ><?php echo $category->name?></option>
                                                <?php if(!empty($category->subcategories) && !$separateCategories){?>
                                                    <?php foreach($category->subcategories as $subCat){?>
                                                            <option value="<?php echo $subCat->id?>" <?php  echo $categorySearch==$subCat->id && $preserve?" selected ":"" ?> >-- <?php echo $subCat->name?></option>
                                                    <?php }?>
                                                <?php }?>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                <?php }?>

                                <?php if($params->get('showTypes')){ ?>
                                    <div class="form-field">
                                        <select name="typeSearch" id="typeSearch">
                                            <option value="0"><?php echo JText::_("LNG_ALL_TYPES") ?></option>
                                            <?php foreach($types as $type){?>
                                                <option value="<?php echo $type->id?>" <?php  echo $session->get('typeSearch')==$type->id && $preserve?" selected ":"" ?> ><?php echo $type->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }?>

                                <?php if($params->get('showZipcode')){ ?>
                                    <div class="form-field zipcode-container" id="zipcode-container" style="display:block;">
                                        
                                        <?php if($params->get('showLocationPopup')){ ?>
                                            <div class="search-dropdown dir-search-preferences-<?php echo $token ?>" style="display:none">
                                                <h3 class="title"><?php echo JText::_("LNG_SEARCH_PREFERENCES")?><i class="la la-close right" onclick="jQuery('.dir-search-preferences-<?php echo $token ?>').hide()"></i></h3>
                                                <?php if($appSettings->zipcode_search_type == SEARCH_BY_DISTNACE){?>
                                                    <div class="geo-radius">
                                                        <div><?php echo JText::_("LNG_RADIUS")?> (<?php echo $appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?>)</div>
                                                    </div>
                                                    <div>
                                                        <input type="text" id="geo-location-radius-<?php echo $token ?>" name="radius" value="<?php echo !empty($radius)?$radius: "0" ?>">
                                                    </div>
                                                <?php } ?>
                                                <div class="geo-location">
                                                    <?php echo JText::_("LNG_GEOLOCATION")?>
                                                    <div id="loading-geo-locaiton-<?php echo $token ?>" class="ui-autocomplete-loading" style="display:none"></div>
                                                    <a id="enable-geolocation-<?php echo $token ?>" class="toggle btn-on <?php echo !empty($geoLocation)?"active":""?>" title="Grid" href="javascript:enableGeoLocation<?php echo $token ?>(<?php echo $token ?>)"><?php echo strtoupper(JText::_("LNG_GEO_ON")) ?></a>
                                                    <a id="disable-geolocation-<?php echo $token ?>" class="toggle btn-off <?php echo empty($geoLocation)?"active":""?>" title="List" href="javascript:disableGeoLocation<?php echo $token ?>(<?php echo $token ?>)"><?php echo strtoupper(JText::_("LNG_GEO_OFF")) ?></a>
                                                </div>
                                            </div>
                                        <?php } else{ ?>
                                            <input type="hidden" name="radius" value="<?php echo !empty($radius)?$radius: "0" ?>">
                                        <?php } ?>

                                        <?php if($params->get('showNearbyLocationPopup')){ ?>
                                            <div class="search-dropdown dir-search-nearby-<?php echo $token ?>" style="display:none">
                                                <ul>
                                                    <li class="all-locations"><?php echo JText::_("LNG_SEARCH_ZIPCODE")?></li>
                                                    <?php foreach($nearByCities as $nCity){?>
                                                        <li><a href="javascript:setLocationSuggestion(<?php echo $nCity->city ?>,'<?php echo $nCity->region ?>','<?php echo $nCity->country ?>')"><?php echo $nCity->city ?></a></li>
                                                    <?php }?>
                                                </ul>
                                            </div>
                                        <?php } ?>

                                        <div class="<?php echo $params->get('fieldLabel')==1?"has-jicon-left":"has-text-left" ?> " id="search-field-container-<?php echo $token ?>">
                                            <?php if($params->get('fieldLabel')==2){ ?>
                                                <label class="text-label"> <?php echo JText::_("LNG_WHERE")?></label>
                                            <?php } ?>
                                            <input class="search-field zipcode-<?php echo $token ?>" placeholder="<?php echo JText::_("LNG_SEARCH_ZIPCODE")?>" type="text" name="zipcode" id="zipcode" value="<?php  echo $zipCode ?>" />
                                            <?php if($params->get('fieldLabel')==1){ ?>
                                                <i class="la la-map-marker"></i>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if($params->get('showCountries')) { ?>
                                    <div class="form-field">
                                        <select name="countrySearch" id="countrySearch" <?php if($choices==1) { ?> onchange="getCitiesByCountry('<?php echo $token ?>');" <?php } else if($choices==2) { ?> onchange="getRegionsByCountry('<?php echo $token ?>');" <?php } ?> >
                                            <option value="0"><?php echo JText::_("LNG_ALL_COUNTRIES") ?></option>
                                            <?php foreach($countries as $country) { ?>
                                                <option value="<?php echo $country->id?>" <?php echo $session->get('countrySearch')==$country->id && $preserve?" selected ":"" ?> ><?php echo $country->country_name?></option>
                                            <?php }?>

                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if($params->get('showProvince')) { ?>
                                    <div class="form-field">
                                        <select name="provinceSearch" id="provinceSearch">
                                            <option value="0"><?php echo JText::_("LNG_ALL_PROVINCES") ?></option>
                                            <?php foreach($provinces as $province){?>
                                                <option value="<?php echo htmlspecialchars($province->province, ENT_QUOTES) ?>" <?php echo $session->get('provinceSearch')==$province->province && $preserve?" selected ":"" ?> ><?php echo $province->province?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if($params->get('showRegions')) { ?>
                                    <div class="form-field">
                                        <select name="regionSearch" id="regionSearch" <?php if($choices && $params->get('showCities')) { ?> onchange="getCitiesByRegion('<?php echo $token ?>');" <?php } ?> >
                                            <option value="0"><?php echo JText::_("LNG_ALL_REGIONS") ?></option>
                                            <?php foreach($regions as $region) { ?>
                                                <option value="<?php echo htmlspecialchars($region->id, ENT_QUOTES) ?>" <?php echo $session->get('regionSearch') == $region->id && $preserve ? " selected " : "" ?> ><?php echo $region->county?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if($params->get('showCities')) { ?>
                                    <div class="form-field">
                                        <select name="citySearch" id="citySearch">
                                            <option value="0"><?php echo JText::_("LNG_ALL_CITIES") ?></option>
                                            <?php foreach($cities as $city){?>
                                                <option value="<?php echo htmlspecialchars($city->id, ENT_QUOTES) ?>" <?php echo $session->get('citySearch') == $city->id && $preserve ? " selected " : "" ?> ><?php echo $city->city?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if(!empty($customAttributes)) { ?>
                                    <?php
                                        require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
                                        $renderedContent = AttributeService::renderAttributesSearch($customAttributes, false, array());
                                        echo $renderedContent;
                                    ?>
                                <?php } ?>

                                <?php if($params->get('showBookmark')) { ?>
                                    <div class="form-field bookmark-container">
                                        <i id="bookmark-icon-<?php echo $token ?>" class="bookmark-icon la <?php echo $session->get('filter-by-fav')?"la-heart":"la-heart-o" ?>"></i>
                                        <input type="hidden" name="filter-by-fav" id="filter-by-fav"/>
                                    </div>
                                <?php } ?>

                            </div>

                            <button type="button" class="btn btn-success search-dir-button" onclick="return checkSearch<?php echo $token ?>()">
                                <i class="la la-search"></i> <?php echo JText::_("LNG_SEARCH")?>
                            </button>
                        </div>

                        <a style="display:none" id="categories-link" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&controller=categories&view=categories&task=displaycategories') ?>"><?php echo JText::_("LNG_CATEGORY_LIST")?></a>
                        <!--input type="hidden" name="option" value="com_jbusinessdirectory" -->
                        <input type="hidden" name="view" value="search">
                        <input type="hidden" name="resetSearch" value="1">
                        <input type="hidden" name="preserve" value="<?php echo $preserve?>">
                        <input type="hidden" name="geo-latitude" id="geo-latitude" value="<?php echo $geoLatitude ?>">
                        <input type="hidden" name="geo-longitude" id="geo-longitude" value="<?php echo $geoLongitude ?>">
                        <input type="hidden" name="geo-country" id="geo-country" value="">
                        <?php if(false) { ?>
                            <?php if(!$params->get('showCities')) { ?>
                                <input type="hidden" name="citySearch" id="citySearch" value="<?php echo $preserve && $params->get('showCities') ?$session->get('citySearch'):"" ?>">
                            <?php } ?>
                            <?php if(!$params->get('showRegions')) { ?>
                                <input type="hidden" name="regionSearch" id="regionSearch" value="<?php echo $preserve && $params->get('showRegions')?$session->get('regionSearch'):"" ?>">
                            <?php } ?>
                            <?php if(!$params->get('showCountries')) { ?>
                                <input type="hidden" name="countrySearch" id="countrySearch" value="">
                            <?php } ?>
                        <?php } ?>
                        <input type="hidden" name="location-detected" id="location-detected" value="<?php echo $preserve?$locationDetected:"" ?>">
                        <input type="hidden" name="geolocation" id="geolocation" value="<?php echo $geoLocation ?>">
                        <input type="hidden" name="categorySuggestion" id="categorySuggestion" value="">
                        <input type="hidden" name="suggestionType" id="suggestionType" value="">
                        <input type="hidden" name="categorySearch" id="categorySearch" value="<?php echo !empty($categorySearch)  && $preserve && $params->get('showCategories') ?$categorySearch:"" ?>">
                    </form>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php } ?>

<?php require_once JPATH_COMPONENT_SITE.'/include/bookmark_utils.php'; ?>

<script>
	var url = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory')?>";
	var minLetters = <?php echo $minLetters ?>;
	var keyword;

	function checkSearch<?php echo $token ?>(){
		jQuery("#dir-container-<?php echo $token ?> #searchkeyword").removeClass("required");
		<?php if($params->get('mandatoryKeyword') && $params->get('showKeyword')){ ?>
			if(document.getElementById('searchkeyword') && jQuery("#dir-container-<?php echo $token ?> #searchkeyword").val().length == 0){
				jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focus();
				jQuery("#dir-container-<?php echo $token ?> #searchkeyword").addClass("required");
				return false;
			}
		<?php } ?>

		<?php if($params->get('showKeyword')){ ?>
    		if(document.getElementById('searchkeyword') && jQuery("#dir-container-<?php echo $token ?> #searchkeyword").val().length > 0 
               && document.getElementsByClassName('checkNumberChars-<?php echo $token ?>') && minLetters!=0) {
                if(!checkNumberLetters(<?php echo $token ?>)) {
                	jQuery(".keyword-search-validation-<?php echo $token ?>").slideDown(500);
                    return false;
                }
            }
        <?php } ?>

		jQuery("#dir-container-<?php echo $token ?> #categories-1").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #categories_1_chosen").removeClass("required");
		<?php if($params->get('mandatoryCategories')){ ?>
			var foo = jQuery("#dir-container-<?php echo $token ?> #categories-1");
			if (foo) {
				if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #categories-1").focus();
					jQuery("#dir-container-<?php echo $token ?> #categories-1").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #categories_1_chosen").addClass("required");
					return false;
				}
			}
		<?php } ?>

		jQuery("#dir-container-<?php echo $token ?> #categories-2").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #categories_2_chosen").removeClass("required");
		<?php if($params->get('mandatoryCategories')){ ?>
			var foo = jQuery("#dir-container-<?php echo $token ?> #categories-2");
			if (foo) {
				if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #categories-2").focus();
					jQuery("#dir-container-<?php echo $token ?> #categories-2").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #categories_2_chosen").addClass("required");
					return false;
				}
			}
		<?php } ?>

		jQuery("#dir-container-<?php echo $token ?> #categories-3").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #categories_3_chosen").removeClass("required");
		<?php if($params->get('mandatoryCategories')){ ?>
			var foo = jQuery("#dir-container-<?php echo $token ?> #categories-3");
			if (foo) {
				if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #categories-3").focus();
					jQuery("#dir-container-<?php echo $token ?> #categories-3").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #categories_3_chosen").addClass("required");
					return false;
				}
			}
		<?php } ?>
		

		jQuery("#dir-container-<?php echo $token ?> #typeSearch").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #typeSearch_chosen").removeClass("required");
		<?php if($params->get('mandatoryTypes')) { ?>
			var foo = jQuery("#dir-container-<?php echo $token ?> #typeSearch");
			if (foo) {
				if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #typeSearch").focus();
					jQuery("#dir-container-<?php echo $token ?> #typeSearch").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #typeSearch_chosen").addClass("required");
					return false;
				}
			}
		<?php } ?>

        jQuery(".zipcode-<?php echo $token ?>").removeClass("required");
        <?php if($params->get('mandatoryZipCode')) { ?>
        if(document.getElementById('zipcode') && jQuery(".zipcode-<?php echo $token ?>").val().length == 0){
            jQuery(".zipcode-<?php echo $token ?>").focus();
            jQuery(".zipcode-<?php echo $token ?>").addClass("required");
            return false;
        }
        <?php } ?>

        jQuery(".zipcode-<?php echo $token ?>").removeClass("required");
        <?php if($params->get('mandatoryLocationSuggestion')) { ?>
        if(document.getElementById('zipcode') && jQuery(".zipcode-<?php echo $token ?>").val().length == 0 && document.getElementById('location-detected') && jQuery("#dir-container-<?php echo $token ?> #location-detected").val().length == 0){
            jQuery(".zipcode-<?php echo $token ?>").focus();
            jQuery(".zipcode-<?php echo $token ?>").addClass("required");
            return false;
        }
       
        <?php } ?>

		jQuery("#dir-container-<?php echo $token ?> #countrySearch").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #countrySearch_chosen").removeClass("required")
		<?php if($params->get('mandatoryCountries')){ ?>
			var foo = jQuery("#dir-container-<?php echo $token ?> #countrySearch");
			if (foo) {
				if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #countrySearch").focus();
					jQuery("#dir-container-<?php echo $token ?> #countrySearch").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #countrySearch_chosen").addClass("required");
					return false;
				}
			}
		<?php } ?>

        jQuery("#dir-container-<?php echo $token ?> #provinceSearch").removeClass("required");
        jQuery("#dir-container-<?php echo $token ?> #provinceSearch_chosen").removeClass("required")
        <?php if($params->get('mandatoryProvince')){ ?>
        var foo = jQuery("#dir-container-<?php echo $token ?> #provinceSearch");
        if (foo) {
            if (foo.find(":selected").index() == 0) {
                jQuery("#dir-container-<?php echo $token ?> #provinceSearch").focus();
                jQuery("#dir-container-<?php echo $token ?> #provinceSearch").addClass("required");
                jQuery("#dir-container-<?php echo $token ?> #provinceSearch_chosen").addClass("required");
                return false;
            }
        }
        <?php } ?>

		jQuery("#dir-container-<?php echo $token ?> #regionSearch").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #regionSearch_chosen").removeClass("required");
		<?php if($params->get('mandatoryRegions')){ ?>
			var foo = jQuery("#dir-container-<?php echo $token ?> #regionSearch");
			if (foo) {
			   if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #regionSearch").focus();
					jQuery("#dir-container-<?php echo $token ?> #regionSearch").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #regionSearch_chosen").addClass("required");
					return false;
				}
			}
		<?php } ?>

		jQuery("#dir-container-<?php echo $token ?> #citySearch").removeClass("required");
		jQuery("#dir-container-<?php echo $token ?> #citySearch_chosen").removeClass("required");
		<?php if($params->get('mandatoryCities')){ ?>
    		var foo = jQuery("#dir-container-<?php echo $token ?> #citySearch");
    		if (foo) {
    		   if (foo.find(":selected").index() == 0) {
					jQuery("#dir-container-<?php echo $token ?> #citySearch").focus();
					jQuery("#dir-container-<?php echo $token ?> #citySearch").addClass("required");
					jQuery("#dir-container-<?php echo $token ?> #citySearch_chosen").addClass("required");
					return false;
    		   }
			}
		<?php } ?>

        <?php if($appSettings->submit_method=="get"){?>
      		disableEmptyFields('<?php echo $token ?>');
        <?php } ?>

        //loading button animation if validation is OK
        jQuery("#dir-container-<?php echo $token ?> .search-dir-button").addClass("loader");
        
        <?php if($params->get('useDynamicFormAction')){?>
            changeFormActionURL(true);
        <?php }else{ ?>
            jQuery("#dir-container-<?php echo $token ?> #keywordSearch").submit();
        <?php } ?>

		return true;
	}

	function checkNumberLetters(token) {
	    var keyword = jQuery("#dir-container-"+token +" .checkNumberChars-"+token).val();
	    if(minLetters !== undefined){
            return keyword.length >= minLetters;
        }else{
            var minLettersCheck = <?php echo $minLetters ?>;
            return keyword.length >= minLettersCheck;
        }
    }

	window.addEventListener('load', function(){

		<?php if ($params->get('setAutoFocus')){ ?>
			jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focus();
		<?php } ?>
		
        <?php if($params->get('autocomplete')){?>
            jQuery(".chosen-select").chosen();

			if(document.getElementById('categories-1')){
				jQuery("#dir-container-<?php echo $token ?> #categories-1").chosen();
			}
            
            <?php if($params->get('showCities')) { ?>
                if(document.getElementById('citySearch')){
                    jQuery("#dir-container-<?php echo $token ?> #citySearch").chosen();
                }
            <?php } ?>

            <?php if($params->get('showRegions')) { ?>
                if(document.getElementById('regionSearch')){
                    jQuery("#dir-container-<?php echo $token ?> #regionSearch").chosen();
                }
            <?php } ?>

            <?php if($params->get('showRegions')) { ?>
                if(document.getElementById('countrySearch')){
                    jQuery("#dir-container-<?php echo $token ?> #countrySearch").chosen();
                }
            <?php } ?>    

            if(document.getElementById('provinceSearch')){
                jQuery("#dir-container-<?php echo $token ?> #provinceSearch").chosen();
            }
			if(document.getElementById('typeSearch')){
				jQuery("#dir-container-<?php echo $token ?> #typeSearch").chosen();
			}
		<?php } ?>

		<?php if ($params->get('enableKeywordSuggestion') && $params->get('showKeyword') && !$params->get('showOnlyMap')){ ?>
    		jQuery("#dir-container-<?php echo $token ?> #searchkeyword").autocomplete({
    			source: "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=categories.getCategories&type='.CATEGORY_TYPE_BUSINESS) ?>",
    			minLength: 2,
                open: function( event, ui ) {
                    jQuery(".categories-suggestion-<?php echo $token?>").hide();
                },
    			select: function (event, ui) {
                    jQuery('#dir-container-<?php echo $token ?> #categorySuggestion').val(ui.item.id);
                    jQuery('#dir-container-<?php echo $token ?> #categorySearch').val(ui.item.id);
                    jQuery('#dir-container-<?php echo $token ?> #suggestionType').val(ui.item.type);
                    keyword = ui.item.label;
    				jQuery(this).val(ui.item.label);
                    changeFormActionURL();
    				return false;
    			}
			
    		}).data('ui-autocomplete')._renderItem = function(ul, item) {
                if(item.header == 1){
                    return jQuery('<li class="ui-state-disabled '+item.parent_class+'">').append("<div> "+item.label+"</div>").appendTo(ul);
                }else{
                    return jQuery('<li class="'+item.parent_class+'">').append("<div> "+item.label+"</div>").appendTo(ul);
                }
            };

           jQuery("#dir-container-<?php echo $token ?> #searchkeyword").change(function() {
                var searchKeyword = jQuery("#searchkeyword").val();
                if (searchKeyword != keyword){
                    jQuery('#categorySuggestion').val("");
                }
            });
		<?php } ?>

        <?php if ($params->get('enableLocationSuggestion') && $params->get('showZipcode') && !$params->get('showOnlyMap')){ ?>
            jQuery("#dir-container-<?php echo $token ?> #zipcode").autocomplete({
                source: "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=search.getLocationSuggestions') ?>",
                minLength: 2,
                select: function (event, ui) {
                    
                    jQuery("#dir-container-<?php echo $token ?> #citySearch").val(ui.item.city_id);
                    jQuery("#dir-container-<?php echo $token ?> #regionSearch").val(ui.item.region_id);
                    jQuery("#dir-container-<?php echo $token ?> #countrySearch").val(ui.item.country_id);

                    if(ui.item.city_id || ui.item.region_id || ui.item.country_id){
                        jQuery("#dir-container-<?php echo $token ?> #location-detected").val("1");
                    }

                    keyword = ui.item.label;
                    jQuery(this).val(ui.item.label);

                    if(changeFormActionURL){
                        changeFormActionURL();
                    }

                    return false;
                },
                open: function( event, ui ) {
                    jQuery(".dir-search-nearby-<?php echo $token ?>").hide();
                    jQuery("#dir-container-<?php echo $token ?> #location-detected").val("");
                    jQuery("#dir-container-<?php echo $token ?> #citySearch").val("");
                    jQuery("#dir-container-<?php echo $token ?> #regionSearch").val("");
                    jQuery("#dir-container-<?php echo $token ?> #countrySearch").val("");

                    if(changeFormActionURL){
                        changeFormActionURL();
                    }
                },
                response: function(event, ui) {
                    if (!ui.content.length) {
                        var allLocations = { parent_class:"all-locations", value:"",label:"<?php echo JText::_("LNG_SEARCH_ZIPCODE")?>" };
                        var noResult = { parent_class:"ui-state-disabled", value:"",label:"<?php echo JText::_("LNG_NO_MATHCING_RESULTS")?>" };
                        ui.content.push(allLocations);
                        ui.content.push(noResult);
                    }
                }
            }).data('ui-autocomplete')._renderItem = function(ul, item) {
                return jQuery('<li class="'+item.parent_class+'">').append("<div><i class='"+item.class+"'></i> "+item.label+"</div>").appendTo(ul);
            };

            jQuery("#searchkeyword").change(function() {
                var searchKeyword = jQuery("#searchkeyword").val();
                if (searchKeyword != keyword){
                    jQuery('#categorySuggestion').val("");
                }
            });
        <?php } ?>


        jQuery(".zipcode-<?php echo $token ?>").focusin(function() {
            jQuery(".dir-search-preferences-<?php echo $token ?>").slideDown(500);
            jQuery(".dir-search-nearby-<?php echo $token ?>").slideDown(500);
            
        });
        jQuery(".zipcode-<?php echo $token ?>").focusout(function() {
            //jQuery(".dir-search-preferences-<?php echo $token ?>").slideUp(500);
            jQuery(".dir-search-nearby-<?php echo $token ?>").slideUp(500);
            <?php if($params->get('mandatoryLocationSuggestion')) { ?>
                if(jQuery("#dir-container-<?php echo $token ?> #location-detected").val() != 1){
                    //jQuery(this).val("");
                    jQuery("#dir-container-<?php echo $token ?> #citySearch").val("");
                    jQuery("#dir-container-<?php echo $token ?> #regionSearch").val("");
                    jQuery("#dir-container-<?php echo $token ?> #countrySearch").val("");
                    if(changeFormActionURL){
                        changeFormActionURL();
                    }
                }
            <?php } ?>
        });

        jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focusin(function() {
            jQuery(".categories-suggestion-<?php echo $token?>").slideDown(500);
        });
        jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focusout(function() {
            jQuery(".categories-suggestion-<?php echo $token?>").slideUp(500);
        });

        jQuery(".checkNumberChars-<?php echo $token?>").focusin(function() {
            jQuery(".keyword-search-validation-<?php echo $token ?>").slideUp(500);
        });

        <?php if($params->get('showZipcode')){ ?>
            <?php if($params->get('showLocationPopup')){ ?>
                jQuery("#geo-location-radius-<?php echo $token ?>").ionRangeSlider({
                    grid: true,
                    min: 0,
                    max: <?php echo $maxRadius ?>,
                    from: <?php echo !empty($radius)?$radius: "0" ?>,
                    to: <?php echo $maxRadius ?>,
                });
            <?php } ?>

    		<?php if(!$params->get('enableLocationSuggestion') && $params->get('enableAutosuggestion') && !$params->get('showOnlyMap')) { ?>
                initializeSearchAutocomplete<?php echo $token ?>(true);
            <?php } ?>
            
        <?php } ?>

		<?php
			$jinput = JFactory::getApplication()->input;
			
			$load = $jinput->get("geo-latitude");
			if(empty($load)){
				$load = $jinput->get("latitude");
			}
		?>

		<?php if($params->get('autolocation') && empty($load)){ ?>
            enableGeoLocation<?php echo $token ?>();
		<?php }?>

        jQuery("#bookmark-icon-<?php echo $token ?>").click(function(){
            <?php if(!$user->ID){?>
                jbdUtils.showLoginNotice();
            <?php }else{?>
                jQuery(this).toggleClass("la-heart");
                jQuery(this).toggleClass("la-heart-o");

                if(jQuery(this).hasClass("la-heart")){
                    jQuery("#filter-by-fav").val(1);
                }else{
                    jQuery("#filter-by-fav").val(0);
                }
            <?php } ?>
        });
    });

    function initializeSearchAutocomplete<?php echo $token ?>(preventSubmit) {
    	jbdUtils.initializeSearchAutocomplete(<?php echo $token ?>, preventSubmit, <?php echo json_encode($limitCountries) ?>);
    }

    function enableGeoLocation<?php echo $token ?>(){
        if (navigator.geolocation) {
            //jQuery("#loading-geo-locaiton-<?php echo $token ?>").show();
            jQuery("#zipcode-container .la.la-map-marker").addClass("dir-beat-animation");
            jQuery("#enable-geolocation-<?php echo $token ?>").addClass("color-animation");
            navigator.geolocation.getCurrentPosition(setGeoLocation<?php echo $token ?>);
        }
        jQuery("#enable-geolocation-<?php echo $token ?>").addClass("active");
        jQuery("#disable-geolocation-<?php echo $token ?>").removeClass("active");
        jQuery("#dir-container-<?php echo $token ?> #geolocation").val(1);
    }

    function disableGeoLocation<?php echo $token ?>(){
        jQuery("#enable-geolocation-<?php echo $token ?>").removeClass("active");
        jQuery("#disable-geolocation-<?php echo $token ?>").addClass("active");
        jQuery("#dir-container-<?php echo $token ?> #geolocation").val(0);
        jQuery("#loading-geo-locaiton-<?php echo $token ?>").hide();
        jQuery("#zipcode-container .la.la-map-marker").removeClass("dir-beat-animation");
        jQuery("#enable-geolocation-<?php echo $token ?>").removeClass("color-animation");
        jQuery("#dir-container-<?php echo $token ?> #geo-latitude").val('');
        jQuery("#dir-container-<?php echo $token ?> #geo-longitude").val('');
        jQuery("#dir-container-<?php echo $token ?> #geo-country").val('');
    }

	function setGeoLocation<?php echo $token ?>(position){
		jQuery("#loading-geo-locaiton-<?php echo $token ?>").hide();
		jQuery("#zipcode-container .la.la-map-marker").removeClass("dir-beat-animation");
		jQuery("#enable-geolocation-<?php echo $token ?>").removeClass("color-animation");
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;
		jQuery("#dir-container-<?php echo $token ?> #geo-latitude").val(latitude);
		jQuery("#dir-container-<?php echo $token ?> #geo-longitude").val(longitude);
        codeLatLng(latitude,longitude);
	}

    function setCategorySuggestion(id, value){
        jQuery('#dir-container-<?php echo $token ?> #categorySuggestion').val(id);
        jQuery('#dir-container-<?php echo $token ?> #suggestionType').val(<?php echo SUGGESTION_TYPE_CATEGORY ?>);
        jQuery("#dir-container-<?php echo $token ?> #searchkeyword").val(value);
        changeFormActionURL();
    }

	<?php if($params->get('showMap')){ ?>
		// jbdMap.loadMapScript();
	<?php }?>

	<?php if($params->get('linklocation')) { ?>

		window.addEventListener('load', function() {
			<?php if($choices==1) { ?>
				getCitiesByCountry('<?php echo $token ?>');
			<?php } elseif($choices==2) { ?>
				getRegionsByCountry('<?php echo $token ?>');
			<?php } elseif($choices==3) { ?>
				getCitiesByRegion('<?php echo $token ?>');
			<?php } ?>
		});

		function getRegionsByCountry(token) {
			var urlRegionsByCountry = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=search.getRegionsByCountryAjax')?>"+'&countryId='+jQuery("#dir-container-"+token+" #countrySearch").val();
			//var urlCitiesByRegion = url+'&task=search.getCitiesByRegionAjax&region='+jQuery("#regionSearch").val();
			jQuery.ajax({
				type: "GET",
				url: urlRegionsByCountry,
				dataType: 'json',
				success: function(data){
					jQuery("#dir-container-"+token+" #regionSearch").empty();
					jQuery("#dir-container-"+token+" #regionSearch").html(data);
					<?php if ($preserve){ ?>
                    var regionToSet = "<?php echo $session->get('regionSearch'); ?>";
                    jQuery("#dir-container-"+token+" #regionSearch").val(regionToSet);
                    <?php } ?>
                    var selected = jQuery('#dir-container-'+token+' #regionSearch option:selected').val();
                    if(typeof selected === 'undefined')
                        jQuery(jQuery('#dir-container-'+token+' #regionSearch option')[0]).attr('selected', 'selected');
                    jbdUtils.updateChosenSelect('#dir-container-'+token+' #regionSearch');
					getCitiesByCountry(token);
				}
			});
		}

		function getCitiesByRegion(token) {
			var urlCitiesByRegion = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=search.getCitiesByRegionAjax')?>"+'&region='+jQuery("#dir-container-"+token+" #regionSearch").val();
			jQuery.ajax({
				type: "GET",
				url: urlCitiesByRegion,
				dataType: 'json',
				success: function(data){
					jQuery("#dir-container-"+token+" #citySearch").empty();
					jQuery("#dir-container-"+token+" #citySearch").html(data);
                    <?php if ($preserve){ ?>
                    var cityToSet = "<?php echo $session->get('citySearch'); ?>";
                    jQuery("#dir-container-"+token+" #citySearch").val(cityToSet);
                    <?php } ?>
                    var selected = jQuery('#dir-container-'+token+' #citySearch option:selected').val();
                    if(typeof selected === 'undefined')
                        jQuery(jQuery('#dir-container-'+token+' #citySearch option')[0]).attr('selected', 'selected');
                    jbdUtils.updateChosenSelect('#dir-container-'+token+' #citySearch');
				}
			});
		}

		function getCitiesByCountry(token) {
			var urlCitiesByCountry = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=search.getCitiesByCountryAjax')?>"+'&countryId='+jQuery("#dir-container-"+token+" #countrySearch").val();
			jQuery.ajax({
				type: "GET",
				url: urlCitiesByCountry,
				dataType: 'json',
				success: function(data){
					jQuery("#dir-container-"+token+" #citySearch").empty();
					jQuery("#dir-container-"+token+" #citySearch").html(data);
                    <?php if ($preserve){ ?>
                    var cityToSet = "<?php echo $session->get('citySearch'); ?>";
                    jQuery("#dir-container-"+token+" #citySearch").val(cityToSet);
                    <?php } ?>
                    var selected = jQuery('#dir-container-'+token+' #citySearch option:selected').val();
                    if(typeof selected === 'undefined')
                        jQuery(jQuery('#dir-container-'+token+' #citySearch option')[0]).attr('selected', 'selected');
                    jbdUtils.updateChosenSelect('#dir-container-'+token+' #citySearch');
				}
			});
		}
	<?php } ?>

	function showCategorySelect<?php echo $token ?>(level, parentId, id){
		var urlGetCategories = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=categories.getSubcategoriesByParentIdAjax') ?>";
		var selectedCat;
		if(parentId===undefined)
			 selectedCat = jQuery('#dir-container-<?php echo $token ?> #categories-'+level).find(":selected").val();
		else
			 selectedCat = parentId;

		var categoryType = <?php echo CATEGORY_TYPE_BUSINESS ?>;
		jQuery('#dir-container-<?php echo $token ?> #categorySearch').val(id);

		if(selectedCat != 0) {
			<?php if($separateCategories) { ?>
				jQuery.ajax({
					type: "GET",
					url: urlGetCategories,
					data: {parentId: selectedCat, categoryType: categoryType, level: level, categoryId: id, token: '<?php echo $token?>', option:'com_jbusinessdirectory'},
					dataType: 'json',
					success: function (data) {
						jQuery('#dir-container-<?php echo $token ?> .categories-form-field').each(function () {
							if (jQuery(this).attr('id') > level)
								jQuery(this).remove();
						});
						if (data != null){
						    if (jQuery('#dir-container-<?php echo $token ?> .categories-form-field').length == 1) {
                                jQuery('#categories-container-<?php echo $token?>').after(data);
                            } else {
                                jQuery('#dir-container-<?php echo $token ?> .categories-form-field').last().after(data);
                            }

							<?php if($params->get('autocomplete')){?>
								if(document.getElementById('categories-'+(level+1))){
									jQuery("#dir-container-<?php echo $token ?> #categories-"+(level+1)).chosen();
								}
							<?php }?>
						}
					}
				});
			<?php } ?>

			if(parentId === undefined) 
				jQuery('#dir-container-<?php echo $token ?> #categorySearch').val(selectedCat);
			else
				jQuery('#dir-container-<?php echo $token ?> #categorySearch').val(id);
		}
    }

    function changeFormActionURL(submitForm){
        
        <?php if($params->get('useDynamicFormAction')){?>
            var urlFormActionRetriever = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=search.getSearchFormActionAjax')?>";
            jQuery.ajax({
                type: "GET",
                url: urlFormActionRetriever,
                data:{
                    category: jQuery('#dir-container-<?php echo $token ?> #categorySearch').val(),
                    country: jQuery("#dir-container-<?php echo $token ?> #countrySearch").val(),
                    city: jQuery("#dir-container-<?php echo $token ?> #citySearch").val(),
                    region: jQuery("#dir-container-<?php echo $token ?> #regionSearch").val()
                },
                dataType: 'json',
                success: function(data){
                    if(data){
                        jQuery("#dir-container-<?php echo $token ?> #keywordSearch").attr('action', data.data.url);

                        if(submitForm == true){
                            jQuery("#dir-container-<?php echo $token ?> #keywordSearch").submit();
                        }
                    }
                }
            });
        <?php } ?>
    }
    
    function disableEmptyFields() {
        //disable all empty fields to have a nice url
        jQuery('#companies-search').submit(function() {
            jQuery(':input[type=text]', this).each(function() {
                this.disabled = !(jQuery(this).val());
            });

            jQuery('#companies-search select').each(function() {
                if(!(jQuery(this).val()) || jQuery(this).val()==0 && !jQuery(this).hasClass('required')){
                    jQuery(this).attr('disabled', 'disabled');
                }
            });
        });
    }
    
    function setLocationSuggestion(city, region, country){
        jQuery("#dir-container-<?php echo $token ?> #regionSearch").val(region);
        jQuery("#dir-container-<?php echo $token ?> #citySearch").val(city);
        jQuery("#dir-container-<?php echo $token ?> #countrySearch").val(country);

        jQuery(".zipcode-<?php echo $token ?>").val(city);
    }

    <?php if ($params->get('overlayEffect')){ ?>
        jQuery(".listing-search-form .form-container").children().focusin(function() {
            jQuery(".search-background-overlay").addClass("overlay-active")
            jQuery(this).addClass("higher-index")
            jQuery("+ .search-dir-button", this).addClass("search-btn-index")
            jQuery(".dir-search-preferences-<?php echo $token ?>").addClass("highest-index")
        }).focusout(function() {
            jQuery(".search-background-overlay").removeClass("overlay-active")
            jQuery(this).removeClass("higher-index")
            jQuery("+ .search-dir-button", this).removeClass("search-btn-index")
            jQuery(".dir-search-preferences-<?php echo $token ?>").removeClass("highest-index")
        })
    <?php } ?>

</script>