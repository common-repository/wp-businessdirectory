<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::includeColorPicker();

$appSettings = JBusinessUtil::getApplicationSettings();
?>

<div class="app_tab" id="panel_6">
    <div class="row panel_6_content">
        <div class="col-md-12 general-settings">
            <fieldset class="form-horizontal">
                <div class="row">
                    <div class="col-md-6 general-settings">
                        <legend><?php echo JText::_('LNG_LISTING_SETTINGS'); ?></legend>
                        <div class="form-container">
                            <div class="control-group">
                                <div class="control-label"><label id="max_business-lbl" for="max_business" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_BUSINESS_LISTINGS_INFO');?></strong><br/><?php echo JText::_('LNG_MAX_BUSINESS_LISTINGS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_BUSINESS_LISTINGS'); ?></label></div>
                                <div class="controls">
                                    <input type="text" size=40 maxlength=20  id="max_business" name = "max_business" value="<?php echo $this->item->max_business?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="company_view-lbl" for="company_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_COMPANY_VIEW');?></strong><br/><?php echo JText::_('LNG_COMPANY_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_COMPANY_VIEW'); ?></label></div>
                                <div class="controls">
                                    <select name="company_view" id="company_view_fld" class="chosen-select">
                                        <?php  foreach( $this->item->companyViews as $key=>$companyView){?>
                                            <option value="<?php echo $key ?>" <?php echo $key == $this->item->company_view ? "selected":"" ; ?>><?php echo JText::_($companyView)  ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="listings_display_info-lbl" for="listings_display_info[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LISTINGS_DISPLAY_INFO');?></strong><br/><?php echo JText::_('LNG_LISTINGS_DISPLAY_INFO_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LISTINGS_DISPLAY_INFO'); ?></label></div>
                                <div class="controls">
                                    <select	id="listings_display_info_fld" name="listings_display_info" class="chzn-color">
                                    <?php
                                    foreach($this->listingsDisplayInfo as $info) {?>
                                        <option value='<?php echo $info->value ?>' <?php echo $info->value == $this->item->listings_display_info ? "selected":"" ; ?>> <?php echo $info->name ?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="max_description_length-lbl" for="max_description_length" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_DESCRIPTION_LENGTH');?></strong><br/><?php echo JText::_('LNG_MAX_DESCRIPTION_LENGTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_DESCRIPTION_LENGTH'); ?></label></div>
                                <div class="controls">
                                    <input type="text" size=40 maxlength=20  id="max_description_length" name = "max_description_length" value="<?php echo $this->item->max_description_length?>">
                                </div>
                            </div>     

                             <div class="control-group">
                                <div class="control-label"><label id="max_short_description_length-lbl" for="max_short_description_length" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_SHORT_DESCRIPTION_LENGTH');?></strong><br/><?php echo JText::_('LNG_MAX_SHORT_DESCRIPTION_LENGTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_SHORT_DESCRIPTION_LENGTH'); ?></label></div>
                                <div class="controls">
                                    <input type="text" size=40 maxlength=20  id="max_short_description_length" name = "max_short_description_length" value="<?php echo $this->item->max_short_description_length?>">
                                </div>
                            </div>            

                            <div class="control-group">
                                <div class="control-label"><label id="max_slogan_length-lbl" for="max_slogan_length" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_SLOGAN_LENGTH');?></strong><br/><?php echo JText::_('LNG_MAX_SLOGAN_LENGTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_SLOGAN_LENGTH'); ?></label></div>
                                <div class="controls">
                                    <input type="text" size=40 maxlength=20  id="max_slogan_length" name = "max_slogan_length" value="<?php echo $this->item->max_slogan_length?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="allow_business_view_style_change-lbl" for="allow_business_view_style_change" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BUSINESS_VIEW_CHANGE');?></strong><br/><?php echo JText::_('LNG_BUSINESS_VIEW_CHANGE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_BUSINESS_VIEW_CHANGE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="allow_business_view_style_change_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="allow_business_view_style_change" id="allow_business_view_style_change1" value="1" <?php echo $this->item->allow_business_view_style_change==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="allow_business_view_style_change1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="allow_business_view_style_change" id="allow_business_view_style_change0" value="0" <?php echo $this->item->allow_business_view_style_change==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="allow_business_view_style_change0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="open_business_website-lbl" for="open_business_website" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OPEN_BUSINESS_URL');?></strong><br/><?php echo JText::_('LNG_OPEN_BUSINESS_URL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_OPEN_BUSINESS_URL'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="open_business_website" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="open_business_website" id="open_business_website1" value="1" <?php echo $this->item->open_business_website==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="open_business_website1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="open_business_website" id="open_business_website0" value="0" <?php echo $this->item->open_business_website==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="open_business_website0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="open_listing_on_new_tab-lbl" for="open_listing_on_new_tab" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OPEN_LISTING_ON_NEW_TAB');?></strong><br/><?php echo JText::_('LNG_OPEN_LISTING_ON_NEW_TAB_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_OPEN_LISTING_ON_NEW_TAB'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="open_listing_on_new_tab" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="open_listing_on_new_tab" id="open_listing_on_new_tab1" value="1" <?php echo $this->item->open_listing_on_new_tab==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="open_listing_on_new_tab1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="open_listing_on_new_tab" id="open_listing_on_new_tab0" value="0" <?php echo $this->item->open_listing_on_new_tab==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="open_listing_on_new_tab0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="edit_form_mode-lbl" for="edit_form_mode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EDIT_FORM_MODE');?></strong><br/><?php echo JText::_('LNG_EDIT_FORM_MODE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_EDIT_FORM_MODE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="edit_form_mode_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="edit_form_mode" id="edit_form_mode1" value="1" <?php echo $this->item->edit_form_mode==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="edit_form_mode1"><?php echo JText::_('LNG_TABS')?></label>
                                        <input type="radio"  name="edit_form_mode" id="edit_form_mode2" value="2" <?php echo $this->item->edit_form_mode==2? 'checked="checked"' :""?> />
                                        <label class="btn" for="edit_form_mode2"><?php echo JText::_('LNG_ONE_PAGE')?></label>
                                        <input type="radio"  name="edit_form_mode" id="edit_form_mode3" value="3" <?php echo $this->item->edit_form_mode==3? 'checked="checked"' :""?> />
                                        <label class="btn" for="edit_form_mode3"><?php echo JText::_('LNG_SECTIONS')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_simple_form-lbl" for="enable_simple_form" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_SIMPLE_FORM');?></strong><br/><?php echo JText::_('LNG_ENABLE_SIMPLE_FORM_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_SIMPLE_FORM'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_simple_form_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_simple_form" id="enable_simple_form1" value="1" <?php echo $this->item->enable_simple_form==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_simple_form1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_simple_form" id="enable_simple_form0" value="0" <?php echo $this->item->enable_simple_form==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_simple_form0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="ssimple_form_fields-lbl" for="simple_form_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_SIMPLE_FORM_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_SIMPLE_FORM_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_SIMPLE_FORM_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <select	id="simple_form_fields[]" name="simple_form_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->item->simpleFormFields as $val=>$field) {
                                            $selected = "";
                                            if (!empty($this->item->simple_form_fields)) {
                                                if (in_array($val, $this->item->simple_form_fields))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $val ?>'<?php echo $selected ?>><?php echo JText::_($field) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="listing_auto_save-lbl" for="listing_auto_save" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LISTING_AUTO_SAVE');?></strong><br/><?php echo JText::_('LNG_LISTING_AUTO_SAVE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LISTING_AUTO_SAVE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="listing_auto_save_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="listing_auto_save" id="listing_auto_save1" value="1" <?php echo $this->item->listing_auto_save==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="listing_auto_save1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="listing_auto_save" id="listing_auto_save0" value="0" <?php echo $this->item->listing_auto_save==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="listing_auto_save0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group auto-save-interval" style="<?php echo $this->item->listing_auto_save == 0? "display:none" :"" ?>">
                                <div class="control-label">
                                    <label id="auto_save_interval-lbl" for="auto_save_interval" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_AUTO_SAVE_INTERVAL');?></strong><br/><?php echo JText::_('LNG_AUTO_SAVE_INTERVAL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_AUTO_SAVE_INTERVAL'); ?></label>
                                </div>

                                <div class="controls">
                                    <select name="auto_save_interval" id="auto_save_interval_fld" class="chosen-select">
                                        <?php foreach( $this->item->autoSaveIterval as $key=>$interval){?>
                                            <option value="<?php echo $interval ?>" <?php echo $interval == $this->item->auto_save_interval ? "selected":"" ; ?>><?php echo $key ?> min</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="allow_contribute-lbl" for="allow_contribute" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ALLOW_CONTRIBUTE');?></strong><br/><?php echo JText::_('LNG_ALLOW_CONTRIBUTE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ALLOW_CONTRIBUTE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="allow_contribute_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="allow_contribute" id="allow_contribute1" value="1" <?php echo $this->item->allow_contribute==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="allow_contribute1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="allow_contribute" id="allow_contribute0" value="0" <?php echo $this->item->allow_contribute==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="allow_contribute0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            
                            <div class="control-group">
                                <div class="control-label"><label id="social_profile-lbl" for="social_profile" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SOCIAL_PROFILE');?></strong><br/><?php echo JText::_('LNG_SOCIAL_PROFILE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SOCIAL_PROFILE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="social_profile_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="social_profile" id="social_profile1" value="0" <?php echo $this->item->social_profile==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="social_profile1"><?php echo JText::_('LNG_NONE')?></label>
                                        <input type="radio"  name="social_profile" id="social_profile2" value="1" <?php echo $this->item->social_profile==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="social_profile2"><?php echo JText::_('LNG_EASY_SOCIAL')?></label>
                                        <input type="radio"  name="social_profile" id="social_profile3" value="2" <?php echo $this->item->social_profile==2? 'checked="checked"' :""?> />
                                        <label class="btn" for="social_profile3"><?php echo JText::_('LNG_JOMSOCIAL')?></label>
                                        <input type="radio"  name="social_profile" id="social_profile4" value="3" <?php echo $this->item->social_profile==3? 'checked="checked"' :""?> />
                                        <label class="btn" for="social_profile4"><?php echo JText::_('LNG_COMMUNITY_BUILDER')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group" style="display:none">
                                <div class="control-label"><label data-original-title="<strong><?php echo JText::_('LNG_NR_IMAGES_SLIDE'); ?></strong><br />Enter the number of images per slide for business detail view slider" id="nr_images_slide-lbl" for="nr_images_slide" class="hasTooltip required" title=""><?php echo JText::_('LNG_NR_IMAGES_SLIDE'); ?></label></div>
                                <div class="controls">
                                    <input name="nr_images_slide" id="nr_images_slide" value="<?php echo $this->item->nr_images_slide?>" size="50" type="text">
                                </div>
                            </div>


                            <div class="control-group">
                                <div class="control-label"><label id="lock_custom_fields-lbl" for="lock_custom_fields" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LOCK_CUSTOM_FIELDS');?></strong><br/><?php echo JText::_('LNG_LOCK_CUSTOM_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LOCK_CUSTOM_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="lock_custom_fields" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="lock_custom_fields" id="lock_custom_fields1" value="1" <?php echo $this->item->lock_custom_fields==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="lock_custom_fields1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="lock_custom_fields" id="lock_custom_fields0" value="0" <?php echo $this->item->lock_custom_fields==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="lock_custom_fields0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="business_update_notification-lbl" for="business_update_notification" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BUSINESS_UPDATE_NOTIFICATION');?></strong><br/><?php echo JText::_('LNG_BUSINESS_UPDATE_NOTIFICATION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_BUSINESS_UPDATE_NOTIFICATION'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="business_update_notification_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="business_update_notification" id="business_update_notification1" value="1" <?php echo $this->item->business_update_notification==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="business_update_notification1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="business_update_notification" id="business_update_notification0" value="0" <?php echo $this->item->business_update_notification==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="business_update_notification0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group" style="display: none">
                                <div class="control-label"><label id="sms_domain-lbl" for="sms_domain" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SMS_DOMAIN');?></strong><br/><?php echo JText::_('LNG_SMS_DOMAIN_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SMS_DOMAIN'); ?></label></div>
                                <div class="controls">
                                    <input type="text" size=40 maxlength=20  id="sms_domain" name = "sms_domain" placeholder="<?php echo JText::_('LNG_SMS_DOMAIN');?>" value="<?php echo $this->item->sms_domain?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/packages.php')){ ?>
                        <div class="col-md-6 general-settings">
                            <legend><?php echo JText::_('LNG_PAYMENT_PLANS'); ?></legend>
                            <div class="form-container">
                                <div class="control-group">
                                    <div class="control-label"><label id="enable_packages-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_PACKAGES');?></strong><br/><?php echo JText::_('LNG_ENABLE_PACKAGES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_PACKAGES'); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="enable_packages_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="enable_packages" id="enable_packages1" value="1" <?php echo $this->item->enable_packages==true? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_packages1"><?php echo JText::_('LNG_YES')?></label>
                                            <input type="radio"  name="enable_packages" id="enable_packages0" value="0" <?php echo $this->item->enable_packages==false? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_packages0"><?php echo JText::_('LNG_NO')?></label>
                                        </fieldset>
                                        <div id="assign-packages" style="display:none">
                                            <span> <?php echo JText::_("LNG_UPDATE_COMPANIES_TO_PACKAGE") ?></span>
                                            <select name="package" class="inputbox input-medium">
                                                <option value="0"><?php echo JText::_("LNG_SELECT_PACKAGE") ?></option>
                                                <?php echo JHtml::_('select.options', $this->packageOptions, 'value', 'text',0);?>
                                            </select>
                                            <div class="dir-notice"><?php echo JText::_('LNG_PACKAGE_NOTICE')?></div>
                                        </div>
                                    </div>
                                </div>

                                <div id="package-settings" style="<?php echo $this->item->enable_packages == 0? "display:none" :"" ?>">
                                    <div class="control-group">
                                        <div class="control-label"><label id="display_attributes_packages-lbl" for="display_attributes_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DISPLAY_ATTRIBUTES_PACKAGES');?></strong><br/><?php echo JText::_('LNG_DISPLAY_ATTRIBUTES_PACKAGES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_DISPLAY_ATTRIBUTES_PACKAGES'); ?></label></div>
                                        <div class="controls">
                                            <fieldset id="display_attributes_packages_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="display_attributes_packages" id="display_attributes_packages1" value="1" <?php echo $this->item->display_attributes_packages==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="display_attributes_packages1"><?php echo JText::_('LNG_YES')?></label>
                                                <input type="radio"  name="display_attributes_packages" id="display_attributes_packages0" value="0" <?php echo $this->item->display_attributes_packages==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="display_attributes_packages0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="control-label"><label id="display_free_packages_bellow-lbl" for="display_free_packages_bellow" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DISPLAY_FREE_PACKAGES_BELLOW');?></strong><br/><?php echo JText::_('LNG_DISPLAY_FREE_PACKAGES_BELLOW_DESC');?>" title=""><?php echo JText::_('LNG_DISPLAY_FREE_PACKAGES_BELLOW'); ?></label></div>
                                        <div class="controls">
                                            <fieldset id="display_free_packages_bellow_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="display_free_packages_bellow" id="display_free_packages_bellow1" value="1" <?php echo $this->item->display_free_packages_bellow==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="display_free_packages_bellow1"><?php echo JText::_('LNG_YES')?></label>
                                                <input type="radio"  name="display_free_packages_bellow" id="display_free_packages_bellow0" value="0" <?php echo $this->item->display_free_packages_bellow==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="display_free_packages_bellow0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="control-label"><label id="display_packages_by_period-lbl" for="display_packages_by_period" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DISPLAY_PACKAGES_BY_PERIOD');?></strong><br/><?php echo JText::_('LNG_DISPLAY_PACKAGES_BY_PERIOD_DESC');?>" title=""><?php echo JText::_('LNG_DISPLAY_PACKAGES_BY_PERIOD'); ?></label></div>
                                        <div class="controls">
                                            <fieldset id="display_packages_by_period_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="display_packages_by_period" id="display_packages_by_period1" value="1" <?php echo $this->item->display_packages_by_period==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="display_packages_by_period1"><?php echo JText::_('LNG_YES')?></label>
                                                <input type="radio"  name="display_packages_by_period" id="display_packages_by_period0" value="0" <?php echo $this->item->display_packages_by_period==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="display_packages_by_period0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="control-group" style="display: none">
                                        <div class="control-label"><label id="edit_form_mode-lbl" for="edit_form_mode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_PACKAGE_FIXED_DATE');?></strong><br/><?php echo JText::_('LNG_PACKAGE_FIXED_DATE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_PACKAGE_FIXED_DATE'); ?></label></div>
                                        <div class="controls">
                                            <?php echo JHTML::_('calendar', $this->item->package_date, 'package_date', 'package_date', $appSettings->calendarFormat, array('style'=>'display:inline;','class'=>'form-control calendar-date', 'size'=>'10', 'maxlength'=>'10')); ?>
                                            <a id="send-payment-notifications" href="javascript:sendPayamentEmailNotifications()" class="btn btn-info mt-3"><?php echo JText::_('LNG_REMIND_SUBSCRIPTION') ?></a>
                                            <img id="send-payment-notifications-loading" style="display:none;width:10%;" class="loading" src='<?php echo BD_ASSETS_FOLDER_PATH."images/loader.gif"?>'>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="control-label"><label id="package_upgrade_banner-lbl" for="package_upgrade_banner" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_BANNER_UPGRADE_PACKAGE');?></strong><br/><?php echo JText::_('LNG_BANNER_UPGRADE_PACKAGE_DESC');?>" title=""><?php echo JText::_('LNG_SHOW_BANNER_UPGRADE_PACKAGE'); ?></label></div>
                                        <div class="controls">
                                            <fieldset id="package_upgrade_banner_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="package_upgrade_banner" id="package_upgrade_banner1" value="1" <?php echo $this->item->package_upgrade_banner==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="package_upgrade_banner1"><?php echo JText::_('LNG_YES')?></label>
                                                <input type="radio"  name="package_upgrade_banner" id="package_upgrade_banner0" value="0" <?php echo $this->item->package_upgrade_banner==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="package_upgrade_banner0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="control-label"><label id="direct_processing-lbl" for="direct_processing" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DIRECT_PROCESSING');?></strong><br/><?php echo JText::_('LNG_DIRECT_PROCESSING_DESCRIPTION');?>"title=""><?php echo JText::_('LNG_DIRECT_PROCESSING'); ?></label></div>
                                        <div class="controls">
                                            <fieldset id="direct_processing_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="direct_processing" id="direct_processing1" value="1" <?php echo $this->item->direct_processing==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="direct_processing1"><?php echo JText::_('LNG_YES')?></label>
                                                <input type="radio"  name="direct_processing" id="direct_processing0" value="0" <?php echo $this->item->direct_processing==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="direct_processing0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <?php if (JBusinessUtil::canAssignPaymentProcessor()) { ?>
                                        <div class="control-group">
                                            <div class="control-label"><label id="default_processor_types-lbl" for="default_processor_types[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_DEFAULT_PAYMENT_PROCESSORS');?></strong><br/><?php echo JText::_('LNG_SELECT_DEFAULT_PAYMENT_PROCESSORS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_DEFAULT_PAYMENT_PROCESSORS'); ?></label></div>
                                            <div class="controls">
                                                <select	id="default_processor_types" name="default_processor_types[]" data-placeholder="<?php echo JText::_("LNG_SELECT_PAYMENT_PROCESSOR") ?>" class="chzn-color" multiple>
                                                    <?php
                                                    foreach($this->item->defaultProcessors as $type => $name) {
                                                        $selected = "";
                                                        if (!empty($this->item->default_processor_types)) {
                                                            if (in_array($type, $this->item->default_processor_types))
                                                                $selected = "selected";
                                                        } ?>
                                                        <option value='<?php echo $type ?>' <?php echo $selected ?>> <?php echo $name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </fieldset>
            <fieldset class="form-horizontal">    
                <legend><?php echo JText::_('LNG_LISTING_FEATURES'); ?></legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-container">
                            <div class="control-group">
                                <div class="control-label"><label id="claim_business-lbl" for="claim_business" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_CLAIM_BUSINESS');?></strong><br/><?php echo JText::_('LNG_ENABLE_CLAIM_BUSINESS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_CLAIM_BUSINESS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="claim_business_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="claim_business" id="claim_business1" value="1" <?php echo $this->item->claim_business==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="claim_business1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="claim_business" id="claim_business0" value="0" <?php echo $this->item->claim_business==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="claim_business0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_claimed-lbl" for="show_claimed" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CLAIMED');?></strong><br/><?php echo JText::_('LNG_SHOW_CLAIMED_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CLAIMED'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_claimed_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_claimed" id="show_claimed1" value="1" <?php echo $this->item->show_claimed==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_claimed1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_claimed" id="show_claimed0" value="0" <?php echo $this->item->show_claimed==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_claimed0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_reporting-lbl" for="enable_reporting" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_REPORTING');?></strong><br/><?php echo JText::_('LNG_ENABLE_REPORTING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_REPORTING'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_reporting_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_reporting" id="enable_reporting1" value="1" <?php echo $this->item->enable_reporting==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_reporting1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_reporting" id="enable_reporting0" value="0" <?php echo $this->item->enable_reporting==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_reporting0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_details_user-lbl" for="show_details_user" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_DETAILS_ONLY_FOR_USERS_INFO');?></strong><br/><?php echo JText::_('LNG_SHOW_DETAILS_ONLY_FOR_USERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_DETAILS_ONLY_FOR_USERS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_details_user_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_details_user" id="show_details_user1" value="1" <?php echo $this->item->show_details_user==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_details_user1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_details_user" id="show_details_user0" value="0" <?php echo $this->item->show_details_user==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_details_user0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="view_count-lbl" for="show_view_count" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_COMPANY_VIEW_COUNT');?></strong><br/><?php echo JText::_('LNG_COMPANY_VIEW_COUNT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_COMPANY_VIEW_COUNT'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_view_count_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_view_count" id="enable_view_count1" value="1" <?php echo $this->item->show_view_count==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_view_count1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_view_count" id="enable_view_count0" value="0" <?php echo $this->item->show_view_count==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_view_count0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_open_status-lbl" for="show_open_status" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_OPEN_STATUS');?></strong><br/><?php echo JText::_('LNG_SHOW_OPEN_STATUS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_OPEN_STATUS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_open_status_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_open_status" id="enable_show_open_status1" value="1" <?php echo $this->item->show_open_status==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_show_open_status1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_open_status" id="enable_show_open_status0" value="0" <?php echo $this->item->show_open_status==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_show_open_status0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_email-lbl" for="show_email" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_EMAIL');?></strong><br/><?php echo JText::_('LNG_SHOW_EMAIL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_EMAIL'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_email_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_email" id="show_email1" value="1" <?php echo $this->item->show_email==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_email1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_email" id="show_email0" value="0" <?php echo $this->item->show_email==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_email0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_recommended-lbl" for="show_recommended" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_RECOMMENDED');?></strong><br/><?php echo JText::_('LNG_SHOW_RECOMMENDED_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_RECOMMENDED'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_recommended_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_recommended" id="show_recommended1" value="1" <?php echo $this->item->show_recommended==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_recommended1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_recommended" id="show_recommended0" value="0" <?php echo $this->item->show_recommended==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_recommended0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_price_list" for="enable_price_list" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_PRICE_LIST');?></strong><br/><?php echo JText::_('LNG_ENABLE_PRICE_LIST_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_PRICE_LIST'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_price_list_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_price_list" id="enable_price_list1" value="1" <?php echo $this->item->enable_price_list==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_price_list1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_price_list" id="enable_price_list0" value="0" <?php echo $this->item->enable_price_list==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_price_list0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group price-list-view" style="<?php echo $this->item->enable_price_list == 0? "display:none" :"" ?>">
                                <div class="control-label"><label id="price_list_view" for="price_list_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_PRICE_LIST_VIEW');?></strong><br/><?php echo JText::_('LNG_PRICE_LIST_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_PRICE_LIST_VIEW'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="price_list_view_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="price_list_view" id="price_list_view1" value="1" <?php echo $this->item->price_list_view==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="price_list_view1"><?php echo JText::_('LNG_LIST_MODE')?></label>
                                        <input type="radio"  name="price_list_view" id="price_list_view0" value="0" <?php echo $this->item->price_list_view==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="price_list_view0"><?php echo JText::_('LNG_GRID_MODE')?></label>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label id="show_contact_cards" for="show_contact_cards" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CONTACT_CARDS');?></strong><br/><?php echo JText::_('LNG_SHOW_CONTACT_CARDS_DESC');?>" title=""><?php echo JText::_('LNG_SHOW_CONTACT_CARDS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_contact_cards_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_contact_cards" id="show_contact_cards1" value="1" <?php echo $this->item->show_contact_cards==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_contact_cards1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_contact_cards" id="show_contact_cards0" value="0" <?php echo $this->item->show_contact_cards==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_contact_cards0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label id="enable_listing_editors" for="enable_listing_editors" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_LISTING_EDITORS');?></strong><br/><?php echo JText::_('LNG_ENABLE_LISTING_EDITORS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_LISTING_EDITORS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_listing_editors_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_listing_editors" id="enable_listing_editors1" value="1" <?php echo $this->item->enable_listing_editors==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_listing_editors1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_listing_editors" id="enable_listing_editors0" value="0" <?php echo $this->item->enable_listing_editors==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_listing_editors0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-container">
                            <div class="control-group">
                                <div class="control-label"><label id="enable_announcements" for="enable_announcements" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ANNOUNCEMENTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_ANNOUNCEMENTS_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_ANNOUNCEMENTS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_announcements_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_announcements" id="enable_announcements1" value="1" <?php echo $this->item->enable_announcements==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_announcements1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_announcements" id="enable_announcements0" value="0" <?php echo $this->item->enable_announcements==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_announcements0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_campaigns" for="enable_campaigns" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_CAMPAIGNS');?></strong><br/><?php echo JText::_('LNG_ENABLE_CAMPAIGNS_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_CAMPAIGNS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_campaigns_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_campaigns" id="enable_campaigns1" value="1" <?php echo $this->item->enable_campaigns==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_campaigns1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_campaigns" id="enable_campaigns0" value="0" <?php echo $this->item->enable_campaigns==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_campaigns0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <?php if (JBusinessUtil::isAppInstalled(JBD_APP_VIDEOS)) { ?>
                                <div class="control-group">
                                    <div class="control-label"><label id="enable_videos" for="enable_videos" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_VIDEOS');?></strong><br/><?php echo JText::_('LNG_ENABLE_VIDEOS_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_VIDEOS'); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="enable_videos_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="enable_videos" id="enable_videos1" value="1" <?php echo $this->item->enable_videos==true? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_videos1"><?php echo JText::_('LNG_YES')?></label>
                                            <input type="radio"  name="enable_videos" id="enable_videos0" value="0" <?php echo $this->item->enable_videos==false? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_videos0"><?php echo JText::_('LNG_NO')?></label>
                                        </fieldset>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_articles" for="enable_articles" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ARTICLES');?></strong><br/><?php echo JText::_('LNG_ENABLE_ARTICLES_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_ARTICLES'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_articles_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_articles" id="enable_articles1" value="1" <?php echo $this->item->enable_articles==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_articles1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_articles" id="enable_articles0" value="0" <?php echo $this->item->enable_articles==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_articles0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_linked_listings-lbl" for="enable_linked_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_LINKED_LISTINGS');?></strong><br/><?php echo JText::_('LNG_ENABLE_LINKED_LISTINGS_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_LINKED_LISTINGS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_linked_listings_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_linked_listings" id="enable_linked_listings1" value="1" <?php echo $this->item->enable_linked_listings==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_linked_listings1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_linked_listings" id="enable_linked_listings0" value="0" <?php echo $this->item->enable_linked_listings==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_linked_listings0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="type_allowed_registering-lbl" for="type_allowed_registering[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_LISTING_TYPE_ALLOWED_TO_REGISTER');?></strong><br/><?php echo JText::_('LNG_SELECT_LISTING_TYPE_ALLOWED_TO_REGISTER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_LISTING_TYPE_ALLOWED_TO_REGISTER'); ?></label></div>
                                <div class="controls">
                                    <select	id="type_allowed_registering[]" name="type_allowed_registering[]" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->typeAllowedRegistering as $type) {
                                            $selected = "";
                                            if (!empty($this->item->type_allowed_registering)) {
                                                if (in_array($type->value, $this->item->type_allowed_registering))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $type->value ?>' <?php echo $selected ?>> <?php echo $type->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_secondary_locations-lbl" for="show_secondary_locations" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_SECONDARY_LOCATIONS');?></strong><br/><?php echo JText::_('LNG_SHOW_SECONDARY_LOCATIONS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_SECONDARY_LOCATIONS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_secondary_locations_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_secondary_locations" id="show_secondary_locations1" value="1" <?php echo $this->item->show_secondary_locations==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_secondary_locations1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_secondary_locations" id="show_secondary_locations0" value="0" <?php echo $this->item->show_secondary_locations==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_secondary_locations0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group show-secondary-locations-search" style="<?php echo $this->item->show_secondary_locations == 0? "display:none" :"" ?>">
                                <div class="control-label"><label id="show_secondary_locations_search-lbl" for="show_secondary_locations_search" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_SECONDARY_LOCATIONS_SEARCH');?></strong><br/><?php echo JText::_('LNG_SHOW_SECONDARY_LOCATIONS_SEARCH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_SECONDARY_LOCATIONS_SEARCH'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_secondary_locations_search_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_secondary_locations_search" id="show_secondary_locations_search1" value="1" <?php echo $this->item->show_secondary_locations_search==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_secondary_locations_search1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_secondary_locations_search" id="show_secondary_locations_search0" value="0" <?php echo $this->item->show_secondary_locations_search==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_secondary_locations_search0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_link_following-lbl" for="enable_link_following" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_LINK_FOLLOWING');?></strong><br/><?php echo JText::_('LNG_ENABLE_LINK_FOLLOWING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_LINK_FOLLOWING'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_link_following_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_link_following" id="enable_link_following1" value="1" <?php echo $this->item->enable_link_following==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_link_following1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_link_following" id="enable_link_following0" value="0" <?php echo $this->item->enable_link_following==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_link_following0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <?php if(file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/companyservice.php')) { ?>
                                <div class="control-group">
                                    <div class="control-label"><label id="enable_services-lbl" for="enable_services" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_COMPANY_SERVICES');?></strong><br/><?php echo JText::_('LNG_ENABLE_COMPANY_SERVICES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_COMPANY_SERVICES'); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="enable_services" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="enable_services" id="enable_services1" value="1" <?php echo $this->item->enable_services==true? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_services1"><?php echo JText::_('LNG_YES')?></label>
                                            <input type="radio"  name="enable_services" id="enable_services0" value="0" <?php echo $this->item->enable_services==false? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_services0"><?php echo JText::_('LNG_NO')?></label>
                                        </fieldset>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="control-group">
                            <div class="control-label">
                                <label id="limit_cities_regions-lbl" for="limit_cities_regions" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LIMIT_CITIES_REGIONS');?></strong><br/><?php echo JText::_('LNG_LIMIT_CITIES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LIMIT_CITIES_REGIONS'); ?></label>
                            </div>
                            <div class="controls">
                                <fieldset id="limit_cities_regions_fld" class="radio btn-group btn-group-yesno">
                                    <input type="radio"  name="limit_cities_regions" id="limit_cities_regions1" value="1" <?php echo $this->item->limit_cities_regions == true ? 'checked="checked"' : "" ?> />
                                    <label class="btn" for="limit_cities_regions1"><?php echo JText::_('LNG_YES')?></label>
                                    <input type="radio"  name="limit_cities_regions" id="limit_cities_regions0" value="0" <?php echo $this->item->limit_cities_regions == false ? 'checked="checked"' : "" ?> />
                                    <label class="btn" for="limit_cities_regions0"><?php echo JText::_('LNG_NO')?></label>
                                </fieldset>
                            </div>
                        </div>

                        <div class="control-group cities-regions-order" style="<?php echo $this->item->limit_cities_regions == 0? "display:none" :"" ?>">
                            <div class="control-label"><label id="cities_regions_order-lbl" for="cities_regions_order" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CITIES_REGIONS_ORDER');?></strong><br/><?php echo JText::_('LNG_CITIES_REGIONS_ORDER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CITIES_REGIONS_ORDER'); ?></label></div>
                            <div class="controls">
                                <select name="cities_regions_order" id="cities_regions_order_fld" class="chosen-select">
                                    <?php
                                    foreach($this->citiesRegionsOrderOptions as $option) {?>
                                        <option value='<?php echo $option->value ?>' <?php echo $option->value == $this->item->cities_regions_order ? "selected":"" ; ?>> <?php echo $option->text ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group" style="display:none">
                            <div class="control-label">
                                <label id="enable_activity_cities-lbl" for="enable_activity_cities" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ACTIVITY_CITIES');?></strong><br/><?php echo JText::_('LNG_LIMIT_CITIES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_ACTIVITY_CITIES'); ?></label>
                            </div>
                            <div class="controls">
                                <fieldset id="enable_activity_cities_fld" class="radio btn-group btn-group-yesno">
                                    <input type="radio"  name="enable_activity_cities" id="enable_activity_cities1" value="1" <?php echo $this->item->enable_activity_cities == true ? 'checked="checked"' : "" ?> />
                                    <label class="btn" for="enable_activity_cities1"><?php echo JText::_('LNG_YES')?></label>
                                    <input type="radio"  name="enable_activity_cities" id="enable_activity_cities0" value="0" <?php echo $this->item->enable_activity_cities == false ? 'checked="checked"' : "" ?> />
                                    <label class="btn" for="enable_activity_cities0"><?php echo JText::_('LNG_NO')?></label>
                                </fieldset>
                            </div>
                        </div>

                        <div class="control-group" style="display:none">
                            <div class="control-label"><label id="max_activity_cities-lbl" for="max_activity_cities" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_ACTIVITY_CITIES');?></strong><br/><?php echo JText::_('LNG_MAX_ACTIVITY_CITIES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_ACTIVITY_CITIES'); ?></label></div>
                            <div class="controls">
                                <input type="text" size=40 maxlength=20  id="max_activity_cities" name = "max_activity_cities" value="<?php echo $this->item->max_activity_cities?>">
                            </div>
                        </div>

                        <div class="control-group" style="display:none">
                            <div class="control-label"><label id="max_activity_regions-lbl" for="max_activity_regions" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_ACTIVITY_REGIONS');?></strong><br/><?php echo JText::_('LNG_MAX_ACTIVITY_REGIONS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_ACTIVITY_REGIONS'); ?></label></div>
                            <div class="controls">
                                <input type="text" size=40 maxlength=20  id="max_activity_regions" name = "max_activity_regions" value="<?php echo $this->item->max_activity_regions?>">
                            </div>
                        </div>

                        <div class="control-group" style="display:none">
                            <div class="control-label"><label id="max_activity_countries-lbl" for="max_activity_countries" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_ACTIVITY_COUNTRIES');?></strong><br/><?php echo JText::_('LNG_MAX_ACTIVITY_COUNTRIES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_ACTIVITY_COUNTRIES'); ?></label></div>
                            <div class="controls">
                                <input type="text" size=40 maxlength=20  id="max_activity_countries" name = "max_activity_countries" value="<?php echo $this->item->max_activity_countries?>">
                            </div>
                        </div>

                        <div class="form-container" style="display:none">
                            <div class="control-group">
                                <div class="control-label"><label id="trail_weeks_dates-lbl" for="trail_weeks_dates" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TRAIL_WEEKS_DATES');?></strong><br/><?php echo JText::_('LNG_TRAIL_WEEKS_DATES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_TRAIL_WEEKS_DATES'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="trail_weeks_dates_fieldset">
                                    <div class="has-jicon-left">
                                        <input type='text' class="pr-0 inputbox calendar-date front-calendar" id="trailDates" autocomplete="off" placeholder="<?php echo JText::_("LNG_PICK_A_DATE")?>">
                                        <input type='hidden' name='trail_weeks_dates' id="trail_weeks_dates" value="<?php echo $this->item->trail_weeks_dates ?>">
                                        <i class="la la-calendar"></i>
                                        <a href="javascript:void(0)" onclick="resetTrailDates()"><?php echo JText::_('LNG_RESET') ?></a>
                                    </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_SEARCH'); ?></legend>
                <div class="row">
                    <div class="col-md-6 general-settings">
                        <div class="form-container">
                            <div class="control-group">
                                <div class="control-label"><label id="submit_method-lbl" for="submit_method" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SUBMIT_METHOD');?></strong><br/><?php echo JText::_('LNG_SUBMIT_METHOD_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SUBMIT_METHOD'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="submit_method_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="submit_method" id="submit_method1" value="post" <?php echo $this->item->submit_method=="post"? 'checked="checked"' :""?> />
                                        <label class="btn" for="submit_method1"><?php echo JText::_('LNG_POST')?></label>
                                        <input type="radio"  name="submit_method" id="submit_method2" value="get" <?php echo $this->item->submit_method=="get"? 'checked="checked"' :""?> />
                                        <label class="btn" for="submit_method2"><?php echo JText::_('LNG_GET')?></label>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="control-group" style="display:none">
                                <div class="control-label"><label id="enable_geolocation-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_GEOLOCATION');?></strong><br/><?php echo JText::_('LNG_ENABLE_GEOLOCATION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_GEOLOCATION'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_geolocation_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_geolocation" id="enable_geolocation1" value="1" <?php echo $this->item->enable_geolocation==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_geolocation1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_geolocation" id="enable_geolocation0" value="0" <?php echo $this->item->enable_geolocation==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_geolocation0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="country_ids-lbl" for="country_ids[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_ZIPCODE_COUNTRY');?></strong><br/><?php echo JText::_('LNG_SELECT_ZIPCODE_COUNTRY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_ZIPCODE_COUNTRY'); ?></label></div>
                                <div class="controls">
                                    <select	id="country_ids" name="country_ids[]" data-placeholder="<?php echo JText::_("LNG_SELECT_COUNTRY") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->item->countries as $country) {
                                            $selected = "";
                                            if (!empty($this->item->country_ids)) {
                                                if (in_array($country->id, $this->item->country_ids))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $country->id ?>' <?php echo $selected ?>> <?php echo $country->country_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="search_view_mode-lbl" for="search_view_mode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DEFAULT_SEARCH_VIEW');?></strong><br/><?php echo JText::_('LNG_DEFAULT_SEARCH_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_DEFAULT_SEARCH_VIEW'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="search_view_mode_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="search_view_mode" id="search_view_mode1" value="1" <?php echo $this->item->search_view_mode==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="search_view_mode1"><?php echo JText::_('LNG_GRID_MODE')?></label>
                                        <input type="radio"  name="search_view_mode" id="search_view_mode0" value="0" <?php echo $this->item->search_view_mode==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="search_view_mode0"><?php echo JText::_('LNG_LIST_MODE')?></label>
                                    </fieldset>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label"><label id="split_edit_form-lbl" for="split_edit_form" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SPLIT_EDIT_FORM');?></strong><br/><?php echo JText::_('LNG_SPLIT_EDIT_FORM_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SPLIT_EDIT_FORM'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="split_edit_form_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="split_edit_form" id="split_edit_form1" value="1" <?php echo $this->item->split_edit_form==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="split_edit_form1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="split_edit_form" id="split_edit_form0" value="0" <?php echo $this->item->split_edit_form==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="split_edit_form0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset> 
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_numbering-lbl" for="enable_numbering" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_NUMBERING');?></strong><br/><?php echo JText::_('LNG_ENABLE_NUMBERING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_NUMBERING'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_numbering_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_numbering" id="enable_numbering1" value="1" <?php echo $this->item->enable_numbering==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_numbering1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_numbering" id="enable_numbering0" value="0" <?php echo $this->item->enable_numbering==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_numbering0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label">
                                    <label id="search_result_view-lbl" for="search_result_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_RESULT_VIEW');?></strong><br/><?php echo JText::_('LNG_SEARCH_RESULT_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SEARCH_RESULT_VIEW'); ?></label>
                                </div>
                                <div class="controls">
                                    <select name="search_result_view" id="search_result_view_fld" class="chosen-select">
                                        <?php foreach( $this->item->searchResultViews as $key=>$searchResultView){?>
                                            <option value="<?php echo $key ?>" <?php echo $key == $this->item->search_result_view ? "selected":"" ; ?>><?php echo JText::_($searchResultView)  ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label id="search_result_grid_view-lbl" for="search_result_grid_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_RESULTS_GRID_VIEW');?></strong><br/><?php echo JText::_('LNG_SEARCH_RESULTS_GRID_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SEARCH_RESULTS_GRID_VIEW'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="search_result_grid_view_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="search_result_grid_view" id="search_result_grid_view1" value="1" <?php echo $this->item->search_result_grid_view==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="search_result_grid_view1"><?php echo JText::_('LNG_STYLE_1')?></label>
                                        <input type="radio"  name="search_result_grid_view" id="search_result_grid_view2" value="2" <?php echo $this->item->search_result_grid_view==2? 'checked="checked"' :""?> />
                                        <label class="btn" for="search_result_grid_view2"><?php echo JText::_('LNG_STYLE_2')?></label>
                                        <input type="radio"  name="search_result_grid_view" id="search_result_grid_view3" value="3" <?php echo $this->item->search_result_grid_view==3? 'checked="checked"' :""?> />
                                        <label class="btn" for="search_result_grid_view3"><?php echo JText::_('LNG_STYLE_3')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group" id="order_search_listings">
                                <div class="control-label"><label id="order_search_listings-lbl" for="order_search_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ORDER_SEARCH_LISTINGS');?></strong><br/><?php echo JText::_('LNG_ORDER_SEARCH_LISTINGS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ORDER_SEARCH_LISTINGS'); ?></label></div>
                                <div class="controls">
                                    <select name="order_search_listings" id="order_search_listings_fld" class="chosen-select">
                                        <?php foreach( $this->item->orderSearchListings as $key=>$orderSearchListing){?>
                                            <option value="<?php echo $key ?>" <?php echo $key == $this->item->order_search_listings ? "selected":"" ; ?>><?php echo JText::_($orderSearchListing)  ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group" id="second_order_search_listings">
                                <div class="control-label"><label id="second_order_search_listings-lbl" for="second_order_search_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SECOND_ORDER_SEARCH_LISTINGS');?></strong><br/><?php echo JText::_('LNG_SECOND_ORDER_SEARCH_LISTINGS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SECOND_ORDER_SEARCH_LISTINGS'); ?></label></div>
                                <div class="controls">
                                    <select name="second_order_search_listings" id="second_order_search_listings_fld" class="chosen-select">
                                        <?php foreach( $this->item->secondOrderSearchListings as $key=>$secondOrderSearchListings){?>
                                            <option value="<?php echo $key ?>" <?php echo $key == $this->item->second_order_search_listings ? "selected":"" ; ?>><?php echo JText::_($secondOrderSearchListings)  ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="show_custom_attributes-lbl" for="show_custom_attributes" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CUSTOM_ATTRIBUTES');?></strong><br/><?php echo JText::_('LNG_SHOW_CUSTOM_ATTRIBUTES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CUSTOM_ATTRIBUTES'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="show_custom_attributes_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="show_custom_attributes" id="show_custom_attributes1" value="1" <?php echo $this->item->show_custom_attributes==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_custom_attributes1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="show_custom_attributes" id="show_custom_attributes0" value="0" <?php echo $this->item->show_custom_attributes==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_custom_attributes0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="mix_results" for="mix_results" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MIX_RESULTS');?></strong><br/><?php echo JText::_('LNG_MIX_RESULTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MIX_RESULTS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="mix_results_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="mix_results" id="mix_results1" value="1" <?php echo $this->item->mix_results==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="mix_results1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="mix_results" id="mix_results0" value="0" <?php echo $this->item->mix_results==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="mix_results0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="listing_featured_bg-lbl" for="listing_featured_bg" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_FEATURED_LISTING_BG');?></strong><br/><?php echo JText::_('LNG_FEATURED_LISTING_BG');?>" title=""><?php echo JText::_('LNG_FEATURED_LISTING_BG'); ?></label></div>
                                <div class="controls">
                                    <input type="text" id="colorpicker" name="listing_featured_bg"  class="minicolors form-control hex" value="<?php echo $this->item->listing_featured_bg?>">
                                    <a href="javascript:clearColor()"><?php echo JText::_("LNG_CLEAR")?></a>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="redirect_to_listing" for="redirect_to_listing" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_REDIRECT_TO_LISTING');?></strong><br/><?php echo JText::_('LNG_REDIRECT_TO_LISTING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_REDIRECT_TO_LISTING'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="redirect_to_listing_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="redirect_to_listing" id="redirect_to_listing1" value="1" <?php echo $this->item->redirect_to_listing==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="redirect_to_listing1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="redirect_to_listing" id="redirect_to_listing0" value="0" <?php echo $this->item->redirect_to_listing==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="redirect_to_listing0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group search-redirect-url" style="<?php echo $this->item->redirect_to_listing == 0? "display:none" :"" ?>">
                                <div class="control-label"><label id="search_redirect_url-lbl" for="search_redirect_url" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_REDIRECT_URL');?></strong><br/><?php echo JText::_('LNG_SEARCH_REDIRECT_URL_DESC');?>" title=""><?php echo JText::_('LNG_SEARCH_REDIRECT_URL'); ?></label></div>
                                <div class="controls">
                                    <input type="text"  id="search_redirect_url" name="search_redirect_url"  class=" form-control" value="<?php echo $this->item->search_redirect_url?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 general-settings">
                        <div class="form-container">
                            <div class="control-group">
                                <div class="control-label"><label id="enable_search_filter-lbl" for="enable_search_filter" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_SEARCH_FILTER');?></strong><br/><?php echo JText::_('LNG_ENABLE_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_ENABLE_SEARCH_FILTER"); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_search_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_search_filter" id="enable_search_filter1" value="1" <?php echo $this->item->enable_search_filter==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_search_filter1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_search_filter" id="enable_search_filter0" value="0" <?php echo $this->item->enable_search_filter==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_search_filter0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>
                            <div id="search-filter-settings" style="<?php echo $this->item->enable_search_filter == 0? "display:none" :"" ?>">
                                <div class="control-group" style="display:none">
                                <?php $this->item->enable_advanced_search_filter = 0; ?>
                                    <div class="control-label"><label id="enable_advanced_search_filter-lbl" for="enable_advanced_search_filter" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ADVANCED_FILTER');?></strong><br/><?php echo JText::_('LNG_ENABLE_ADVANCED_FILTER_DESC');?>" title=""><?php echo JText::_("LNG_ENABLE_ADVANCED_FILTER"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="enable_search_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="enable_advanced_search_filter" id="enable_advanced_search_filter1" value="1" <?php echo $this->item->enable_advanced_search_filter==1? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_advanced_search_filter1"><?php echo JText::_('LNG_YES')?></label>
                                            <input type="radio"  name="enable_advanced_search_filter" id="enable_advanced_search_filter0" value="0" <?php echo $this->item->enable_advanced_search_filter==0? 'checked="checked"' :""?> />
                                            <label class="btn" for="enable_advanced_search_filter0"><?php echo JText::_('LNG_NO')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="search_type-lbl" for="search_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="search_type_fld" class="radio btn-group btn-group-yesno">
                                            <!--input type="radio"  name="search_type" id="search_type1" value="1" <?php echo $this->item->search_type==true? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_type1"><?php echo JText::_('LNG_FACETED')?></label-->
                                            <input type="radio"  name="search_type" id="search_type0" value="0" <?php echo $this->item->search_type==false ||  true? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_type0"><?php echo JText::_('LNG_FILTER_REGULAR')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="search_filter_type-lbl" for="search_filter_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_search_filter_type');?></strong><br/><?php echo JText::_('LNG_search_filter_type_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_search_filter_type"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="search_filter_type_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="search_filter_type" id="search_filter_type1" value="1" <?php echo $this->item->search_filter_type==1? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_filter_type1"><?php echo JText::_('LNG_HORIZONTAL')?></label>
                                            <input type="radio"  name="search_filter_type" id="search_filter_type2" value="2" <?php echo $this->item->search_filter_type==2? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_filter_type2"><?php echo JText::_('LNG_VERTICAL')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="show_top_filter-lbl" for="show_top_filter" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_TOP_FILTER');?></strong><br/><?php echo JText::_('LNG_SHOW_TOP_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SHOW_TOP_FILTER"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="enable_search_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="show_top_filter" id="show_top_filter1" value="1" <?php echo $this->item->show_top_filter==true? 'checked="checked"' :""?> />
                                            <label class="btn" for="show_top_filter1"><?php echo JText::_('LNG_YES')?></label>
                                            <input type="radio"  name="show_top_filter" id="show_top_filter0" value="0" <?php echo $this->item->show_top_filter==false? 'checked="checked"' :""?> />
                                            <label class="btn" for="show_top_filter0"><?php echo JText::_('LNG_NO')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="search_filter_view-lbl" for="search_filter_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER_VIEW');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER_VIEW"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="search_filter_view_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="search_filter_view" id="search_filter_view1" value="1" <?php echo $this->item->search_filter_view==1? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_filter_view1"><?php echo JText::_('LNG_STYLE_1')?></label>
                                            <input type="radio"  name="search_filter_view" id="search_filter_view2" value="2" <?php echo $this->item->search_filter_view==2? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_filter_view2"><?php echo JText::_('LNG_STYLE_2')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="search_filter_items-lbl" for="search_filter_items" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER_ITEM');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_ITEM_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SEARCH_FILTER_ITEM'); ?></label></div>
                                    <div class="controls">
                                        <input type="text" size=40 maxlength=20  id="search_filter_items" name="search_filter_items" value="<?php echo $this->item->search_filter_items?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="search_categories-lbl" for="search_categories[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_SEARCH_CATEGORIES');?></strong><br/><?php echo JText::_('LNG_SELECT_SEARCH_CATEGORIES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_SEARCH_CATEGORIES'); ?></label></div>
                                    <div class="controls">
                                        <select	id="search_categories[]" name="search_categories[]" data-placeholder="<?php echo JText::_("LNG_SELECT_CATEGORIES") ?>" class="chzn-color" multiple>
                                            <?php
                                            foreach( $this->mainCategoriesOptions as $cat) {
                                                $selected = "";
                                                if (!empty($this->item->search_categories)) {
                                                    if (in_array($cat->value, $this->item->search_categories))
                                                        $selected = "selected";
                                                } ?>
                                                <option value='<?php echo $cat->value ?>' <?php echo $selected ?>> <?php echo $cat->text ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"><label id="search-filter_fields-lbl" for="search-filter_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_FILTER_FIELDS'); ?></label></div>
                                    <div class="controls">
                                        <select	id="search_filter_fields[]" name="search_filter_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
                                            <?php
                                            foreach($this->searchFilterFields as $field) {
                                                $selected = "";
                                                if (!empty($this->item->search_filter_fields)) {
                                                    if (in_array($field->value, $this->item->search_filter_fields))
                                                        $selected = "selected";
                                                } ?>
                                                <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="search_fields-lbl" for="search_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_SEARCH_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_SEARCH_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_SEARCH_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <select	id="search_fields[]" name="search_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->searchFields as $field) {
                                            $selected = "";
                                            if (!empty($this->item->search_fields)) {
                                                if (in_array($field->value, $this->item->search_fields))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="order-by_fields-lbl" for="order-by_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_ORDER_BY_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_ORDER_BY_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_ORDER_BY_FIELDS'); ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?></label></div>
                                <div class="controls">
                                    <select	id="order_by_fields[]" name="order_by_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_ORDER_BY_FIELDS") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->orderByFields as $field) {
                                            $selected = "";
                                            if (!empty($this->item->order_by_fields)) {
                                                if (in_array($field->value, $this->item->order_by_fields))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                                            
                            <div class="control-group" style="display:none">
                                <div class="control-label"><label id="enable_search_letters-lbl" for="enable_search_letters" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_SEARCH_LETTERS');?></strong><br/><?php echo JText::_('LNG_ENABLE_SEARCH_LETTERS_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_ENABLE_SEARCH_LETTERS"); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_search_letters_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_search_letters" id="enable_search_letters1" value="1" <?php echo $this->item->enable_search_letters==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_search_letters1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_search_letters" id="enable_search_letters0" value="0" <?php echo $this->item->enable_search_letters==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_search_letters0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="zipcode_search_type-lbl" for="zipcode_search_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ZIPCODE_SEARCH_TYPE');?></strong><br/><?php echo JText::_('LNG_ZIPCODE_SEARCH_TYPE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ZIPCODE_SEARCH_TYPE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="zipcode_search_type_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="zipcode_search_type" id="zipcode_search_type1" value="1" <?php echo $this->item->zipcode_search_type==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="zipcode_search_type1"><?php echo JText::_('LNG_BY_BUSINESS_ACTIVITY_RADIUS')?></label>
                                        <input type="radio"  name="zipcode_search_type" id="zipcode_search_type0" value="0" <?php echo $this->item->zipcode_search_type==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="zipcode_search_type0"><?php echo JText::_('LNG_BY_DISTANCE')?></label>
                                        <input type="radio"  name="zipcode_search_type" id="zipcode_search_type2" value="2" <?php echo $this->item->zipcode_search_type==2? 'checked="checked"' :""?> />
                                        <label class="btn" for="zipcode_search_type2"><?php echo JText::_('LNG_EXACT')?></label>
                                        <!--input type="radio"  name="zipcode_search_type" id="zipcode_search_type3" value="3" <?php echo $this->item->zipcode_search_type==3? 'checked="checked"' :""?> />
                                        <label class="btn" for="zipcode_search_type3"><?php echo JText::_('LNG_BY_ACTIVITY_AREA')?></label-->
                                    </fieldset>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_REQUEST_QUOTES'); ?></legend>
                <div class="form-container">
                    <?php if (JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS)) { ?>
                        <div class="control-group">
                            <div class="control-label"><label id="enable_request_quote_app" for="enable_request_quote_app" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_REQUEST_QUOTE_APP');?></strong><br/><?php echo JText::_('LNG_ENABLE_REQUEST_QUOTE_APP_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_REQUEST_QUOTE_APP'); ?></label></div>
                            <div class="controls">
                                <fieldset id="enable_request_quote_app_fld" class="radio btn-group btn-group-yesno">
                                    <input type="radio" name="enable_request_quote_app" id="enable_request_quote_app1" value="1" <?php echo $this->item->enable_request_quote_app==true? 'checked="checked"' :""?> />
                                    <label class="btn" for="enable_request_quote_app1"><?php echo JText::_('LNG_YES')?></label>
                                    <input type="radio" name="enable_request_quote_app" id="enable_request_quote_app0" value="0" <?php echo $this->item->enable_request_quote_app==false? 'checked="checked"' :""?> />
                                    <label class="btn" for="enable_request_quote_app0"><?php echo JText::_('LNG_NO')?></label>
                                </fieldset>
                            </div>
                        </div>
                    
                        <div id="quote-request-app" style="<?php echo $this->item->enable_request_quote_app == 0? "display:none" :"" ?>">
                            <div class="control-group">
                                <div class="control-label"><label id="request_quote_radius-lbl" for="request_quote_radius" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_REQUEST_QUOTE_RADIUS');?></strong><br/><?php echo JText::_('LNG_REQUEST_QUOTE_RADIUS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_REQUEST_QUOTE_RADIUS'); ?></label></div>
                                <div class="controls">
                                    <input type="text" size=40 maxlength=20  id="request_quote_radius" name="request_quote_radius" value="<?php echo $this->item->request_quote_radius?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="quotes_search_filter_fields-lbl" for="quotes_search_filter_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_FILTER_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <select	id="quotes_search_filter_fields[]" name="quotes_search_filter_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->quotesFilterFields as $field) {
                                            $selected = "";
                                            if (!empty($this->item->quotes_search_filter_fields)) {
                                                if (in_array($field->value, $this->item->quotes_search_filter_fields))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="quote_request_type-lbl" for="quote_request_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_QUOTE_REQUEST_TYPE');?></strong><br/><?php echo JText::_('LNG_QUOTE_REQUEST_TYPE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_QUOTE_REQUEST_TYPE"); ?></label></div>
                                <div class="controls">
                                    <fieldset id="quote_request_type_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="quote_request_type" id="quote_request_type1" value="1" <?php echo $this->item->quote_request_type== QUOTE_LOCATION_SEARCH_FLEXIBLE ? 'checked="checked"' :""?> />
                                        <label class="btn" for="quote_request_type1"><?php echo JText::_('LNG_FLEXIBLE')?></label>
                                        <input type="radio"  name="quote_request_type" id="quote_request_type2" value="2" <?php echo $this->item->quote_request_type== QUOTE_LOCATION_SEARCH_EXACT ? 'checked="checked"' :""?> />
                                        <label class="btn" for="quote_request_type2"><?php echo JText::_('LNG_EXACT')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="enable_apply_with_price-lbl" for="enable_apply_with_price" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_APPLY_WITH_PRICE');?></strong><br/><?php echo JText::_('LNG_ENABLE_APPLY_WITH_PRICE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_APPLY_WITH_PRICE'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_apply_with_price" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_apply_with_price" id="enable_apply_with_price1" value="1" <?php echo $this->item->enable_apply_with_price==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_apply_with_price1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_apply_with_price" id="enable_apply_with_price0" value="0" <?php echo $this->item->enable_apply_with_price==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_apply_with_price0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><label id="quote_search_type-lbl" for="quote_search_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER"); ?></label></div>
                            <div class="controls">
                                <fieldset id="quote_search_type_fld" class="radio btn-group btn-group-yesno">
                                    <!-- <input type="radio"  name="quote_search_type" id="quote_search_type1" value="1" <?php echo $this->item->quote_search_type==true? 'checked="checked"' :""?> />
                                    <label class="btn" for="quote_search_type1"><?php echo JText::_('LNG_FACETED')?></label> -->
                                    <input type="radio"  name="quote_search_type" id="quote_search_type0" value="0" <?php echo $this->item->quote_search_type==false? 'checked="checked"' :""?> />
                                    <label class="btn" for="quote_search_type0"><?php echo JText::_('LNG_FILTER_REGULAR')?></label>
                                </fieldset>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="control-group">
                            <div class="control-label"><label id="enable_request_quote-lbl" for="enable_request_quote" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_REQUEST_QUOTE');?></strong><br/><?php echo JText::_('LNG_ENABLE_REQUEST_QUOTE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_REQUEST_QUOTE'); ?></label></div>
                            <div class="controls">
                                <fieldset id="enable_request_quote" class="radio btn-group btn-group-yesno">
                                    <input type="radio"  name="enable_request_quote" id="enable_request_quote1" value="1" <?php echo $this->item->enable_request_quote==true? 'checked="checked"' :""?> />
                                    <label class="btn" for="enable_request_quote1"><?php echo JText::_('LNG_YES')?></label>
                                    <input type="radio"  name="enable_request_quote" id="enable_request_quote0" value="0" <?php echo $this->item->enable_request_quote==false? 'checked="checked"' :""?> />
                                    <label class="btn" for="enable_request_quote0"><?php echo JText::_('LNG_NO')?></label>
                                </fieldset>
                            </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col-md-6 general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_PROJECTS'); ?></legend>

                <div class="control-group">
                    <div class="control-label"><label id="enable_projects" for="enable_projects" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_PROJECTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_PROJECTS_DESC');?>" title=""><?php echo JText::_('LNG_ENABLE_PROJECTS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_projects_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio" name="enable_projects" id="enable_projects1" value="1" <?php echo $this->item->enable_projects==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_projects1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio" name="enable_projects" id="enable_projects0" value="0" <?php echo $this->item->enable_projects==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_projects0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                
                <div id="project-settings" style="<?php echo $this->item->enable_projects == 0? "display:none" :"" ?>">
                    <div class="control-group">
                        <div class="control-label"><label id="projects_style-lbl" for="projects_style" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BUSINESS_PROJECTS_STYLE');?></strong><br/><?php echo JText::_('LNG_BUSINESS_PROJECTS_STYLE_DESC');?>" title=""><?php echo JText::_('LNG_BUSINESS_PROJECTS_STYLE'); ?></label></div>
                        <div class="controls">
                            <fieldset id="projects_style_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio" name="projects_style" id="projects_style1" value="1" <?php echo $this->item->projects_style == 1 ? 'checked="checked"' :""?> />
                                <label class="btn" for="projects_style1"><?php echo JText::_('LNG_STYLE_1')?></label>
                                <input type="radio" name="projects_style" id="projects_style2" value="2" <?php echo $this->item->projects_style == 2 ? 'checked="checked"' :""?> />
                                <label class="btn" for="projects_style2"><?php echo JText::_('LNG_STYLE_2')?></label>
                                <input type="radio" name="projects_style" id="projects_style3" value="3" <?php echo $this->item->projects_style == 3 ? 'checked="checked"' :""?> />
                                <label class="btn" for="projects_style3"><?php echo JText::_('LNG_STYLE_3')?></label>

                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="projects_show_images-lbl" for="projects_show_images" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BUSINESS_PROJECTS_SHOW_ADDITIONAL_IMAGES');?></strong><br/><?php echo JText::_('LNG_BUSINESS_PROJECTS_SHOW_ADDITIONAL_IMAGES_DESC');?>" title=""><?php echo JText::_('LNG_BUSINESS_PROJECTS_SHOW_ADDITIONAL_IMAGES'); ?></label></div>
                        <div class="controls">
                            <fieldset id="projects_show_images_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio" name="projects_show_images" id="projects_show_images0" value="0" <?php echo $this->item->projects_show_images == 0 ? 'checked="checked"' :""?> />
                                <label class="btn" for="projects_show_images0"><?php echo JText::_('LNG_NO')?></label>
                                <input type="radio" name="projects_show_images" id="projects_show_images1" value="1" <?php echo $this->item->projects_show_images == 1 ? 'checked="checked"' :""?> />
                                <label class="btn" for="projects_show_images1"><?php echo JText::_('LNG_YES')?></label>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        // Hide settings not taken into consideration
        jQuery("#limit_cities_regions1").click(function(){
            jQuery(".cities-regions-order").show(500);
        });
        jQuery("#limit_cities_regions0").click(function(){
            jQuery(".cities-regions-order").hide(500);
        });

        jQuery("#listing_auto_save1").click(function(){
            jQuery(".auto-save-interval").show(300);
        });
        jQuery("#listing_auto_save0").click(function(){
            jQuery(".auto-save-interval").hide(300);
        });

        jQuery("#enable_price_list1").click(function(){
            jQuery(".price-list-view").show(300);
        });
        jQuery("#enable_price_list0").click(function(){
            jQuery(".price-list-view").hide(300);
        });

        jQuery("#show_secondary_locations1").click(function(){
            jQuery(".show-secondary-locations-search").show(300);
        });
        jQuery("#show_secondary_locations0").click(function(){
            jQuery(".show-secondary-locations-search").hide(300);
        });

        jQuery("#redirect_to_listing1").click(function(){
            jQuery(".search-redirect-url").show(300);
        });
        jQuery("#redirect_to_listing0").click(function(){
            jQuery(".search-redirect-url").hide(300);
        });

        jQuery("#enable_request_quote_app1").click(function(){
            jQuery("#quote-request-app").show(300);
        });
        jQuery("#enable_request_quote_app0").click(function(){
            jQuery("#quote-request-app").hide(300);
        });

        jQuery("#enable_projects1").click(function(){
            jQuery("#project-settings").show(300);
        });
        jQuery("#enable_projects0").click(function(){
            jQuery("#project-settings").hide(300);
        });

        jQuery("#enable_packages1").click(function(){
            jQuery("#package-settings").show(300);
        });
        jQuery("#enable_packages0").click(function(){
            jQuery("#package-settings").hide(300);
        });

        jQuery("#enable_search_filter1").click(function(){
            jQuery("#search-filter-settings").show(300);
        });
        jQuery("#enable_search_filter0").click(function(){
            jQuery("#search-filter-settings").hide(300);
        });
    });

	function sendPayamentEmailNotifications(){
        jQuery('#send-payment-notifications-loading').show();
        let url = jbdUtils.getAjaxUrl('sendPayamentEmailNotificationsAjax', 'applicationsettings');
        jQuery.ajax({
            type:"GET",
            url: url,
            dataType: 'json',
            success: function(data) {
                jQuery('#send-payment-notifications-loading').hide();
                if (data) {
                    jQuery('#clear-osm-btn').addClass('disabled');
                } else {
                    alert("<?php echo JText::_('LNG_SOMETHING_WENT_WRONG') ?>");
                }
            }
        });
    }

    if(jQuery("#order_search_listings_fld").val()!="packageOrder desc"){
        jQuery("#second_order_search_listings").hide();
    }

    jQuery("#order_search_listings").click(function(){
        if(jQuery("#order_search_listings_fld").val()=="packageOrder desc"){
            jQuery("#second_order_search_listings").slideDown(500);
        } else {
            jQuery("#second_order_search_listings").hide();
        }
    });

</script>