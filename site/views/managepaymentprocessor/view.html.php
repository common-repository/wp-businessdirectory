<?php
/**
 * @package     JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');


JBusinessUtil::loadJQueryChosen();

JBusinessUtil::enqueueStyle('libraries/dropzone/basic.css');

JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');

JBusinessUtil::enqueueScript('libraries/chosen/ajax-chosen.min.js');

// following translations will be used in js
JText::script('LNG_IMAGE_SIZE_WARNING');

JBusinessUtil::includeValidation();

require_once BD_HELPERS_PATH . '/helper.php';

/**
 * The HTML  View.
 */
class JBusinessDirectoryViewManagePaymentProcessor extends JViewLegacy {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->companies = JBusinessUtil::getCompaniesByUserId();
		$this->item      = $this->get('Item');
		$this->state     = $this->get('State');
		
		$this->item->services = JBusinessUtil::getAvailablePayableServices(true);

		$this->statuses = JBusinessDirectoryHelper::getStatuses();
		$this->modes = JBusinessDirectoryHelper::getModes();

		$this->defaultProcessors = JBusinessUtil::getDefaultPaymentProcessors(true);
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		$user                 = JBusinessUtil::getUser();
		$this->companyOptions = JBusinessUtil::getCompaniesOptions($this->item->company_id, $user->ID);

		$this->actions = JBusinessDirectoryHelper::getActions();

		parent::display($tpl);
	}
}
