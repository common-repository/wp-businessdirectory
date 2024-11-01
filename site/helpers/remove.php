<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

require_once('utils.php');

$_filename		= '';
$_root_app		= '';
$_pos			= '';
$is_error		= false;

if (
	!isset($_GET['_pos'])
	||
	$_GET['_filename']	==''
	||
	!isset($_GET['_filename'])
	||
	$_GET['_root_app']	==''
	||
	!isset($_GET['_root_app'])
) {
	$f=$n='';
	$i='Invalid params !';
	$e=2;
	$is_error		= true;
}

if ($is_error==false) {
	$_root_app	= $_GET['_root_app'];
	$_filename	= $_GET['_filename'];
	$_pos 		= $_GET['_pos'];

	if ($_root_app[ strlen($_root_app)-1 ] != '/' && $_filename[ strlen($_filename) -1 ] !='/') {
		$_root_app .= '/';
	}
	$file_tmp = JBusinessUtil::makePathFile($_root_app.$_filename);
	// dbg($file_tmp);
	
	if (@unlink($file_tmp)) {
		$f	=	htmlentities($file_tmp);
		$n	= 	basename($file_tmp);
		$i	=	$file_tmp;
		$e	=	0;
	//print_r( array( htmlentities( $_uri.$_target), "") );
	} else {
		$f=$n=$file_tmp;
		//$i='Error remove uploaded file';
		//$e=2;
	}
}

echo '<?xml version="1.0" encoding="utf-8" ?>';
echo '<remove>';
echo '<picture filename="'.$f.'" info="'.$i.'" name="'.$n.'" error="'.$e.'" pos="'.$_pos.'" />';
echo '</remove>';
echo '</xml>';
