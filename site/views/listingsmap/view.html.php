<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::loadJQueryChosen();

// following translations will be used in js
JText::script('LNG_SHOW_FILTER');
JText::script('LNG_HIDE_FILTER');
JText::script('LNG_DISTANCE');
JText::script('LNG_MILES');
JText::script('LNG_KM');
JText::script('LNG_CATEGORIES');
JText::script('LNG_MORE');
JText::script('LNG_LESS');
JText::script('LNG_TYPES');
JText::script('LNG_COUNTRIES');
JText::script('LNG_REGIONS');
JText::script('LNG_CITIES');
JText::script('LNG_AREA');
JText::script('LNG_PROVINCE');
JText::script('LNG_MONTHS');

class JBusinessDirectoryViewListingsMap extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}
	
	public function display($tpl = null) {
		$state = $this->get('State');
		$this->params = $state->get("parameters.menu");

		$this->items = $this->get('Items');

		parent::display($tpl);
	}
}
