<?php
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class JBusinessDirectoryViewActivityItinerary extends JViewLegacy {
	public function __construct() {
		parent::__construct();
	}


	public function display($tpl = null) {
		$jinput = JFactory::getApplication()->input;

		$this->items = $this->get('Items');
		$startDate = $jinput->get("startDate");
		$endDate = $jinput->get("endDate");
		if (empty($startDate) && empty($endDate)) {
			$startDate = date('d-m-Y');
			$endDate = date('d-m-Y');
		}
		$this->days = JBusinessUtil::getAllDatesInInterval($startDate, $endDate);
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->selectedActivities = $this->get('SelectedActivities');
		$this->activities = $this->get('Activities');

		$layout = JFactory::getApplication()->input->get('layout');
		if (!empty($layout)) {
			$tpl = $layout;
			if ($layout == 'default') {
				$tpl = null;
			}
		}

		parent::display($tpl);
	}
}
