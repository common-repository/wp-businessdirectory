<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'companypricelists.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'companypricelists.php');
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryControllerManageCompanyPriceLists extends JBusinessDirectoryControllerCompanyPriceLists {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}

	/**
	 * Delete an event or the associated reccuring events.
	 */
	public function deletePriceList() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the model.
		$model = $this->getModel("ManageCompanyPriceList");
		$this->delete($model);

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view='.$this->input->get('view'));
	}
}
