<?php
/**
 * @package    J-BusinessDirectory
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
?>

<div class="slider-loader" id="slider-loader">
	<div class="loader"></div>
</div>
<div id="gallery" style="display:none;">
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

        <?php
	} ?>
    <?php
} else { ?>
        <?php echo JText::_("LNG_NO_IMAGES"); ?>
    <?php } ?>
</div>
<div style="clear:both;"></div>

			
<?php if (!empty($this->pictures)) {?>	
	<script type="text/javascript">
		var unitegallery = null;
        window.addEventListener('load', function() {
    		var galleryHeight = "600";
    		containerHeight = jQuery(".style4 .company-info-container").height();
    		if(containerHeight){
    			galleryHeight = containerHeight + 315; 
    		}

    		if(window.innerWidth < 480){
    			galleryHeight = "350";
    		}
        		
        	unitegallery = jQuery("#gallery").unitegallery({
                gallery_theme: "default",
                gallery_height: galleryHeight,
            	<?php if (count($this->pictures)<=1) { ?>
					theme_hide_panel_under_width: 4000,
					slider_enable_arrows: false,
				<?php } ?>
                	theme_enable_text_panel: <?php if ($hasDescription) {
					echo 'true';
				} else {
					echo 'false';
				} ?>,
                slider_control_zoom: false,
                slider_enable_zoom_panel: false,
                slider_scale_mode:"fit",
              	theme_hide_panel_under_width: 480,		
                thumb_fixed_size: false,
                gallery_autoplay: <?php echo $this->appSettings->autoplay_gallery?'true':'false'; ?>
            });

			if(jQuery('#gallery:hidden').length == 0) {
				jQuery('#slider-loader').hide()
			}
        });     
	</script>
<?php } ?>
	
	
