<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

$options = array(
    'onActive' => 'function(title, description) {
    description.setStyle("display", "block");
    title.addClass("open").removeClass("closed");
}',
    'onBackground' => 'function(title, description) {
    description.setStyle("display", "none");
    title.addClass("closed").removeClass("open");
}',
    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
    'useCookie' => true, // this must not be a string. Don't use quotes.
);
$jbdTabs = new JBDTabs();
$jbdTabs->setOptions($options);

?>

<style>
    .accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 18px;
        width: 97%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
    }

    .accordion:hover {
        background-color: #ccc;
    }

    .accordion:after {
        content: '\2965';
        color: #777;
        font-weight: bold;
        float: right;
        margin-left: 5px;
    }

    .panel {
        padding: 0 18px;
        background-color: white;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.2s ease-out;
    }

    fieldset.form-horizontal{
        margin-bottom: 50px;
    }
</style>

<div class="app_tab" id="panel_1">
<div class="row panel_1_content">
	<div class="col-md-6 general-settings">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_GENERAL_SETTINGS'); ?></legend>
            <div class="form-container">
                <div class="control-group" style="display:none">
                    <div class="control-label"><label id="enable_cache-lbl" for="enable_cache" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_CACHE');?></strong><br/><?php echo JText::_('LNG_ENABLE_CACHE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_CACHE'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_cache_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_cache" id="enable_cache1" value="1" <?php echo $this->item->enable_cache==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_cache1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_cache" id="enable_cache0" value="0" <?php echo $this->item->enable_cache==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_cache0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="search_results_loading-lbl" for="search_results_loading" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_RESULTS_LOADING');?></strong><br/><?php echo JText::_('LNG_SEARCH_RESULTS_LOADING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SEARCH_RESULTS_LOADING'); ?></label></div>
                    <div class="controls">
                        <fieldset id="search_results_loading_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="search_results_loading" id="search_results_loading1" value="1" <?php echo $this->item->search_results_loading==1? 'checked="checked"' :""?> />
                            <label class="btn" for="search_results_loading1"><?php echo JText::_('LNG_DYNAMIC')?> (AJAX)</label>
                            <input type="radio"  name="search_results_loading" id="search_results_loading0" value="0" <?php echo $this->item->search_results_loading==0? 'checked="checked"' :""?> />
                            <label class="btn" for="search_results_loading0"><?php echo JText::_('LNG_STATIC')?> (PHP)</label>
                        </fieldset> 
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="item_decouple-lbl" for="item_decouple" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ITEM_DECOUPLE');?></strong><br/><?php echo JText::_('LNG_ITEM_DECOUPLE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ITEM_DECOUPLE'); ?></label></div>
                    <div class="controls">
                        <fieldset id="item_decouple_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="item_decouple" id="item_decouple1" value="1" <?php echo $this->item->item_decouple==true? 'checked="checked"' :""?> />
                            <label class="btn" for="item_decouple1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="item_decouple" id="item_decouple0" value="0" <?php echo $this->item->item_decouple==false? 'checked="checked"' :""?> />
                            <label class="btn" for="item_decouple0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="enable_rss-lbl" for="enable_rss" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_RSS');?></strong><br/><?php echo JText::_('LNG_ENABLE_RSS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_RSS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_rss_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_rss" id="enable_rss1" value="1" <?php echo $this->item->enable_rss==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_rss1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_rss" id="enable_rss0" value="0" <?php echo $this->item->enable_rss==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_rss0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="enable_multilingual-lbl" for="enable_multilingual" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_MULTILINGUAL');?></strong><br/><?php echo JText::_('LNG_ENABLE_MULTILINGUAL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_MULTILINGUAL'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_multilingual_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_multilingual" id="enable_multilingual1" value="1" <?php echo $this->item->enable_multilingual==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_multilingual1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_multilingual" id="enable_multilingual0" value="0" <?php echo $this->item->enable_multilingual==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_multilingual0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="available_languages-lbl" for="available_languages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_AVAILABLE_LANGUAGES');?></strong><br/><?php echo JText::_('LNG_AVAILABLE_LANGUAGES');?>" title=""><?php echo JText::_('LNG_AVAILABLE_LANGUAGES'); ?></label></div>
                    <div class="controls">
                        <select	id="available_languages[]" name="available_languages[]" multiple>
                             <?php
		                        $availableLanguages = JBusinessUtil::getAvailableLanguages();
                                foreach($availableLanguages as $key=>$value) {
                                    $selected = "";
                                    if (!empty($this->item->available_languages)) {
                                        if (in_array($value, $this->item->available_languages))
                                            $selected = "selected";
                                    } ?>
                                    <option value='<?php echo $value ?>' <?php echo $selected ?>> <?php echo $key ?></option>
                            <?php } ?>
                		</select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="enable_socials-lbl" for="enable_socials" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_SOCIALS');?></strong><br/><?php echo JText::_('LNG_ENABLE_SOCIALS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_SOCIALS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_socials" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_socials" id="enable_socials1" value="1" <?php echo $this->item->enable_socials==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_socials1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_socials" id="enable_socials0" value="0" <?php echo $this->item->enable_socials==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_socials0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="show_contact_form" for="show_contact_form" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CONTACT_FORM');?></strong><br/><?php echo JText::_('LNG_SHOW_CONTACT_FORM_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CONTACT_FORM'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_contact_form_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_contact_form" id="show_contact_form1" value="1" <?php echo $this->item->show_contact_form==true? 'checked="checked"' :""?> />
                            <label class="btn" for="show_contact_form1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_contact_form" id="show_contact_form0" value="0" <?php echo $this->item->show_contact_form==false? 'checked="checked"' :""?> />
                            <label class="btn" for="show_contact_form0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="redirect_contact_url-lbl" for="redirect_contact_url" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_REDIRECT_CONTACT_URL');?></strong><br/><?php echo JText::_('LNG_REDIRECT_CONTACT_URL_DESC');?>" title=""><?php echo JText::_('LNG_REDIRECT_CONTACT_URL'); ?></label></div>
                    <div class="controls">
                        <input type="text" maxlength="255" id="redirect_contact_url" name = "redirect_contact_url" value="<?php echo htmlspecialchars($this->item->redirect_contact_url, ENT_QUOTES) ?>">
                    </div>
                </div>

                <div class="control-group" style="display:none">
                    <div class="control-label"><label id="enable_messages-lbl" for="enable_messages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_MESSAGES');?></strong><br/><?php echo JText::_('LNG_ENABLE_MESSAGES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_MESSAGES'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_messages" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_messages" id="enable_messages1" value="1" <?php echo $this->item->enable_messages=='1'? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_messages1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_messages" id="enable_messages0" value="0" <?php echo $this->item->enable_messages=='0'? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_messages0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label"><label id="front_end_acl-lbl" for="front_end_acl" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_FRONT_END_ACL');?></strong><br/><?php echo JText::_('LNG_ENABLE_FRONT_END_ACL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_FRONT_END_ACL'); ?></label></div>
                    <div class="controls">
                        <fieldset id="front_end_acl_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="front_end_acl" id="front_end_acl1" value="1" <?php echo $this->item->front_end_acl==true? 'checked="checked"' :""?> />
                            <label class="btn" for="front_end_acl1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="front_end_acl" id="front_end_acl0" value="0" <?php echo $this->item->front_end_acl==false? 'checked="checked"' :""?> />
                            <label class="btn" for="front_end_acl0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                        <div>
                            <div class="dir-notice"><?php echo JText::_('LNG_FRONT_END_ACL_NOTICE')?></div>
                        </div>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="front_end_meta_data-lbl" for="front_end_meta_data" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_FRONT_END_META_DATA');?></strong><br/><?php echo JText::_('LNG_ENABLE_FRONT_END_META_DATA_DSCR');?>" title=""><?php echo JText::_('LNG_ENABLE_FRONT_END_META_DATA'); ?></label></div>
                    <div class="controls">
                        <fieldset id="front_end_meta_data_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="front_end_meta_data" id="front_end_meta_data1" value="1" <?php echo $this->item->front_end_meta_data==true? 'checked="checked"' :""?> />
                            <label class="btn" for="front_end_meta_data1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="front_end_meta_data" id="front_end_meta_data0" value="0" <?php echo $this->item->front_end_meta_data==false? 'checked="checked"' :""?> />
                            <label class="btn" for="front_end_meta_data0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

				<div class="control-group">
                    <div class="control-label"><label id="captcha-lbl" for="captcha" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_CAPTCHA');?></strong><br/><?php echo JText::_('LNG_ENABLE_CAPTCHA_DESCRIPTION');?><br><em><?php echo JText::_('LNG_ENABLE_CAPTCHA_DESCRIPTION_NOTE');?></em>" title=""><?php echo JText::_('LNG_ENABLE_CAPTCHA'); ?></label></div>
                    <div class="controls">
                        <fieldset id="captcha_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="captcha" id="captcha1" value="1" <?php echo $this->item->captcha==true? 'checked="checked"' :""?> />
                            <label class="btn" for="captcha1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="captcha" id="captcha0" value="0" <?php echo $this->item->captcha==false? 'checked="checked"' :""?> />
                            <label class="btn" for="captcha0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                        <div>
                            <div class="dir-notice"><?php echo JText::_('LNG_CAPTCHA_NOTICE')?></div>
                        </div>
                    </div>
                </div>

                <div class="control-group" style="display:none">
                    <div class="control-label"><label id="allow_multiple_companies-lbl" for="allow_multiple_companies" class="hasTooltip" title=""><?php echo JText::_('LNG_ALLOW_MULTIPLE_COMPANIES_PER_USER'); ?></label></div>
                    <div class="controls">
                        <fieldset id="allow_multiple_companies_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="allow_multiple_companies" id="allow_multiple_companies1" value="1" <?php echo $this->item->allow_multiple_companies==true? 'checked="checked"' :""?> />
                            <label class="btn" for="allow_multiple_companies1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="allow_multiple_companies" id="allow_multiple_companies0" value="0" <?php echo $this->item->allow_multiple_companies==false? 'checked="checked"' :""?> />
                            <label class="btn" for="allow_multiple_companies0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="enable_bookmarks-lbl" for="enable_bookmarks" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_BOOKMARKS');?></strong><br/><?php echo JText::_('LNG_ENABLE_BOOKMARKS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_BOOKMARKS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_bookmarks_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_bookmarks" id="enable_bookmarks1" value="1" <?php echo $this->item->enable_bookmarks==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_bookmarks1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_bookmarks" id="enable_bookmarks0" value="0" <?php echo $this->item->enable_bookmarks==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_bookmarks0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="enable_https_payment-lbl" for="enable_https_payment" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_HTTPS_ON_PAYMENT_INFO');?></strong><br/><?php echo JText::_('LNG_ENABLE_HTTPS_ON_PAYMENT_DESCRIPTION');?><br/><em><?php echo JText::_('LNG_ENABLE_HTTPS_ON_PAYMENT_DESCRIPTION_2');?></em>" title=""><?php echo JText::_('LNG_ENABLE_HTTPS_ON_PAYMENT'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_https_payment_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_https_payment" id="enable_https_payment1" value="1" <?php echo $this->item->enable_https_payment==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_https_payment1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_https_payment" id="enable_https_payment0" value="0" <?php echo $this->item->enable_https_payment==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_https_payment0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                 <div class="control-group">
                    <div class="control-label"><label id="expiration_day_notice-lbl" for="expiration_day_notice" class="hasTooltip"  data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EXPIRATION_DAYS_NOTICE');?></strong><br/><?php echo JText::_('LNG_EXPIRATION_DAYS_NOTICE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_EXPIRATION_DAYS_NOTICE'); ?></label></div>
                    <div class="controls">
                        <select id="expiration_day_notice" name="expiration_day_notice[]" multiple="multiple">
                        	<?php for($i=1;$i<31;$i++){?>
								<option value="<?php echo $i?>" <?php echo in_array($i, $this->item->expiration_day_notice)?'selected="selected"':'' ?>><?php echo $i?></option>                		
                        	<?php }?>
                        </select>
                    </div>
                </div>

                <div class="control-group" style="display:none">
                    <div class="control-label"><label id="service_notification_days-lbl" for="service_notification_days" class="hasTooltip"  data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SERVICE_NOTIFICATION_DAYS');?></strong><br/><?php echo JText::_('LNG_SERVICE_NOTIFICATION_DAYS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SERVICE_NOTIFICATION_DAYS'); ?></label></div>
                    <div class="controls">
                        <select id="service_notification_days" name="service_notification_days" >
                            <?php for($i=1;$i<31;$i++){?>
                                <option value="<?php echo $i?>" <?php echo $i == $this->item->service_notification_days?'selected="selected"':'' ?>><?php echo $i?></option>                		
                            <?php }?>
                        </select>
                    </div>
                </div>
			</div>
		</fieldset>
	</div>
	<div class="col-md-6">
        <fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_USER_SETTINGS'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="allow_user_creation-lbl" for="allow_user_creation" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ALLOW_USER_CREATION');?></strong><br/><?php echo JText::_('LNG_ALLOW_USER_CREATION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ALLOW_USER_CREATION'); ?></label></div>
                    <div class="controls">
                        <fieldset id="allow_user_creation_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="allow_user_creation" id="allow_user_creation1" value="1" <?php echo $this->item->allow_user_creation==1? 'checked="checked"' :""?> />
                            <label class="btn" for="allow_user_creation1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="allow_user_creation" id="allow_user_creation0" value="0" <?php echo $this->item->allow_user_creation==0? 'checked="checked"' :""?> />
                            <label class="btn" for="allow_user_creation0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                
                <div class="control-group user-login-position" style="<?php echo $this->item->allow_user_creation == 0? "display:none" :"" ?>">
                    <div class="control-label"><label id="user_login_position-lbl" for="user_login_position" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_USER_LOGIN_POSITION');?></strong><br/><?php echo JText::_('LNG_USER_LOGIN_POSITION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_USER_LOGIN_POSITION'); ?></label></div>
                    <div class="controls">
                        <fieldset id="user_login_position_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="user_login_position" id="user_login_position1" value="1" <?php echo $this->item->user_login_position==1? 'checked="checked"' :""?> />
                            <label class="btn" for="user_login_position1"><?php echo JText::_('LNG_BEFORE_LISTING_CREATION')?></label>
                            <input type="radio"  name="user_login_position" id="user_login_position0" value="2" <?php echo $this->item->user_login_position==2? 'checked="checked"' :""?> />
                            <label class="btn" for="user_login_position0"><?php echo JText::_('LNG_AFTER_LISTING_CREATION')?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group" style="display:none">
                    <div class="control-label"><label id="custom-regstriation-lbl" for="custom_registration" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CUSTOM_REGISTRATION');?></strong><br/><?php echo JText::_('LNG_CUSTOM_REGISTRATION_DESC');?>" title=""><?php echo JText::_('LNG_CUSTOM_REGISTRATION'); ?></label></div>
                    <div class="controls">
                        <fieldset id="custom_registration_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="custom_registration" id="custom_registration1" value="1" <?php echo $this->item->custom_registration==1? 'checked="checked"' :""?> />
                            <label class="btn" for="custom_registration1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="custom_registration" id="custom_registration0" value="0" <?php echo $this->item->custom_registration==0? 'checked="checked"' :""?> />
                            <label class="btn" for="custom_registration0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="generate_auto_user-lbl" for="generate_auto_user" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GENERATE_AUTO_USER');?></strong><br/><?php echo JText::_('LNG_GENERATE_AUTO_USER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GENERATE_AUTO_USER'); ?></label></div>
                    <div class="controls">
                        <fieldset id="generate_auto_user_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="generate_auto_user" id="generate_auto_user1" value="1" <?php echo $this->item->generate_auto_user==true? 'checked="checked"' :""?> />
                            <label class="btn" for="generate_auto_user1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="generate_auto_user" id="generate_auto_user0" value="0" <?php echo $this->item->generate_auto_user==false? 'checked="checked"' :""?> />
                            <label class="btn" for="generate_auto_user0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="show_cp_suggestions-lbl" for="show_cp_suggestions" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CP_SUGGESTIONS');?></strong><br/><?php echo JText::_('LNG_SHOW_CP_SUGGESTIONS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CP_SUGGESTIONS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_cp_suggestions_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_cp_suggestions" id="show_cp_suggestions1" value="1" <?php echo $this->item->show_cp_suggestions==1? 'checked="checked"' :""?> />
                            <label class="btn" for="show_cp_suggestions1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_cp_suggestions" id="show_cp_suggestions0" value="0" <?php echo $this->item->show_cp_suggestions==0? 'checked="checked"' :""?> />
                            <label class="btn" for="show_cp_suggestions0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="usergroup-lbl" for="usergroup" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CHOOSE_USERGROUP');?></strong><br/><?php echo JText::_('LNG_CHOOSE_USERGROUP_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CHOOSE_USERGROUP'); ?></label></div>
                    <div class="controls">
                        <select	id="usergroup" name="usergroup" class="chzn-color">
                            <?php echo JHtml::_('select.options',$this->userGroups, 'value', 'name', $this->item->usergroup);?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="business_usergroup-lbl" for="business_usergroup" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_BUSINESS_USERGROUP');?></strong><br/><?php echo JText::_('LNG_BUSINESS_USERGROUP_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_BUSINESS_USERGROUP'); ?></label></div>
                    <div class="controls">
                        <select	id="business_usergroup" name="business_usergroup" class="chzn-color">
                            <option value=""><?php echo JText::_('NONE') ?></option>
                            <?php echo JHtml::_('select.options',$this->userGroups, 'value', 'name', $this->item->business_usergroup);?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="paid_business_usergroup-lbl" for="paid_business_usergroup" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_PAID_BUSINESS_USERGROUP');?></strong><br/><?php echo JText::_('LNG_PAID_BUSINESS_USERGROUP_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_PAID_BUSINESS_USERGROUP'); ?></label></div>
                    <div class="controls">
                        <select	id="paid_business_usergroup" name="paid_business_usergroup" class="chzn-color">
                            <option value=""><?php echo JText::_('NONE') ?></option>
                            <?php echo JHtml::_('select.options',$this->userGroups, 'value', 'name', $this->item->paid_business_usergroup);?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="mobile_usergroup-lbl" for="mobile_usergroup" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CHOOSE_MOBILE_USERGROUP');?></strong><br/><?php echo JText::_('LNG_CHOOSE_MOBILE_USERGROUP_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CHOOSE_MOBILE_USERGROUP'); ?></label></div>
                    <div class="controls">
                        <select	id="mobile_usergroup" name="mobile_usergroup" class="chzn-color">
				            <?php echo JHtml::_('select.options',$this->userGroups, 'value', 'name', $this->item->mobile_usergroup); ?>
                        </select>
                    </div>
                </div>
                <?php if (JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS)) { ?>
                    <div class="control-group">
                        <div class="control-label"><label id="request_quote_usergroup-lbl" for="request_quote_usergroup" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CHOOSE_REQUEST_QUOTE_USERGROUP');?></strong><br/><?php echo JText::_('LNG_REQUEST_QUOTE_CHOOSE_USERGROUP_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CHOOSE_REQUEST_QUOTE_USERGROUP'); ?></label></div>
                        <div class="controls">
                            <select	id="request_quote_usergroup" name="request_quote_usergroup" class="chzn-color">
                                <?php echo JHtml::_('select.options',$this->userGroups, 'value', 'name', $this->item->request_quote_usergroup);?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </fieldset>


		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_DATE_TIME'); ?></legend>
            <div class="form-container">
            	<div class="control-group">
                    <div class="control-label"><label id="company_name-lbl" for="company_name" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DATE_FORMAT');?></strong><br/><?php echo JText::_('LNG_DATE_FORMAT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_DATE_FORMAT'); ?></label></div>
                    <div class="controls">
                        <select id='date_format_id' name='date_format_id'>
                            <?php foreach ($this->item->dateFormats as $dateFormat){?>
                                <option value = '<?php echo $dateFormat->id?>' <?php echo $dateFormat->id==$this->item->date_format_id? "selected" : ""?>> <?php echo $dateFormat->name?></option>
                            <?php }	?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="time_format-lbl" for="time_format" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TIME_FORMAT');?></strong><br/><?php echo JText::_('LNG_TIME_FORMAT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_TIME_FORMAT'); ?></label></div>
                    <div class="controls">
                        <select id='time_format' name='time_format'>
                            <option value = "h:i A" <?php echo $this->item->time_format=="h:i A"? "selected" : ""?>><?php echo "12"." ".JText::_("LNG_HOURS")?></option>
                            <option value = "H:i" <?php echo $this->item->time_format=="H:i"? "selected" : ""?>><?php echo "24"." ".JText::_("LNG_HOURS")?></option>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="default_time_zone-lbl" for="default_time_zone" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DEFAULT_TIME_ZONE');?></strong><br/><?php echo JText::_('LNG_DEFAULT_TIME_ZONE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_DEFAULT_TIME_ZONE'); ?></label></div>
                    <div class="controls">
                        <select class=" chosen-select" id="default_time_zone" name="default_time_zone">
                            <?php $timeZones = JBusinessUtil::timeZonesList();
                            foreach ($timeZones as $key => $zone) {
                                $selected = ($key == $this->item->default_time_zone) ? " selected" : "";
                                echo "<option value='" . $key . "'" . $selected . ">" . $zone . "</option>";
                            }?>
                        </select>
                    </div>
                </div>
            </div>
        </fieldset>
        
        <?php if (JBusinessUtil::isAppInstalled(JBD_APP_ELASTIC_SEARCH)) { ?>
            <fieldset class="form-horizontal mt-5">
                <legend><?php echo JText::_('LNG_ELASTIC_SEARCH'); ?></legend>
                <div class="form-container">
                    <div class="control-group">
                        <div class="control-label"><label id="enable_elastic_search-lbl" for="enable_elastic_search" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ELASTIC_SEARCH');?></strong><br/><?php echo JText::_('LNG_ENABLE_ELASTIC_SEARCH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_ELASTIC_SEARCH'); ?></label></div>
                        <div class="controls">
                            <fieldset id="enable_elastic_search_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="enable_elastic_search" id="enable_elastic_search1" value="1" onclick="showItemModeration('show');" <?php echo $this->item->enable_elastic_search=="1"? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_elastic_search1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="enable_elastic_search" id="enable_elastic_search0" value="0" onclick="showItemModeration('hide');" <?php echo $this->item->enable_elastic_search=="0"? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_elastic_search0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div id="elastic_search" style="<?php echo $this->item->enable_elastic_search == 0? "display:none" :"" ?>">
                        <div class="control-group">
                            <div class="control-label"><label id="elastic_search_version-lbl" for="elastic_search_version" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ELASTIC_VERSION');?></strong><br/><?php echo JText::_('LNG_ELASTIC_VERSION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ELASTIC_VERSION'); ?></label></div>
                            <div class="controls">
                                <select name="elastic_search_version" id="elastic_search_version" class="chosen-select">
                                    <?php foreach( $this->item->elasticSearchVersions as $key=>$version){?>
                                        <option value="<?php echo $key ?>" <?php echo $key == $this->item->elastic_search_version ? "selected":"" ; ?>><?php echo JText::_($version)  ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><label id="elastic_endpoint-lbl" for="elastic_endpoint" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ELASTIC_ENDPOINT');?></strong><br/><?php echo JText::_('LNG_ELASTIC_ENDPOINT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ELASTIC_ENDPOINT'); ?></label></div>
                            <div class="controls">
                                <input type="text"  id="elastic_endpoint" name="elastic_endpoint"  class=" form-control" value="<?php echo $this->item->elastic_endpoint?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><label id="elastic_search_endpoint-lbl" for="elastic_search_endpoint" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ELASTIC_SEARCH_ENDPOINT');?></strong><br/><?php echo JText::_('LNG_ELASTIC_SEARCH_ENDPOINT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ELASTIC_SEARCH_ENDPOINT'); ?></label></div>
                            <div class="controls">
                                <input type="text"  id="elastic_search_endpoint" name="elastic_search_endpoint"  class=" form-control" value="<?php echo $this->item->elastic_search_endpoint?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><label id="elastic_search_index-lbl" for="elastic_search_index" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ELASTIC_SEARCH_INDEX');?></strong><br/><?php echo JText::_('LNG_ELASTIC_SEARCH_INDEX_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ELASTIC_SEARCH_INDEX'); ?></label></div>
                            <div class="controls">
                                <input type="text"  id="elastic_search_index" name="elastic_search_index"  class=" form-control" value="<?php echo $this->item->elastic_search_index?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><label id="elastic_search_user-lbl" for="elastic_search_user" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ELASTIC_SEARCH_USER');?></strong><br/><?php echo JText::_('LNG_ELASTIC_SEARCH_USER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ELASTIC_SEARCH_USER'); ?></label></div>
                            <div class="controls">
                                <input type="text"  id="elastic_search_user" name="elastic_search_user"  class=" form-control" value="<?php echo $this->item->elastic_search_user?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><label id="elastic_search_password-lbl" for="elastic_search_password" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ELASTIC_SEARCH_PASSWORD');?></strong><br/><?php echo JText::_('LNG_ELASTIC_SEARCH_PASSWORD_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ELASTIC_SEARCH_PASSWORD'); ?></label></div>
                            <div class="controls">
                                <input type="text"  id="elastic_search_password" name="elastic_search_password"  class=" form-control" value="<?php echo $this->item->elastic_search_password?>">
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        <?php } ?>

      	<fieldset class="form-horizontal mt-5">
            <legend><?php echo JText::_('LNG_ITEM_MODERATION'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="enable_item_moderation-lbl" for="enable_item_moderation" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ITEM_MODERATION');?></strong><br/><?php echo JText::_('LNG_ENABLE_ITEM_MODERATION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_ITEM_MODERATION'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_item_moderation_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_item_moderation" id="enable_item_moderation1" value="1" onclick="showItemModeration('show');" <?php echo $this->item->enable_item_moderation=="1"? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_item_moderation1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_item_moderation" id="enable_item_moderation0" value="0" onclick="showItemModeration('hide');" <?php echo $this->item->enable_item_moderation=="0"? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_item_moderation0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div id="moderate_item">

                    <div class="control-group">
                        <div class="control-label"><label id="show_pending_approval-lbl" for="show_pending_approval" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_PENDING_APPROVAL');?></strong><br/><?php echo JText::_('LNG_SHOW_PENDING_APPROVAL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_PENDING_APPROVAL'); ?></label></div>
                        <div class="controls">
                            <fieldset id="show_pending_approval_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="show_pending_approval" id="show_pending_approval1" value="1" <?php echo $this->item->show_pending_approval==true? 'checked="checked"' :""?> />
                                <label class="btn" for="show_pending_approval1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="show_pending_approval" id="show_pending_approval0" value="0" <?php echo $this->item->show_pending_approval==false? 'checked="checked"' :""?> />
                                <label class="btn" for="show_pending_approval0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="enable_automated_moderation-lbl" for="enable_automated_moderation" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_AUTOMATED_MODERATION');?></strong><br/><?php echo JText::_('LNG_ENABLE_AUTOMATED_MODERATION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_AUTOMATED_MODERATION'); ?></label></div>
                        <div class="controls">
                            <fieldset id="enable_automated_moderation_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="enable_automated_moderation" id="enable_automated_moderation1" value="1" <?php echo $this->item->enable_automated_moderation=="1"? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_automated_moderation1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="enable_automated_moderation" id="enable_automated_moderation0" value="0" <?php echo $this->item->enable_automated_moderation=="0"? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_automated_moderation0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="moderate_threshold-lbl" for="moderate_threshold" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MODERATE_THRESHOLD');?></strong><br/><?php echo JText::_('LNG_MODERATE_THRESHOLD_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MODERATE_THRESHOLD'); ?></label></div>
                        <div class="controls">
                            <input type="text" size=40 maxlength=20  id="moderate_threshold" name = "moderate_threshold" value="<?php echo $this->item->moderate_threshold?>">
                        </div>
                    </div>

                </div>
            </div>
        </fieldset>
        
        <fieldset class="form-horizontal mt-5 bg-light">
            <div class="form-container">
            	<div class="control-group">
                    <div class="control-label"><label id="clear-demo-data-lbl" for="clear-demo-data" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CLEAR_DATA');?></strong><br/><?php echo JText::_('LNG_CLEAR_DATA_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CLEAR_DATA'); ?></label></div>
                    <div class="controls">
                        <a id="clear-demo-data" href="javascript:clearDemoData()" class="btn btn-info mt-3"><?php echo JText::_('LNG_CLEAR_DATA') ?></a>
                        <img id="clear-demo-data-loading" style="display:none;width:10%;" class="loading" src='<?php echo BD_ASSETS_FOLDER_PATH."images/loader.gif"?>'>
                    </div>
                </div>
            </div>
        </fieldset>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_REVIEWS'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="enable_reviews-lbl" for="enable_reviews" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_REVIEWS');?></strong><br/><?php echo JText::_('LNG_ENABLE_REVIEWS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_REVIEWS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_reviews_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_reviews" id="enable_reviews1" value="1" <?php echo $this->item->enable_reviews==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_reviews1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_reviews" id="enable_reviews0" value="0" <?php echo $this->item->enable_reviews==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_reviews0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div id="review-settings" style="<?php echo $this->item->enable_reviews == 0? "display:none" :"" ?>">
                    <div class="control-group">
                        <div class="control-label"><label id="enable_reviews_users-lbl" for="enable_reviews_users" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_REVIEWS_USERS_ONLY');?></strong><br/><?php echo JText::_('LNG_ENABLE_REVIEWS_USERS_ONLY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_REVIEWS_USERS_ONLY'); ?></label></div>
                        <div class="controls">
                            <fieldset id="enable_reviews_users_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="enable_reviews_users" id="enable_reviews_users1" value="1" <?php echo $this->item->enable_reviews_users==true? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_reviews_users1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="enable_reviews_users" id="enable_reviews_users0" value="0" <?php echo $this->item->enable_reviews_users==false? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_reviews_users0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="show_pending_review-lbl" for="show_pending_review" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_PENDING_REVIEW');?></strong><br/><?php echo JText::_('LNG_SHOW_PENDING_REVIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_PENDING_REVIEW'); ?></label></div>
                        <div class="controls">
                            <fieldset id="show_pending_review_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="show_pending_review" id="show_pending_review1" value="1" <?php echo $this->item->show_pending_review==true? 'checked="checked"' :""?> />
                                <label class="btn" for="show_pending_review1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="show_pending_review" id="show_pending_review0" value="0" <?php echo $this->item->show_pending_review==false? 'checked="checked"' :""?> />
                                <label class="btn" for="show_pending_review0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="share_reviews-lbl" for="share_reviews" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHARE_REVIEW');?></strong><br/><?php echo JText::_('LNG_SHARE_REVIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHARE_REVIEW'); ?></label></div>
                        <div class="controls">
                            <fieldset id="share_reviews" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="share_reviews" id="share_reviews1" value="1" <?php echo $this->item->share_reviews=='1'? 'checked="checked"' :""?> />
                                <label class="btn" for="share_reviews1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="share_reviews" id="share_reviews0" value="0" <?php echo $this->item->share_reviews=='0'? 'checked="checked"' :""?> />
                                <label class="btn" for="share_reviews0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="enable_ratings-lbl" for="enable_ratings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_RATINGS');?></strong><br/><?php echo JText::_('LNG_ENABLE_RATINGS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_RATINGS'); ?></label></div>
                        <div class="controls">
                            <fieldset id="enable_ratings_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="enable_ratings" id="enable_ratings1" value="1" <?php echo $this->item->enable_ratings==true? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_ratings1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="enable_ratings" id="enable_ratings0" value="0" <?php echo $this->item->enable_ratings==false? 'checked="checked"' :""?> />
                                <label class="btn" for="enable_ratings0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="edit_ratings-lbl" for="edit_ratings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EDIT_RATINGS');?></strong><br/><?php echo JText::_('LNG_EDIT_RATINGS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_EDIT_RATINGS'); ?></label></div>
                        <div class="controls">
                            <fieldset id="edit_ratings_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="edit_ratings" id="edit_ratings1" value="1" <?php echo $this->item->edit_ratings==true? 'checked="checked"' :""?> />
                                <label class="btn" for="edit_ratings1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="edit_ratings" id="edit_ratings0" value="0" <?php echo $this->item->edit_ratings==false? 'checked="checked"' :""?> />
                                <label class="btn" for="edit_ratings0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="show_verified_review_badge-lbl" for="show_verified_review_badge" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_VERIFIED_REVIEW_BADGE');?></strong><br/><?php echo JText::_('LNG_SHOW_VERIFIED_REVIEW_BADGE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SHOW_VERIFIED_REVIEW_BADGE"); ?></label></div>
                        <div class="controls">
                            <fieldset id="show_verified_review_badge_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="show_verified_review_badge" id="show_verified_review_badge1" value="1" <?php echo $this->item->show_verified_review_badge==true? 'checked="checked"' :""?> />
                                <label class="btn" for="show_verified_review_badge1"><?php echo JText::_('LNG_YES')?></label>
                                <input type="radio"  name="show_verified_review_badge" id="show_verified_review_badge0" value="0" <?php echo $this->item->show_verified_review_badge==false? 'checked="checked"' :""?> />
                                <label class="btn" for="show_verified_review_badge0"><?php echo JText::_('LNG_NO')?></label>
                            </fieldset>
                        </div>
                    </div>
			    </div>
			</div>
		</fieldset>
	</div>
	<div class="col-md-6">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_CURRENCY'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="company_name-lbl" for="company_name" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_NAME');?></strong><br/><?php echo JText::_('LNG_NAME_CURRENCY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_NAME'); ?></label></div>
                    <div class="controls">
                        <select	id="currency_id" name="currency_id" class="chzn-color">
                            <?php
                                for($i = 0; $i <  count( $this->item->currencies ); $i++){
                                    $currency = $this->item->currencies[$i];
                            ?>
                                <option value = '<?php echo $currency->currency_id?>' <?php echo $currency->currency_id==$this->item->currency_id? "selected" : ""?>> <?php echo $currency->currency_name." - ". $currency->currency_description ?></option>
                            <?php }	?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="currency_symbol-lbl" for="currency_symbol" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CURRENCY_SYMBOL');?></strong><br/><?php echo JText::_('LNG_CURRENCY_SYMBOL_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CURRENCY_SYMBOL'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input name="currency_symbol" id="currency_symbol" value="<?php echo $this->item->currency_symbol?>" size="50" maxlength="45" type="text">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="currency_display-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CURRENCY_DISPLAY');?></strong><br/><?php echo JText::_('LNG_CURRENCY_DISPLAY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CURRENCY_DISPLAY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="currency_display_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="currency_display" id="currency_display1" value="1" <?php echo $this->item->currency_display==1? 'checked="checked"' :""?> />
                            <label class="btn" for="currency_display1"><?php echo JText::_('LNG_NAME')?></label>
                            <input type="radio"  name="currency_display" id="currency_display2" value="2" <?php echo $this->item->currency_display==2? 'checked="checked"' :""?> />
                            <label class="btn" for="currency_display2"><?php echo JText::_('LNG_SYMBOL')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="currency_location-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_CURRENCY');?></strong><br/><?php echo JText::_('LNG_SHOW_CURRENCY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_CURRENCY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="currency_location_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="currency_location" id="currency_location1" value="1" <?php echo $this->item->currency_location==1? 'checked="checked"' :""?> />
                            <label class="btn" for="currency_location1"><?php echo JText::_('LNG_BEFORE_PRICE')?></label>
                            <input type="radio"  name="currency_location" id="currency_location2" value="2" <?php echo $this->item->currency_location==2? 'checked="checked"' :""?> />
                            <label class="btn" for="currency_location2"><?php echo JText::_('LNG_AFTER_PRICE')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="amount_separator-lbl" for="enable_packages" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_AMOUNT_SEPARATOR');?></strong><br/><?php echo JText::_('LNG_AMOUNT_SEPARATOR_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_AMOUNT_SEPARATOR'); ?></label></div>
                    <div class="controls">
                        <fieldset id="amount_separator_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="amount_separator" id="amount_separator1" value="1" <?php echo $this->item->amount_separator==1? 'checked="checked"' :""?> />
                            <label class="btn" for="amount_separator1"><?php echo JText::_('LNG_DOT_SEPARATOR')?></label>
                            <input type="radio"  name="amount_separator" id="amount_separator2" value="2" <?php echo $this->item->amount_separator==2? 'checked="checked"' :""?> />
                            <label class="btn" for="amount_separator2"><?php echo JText::_('LNG_COMMA_SEPARATOR')?></label>
                        </fieldset>
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label"><label id="number_of_decimals-lbl" for="number_of_decimals" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_NUMBER_OF_DECIMLAS');?></strong><br/><?php echo JText::_('LNG_NUMBER_OF_DECIMLAS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_NUMBER_OF_DECIMLAS'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <select	id="number_of_decimals" name="number_of_decimals" >
                            <?php echo JHtml::_('select.options',$this->item->numbers, 'value', 'name', $this->item->number_of_decimals);?>
                        </select>
                    </div>

                </div>

                <div class="control-group">
                    <div class="control-label"><label id="currency_converter_api-lbl" for="currency_converter_api" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CURRENCY_CONVERTER_API_KEY');?></strong><br/><?php echo JText::_('LNG_CURRENCY_CONVERTER_API_KEY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CURRENCY_CONVERTER_API_KEY'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input name="currency_converter_api" id="currency_converter_api" value="<?php echo $this->item->currency_converter_api?>" size="100" maxlength="255" type="text">
                    </div>
                </div>
            </div>
		</fieldset>
	</div>
</div>
<div class="row">
	<div class="col-md-6">		
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_MEDIA'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="enable_attachments-lbl" for="enable_attachments" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_ATTACHMENTS');?></strong><br/><?php echo JText::_('LNG_ENABLE_ATTACHMENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_ATTACHMENTS'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_attachments_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="enable_attachments" id="enable_attachments1" value="1" <?php echo $this->item->enable_attachments==true? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_attachments1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="enable_attachments" id="enable_attachments0" value="0" <?php echo $this->item->enable_attachments==false? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_attachments0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="max_video-lbl" for="max_video" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_VIDEOS');?></strong><br/><?php echo JText::_('LNG_MAX_VIDEOS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_VIDEOS'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=20  id="max_video" name = "max_video" value="<?php echo $this->item->max_video?>">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="max_pictures-lbl" for="max_pictures" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_PICTURES');?></strong><br/><?php echo JText::_('LNG_MAX_PICTURES_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_PICTURES'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=20  id="max_pictures" name = "max_pictures" value="<?php echo $this->item->max_pictures?>">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="max_review_images-lbl" for="max_review_images" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_IMAGES_REVIEW');?></strong><br/><?php echo JText::_('LNG_MAX_IMAGES_REVIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_IMAGES_REVIEW'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=20  id="max_review_images" name = "max_review_images" value="<?php echo $this->item->max_review_images?>">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="max_sound-lbl" for="max_sound" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_SOUNDS');?></strong><br/><?php echo JText::_('LNG_MAX_SOUNDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_SOUNDS'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=20  id="max_sound" name = "max_sound" value="<?php echo $this->item->max_sound?>">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="max_attachments-lbl" for="max_attachments" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_ATTACHMENTS_INFO');?></strong><br/><?php echo JText::_('LNG_MAX_ATTACHMENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_ATTACHMENTS'); ?></label></div>
                    <div class="controls">
                        <input type="text" size="40" maxlength="20"  id="max_attachments" name="max_attachments" value="<?php echo $this->item->max_attachments?>">
                    </div>
                </div>
            </div>
		</fieldset>
	</div>
	<div class="col-md-6">	
		<fieldset class="form-horizontal">
            <legend><?php echo JText::_('LNG_IMAGES'); ?></legend>
            <div class="form-container">
	            <div class="control-group">
	                <div class="control-label"><label id="logo_width-lbl" for="logo_width" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LOGO_WIDTH');?></strong><br/><?php echo JText::_('LNG_LOGO_WIDTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LOGO_WIDTH'); ?></label></div>
	                <div class="controls">
	                    <input type="text" size=40 maxlength=20  id="logo_width" name="logo_width" value="<?php echo $this->item->logo_width?>">
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="logo_height-lbl" for="logo_height" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LOGO_HEIGHT');?></strong><br/><?php echo JText::_('LNG_LOGO_HEIGHT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LOGO_HEIGHT'); ?></label></div>
	                <div class="controls">
	                    <input type="text" size=40 maxlength=20  id="logo_height" name="logo_height" value="<?php echo $this->item->logo_height?>">
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="cover_width-lbl" for="cover_width" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_COVER_WIDTH');?></strong><br/><?php echo JText::_('LNG_COVER_WIDTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_COVER_WIDTH'); ?></label></div>
	                <div class="controls">
	                    <input type="text" size=40 maxlength=20  id="cover_width" name="cover_width" value="<?php echo $this->item->cover_width?>">
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="cover_height-lbl" for="cover_height" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_COVER_HEIGHT');?></strong><br/><?php echo JText::_('LNG_COVER_HEIGHT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_COVER_HEIGHT'); ?></label></div>
	                <div class="controls">
	                    <input type="text" size=40 maxlength=20  id="cover_height" name="cover_height" value="<?php echo $this->item->cover_height?>">
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="gallery_width-lbl" for="gallery_width" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GALLERY_WIDTH');?></strong><br/><?php echo JText::_('LNG_GALLERY_WIDTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GALLERY_WIDTH'); ?></label></div>
	                <div class="controls">
	                    <input type="text" size=40 maxlength=20  id="gallery_width" name="gallery_width" value="<?php echo $this->item->gallery_width?>">
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="gallery_height-lbl" for="gallery_height" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GALLERY_HEIGHT');?></strong><br/><?php echo JText::_('LNG_GALLERY_HEIGHT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GALLERY_HEIGHT'); ?></label></div>
	                <div class="controls">
	                    <input type="text" size=40 maxlength=20  id="gallery_height" name="gallery_height" value="<?php echo $this->item->gallery_height?>">
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="enable_crop-lbl" for="enable_crop" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_CROPPING');?></strong><br/><?php echo JText::_('LNG_ENABLE_CROPPING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_CROPPING'); ?></label></div>
	                <div class="controls">
	                    <fieldset id="enable_crop_fld" class="radio btn-group btn-group-yesno">
	                        <input type="radio"  name="enable_crop" id="enable_crop1" value="1" <?php echo $this->item->enable_crop==true? 'checked="checked"' :""?> />
	                        <label class="btn" for="enable_crop1"><?php echo JText::_('LNG_YES')?></label>
	                        <input type="radio"  name="enable_crop" id="enable_crop0" value="0" <?php echo $this->item->enable_crop==false? 'checked="checked"' :""?> />
	                        <label class="btn" for="enable_crop0"><?php echo JText::_('LNG_NO')?></label>
	                    </fieldset>
	                </div>
	            </div>
	
	            <div class="control-group">
	                <div class="control-label"><label id="enable_resolution_check-lbl" for="enable_resolution_check" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_RESTRICT_IMAGE_SIZE');?></strong><br/><?php echo JText::_('LNG_RESTRICT_IMAGE_SIZE_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_RESTRICT_IMAGE_SIZE'); ?></label></div>
	                <div class="controls">
	                    <fieldset id="enable_resolution_check_fld" class="radio btn-group btn-group-yesno">
	                        <input type="radio"  name="enable_resolution_check" id="enable_resolution_check1" value="1" <?php echo $this->item->enable_resolution_check==true? 'checked="checked"' :""?> />
	                        <label class="btn" for="enable_resolution_check1"><?php echo JText::_('LNG_YES')?></label>
	                        <input type="radio"  name="enable_resolution_check" id="enable_resolution_check0" value="0" <?php echo $this->item->enable_resolution_check==false? 'checked="checked"' :""?> />
	                        <label class="btn" for="enable_resolution_check0"><?php echo JText::_('LNG_NO')?></label>
	                    </fieldset>
	                </div>
	            </div>
	            <div class="control-group" style="display: none">
                    <div class="control-label"><label id="adaptive_height_gallery-lbl" for="adaptive_height_gallery" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GALLERY_ADAPTIVE_HEIGHT');?></strong><br/><?php echo JText::_('LNG_GALLERY_ADAPTIVE_HEIGHT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GALLERY_ADAPTIVE_HEIGHT'); ?></label></div>
                    <div class="controls">
                        <fieldset id="adaptive_height_gallery_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="adaptive_height_gallery" id="adaptive_height_gallery1" value="1" <?php echo $this->item->adaptive_height_gallery==true? 'checked="checked"' :""?> />
                            <label class="btn" for="adaptive_height_gallery1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="adaptive_height_gallery" id="adaptive_height_gallery0" value="0" <?php echo $this->item->adaptive_height_gallery==false? 'checked="checked"' :""?> />
                            <label class="btn" for="adaptive_height_gallery0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="autoplay_gallery-lbl" for="autoplay_gallery" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GALLERY_AUTOPLAY');?></strong><br/><?php echo JText::_('LNG_GALLERY_AUTOPLAY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GALLERY_AUTOPLAY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="autoplay_gallery_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="autoplay_gallery" id="autoplay_gallery1" value="1" <?php echo $this->item->autoplay_gallery==true? 'checked="checked"' :""?> />
                            <label class="btn" for="autoplay_gallery1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="autoplay_gallery" id="autoplay_gallery0" value="0" <?php echo $this->item->autoplay_gallery==false? 'checked="checked"' :""?> />
                            <label class="btn" for="autoplay_gallery0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="image_display" for="image_display" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_IMAGE_DISPLAY');?></strong><br/><?php echo JText::_('LNG_IMAGE_DISPLAY');?>" title=""><?php echo JText::_('LNG_IMAGE_DISPLAY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="image_display_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="image_display" id="image_display1" value="1" <?php echo $this->item->image_display==1? 'checked="checked"' :""?> />
                            <label class="btn" for="image_display1"><?php echo JText::_('LNG_COVER')?></label>
                            <input type="radio"  name="image_display" id="image_display0" value="2" <?php echo $this->item->image_display==2? 'checked="checked"' :""?> />
                            <label class="btn" for="image_display0"><?php echo JText::_('LNG_CONTAINED')?></label>
                        </fieldset>
                    </div>
                </div>
            </div>
        </fieldset>	
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<fieldset class="form-horizontal">
            <legend><?php echo JText::_('LNG_USER_AUTHENTICATION'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="facebook_client_id-lbl" for="facebook_client_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_FACEBOOK_CLIENT_ID');?></strong><br/><?php echo JText::_('LNG_FACEBOOK_CLIENT_ID_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_FACEBOOK_CLIENT_ID'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=255 id="facebook_client_id" name="facebook_client_id" value="<?php echo $this->item->facebook_client_id?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="facebook_client_secret-lbl" for="facebook_client_secret" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_FACEBOOK_CLIENT_SECRET');?></strong><br/><?php echo JText::_('LNG_FACEBOOK_CLIENT_SECRET_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_FACEBOOK_CLIENT_SECRET'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=255 id="facebook_client_secret" name="facebook_client_secret" value="<?php echo $this->item->facebook_client_secret?>">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="google_client_id-lbl" for="google_client_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GOOGLE_CLIENT_ID');?></strong><br/><?php echo JText::_('LNG_GOOGLE_CLIENT_ID_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GOOGLE_CLIENT_ID'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=255 id="google_client_id" name="google_client_id" value="<?php echo $this->item->google_client_id?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="google_client_secret-lbl" for="google_client_secret" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_GOOGLE_CLIENT_SECRET');?></strong><br/><?php echo JText::_('LNG_GOOGLE_CLIENT_SECRET_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_GOOGLE_CLIENT_SECRET'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=255 id="google_client_secret" name="google_client_secret" value="<?php echo $this->item->google_client_secret?>">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="linkedin_client_id-lbl" for="linkedin_client_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LINKEDIN_CLIENT_ID');?></strong><br/><?php echo JText::_('LNG_LINKEDIN_CLIENT_ID_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LINKEDIN_CLIENT_ID'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=255 id="linkedin_client_id" name="linkedin_client_id" value="<?php echo $this->item->linkedin_client_id?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="linkedin_client_secret-lbl" for="linkedin_client_secret" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LINKEDIN_CLIENT_SECRET');?></strong><br/><?php echo JText::_('LNG_LINKEDIN_CLIENT_SECRET_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_LINKEDIN_CLIENT_SECRET'); ?></label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=255 id="linkedin_client_secret" name="linkedin_client_secret" value="<?php echo $this->item->linkedin_client_secret?>">
                    </div>
                </div>
            </div>
        </fieldset>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<fieldset class="form-horizontal">
            <legend><?php echo JText::_('LNG_TERMS_AND_CONDITIONS'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="show_terms_conditions_article-lbl" for="show_terms_conditions_article" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TERMS_CONDITIONS_ARTICLE');?></strong><br/><?php echo JText::_('LNG_TERMS_CONDITIONS_DESC');?>" title=""><?php echo JText::_('LNG_TERMS_CONDITIONS_ARTICLE'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_terms_conditions_article_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_terms_conditions_article" id="show_terms_conditions_article1" value="1" <?php echo $this->item->show_terms_conditions_article==true? 'checked="checked"' :""?> />
                            <label class="btn" for="show_terms_conditions_article1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_terms_conditions_article" id="show_terms_conditions_article0" value="0" <?php echo $this->item->show_terms_conditions_article==false? 'checked="checked"' :""?> />
                            <label class="btn" for="show_terms_conditions_article0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                    
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="show_privacy-lbl" for="show_privacy" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_PRIVACY');?></strong><br/><?php echo JText::_('LNG_SHOW_PRIVACY_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SHOW_PRIVACY'); ?></label></div>
                    <div class="controls">
                        <fieldset id="show_privacy_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="show_privacy" id="show_privacy1" value="1" <?php echo $this->item->show_privacy==true? 'checked="checked"' :""?> />
                            <label class="btn" for="show_privacy1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio"  name="show_privacy" id="show_privacy0" value="0" <?php echo $this->item->show_privacy==false? 'checked="checked"' :""?> />
                            <label class="btn" for="show_privacy0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
                

            </div>
        </fieldset>
	</div>
</div>

<div class="accordion"><?php echo JText::_('LNG_COMPANY_TERMS_AND_CONDITIONS'); ?></div> 
<div class="panel">
    <?php if($this->appSettings->show_terms_conditions_article == 1) { ?>
        <div class="control-group">
            <div class="control-label"><label id="terms_conditions_article_id-lbl" for="terms_conditions_article_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TERMS_CONDITIONS_ARTICLE_ID');?></strong><br/><?php echo JText::_('LNG_TERMS_CONDITIONS_ARTICLE_ID_DESC');?>" title=""><?php echo JText::_('LNG_TERMS_CONDITIONS_ARTICLE_ID'); ?></label></div>
            <div class="controls">
                <?php
                if($this->appSettings->enable_multilingual) {
                    echo $jbdTabs->startTabSet('tab_group_a_ids');
                    foreach( $this->languagesTranslations  as $k=>$lng ) {
                        echo $jbdTabs->addTab('tab_group_a_ids', 'tab-'.$lng, $k);
                        $langContent = isset($this->terms_conditions_article_id_translations[$lng])?$this->terms_conditions_article_id_translations[$lng]:"";
                        if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)) {
                            $langContent = $this->item->terms_conditions_article_id;
                        }
                        echo "<input type='text' id='terms_conditions_article_id_$lng' name='terms_conditions_article_id_$lng' class='form-control' value=\"" . $this->escape($langContent) . "\" maxLength='20'>";
                        echo $jbdTabs->endTab();
                    }
                    echo $jbdTabs->endTabSet();
                } else { ?>
                    <input type="text" size=40 maxlength=20  id="terms_conditions_article_id" name = "terms_conditions_article_id" value="<?php echo $this->item->terms_conditions_article_id?>">
                <?php } ?>
            </div>
        </div>
    <?php } ?>                
    <div class="row">
        <div class="col-md-12">
            <fieldset class="form-horizontal">
                <div class="control-group">
                    <?php
                    if($this->appSettings->enable_multilingual) {
                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                        foreach ($this->languagesTranslations as $k => $lng) {
                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);

                            $langContent = isset($this->translations[$lng]) ? $this->translations[$lng] : "";

                            if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                $langContent = $this->item->terms_conditions;
                            }

	                        $editor = JBusinessUtil::getEditor();
                            echo $editor->display('terms_conditions_'.$lng, $langContent, '550', '200', '80', '10',false);
                            echo $jbdTabs->endTab();
                        }
                        echo $jbdTabs->endTabSet();
                    } else {
                        $editor = JBusinessUtil::getEditor();
                        echo $editor->display('terms_conditions', $this->item->terms_conditions, '550', '200', '80', '10', false);
                    }
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<div class="accordion" ><?php echo JText::_('LNG_REVIEW_TERMS_AND_CONDITIONS'); ?></div>
<div class="panel">
    <?php if($this->appSettings->show_terms_conditions_article == 1) { ?>
        <div class="control-group">
            <div class="control-label"><label id="reviews_terms_conditions_article_id-lbl" for="reviews_terms_conditions_article_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_REVIEWS_TERMS_CONDITIONS_ARTICLE_ID');?></strong><br/><?php echo JText::_('LNG_REVIEWS_TERMS_ARTICLE_ID_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_REVIEWS_TERMS_CONDITIONS_ARTICLE_ID'); ?></label></div>
            <div class="controls">
            <?php
                if($this->appSettings->enable_multilingual) {
                    echo $jbdTabs->startTabSet('tab_group_a_ids');
                    foreach( $this->languagesTranslations  as $k=>$lng ) {
                        echo $jbdTabs->addTab('tab_group_a_ids', 'tab-'.$lng, $k);
                        $langContent = isset($this->reviews_terms_conditions_article_id_translations[$lng])?$this->reviews_terms_conditions_article_id_translations[$lng]:"";
                        if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)) {
                            $langContent = $this->item->reviews_terms_conditions_article_id;
                        }
                        echo "<input type='text' id='reviews_terms_conditions_article_id_$lng' name='reviews_terms_conditions_article_id_$lng' class='form-control' value=\"" . $this->escape($langContent) . "\" maxLength='20'>";
                        echo $jbdTabs->endTab();
                    }
                    echo $jbdTabs->endTabSet();
                } else { ?>
                    <input type="text" size=40 maxlength=20  id="reviews_terms_conditions_article_id" name = "reviews_terms_conditions_article_id" value="<?php echo $this->item->reviews_terms_conditions_article_id?>">
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <fieldset class="form-horizontal">
                <div class="control-group">
                    <?php
                    if($this->appSettings->enable_multilingual) {
                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                        foreach ($this->languagesTranslations as $k => $lng) {
                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                            $langContent = isset($this->reviews_translations[$lng]) ? $this->reviews_translations[$lng] : "";
                            if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                $langContent = $this->item->reviews_terms_conditions;
                            }

                            $editor = JBusinessUtil::getEditor();
                            echo $editor->display('reviews_terms_conditions_'.$lng, $langContent, '550', '200', '80', '10', false);
                            echo $jbdTabs->endTab();
                        }
                        echo $jbdTabs->endTabSet();
                    } else {
                        $editor = JBusinessUtil::getEditor();
                        echo $editor->display('reviews_terms_conditions', $this->item->reviews_terms_conditions, '550', '200', '80', '10', false);
                    }
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<div class="accordion" ><?php echo JText::_('LNG_CONTACT_TERMS_AND_CONDITIONS'); ?></div>
<div class="panel">
    <?php if($this->appSettings->show_terms_conditions_article == 1) { ?>
        <div class="control-group">
            <div class="control-label"><label id="contact_terms_conditions_article_id-lbl" for="contact_terms_conditions_article_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CONTACT_TERMS_CONDITIONS_ARTICLE_ID');?></strong><br/><?php echo JText::_('LNG_CONTACT_TERMS_ARTICLE_ID_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_CONTACT_TERMS_CONDITIONS_ARTICLE_ID'); ?></label></div>
            <div class="controls">
            <?php
                if($this->appSettings->enable_multilingual) {
                    echo $jbdTabs->startTabSet('tab_group_a_ids');
                    foreach( $this->languagesTranslations  as $k=>$lng ) {
                        echo $jbdTabs->addTab('tab_group_a_ids', 'tab-'.$lng, $k);
                        $langContent = isset($this->contact_terms_conditions_article_id_translations[$lng])?$this->contact_terms_conditions_article_id_translations[$lng]:"";
                        if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)) {
                            $langContent = $this->item->contact_terms_conditions_article_id;
                        }
                        echo "<input type='text' id='contact_terms_conditions_article_id_$lng' name='contact_terms_conditions_article_id_$lng' class='form-control' value=\"" . $this->escape($langContent) . "\" maxLength='20'>";
                        echo $jbdTabs->endTab();
                    }
                    echo $jbdTabs->endTabSet();
                } else { ?>
                    <input type="text" size=40 maxlength=20  id="contact_terms_conditions_article_id" name = "contact_terms_conditions_article_id" value="<?php echo $this->item->contact_terms_conditions_article_id?>">
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="row-fluid">
        <div class="col-md-12">
            <fieldset class="form-horizontal">
                <div class="control-group">
                    <?php
                    if($this->appSettings->enable_multilingual) {
                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                        foreach ($this->languagesTranslations as $k => $lng) {
                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);

                            $langContent = isset($this->contact_translations[$lng]) ? $this->contact_translations[$lng] : "";

                            if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                $langContent = $this->item->contact_terms_conditions;
                            }

                            $editor = JBusinessUtil::getEditor();
                            echo $editor->display('contact_terms_conditions_'.$lng, $langContent, '550', '200', '80', '10', false);
                            echo $jbdTabs->endTab();
                        }
                        echo $jbdTabs->endTabSet();
                    } else {
                        $editor = JBusinessUtil::getEditor();
                        echo $editor->display('contact_terms_conditions', $this->item->contact_terms_conditions, '550', '200', '80', '10', false);
                    }
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="accordion" ><?php echo JText::_('LNG_CONTENT_RESPONSIBLE_PERSON'); ?></div>
<div class="panel">
    <div class="row-fluid">
        <div class="col-md-12">
            <fieldset class="form-horizontal">
                <div class="control-group">
                    <?php
                    if($this->appSettings->enable_multilingual) {
                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                        foreach ($this->languagesTranslations as $k => $lng) {
                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);

                            $langContent = isset($this->content_responsible_translations[$lng]) ? $this->content_responsible_translations[$lng] : "";

                            if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                $langContent = $this->item->content_responsible;
                            }

                            $editor = JBusinessUtil::getEditor();
                            echo $editor->display('content_responsible_'.$lng, $langContent, '550', '200', '80', '10', false);
                            echo $jbdTabs->endTab();
                        }
                        echo $jbdTabs->endTabSet();
                    } else {
                        $editor = JBusinessUtil::getEditor();
                        echo $editor->display('content_responsible', $this->item->content_responsible, '550', '200', '80', '10', false);
                    }
                    ?>
                </div>
                <a href="javascript:void()" id="open_legend">
                    <h5 class="right"><?php echo JText::_('LNG_PLACEHOLDERS_LEGEND'); ?></h5>
                </a>
            </fieldset>
        </div>
    </div>
