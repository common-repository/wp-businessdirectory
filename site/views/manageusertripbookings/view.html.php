<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// following translations will be used in js

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageUserTripBookings extends JBusinessDirectoryFrontEndView {
	protected $items;
	protected $pagination;
	protected $state;

	public function __construct() {
		$this->userDashboard = true;
		parent::__construct();
	}

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->states = JBusinessUtil::getTripBookingStates();

		$this->actions = JBusinessDirectoryHelper::getActions();

		parent::display($tpl);
	}
}
