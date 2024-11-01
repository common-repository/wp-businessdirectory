<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'companymessages.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'companymessages.php');

class JBusinessDirectoryControllerManageCompanyMessages extends JBusinessDirectoryControllerCompanyMessages
{
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */

    function __construct()
    {
        parent::__construct();
        $this->log = Logger::getInstance();
    }

    
    /**
     * Removes an item
     */
    public function delete(){
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('id', array(), '', 'array');
        
        if (!is_array($cid) || count($cid) < 1)
        {
            JError::raiseWarning(500, JText::_('COM_JBUSINESSDIRECTORY_NO_COMPANY_MESSAGES_SELECTED'));
        }
        else
        {
            // Get the model.
            $model = $this->getModel("ManageCompanyMessages");
            
            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            ArrayHelper::toInteger($cid);
            
            // Remove the items.
            if (!$model->delete($cid))
            {
                $this->setMessage($model->getError());
            } else {
                $this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_COMPANY_MESSAGES_DELETED', count($cid)));
            }
        }
        
        $this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanymessages');
    }
}