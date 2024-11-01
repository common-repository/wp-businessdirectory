<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


JBusinessUtil::includeValidation();

class JBusinessDirectoryViewBusinessUser extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	public function display($tpl = null) {

		$user = JBusinessUtil::getUser();
		if(!empty($user->ID)){
			$app = JFactory::getApplication();
			$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
			$url = $base_url . $_SERVER["REQUEST_URI"];
			$url .= "?task=businessuser.checkuser";
			$app->redirect($url);
		}

		$this->form   = $this->get('Form');
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->filter_package = JFactory::getApplication()->input->get("filter_package");
		$this->claimListing = JFactory::getApplication()->input->get("claim_listing_id");
		$this->editorListingId = JFactory::getApplication()->input->get("editor_listing_id");
		$this->serviceType = JFactory::getApplication()->input->get("serviceType");
		$this->orderId = JFactory::getApplication()->input->get("orderId");
		$this->companyId = JFactory::getApplication()->input->get("companyId");
		$this->package = JBusinessUtil::getPackage($this->filter_package);
		$this->company = JBusinessUtil::getCompany($this->claimListing);
		
		if (!empty($this->package)) {
			$this->packageFeatures = JBusinessDirectoryHelper::getDefaultPackageFeatures($this->package);
			$this->customAttributes = JBusinessUtil::getPackagesAttributes($this->package);
		}

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, PRIVACY_POLICY_TRANSLATION);
		}
		parent::display($tpl);
	}
}
