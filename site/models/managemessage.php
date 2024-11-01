<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'message.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'message.php');

class JBusinessDirectoryModelManageMessage extends JBusinessDirectoryModelMessage {

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Return array with all companies based on some letter it may include
	 * @return array
	 */
	public function readMessage() {
		$companyTable = $this->getTable();
		$id = JFactory::getApplication()->input->get('id', null);

		$result = $companyTable->readMessage($id);

		return $result;
	}

	public function changeStatus($id, $value) {
		$table = $this->getTable();
		return $table->changeStatus($id, $value);
	}
}
