<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.model');

class JBusinessDirectoryModelLanguage extends JModelLegacy {
	
	function __construct() {
		parent::__construct();
		$this->jInput = JFactory::getApplication()->input;
	}
	
	public function getData() {
		// Load the data
	}

	/**
	 *
	 * @param
	 *        	$array1
	 * @param
	 *        	$array2
	 * @return array
	 */
	private function uniqueValues($array1, $array2) {
		$finalArray = array();
		if (! empty($array1)) {
			foreach ($array1 as $k => $value) {
				foreach ($array2 as $key => $values) {
					$value2 = str_replace(PHP_EOL, null, $values);
					if ($value == $value2) {
						$finalArray [$key] = $value2;
					}
				}
			}
		}
		$a = array_diff($array1, $finalArray);
		$b = array_diff($finalArray, $array1);
		$c = array_merge($a, $b);
		return array_unique($c);
	}
	
	/**
	 * Create a new language
	 *
	 * @return unknown
	 */
	public function createLanguage() {
		set_time_limit(300);
		$code = $this->jInput->getString('code');
		$content = $_POST['content'];
		$content = JBusinessUtil::make_safe_for_utf8_use($content);
		
		if (! empty($code) && ! empty($content)) {
			$path = JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . $code . DS . $code . '.' . JBusinessUtil::getComponentName() . '.ini';
			$sysPath = JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . $code . DS . $code . '.' . JBusinessUtil::getComponentName() . '.sys.ini';

			JFolder::create(JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . $code, $mode = 0755);

			JFile::write($path, $content);
			$this->fileAppend('', $sysPath);
			$msg = JText::_('LNG_LANGUAGE_SUCCESSFULLY_SAVED', true);
		}
		return $msg;
	}
	
	/**
	 * Save the language file together with the custom content
	 *
	 * @param array $onInstallOptions
	 * @return void|unknown
	 */
	public function saveLanguage($onInstallOptions = array()) {
		set_time_limit(300);
		$app = JFactory::getApplication();

		if (count($onInstallOptions) > 0) {
			$code = $onInstallOptions ["code"];
			$content = $onInstallOptions ["content"];
			$custom_content = $onInstallOptions ["custom_content"];
		} else {
			$code = $this->jInput->getString('code');
			$content = $_POST['content'];
			$custom_content = $_POST['custom_content'];
		}

		if (empty($code)) {
			$app->enqueueMessage(JText::_('LNG_CODE_NOT_SPECIFIED', true));
			return;
		}

		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jbusinessdirectory' . DS . 'language' . DS . $code . DS . $code . '.' . 'com_jbusinessdirectory' . '.ini';
		$newPath = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jbusinessdirectory' . DS . 'language' . DS . $code . DS . $code . '-custom.' . 'com_jbusinessdirectory' . '.ini';

		if (file_exists($path)) {
			if (! empty($content)) {
				$content = JBusinessUtil::make_safe_for_utf8_use($content);
				JFile::write($path, $content);
				$msg = JText::_('LNG_LANGUAGE_SUCCESSFULLY_SAVED', true);
			} else {
				$empty = ' ';
				JFile::write($path, $empty);
				$msg = JText::_('LNG_LANGUAGE_SUCCESSFULLY_SAVED', true);
			}
		} else {
			$app->enqueueMessage('File not found : ' . $path);
			$msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_ERROR_SAVING_LANGUAGE'), 'error');
		}

		$custom_content = JBusinessUtil::make_safe_for_utf8_use($custom_content);
		$contentToSave = preg_split('/([\r\n\t])/', $custom_content);
		$uniqueValues = array_unique($contentToSave);
		$savedContent = preg_split('/([\r\n\t])/', $content);
		$savedContent = array_unique($savedContent);

		switch (file_exists($newPath)) {
			case false:
				if (! empty($custom_content)) {
					$actualVal = file($path);
					$array1 = $this->uniqueValues($savedContent, $actualVal);
					// $array1 = array_unique($array1); // perform this function to minimize more the number of element on the array to save time
					$contentToAppendNewPath = "";
					foreach ($uniqueValues as $k => $value) {
						if (in_array($value, $array1)) {
							unset($uniqueValues [$k]);
						} else {
							$formatedContent = PHP_EOL . $value;
							$this->fileAppend($formatedContent, $path);
							$contentToAppendNewPath = $contentToAppendNewPath . $formatedContent;
						}
					}
					JFile::write($newPath, $contentToAppendNewPath);
					$msg = JText::_('LNG_LANGUAGE_SUCCESSFULLY_SAVED', true);
				}
				break;
			case true:
				if (! empty($custom_content) && ! empty($uniqueValues)) {
					// $fileValues = file($newPath);
					$actualVal = file($path);
					$array = $this->uniqueValues($uniqueValues, $actualVal);

					$array1 = $this->uniqueValues($savedContent, $actualVal);
					$array1 = array_unique($array1);

					foreach ($array as $key => $value) {
						if (! in_array($value, $array1)) {
							$splittedRow = explode("=",$value);
							foreach ($savedContent as $k => $v) {
								if (str_contains($v, $splittedRow[0]."=")) {
									$formatedContentToAppend = str_replace($v,$value,$content);
									JFile::write($path, $formatedContentToAppend);
								} 
							}
							if(!isset($formatedContentToAppend)){
								$formatedContentToAppend = PHP_EOL . $value;
								$this->fileAppend($formatedContentToAppend, $path);	
							}
						}
					}

					unlink($newPath);
					$uniqueValues = array_values($uniqueValues);
					file_put_contents($newPath, $uniqueValues [0]);
					for ($i = 1; $i < count($uniqueValues); $i ++) {
						$uniqueValues [$i] = PHP_EOL . $uniqueValues [$i];
						file_put_contents($newPath, $uniqueValues [$i], FILE_APPEND | LOCK_EX);
					}

					$msg = JText::_('LNG_LANGUAGE_SUCCESSFULLY_SAVED', true);
				}
				if (empty($custom_content) && $custom_content == '') {
					$delete = $this->deleteFile($newPath);
					$msg = JText::_($delete, true);
				}
				break;
			default:
				$app->enqueueMessage('File not found : ' . $newPath);
				$msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_ERROR_SAVING_LANGUAGE'), 'error');
				break;
		}
		return $msg;
	}

