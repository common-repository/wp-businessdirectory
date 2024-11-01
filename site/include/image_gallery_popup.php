<?php

/**
 * @package    J-BusinessDirectory
 *
 * Image slider with gallery component
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::enqueueStyle('libraries/unitegallery/css/unite-gallery.css');
JBusinessUtil::enqueueStyle('libraries/unitegallery/themes/default/ug-theme-default.css');
JBusinessUtil::enqueueScript('libraries/unitegallery/js/unitegallery.js');
JBusinessUtil::enqueueScript('libraries/unitegallery/themes/default/ug-theme-default.js');

$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');
$sliderId = rand(1000, 10000);
?>

<div class="popup-gallery" id="slider-<?php echo $sliderId ?>">
    <?php if (!empty($this->pictures)) { ?>
        <div class="row">
            <?php if (count($this->pictures) > 1) { ?>
                <div class="col-3 gallery-thumbnails d-none d-sm-block">
                    <div class="row h-100">
                        <?php for ($i = 0; $i < 4; $i++) { ?>
                            <?php if (isset($this->pictures[$i])) { ?>
                                <?php $picture = $this->pictures[$i]; ?>
                                <div class="col-12 thumb-wrapper-small" data-toggle="gallery" data-webm-clickvalue="view-gallery" data-webm-image-type="image" data-gallery-index="<?php echo $i ?>" data-gallery-category="photos-and-video">
                                    <div class="thumb-small"> 
                                        <img src="<?php echo BD_PICTURES_PATH . $picture->picture_path ?>" />

                                        <?php if($i == 3 && count($this->pictures)>4){?>
                                            <?php if (!empty($this->videos)) { ?>
                                                <div class="thumb-overlay"><div class="thumb-content"> <i class="la la-play rounded-circle p-2 bg-dark"></i> </div></div>
                                            <?php }else{ ?>
                                                <div class="thumb-overlay"><div class="thumb-content">+ <?php echo count($this->pictures) -4 ?> </div></div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="col thumb-wrapper-hero gallery-main" data-toggle="gallery" data-webm-clickvalue="view-gallery" data-gallery-index="0" data-gallery-category="photos-and-video">
                <img class="img-fluid thumb-big" src="<?php echo BD_PICTURES_PATH . $this->pictures[0]->picture_path ?>">
            </div>
        </div>
    <?php } ?>
</div>

<div id="popup-gallery-full" class="popup-gallery-full" style="display:none">
    <div class="close"><i class="la la-close"></i></div>
    <div class="gallery-wrapper">
        <div class="row h-100">
            <div class="col-12 col-lg-12">
                <div class="title pb-2"><?php echo $this->popupTitle ?></div>
                <div id="unite-gallery" class="">
                    <?php //dump($this->videos) ?>
                    <?php if (!empty($this->videos)) { ?>
                        <?php foreach ($this->videos as $video) { ?>
                            <img alt=""
                                data-type="<?php echo $video->videoType?>"
                                data-videoid="<?php echo $video->videoId?>"
                                data-description="">
                        <?php } ?>
                    <?php } ?>

                    <?php if (!empty($this->pictures)) {
                        $hasDescription = false; ?>
                        <?php foreach ($this->pictures as $picture) {
                        if (!empty($picture->picture_info)) {
                            $hasDescription = true;
                        } ?>
                            <img title="<?php echo $picture->picture_title ?>" alt="<?php echo $picture->picture_info ?>" src="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>"
                                data-image="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>"
                                data-title="<?php echo $picture->picture_title ?>"
                                data-description="<?php echo $picture->picture_info ?>">
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-5 col-lg-3" style="display:none">
                <div class="sidebar">
                    <div class="gallery-side-bar hidden" style="display: block;">
                        <div class="gallery-side-bar-wrapper">
                            <form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <p class="header-title"><?php echo JText::_('LNG_CONTACT') ?></p>

                                <div class="form-group">
                                    <label for="description"><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
                                    <div class="outer_input">
                                        <textarea rows="7" name="description" id="description" cols="50" class="form-control validate[required]" required=""></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="firstName"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                                            <div class="outer_input">
                                                <input type="text" name="firstName" id="firstName" class="validate[required]" required="">
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label>
                                            <div class="outer_input">
                                                <input type="text" name="lastName" id="lastName" class="validate[required]" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="jinput-email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                                    <div class="outer_input">
                                        <input type="text" name="email" id="jinput-email" class="validate[required,custom[email]]" required="">
                                    </div>
                                </div>                   

                                <div class="row">
                                    <div class="col-12">
                                        <div class="jbd-checkbox justify-content-end">
                                            <input type="checkbox"  name="copy-me" value="1">
                                            <label for="copy-me"><?php echo JText::_('LNG_COPY_ME')?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>
                                    </div>

                                    <?php if ($this->appSettings->captcha) { ?>
                                        <div class="form-item">
                                            <?php
                                            $namespace = "jbusinessdirectory.contact";
                                            $class = " required";

                                            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                                            if (!empty($captcha)) {
                                                echo $captcha->display("captcha", "captcha-div-contact", $class);
                                            }
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="btn-group" role="group" aria-label="">
                                    <button type="button" class="btn btn-success" onclick="jbdUtils.saveForm('contactCompanyFrm')"><?php echo JText::_("LNG_SEND")?></button>
                                </div>
              
                                <?php echo JHTML::_('form.token'); ?>
                                <input type='hidden' name="option" value="com_jbusinessdirectory"/>
                                <input type='hidden' name='task' id="task" value='offer.contactCompany'/>
                                <input type="hidden" name="contact_id_offer" value="<?php echo $this->offer->company->email ?>"/>
                                <input type='hidden' name='userId' value='<?php echo $user->ID ?>'/>
                                <input type="hidden" name="companyId" value="<?php echo $this->offer->company->id ?>"/>
                                <input type="hidden" name="offer_Id" value="<?php echo $this->offer->id ?>"/>
                                <input type="hidden" name="item_type" value="<?php echo $this->offer->item_type ?>"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       
    <div class="overlay"></div>
</div>

<script type="text/javascript">
    var unitegallery = null;

    window.addEventListener('load', function() {
        jQuery(".popup-gallery img").click(function(){
            jQuery('#popup-gallery-full').show();
            jQuery("body").addClass("fixed");
        });

        jQuery(".thumb-overlay").click(function(){
            jQuery('#popup-gallery-full').show();
            jQuery("body").addClass("fixed");
        });
       
        jQuery(".overlay").click(function(){
            jQuery('#popup-gallery-full').hide();
            jQuery("body").removeClass("fixed");
        });

        jQuery(document).keyup(function(e) {
            if (e.key === "Escape") { // escape key maps to keycode `27`
                jQuery('#popup-gallery-full').hide();
                jQuery("body").removeClass("fixed");
            }
        });

        jQuery(".close").click(function(){
            jQuery('#popup-gallery-full').hide();
            jQuery("body").removeClass("fixed");
        });

        jQuery('#popup-gallery-full').appendTo(document.body);

        jQuery(".thumb-small img").mouseover(function() {
            jQuery( ".thumb-big" ).attr("src",jQuery(this).attr("src"));
        });

        unitegallery = jQuery("#unite-gallery").unitegallery({
                gallery_theme: "default",
                gallery_height: "calc(100% - 18px)",
                gallery_width: "100%",		
				theme_enable_text_panel: false,
                slider_control_zoom: false,
                slider_enable_zoom_panel: false,
                slider_scale_mode:"fit",
              	theme_hide_panel_under_width: 480,		
                thumb_fixed_size: false,
                gallery_autoplay: false,
                theme_enable_fullscreen_button: false,	//show, hide the theme fullscreen button. The position in the theme is constant
				theme_enable_play_button: false,			//show, hide the theme play button. The position in the theme is constant
				theme_enable_hidepanel_button: false,	//show, hide the hidepanel button
				thumb_height:100,								//thumb width
				thumb_fixed_size:false,						//true,false - fixed/dynamic thumbnail width	
                thumb_border_width: 0,	
                strippanel_enable_buttons: false,
                strippanel_enable_handle: false,
                thumb_selected_border_width: 0,	
                slider_enable_text_panel: false,
                slider_enable_zoom_panel: false,
                slider_controls_always_on: true,
                slider_enable_zoom_panel: false,
                slider_enable_fullscreen_button: false,
                gallery_background_color: "transparent",	
                strippanel_background_color:"transparent"
            });

    });


</script>