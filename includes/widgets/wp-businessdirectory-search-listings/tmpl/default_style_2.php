<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');


$preserve = $params->get('preserve');
$categorySearch = $session->get('categorySearch');
$minLetters = $params->get('minLetters');
$minLetters = is_numeric($minLetters)?$minLetters:3;
$token = rand(10, 1000);
JBusinessUtil::loadMapScripts();

$jinput = JFactory::getApplication()->input;
$searchkeyword = $jinput->getString('searchkeyword');
$suggestionType = $jinput->getInt('suggestionType');
$categorySuggestion = $jinput->getInt('categorySuggestion');

$regionSearch = $session->get('regionSearch');
$citySearch = $session->get('citySearch');
?>
<div class="search-background-overlay"></div>
<div class="module-search-map">
	<?php
		if($params->get('showMap')) {
			require JPATH_COMPONENT_SITE.'/include/search-map.php';
		}
	?>
</div>

<?php if(!$params->get('showOnlyMap')) { ?>

<div id="companies-search" class="business-directory<?php echo $moduleclass_sfx ?> <?php echo $layoutType?> search-style-2" style="<?php echo $bgStyle ?>">
	<div id="dir-container-<?php echo $token ?>">
    	<div id="searchform" class="ui-tabs listing-search-form">
    		<?php $title = $params->get('title'); ?>
    		<?php if(!empty($title)){ ?>
    			<h1><?php echo $title ?></h1><br/>
    		<?php } ?>
    
    		<?php $description = $params->get('description'); ?>
    		<?php if(!empty($description)){ ?>
    			<p><?php echo $description ?></p>
    		<?php } ?>
    
    		<form action="<?php echo $formAction ?>" method="<?php echo $appSettings->submit_method?>" name="keywordSearch" id="keywordSearch" onsubmit="return checkSearch<?php echo $token ?>()">
    			<div class="form-container">
    				<?php if($params->get('showKeyword')){ ?>
    					<div class="form-field search">
    						<div id="keyword-search-suggestion" class="search-suggestions" style="display:none">
    							<div class="scol-3">
        							<ul>
        								<?php $index = 1;?>
            							<?php foreach($categories as $category){?>
            								<li> <div onclick="setSuggestedCategory('<?php echo $category->name?>','<?php echo $category->id?>','<?php echo $token ?>')"><strong><?php echo $category->name?></strong></div></li>
                								<?php if(!empty($category->subcategories)){?>
                									<?php foreach($category->subcategories as $subCat){?>
            											<li> <div onclick="setSuggestedCategory('<?php echo $subCat->name?>','<?php echo $subCat->id?>','<?php echo $token ?>')"><?php echo $subCat->name?></div></li>
            											<?php if($index%20==0){?>
            												</ul>
            											</div>
            											<div class="scol-3">
        													<ul>
        											<?php } ?>
        											<?php $index++;?>
        										<?php }?>
            								<?php }?>
            							<?php }?>
        							</ul>
    							</div>
    						</div>
    						<input class="search-field checkNumberChars-<?php echo $token?>" type="text" placeholder="<?php echo JText::_("LNG_SEARCH_FOR")?>" name="searchkeyword" id="searchkeyword" value="<?php  echo $preserve?$searchkeyword:"";?>" />
    					</div>
    				<?php } ?>
    				<?php if($params->get('showKeyword') && false){ ?>
    					<div class="form-field" id="search-field-container-<?php echo $token ?>">
    						<i class="la la-map-marker"></i>
    						<input class="search-field" placeholder="<?php echo JText::_("LNG_LOCATION")?>" type="text" name="searchkeywordLocation" id="searchkeywordLocation" value="<?php  echo $preserve?$session->get('searchkeywordLocation'):"";?>" />
    					</div>
    				<?php } ?>
    			
                    <?php if($params->get('showZipcode')){ ?>
                            <div class="form-field zipcode-container" id="zipcode-container" style="display:block;">
                                <div id="location-search-suggestion" class="search-suggestions" style="display:none">
                                   <div class="scol-2">
            							<ul>
            								<?php $index = 1;?>
                							<?php foreach($regiongCities as $value=>$region){?>
                								<li class="region-suggestion"> <div onclick="setSuggestedRegion('<?php echo $value?>','<?php echo $value?>','<?php echo $token ?>')"><strong><?php echo $value?></strong></div></li>
                									<?php foreach($region as $city){?>
            											<li class="city-suggestion"> <div onclick="setSuggestedCity('<?php echo $city?>','<?php echo $value?>','<?php echo $token ?>')"><?php echo $city?></div></li>
            											<?php if($index%7==0){?>
                												</ul>
                											</div>
                											<div class="scol-2">
            													<ul>
            											<?php } ?>
  														<?php $index++;?>
                								<?php }?>
                							<?php }?>
            							</ul>
        							</div>
                                </div>
                                <input class="search-field zipcode-<?php echo $token ?>" placeholder="<?php echo JText::_("LNG_WHERE")?>" type="text" name="" id="zipcode" value="<?php  echo $preserve?$regionSearch.$citySearch:"";?>" />
                            </div>
                        <?php } ?>
    
    				<button type="submit" class="btn btn-success search-dir-button">
        				<i class="la la-search"></i> <?php echo JText::_("LNG_SEARCH")?>
        			</button>
    			</div>
    
    			<a style="display:none" id="categories-link" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&controller=categories&view=categories&task=displaycategories') ?>"><?php echo JText::_("LNG_CATEGORY_LIST")?></a>
    			<input type="hidden" name="option" value="com_jbusinessdirectory">
    			<input type="hidden" name="view" value="search">
    			<input type="hidden" name="resetSearch" value="1">
    			<input type="hidden" name="preserve" value="<?php echo $preserve?>">
    			<input type="hidden" name="geo-latitude" id="geo-latitude" value="<?php echo $preserve?$session->get('geo-latitude'):""; ?>">
    			<input type="hidden" name="geo-longitude" id="geo-longitude" value="<?php echo $preserve?$session->get('geo-longitude'):""; ?>">
                <input type="hidden" name="geo-country" id="geo-country" value="">
                <input type="hidden" name="location-detected" id="location-detected" value="<?php echo $preserve?$session->get('location-detected'):""; ?>">
                <input type="hidden" name="geolocation" id="geolocation" value="<?php echo $geoLocation ?>">
                <input type="hidden" name="categorySuggestion" id="categorySuggestion" value="<?php echo $categorySuggestion ?>">
    			<input type="hidden" name="categorySearch" id="categorySearch" value="<?php echo !empty($categorySearch)  && $preserve && $params->get('showCategories') ?$categorySearch:"" ?>">
    			<input type="hidden" name="suggestionType" id="suggestionType" value="<?php echo $suggestionType ?>">
    			<input type="hidden" name="regionSearch" id="regionSearch" value="<?php echo $regionSearch?>" >
    			<input type="hidden" name="citySearch" id="citySearch" value="<?php echo $citySearch?>" >
    		</form>
    	</div>
    </div>
	<div class="clear"></div>
</div>

<?php } ?>

