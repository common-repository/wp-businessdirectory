<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

// DENY DIRECT ACCESS TO THE FILE
if (!defined('ABSPATH'))
	die ('Restricted access');

add_action("admin_init", "wpbd_businessdirectory_admin_init");

/**
 *
 * Initialized on admin load **
 */
function wpbd_businessdirectory_admin_init()
{
    wpbd_add_capabilities();
    wpbd_do_output_buffer();
    //add_filter('run_wptexturize', false);
    remove_filter('the_content', 'wptexturize');

    if (isset($_REQUEST['option']) && $_REQUEST['option'] == "com_businessdirectory") {
		require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'jbusinessdirectory.php');
	}

	$task = isset($_GET['task'])?sanitize_text_field($_GET['task']):"";
	
	if (isset($task) && (strpos($task,"Ajax"))!== false){
        ob_clean();
        $input = new Input();
        $controller	= JControllerLegacy::getInstance('JBusinessDirectory');
        $controller->execute($input->get("task"));
        $controller->redirect();
    }
    

    $task = isset($_POST['task'])?sanitize_text_field($_POST['task']):"";
    if (isset($task)
        &&
        (
            strpos($task,"CreationListingsNotification") ||
            strpos($task,"StatisticsNotification") ||
            strpos($task,"UpgradeNotification") ||
            strpos($task,"getLatestServerNewsAjax") ||
            strpos($task,"newOffers") ||
            strpos($task,"newEvents") ||
            strpos($task,"income") ||
            strpos($task,"newCompanies") ||
            strpos($task,"billingdetails.save") ||
            strpos($task,"export")
        )
        !== false) {

        $input = new Input();
        $controller	= JControllerLegacy::getInstance('JBusinessDirectory');
        $controller->execute($input->get("task"));
        $controller->redirect();

    }
}

//allow ajax calls as well as redirects
function wpbd_do_output_buffer(){
	ob_start();
}

add_action('wp_loaded', function ($query) {
    $task = isset($_REQUEST['task'])?sanitize_text_field($_REQUEST['task']):"";
	
    if (isset($task) && ($task=="offer.generateCoupon" ||  (strpos($task,"Ajax"))!== false)) {
		JBusinessUtil::includeCSSLibraries();
		JBusinessUtil::loadBaseScripts();
		
		remove_filter('the_content', 'wpautop');
        remove_filter('the_content', 'wptexturize');
        require_once(JPATH_COMPONENT . DS . 'jbusinessdirectory.php');
    }
    
});

add_action( 'parse_query', function( $query_vars ){
	//$session = JFactory::getSession();
	// dump("query_vars");

        
	// 	dump("main query");

    //     //$log = \Logger::getInstance();
    //     //$log->LogDebug("execute call ".$_SERVER['REQUEST_URI']);
        
    // 	$alias = wpbd_parseUrl();
    
    // 	$current_url = $_SERVER['REQUEST_URI'];
    	
    // 	$directory = isset($_REQUEST['directory'])?sanitize_text_field($_REQUEST['directory']):"";
    // 	if (!empty($directory) || $alias=="businessdirectory") {
	// 		$session = JFactory::getSession();
    // 	    remove_filter('the_content', 'wpautop');
    // 	    remove_filter('the_content', 'wptexturize');
    // 		require_once(JPATH_COMPONENT . DS . 'jbusinessdirectory.php');
    // 	}
    
});

// Add the new capability to all roles having a certain built-in capability
function wpbd_add_capabilities()
{
	$roles = get_editable_roles();
	require_once BD_HELPERS_PATH.'/helper.php';
	$capatabilities = JBusinessDirectoryHelper::getCapabilities();

	foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
		if (isset($roles[$key]) && $role->has_cap('create_users')) {
		    foreach($capatabilities as $capability){
		        $capability = str_replace(".", "_", $capability);
		        $role->add_cap($capability);
		    }
		}
	}	
}

/**
 * Parse the current url and detect if an event alias is present
 *
 */
function wpbd_parseUrl(){
	global $wp;
	$current_url = $_SERVER['REQUEST_URI'];
	$current_url = trim($current_url, "/");
	
	if(strpos($current_url,"?")!==false){
	   $current_url = substr($current_url, 0, strpos($current_url,"?"));
	}
	
	$urlParams   = explode("/", $current_url);
	$urlParams = array_filter($urlParams);
	$alias       = end($urlParams);
	
	
	return $alias;
}

