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

use BusinessDirectory\Settings as Setting;

/**
 * * Register Autoloader **
 */
spl_autoload_register('wpbusinessdirectory_autoloader');

/**
 * WPB Autoloader Functions
 *
 * @param
 *            $class_name
 */
function wpbusinessdirectory_autoloader($class_name)
{
    
   
}

/**
 * Simple Autoloader
 *
 * @param
 *            $class_name
 * @param
 *            $d
 * @param
 *            $f
 * @param
 *            $path_prefix
 * @param
 *            $path_suffix
 * @param
 *            $file_suffix
 * @return string
 */
function autoload_($class_name, $d, $f, $path_prefix, $path_suffix, $file_suffix)
{
    $cl_file = explode($d, $class_name);
    $class_file = $cl_file[1] . $file_suffix;
    $classes_dir = WP_BUSINESSDIRECTORY_PATH . "" . $f . "/" . $path_prefix . $cl_file[1] . "/" . $path_suffix;
    return $classes_dir . $class_file;
}

/**
 * System Autoloader
 *
 * @param
 *            $class_name
 * @param
 *            $d
 * @param
 *            $f
 * @param
 *            $explode
 * @param
 *            $file_suffix
 * @return string
 */
function autoload_system($class_name, $d, $f, $explode, $file_suffix)
{
    $cl_file = explode($d, $class_name);
    $cf = explode($explode, $cl_file[1]);
    $class_file = $cf[0] . $file_suffix; // 'Controller.php';
    $classes_dir = WP_BUSINESSDIRECTORY_PATH . $f . "/";
    return $classes_dir . $class_file;
}

/**
 * Bundx Autoloader
 *
 * @param
 *            $class_name
 * @param
 *            $d
 * @param
 *            $path
 * @param
 *            $folder
 * @param
 *            $sufix
 * @return string
 */
function autoloadclass($class_name, $d, $path, $folder, $sufix)
{
    $cl_file = explode($d, $class_name);
    if (preg_match("/_/i", $cl_file[1])) :
        $x = explode("_", $cl_file[1]);
        $x[0] = $x[1] . "/";
    else :
        $x[0] = "";
        $x[1] = $cl_file[1];
    endif;
    $req = WP_BUSINESSDIRECTORY_PATH . $path . "/" . $folder . $x[1] . $sufix;
    return $req;
}

/**
 * Bundx Autoloader
 *
 * @param
 *            $class_name
 * @param
 *            $d
 * @param
 *            $path
 * @param
 *            $folder
 * @param
 *            $sufix
 * @return string
 */
function autoload_bundx($class_name, $d, $path, $folder, $sufix)
{
    $cl_file = explode($d, $class_name);
    if (preg_match("/_/i", $cl_file[1])) :
        $x = explode("_", $cl_file[1]);
        $x[0] = $x[0] . "/";
    else :
        $x[0] = "";
        $x[1] = $cl_file[1];
    endif;
    $req = WP_BUSINESSDIRECTORY_PATH . $path . "/" . $x[0] . $folder . $x[1] . $sufix;
    return $req;
}

/**
 * Helpers Autoloader
 *
 * @param
 *            $class_name
 * @param
 *            $d
 * @param
 *            $path
 * @param
 *            $sufix
 * @return string
 */
function autoload_helpers($class_name, $d, $path, $sufix)
{
    $cl_file = explode($d, $class_name);
    $x = explode("_", $cl_file[1]);
    $req = WP_BUSINESSDIRECTORY_PATH . $path . "/" . $x[0] . "/" . $x[1] . $sufix;
    return $req;
}