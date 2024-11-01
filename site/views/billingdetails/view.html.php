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
JText::script('LNG_SELECT_CITY');
JText::script('LNG_SELECT_REGION');

/**
 * The HTML  View.
 */
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
JBusinessUtil::includeValidation();

JBusinessUtil::loadJQueryUI();

class JBusinessDirectoryViewBillingDetails extends JBusinessDirectoryFrontEndView {
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->item	 = $this->get('Item');

		$this->state = $this->get('State');
		$this->company = $this->get('Company');
		$this->orderId = JFactory::getApplication()->input->get("orderId");
		$this->userCreated = JFactory::getApplication()->input->get("userCreated");
		$this->order   = $this->get('Order');

		if ($this->appSettings->limit_cities_regions) {
			$this->cities = $this->get('Cities');
			$this->regions = $this->get('Regions');
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		if (!empty($this->orderId)) {
			parent::displayParent($tpl);
		} else {
			parent::display($tpl);
		}
	}
}