function wpbd_get_current_url() {
    // Get current URL path, stripping out slashes on boundaries
    $current_url = trim(esc_url_raw(add_query_arg([])), '/');
    // Get the path of the home URL, stripping out slashes on boundaries
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    // If a URL part exists, and the current URL part starts with it...
    if ($home_path && strpos($current_url, $home_path) === 0) {
        // ... just remove the home URL path form the current URL path
        $current_url = trim(substr($current_url, strlen($home_path)), '/');
    }

    return $current_url;
}

function wpbd_add_frontend_route( $pattern, callable $callback ) {
	add_filter( 'routing_add_routes', function( $routes ) use( $pattern, $callback ) {
		$routes[ $pattern ] = $callback;
		return $routes;
	} );
}

$allowed = ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );

//avoid WP to parse url if route found 
$allowed and add_action( 'do_parse_request', function( $do_parse, $wp ) {
	$routes = [];// Let's initialize an empty array of routes
	
	$current_url = wpbd_get_current_url();
	
	// Users can add routes using the 'routing_add_routes' hook
	$routes = apply_filters( 'routing_add_routes', $routes, $current_url );
	// If there are no routes, just let WordPress do its work...
	if ( empty( $routes ) || ! is_array( $routes )  ) {
		return $do_parse;
	}
	$urlParts = explode( '?', $current_url, 2 );
	$urlPath = trim( $urlParts[0], '/' );
	$urlVars = [];
	if ( isset( $urlParts[1] ) ) {
		parse_str( $urlParts[1], $urlVars );
	}
	
	$query_vars = null;
	foreach( $routes as $pattern => $callback ) {
		if ( preg_match( '~' . trim( $pattern, '/' ) . '~', $urlPath, $matches ) ) {            
			//call callback stored in the route to obtain query vars
			$routeVars = $callback( $matches );
			if ( is_array( $routeVars ) ) {
				$query_vars = array_merge( $routeVars, $urlVars );
				break;
			}
		}
	}
	// If parse_routes() returns an array of query arguments as we expect...
	if ( is_array( $query_vars ) ) {
		// ...we set query vars in WP object
		$wp->query_vars = $query_vars;
		
		// Fire an action, this may be useful later to know when a route matched
		do_action( 'routing_matched_vars', $query_vars );
		
		// Finally return false to stop WordPress from parsing the request
		return false;
	}
	
	// In other cases, we just let WordPress do its work...
	return $do_parse;
	
}, 30, 2 );

//cut off any template redirects when routes found
$allowed and add_action( 'routing_matched_vars', function() {remove_action( 'template_redirect', 'redirect_canonical' );}, 30 );

unset( $allowed );


//add routes to be parsed
wpbd_add_frontend_route('/([^/]*)/([^/]*)/?', function($matches) {
	$wbdUrlTranslator = new WBDUrlTranslator();
	$alias = wpbd_parseUrl();
	$params = $wbdUrlTranslator->translateRoute($matches[1],$matches[2]);
	if(!empty($params)){
		$jinput = JFactory::getApplication()->input;
		foreach($params as $key=>$param){
			$jinput->set($key, $param);
		}
	}

	$directory = isset($_REQUEST['directory'])?sanitize_text_field($_REQUEST['directory']):"";
    	if (!empty($directory) || $alias=="businessdirectory") {
			$session = JFactory::getSession();
    	    remove_filter('the_content', 'wpautop');
    	    remove_filter('the_content', 'wptexturize');
    		require_once(JPATH_COMPONENT . DS . 'jbusinessdirectory.php');
    	}

	return $params;
});

wpbd_add_frontend_route('/([^/]*)/?', function($matches) {
	$wbdUrlTranslator = new WBDUrlTranslator();
	$alias = wpbd_parseUrl();
	$params = $wbdUrlTranslator->translateRoute($matches[1],$matches[1]);
	if(!empty($params)){
		$jinput = JFactory::getApplication()->input;
		foreach($params as $key=>$param){
			$jinput->set($key, $param);
		}
	}

	$directory = isset($_REQUEST['directory'])?sanitize_text_field($_REQUEST['directory']):"";
    	if (!empty($directory) || $alias=="businessdirectory") {
			$session = JFactory::getSession();
    	    remove_filter('the_content', 'wpautop');
    	    remove_filter('the_content', 'wptexturize');
    		require_once(JPATH_COMPONENT . DS . 'jbusinessdirectory.php');
    	}

	return $params;
});

