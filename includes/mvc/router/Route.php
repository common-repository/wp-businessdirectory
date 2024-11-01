<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MVC\Router;

defined('JPATH_PLATFORM') or die;

use JBusinessUtil;
use MVC\Factory;
use MVC\Uri\Uri;

/**
 * Route handling class
 *
 * @since  11.1
 */
class Route
{
	/**
	 * The route object so we don't have to keep fetching it.
	 *
	 * @var    Router
	 * @since  12.2
	 */
	private static $_router = null;

	/**
	 * Translates an internal Joomla URL to a humanly readable URL.
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compliance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             0: (default) No change, use the protocol currently used in the request
	 *                             1: Make URI secure using global secure site URI.
	 *                             2: Make URI unsecure using the global unsecure site URI.
	 *
	 * @return string The translated humanly readable URL.
	 *
	 * @since   11.1
	 */
	public static function _($url, $xhtml = true, $ssl = null){
	    
	    if(!empty($url) &&  strpos($url,"http") === 0){
	        return $url;
	    }
	    
	    if(strpos($url, 'businessdirectory') === 0){
	        $siteAccessPoint = site_url();
	        $url= $siteAccessPoint."/".$url;
	        if(strpos($url,"?")===false){
	            $url.= "?businessdirectory=1";
	        }else{
	            $url.= "&businessdirectory=1";
	        }
	    }else{
	        $page="";
	        if(isset($_REQUEST["page"])){
    	        $page = $_REQUEST["page"];
	        }
	        
	        if(empty($page)){
	            $parts = explode("&",$url);
	            if(in_array("view", $parts)){
    	            foreach($parts as $part){
    	                $part = explode("=",$part);
    	                if(is_array($part) && $part[0]="view"){
    	                    $page = "jbd_".$part[1];
    	                }
    	            }
	            }
	        }

			$lang = get_locale();
			$lang = explode("_", $lang);
			$lang = $lang[0];

	        $baseUrl = !empty($page)?admin_url('admin.php?page='.$page):home_url()."/$lang/businessdirectory/?directory=1";
	        
    	    if( strpos($url,"index.php?option=com_jbusinessdirectory") !== false){
    	        $url= str_replace("index.php?option=com_jbusinessdirectory", $baseUrl, $url);
    	    }else{
    	        if(strpos($url,"&")===0){
    	            $url= $baseUrl.$url;
    	        }else{
    	            $url= $baseUrl."&".$url;
    	        }
    	    }
    	    
	    }
	    
	    return $url;
	}
}
