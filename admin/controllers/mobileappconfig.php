<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * The Company Controller
 *
 */
class JBusinessDirectoryControllerMobileAppConfig extends JControllerForm {
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=mobileappconfig', false));
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string $key The name of the primary key of the URL variable.
	 * @param   string $urlVar The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 */
	public function edit($key = null, $urlVar = null) {
		$app = JFactory::getApplication();
		$result = parent::edit();

		return true;
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$jinput = JFactory::getApplication()->input;
		$files = $jinput->files->getArray();

		$model  = $this->getModel('mobileappconfig');
		$data   = $jinput->post->getArray();

		$fileInputs = array();
		$build_uploads_path = JBusinessUtil::makePathFile(BD_MOBILE_APP_BUILD_UPLOAD_PATH.'/');

		foreach ($files as $inputName => $value) {
			$fileInputs[] = $inputName;
		}

		foreach ($fileInputs as $inputName) {
			jimport('joomla.filesystem.file');
			$file = $jinput->files->get($inputName, null, 'raw');
			if ($file['error'] == 0) {
				$fileName = JFile::makeSafe($file['name']);
				$fileTmp = $file['tmp_name'];

				// Rename the file based on language input name
				if (strpos($inputName, 'app_') === 0) {
					$languageCode = substr($inputName, strlen('app_'));
					$fileName = 'app_' . $languageCode . '.' . JFile::getExt($fileName);
				}

				$uploadPath = $build_uploads_path . $fileName;
				if (!JFile::upload($fileTmp, $uploadPath)) {
					$this->setError(JText::_('COM_JBUSINESSDIRECTORY_UPLOAD_ERROR'));
					return false;
				}
				$data[$inputName] = $fileName;
			} 
		}

		// Generate the xml
		$xml = $this->generateConfigXml($data);
		$xmlFileName = "client_configs.xml";
		$xmlFilePath = $build_uploads_path . $xmlFileName;
		file_put_contents($xmlFilePath, $xml->asXML());

		// Add the xml file name to the data array
		$data['client_configs'] = $xmlFileName;
		$data['fileInputs'] = implode(',', $fileInputs);
		
		if ($model->store($data)) {
			$msg = JText::_('LNG_MOBILE_APP_CONFIG_SAVED');
		} else {
			$msg = JText::_('LNG_ERROR_SAVING_MOBILE_APP_CONFIG');
		}

		$task = $this->getTask();

		switch ($task) {
			case 'apply':
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=edit', false), $msg);
				break;
			default:
				// Check the table in so it can be edited.... we are done with it anyway
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item, false), $msg);
				break;
		}
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel($key = null) {
		$msg = JText::_('LNG_OPERATION_CANCELLED');
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=mobileappconfig', $msg);
	}

	public function sendTopicNotificationsAjax() {
		$title = JFactory::getApplication()->input->get("title", '', 'RAW');
		$body  = JFactory::getApplication()->input->get("body", '', 'RAW');
		$topic = JFactory::getApplication()->input->get("topic");

		$notification          = array();
		$notification["title"] = $title;
		$notification["body"]  = $body;

		$result = NotificationService::sendTopicNotifications($notification, $topic);

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function updateOrderDetailsAjax() {
		$isAndroid = JFactory::getApplication()->input->get("isAndroid");
		$androidOrderId = JFactory::getApplication()->input->get("androidOrderId");
		$iosOrderId = JFactory::getApplication()->input->get("iosOrderId");
		$androidOrderEmail = JFactory::getApplication()->input->get("androidOrderEmail");
		$iosOrderEmail = JFactory::getApplication()->input->get("iosOrderEmail");

		$data          = array();
		if($isAndroid) {
			$data["androidOrderId"]  = $androidOrderId;
			$data["androidOrderEmail"]  = $androidOrderEmail;
		} else {
			$data["iosOrderId"]  = $iosOrderId;
			$data["iosOrderEmail"]  = $iosOrderEmail;
		}

		$model  = $this->getModel('mobileappconfig');
		$result = $model->store($data);

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}


	public function reset() {
		$model = $this->getModel('mobileappconfig');
		if(!$model->resetConfigurations()){
			$msg = JText::_('LNG_ERROR_CONFIG_RESET');
		} else {
			$msg = JText::_('LNG_CONFIG_RESET_SUCCESS');
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=mobileappconfig', $msg);
	}

	public function generateConfigXml($data) {
		$object = json_decode(json_encode((object) $data));

		$xml = new SimpleXMLElement('<resources/>');

		$colorElements = array("primaryColor", "backgroundColor", "textPrimary", "genericText", "iconColor");
		foreach ($colorElements as $color) {
			if(isset($object->$color)){
				$colorNode = $xml->addChild('color');
				$colorNode->addAttribute('name', $color);
				$colorNode[0] = str_replace("#", "", $object->$color);
			}
		}

		$boolElements = array("isLocationMandatory", "showLatestListings", "showFeaturedListings", "showFeaturedOffers", "showFeaturedEvents", "showOffers", "showEvents", "enableReviews", "showMessages", "isJoomla", "allowGuests", "showEmails", "enableGoogleLogin", "enableFacebookLogin");
		foreach ($boolElements as $bool) {
			if(isset($object->$bool)){
				$boolNode = $xml->addChild('bool');
				$boolNode->addAttribute('name', $bool);
				$boolNode[0] = ($object->$bool == 1) ? 'true' : 'false';
			}
		}

		$stringElements = array("customer", "baseUrl", "mapsApiKey", "limit", "mapType");
		foreach ($stringElements as $string) {
			if(isset($object->$string)){
				$stringNode = $xml->addChild('string');
				$stringNode->addAttribute('name', $string);
				$stringNode->addAttribute('translatable', 'false');
				if ($string == 'baseUrl') {
					$baseurl = $object->$string;
					if(substr($baseurl, -1) != "/"){
						$baseurl .= "/";
					}
					$dom = dom_import_simplexml($stringNode);
					$cdata = $dom->ownerDocument->createCDATASection($baseurl);
					$dom->appendChild($cdata);
				} else {
					$stringNode[0] = $object->$string;
				}
			}
		}

		$language_keys = $xml->addChild('string-array');
		$language_keys->addAttribute('name', 'language_keys');

		$language_values = $xml->addChild('string-array');
		$language_values->addAttribute('name', 'language_values');

        $language_keys_values = $object->language_keys;
        foreach ($language_keys_values as $key) {
            $key = explode("-",$key)[0];
            $language_keys->addChild('item', $key);
        }
        foreach ($language_keys_values as $key) {
            $value = Locale::getDisplayLanguage(($key),'en_US');
            $language_values->addChild('item', $value);
        }

		return $xml;
	}

	public function build()
	{
		$jinput = JFactory::getApplication()->input;
		$configs = JBusinessUtil::getMobileAppSettings();
		$data = get_object_vars($configs);
		$fileInputs = explode(',', $data['fileInputs']);

		foreach($fileInputs as $inputName) {
			if(!empty($data[$inputName])) {
				$filepath = $data[$inputName];
				$file =  new CURLFile(BD_MOBILE_APP_BUILD_UPLOAD_PATH.'/'.$filepath);
				$data[$inputName] = $file;
			}
		}
		$data['is_android']	= (int) $jinput->getInt('is_android');

		$data['domain'] = $data['baseUrl'];
		if($data['is_android']) {
			$data['orderId'] = $data['androidOrderId'];
			$data['orderEmail'] = $data['androidOrderEmail'];
		} else {
			$data['orderId'] = $data['iosOrderId'];
			$data['orderEmail'] = $data['iosOrderEmail'];
		}

		$data['method'] = 'generateBuild';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.cmsjunkie.com/mobile_upload/endpoint/index.php');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data')); //check authorization
		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response);

		if($res->id == '0') {
			$type = 'success';
		} else {
			$type = 'error';
		}

		if(curl_error($ch)){
			//if curl error
			$this->setRedirect('index.php?option=com_jbusinessdirectory&view=mobileappconfig', 'There was an error processing your request. Please try again later', 'error');
		}else{
			$this->setRedirect('index.php?option=com_jbusinessdirectory&view=mobileappconfig', $res->message, $type);
		}
	}

	public function getBuildCountAjax() {
		$is_android = (int) JFactory::getApplication()->input->getInt('is_android');
		$mobileAppSettings = JBusinessUtil::getMobileAppSettings();

		if($is_android == '1') {
			$orderId = $mobileAppSettings->androidOrderId;
			$orderEmail = $mobileAppSettings->androidOrderEmail;
		} else {
			$orderId = $mobileAppSettings->iosOrderId;
			$orderEmail = $mobileAppSettings->iosOrderEmail;
		}
		$baseUrl = $mobileAppSettings->baseUrl;


		$url = 'https://www.cmsjunkie.com/mobile_upload/endpoint/index.php';
		 // Data to be sent in the POST request
		 $data = array('orderId' => $orderId, 'orderEmail' => $orderEmail, 'method' => 'buildCount', 'is_android' => $is_android, 'domain' => $baseUrl);
		  // Setup cURL
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($ch, CURLOPT_FAILONERROR, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: multipart/form-data',
			'Access-Control-Allow-Origin: *'
		));
		  // Execute the cURL request
		  $response = curl_exec($ch);
  
		  // Close the cURL session
		  curl_close($ch);
  
		  // Process the API response
		  $result = json_decode($response);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}