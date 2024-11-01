<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::enqueueStyle('libraries/magnific-popup/magnific-popup.css');

JBusinessUtil::enqueueScript('libraries/jquery/jquery.opacityrollover.js');
JBusinessUtil::enqueueScript('libraries/magnific-popup/jquery.magnific-popup.min.js');

JBusinessUtil::enqueueScript('libraries/star-rating/star-rating.js');
JBusinessUtil::enqueueStyle('libraries/star-rating/star-rating.css');

JBusinessUtil::includeValidation();

// following translations will be used in js
JText::script('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageReviews extends JBusinessDirectoryFrontEndView {
	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->types        = $this->get('ReviewTypes');
		$this->actions = JBusinessDirectoryHelper::getActions();
		
		parent::display($tpl);
	}
}
