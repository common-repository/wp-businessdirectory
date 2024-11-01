<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$user = JBusinessUtil::getUser();
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
?>
<style>
.subhead-collapse{
	margin: 0 !important;
	padding: 0 !important;	
}

</style>

<div id="jdb-wrapper" class="jbd-container jdb-wrapper-front tmpl-style-1">
	
	<div id="menu-button" class="menu-button btn-primary">
		<i class="la la-bars"></i> <?php echo JText::_("LNG_SHOW_MENU") ?>
	</div>

	<nav class="navbar-default navbar-static-side closed" role="navigation" id="dir-navigation">
		<div class="sidebar-collapse">
			<ul class="nav metismenu" id="side-menu">
				<li class="nav-header">
					<div class="navbar-minimalize minimalize-styl-2"><i class="la la-bars"></i></div>
					<div class="user-info">
                        <?php
						if ($appSettings->social_profile) {
							require JPATH_COMPONENT_SITE.'/include/profile/listing_profile.php';
						} else { ?>
                            <div class="round-badge">
                                <?php
								if (strpos($user->display_name, " ")) {
									$user->display_name = str_replace("  ", " ", $user->display_name);
									$name = explode(" ", $user->display_name);
									echo mb_substr($name[0], 0, 1).".".mb_substr($name[1], 0, 1).".";
								} else {
									echo $user->display_name[0] . ".";
								}
								?>
                            </div>
                            <div class="user-details">
                                <div class="user-name"><?php echo $user->display_name ?></div>
                            </div>
                            <?php
						} ?>
					</div>
				</li>
				<?php foreach ($template->menus as $menu) {?>
					<li class="<?php echo isset($menu["active"])?"active":""?>">
						<a href="<?php echo JRoute::_($menu["link"])?>">
							<i class="<?php echo $menu["icon"] ?>"></i>	<span class="nav-label"><?php echo $menu["title"] ?></span>
                            <?php if (isset($menu['display-unread-message'])) { ?>
                                <span class="nav-label" id="message-unreaded"> (<?php echo $menu['nrMessages'] ?>)</span>
                            <?php } ?>
                            <?php if (isset($menu['display-unread-quote']) && $menu['nrQuotes'] != 0) { ?>
                                <span class="nav-label"> (<?php echo $menu['nrQuotes'] ?>) </span>
                            <?php } ?>
                            <?php if (isset($menu["new"])) {?>
								<span class="label label-info pull-right"><?php echo JText::_("LNG_NEW")?></span>
								<?php } ?>
								
							 <?php if (isset($menu["submenu"])) {?> 
								 <span class="la la-menu-arrow"></span>
							 <?php } ?>
						</a>
						 <?php if (isset($menu["submenu"])) {?> 
							<ul class="nav nav-second-level">
								<?php foreach ($menu["submenu"] as $submenu) {?>
									<li class="<?php echo isset($submenu["active"])?"active":""?>">
										<a href="<?php echo JRoute::_($submenu["link"])?>">
											<?php echo $submenu["title"] ?>
											<?php if (isset($submenu["new"])) {?>
												<span class="label label-info pull-right"><?php echo JText::_("LNG_NEW")?></span>
											<?php } ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		</div>
	</nav>
	<div id="page-wrapper">
		<div class="normalheader transition animated fadeIn">
		    <div class="hpanel">
		        <div class="panel-body">
		        	<div class="row">
		        		<div class="col-md">
		        		    <h2 class="font-light">
        		                <?php echo $this->section_name?>
        		            </h2>
		            		<small><?php echo $this->section_description ?></small>
		        		</div>
		        		<div class="col-md-5">
		                    <div id="hbreadcrumb">
        		                <ol class="hbreadcrumb">
        		                    <li><a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=useroptions")?>"><?php echo JText::_("LNG_DASHBOARD")?></a></li>
        		                    <li class="active">
        		                        <span><?php echo $this->section_name?></span>
        		                    </li>
        		                </ol>
        		            </div>
		        		</div>
		        	</div>
		        
		        </div>
		    </div>
		</div>
		<div id="content-wrapper">
			<?php if(!empty($this->message)){ ?>
				<div id="user-messages">
					<div class="jitem-card card-plain card-round horizontal">
						<div class="jitem-wrapper">
							<div class="jitem-section">
								<div class="jitem-title">
									<?php echo $this->message->title ?>
								</div>
								<div class="jitem-subtitle">
								<?php echo $this->message->text ?>
								</div>
							</div>
							<div class="jitem-section">
								<a class="btn btn-primary" href="<?php echo $this->message->button_link ?>"><?php echo $this->message->button_text ?></a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			
			<?php echo $template->content?>
			<div class="clear"></div>
		</div>
	</div>
</div>

<script>

window.addEventListener("load", function () {
	// Minimalize menu
	jQuery('.navbar-minimalize').click(function () {
	    jQuery("#jdb-wrapper").toggleClass("mini-navbar");
	    SmoothlyMenu();
	
	});

	setupNav();
	jQuery(window).bind("resize", function () {
		setupNav();
	});

	 // MetisMenu
    jQuery("#side-menu").metisMenu();

	// Collapse ibox function
    jQuery('.collapse-link').click(function () {
        var ibox = jQuery(this).closest('div.ibox');
        var button = jQuery(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('la la-chevron-up').toggleClass('la la-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    jQuery('.close-link').click(function () {
        var content = jQuery(this).closest('div.ibox');
        content.remove();
    });

    if(jQuery("#page-wrapper").height() < jQuery("#dir-navigation").height())
   		jQuery("#page-wrapper").css("min-height", jQuery("#dir-navigation").height()+'px');

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

    jQuery("#menu-button").click(function(){
     	jQuery("#dir-navigation").slideToggle(500);   
     	if (jQuery('#dir-navigation').height()<100){
     		jQuery(this).html("<?php echo "<i class='la la-bars'></i> ".JText::_("LNG_HIDE_MENU")?>");
     	}else{
     		jQuery(this).html("<?php echo "<i class='la la-bars'></i> ".JText::_("LNG_SHOW_MENU")?>");
     	}
    });
});

function setupNav(){
	  if (jQuery(window).width() < 992 && jQuery(window).width() > 768  ) {
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