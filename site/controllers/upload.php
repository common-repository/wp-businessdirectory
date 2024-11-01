<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

define('TYPE_UPLOAD', 1);
define('TYPE_REMOVE', 2);
define('TYPE_UPLOAD_FILE', 3);

require_once BD_HELPERS_PATH.'/class.resizeImage.php';

/**
 * Class JBusinessDirectoryControllerUpload
 */
class JBusinessDirectoryControllerUpload extends JControllerLegacy {
	private $appSettings;

	private $_target = '';
	private $upload_no_image_image = '';
	private $_root_app = '';
	private $is_error = false;
	private $triggerWarning = false;

	private $maxPictureWidth;
	private $maxPictureHeight;

	private $_filename;
	private $_pos;

	private $files;

	/**
	 * Path
	 *
	 * @var string
	 */
	private $p;

	/**
	 * Name
	 *
	 * @var string
	 */
	private $n;

	/**
	 * Info
	 *
	 * @var string
	 */
	private $i;

	/**
	 * Error
	 *
	 * @var int
	 */
	private $e;

	/**
	 * Filename
	 *
	 * @var string
	 */
	private $f;


	/**
	 *  Type
	 */
	 private $pictureType;

	/**
	 * JBusinessDirectoryControllerUpload constructor
	 */
	public function __construct() {
		if (!extension_loaded('gd') && !extension_loaded('gd2')) {
			$this->p        = $this->n = '';
			$this->i        = 'GD is not loaded !';
			$this->e        = 3;
			$this->is_error = true;
			$this->pictureType = "";
		}

		$this->files       = JFactory::getApplication()->input->files;
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		parent::__construct();
	}

	/**
	 * Checks to see if the required parameters are present
	 *
	 * @param int $type upload, remove or uploadFile functions
	 */
	public function checkParams($type = TYPE_UPLOAD) {
		$this->_root_app = BD_PICTURES_UPLOAD_PATH;
		$pathType = JFactory::getApplication()->input->get('_path_type', 1);
		if ($pathType == 2) {
			$this->_root_app = JPATH_COMPONENT_SITE;
		} elseif ($pathType == 3) {
			$this->_root_app = JPATH_COMPONENT_ADMINISTRATOR;
		} elseif ($pathType == 4) {
			$this->_root_app = BD_ATTACHMENT_UPLOAD_PATH;
		}

		if ($type == TYPE_UPLOAD || $type == TYPE_UPLOAD_FILE) {
			$this->_target = JFactory::getApplication()->input->get('_target', null, 'RAW');
			$this->upload_no_image_image = JFactory::getApplication()->input->get('no_image', null, 'RAW');

			if ((empty($this->_target) || empty($this->_root_app)) && empty($this->upload_no_image_image)) {
				$this->p        = $this->n = '';
				$this->i        = 'Invalid params !';
				$this->e        = 2;
				$this->is_error = true;
			}
		} elseif ($type == TYPE_REMOVE) {
			$this->_filename = JFactory::getApplication()->input->get('_filename', null, 'RAW');
			$this->_pos      = JFactory::getApplication()->input->get('_pos', null, 'RAW');

			if (empty($this->_filename) || empty($this->_root_app)) {
				$this->p        = $this->n = '';
				$this->i        = 'Invalid params !';
				$this->e        = 2;
				$this->is_error = true;
			}
		}
	}

