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

class JBusinessDirectoryViewListingConfirmation extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	
	public function display($tpl = null) {
		$layout = JFactory::getApplication()->input->get('layout', null);
		if (isset($layout)) {
			$tpl = $layout;
		}
		$this->state = $this->get('State');

		$this->listingId = JFactory::getApplication()->input->get("listing_id");
		$this->onlyContribute = JFactory::getApplication()->input->get("only_contribute");
		$this->userCreated = JFactory::getApplication()->input->get("user_created");
		//$this->allowFreeTrial = JFactory::getApplication()->input->get("allow_free_trial");
		
		parent::display($tpl);
	}
	
}
