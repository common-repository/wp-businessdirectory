<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', realpath(dirname(__FILE__) . DS .'..'. DS .'..'. DS .'..'. DS));

require_once JPATH_BASE . DS .'includes' . DS .'framework.php';

require_once 'utils.php';
require_once 'defines.php';
require_once 'class.resizeImage.php';

$appSettings = JBusinessUtil::getApplicationSettings();
$_target		= '';
$is_error		= false;
$triggerWarning = false;

if (!extension_loaded('gd') && !extension_loaded('gd2')) {
	$p=$n='';
	$i='GD is not loaded !';
	$e=3;
	$is_error		= true;
}

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
				
				/*if (is_dir($dir))
				{
					if ($dh = opendir($dir)) {
						while (($file = readdir($dh)) !== false) {
							echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
						}
						closedir($dh);
					}
				}
				*/
			} else {
				//dbg('Am '.$dir);
			}
			
			$_target_tmp.=$e.DIRECTORY_SEPARATOR;
		}
		
		if ($is_error == false) {
			$identifier = 'file';
			if (isset($_FILES['uploadfile'])) {
				$identifier = 'uploadfile';
			}
			if (isset($_FILES['markerfile'])) {
				$identifier = 'markerfile';
			}
			if (isset($_FILES['uploadLogo'])) {
				$identifier = 'uploadLogo';
			}
			if (isset($_FILES['iconfile'])) {
				$identifier = 'iconfile';
			}

			if (isset($_FILES['croppedimage'])) {
				$identifier = 'croppedimage';
				$type = $_FILES['croppedimage']['type'];
				$_FILES['croppedimage']['name'] = 'cropped.'.substr($type, strpos($type, '/')+1, strlen($type)-strpos($type, '/'));
			}
			
			//echo($identifier);
			//echo "\n";
			$imageName = substr($_FILES[$identifier]['name'], 0, strrpos($_FILES[$identifier]['name'], '.'));
			$imageName = preg_replace("/[^a-zA-Z0-9.]/", "", $imageName);
			//echo ($imageName);
			//echo "\n";
			$imageExt =substr($_FILES[$identifier]['name'], strrpos($_FILES[$identifier]['name'], '.'));
			;
			$resultFileName = $imageName."-".time().$imageExt;

			$fileExt = strtolower($imageExt);
			$fileExt = str_replace(".", "", $fileExt);
			if (strpos(ALLOWED_FILE_EXTENSIONS, $fileExt)===false) {
				$p=$n='';
				$i='File extension not allowed!';
				$e=5;
				$is_error = true;
			}

			if (!strpos($_root_app, 'media') || (!strpos($_root_app, 'pictures') && !strpos($_root_app, 'attachments'))) {
				$p=$n='';
				$i='File path not valid!';
				$e=6;
				$is_error = true;
			}

			if ($identifier == 'file') {
				$resultFileName = $imageName.$imageExt;
			}
			
			$_target = $_root_app.$_target . basename($resultFileName);
				
			
			$file_tmp = JBusinessUtil::makePathFile($_target);
			
			/* if( is_file($file_tmp) )
			{
				$p	=	'';
				$n	= 	basename( $file_tmp);
				$i	=	'This file exist !';
				$e	=	1;
			} */
			if (!$is_error) {
				$pictureType = 	$_GET['picture_type'];
//				if(empty($pictureType)){
//					$pictureType = PICTURE_TYPE_COMPANY;
//				}
				//echo "\n";
				//echo($pictureType);
				//echo "\n";
				if (strcmp($pictureType, PICTURE_TYPE_COMPANY)==0) {
					$maxPictureWidth =  !empty($appSettings->cover_width)?$appSettings->cover_width:MAX_COMPANY_PICTURE_WIDTH;
					$maxPictureHeight =  !empty($appSettings->cover_height)?$appSettings->cover_height:MAX_COMPANY_PICTURE_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_OFFER)==0) {
					$maxPictureWidth =  !empty($appSettings->gallery_width)?$appSettings->gallery_width:MAX_OFFER_PICTURE_WIDTH;
					$maxPictureHeight = !empty($appSettings->gallery_height)?$appSettings->gallery_height:MAX_OFFER_PICTURE_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_LOGO)==0) {
					$maxPictureWidth =  !empty($appSettings->logo_width)?$appSettings->logo_width:MAX_LOGO_WIDTH;
					$maxPictureHeight = !empty($appSettings->logo_height)?$appSettings->logo_height:MAX_LOGO_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_EVENT)==0) {
					$maxPictureWidth =  !empty($appSettings->gallery_width)?$appSettings->gallery_width:MAX_OFFER_PICTURE_WIDTH;
					$maxPictureHeight = !empty($appSettings->gallery_height)?$appSettings->gallery_height:MAX_OFFER_PICTURE_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_GALLERY)==0) {
					$maxPictureWidth =  !empty($appSettings->gallery_width)?$appSettings->gallery_width:MAX_GALLERY_WIDTH;
					$maxPictureHeight = !empty($appSettings->gallery_height)?$appSettings->gallery_height:MAX_GALLERY_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_SPEAKER) == 0) {
					$maxPictureWidth  = !empty($appSettings->speaker_img_width) ? $appSettings->speaker_img_width:MAX_SPEAKER_IMAGE_WIDTH;
					$maxPictureHeight = !empty($appSettings->speaker_img_height) ? $appSettings->speaker_img_height:MAX_SPEAKER_IMAGE_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_THUMBNAIL) == 0) {
					$maxPictureWidth  = !empty($appSettings->gallery_width) ? $appSettings->gallery_width : MAX_THUMBNAIL_WIDTH;
					$maxPictureHeight = !empty($appSettings->gallery_height) ? $appSettings->gallery_height : MAX_THUMBNAIL_HEIGHT;
				}  elseif (strcmp($pictureType, PICTURE_TYPE_BANNER) == 0) {
					$maxPictureWidth  = !empty($appSettings->gallery_width) ? $appSettings->gallery_width : MAX_BANNER_WIDTH;
					$maxPictureHeight = !empty($appSettings->gallery_height) ? $appSettings->gallery_height : MAX_BANNER_HEIGHT;
				} elseif (strcmp($pictureType, PICTURE_TYPE_CATEGORY_ICON) == 0) {
					$this->maxPictureWidth  = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_CATEGORY_ICON_IMAGE_WIDTH;
					$this->maxPictureHeight = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_CATEGORY_ICON_IMAGE_HEIGHT;
				}

				//echo($file_tmp);
				//echo "\n";
				//echo($_FILES[$identifier]['tmp_name']);
				//echo "\n";
				if (move_uploaded_file($_FILES[$identifier]['tmp_name'], $file_tmp)) {
					$image = new Resize_Image;
					$image->ratio = true;
					
					$ratio = $maxPictureWidth/$maxPictureHeight;
					$size = getimagesize($file_tmp);
					$imageRatio = $size[0]/$size[1];

					if ($size[0] < $maxPictureWidth || $size[1] < $maxPictureHeight) {
						$triggerWarning = true;
					}
					
					if (!isset($maxPictureWidth)) {
						$maxPictureWidth = $size[0];
						$maxPictureHeight = $size[1];
					}
					$resizeImage = false;

					// if crop is enabled, resize the image only if it is being sent by cropper functions
					$checkSize = true;
					if ($appSettings->enable_crop) {
						if (isset($_GET['croppable']) && !isset($_GET['crop'])) {
							$checkSize = false;
						}
					}

					//set new height or new width depending on image ratio
					if (($size[1] > $maxPictureHeight || $size[0] > $maxPictureWidth) && $checkSize) {
						if ($ratio<$imageRatio) {
							$image->new_width 	= $maxPictureWidth;
						} else {
							$image->new_height 	= $maxPictureHeight;
						}
						$resizeImage = true;
					}

						
					$image->image_to_resize = $file_tmp; 	// Full Path to the file
					
					$image->new_image_name 	= basename($file_tmp);
					$image->save_folder 	= dirname($file_tmp).DIRECTORY_SEPARATOR;
					
					if ($resizeImage) {
						//dump("resize");
						$process 			= $image->resize();
						if ($process['result'] && $image->save_folder) {
							$p	=	basename($file_tmp);
							$n	= 	basename($file_tmp);
							$i	=	$file_tmp;
							$e	=	0;
						} else {
							unlink($file_tmp);
							$p=$n='';
							$i='Error resize uploaded file';
							$e=4;
						}
					} else {
						$p	=	basename($file_tmp);
						$n	= 	basename($file_tmp);
						$i	=	$file_tmp;
						$e	=	0;
					}
				} else {
					$p=$n='';
					$i='Error move uploaded file';
					$e=2;
				}
			}
		}
	}
}

echo '<?xml version="1.0" encoding="utf-8" ?>';
echo '<uploads>';
if ($triggerWarning) {
	echo '<warning value="1" width="'.$maxPictureWidth.'" height="'.$maxPictureHeight.'"></warning>';
}
echo '<picture path="'.$p.'" info="'.$i.'" name="'.$n.'" error="'.$e.'" />';
echo '</uploads>';
echo '</xml>';
