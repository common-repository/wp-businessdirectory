<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

function wpbd_load_async( $tag, $handle, $src ) {
    if ( $handle !== 'jbdwp-bing-map-deffer' ) {
        return $tag;
    }
    
    return "<script src='$src' async></script>";
}