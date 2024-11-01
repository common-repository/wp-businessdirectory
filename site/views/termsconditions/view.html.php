<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryViewTermsConditions extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	
	public function display($tpl = null) {
		$jinput = JFactory::getApplication()->input;
		$this->appSettings = JBusinessUtil::getApplicationSettings();       
        $this->type = $jinput->getString('type');
		

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, REVIEWS_TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, CONTACT_TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, RESPONSIBLE_CONTENT_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, PRIVACY_POLICY_TRANSLATION);
		}

        $this->generalTermsConditions = $this->appSettings->terms_conditions;
        $this->reviewsTermsConditions = $this->appSettings->reviews_terms_conditions;
        $this->contactTermsConditions = $this->appSettings->contact_terms_conditions;
        $this->privacyPolicy = $this->appSettings->privacy_policy;

		parent::display($tpl);
	}
}
