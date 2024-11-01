<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewBillingOverview extends JBusinessDirectoryFrontEndView {
	public function __construct() {
		parent::__construct();
	}
	
	public function display($tpl = null) {
		$this->items =  $this->get('Orders');
		$this->appSettings =  JBusinessUtil::getApplicationSettings();
		$this->pagination	= $this->get('Pagination');
		
		parent::display($tpl);
	}
}
