<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerStatistics extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}

	public function increaseCountAjax() {
		$model = $this->getModel('statistics');
		
		$input = JFactory::getApplication()->input;
		$itemId = $input->getInt("item_id");
		$itemType = $input->get('item_type');
		$statType = $input->get('stat_type');

		$model->increaseCount($itemId, $itemType, $statType);
		
		JFactory::getApplication()->close();
	}
}
