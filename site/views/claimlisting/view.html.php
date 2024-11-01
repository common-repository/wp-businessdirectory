<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */
JBusinessUtil::includeValidation();

class JBusinessDirectoryViewClaimListing extends JViewLegacy {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->appSettings =  JBusinessUtil::getApplicationSettings();
		
		$this->claimListing = JFactory::getApplication()->input->get("claim_listing_id");
		$this->company = $this->get("Company");

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, PRIVACY_POLICY_TRANSLATION);
		}
		
		parent::display($tpl);
	}
}
