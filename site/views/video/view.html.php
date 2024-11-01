<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');

class JBusinessDirectoryViewVideo extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->video = $this->get('Video');
		$this->relatedVideos = $this->get('RelatedVideos');

		if (empty($this->video)) {
			$tpl="inactive";
		} else {
			$jinput = JFactory::getApplication()->input;
			$layout = $jinput->getString('layout');
			if (!empty($layout)) {
				$tpl = $layout;
				if ($layout == 'default') {
					$tpl = null;
				}
			}
		}

		parent::display($tpl);
	}
}
