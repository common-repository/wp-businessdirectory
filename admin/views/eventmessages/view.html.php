<?php
/**
 * @package    WBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML Menus Menu Menus View.
 *
 * @package    WBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryViewEventMessages extends JBusinessDirectoryAdminView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $searchType;

    public function display($tpl =  null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->searchType = $this->get('SearchTypes');

        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        JBusinessDirectoryHelper::addSubmenu('eventmessages');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors),'error');
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */

    protected function addToolBar()
    {
        $canDo = JBusinessDirectoryHelper::getActions();
        $user = JBusinessUtil::getUser();

        JToolBarHelper::title('J-BusinessDirectory : '.JText::_('LNG_EVENT_MESSAGES'), 'generic.php');

        if($canDo->get('core.delete'))
        {
            JToolbarHelper::divider();
            JToolbarHelper::deleteList('','eventmessages.delete');
        }

        if ($canDo->get('core.admin'))
        {
            JToolbarHelper::preferences('com_jbusinessdirectory');
        }

        JToolbarHelper::divider();
        JToolBarHelper::custom( 'eventmessages.back', 'dashboard', 'dashboard', JText::_("LNG_CONTROL_PANEL"), false, false );
    }
}