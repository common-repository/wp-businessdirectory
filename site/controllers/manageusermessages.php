<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'messages.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'messages.php');

class JBusinessDirectoryControllerManageUserMessages extends JBusinessDirectoryControllerMessages {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}


    public function changeSMSStatus() {
        //dump("erdhi");exit;
        // Get items to publish from the request.
        $id = $this->input->get('id');
        $oldStatus = $this->input->get('currentStatus');

        if ($oldStatus == '0') {
            $newStatus = 1;
        } else {
            $newStatus = 0;
        }

        $model = $this->getModel('ManageMessage');

        if (!$model->changeStatus($id, $newStatus)) {
            $this->setMessage(JText::_('LNG_ERROR_CHANGE_STATUS'), 'warning');
        } else {
            $ntext = "COM_JBUSINESSDIRECTORY_N_ITEMS_CHANGED";
            if ($ntext !== null) {
                $this->setMessage(\JText::plural($ntext, count(array($id))));
            }
        }

        $this->setRedirect('index.php?option=com_jbusinessdirectory&view=manageusermessages');
    }

    /**
     * Removes an item
     */
    public function delete() {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('id');

        if (empty($cid)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_MESSAGES_SELECTED'), 'error');
        } else {
            // Get the model.
            $model = $this->getModel("ManageMessages");

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            ArrayHelper::toInteger($cid);

            // Remove the items.
            if (!$model->delete($cid)) {
                $this->setMessage($model->getError());
            } elseif (!empty($model->getErrors())){
                $implodeErrors = implode('<br />', $model->getErrors());
                $this->setMessage(JText::sprintf( 'COM_JBUSINESSDIRECTORY_DELETED_WARNING',$implodeErrors),'Warning');
            } else {
                $this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_MESSAGES_DELETED', count($cid)));
            }
        }

        $this->setRedirect('index.php?option=com_jbusinessdirectory&view=manageusermessages');
    }
}
