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

<div class="app_tab" id="panel_3">

<div class="row panel_3_content">
    <div class="col-md-6 general-settings">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('LNG_SEO_SETTINGS'); ?></legend>
        <div class="form-container">
		<div class="control-group">
			<div class="control-label"><label id="enable_seo-lbl" for="enable_seo" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEO_MECHANISM');?></strong><br/><?php echo JText::_('LNG_SEO_MECHANISM');?>" title=""><?php echo JText::_('LNG_SEO_MECHANISM'); ?></label></div>
			<div class="controls">
				<fieldset id="enable_seo_fld" class="radio btn-group btn-group-yesno">
					<input type="radio" name="enable_seo" id="enable_seo1" value="1" <?php echo $this->item->enable_seo==true? 'checked="checked"' :""?> />
					<label class="btn" for="enable_seo1"><?php echo JText::_('LNG_USE_DIRECTORY')?></label> 
					<input type="radio" name="enable_seo" id="enable_seo0" value="0" <?php echo $this->item->enable_seo==false? 'checked="checked"' :""?> />
					<label class="btn" for="enable_seo0"><?php echo JText::_('LNG_USE_JOOMLA')?></label>
                </fieldset>
			</div>
		</div>
		
		
		<div class="directory-mechanism" style="<?php echo $this->item->enable_seo == 0? "display:none" :"" ?>">
		
			<div class="control-group">
				<div class="control-label"><label id="menu_item_id-lbl" for="menu_item_id" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MENU_ITEM_ID');?></strong><br/><?php echo JText::_('LNG_MENU_ITEM_ID_SEO');?>" title=""><?php echo JText::_('LNG_MENU_ITEM_ID'); ?></label></div>
				<div class="controls">
                    <input name="menu_item_id" id="menu_item_id" value="<?php echo $this->item->menu_item_id ?>" size="50" maxlength="10" type="text">
                </div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><label id="listing_url_type-lbl" for="listing_url_type_fld" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_URL_FIELDS');?></strong><br/><?php echo JText::_('LNG_URL_FIELDS');?>" title=""><?php echo JText::_('LNG_URL_FIELDS'); ?></label></div>
				<div class="controls">
					<select	id="url_fields" name="url_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
						<?php
    						foreach($this->urlFields as $field) {
    							$selected = "";
    							if (!empty($this->item->url_fields)) {
    							    if (in_array($field->value, $this->item->url_fields))
    									$selected = "selected";
    					} ?>
							<option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
						<?php } ?>
					</select>
                </div>
			</div>
			
			<div class="control-group" style="display:none">
				<div class="control-label"><label id="listing_url_type-lbl" for="listing_url_type_fld" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_URL_TYPE');?></strong><br/><?php echo JText::_('LNG_URL_TYPE_SEO');?>" title=""><?php echo JText::_('LNG_URL_TYPE'); ?></label></div>
				<div class="controls">
					<fieldset id="listing_url_type_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="listing_url_type" id="listing_url_type1" value="1" <?php echo $this->item->listing_url_type==1? 'checked="checked"' :""?> />
						<label class="btn" for="listing_url_type1"><?php echo JText::_('LNG_SIMPLE')?></label> 
						<input type="radio"  name="listing_url_type" id="listing_url_type2" value="2" <?php echo $this->item->listing_url_type==2? 'checked="checked"' :""?> />
						<label class="btn" for="listing_url_type2"><?php echo JText::_('LNG_CATEGORY')?></label> 
						<input type="radio"  name="listing_url_type" id="listing_url_type3" value="3" <?php echo $this->item->listing_url_type==3? 'checked="checked"' :""?> />
						<label class="btn" for="listing_url_type3"><?php echo JText::_('LNG_REGION')?></label> 
						<!-- input type="radio"  name="listing_url_type" id="listing_url_type4" value="4" <?php echo $this->item->listing_url_type==4? 'checked="checked"' :""?> />
						<label class="btn" for="listing_url_type4"><?php echo JText::_('LNG_PROVINCE')?> full</label--> 
						<input type="radio"  name="listing_url_type" id="listing_url_type5" value="5" <?php echo $this->item->listing_url_type==5? 'checked="checked"' :""?> >
						<label class="btn" for="listing_url_type5"><?php echo JText::_('LNG_PROVINCE')?></label> 
						<!-- input type="radio"  name="listing_url_type" id="listing_url_type6" value="6" <?php echo $this->item->listing_url_type==6? 'checked="checked"' :""?> />
						<label class="btn" for="listing_url_type6"><?php echo JText::_('LNG_COUNTRY')?></label-->
                    </fieldset>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="category_url_type-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CATEGORY_URL_TYPE');?></strong><br/><?php echo JText::_('LNG_CATEGORY_URL_TYPE_SEO');?>" title=""><?php echo JText::_('LNG_CATEGORY_URL_TYPE'); ?></label></div>
				<div class="controls">
					<fieldset id="category_url_type_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="category_url_type" id="category_url_type1" value="1" <?php echo $this->item->category_url_type==1? 'checked="checked"' :""?> />
						<label class="btn" for="category_url_type1"><?php echo JText::_('LNG_KEYWORD')?></label>
						<input type="radio"  name="category_url_type" id="category_url_type2" value="2" <?php echo $this->item->category_url_type==2? 'checked="checked"' :""?> />
						<label class="btn" for="category_url_type2"><?php echo JText::_('LNG_SIMPLE')?></label>
                    </fieldset>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="enable_menu_alias_url-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ADD_MENU_ALIAS_URL');?></strong><br/><?php echo JText::_('LNG_ADD_MENU_ALIAS_URL_SEO');?>" title=""><?php echo JText::_('LNG_ADD_MENU_ALIAS_URL'); ?></label></div>
				<div class="controls">
					<fieldset id="enable_menu_alias_url_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="enable_menu_alias_url" id="enable_menu_alias_url1" value="1" <?php echo $this->item->enable_menu_alias_url==1? 'checked="checked"' :""?> />
						<label class="btn" for="enable_menu_alias_url1"><?php echo JText::_('LNG_YES')?></label>
						<input type="radio"  name="enable_menu_alias_url" id="enable_menu_alias_url0" value="0" <?php echo $this->item->enable_menu_alias_url==0? 'checked="checked"' :""?> />
						<label class="btn" for="enable_menu_alias_url0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
				</div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><label id="url_menu_alias-lbl" for="url_menu_alias" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_URL_MENU_ALIAS_SEO');?></strong><br/><?php echo JText::_('LNG_URL_MENU_ALIAS_SEO');?>" title=""><?php echo JText::_('LNG_URL_MENU_ALIAS_SEO'); ?></label></div>
				<div class="controls">
                    <input name="url_menu_alias" id="url_menu_alias" value="<?php echo $this->item->url_menu_alias?>" size="50" maxlength="50" type="text">
                </div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><label id="add_url_id-lbl" for="add_url_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ADD_URL_ID');?></strong><br/><?php echo JText::_('LNG_ADD_URL_ID_SEO');?>" title=""><?php echo JText::_('LNG_ADD_URL_ID'); ?></label></div>
				<div class="controls">
					<fieldset id="add_url_id_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="add_url_id" id="add_url_id1" value="1" <?php echo $this->item->add_url_id==true? 'checked="checked"' :""?> />
						<label class="btn" for="add_url_id1"><?php echo JText::_('LNG_YES')?></label> 
						<input type="radio"  name="add_url_id" id="add_url_id0" value="0" <?php echo $this->item->add_url_id==false? 'checked="checked"' :""?> />
						<label class="btn" for="add_url_id0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label"><label id="add_url_language-lbl" for="add_url_language" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ADD_URL_LANGUAGE');?></strong><br/><?php echo JText::_('LNG_ADD_URL_LANGUAGE_SEO');?>" title=""><?php echo JText::_('LNG_ADD_URL_LANGUAGE'); ?></label></div>
				<div class="controls">
					<fieldset id="add_url_language_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="add_url_language" id="add_url_language1" value="1" <?php echo $this->item->add_url_language==true? 'checked="checked"' :""?> />
						<label class="btn" for="add_url_language1"><?php echo JText::_('LNG_YES')?></label> 
						<input type="radio"  name="add_url_language" id="add_url_language0" value="0" <?php echo $this->item->add_url_language==false? 'checked="checked"' :""?> />
						<label class="btn" for="add_url_language0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
				</div>
			</div>
		</div>
        </div>
	</fieldset>
    </div>

	<div class="col-md-6 general-settings">
		<fieldset class="form-horizontal">
		<legend><?php echo JText::_('LNG_URL_KEYWORDS'); ?></legend>
        <div class="form-container">
		<div class="directory-mechanism" style="<?php echo $this->item->enable_seo == 0? "display:none" :"" ?>">
			<div class="control-group">
				<div class="control-label"><label id="category_url_naming-lbl" for="category_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CATEGORY_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_CATEGORY_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_CATEGORY_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="category_url_naming" id="category_url_naming" value="<?php echo $this->item->category_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="offer_category_url_naming-lbl" for="offer_category_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_CATEGORY_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_OFFER_CATEGORY_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_OFFER_CATEGORY_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="offer_category_url_naming" id="offer_category_url_naming" value="<?php echo $this->item->offer_category_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="event_category_url_naming-lbl" for="event_category_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENT_CATEGORY_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_EVENT_CATEGORY_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_EVENT_CATEGORY_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="event_category_url_naming" id="event_category_url_naming" value="<?php echo $this->item->event_category_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="offer_url_naming-lbl" for="offer_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_OFFER_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_OFFER_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="offer_url_naming" id="offer_url_naming" value="<?php echo $this->item->offer_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="event_url_naming-lbl" for="event_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENT_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_EVENT_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_EVENT_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="event_url_naming" id="event_url_naming" value="<?php echo $this->item->event_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="city_url_naming-lbl" for="city_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CITY_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_CITY_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_CITY_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="city_url_naming" id="city_url_naming" value="<?php echo $this->item->city_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="region_url_naming-lbl" for="region_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_REGION_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_REGION_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_REGION_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="region_url_naming" id="region_url_naming" value="<?php echo $this->item->region_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="conference_url_naming-lbl" for="conference_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CONFERENCE_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_CONFERENCE_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_CONFERENCE_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="conference_url_naming" id="conference_url_naming" value="<?php echo $this->item->conference_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="conference_session_url_naming-lbl" for="conference_session_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CONFERENCE_SESSION_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_CONFERENCE_SESSION_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_CONFERENCE_SESSION_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="conference_session_url_naming" id="conference_session_url_naming" value="<?php echo $this->item->conference_session_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="speaker_url_naming-lbl" for="speaker_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SPEAKER_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_SPEAKER_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_SPEAKER_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="speaker_url_naming" id="speaker_url_naming" value="<?php echo $this->item->speaker_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><label id="video_url_naming-lbl" for="video_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_VIDEO_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_VIDEO_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_VIDEO_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="video_url_naming" id="video_url_naming" value="<?php echo $this->item->video_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>

			<div class="control-group">
				<div class="control-label"><label id="videos_url_naming-lbl" for="videos_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_VIDEOS_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_VIDEOS_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_VIDEOS_URL_NAMING'); ?></label></div>
				<div class="controls">
                    <input class="" name="videos_url_naming" id="videos_url_naming" value="<?php echo $this->item->videos_url_naming?>" size="50" maxlength="50" type="text">
                </div>
			</div>
			
			<?php if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) { ?>
				<div class="control-group">
					<div class="control-label"><label id="trips_url_naming-lbl" for="trips_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TRIPS_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_TRIPS_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_TRIPS_URL_NAMING'); ?></label></div>
					<div class="controls">
						<input class="" name="trips_url_naming" id="trips_url_naming" value="<?php echo $this->item->trips_url_naming?>" size="50" maxlength="50" type="text">
					</div>
				</div>

				<div class="control-group">
					<div class="control-label"><label id="trip_url_naming-lbl" for="trip_url_naming" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TRIP_URL_NAMING');?></strong><br/><?php echo JText::_('LNG_TRIP_URL_NAMING_DESC');?>" title=""><?php echo JText::_('LNG_TRIP_URL_NAMING'); ?></label></div>
					<div class="controls">
						<input class="" name="trip_url_naming" id="trip_url_naming" value="<?php echo $this->item->trip_url_naming?>" size="50" maxlength="50" type="text">
					</div>
				</div>
			<?php } ?>
			
		</div>
        </div>
	</fieldset>
    </div>
</div>

</div>

<script>
window.addEventListener('load', function() {
	jQuery("#enable_seo1").click(function(){
		jQuery(".directory-mechanism").slideDown(500);
	});

	jQuery("#enable_seo0").click(function(){
		jQuery(".directory-mechanism").slideUp(500);
	});
});
</script>
