<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

// DENY DIRECT ACCESS TO THE FILE
if (! defined('ABSPATH'))
    die('Restricted access');

/**
 * * Add action to start the above javascripts script **
 */
add_action('admin_enqueue_scripts', 'wpbd_businessdirectory_add_admin_custom_scripts');

// load admin plugin scripts
function wpbd_businessdirectory_add_admin_custom_scripts()
{
	//avoild loading libraries outside railres
    $page = isset($_GET['page'])?sanitize_text_field($_GET['page']):"";
	if(strpos($page,"jbd_")===false)
		return;
	
	//set site url, used for javascript
	wp_localize_script('businessdirectory-site-url', 'WPURLS', array( 'siteurl' => get_option('siteurl') ));

    JBusinessUtil::enqueueScript('libraries/jquery/jquery-noconflict.js');
    
    JBusinessUtil::enqueueScript('libraries/chosen/chosen.jquery.min.js');
    JBusinessUtil::enqueueStyle('libraries/chosen/chosen.css');
    JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
    JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');
    
    JBusinessUtil::enqueueStyle('css/jbd-style.css');
    JBusinessUtil::enqueueStyle('css/common.css');
    JBusinessUtil::enqueueStyle('css/line-awesome.css');
    JBusinessUtil::enqueueStyle('css/jbd-style_v3.css');

    JBusinessUtil::enqueueScript('js/core.js');
    JBusinessUtil::enqueueScript('libraries/react/production/react.production.min.js');
    JBusinessUtil::enqueueScript('libraries/react/production/react-dom.production.min.js');
    JBusinessUtil::enqueueScript('libraries/babel/babel.min.js');

    JBusinessUtil::enqueueScript('js/jbd-app.js');
    JBusinessUtil::loadMapScripts();
    
    // add validation engine
    JBusinessUtil::enqueueStyle('libraries/validation-engine/validationEngine.jquery.css');
    $tag = get_locale();
    if (! file_exists(WP_BUSINESSDIRECTORY_PATH.'assets/js/validation/jquery.validationEngine-' . $tag . '.js')){
        $tag = "en";
    }
    JBusinessUtil::enqueueScript('libraries/validation-engine/jquery.validationEngine.js');
    JBusinessUtil::enqueueScript('libraries/validation-engine/jquery.validationEngine-' . $tag . '.js');
}

// Add Front End
add_action('wp_enqueue_scripts', 'wpbd_businessdirectory_add_frontend_custom_scripts');

// load front-end plugin scripts
function wpbd_businessdirectory_add_frontend_custom_scripts()
{
	
	//set site url, used for javascript
	wp_localize_script('businessdirectory-site-url', 'WPURLS', array( 'siteurl' => get_option('siteurl') ));
	
    JBusinessUtil::enqueueStyle('css/line-awesome.css');
    JBusinessUtil::enqueueScript('js/core.js');
    JBusinessUtil::enqueueScript('libraries/slick/slick.js');
    JBusinessUtil::enqueueScript('libraries/chosen/chosen.jquery.min.js');
    JBusinessUtil::enqueueStyle('libraries/chosen/chosen.css');
    JBusinessUtil::loadMapScripts();

    JBusinessUtil::enqueueStyle('css/jbd-template.css');
    
    // add validation engine
    JBusinessUtil::enqueueStyle('libraries/validation-engine/validationEngine.jquery.css');
    $tag = get_locale();
    if (! file_exists(WP_BUSINESSDIRECTORY_PATH.'assets/js/validation/jquery.validationEngine-' . $tag . '.js')){
        $tag = "en";
    }
    JBusinessUtil::enqueueScript('libraries/validation-engine/jquery.validationEngine.js');
    JBusinessUtil::enqueueScript('libraries/validation-engine/jquery.validationEngine-' . $tag . '.js');
}

add_action('businessdirectory_daily_event', 'wpbd_businessdirectory_daily');

function wpbd_businessdirectory_daily()
{
	//AlertService::generateAlerts();
}

add_action( 'wp_print_styles', 'wpbd_my_deregister_styles');

function wpbd_my_deregister_styles() {
	
    $directoryPlugin = isset($_REQUEST['directory'])?1:0;
    if ( $directoryPlugin==null) {
		wp_deregister_style( 'businessdirectory-timeout-modal-style' );
	}
	else 
		remove_filter ('the_content', 'wpautop');
}