</div>

<div class="accordion"><?php echo JText::_('LNG_PRIVACY_POLICY'); ?></div>
    <div class="panel">
        <?php if($this->appSettings->show_terms_conditions_article == 1) { ?>
            <div class="control-group">
                <div class="control-label"><label id="privacy_policy_article_id-lbl" for="privacy_policy_article_id" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_PRIVACY_POLICY_ARTICLE_ID');?></strong><br/><?php echo JText::_('LNG_PRIVACY_POLICY_ARTICLE_ID_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_PRIVACY_POLICY_ARTICLE_ID'); ?></label></div>
                <div class="controls">
                    
                <?php
                if($this->appSettings->enable_multilingual) {
                    echo $jbdTabs->startTabSet('tab_group_a_ids');
                    foreach( $this->languagesTranslations  as $k=>$lng ) {
                        echo $jbdTabs->addTab('tab_group_a_ids', 'tab-'.$lng, $k);
                        $langContent = isset($this->privacy_policy_article_id_translations[$lng])?$this->privacy_policy_article_id_translations[$lng]:"";
                        if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)) {
                            $langContent = $this->item->privacy_policy_article_id;
                        }
                        echo "<input type='text' id='privacy_policy_article_id_$lng' name='privacy_policy_article_id_$lng' class='form-control' value=\"" . $this->escape($langContent) . "\" maxLength='20'>";
                        echo $jbdTabs->endTab();
                    }
                    echo $jbdTabs->endTabSet();
                } else { ?>
                    <input type="text" size=40 maxlength=20  id="privacy_policy_article_id" name = "privacy_policy_article_id" value="<?php echo $this->item->privacy_policy_article_id?>">
                <?php } ?>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <fieldset class="form-horizontal">
                    <div class="control-group">
                        <?php
                        if($this->appSettings->enable_multilingual) {
                            echo $jbdTabs->startTabSet('tab_groupsd_id');
                            foreach ($this->languagesTranslations as $k => $lng) {
                                echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);

                                $langContent = isset($this->privacy_policy_translations[$lng]) ? $this->privacy_policy_translations[$lng] : "";

                                if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                    $langContent = $this->item->privacy_policy;
                                }

                                $editor = JBusinessUtil::getEditor();
                                echo $editor->display('privacy_policy_'.$lng, $langContent, '550', '200', '80', '10',false);
                                echo $jbdTabs->endTab();
                            }
                            echo $jbdTabs->endTabSet();
                        } else {
                            $editor = JBusinessUtil::getEditor();
                            echo $editor->display('privacy_policy', $this->item->privacy_policy, '550', '200', '80', '10', false);
                        }   
                        ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="jbd-container" id="legend" style="display:none">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_PLACEHOLDERS_LEGEND') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <dl class="dl-horizontal">
                                <?php foreach ($this->responsiblePersonPlaceholders as $placeholder => $placeholderText){  ?>
                                    <dt><span class="status-badge badge-success"><?php echo $placeholder ?></span></dt>
                                    <dd><?php echo $placeholderText ?></dd>
                                <?php } ?>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var acc = document.getElementsByClassName("accordion");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight){
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }

        window.addEventListener('load', function() {
            uploadInstance.imageUploader(appImgFolder, appImgFolderPath);

            var moderateOn = '<?php echo $this->item->enable_item_moderation ?>';
            if(moderateOn == '1'){
                jQuery('#moderate_item').addClass("show").removeClass("hide");
            }else{
                jQuery('#moderate_item').addClass("hide").removeClass("show");
            };

            jQuery('#open_legend').click(function() {
                jQuery('#legend').jbdModal();
            });
            
            // Hide settings not taken into consideration
            jQuery("#allow_user_creation1").click(function(){
                jQuery(".user-login-position").show(300);
            });
            jQuery("#allow_user_creation0").click(function(){
                jQuery(".user-login-position").hide(300);
            });

            jQuery("#enable_elastic_search1").click(function(){
                jQuery("#elastic_search").show(300);
            });
            jQuery("#enable_elastic_search0").click(function(){
                jQuery("#elastic_search").hide(300);
            });
            
            jQuery("#enable_reviews1").click(function(){
                jQuery("#review-settings").show(300);
            });
            jQuery("#enable_reviews0").click(function(){
                jQuery("#review-settings").hide(300);
            });
        });

        function clearDemoData(){
            jQuery('#clear-demo-data-loading').show();
            if (!confirm(JBD.JText._("LNG_DELETE_DEMO_CONFIRM"))) {
                jQuery('#clear-demo-data-loading').hide();
                return;
            }
            let url = jbdUtils.getAjaxUrl('clearDemoDataAjax', 'applicationsettings');
            jQuery.ajax({
                type:"GET",
                url: url,
                dataType: 'json',
                success: function(data) {
                    jQuery('#clear-demo-data-loading').hide();
                    if (data) {
                        // jQuery('#clear-demo-data').addClass('disabled');
                    } else {
                        alert("<?php echo JText::_('LNG_SOMETHING_WENT_WRONG') ?>");
                    }
                }
            });
        }

        function showItemModeration(className){
            if(className == 'show') {
                jQuery('#moderate_item').addClass(className).removeClass("hide");
            }else{
                jQuery('#moderate_item').addClass(className).removeClass("show");
            }
        }
    </script>
</div>