<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * The Bookmark Controller
 *
 */
class JBusinessDirectoryControllerBookmark  extends JControllerLegacy {

    public function __construct() {
        parent::__construct();
        $this->appSettings = JBusinessUtil::getApplicationSettings();
    }

    /**
     * Add bookmark
     *
     */
    public function addBookmarkAjax() {
        // Check for request forgeries.
        // JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('bookmark');
        $data = JFactory::getApplication()->input->get->getArray();
        $data['note'] = htmlentities(JFactory::getApplication()->input->get('note', '', 'RAW'), ENT_QUOTES);
        $data['user_id'] = JBusinessUtil::getUser()->ID;
        $result = $model->addBookmark($data);

        $response= array();
        if ($result) {
            $response["response_message"] =JText::sprintf('COM_JBUSINESS_BOOKMARK_ADDED', '<a href="'.JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks').'">'.JText::_('LNG_HERE').'</a>');
        } else {
            $response["response_message"] =JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED');
        }

        $status = $result?AJAX_RESPONSE_SUCCESS:AJAX_RESPONSE_FAILURE;
        JBusinessUtil::sendJsonResponse($response, $status);

    }

    /**
     * Update Bookmark
     *
     */
    public function updateBookmarkAjax() {
        // Check for request forgeries.
        //JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = JFactory::getApplication()->input->get->getArray();
        $note = htmlentities(JFactory::getApplication()->input->get('note', '', 'RAW'), ENT_QUOTES);
        $data['note'] = $note;

        $model = $this->getModel('bookmark');
        $result = $model->updateBookmark($data);

        $response= array();
        if ($result) {
            $response["response_message"] = JText::_('COM_JBUSINESS_BOOKMARK_UPDATED');
        } else {
            $response["response_message"] = JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED');
        }

        $status = $result?AJAX_RESPONSE_SUCCESS:AJAX_RESPONSE_FAILURE;
        JBusinessUtil::sendJsonResponse($response, $status);

    }

    /**
     * Remove bookmark
     *
     * @throws Exception
     *
     */
    public function removeBookmarkAjax() {
        // Check for request forgeries.
        $data = JFactory::getApplication()->input->get->getArray();
        $note = htmlentities(JFactory::getApplication()->input->get('note', '', 'RAW'), ENT_QUOTES);
        $data['note'] = $note;

        $model = $this->getModel('bookmark');
        $result = $model->removeBookmark($data);

        $response= array();
        if ($result) {
            $response["response_message"] = JText::_('COM_JBUSINESS_BOOKMARK_REMVED');
        } else {
            $response["response_message"] = JText::_('JLIB_APPLICATION_ERROR_SAVE_FAILED');
        }

        $status = $result?AJAX_RESPONSE_SUCCESS:AJAX_RESPONSE_FAILURE;
        JBusinessUtil::sendJsonResponse($response, $status);

    }

    /**
     * Retrieve bookmark for an item
     *
     */
    public function getBookmarkAjax(){
        // Check for request forgeries.
        $data = JFactory::getApplication()->input->get->getArray();

        $model = $this->getModel('bookmark');
        $result = $model->getBookmark($data);

        $response= array();
        $response["bookmark"] = $result;

        $status = $result?AJAX_RESPONSE_SUCCESS:AJAX_RESPONSE_FAILURE;
        JBusinessUtil::sendJsonResponse($response, $status);

    }

}

