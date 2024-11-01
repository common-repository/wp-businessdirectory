<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

class Resize_Image {
	public $image_to_resize;
	public $new_width;
	public $new_height;
	public $ratio;
	public $new_image_name;
	public $save_folder;

	/**
	 * Resize image to the new width and height
	 *
	 * @return array|void return result containing the new image path
	 */
	public function resize() {
		if (!file_exists($this->image_to_resize)) {
			var_dump("File ".$this->image_to_resize." does not exist.");
			return;
		}

		$info = GetImageSize($this->image_to_resize);

		if (empty($info)) {
			var_dump("The file ".$this->image_to_resize." doesn't seem to be an image.");
			return;
		}

		$width 		= $info[0];
		$height	 	= $info[1];
		$mime 		= $info['mime'];

		/*
		Keep Aspect Ratio?

		*/

		if ($this->ratio) {
			// if preserving the ratio, only new width or new height
			// is used in the computation. if both
			// are set, use width

			if (isset($this->new_width)) {
				$factor = (float)$this->new_width / (float)$width;
				$this->new_height = $factor * $height;
			} elseif (isset($this->new_height)) {
				$factor = (float)$this->new_height / (float)$height;
				$this->new_width = $factor * $width;
			} else {
				var_dump('neither new height or new width has been set');
				return;
			}
		}

		// What sort of image?

		$type = substr(strrchr($mime, '/'), 1);

		switch ($type) {
		case 'jpeg':
			$image_create_func 	= 'ImageCreateFromJPEG';
			$image_save_func 	= 'ImageJPEG';
			$new_image_ext 		= 'jpg';
			break;

		case 'png':
			$image_create_func 	= 'ImageCreateFromPNG';
			$image_save_func 	= 'ImagePNG';
			$new_image_ext 		= 'png';
			break;

		case 'webp':
			$image_create_func 	= 'ImageCreateFromWebP';
			$image_save_func 	= 'ImageWEBP';
			$new_image_ext 		= 'webp';
			break;
		
		case 'bmp':
			$image_create_func 	= 'ImageCreateFromBMP';
			$image_save_func 	= 'ImageBMP';
			$new_image_ext 		= 'bmp';
			break;

		case 'gif':
			$image_create_func 	= 'ImageCreateFromGIF';
			$image_save_func 	= 'ImageGIF';
			$new_image_ext 		= 'gif';
			break;

		case 'vnd.wap.wbmp':
			$image_create_func 	= 'ImageCreateFromWBMP';
			$image_save_func 	= 'ImageWBMP';
			$new_image_ext 		= 'bmp';
			break;

		case 'xbm':
			$image_create_func 	= 'ImageCreateFromXBM';
			$image_save_func 	= 'ImageXBM';
			$new_image_ext 		= 'xbm';
			break;

		default:
			$image_create_func 	= 'ImageCreateFromJPEG';
			$image_save_func 	= 'ImageJPEG';
			$new_image_ext 		= 'jpg';
		}

		// New Image
		$image_c = ImageCreateTrueColor($this->new_width, $this->new_height);

		$new_image = $image_create_func($this->image_to_resize);

		imagealphablending($image_c, false);
		imagesavealpha($image_c, true);
		
		ImageCopyResampled($image_c, $new_image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);

		if ($this->save_folder) {
			if ($this->new_image_name) {
				$new_name = $this->new_image_name;//.'.'.$new_image_ext;
			} else {
				$new_name = $this->new_thumb_name(basename($this->image_to_resize)).'_resized.'.$new_image_ext;
			}

			$save_path = $this->save_folder.$new_name;
		} else {
			/* Show the image without saving it to a folder */
			header("Content-Type: ".$mime);

			$image_save_func($image_c);

			$save_path = '';
		}

		$process = $image_save_func($image_c, $save_path);

		return array('result' => $process, 'new_file_path' => $save_path);
	}

	/**
	 * Retrieve thumb name for the file
	 *
	 * @param $filename string file name
	 * @return string|string[]
	 */
	public function new_thumb_name($filename) {
		$string = trim($filename);
		$string = strtolower($string);
		$string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));
		$string = ereg_replace("[ tnr]+", "_", $string);
		$string = str_replace(" ", '_', $string);
		$string = ereg_replace("[ _]+", "_", $string);

		return $string;
	}
}