	/**
	 * Default upload function. Used for images.
	 */
	public function upload() {
		$this->checkParams();

		if (!$this->is_error) {
			$ex = array();
			$ex += explode('/', $this->_target);

			if ($this->_root_app[strlen($this->_root_app) - 1] != '/') {
				$this->_root_app .= '/';
			}
			$_target_tmp = JBusinessUtil::makePathFile($this->_root_app);
			foreach ($ex as $e) {
				if ($e == '') {
					continue;
				}

				$dir = $_target_tmp . $e;
				if (!is_dir($dir)) {
					if (!@mkdir($dir)) {
						$this->p        = $this->n = '';
						$this->i        = 'Error creating directory ' . $_target_tmp . DIRECTORY_SEPARATOR . $e . ' !';
						$this->e        = 2;
						$this->is_error = true;
						break;
					}
				}

				$_target_tmp .= $e . DIRECTORY_SEPARATOR;
			}
			
			if (!$this->is_error) {
				$identifier = 'file';
				if (!empty($this->files->get('uploadFile')) || !empty($this->files->get('uploadfile'))) {
					$identifier = 'uploadfile';
				}

				if (!empty($this->files->get('markerfile'))) {
					$identifier = 'markerfile';
				}

				if (!empty($this->files->get('uploadLogo'))) {
					$identifier = 'uploadLogo';
				}

				if (!empty($this->files->get('iconfile'))) {
					$identifier = 'iconfile';
				}

				if (!empty($this->files->get('croppedimage'))) {
					$identifier = 'croppedimage';
					$type       = $this->files->get('croppedimage')['type'];
					$cropName   = 'cropped.' . substr($type, strpos($type, '/') + 1, strlen($type) - strpos($type, '/'));
				}

				$_file = $this->files->get($identifier);
				if ($identifier == 'croppedimage') {
					$_file['name'] = $cropName;
				}

				if($this->appSettings->enable_crop && !empty(JFactory::getApplication()->input->get('croppable'))) {
					$imageName = JFactory::getApplication()->input->get('image_name');
					if(empty($imageName)) {
						$imageName = "DSC";
					}
				} else {
					$imageName = substr($_file['name'], 0, strrpos($_file['name'], '.'));
					$imageName = preg_replace("/[^a-zA-Z0-9.]/", "_", $imageName);
				}
				
				$imageExt = substr($_file['name'], strrpos($_file['name'], '.'));
				$resultFileName = $imageName . "_" . time() . $imageExt;

				$fileExt = strtolower($imageExt);
				$fileExt = str_replace(".", "", $fileExt);
				if (strpos(ALLOWED_FILE_EXTENSIONS, $fileExt) === false) {
					$this->p        = $this->n = '';
					$this->i        = 'File extension not allowed!';
					$this->e        = 5;
					$this->is_error = true;
				}

				if (!strpos($this->_root_app, 'uploads') || (!strpos($this->_root_app, 'pictures')
						&& !strpos($this->_root_app, 'attachments'))) {
					$this->p        = $this->n = '';
					$this->i        = 'File path not valid!';
					$this->e        = 6;
					$this->is_error = true;
				}

				if ($identifier == 'file') {
					$resultFileName = $imageName . $imageExt;
				}

				if (empty($this->upload_no_image_image)) {
					$this->_target = $this->_root_app . $this->_target . basename($resultFileName);
				}else{
					$this->_target = $this->_root_app . $this->_target . "no_image.jpg";
				}
				$file_tmp      = JBusinessUtil::makePathFile($this->_target);

				if (!$this->is_error) {
					$pictureType = JFactory::getApplication()->input->get('picture_type');
					$this->pictureType = $pictureType;

					if (strcmp($pictureType, PICTURE_TYPE_COMPANY) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->cover_width) ? $this->appSettings->cover_width : MAX_COMPANY_PICTURE_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->cover_height) ? $this->appSettings->cover_height : MAX_COMPANY_PICTURE_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_OFFER) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_OFFER_PICTURE_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->gallery_height) ? $this->appSettings->gallery_height : MAX_OFFER_PICTURE_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_LOGO) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->logo_width) ? $this->appSettings->logo_width : MAX_LOGO_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->logo_height) ? $this->appSettings->logo_height : MAX_LOGO_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_EVENT) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_OFFER_PICTURE_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->gallery_height) ? $this->appSettings->gallery_height : MAX_OFFER_PICTURE_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_GALLERY) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_GALLERY_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->gallery_height) ? $this->appSettings->gallery_height : MAX_GALLERY_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_SPEAKER) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->speaker_img_width) ? $this->appSettings->speaker_img_width : MAX_SPEAKER_IMAGE_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->speaker_img_height) ? $this->appSettings->speaker_img_height : MAX_SPEAKER_IMAGE_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_THUMBNAIL) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_THUMBNAIL_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->gallery_height) ? $this->appSettings->gallery_height : MAX_THUMBNAIL_HEIGHT;
					} elseif (strcmp($pictureType, PICTURE_TYPE_CATEGORY_ICON) == 0) {
						$this->maxPictureWidth  = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_CATEGORY_ICON_IMAGE_WIDTH;
						$this->maxPictureHeight = !empty($this->appSettings->gallery_width) ? $this->appSettings->gallery_width : MAX_CATEGORY_ICON_IMAGE_HEIGHT;
					}

					if (move_uploaded_file($_file['tmp_name'], $file_tmp)) {
						$image        = new Resize_Image;
						$image->ratio = true;

						$size       = getimagesize($file_tmp);
						$imageRatio = 1;
						if(!empty($size[1])){
							$imageRatio = $size[0] / $size[1];
						}

						$ratio = $imageRatio;
						if(!empty($this->maxPictureHeight)){
							$this->maxPictureWidth / $this->maxPictureHeight;
						}
						
						if ($size[0] < $this->maxPictureWidth || $size[1] < $this->maxPictureHeight) {
							$this->triggerWarning = true;
						}

						if (!isset($this->maxPictureWidth)) {
							$this->maxPictureWidth  = $size[0];
							$this->maxPictureHeight = $size[1];
						}
						$resizeImage = false;

						// if crop is enabled, resize the image only if it is being sent by cropper functions
						$checkSize = true;
						if ($this->appSettings->enable_crop) {
							$croppable = JFactory::getApplication()->input->get('croppable');
							$crop      = JFactory::getApplication()->input->get('crop');

							if (!empty($croppable) && empty($crop)) {
								$checkSize = false;
							}
						}

						//set new height or new width depending on image ratio
						if (($size[1] > $this->maxPictureHeight || $size[0] > $this->maxPictureWidth) && $checkSize) {
							if ($ratio > 1) {
								$image->new_width = $this->maxPictureWidth;
							} else {
								$image->new_height = $this->maxPictureHeight;
							}
							$resizeImage = true;
						}

						$image->image_to_resize = $file_tmp;    // Full Path to the file

						$image->new_image_name = basename($file_tmp);
						$image->save_folder    = dirname($file_tmp) . DIRECTORY_SEPARATOR;

						if ($resizeImage) {
							$process = $image->resize();
							if ($process['result'] && $image->save_folder) {
								$this->p = basename($file_tmp);
								$this->n = basename($file_tmp);
								$this->i = $file_tmp;
								$this->e = 0;
							} else {
								unlink($file_tmp);
								$this->p = $this->n = '';
								$this->i = 'Error resize uploaded file';
								$this->e = 4;
							}
						} else {
							$this->p = basename($file_tmp);
							$this->n = basename($file_tmp);
							$this->i = $file_tmp;
							$this->e = 0;
						}
					} else {
						$this->p = $this->n = '';
						$this->i = 'Error move uploaded file';
						$this->e = 2;
					}
				}
			}
		}

		$this->sendResponse();
	}

	/**
	 * Uploads file
	 */
	public function uploadFile() {
		$this->checkParams(TYPE_UPLOAD_FILE);

		if (!$this->is_error) {
			$ex = array();
			$ex += explode('/', $this->_target);

			if ($this->_root_app[strlen($this->_root_app) - 1] != '/') {
				$this->_root_app .= '/';
			}
			$_target_tmp = JBusinessUtil::makePathFile($this->_root_app);

			foreach ($ex as $e) {
				if ($e == '') {
					continue;
				}

				$dir = $_target_tmp . $e;
				if (!is_dir($dir)) {
					if (!@mkdir($dir)) {
						$this->p        = $this->n = '';
						$this->i        = 'Error creating directory ' . $_target_tmp . DIRECTORY_SEPARATOR . $e . ' !';
						$this->e        = 2;
						$this->is_error = true;
						break;
					}
				}

				$_target_tmp .= $e . DIRECTORY_SEPARATOR;
			}

			if (!strpos($this->_root_app, 'uploads') || (!strpos($this->_root_app, 'pictures')
					&& !strpos($this->_root_app, 'attachments'))) {
				$this->p        = $this->n = '';
				$this->i        = 'File path not valid!';
				$this->e        = 6;
				$this->is_error = true;
			}

			if (!$this->is_error) {
				$identifier = 'uploadAttachment';

				$_file = $this->files->get($identifier);

				$fileName = substr($_file['name'], 0, strrpos($_file['name'], '.'));
				$fileName = preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
				$fileExt  = substr($_file['name'], strrpos($_file['name'], '.'));
				;
				$fileExt = strtolower($fileExt);

				$fileEx          = str_replace(".", "", $fileExt);
				$allowedFileSize = JBusinessUtil::convertToBytes(ALLOWED_FILE_SIZE);
				if ($_file['size'] > $allowedFileSize) {
					$this->p = $this->n = '';
					$this->i = 'File size is higher than ' . ALLOWED_FILE_SIZE . '!';
					$this->e = 3;
				} elseif (strpos(ALLOWED_FILE_EXTENSIONS, $fileEx) === false) {
					$this->p = $this->n = '';
					$this->i = 'File extension not allowed!';
					$this->e = 2;
				} else {
					$resultFileName = $fileName . "-" . time() . $fileExt;
					$this->_target  = $this->_root_app . $this->_target . basename($resultFileName);
					$file_tmp       = JBusinessUtil::makePathFile($this->_target);

					$this->p = basename($file_tmp);
					$this->n = basename($file_tmp);
					$this->i = $file_tmp;
					$this->e = 0;
					if (!move_uploaded_file($_file['tmp_name'], $file_tmp)) {
						$this->p = $this->n = '';
						$this->i = 'Error move uploaded file';
						$this->e = 2;
					}
				}
			}
		}

		$this->sendResponse(TYPE_UPLOAD_FILE);
	}

	/**
	 * Removes file/image
	 */
	public function remove() {
		$this->checkParams(TYPE_REMOVE);

		if (!$this->is_error) {
			if ($this->_root_app[strlen($this->_root_app) - 1] != '/' && $this->_filename[strlen($this->_filename) - 1] != '/') {
				$this->_root_app .= '/';
			}
			$file_tmp = JBusinessUtil::makePathFile($this->_root_app . $this->_filename);

			if (@unlink($file_tmp)) {
				$this->f = htmlentities($file_tmp);
				$this->n = basename($file_tmp);
				$this->i = $file_tmp;
				$this->e = 0;
			} else {
				$this->f = $this->n = $file_tmp;
			}
		}

		$this->sendResponse(TYPE_REMOVE);
	}

	/**
	 * Sends xml response after one of the actions has been done
	 *
	 * @param int $type upload, remove or uploadFile functions
	 */
	public function sendResponse($type = TYPE_UPLOAD) {
		if ($type == TYPE_UPLOAD) {
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			echo '<uploads>';
			if ($this->triggerWarning) {
				echo '<warning value="1" width="' . $this->maxPictureWidth . '" height="' . $this->maxPictureHeight . '"></warning>';
			}
			echo '<picture path="' . $this->p . '" info="' . $this->i . '" name="' . $this->n . '" error="' . $this->e . '" picture-type="'.$this->pictureType.'" />';
			echo '</uploads>';
			echo '</xml>';
		} elseif ($type == TYPE_REMOVE) {
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			echo '<remove>';
			echo '<picture filename="' . $this->f . '" info="' . $this->i . '" name="' . $this->n . '" error="' . $this->e . '" pos="' . $this->_pos . '" />';
			echo '</remove>';
			echo '</xml>';
		} elseif ($type == TYPE_UPLOAD_FILE) {
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			echo '<uploads>';
			echo '<attachment path="' . $this->p . '" info="' . $this->i . '" name="' . $this->n . '" error="' . $this->e . '" />';
			echo '</uploads>';
			echo '</xml>';
		}

		exit;
	}
}
