<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$appSettings = JBusinessUtil::getApplicationSettings();
if ($appSettings->enable_ratings) {
    JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
    JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');
}

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewSuggestions extends JBusinessDirectoryFrontEndView {
	protected $pagination;
	protected $items;
	protected $state;

	public function __construct() {
        $this->userDashboard = true;
	    parent::__construct();
	}

	public function display($tpl = null) {
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		$this->state		= $this->get('State');
		$this->total		= $this->get('TotalItems');

		parent::display($tpl);
	}
}
