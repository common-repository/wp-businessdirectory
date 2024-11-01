<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS . 'components' . DS . 'com_jbusinessdirectory' . DS . 'tables');
require_once BD_HELPERS_PATH . '/category_lib.php';

class JBusinessDirectoryModelSuggestions extends JModelList
{

    public $dataModel = null;
    public $type = null;

    public function __construct()
    {
        parent::__construct();
        $jinput = JFactory::getApplication()->input;

        $jinput->set("layout", "ready");
        $user = JBusinessUtil::getUser();
        $userProfile = JUserHelper::getProfile($user->ID);

        $this->type = $jinput->getInt("type", 1);
        $this->setState('type', $this->type);
        switch ($this->type) {
            case 1:
                if (isset($userProfile->profile["listing-categories"])) {
                    $categories = $userProfile->profile["listing-categories"];
                    if (!empty($categories)) {
                        $categories = json_decode($categories);
                        $categories = implode(";", $categories);

                        $jinput->set("categories", $categories);
                        $this->dataModel = JModelLegacy::getInstance('Search', 'JBusinessDirectoryModel', array('ignore_request' => true));
                    }
                }
                break;
            case 2:
                if (isset($userProfile->profile["offer-categories"])) {
                    $categories = $userProfile->profile["offer-categories"];
                    
                    if (!empty($categories)) {
                        $categories = json_decode($categories);
                        $categories = implode(";", $categories);

                        $jinput->set("categories", $categories);
                        $this->dataModel = JModelLegacy::getInstance('Offers', 'JBusinessDirectoryModel', array('ignore_request' => true));
                    }
                }
                break;
            case 3:
                if (isset($userProfile->profile["event-categories"])) {
                    $categories = $userProfile->profile["event-categories"];
                    if (!empty($categories)) {
                        $categories = json_decode($categories);
                        $categories = implode(";", $categories);

                        $jinput->set("categories", $categories);
                        $this->dataModel = JModelLegacy::getInstance('Events', 'JBusinessDirectoryModel', array('ignore_request' => true));
                    }
                }
                break;
            case 4:
                if (isset($userProfile->profile["conference-categories"])) {
                    $categories = $userProfile->profile["conference-categories"];

                    if (!empty($categories)) {
                        $categories = json_decode($categories);
                        $categories = implode(";", $categories);
                    }

                    $jinput->set("categories", $categories);
                    $this->dataModel = JModelLegacy::getInstance('Conferences', 'JBusinessDirectoryModel', array('ignore_request' => true));
                }
                break;
        }

        $this->appSettings = JBusinessUtil::getApplicationSettings();
        $this->appSettings->search_type = 1;
        $this->appSettings->offer_search_type = 1;
        $this->appSettings->event_search_type = 1;
        $this->appSettings->conference_search_type = 1;

        $app = JFactory::getApplication();
        // Get pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $this->appSettings->dir_list_limit, 'int');
        $limitstart = $app->input->getInt('limitstart', 0, 'uint');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    /*
     * Retrieve the current items
     *
     */
    public function getItems()
    {

        if (!empty($this->dataModel)) {
            return $this->dataModel->getItems();
        }

        return null;
    }

    /**
     * Get the total items
     *
     * @return void
     */
    public function getTotalItems()
    {
        // Load the content if it doesn't already exist

        if (empty($this->dataModel)) {
            return 0;
        }

        if (empty($this->_total)) {
            return $this->dataModel->getTotal();
        }
        return $this->_total;
    }

    public function getType()
    {
        $table = $this->getTable("EventType", "JTable");
        $item = $table->getEventType($this->typeSearch);

        return $item;
    }

    public function getPagination()
    {
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            require_once BD_HELPERS_PATH . '/dirpagination.php';
            $this->_pagination = new JBusinessDirectoryPagination($this->getTotalItems(), $this->getState('limitstart'), $this->getState('limit'));
            $this->_pagination->setAdditionalUrlParam('controller', 'suggestions');

            $this->_pagination->setAdditionalUrlParam('type', $this->type);
            $this->_pagination->setAdditionalUrlParam('view', 'suggestions');
        }
        return $this->_pagination;
    }

}
