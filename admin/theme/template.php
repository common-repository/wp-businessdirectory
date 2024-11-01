<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
 
defined('_JEXEC') or die('Restricted access');
?>

<style>

.container-fluid.container-main, .subhead-collapse{
	margin: 0 !important;
	padding: 0 !important;	
}

header.header{
	display: none;
}
</style>

<div id="jdb-wrapper" class="jbd-container jdb-wrapper-admin tmpl-style-1 <?php echo $page ?>">
	<div id="page-wrapper">
		<?php if(!empty($template->menus)){ ?>
    		<div class="jbd-toolbar">
    			<?php foreach($template->menus as $menu){?>    				
    					<a class="btn <?php echo isset($menu["active"])?"active":""?>" href="<?php echo JRoute::_($menu["link"])?>">
    						<?php if(isset($menu["icon"])){?>
    							<i class="<?php echo $menu["icon"] ?>"></i>	
    						<?php } ?>
    						<span class=""><?php echo $menu["title"] ?></span>
    					</a>    				
    			<?php } ?>
    		</div>
		<?php } ?>
		
		<?php if(!$hidemainmenu){?>
    		<div class="normalheader transition animated fadeIn">
    		    <div class="hpanel">
    		        <div class="panel-body">
    		         	<h2 class="font-light m-b-xs">
    		                <?php echo $this->section_name?>
    		            </h2>
    		            <?php if(!$hidemainmenu){?>
    		           	 	<small><?php echo $this->section_description ?></small>
    		           	<?php } ?>
    		        </div>
    		    </div>
    		</div>
    	<?php } ?>

		<div class="toolbar">
			<?php echo JToolbar::getInstance('toolbar')->render('toolbar'); ?>
		</div>
		<div id="content-wrapper">
			<?php echo $template->content?>
			<div class="clear"></div>
		</div>
		<div class="toolbar">
			<?php echo JToolbar::getInstance('toolbar')->render('toolbar'); ?>
		</div>
	</div>
</div>

<script>

jQuery(document).ready(function () {
    // Close ibox function
    jQuery('.close-link').click(function () {
        var content = jQuery(this).closest('div.ibox');
        content.remove();
    });

    if(jQuery("#page-wrapper").height() < jQuery("#dir-navigation").height())
   		jQuery("#page-wrapper").css("height", jQuery("#dir-navigation").height()+'px');

    // Fullscreen ibox function
    jQuery('.fullscreen-link').click(function() {
        var ibox = jQuery(this).closest('div.ibox');
        var button = jQuery(this).find('i');
        jQuery('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('la-expand').toggleClass('la-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function() {
            jQuery(window).trigger('resize');
        }, 100);
    });
});

function setupNav(){
	 if (jQuery(this).width() < 769 && jQuery(this).width() > 480) {
    	jQuery('#jdb-wrapper').addClass('mini-navbar')
    } else {
    	jQuery('#jdb-wrapper').removeClass('mini-navbar')
    }
}

function SmoothlyMenu() {
    if (!jQuery('#side-menu').hasClass('mini-navbar') || jQuery('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        jQuery('#side-menu').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                jQuery('#side-menu').fadeIn(500);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        jQuery('#side-menu').removeAttr('style');
    }
}
</script>