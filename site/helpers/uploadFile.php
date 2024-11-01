<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

require_once('utils.php');
require_once('defines.php');
require_once('class.resizeImage.php');

$_target		= '';
$is_error		= false;
$i = '';

if ($is_error==false) {
	if (
		!isset($_GET['_target'])
		||
		$_GET['_target']==''
		||
		!isset($_GET['_root_app'])
		||
		$_GET['_root_app']==''
	) {
		$p=$n='';
		$i='Invalid params !';
		$e=2;
		$is_error		= true;
	}

	if ($is_error==false) {
		$_root_app	= $_GET['_root_app'];
		$_target	= $_GET['_target'];
		$ex			= array();
		$ex			+= explode('/', $_target);

		if ($_root_app[ strlen($_root_app)-1 ] != '/') {
			$_root_app .= '/';
		}
		$_target_tmp	= JBusinessUtil::makePathFile($_root_app);
		
		foreach ($ex as $e) {
			if ($e == '') {
				continue;
			}
			$dir = $_target_tmp.$e;
			//echo($dir);
			//echo "\n";
			if (!is_dir($dir)) {
				//echo($dir);
				//echo "\n";
				if (!@mkdir($dir)) {
					$p=$n='';
					$i='Error create directory '.$_target_tmp.DIRECTORY_SEPARATOR.$e.' !';
					$e=2;
					$is_error		= true;
					break;
				}
			} else {
				//dbg('Am '.$dir);
			}
			
			$_target_tmp.=$e.DIRECTORY_SEPARATOR;
		}

		if (!strpos($_root_app, 'media') || (!strpos($_root_app, 'pictures') && !strpos($_root_app, 'attachments'))) {
			$p=$n='';
			$i='File path not valid!';
			$e=6;
			$is_error = true;
		}

		if ($is_error == false) {
			$identifier = 'uploadAttachment';
			
			$fileName = substr($_FILES[$identifier]['name'], 0, strrpos($_FILES[$identifier]['name'], '.'));
			$fileName = preg_replace("/[^a-zA-Z0-9.]/", "", $fileName);
			$fileExt =substr($_FILES[$identifier]['name'], strrpos($_FILES[$identifier]['name'], '.'));
			;
			$fileExt = strtolower($fileExt);
			
			$fileEx = str_replace(".", "", $fileExt);
			$allowedFileSize = JBusinessUtil::convertToBytes(ALLOWED_FILE_SIZE);
			if ($_FILES[$identifier]['size'] > $allowedFileSize) {
				$p=$n='';
				$i='File size is higher than '.ALLOWED_FILE_SIZE.'!';
				$e=3;
			} elseif (strpos(ALLOWED_FILE_EXTENSIONS, $fileEx)===false) {
				$p=$n='';
				$i='File extension not allowed!';
				$e=2;
			} else {
				$resultFileName = $fileName."-".time().$fileExt;
				$_target = $_root_app.$_target . basename($resultFileName);
				$file_tmp = JBusinessUtil::makePathFile($_target);
			
				$p	=	basename($file_tmp);
				$n	= 	basename($file_tmp);
				$i	=	$file_tmp;
				$e	=	0;
				if (!move_uploaded_file($_FILES[$identifier]['tmp_name'], $file_tmp)) {
					$p=$n='';
					$i='Error move uploaded file';
					$e=2;
				}
				//}
			}
		}
	}
}

echo '<?xml version="1.0" encoding="utf-8" ?>';
echo '<uploads>';
echo '<attachment path="'.$p.'" info="'.$i.'" name="'.$n.'" error="'.$e.'" />';
echo '</uploads>';
echo '</xml>';