    public function getFile() {
		$app =JFactory::getApplication();
		$code = JFactory::getApplication()->input->getString('code');		
	
		$file = new stdClass();
		$file->name = $code;		
		$path = BD_LANGUAGE_FOLDER_PATH.DS.$code.DS.$code.'.'.JBusinessUtil::getComponentName().'.ini';
		$customPath = BD_LANGUAGE_FOLDER_PATH.DS.$code.DS.$code.'-custom.'.JBusinessUtil::getComponentName().'.ini';
		$file->path = $path;
		$file->customPath = $customPath;

		jimport('joomla.filesystem.file');
		$showLatest = true;
		$loadLatest = false;

		if (JFile::exists($path)) {
			$file->content = file_get_contents($path);
			if (empty($file->content)) {
				$app->enqueueMessage('File not found : '.$path);
			}
		} else {
			$loadLatest = true;
			$file->content = file_get_contents(BD_LANGUAGE_FOLDER_PATH.DS.'en-GB'.DS.'en-GB.'.JBusinessUtil::getComponentName().'.ini');
		}

		if (JFile::exists($customPath)) {
			$file->custom_content = file_get_contents($customPath);
			if (empty($file->custom_content)) {
				$app->enqueueMessage('File not found : '.$customPath);
			}
		} else {
			$file->custom_content = " ";
		}		
		
		return $file;
	}

	/**
     * @param $string
     * @return string
     * Function to check and format the content to UTF-8 Encoding
     */
    private function make_safe_for_utf8_use($string) {
        $encoding = mb_detect_encoding($string, "UTF-8,WINDOWS-1252");

        if ($encoding != 'UTF-8') {
            return iconv($encoding, 'UTF-8//TRANSLIT', $string);
        } else {
            return $string;
        }
    }

    /**
     * @param $array1
     * @param $array2
     * @return array
     */

    function send_email($code) {
        $subject = "New language proposal for J-BusinessDirectory - $code";
        $body = "Hi,<br/><br/>Please find attached the language files for $code language.";
        $to = LANGUAGE_RECEIVING_EMAIL;

        # Invoke JMail Class
        $mailer = JFactory::getMailer();
        $mailer->addRecipient($to);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        $mailer->isHTML(true);
        
        $languageFile = BD_LANGUAGE_FOLDER_PATH.DS.$code.DS.$code.'.'.JBusinessUtil::getComponentName().'.ini';
        $systemLanguageFile = BD_LANGUAGE_FOLDER_PATH.DS.$code.DS.$code.'.'.JBusinessUtil::getComponentName().'.sys.ini';

        if (file_exists($languageFile)) { $mailer->addAttachment($languageFile); }
        if (file_exists($systemLanguageFile)) { $mailer->addAttachment($systemLanguageFile); }

        if( $mailer->send() ) {
            $msg = JText::_('LNG_LANGUAGE_FILES_SUCCESSFULLY_SEND', true);
        } else {
            $msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_SOMETHING_WENT_WRONG'), 'error');
        }
        return $msg;
    }

    function fileAppend($content, $path) {
        $filePath = fopen($path, 'a+') or die(JFactory::getApplication()->enqueueMessage(JText::_('LNG_COULD_NOT_OPEN_THE_FILE'), 'error'));
        fwrite($filePath, $content);
        fclose($filePath);
        return true;
    }

    function writeFile($content, $path) {
        $filePath = fopen($path, 'w') or die(JFactory::getApplication()->enqueueMessage(JText::_('LNG_COULD_NOT_OPEN_THE_FILE'), 'error'));
        fwrite($filePath, $content);
        fclose($filePath);
        return true;
    }

    function deleteFile($path) {
        if(unlink($path)) {
            $msg = JText::_('LNG_CUSTOM_LANGUAGE_VALUES_DELETED', true);
        }
        else {
            $msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_UNABLE_TO_DELETE_CUSTOM_LANGUAGE_VALUES'), 'error');
        }
        return $msg;
    }

    function deleteFolder($code) {
        $languagePath = JFolder::delete(BD_LANGUAGE_FOLDER_PATH.DS.$code);
        if($languagePath) {
            $msg = JText::_('LNG_LANGUAGE_PACK_SUCCESSFULLY_DELETED', true);
        } else {
            $msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_LANGUAGE_PACK_NOT_DELETED'), 'error');
        }
        return $msg;
    }
}
?>