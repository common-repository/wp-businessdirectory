<?php
/**
 * Main view responsable for creating the extension menu structure and admin template
 *
 * @package    WBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');

//
JBusinessUtil::enqueueStyle('libraries/metis-menu/metisMenu.css');

require_once BD_HELPERS_PATH.'/helper.php';
require_once WP_BUSINESSDIRECTORY_PATH.'includes/mvc/document/Renderer/Html/MessageRenderer.php';
use MVC\Document\Renderer\Html\MessageRenderer;

class JBusinessDirectoryAdminView extends JViewLegacy{

	var $section_name="";
	var $section_description = "";

	function __construct($config = array()){
		parent::__construct($config);
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->section_name= JText::_("LNG_".strtoupper($this->_name));
		$this->section_description = JText::_("LNG_".strtoupper($this->_name)."_HEADER_DESCR");
	}

	/**
	 * Generate the main display for extension views
	 *
	 * @param unknown_type $tpl
	 */
    public function display($tpl = null)
    {
        $content = $this->loadTemplate($tpl);
        $document = JFactory::getDocument();
        
        if ($content instanceof Exception)
        {
            return $content;
        }

        $page="";
        if(isset($_REQUEST["page"])){
            $page = sanitize_text_field($_REQUEST["page"]);
        }

        $template = new stdClass();
        $template->content = $content;

        $input = JFactory::getApplication()->input;
        $hidemainmenu = $input->get('hidemainmenu');
        
        if(!$hidemainmenu){
            $template->menus = $this->generateMenu($page);
            $this->checkAccessRights($template->menus);
            $this->setActiveMenus($template->menus, $this->_name);
        }else{
           // $this->section_name = $document->getTitle();
        }

        //include the template and create the view
        $path = JPATH_COMPONENT_ADMINISTRATOR . '/theme/template.php';
        $templateFileExists = file_exists($path);

        $templateContent = $content;

        $render = new MessageRenderer($document);
        $messages = $render->render("WP-JBD");

        if($templateFileExists){
            ob_start();

            $scripts = JBusinessUtil::generateScriptDeclarations();
            echo $scripts;

            echo $messages;
            // Include the requested template filename in the local scope
            // (this will execute the view logic).
            include $path;

            // Done with the requested template; get the buffer and
            // clear it.
            $templateContent = ob_get_contents();
            ob_end_clean();
        }

        echo $templateContent;
    }

	/**
	 * Check for selected menu and set it active
	 *
	 */
    private function setActiveMenus(&$menus, $view){
        $app = JFactory::getApplication();
        $type="";
        if($view=="categories"){
            $type = $app->getUserStateFromRequest('com_jbusinessdirectory.categories'.'.filter.type', 'filter_type', CATEGORY_TYPE_BUSINESS);
        }
        if($view=="attributes"){
            $type = $app->getUserStateFromRequest('com_jbusinessdirectory.attributes'.'.filter.attribute_type', 'filter_attribute_type', ATTRIBUTE_TYPE_BUSINESS);
        }

        if(!empty($menus)){
            foreach($menus as &$menu){
                if($menu["view"] == $view){
                    $menu["active"] = true;
                }
                if(isset($menu["submenu"])){
                    foreach($menu["submenu"] as &$submenu){
                        if($submenu["view"] == $view){
                            if(preg_match('/type=/', $submenu["link"])) {
                                switch ($type) {
                                    case CATEGORY_TYPE_OFFER:
                                        $parent = 'offers';
                                        break;
                                    case CATEGORY_TYPE_EVENT:
                                        $parent = 'events';
                                        break;
                                    case CATEGORY_TYPE_CONFERENCE:
                                        $parent = 'sessions';
                                        break;
                                    case CATEGORY_TYPE_VIDEO:
                                        $parent = 'videos';
                                        break;
    
                                    default:
                                        $parent = 'companies';
                                }
                                if($menu["view"] == $parent){
                                    $menu["active"] = true;
                                    $submenu["active"] = true;
                                }
                            }
                            else {
                                $submenu["active"] = true;
                                $menu["active"] = true;
                            }
                        }
                    }
                }
            }
        }
    }

	/**
	 * Check the access rights for the menu items
	 * @param unknown_type $menus
	 */
    private function checkAccessRights(&$menus){
        $actions = JBusinessDirectoryHelper::getActions();
            
        if(empty($menus)){
            return array();
        }
        
        foreach($menus as $i=>$menu){
            if(!$actions->get(str_replace("_", ".",  $menu["access"]))){
                unset($menus[$i]);
                continue;
            }
            if(isset($menu["submenu"])){
                foreach($menu["submenu"] as $j=>&$submenu){
                    if(!$actions->get($submenu["access"])){
                        unset($menu["submenu"][$j]);
                        continue;
                    }
                }
            }
        }

        return $menus;
    }
	
	/**
	 * Build the menu items with all subments
	 *
	 */
    private function generateMenu($page){

        if(empty($page)){
            return null;
        }

        $menus = array();

        $submenu = array();

        switch ($page){
            case 'jbd_businesslistings':
                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
                    "access"=> "directory_access_listings",
                    "link" => "index.php?option=com_jbusinessdirectory&view=companies",
                    "icon" => "la la-building",
                    "view" => "companies");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_CATEGORIES'),
                    "access"=> "directory_access_listings",
                    "link" => "index.php?option=com_jbusinessdirectory&view=categories&filter_type=".CATEGORY_TYPE_BUSINESS,
                    "view" => "categories",
                    "icon" => "la la-folder-open");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ATTRIBUTES'),
                    "access"=> "directory_access_listings",
                    "link" => "index.php?option=com_jbusinessdirectory&view=attributes&filter_attribute_type=1",
                    "icon" => "la la-object-group",
                    "view" => "attributes");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_TYPES'),
                    "access"=> "directory_access_listings",
                    "link" => "index.php?option=com_jbusinessdirectory&view=companytypes",
                    "icon" => "la la-cubes",
                    "view" => "companytypes");
                $submenu[] = $smenuItem;

                if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/companypricelists.php')) {
                    $smenuItem  = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_PRICE_LIST'),
                        "access"=> "directory_access_listings",
                        "link" => "index.php?option=com_jbusinessdirectory&view=companypricelists",
                        "icon" => "la la-tags",
                        "view" => "companypricelists");
                    $submenu[] = $smenuItem;
                }
                
                if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/projects.php')) {
                    $smenuItem  = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_PROJECTS'),
                        "access"=> "directory_access_listings",
                        "link" => "index.php?option=com_jbusinessdirectory&view=projects",
                        "icon" => "la la-newspaper-o",
                        "view" => "projects");
                    $submenu[] = $smenuItem;
                }

                if ($this->appSettings->enable_announcements && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/announcements.php')) {
                    $smenuItem  = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ANNOUNCEMENTS'),
                        "access"=> "directory_access_announcements",
                        "link" => "index.php?option=com_jbusinessdirectory&view=announcements",
                        "icon" => "la la-bullhorn",
                        "view" => "announcements"
                    );
                    $submenu[] = $smenuItem;
                }

                if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/memberships.php')) {
                    $smenuItem  = array(
                        "title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MEMBERSHIP'),
                        "access"  => "directory_access_memberships",
                        "link" => "index.php?option=com_jbusinessdirectory&view=memberships",
                        "icon" => "la la-users",
                        "view"    => "memberships"
                    );

                    $submenu[] = $smenuItem;
                }

                $menus[$page] = $submenu;
                break;
            
            case 'jbd_appointments':
                if($this->appSettings->enable_services==1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/companyservice.php')) {
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICES'),
                        "access" => "directory_access_listings",
                        "link" => "index.php?option=com_jbusinessdirectory&view=companyservices",
                        "icon" => "la la-list",
                        "view" => "companyservices");
                    $submenu[] = $smenuItem;

                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICE_PROVIDERS'),
                        "access" => "directory_access_listings",
                        "link" => "index.php?option=com_jbusinessdirectory&view=companyserviceproviders",
                        "icon" => "la la-users",
                        "view" => "companyserviceproviders");
                    $submenu[] = $smenuItem;

                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICE_RESERVATIONS'),
                        "access" => "directory_access_listings",
                        "link" => "index.php?option=com_jbusinessdirectory&view=companyservicereservations",
                        "icon" => "la la-calendar",
                        "view" => "companyservicereservations");
                    $submenu[] = $smenuItem;
                }
                $menus[$page] = $submenu;
                break;

            case 'jbd_offers':
                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
                    "access"=> "directory_access_offers",
                    "link" => "index.php?option=com_jbusinessdirectory&view=offers",
                    "view" => "offers",
                    "icon" => "la la-certificate");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_CATEGORIES'),
                    "access"=> "directory_access_offers",
                    "link" => "index.php?option=com_jbusinessdirectory&view=categories&filter_type=".CATEGORY_TYPE_OFFER,
                    "view" => "categories",
                    "icon" => "la la-folder-open");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ATTRIBUTES'),
                    "access"=> "directory_access_listings",
                    "link" => "index.php?option=com_jbusinessdirectory&view=attributes&filter_attribute_type=2",
                    "icon" => "la la-object-group",
                    "view" => "attributes");
                $submenu[] = $smenuItem;
                
                if($this->appSettings->enable_offer_selling==1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/offerorder.php')) {
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_ORDERS'),
                        "access" => "directory_access_offers",
                        "link" => "index.php?option=com_jbusinessdirectory&view=offerorders",
                        "view" => "offerorders",
                        "icon" => "la la-shopping-cart");
                    $submenu[] = $smenuItem;
                }

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERCOUPONS'),
                    "access"=> "directory_access_offers",
                    "link" => "index.php?option=com_jbusinessdirectory&view=offercoupons",
                    "view" => "offercoupons",
                    "icon" => "la la-ticket");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_TYPES'),
                    "access"=> "directory_access_offers",
                    "link" => "index.php?option=com_jbusinessdirectory&view=offertypes",
                    "view" => "offertypes",
                    "icon" => "la la-cubes");
                $submenu[] = $smenuItem;

                $menus[$page] = $submenu;
                break;

            case 'jbd_events':
                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
                    "access"=> "directory_access_events",
                    "link" => "index.php?option=com_jbusinessdirectory&view=events",
                    "view" => "events",
                    "icon" => "la la-calendar");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_CATEGORIES'),
                    "access"=> "directory_access_events",
                    "link" => "index.php?option=com_jbusinessdirectory&view=categories&filter_type=".CATEGORY_TYPE_EVENT,
                    "view" => "categories",
                    "icon" => "la la-folder-open");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ATTRIBUTES'),
                    "access"=> "directory_access_listings",
                    "link" => "index.php?option=com_jbusinessdirectory&view=attributes&filter_attribute_type=3",
                    "icon" => "la la-object-group",
                    "view" => "attributes");
                $submenu[] = $smenuItem;
                
                if ($this->appSettings->enable_event_reservation && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/eventticket.php')) {
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_TICKETS'),
                        "access" => "directory_access_events",
                        "link" => "index.php?option=com_jbusinessdirectory&view=eventtickets&filter_event_id=0",
                        "view" => "eventtickets",
                        "icon" => "la la-ticket");
                    $submenu[] = $smenuItem;

                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_RESERVATIONS'),
                        "access" => "directory_access_events",
                        "link" => "index.php?option=com_jbusinessdirectory&view=eventreservations&filter_event_id=0",
                        "view" => "eventreservations",
                        "icon" => "la la-list-alt");
                    $submenu[] = $smenuItem;
                }

                if($this->appSettings->enable_event_appointments && file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/eventappointment.php')) {
                	$smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_APPOINTMENTS'),
                        "access" => "directory_access_events",
                        "link" => "index.php?option=com_jbusinessdirectory&view=eventappointments&filter_event_id=0",
                        "view" => "eventappointments",
                        "icon" => "la la-calendar-check-o");
                    $submenu[] = $smenuItem;
                }

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_TYPES'),
                    "access"=> "directory_access_events",
                    "link" => "index.php?option=com_jbusinessdirectory&view=eventtypes",
                    "view" => "eventtypes",
                    "icon" => "la la-cubes");
                $submenu[] = $smenuItem;

                $menus[$page] = $submenu;
                break;

            case 'jbd_quoterequests':
                if (JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS) && $this->appSettings->enable_request_quote_app) {
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REQUEST_QUOTES'),
                        "access" => "directory_access_request_quote",
                        "link" => "index.php?option=com_jbusinessdirectory&view=requestquotes",
                        "view" => "requestquotes",
                        "icon" => "la la-envelope-square");
                    $submenu[] = $smenuItem;
                 
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REQUEST_QUOTE_QUESTIONS'),
                        "access" => "directory_access_request_quote_questions",
                        "link" => "index.php?option=com_jbusinessdirectory&view=requestquotequestions",
                        "view" => "requestquotequestions",
                        "icon" => "la la-list-ol");
                    $submenu[] = $smenuItem;
                    
                    $menus[$page] = $submenu;
                }
                break;
            
            case 'jbd_trips':    
                if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) {
                    $submenu = array();
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIPS'),
                        "access" => "directory_access_trips",
                        "link" => "index.php?option=com_jbusinessdirectory&view=trips",
                        "view" => "trips",
                        "icon" => "la la-bus");
                    $submenu[] = $smenuItem;
                    
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIP_BOOKINGS'),
                        "access" => "directory_access_trips",
                        "link" => "index.php?option=com_jbusinessdirectory&view=tripbookings",
                        "view" => "tripbookings",
                        "icon" => "la la-calendar-check-o");
                    $submenu[] = $smenuItem;
                    
                    $menus[$page] = $submenu;
                }    
                break;

            case 'jbd_videos':       
                if (JBusinessUtil::isAppInstalled(JBD_APP_VIDEOS) && $this->appSettings->enable_videos) {
                    $submenu = array();
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_VIDEOS'),
                        "access" => "directory_access_videos",
                        "link" => "index.php?option=com_jbusinessdirectory&view=videos",
                        "view" => "videos",
                        "icon" => "la la-film");
                    $submenu[] = $smenuItem;
        
                    $smenuItem  = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ATTRIBUTES'),
                        "access"=> "directory_access_attributes",
                        "link" => "index.php?option=com_jbusinessdirectory&view=attributes&filter_attribute_type=4",
                        "icon" => "la la-object-group",
                        "view" => "attributes");
                    $submenu[] = $smenuItem;
        
                    $smenuItem  = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CATEGORIES'),
                        "access"=> "directory_access_categories",
                        "link" => "index.php?option=com_jbusinessdirectory&view=categories&filter_type=".CATEGORY_TYPE_VIDEO,
                        "view" => "categories",
                        "icon" => "la la-folder-open");
                    $submenu[] = $smenuItem;
        
                    $menus[$page] = $submenu;
                }
                break;
            case 'jbd_conferences':
                if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/conference.php')){
                    $submenu = array();
                    $smenuItem  = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CONFERENCES'),
                        "access"=> "directory_access_conferences",
                        "link" => "index.php?option=com_jbusinessdirectory&view=conferences",
                        "view" => "conferences",
                        "icon" => "la la-graduation-cap");
                    $submenu[] = $smenuItem;
                    $menus[$page] = $submenu;
                }
                break;
                case 'jbd_speakers':
                    if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/conference.php')){
                       
                        $submenu = array();
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SPEAKERS'),
                            "access"=> "directory_access_conferences",
                            "link" => "index.php?option=com_jbusinessdirectory&view=speakers",
                            "view" => "speakers",
                            "icon" => "la la-user");
                        $submenu[] = $smenuItem;
            
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SPEAKER_TYPES'),
                            "access"=> "directory_access_directory_management",
                            "link" => "index.php?option=com_jbusinessdirectory&view=speakertypes",
                            "view" => "speakertypes",
                            "icon" => "la la-cubes");
                        $submenu[] = $smenuItem;  
                        $menus[$page] = $submenu;
                    }
                    break;

                case 'jbd_sessions':
                    if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/conference.php')){
                        $submenu = array();
                        $menus[$page] = $submenu;
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSIONS'),
                            "access"=> "directory_access_conferences",
                            "link" => "index.php?option=com_jbusinessdirectory&view=sessions",
                            "view" => "sessions",
                            "icon" => "la la-language");
                        $submenu[] = $smenuItem;
            
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_CATEGORIES'),
                            "access"=> "directory_access_conferences",
                            "link" => "index.php?option=com_jbusinessdirectory&view=categories&filter_type=".CATEGORY_TYPE_CONFERENCE,
                            "view" => "categories",
                            "icon" => "la la-folder-open");
                        $submenu[] = $smenuItem;
            
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_TYPES'),
                            "access"=> "directory_access_directory_management",
                            "link" => "index.php?option=com_jbusinessdirectory&view=sessiontypes",
                            "view" => "sessiontypes",
                            "icon" => "la la-cubes");
                        $submenu[] = $smenuItem;
            
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_LOCATIONS'),
                            "access"=> "directory_access_directory_management",
                            "link" => "index.php?option=com_jbusinessdirectory&view=sessionlocations",
                            "view" => "sessionlocations",
                            "icon" => "la la-map-pin");
                        $submenu[] = $smenuItem;
            
                        $smenuItem  = array(
                            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSION_LEVELS'),
                            "access"=> "directory_access_directory_management",
                            "link" => "index.php?option=com_jbusinessdirectory&view=sessionlevels",
                            "view" => "sessionlevels",
                            "icon" => "la la-list-ol");
                        $submenu[] = $smenuItem;
    
                        $menus[$page] = $submenu;
                    }
                    break;
	        case 'jbd_campaigns':
		        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/campaigns.php')) {
			        $smenuItem = array(
				        "title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CAMPAIGNS'),
				        "access" => "directory_access_events",
				        "link"   => "index.php?option=com_jbusinessdirectory&view=campaigns",
				        "view"   => "campaigns",
				        "icon"   => "la la-envelope");
			        $submenu[] = $smenuItem;

			        $smenuItem = array(
				        "title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CAMPAIGN_PLANS'),
				        "access" => "directory_access_events",
				        "link"   => "index.php?option=com_jbusinessdirectory&view=campaignplans",
				        "view"   => "campaignplans",
				        "icon"   => "la la-tasks");
			        $submenu[] = $smenuItem;

			        $menus[$page] = $submenu;
		        }
		        break;

	        case 'jbd_shippingmethods':
		        if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/shippingmethods.php') && $this->appSettings->enable_shipping==1) {
		        	$smenuItem = array(
				        "title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SHIPPING_METHODS'),
				        "access" => "directory_access_events",
				        "link"   => "index.php?option=com_jbusinessdirectory&view=shippingmethods",
				        "view"   => "shippingmethods",
				        "icon"   => "la la-ship");
			        $submenu[] = $smenuItem;

			        $menus[$page] = $submenu;
		        }
		        break;

            case 'jbd_reviews':
                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW'),
                    "access"=> "directory_access_reviews",
                    "link" => "index.php?option=com_jbusinessdirectory&view=reviews",
                    "view" => "reviews",
                    "icon" => "la la-comment");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_QUESTIONS'),
                    "access"=> "directory_access_reviews",
                    "link" => "index.php?option=com_jbusinessdirectory&view=reviewquestions",
                    "view" => "reviewquestions",
                    "icon" => "la la-list");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_CRITERIAS'),
                    "access"=> "directory_access_reviews",
                    "link" => "index.php?option=com_jbusinessdirectory&view=reviewcriterias",
                    "view" => "reviewcriterias",
                    "icon" => "la la-pie-chart");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_RESPONSE'),
                    "access"=> "directory_access_reviews",
                    "link" => "index.php?option=com_jbusinessdirectory&view=reviewresponses",
                    "view" => "reviewresponses",
                    "icon" => "la la-comments");
                $submenu[] = $smenuItem;

                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_ABUSE'),
                    "access"=> "directory_access_reviews",
                    "link" => "index.php?option=com_jbusinessdirectory&view=reviewabuses",
                    "view" => "reviewabuses",
                    "icon" => "la la-sticky-note");
                $submenu[] = $smenuItem;

                $menus[$page] = $submenu;
                break;

        }

        if (isset($menus[$page])) {
            return $menus[$page];
        }

        return null;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PACKAGES'),
            "access"=> "directory_access_packages",
            "link" => "index.php?option=com_jbusinessdirectory&view=packages",
            "view" => "packages",
            "icon" => "la la-database");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_DISCOUNTS'),
            "access"=> "directory_access_discounts",
            "link" => "index.php?option=com_jbusinessdirectory&view=discounts",
            "view" => "discounts",
            "icon" => "la la-ticket");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ORDERS'),
            "access"=> "directory_access_directory_management",
            "link" => "index.php?option=com_jbusinessdirectory&view=orders",
            "view" => "orders",
            "icon" => "la la-shopping-cart");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TAXES'),
            "access"=> "directory_access_taxes",
            "link" => "index.php?option=com_jbusinessdirectory&view=taxes",
            "view" => "taxes",
            "icon" => "la la-pencil-square");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MEMBERSHIP'),
            "access"=> "directory_access_memberships",
            "link" => "index.php?option=com_jbusinessdirectory&view=memberships",
            "view" => "memberships",
            "icon" => "la la-group");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PAYMENT_PROCESSORS'),
            "access"=> "directory_access_paymentprocessors",
            "link" => "index.php?option=com_jbusinessdirectory&view=paymentprocessors",
            "view" => "paymentprocessors",
            "icon" => "la la-money");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SHIPPING_METHODS'),
            "access"=> "directory_access_paymentprocessors",
            "link" => "index.php?option=com_jbusinessdirectory&view=shippingmethods",
            "view" => "shippingmethods",
            "icon" => "la la-ship");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COUNTRIES'),
            "access"=> "directory_access_countries",
            "link" => "index.php?option=com_jbusinessdirectory&view=countries",
            "view" => "countries",
            "icon" => "la la-globe");
        $menus[] = $menuItem;

        if ($this->appSettings->limit_cities_regions == 1) {
            $menuItem  = array(
                "title" => JText::_('LNG_MANAGE_CITIES'),
                "access"=> "directory_access_cities",
                "link" => "index.php?option=com_jbusinessdirectory&view=cities",
                "view" => "cities",
                "icon" => "la la-cog");
            $menus[] = $menuItem;
        }

        if ($this->appSettings->limit_cities_regions == 1) {
            $menuItem  = array(
                "title" => JText::_('LNG_MANAGE_REGIONS'),
                "access"=> "directory_access_regions",
                "link" => "index.php?option=com_jbusinessdirectory&view=regions",
                "view" => "regions",
                "icon" => "la la-cog");
            $menus[] = $menuItem;
        }

        $smenuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_RATING'),
            "access"=> "directory_access_reviews",
            "link" => "index.php?option=com_jbusinessdirectory&view=ratings",
            "view" => "ratings",
            "icon" => "la la-cog");
        //$submenu[] = $smenuItem;


        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MARKETING'),
            "access"=> "directory_access_marketing",
            "link" => "index.php?option=com_jbusinessdirectory&view=marketing",
            "view" => "marketing",
            "icon" => "la la-bullhorn");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REPORTS'),
            "access"=> "directory_access_reports",
            "link" => "index.php?option=com_jbusinessdirectory&view=reports",
            "view" => "reports",
            "icon" => "la la-bar-chart");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EMAILS_TEMPLATES'),
            "access"=> "directory_access_emails",
            "link" => "index.php?option=com_jbusinessdirectory&view=emailtemplates",
            "view" => "emailtemplates",
            "icon" => "la la-envelope");
        $menus[] = $menuItem;

        $menuItem  = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_STATISTICS'),
			"access"=> "directory_access_statistics",
			"link" => "index.php?option=com_jbusinessdirectory&view=statistics",
			"view" => "statistics",
			"icon" => "la la-pie-chart");
		$menus[] = $menuItem;

        return $menus;
    }

	public function setSectionDetails($name, $description){
		$this->section_name = $name;
		$this->section_description = $description;
	}
}