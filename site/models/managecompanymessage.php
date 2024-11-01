<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'companymessage.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'companymessage.php');

class JBusinessDirectoryModelManageCompanyMessages extends JBusinessDirectoryModelCompanyMessage {

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
}
?>