<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_OFFERS_CONFIRM_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

JBusinessUtil::loadJQueryUI();

class JBusinessDirectoryViewManageCompanyOffers extends JBusinessDirectoryFrontEndView {
	public function __construct() {
		parent::__construct();
	}
	
	public function display($tpl = null) {
		$this->companyId 	= $this->get('CompanyId');
		$this->items		= $this->get('Offers');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->total		= $this->get('Total');
		$this->isCreateOfferAllow = $this->get('CreateOfferPermission');
		
		$this->actions = JBusinessDirectoryHelper::getActions();

		parent::display($tpl);
	}
}
