<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

// DENY DIRECT ACCESS TO THE FILE
if (!defined('ABSPATH'))
	die ('Restricted access');

/**
 * Create the menu structure on admin
 *
 * @author George
 *
 */
class BusinessDirectory_Admin_Menu
{
	public function __construct()
	{
		add_action('admin_menu', array(
			$this,
			'setup_admin_menu'
		));
	}
	
	
	function setup_admin_menu()
	
	{
	    
	    JBusinessUtil::loadSiteLanguage();
        $appSettings = JBusinessUtil::getApplicationSettings();

		add_menu_page(
		    "WP-BusinessDirectory",
		    "WP-BusinessDirectory",
			"directory_access_directory_management",
			"jbd_businessdirectory",
		    "wpbd_dashboard", BD_ASSETS_FOLDER_PATH.'images/wpbd-icon.png', 3);

		$menuItems = array();

		$menuItems [] = array(
			"parent_slug" => "jbd_businessdirectory",
		    "page_title"  =>  JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SETTINGS'),
		    "menu_title"  =>  JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SETTINGS'),
			"capability"  => "directory_access_directory_management",
			"url_slug"    => "jbd_applicationsettings",
			"callback"    => "wpbd_applicationsettings"
		);

		$menuItems [] = array(
			"parent_slug" => "jbd_businessdirectory",
		    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
		    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
			"capability"  => "directory_access_listings",
			"url_slug"    => "jbd_businesslistings",
			"callback"    => "wpbd_businesslistings"
		);

		if($appSettings->enable_services==1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/models/companyservice.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
				"page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_APPOINTMENTS'),
				"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_APPOINTMENTS'),
				"capability"  => "directory_access_listings",
				"url_slug"    => "jbd_appointments",
				"callback"    => "wpbd_appointments"
			);
		}

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/offers.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
				"capability"  => "directory_access_offers",
				"url_slug"    => "jbd_offers",
				"callback"    => "wpbd_offers"
			);
		}
		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/events.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
				"capability"  => "directory_access_events",
				"url_slug"    => "jbd_events",
				"callback"    => "wpbd_events"
			);
		}

		if (JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS) && $appSettings->enable_request_quote_app) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REQUEST_QUOTES'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REQUEST_QUOTES'),
				"capability"  => "directory_access_request_quote",
				"url_slug"    => "jbd_quoterequests",
				"callback"    => "wpbd_quoterequests"
			);
		}

		 if (JBusinessUtil::isAppInstalled(JBD_APP_TRIPS)) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIPS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TRIPS'),
				"capability"  => "directory_access_trips",
				"url_slug"    => "jbd_trips",
				"callback"    => "wpbd_trips"
			);
		}

		if (JBusinessUtil::isAppInstalled(JBD_APP_VIDEOS) && $appSettings->enable_videos) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
				"page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_VIDEOS'),
				"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_VIDEOS'),
				"capability"  => "directory_access_videos",
				"url_slug"    => "jbd_videos",
				"callback"    => "wpbd_videos"
			);
		}

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/campaigns.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CAMPAIGNS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CAMPAIGNS'),
				"capability"  => "directory_access_events",
				"url_slug"    => "jbd_campaigns",
				"callback"    => "wpbd_campaigns"
			);
		}

        $menuItems [] = array(
            "parent_slug" => "jbd_businessdirectory",
            "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MESSAGES'),
            "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MESSAGES'),
            "capability"  => "directory_access_messages",
            "url_slug"    => "jbd_messages",
            "callback"    => "wpbd_messages"
        );

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/packages.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PACKAGES'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PACKAGES'),
				"capability"  => "directory_access_packages",
				"url_slug"    => "jbd_packages",
				"callback"    => "wpbd_packages"
			);
		}
		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/discount.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_DISCOUNTS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_DISCOUNTS'),
				"capability"  => "directory_access_discounts",
				"url_slug"    => "jbd_discounts",
				"callback"    => "wpbd_discounts"
			);
		}

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/orders.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ORDERS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ORDERS'),
				"capability"  => "directory_access_orders",
				"url_slug"    => "jbd_orders",
				"callback"    => "wpbd_orders"
			);
		}

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/taxes.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TAXES'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_TAXES'),
				"capability"  => "directory_access_taxes",
				"url_slug"    => "jbd_taxes",
				"callback"    => "wpbd_taxes"
			);
		}


		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/paymentprocessors.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PAYMENT_PROCESSORS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_PAYMENT_PROCESSORS'),
				"capability"  => "directory_access_paymentprocessors",
				"url_slug"    => "jbd_paymentprocessors",
				"callback"    => "wpbd_paymentprocessors"
			);
		}

		$menuItems [] = array(
			"parent_slug" => "jbd_businessdirectory",
			"page_title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CURRENCIES'),
			"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CURRENCIES'),
			"capability" => "directory_access_currencies",
			"url_slug"    => "jbd_currencies",
			"callback"    => "wpbd_currencies");

		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/shippingmethods.php') && $appSettings->enable_shipping==1) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
			    "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SHIPPING_METHODS'),
			    "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SHIPPING_METHODS'),
				"capability"  => "directory_access_paymentprocessors",
				"url_slug"    => "jbd_shippingmethods",
				"callback"    => "wpbd_shippingmethods"
			);
		}

        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/reviews.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_AND_RATING'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REVIEW_AND_RATING'),
                "capability"  => "directory_access_reviews",
                "url_slug"    => "jbd_reviews",
                "callback"    => "wpbd_reviews"
            );
        }

        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/countries.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COUNTRIES'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COUNTRIES'),
                "capability"  => "directory_access_countries",
                "url_slug"    => "jbd_countries",
                "callback"    => "wpbd_countries"
            );
        }

		if($appSettings->limit_cities_regions == 1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/cities.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('LNG_CITIES'),
                "menu_title"  => JText::_('LNG_CITIES'),
                "capability"  => "directory_access_cities",
                "url_slug"    => "jbd_cities",
                "callback"    => "wpbd_cities"
            );
        }

		if($appSettings->limit_cities_regions == 1 && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/regions.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('LNG_REGIONS'),
                "menu_title"  => JText::_('LNG_REGIONS'),
                "capability"  => "directory_access_regions",
                "url_slug"    => "jbd_regions",
                "callback"    => "wpbd_regions"
            );
        }

        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/emailtemplates.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EMAILS_TEMPLATES'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EMAILS_TEMPLATES'),
                "capability"  => "directory_access_emails",
                "url_slug"    => "jbd_emails",
                "callback"    => "wpbd_emails"
            );
        }

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/statistics.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_STATISTICS'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_STATISTICS'),
                "capability"  => "directory_access_statistics",
                "url_slug"    => "jbd_statistics",
                "callback"    => "wpbd_statistics"
            );
        }
        
        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/reports.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REPORTS'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_REPORTS'),
                "capability"  => "directory_access_reports",
                "url_slug"    => "jbd_reports",
                "callback"    => "wpbd_reports"
            );
        }

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/searchlogs.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SEARCH_LOGS'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SEARCH_LOGS'),
                "capability"  => "directory_access_search_logs",
                "url_slug"    => "jbd_searchlogs",
                "callback"    => "wpbd_searchlogs"
            );
        }
        
        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/marketing.php')) {
	        $menuItems [] = array(
	            "parent_slug" => "jbd_businessdirectory",
	            "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MARKETING'),
	            "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MARKETING'),
	            "capability"  => "directory_access_marketing",
	            "url_slug"    => "jbd_marketing",
	            "callback"    => "wpbd_marketing"
	        );
		}

		if($appSettings->enable_articles && file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/companyarticles.php')) {
			$menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
				"page_title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_ARTICLES'),
				"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_ARTICLES'),
                "capability"  => "directory_access_listings",
                "url_slug"    => "jbd_companyarticles",
                "callback"    => "wpbd_companyarticles"
			);
		}

		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/conference.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
				"page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CONFERENCES'),
				"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_CONFERENCES'),
				"capability"  => "directory_access_conferences",
				"url_slug"    => "jbd_conferences",
				"callback"    => "wpbd_conferences"
			);
		}

		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/conference.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
				"page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SPEAKERS'),
				"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SPEAKERS'),
				"capability"  => "directory_access_conferences",
				"url_slug"    => "jbd_speakers",
				"callback"    => "wpbd_speakers"
			);
		}

		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/conference.php')) {
			$menuItems [] = array(
				"parent_slug" => "jbd_businessdirectory",
				"page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSIONS'),
				"menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_SESSIONS'),
				"capability"  => "directory_access_conferences",
				"url_slug"    => "jbd_sessions",
				"callback"    => "wpbd_sessions"
			);
		}

		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/mobileappconfig.php')) {
            $menuItems [] = array(
                "parent_slug" => "jbd_businessdirectory",
                "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MOBILE_APP_CONFIG'),
                "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_MOBILE_APP_CONFIG'),
                "capability"  => "directory_access_directory_management",
                "url_slug"    => "jbd_mobileappconfig",
                "callback"    => "wpbd_mobileappconfig"
            );
        }
		
		if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/updates.php')) {
		    $menuItems [] = array(
		        "parent_slug" => "jbd_businessdirectory",
		        "page_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_UPDATE'),
		        "menu_title"  => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_UPDATE'),
		        "capability"  => "directory_access_directory_management",
		        "url_slug"    => "jbd_updates",
		        "callback"    => "wpbd_updates");
		}
		
		if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/events.php')) {
	        $menuItems [] = array(
	            "parent_slug" => "jbd_businessdirectory",
	            "page_title"  => "Go PRO",
	            "menu_title"  => "<a href='https://www.cmsjunkie.com/wp-businessdirectory' target='_blank'><i class='dashicons-before dashicons-awards red'></i> Go PRO </a>",
	            "capability"  => "directory_access_countries",
	            "url_slug"    => "directory_update_pro",
	            "callback"    => "wpbd_update_pro");
		}

		$this->add_sub_pages($menuItems);
	}

	/**
	 * Wordpress Add menu page
	 */
	protected function settings_page()
	{
		add_menu_page($this->settings_page ["page_title"], $this->settings_page ["menu_title"], 'manage_options', $this->settings_page ["url_slug"], $this->settings_page ["callback"], $this->settings_page ["settings_icon"], $this->settings_page ["position"]);
	}

	/**
	 * Add submenu page
	 */
	protected function add_sub_pages($menuItems)
	{
		foreach ($menuItems as $menuItem) {
			$parent = $menuItem ["url_slug"];
			if (isset ($menuItem ["parent_slug"])) {
				$parent = $menuItem ["parent_slug"];
			}
			add_submenu_page($parent, $menuItem ["page_title"], $menuItem ["menu_title"], $menuItem ["capability"], $menuItem ["url_slug"], $menuItem ["callback"]);
		}
	}

	/**
	 * Register option_variables
	 */
	protected function register_settings()
	{
		foreach ($this->option_variables as $options)
			register_setting($this->plugin_name, $options);
	}
}

return new BusinessDirectory_Admin_Menu ();
