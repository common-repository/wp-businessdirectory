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
$langs = array();
foreach($this->languages as $lng) {
    array_push($langs, $lng);
}
?>

<script type="text/javascript">
let uploadInstance;

window.addEventListener('load', function() {
    JBD.submitbutton = function (task) {

        jQuery("#item-form").validationEngine('detach');
        let evt = document.createEvent("HTMLEvents");
        evt.initEvent("click", true, true);
       
        if (task == 'trip.cancel' || !jbdUtils.validateCmpForm(true, false)) {
            JBD.submitform(task, document.getElementById('item-form'));
        }
        jQuery("#item-form").validationEngine('attach');
    }

    uploadInstance = JBDUploadHelper.getUploadInstance({
      'removePath': removePath
    });
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=mobileappconfig&layout=edit'); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-7">
                <fieldset class="boxed">
                    <h3><?php echo JText::_('LNG_ORDER_DETAILS',true); ?></h3>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="name"><?php echo JText::_('LNG_ANDROID_ORDER_ID')?> </label>
                                <input type="text" name="androidOrderId" id="androidOrderId" value="<?php echo isset($this->item->androidOrderId)?$this->item->androidOrderId:""?>">
                                <?php echo JText::_('LNG_ORDER_NOTICE')?>
                            </div>
                            <div class="form-group">
                                <label for="name"><?php echo JText::_('LNG_ANDROID_ORDER_EMAIL')?> </label>
                                <input type="text" name="androidOrderEmail" id="androidOrderEmail" value="<?php echo isset($this->item->androidOrderEmail)?$this->item->androidOrderEmail:""?>">
                                <?php echo JText::_('LNG_ORDER_EMAIL_NOTICE')?>
                            </div>
                            <div class="form-group">
                                <label for="name"><?php echo JText::_('LNG_IOS_ORDER_ID')?> </label>
                                <input type="text" name="iosOrderId" id="iosOrderId" value="<?php echo isset($this->item->iosOrderId)?$this->item->iosOrderId:""?>">
                                <?php echo JText::_('LNG_ORDER_NOTICE')?>
                            </div>
                            <div class="form-group">
                                <label for="name"><?php echo JText::_('LNG_IOS_ORDER_EMAIL')?> </label>
                                <input type="text" name="iosOrderEmail" id="iosOrderEmail" value="<?php echo isset($this->item->iosOrderEmail)?$this->item->iosOrderEmail:""?>">
                                <?php echo JText::_('LNG_ORDER_EMAIL_NOTICE')?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    
        <div class="row">
            <div class="col-md-7 order-2">
                <div class="row">
                    <div class="col-md">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_MOBILE_APP_CONFIG'); ?></h2>
                            <p> <?php echo JText::_('LNG_MOBILE_APP_CONFIG_INFORMATION_TEXT'); ?></p>
                            <p> <?php echo JText::_('LNG_MOBILE_APP_CONFIG_DOC'); ?></p>
                            <div id="mobile-config-details">
                                <div class="form-container label-w-100" id="mobile-config-form-box">

                                    <input name="customer" id="customer" value="" size="50" type="hidden">

                                    <div class="form-group">
                                        <label for="app_name"><?php echo JText::_('LNG_APP_NAME') ?></label>                                        
                                        <input name="app_name" id="app_name" value="<?php echo $this->item->app_name?>" size="50" maxlength="255" type="text" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="baseUrl"><?php echo JText::_('LNG_BASE_URL') ?></label>                                        
                                        <input name="baseUrl" id="baseUrl" value="<?php echo $this->item->baseUrl?>" size="50" maxlength="255" type="text" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="user_email"><?php echo JText::_('LNG_USER_EMAIL')?> </label>
                                        <input type="text" name="user_email" id="user_email" value="<?php echo isset($this->item->user_email)?$this->item->user_email:""?>" required>
                                        <div><?php echo JText::_('LNG_USER_EMAIL_DESC') ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="mapsApiKey"><?php echo JText::_('LNG_MAPS_API_KEY') ?></label>                                        
                                        <input name="mapsApiKey" id="mapsApiKey" value="<?php echo $this->item->mapsApiKey?>" size="50" maxlength="255" type="text" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="firebase_server_key"><?php echo JText::_('LNG_FIREBASE_SERVER_KEY') ?></label>                                        
                                        <input type="text" size=40 maxlength=255  id="firebase_server_key" name="firebase_server_key" value="<?php echo $this->item->firebase_server_key?>" >
                                    </div>
                                    

                                    <div class="form-group">
                                        <label for="primaryColor"><?php echo JText::_('LNG_PRIMARY_COLOR') ?></label>                                        
                                        <input name="primaryColor" id="primaryColor" value="<?php echo $this->item->primaryColor?>" size="50" maxlength="255" type="color">
                                    </div>

                                    <div class="form-group">
                                        <label for="backgroundColor"><?php echo JText::_('LNG_BACKGROUND_COLOR') ?></label>                                        
                                        <input name="backgroundColor" id="backgroundColor" value="<?php echo $this->item->backgroundColor?>" size="50" maxlength="255" type="color">
                                    </div>

                                    <div class="form-group">
                                        <label for="textPrimary"><?php echo JText::_('LNG_TEXT_PRIMARY') ?></label>                                        
                                        <input name="textPrimary" id="textPrimary" value="<?php echo $this->item->textPrimary?>" size="50" maxlength="255" type="color">
                                    </div>

                                    <div class="form-group">
                                        <label for="genericText"><?php echo JText::_('LNG_GENERIC_TEXT') ?></label>                                        
                                        <input name="genericText" id="genericText" value="<?php echo $this->item->genericText?>" size="50" maxlength="255" type="color">
                                    </div>

                                    <div class="form-group">
                                        <label for="iconColor"><?php echo JText::_('LNG_ICON_COLOR') ?></label>                                        
                                        <input name="iconColor" id="iconColor" value="<?php echo $this->item->iconColor?>" size="50" maxlength="255" type="color">
                                    </div>


                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_IS_LOCATION_MANDATORY') ?></label>                                           
                                            <fieldset id="isLocationMandatory_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="isLocationMandatory" id="isLocationMandatory1" value="1"<?php echo $this->item->isLocationMandatory==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="isLocationMandatory1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="isLocationMandatory" id="isLocationMandatory0" value="0"<?php echo $this->item->isLocationMandatory==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="isLocationMandatory0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="showLatestListings"><?php echo JText::_('LNG_SHOW_LATEST_LISTINGS') ?></label>                                        
                                            <fieldset id="showLatestListings_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showLatestListings" id="showLatestListings1" value="1" <?php echo $this->item->showLatestListings==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showLatestListings1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showLatestListings" id="showLatestListings0" value="0" <?php echo $this->item->showLatestListings==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showLatestListings0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>

                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SHOW_FEATURED_LISTINGS') ?></label>                                        
                                            <fieldset id="showFeaturedListings_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showFeaturedListings" id="showFeaturedListings1" value="1" <?php echo $this->item->showFeaturedListings==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showFeaturedListings1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showFeaturedListings" id="showFeaturedListings0" value="0" <?php echo $this->item->showFeaturedListings==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showFeaturedListings0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SHOW_FEATURED_OFFERS') ?></label>                                        
                                            <fieldset id="showFeaturedOffers_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showFeaturedOffers" id="showFeaturedOffers1" value="1" <?php echo $this->item->showFeaturedOffers==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showFeaturedOffers1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showFeaturedOffers" id="showFeaturedOffers0" value="0" <?php echo $this->item->showFeaturedOffers==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showFeaturedOffers0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SHOW_FEATURED_EVENTS') ?></label>                                        
                                            <fieldset id="showFeaturedEvents_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showFeaturedEvents" id="showFeaturedEvents1" value="1" <?php echo $this->item->showFeaturedEvents==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showFeaturedEvents1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showFeaturedEvents" id="showFeaturedEvents0" value="0" <?php echo $this->item->showFeaturedEvents==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showFeaturedEvents0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SHOW_OFFERS') ?></label>                                        
                                            <fieldset id="showOffers_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showOffers" id="showOffers1" value="1" <?php echo $this->item->showOffers==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showOffers1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showOffers" id="showOffers0" value="0" <?php echo $this->item->showOffers==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showOffers0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SHOW_EVENTS') ?></label>                                        
                                            <fieldset id="showEvents_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showEvents" id="showEvents1" value="1" <?php echo $this->item->showEvents==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showEvents1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showEvents" id="showEvents0" value="0" <?php echo $this->item->showEvents==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showEvents0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_ENABLE_REVIEWS') ?></label>                                        
                                            <fieldset id="enableReviews_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="enableReviews" id="enableReviews1" value="1" <?php echo $this->item->enableReviews==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="enableReviews1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="enableReviews" id="enableReviews0" value="0" <?php echo $this->item->enableReviews==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="enableReviews0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SHOW_MESSAGES') ?></label>                                        
                                            <fieldset id="showMessages_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showMessages" id="showMessages1" value="1" <?php echo $this->item->showMessages==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showMessages1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showMessages" id="showMessages0" value="0" <?php echo $this->item->showMessages==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showMessages0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SELECT_PLATFORM') ?></label>                                        
                                            <fieldset id="isJoomla_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="isJoomla" id="isJoomla1" value="1" <?php echo $this->item->isJoomla==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="isJoomla1"><?php echo JText::_('LNG_JOOMLA')?></label> 
                                                <input type="radio"  name="isJoomla" id="isJoomla0" value="0" <?php echo $this->item->isJoomla==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="isJoomla0"><?php echo JText::_('LNG_WORDPRESS')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="allowGuests"><?php echo JText::_('LNG_ALLOW_GUESTS') ?></label>                                        
                                            <fieldset id="allowGuests_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="allowGuests" id="allowGuests1" value="1" <?php echo $this->item->allowGuests==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="allowGuests1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="allowGuests" id="allowGuests0" value="0" <?php echo $this->item->allowGuests==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="allowGuests0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="showEmails"><?php echo JText::_('LNG_DISPLAY_EMAILS') ?></label>                                        
                                            <fieldset id="showEmails_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="showEmails" id="showEmails1" value="1" <?php echo $this->item->showEmails==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="showEmails1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="showEmails" id="showEmails0" value="0" <?php echo $this->item->showEmails==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="showEmails0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="enableGoogleLogin"><?php echo JText::_('LNG_ENABLE_GOOGLE_LOGIN') ?></label>                                        
                                            <fieldset id="enableGoogleLogin_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="enableGoogleLogin" id="enableGoogleLogin1" value="1" <?php echo $this->item->enableGoogleLogin==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="enableGoogleLogin1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="enableGoogleLogin" id="enableGoogleLogin0" value="0" <?php echo $this->item->enableGoogleLogin==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="enableGoogleLogin0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div>
                                            <label for="enableFacebookLogin"><?php echo JText::_('LNG_ENABLE_FACEBOOK_LOGIN') ?></label>                                        
                                            <fieldset id="enableFacebookLogin_fld" class="radio btn-group btn-group-yesno">
                                                <input type="radio"  name="enableFacebookLogin" id="enableFacebookLogin1" value="1" <?php echo $this->item->enableFacebookLogin==true? 'checked="checked"' :""?> />
                                                <label class="btn" for="enableFacebookLogin1"><?php echo JText::_('LNG_YES')?></label> 
                                                <input type="radio"  name="enableFacebookLogin" id="enableFacebookLogin0" value="0" <?php echo $this->item->enableFacebookLogin==false? 'checked="checked"' :""?> />
                                                <label class="btn" for="enableFacebookLogin0"><?php echo JText::_('LNG_NO')?></label>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div id="facebook-configurations" style="<?php echo $this->item->enableFacebookLogin == 0? "display:none" :"" ?>">
                                        <div class="form-group">
                                            <label for="facebook_app_name"><?php echo JText::_('LNG_FACEBOOK_APP_NAME') ?></label>                                        
                                            <input type="text" size=40 maxlength=255  id="facebook_app_name" name="facebook_app_name" value="<?php echo $this->item->facebook_app_name?>" >
                                        </div>

                                        <div class="form-group">
                                            <label for="facebook_app_id"><?php echo JText::_('LNG_FACEBOOK_APP_ID') ?></label>                                        
                                            <input type="text" size=40 maxlength=255  id="facebook_app_id" name="facebook_app_id" value="<?php echo $this->item->facebook_app_id?>" >
                                        </div>

                                        <div class="form-group">
                                            <label for="fb_login_protocol_scheme"><?php echo JText::_('LNG_FACEBOOK_LOGIN_PROTOCOL_SCHEME') ?></label>                                        
                                            <input type="text" size=40 maxlength=255  id="fb_login_protocol_scheme" name="fb_login_protocol_scheme" value="<?php echo $this->item->fb_login_protocol_scheme?>" >
                                        </div>

                                        <div class="form-group">
                                            <label for="facebook_client_token"><?php echo JText::_('LNG_FACEBOOK_CLIENT_TOKEN') ?></label>                                        
                                            <input type="text" size=40 maxlength=255  id="facebook_client_token" name="facebook_client_token" value="<?php echo $this->item->facebook_client_token?>" >
                                        </div>
                                    </div>
                                  

                                    <div class="col-md">
                                        <div>
                                            <p> <?php echo JText::_('LNG_MOBILE_APP_CONFIG_LANGAUGES'); ?></p>
                                            <label for="isLocationMandatory"><?php echo JText::_('LNG_SELECT_LANGUAGES') ?></label>
                                            <select	id="language_keys[]" name="language_keys[]" data-placeholder="<?php echo JText::_("LNG_SELECT_LANGUAGES") ?>" class="chzn-color" multiple>
                                                <?php
                                                foreach($this->languages as $key => $lng) {
                                                    $selected = "";
                                                    if (!empty($this->item->language_keys)) {
                                                        if (in_array($lng, $this->item->language_keys))
                                                            $selected = "selected";
                                                    } ?>
                                                    <option value='<?php echo $lng ?>' <?php echo $selected ?>> <?php echo $key ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php foreach($this->languages as $language) { ?>
                                        <?php if (in_array($language, (array) $this->item->language_keys)) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="app_<?php echo $language; ?>"><?php echo JText::_('LNG_LANGUAGE_FILE'); ?> - <?php echo $language; ?></label>
                                                    <input type="file" id="app_<?php echo $language; ?>" name="app_<?php echo substr($language, 0, 2); ?>" accept=".arb" required>
                                                    <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . 'app_'.substr($language, 0, 2). '.arb')) : ?>
                                                        <div class="text-success small"><?php echo JText::_('LNG_FILE_UPLOADED'); ?></div>
                                                    <?php endif; ?>
                                                    <div>(*.arb)</div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </fieldset>                     
                        <hr/>
                    </div>
                </div>
                
            </div>
            <div class="col-12 order-1 col-md-5 col-lg-4 order-md-2 ml-0 ml-lg-auto">				
                <?php require_once 'mobile_device_ui.php'; ?>
            </div>
        </div>
        
        <?php 
            $menus = $this->item->menus;
            $icons = [  "address-book",  "calendar",  "check",  "clock",  "settings",  "envelope",  "warning",  "globe",  "heart",  "info",  "lock",  "map-marker",  "pencil",  "phone",  "question",  "search",  "star",  "thumb-up", "thumb-down",  "user", "vehicle", "bell",  "camera",  "comment",  "credit-card",  "file",  "folder",  "image",  "paperclip",  "power-off",  "trash"];
        ?>

        <div class="row">
            <div class="col-md-7">
                <fieldset class="boxed">
                    <h2> <?php echo JText::_('LNG_CUSTOM_MENUS'); ?></h2>
                    <p> <?php echo JText::_('LNG_USER_CUSTOM_MENU'); ?></p>
                    <div class="form-group">
                        <?php echo JText::_('LNG_ADD_MENU') ?>
                        <a href="javascript:void(0);" id="add-menu-field" class="btn btn-success ml-3"><i class="la la-plus"></i></a>

                        <div class="form-container mt-3">
                            <div class="form-group" id="menu-container">
                                <?php foreach ($menus as $menu) { ?>
                                    <?php if (!empty($menu->id)) { ?>
                                        <div class="mb-2">
                                            <div class="d-flex">
                                                <input type="text" class="validate[required]" maxlength="45" name="title[]" value="<?php echo $menu->title ?>" placeholder="<?php echo JText::_('LNG_TITLE') ?>"></input>
                                                <input class="mx-1 validate[required]" maxlength="255" type="text" name="urls[]" value="<?php echo $menu->url ?>" placeholder="<?php echo JText::_('LNG_MENU') ?>"></input>
                                                <select id="icon-holder" name="icons[]" data-placeholder="<?php echo JText::_('LNG_CHOOSE_ICON') ?>" class="icon-select">
                                                    <!-- <option value=""><?php echo JText::_('LNG_CHOOSE_ICON') ?></option> -->
                                                    <?php foreach($icons as $icon) { ?>
                                                        <option value="<?php echo $icon; ?>" <?php echo $menu->icon == $icon ? 'selected' : ''; ?>><?php echo $icon; ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>

                                                <select class="user-group mr-1" name="groups[]" data-placeholder="<?php echo JText::_('LNG_USER_GROUP') ?>">
                                                    <!-- <option value=""><?php echo JText::_('LNG_CHOSE_USER_GROUP') ?></option> -->
                                                    <option value="user" <?php echo $menu->group == 'user' ? 'selected' : ''; ?>><?php echo JText::_('LNG_ALL_USERS') ?></option>
                                                    <option value="registered" <?php echo $menu->group == 'registered' ? 'selected' : ''; ?>><?php echo JText::_('LNG_REGISTERED_USERS') ?></option>
                                                    <option value="business" <?php echo $menu->group == 'business' ? 'selected' : ''; ?>><?php echo JText::_('LNG_BUSINESS_OWNERS_ONLY') ?></option>
                                                </select>

                                                <select class="user-group" name="positions[]" data-placeholder="<?php echo JText::_('LNG_CHOSE_POSITION') ?>">
                                                    <!-- <option value=""><?php echo JText::_('LNG_CHOSE_POSITION') ?></option> -->
                                                    <option value="dashboard" <?php echo $menu->position == 'dashboard' ? 'selected' : ''; ?>><?php echo JText::_('LNG_DASHBOARD') ?></option>
                                                    <option value="drawer" <?php echo $menu->position == 'drawer' ? 'selected' : ''; ?>><?php echo JText::_('LNG_SIDE_DRAWER') ?></option>
                                                </select>

                                                <select class="user-group" name="types[]" data-placeholder="<?php echo JText::_('LNG_CHOSE_TYPE') ?>">
                                                    <!-- <option value=""><?php echo JText::_('LNG_CHOSE_TYPE') ?></option> -->
                                                    <option value="url" <?php echo $menu->type == 'url' ? 'selected' : ''; ?>><?php echo JText::_('LNG_URL') ?></option>
                                                    <option value="phone" <?php echo $menu->type == 'phone' ? 'selected' : ''; ?>><?php echo JText::_('LNG_PHONE') ?></option>
                                                    <option value="email" <?php echo $menu->type == 'email' ? 'selected' : ''; ?>><?php echo JText::_('LNG_EMAIL') ?></option>
                                                </select>

                                                <select id="icon-holder" name="langs[]" data-placeholder="<?php echo JText::_('LNG_CHOSE_LANGUAGE') ?>" class="icon-select">
                                                    <!-- <option value=""><?php echo JText::_('LNG_CHOSE_LANGUAGE') ?></option> -->
                                                    <?php foreach($this->languages as $language) { ?>
                                                        <option value="<?php echo $language; ?>" <?php echo $menu->lang == $language ? 'selected' : ''; ?>><?php echo $language; ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>

                                                <input type="hidden" type="text" name="menu_id[]" value="<?php echo $menu->id ?>"></input>
                                                <a href="javascript:void(0);" onclick="jQuery(this).parent().parent().remove()" class="btn btn-danger ml-2"><i class="la la-trash"></i></a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <fieldset class="boxed">
                    <h2> <?php echo JText::_('LNG_MOBILE_APP_UPLOADS'); ?></h2>
                    <p> <?php echo JText::_('LNG_MOBILE_APP_UPLOADS_INFORMATION_TEXT'); ?></p>

                    <div class="form-container label-w-100" id="form-box">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="slide1"><?php echo JText::_('LNG_SLIDE_1'); ?></label>
                            <input type="file" id="slide1" name="slide1" accept="image/png, image/jpeg, image/gif" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->slide1)) : ?>
                            <img style="max-width:250px;max-height:200px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->slide1?>"/>
                            <?php endif; ?>
                            <div>(*.jpg, *.png, *.gif)</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="slide2"><?php echo JText::_('LNG_SLIDE_2'); ?></label>
                            <input type="file" id="slide2" name="slide2" accept="image/png, image/jpeg, image/gif" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->slide2)) : ?>
                                <img style="max-width:250px;max-height:200px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->slide2?>"/>
                            <?php endif; ?>
                            <div>(*.jpg, *.png, *.gif)</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="slide3"><?php echo JText::_('LNG_SLIDE_3'); ?></label>
                            <input type="file" id="slide3" name="slide3" accept="image/png, image/jpeg, image/gif" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->slide3)) : ?>
                                <img style="max-width:250px;max-height:200px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->slide3?>"/>
                            <?php endif; ?>
                            <div>(*.jpg, *.png, *.gif)</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="login_img"><?php echo JText::_('LNG_LOGIN_SCREEN_IMG'); ?></label>
                            <input type="file" id="login_img" name="login_img" accept="image/png, image/jpeg, image/gif" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->login_img)) : ?>
                                <img style="max-width:250px;max-height:200px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->login_img?>"/>
                            <?php endif; ?>
                            <div>(*.jpg, *.png, *.gif)</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="home_header"><?php echo JText::_('LNG_HOME_HEADER'); ?></label>
                            <input type="file" id="home_header" name="home_header" accept="image/png, image/jpeg, image/gif" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->home_header)) : ?>
                                <img style="max-width:250px;max-height:200px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->home_header?>"/>
                            <?php endif; ?>
                            <div>(*.jpg, *.png, *.gif)</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="featured_placeholder"><?php echo JText::_('LNG_FEATURED_PLACEHOLDER'); ?></label>
                            <input type="file" id="featured_placeholder" name="featured_placeholder" accept="image/png, image/jpeg, image/gif" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->featured_placeholder)) : ?>
                                <img style="max-width:250px;max-height:200px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->featured_placeholder?>"/>
                            <?php endif; ?>
                            <div>(*.jpg, *.png, *.gif)</div>
                        </div>
                    </div>
                    <fieldset class="boxed">
                        <h5><?php echo JText::_('LNG_ANDROID_FILES'); ?></h5>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="logo_android"><?php echo JText::_('LNG_LOGO'); ?></label>
                                <input type="file" id="logo_android" name="logo_android" required>
                                <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->logo_android)) : ?>
                                    <img style="max-width:75px;max-height:75px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->logo_android?>"/>
                                <?php endif; ?>
                                <div><?php echo JText::_('LNG_LOGO_ANDROID_DESC'); ?> </div>
                            </div>
                        </div>

                        <div class="col-md">
                            <div class="form-group">
                                <label for="logo_android_nb"><?php echo JText::_('LNG_LOGO_NB'); ?></label>
                                <input type="file" id="logo_android_nb" name="logo_android_nb" required>
                                <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->logo_android_nb)) : ?>
                                <img style="max-width:75px;max-height:75px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->logo_android_nb?>"/>
                                <?php endif; ?>
                                <div>- transparent background</div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="google-services"><?php echo JText::_('LNG_GOOGLE_SERVICES'); ?></label>
                                <input type="file" id="google-services" name="google-services" required>
                                <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->{'google-services'})) : ?>
                                <span class="text-success small"><?php echo JText::_('LNG_FILE_UPLOADED'); ?></span>
                                <?php endif; ?>
                                <div>(Google google-services.json file from Firebase project)</div>
                            </div>
                        </div>

                    </fieldset>
                    <fieldset class="boxed">
                    <h5><?php echo JText::_('LNG_IOS_FILES'); ?></h5>
                    <div class="col-md">
                        <div class="form-group">
                            <label for="logo_ios"><?php echo JText::_('LNG_LOGO'); ?></label>
                            <input type="file" id="logo_ios" name="logo_ios" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->logo_ios)) : ?>
                                <img style="max-width:75px;max-height:75px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->logo_ios?>"/>
                            <?php endif; ?>
                            <div>Image will also be used as the app icon. Please do not use transparency.</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="logo_ios_nb"><?php echo JText::_('LNG_LOGO_NB'); ?></label>
                            <input type="file" id="logo_ios_nb" name="logo_ios_nb" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->logo_ios_nb)) : ?>
                                <img style="max-width:75px;max-height:75px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->logo_ios_nb?>"/>
                            <?php endif; ?>
                            <div>- transparent background</div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-group">
                            <label for="google-plist"><?php echo JText::_('LNG_GOOGLE_PLIST'); ?></label>
                            <input type="file" id="google-plist" name="google-plist" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->{'google-plist'})) : ?>
                            <span class="text-success small"><?php echo JText::_('LNG_FILE_UPLOADED'); ?></span>
                            <?php endif; ?>
                            <div>(Google GoogleService-Info.plist file from Firebase project)</div>
                        </div>
                    </div>


                    <div class="col-md">
                        <div class="form-group">
                            <label for="mobileprovisioning"><?php echo JText::_('LNG_MOBILE_PROVISIONING'); ?></label>
                            <input type="file" id="mobileprovisioning" name="mobileprovisioning" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->mobileprovisioning)) : ?>
                            <span class="text-success small"><?php echo JText::_('LNG_FILE_UPLOADED'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                            <label for="certificate"><?php echo JText::_('LNG_CERTIFICATE_PKEY'); ?></label>
                            <input type="file" id="certificate" name="certificate" required>
                            <?php if (is_file(BD_MOBILE_APP_BUILD_UPLOAD_PATH . DS . $this->item->certificate)) : ?>
                            <span class="text-success small"><?php echo JText::_('LNG_FILE_UPLOADED'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    </fieldset>
                    
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <fieldset class="boxed">
                    <h2> <?php echo JText::_('LNG_MOBILE_APP_SERVER_CONFIGURATIONS'); ?></h2>
                    <p> <?php echo JText::_('LNG_MOBILE_APP_SERVER_CONFIGURATIONS_INFORMATION_TEXT'); ?></p>
                    <div id="mobile-server-config-details">
                        <div class="form-container label-w-100" id="mobile-server-config-form-box">
                            <div class="control-group">
                                <div class="control-label"><label id="mobile_only_featured_listings-lbl" for="mobile_only_featured_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ONLY_FEATURED_LISTINGS');?></strong><br/><?php echo JText::_('LNG_ONLY_FEATURED_LISTINGS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ONLY_FEATURED_LISTINGS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="only_featured_listings_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="mobile_only_featured_listings" id="only_featured_listings1" value="1" <?php echo $this->item->mobile_only_featured_listings==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="only_featured_listings1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="mobile_only_featured_listings" id="only_featured_listings0" value="0" <?php echo $this->item->mobile_only_featured_listings==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="only_featured_listings0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="mobile_only_featured_offers-lbl" for="mobile_only_featured_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ONLY_FEATURED_OFFERS');?></strong><br/><?php echo JText::_('LNG_ONLY_FEATURED_OFFERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ONLY_FEATURED_OFFERS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="only_featured_offers_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="mobile_only_featured_offers" id="only_featured_offers1" value="1" <?php echo $this->item->mobile_only_featured_offers==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="only_featured_offers1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="mobile_only_featured_offers" id="only_featured_offers0" value="0" <?php echo $this->item->mobile_only_featured_offers==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="only_featured_offers0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="mobile_only_featured_events-lbl" for="mobile_only_featured_events" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ONLY_FEATURED_EVENTS');?></strong><br/><?php echo JText::_('LNG_ONLY_FEATURED_EVENTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ONLY_FEATURED_EVENTS'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="only_featured_events_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="mobile_only_featured_events" id="only_featured_events1" value="1" <?php echo $this->item->mobile_only_featured_events==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="only_featured_events1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="mobile_only_featured_events" id="only_featured_events0" value="0" <?php echo $this->item->mobile_only_featured_events==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="only_featured_events0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="mobile-company-categories-filter-lbl" for="mobile-company-categories-filter[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_COMPANY_CATEGORIES_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_COMPANY_CATEGORIES_FILTER_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_COMPANY_CATEGORIES_FILTER_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <select	id="mobile_company_categories_filter[]" name="mobile_company_categories_filter[]" data-placeholder="<?php echo JText::_("LNG_SELECT_CATEGORY_FILTER") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->mainCategoriesOptions as $field) {
                                            $selected = "";
                                            if (!empty($this->item->mobile_company_categories_filter)) {
                                                if (in_array($field->value, $this->item->mobile_company_categories_filter))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->text ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="mobile-offer-categories-filter-lbl" for="mobile-offer-categories-filter[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_OFFER_CATEGORIES_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_OFFER_CATEGORIES_FILTER_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_OFFER_CATEGORIES_FILTER_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <select	id="mobile_offer_categories_filter[]" name="mobile_offer_categories_filter[]" data-placeholder="<?php echo JText::_("LNG_SELECT_CATEGORY_FILTER") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->offerCategoriesOptions as $field) {
                                            $selected = "";
                                            if (!empty($this->item->mobile_offer_categories_filter)) {
                                                if (in_array($field->value, $this->item->mobile_offer_categories_filter))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->text ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><label id="mobile-event-categories-filter-lbl" for="mobile-event-categories-filter[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_EVENT_CATEGORIES_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_EVENT_CATEGORIES_FILTER_FIELDS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_EVENT_CATEGORIES_FILTER_FIELDS'); ?></label></div>
                                <div class="controls">
                                    <select	id="mobile_event_categories_filter[]" name="mobile_event_categories_filter[]" data-placeholder="<?php echo JText::_("LNG_SELECT_CATEGORY_FILTER") ?>" class="chzn-color" multiple>
                                        <?php
                                        foreach($this->eventCategoriesOptions as $field) {
                                            $selected = "";
                                            if (!empty($this->item->mobile_event_categories_filter)) {
                                                if (in_array($field->value, $this->item->mobile_event_categories_filter))
                                                    $selected = "selected";
                                            } ?>
                                            <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->text ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label"><label id="mobile_list_limit-lbl" for="mobile_list_limit" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MOBILE_LIST_LIMIT');?></strong><br/><?php echo JText::_('LNG_MOBILE_LIST_LIMIT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MOBILE_LIST_LIMIT'); ?></label></div>
                                <div class="controls">
                                    <select id='mobile_list_limit' name='mobile_list_limit'>
                                        <option value="5" <?php echo $this->item->mobile_list_limit==5? "selected" : ""?>>5</option>
                                        <option value="10" <?php echo $this->item->mobile_list_limit==10? "selected" : ""?>>10</option>
                                        <option value="15" <?php echo $this->item->mobile_list_limit==15? "selected" : ""?>>15</option>
                                        <option value="20" <?php echo $this->item->mobile_list_limit==20? "selected" : ""?>>20</option>
                                        <option value="25" <?php echo $this->item->mobile_list_limit==25? "selected" : ""?>>25</option>
                                        <option value="30" <?php echo $this->item->mobile_list_limit==30? "selected" : ""?>>30</option>
                                        <option value="50" <?php echo $this->item->mobile_list_limit==50? "selected" : ""?>>50</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 app-settings">
                            <fieldset class="form-horizontal">
                                <legend><?php echo JText::_('LNG_MEDIA'); ?></legend>
                                <div class="form-container">
                                    <div class="control-group">
                                        <div class="control-label"><label id="mobile_business_img-image-uploader-lbl" for="mobile_business_img-image-uploader" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MOBILE_BUSINESS_IMG');?></strong><br/><?php echo JText::_('LNG_MOBILE_BUSINESS_IMG_DESC');?>" title=""><?php echo JText::_('LNG_MOBILE_BUSINESS_IMG'); ?><span class="star">&nbsp;</span></label></div>
                                        <div class="controls">
                                            <div class="jupload logo-jupload">
                                                <div class="jupload-body">
                                                    <div class="jupload-files">
                                                        <div class="jupload-files-img image-fit-contain" id="mobile_business_img-picture-preview">
                                                            <?php
                                                            if (!empty($this->item->mobile_business_img)) {
                                                                echo '<img  id="mobile_business_imgImg" src="'.BD_PICTURES_PATH.$this->item->mobile_business_img.'"/>';
                                                            }else{
                                                                echo '<i class="la la-image"></i>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" name="mobile_business_img" style="visibility:hidden;height:1px; width:1px;" id="mobile_business_img-imageLocation" class="form-control" value="<?php echo $this->item->mobile_business_img?>" >
                                                <div class="jupload-footer">
                                                    <fieldset>
                                                        <input type="file" id="mobile_business_img-imageUploader" name="uploadLogo" size="50" required>
                                                    </fieldset>
                                                    <div class="btn-group">
                                                        <label for="mobile_business_img-imageUploader" class="btn btn-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                                        <a name="" id="" class="btn btn-danger" href="javascript:uploadInstance.removeImage('mobile_business_img-')" role="button"><?php echo JText::_("LNG_REMOVE")?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="control-label"><label id="mobile_offer_img-image-uploader-lbl" for="mobile_offer_img-image-uploader" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MOBILE_OFFER_IMG');?></strong><br/><?php echo JText::_('LNG_MOBILE_OFFER_IMG_DESC');?>" title=""><?php echo JText::_('LNG_MOBILE_OFFER_IMG'); ?><span class="star">&nbsp;</span></label></div>
                                        <div class="controls">
                                            <div class="jupload logo-jupload">
                                                <div class="jupload-body">
                                                    <div class="jupload-files">
                                                        <div class="jupload-files-img image-fit-contain" id="mobile_offer_img-picture-preview">
                                                            <?php
                                                            if (!empty($this->item->mobile_offer_img)) {
                                                                echo '<img  id="mobile_offer_imgImg" src="'.BD_PICTURES_PATH.$this->item->mobile_offer_img.'"/>';
                                                            }else{
                                                                echo '<i class="la la-image"></i>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" name="mobile_offer_img" style="visibility:hidden;height:1px; width:1px;" id="mobile_offer_img-imageLocation" class="form-control" value="<?php echo $this->item->mobile_offer_img?>" >
                                                <div class="jupload-footer">
                                                    <fieldset>
                                                        <input type="file" id="mobile_offer_img-imageUploader" name="uploadLogo" size="50">
                                                    </fieldset>
                                                    <div class="btn-group">
                                                        <label for="mobile_offer_img-imageUploader" class="btn btn-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                                        <a name="" id="" class="btn btn-danger" href="javascript:uploadInstance.removeImage('mobile_offer_img-')" role="button"><?php echo JText::_("LNG_REMOVE")?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="control-label"><label id="mobile_event_img-image-uploader-lbl" for="mobile_event_img-image-uploader" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MOBILE_EVENT_IMG');?></strong><br/><?php echo JText::_('LNG_MOBILE_EVENT_IMG_DESC');?>" title=""><?php echo JText::_('LNG_MOBILE_EVENT_IMG'); ?><span class="star">&nbsp;</span></label></div>
                                        <div class="controls">
                                            <div class="jupload logo-jupload">
                                                <div class="jupload-body">
                                                    <div class="jupload-files">
                                                        <div class="jupload-files-img image-fit-contain" id="mobile_event_img-picture-preview">
                                                            <?php
                                                            if (!empty($this->item->mobile_event_img)) {
                                                                echo '<img  id="mobile_event_imgImg" src="'.BD_PICTURES_PATH.$this->item->mobile_event_img.'"/>';
                                                            }else{
                                                                echo '<i class="la la-image"></i>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" name="mobile_event_img" style="visibility:hidden;height:1px; width:1px;" id="mobile_event_img-imageLocation" class="form-control" value="<?php echo $this->item->mobile_event_img?>" >
                                                <div class="jupload-footer">
                                                    <fieldset>
                                                        <input type="file" id="mobile_event_img-imageUploader" name="uploadLogo" size="50">
                                                    </fieldset>
                                                    <div class="btn-group">
                                                        <label for="mobile_event_img-imageUploader" class="btn btn-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                                        <a name="" id="" class="btn btn-danger" href="javascript:uploadInstance.removeImage('mobile_event_img-')" role="button"><?php echo JText::_("LNG_REMOVE")?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

    	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    	<input type="hidden" name="task" value="" />
    	<input type="hidden" name="app_id" value="<?php echo $this->item->app_id?>" />
    	<?php echo JHtml::_('form.token'); ?>
    </form>
</div>

<script>
	function clearColor() {
		jQuery("#colorpicker").val("");
		jQuery(".minicolors-swatch").html("");
	}
    
    let appImgFolder = '<?php echo APP_PICTURES_PATH ?>';
    let appImgFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_COMPANY_LOGO?>&_path_type=1&_target=<?php echo urlencode(APP_PICTURES_PATH)?>';
	let removePath = '<?php echo JBusinessUtil::getUploadUrl('remove') ?>&_path_type=2&_filename=';
    
    window.addEventListener('load', function() {
        jQuery('select').chosen();
        jbdUtils.setProperty("enable_crop", false);


        uploadInstance.imageUploader(appImgFolder, appImgFolderPath, 'mobile_business_img-');
        uploadInstance.imageUploader(appImgFolder, appImgFolderPath, 'mobile_offer_img-');
        uploadInstance.imageUploader(appImgFolder, appImgFolderPath, 'mobile_event_img-');

        let icons = <?php echo json_encode($icons); ?>;
        let langs = <?php echo json_encode($langs); ?>;

        jQuery('#add-menu-field').click(function(){
            jQuery('#menu-container').append(`
            <div class="mb-2">
                <div class="d-flex">
                <input type="text" class="validate[required]" maxlength="45" name="title[]" value="" placeholder="<?php echo JText::_('LNG_TITLE') ?>">
                <input class="mx-1 validate[required]" maxlength="255" type="text" name="urls[]" value="" placeholder="<?php echo JText::_('LNG_MENU') ?>">
                <select id="icon-holder" name="icons[]" data-placeholder="<?php echo JText::_('LNG_CHOOSE_ICON') ?>" class="icon-select">
                    ${icons.map(icon => `<option value="${icon}">${icon}</option>`)}
                </select>
                <select class="user-group mr-1" name="groups[]" data-placeholder="<?php echo JText::_('LNG_USER_GROUP') ?>">
                    <option value="user"><?php echo JText::_('LNG_ALL_USERS') ?></option>
                    <option value="registered"><?php echo JText::_('LNG_REGISTERED_USERS') ?></option>
                    <option value="business"><?php echo JText::_('LNG_BUSINESS_OWNER') ?></option>
                </select>
                <select class="" name="positions[]" data-placeholder="<?php echo JText::_('LNG_CHOSE_POSITION') ?>">
                    <option value="drawer"><?php echo JText::_('LNG_SIDE_DRAWER') ?></option>
                    <option value="dashboard"><?php echo JText::_('LNG_DASHBOARD') ?></option>
                </select>
                <select class="" name="types[]" data-placeholder="<?php echo JText::_('LNG_CHOSE_TYPE') ?>">
                    <option value="url"><?php echo JText::_('LNG_URL') ?></option>
                    <option value="phone"><?php echo JText::_('LNG_PHONE') ?></option>
                    <option value="email"><?php echo JText::_('LNG_EMAIL') ?></option>
                </select>
                <select id="icon-holder" name="langs[]" data-placeholder="<?php echo JText::_('LNG_CHOSE_LANGUAGE') ?>" class="icon-select">
                    ${langs.map(lang => `<option value="${lang}">${lang}</option>`)}
                </select>
                <input type="hidden" name="menu_id[]" value="">
                <a href="javascript:void(0);" onclick="jQuery(this).parent().parent().remove()" class="btn btn-danger ml-2"><i class="la la-trash"></i></a>
                </div>
            </div>
            `);
        jQuery('select').chosen();

        });

        jQuery("#enableFacebookLogin1").click(function(){
            jQuery("#facebook-configurations").slideDown(500);
        });

        jQuery("#enableFacebookLogin0").click(function(){
            jQuery("#facebook-configurations").slideUp(500);
        });
	});
</script>

<?php JBusinessUtil::loadUploadScript(); ?>