<?php require_once JPATH_COMPONENT_SITE.'/include/bookmark_utils.php'; ?>

<script>
	var url = "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>";
	var minLetters = <?php echo $minLetters ?>;
	var keyword;

	function checkSearch<?php echo $token ?>(){
		jQuery("#dir-container-<?php echo $token ?> #searchkeyword").removeClass("required");
		<?php if($params->get('mandatoryKeyword')){ ?>
			if(document.getElementById('searchkeyword') && jQuery("#dir-container-<?php echo $token ?> #searchkeyword").val().length == 0){
				jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focus();
				jQuery("#dir-container-<?php echo $token ?> #searchkeyword").addClass("required");
				return false;
			}
		<?php } ?>

		if(document.getElementById('searchkeyword') && document.getElementsByClassName('checkNumberChars-<?php echo $token ?>') && jQuery("#dir-container-<?php echo $token ?> .checkNumberChars-<?php echo $token ?>").val().length > 0 && minLetters!=0) {
            if(!checkNumberLetters(<?php echo $token ?>)) {
            	alert('<?php echo addslashes(JText::plural('LNG_MIN_NUMBER_LETTERS_IS_N', $minLetters)) ?>');
                return false;
            }
        }

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

		<?php if($appSettings->submit_method=="get"){?>
      		disableEmptyFields();
        <?php } ?>

        //loading button animation if validation is OK
        jQuery("#dir-container-<?php echo $token ?> .search-dir-button").addClass("loader");
        
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
        jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});

        <?php if($params->get('autocomplete')){?>
			if(document.getElementById('categories-1')){
				jQuery("#dir-container-<?php echo $token ?> #categories-1").chosen();
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
    			source: "<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=categories.getCategories&type='.CATEGORY_TYPE_BUSINESS) ?>",
    			minLength: 0,
    			search: function( event, ui ) {
    				  jQuery("#dir-container-<?php echo $token ?> #keyword-search-suggestion").slideUp(500);
        			},
    			select: function( event, ui ) {
                    jQuery('#dir-container-<?php echo $token ?> #categorySuggestion').val(ui.item.id);
                    keyword = ui.item.label;
    				jQuery(this).val(ui.item.label);
    				return false;
    			}
    		});
    
            jQuery("#searchkeyword").change(function() {
                var searchKeyword = jQuery("#searchkeyword").val();
                if (searchKeyword != keyword){
                    jQuery('#categorySuggestion').val("");
                }
            });
		<?php } ?>

        jQuery(".zipcode-<?php echo $token ?>").change(function() {
        	jQuery('#dir-container-<?php echo $token ?> #regionSearch').val('');
    		jQuery('#dir-container-<?php echo $token ?> #citySearch').val('');
          
        });
	

        jQuery(".zipcode-<?php echo $token ?>").focusin(function() {
        	jQuery("#dir-container-<?php echo $token ?> #location-search-suggestion").slideDown(500);
        });
        jQuery(".zipcode-<?php echo $token ?>").focusout(function() {
        	jQuery("#dir-container-<?php echo $token ?> #location-search-suggestion").slideUp(500);
        });

        jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focusin(function() {
            jQuery("#dir-container-<?php echo $token ?> #keyword-search-suggestion").slideDown(500);
        });
        jQuery("#dir-container-<?php echo $token ?> #searchkeyword").focusout(function() {
            jQuery("#dir-container-<?php echo $token ?> #keyword-search-suggestion").slideUp(500);
        });

        <?php if($params->get('showZipcode')){ ?>
	        jQuery("#geo-location-radius-<?php echo $token ?>").ionRangeSlider({
	            grid: true,
	            min: 0,
	            max: <?php echo $maxRadius ?>,
	            from: <?php echo !empty($radius)?$radius: "0" ?>,
	            to: <?php echo $maxRadius ?>,
	        });
	
			<?php if($params->get('enableAutosuggestion') && !$params->get('showOnlyMap')) { ?>
	            initializeSearchAutocomplete<?php echo $token ?>(true);
	        <?php } ?>
        <?php } ?>
    });

    function initializeSearchAutocomplete<?php echo $token ?>(preventSubmit) {
    	jbdUtils.initializeSearchAutocomplete(<?php echo $token ?>, preventSubmit);
    }

    function disableEmptyFields() {
        //disable all empty fields to have a nice url
        jQuery('#dir-container-<?php echo $token ?>').submit(function() {
            jQuery(':input', this).each(function() {
                this.disabled = !(jQuery(this).val());
            });

            jQuery('#dir-container-<?php echo $token ?> select').each(function() {
                if(!(jQuery(this).val()) || jQuery(this).val()==0 && !jQuery(this).hasClass('required')){
                    jQuery(this).attr('disabled', 'disabled');
                }
            });
        });
    }

    function enableGeoLocation<?php echo $token ?>(){
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(setGeoLocation<?php echo $token ?>);
            jQuery("#loading-geo-locaiton").show();
            jQuery(".la la-bullseye").addClass("dir-beat-animation");
        }
        jQuery("#enable-geolocation-<?php echo $token ?>").addClass("active");
        jQuery("#disable-geolocation-<?php echo $token ?>").removeClass("active");
        jQuery("#dir-container-<?php echo $token ?> #geolocation").val(1);
    }

    function disableGeoLocation<?php echo $token ?>(){
        jQuery("#enable-geolocation-<?php echo $token ?>").removeClass("active");
        jQuery("#disable-geolocation-<?php echo $token ?>").addClass("active");
        jQuery("#dir-container-<?php echo $token ?> #geolocation").val(0);
        jQuery("#loading-geo-locaiton-"+token).hide();
        jQuery(".la la-bullseye").removeClass("dir-beat-animation");
        jQuery("#dir-container-<?php echo $token ?> #geo-latitude").val('');
        jQuery("#dir-container-<?php echo $token ?> #geo-longitude").val('');
        jQuery("#dir-container-<?php echo $token ?> #geo-country").val('');
    }

	function setGeoLocation<?php echo $token ?>(position){
		jQuery("#loading-geo-locaiton").hide();
		jQuery(".la la-bullseye").removeClass("dir-beat-animation");
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;
		jQuery("#dir-container-<?php echo $token ?> #geo-latitude").val(latitude);
		jQuery("#dir-container-<?php echo $token ?> #geo-longitude").val(longitude);
        codeLatLng(latitude,longitude);
	}

    function codeLatLng(lat, lng) {
	    var location = [];
	    location.latitude  = lat;
	    location.longitude = lng;

        var callback = function(country_code) {
            jQuery("#geo-country").val(country_code);
        };

        JBDMapHelper.reverseGeocoding(location, callback);
    }

	<?php if($params->get('autolocation')){ ?>
		enableGeoLocation<?php echo $token ?>();
	<?php }?>

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
			var urlRegionsByCountry = url+'&task=search.getRegionsByCountryAjax&countryId='+jQuery("#dir-container-"+token+" #countrySearch").val();
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
			var urlCitiesByRegion = url+'&task=search.getCitiesByRegionAjax&region='+jQuery("#dir-container-"+token+" #regionSearch").val();
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
			var urlCitiesByCountry = url+'&task=search.getCitiesByCountryAjax&countryId='+jQuery("#dir-container-"+token+" #countrySearch").val();
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

	function setSuggestedCategory(name, value){
		jQuery('#dir-container-<?php echo $token ?> #searchkeyword').val(name);
		jQuery('#dir-container-<?php echo $token ?> #categorySuggestion').val(value);
		jQuery('#dir-container-<?php echo $token ?> #suggestionType').val(<?php echo SUGGESTION_TYPE_CATEGORY ?>);
	}

	function setSuggestedRegion(name, value){
		jQuery('#dir-container-<?php echo $token ?> #regionSearch').val(name);
		jQuery('#dir-container-<?php echo $token ?> #citySearch').val("");
		jQuery('#dir-container-<?php echo $token ?> #zipcode').val(name);
	}

	function setSuggestedCity(name, value){
		jQuery('#dir-container-<?php echo $token ?> #citySearch').val(name);
		jQuery('#dir-container-<?php echo $token ?> #regionSearch').val("");
		jQuery('#dir-container-<?php echo $token ?> #zipcode').val(name);
	}

	<?php if ($params->get('setAutoFocus')){ ?>
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