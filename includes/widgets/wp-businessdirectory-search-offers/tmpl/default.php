<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$preserve = $params->get('preserve');

$minLetters = $params->get('minLetters');
$minLetters = is_numeric($minLetters)?$minLetters:3;
$token = rand(10, 1000);
JBusinessUtil::loadMapScripts();
$limitCountries = JBusinessUtil::getCountryRestriction();
$user = JBusinessUtil::getUser();

?>
<div class="search-background-overlay"></div>

<?php if(!empty($offers)){?>
    <div class="module-search-map">
    	<?php
    	if($params->get('showMap')) {
    		require JPATH_COMPONENT_SITE  ."/".'views'."/".'offers'."/".'tmpl'."/".'map.php';
    	}
    	?>
    </div>
<?php }?>

<?php if(!$params->get('showOnlyMap')) { ?>
<div id="companies-search" class="jbd-container business-directory<?php echo $moduleclass_sfx ?> <?php echo $layoutType?>" style="<?php echo $bgStyle ?>">
	<div id="dir-container-<?php echo $token ?>">
    	<div id="searchform" class="ui-tabs offer-search-form">
    			<?php $title = $params->get('title'); ?>
    			<?php if(!empty($title)){ ?>
    				<h1><?php echo $title ?></h1>
    			<?php } ?>
    			
    			<?php $description = $params->get('description'); ?>
    			<?php if(!empty($description)){ ?>
    				<p><?php echo $description ?></p>
    			<?php } ?>
    			
    			<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=offers'.$menuItemId) ?>" method="post" name="keywordSearch" id="keywordSearch" onsubmit="return checkSearch<?php echo $token ?>()">
    				<input type='hidden' name='preserve' value='<?php echo $preserve?>'>
    				<div class="form-container">
    					<?php if($params->get('showKeyword')){ ?>
    						<div class="form-field search">
    							<div class="has-jicon-left" id="search-field-container-<?php echo $token ?>">
    								<input class="search-field checkNumberChars-<?php echo $token?>" type="text" placeholder="<?php echo JText::_("LNG_SEARCH")?>" name="searchkeyword" id="searchkeyword" value="<?php echo $preserve?$session->get('of-searchkeyword'):"";?>" />
    								<i class="la la-search"></i>
    							</div>
    						</div>
    					<?php } ?>
    					
    					<?php if($params->get('showCategories')){ ?>
    					<div class="form-field">
    						<select name="categorySearch" id="categories">
    							<option value="0"><?php echo JText::_("LNG_ALL_CATEGORIES") ?></option>
    							<?php foreach($categories as $category){?>
    								<option value="<?php echo $category->id?>" <?php echo $session->get('of-categorySearch')==$category->id && $preserve?" selected ":"" ?> ><?php echo $category->name?></option>
    								<?php if(!empty($category->subcategories)){?>
    									<?php foreach($category->subcategories as $subCat){?>
    											<option value="<?php echo $subCat->id?>" <?php  echo $session->get('of-categorySearch')==$subCat->id && $preserve?" selected ":"" ?> >-- <?php echo $subCat->name?></option>
    									<?php }?>
    								<?php }?>
    							<?php }?>
    						</select>
    					</div>
    					<?php }?>

                        <?php if($params->get('showTypes')){ ?>
                            <div class="form-field">
                                <select name="typeSearch" id="typeSearch">
                                    <option value="0"><?php echo JText::_("LNG_ALL_TYPES") ?></option>
                                    <?php foreach($types as $type){?>
                                        <option value="<?php echo $type->id?>" <?php  echo $session->get('of-typeSearch')==$type->id && $preserve?" selected ":"" ?> ><?php echo $type->name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php }?>
    
    					<?php if($params->get('showZipcode')){ ?>
    						<div class="form-field zipcode-container" id="zipcode-container">
								<?php if($params->get('showLocationPopup')){ ?>
									<div id="dir-search-preferences" class="dir-search-preferences-<?php echo $token ?>" style="display:none">
										<h3 class="title"><?php echo JText::_("LNG_SEARCH_PREFERENCES")?><i class="la la-close right" onclick="jQuery('.dir-search-preferences-<?php echo $token ?>').hide()"></i></h3>
										<div class="geo-radius">
											<div><?php echo JText::_("LNG_RADIUS")?> (<?php echo $appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?>)</div>
										</div>
										<div>
											<input type="text" id="geo-location-radius-<?php echo $token ?>" name="radius" value="<?php echo !empty($radius)?$radius: "0" ?>">
										</div>
										<div class="geo-location">
											<?php echo JText::_("LNG_GEOLOCATION")?>
											<div id="loading-geo-locaiton" class="ui-autocomplete-loading" style="display:none"></div>
											<a id="enable-geolocation-<?php echo $token ?>" class="toggle btn-on <?php echo !empty($geoLocation)?"active":""?>" title="Grid" href="javascript:enableGeoLocation(<?php echo $token ?>)"><?php echo strtoupper(JText::_("LNG_GEO_ON")) ?></a>
											<a id="disable-geolocation-<?php echo $token ?>" class="toggle btn-off <?php echo empty($geoLocation)?"active":""?>" title="List" href="javascript:disableGeoLocation(<?php echo $token ?>)"><?php echo strtoupper(JText::_("LNG_GEO_OFF")) ?></a>
										</div>
									</div>
								<?php } else{ ?>
									<input type="hidden" name="radius" value="<?php echo !empty($radius)?$radius: "0" ?>">
								<?php } ?>
								
    							<div class="has-jicon-left" id="search-field-container-<?php echo $token ?>">
    								<input class="search-field zipcode-<?php echo $token ?>" placeholder="<?php echo JText::_("LNG_ZIPCODE")?>" type="text" name="zipcode" id="zipcode" value="<?php  echo $preserve?$session->get('of-zipcode'):"";?>" />
    								<i class="la la-map-marker"></i>
    							</div>
    						</div>
    					<?php } ?>

                        <?php if($params->get('showCountries')) { ?>
                            <div class="form-field">
                                <select name="countrySearch" id="countrySearch" <?php if($choices==1) { ?> onchange="getCitiesByCountry('<?php echo $token ?>');" <?php } else if($choices==2) { ?> onchange="getRegionsByCountry('<?php echo $token ?>');" <?php } ?>>
                                    <option value="0"><?php echo JText::_("LNG_ALL_COUNTRIES") ?></option>
                                    <?php foreach($countries as $country) { ?>
                                        <option value="<?php echo $country->id?>" <?php echo $session->get('of-countrySearch')==$country->id && $preserve?" selected ":"" ?> ><?php echo $country->country_name?></option>
                                    <?php }?>

                                </select>
                            </div>
                        <?php } ?>
                        
                        <?php if($params->get('showProvince')) { ?>
                            <div class="form-field">
                                <select name="provinceSearch" id="provinceSearch">
                                    <option value="0"><?php echo JText::_("LNG_ALL_PROVINCES") ?></option>
                                    <?php foreach($provinces as $province){?>
                                        <option value="<?php echo htmlspecialchars($province->province, ENT_QUOTES) ?>" <?php echo $session->get('of-provinceSearch')==$province->province && $preserve?" selected ":"" ?> ><?php echo $province->province?></option>
                                    <?php }?>
                                </select>
                            </div>
                        <?php } ?>
    					
    					<?php if($params->get('showRegions')){ ?>
    						<div class="form-field">
    							<select name="regionSearch" id="regionSearch" <?php if($choices && $params->get('showCities')) { ?> onchange="getCitiesByRegion('<?php echo $token ?>');" <?php } ?>>
    								<option value="0"><?php echo JText::_("LNG_ALL_REGIONS") ?></option>
    								<?php foreach($regions as $region){?>o
    									<option value="<?php echo $region->county?>" <?php echo $session->get('of-regionSearch')==$region->county && $preserve?" selected ":"" ?> ><?php echo $region->county?></option>
    								<?php }?>
    							</select>
    						</div>
    					<?php } ?>
    					
    					<?php if($params->get('showCities')){ ?>
    					<div class="form-field">
    						<select name="citySearch" id="citySearch">
    							<option value="0"><?php echo JText::_("LNG_ALL_CITIES") ?></option>
    							<?php foreach($cities as $city){?>
    								<option value="<?php echo htmlspecialchars($city->city, ENT_QUOTES) ?>" <?php echo $session->get('of-citySearch')==$city->city && $preserve?" selected ":"" ?> ><?php echo $city->city?></option>
    							<?php }?>
    						</select>
    					</div>
    					<?php } ?>
    					
    					<?php if($layoutType == "horizontal"){?>
        					<?php if($params->get('showMinPrice')){ ?>
        						<div class="form-field">
        							<input class="search-field" type="text" placeholder="<?php echo JText::_("LNG_MIN_PRICE")?>" name="minprice" id="minprice" value="<?php echo $preserve?$session->get('of-minprice'):"";?>" />
        						</div>
        					<?php } ?>
        
        					<?php if($params->get('showMaxPrice')){ ?>
        						<div class="form-field">
        							<input class="search-field" type="text" placeholder="<?php echo JText::_("LNG_MAX_PRICE")?>" name="maxprice" id="maxprice" value="<?php echo $preserve?$session->get('of-maxprice'):"";?>" />
        						</div>
        					<?php } ?>
    					<?php }else{ ?>
    						<?php if($params->get('showMinPrice') && $params->get('showMaxPrice')){ ?>
    							<div class="form-field">
    								<div>
        								<input type="hidden" name="price-range" id="price-range-<?php echo $token ?>" value="<?php echo $preserve?$session->get('of-price-range'):"";?>" />
        							</div>
        							<div class="clear"></div>
        						</div>
    						<?php } ?>
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
                                <i id="bookmark-icon-<?php echo $token ?>" class="bookmark-icon la <?php echo $session->get('of-filter-by-fav')?"la-heart":"la-heart-o" ?>"></i>
                                <input type="hidden" name="filter-by-fav" id="filter-by-fav"/>
                            </div>
                        <?php } ?>
    
    					<button type="submit" class="btn btn-success search-dir-button">
    						<i class="la la-search"></i>
    						<span class="ui-button-text"><?php echo JText::_("LNG_SEARCH")?></span>
    					</button>
    				</div>
    	
    				<input type="hidden" name="option" value="com_jbusinessdirectory">
    				<input type='hidden' name='view' value='offers'>
    				<input type="hidden" name="resetSearch" value="1">
    				<input type='hidden' name='preserve' value='<?php echo $preserve?>'>
    				<input type="hidden" name="geo-latitude" id="geo-latitude" value="">
    				<input type="hidden" name="geo-longitude" id="geo-longitude" value="">
    				<input type="hidden" name="geolocation" id="geolocation" value="<?php echo $geoLocation ?>">
                    <input type="hidden" name="suggestionType" id="suggestionType" value="">
                    <input type="hidden" name="itemType" id="itemType" value="<?php echo $params->get('itemType') ?>">
					<input type="hidden" name="categorySuggestion" id="categorySuggestion" value="">
                    <a  style="display:none" id="categories-link" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&controller=categories&view=categories&task=displaycategories') ?>"><?php echo JText::_("LNG_CATEGORY_LIST")?></a>
    			</form>
    	</div>
    </div>
	<div class="clear"></div>
</div>

<div id="login-notice" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INFO') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p>
                <?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
            </p>
            <p>
                <a href="<?php echo JBusinessUtil::getLoginUrl($url); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
            </p>
        </div>
    </div>
</div>

<?php } ?>

<script>
var minLetters = <?php echo $minLetters ?>;

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
		if(document.getElementById('searchkeyword') && document.getElementsByClassName('checkNumberChars-<?php echo $token ?>') && jQuery("#dir-container-<?php echo $token ?> .checkNumberChars-<?php echo $token ?>").val().length > 0 && minLetters!=0) {
            if(!checkNumberLetters<?php echo $token ?>()) {
            	alert('<?php echo addslashes(JText::plural('LNG_MIN_NUMBER_LETTERS_IS_N', $minLetters)) ?>');
                return false;
            }
        }
    <?php } ?>
	jQuery("#dir-container-<?php echo $token ?> #categories").removeClass("required");
	jQuery("#dir-container-<?php echo $token ?> #categories_chosen").removeClass("required");
	<?php if($params->get('mandatoryCategories')){ ?>
    	var foo = jQuery("#dir-container-<?php echo $token ?> #categories");
    	if (foo) {
    	   if (foo.find(":selected").index() == 0) {
				jQuery("#dir-container-<?php echo $token ?> #categories").focus();
				jQuery("#dir-container-<?php echo $token ?> #categories").addClass("required");
				jQuery("#dir-container-<?php echo $token ?> #categories_chosen").addClass("required");
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

    jQuery("#dir-container-<?php echo $token ?> #countrySearch").removeClass("required");
    jQuery("#dir-container-<?php echo $token ?> #countrySearch_chosen").removeClass("required");
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


    jQuery("#dir-container-<?php echo $token ?> #typeSearch").removeClass("required");
    jQuery("#dir-container-<?php echo $token ?> #typeSearch_chosen").removeClass("required");
    <?php if($params->get('mandatoryTypes')){ ?>
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

    <?php if($appSettings->offer_submit_method=="get"){?>
    disableEmptyFields();
    <?php } ?>

    //loading button animation if validation is OK
    jQuery("#dir-container-<?php echo $token ?> .search-dir-button").addClass("loader");

	return true;
}

function checkNumberLetters<?php echo $token ?>() {
    var keyword = jQuery("#dir-container-<?php echo $token ?> #searchkeyword").val();
    return keyword.length >= minLetters;
}

window.addEventListener('load', function(){

	<?php if($params->get('autocomplete')){?>
		jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
    	if(document.getElementById('categories')){
    		jQuery("#dir-container-<?php echo $token ?> #categories").chosen();
    	}
    	if(document.getElementById('citySearch')){
    		jQuery("#dir-container-<?php echo $token ?> #citySearch").chosen();
    	}
    	if(document.getElementById('regionSearch')){
    		jQuery("#dir-container-<?php echo $token ?> #regionSearch").chosen();
    	}
    	if(document.getElementById('countrySearch')){
    		jQuery("#dir-container-<?php echo $token ?> #countrySearch").chosen();
    	}
        if(document.getElementById('provinceSearch')){
            jQuery("#dir-container-<?php echo $token ?> #provinceSearch").chosen();
        }
    	if(document.getElementById('typeSearch')){
    		jQuery("#dir-container-<?php echo $token ?> #typeSearch").chosen();
    	}
	<?php } ?>

	<?php if ($params->get('enableKeywordSuggestion') && $params->get('showKeyword') && !$params->get('showOnlyMap')){ ?>
		jQuery("#dir-container-<?php echo $token ?> #searchkeyword").autocomplete({
			source: "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=categories.getCategories&type='.CATEGORY_TYPE_OFFER) ?>",
			minLength: 2,
            select: function (event, ui) {
                jQuery('#dir-container-<?php echo $token ?> #suggestionType').val(ui.item.type);
                jQuery('#dir-container-<?php echo $token ?> #categorySuggestion').val(ui.item.id);
                jQuery(this).val(ui.item.label);
                return false;
            }
		}).autocomplete().data("uiAutocomplete")._renderItem = function(ul, item) {
            return jQuery('<li>').append("<div><i class='"+item.class+"'></i> "+item.label+"</div>").appendTo(ul);
        };
	<?php } ?>


    <?php if($params->get('showMinPrice') && $params->get('showMaxPrice') && $layoutType == "vertical"){ ?>
         jQuery("#price-range-<?php echo $token ?>").ionRangeSlider({
        	 type: "double",
        	 grid: true,
        	 from: 200,
        	 to: 800,
        	 min: 0,
             max: <?php echo $maxPrice ?>,
             from: 0,
             to: <?php echo $maxPrice ?>
         });
     <?php } ?>

	jQuery(".zipcode-<?php echo $token ?>").focusin(function() {
		jQuery(".dir-search-preferences-<?php echo $token ?>").slideDown(500);
	});
	jQuery(".zipcode-<?php echo $token ?>").focusout(function() {
		//jQuery(".dir-search-preferences-<?php echo $token ?>").slideUp(500);
	});

	<?php if($params->get('showZipcode')){ ?>
		<?php if($params->get('showLocationPopup')){ ?>
			jQuery("#geo-location-radius-<?php echo $token ?>").ionRangeSlider({
				grid: true,
				min: 0,
				max: 500,
				from: <?php echo !empty($radius)?$radius: "0" ?>,
				to: 500,
			});
		<?php } ?>

		<?php if($params->get('enableAutosuggestion')) { ?>
			initializeSearchAutocomplete<?php echo $token ?>(true);
		<?php } ?>
		
    <?php } ?>

	<?php if($params->get('autolocation')){ ?>
     enableGeoLocation();
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

function enableGeoLocation(token){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setGeoLocation);
        jQuery("#loading-geo-locaiton-"+token).show();
        jQuery("#zipcode-container .la.la-map-marker").addClass("dir-beat-animation");
        jQuery("#enable-geolocation-<?php echo $token ?>").addClass("color-animation");
    }
    jQuery("#enable-geolocation-"+token).addClass("active");
    jQuery("#disable-geolocation-"+token).removeClass("active");
    jQuery("#geolocation").val(1);
}

function disableGeoLocation(token){
    jQuery("#enable-geolocation-"+token).removeClass("active");
    jQuery("#disable-geolocation-"+token).addClass("active");
    jQuery("#geolocation").val(0);
    jQuery("#loading-geo-locaiton-"+token).hide();
    jQuery("#zipcode-container .la.la-map-marker").removeClass("dir-beat-animation");
    jQuery("#enable-geolocation-<?php echo $token ?>").removeClass("color-animation");
    jQuery("#geo-latitude").val('');
    jQuery("#geo-longitude").val('');
}

function setGeoLocation(position){
    jQuery("#loading-geo-locaiton").hide();
    jQuery("#zipcode-container .la.la-map-marker").removeClass("dir-beat-animation");
    jQuery("#enable-geolocation-<?php echo $token ?>").removeClass("color-animation");
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
    jQuery("#geo-latitude").val(latitude);
    jQuery("#geo-longitude").val(longitude);
}

<?php if($params->get('showMap')){ ?>
// jbdMap.loadMapScript();
<?php }?>

<?php if($params->get('linklocation')) { ?>
    window.addEventListener("load", function () {
        <?php if($choices == 1) { ?>
        getCitiesByCountry();
        <?php } elseif($choices == 2) { ?>
        getRegionsByCountry();
        <?php } elseif($choices == 3) { ?>
        getCitiesByRegion();
        <?php } ?>
    });
<?php } ?>
<?php if ($params->get('overlayEffect')){ ?>
	jQuery(".offer-search-form").children().focusin(function() {
		jQuery(".search-background-overlay").addClass("overlay-active")
		jQuery(".offer-search-form").addClass("higher-index")
		jQuery(".dir-search-preferences-<?php echo $token ?>").addClass("highest-index")
	}).focusout(function() {
		jQuery(".search-background-overlay").removeClass("overlay-active")
		jQuery(".offer-search-form").removeClass("higher-index")
		jQuery(".dir-search-preferences-<?php echo $token ?>").removeClass("highest-index")
	});
<?php } ?>

</script>
