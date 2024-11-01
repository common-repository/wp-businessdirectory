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
 * * Application Administration page Callback **
 */

/**
 * General execute request function
 *
 */
function wpbd_executeRequest()
{
	require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'jbusinessdirectory.php');
}

/**
 * Callback function for main dashboard
 */
function wpbd_dashboard()
{
	wpbd_executeRequest();
}

/**
 * Callback function for application setting menu item
 */
function wpbd_applicationsettings()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"]   = "applicationsettings";
		$_REQUEST["layout"] = "edit";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for business listings menu item
 */
function wpbd_businesslistings()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "companies";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for appointments menu item
 */
function wpbd_appointments()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "companyserviceproviders";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for currencies menu item
 */
function wpbd_currencies()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "currencies";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for cities menu item
 */
function wpbd_cities()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "cities";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for regions menu item
 */
function wpbd_regions()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "regions";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for searchlogs menu item
 */
function wpbd_searchlogs()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "searchlogs";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for mobileappconfig menu item
 */
function wpbd_mobileappconfig()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "mobileappconfig";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for companyarticles menu item
 */
function wpbd_companyarticles()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "companyarticles";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for offers menu item
 */
function wpbd_offers()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "offers";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for events menu item
 */
function wpbd_events()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "events";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for quote requests menu item
 */
function wpbd_quoterequests()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "requestquotes";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for quote requests menu item
 */
function wpbd_trips()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "trips";
	}

	wpbd_executeRequest();
}


/**
 * Callback function for quote requests menu item
 */
function wpbd_videos()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "videos";
	}

	wpbd_executeRequest();
}


/**
 * Callback function for quote requests menu item
 */
function wpbd_conferences()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "conferences";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for quote requests menu item
 */
function wpbd_speakers()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "speakers";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for quote requests menu item
 */
function wpbd_sessions()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "sessions";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for campaigns menu item
 */
function wpbd_campaigns()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "campaigns";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for categories menu item
 */
function wpbd_categories()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "categories";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for packages menu item
 */
function wpbd_packages()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "packages";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for discounts menu item
 */
function wpbd_discounts()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "discounts";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for orders menu item
 */
function wpbd_orders()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "orders";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for taxes menu item
 */
function wpbd_taxes()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "taxes";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for memberships menu item
 */
function wpbd_memberships()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "memberships";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for payment processors menu item
 */
function wpbd_paymentprocessors()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "paymentprocessors";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for shipping methods menu item
 */
function wpbd_shippingmethods()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "shippingmethods";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for attributes menu item
 */
function wpbd_attributes()
{
	if (empty($_REQUEST["view"])) {
		$_REQUEST["view"] = "attributes";
	}

	wpbd_executeRequest();
}

/**
 * Callback function for reviews menu item
 */
function wpbd_reviews()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "reviews";
    }

    wpbd_executeRequest();
}

/**
 * Callback function for countries menu item
 */
function wpbd_countries()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "countries";
    }

    wpbd_executeRequest();
}

/**
 * Callback function for emailtemplates menu item
 */
function wpbd_emails()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "emailtemplates";
    }

    wpbd_executeRequest();
}

/**
 * Callback function for emailtemplates menu item
 */
function wpbd_statistics()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "statistics";
    }

    wpbd_executeRequest();
}

/**
 * Callback function for reports menu item
 */
function wpbd_reports()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "reports";
    }

    wpbd_executeRequest();
}

/**
 * Callback function for marketing menu item
 */
function wpbd_marketing()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "marketing";
    }

    wpbd_executeRequest();
}


/**
 * Callback function for marketing menu item
 */
function wpbd_messages()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "messages";
    }

    wpbd_executeRequest();
}

/**
 * Callback function for marketing menu item
 */
function wpbd_announcements()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "announcements";
    }

    wpbd_executeRequest();
}


/**
 * Callback function for marketing menu item
 */
function wpbd_updates()
{
    if (empty($_REQUEST["view"])) {
        $_REQUEST["view"] = "updates";
    }
    
    wpbd_executeRequest();
}