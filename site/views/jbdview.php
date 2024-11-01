<?php
/**
 * Main view responsable for creating the extension menu structure and admin template
 *
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Document\Renderer\Html\MessageRenderer;

JBusinessUtil::enqueueStyle('libraries/metis-menu/metisMenu.css');
JBusinessUtil::enqueueScript('libraries/metis-menu/metisMenu.js');
JBusinessUtil::enqueueStyle('css/jbd-template.css');

require_once BD_HELPERS_PATH.'/helper.php';

class JBusinessDirectoryFrontEndView extends JViewLegacy {
	public $section_name="";
	public $section_description = "";
	public $userDashboard = false;
	public $jbdTemplate = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$type = JFactory::getApplication()->input->get('filter_type');
		if ($type == OFFER_TYPE_PRODUCT && $this->_name == 'managecompanyoffers') {
			$this->_name = "products";
		}
		if ($type == OFFER_TYPE_PRODUCT && $this->_name == 'managecompanyofferorders') {
			$this->_name = "productsorders";
		}
		$this->section_name= JText::_("LNG_".strtoupper($this->_name));
		$this->section_description = JText::_("LNG_".strtoupper($this->_name)."_HEADER_DESCR");

		//show the upgrade banner
		if($this->appSettings->package_upgrade_banner){
			$user = JBusinessUtil::getUser();
			$this->upgradeListingId = JBusinessUtil::getUpgradeListingID($user->ID);
		}
	}

	/**
	 * Utility function for calling the parent display
	 * @param unknown $tpl
	 */
	public function displayParent($tpl = null) {
		parent::display($tpl);
	}
	
	
	/**
	 * Generate the main display for extension views
	 *
	 * @param unknown_type $tpl
	 */
	public function display($tpl = null) {

		$content = $this->loadTemplate($tpl);
        $document = JFactory::getDocument();

		if ($content instanceof Exception) {
			return $content;
		}
		
		$page="";
        if(isset($_REQUEST["page"])){
            $page = sanitize_text_field($_REQUEST["page"]);
        }

		$template = new stdClass();
		$template->content = $content;

		$input = JFactory::getApplication()->input;
		
		
		if ($this->userDashboard) {
			$template->menus = $this->generateUserMenu();
		} else {
			$template->menus = $this->generateMenu();
		}
		if ($this->appSettings->front_end_acl) {
			$template->menus = $this->checkAccessRights($template->menus);
		}
		$this->setActiveMenus($template->menus, $this->_name);
		
		
		$path = "";
		if(!$this->userDashboard){
		    // style for business CP
			if($this->appSettings->business_cp_style == 2){
                $path = JPATH_COMPONENT_SITE.'/theme/tpl_style_2.php';
            }else if($this->appSettings->business_cp_style == 3){
                $path = JPATH_COMPONENT_SITE.'/theme/tpl_style_3.php';
            }else{
				$path = JPATH_COMPONENT_SITE.'/theme/tpl_style_1.php';
			}

		}else{
            // style for user CP
			if($this->appSettings->user_cp_style == 2){
                $path = JPATH_COMPONENT_SITE.'/theme/tpl_style_2.php';
            }else if($this->appSettings->user_cp_style == 3){
                $path = JPATH_COMPONENT_SITE.'/theme/tpl_style_3.php';
            }else{
				$path = JPATH_COMPONENT_SITE.'/theme/tpl_style_1.php';
			}
		}
		
		$this->jbdTemplate = $template;
		
		$templateContent = $content;

		$templateFileExists = file_exists($path);

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
		
		$this->_output = $templateContent;
		add_filter('template_redirect',array($this,'createPage'), 10 , 2 );
        
        add_filter( 'page_template', function(){
            $page_template = WP_BUSINESSDIRECTORY_PATH . '/templates/template.php';
            require $page_template;
            die;
        });
	}

	/**
	 * Check for selected menu and set it active
	 *
	 */
	private function setActiveMenus(&$menus, $view) {
		$app = JFactory::getApplication();
		$type = '';
		if ($view=="managecompanyoffers") {
			$type = $app->getUserStateFromRequest('com_jbusinessdirectory.managecompanyoffers'.'.filter.type', 'filter_type', OFFER_TYPE_OFFER);
		}
		if ($view=="managecompanyofferorders") {
			$type = $app->getUserStateFromRequest('com_jbusinessdirectory.managecompanyofferorders'.'.filter.type', 'filter_type', OFFER_TYPE_OFFER);
		}

		if ($view == "suggestions"){
			$type = $app->input->getInt("type");
		}
		
		foreach ($menus as &$menu) {
			if ($view == "managecompanyoffers" && $type == OFFER_TYPE_PRODUCT && $menu['view'] == 'products') {
				$menu["active"] = true;
				break;
			}else if ($view == "suggestions" && $menu['view'] == 'suggestions') {
				if(strpos($menu["link"],"type=".$type)!==false){
					$menu["active"] = true;
					break;
				}
			}else if ($menu["view"] == $view) {
				$menu["active"] = true;
			}
			
			if (isset($menu["submenu"])) {
				foreach ($menu["submenu"] as &$submenu) {
					if ($view == "managecompanyofferorders" && $type == OFFER_TYPE_PRODUCT && $submenu['view'] == 'productsorders') {
						$menu["active"] = true;
						$submenu["active"] = true;
					}else if ($submenu["view"] == $view) {
						$submenu["active"] = true;
						$menu["active"] = true;
					}
				}
			}
		}
	}

	/**
	 * Rietrieve the sub items for active menu item
	 *
	 * @param [type] $menus
	 * @return void
	 */
	private function getActiveMenuItems($menus){
		
		if(empty($menus)){
			return null;
		}

		foreach ($menus as &$menu) {
			if ( !empty($menu["active"]) && $menu["active"] && isset($menu["submenu"])) {
				return $menu["submenu"];
			}
		}

		return null;
	}

	/**
	 * Check the access rights for the menu items
	 * @param unknown_type $menus
	 */
	private function checkAccessRights($menus) {
		$actions = JBusinessDirectoryHelper::getActions();

		foreach ($menus as $i=>$menu) {
			if (!$actions->get($menu["access"])) {
				unset($menus[$i]);
				continue;
			}
			
			if (isset($menu["submenu"])) {
				foreach ($menu["submenu"] as $j=>$submenu) {
					if (!$actions->get($submenu["access"])) {
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
	private function generateMenu() {
		$actions = JBusinessDirectoryHelper::getActions();
		$menus = array();

		$menuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_DASHBOARD'),
			"access" => "directory.access.directory.management",
			"link" => "index.php?option=com_jbusinessdirectory&view=useroptions",
			"view" => "useroptions",
			"icon" => "la la-th-large");
		$menus[] = $menuItem;

        $menuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
			"access" => "directory.access.listings",
			"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'),
			"view" => "managecompanies",
			"icon" => "la la-building");
		
			$submenu = array();
		$smenuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_LISTINGS'),
			"access" => "directory.access.listings",
			"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'),
			"view" => "managecompanies");
		$submenu[] = $smenuItem;

		if ($this->appSettings->enable_projects) {
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_PROJECTS'),
				"access" => "directory.access.projects",
				"link" => "index.php?option=com_jbusinessdirectory&view=managecompanyprojects",
				"view" => "managecompanyprojects");
			$submenu[] = $smenuItem;
		}

		if ($this->appSettings->enable_price_list) {
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_PRICE_LIST'),
				"access" => "directory.access.listing.pricelist",
				"link" => "index.php?option=com_jbusinessdirectory&view=managecompanypricelists",
				"view" => "managecompanypricelists");
			$submenu[] = $smenuItem;
		}

		if ($this->appSettings->enable_announcements) {
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ANNOUNCEMENTS'),
				"access" => "directory.access.announcements",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyannouncements'),
				"view" => "managecompanyannouncements",
				"icon" => "la la-bullhorn");
			$submenu[] = $smenuItem;
		}

		if ($this->appSettings->enable_linked_listings) {
			$smenuItem  = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_LISTING_REGISTERED'),
				"access"=> "directory.access.listing.registrations",
				"link" => "index.php?option=com_jbusinessdirectory&view=managelistingregistrations",
				"view" => "managelistingregistrations",
				"icon" => "la la-user");
			$submenu[] = $smenuItem;		
		}

		if (file_exists(JPATH_SITE . '/plugins/content/business/business.php')) {
			if ($this->appSettings->enable_articles) {
				$smenuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_ARTICLES'),
					"access" => "directory.access.articles",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyarticles'),
					"icon" => "la la-newspaper-o",
					"view" => "managecompanyarticles");
				$submenu[] = $smenuItem;
			}
		}

		$menuItem["submenu"] = $submenu;
		$menus[] = $menuItem;


		if ($this->appSettings->enable_services == 1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/companyservice.php')) {
		
			$menuItem  = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_APPOINTMENTS'),
				"access"=> "directory.access.listing.services",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyservices'),
				"view" => "companyservices",
				"icon" => "la la-list");

			$submenu = array();
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICES'),
				"access" => "directory.access.listing.services",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyservices'),
				"view" => "managecompanyservices");
			$submenu[] = $smenuItem;

			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICE_PROVIDERS'),
				"access" => "directory.access.listing.providers",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyserviceproviders'),
				"view" => "managecompanyserviceproviders");
			$submenu[] = $smenuItem;

			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICE_RESERVATIONS'),
				"access" => "directory.access.listing.service.reservation",
				"link" => "index.php?option=com_jbusinessdirectory&view=managecompanyservicereservations",
				"view" => "managecompanyservicereservations");
			$submenu[] = $smenuItem;

            $smenuItem = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BUSINESS_BOOKINGS_CALENDAR'),
                "access" => "directory.access.listing.service.reservation",
                "link" => "index.php?option=com_jbusinessdirectory&view=businessbookingscalendar",
                "view" => "businessbookingscalendar");
            $submenu[] = $smenuItem;

			$menuItem["submenu"] = $submenu;
			$menus[] = $menuItem;
			
		}

		if ($this->appSettings->enable_offers && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/offers.php')) {
			$submenu = array();
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
				"access" => "directory.access.offers",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers&filter_type='.OFFER_TYPE_OFFER),
				"view" => "managecompanyoffers",
				"icon" => "la la-archive");

			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS_ITEMS'),
				"access" => "directory.access.offers",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers&filter_type='.OFFER_TYPE_OFFER),
				"view" => "managecompanyoffers");
			$submenu[] = $smenuItem;


			if ($this->appSettings->enable_offer_selling && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/offerorder.php')) {
				$smenuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_ORDERS'),
					"access" => "directory.access.offer.orders",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyofferorders&filter_type='.OFFER_TYPE_OFFER),
					"view" => "managecompanyofferorders",
					"icon" => "la la-cog");
				$submenu[] = $smenuItem;

				if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php') && $this->appSettings->enable_shipping) {
					$smenuItem = array(
						"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SHIPPING_METHODS'),
						"access" => "",
						"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=manageshippingmethods'),
						"view" => "manageshippingmethods",
						"icon" => "la la-shipping-fast");
					$submenu[] = $smenuItem;
				}
			}

			if ($this->appSettings->enable_offer_coupons) {
				$smenuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERCOUPONS'),
					"access" => "directory.access.offercoupons",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffercoupons'),
					"view" => "managecompanyoffercoupons",
					"icon" => "la la-ticket");
				$submenu[] = $smenuItem;
			}

			$menuItem["submenu"] = $submenu;
			$menus[] = $menuItem;
		}
		
		if ($this->appSettings->enable_events && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/events.php')) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
				"access" => "directory.access.events",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents'),
				"view" => "managecompanyevents",
				"icon" => "la la-calendar");

			$submenu = array();
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS_ITEMS'),
				"access" => "directory.access.events",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents'),
				"view" => "managecompanyevents");
			$submenu[] = $smenuItem;

			if ($this->appSettings->enable_event_reservation && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventticket.php')) {
				$smenuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_TICKETS'),
					"access" => "directory.access.eventtickets",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyeventtickets'),
					"view" => "managecompanyeventtickets");
				$submenu[] = $smenuItem;

				$smenuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_RESERVATIONS'),
					"access" => "directory.access.eventreservations",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyeventreservations'),
					"view" => "managecompanyeventreservations");
				$submenu[] = $smenuItem;
			}

			if ($this->appSettings->enable_event_appointments && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventappointment.php')) {
				$smenuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_APPOINTMENTS'),
					"access" => "directory.access.eventappointments",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyeventappointments'),
					"view" => "managecompanyeventappointments");
				$submenu[] = $smenuItem;
			}

			$menuItem["submenu"] = $submenu;

			$menus[] = $menuItem;
		}
		
		if ($this->appSettings->show_contact_form) {

            $user = JBusinessUtil::getUser();
            $nrMessages = JBusinessUtil::getTotalUserMessages($user->ID, true);

			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MESSAGES'),
				"access" => "directory.access.messages",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managemessages'),
				"view" => "managemessages",
				"icon" => "la la-comments",
                "nrMessages" => "$nrMessages",
				"display-unread-message" => true);
			$menus[] = $menuItem;
		}

		if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIPS'),
				"access" => "directory.access.trips",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managetrips'),
				"view" => "managetrips",
				"icon" => "la la-bus");
			
				$submenu = array();
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIPS'),
				"access" => "directory.access.trips",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managetrips'),
				"view" => "managetrips");
			$submenu[] = $smenuItem;

			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIP_BOOKINGS'),
				"access" => "directory.access.trips",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managetripbookings'),
				"view" => "managetripbookings");
			$submenu[] = $smenuItem;

			$menuItem["submenu"] = $submenu;
			$menus[] = $menuItem;
		}

		
		if (JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS) && $this->appSettings->enable_request_quote_app) {
            $user = JBusinessUtil::getUser();
            $nrQuotes = JBusinessUtil::getUnreadUserQuotes($user->ID);
		    $menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REQUEST_QUOTES'),
				"access" => "directory.access.request.quote",
				"link" => "index.php?option=com_jbusinessdirectory&view=managelistingrequestquotes",
				"view" => "managelistingrequestquotes",
				"icon" => "la la-envelope-square",
                "nrQuotes" => "$nrQuotes",
                "display-unread-quote" => true);
			$menus[] = $menuItem;
		}

		if (JBusinessUtil::isAppInstalled(JBD_APP_CAMPAIGNS)) {
			if ($this->appSettings->enable_campaigns) {
				$menuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CAMPAIGNS'),
					"access" => "directory.access.listings",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecampaigns&layout=edit'),
					"view" => "managecampaigns",
					"icon" => "la la-envelope");
				$menus[] = $menuItem;
			}
		}


		if ($this->appSettings->enable_packages) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BILLING'),
				"access" => "directory.access.orders",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=billingoverview'),
				"view" => "billingoverview",
				"icon" => "la la-cog");
			$submenu = array();
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BILLING_OVERVIEW'),
				"access" => "directory.access.orders",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=billingoverview'),
				"view" => "billingoverview",
				"icon" => "la la-cog");
			$submenu[] = $smenuItem;

			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BILLING_DETAILS'),
				"access" => "directory.access.listings",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit'),
				"view" => "billingdetails",
				"icon" => "la la-list-alt");
			$submenu[] = $smenuItem;

			$smenuItem  = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SUBSCRIPTIONS'),
				"access"=> "directory.access.orders",
				"link" => "index.php?option=com_jbusinessdirectory&view=managesubscriptions",
				"view" => "managesubscriptions");
			$submenu[] = $smenuItem;

			$menuItem["submenu"] = $submenu;
			$menus[] = $menuItem;
		}

		if (JBusinessUtil::canAssignPaymentProcessor(true)) {
			$menuItem = array(
				"title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PAYMENT_PROCESSORS'),
				"access" => "directory.access.payment.config",
				"link"   => JRoute::_('index.php?option=com_jbusinessdirectory&view=managepaymentprocessors'),
				"view"   => "managepaymentprocessors",
				"icon"   => "la la-credit-card");
			$menus[]  = $menuItem;
		}

		/*
		$submenu = array();
		$menuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PRODUCTS'),
			"access" => "directory.access.products",
			"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers&filter_type=' . OFFER_TYPE_PRODUCT),
			"view" => "products",
			"icon" => "la la-gift");

		$smenuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PRODUCTS'),
			"access" => "directory.access.products",
			"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers&filter_type=' . OFFER_TYPE_PRODUCT),
			"view" => "products",
			"icon" => "la la-gift");
		$submenu[] = $smenuItem;

		$smenuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PRODUCT_ORDERS'),
			"access" => "directory.access.products",
			"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyofferorders&filter_type=' . OFFER_TYPE_PRODUCT),
			"view" => "productsorders",
			"icon" => "la la-cog");
		$submenu[] = $smenuItem;

		$menuItem["submenu"] = $submenu;
		$menus[] = $menuItem;*/

		if ($this->appSettings->enable_reviews) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEWS'),
				"access" => "directory.access.reviews",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managereviews'),
				"view" => "managereviews",
				"icon" => "la la-comment");
			$menus[] = $menuItem;
		}
	
		if ($this->appSettings->enable_bookmarks) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BOOKMARKS'),
				"access" => "directory.access.bookmarks",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks'),
				"view" => "managebookmarks",
				"icon" => "la la-bookmark");
			$menus[] = $menuItem;
		}

		$menuItem  = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_STATISTICS'),
			"access"=> "directory.access.statistics",
			"link" => "index.php?option=com_jbusinessdirectory&view=managestatistics",
			"view" => "managestatistics",
			"icon" => "la la-pie-chart");
		$menus[] = $menuItem;

		if (($actions->get('directory.access.customers')) && false) {
			$smenuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_CUSTOMERS'),
				"access" => "directory.access.customers",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=customers'),
				"view" => "customers",
				"icon" => "la la-user");
			$menus[] = $smenuItem;
		}

		$userToken = JSession::getFormToken();
		$return = base64_encode(JBusinessUtil::getWebsiteUrl(true));
		$menuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_LOGOUT'),
			"access" => "directory.access.listings",
			"link" => wp_logout_url(),
			"view" => "logout",
			"icon" => "la la-sign-out");
		$menus[] = $menuItem;

		return $menus;
	}

	public function generateUserMenu() {
		$menus = array();

        if ($this->appSettings->enable_bookmarks) {
            $menuItem = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BOOKMARKS'),
                "access" => "directory.access.bookmarks",
                "link" => "index.php?option=com_jbusinessdirectory&view=managebookmarks&user_dashboard=1",
                "view" => "managebookmarks",
                "icon" => "la la-bookmark");
            $menus[] = $menuItem;
        }

		if ($this->appSettings->show_cp_suggestions) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES_SUGGESTIONS'),
				"access" => "directory.access.listings",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=suggestions&type=1'),
				"view" => "suggestions",
				"icon" => "la la-list-ul");
			$menus[] = $menuItem;

			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_SUGGESTIONS'),
				"access" => "directory.access.listings",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=suggestions&type=2'),
				"view" => "suggestions",
				"icon" => "la la-list-ul");
			$menus[] = $menuItem;

			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_SUGGESTIONS'),
				"access" => "directory.access.listings",
				"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=suggestions&type=3'),
				"view" => "suggestions",
				"icon" => "la la-list-ul");
			$menus[] = $menuItem;

			if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/conference.php')) {
				$menuItem = array(
					"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CONFERENCE_SUGGESTIONS'),
					"access" => "directory.access.listings",
					"link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=suggestions&type=4'),
					"view" => "suggestions",
					"icon" => "la la-list-ul");
				$menus[] = $menuItem;
			}
		}

		if (JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS) && $this->appSettings->enable_request_quote_app) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REQUEST_QUOTES'),
				"access" => "directory.access.request.quote",
				"link" => "index.php?option=com_jbusinessdirectory&view=managerequestquotes",
				"view" => "managerequestquotes",
				"icon" => "la la-envelope-square");
			$menus[] = $menuItem;
		}

		if ($this->appSettings->enable_reviews) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEWS'),
				"access" => "directory.access.reviews",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageuserreviews",
				"view" => "manageuserreviews",
				"icon" => "la la-comment");
			$menus[] = $menuItem;
		}

		if ($this->appSettings->show_contact_form && false) {

            $user = JBusinessUtil::getUser();
            $nrMessages = JBusinessUtil::getTotalUserMessages($user->ID, true);

			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MESSAGES'),
				"access" => "directory.access.messages",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageusermessages",
				"view" => "manageusermessages",
				"icon" => "la la-comments",
                "nrMessages" => "$nrMessages",
                "display-unread-message" => true);
			$menus[] = $menuItem;
		}

		if ($this->appSettings->enable_packages) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PACKAGE_ORDERS'),
				"access" => "directory.access.orders",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageuserpackageorders",
				"view" => "manageuserpackageorders",
				"icon" => "la la-cog");
			$menus[] = $menuItem;
		}

		if ($this->appSettings->enable_events && $this->appSettings->enable_event_reservation && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventticket.php')) {
			
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_RESERVATIONS'),
				"access" => "directory.access.event.reservation",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageusereventreservations",
				"view" => "manageusereventreservations",
				"icon" => "la la-ticket");
			$menus[] = $menuItem;
		}

		if ($this->appSettings->enable_services == 1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/companyservice.php')) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SERVICE_BOOKINGS'),
				"access" => "directory.access.listing.service.reservation",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageuserservicereservations",
				"view" => "manageuserservicereservations",
				"icon" => "la la-calendar");
			$menus[] = $menuItem;
		}

		if ($this->appSettings->enable_events && $this->appSettings->enable_event_appointments && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/eventappointment.php')) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_APPOINTMENTS'),
				"access" => "directory.access.event.appointments",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageusereventappointments",
				"view" => "manageusereventappointments",
				"icon" => "la la-calendar");
			$menus[] = $menuItem;
		}

		if ($this->appSettings->enable_offers && $this->appSettings->enable_offer_selling && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/offerorder.php')) {
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_ORDERS'),
				"access" => "directory.access.offers",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageuserofferorders",
				"view" => "manageuserofferorders",
				"icon" => "la la-ticket");
			$menus[] = $menuItem;
		}
		
		if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) { 
			$menuItem = array(
				"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIP_BOOKINGS'),
				"access" => "directory.access.trips",
				"link" => "index.php?option=com_jbusinessdirectory&view=manageusertripbookings",
				"view" => "manageusertripbookings",
				"icon" => "la la-calendar");
			$menus[] = $menuItem;
		}
		$userToken = JSession::getFormToken();
		$return = base64_encode(JBusinessUtil::getWebsiteUrl(true));
		$menuItem = array(
			"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_LOGOUT'),
			"access" => "directory.access.listings",
			"link" => wp_logout_url(),
			"view" => "logout",
			"icon" => "la la-sign-out");
		$menus[] = $menuItem;

		return $menus;
	}



	public function setSectionDetails($name, $description) {
		$this->section_name = $name;
		$this->section_description = $description;
	}
}
