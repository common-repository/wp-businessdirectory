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

class JBusinessDirectoryModelStatistics extends JModelLegacy{

    public function __construct(){
        parent::__construct();
    }

    /**
     * Returns a Table object, always creating it
     *
     * @param   type	The table type to instantiate
     * @param   string	A prefix for the table class name. Optional.
     * @param   array  Configuration array for model. Optional.
     * @return  JTable	A database object
     */
    public function getTable($type = 'Statistics', $prefix = 'JTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    
    /**
	 * Increase the website access number when clicked
	 *
	 * @param int $companyId
	 * @return mixed
	 */
	public function increaseCount($itemId, $itemType, $statType) {
		
		// prepare the array with the table fields
		$data = array();
		$data["id"] = 0;
		$data["item_id"] = $itemId;
		$data["item_type"] = $itemType;
		$data["date"] = JBusinessUtil::convertToMysqlFormat(date('Y-m-d')); //current date
		$data["type"] = $statType;		

        $statisticsTable = $this->getTable("Statistics", "JTable");
		if (!$statisticsTable->save($data)) {
			return false;
		}

		return true;
	}
}