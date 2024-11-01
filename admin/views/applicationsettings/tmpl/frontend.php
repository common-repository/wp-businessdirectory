<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<div class="app_tab" id="panel_5">

<div class="row panel_5_content">
    <div class="col-md-6 general-settings">
    	<fieldset class="form-horizontal">
    		<legend><?php echo JText::_('LNG_GENERAL'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="add_country_address-lbl" for="add_country_address" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ADD_COUNTRY_ADDRESS');?></strong><br/><?php echo JText::_('LNG_ADD_COUNTRY_ADDRESS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ADD_COUNTRY_ADDRESS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="add_country_address_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="add_country_address" id="add_country_address1" value="1" <?php echo $this->item->add_country_address==true? 'checked="checked"' :""?> />
                            <label class="btn" for="add_country_address1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="add_country_address" id="add_country_address0" value="0" <?php echo $this->item->add_country_address==false? 'checked="checked"' :""?> />
                            <label class="btn" for="add_country_address0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="address_format-lbl" for="address_format" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ADDRESS_FORMAT');?></strong><br/><?php echo JText::_('LNG_ADDRESS_FORMAT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ADDRESS_FORMAT'); ?></label></div>
                    <div class="controls">
                        <select name="address_format" id="address_format_fld" class="chosen-select" onchange="showCustom()">
                            <?php foreach( $this->item->addressFormats as $key=>$addressFormat){?>
                                <option value="<?php echo $key ?>" <?php echo $key == $this->item->address_format ? "selected":"" ; ?>><?php echo JText::_($addressFormat)  ?></option>
                            <?php } ?>
                        </select>
                        <div>
                            <div id="address-hint" class="dir-notice"></div>
                            <input style="display: none; width: 97%" type="text" id="custom_address" name="custom_address" value="">
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="show_grid_list_option-lbl" for="show_grid_list_option" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_GRID_LIST_OPTIONS');?></strong><br/><?php echo JText::_('LNG_SHOW_GRID_LIST_OPTIONS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_GRID_LIST_OPTIONS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_grid_list_option_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_grid_list_option" id="show_grid_list_option1" value="1" <?php echo $this->item->show_grid_list_option==true? 'checked="checked"' :""?> />
                            <label class="btn" for="show_grid_list_option1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_grid_list_option" id="show_grid_list_option0" value="0" <?php echo $this->item->show_grid_list_option==false? 'checked="checked"' :""?> />
                            <label class="btn" for="show_grid_list_option0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="business_cp_style-lbl" for="business_cp_style" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BUSINESS_CP_STYLE');?></strong><br/><?php echo JText::_('LNG_BUSINESS_CP_STYLE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_BUSINESS_CP_STYLE'); ?></label></div>
                    <div class="controls">
                        <fieldset id="business_cp_style_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="business_cp_style" id="business_cp_style1" value="1" <?php echo $this->item->business_cp_style==1? 'checked="checked"' :""?> />
                            <label class="btn" for="business_cp_style1"><?php echo JText::_('LNG_VERTICAL_STYLE_1')?></label>
                            <input type="radio"  name="business_cp_style" id="business_cp_style3" value="3" <?php echo $this->item->business_cp_style==3? 'checked="checked"' :""?> />
                            <label class="btn" for="business_cp_style3"><?php echo JText::_('LNG_VERTICAL_STYLE_2')?></label>
                            <input type="radio"  name="business_cp_style" id="business_cp_style2" value="2" <?php echo $this->item->business_cp_style==2? 'checked="checked"' :""?> />
                            <label class="btn" for="business_cp_style2"><?php echo JText::_('LNG_HORIZONTAL')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="user_cp_style-lbl" for="user_cp_style" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_USER_CP_STYLE');?></strong><br/><?php echo JText::_('LNG_USER_CP_STYLE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_USER_CP_STYLE'); ?></label></div>
                    <div class="controls">
                        <fieldset id="user_cp_style_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="user_cp_style" id="user_cp_style1" value="1" <?php echo $this->item->user_cp_style==1? 'checked="checked"' :""?> />
                            <label class="btn" for="user_cp_style1"><?php echo JText::_('LNG_VERTICAL')?></label>
                            <input type="radio"  name="user_cp_style" id="user_cp_style2" value="2" <?php echo $this->item->user_cp_style==2? 'checked="checked"' :""?> />
                            <label class="btn" for="user_cp_style2"><?php echo JText::_('LNG_HORIZONTAL')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="dir_list_limit-lbl" for="dir_list_limit" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DIR_LIST_LIMIT');?></strong><br/><?php echo JText::_('LNG_DIR_LIST_LIMIT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_DIR_LIST_LIMIT'); ?></label></div>
                    <div class="controls">
                        <select id='dir_list_limit' name='dir_list_limit'>
                            <option value="5" <?php echo $this->item->dir_list_limit==5? "selected" : ""?>>5</option>
                            <option value="10" <?php echo $this->item->dir_list_limit==10? "selected" : ""?>>10</option>
                            <option value="15" <?php echo $this->item->dir_list_limit==15? "selected" : ""?>>15</option>
                            <option value="20" <?php echo $this->item->dir_list_limit==20? "selected" : ""?>>20</option>
                            <option value="21" <?php echo $this->item->dir_list_limit==21? "selected" : ""?>>21</option>
                            <option value="24" <?php echo $this->item->dir_list_limit==24? "selected" : ""?>>24</option>
                            <option value="25" <?php echo $this->item->dir_list_limit==25? "selected" : ""?>>25</option>
                            <option value="30" <?php echo $this->item->dir_list_limit==30? "selected" : ""?>>30</option>
                            <option value="33" <?php echo $this->item->dir_list_limit==30? "selected" : ""?>>33</option>
                            <option value="50" <?php echo $this->item->dir_list_limit==50? "selected" : ""?>>50</option>
                            <option value="51" <?php echo $this->item->dir_list_limit==51? "selected" : ""?>>51</option>
                            <option value="100" <?php echo $this->item->dir_list_limit==100? "selected" : ""?>>100</option>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="metric-lbl" for="metric" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_METRIC');?></strong><br/><?php echo JText::_('LNG_METRIC_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_METRIC'); ?></label></div>
                    <div class="controls">
                        <fieldset id="metric_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="metric" id="metric1" value="1" <?php echo $this->item->metric==true? 'checked="checked"' :""?> />
                            <label class="btn" for="metric1"><?php echo JText::_('LNG_MILES')?></label>
                            <input type="radio"  name="metric" id="metric0" value="0" <?php echo $this->item->metric==false? 'checked="checked"' :""?> />
                            <label class="btn" for="metric0"><?php echo JText::_('LNG_KM')?></label>
                        </fieldset>
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label"><label id="show_alias" for="show_alias" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_ALIAS');?></strong><br/><?php echo JText::_('LNG_SHOW_ALIAS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_ALIAS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_alias_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_alias" id="show_alias1" value="1" <?php echo $this->item->show_alias==1? 'checked="checked"' :""?> />
                            <label class="btn" for="show_alias1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_alias" id="show_alias0" value="0" <?php echo $this->item->show_alias==0? 'checked="checked"' :""?> />
                            <label class="btn" for="show_alias0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="show_apply_discount" for="show_apply_discount" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_APPLY_DISCOUNT');?></strong><br/><?php echo JText::_('LNG_SHOW_APPLY_DISCOUNT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_APPLY_DISCOUNT'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_apply_discount_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_apply_discount" id="show_apply_discount1" value="1" <?php echo $this->item->show_apply_discount==1? 'checked="checked"' :""?> />
                            <label class="btn" for="show_apply_discount1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_apply_discount" id="show_apply_discount0" value="0" <?php echo $this->item->show_apply_discount==0? 'checked="checked"' :""?> />
                            <label class="btn" for="show_apply_discount0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="no_image-lbl" for="no_image" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_NO_IMAGE');?></strong><br/><?php echo JText::_('LNG_NO_IMAGE_DETAILS');?>" title=""><?php echo JText::_('LNG_NO_IMAGE'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <div class="jupload logo-jupload">
                            <div class="jupload-body">
                                <div class="jupload-files">
                                    <div class="jupload-files-img image-fit-contain" id="no_image-picture-preview">
							            <?php echo "<img  id='nosImg' src='".BD_PICTURES_PATH."/no_image.jpg'/>"; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="jupload-options">
                                <div class="jupload-options-btn jupload-actions">
                                    <label for="no_image-imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                </div>
                                <div class="">
                                    <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                </div>
                            </div>
                            <input type="text" name="no_image" style="visibility:hidden;height:1px; width:1px;" class="form-control validate[required]" value="<?php echo "/no_image.jpg" ?>" >
                            <div class="jupload-footer">
                                <fieldset>
                                    <input type="file" id="no_image-imageUploader" name="uploadfile" size="50">
                                </fieldset>                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="default_bg_listing-image-uploader-lbl" for="default_bg_listing-image-uploader" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DEFAULT_BG_LISTING');?></strong><br/><?php echo JText::_('LNG_DEFAULT_BG_LISTING_DESC');?>" title=""><?php echo JText::_('LNG_DEFAULT_BG_LISTING'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <div class="jupload logo-jupload">
                            <div class="jupload-body">
                                <div class="jupload-files">
                                    <div class="jupload-files-img image-fit-contain" id="default_bg_listing-picture-preview">
                                        <?php
                                        if (!empty($this->item->default_bg_listing)) {
                                            echo '<img  id="default_bg_listingImg" src="'.BD_PICTURES_PATH.$this->item->default_bg_listing.'"/>';
                                        }else{
                                            echo '<i class="la la-image"></i>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="jupload-options">
                                <div class="jupload-options-btn jupload-actions">
                                    <label for="default_bg_listing-imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                    <a name="" id="" class="" href=javascript:uploadInstance.removeImage('default_bg_listing-')" role="button"><i class="la la-trash"></i></a>
                                </div>
                                <div class="">
                                    <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                </div>
                            </div>
                            <input type="text" name="default_bg_listing" style="visibility:hidden;height:1px; width:1px;" id="default_bg_listing-imageLocation" class="form-control" value="<?php echo $this->item->default_bg_listing?>" >
                            <div class="jupload-footer">
                                <fieldset>
                                    <input type="file" id="default_bg_listing-imageUploader" name="uploadLogo" size="50">
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    	</fieldset>
    </div>
    <div class="col-md-6 general-settings">
    	<fieldset class="form-horizontal">
    		<legend><?php echo JText::_('LNG_MAP'); ?></legend>
            <div class="form-container">
    		<div class="control-group">
    			<div class="control-label"><label id="show_search_map-lbl" for="show_search_map" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_SEARCH_MAP');?></strong><br/><?php echo JText::_('LNG_SHOW_SEARCH_MAP_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_SEARCH_MAP'); ?></label></div>
    			<div class="controls">
    				<fieldset id="show_search_map_fld" class="radio btn-group btn-group-yesno">
    					<input type="radio"  name="show_search_map" id="show_search_map1" value="1" <?php echo $this->item->show_search_map==true? 'checked="checked"' :""?> />
    					<label class="btn" for="show_search_map1"><?php echo JText::_('LNG_YES')?></label> 
    					<input type="radio"  name="show_search_map" id="show_search_map0" value="0" <?php echo $this->item->show_search_map==false? 'checked="checked"' :""?> />
    					<label class="btn" for="show_search_map0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
    			</div>
    		</div>
            <div class="control-group">
                <div class="control-label"><label id="map_type-lbl" for="map_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAP_TYPE');?></strong><br/><?php echo JText::_('LNG_MAP_TYPE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAP_TYPE'); ?></label></div>
                <div class="controls d-flex justify-content-between">
                    <fieldset id="map_type_fld" class="btn-group btn-group-yesno">
                        <select name="map_type" id="map_type_select" class="chosen-select">
                            <option value="<?php echo MAP_TYPE_GOOGLE ?>" <?php echo $this->item->map_type == MAP_TYPE_GOOGLE ? 'selected' : '' ?>><?php echo JText::_('LNG_GOOGLE')?></option>
                            <option value="<?php echo MAP_TYPE_BING ?>" <?php echo $this->item->map_type == MAP_TYPE_BING ? 'selected' : '' ?>><?php echo JText::_('LNG_BING')?></option>
                            <option value="<?php echo MAP_TYPE_OSM ?>" <?php echo $this->item->map_type == MAP_TYPE_OSM ? 'selected' : '' ?>><?php echo JText::_('LNG_OPEN_STREET_MAP')?></option>
                        </select>
                    </fieldset>
                    <a class="btn btn-primary hasTooltip" href="javascript:void(0);" data-toggle="tooltip" data-original-title="<?php echo JText::_('LNG_ADDRESS_AUTOCOMPLETE_CONFIGURATION') ?>" onclick="showAutocompleteConfig()">
                        <i class="la la-cog"></i>
                    </a>
                </div>
            </div>

    		<div class="control-group google map-field">
    			<div class="control-label"><label id="google_map_key-lbl" for="google_map_key" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GOOGLE_MAP_KEY');?></strong><br/><?php echo JText::_('LNG_GOOGLE_MAP_KEY_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_GOOGLE_MAP_KEY"); ?></label></div>
    			<div class="controls">
    				<input type="text" id="google_map_key" name="google_map_key" maxlength="45" value="<?php echo $this->item->google_map_key ?>">
                </div>
    		</div>

    		<div class="control-group google map-field">
    			<div class="control-label"><label id="google_map_key-lbl" for="google_map_key_zipcode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GOOGLE_MAP_KEY');?></strong><br/><?php echo JText::_('LNG_GOOGLE_MAP_KEY_ZIPCODE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_GOOGLE_MAP_KEY_ZIPCODE"); ?></label></div>
    			<div class="controls">
    				<input type="text" id="google_map_key_zipcode" name="google_map_key_zipcode" maxlength="45" value="<?php echo $this->item->google_map_key_zipcode ?>">
                </div>
    		</div>

            <div class="control-group bing map-field">
                <div class="control-label"><label id="bing_map_key-lbl" for="bing_map_key" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BING_MAP_KEY');?></strong><br/><?php echo JText::_('LNG_BING_MAP_KEY_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_BING_MAP_KEY"); ?></label></div>
                <div class="controls">
                    <input type="text" id="bing_map_key" name="bing_map_key" maxlength="100" value="<?php echo $this->item->bing_map_key ?>">
                </div>
            </div>

            <div class="control-group osm map-field">
                <div class="control-label"><label id="clear_map_cache-lbl" for="clear_map_cache" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CLEAR_MAP_CACHE');?></strong><br/><?php echo JText::_('LNG_CLEAR_MAP_CACHE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_CLEAR_MAP_CACHE"); ?></label></div>
                <?php
                $disabled = "";
                if (!file_exists(JPATH_SITE.'/components/com_jbusinessdirectory/libraries/cache')) {
                    $disabled = "disabled";
                }
                ?>
                <div class="controls">
                    <a id="clear-osm-btn" href="javascript:void(0)" class="btn btn-info <?php echo $disabled ?>" onclick="clearOSMCache()"><?php echo JText::_('LNG_CLEAR') ?></a>
                    <img id="clear-osm-loading" style="display:none;width:10%;" class="loading" src='<?php echo BD_ASSETS_FOLDER_PATH."images/loader.gif"?>'>
                </div>
            </div>
            
            <div id="map-config" style="display:none;">
                <legend><?php echo JText::_('LNG_ADDRESS_AUTOCOMPLETE_CONFIGURATION') ?></legend>
                <?php foreach ($this->autocompleteConfig as $map => $configs) { ?>
                    <div id="map-config-<?php echo $map ?>" class="map-config mt-1 mb-3" style="display:none;">
                        <?php foreach ($configs as $field => $mappedFields) { ?>
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_('LNG_'.strtoupper($field)) ?> </label></div>
                                <div class="controls">
                                    <select class="chosen" multiple="true" name="config-<?php echo $map ?>-<?php echo $field ?>[]">
                                        <?php foreach ($this->autocompleteConfigOptions[$map] as $option) { 
                                            $selected = "";
                                            if (in_array($option, $mappedFields)) {
                                                $selected = "selected";
                                            }
                                            ?>
                                            <option value="<?php echo $option ?>" <?php echo $selected ?>><?php echo $option ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="enable_map_clustering-lbl" for="enable_map_clustering" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_MAP_CLUSTERING');?></strong><br/><?php echo JText::_('LNG_ENABLE_MAP_CLUSTERING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_MAP_CLUSTERING'); ?></label></div>
                <div class="controls">
                    <fieldset id="enable_map_clustering_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="enable_map_clustering" id="enable_map_clustering1" value="1" <?php echo $this->item->enable_map_clustering==true? 'checked="checked"' :""?> />
                        <label class="btn" for="enable_map_clustering1"><?php echo JText::_('LNG_YES')?></label>
                        <input type="radio"  name="enable_map_clustering" id="enable_map_clustering0" value="0" <?php echo $this->item->enable_map_clustering==false? 'checked="checked"' :""?> />
                        <label class="btn" for="enable_map_clustering0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>
    		<div class="control-group">
    			<div class="control-label"><label id="map_auto_show-lbl" for="map_auto_show" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAP_AUTO_SHOW');?></strong><br/><?php echo JText::_('LNG_MAP_AUTO_SHOW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAP_AUTO_SHOW'); ?></label></div>
    			<div class="controls">
    				<fieldset id="map_auto_show_fld" class="radio btn-group btn-group-yesno">
    					<input type="radio"  name="map_auto_show" id="map_auto_show1" value="1" <?php echo $this->item->map_auto_show==true? 'checked="checked"' :""?> />
    					<label class="btn" for="map_auto_show1"><?php echo JText::_('LNG_YES')?></label> 
    					<input type="radio"  name="map_auto_show" id="map_auto_show0" value="0" <?php echo $this->item->map_auto_show==false? 'checked="checked"' :""?> />
    					<label class="btn" for="map_auto_show0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
    			</div>
    		</div>
            <div class="control-group">
                <div class="control-label"><label id="enable_map_gdpr-lbl" for="enable_map_gdpr" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_MAP_GDPR');?></strong><br/><?php echo JText::_('LNG_ENABLE_MAP_GDPR_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_MAP_GDPR'); ?></label></div>
                <div class="controls">
                    <fieldset id="enable_map_gdpr_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="enable_map_gdpr" id="enable_map_gdpr1" value="1" <?php echo $this->item->enable_map_gdpr==true? 'checked="checked"' :""?> />
                        <label class="btn" for="enable_map_gdpr1"><?php echo JText::_('LNG_YES')?></label>
                        <input type="radio"  name="enable_map_gdpr" id="enable_map_gdpr0" value="0" <?php echo $this->item->enable_map_gdpr==false? 'checked="checked"' :""?> />
                        <label class="btn" for="enable_map_gdpr0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>
    		<div class="control-group">
    			<div class="control-label"><label id="map_latitude-lbl" for="map_latitude" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LATITUDE');?></strong><br/><?php echo JText::_('LNG_LATITUDE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_LATITUDE"); ?></label></div>
    			<div class="controls">
    				<input type="text" id="map_latitude" name="map_latitude" value="<?php echo $this->item->map_latitude ?>">
                </div>
    		</div>
    		<div class="control-group">
    			<div class="control-label"><label id="map_longitude-lbl" for="map_longitude" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LONGITUDE');?></strong><br/><?php echo JText::_('LNG_LONGITUDE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_LONGITUDE"); ?></label></div>
    			<div class="controls">
    				<input type="text" id="map_longitude" name="map_longitude" value="<?php echo $this->item->map_longitude ?>">
                </div>
    		</div>
    		<div class="control-group">
    			<div class="control-label"><label id="map_zoom-lbl" for="map_zoom" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ZOOM');?></strong><br/><?php echo JText::_('LNG_ZOOM_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_ZOOM"); ?></label></div>
    			<div class="controls">
    				<input type="text" id="map_zoom" name="map_zoom" value="<?php echo $this->item->map_zoom ?>">
                </div>
    		</div>
    		<div class="control-group">
    			<div class="control-label"><label id="map_enable_auto_locate-lbl" for="map_enable_auto_locate" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_AUTO_LOCATE');?></strong><br/><?php echo JText::_('LNG_ENABLE_AUTO_LOCATE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_AUTO_LOCATE'); ?></label></div>
    			<div class="controls">
    				<fieldset id="map_enable_auto_locate_fld" class="radio btn-group btn-group-yesno">
    					<input type="radio"  name="map_enable_auto_locate" id="map_enable_auto_locate1" value="1" <?php echo $this->item->map_enable_auto_locate==true? 'checked="checked"' :""?> />
    					<label class="btn" for="map_enable_auto_locate1"><?php echo JText::_('LNG_YES')?></label> 
    					<input type="radio"  name="map_enable_auto_locate" id="map_enable_auto_locate0" value="0" <?php echo $this->item->map_enable_auto_locate==false? 'checked="checked"' :""?> />
    					<label class="btn" for="map_enable_auto_locate0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
    			</div>
    		</div>
    		<div class="control-group">
    			<div class="control-label"><label id="map_apply_search-lbl" for="map_apply_search" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_APPLY_SEARCH');?></strong><br/><?php echo JText::_('LNG_APPLY_SEARCH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_APPLY_SEARCH'); ?></label></div>
    			<div class="controls">
    				<fieldset id="map_apply_search_fld" class="radio btn-group btn-group-yesno">
    					<input type="radio"  name="map_apply_search" id="map_apply_search1" value="1" <?php echo $this->item->map_apply_search==true? 'checked="checked"' :""?> />
    					<label class="btn" for="map_apply_search1"><?php echo JText::_('LNG_YES')?></label> 
    					<input type="radio"  name="map_apply_search" id="map_apply_search0" value="0" <?php echo $this->item->map_apply_search==false? 'checked="checked"' :""?> />
    					<label class="btn" for="map_apply_search0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
    			</div>
    		</div>
    
            <div class="control-group">
                <div class="control-label"><label id="show_custom_markers-lbl" for="show_custom_markers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CUSTOM_MARKERS');?></strong><br/><?php echo JText::_('LNG_SHOW_CUSTOM_MARKERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CUSTOM_MARKERS'); ?></label></div>
                <div class="controls">
                    <fieldset id="show_custom_markers_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="show_custom_markers" id="show_custom_markers1" value="1" <?php echo $this->item->show_custom_markers==true? 'checked="checked"' :""?> />
                        <label class="btn" for="show_custom_markers1"><?php echo JText::_('LNG_YES')?></label>
                        <input type="radio"  name="show_custom_markers" id="show_custom_markers0" value="0" <?php echo $this->item->show_custom_markers==false? 'checked="checked"' :""?> />
                        <label class="btn" for="show_custom_markers0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="map_info_box_style-lbl" for="map_info_box_style" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAP_INFO_BOX_STYLE');?></strong><br/><?php echo JText::_('LNG_MAP_INFO_BOX_STYLE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAP_INFO_BOX_STYLE'); ?></label></div>
                <div class="controls">
                    <fieldset id="map_info_box_style_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="map_info_box_style" id="map_info_box_style1" value="1" <?php echo $this->item->map_info_box_style==1? 'checked="checked"' :""?> />
                        <label class="btn" for="map_info_box_style1"><?php echo JText::_('LNG_STYLE_1')?></label>
                        <input type="radio"  name="map_info_box_style" id="map_info_box_style2" value="2" <?php echo $this->item->map_info_box_style==2? 'checked="checked"' :""?> />
                        <label class="btn" for="map_info_box_style2"><?php echo JText::_('LNG_STYLE_2')?></label>
                    </fieldset>
                </div>
            </div>
    		
    		<div class="control-group">
    			<div class="control-label"><label id="company_email-lbl" for="company_email" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LOGO');?></strong><br/><?php echo JText::_('LNG_MAP_MARKER_DETAILS');?>" title=""><?php echo JText::_('LNG_MAP_MARKER'); ?><span class="star">&nbsp;</span></label></div>
    			<div class="controls">
    				<div class="form-upload-elem">
    					<div class="form-upload">
                            <input type="text" name="map_marker" style="visibility:hidden;height:1px; width:1px;" id="mapimageLocation" class="form-control" value="<?php echo $this->item->map_marker?>" >
<!--                            <input type="hidden" name="map_marker" id="mapimageLocation" value="--><?php //echo $this->item->map_marker?><!--">-->
    						<input type="file" id="mapimageUploader" name="uploadfile" size="50">
    						<a href="javascript:uploadInstance.removeImage('map');"><?php echo JText::_("LNG_REMOVE")?></a>
    					</div>
    				</div>
    				<div class="picture-preview-settings" id="mappicture-preview">
    					<?php
    					      if(!empty($this->item->map_marker)) {
    							echo "<img  id='map-pciture' src='".BD_PICTURES_PATH.$this->item->map_marker."'/>";
    						}
    					?>
    				</div>
    			</div>
    		</div>

                <div class="control-group">
                    <div class="control-label"><label id="feature_map_marker-lbl" for="feature_map_marker" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_FEATURE_MAP_MARKER');?></strong><br/><?php echo JText::_('LNG_FEATURE_MAP_MARKER_DETAILS');?>" title=""><?php echo JText::_('LNG_FEATURE_MAP_MARKER'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <div class="form-upload-elem">
                            <div class="form-upload">
                                <input type="text" name="feature_map_marker" style="visibility:hidden;height:1px; width:1px;" id="fMarkerimageLocation" class="form-control" value="<?php echo $this->item->feature_map_marker?>" >
<!--                                <input type="hidden" name="feature_map_marker" id="fMarkerimageLocation" value="--><?php //echo $this->item->feature_map_marker?><!--">-->
                                <input type="file" id="fMarkerimageUploader" name="uploadfile" size="50">
                                <a href="javascript:uploadInstance.removeImage('fMarker');"><?php echo JText::_("LNG_REMOVE")?></a>
                            </div>
                        </div>
                        <div class="picture-preview-settings" id="fMarkerpicture-preview">
                            <?php
                            if(!empty($this->item->feature_map_marker)) {
                                echo "<img  id='fMarker-pciture' src='".BD_PICTURES_PATH.$this->item->feature_map_marker."'/>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="location_map_marker-lbl" for="location_map_marker" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LOCATION_MAP_MARKER');?></strong><br/><?php echo JText::_('LNG_LOCATION_MAP_MARKER_DETAILS');?>" title=""><?php echo JText::_('LNG_LOCATION_MAP_MARKER'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <div class="form-upload-elem">
                            <div class="form-upload">
                                <input type="text" name="location_map_marker" style="visibility:hidden;height:1px; width:1px;" id="locationMarkerimageLocation" class="form-control" value="<?php echo $this->item->location_map_marker?>" >
<!--                                <input type="hidden" name="location_map_marker" id="locationMarkerimageLocation" value="--><?php //echo $this->item->location_map_marker?><!--">-->
                                <input type="file" id="locationMarkerimageUploader" name="uploadfile" size="50">
                                <a href="javascript:uploadInstance.removeImage('locationMarker');"><?php echo JText::_("LNG_REMOVE")?></a>
                            </div>
                        </div>
                        <div class="picture-preview-settings" id="locationMarkerpicture-preview">
                            <?php
                            if(!empty($this->item->location_map_marker)) {
                                echo "<img  id='locationMarker-pciture' src='".BD_PICTURES_PATH.$this->item->location_map_marker."'/>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="marker_size-lbl" for="marker_size" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MARKER_SIZE');?></strong><br/><?php echo JText::_('LNG_MARKER_SIZE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_MARKER_SIZE"); ?></label></div>
                    <div class="controls col-md-2">
                        <span><?php echo JText::_('LNG_WIDTH') ?> (px):</span>
                        <input type="text" id="marker_size" name="marker_size_width" value="<?php echo isset($this->item->marker_size->width) ? $this->item->marker_size->width : '' ?>">
                    </div>
                    <div class="controls col-md-2">
                        <span><?php echo JText::_('LNG_HEIGHT') ?> (px):</span>
                        <input type="text" id="marker_size_height" name="marker_size_height" value="<?php echo isset($this->item->marker_size->height) ? $this->item->marker_size->height : '' ?>">
                    </div>
                </div>
                
            </div>
    	</fieldset>
    </div>
</div>

<div class="row">
    <div class="col-md-6  general-settings">
        <fieldset class="form-horizontal">
            <legend><?php echo JText::_('LNG_CATEGORIES'); ?></legend>
            <div class="form-container">
            <div class="control-group">
                <div class="control-label"><label id="category_view-lbl" for="category_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CATEGORIES_VIEW');?></strong><br/><?php echo JText::_('LNG_CATEGORIES_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CATEGORIES_VIEW'); ?></label></div>
                <div class="controls">
                    <select name="category_view" id="category_view_fld" class="chosen-select">
                        <?php foreach( $this->item->categoryViews as $key=>$categoryView){?>
                            <option value="<?php echo $key ?>" <?php echo $key == $this->item->category_view ? "selected":"" ; ?>><?php echo JText::_($categoryView)  ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"><label id="show_cat_description-lbl" for="show_cat_description" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CAT_DESCRIPTION');?></strong><br/><?php echo JText::_('LNG_SHOW_CAT_DESCRIPTION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CAT_DESCRIPTION'); ?></label></div>
                <div class="controls">
                    <fieldset id="show_cat_description_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="show_cat_description" id="show_cat_description1" value="1" <?php echo $this->item->show_cat_description==true? 'checked="checked"' :""?> />
                        <label class="btn" for="show_cat_description1"><?php echo JText::_('LNG_YES')?></label>
                        <input type="radio"  name="show_cat_description" id="show_cat_description0" value="0" <?php echo $this->item->show_cat_description==false? 'checked="checked"' :""?> />
                        <label class="btn" for="show_cat_description0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"><label id="listing_category_display-lbl" for="listing_category_display" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LISTING_CATEGORY_DISPLAY');?></strong><br/><?php echo JText::_('LNG_LISTING_CATEGORY_DISPLAY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LISTING_CATEGORY_DISPLAY'); ?></label></div>
                <div class="controls">
                    <fieldset id="listing_category_display_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="listing_category_display" id="listing_category_display1" value="1" <?php echo $this->item->listing_category_display==1? 'checked="checked"' :""?> />
                        <label class="btn" for="listing_category_display1"><?php echo JText::_('LNG_STANDARD')?></label>
                        <input type="radio"  name="listing_category_display" id="listing_category_display2" value="2" <?php echo $this->item->listing_category_display==2? 'checked="checked"' :""?> />
                        <label class="btn" for="listing_category_display2"><?php echo JText::_('LNG_LINKED')?></label>
                    </fieldset>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"><label id="max_categories-lbl" for="max_categories" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_CATEGORIES');?></strong><br/><?php echo JText::_('LNG_MAX_CATEGORIES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_CATEGORIES'); ?></label></div>
                <div class="controls">
                    <input type="text" size="40" maxlength="20"  id="max_categories" name="max_categories" value="<?php echo $this->item->max_categories?>">
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"><label id="show_total_business_count-lbl" for="show_total_business_count" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_TOTAL_BUSINESS_COUNT_INFO');?></strong><br/><?php echo JText::_('LNG_SHOW_TOTAL_BUSINESS_COUNT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_TOTAL_BUSINESS_COUNT'); ?></label></div>
                <div class="controls">
                    <fieldset id="show_total_business_count" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="show_total_business_count" id="show_total_business_count1" value="1" <?php echo $this->item->show_total_business_count==true? 'checked="checked"' :""?> />
                        <label class="btn" for="show_total_business_count1"><?php echo JText::_('LNG_YES')?></label>
                        <input type="radio"  name="show_total_business_count" id="show_total_business_count0" value="0" <?php echo $this->item->show_total_business_count==false? 'checked="checked"' :""?> />
                        <label class="btn" for="show_total_business_count0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="category_order-lbl" for="category_order" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CATEGORY_ORDER');?></strong><br/><?php echo JText::_('LNG_CATEGORY_ORDER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CATEGORY_ORDER'); ?></label></div>
                <div class="controls">
                    <select name="category_order" id="category_order_fld" class="chosen-select">
                        <?php
                        foreach($this->categoryOrderOptions as $option) {?>
                            <option value='<?php echo $option->value ?>' <?php echo $option->value == $this->item->category_order ? "selected":"" ; ?>> <?php echo $option->text ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                    <div class="control-label"><label id="enable_attribute_category-lbl" for="enable_attribute_category" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ATTRIBUTE_CATEGORY');?></strong><br/><?php echo JText::_('LNG_ENABLE_ATTRIBUTE_CATEGORY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_ATTRIBUTE_CATEGORY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_attribute_category_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_attribute_category" id="enable_attribute_category1" value="1" <?php echo $this->item->enable_attribute_category==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_attribute_category1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_attribute_category" id="enable_attribute_category0" value="0" <?php echo $this->item->enable_attribute_category==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_attribute_category0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="enable_criteria_category-lbl" for="enable_criteria_category" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_CRITERIA_CATEGORY');?></strong><br/><?php echo JText::_('LNG_ENABLE_CRITERIA_CATEGORY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_CRITERIA_CATEGORY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_criteria_category_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_criteria_category" id="enable_criteria_category1" value="1" <?php echo $this->item->enable_criteria_category==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_criteria_category1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_criteria_category" id="enable_criteria_category0" value="0" <?php echo $this->item->enable_criteria_category==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_criteria_category0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <?php if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) { ?>
        <div class="col-md-6  general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_TRIPS'); ?></legend>
                <div class="control-group">
                        <div class="control-label"><label id="trips_search_view_mode-lbl" for="trips_search_view_mode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TRIPS_SEARCH_VIEW_MODE');?></strong><br/><?php echo JText::_('LNG_TRIPS_SEARCH_VIEW_MODE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_TRIPS_SEARCH_VIEW_MODE'); ?></label></div>
                        <div class="controls">
                            <fieldset id="trips_search_view_mode_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="trips_search_view_mode" id="trips_search_view_mode1" value="1" <?php echo $this->item->trips_search_view_mode==1? 'checked="checked"' :""?> />
                                <label class="btn" for="trips_search_view_mode1"><?php echo JText::_('LNG_LIST')?></label>
                                <input type="radio"  name="trips_search_view_mode" id="trips_search_view_mode2" value="2" <?php echo $this->item->trips_search_view_mode==2? 'checked="checked"' :""?> />
                                <label class="btn" for="trips_search_view_mode2"><?php echo JText::_('LNG_GRID')?></label>
                            </fieldset>
                        </div>
                    </div>
            </fieldset>
        </div>
    <?php } ?>
</div>

</div>

<script>

	var appImgFolderPathMarker = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_MARKER?>&_path_type=1&_target=<?php echo urlencode(APP_PICTURES_PATH)?>';
    var noImageFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_MARKER?>&_path_type=1&no_image=1&_target=""';

    window.addEventListener('load', function() {
        uploadInstance.imageUploader(appImgFolder, appImgFolderPathMarker, "map");
        uploadInstance.imageUploader(appImgFolder, appImgFolderPathMarker, "fMarker");
        uploadInstance.imageUploader(appImgFolder, appImgFolderPathMarker, "locationMarker");
        uploadInstance.imageUploader("/", noImageFolderPath, "no_image-");

        var text = 'LNG_ADDRESES_FORMAT_TEXT_';
        var type = jQuery('#address_format_fld').val();
        var textHint = text + type;
        if (type == 8){
            jQuery('#custom_address').val('<?php echo $this->item->custom_address ?>');
            jQuery('#custom_address').show();
        }else{
            jQuery('#custom_address').val("");
            jQuery('#custom_address').hide();
        }
        jQuery('#address-hint').html(JBD.JText._(textHint));
        jQuery('#address_format_fld').change(function(){
            type = jQuery('#address_format_fld').val();
            textHint = text+type;
            jQuery('#address-hint').html(JBD.JText._(textHint));
            if (type == 8){
                jQuery('#custom_address').show();
            }else{
                jQuery('#custom_address').val("");
                jQuery('#custom_address').hide();
            }
        });

        showMapFields();
        jQuery('#map_type_select').change(function(){
            showMapFields();
        });
    });

    function showCustom()
    {
        var type = jQuery('#address_format_fld').val();
        if (type == 8) {
            jQuery('#custom_address').val('<?php echo ADDRESS_STREET_NUMBER . " " . ADDRESS_ADDRESS . "," . ADDRESS_AREA . "," . ADDRESS_CITY . " " . ADDRESS_POSTAL_CODE . "," . ADDRESS_REGION . "," . ADDRESS_PROVINCE . "," . ADDRESS_COUNTRY ?>');
            jQuery('#custom_address').show();
        }else{
            jQuery('#custom_address').val("");
            jQuery('#custom_address').hide();
        }
    }

    function showMapFields()
    {
        var type = jQuery('#map_type_select').val();

        jQuery('.map-field').hide();
        if (type == <?php echo MAP_TYPE_GOOGLE ?>) {
            jQuery('.google').show(500);
        } else if (type == <?php echo MAP_TYPE_BING ?>) {
            jQuery('.bing').show(500);
        } else if (type == <?php echo MAP_TYPE_OSM ?>) {
            jQuery('.osm').show(500);
        }

        // show correct autocomplete config fields
        jQuery('.map-config').hide();
        let mapValue = jQuery('#map_type_select').chosen().val();
        let mapType = "google";

        mapValue = parseInt(mapValue);

        switch (mapValue) {
            case JBDConstants.MAP_TYPE_GOOGLE:
                mapType = "google";
                break;
            case JBDConstants.MAP_TYPE_BING:
                mapType = "bing";
                break;
            case JBDConstants.MAP_TYPE_OSM:
                mapType = "openstreet";
                break;
        }

        jQuery('#map-config-'+mapType).show(500);
    }

    function clearOSMCache()
    {
        jQuery('#clear-osm-loading').show();
        let url = jbdUtils.getAjaxUrl('clearOSMCacheAjax', 'applicationsettings');
        jQuery.ajax({
            type:"GET",
            url: url,
            dataType: 'json',
            success: function(data) {
                jQuery('#clear-osm-loading').hide();
                if (data) {
                    jQuery('#clear-osm-btn').addClass('disabled');
                } else {
                    alert("<?php echo JText::_('LNG_SOMETHING_WENT_WRONG') ?>");
                }
            }
        });
    }

    function showAutocompleteConfig() {
        jQuery('#map-config').toggle(500);
    }

</script>
