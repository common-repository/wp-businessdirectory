<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
use MVC\Utilities\ArrayHelper;
class JBusinessDirectoryControllerEventMessages extends JControllerAdmin {

    /**
     * Display the view
     *
     * @param   boolean			If true, the view output will be cached
     * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController		This object to support chaining.
     * @since   1.6
     */
    public function display($cachable = false, $urlparms = false){
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.6
     */
    public function getModel($name = 'EventMessages', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)){
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function back(){
        $this->setRedirect('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory');
    }

    /**
     * Removes an item
     */
    public function delete(){
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = $this->input->post->get('cid', array(), '', 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_EVENT_MESSAGES_SELECTED'),'error');
        }
        else
        {
            // Get the model.
            $model = $this->getModel("EventMessage");

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            ArrayHelper::toInteger($cid);

            // Remove the items.
            if (!$model->delete($cid))
            {
                $this->setMessage($model->getError());
            } else {
                $this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_EVENT_MESSAGES_DELETED', count($cid)));
            }
        }

        $this->setRedirect('index.php?option=com_jbusinessdirectory&view=eventmessages');
    }
}