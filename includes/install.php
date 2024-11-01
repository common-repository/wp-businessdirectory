<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

// Access Denied
if (! defined('ABSPATH'))
    die('Restricted access');

/**
 * Handle the installation of the WP-BusinessDirectory plugin
 * 
 * @author George
 *
 */    
    
class BusinessDirectoryInstall
{

    /**
     * Install the WP-BusinessDirectory plugin.
     */
    public static function install(){

        if (WP_BUSINESSDIRECTORY_DB_VERSION_NUM === get_option(WP_BUSINESSDIRECTORY_DB_VERSION_KEY)) {
          return;
        } 

        // Check if we are not already running this routine.
        if ('yes' === get_transient('businessdirectory_installing')) {
            // return;
        }
        
        //dump("checking the database");

        // If we made it till here nothing is running yet, lets set the transient now.
        set_transient('businessdirectory_installing', 'yes', MINUTE_IN_SECONDS * 10);
        
        self::create_tables();
        
        delete_transient('businessdirectory_installing');

          // schedule cron jobs
        if (!wp_next_scheduled ('businessdirectory_daily_event')) {
            wp_schedule_event(time(), 'daily', 'businessdirectory_daily_event');
        }

        $uploadDir = wp_upload_dir();
        $src = WP_BUSINESSDIRECTORY_PATH."uploads";
        $dest = $uploadDir["basedir"]."/".WP_BUSINESS_DIRECTORY_UPLOAD_DIRECTORY;

        self::wpbd_copy_dir($src, $dest);
        do_action( 'businessdirectory_installed' );
    }

     /**
     * Uninstall the WP-BusinessDirectory plugin. Removes all data from the database
     */
    public static function uninstall() {
      global $wpdb;

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      $queries = self::get_uninstall_queries();
     
      $queries = explode( ';', $queries );
      $queries = array_filter( $queries );
  
      foreach ( $queries as $query ) {
          $wpdb->query( $query );
      }
     
      delete_option(WP_BUSINESSDIRECTORY_DB_VERSION_KEY);
    }

    
    /**
     * Copy the files from src to dest
     *
     * @param [type] $src
     * @param [type] $dst
     * @return void
     */
    public static function wpbd_copy_dir($src,$dst) {
        $dir = opendir($src);
        if(!is_dir($dst) ){
            @mkdir($dst);
        }
        while(false !== ($file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                
                if ( is_dir($src . '/' . $file) ) {
                    self::wpbd_copy_dir($src .'/'. $file, $dst .'/'. $file);
                }
                else {
                    copy($src .'/'. $file, $dst .'/'. $file);
                }
            }
        }
        closedir($dir);
    }
    

   /**
    * Run the queries for database creation and populate
    *
    * @return void
    */
    private static function create_tables()
    {
        // Create Media
        global $wpdb;
        $wpdb->hide_errors();
        
        $installedVersion = get_option(WP_BUSINESSDIRECTORY_DB_VERSION_KEY);
        //dump($installedVersion);
        if (WP_BUSINESSDIRECTORY_DB_VERSION_NUM !== $installedVersion) {
          //dump("running install query");
          require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
          $queries = self::get_schema();
          dbDelta($queries);

          //update the datbase version
          update_option(WP_BUSINESSDIRECTORY_DB_VERSION_KEY, WP_BUSINESSDIRECTORY_DB_VERSION_NUM);
        }


        //installing the initial data & demo data
        if(empty($installedVersion)){
          //dump("running the data install");
          require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
          $queries = self::get_installation_data();
          dbDelta($queries);
        }

    }

    /**
     * Retrieve the database schema
     *
     * @return void
     */
    private static function get_schema()
    {
        global $wpdb;

$sql = "



CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_application_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `text` varchar(100) DEFAULT NULL,
  `description` varchar(110) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `is_mandatory` int(1) NOT NULL DEFAULT '0',
  `show_in_filter` tinyint(1) NOT NULL DEFAULT '1',
  `only_for_admin` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_front` tinyint(1) NOT NULL DEFAULT '0',
  `show_on_search` tinyint(1) NOT NULL DEFAULT '0',
  `show_icon` tinyint(1) NOT NULL DEFAULT '0',
  `show_name` tinyint(1) NOT NULL DEFAULT '1',
  `color` varchar(10) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `attribute_type` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_list_view` tinyint(1) NOT NULL DEFAULT '0',
  `use_attribute_for_selling` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`),
  KEY `idx_attribute_type` (`attribute_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_attribute_category` (
  `attributeId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`attributeId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_attribute_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_attribute_id` (`attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_attribute_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_billing_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `company_name` varchar(55) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `street_number` varchar(100) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `region` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vat_details` varchar(455) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_bookmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `item_type` tinyint(1) DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_bkm` (`item_id`,`user_id`,`item_type`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_item_type` (`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(145) NOT NULL,
  `initial_budget` decimal(12,2) DEFAULT '0.00',
  `budget` decimal(12,2) DEFAULT '0.00',
  `status` tinyint(4) DEFAULT '0',
  `published` tinyint(4) DEFAULT '1',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_status` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_campaign_has_plans` (
  `campaign_id` int(11) NOT NULL,
  `campaign_plan_id` int(11) NOT NULL,
  `nr_clicks` int(11) DEFAULT '0',
  PRIMARY KEY (`campaign_id`,`campaign_plan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_campaign_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(145) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `click_price` decimal(12,2) DEFAULT NULL,
  `published` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `description` text,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `imageLocation` varchar(250) DEFAULT NULL,
  `markerLocation` varchar(250) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `clickCount` int(11) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `iconImgLocation` varchar(250) DEFAULT NULL,
  `keywords` varchar(250) DEFAULT NULL,
  `user_as_container` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_alias` (`alias`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_name` (`name`),
  KEY `idx_state` (`published`),
  KEY `idx_type` (`type`),
  KEY `idx_lft` (`lft`),
  KEY `idx_rgt` (`rgt`),
  KEY `idx_level` (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(65) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_region` (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `comercialName` varchar(120) DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` text,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` text,
  `street_number` varchar(100) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `county` varchar(60) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `area` varchar(60) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `website` varchar(250) DEFAULT NULL,
  `keywords` varchar(250) DEFAULT NULL,
  `registrationCode` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  `typeId` varchar(255) DEFAULT NULL,
  `logoLocation` varchar(245) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `mainSubcategory` int(11) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `activity_radius` decimal(10,0) DEFAULT NULL,
  `userId` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `averageRating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `review_score` decimal(2,1) NOT NULL DEFAULT '0.0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `viewCount` int(11) NOT NULL DEFAULT '0',
  `websiteCount` int(11) NOT NULL DEFAULT '0',
  `contactCount` int(11) NOT NULL DEFAULT '0',
  `taxCode` varchar(45) DEFAULT NULL,
  `package_id` int(11) NOT NULL DEFAULT '0',
  `facebook` varchar(150) DEFAULT NULL,
  `twitter` varchar(150) DEFAULT NULL,
  `googlep` varchar(150) DEFAULT NULL,
  `skype` varchar(150) DEFAULT NULL,
  `linkedin` varchar(150) DEFAULT NULL,
  `youtube` varchar(150) DEFAULT NULL,
  `instagram` varchar(150) DEFAULT NULL,
  `pinterest` varchar(150) DEFAULT NULL,
  `whatsapp` varchar(150) DEFAULT NULL,
  `postalCode` varchar(55) DEFAULT NULL,
  `mobile` varchar(55) DEFAULT NULL,
  `slogan` varchar(500) DEFAULT NULL,
  `publish_only_city` tinyint(1) DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `business_hours` varchar(255) DEFAULT NULL,
  `notes_hours` varchar(255) DEFAULT NULL,
  `custom_tab_name` varchar(100) DEFAULT NULL,
  `custom_tab_content` text,
  `business_cover_image` varchar(245) DEFAULT NULL,
  `publish_start_date` date DEFAULT NULL,
  `publish_end_date` date DEFAULT NULL,
  `notified_date` timestamp NULL DEFAULT NULL,
  `time_zone` varchar(15) DEFAULT NULL,
  `establishment_year` varchar(10) DEFAULT NULL,
  `employees` varchar(100) DEFAULT NULL,
  `ad_image` varchar(250) DEFAULT NULL,
  `disapproval_text` text,
  `yelp_id` varchar(255) DEFAULT NULL,
  `enable_request_quote` tinyint(1) DEFAULT '1',
  `trail_weeks_hours` text,
  `trail_weeks_status` tinyint(1) DEFAULT '1',
  `trail_weeks_address` varchar(100) DEFAULT NULL,
  `ad_caption` varchar(245) DEFAULT NULL,
  `recommended` tinyint(1) DEFAULT '0',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `company_view` tinyint(1) DEFAULT '0',
  `opening_status` tinyint(4) NOT NULL DEFAULT '0',
  `min_project_size` varchar(100) DEFAULT NULL,
  `hourly_rate` varchar(10) DEFAULT NULL,
  `review_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`typeId`),
  KEY `idx_user` (`userId`),
  KEY `idx_state` (`state`),
  KEY `idx_approved` (`approved`),
  KEY `idx_country` (`countryId`),
  KEY `idx_package` (`package_id`),
  KEY `idx_name` (`name`),
  KEY `idx_keywords` (`keywords`),
  KEY `idx_description` (`description`(100)),
  KEY `idx_city` (`city`),
  KEY `idx_county` (`county`),
  KEY `idx_maincat` (`mainSubcategory`),
  KEY `idx_zipcode` (`latitude`,`longitude`),
  KEY `idx_phone` (`phone`),
  KEY `idx_creationDate` (`creationDate`),
  KEY `idx_review_score` (`review_score`),
  KEY `idx_province` (`province`),
  KEY `idx_area` (`area`),
  KEY `idx_publish_start_date` (`publish_start_date`),
  KEY `idx_publish_end_date` (`publish_end_date`),
  KEY `idx_alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_activity_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IND_UNQ` (`company_id`,`city_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_city` (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_activity_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IND_UNQ` (`company_id`,`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_activity_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IND_UNQ` (`company_id`,`region_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_city` (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call_to_action` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `expiration_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_articles` (
  `article_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`),
  KEY `idx_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `path` varchar(155) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_object` (`object_id`),
  KEY `idx_status` (`status`),
  KEY `idx_order` (`ordering`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(250) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_UNIQUE` (`company_id`,`attribute_id`,`value`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_attribute_id` (`attribute_id`),
  KEY `idx_option_id` (`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_category` (
  `companyId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`companyId`,`categoryId`),
  KEY `idx_category` (`companyId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IND_UNQ` (`company_id`,`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_claim` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) DEFAULT NULL,
  `firstName` varchar(55) DEFAULT NULL,
  `lastName` varchar(55) DEFAULT NULL,
  `function` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(65) DEFAULT NULL,
  `status` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`companyId`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `contact_name` varchar(50) DEFAULT NULL,
  `contact_function` varchar(50) DEFAULT NULL,
  `contact_department` varchar(100) DEFAULT NULL,
  `contact_job_title` varchar(100) DEFAULT NULL,
  `contact_email` varchar(60) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_fax` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`companyId`),
  KEY `R_13` (`companyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_editors` (
  `company_id` int(11) NOT NULL,
  `editor_id` int(11) NOT NULL,
  PRIMARY KEY (`company_id`,`editor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(110) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `short_description` varchar(245) DEFAULT NULL,
  `description` text,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  `price` varchar(100) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `street_number` varchar(100) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `county` varchar(45) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `area` varchar(60) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `view_count` int(11) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `state` tinyint(1) DEFAULT NULL,
  `recurring_id` int(11) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(60) DEFAULT NULL,
  `doors_open_time` time DEFAULT NULL,
  `booking_open_date` date DEFAULT NULL,
  `booking_close_date` date DEFAULT NULL,
  `booking_open_time` time DEFAULT NULL,
  `booking_close_time` time DEFAULT NULL,
  `show_start_time` tinyint(1) NOT NULL DEFAULT '0',
  `show_end_time` tinyint(1) NOT NULL DEFAULT '0',
  `show_end_date` tinyint(1) NOT NULL DEFAULT '0',
  `show_doors_open_time` tinyint(1) NOT NULL DEFAULT '0',
  `currency_id` int(11) NOT NULL DEFAULT '0',
  `total_tickets` int(11) DEFAULT NULL,
  `expiration_email_date` datetime DEFAULT NULL,
  `main_subcategory` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `enable_subscription` tinyint(1) NOT NULL DEFAULT '0',
  `postalCode` varchar(55) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `time_zone` varchar(15) DEFAULT NULL,
  `recurring_info` varchar(255) DEFAULT NULL,
  `attendance_mode` tinyint(4) DEFAULT NULL,
  `attendance_url` varchar(245) DEFAULT NULL,
  `min_age` tinyint(4) DEFAULT NULL,
  `max_age` tinyint(4) DEFAULT NULL,
  `ticket_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_name` (`name`),
  KEY `idx_alias` (`alias`),
  KEY `idx_countryId` (`countryId`),
  KEY `idx_created` (`created`),
  KEY `idx_recurring_id` (`recurring_id`),
  KEY `idx_expiration_email_date` (`expiration_email_date`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_search` (`start_date`,`end_time`,`end_date`,`state`,`approved`,`start_time`),
  KEY `idx_type` (`type`),
  KEY `idx_featured` (`featured`),
  KEY `idx_currency` (`currency_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `company_name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `remarks` varchar(300) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `event_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_event` (`event_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_associated_items` (
  `event_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `address` varchar(55) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `region` varchar(45) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `initial_amount` decimal(12,2) DEFAULT NULL,
  `vat_amount` decimal(12,2) DEFAULT NULL,
  `vat` decimal(12,2) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_event` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_booking_tickets` (
  `booking_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `ticket_quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`booking_id`,`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_category` (
  `eventId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`eventId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_pictures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `eventId` int(10) NOT NULL DEFAULT '0',
  `picture_info` varchar(255) DEFAULT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `picture_enable` tinyint(1) NOT NULL DEFAULT '1',
  `picture_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_event` (`eventId`),
  KEY `idx_status` (`picture_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `price` decimal(12,2) DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `min_booking` int(11) DEFAULT NULL,
  `max_booking` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_event_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `street_number` varchar(100) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `county` varchar(60) DEFAULT NULL,
  `postalCode` varchar(45) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `activity_radius` decimal(10,2) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `area` varchar(60) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_country` (`countryId`),
  KEY `idx_company` (`company_id`),
  KEY `idx_identifier` (`identifier`),
  KEY `idx_city` (`city`),
  KEY `idx_county` (`county`),
  KEY `idx_province` (`province`),
  KEY `idx_area` (`area`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_marketing_email_sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_type` int(11) DEFAULT NULL,
  `number_email_sent` int(11) DEFAULT NULL,
  `sending_date` datetime DEFAULT NULL,
  `failed_sent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `description` varchar(350) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_membership` (
  `company_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  PRIMARY KEY (`company_id`,`membership_id`),
  KEY `idx_category` (`company_id`,`membership_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `subject` varchar(110) DEFAULT NULL,
  `description` text,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  `price` decimal(12,2) DEFAULT NULL,
  `specialPrice` decimal(12,2) DEFAULT NULL,
  `price_base` decimal(12,2) DEFAULT NULL,
  `price_base_unit` varchar(45) DEFAULT NULL,
  `special_price_base` decimal(12,2) DEFAULT NULL,
  `special_price_base_unit` varchar(45) DEFAULT NULL,
  `total_coupons` int(11) NOT NULL DEFAULT '0',
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `offerOfTheDay` tinyint(1) NOT NULL DEFAULT '0',
  `viewCount` int(10) DEFAULT '0',
  `alias` varchar(100) DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `street_number` varchar(100) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `county` varchar(45) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `area` varchar(60) DEFAULT NULL,
  `publish_start_date` date DEFAULT NULL,
  `publish_end_date` date DEFAULT NULL,
  `view_type` tinyint(2) NOT NULL DEFAULT '1',
  `url` varchar(145) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `show_time` tinyint(1) DEFAULT '0',
  `publish_start_time` time DEFAULT NULL,
  `publish_end_time` time DEFAULT NULL,
  `expiration_email_date` datetime DEFAULT NULL,
  `main_subcategory` int(11) DEFAULT NULL,
  `enable_offer_selling` tinyint(1) DEFAULT '1',
  `min_purchase` int(11) DEFAULT NULL,
  `max_purchase` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `postalCode` varchar(55) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `countryId` int(11) DEFAULT NULL,
  `price_text` varchar(100) DEFAULT NULL,
  `offer_type` int(11) DEFAULT NULL,
  `time_zone` varchar(15) DEFAULT NULL,
  `item_type` tinyint(1) NOT NULL DEFAULT '1',
  `notify_offer_quantity` int(11) DEFAULT NULL,
  `use_stock_price` tinyint(1) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `add_to_price_list` tinyint(1) DEFAULT '0',
  `review_score` decimal(2,1) DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `idx_alias` (`alias`),
  KEY `idx_company` (`companyId`),
  KEY `idx_country` (`countryId`),
  KEY `idx_approved` (`approved`),
  KEY `idx_type` (`type`),
  KEY `idx_featured` (`featured`),
  KEY `idx_latitude` (`latitude`),
  KEY `idx_longitude` (`longitude`),
  KEY `idx_price` (`price`),
  KEY `idx_specialPrice` (`specialPrice`),
  KEY `idx_subject` (`subject`),
  KEY `idx_city` (`city`),
  KEY `idx_county` (`county`),
  KEY `idx_area` (`area`),
  KEY `idx_province` (`province`),
  KEY `idx_short_description` (`short_description`(100)),
  KEY `idx_address` (`address`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expiration_email_date` (`expiration_email_date`),
  KEY `idx_search` (`state`,`endDate`,`startDate`,`publish_end_date`,`publish_start_date`),
  KEY `idx_publish_time` (`publish_start_time`,`publish_end_time`),
  KEY `idx_offer_type` (`offer_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offer_category` (
  `offerId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`offerId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offer_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `generated_time` datetime DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_offer_id` (`offer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offer_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `address` varchar(55) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `region` varchar(45) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `tracking_link` varchar(255) DEFAULT NULL,
  `shipping` decimal(12,2) DEFAULT NULL,
  `initial_amount` decimal(12,2) DEFAULT NULL,
  `shipping_method` int(11) DEFAULT NULL,
  `vat_amount` decimal(12,2) DEFAULT NULL,
  `vat` decimal(12,2) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offer_order_products` (
  `order_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `currencyId` int(11) NOT NULL,
  `combination_ids` varchar(255) DEFAULT NULL,
  `combination_values` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`order_id`,`offer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offer_pictures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `offerId` int(10) NOT NULL DEFAULT '0',
  `picture_info` varchar(255) DEFAULT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `picture_enable` tinyint(1) NOT NULL DEFAULT '1',
  `picture_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offer` (`offerId`),
  KEY `idx_status` (`picture_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_offer_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_pictures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `companyId` int(10) NOT NULL DEFAULT '0',
  `picture_info` varchar(255) DEFAULT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `picture_enable` tinyint(1) NOT NULL DEFAULT '1',
  `picture_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`companyId`),
  KEY `idx_picture_enable` (`picture_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_pictures_extra` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `companyId` int(10) NOT NULL DEFAULT '0',
  `image_info` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_enable` tinyint(1) NOT NULL DEFAULT '1',
  `image_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`companyId`),
  KEY `idx_image_enable` (`image_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_product_merchants` (
  `product_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`company_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_projects_pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL DEFAULT '0',
  `picture_info` varchar(255) DEFAULT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `picture_enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_projectId` (`projectId`),
  KEY `idx_status` (`picture_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `max_clients` int(11) DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `company_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `istart_date` date DEFAULT NULL,
  `iend_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_published` (`published`),
  KEY `idx_availability` (`availability`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_provider_hours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weekday` tinyint(1) DEFAULT NULL,
  `start_hour` time DEFAULT NULL,
  `end_hour` time DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `item_type` tinyint(1) DEFAULT '0',
  `provider_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_provider_id` (`provider_id`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_provider_services` (
  `provider_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  PRIMARY KEY (`provider_id`,`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `ipAddress` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company` (`companyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IND_UNQ` (`company_id`,`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_registered` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `joined_company_id` int(11) NOT NULL,
  `approved` tinyint(2) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_join_company` (`joined_company_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_approved` (`approved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_related` (
  `company_id` int(11) NOT NULL,
  `related_company_id` int(11) NOT NULL,
  PRIMARY KEY (`company_id`,`related_company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_requests` (
  `company_id` int(11) NOT NULL,
  `quote_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`company_id`,`quote_id`),
  KEY `idx_company_quote` (`company_id`,`quote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text,
  `userId` int(11) NOT NULL,
  `email` varchar(55) DEFAULT NULL,
  `likeCount` smallint(6) DEFAULT '0',
  `dislikeCount` smallint(6) DEFAULT '0',
  `loveCount` smallint(6) DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '1',
  `itemId` int(11) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aproved` tinyint(1) NOT NULL DEFAULT '0',
  `ipAddress` varchar(45) DEFAULT NULL,
  `abuseReported` tinyint(1) NOT NULL DEFAULT '0',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `review_type` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_userId` (`userId`),
  KEY `idx_aproved` (`aproved`),
  KEY `idx_approved` (`approved`),
  KEY `idx_review_type` (`review_type`),
  KEY `idx_item_id` (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_reviews_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(77) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `published` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_reviews_criteria_category` (
  `criteriaId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`criteriaId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_reviews_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `is_mandatory` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_reviews_question_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `answer` varchar(455) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_review_id` (`review_id`),
  KEY `idx_question_id` (`question_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_reviews_user_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `score` decimal(2,1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_review_id` (`review_id`),
  KEY `idx_criteria_id` (`criteria_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_review_abuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviewId` int(11) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `description` text,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_reviewId` (`reviewId`),
  KEY `idx_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_review_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `reviewId` int(11) NOT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `response` text,
  `email` varchar(45) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_review` (`reviewId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `duration` int(11) DEFAULT NULL,
  `show_duration` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(12,2) DEFAULT NULL,
  `currency_id` int(10) NOT NULL,
  `max_booking` int(11) DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `company_id` int(11) NOT NULL,
  `attendance_mode` tinyint(1) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `istart_date` date DEFAULT NULL,
  `iend_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_show_duration` (`show_duration`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_services_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `service_section` varchar(100) DEFAULT NULL,
  `service_name` varchar(100) DEFAULT NULL,
  `service_description` text,
  `service_price` decimal(12,2) DEFAULT NULL,
  `service_image` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`companyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_service_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `address` varchar(55) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `region` varchar(45) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` varchar(245) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `initial_amount` decimal(12,2) DEFAULT NULL,
  `vat_amount` decimal(12,2) DEFAULT NULL,
  `vat` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_provider_id` (`provider_id`),
  KEY `idx_status` (`status`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_sounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `url` varchar(450) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `testimonial_title` varchar(50) DEFAULT NULL,
  `testimonial_name` varchar(50) DEFAULT NULL,
  `testimonial_description` varchar(450) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`companyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `only_for_admin` tinyint(1) NOT NULL DEFAULT '0',
  `company_view` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_only_for_admin` (`only_for_admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) DEFAULT NULL,
  `url` varchar(245) DEFAULT NULL,
  `title` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_companyId` (`companyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_company_zipcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(125) DEFAULT NULL,
  `alias` varchar(125) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  `place` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `registration_link` varchar(255) DEFAULT NULL,
  `viewCount` int(11) NOT NULL DEFAULT '0',
  `featured` tinyint(1) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_featured` (`featured`),
  KEY `idx_published` (`published`),
  KEY `search` (`start_date`,`end_date`,`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `locationId` int(11) DEFAULT NULL,
  `sessiontypeId` int(11) DEFAULT NULL,
  `sessionlevelId` int(11) DEFAULT NULL,
  `short_description` varchar(355) DEFAULT NULL,
  `description` text,
  `viewCount` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) DEFAULT '1',
  `conferenceId` int(11) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `register_url` varchar(245) DEFAULT NULL,
  `time_zone` varchar(45) DEFAULT NULL,
  `session_url` varchar(245) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_name` (`name`),
  KEY `idx_short_description` (`short_description`(100)),
  KEY `conference` (`conferenceId`),
  KEY `idx_sessiontypeId` (`sessiontypeId`),
  KEY `idx_sessionlevelId` (`sessionlevelId`),
  KEY `idx_alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `path` varchar(155) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_object` (`object_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_categories` (
  `sessionId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY (`sessionId`,`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_companies` (
  `sessionId` int(11) NOT NULL,
  `companyId` int(11) NOT NULL,
  PRIMARY KEY (`sessionId`,`companyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_registers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `joined` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique` (`user_id`,`session_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_speakers` (
  `sessionId` int(11) NOT NULL,
  `speakerId` int(11) NOT NULL,
  `speaker_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionId`,`speakerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_session_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `clickCount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_speakers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) DEFAULT NULL,
  `alias` varchar(125) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `company_name` varchar(55) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `speaker_language` varchar(100) DEFAULT NULL,
  `biography` text,
  `sessionId` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `speakertypeId` int(11) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `googlep` varchar(100) DEFAULT NULL,
  `linkedin` varchar(100) DEFAULT NULL,
  `short_biography` text,
  `additional_info_link` varchar(100) DEFAULT NULL,
  `viewCount` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_speakertypeId` (`speakertypeId`),
  KEY `idx_name` (`name`),
  KEY `idx_title` (`title`),
  KEY `idx_company_name` (`company_name`),
  KEY `idx_phone` (`phone`),
  KEY `idx_countryId` (`countryId`),
  KEY `idx_alias` (`alias`),
  KEY `idx_session` (`sessionId`),
  KEY `idx_featured` (`featured`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_conference_speaker_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_countries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `country_name` char(255) DEFAULT NULL,
  `country_code` varchar(4) DEFAULT NULL,
  `country_currency` char(255) DEFAULT NULL,
  `country_currency_short` char(50) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `description` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_code` (`country_code`),
  KEY `idx_name` (`country_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_currencies` (
  `currency_id` int(10) NOT NULL AUTO_INCREMENT,
  `currency_name` char(10) DEFAULT NULL,
  `currency_description` varchar(70) DEFAULT NULL,
  `currency_symbol` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_date_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `dateFormat` varchar(45) DEFAULT NULL,
  `calendarFormat` varchar(45) DEFAULT NULL,
  `defaultDateValue` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_default_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) DEFAULT NULL,
  `listing_config` tinyint(1) DEFAULT NULL,
  `offer_config` tinyint(1) DEFAULT NULL,
  `event_config` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_directory_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `app_name` varchar(255) DEFAULT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT '',
  `store_link` varchar(255) DEFAULT '',
  `doc_link` varchar(255) DEFAULT '',
  `version` varchar(50) DEFAULT NULL,
  `required_version` varchar(50) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_discounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `value` decimal(6,2) NOT NULL DEFAULT '0.00',
  `percent` tinyint(1) NOT NULL DEFAULT '0',
  `price_type` tinyint(1) NOT NULL DEFAULT '1',
  `package_ids` varchar(255) DEFAULT NULL,
  `code` varchar(50) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `uses_per_coupon` int(11) DEFAULT NULL,
  `coupon_used` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`),
  KEY `idx_state` (`state`),
  KEY `idx_date` (`end_date`,`start_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_emails` (
  `email_id` int(10) NOT NULL AUTO_INCREMENT,
  `email_subject` char(255) DEFAULT NULL,
  `email_name` char(255) DEFAULT NULL,
  `email_type` varchar(255) DEFAULT NULL,
  `email_content` blob NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `send_to_admin` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`email_id`),
  KEY `idx_type` (`email_type`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_event_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(250) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_UNIQUE` (`event_id`,`attribute_id`,`value`),
  KEY `idx_option_id` (`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_event_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventId` int(11) DEFAULT NULL,
  `url` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ids_eventId` (`eventId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_language_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL,
  `language_tag` varchar(10) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `content_short` varchar(355) DEFAULT NULL,
  `content` text,
  `additional_content` text,
  PRIMARY KEY (`id`),
  KEY `idx_object` (`object_id`),
  KEY `ids_langauge` (`language_tag`),
  KEY `ids_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `item_type` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` tinyint(4) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_memberships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `description` varchar(250) DEFAULT NULL,
  `logo_location` varchar(250) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `show_in_front` tinyint(4) DEFAULT '1',
  `url` varchar(245) DEFAULT NULL,
  `image_title` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(145) DEFAULT NULL,
  `surname` varchar(145) DEFAULT NULL,
  `email` char(255) DEFAULT NULL,
  `message` text,
  `item_id` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `read` tinyint(1) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_contact_id` (`contact_id`),
  KEY `idx_type` (`type`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_mobile_app_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_mobile_app_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `group` varchar(45) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `lang` varchar(45) DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_mobile_app_notifications` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `nr_contacts` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `itemType` tinyint(1) DEFAULT NULL,
  `itemId` int(10) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_mobile_devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `device_id` varchar(30) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `firebase_token` varchar(255) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `enable_push_notifications` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token`),
  KEY `idx_firebase` (`firebase_token`),
  KEY `idx_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `description` text,
  `publish_date` datetime DEFAULT NULL,
  `retrieve_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_offer_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(250) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_UNIQUE` (`offer_id`,`attribute_id`,`value`),
  KEY `idx_option` (`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_offer_shipping_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) DEFAULT NULL,
  `shipping_method_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IND_UNQ` (`offer_id`,`shipping_method_id`),
  KEY `idx_offer` (`offer_id`),
  KEY `idx_shipping_method` (`shipping_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_offer_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `notify_stock_qty` int(11) NOT NULL DEFAULT '0',
  `stock_main_category` int(11) NOT NULL DEFAULT '0',
  `price` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offer` (`offer_id`),
  KEY `ixd_category` (`stock_main_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_offer_stock_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) NOT NULL,
  `attribute_id` int(11) DEFAULT NULL,
  `attribute_value` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_stock` (`stock_id`),
  KEY `idx_attribute` (`attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_offer_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offerId` int(11) DEFAULT NULL,
  `url` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_offerId` (`offerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(145) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `initial_amount` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid_at` datetime DEFAULT NULL,
  `state` tinyint(4) DEFAULT NULL,
  `transaction_id` varchar(145) DEFAULT NULL,
  `user_name` varchar(145) DEFAULT NULL,
  `service` varchar(145) DEFAULT NULL,
  `description` varchar(145) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_trial_date` date DEFAULT NULL,
  `end_trial_date` date DEFAULT NULL,
  `trial_initial_amount` decimal(12,2) DEFAULT NULL,
  `only_trial` tinyint(1) DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `currency` varchar(10) DEFAULT NULL,
  `expiration_email_date` datetime DEFAULT NULL,
  `discount_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT '0.00',
  `vat` decimal(12,2) DEFAULT NULL,
  `vat_amount` decimal(12,2) DEFAULT NULL,
  `observation` varchar(250) DEFAULT NULL,
  `trial_amount` decimal(6,2) DEFAULT NULL,
  `trial_days` tinyint(4) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notify_payment` tinyint(4) NOT NULL DEFAULT '0',
  `expiration_processed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_package` (`package_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_state` (`state`),
  KEY `idx_expiration_email_date` (`expiration_email_date`),
  KEY `idx_start_date` (`start_date`),
  KEY `idx_start_trial_date` (`start_trial_date`),
  KEY `idx_type` (`type`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_end_date` (`end_date`),
  KEY `idx_end_trial_date` (`end_trial_date`),
  KEY `idx_date` (`start_date`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_order_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `name` varchar(145) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `expiration_type` tinyint(1) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `trial_price` decimal(12,2) DEFAULT NULL,
  `trial_days` smallint(6) NOT NULL DEFAULT '0',
  `trial_period_unit` varchar(10) NOT NULL DEFAULT 'D',
  `trial_period_amount` smallint(6) DEFAULT NULL,
  `recurrence_count` tinyint(4) DEFAULT NULL,
  `special_price` decimal(12,2) DEFAULT NULL,
  `special_from_date` date DEFAULT NULL,
  `special_to_date` date DEFAULT NULL,
  `days` smallint(6) NOT NULL DEFAULT '1',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `time_unit` varchar(10) NOT NULL DEFAULT 'D',
  `time_amount` mediumint(9) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_price` (`price`),
  KEY `idx_order` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_order_taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `tax_name` varchar(50) DEFAULT NULL,
  `tax_type` tinyint(1) NOT NULL DEFAULT '1',
  `tax_amount` decimal(12,2) DEFAULT NULL,
  `tax_description` varchar(255) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `order_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tax_type` (`tax_type`),
  KEY `idx_order` (`order_id`),
  KEY `idx_order_type` (`order_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(145) DEFAULT NULL,
  `description` text,
  `show_features` tinyint(1) DEFAULT '1',
  `show_buttons` tinyint(1) DEFAULT '1',
  `price_description` text,
  `expiration_type` tinyint(1) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `trial_price` decimal(12,2) DEFAULT NULL,
  `trial_days` smallint(6) NOT NULL DEFAULT '0',
  `trial_period_unit` varchar(10) NOT NULL DEFAULT 'D',
  `trial_period_amount` smallint(6) DEFAULT NULL,
  `recurrence_count` tinyint(4) DEFAULT NULL,
  `special_price` decimal(12,2) DEFAULT NULL,
  `special_from_date` date DEFAULT NULL,
  `special_to_date` date DEFAULT NULL,
  `days` smallint(6) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `time_unit` varchar(10) NOT NULL DEFAULT 'D',
  `time_amount` mediumint(9) NOT NULL DEFAULT '1',
  `show_price_per_month` tinyint(1) NOT NULL DEFAULT '0',
  `max_pictures` smallint(4) NOT NULL DEFAULT '15',
  `max_videos` smallint(4) NOT NULL DEFAULT '5',
  `max_attachments` smallint(4) NOT NULL DEFAULT '5',
  `max_categories` smallint(4) NOT NULL DEFAULT '10',
  `popular` tinyint(1) NOT NULL DEFAULT '0',
  `max_offers` smallint(4) NOT NULL DEFAULT '0',
  `offer_count_type` tinyint(1) NOT NULL DEFAULT '0',
  `max_events` smallint(4) NOT NULL DEFAULT '0',
  `only_for_admin` tinyint(1) NOT NULL DEFAULT '0',
  `package_usergroup` varchar(255) DEFAULT '1',
  `bg_color` varchar(45) DEFAULT NULL,
  `text_color` varchar(45) DEFAULT NULL,
  `border_color` varchar(45) DEFAULT NULL,
  `show_disable_features` tinyint(1) DEFAULT '1',
  `max_quote_replies` int(11) NOT NULL DEFAULT '5',
  `renewal_price` decimal(12,2) DEFAULT NULL,
  `max_zipcodes` smallint(4) NOT NULL DEFAULT '0',
  `package_type` tinyint(1) DEFAULT '1',
  `max_trips` smallint(4) NOT NULL DEFAULT '0',
  `max_locations` smallint(4) NOT NULL DEFAULT '0',
  `max_description_length` smallint(4) DEFAULT '1200',
  `max_sounds` smallint(4) NOT NULL DEFAULT '5',
  `max_projects` smallint(4) DEFAULT '5',
  `max_activity_cities` smallint(4) DEFAULT '0',
  `max_activity_regions` smallint(4) DEFAULT '0',
  `max_activity_countries` smallint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_package_usergroup` (`package_usergroup`),
  KEY `idx_status` (`status`),
  KEY `idx_price` (`price`),
  KEY `idx_only_for_admin` (`only_for_admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_package_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) DEFAULT NULL,
  `feature` varchar(145) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_package_id` (`package_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_payments` (
  `payment_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL,
  `booking_id` int(10) DEFAULT NULL,
  `processor_type` varchar(100) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_date` date NOT NULL,
  `transaction_id` varchar(80) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` char(5) NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `response_code` varchar(45) DEFAULT NULL,
  `message` blob,
  `payment_status` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`payment_id`),
  KEY `NewIndex` (`order_id`),
  KEY `idx_booking_id` (`booking_id`),
  KEY `idx_processor_type` (`processor_type`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_payment_processors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `mode` enum('live','test') NOT NULL DEFAULT 'live',
  `timeout` int(7) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `displayfront` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `idx_displayfront` (`displayfront`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_payment_processor_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column_name` varchar(100) DEFAULT NULL,
  `column_value` varchar(255) DEFAULT NULL,
  `processor_id` int(11) NOT NULL,
  `column_mode` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_processor_id` (`processor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_payment_processor_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `processor_id` int(11) DEFAULT NULL,
  `app_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_processor_id` (`processor_id`),
  KEY `idx_app` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(65) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_country` (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(145) DEFAULT NULL,
  `description` text,
  `selected_params` text,
  `custom_params` text,
  `type` tinyint(1) DEFAULT NULL,
  `listing_status` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_request_quote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `additional_information` varchar(255) DEFAULT NULL,
  `summary` text,
  `status` int(11) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_category` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_request_quote_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_reply_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `text` text,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_quote_reply` (`quote_reply_id`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_request_quote_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_request_quote_question_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_question` (`question_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_request_quote_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `quote_id` int(11) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` decimal(12,2) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`),
  KEY `idx_quote` (`quote_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_review_pictures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reviewId` int(10) NOT NULL DEFAULT '0',
  `picture_info` varchar(255) DEFAULT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `picture_enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_reviewId` (`reviewId`),
  KEY `idx_status` (`picture_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_scheduled_notifications` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_search_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_type` tinyint(4) NOT NULL DEFAULT '1',
  `object_type` tinyint(4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `has_text` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_item_value` (`value`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_object_type` (`object_type`),
  KEY `idx_date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_search_logs_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_type` tinyint(4) NOT NULL DEFAULT '1',
  `object_type` tinyint(4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `has_text` tinyint(2) NOT NULL DEFAULT '0',
  `item_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unq` (`item_type`,`date`,`object_type`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_shipping_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(12,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `traceable` tinyint(4) DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `user_id` int(11) DEFAULT NULL,
  `default` tinyint(4) DEFAULT '0',
  `threshold` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_type` tinyint(4) NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `article_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_item_type` (`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_statistics_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_type` tinyint(4) NOT NULL DEFAULT '1',
  `date` date DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `article_id` int(11) DEFAULT NULL,
  `item_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unq` (`item_id`,`item_type`,`date`,`type`),
  KEY `idx_article` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `trial_start_date` date DEFAULT NULL,
  `trial_end_date` date DEFAULT NULL,
  `trial_amount` decimal(12,2) DEFAULT NULL,
  `subscription_id` varchar(255) DEFAULT NULL,
  `processor_type` varchar(100) NOT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `payment_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `time_unit` varchar(10) NOT NULL DEFAULT 'D',
  `time_amount` mediumint(9) NOT NULL DEFAULT '1',
  `status` tinyint(4) DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_payment` (`payment_id`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(200) DEFAULT NULL,
  `tax_type` tinyint(1) NOT NULL DEFAULT '1',
  `tax_amount` decimal(12,2) DEFAULT NULL,
  `tax_description` varchar(255) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_tax_type` (`tax_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_tax_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tax_id` (`tax_id`),
  KEY `idx_country` (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_tax_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_id` int(11) DEFAULT NULL,
  `app_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tax_id` (`tax_id`),
  KEY `idx_app` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_trips` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(100) NOT NULL DEFAULT '',
  `description` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `frequency` int(11) NOT NULL DEFAULT '0',
  `recurring_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `capacity` int(11) DEFAULT NULL,
  `organizer` varchar(100) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_alias` (`alias`),
  KEY `idx_frequency` (`frequency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_trip_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL,
  `trip_date` date NOT NULL,
  `trip_time` time DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `address` varchar(55) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_trip` (`trip_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_trip_capacity_overrides` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tripId` int(10) NOT NULL,
  `start_date` date DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tripId` (`tripId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_trip_dates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tripId` int(10) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tripId` (`tripId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_trip_pictures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tripId` int(10) NOT NULL DEFAULT '0',
  `picture_info` varchar(255) NOT NULL,
  `picture_path` varchar(255) NOT NULL,
  `picture_enable` tinyint(1) NOT NULL DEFAULT '1',
  `picture_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tripId` (`tripId`),
  KEY `idx_picture_enable` (`picture_enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_user_notifications` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) DEFAULT NULL,
  `notification_id` varchar(255) DEFAULT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_notification` (`notification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) DEFAULT NULL,
  `provider_type` tinyint(4) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT NULL,
  `activation_code` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` text,
  `main_subcategory` int(11) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `thumbnail` varchar(245) DEFAULT NULL,
  `transcript` text,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_video_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(250) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `video_UNIQUE` (`video_id`,`attribute_id`,`value`),
  KEY `idx_option_id` (`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{$wpdb->prefix}jbusinessdirectory_video_category` (
  `video_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`video_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        return $sql;
    }

/**
 * Retrieve queries to populate data in the directory
 *
 * @return void
 */
    private static function get_installation_data()
    {
        global $wpdb;

$sql = "

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_application_settings` (`id`, `name`, `value`, `text`, `description`) VALUES
(3, 'applicationsettings_id', '1', '', ''),
(4, 'company_name', 'JBusinessDirectory', 'LNG_DIRECTORY_NAME', 'LNG_NAME_BUSINESS_DETAILS'),
(5, 'company_email', '', 'LNG_DIRECTORY_EMAIL', 'LNG_EMAIL_BUSINESS_DETAILS'),
(6, 'currency_id', '143', 'LNG_CURRENCY_NAME', 'LNG_NAME_CURRENCY_DESCRIPTION'),
(7, 'country_ids', '', 'LNG_SELECT_ZIPCODE_COUNTRY', 'LNG_SELECT_ZIPCODE_COUNTRY_DESCRIPTION'),
(8, 'css_style', '', '', ''),
(9, 'css_module_style', 'style.css', '', ''),
(10, 'show_frontend_language', '1', '', ''),
(11, 'default_frontend_language', 'en-GB', '', ''),
(12, 'date_format_id', '2', 'LNG_DATE_FORMAT', 'LNG_DATE_FORMAT_DESCRIPTION'),
(13, 'enable_cache', '0', 'LNG_ENABLE_CACHE', 'LNG_ENABLE_CACHE_DESCRIPTION'),
(14, 'enable_packages', '0', 'LNG_ENABLE_PACKAGES', 'LNG_ENABLE_PACKAGES_DESCRIPTION'),
(15, 'edit_form_mode', '1', 'LNG_EDIT_FORM_MODE', 'LNG_EDIT_FORM_MODE_DESCRIPTION'),
(16, 'enable_ratings', '1', 'LNG_ENABLE_RATINGS', 'LNG_ENABLE_RATINGS_DESCRIPTION'),
(17, 'enable_reviews', '1', 'LNG_ENABLE_REVIEWS', 'LNG_ENABLE_REVIEWS_DESCRIPTION'),
(18, 'enable_offers', '1', 'LNG_ENABLE_OFFERS', 'LNG_ENABLE_OFFERS_DESCRIPTION'),
(19, 'enable_offer_coupons', '1', 'LNG_ENABLE_OFFER_COUPONS', 'LNG_ENABLE_OFFER_COUPONS_DESCRIPTION'),
(20, 'enable_events', '1', 'LNG_ENABLE_EVENTS', 'LNG_ENABLE_EVENTS_DESCRIPTION'),
(21, 'enable_seo', '1', 'LNG_SEO_MECHANISM', 'LNG_SEO_MECHANISM'),
(22, 'enable_rss', '0', 'LNG_ENABLE_RSS', 'LNG_ENABLE_RSS_DESCRIPTION'),
(23, 'enable_search_filter', '1', 'LNG_ENABLE_SEARCH_FILTER', 'LNG_ENABLE_SEARCH_FILTER_DESCRIPTION'),
(24, 'enable_search_letters', '0', '', ''),
(25, 'enable_reviews_users', '0', 'LNG_ENABLE_REVIEWS_USERS_ONLY', 'LNG_ENABLE_REVIEWS_USERS_ONLY_DESCRIPTION'),
(26, 'enable_socials', '1', 'LNG_ENABLE_SOCIALS', 'LNG_ENABLE_SOCIALS_DESCRIPTION'),
(27, 'enable_numbering', '1', 'LNG_ENABLE_NUMBERING', 'LNG_ENABLE_NUMBERING_DESCRIPTION'),
(28, 'enable_search_filter_offers', '1', 'LNG_ENABLE_SEARCH_FILTER_OFFERS', 'LNG_ENABLE_SEARCH_FILTER_OFFERS_DESCRIPTION'),
(29, 'enable_search_filter_events', '1', 'LNG_ENABLE_SEARCH_FILTER_EVENTS', 'LNG_ENABLE_SEARCH_FILTER_EVENTS_DESCRIPTION'),
(30, 'show_search_map', '1', 'LNG_SHOW_SEARCH_MAP', 'LNG_SHOW_SEARCH_MAP_DESCRIPTION'),
(31, 'show_search_description', '1', '', ''),
(32, 'show_details_user', '0', 'LNG_SHOW_DETAILS_ONLY_FOR_USERS_INFO', 'LNG_SHOW_DETAILS_ONLY_FOR_USERS_DESCRIPTION'),
(33, 'company_view', '5', 'LNG_COMPANY_VIEW', 'LNG_COMPANY_VIEW_DESCRIPTION'),
(34, 'category_view', '3', 'LNG_CATEGORIES_VIEW', 'LNG_CATEGORIES_VIEW_DESCRIPTION'),
(35, 'search_result_view', '6', 'LNG_SEARCH_RESULT_VIEW', 'LNG_SEARCH_RESULT_VIEW_DESCRIPTION'),
(36, 'captcha', '0', 'LNG_ENABLE_CAPTCHA', 'LNG_ENABLE_CAPTCHA_DESCRIPTION'),
(37, 'nr_images_slide', '5', '', ''),
(38, 'show_pending_approval', '0', 'LNG_SHOW_PENDING_APPROVAL', 'LNG_SHOW_PENDING_APPROVAL_DESCRIPTION'),
(39, 'allow_multiple_companies', '1', '', ''),
(40, 'meta_description', '', 'LNG_META_DESCRIPTION', 'LNG_META_DESCRIPTION_META'),
(41, 'meta_keywords', '', 'LNG_META_KEYWORDS', 'LNG_META_KEYWORDS_META'),
(42, 'meta_description_facebook', '', 'LNG_META_DESCRIPTION_FACEBOOK', 'LNG_META_DESCRIPTION_FACEBOOK_META'),
(43, 'limit_cities_regions', '0', 'LNG_LIMIT_CITIES_REGIONS', 'LNG_LIMIT_CITIES_DESCRIPTION'),
(44, 'metric', '1', 'LNG_METRIC', 'LNG_METRIC_DESCRIPTION'),
(45, 'user_location', '1', '', ''),
(46, 'search_type', '0', 'LNG_LISTING_SEARCH_FILTER', 'LNG_LISTING_SEARCH_FILTER_DESCRIPTION'),
(47, 'zipcode_search_type', '0', 'LNG_ZIPCODE_SEARCH_TYPE', 'LNG_ZIPCODE_SEARCH_TYPE_DESCRIPTION'),
(48, 'map_auto_show', '0', 'LNG_MAP_AUTO_SHOW', 'LNG_MAP_AUTO_SHOW_DESCRIPTION'),
(49, 'menu_item_id', '', 'LNG_MENU_ITEM_ID', 'LNG_MENU_ITEM_ID_SEO'),
(50, 'order_id', '', '', ''),
(51, 'order_email', '', '', ''),
(52, 'claim_business', '1', 'LNG_ENABLE_CLAIM_BUSINESS', 'LNG_ENABLE_CLAIM_BUSINESS_DESCRIPTION'),
(53, 'terms_conditions', '', 'LNG_COMPANY_TERMS_AND_CONDITIONS', 'LNG_TERMS_CONDITIONS_DESCRIPTION'),
(54, 'reviews_terms_conditions', '', 'LNG_REVIEW_TERMS_AND_CONDITIONS', 'LNG_REVIEWS_TERMS_CONDITIONS_DESCRIPTION'),
(55, 'contact_terms_conditions', '', 'LNG_CONTACT_TERMS_AND_CONDITIONS', 'LNG_CONTACT_TERMS_CONDITIONS_DESCRIPTION'),
(56, 'vat', '0', 'LNG_VAT', 'LNG_VAT_NUMBER_INVOICE'),
(57, 'expiration_day_notice', '0', 'LNG_EXPIRATION_DAYS_NOTICE', 'LNG_EXPIRATION_DAYS_NOTICE_DESCRIPTION'),
(58, 'show_cat_description', '1', 'LNG_SHOW_CAT_DESCRIPTION', 'LNG_SHOW_CAT_DESCRIPTION_DESCRIPTION'),
(59, 'direct_processing', '0', '', ''),
(60, 'max_video', '10', 'LNG_MAX_VIDEOS', 'LNG_MAX_VIDEOS_DESCRIPTION'),
(61, 'max_pictures', '15', 'LNG_MAX_PICTURES', 'LNG_MAX_PICTURES_DESCRIPTION'),
(62, 'show_secondary_locations', '0', 'LNG_SHOW_SECONDARY_LOCATIONS', 'LNG_SHOW_SECONDARY_LOCATIONS_DESCRIPTION'),
(63, 'search_view_mode', '0', 'LNG_DEFAULT_SEARCH_VIEW', 'LNG_DEFAULT_SEARCH_VIEW_DESCRIPTION'),
(64, 'address_format', '1', 'LNG_ADDRESS_FORMAT', 'LNG_ADDRESS_FORMAT_DESCRIPTION'),
(65, 'offer_search_results_grid_view', '0', 'LNG_OFFER_SEARCH_RESULT_GRID_VIEW', 'LNG_OFFER_SEARCH_RESULT_GRID_VIEW_DESCRIPTION'),
(66, 'offer_search_results_list_view', '1', 'LNG_OFFER_SEARCH_RESULT_LIST_VIEW', 'LNG_OFFER_SEARCH_RESULT_LIST_VIEW_DESCRIPTION'),
(67, 'events_search_results_list_view', '1', 'LNG_EVENTS_SEARCH_RESULT_LIST_VIEW', 'LNG_EVENTS_SEARCH_RESULT_LIST_VIEW_DESCRIPTION'),
(68, 'enable_multilingual', '0', 'LNG_ENABLE_MULTILINGUAL', 'LNG_ENABLE_MULTILINGUAL_DESCRIPTION'),
(69, 'offers_view_mode', '0', 'LNG_DEFAULT_OFFERS_VIEW', 'LNG_DEFAULT_OFFERS_VIEW_DESCRIPTION'),
(70, 'enable_geolocation', '1', 'LNG_ENABLE_GEOLOCATION', 'LNG_ENABLE_GEOLOCATION_DESCRIPTION'),
(71, 'enable_map_clustering', '1', 'LNG_ENABLE_MAP_CLUSTERING', 'LNG_ENABLE_MAP_CLUSTERING_DESCRIPTION'),
(72, 'add_url_id', '0', 'LNG_ADD_URL_ID', 'LNG_ADD_URL_ID_SEO'),
(73, 'add_url_language', '0', 'LNG_ADD_URL_LANGUAGE', 'LNG_ADD_URL_LANGUAGE_SEO'),
(74, 'currency_display', '1', 'LNG_CURRENCY_DISPLAY', 'LNG_CURRENCY_DISPLAY_DESCRIPTION'),
(75, 'amount_separator', '1', 'LNG_AMOUNT_SEPARATOR', 'LNG_AMOUNT_SEPARATOR_DESCRIPTION'),
(76, 'currency_location', '1', 'LNG_SHOW_CURRENCY', 'LNG_SHOW_CURRENCY_DESCRIPTION'),
(77, 'currency_symbol', '1', 'LNG_CURRENCY_SYMBOL', 'LNG_CURRENCY_SYMBOL_DESCRIPTION'),
(78, 'show_email', '1', 'LNG_SHOW_EMAIL', 'LNG_SHOW_EMAIL_DESCRIPTION'),
(79, 'enable_attachments', '1', 'LNG_ENABLE_ATTACHMENTS', 'LNG_ENABLE_ATTACHMENTS_DESCRIPTION'),
(80, 'order_search_listings', 'packageOrder desc', 'LNG_ORDER_SEARCH_LISTINGS', 'LNG_ORDER_SEARCH_LISTINGS_DESCRIPTION'),
(81, 'order_search_offers', '', 'LNG_ORDER_SEARCH_OFFERS', 'LNG_ORDER_SEARCH_OFFERS_DESCRIPTION'),
(82, 'order_search_events', '', 'LNG_ORDER_SEARCH_EVENTS', 'LNG_ORDER_SEARCH_EVENTS_DESCRIPTION'),
(83, 'events_search_view', '2', 'LNG_DEFAULT_EVENTS_VIEW', 'LNG_DEFAULT_EVENTS_VIEW_DESCRIPTION'),
(84, 'enable_bookmarks', '1', 'LNG_ENABLE_BOOKMARKS', 'LNG_ENABLE_BOOKMARKS_DESCRIPTION'),
(85, 'max_attachments', '5', 'LNG_MAX_ATTACHMENTS', 'LNG_MAX_ATTACHMENTS_DESCRIPTION'),
(86, 'max_categories', '10', 'LNG_MAX_CATEGORIES', 'LNG_MAX_CATEGORIES_DESCRIPTION'),
(87, 'max_offers', '20', 'LNG_MAX_OFFERS', 'LNG_MAX_OFFERS_DESCRIPTION'),
(88, 'max_events', '20', 'LNG_MAX_EVENTS', 'LNG_MAX_EVENTS_DESCRIPTION'),
(89, 'max_business', '20', 'LNG_MAX_BUSINESS_LISTINGS', 'LNG_MAX_BUSINESS_LISTINGS_REACHED'),
(90, 'time_format', 'h:i A', 'LNG_TIME_FORMAT', 'LNG_TIME_FORMAT_DESCRIPTION'),
(91, 'front_end_acl', '0', 'LNG_ENABLE_FRONT_END_ACL', 'LNG_ENABLE_FRONT_END_ACL_DESCRIPTION'),
(92, 'listing_url_type', '1', 'LNG_URL_TYPE', 'LNG_URL_TYPE_SEO'),
(93, 'search_result_grid_view', '1', 'LNG_SEARCH_RESULTS_GRID_VIEW', 'LNG_SEARCH_RESULTS_GRID_VIEW_DESCRIPTION'),
(94, 'facebook', 'http://www.facebook.com', 'LNG_FACEBOOK', 'LNG_FACEBOOK_BUSINESS_DETAILS'),
(95, 'twitter', 'http://www.twiter.com', 'LNG_TWITTER', 'LNG_TWITTER_BUSINESS_DETAILS'),
(96, 'googlep', 'http://www.googleplus.com', '', ''),
(97, 'linkedin', 'http://www.linkedin.com', 'LNG_LINKEDIN', 'LNG_LINKEDIN_BUSINESS_DETAILS'),
(98, 'youtube', 'http://www.youtube.com', 'LNG_YOUTUBE', 'LNG_YOUTUBE_BUSINESS_DETAILS'),
(99, 'logo', '/app/directorylogo-1519226466.jpg', 'LNG_LOGO', 'LNG_LOGO_BUSINESS_DETAILS'),
(100, 'map_latitude', '', 'LNG_LATITUDE', 'LNG_LATITUDE_DESCRIPTION'),
(101, 'map_longitude', '', 'LNG_LONGITUDE', 'LNG_LONGITUDE_DESCRIPTION'),
(102, 'map_zoom', '15', 'LNG_ZOOM', 'LNG_ZOOM_DESCRIPTION'),
(103, 'map_enable_auto_locate', '1', 'LNG_ENABLE_AUTO_LOCATE', 'LNG_ENABLE_AUTO_LOCATE_DESCRIPTION'),
(104, 'map_apply_search', '0', 'LNG_APPLY_SEARCH', 'LNG_APPLY_SEARCH_DESCRIPTION'),
(105, 'google_map_key', '', 'LNG_GOOGLE_MAP_KEY', 'LNG_GOOGLE_MAP_KEY_DESCRIPTION'),
(106, 'google_map_key_zipcode', '', 'LNG_GOOGLE_MAP_KEY_ZIPCODE', 'LNG_GOOGLE_MAP_KEY_ZIPCODE_DESCRIPTION'),
(107, 'submit_method', 'post', 'LNG_LISTING_SUBMIT_METHOD', 'LNG_LISTING_SUBMIT_METHOD_DESCRIPTION'),
(108, 'add_country_address', '1', 'LNG_ADD_COUNTRY_ADDRESS', 'LNG_ADD_COUNTRY_ADDRESS_DESCRIPTION'),
(109, 'usergroup', '2', 'LNG_CHOOSE_USERGROUP', 'LNG_CHOOSE_USERGROUP_DESCRIPTION'),
(110, 'business_usergroup', '', 'LNG_BUSINESS_USERGROUP', 'LNG_BUSINESS_USERGROUP_DESCRIPTION'),
(111, 'category_url_type', '1', 'LNG_CATEGORY_URL_TYPE', 'LNG_CATEGORY_URL_TYPE_SEO'),
(112, 'enable_menu_alias_url', '0', 'LNG_ADD_MENU_ALIAS_URL', 'LNG_ADD_MENU_ALIAS_URL_SEO'),
(113, 'url_menu_alias', '', 'LNG_URL_MENU_ALIAS_SEO', 'LNG_URL_MENU_ALIAS_SEO'),
(114, 'adaptive_height_gallery', '0', 'LNG_GALLERY_ADAPTIVE_HEIGHT', 'LNG_GALLERY_ADAPTIVE_HEIGHT_DESCRIPTION'),
(115, 'autoplay_gallery', '0', 'LNG_GALLERY_AUTOPLAY', 'LNG_GALLERY_AUTOPLAY_DESCRIPTION'),
(116, 'invoice_company_name', '', 'LNG_DIRECTORY_INVOICE_NAME', 'LNG_NAME_INVOICE'),
(117, 'invoice_company_address', '', 'LNG_ADDRESS', 'LNG_ADDRESS_INVOICE'),
(118, 'invoice_company_phone', '', 'LNG_TELEPHONE_NUMBER', 'LNG_TELEPHONE_NUMBER_INVOICE'),
(119, 'invoice_company_email', '', 'LNG_DIRECTORY_INVOICE_EMAIL', 'LNG_EMAIL_NUMBER_INVOICE'),
(120, 'invoice_vat', '0', 'LNG_VAT', 'LNG_VAT_NUMBER_INVOICE'),
(121, 'invoice_details', '', 'LNG_INVOICE_DETAILS', 'LNG_INVOICE_DETAILS_NUMBER_INVOICE'),
(122, 'invoice_prefix', '', 'LNG_PREFIX_NUMBER', 'LNG_PREFIX_NUMBER_NUMBER_INVOICE'),
(123, 'show_total_business_count', '0', 'LNG_SHOW_TOTAL_BUSINESS_COUNT', 'LNG_SHOW_TOTAL_BUSINESS_COUNT_DESCRIPTION'),
(124, 'enable_event_reservation', '0', 'LNG_ENABLE_EVENT_RESERVATION', 'LNG_ENABLE_EVENT_RESERVATION_DESCRIPTION'),
(125, 'enable_https_payment', '0', 'LNG_ENABLE_HTTPS_ON_PAYMENT', 'LNG_ENABLE_HTTPS_ON_PAYMENT_INFO'),
(126, 'search_fields', 'cp.name,cp.slogan,cg.name,cp.meta_description,cp.short_description,cp.phone,cp.address,cp.postalCode,cp.city,cp.county', 'LNG_SELECT_SEARCH_FIELDS', 'LNG_SELECT_SEARCH_FIELDS_DESCRIPTION'),
(127, 'search_filter_fields', 'categories,types,countries,regions,cities,starRating', 'LNG_SELECT_FILTER_FIELDS', 'LNG_SELECT_SEARCH_FILTER_DESCRIPTION'),
(128, 'url_fields', '', 'LNG_URL_FIELDS', 'LNG_URL_FIELDS'),
(129, 'show_pending_review', '0', 'LNG_SHOW_PENDING_REVIEW', 'LNG_SHOW_PENDING_REVIEW_DESCRIPTION'),
(130, 'show_view_count', '1', 'LNG_COMPANY_VIEW_COUNT', 'LNG_COMPANY_VIEW_COUNT_DESCRIPTION'),
(131, 'business_update_notification', '1', 'LNG_BUSINESS_UPDATE_NOTIFICATION', 'LNG_BUSINESS_UPDATE_NOTIFICATION_DESCRIPTION'),
(132, 'enable_offer_selling', '0', 'LNG_ENABLE_OFFER_SELLING', 'LNG_ENABLE_OFFER_SELLING_DESCRIPTION'),
(133, 'offer_view', '1', 'LNG_OFFER_VIEW', 'LNG_OFFER_VIEW_DESCRIPTION'),
(134, 'event_view', '1', 'LNG_EVENT_VIEW', 'LNG_EVENT_VIEW_DESCRIPTION'),
(135, 'allow_user_creation', '0', 'LNG_ALLOW_USER_CREATION', 'LNG_ALLOW_USER_CREATION_DESCRIPTION'),
(136, 'enable_event_appointments', '0', 'LNG_ENABLE_EVENT_APPOINTMENTS', 'LNG_ENABLE_EVENT_APPOINTMENTS_DESCRIPTION'),
(137, 'enable_services', '0', 'LNG_ENABLE_COMPANY_SERVICES', 'LNG_ENABLE_COMPANY_SERVICES_DESCRIPTION'),
(138, 'social_profile', '0', 'LNG_SOCIAL_PROFILE', 'LNG_SOCIAL_PROFILE_DESCRIPTION'),
(139, 'enable_reporting', '0', 'LNG_ENABLE_REPORTING', 'LNG_ENABLE_REPORTING_DESCRIPTION'),
(140, 'enable_event_subscription', '0', 'LNG_ENABLE_EVENT_SUBSCRIPTION', 'LNG_ENABLE_EVENT_SUBSCRIPTION_DESCRIPTION'),
(141, 'show_contact_form', '1', 'LNG_SHOW_CONTACT_FORM', 'LNG_SHOW_CONTACT_FORM_DESCRIPTION'),
(142, 'enable_attribute_category', '0', 'LNG_ENABLE_ATTRIBUTE_CATEGORY', 'LNG_ENABLE_ATTRIBUTE_CATEGORY_DESCRIPTION'),
(143, 'enable_criteria_category', '0', 'LNG_ENABLE_CRITERIA_CATEGORY', 'LNG_ENABLE_CRITERIA_CATEGORY_DESCRIPTION'),
(144, 'max_review_images', '6', 'LNG_MAX_IMAGES_REVIEW', 'LNG_MAX_IMAGES_REVIEW_DESCRIPTION'),
(145, 'apply_attr_offers', '0', '', ''),
(146, 'apply_attr_events', '0', '', ''),
(147, 'map_marker', '', 'LNG_MAP_MARKER', 'LNG_MAP_MARKER_DETAILS'),
(148, 'enable_link_following', '0', 'LNG_ENABLE_LINK_FOLLOWING', 'LNG_ENABLE_LINK_FOLLOWING_DESCRIPTION'),
(149, 'item_decouple', '0', 'LNG_ITEM_DECOUPLE', 'LNG_ITEM_DECOUPLE_DESCRIPTION'),
(150, 'search_filter_items', '5', 'LNG_SEARCH_FILTER_ITEM', 'LNG_SEARCH_FILTER_ITEM_DESCRIPTION'),
(151, 'price_list_view', '1', 'LNG_PRICE_LIST_VIEW', 'LNG_PRICE_LIST_VIEW_DESCRIPTION'),
(152, 'cover_width', '1000', 'LNG_COVER_WIDTH', 'LNG_COVER_WIDTH_DESCRIPTION'),
(153, 'cover_height', '400', 'LNG_COVER_HEIGHT', 'LNG_COVER_HEIGHT_DESCRIPTION'),
(154, 'gallery_height', '400', 'LNG_GALLERY_HEIGHT', 'LNG_GALLERY_HEIGHT_DESCRIPTION'),
(155, 'gallery_width', '400', 'LNG_GALLERY_WIDTH', 'LNG_GALLERY_WIDTH_DESCRIPTION'),
(156, 'logo_width', '800', 'LNG_LOGO_WIDTH', 'LNG_LOGO_WIDTH_DESCRIPTION'),
(157, 'logo_height', '800', 'LNG_LOGO_HEIGHT', 'LNG_LOGO_HEIGHT_DESCRIPTION'),
(158, 'enable_crop', '1', 'LNG_ENABLE_CROPPING', 'LNG_ENABLE_CROPPING_DESCRIPTION'),
(159, 'enable_resolution_check', '0', 'LNG_RESTRICT_IMAGE_SIZE', 'LNG_RESTRICT_IMAGE_SIZE_DESCRIPTION'),
(160, 'show_offer_free', '1', 'LNG_SHOW_OFFER_FREE_PRICE', 'LNG_SHOW_OFFER_FREE_PRICE_DESCRIPTION'),
(161, 'default_time_zone', '0', 'LNG_DEFAULT_TIME_ZONE', 'LNG_DEFAULT_TIME_ZONE_DESCRIPTION'),
(162, 'category_order', '1', 'LNG_CATEGORY_ORDER', 'LNG_CATEGORY_ORDER_DESCRIPTION'),
(163, 'custom_address', '{street_number} {address},{area},{city} {postal_code},{region},{province},{country}', 'LNG_CUSTOM_ADDRESS', 'LNG_ADDRESS_FORMAT_DESCRIPTION'),
(164, 'redirect_contact_url', '', 'LNG_REDIRECT_CONTACT_URL', 'LNG_REDIRECT_CONTACT_URL_DESC'),
(165, 'chat_port', '3000', 'LNG_CHAT_PORT', 'LNG_CHAT_PORT_DESCRIPTION'),
(166, 'firebase_server_key', '', 'LNG_FIREBASE_SERVER_KEY', 'LNG_FIREBASE_SERVER_KEY_DESCRIPTION'),
(167, 'event_booking_timeout', '', 'LNG_EVENT_BOOKING_TIMEOUT', 'LNG_EVENT_BOOKING_TIMEOUT_DESCRIPTION'),
(168, 'sms_domain', '', 'LNG_SMS_DOMAIN', 'LNG_SMS_DOMAIN_DESCRIPTION'),
(169, 'map_type', '1', 'LNG_MAP_TYPE', 'LNG_MAP_TYPE_DESCRIPTION'),
(170, 'bing_map_key', '', 'LNG_BING_MAP_KEY', 'LNG_BING_MAP_KEY_DESCRIPTION'),
(171, 'dir_list_limit', '20', 'LNG_DIR_LIST_LIMIT', 'LNG_DIR_LIST_LIMIT_DESCRIPTION'),
(172, 'show_custom_markers', '1', 'LNG_SHOW_CUSTOM_MARKERS', 'LNG_SHOW_CUSTOM_MARKERS_DESCRIPTION'),
(173, 'conference_view_mode', '', 'LNG_CONFERNCE_VIEW_MODE', 'LNG_CONFERNCE_VIEW_MODE'),
(174, 'sessions_view', '', 'LNG_SESSIONS_VIEW', 'LNG_SESSIONS_VIEW'),
(175, 'speakers_view', '', 'LNG_SPEAKERS_VIEW', 'LNG_SPEAKERS_VIEW'),
(176, 'listing_featured_bg', '', 'LNG_FEATURED_LISTING_BG', 'LNG_FEATURED_LISTING_BG'),
(177, 'show_grid_list_option', '1', 'LNG_SHOW_GRID_LIST_OPTIONS', 'LNG_SHOW_GRID_LIST_OPTIONS_DESCRIPTION'),
(178, 'enable_item_moderation', '1', 'LNG_ENABLE_ITEM_MODERATION', 'LNG_ENABLE_ITEM_MODERATION_DESCRIPTION'),
(179, 'enable_automated_moderation', '0', 'LNG_ENABLE_AUTOMATED_MODERATION', 'LNG_ENABLE_AUTOMATED_MODERATION_DESCRIPTION'),
(180, 'moderate_threshold', '0', 'LNG_MODERATE_THRESHOLD', 'LNG_MODERATE_THRESHOLD_DESCRIPTION'),
(181, 'events_search_results_grid_view', '1', 'LNG_EVENTS_SEARCH_RESULT_GRID_VIEW', 'LNG_EVENTS_SEARCH_RESULT_GRID_VIEW_DESCRIPTION'),
(182, 'show_open_status', '1', 'LNG_SHOW_OPEN_STATUS', 'LNG_SHOW_OPEN_STATUS_DESCRIPTION'),
(183, 'currency_converter_api', '', 'LNG_CURRENCY_CONVERTER_API_KEY', 'LNG_CURRENCY_CONVERTER_API_KEY_DESCRIPTION'),
(184, 'feature_map_marker', '', 'LNG_FEATURE_MAP_MARKER', 'LNG_FEATURE_MAP_MARKER_DETAILS'),
(185, 'offer_submit_method', 'post', 'LNG_OFFER_SUBMIT_METHOD', 'LNG_OFFER_SUBMIT_METHOD_DESCRIPTION'),
(186, 'event_submit_method', 'post', 'LNG_EVENT_SUBMIT_METHOD', 'LNG_EVENT_SUBMIT_METHOD_DESCRIPTION'),
(187, 'offer_search_filter_items', '5', 'LNG_OFFER_SEARCH_FILTER_ITEM', 'LNG_OFFER_SEARCH_FILTER_ITEM_DESCRIPTION'),
(188, 'event_search_filter_items', '5', 'LNG_EVENT_SEARCH_FILTER_ITEM', 'LNG_EVENT_SEARCH_FILTER_ITEM_DESCRIPTION'),
(189, 'offer_search_filter_fields', 'categories,types,countries,regions,cities,starRating', 'LNG_OFFER_SELECT_FILTER_FIELDS', 'LNG_OFFER_SELECT_SEARCH_FILTER_DESCRIPTION'),
(190, 'event_search_filter_fields', 'categories,types,countries,regions,cities,starRating', 'LNG_EVENT_SELECT_FILTER_FIELDS', 'LNG_EVENT_SELECT_SEARCH_FILTER_DESCRIPTION'),
(191, 'offer_search_type', '0', 'LNG_OFFER_SEARCH_FILTER', 'LNG_OFFER_SEARCH_FILTER_DESCRIPTION'),
(192, 'event_search_type', '0', 'LNG_EVENT_SEARCH_FILTER', 'LNG_EVENT_SEARCH_FILTER_DESCRIPTION'),
(193, 'enable_shipping', '0', 'LNG_ENABLE_SHIPPING', 'LNG_ENABLE_SHIPPING_DESCRIPTION'),
(194, 'image_display', '1', 'LNG_IMAGE_DISPLAY', 'LNG_IMAGE_DISPLAY'),
(195, 'front_end_meta_data', '0', 'LNG_ENABLE_FRONT_END_META_DATA', 'LNG_ENABLE_FRONT_END_META_DATA_DSCR'),
(196, 'show_contact_cards', '0', 'LNG_SHOW_CONTACT_CARDS', 'LNG_SHOW_CONTACT_CARDS_DESC'),
(197, 'enable_announcements', '0', 'LNG_ENABLE_ANNOUNCEMENTS', 'LNG_ENABLE_ANNOUNCEMENTS'),
(198, 'enable_price_list', '1', 'LNG_ENABLE_PRICE_LIST', 'LNG_ENABLE_PRICE_LIST_DESCRIPTION'),
(199, 'listing_auto_save', '0', 'LNG_LISTING_AUTO_SAVE', 'LNG_LISTING_AUTO_SAVE_DESCRIPTION'),
(200, 'auto_save_interval', '120000', 'LNG_AUTO_SAVE_INTERVAL', 'LNG_AUTO_SAVE_INTERVAL_DESCRIPTION'),
(201, 'content_responsible', '', 'LNG_CONTENT_RESPONSIBLE_PERSON', 'LNG_CONTENT_RESPONSIBLE_PERSON'),
(202, 'display_attributes_packages', '1', 'LNG_DISPLAY_ATTRIBUTES_PACKAGES', 'LNG_DISPLAY_ATTRIBUTES_PACKAGES_DESCRIPTION'),
(203, 'show_claimed', '1', 'LNG_SHOW_CLAIMED', 'LNG_SHOW_CLAIMED_DESCRIPTION'),
(204, 'enable_map_gdpr', '0', 'LNG_ENABLE_MAP_GDPR', 'LNG_ENABLE_MAP_GDPR_DESCRIPTION'),
(205, 'search_filter_view', '1', 'LNG_SEARCH_FILTER_VIEW', 'LNG_SEARCH_FILTER_VIEW_DESCRIPTION'),
(206, 'search_filter_view_offers', '1', 'LNG_SEARCH_FILTER_VIEW_OFFERS', 'LNG_SEARCH_FILTER_VIEW_OFFERS_DESCRIPTION'),
(207, 'search_filter_view_events', '1', 'LNG_SEARCH_FILTER_VIEW_EVENTS', 'LNG_SEARCH_FILTER_VIEW_OFFERS_DESCRIPTION'),
(208, 'search_filter_type', '2', 'LNG_SEARCH_FILTER_TYPE', 'LNG_SEARCH_FILTER_TYPE_DESCRIPTION'),
(209, 'enable_request_quote', '1', 'LNG_ENABLE_REQUEST_QUOTE', 'LNG_ENABLE_REQUEST_QUOTE_DESCRIPTION'),
(210, 'facebook_client_id', '', 'LNG_FACEBOOK_CLIENT_ID', 'LNG_FACEBOOK_CLIENT_ID_DESCRIPTION'),
(211, 'facebook_client_secret', '', 'LNG_FACEBOOK_CLIENT_SECRET', 'LNG_FACEBOOK_CLIENT_SECRET_DESCRIPTION'),
(212, 'google_client_id', '', 'LNG_GOOGLE_CLIENT_ID', 'LNG_GOOGLE_CLIENT_ID_DESCRIPTION'),
(213, 'google_client_secret', '', 'LNG_GOOGLE_CLIENT_SECRET', 'LNG_GOOGLE_CLIENT_SECRET_DESCRIPTION'),
(214, 'share_reviews', '0', 'LNG_SHARE_REVIEW', 'LNG_SHARE_REVIEW_DESCRIPTION'),
(215, 'open_business_website', '0', 'LNG_OPEN_BUSINESS_URL', 'LNG_OPEN_BUSINESS_URL_DESCRIPTION'),
(216, 'open_listing_on_new_tab', '0', 'LNG_OPEN_LISTING_ON_NEW_TAB', 'LNG_OPEN_LISTING_ON_NEW_TAB_DESCRIPTION'),
(217, 'request_quote_usergroup', '2', 'LNG_CHOOSE_REQUEST_QUOTE_USERGROUP', 'LNG_REQUEST_QUOTE_CHOOSE_USERGROUP_DESCRIPTION'),
(218, 'request_quote_radius', '100', 'LNG_REQUEST_QUOTE_RADIUS', 'LNG_REQUEST_QUOTE_RADIUS_DESCRIPTION'),
(219, 'package_date', '', 'LNG_PACKAGE_FIXED_DATE', 'LNG_PACKAGE_FIXED_DATE_DESCRIPTION'),
(220, 'lock_custom_fields', '1', 'LNG_LOCK_CUSTOM_FIELDS', 'LNG_LOCK_CUSTOM_FIELDS_DESCRIPTION'),
(221, 'show_alias', '1', 'LNG_SHOW_ALIAS', 'LNG_SHOW_ALIAS_DESCRIPTION'),
(222, 'show_apply_discount', '1', 'LNG_SHOW_APPLY_DISCOUNT', 'LNG_SHOW_APPLY_DISCOUNT_DESCRIPTION'),
(223, 'enable_messages', '1', 'LNG_ENABLE_MESSAGES', 'LNG_ENABLE_MESSAGES_DESCRIPTION'),
(224, 'enable_campaigns', '1', 'LNG_ENABLE_CAMPAIGNS', 'LNG_ENABLE_CAMPAIGNS_DESC'),
(225, 'enable_articles', '1', 'LNG_ENABLE_ARTICLES', 'LNG_ENABLE_ARTICLES_DESC'),
(226, 'enable_request_quote_app', '1', 'LNG_ENABLE_REQUEST_QUOTE_APP', 'LNG_ENABLE_REQUEST_QUOTE_APP_DESC'),
(227, 'enable_projects', '1', 'LNG_ENABLE_PROJECTS', 'LNG_ENABLE_PROJECTS_DESC'),
(228, 'mobile_usergroup', '2', 'LNG_CHOOSE_MOBILE_USERGROUP', 'LNG_CHOOSE_MOBILE_USERGROUP_DESCRIPTION'),
(229, 'search_redirect_url', '', 'LNG_SEARCH_REDIRECT_URL', 'LNG_SEARCH_REDIRECT_URL_DESC'),
(230, 'max_listing_events_display', '6', 'LNG_MAX_LISTING_EVENTS_DISPLAY', 'LNG_MAX_LISTING_EVENTS_DISPLAY_DESCRIPTION'),
(231, 'default_processor_types', '', 'LNG_SELECT_DEFAULT_PAYMENT_PROCESSORS', 'LNG_SELECT_DEFAULT_PAYMENT_PROCESSORS_DESCRIPTION'),
(232, 'trail_weeks_dates', '', 'LNG_TRAIL_WEEKS_DATES', 'LNG_TRAIL_WEEKS_DATES_DESCRIPTION'),
(233, 'marker_size', '', 'LNG_MARKER_SIZE', 'LNG_MARKER_SIZE_DESCRIPTION'),
(234, 'type_allowed_registering', '', 'LNG_SELECT_LISTING_TYPE_ALLOWED_TO_REGISTER', 'LNG_SELECT_LISTING_TYPE_ALLOWED_TO_REGISTER_DESCRIPTION'),
(235, 'mix_results', '0', 'LNG_MIX_RESULTS', 'LNG_MIX_RESULTS_DESCRIPTION'),
(236, 'mix_results_offers', '0', 'LNG_MIX_RESULTS', 'LNG_MIX_RESULTS_DESCRIPTION'),
(237, 'mix_results_events', '0', 'LNG_MIX_RESULTS', 'LNG_MIX_RESULTS_DESCRIPTION'),
(238, 'no_image', '/no_image.jpg', 'LNG_NO_IMAGE', 'LNG_NO_IMAGE_DETAILS'),
(239, 'generate_auto_user', '0', 'LNG_GENERATE_AUTO_USER', 'LNG_GENERATE_AUTO_USER_DESCRIPTION'),
(240, 'number_of_decimals', '2', 'LNG_NUMBER_OF_DECIMLAS', 'LNG_NUMBER_OF_DECIMLAS_DESCRIPTION'),
(241, 'listing_category_display', '1', 'LNG_LISTING_CATEGORY_DISPLAY', 'LNG_LISTING_CATEGORY_DISPLAY_DESCRIPTION'),
(242, 'show_secondary_locations_search', '0', 'LNG_SHOW_SECONDARY_LOCATIONS_SEARCH', 'LNG_SHOW_SECONDARY_LOCATIONS_SEARCH_DESCRIPTION'),
(243, 'allow_contribute', '0', 'LNG_ALLOW_CONTRIBUTE', 'LNG_ALLOW_CONTRIBUTE_DESCRIPTION'),
(244, 'business_cp_style', '1', 'LNG_BUSINESS_CP_STYLE', 'LNG_BUSINESS_CP_STYLE_DESCRIPTION'),
(245, 'user_cp_style', '2', 'LNG_USER_CP_STYLE', 'LNG_USER_CP_STYLE_DESCRIPTION'),
(246, 'session_view', '2', 'LNG_SESSION_VIEW', 'LNG_SESSION_VIEW_DESCRIPTION'),
(247, 'linkedin_client_id', '', 'LNG_LINKEDIN_CLIENT_ID', 'LNG_LINKEDIN_CLIENT_ID_DESCRIPTION'),
(248, 'linkedin_client_secret', '', 'LNG_LINKEDIN_CLIENT_SECRET', 'LNG_LINKEDIN_CLIENT_SECRET_DESCRIPTION'),
(249, 'autocomplete_config', '{\"google\":{\"street_number\":[\"street_number\"],\"route\":[\"route\"],\"locality\":[\"locality\",\"administrative_area_level_1\"],\"area_id\":[\"administrative_area_level_2\"],\"administrative_area_level_1\":[\"administrative_area_level_1\"],\"administrative_area_level_2\":[\"administrative_area_level_2\"],\"country\":[\"country\"],\"postal_code\":[\"postal_code\"]},\"bing\":{\"street_number\":[\"street_number\"],\"route\":[\"addressLine\"],\"locality\":[\"city\"],\"area_id\":[\"district\"],\"administrative_area_level_1\":[\"adminDistrict\"],\"administrative_area_level_2\":[\"district\"],\"country\":[\"countryRegion\"],\"postal_code\":[\"postalCode\"]},\"openstreet\":{\"street_number\":[\"street_number\",\"house_number\"],\"route\":[\"street\",\"road\",\"suburb\"],\"locality\":[\"city\",\"town\"],\"area_id\":[\"county\"],\"administrative_area_level_1\":[\"county\"],\"administrative_area_level_2\":[\"state\"],\"country\":[\"country\"],\"postal_code\":[\"postcode\"]}}', 'LNG_AUTOCOMPLETE_CONFIGURATION', 'LNG_AUTOCOMPLETE_CONFIGURATION_DESCRIPTION'),
(250, 'vat_config', '', 'LNG_VAT_CONFIGURATION', 'LNG_VAT_CONFIGURATION_DESCRIPTION'),
(251, 'enable_elastic_search', '0', 'LNG_ENABLE_ELASTIC_SEARCH', 'LNG_ENABLE_ELASTIC_SEARCH_DESCRIPTION'),
(252, 'elastic_search_version', '7', 'LNG_ELASTIC_VERSION', 'LNG_ELASTIC_VERSION_DESCRIPTION'),
(253, 'elastic_endpoint', NULL, 'LNG_ELASTIC_ENDPOINT', 'LNG_ELASTIC_ENDPOINT_DESCRIPTION'),
(254, 'elastic_search_endpoint', NULL, 'LNG_ELASTIC_SEARCH_ENDPOINT', 'LNG_ELASTIC_SEARCH_ENDPOINT_DESCRIPTION'),
(255, 'elastic_search_index', NULL, 'LNG_ELASTIC_SEARCH_INDEX', 'LNG_ELASTIC_SEARCH_INDEX_DESCRIPTION'),
(256, 'elastic_search_user', NULL, 'LNG_ELASTIC_SEARCH_USER', 'LNG_ELASTIC_SEARCH_USER_DESCRIPTION'),
(257, 'elastic_search_password', NULL, 'LNG_ELASTIC_SEARCH_PASSWORD', 'LNG_ELASTIC_SEARCH_PASSWORD_DESCRIPTION'),
(258, 'show_recommended', '1', 'LNG_SHOW_RECOMMENDED', 'LNG_SHOW_RECOMMENDED_DESCRIPTION'),
(259, 'package_upgrade_banner', '0', 'LNG_SHOW_BANNER_UPGRADE_PACKAGE', 'LNG_BANNER_UPGRADE_PACKAGE_DESC'),
(260, 'show_privacy', '0', 'LNG_PRIVACY_POLICY', 'LNG_PRIVACY_POLICY_DESC'),
(261, 'packages_vat_apply', '0', 'LNG_APPLY_VAT_DIRECTLY', 'LNG_APPLY_VAT_DIRECTLY_DESC'),
(262, 'category_url_naming', 'category', 'LNG_CATEGORY_URL_NAMING', 'LNG_CATEGORY_URL_NAMING_DESC'),
(263, 'offer_category_url_naming', 'offer-category', 'LNG_OFFER_CATEGORY_URL_NAMING', 'LNG_OFFER_CATEGORY_URL_NAMING_DESC'),
(264, 'event_category_url_naming', 'event-category', 'LNG_EVENT_CATEGORY_URL_NAMING', 'LNG_EVENT_CATEGORY_URL_NAMING_DESC'),
(265, 'offer_url_naming', 'offer', 'LNG_OFFER_URL_NAMING', 'LNG_OFFER_URL_NAMING_DESC'),
(266, 'event_url_naming', 'event', 'LNG_EVENT_URL_NAMING', 'LNG_EVENT_URL_NAMING_DESC'),
(267, 'city_url_naming', 'city', 'LNG_CITY_URL_NAMING', 'LNG_CITY_URL_NAMING_DESC'),
(268, 'region_url_naming', 'region', 'LNG_REGION_URL_NAMING', 'LNG_REGION_URL_NAMING_DESC'),
(269, 'conference_url_naming', 'conference', 'LNG_CONFERENCE_URL_NAMING', 'LNG_CONFERENCE_URL_NAMING_DESC'),
(270, 'conference_session_url_naming', 'session', 'LNG_CONFERENCE_SESSION_URL_NAMING', 'LNG_CONFERENCE_SESSION_URL_NAMING_DESC'),
(271, 'speaker_url_naming', 'speaker', 'LNG_SPEAKER_URL_NAMING', 'LNG_SPEAKER_URL_NAMING_DESC'),
(272, 'projects_style', '1', 'LNG_BUSINESS_PROJECTS_STYLE', 'LNG_BUSINESS_PROJECTS_STYLE_DESCRIPTION'),
(273, 'projects_show_images', '1', 'LNG_BUSINESS_PROJECTS_SHOW_ADDITIONAL_IMAGES', 'LNG_BUSINESS_PROJECTS_SHOW_ADDITIONAL_IMAGES_DESCRIPTION'),
(274, 'edit_ratings', '0', 'LNG_EDIT_RATINGS', 'LNG_EDIT_RATINGS_DESCRIPTION'),
(275, 'show_cp_suggestions', '0', 'LNG_SHOW_CP_SUGGESTIONS', 'LNG_SHOW_CP_SUGGESTIONS_DESCRIPTION'),
(276, 'privacy_policy', '', 'LNG_PRIVACY_POLICY', 'LNG_PRIVACY_POLICY'),
(277, 'enable_linked_listings', '0', 'LNG_ENABLE_LINKED_LISTINGS', 'LNG_ENABLE_LINKED_LISTINGS_DESC'),
(278, 'speaker_img_width', '600', 'LNG_SPEAKER_IMG_WIDTH', 'LNG_LNG_SPEAKER_IMG_WIDTH_DESCRIPTION'),
(279, 'speaker_img_height', '600', 'LNG_SPEAKER_IMG_HEIGHT', 'LNG_SPEAKER_IMG_HEIGHT_DESCRIPTION'),
(281, 'order_by_fields', 'packageOrder,id_desc,id_asc,companyName_asc,companyName_desc,city,review_score,distance', 'LNG_SELECT_ORDER_BY_FIELDS', 'LNG_SELECT_ORDER_BY_DESCRIPTION'),
(282, 'default_bg_listing', '', 'LNG_DEFAULT_BG_LISTING', 'LNG_DEFAULT_BG_LISTING_DESC'),
(283, 'video_url_naming', 'video', 'LNG_VIDEO_URL_NAMING', 'LNG_VIDEO_URL_NAMING_DESC'),
(284, 'show_offer_price_list', '0', 'LNG_SHOW_PRICE_LIST', 'LNG_SHOW_PRICE_LIST_DESC'),
(285, 'offer_price_list_view_style', '1', 'LNG_OFFER_PRICE_LIST_VIEW_STYLE', 'LNG_OFFER_PRICE_LIST_VIEW_STYLE_DESC'),
(286, 'allow_business_view_style_change', '0', 'LNG_BUSINESS_VIEW_CHANGE', 'LNG_BUSINESS_VIEW_CHANGE_DESC'),
(287, 'location_map_marker', '', 'LNG_LOCATION_MAP_MARKER', 'LNG_LOCATION_MAP_MARKER_DETAILS'),
(288, 'show_terms_conditions_article', '0', 'LNG_TERMS_CONDITIONS_ARTICLE', 'LNG_TERMS_CONDITIONS_ARTICLE_DESC'),
(289, 'terms_conditions_article_id', '', 'LNG_TERMS_CONDITIONS_ARTICLE_ID', 'LNG_TERMS_CONDITIONS_ARTICLE_DESC'),
(290, 'reviews_terms_conditions_article_id', '', 'LNG_REVIEWS_TERMS_CONDITIONS_ARTICLE_ID', 'LNG_REVIEWS_TERMS_CONDITIONS_ARTICLE_ID_DESC'),
(291, 'contact_terms_conditions_article_id', '', 'LNG_CONTACT_TERMS_CONDITIONS_ARTICLE_ID', 'LNG_CONTACT_TERMS_CONDITIONS_ARTICLE_ID_DESC'),
(292, 'privacy_policy_article_id', '', 'LNG_PRICACY_POLICY_ARTICLE_ID', 'LNG_PRICACY_POLICY_ARTICLE_ID_DESC'),
(293, 'appointments_commission', '', 'LNG_APPOINTMENTS_COMMISSION', 'LNG_APPOINTMENTS_COMMISSION_DESC'),
(294, 'offer_selling_commission', '', 'LNG_OFFER_SELLING_COMMISSION', 'LNG_OFFER_SELLING_COMMISSION_DESC'),
(295, 'event_tickets_commission', '', 'LNG_EVENT_TICKETS_COMMISSION', 'LNG_EVENT_TICKETS_COMMISSION_DESC'),
(296, 'display_free_packages_bellow', '0', 'LNG_DISPLAY_FREE_PACKAGES_BELLOW', 'LNG_DISPLAY_FREE_PACKAGES_BELLOW_DESC'),
(297, 'display_packages_by_period', '0', 'LNG_DISPLAY_PACKAGES_BY_PERIOD', 'LNG_DISPLAY_PACKAGES_BY_PERIOD_DESC'),
(298, 'custom_registration', '1', 'LNG_CUSTOM_REGISTRATION', 'LNG_CUSTOM_REGISTRATION_DESC'),
(299, 'last_schema_check_version', '', 'LNG_SCHEMA_VERSION_CHECK', 'LNG_SCHEMA_VERSION_CHECK_DESC'),
(300, 'enable_advanced_search_filter', '0', 'LNG_ENABLE_ADVANCED_FILTER', 'LNG_ENABLE_ADVANCED_FILTER_DESC'),
(301, 'videos_url_naming', 'videos', 'LNG_VIDEOS_URL_NAMING', 'LNG_VIDEOS_URL_NAMING_DESC'),
(302, 'show_verified_review_badge', '0', 'LNG_SHOW_VERIFIED_REVIEW_BADGE', 'LNG_SHOW_VERIFIED_REVIEW_BADGE_DESCRIPTION'),
(303, 'user_login_position', '1', 'LNG_USER_LOGIN_POSITION', 'LNG_USER_LOGIN_POSITION_DESCRIPTION'),
(304, 'service_notification_days', '5', 'LNG_SERVICE_NOTIFICATION_DAYS', 'LNG_SERVICE_NOTIFICATION_DAYS_DESCRIPTION'),
(305, 'quote_request_type', '0', 'LNG_QUOTE_REQUEST_TYPE', 'LNG_QUOTE_REQUEST_TYPE_DESCRIPTION'),
(306, 'quote_search_type', '0', 'LNG_QUOTE_SEARCH_TYPE', 'LNG_QUOTE_SEARCH_TYPE_DESCRIPTION'),
(307, 'quotes_search_filter_fields', 'categories,dates,companies', 'LNG_SELECT_FILTER_FIELDS', 'LNG_SELECT_SEARCH_FILTER_DESCRIPTION'),
(308, 'redirect_to_listing', '0', 'LNG_REDIRECT_TO_LISTING', 'LNG_REDIRECT_TO_LISTING_DESCRIPTION'),
(309, 'lazy_loading', '0', 'LNG_LAZY_LOADING', 'LNG_LAZY_LOADING_DESCRIPTION'),
(310, 'trips_url_naming', 'trips', 'LNG_TRIPS_URL_NAMING', 'LNG_TRIPS_URL_NAMING_DESC'),
(311, 'trip_url_naming', 'trip', 'LNG_TRIP_URL_NAMING', 'LNG_TRIP_URL_NAMING_DESC'),
(312, 'search_categories', '', 'LNG_SELECT_SEARCH_CATEGORIES', 'LNG_SELECT_SEARCH_CATEGORIES_DESCRIPTION'),
(313, 'show_top_filter', '0', 'LNG_SHOW_TOP_FILTER', 'LNG_SHOW_TOP_FILTER_DESCRIPTION'),
(314, 'paid_business_usergroup', '', 'LNG_PAID_BUSINESS_USERGROUP', 'LNG_PAID_BUSINESS_USERGROUP_DESCRIPTION'),
(315, 'max_sound', '5', 'LNG_MAX_SOUNDS', 'LNG_MAX_SOUNDS_DESCRIPTION'),
(316, 'enable_recurring_events', '1', 'LNG_ENABLE_RECURRING_EVENTS', 'LNG_ENABLE_RECURRING_EVENTS_DESCRIPTION'),
(327, 'offers_search_filter_type', '1', 'LNG_OFFERS_SEARCH_FILTER_TYPE', 'LNG_OFFERS_SEARCH_FILTER_TYPE_DESCRIPTION'),
(328, 'events_search_filter_type', '1', 'LNG_EVENTS_SEARCH_FILTER_TYPE', 'LNG_EVENTS_SEARCH_FILTER_TYPE_DESCRIPTION'),
(329, 'search_results_loading', '0', 'LNG_SEARCH_RESULTS_LOADING', 'LNG_SEARCH_RESULTS_LOADING_DESCRIPTION'),
(330, 'split_edit_form', '0', 'LNG_SPLIT_EDIT_FORM', 'LNG_SPLIT_EDIT_FORM_DESCRIPTION'),
(331, 'enable_apply_with_price', '0', 'LNG_ENABLE_APPLY_WITH_PRICE', 'LNG_ENABLE_APPLY_WITH_PRICE_DESCRIPTION'),
(332, 'listings_display_info', 'opening_hours', 'LNG_LISTINGS_DISPLAY_INFO', 'LNG_LISTINGS_DISPLAY_INFO_DESCRIPTION'),
(333, 'show_custom_attributes', '1', 'LNG_SHOW_CUSTOM_ATTRIBUTES', 'LNG_SHOW_CUSTOM_ATTRIBUTES_DESCRIPTION'),
(334, 'second_order_search_listings', 'viewCount desc', 'LNG_SECOND_ORDER_SEARCH_LISTINGS', 'LNG_SECOND_ORDER_SEARCH_LISTINGS_DESCRIPTION'),
(335, 'enable_listing_editors', '0', 'LNG_ENABLE_LISTING_EDITORS', 'LNG_ENABLE_LISTING_EDITORS_DESCRIPTION'),
(336, 'enable_videos', '1', 'LNG_ENABLE_VIDEOS', 'LNG_ENABLE_VIDEOS_DESC'),
(337, 'max_description_length', '1200', 'LNG_MAX_OFFERS', 'LNG_MAX_OFFERS_DESCRIPTION'),
(338, 'max_short_description_length', '255', 'LNG_MAX_OFFERS', 'LNG_MAX_OFFERS_DESCRIPTION'),
(339, 'max_slogan_length', '255', 'LNG_MAX_OFFERS', 'LNG_MAX_OFFERS_DESCRIPTION'),
(340, 'cities_regions_order', '0', 'LNG_CITIES_REGIONS_ORDER', 'LNG_CITIES_REGIONS_ORDER_DESCRIPTION'),
(341, 'enable_simple_form', '0', 'LNG_ENABLE_SIMPLE_FORM', 'LNG_ENABLE_SIMPLE_FORM_DESCRIPTION'),
(342, 'simple_form_fields', 'type,website,phone,email,address,logo,cover_image,slogan,short_description,description,category,social,custom_attributes', 'LNG_SELECT_SIMPLE_FORM_FIELDS', 'LNG_SELECT_SIMPLE_FORM_FIELDS_DESCRIPTION'),
(343, 'available_languages', '', 'LNG_AVAILABLE_LANGUAGES', 'LNG_AVAILABLE_LANGUAGES_DESCRIPTION');


INSERT INTO `{$wpdb->prefix}jbusinessdirectory_attribute_types` (`id`, `code`, `name`) VALUES
(1, 'input', 'Input'),
(2, 'select_box', 'Select Box'),
(3, 'checkbox', 'Checkbox(Multiple Select)'),
(4, 'radio', 'Radio(Single Select)'),
(5, 'header', 'Header'),
(6, 'textarea', 'Textarea'),
(7, 'link', 'Link'),
(8, 'multiselect', 'Multiple Select Box');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_campaigns` (`id`, `name`, `initial_budget`, `budget`, `status`, `published`, `company_id`) VALUES
(1, 'Vintage campaign', '2000.00', '2000.00', 1, 1, 8);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_campaign_has_plans` (`campaign_id`, `campaign_plan_id`, `nr_clicks`) VALUES
(1, 1, 0),
(1, 2, 0),
(1, 3, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_campaign_plans` (`id`, `name`, `image`, `click_price`, `published`) VALUES
(1, 'Home page', '', '1.00', 1),
(2, 'Search results', '', '0.50', 1),
(3, 'Listing details', '', '0.50', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_categories` (`id`, `parent_id`, `lft`, `rgt`, `level`, `type`, `name`, `alias`, `description`, `published`, `imageLocation`, `markerLocation`, `color`, `icon`, `path`, `clickCount`, `meta_title`, `meta_description`, `meta_keywords`, `iconImgLocation`, `keywords`, `user_as_container`) VALUES
(1, 0, 0, 410, 0, 0, 'root', '', '1', 1, '', NULL, '', '0', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 1, 31, 32, 1, 1, 'Books', 'books-1-1', '', 1, '/categories/Reading-1519301686.jpg', NULL, '', 'book', 'books-1-1', 0, '', '', '', NULL, NULL, NULL),
(34, 7, 83, 84, 2, 1, 'Software', 'software', '', 1, '', NULL, '', '', 'electronics-1/software', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 8, 129, 130, 2, 1, 'Women', 'women', '', 1, '', NULL, '', '', 'fashion-1/women', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 8, 125, 126, 2, 1, 'Man', 'man', '', 1, '', NULL, '', '', 'fashion-1/man', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 8, 123, 124, 2, 1, 'Kids & Baby', 'kids-&-baby', '', 1, '', NULL, '', '', 'fashion-1/kids-&-baby', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 8, 127, 128, 2, 1, 'Shoes', 'shoes', '', 1, '', NULL, '', '', 'fashion-1/shoes', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 29, 153, 154, 2, 1, 'Grocery & Gourmet Food', 'grocery-&-gourmet-food', '', 1, '', NULL, '', '', 'grocery-health-beauty-1/grocery-&-gourmet-food', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 29, 159, 160, 2, 1, 'Wine', 'wine', '', 1, '', NULL, '', '', 'grocery-health-beauty-1/wine', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 29, 157, 158, 2, 1, 'Natural & Organic', 'natural-&-organic', '', 1, '', NULL, '', '', 'grocery-health-beauty-1/natural-&-organic', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 29, 155, 156, 2, 1, 'Health & Personal Care', 'health-&-personal-care', '', 1, '', NULL, '', '', 'grocery-health-beauty-1/health-&-personal-care', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 11, 189, 190, 2, 1, 'Kitchen & Dining', 'kitchen-&-dining', '', 1, '', NULL, '', '', 'home-garden-1/kitchen-&-dining', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 11, 187, 188, 2, 1, 'Furniture & D', 'furniture-&-d', '', 1, '', NULL, '', '', 'home-garden-1/furniture-&-d', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 11, 185, 186, 2, 1, 'Bedding & Bath', 'bedding-&-bath', '', 1, '', NULL, '', '', 'home-garden-1/bedding-&-bath', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 11, 191, 192, 2, 1, 'Patio, Lawn & Garden', 'patio,-lawn-&-garden', '', 1, '', NULL, '', '', 'home-garden-1/patio,-lawn-&-garden', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 11, 183, 184, 2, 1, 'Arts, Crafts & Sewing', 'arts,-crafts-&-sewing', '', 1, '', NULL, '', '', 'home-garden-1/arts,-crafts-&-sewing', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 30, 227, 228, 2, 1, 'Watches', 'watches', '', 1, '', NULL, '', '', 'jewelry/watches', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 30, 225, 226, 2, 1, 'Fine Jewelry', 'fine-jewelry', '', 1, '', NULL, '', '', 'jewelry/fine-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 30, 221, 222, 2, 1, 'Fashion Jewelry', 'fashion-jewelry', '', 1, '', NULL, '', '', 'jewelry/fashion-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 30, 223, 224, 2, 1, 'Fashion Jewelry', 'fashion-jewelry', '', 1, '', NULL, '', '', 'jewelry/fashion-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 30, 219, 220, 2, 1, 'Engagement & Wedding', 'engagement-&-wedding', '', 1, '', NULL, '', '', 'jewelry/engagement-&-wedding', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 10, 259, 260, 2, 1, 'Movies & TV', 'movies-&-tv', '', 1, '', NULL, '', '', 'movies-music-games-1/movies-&-tv', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 10, 255, 256, 2, 1, 'Blu-ray', 'blu-ray', '', 1, '', NULL, '', '', 'movies-music-games-1/blu-ray', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 1, 217, 218, 1, 1, 'Jewelry', 'jewelry', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/jewelery-1519648439.jpg', NULL, '', 'heart', 'jewelry', 0, '', '', '', NULL, NULL, NULL),
(5, 1, 337, 338, 1, 1, 'Sports & Outdors', 'sports-outdors-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/outdoor-1519303622.jpg', NULL, '', 'male', 'sports-outdors-1', 0, '', '', '', NULL, NULL, NULL),
(31, 7, 75, 76, 2, 1, 'Cell Phones & Accessories', 'cell-phones-&-accessories', '', 1, '', NULL, '', '', 'electronics-1/cell-phones-&-accessories', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 73, 74, 1, 1, 'Electronics', 'electronics-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/electronics-1519648407.jpg', NULL, '', 'laptop', 'electronics-1', 0, '', '', '', NULL, NULL, NULL),
(8, 1, 121, 122, 1, 1, 'Fashion', 'fashion-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/fashion-1519291715.jpg', NULL, '', 'gift', 'fashion-1', 0, '', '', '', NULL, NULL, NULL),
(9, 1, 373, 374, 1, 1, 'Toy,Kids & Babies', 'toy-kids-babies-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/toys-1519648484.jpg', NULL, '', 'cab', 'toy-kids-babies-1', 0, '', '', '', NULL, NULL, NULL),
(10, 1, 253, 254, 1, 1, 'Movies, Music & Games', 'movies-music-games-1', '', 1, '/categories/music-1519302667.jpg', NULL, '', 'music', 'movies-music-games-1', 0, '', '', '', NULL, NULL, NULL),
(11, 1, 181, 182, 1, 1, 'Home & Garden', 'home-garden-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/home-1519291684.jpg', NULL, '', 'home', 'home-garden-1', 0, '', '', '', NULL, NULL, NULL),
(32, 7, 87, 88, 2, 1, 'Video Games', 'video-games', '', 1, '', NULL, '', '', 'electronics-1/video-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 7, 77, 78, 2, 1, 'Computer Parts & Components', 'computer-parts-&-components', '', 1, '', NULL, '', '', 'electronics-1/computer-parts-&-components', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 7, 79, 80, 2, 1, 'Electronics Accessories', 'electronics-accessories', '', 1, '', NULL, '', '', 'electronics-1/electronics-accessories', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 7, 81, 82, 2, 1, 'Home, Audio & Theater', 'home,-audio-&-theater', '', 1, '', NULL, '', '', 'electronics-1/home,-audio-&-theater', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 7, 85, 86, 2, 1, 'TV', 'tv-', '', 1, '', NULL, '', '', 'electronics-1/tv-', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 13, 59, 60, 2, 1, 'Photography', 'photography-1', '', 1, '', NULL, '', 'camera-retro', 'camera-photography-1/photography-1', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 13, 57, 58, 2, 1, 'Camera', 'camera', '', 1, '', NULL, '', '', 'camera-photography-1/camera', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 12, 37, 38, 2, 1, 'Textbooks', 'textbooks', '', 1, '', NULL, '', '', 'books-1-1/textbooks', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 12, 35, 36, 2, 1, 'Children\'s Books', 'children\'s-books', '', 1, '', NULL, '', '', 'books-1-1/children\'s-books', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 12, 33, 34, 2, 1, 'Books', 'books', '', 1, '', NULL, '', '', 'books-1-1/books', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 74, 9, 10, 2, 1, 'Tires ', 'tires-', '', 1, '', NULL, '', '', 'automotive-motors-1/tires-', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 74, 7, 8, 2, 1, 'Car Electronics', 'car-electronics', '', 1, '', NULL, '', '', 'automotive-motors-1/car-electronics', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 74, 5, 6, 2, 1, 'Automotive Tools', 'automotive-tools', '', 1, '', NULL, '', '', 'automotive-motors-1/automotive-tools', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 74, 3, 4, 2, 1, 'Automotive Parts', 'automotive-parts', '', 1, '', NULL, '', '', 'automotive-motors-1/automotive-parts', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 1, 55, 56, 1, 1, 'Camera & Photography', 'camera-photography-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/photography-1519301785.jpg', NULL, '', 'camera', 'camera-photography-1', 0, '', '', '', NULL, NULL, NULL),
(56, 10, 263, 264, 2, 1, 'Musical Instruments', 'musical-instruments', '', 1, '', NULL, '', '', 'movies-music-games-1/musical-instruments', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 10, 261, 262, 2, 1, 'MP3 Downloads', 'mp3-downloads', '', 1, '', NULL, '', '', 'movies-music-games-1/mp3-downloads', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 10, 257, 258, 2, 1, 'Game Downloads', 'game-downloads', '', 1, '', NULL, '', '', 'movies-music-games-1/game-downloads', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 5, 341, 342, 2, 1, 'Exercise & Fitness', 'exercise-&-fitness', '', 1, '', NULL, '', '', 'sports-outdors-1/exercise-&-fitness', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 5, 345, 346, 2, 1, 'Outdoor Recreation', 'outdoor-recreation', '', 1, '', NULL, '', '', 'sports-outdors-1/outdoor-recreation', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 5, 343, 344, 2, 1, 'Hunting & Fishing', 'hunting-&-fishing', '', 1, '', NULL, '', '', 'sports-outdors-1/hunting-&-fishing', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 5, 339, 340, 2, 1, 'Cycling', 'cycling', '', 1, '', NULL, '', '', 'sports-outdors-1/cycling', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 5, 347, 348, 2, 1, 'Team Sports', 'team-sports', '', 1, '', NULL, '', '', 'sports-outdors-1/team-sports', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 9, 381, 382, 2, 1, 'Toys & Games', 'toys-&-games', '', 1, '', NULL, '', '', 'toy-kids-babies-1/toys-&-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 9, 375, 376, 2, 1, 'Baby', 'baby', '', 1, '', NULL, '', '', 'toy-kids-babies-1/baby', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 9, 379, 380, 2, 1, 'Clothing (Kids & Baby)', 'clothing-(kids-&-baby)', '', 1, '', NULL, '', '', 'toy-kids-babies-1/clothing-(kids-&-baby)', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 9, 383, 384, 2, 1, 'Video Games for Kids', 'video-games-for-kids', '', 1, '', NULL, '', '', 'toy-kids-babies-1/video-games-for-kids', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 9, 377, 378, 2, 1, 'Baby Registry', 'baby-registry', '', 1, '', NULL, '', '', 'toy-kids-babies-1/baby-registry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 3, 323, 324, 2, 1, 'Services', 'services', '', 1, '', NULL, '', '', 'services-1/services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 3, 321, 322, 2, 1, 'IT Services', 'it-services', '', 1, '', NULL, '', '', 'services-1/it-services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 1, 151, 152, 1, 1, 'Health & Beauty', 'grocery-health-beauty-1', '', 1, '/categories/healthbeauty-1519304769.jpg', NULL, '', 'heart', 'grocery-health-beauty-1', 0, '', '', '', NULL, NULL, NULL),
(3, 1, 319, 320, 1, 1, 'Services', 'services-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/services-1519303368.jpg', NULL, '', 'area-chart', 'services-1', 0, '', '', '', NULL, NULL, NULL),
(74, 1, 1, 2, 1, 1, 'Automotive & Motors', 'automotive-motors-1', '<p>Pellentesque convallis est vel velit luctus, in consequat tortor r<strong>utrum. In lectus quam  </strong> , tempor eu a sdf   sdiam ef fi citur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po</p>', 1, '/categories/automotive-1519648388.jpg', NULL, '', 'car', 'automotive-motors-1', 0, '', '', '', NULL, NULL, NULL),
(75, 1, 295, 296, 1, 1, 'Restaurants', 'restaurants-1', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur. Mauris c</p>', 1, '/categories/restaurant-1519304216.jpg', NULL, '', 'cutlery', 'restaurants-1', 0, '', '', '', NULL, NULL, NULL),
(76, 75, 297, 298, 2, 1, 'Asian Restaurants', 'asian-restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '', NULL, '', '', 'restaurants-1/asian-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 75, 299, 300, 2, 1, 'French Restaurants', 'french-restaurants', '', 1, '', NULL, '', '', 'restaurants-1/french-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 75, 301, 302, 2, 1, 'Italian Restaurants', 'italian-restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '', NULL, '', '', 'restaurants-1/italian-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 1, 289, 290, 1, 1, 'Real Estate', 'real-estate-1', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.</p>', 1, '/categories/realestate-1519302775.jpg', NULL, '', 'building', 'real-estate-1', 0, '', '', '', NULL, NULL, NULL),
(80, 1, 39, 40, 1, 2, 'Books', 'books-1', '', 1, '/categories/image5-1427100316.jpg', NULL, '', '', 'books-1', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 105, 99, 100, 2, 2, 'Software', 'software', '', 1, '', NULL, '', '', 'electronics/software', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 106, 139, 140, 2, 2, 'Women', 'women', '', 1, '', NULL, '', '', 'fashion/women', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 106, 135, 136, 2, 2, 'Man', 'man', '', 1, '', NULL, '', '', 'fashion/man', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 106, 133, 134, 2, 2, 'Kids & Baby', 'kids-&-baby', '', 1, '', NULL, '', '', 'fashion/kids-&-baby', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 106, 137, 138, 2, 2, 'Shoes', 'shoes', '', 1, '', NULL, '', '', 'fashion/shoes', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 140, 163, 164, 2, 2, 'Grocery & Gourmet Food', 'grocery-&-gourmet-food', '', 1, '', NULL, '', '', 'grocery-health-beauty/grocery-&-gourmet-food', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 140, 169, 170, 2, 2, 'Wine', 'wine', '', 1, '', NULL, '', '', 'grocery-health-beauty/wine', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 140, 167, 168, 2, 2, 'Natural & Organic', 'natural-&-organic', '', 1, '', NULL, '', '', 'grocery-health-beauty/natural-&-organic', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 140, 165, 166, 2, 2, 'Health & Personal Care', 'health-&-personal-care', '', 1, '', NULL, '', '', 'grocery-health-beauty/health-&-personal-care', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 109, 201, 202, 2, 2, 'Kitchen & Dining', 'kitchen-&-dining', '', 1, '', NULL, '', '', 'home-garden/kitchen-&-dining', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 109, 199, 200, 2, 2, 'Furniture & D', 'furniture-&-d', '', 1, '', NULL, '', '', 'home-garden/furniture-&-d', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 109, 197, 198, 2, 2, 'Bedding & Bath', 'bedding-&-bath', '', 1, '', NULL, '', '', 'home-garden/bedding-&-bath', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 109, 203, 204, 2, 2, 'Patio, Lawn & Garden', 'patio,-lawn-&-garden', '', 1, '', NULL, '', '', 'home-garden/patio,-lawn-&-garden', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 109, 195, 196, 2, 2, 'Arts, Crafts & Sewing', 'arts,-crafts-&-sewing', '', 1, '', NULL, '', '', 'home-garden/arts,-crafts-&-sewing', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 102, 239, 240, 2, 2, 'Watches', 'watches', '', 1, '', NULL, '', '', 'jewelry-watches/watches', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 102, 237, 238, 2, 2, 'Fine Jewelry', 'fine-jewelry', '', 1, '', NULL, '', '', 'jewelry-watches/fine-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 102, 233, 234, 2, 2, 'Fashion Jewelry', 'fashion-jewelry', '', 1, '', NULL, '', '', 'jewelry-watches/fashion-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 102, 235, 236, 2, 2, 'Fashion Jewelry', 'fashion-jewelry', '', 1, '', NULL, '', '', 'jewelry-watches/fashion-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 102, 231, 232, 2, 2, 'Engagement & Wedding', 'engagement-&-wedding', '', 1, '', NULL, '', '', 'jewelry-watches/engagement-&-wedding', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 108, 271, 272, 2, 2, 'Movies & TV', 'movies-&-tv', '', 1, '', NULL, '', '', 'movies-music-games/movies-&-tv', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 108, 267, 268, 2, 2, 'Blu-ray', 'blu-ray', '', 1, '', NULL, '', '', 'movies-music-games/blu-ray', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 1, 229, 230, 1, 2, 'Jewelry & Watches', 'jewelry-watches', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/watch-1427100687.jpg', NULL, '', '', 'jewelry-watches', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 1, 349, 350, 1, 2, 'Sports & Outdors', 'sports-outdors', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image3-1411751531.jpg', NULL, '', '', 'sports-outdors', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 105, 91, 92, 2, 2, 'Cell Phones & Accessories', 'cell-phones-&-accessories', '', 1, '', NULL, '', '', 'electronics/cell-phones-&-accessories', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 1, 89, 90, 1, 2, 'Electronics', 'electronics', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image6-1427100355.jpg', NULL, '', '', 'electronics', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 1, 131, 132, 1, 2, 'Fashion', 'fashion', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/slide-image-8-1427100458.jpg', NULL, '', '', 'fashion', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 1, 385, 386, 1, 2, 'Toy,Kids & Babies', 'toy-kids-babies', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image1-1427100760.jpg', NULL, '', '', 'toy-kids-babies', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 1, 265, 266, 1, 2, 'Movies, Music & Games', 'movies-music-games', '', 1, '/categories/image2-1427100712.jpg', NULL, '', '', 'movies-music-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 1, 193, 194, 1, 2, 'Home & Garden', 'home-garden', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image7-1427100616.jpg', NULL, '', '', 'home-garden', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 105, 103, 104, 2, 2, 'Video Games', 'video-games', '', 1, '', NULL, '', '', 'electronics/video-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 105, 93, 94, 2, 2, 'Computer Parts & Components', 'computer-parts-&-components', '', 1, '', NULL, '', '', 'electronics/computer-parts-&-components', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 105, 95, 96, 2, 2, 'Electronics Accessories', 'electronics-accessories', '', 1, '', NULL, '', '', 'electronics/electronics-accessories', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 105, 97, 98, 2, 2, 'Home, Audio & Theater', 'home,-audio-&-theater', '', 1, '', NULL, '', '', 'electronics/home,-audio-&-theater', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 105, 101, 102, 2, 2, 'TV ', 'tv-', '', 1, '', NULL, '', '', 'electronics/tv-', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 124, 65, 66, 2, 2, 'Photography', 'photography', '', 1, '', NULL, '', '', 'camera-photography/photography', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 124, 63, 64, 2, 2, 'Camera', 'camera', '', 1, '', NULL, '', '', 'camera-photography/camera', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 80, 45, 46, 2, 2, 'Textbooks', 'textbooks', '', 1, '', NULL, '', '', 'books-1/textbooks', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 80, 43, 44, 2, 2, 'Children\'s Books', 'children\'s-books', '', 1, '', NULL, '', '', 'books-1/children\'s-books', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 80, 41, 42, 2, 2, 'Books', 'books', '', 1, '', NULL, '', '', 'books-1/books', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 142, 19, 20, 2, 2, 'Tires ', 'tires-', '', 1, '', NULL, '', '', 'automotive-motors/tires-', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 142, 17, 18, 2, 2, 'Car Electronics', 'car-electronics', '', 1, '', NULL, '', '', 'automotive-motors/car-electronics', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 142, 15, 16, 2, 2, 'Automotive Tools', 'automotive-tools', '', 1, '', NULL, '', '', 'automotive-motors/automotive-tools', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 142, 13, 14, 2, 2, 'Automotive Parts', 'automotive-parts', '', 1, '', NULL, '', '', 'automotive-motors/automotive-parts', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 1, 61, 62, 1, 2, 'Camera & Photography', 'camera-photography', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image7-1427100335.jpg', NULL, '', '', 'camera-photography', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 108, 275, 276, 2, 2, 'Musical Instruments', 'musical-instruments', '', 1, '', NULL, '', '', 'movies-music-games/musical-instruments', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 108, 273, 274, 2, 2, 'MP3 Downloads', 'mp3-downloads', '', 1, '', NULL, '', '', 'movies-music-games/mp3-downloads', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 108, 269, 270, 2, 2, 'Game Downloads', 'game-downloads', '', 1, '', NULL, '', '', 'movies-music-games/game-downloads', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 103, 353, 354, 2, 2, 'Exercise & Fitness', 'exercise-&-fitness', '', 1, '', NULL, '', '', 'sports-outdors/exercise-&-fitness', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 103, 357, 358, 2, 2, 'Outdoor Recreation', 'outdoor-recreation', '', 1, '', NULL, '', '', 'sports-outdors/outdoor-recreation', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 103, 355, 356, 2, 2, 'Hunting & Fishing', 'hunting-&-fishing', '', 1, '', NULL, '', '', 'sports-outdors/hunting-&-fishing', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 103, 351, 352, 2, 2, 'Cycling', 'cycling', '', 1, '', NULL, '', '', 'sports-outdors/cycling', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 103, 359, 360, 2, 2, 'Team Sports', 'team-sports', '', 1, '', NULL, '', '', 'sports-outdors/team-sports', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 107, 393, 394, 2, 2, 'Toys & Games', 'toys-&-games', '', 1, '', NULL, '', '', 'toy-kids-babies/toys-&-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 107, 387, 388, 2, 2, 'Baby', 'baby', '', 1, '', NULL, '', '', 'toy-kids-babies/baby', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 107, 391, 392, 2, 2, 'Clothing (Kids & Baby)', 'clothing-(kids-&-baby)', '', 1, '', NULL, '', '', 'toy-kids-babies/clothing-(kids-&-baby)', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 107, 395, 396, 2, 2, 'Video Games for Kids', 'video-games-for-kids', '', 1, '', NULL, '', '', 'toy-kids-babies/video-games-for-kids', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 107, 389, 390, 2, 2, 'Baby Registry', 'baby-registry', '', 1, '', NULL, '', '', 'toy-kids-babies/baby-registry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 141, 329, 330, 2, 2, 'Services', 'services', '', 1, '', NULL, '', '', 'services/services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 141, 327, 328, 2, 2, 'IT Services', 'it-services', '', 1, '', NULL, '', '', 'services/it-services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 1, 161, 162, 1, 2, 'Health & Beauty', 'grocery-health-beauty', '', 1, '/categories/slide1-the-health-and-beauty-world-1427100497.jpg', NULL, '', '', 'grocery-health-beauty', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 1, 325, 326, 1, 2, 'Services', 'services', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image9-1411751440.png', NULL, '', '', 'services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 1, 11, 12, 1, 2, 'Automotive & Motors', 'automotive-motors', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image5-1427100860.jpg', NULL, '', '', 'automotive-motors', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 1, 303, 304, 1, 2, 'Restaurants', 'restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur. Mauris c', 1, '/categories/image3-1427100807.jpg', NULL, '', '', 'restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 143, 305, 306, 2, 2, 'Asian Restaurants', 'asian-restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '', NULL, '', '', 'restaurants/asian-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 143, 307, 308, 2, 2, 'French Restaurants', 'french-restaurants', '', 1, '', NULL, '', '', 'restaurants/french-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 143, 309, 310, 2, 2, 'Italian Restaurants', 'italian-restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '', NULL, '', '', 'restaurants/italian-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 1, 291, 292, 1, 2, 'Real Estate', 'real-estate', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '/categories/image9-1427100841.jpg', NULL, '', '', 'real-estate', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 1, 47, 48, 1, 3, 'Books', 'books-1', '', 1, '/categories/image5-1427100316.jpg', NULL, '', '', 'books-1', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 173, 115, 116, 2, 3, 'Software', 'software', '', 1, '', NULL, '', '', 'electronics/software', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 174, 149, 150, 2, 3, 'Women', 'women', '', 1, '', NULL, '', '', 'fashion/women', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 174, 145, 146, 2, 3, 'Man', 'man', '', 1, '', NULL, '', '', 'fashion/man', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(152, 174, 143, 144, 2, 3, 'Kids & Baby', 'kids-&-baby', '', 1, '', NULL, '', '', 'fashion/kids-&-baby', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 174, 147, 148, 2, 3, 'Shoes', 'shoes', '', 1, '', NULL, '', '', 'fashion/shoes', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(154, 208, 173, 174, 2, 3, 'Grocery & Gourmet Food', 'grocery-&-gourmet-food', '', 1, '', NULL, '', '', 'grocery-health-beauty/grocery-&-gourmet-food', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 208, 179, 180, 2, 3, 'Wine', 'wine', '', 1, '', NULL, '', '', 'grocery-health-beauty/wine', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(156, 208, 177, 178, 2, 3, 'Natural & Organic', 'natural-&-organic', '', 1, '', NULL, '', '', 'grocery-health-beauty/natural-&-organic', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(157, 208, 175, 176, 2, 3, 'Health & Personal Care', 'health-&-personal-care', '', 1, '', NULL, '', '', 'grocery-health-beauty/health-&-personal-care', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(158, 177, 213, 214, 2, 3, 'Kitchen & Dining', 'kitchen-&-dining', '', 1, '', NULL, '', '', 'home-garden/kitchen-&-dining', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(159, 177, 211, 212, 2, 3, 'Furniture & D', 'furniture-&-d', '', 1, '', NULL, '', '', 'home-garden/furniture-&-d', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 177, 209, 210, 2, 3, 'Bedding & Bath', 'bedding-&-bath', '', 1, '', NULL, '', '', 'home-garden/bedding-&-bath', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(161, 177, 215, 216, 2, 3, 'Patio, Lawn & Garden', 'patio,-lawn-&-garden', '', 1, '', NULL, '', '', 'home-garden/patio,-lawn-&-garden', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(162, 177, 207, 208, 2, 3, 'Arts, Crafts & Sewing', 'arts,-crafts-&-sewing', '', 1, '', NULL, '', '', 'home-garden/arts,-crafts-&-sewing', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(163, 170, 251, 252, 2, 3, 'Watches', 'watches', '', 1, '', NULL, '', '', 'jewelry-watches/watches', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 170, 249, 250, 2, 3, 'Fine Jewelry', 'fine-jewelry', '', 1, '', NULL, '', '', 'jewelry-watches/fine-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(165, 170, 245, 246, 2, 3, 'Fashion Jewelry', 'fashion-jewelry', '', 1, '', NULL, '', '', 'jewelry-watches/fashion-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(166, 170, 247, 248, 2, 3, 'Fashion Jewelry', 'fashion-jewelry', '', 1, '', NULL, '', '', 'jewelry-watches/fashion-jewelry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(167, 170, 243, 244, 2, 3, 'Engagement & Wedding', 'engagement-&-wedding', '', 1, '', NULL, '', '', 'jewelry-watches/engagement-&-wedding', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(168, 176, 283, 284, 2, 3, 'Movies & TV', 'movies-&-tv', '', 1, '', NULL, '', '', 'movies-music-games/movies-&-tv', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(169, 176, 279, 280, 2, 3, 'Blu-ray', 'blu-ray', '', 1, '', NULL, '', '', 'movies-music-games/blu-ray', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 1, 241, 242, 1, 3, 'Jewelry & Watches', 'jewelry-watches', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/watch-1427100687.jpg', NULL, '', '', 'jewelry-watches', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 1, 361, 362, 1, 3, 'Sports & Outdors', 'sports-outdors', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image3-1411751531.jpg', NULL, '', '', 'sports-outdors', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 173, 107, 108, 2, 3, 'Cell Phones & Accessories', 'cell-phones-&-accessories', '', 1, '', NULL, '', '', 'electronics/cell-phones-&-accessories', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 1, 105, 106, 1, 3, 'Electronics', 'electronics', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image6-1427100355.jpg', NULL, '', '', 'electronics', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 1, 141, 142, 1, 3, 'Fashion', 'fashion', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/slide-image-8-1427100458.jpg', NULL, '', '', 'fashion', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(175, 1, 397, 398, 1, 3, 'Toy,Kids & Babies', 'toy-kids-babies', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image1-1427100760.jpg', NULL, '', '', 'toy-kids-babies', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(176, 1, 277, 278, 1, 3, 'Movies, Music & Games', 'movies-music-games', '', 1, '/categories/image2-1427100712.jpg', NULL, '', '', 'movies-music-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(177, 1, 205, 206, 1, 3, 'Home & Garden', 'home-garden', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image7-1427100616.jpg', NULL, '', '', 'home-garden', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(178, 173, 119, 120, 2, 3, 'Video Games', 'video-games', '', 1, '', NULL, '', '', 'electronics/video-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(179, 173, 109, 110, 2, 3, 'Computer Parts & Components', 'computer-parts-&-components', '', 1, '', NULL, '', '', 'electronics/computer-parts-&-components', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(180, 173, 111, 112, 2, 3, 'Electronics Accessories', 'electronics-accessories', '', 1, '', NULL, '', '', 'electronics/electronics-accessories', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(181, 173, 113, 114, 2, 3, 'Home, Audio & Theater', 'home,-audio-&-theater', '', 1, '', NULL, '', '', 'electronics/home,-audio-&-theater', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 173, 117, 118, 2, 3, 'TV ', 'tv-', '', 1, '', NULL, '', '', 'electronics/tv-', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(183, 192, 71, 72, 2, 3, 'Photography', 'photography', '', 1, '', NULL, '', '', 'camera-photography/photography', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(184, 192, 69, 70, 2, 3, 'Camera', 'camera', '', 1, '', NULL, '', '', 'camera-photography/camera', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 148, 53, 54, 2, 3, 'Textbooks', 'textbooks', '', 1, '', NULL, '', '', 'books-1/textbooks', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(186, 148, 51, 52, 2, 3, 'Children\'s Books', 'children\'s-books', '', 1, '', NULL, '', '', 'books-1/children\'s-books', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(187, 148, 49, 50, 2, 3, 'Books', 'books', '', 1, '', NULL, '', '', 'books-1/books', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 210, 29, 30, 2, 3, 'Tires ', 'tires-', '', 1, '', NULL, '', '', 'automotive-motors/tires-', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(189, 210, 27, 28, 2, 3, 'Car Electronics', 'car-electronics', '', 1, '', NULL, '', '', 'automotive-motors/car-electronics', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(190, 210, 25, 26, 2, 3, 'Automotive Tools', 'automotive-tools', '', 1, '', NULL, '', '', 'automotive-motors/automotive-tools', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 210, 23, 24, 2, 3, 'Automotive Parts', 'automotive-parts', '', 1, '', NULL, '', '', 'automotive-motors/automotive-parts', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(192, 1, 67, 68, 1, 3, 'Camera & Photography', 'camera-photography', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image7-1427100335.jpg', NULL, '', '', 'camera-photography', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(193, 176, 287, 288, 2, 3, 'Musical Instruments', 'musical-instruments', '', 1, '', NULL, '', '', 'movies-music-games/musical-instruments', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(194, 176, 285, 286, 2, 3, 'MP3 Downloads', 'mp3-downloads', '', 1, '', NULL, '', '', 'movies-music-games/mp3-downloads', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(195, 176, 281, 282, 2, 3, 'Game Downloads', 'game-downloads', '', 1, '', NULL, '', '', 'movies-music-games/game-downloads', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(196, 171, 365, 366, 2, 3, 'Exercise & Fitness', 'exercise-&-fitness', '', 1, '', NULL, '', '', 'sports-outdors/exercise-&-fitness', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 171, 369, 370, 2, 3, 'Outdoor Recreation', 'outdoor-recreation', '', 1, '', NULL, '', '', 'sports-outdors/outdoor-recreation', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(198, 171, 367, 368, 2, 3, 'Hunting & Fishing', 'hunting-&-fishing', '', 1, '', NULL, '', '', 'sports-outdors/hunting-&-fishing', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(199, 171, 363, 364, 2, 3, 'Cycling', 'cycling', '', 1, '', NULL, '', '', 'sports-outdors/cycling', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 171, 371, 372, 2, 3, 'Team Sports', 'team-sports', '', 1, '', NULL, '', '', 'sports-outdors/team-sports', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 175, 405, 406, 2, 3, 'Toys & Games', 'toys-&-games', '', 1, '', NULL, '', '', 'toy-kids-babies/toys-&-games', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 175, 399, 400, 2, 3, 'Baby', 'baby', '', 1, '', NULL, '', '', 'toy-kids-babies/baby', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 175, 403, 404, 2, 3, 'Clothing (Kids & Baby)', 'clothing-(kids-&-baby)', '', 1, '', NULL, '', '', 'toy-kids-babies/clothing-(kids-&-baby)', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(204, 175, 407, 408, 2, 3, 'Video Games for Kids', 'video-games-for-kids', '', 1, '', NULL, '', '', 'toy-kids-babies/video-games-for-kids', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(205, 175, 401, 402, 2, 3, 'Baby Registry', 'baby-registry', '', 1, '', NULL, '', '', 'toy-kids-babies/baby-registry', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 209, 335, 336, 2, 3, 'Services', 'services', '', 1, '', NULL, '', '', 'services/services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(207, 209, 333, 334, 2, 3, 'IT Services', 'it-services', '', 1, '', NULL, '', '', 'services/it-services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(208, 1, 171, 172, 1, 3, 'Health & Beauty', 'grocery-health-beauty', '', 1, '/categories/slide1-the-health-and-beauty-world-1427100497.jpg', NULL, '', '', 'grocery-health-beauty', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(209, 1, 331, 332, 1, 3, 'Services', 'services', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image9-1411751440.png', NULL, '', '', 'services', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(210, 1, 21, 22, 1, 3, 'Automotive & Motors', 'automotive-motors', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices po', 1, '/categories/image5-1427100860.jpg', NULL, '', '', 'automotive-motors', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(211, 1, 311, 312, 1, 3, 'Restaurants', 'restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur. Mauris c', 1, '/categories/image3-1427100807.jpg', NULL, '', '', 'restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 211, 313, 314, 2, 3, 'Asian Restaurants', 'asian-restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '', NULL, '', '', 'restaurants/asian-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(213, 211, 315, 316, 2, 3, 'French Restaurants', 'french-restaurants', '', 1, '', NULL, '', '', 'restaurants/french-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(214, 211, 317, 318, 2, 3, 'Italian Restaurants', 'italian-restaurants', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '', NULL, '', '', 'restaurants/italian-restaurants', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 1, 293, 294, 1, 3, 'Real Estate', 'real-estate', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam purus libero, luctus id felis at, porta malesuada massa. Vestibulum vitae imperdiet justo, eget ullamcorper nunc. Cras eget ligula sodales, congue lacus eget, tincidunt justo. Ut vestibulum bibendum ante, vitae scelerisque leo faucibus id. Nunc congue, justo id porttitor fringilla, ipsum ex rutrum lorem, in sodales nisi ipsum at massa. Duis a cursus ipsum. Aliquam vitae est tortor. Aenean aliquet ultrices magna et efficitur.', 1, '/categories/image9-1427100841.jpg', NULL, '', '', 'real-estate', 0, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_cities` (`id`, `name`, `region_id`, `ordering`) VALUES
(1, 'Toronto', NULL, 0),
(2, 'Montreal', NULL, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_companies` (`id`, `name`, `alias`, `comercialName`, `short_description`, `description`, `meta_title`, `meta_description`, `street_number`, `address`, `city`, `county`, `province`, `area`, `countryId`, `website`, `keywords`, `registrationCode`, `phone`, `email`, `fax`, `state`, `typeId`, `logoLocation`, `creationDate`, `modified`, `mainSubcategory`, `latitude`, `longitude`, `activity_radius`, `userId`, `created_by`, `averageRating`, `review_score`, `approved`, `viewCount`, `websiteCount`, `contactCount`, `taxCode`, `package_id`, `facebook`, `twitter`, `googlep`, `skype`, `linkedin`, `youtube`, `instagram`, `pinterest`, `whatsapp`, `postalCode`, `mobile`, `slogan`, `publish_only_city`, `featured`, `business_hours`, `notes_hours`, `custom_tab_name`, `custom_tab_content`, `business_cover_image`, `publish_start_date`, `publish_end_date`, `notified_date`, `time_zone`, `establishment_year`, `employees`, `ad_image`, `disapproval_text`, `yelp_id`, `enable_request_quote`, `trail_weeks_hours`, `trail_weeks_status`, `trail_weeks_address`, `ad_caption`, `recommended`, `ordering`, `company_view`, `opening_status`, `min_project_size`, `hourly_rate`) VALUES
(1, 'Wedding Venue', 'wedding-venue', 'Home & Gardem', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis justo nulla, a tempus elit pulvinar eget. Nunc tempus leo in arcu mattis lobortis. Fusce ut sollicitudin nulla. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '<p>Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc. Proin pellentesque, lorem porttitor commodo hendrerit, enim leo mattis risus, ac viverra ante tellus quis velit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim tristique sapien ut pretium. Duis sollicitudin dolor sed nisi venenatis quis fringilla diam suscipit.</p>\r\n<p>Sed convallis lectus non nibh suscipit ullamcorper. Fusce in magna ac lacus semper convallis. Morbi sagittis auctor massa vel consequat. Nulla fermentum, sapien a sagittis accumsan, tellus ipsum posuere tellus, a lacinia tortor lacus in nisl. Vestibulum posuere dictum ipsum ac viverra. Integer neque neque, blandit non adipiscing vel, auctor non odio. Maecenas quis nibh a diam eleifend rhoncus sed in turpis. Pellentesque mollis fermentum dolor et mollis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed ullamcorper ante ac nunc commodo vitae rutrum sem placerat. Morbi et nisi metus.</p>', '', '', '123', 'Old San Francisco', 'Sunnyvale', 'California', '', '', 226, 'http://www.garden.com', 'wedding, planning, venue', '342423422', '34242123123', 'email@decoration.com', '434312312321', 1, '6', '/companies/1/wedding-venue_1675355263.jpeg', '2023-02-02 22:28:07', '2023-02-02 16:28:07', 53, '37.3681865', '-122.031385', '0', 0, 380, '4.0', '0.0', 2, 43, 0, 0, '123123', 0, 'http://www.facebook.com/cmsjunkie', '', '', '', '', '', 'https://www.instagram.com/', 'https://www.pinterest.com/', 'https://www.whatsapp.com/', '', '', 'Your event in a magical place!', 0, 0, '', '', '', '', '/companies/1/wedding-venue_1675355271.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(4, 'Water Sports', 'water-sports', 'Rent a car', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis justo nulla, a tempus elit pulvinar eget. Nunc tempus leo in arcu mattis lobortis. Fusce ut sollicitudin nulla. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis justo nulla, a tempus elit pulvinar eget. Nunc tempus leo in arcu mattis lobortis. Fusce ut sollicitudin nulla. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut laoreet feugiat lectus id ornare. Nulla ut odio eget justo faucibus consectetur. Ut faucibus ultrices accumsan. Aenean leo neque, accumsan ac eleifend vel, pulvinar id urna. Phasellus non malesuada augue. Maecenas id egestas quam, at molestie tortor. Sed quis dictum eros.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis justo nulla, a tempus elit pulvinar eget. Nunc tempus leo in arcu mattis lobortis. Fusce ut sollicitudin nulla. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut laoreet feugiat lectus id ornare. Nulla ut odio eget justo faucibus consectetur. Ut faucibus ultrices accumsan. Aenean leo neque, accumsan ac eleifend vel, pulvinar id urna. Phasellus non malesuada augue. Maecenas id egestas quam, at molestie tortor. Sed quis dictum eros.</p>', '', '', '11', 'South Denley Drive', 'Dallas', 'Dallas County', '', 'Dallas County', 226, 'http://www.cmsjunkie.com', '', 'JBD-5512312', '0010727321321', 'office@email.com', '0010269/220123', 1, '6', '/companies/4/water-sports_1675431398.jpeg', '2023-02-03 19:36:41', '2023-02-03 13:36:41', 5, '32.723180150000005', '-96.80519125000001', '0', 0, 380, '4.8', '0.0', 2, 44, 0, 0, '123123', 0, '', '', '', '', '', 'https://www.youtube.com/', 'https://www.instagram.com/', '', 'https://www.whatsapp.com/', '75216', '001072744333', 'Live your life at maximum.', 0, 0, '', '', '', '', '/companies/4/water-sports_1675431274.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(5, 'Yoga Club', 'yoga-club', 'AQUACON PROJECT', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam.</p>\r\n<p>Quisque pellentesque libero eget dui elementum scelerisque. Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. .</p>', '', '', '44', 'Paris', 'Paris', 'Ile-de-France', '', '', 72, 'http://www.cmsjunkie.com', '', 'YG-12312', '0727321321', 'office@site.com', '0269/220123', 1, '6', '/companies/5/yoga-club_1675355084.jpeg', '2023-02-02 22:25:07', '2023-02-02 16:25:07', 42, '48.8588897', '2.3200410217200766', '15', 0, 380, '3.0', '0.0', 2, 15, 0, 0, '123', 0, '', '', '', 'https://www.skype.com/en/', '', 'https://www.youtube.com/', 'https://www.instagram.com/', '', 'https://www.whatsapp.com/', '', '', 'Get in harmony with your body and soul!', 0, 0, '', '', '', '', '/companies/5/yoga-club_1675355091.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(8, 'Professional Photography', 'vintage-photography', 'Contruction Company', 'Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc.', '<p>Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc. Proin pellentesque, lorem porttitor commodo hendrerit, enim leo mattis risus, ac viverra ante tellus quis velit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim tristique sapien ut pretium. Duis sollicitudin dolor sed nisi venenatis quis fringilla diam suscipit.</p>\r\n<p>Sed convallis lectus non nibh suscipit ullamcorper. Fusce in magna ac lacus semper convallis. Morbi sagittis auctor massa vel consequat. Nulla fermentum, sapien a sagittis accumsan, tellus ipsum posuere tellus, a lacinia tortor lacus in nisl. Vestibulum posuere dictum ipsum ac viverra. Integer neque neque, blandit non adipiscing vel, auctor non odio. Maecenas quis nibh a diam eleifend rhoncus sed in turpis. Pellentesque mollis fermentum dolor et mollis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed ullamcorper ante ac nunc commodo vitae rutrum sem placerat. Morbi et nisi metus.</p>', '', '', '22', 'Lawrance', 'Toronto', 'Ontario', '', '', 36, 'http://google.com', '', 'JBD-343412', '0727321321', 'office@site.com', '0269/220123', 1, '6', '/companies/8/vintage-photography_1675355033.jpeg', '2023-02-02 22:24:27', '2023-02-02 16:24:27', 24, '43.65057816594119', '-79.37493324279785', '0', 0, 380, '4.7', '4.1', 2, 391, 0, 0, '12312', 0, 'http://www.facebook.com/cmsjunkie', '', 'http://www.google.com/cmsjunkie', '', '', '', 'https://www.instagram.com/', 'https://www.pinterest.com/', '', '', '', 'Good old day are coming back.', 0, 1, '', '', '', '', '/companies/8/vintage-photography_1675355038.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(9, 'Flower Shop', 'flower-shop', 'IT Services', 'Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc.', '<p>Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc. Proin pellentesque, lorem porttitor commodo hendrerit, enim leo mattis risus, ac viverra ante tellus quis velit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim tristique sapien ut pretium. Duis sollicitudin dolor sed nisi venenatis quis fringilla diam suscipit.</p>\r\n<p>Sed convallis lectus non nibh suscipit ullamcorper. Fusce in magna ac lacus semper convallis. Morbi sagittis auctor massa vel consequat. Nulla fermentum, sapien a sagittis accumsan, tellus ipsum posuere tellus, a lacinia tortor lacus in nisl. Vestibulum posuere dictum ipsum ac viverra. Integer neque neque, blandit non adipiscing vel, auctor non odio. Maecenas quis nibh a diam eleifend rhoncus sed in turpis. Pellentesque mollis fermentum dolor et mollis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed ullamcorper ante ac nunc commodo vitae rutrum sem placerat. Morbi et nisi metus.</p>', '', '', '32', 'Canal Ring', 'Amsterdam', 'Centrum', '', '', 161, 'http://google.com', '', '3424234221212', '0727321321', 'office@company.com', '0010269/220123', 1, '5', '/companies/9/flower-shop_1675354998.jpeg', '2023-02-02 22:23:38', '2023-02-02 16:23:38', 11, '52.37197895', '4.884726800042784', '0', 380, 380, '1.5', '5.0', 2, 27, 0, 0, '123123', 1, 'http://www.facebook.com/cmsjunkie', '', '', '', '', '', 'https://www.instagram.com/', 'https://www.pinterest.com/', 'https://www.whatsapp.com/', '', '', 'One flow for one happy person.', 0, 0, '', '', '', '', '/companies/9/flower-shop_1675355007.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(30, 'Real Property', 'real-property', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam.</p>\r\n<p>Quisque pellentesque libero eget dui elementum scelerisque. Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. </p>', '', '', '1123', 'New York Ave', 'New York', 'New York', '', '', 226, '', '', '', '+1 232 883 9932', 'office@site.com', '', 1, '1', '/companies/30/real-property_1675354950.jpeg', '2023-02-02 22:23:03', '2023-02-02 16:23:03', 79, '40.645796', '-73.94583599999999', '0', 0, 380, '0.0', '0.0', 2, 14, 0, 0, NULL, 0, 'http://www.facebook.com/cmsjunkie', '', 'http://www.googleplus.com', '', 'https://al.linkedin.com/', '', 'https://www.instagram.com/', '', 'https://www.whatsapp.com/', '', '+1 555 883 9932', 'We can sell it for you', 0, 0, '', '', '', '', '/companies/30/real-property_1675354961.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(33, 'Amusement Park', 'amusement-park', NULL, 'Donec eleifend purus nulla, non vehicula nisi dictum quis. Maecenas in odio purus. Etiam vulputate nisi eget pharetra tincidunt. Morbi et eros consectetur, ultricies ligula quis, ullamcorper neque. Donec pellentesque felis vel luctus tempus.', '<p>Donec eleifend purus nulla, non vehicula nisi dictum quis. Maecenas in odio purus. Etiam vulputate nisi eget pharetra tincidunt. Morbi et eros consectetur, ultricies ligula quis, ullamcorper neque. Donec pellentesque felis vel luctus tempus. Curabitur blandit dui purus, non viverra magna consequat vitae. Nunc volutpat malesuada orci vitae varius.</p>\r\n<p>Suspendisse accumsan nunc non dictum bibendum. Sed suscipit id ipsum ut tincidunt. Vivamus condimentum diam at condimentum scelerisque. Etiam vulputate pellentesque maximus. Curabitur tincidunt nibh et nisl porttitor, eget ultrices turpis maximus. Fusce molestie elit eget felis cursus volutpat. Nam tincidunt lacus nec massa sagittis, eu dapibus purus bibendum. Ut hendrerit, felis nec congue posuere, lorem urna eleifend est, ac venenatis quam augue a arcu. </p>', '', '', '12', 'Hopkins Avenue', 'New York', 'New Jersey', '', '', 226, '', '', '', '+1 444 777 9999', 'office@site.com', '', 1, '4', '/companies/33/amusement-park_1675354908.jpeg', '2023-02-02 22:22:16', '2023-02-02 16:22:16', 64, '40.559463', '-74.134697', '0', 0, 380, '0.0', '0.0', 2, 27, 0, 0, NULL, 0, 'http://www.facebook.com/cmsjunkie', '', '', 'https://www.skype.com/en/', '', '', '', '', 'https://www.whatsapp.com/', '', '+1 555 883 9932', 'Our main concern is your entertainment', 0, 0, '', '', '', '', '/companies/33/amusement-park_1675354914.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(43, 'Fitness Gym', 'fitness-gym', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam.  </p>\r\n<p>Quisque pellentesque libero eget dui elementum scelerisque. Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam.</p>', '', '', '100', 'Queen Street West', 'Old Toronto', 'Old Toronto', 'Ontario', '', 36, '', 'fitness, gym, exercise', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '1', '/companies/43/fitness-gym_1675354871.jpeg', '2023-02-02 22:21:30', '2023-02-02 16:21:30', 5, '43.6512853', '-79.3845398', '0', 0, 380, '0.0', '0.0', 2, 20, 0, 0, NULL, 0, 'http://www.facebook.com/cmsjunkie', '', NULL, '', '', '', 'https://www.instagram.com/', '', '', '', '+1 555 888 9932', 'Get your body and mind in shape!', 0, 0, '', '', '', '', '/companies/43/fitness-gym_1675354877.jpeg', '0000-00-00', '0000-00-00', NULL, '-03:00', '2017', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(46, 'Lucha Restaurant', 'lucha-restaurant', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mollis justo nulla, a tempus elit pulvinar eget. Nunc tempus leo in arcu mattis lobortis. Fusce ut sollicitudin nulla. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '<p>Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. </p>\r\n<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur.</p>', '', '', '41', 'Viale Gorizia', 'Rome', 'Roma Capitale', '', '', 107, '', 'restaurant, fancy, dinner, lunch', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '1', '/companies/46/lucha-restaurant_1675354828.jpeg', '2023-02-02 22:20:49', '2023-02-02 16:20:49', 78, '41.9185819', '12.51433', '0', 0, 380, '0.0', '0.0', 2, 20, 0, 0, NULL, 0, 'http://www.facebook.com/cmsjunkie', '', NULL, '', '', '', 'https://www.instagram.com/', '', 'https://www.whatsapp.com/', '', '+1 555 888 9932', 'The food taste like never before!', 0, 0, '', '', '', '', '/companies/46/lucha-restaurant_1675354837.jpeg', '0000-00-00', '0000-00-00', NULL, '+00:00', '2016', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', '10 - 20'),
(48, 'Fashion Inc.', 'fashion-inc', NULL, 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.', '<p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.</p>\r\n<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.</p>', '', '', '', 'Berbenno', 'Milano', 'Bergamo', 'Lombardy', '', 107, '', 'art, fashion', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '2', '/companies/48/fashion-inc_1675354768.jpeg', '2023-02-02 22:19:50', '2023-02-02 16:19:50', 8, '45.8084878', '9.5768675', '0', 0, 380, '0.0', '0.0', 2, 23, 0, 0, NULL, 0, '', 'http://www.twitter.com/cmsjunkie', NULL, '', 'https://al.linkedin.com/', 'https://www.youtube.com/', '', 'https://www.pinterest.com/', '', '', '+1 555 888 9932', 'Fashion is art!', 0, 0, '', '', '', '', '/companies/48/fashion-inc_1675354777.jpeg', '0000-00-00', '0000-00-00', NULL, '-11:00', '2018', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(50, 'Sun Motors', 'life-in-pages', NULL, 'Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.', '<p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>\r\n<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', '', '', '11', 'Bahnhofsplatz', 'Frankfurt (Oder)', 'Brandenburg', '', '', 54, '', 'vehicle, motor', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '5', '/companies/50/life-in-pages_1675354734.jpeg', '2023-02-02 22:19:13', '2023-02-02 16:19:13', 74, '52.3365359', '14.5466945', '0', 0, 380, '0.0', '0.0', 2, 16, 0, 0, NULL, 0, 'http://www.facebook.com/cmsjunkie', 'http://www.twitter.com/cmsjunkie', NULL, 'https://www.skype.com/en/', 'https://al.linkedin.com/', '', '', '', '', '', '+1 555 888 9932', 'Quality in every extra mile!', 0, 0, '', '', '', '', '/companies/50/life-in-pages_1675354738.jpeg', '0000-00-00', '0000-00-00', NULL, '+01:00', '2014', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(51, 'All About Travel', 'all-about-travel', NULL, 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>\r\n<p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>', '', '', '32', 'Victoria', 'Victoria', 'Mackay Regional', '', '', 14, '', 'travel, adventure, cities, countries', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '7', '/companies/51/all-about-travel_1675354695.jpeg', '2023-02-02 22:18:37', '2023-02-02 16:18:37', 60, '-21.2309116', '148.9741517', '0', 0, 380, '0.0', '0.0', 2, 23, 0, 0, NULL, 0, '', '', NULL, '', '', 'https://www.youtube.com/', 'https://www.instagram.com/', 'https://www.pinterest.com/', '', '', '+1 555 888 9932', 'All about travel your way!', 0, 0, '', '', '', '', '/companies/51/all-about-travel_1675354703.jpeg', '0000-00-00', '0000-00-00', NULL, '+10:00', '2010', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 6, 0, '', ''),
(54, 'Harbour 99', 'harbour-nine', NULL, 'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\r\n<p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. </p>', '', '', '24', 'Wandsbeker Marktstraße', 'Hamburg', 'Wandsbek', '', '', 54, '', 'yacht, club, cruise', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '1', '/companies/54/harbour-nine_1675354602.jpeg', '2023-02-02 22:17:56', '2023-02-02 16:17:56', 60, '53.5706806', '10.0624622', '0', 0, 380, '0.0', '0.0', 2, 9, 0, 0, NULL, 0, '', 'http://www.twitter.com/cmsjunkie', NULL, '', '', '', 'https://www.instagram.com/', '', 'https://www.whatsapp.com/', '', '+1 555 888 9932', 'High life with Harbour Heaven!', 0, 0, '', '', '', '', '/companies/54/harbour-nine_1675354608.jpeg', '0000-00-00', '0000-00-00', NULL, '-08:00', '2018', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(55, 'Seaside Yacht Club', 'seaside-yacht-club', NULL, 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.', '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>\r\n<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. </p>', '', '', '3', 'Carrer del Temple', 'Palma', 'Canamunt', 'Balearic Islands', '', 65, '', 'yacht, seaside', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '5', '/companies/55/seaside-yacht-club_1675354565.jpeg', '2023-02-02 22:16:25', '2023-02-02 16:16:25', 60, '39.5675897', '2.6548577', '0', 0, 380, '0.0', '0.0', 2, 33, 0, 0, NULL, 0, '', 'http://www.twitter.com/cmsjunkie', NULL, 'https://www.skype.com/en/', '', '', '', 'https://www.pinterest.com/', '', '', '', 'Think Seaside Yacht Club!', 0, 0, '', '', '', '', '/companies/55/seaside-yacht-club_1675354573.jpeg', '0000-00-00', '0000-00-00', NULL, '-08:00', '2014', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 0, '', ''),
(56, 'Better together', 'better-together', NULL, 'Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\r\n<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. </p>', '', '', '', 'PATH', 'Old Toronto', 'Entertainment District', 'Ontario', '', 36, '', '', NULL, '+1 555 888 9932', 'office@site.com', NULL, 1, '7', '/companies/56/better-together_1675354540.jpeg', '2023-02-02 22:15:43', '2023-02-02 16:15:43', 42, '43.65238435', '-79.38356765', '0', 0, 380, '0.0', '0.0', 2, 22, 0, 0, NULL, 0, 'http://www.facebook.com/cmsjunkie', 'http://www.twitter.com/cmsjunkie', NULL, '', '', 'https://www.youtube.com/', '', '', 'https://www.whatsapp.com/', '', '', 'There’s power in unity!', 0, 0, '', '', '', '', '/companies/56/better-together_1675354530.jpeg', '0000-00-00', '0000-00-00', NULL, '-06:00', '', '', NULL, '', NULL, 1, NULL, 1, NULL, NULL, 0, 0, 0, 1, '', '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_activity_city` (`id`, `company_id`, `city_id`) VALUES
(24, 1, -1),
(27, 4, -1),
(23, 8, -1),
(26, 9, -1),
(25, 12, -1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_announcements` (`id`, `call_to_action`, `company_id`, `title`, `description`, `icon`, `button_text`, `button_link`, `status`, `expiration_date`) VALUES
(1, 1, 8, 'New course available!', 'We are proud to announce launch of new brCras eget lorem libero. Nulla facilisi. Aliquam ac volutpat erat. Etiam vulputate pellentesque maximus. Nunc id metus nunc. anch', 'la la-bullhorn', 'Join now', 'https://www.cmsjunkie.com/j-businessdirectory', 1, '0000-00-00'),
(2, 3, 8, 'Free photo session!', 'Etiam vulputate pellentesque maximus. Nunc id metus nunc. Cras eget lorem libero. Nulla facilisi. Aliquam ac volutpat erat. ', 'la la-calendar-check-o', 'Book now!', 'https://www.cmsjunkie.com/j-businessdirectory', 1, '0000-00-00'),
(3, 4, 8, 'Save 40% on our personal photo session', 'Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc. ', 'la la-cart-plus', 'Buy now!', 'https://www.cmsjunkie.com/j-businessdirectory', 1, '0000-00-00'),
(4, 1, 31, 'All locations are open now!', 'Nunc tempus leo in arcu mattis lobortis. Fusce ut sollicitudin nulla. Lorem ipsum dolor sit amet, consectetur adipiscing elit.  ', 'la la-bullhorn', 'Book a table!', 'https://www.cmsjunkie.com/j-businessdirectory', 1, '0000-00-00');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_attachments` (`id`, `type`, `object_id`, `name`, `path`, `status`, `ordering`) VALUES
(22, 2, 13, 'Treatment instructions', '/offers/13/SPA_Woman_Face_1280_770_d-1426864490.jpg', 1, 0),
(27, 1, 12, 'Healthcare catalog', '/companies/12/natural-health-1426863987.jpg', 1, 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_category` (`companyId`, `categoryId`) VALUES
(1, 53),
(1, 75),
(4, 5),
(4, 60),
(5, 42),
(6, 5),
(6, 17),
(8, 13),
(8, 24),
(9, 11),
(9, 46),
(20, 290),
(20, 292),
(30, 79),
(33, 64),
(43, 5),
(43, 63),
(46, 78),
(48, 8),
(48, 35),
(48, 38),
(50, 17),
(50, 74),
(51, 60),
(54, 60),
(55, 5),
(55, 60),
(56, 42),
(57, -1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_contact` (`id`, `companyId`, `contact_name`, `contact_function`, `contact_department`, `contact_job_title`, `contact_email`, `contact_phone`, `contact_fax`) VALUES
(3, 12, 'Joanne Smith', NULL, '', NULL, 'joaan@joann.com', '+1 323 999 672', '+1 323 231 754'),
(4, 9, 'John rice', NULL, '', NULL, 'john@organic.co', '+1 221 359 888', ''),
(6, 7, 'John Doe', NULL, '', NULL, 'john@john.com', '01 232 495 999', ''),
(8, 29, 'John Smith', NULL, '', NULL, 'john@smith.com', '+1 221 359 888', ''),
(10, 31, 'Chef Michael', NULL, '', NULL, 'joaan@joann.com', '', ''),
(12, 33, 'Brian Lindow', NULL, '', NULL, 'office@site.com', '+1 323 999 672', '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_events` (`id`, `company_id`, `name`, `alias`, `short_description`, `description`, `meta_title`, `meta_description`, `meta_keywords`, `price`, `type`, `start_date`, `start_time`, `end_date`, `end_time`, `address`, `street_number`, `city`, `county`, `province`, `area`, `location`, `latitude`, `longitude`, `featured`, `created`, `view_count`, `approved`, `state`, `recurring_id`, `contact_phone`, `contact_email`, `doors_open_time`, `booking_open_date`, `booking_close_date`, `booking_open_time`, `booking_close_time`, `show_start_time`, `show_end_time`, `show_end_date`, `show_doors_open_time`, `currency_id`, `total_tickets`, `expiration_email_date`, `main_subcategory`, `user_id`, `enable_subscription`, `postalCode`, `countryId`, `time_zone`, `recurring_info`, `attendance_mode`, `attendance_url`, `min_age`, `max_age`, `ticket_url`) VALUES
(20, 51, 'Trip to Bali', 'trip-to-bali', 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.', '<p>Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.</p>\r\n<p>ouTemporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>', '', '', '', '70.00', 1, '2023-01-13', '00:00:00', '2023-02-09', '00:00:00', 'Cinere', '13', 'Bali', 'West Java', '', '', '', '-6.3472811', '106.7790138', 0, '2023-02-02 22:38:53', 4, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 197, 0, 0, '', 98, '-11:00', NULL, 3, '', 2, NULL, ''),
(10, 43, 'Bike Adventure', 'bike-adventure', 'In faucibus posuere purus, at egestas dolor dictum ac. Maecenas volutpat lectus eget purus hendrerit, sit amet hendrerit diam mattis. Nulla imperdiet metus ac metus molestie, sed imperdiet leo eleifend.', '<p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>\r\n<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.</p>', '', '', '', '10.00', 2, '2023-09-01', '17:00:00', '2024-12-02', '20:00:00', 'Gamble Avenue', '', 'Toronto', 'Ontario', '', '', 'Gamble Avenue, Toronto, Canada', '43.6903809', '-79.3491781', 0, '2023-02-02 22:36:05', 12, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, NULL, '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 171, 0, 0, '', 36, '-11:00', NULL, 1, '', NULL, NULL, ''),
(11, 8, 'Violin concert', 'violin-concert', 'Nulla sagittis pretium sagittis. Aliquam tincidunt sodales dui, a facilisis nisi sollicitudin quis. Sed nec mattis augue. Sed hendrerit odio non mauris fermentum semper.', '<p>Proin posuere nibh libero, ac euismod nulla tincidunt in. Mauris est nunc, fringilla ac facilisis a, ornare a leo. Nam lobortis tortor fringilla, lobortis nisl sit amet, cursus dolor. In at lectus massa. Integer ut nulla dapibus, volutpat nisi vitae, laoreet tellus. Quisque hendrerit blandit leo at dapibus.</p>\r\n<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. </p>', '', '', '', '0', 5, '2023-01-11', '15:00:00', '2024-07-09', '12:00:00', 'Gudrunstraße', '24', 'Vienna', 'Favoriten', '', '', 'Gamble Avenue, Toronto, Canada', '48.1788173', '16.3670371', 1, '2023-02-02 22:36:29', 11, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, NULL, '00:00:00', '00:00:00', 0, 0, 0, 0, 44, 0, NULL, 176, 0, 0, '', 13, '-11:00', NULL, 1, '', NULL, NULL, ''),
(16, 55, 'Festival of Lights', 'festival-of-lights', 'Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur quis autem vel eum iure reprehenderit qui in ea voluptate velit.', '<p> Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.</p>\r\n<p>Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum .</p>', '', '', '', '50.00', 4, '2023-04-01', '11:00:00', '2023-04-04', '01:00:00', 'Placa de Sant Jaume', '', 'Barcelona', 'Catalonia', '', '', '', '41.3828939', '2.1774322', 0, '2023-02-02 22:37:20', 6, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', '2023-01-01', '2023-04-04', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 181, 0, 0, '', 65, '-11:00', NULL, 1, '', 10, NULL, ''),
(17, 8, 'Photography Expo', 'photography-course', 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur.', '<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>\r\n<p>Cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. </p>', '', '', '', '50.00', 5, '2023-01-13', '00:00:00', '2023-02-07', '00:00:00', 'Historic Trail', '', 'San Francisco', 'Garden Island Condoville', '', '', '', '37.762725700000004', '-122.45825681806235', 0, '2023-02-02 22:37:46', 5, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', '2023-01-13', '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 192, 0, 0, '', 226, '-11:00', NULL, 1, '', 10, NULL, ''),
(18, 48, 'Radiance Show', 'radiance-show', 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.', '<p>Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus.</p>\r\n<p>Ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.Efasxcepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>', '', '', '', '50.00', 3, '2023-01-13', '00:00:00', '2023-03-04', '00:00:00', 'Foro Buonaparte', '', 'Milan', 'Lombardy', '', '', '', '45.4676138', '9.179283', 0, '2023-02-02 22:38:02', 7, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', '2023-01-13', '2023-03-11', '00:00:00', '00:00:00', 0, 0, 0, 0, 44, 100, NULL, 174, 0, 0, '', 107, '-11:00', NULL, 2, '', 10, NULL, ''),
(19, 8, 'City Photo Contest', 'city-photo-contest', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>\r\n<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>', '', '', '', '30.00', 5, '2023-01-13', '00:00:00', '2023-02-09', '00:00:00', 'Rütistrasse', '1', 'Zurich', 'District Zurich', '', '', '', '47.3684437', '8.5608667', 0, '2023-02-02 22:38:32', 2, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', '2023-01-13', '2023-02-08', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 183, 0, 0, '', 41, '-11:00', NULL, 3, '', 10, NULL, ''),
(15, 54, 'Design Trends', 'design-trends', 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.', '<p>Saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>\r\n<p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.</p>', '', '', '', '25', 5, '2023-09-01', '11:00:00', '2024-12-02', '18:30:00', 'Hollywood Boulevard', '', 'Los Angeles', 'California', '', '', 'New York, US', '34.10177079166667', '-118.35883383333334', 1, '2023-02-02 22:36:53', 7, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '07:00:00', '2019-02-11', '0000-00-00', '00:00:00', '00:00:00', 1, 1, 0, 1, 143, 100, NULL, 215, 0, 1, '', 226, '-11:00', NULL, 1, '', NULL, NULL, ''),
(21, 46, 'Wine Testing', 'wine-testing', 'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur.', '<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\r\n<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? </p>', '', '', '', '50.00', 3, '2023-01-13', '00:00:00', '2023-03-11', '00:00:00', 'Via Manzoni', '', 'Roma', 'Lazio', '', '', '', '42.2072966', '12.4793573', 0, '2023-02-02 22:39:14', 3, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 155, 0, 0, '', 107, '-11:00', NULL, NULL, '', 21, NULL, ''),
(22, 51, 'Mountain Hiking', 'mountain-hiking', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.', '<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>\r\n<p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>', '', '', '', '50.00', 2, '2023-01-13', '00:00:00', '2024-01-20', '00:00:00', 'Schützenstrasse', '', 'Basel', 'Bezirk Waldenburg', '', '', '', '47.3809695', '7.8112837', 0, '2023-02-02 22:40:06', 7, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 171, 0, 0, '', 41, '-11:00', NULL, 1, '', 15, NULL, ''),
(23, 46, 'Cooking Class', 'cooking-class', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Et harum quidem rerum facilis est et expedita distinctio.</p>\r\n<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>\r\n<p> </p>', '', '', '', '30.00', 2, '2023-01-13', '00:00:00', '2024-01-18', '00:00:00', 'Viale Gorizia', '', 'Rome', 'Lazio', '', 'Capitale', '', '41.9185819', '12.51433', 0, '2023-02-02 22:40:24', 2, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 154, 0, 0, '', 107, '-11:00', NULL, 3, '', 15, NULL, ''),
(24, 54, 'Celebration Party', 'celebration-party', 'Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.', '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\r\n<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>', '', '', '', '70.00', 3, '2023-01-13', '00:00:00', '2024-01-30', '00:00:00', 'Lexington Avenue', '', 'New York', 'New York County', '', '', '', '40.7830439', '-73.9527658', 0, '2023-02-02 22:40:47', 3, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 176, 0, 0, '', 226, '-11:00', NULL, 3, '', 10, NULL, ''),
(25, 48, 'Fashion Week', 'fashion-week', 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deseru', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Etiam lacinia sapien in nulla ultricies eleifend. Aliquam feugiat vitae magna id aliquet.</p>\r\n<p>Nam sem ligula, sollicitudin in placerat quis, scelerisque elementum tortor. Aenean imperdiet dictum lorem. Praesent sit amet arcu id mi hendrerit scelerisque. Integer tincidunt massa eget lectus laoreet porttitor. Fusce quis luctus orci.Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? </p>', '', '', '', '50.00', 3, '2023-01-13', '00:00:00', '2023-03-01', '00:00:00', 'Ripa di Porta Ticinese', '', 'Milan', 'Lombardy', '', '', '', '45.4496228', '9.165181', 0, '2023-02-02 22:42:03', 10, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 174, 0, 0, '', 107, '-11:00', NULL, 2, '', 10, NULL, ''),
(26, 50, 'London Expo 2024', 'london-expo-2024', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '<p>Mauris quis finibus tellus, eget dignissim tellus. Cras eget lorem libero. Nulla facilisi. Aliquam ac volutpat erat. Nunc id metus nunc. Phasellus finibus ante et finibus viverra. Mauris scelerisque dignissim mauris, sit amet congue nisi sagittis vel. Etiam lacinia sapien in nulla ultricies eleifend. Aliquam feugiat vitae magna id aliquet. Nam sem ligula, sollicitudin in placerat quis, scelerisque elementum tortor. Aenean imperdiet dictum lorem. Praesent sit amet arcu id mi hendrerit scelerisque. Integer tincidunt massa eget lectus laoreet porttitor. Fusce quis luctus orci.</p>\r\n<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.</p>', '', '', '', '30.00', 1, '2023-01-13', '00:00:00', '2024-01-18', '00:00:00', 'Steglitz-Zehlendorf', '115', 'Berlin', 'Nikolassee', '', '', '', '52.4187324', '13.1964052', 0, '2023-02-02 22:42:30', 4, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 210, 0, 0, '', 54, '-11:00', NULL, 1, '', 5, NULL, ''),
(27, 33, 'Funland', 'funland', 'Mauris quis finibus tellus, eget dignissim tellus. Cras eget lorem libero. Nulla facilisi. Aliquam ac volutpat erat. Nunc id metus nunc. Phasellus finibus ante et finibus viverra.', '<p>Etiam lacinia sapien in nulla ultricies eleifend. Aliquam feugiat vitae magna id aliquet. Nam sem ligula, sollicitudin in placerat quis, scelerisque elementum tortor. Aenean imperdiet dictum lorem. Praesent sit amet arcu id mi hendrerit scelerisque. Integer tincidunt massa eget lectus laoreet porttitor. Fusce quis luctus orci.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\r\n<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. </p>', '', '', '', '70.00', 2, '2023-01-13', '00:00:00', '2023-02-10', '00:00:00', 'Broadgate', '', 'City of London', 'Bishopsgate', '', '', '', '51.519304', '-0.0828012', 0, '2023-02-02 22:42:49', 6, 1, 1, 0, '+1 555 888 9932', 'office@site.com', '00:00:00', NULL, '0000-00-00', '00:00:00', '00:00:00', 0, 0, 0, 0, 143, 100, NULL, 171, 0, 0, '', 224, '-11:00', NULL, 1, '', 5, NULL, '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_event_associated_items` (`event_id`, `company_id`) VALUES
(11, 7),
(11, 8),
(11, 9),
(11, 32),
(12, 1),
(12, 7),
(12, 8),
(12, 9),
(13, 1),
(13, 7),
(13, 8),
(13, 32);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_event_category` (`eventId`, `categoryId`) VALUES
(10, 171),
(10, 199),
(11, 176),
(15, 215),
(16, 181),
(17, 183),
(17, 192),
(18, 174),
(19, 183),
(19, 192),
(20, 197),
(21, 154),
(21, 155),
(22, 171),
(22, 200),
(23, 154),
(24, 176),
(25, 174),
(26, 190),
(26, 191),
(26, 210),
(27, 171);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_event_pictures` (`id`, `eventId`, `picture_info`, `picture_path`, `picture_enable`, `picture_title`) VALUES
(365, 20, '', '/events/20/bali1.jpg', 1, ''),
(366, 20, '', '/events/20/bali2.jpg', 1, ''),
(367, 20, '', '/events/20/bali4.jpeg', 1, ''),
(368, 20, '', '/events/20/bali3.jpeg', 1, ''),
(374, 24, '', '/events/24/yacht_party2.jpg', 1, ''),
(375, 24, '', '/events/24/yacht_party1.jpg', 1, ''),
(382, 27, '', '/events/27/funland3.jpg', 1, ''),
(383, 27, '', '/events/27/funland2.jpg', 1, ''),
(384, 27, '', '/events/27/funland1.jpg', 1, ''),
(385, 27, '', '/events/27/funland4.jpg', 1, ''),
(359, 16, '', '/events/16/lantern2.jpg', 1, ''),
(360, 16, '', '/events/16/lantern1.jpg', 1, ''),
(351, 11, '', '/events/11/violin4.jpg', 1, ''),
(352, 11, '', '/events/11/violin1.jpg', 1, ''),
(353, 11, '', '/events/11/violin3.jpg', 1, ''),
(354, 11, '', '/events/11/violin2.jpg', 1, ''),
(355, 11, '', '/events/11/violin5.jpg', 1, ''),
(373, 23, '', '/events/23/cooking_class.jpg', 1, ''),
(376, 25, '', '/events/25/fashioninc2.jpg', 1, ''),
(377, 25, '', '/events/25/fashioninc1.jpg', 1, ''),
(369, 21, '', '/events/21/wine_testing1.jpg', 1, ''),
(370, 21, '', '/events/21/wine_testing2.jpg', 1, ''),
(348, 10, '', '/events/10/bike3.jpg', 1, ''),
(349, 10, '', '/events/10/bike1.jpg', 1, ''),
(350, 10, '', '/events/10/bike2.jpg', 1, ''),
(380, 26, '', '/events/26/londonexpo1.jpg', 1, ''),
(381, 26, '', '/events/26/londonexpo2.jpg', 1, ''),
(363, 18, '', '/events/18/radiance1.jpg', 1, ''),
(361, 17, '', '/events/17/photographyexpo1.jpg', 1, ''),
(362, 17, '', '/events/17/photographyexpo2.jpg', 1, ''),
(358, 15, '', '/events/15/design3.jpg', 1, ''),
(356, 15, '', '/events/15/design2.jpg', 1, ''),
(357, 15, '', '/events/15/design1.jpg', 1, ''),
(364, 19, '', '/events/19/citycontest1.jpg', 1, ''),
(371, 22, '', '/events/22/hiking1.jpg', 1, ''),
(372, 22, '', '/events/22/hiking2.jpg', 1, '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_event_tickets` (`id`, `event_id`, `name`, `description`, `price`, `published`, `min_booking`, `max_booking`, `quantity`, `ordering`) VALUES
(28, 15, 'Premium Ticket', 'Premium Ticket', '30.00', 1, 1, 5, 100, 0),
(29, 15, 'Gold Ticket', 'Gold Ticket', '35.00', 1, 1, 5, 100, 0),
(30, 15, 'Basic Ticket', 'Basic Ticket', '25.00', 1, 1, 5, 100, 0),
(31, 16, 'First Day', 'First Day', '25.00', 1, 1, 5, 100, 0),
(32, 16, 'Full Package', 'Full Package', '60.00', 1, 1, 5, 100, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_event_types` (`id`, `name`, `ordering`) VALUES
(1, 'Seminar', 0),
(2, 'Training', 0),
(3, 'Workshop', 0),
(4, 'Party', 0),
(5, 'Presentation', 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_membership` (`company_id`, `membership_id`) VALUES
(9, 2),
(30, 1),
(30, 2),
(30, 3),
(33, 1),
(33, 2),
(43, 2),
(43, 3),
(46, 1),
(46, 2),
(48, 1),
(50, 1),
(50, 2),
(50, 3),
(54, 2),
(54, 3),
(55, 1),
(55, 2),
(56, 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_offers` (`id`, `companyId`, `currencyId`, `subject`, `description`, `meta_title`, `meta_description`, `meta_keywords`, `price`, `specialPrice`, `price_base`, `price_base_unit`, `special_price_base`, `special_price_base_unit`, `total_coupons`, `startDate`, `endDate`, `state`, `approved`, `offerOfTheDay`, `viewCount`, `alias`, `address`, `street_number`, `city`, `short_description`, `county`, `province`, `area`, `publish_start_date`, `publish_end_date`, `view_type`, `url`, `article_id`, `latitude`, `longitude`, `featured`, `created`, `show_time`, `publish_start_time`, `publish_end_time`, `expiration_email_date`, `main_subcategory`, `enable_offer_selling`, `min_purchase`, `max_purchase`, `quantity`, `user_id`, `postalCode`, `type`, `countryId`, `price_text`, `offer_type`, `time_zone`, `item_type`, `notify_offer_quantity`, `use_stock_price`, `contact_email`, `add_to_price_list`, `review_score`) VALUES
(3, 9, 143, 'Garden Arrangements', '<p>Etiam eget urna est. Nullam turpis magna, pharetra id venenatis id, adipiscing at velit. In lobortis ornare congue. Sed vitae neque lacus, et rutrum lorem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque quis rhoncus felis. Sed adipiscing tellus laoreet neque adipiscing ac euismod felis gravida. Aenean fermentum, nulla non adipiscing tristique, lacus justo ornare nunc, eu aliquam nunc massa non justo.</p>\r\n<p>Sed at sapien vitae eros luctus condimentum non at libero. Morbi id arcu nec mi suscipit molestie. Integer ullamcorper suscipit erat, quis convallis quam interdum convallis. Sed lectus justo, vehicula et euismod rhoncus, tempus vel magna. Pellentesque laoreet, odio id iaculis bibendum, erat quam mollis urna, ac pretium neque mi vitae nisl. Fusce euismod bibendum risus vel suscipit. Suspendisse sapien tortor, vehicula sed lobortis tempus, pellentesque ut lectus.</p>', '', '', '', '120.00', '90.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2024-01-02', 1, 1, 1, 30, 'garden-arrangements', 'Rue des Chasseurs', '7100', 'La Louvière', 'Etiam eget urna est. Nullam turpis magna, pharetra id venenatis id, adipiscing at velit. In lobortis ornare congue. Sed vitae neque lacus, et rutrum lorem. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;', 'Hainaut', '', '', '2023-01-04', '2024-01-01', 1, '', 0, '50.4570467', '4.171554', 0, '2023-02-02 22:29:04', 0, '00:00:00', '00:00:00', NULL, 93, 2, NULL, NULL, NULL, 0, '', 1, 20, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(32, 5, 143, 'Yoga for Health', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam. Quisque pellentesque libero eget dui elementum scelerisque.</p>\r\n<p>Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam purus.</p>', '', '', '', '70.00', '50.00', '0.00', '', '0.00', '', 0, '2023-01-12', '2024-02-13', 1, 1, 0, 4, 'yoga-meditation-day', 'Pracharat Bamphen', '12', 'Boyolali', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'Central Java', '', '', '2023-01-12', '2024-02-13', 1, NULL, NULL, '-7.3300335', '110.6666887', 0, '2023-02-02 22:33:57', 0, '00:00:00', '00:00:00', NULL, 140, 2, NULL, NULL, NULL, 0, '', 1, 98, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(35, 56, 143, 'Counselling Session', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam. Quisque pellentesque libero eget dui elementum scelerisque.</p>\r\n<p>Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam purus.</p>', '', '', '', '80.00', '45.00', '0.00', '', '0.00', '', 0, '2023-01-03', '2024-01-26', 1, 1, 0, 18, 'counselling-session', 'Steeles Avenue', '13850', 'Ontario', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'San Bernardino', '', '', '2023-01-10', '2024-02-09', 1, NULL, NULL, '34.065846', '-117.6484304', 0, '2023-02-02 22:35:02', 0, '00:00:00', '00:00:00', NULL, 89, 2, NULL, NULL, NULL, 0, '', 1, 226, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(28, 8, 143, 'Landscape Photography', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl. Vestibulum quis ornare dui.</p>\r\n<p>Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id. Phasellus sed feugiat nunc, sed pharetra risus. Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '30.00', '20.00', '0.00', '', '0.00', '', 0, '2023-01-01', '2024-01-01', 1, 1, 0, 6, 'photograpy-course', 'Florida', '12', 'Orlando', '', 'Orange County', '', '', '2023-01-02', '2024-01-01', 1, NULL, NULL, '28.5421109', '-81.3790304', 0, '2023-02-02 22:32:28', 0, '00:00:00', '00:00:00', NULL, 115, 2, NULL, NULL, NULL, 0, '', 1, 226, '', NULL, '-11:00', 1, NULL, NULL, '', 0, '0.0'),
(29, 8, 143, 'Zen Photography Course', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl. Vestibulum quis ornare dui.</p>\r\n<p>Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id. Phasellus sed feugiat nunc, sed pharetra risus. Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '180.00', '120.00', '0.00', '', '0.00', '', 0, '2021-02-02', '2023-12-13', 1, 1, 0, 8, 'zen-photography-course', 'Golden Horseshoe', '12', 'Toronto', 'Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula.', 'Ontario', '', '', '2021-10-11', '2023-05-31', 1, NULL, NULL, '43.6534817', '-79.3839347', 0, '2023-02-02 22:32:52', 0, '00:00:00', '00:00:00', NULL, 124, 1, 1, 10, 100, 0, '', 1, 36, '', NULL, '-11:00', 1, 100, NULL, 'office@site.com', 0, '0.0'),
(18, 46, 143, 'Chinese Night', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam. Quisque pellentesque libero eget dui elementum scelerisque. Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla.</p>\r\n<p>Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam purus. P</p>', '', '', '', '95.00', '75.00', '0.00', '', '0.00', '', 0, '2023-01-12', '2024-11-02', 1, 1, 0, 9, 'chinese-night', 'Viale Gorizia', '12', 'Rome', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'Roma Capitale', '', '', '2023-01-12', '2024-11-01', 1, '', 0, '41.9185819', '12.51433', 0, '2023-02-02 22:29:47', 0, '00:00:00', '00:00:00', NULL, 86, 2, NULL, NULL, NULL, 0, '', 1, 107, '', NULL, '-11:00', 1, NULL, NULL, '', 0, '0.0'),
(33, 51, 143, 'Jungle Cruise', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam. Quisque pellentesque libero eget dui elementum scelerisque.</p>\r\n<p>Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam purus.</p>', '', '', '', '50.00', '40.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2024-01-02', 1, 1, 0, 4, 'jungle-cruise', 'Rue Montorgueil', '12', 'Tabatinga', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'North Region', '', '', '2023-01-03', '2024-01-01', 1, NULL, NULL, '-4.3336997', '-69.865395', 0, '2023-02-02 22:34:23', 0, '00:00:00', '00:00:00', NULL, 129, 2, NULL, NULL, NULL, 0, '', 1, 29, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(34, 51, 143, 'Travel to Rome', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam. Quisque pellentesque libero eget dui elementum scelerisque.</p>\r\n<p>Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam purus.</p>', '', '', '', '70.00', '60.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2024-02-01', 1, 1, 0, 13, 'travel-to-rome', 'Viale Gorizia', '12', 'Rome', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'Roma Capitale', '', '', '2023-01-16', '2024-01-16', 1, NULL, NULL, '41.9185819', '12.51433', 0, '2023-02-02 22:34:44', 0, '00:00:00', '00:00:00', NULL, 129, 2, NULL, NULL, NULL, 0, '', 1, 107, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(21, 33, 143, 'Roller Coaster', '<p>Donec eleifend purus nulla, non vehicula nisi dictum quis. Maecenas in odio purus. Etiam vulputate nisi eget pharetra tincidunt. Morbi et eros consectetur, ultricies ligula quis, ullamcorper neque. Donec pellentesque felis vel luctus tempus. Curabitur blandit dui purus, non viverra magna consequat vitae. Nunc volutpat malesuada orci vitae varius. Suspendisse accumsan nunc non dictum bibendum. Sed suscipit id ipsum ut tincidunt. Vivamus condimentum diam at condimentum scelerisque. Etiam vulputate pellentesque maximus. Curabitur tincidunt nibh et nisl porttitor, eget ultrices turpis maximus.</p>\r\n<p>Fusce molestie elit eget felis cursus volutpat. Nam tincidunt lacus nec massa sagittis, eu dapibus purus bibendum. Ut hendrerit, felis nec congue posuere, lorem urna eleifend est, ac venenatis quam augue a arcu. Nullam sit amet finibus diam. Aenean placerat gravida mi at eleifend. Sed felis nulla, tempus ac vulputate vitae, condimentum vel nunc. Nam egestas, nunc sit amet tempor pellentesque, sapien justo aliquam tortor, at posuere elit purus eget orci. Aliquam hendrerit enim turpis, vitae ultrices libero accumsan nec.</p>', '', '', '', '50.00', '30.00', '0.00', '', '0.00', '', 10, '2023-01-02', '2024-01-02', 1, 1, 0, 18, 'roller-coaster', 'Hopkins Avenue', '55', 'New York', 'Donec eleifend purus nulla, non vehicula nisi dictum quis. Maecenas in odio purus. Etiam vulputate nisi eget pharetra tincidunt. Morbi et eros consectetur, ultricies ligula quis, ullamcorper neque. Donec pellentesque felis vel luctus tempus.', 'Middlesbrough', '', '', '2023-01-13', '2024-01-01', 1, '', 0, '40.559463', '-74.134697', 1, '2023-02-02 22:30:15', 0, '00:00:00', '00:00:00', NULL, 129, 2, 1, 10, 100, 0, '', 1, 226, '', 6, '+01:00', 1, 10, NULL, 'office@site.com', 0, '0.0'),
(31, 9, 143, 'Balloon Bouquets', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc scelerisque enim ut magna vulputate feugiat. Suspendisse rutrum lectus et diam congue, sed pretium eros facilisis. Pellentesque pretium lectus orci, non accumsan velit vestibulum a. Fusce orci dui, tincidunt et tortor non, auctor rutrum mauris. Vestibulum sed ultricies enim, at ultrices quam. Quisque pellentesque libero eget dui elementum scelerisque.</p>\r\n<p>Pellentesque tempor arcu in hendrerit molestie. Phasellus euismod nisi in malesuada convallis. Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. Mauris mi tortor, maximus eu risus vitae, bibendum vestibulum leo. Nulla vitae efficitur lectus. Aenean aliquet massa magna. Nullam at dapibus mi. Vivamus massa nibh, venenatis mattis nibh pretium, pretium volutpat leo. Vestibulum eu sem elit. Duis consequat, magna id semper elementum, est nisi pharetra orci, eget molestie diam purus sed sem. Vestibulum est purus, sollicitudin eget lectus ut, molestie aliquam purus.</p>', '', '', '', '100.00', '80.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2024-01-02', 1, 1, 0, 7, 'balloon-bouquets', 'Stationsplein', '4545', 'Rotterdam', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc interdum mauris vitae urna ultrices, et fermentum magna convallis. Nullam quis vulputate magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'Centrum', '', '', '2023-01-12', '2024-01-01', 1, NULL, NULL, '51.8530467', '4.476376756849403', 0, '2023-02-02 22:33:37', 0, '00:00:00', '00:00:00', NULL, 109, 2, NULL, NULL, NULL, 0, '', 1, 161, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(30, 8, 143, 'Lens Scape Course', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl. Vestibulum quis ornare dui.</p>\r\n<p>Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id. Phasellus sed feugiat nunc, sed pharetra risus. Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '125.00', '65.00', '0.00', '', '0.00', '', 0, '2022-01-31', '2024-12-11', 1, 1, 0, 6, 'lens-scape-course', 'Rue Montorgueil', '12', 'Val-de-Marne', 'Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula.', 'Ile-de-France', '', '', '2023-01-02', '2024-05-16', 1, NULL, NULL, '48.774489349999996', '2.4543321444588204', 0, '2023-02-02 22:33:13', 0, '00:00:00', '00:00:00', NULL, 124, 1, 1, 10, 100, 0, '', 1, 72, '', NULL, '-11:00', 1, 100, NULL, 'office@site.com', 0, '0.0'),
(24, 46, 44, 'Menu of the Day', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl. Vestibulum quis ornare dui. Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id.</p>\r\n<p>Phasellus sed feugiat nunc, sed pharetra risus. Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '50.00', '30.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2024-01-05', 1, 1, 0, 11, 'menu-of-the-day', 'Via del Corso', '12', 'Abruzzo', '', 'L\'Aquila', '', '', '2023-01-03', '2024-01-01', 1, NULL, NULL, '41.8214935', '13.545353', 0, '2023-02-02 22:30:39', 0, '00:00:00', '00:00:00', NULL, 143, 2, NULL, NULL, NULL, 0, '', 1, 107, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0'),
(25, 46, 143, 'Friends night - 50% off on all drinks', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl. Vestibulum quis ornare dui. Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id.</p>\r\n<p>Phasellus sed feugiat nunc, sed pharetra risus. Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '60.00', '30.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2023-11-16', 1, 1, 0, 3, 'morgan-s-terrace', 'Via Cola di Rienzo', '4545', 'Rome', '', 'Roma Capitale', '', '', '2023-01-03', '2023-10-12', 1, NULL, NULL, '41.9079178', '12.4649932', 0, '2023-02-02 22:31:04', 0, '00:00:00', '00:00:00', NULL, 143, 2, NULL, NULL, NULL, 0, '', 1, 107, '', NULL, '-11:00', 1, NULL, NULL, '', 0, '0.0'),
(26, 4, 143, 'Water Sports Activities', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl. Vestibulum quis ornare dui. Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id. Phasellus sed feugiat nunc, sed pharetra risus.</p>\r\n<p>Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '50.00', '30.00', '0.00', '', '0.00', '', 0, '2023-01-02', '2024-01-23', 1, 1, 0, 6, 'water-sports-activities', 'Mechanic Street', '12', 'Niagara Falls', '', 'Niagara Region', '', '', '2023-01-03', '2024-01-03', 1, NULL, NULL, '43.062333800000005', '-79.05271247766484', 0, '2023-02-02 22:31:39', 0, '00:00:00', '00:00:00', NULL, 103, 2, NULL, NULL, NULL, 0, '', 1, 36, '', NULL, '-11:00', 1, NULL, NULL, '', 0, '0.0'),
(27, 56, 143, '50% off Weekend Treat', '<p>Duis faucibus odio quis sapien imperdiet, nec congue turpis pellentesque. Integer mi turpis, eleifend et mollis eu, dapibus quis elit. Pellentesque at turpis urna. Sed scelerisque Diam scelerisque fermentum finibus. Mauris elementum euismod erat sed condimentum. Nulla imperdiet mattis massa, at fermentum erat tristique ac. Praesent eget velit maximus, blandit nisi at, porta ligula. Etiam quis libero nisl.</p>\r\n<p>Vestibulum quis ornare dui. Suspendisse quis lobortis nunc. Pellentesque quis pharetra metus. Phasellus vulputate orci in pharetra feugiat. Etiam vehicula lacus augue, et lacinia turpis mollis id. Phasellus sed feugiat nunc, sed pharetra risus. Etiam eleifend quis lectus et gravida. Nunc pretium nisi id mi maximus mollis. Aliquam tempus dictum mi. Donec cursus pharetra neque, at gravida dolor vestibulum sit amet. Donec quam urna, molestie pharetra venenatis in, tincidunt quis elit. Praesent pharetra eget metus vitae vestibulum. Mauris gravida turpis lorem, aliquam semper justo auc.</p>', '', '', '', '70.00', '35.00', '0.00', '', '0.00', '', 0, '2023-01-03', '2024-02-09', 1, 1, 0, 8, '50-off-weekend-treat', 'Cuba', '12', 'Kansas', '', 'Republic County', '', '', '2023-01-04', '2024-01-04', 1, NULL, NULL, '39.802781', '-97.455872', 0, '2023-02-02 22:32:09', 0, '00:00:00', '00:00:00', NULL, 89, 2, NULL, NULL, NULL, 0, '', 1, 226, '', NULL, '-11:00', 1, NULL, NULL, 'office@site.com', 0, '0.0');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_offer_category` (`offerId`, `categoryId`) VALUES
(3, 93),
(3, 109),
(18, 86),
(18, 88),
(21, 129),
(24, 143),
(24, 146),
(25, 143),
(25, 146),
(26, 103),
(27, 89),
(28, 115),
(28, 116),
(29, 116),
(29, 124),
(30, 116),
(30, 124),
(31, 109),
(32, 140),
(33, 129),
(33, 130),
(34, 129),
(35, 89);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_offer_coupons` (`id`, `user_id`, `offer_id`, `code`, `generated_time`, `order_id`) VALUES
(1, 936, 17, 'EGF-0001', '2019-11-29 13:33:23', NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_offer_pictures` (`id`, `offerId`, `picture_info`, `picture_path`, `picture_enable`, `picture_title`) VALUES
(788, 28, '', '/offers/28/landscape1.jpg', 1, ''),
(790, 29, '', '/offers/29/zen2.jpg', 1, ''),
(791, 29, '', '/offers/29/zen1.jpg', 1, ''),
(792, 29, '', '/offers/29/zen4.jpg', 1, ''),
(793, 29, '', '/offers/29/zen5.jpg', 1, ''),
(794, 29, '', '/offers/29/zen6.jpg', 1, ''),
(806, 33, '', '/offers/33/jungle3.jpg', 1, ''),
(807, 33, '', '/offers/33/jungle1.jpg', 1, ''),
(808, 33, '', '/offers/33/jungle2.jpg', 1, ''),
(809, 33, '', '/offers/33/jungle4.jpg', 1, ''),
(781, 25, '', '/offers/25/dinner1.jpg', 1, ''),
(783, 26, '', '/offers/26/watersports2.jpg', 1, ''),
(784, 26, '', '/offers/26/watersports3.jpg', 1, ''),
(785, 27, '', '/offers/27/weekendtreat1.jpg', 1, ''),
(786, 27, '', '/offers/27/weekendtreat3.jpg', 1, ''),
(787, 27, '', '/offers/27/weekendtreat2.jpg', 1, ''),
(775, 21, '', '/offers/21/roller2.jpg', 1, ''),
(776, 21, '', '/offers/21/roller1.jpg', 1, ''),
(769, 18, '', '/offers/18/chinese1.jpg', 1, ''),
(770, 18, '', '/offers/18/chinese2.jpg', 1, ''),
(799, 31, '', '/offers/31/balloons1.jpg', 1, ''),
(800, 31, '', '/offers/31/balloons2.jpg', 1, ''),
(801, 31, '', '/offers/31/balloons3.jpg', 1, ''),
(802, 31, '', '/offers/31/balloons4.jpg', 1, ''),
(782, 26, '', '/offers/26/watersports1.jpg', 1, ''),
(768, 3, '', '/offers/3/garden1.jpg', 1, ''),
(778, 21, '', '/offers/21/roller3.jpg', 1, ''),
(798, 30, '', '/offers/30/lensscape3.jpg', 1, ''),
(797, 30, '', '/offers/30/lensscape2.jpg', 1, ''),
(795, 30, '', '/offers/30/lensscape4.jpg', 1, ''),
(796, 30, '', '/offers/30/lensscape1.jpg', 1, ''),
(803, 32, '', '/offers/32/yoga2.jpg', 1, ''),
(804, 32, '', '/offers/32/yoga1.jpg', 1, ''),
(805, 32, '', '/offers/32/yoga3.jpg', 1, ''),
(789, 29, '', '/offers/29/zen3.jpg', 1, ''),
(812, 35, '', '/offers/35/counsellingsession.jpg', 1, ''),
(810, 34, '', '/offers/34/rome1.jpg', 1, ''),
(811, 34, '', '/offers/34/rome2.jpg', 1, ''),
(777, 21, '', '/offers/21/roller4.jpg', 1, ''),
(780, 24, '', '/offers/24/daymenu2.jpg', 1, ''),
(767, 3, '', '/offers/3/garden2.jpg', 1, ''),
(779, 24, '', '/offers/24/daymenu1.jpg', 1, '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_pictures` (`id`, `companyId`, `picture_info`, `picture_path`, `picture_enable`, `picture_title`) VALUES
(8821, 8, '', '/companies/8/profesional4.jpg', 1, ''),
(8822, 5, '', '/companies/5/yoga1.jpg', 1, ''),
(8764, 54, '', '/companies/54/harbour5.jpg', 1, ''),
(8765, 54, '', '/companies/54/harbour4.jpg', 1, ''),
(8802, 33, '', '/companies/33/amusement4.jpg', 1, ''),
(8836, 1, '', '/companies/1/wedding4.jpg', 1, ''),
(8837, 1, '', '/companies/1/wedding2.jpg', 1, ''),
(8763, 55, '', '/companies/55/seaside5.jpg', 1, ''),
(8817, 9, '', '/companies/9/flower5.jpg', 1, ''),
(8816, 9, '', '/companies/9/flower2.jpg', 1, ''),
(8825, 5, '', '/companies/5/yoga5.jpg', 1, ''),
(8826, 5, '', '/companies/5/yoga4.jpg', 1, ''),
(8788, 46, '', '/companies/46/lucha7.jpg', 1, ''),
(8778, 50, '', '/companies/50/cars1.jpg', 1, ''),
(8777, 50, '', '/companies/50/cars5.jpg', 1, ''),
(8776, 50, '', '/companies/50/cars6.jpg', 1, ''),
(8799, 33, '', '/companies/33/amusement5.jpg', 1, ''),
(8800, 33, '', '/companies/33/amusement2.jpg', 1, ''),
(8801, 33, '', '/companies/33/amusement3.jpg', 1, ''),
(8798, 33, '', '/companies/33/amusement1.jpg', 1, ''),
(8811, 30, '', '/companies/30/real4.jpg', 1, ''),
(8766, 54, '', '/companies/54/harbour6.jpg', 1, ''),
(8767, 54, '', '/companies/54/harbour3.jpg', 1, ''),
(8768, 54, '', '/companies/54/harbour7.jpg', 1, ''),
(8755, 56, '', '/companies/56/therapy2.jpg', 1, ''),
(8762, 55, '', '/companies/55/seaside4.jpg', 1, ''),
(8787, 46, '', '/companies/46/lucha6.jpg', 1, ''),
(8783, 48, '', '/companies/48/fashion4.jpg', 1, ''),
(8786, 46, '', '/companies/46/lucha5.jpg', 1, ''),
(8785, 46, '', '/companies/46/lucha1.jpg', 1, ''),
(8784, 46, '', '/companies/46/lucha2.jpg', 1, ''),
(8789, 43, '', '/companies/43/fitness3.jpg', 1, ''),
(8790, 43, '', '/companies/43/fitness2.jpg', 1, ''),
(8791, 43, '', '/companies/43/fitness1.jpg', 1, ''),
(8761, 55, '', '/companies/55/seaside3.jpg', 1, ''),
(8760, 55, '', '/companies/55/seaside2.jpg', 1, ''),
(8759, 55, '', '/companies/55/seaside1.jpg', 1, ''),
(8812, 30, '', '/companies/30/real5.jpg', 1, ''),
(8813, 9, '', '/companies/9/flower1.jpg', 1, ''),
(8814, 9, '', '/companies/9/flower3.jpg', 1, ''),
(8815, 9, '', '/companies/9/flower4.jpg', 1, ''),
(8810, 30, '', '/companies/30/real3.jpg', 1, ''),
(8839, 1, '', '/companies/1/wedding5.jpg', 1, ''),
(8840, 1, '', '/companies/1/wedding1.jpg', 1, ''),
(8820, 8, '', '/companies/8/profesional3.jpg', 1, ''),
(8819, 8, '', '/companies/8/profesional2.jpg', 1, ''),
(8838, 1, '', '/companies/1/wedding3.jpg', 1, ''),
(8771, 51, '', '/companies/51/travel3.jpg', 1, ''),
(8772, 51, '', '/companies/51/travel4.jpg', 1, ''),
(8773, 51, '', '/companies/51/travel5.jpg', 1, ''),
(8808, 30, '', '/companies/30/real1.jpg', 1, ''),
(8809, 30, '', '/companies/30/real2.jpg', 1, ''),
(8818, 8, '', '/companies/8/profesional1.jpg', 1, ''),
(8781, 48, '', '/companies/48/fashion2.jpg', 1, ''),
(8782, 48, '', '/companies/48/fashion3.jpg', 1, ''),
(8792, 43, '', '/companies/43/fitness4.jpg', 1, ''),
(8888, 4, '', '/companies/4/water1.jpg', 1, ''),
(8887, 4, '', '/companies/4/water2.jpg', 1, ''),
(8886, 4, '', '/companies/4/water3.jpg', 1, ''),
(8885, 4, '', '/companies/4/water4.jpg', 1, ''),
(8889, 4, '', '/companies/4/water5.jpg', 1, ''),
(8780, 48, '', '/companies/48/fashion6.jpg', 1, ''),
(8757, 56, '', '/companies/56/therapy4.jpg', 1, ''),
(8758, 56, '', '/companies/56/therapy5.jpg', 1, ''),
(8756, 56, '', '/companies/56/therapy1.jpg', 1, ''),
(8754, 56, '', '/companies/56/therapy3.jpg', 1, ''),
(8779, 48, '', '/companies/48/fashion5.jpg', 1, ''),
(8775, 50, '', '/companies/50/cars3.jpg', 1, ''),
(8774, 50, '', '/companies/50/cars4.jpg', 1, ''),
(8823, 5, '', '/companies/5/yoga2.jpg', 1, ''),
(8824, 5, '', '/companies/5/yoga3.jpg', 1, ''),
(8769, 51, '', '/companies/51/travel1.jpg', 1, ''),
(8770, 51, '', '/companies/51/travel2.jpg', 1, '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_projects` (`id`, `name`, `company_id`, `user_id`, `description`, `status`) VALUES
(1, 'Landscapes', 8, NULL, 'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim tristique sapien ut pretium.', 1),
(2, 'Birds', 8, NULL, 'Donec massa augue, lobortis eu cursus in, tincidunt ut nunc. Proin pellentesque, lorem porttitor commodo hendrerit, enim leo mattis risus, ac viverra ante tellus quis velit. ', 1),
(3, 'Portrait', 8, NULL, 'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim tristique sapien ut pretium.', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_projects_pictures` (`id`, `projectId`, `picture_info`, `picture_path`, `picture_enable`) VALUES
(68, 2, '', '/project/2/birds_3.jpg', 1),
(67, 2, '', '/project/2/birds_2.jpg', 1),
(66, 2, '', '/project/2/birds_1.jpg', 1),
(70, 1, '', '/project/1/landscape_2.jpg', 1),
(69, 1, '', '/project/1/landscape_3.jpg', 1),
(71, 1, '', '/project/1/landscape_1.jpg', 1),
(75, 3, '', '/project/3/portrait_1.jpg', 1),
(76, 3, '', '/project/3/portrait_3.jpg', 1),
(77, 3, '', '/project/3/portrait_2.jpg', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_providers` (`id`, `name`, `description`, `email`, `phone`, `max_clients`, `availability`, `published`, `company_id`, `image`, `type`, `istart_date`, `iend_date`) VALUES
(1, 'John Alister', '', 'john@alister.com', '(999)7723-321', 10, ',', 1, 8, '/service_providers/cropped-1620646520.jpeg', 1, '0000-00-00', '0000-00-00'),
(2, 'Anne Smith', '', 'anne@smith.com', '(999)7723-321', 10, ',', 1, 8, '/service_providers/cropped-1620646504.jpeg', 1, '0000-00-00', '0000-00-00'),
(3, 'Max Glonn', '', 'max@glonn.com', '(999)7723-321', 10, ',', 1, 8, '/service_providers/cropped-1620646484.jpeg', 1, '0000-00-00', '0000-00-00');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_provider_hours` (`id`, `weekday`, `start_hour`, `end_hour`, `type`, `status`, `item_type`, `provider_id`, `date`) VALUES
(1, 1, '08:00:00', '20:00:00', 0, 1, 1, 8, NULL),
(2, 2, '08:00:00', '20:00:00', 0, 1, 1, 8, NULL),
(3, 3, '08:00:00', '20:00:00', 0, 1, 1, 8, NULL),
(4, 4, '08:00:00', '20:00:00', 0, 1, 1, 8, NULL),
(5, 5, '08:00:00', '20:00:00', 0, 1, 1, 8, NULL),
(6, 6, '08:00:00', '17:00:00', 0, 1, 1, 8, NULL),
(7, 7, '12:00:00', '12:00:00', 0, 0, 1, 8, NULL),
(8, 1, '09:00:00', '17:00:00', 0, 1, 1, 33, NULL),
(9, 2, '09:00:00', '17:00:00', 0, 1, 1, 33, NULL),
(10, 3, '09:00:00', '17:00:00', 0, 1, 1, 33, NULL),
(11, 4, '09:00:00', '17:00:00', 0, 1, 1, 33, NULL),
(12, 5, '09:00:00', '17:00:00', 0, 1, 1, 33, NULL),
(13, 6, '09:00:00', '14:00:00', 0, 1, 1, 33, NULL),
(14, 7, '24:00:00', '24:00:00', 0, 0, 1, 33, NULL),
(15, 1, '10:00:00', '17:00:00', 0, 1, 1, 32, NULL),
(16, 2, '10:00:00', '17:00:00', 0, 1, 1, 32, NULL),
(17, 3, '10:00:00', '17:00:00', 0, 1, 1, 32, NULL),
(18, 4, '10:00:00', '17:00:00', 0, 1, 1, 32, NULL),
(19, 5, '10:00:00', '17:00:00', 0, 1, 1, 32, NULL),
(20, 6, '10:00:00', '13:00:00', 0, 1, 1, 32, NULL),
(21, 7, '00:00:00', '00:00:00', 0, 0, 1, 32, NULL),
(22, 1, '06:00:00', '08:30:00', 0, 1, 1, 7, NULL),
(23, 2, '24:00:00', '24:00:00', 0, 0, 1, 7, NULL),
(24, 3, '24:00:00', '24:00:00', 0, 0, 1, 7, NULL),
(25, 4, '07:00:00', '21:30:00', 0, 1, 1, 7, NULL),
(26, 5, '06:00:00', '22:00:00', 0, 1, 1, 7, NULL),
(27, 6, '24:00:00', '24:00:00', 0, 0, 1, 7, NULL),
(28, 7, '24:00:00', '24:00:00', 0, 0, 1, 7, NULL),
(29, 1, '09:00:00', '21:00:00', 0, 1, 1, 42, NULL),
(30, 2, '09:00:00', '21:00:00', 0, 1, 1, 42, NULL),
(31, 3, '09:00:00', '21:00:00', 0, 1, 1, 42, NULL),
(32, 4, '09:00:00', '21:00:00', 0, 1, 1, 42, NULL),
(33, 5, '09:00:00', '21:00:00', 0, 1, 1, 42, NULL),
(34, 6, '10:00:00', '18:00:00', 0, 1, 1, 42, NULL),
(35, 7, '12:00:00', '12:00:00', 0, 0, 1, 42, NULL),
(49, 7, '10:00:00', '18:00:00', 0, 1, 1, 41, NULL),
(48, 6, '10:00:00', '18:00:00', 0, 1, 1, 41, NULL),
(47, 5, '09:00:00', '21:00:00', 0, 1, 1, 41, NULL),
(46, 4, '09:00:00', '21:00:00', 0, 1, 1, 41, NULL),
(45, 3, '09:00:00', '21:00:00', 0, 1, 1, 41, NULL),
(44, 2, '09:00:00', '21:00:00', 0, 1, 1, 41, NULL),
(43, 1, '09:00:00', '21:00:00', 0, 1, 1, 41, NULL),
(50, 1, '09:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(51, 2, '09:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(52, 3, '09:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(53, 4, '12:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(54, 5, '09:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(55, 6, '09:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(56, 7, '09:00:00', '21:00:00', 0, 1, 1, 43, NULL),
(57, 1, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(58, 2, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(59, 3, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(60, 4, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(61, 5, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(62, 6, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(63, 7, '13:00:00', '14:00:00', 1, 1, 1, 43, NULL),
(64, 1, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(65, 2, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(66, 3, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(67, 4, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(68, 5, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(69, 6, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(70, 7, '09:00:00', '23:30:00', 0, 1, 1, 46, NULL),
(91, 7, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(90, 6, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(89, 5, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(88, 4, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(87, 3, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(86, 2, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(85, 1, '09:00:00', '22:00:00', 0, 1, 1, 47, NULL),
(78, 1, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(79, 2, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(80, 3, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(81, 4, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(82, 5, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(83, 6, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(84, 7, '09:00:00', '21:00:00', 0, 1, 1, 44, NULL),
(92, 1, '10:00:00', '17:00:00', 0, 1, 1, 48, NULL),
(93, 2, '10:00:00', '17:00:00', 0, 1, 1, 48, NULL),
(94, 3, '10:00:00', '17:00:00', 0, 1, 1, 48, NULL),
(95, 4, '10:00:00', '17:00:00', 0, 1, 1, 48, NULL),
(96, 5, '10:00:00', '17:00:00', 0, 1, 1, 48, NULL),
(97, 6, '10:00:00', '13:30:00', 0, 1, 1, 48, NULL),
(98, 7, '10:00:00', '12:00:00', 0, 0, 1, 48, NULL),
(99, 1, '10:00:00', '17:00:00', 0, 1, 1, 49, NULL),
(100, 2, '10:00:00', '17:00:00', 0, 1, 1, 49, NULL),
(101, 3, '10:00:00', '17:00:00', 0, 1, 1, 49, NULL),
(102, 4, '10:00:00', '17:00:00', 0, 1, 1, 49, NULL),
(103, 5, '10:00:00', '17:00:00', 0, 1, 1, 49, NULL),
(104, 6, '10:00:00', '13:00:00', 0, 1, 1, 49, NULL),
(105, 7, '12:00:00', '12:00:00', 0, 0, 1, 49, NULL),
(106, 1, '08:00:00', '19:00:00', 0, 1, 1, 50, NULL),
(107, 2, '08:00:00', '19:00:00', 0, 1, 1, 50, NULL),
(108, 3, '08:00:00', '19:00:00', 0, 1, 1, 50, NULL),
(109, 4, '08:00:00', '19:00:00', 0, 1, 1, 50, NULL),
(110, 5, '08:00:00', '19:00:00', 0, 1, 1, 50, NULL),
(111, 6, '08:00:00', '19:00:00', 0, 1, 1, 50, NULL),
(112, 7, '12:00:00', '12:00:00', 0, 0, 1, 50, NULL),
(113, 1, '08:00:00', '16:00:00', 0, 1, 1, 51, NULL),
(114, 2, '08:00:00', '16:00:00', 0, 1, 1, 51, NULL),
(115, 3, '08:00:00', '16:00:00', 0, 1, 1, 51, NULL),
(116, 4, '08:00:00', '16:00:00', 0, 1, 1, 51, NULL),
(117, 5, '08:00:00', '16:00:00', 0, 1, 1, 51, NULL),
(118, 6, '08:00:00', '16:00:00', 0, 1, 1, 51, NULL),
(119, 7, '12:00:00', '12:00:00', 0, 0, 1, 51, NULL),
(120, 1, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(121, 2, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(122, 3, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(123, 4, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(124, 5, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(125, 6, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(126, 7, '08:00:00', '20:00:00', 0, 1, 1, 52, NULL),
(127, 1, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(128, 2, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(129, 3, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(130, 4, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(131, 5, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(132, 6, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(133, 7, '08:00:00', '19:30:00', 0, 1, 1, 53, NULL),
(134, 1, '07:30:00', '23:00:00', 0, 1, 1, 54, NULL),
(135, 2, '07:30:00', '23:00:00', 0, 1, 1, 54, NULL),
(136, 3, '07:30:00', '23:00:00', 0, 1, 1, 54, NULL),
(137, 4, '07:30:00', '23:00:00', 0, 1, 1, 54, NULL),
(138, 5, '07:30:00', '23:00:00', 0, 1, 1, 54, NULL),
(139, 6, '07:30:00', '23:00:00', 0, 1, 1, 54, NULL),
(140, 7, '12:00:00', '12:00:00', 0, 0, 1, 54, NULL),
(141, 1, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(142, 2, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(143, 3, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(144, 4, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(145, 5, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(146, 6, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(147, 7, '08:00:00', '16:00:00', 0, 1, 1, 55, NULL),
(350, 7, '12:00:00', '12:00:00', 0, 1, 1, 12, NULL),
(349, 6, '10:00:00', '14:00:00', 0, 1, 1, 12, NULL),
(348, 5, '09:30:00', '18:00:00', 0, 1, 1, 12, NULL),
(347, 4, '09:30:00', '18:00:00', 0, 1, 1, 12, NULL),
(346, 3, '09:30:00', '18:00:00', 0, 1, 1, 12, NULL),
(345, 2, '09:30:00', '18:00:00', 0, 1, 1, 12, NULL),
(344, 1, '09:30:00', '18:00:00', 0, 1, 1, 12, NULL),
(364, 7, '12:00:00', '12:00:00', 0, 1, 1, 5, NULL),
(363, 6, '10:00:00', '17:00:00', 0, 1, 1, 5, NULL),
(362, 5, '10:00:00', '17:00:00', 0, 1, 1, 5, NULL),
(361, 4, '10:00:00', '17:00:00', 0, 1, 1, 5, NULL),
(360, 3, '10:00:00', '17:00:00', 0, 1, 1, 5, NULL),
(359, 2, '10:00:00', '17:00:00', 0, 1, 1, 5, NULL),
(358, 1, '10:00:00', '17:00:00', 0, 1, 1, 5, NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_provider_services` (`provider_id`, `service_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_ratings` (`id`, `companyId`, `rating`, `ipAddress`) VALUES
(1, 8, '4.0', '5.15.238.52'),
(2, 12, '4.0', '5.15.238.52'),
(3, 5, '3.0', '5.15.238.52'),
(4, 4, '5.0', '5.15.238.52'),
(5, 1, '3.0', '5.15.238.52'),
(6, 7, '3.5', '5.15.238.52'),
(7, 9, '1.5', '5.15.238.52'),
(8, 8, '5.0', '127.0.0.1'),
(9, 1, '5.0', '127.0.0.1'),
(10, 7, '5.0', '127.0.0.1');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_related` (`company_id`, `related_company_id`) VALUES
(8, 1),
(8, 7),
(8, 32);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_reviews` (`id`, `name`, `subject`, `description`, `userId`, `email`, `likeCount`, `dislikeCount`, `loveCount`, `state`, `itemId`, `creationDate`, `aproved`, `ipAddress`, `abuseReported`, `rating`, `approved`, `review_type`) VALUES
(8, 'Kelly', 'The best experience ever', 'Ut scelerisque eget mi eget porttitor. Nunc risus enim, volutpat et tempor eu, pretium et est. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam turpis nisl, laoreet varius mauris ac, porta pulvinar felis. Vestibulum placerat, velit eleifend facilisis cursus, turpis nisl ullamcorper eros, eu tristique est nisl a dui. Vestibulum elementum diam sed iaculis porttitor.', 439, NULL, 0, 0, 0, 1, 12, '2015-03-24 05:03:37', 0, '127.0.0.1', 0, '4.2', 2, 1),
(9, 'Sam', 'A happy customer', 'Sed non risus erat. Cras ac dapibus augue. Pellentesque non purus at massa viverra tempus vel vestibulum elit. Quisque in libero in diam consectetur convallis at sed dolor. Nunc finibus arcu sed maximus lacinia. Vestibulum et eleifend lectus, ut laoreet elit. ', 439, NULL, 0, 0, 0, 1, 12, '2015-03-24 05:09:42', 0, '127.0.0.1', 0, '4.7', 2, 1),
(4, 'Loren Jonson', 'Love the products', 'I had such a good experience on this store.', 0, NULL, 0, 0, 0, 1, 9, '2015-03-20 10:09:48', 0, '127.0.0.1', 0, '5.0', 2, 1),
(5, 'John', 'This is what I was looking for', 'Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Cras interdum ut ante non porta. Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien.', 439, NULL, 0, 0, 0, 1, 8, '2015-03-24 03:59:05', 0, '127.0.0.1', 0, '5.0', 2, 1),
(6, 'Michael', 'Greate store', 'Praesent id leo ex. Donec condimentum tincidunt metus at auctor. Nulla facilisis orci vitae ipsum volutpat pharetra. Proin eleifend lobortis nunc, in fringilla justo. Ut sollicitudin lacinia ex eget dapibus. Cras pharetra diam eu malesuada sagittis. Mauris eget ligula gravida, imperdiet ligula a, dictum ex. ', 439, NULL, 0, 0, 0, 1, 29, '2015-03-24 04:49:41', 0, '127.0.0.1', 0, '4.3', 2, 1),
(7, 'Kevin', 'The best experience ever', 'Pellentesque convallis est vel velit luctus, in consequat tortor rutrum. In lectus quam, tempor eu diam efficitur, fringilla aliquet sapien. Praesent quis tellus id enim imperdiet tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Cras interdum ut ante non porta. ', 439, NULL, 0, 0, 0, 1, 8, '2015-03-24 04:58:42', 0, '127.0.0.1', 0, '4.8', 2, 1),
(10, 'John', 'Great food, great service', 'Maecenas convallis malesuada iaculis. Nullam non maximus velit, id molestie est. Pellentesque lobortis sodales tortor. Proin non sollicitudin felis, aliquam ornare massa. Fusce vel turpis est. Nam tempus turpis at orci rutrum tincidunt quis ac odio. Maecenas nec molestie arcu. Suspendisse potenti. Donec gravida diam urna, rutrum malesuada nisi tempor in. Vivamus vel ligula sed ante mattis venenatis. Cras ultricies ornare elit nec blandit. Morbi convallis tellus laoreet, egestas sapien non, condimentum ante. Donec egestas scelerisque est ut aliquam. Nam vulputate felis eu massa imperdiet facilisis. Interdum et malesuada fames ac ante ipsum primis in faucibus. ', 439, NULL, 0, 0, 0, 1, 31, '2015-03-24 08:16:10', 0, '127.0.0.1', 0, '3.8', 2, 1),
(11, 'John McColin', 'Great experience', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Eu tincidunt tortor aliquam nulla facilisi. Urna nunc id cursus metus. Integer eget aliquet nibh praesent tristique magna sit. Sed euismod nisi porta lorem. ', 0, 'john@gmail.com', 0, 0, 0, 1, 8, '2018-02-26 04:14:32', 0, '::1', 0, '5.0', 2, 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_reviews_criteria` (`id`, `name`, `ordering`, `published`) VALUES
(1, 'Service', 1, NULL),
(2, 'Quality', 2, NULL),
(3, 'Staff', 3, NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_reviews_criteria_category` (`criteriaId`, `categoryId`) VALUES
(1, -1),
(2, -1),
(3, -1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_reviews_user_criteria` (`id`, `review_id`, `criteria_id`, `score`) VALUES
(28, 3, 1, '5.0'),
(29, 3, 2, '4.0'),
(30, 3, 3, '5.0'),
(31, 4, 1, '5.0'),
(32, 4, 2, '5.0'),
(33, 4, 3, '5.0'),
(34, 5, 1, '5.0'),
(35, 5, 2, '5.0'),
(36, 5, 3, '5.0'),
(37, 6, 1, '4.0'),
(38, 6, 2, '4.0'),
(39, 6, 3, '5.0'),
(40, 7, 1, '5.0'),
(41, 7, 2, '5.0'),
(42, 7, 3, '5.0'),
(43, 8, 1, '4.0'),
(44, 8, 2, '5.0'),
(45, 8, 3, '4.0'),
(46, 9, 1, '5.0'),
(47, 9, 2, '5.0'),
(48, 9, 3, '5.0'),
(49, 10, 1, '4.0'),
(50, 10, 2, '4.0'),
(51, 10, 3, '4.0');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_services` (`id`, `name`, `description`, `duration`, `show_duration`, `price`, `currency_id`, `max_booking`, `published`, `company_id`, `attendance_mode`, `type`, `istart_date`, `iend_date`) VALUES
(1, 'Photography course', '', 60, 1, '20.00', 143, 10, 1, 8, NULL, 1, NULL, NULL),
(2, 'Photo Session', '', 120, 1, '70.00', 143, 2, 1, 8, NULL, 1, NULL, NULL),
(3, 'Nature photography', '', 180, 1, '120.00', 143, 15, 1, 8, NULL, 1, NULL, NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_services_list` (`id`, `companyId`, `service_section`, `service_name`, `service_description`, `service_price`, `service_image`) VALUES
(1, 31, '', 'Chinese Noodels', 'Vivamus condimentum diam at condimentum scelerisque.', '10.00', '/pricelist/31/cropped-1620634013.jpeg'),
(2, 31, '', 'Steak', 'Quisque pellentesque libero eget dui elementum scelerisque. ', '30.00', '/pricelist/31/cropped-1620634032.jpeg'),
(3, 31, '', 'Soup', 'Praesent sapien neque, fermentum a laoreet eget, tempus ultricies nulla. ', '10.00', '/pricelist/31/cropped-1620634040.jpeg'),
(4, 31, '', 'Desert', 'Fraesent sapien neque, a laoreet eget, tempus ultricies nulla. ', '12.00', '/pricelist/31/cropped-1620634053.jpeg');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_service_bookings` (`id`, `service_id`, `provider_id`, `date`, `time`, `first_name`, `last_name`, `address`, `email`, `phone`, `postal_code`, `city`, `region`, `amount`, `created`, `paid_at`, `status`, `user_id`, `url`, `country_id`, `currency_id`, `initial_amount`, `vat_amount`, `vat`) VALUES
(28, 3, 1, '2019-12-30', '08:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-03 10:09:31', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_testimonials` (`id`, `companyId`, `testimonial_title`, `testimonial_name`, `testimonial_description`) VALUES
(6, 8, 'Great experience', 'John Doe', 'Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. Donec massa augue, lobortis eu cursus in, tincidunt ut nunc.'),
(7, 33, '', '', ''),
(8, 32, '', '', ''),
(9, 8, 'Wonderfull services', 'Clara ', 'Donec massa augue, lobortis eu cursus in, tincidunt ut nunc. Quisque cursus nunc ut diam pulvinar luctus. Nulla facilisi. Donec porta lorem id diam malesuada nec pretium enim euismod. '),
(10, 7, '', '', ''),
(11, 30, '', '', ''),
(12, 29, '', '', ''),
(13, 12, '', '', ''),
(22, 9, '', '', ''),
(25, 5, '', '', ''),
(26, 4, '', '', ''),
(32, 1, '', '', ''),
(34, 31, '', '', '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_types` (`id`, `name`, `ordering`, `only_for_admin`, `company_view`) VALUES
(1, 'Manufacturer/producer', 1, 0, 0),
(2, 'Distributor ', 2, 0, 0),
(4, 'Wholesaler ', 3, 0, 0),
(5, 'Retailer', 4, 0, 0),
(6, 'Service Provider', 5, 0, 0),
(7, 'Subcontractor', 6, 0, 0),
(8, 'Agent/Representative', 7, 0, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_company_videos` (`id`, `companyId`, `url`, `title`) VALUES
(65, 8, 'https://youtu.be/oO7Bh3q-Xm4', NULL),
(64, 8, 'https://youtu.be/jAk_xhs0Rcw', NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conferences` (`id`, `name`, `alias`, `short_description`, `description`, `place`, `start_date`, `end_date`, `logo`, `registration_link`, `viewCount`, `featured`, `published`, `created`) VALUES
(2, 'Music Conference', 'music-conference', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium ', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.</p>', 'London', '2019-12-05', '2022-12-09', '/conferences/cropped-1620648515.jpeg', 'http://www.cmsjunkie.com', 14, NULL, 1, '2023-04-10 08:30:36'),
(3, 'Technology Conference', 'technology-conference', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Perturbationes autem nulla naturae vi commoventur, omniaque ea sunt opiniones ac iudicia levitatis. Utinam quidem dicerent alium alio beatiorem! Iam ruinas videres.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Perturbationes autem nulla naturae vi commoventur, omniaque ea sunt opiniones ac iudicia levitatis. Utinam quidem dicerent alium alio beatiorem! Iam ruinas videres. Quodsi ipsam honestatem undique pertectam atque absolutam. Quid, de quo nulla dissensio est? Tum Quintus: Est plane, Piso, ut dicis, inquit.</p>\r\n<p>Nobis Heracleotes ille Dionysius flagitiose descivisse videtur a Stoicis propter oculorum dolorem. Maximas vero virtutes iacere omnis necesse est voluptate dominante. <i>Inde sermone vario sex illa a Dipylo stadia confecimus.</i> Bonum incolumis acies: misera caecitas. Qui non moveatur et offensione turpitudinis et comprobatione honestatis? Tubulo putas dicere? <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Huius ego nunc auctoritatem sequens idem faciam.</a> Quis hoc dicit? Sint ista Graecorum; <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Praeclare hoc quidem.</a> An nisi populari fama? Hoc est non modo cor non habere, sed ne palatum quidem.</p>\r\n<p>Duo Reges: constructio interrete. <b>Urgent tamen et nihil remittunt.</b> Si enim ita est, vide ne facinus facias, cum mori suadeas. <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Addidisti ad extremum etiam indoctum fuisse.</a> Quis non odit sordidos, vanos, leves, futtiles? De vacuitate doloris eadem sententia erit. Quia dolori non voluptas contraria est, sed doloris privatio. <mark>Si quidem, inquit, tollerem, sed relinquo.</mark> Iam id ipsum absurdum, maximum malum neglegi.</p>', 'Rome', '2019-11-05', '2022-11-09', '/conferences/cropped-1620648159.jpeg', 'http://www.cmsjunkie.com', 4, NULL, 1, '2023-04-10 08:30:36'),
(4, 'Invest in you!', 'human-rights-conferenc', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc haec primum fortasse audientis servire debemus.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Quid enim est a Chrysippo praetermissum in Stoicis?</a> Et nunc quidem quod eam tuetur, ut de vite potissimum loquar, est id extrinsecus; <mark>Bonum patria: miserum exilium.</mark> <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Duo Reges: constructio interrete.</a> <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Velut ego nunc moveor.</a> Si sapiens, ne tum quidem miser, cum ab Oroete, praetore Darei, in crucem actus est. Praeclare hoc quidem. Si longus, levis. Et quidem, inquit, vehementer errat;</p>\r\n<p>Duarum enim vitarum nobis erunt instituta capienda. <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Vide, quaeso, rectumne sit.</a> <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">An haec ab eo non dicuntur?</a> Nec vero alia sunt quaerenda contra Carneadeam illam sententiam. Idem etiam dolorem saepe perpetiuntur, ne, si id non faciant, incidant in maiorem. <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Videamus animi partes, quarum est conspectus illustrior;</a> Qualem igitur hominem natura inchoavit? Si stante, hoc natura videlicet vult, salvam esse se, quod concedimus;</p>\r\n<p>Non quaeritur autem quid naturae tuae consentaneum sit, sed quid disciplinae. Non dolere, inquam, istud quam vim habeat postea videro; Paria sunt igitur. <b>Nullus est igitur cuiusquam dies natalis.</b> Traditur, inquit, ab Epicuro ratio neglegendi doloris. Conferam tecum, quam cuique verso rem subicias; Ergo instituto veterum, quo etiam Stoici utuntur, hinc capiamus exordium. De vacuitate doloris eadem sententia erit. <i>Sed haec quidem liberius ab eo dicuntur et saepius.</i> Quamquam te quidem video minime esse deterritum.</p>', 'New York', '2019-10-13', '2022-10-15', '/conferences/cropped-1620648432.jpeg', 'http://www.cmsjunkie.com', 7, NULL, 1, '2023-04-10 08:30:36');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_sessions` (`id`, `name`, `alias`, `date`, `start_time`, `end_time`, `locationId`, `sessiontypeId`, `sessionlevelId`, `short_description`, `description`, `viewCount`, `published`, `conferenceId`, `video`, `color`, `register_url`, `time_zone`, `session_url`, `created`) VALUES
(7, 'Electric City Music Conference', 'electric-city-music-conference', '2024-02-07', '11:00:00', '12:30:00', 2, 4, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duae sunt enim res quoque, ne tu verba solum putes.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duae sunt enim res quoque, ne tu verba solum putes. Videamus animi partes, quarum est conspectus illustrior; Sed vobis voluptatum perceptarum recordatio vitam beatam facit, et quidem corpore perceptarum. Tum Piso: Quoniam igitur aliquid omnes, quid Lucius noster? Quod quidem nobis non saepe contingit. Cave putes quicquam esse verius.</p>\r\n<p>Sed ad haec, nisi molestum est, habeo quae velim. Duo Reges: constructio interrete. Quam nemo umquam voluptatem appellavit, appellat; Qui autem de summo bono dissentit de tota philosophiae ratione dissentit.</p>\r\n<p>Tum, Quintus et Pomponius cum idem se velle dixissent, Piso exorsus est. Recte, inquit, intellegis. Quae sunt igitur communia vobis cum antiquis, iis sic utamur quasi concessis; Quamquam te quidem video minime esse deterritum. Unum nescio, quo modo possit, si luxuriosus sit, finitas cupiditates habere. Hosne igitur laudas et hanc eorum, inquam, sententiam sequi nos censes oportere?</p>', 14, 1, 2, '<iframe width=854 height=480 src=https://www.youtube.com/embed/Z6-DFMc10J8 frameborder=0 allow=accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture allowfullscreen></iframe>', '', NULL, NULL, NULL, '2023-04-10 08:30:36'),
(8, 'IoT Tech Expo Global', 'iot-tech-expo-global', '2024-03-07', '15:30:00', '18:30:00', 2, 5, 3, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duae sunt enim res quoque, ne tu verba solum putes.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duae sunt enim res quoque, ne tu verba solum putes. Videamus animi partes, quarum est conspectus illustrior; Sed vobis voluptatum perceptarum recordatio vitam beatam facit, et quidem corpore perceptarum. Tum Piso: Quoniam igitur aliquid omnes, quid Lucius noster? Quod quidem nobis non saepe contingit. Cave putes quicquam esse verius.</p>\r\n<p>Sed ad haec, nisi molestum est, habeo quae velim. Duo Reges: constructio interrete. Quam nemo umquam voluptatem appellavit, appellat; Qui autem de summo bono dissentit de tota philosophiae ratione dissentit.</p>\r\n<p>Tum, Quintus et Pomponius cum idem se velle dixissent, Piso exorsus est. Recte, inquit, intellegis. Quae sunt igitur communia vobis cum antiquis, iis sic utamur quasi concessis; Quamquam te quidem video minime esse deterritum. Unum nescio, quo modo possit, si luxuriosus sit, finitas cupiditates habere. Hosne igitur laudas et hanc eorum, inquam, sententiam sequi nos censes oportere?</p>', 1, 1, 3, '<iframe width=854 height=480 src=https://www.youtube.com/embed/3mk64j0_9cI frameborder=0 allow=accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture allowfullscreen></iframe>', '', NULL, NULL, NULL, '2023-04-10 08:30:36'),
(9, 'Annual Winter Music Conference', 'annual-winter-music-conference', '2024-12-12', '16:30:00', '19:00:00', 4, 4, 4, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quid ad utilitatem tantae pecuniae.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quid ad utilitatem tantae pecuniae? Utrum igitur tibi litteram videor an totas paginas commovere? Qui enim existimabit posse se miserum esse beatus non erit. Hoc non est positum in nostra actione. Tanta vis admonitionis inest in locis; Hoc non est positum in nostra actione. Duo Reges: constructio interrete. Nec tamen ille erat sapiens quis enim hoc aut quando aut ubi aut unde? Quae tamen a te agetur non melior, quam illae sunt, quas interdum optines.</p>\r\n<p>Tertium autem omnibus aut maximis rebus iis, quae secundum naturam sint, fruentem vivere. Est tamen ea secundum naturam multoque nos ad se expetendam magis hortatur quam superiora omnia. Quaero igitur, quo modo hae tantae commendationes a natura profectae subito a sapientia relictae sint. <b>Tum ille: Ain tandem?</b> Id Sextilius factum negabat. Paulum, cum regem Persem captum adduceret, eodem flumine invectio? <i>Negare non possum.</i> An me, inquam, nisi te audire vellem, censes haec dicturum fuisse?</p>\r\n<p>Cave putes quicquam esse verius. Quodsi ipsam honestatem undique pertectam atque absolutam. Virtutis, magnitudinis animi, patientiae, fortitudinis fomentis dolor mitigari solet. Primum in nostrane potestate est, quid meminerimus? <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Hoc Hieronymus summum bonum esse dixit.</a> <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Paria sunt igitur.</a> <b>Ita fit cum gravior, tum etiam splendidior oratio.</b> Atqui reperies, inquit, in hoc quidem pertinacem; <mark>Ergo illi intellegunt quid Epicurus dicat, ego non intellego?</mark></p>', 1, 1, 2, '<iframe width=901 height=507 src=https://www.youtube.com/embed/hXI14GlaUjA frameborder=0 allow=accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture allowfullscreen></iframe>', '', NULL, NULL, NULL, '2023-04-10 08:30:36'),
(10, 'Millennium Music Conference', 'millennium-music-conference', '2024-04-12', '11:00:00', '23:00:00', 4, 5, 4, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quid ad utilitatem tantae pecuniae', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quid ad utilitatem tantae pecuniae? Utrum igitur tibi litteram videor an totas paginas commovere? Qui enim existimabit posse se miserum esse beatus non erit. Hoc non est positum in nostra actione. Tanta vis admonitionis inest in locis; Hoc non est positum in nostra actione. Duo Reges: constructio interrete. Nec tamen ille erat sapiens quis enim hoc aut quando aut ubi aut unde? Quae tamen a te agetur non melior, quam illae sunt, quas interdum optines.</p>\r\n<p>Tertium autem omnibus aut maximis rebus iis, quae secundum naturam sint, fruentem vivere. Est tamen ea secundum naturam multoque nos ad se expetendam magis hortatur quam superiora omnia. Quaero igitur, quo modo hae tantae commendationes a natura profectae subito a sapientia relictae sint. <b>Tum ille: Ain tandem?</b> Id Sextilius factum negabat. Paulum, cum regem Persem captum adduceret, eodem flumine invectio? <i>Negare non possum.</i> An me, inquam, nisi te audire vellem, censes haec dicturum fuisse?</p>\r\n<p>Cave putes quicquam esse verius. Quodsi ipsam honestatem undique pertectam atque absolutam. Virtutis, magnitudinis animi, patientiae, fortitudinis fomentis dolor mitigari solet. Primum in nostrane potestate est, quid meminerimus? <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Hoc Hieronymus summum bonum esse dixit.</a> <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Paria sunt igitur.</a> <b>Ita fit cum gravior, tum etiam splendidior oratio.</b> Atqui reperies, inquit, in hoc quidem pertinacem; <mark>Ergo illi intellegunt quid Epicurus dicat, ego non intellego?</mark></p>', 1, 1, 2, '<iframe width=901 height=507 src=https://www.youtube.com/embed/ayoHz82K41Q frameborder=0 allow=accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture allowfullscreen></iframe>', '', '', '-11:00', '', '2023-04-10 08:30:36'),
(11, 'Summit For Human Rights And Democracy', 'summit-for-human-rights-and-democracy', '2024-03-12', '17:00:00', '19:00:00', 3, 5, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quid ad utilitatem tantae pecuniae', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quid ad utilitatem tantae pecuniae? Utrum igitur tibi litteram videor an totas paginas commovere? Qui enim existimabit posse se miserum esse beatus non erit. Hoc non est positum in nostra actione. Tanta vis admonitionis inest in locis; Hoc non est positum in nostra actione. Duo Reges: constructio interrete. Nec tamen ille erat sapiens quis enim hoc aut quando aut ubi aut unde? Quae tamen a te agetur non melior, quam illae sunt, quas interdum optines.</p>\r\n<p>Tertium autem omnibus aut maximis rebus iis, quae secundum naturam sint, fruentem vivere. Est tamen ea secundum naturam multoque nos ad se expetendam magis hortatur quam superiora omnia. Quaero igitur, quo modo hae tantae commendationes a natura profectae subito a sapientia relictae sint. <b>Tum ille: Ain tandem?</b> Id Sextilius factum negabat. Paulum, cum regem Persem captum adduceret, eodem flumine invectio? <i>Negare non possum.</i> An me, inquam, nisi te audire vellem, censes haec dicturum fuisse?</p>\r\n<p>Cave putes quicquam esse verius. Quodsi ipsam honestatem undique pertectam atque absolutam. Virtutis, magnitudinis animi, patientiae, fortitudinis fomentis dolor mitigari solet. Primum in nostrane potestate est, quid meminerimus? <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Hoc Hieronymus summum bonum esse dixit.</a> <a href=\"http://loripsum.net/\" target=\"_blank\" rel=\"noopener noreferrer\">Paria sunt igitur.</a> <b>Ita fit cum gravior, tum etiam splendidior oratio.</b> Atqui reperies, inquit, in hoc quidem pertinacem; <mark>Ergo illi intellegunt quid Epicurus dicat, ego non intellego?</mark></p>', 14, 1, 4, '', '', NULL, NULL, NULL, '2023-04-10 08:30:36');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_attachments` (`id`, `type`, `object_id`, `name`, `path`, `status`, `ordering`) VALUES
(34, 4, 7, 'musicbusiness-1549374419.jpg', '/sessions/0/musicbusiness-1549374419.jpg', 1, 0),
(33, 4, 8, 'ISTQB Agile Tester Extension', '/sessions/0/ISTQBAgileTesterExtension-1549374765.pdf', 1, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_categories` (`sessionId`, `categoryId`) VALUES
(7, 218),
(8, 217),
(9, 218),
(11, 219);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_companies` (`sessionId`, `companyId`) VALUES
(7, 1),
(7, 7),
(7, 31),
(8, 1),
(9, 1),
(9, 7),
(10, 1),
(11, 8);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_levels` (`id`, `name`) VALUES
(2, 'First'),
(3, 'Third'),
(4, 'Advanced');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_locations` (`id`, `name`, `location`, `position`, `capacity`) VALUES
(2, 'Rome', 'Italy', '', 0),
(3, 'London', 'United Kingdom', '', 0),
(4, 'New York', 'United States', '', 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_speakers` (`sessionId`, `speakerId`, `speaker_order`) VALUES
(7, 3, 0),
(7, 6, 0),
(8, 4, 0),
(9, 6, 0),
(10, 3, 0),
(10, 6, 1),
(11, 5, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_session_types` (`id`, `name`, `clickCount`) VALUES
(4, 'Opera Room', 1),
(5, 'University Open Session', 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_speakers` (`id`, `name`, `alias`, `title`, `company_name`, `company_logo`, `countryId`, `speaker_language`, `biography`, `sessionId`, `photo`, `speakertypeId`, `featured`, `email`, `phone`, `facebook`, `twitter`, `googlep`, `linkedin`, `short_biography`, `additional_info_link`, `viewCount`, `published`, `created`) VALUES
(3, 'John Smith', 'john-smith', 'Musician', 'Music Business Workshop', '/speakers/company_1.jpg', 224, NULL, 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.', NULL, '/speakers/cropped-1620648707.jpeg', 2, NULL, 'office@site.com', '675411232', 'http://www.facebook.com/', 'http://www.twitter.com/', '', 'http://www.linkedin.com/', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate ', '', 5, 1, '2023-04-10 08:30:36'),
(4, 'Stacey Harington', 'stacey-harington', 'CEO', 'Intelligent Solutions', '/speakers/company_2.png', 226, NULL, 'Vivamus in mollis augue. Vestibulum ex metus, bibendum porttitor tempus non, feugiat non turpis. Nam arcu augue, sollicitudin id arcu ac, auctor porta ipsum. Donec ut imperdiet leo, et feugiat ante. Quisque tristique vehicula ipsum eget lobortis. Nam scelerisque, lectus sit amet accumsan suscipit, massa urna pellentesque purus, in blandit tellus tortor sit amet ante. Ut ornare est ac nunc semper feugiat. Curabitur cursus tempor lacinia. Pellentesque interdum blandit velit. Mauris ac ornare libero. Duis efficitur pretium tortor, ac blandit purus imperdiet eu. Aliquam sagittis mi hendrerit risus convallis imperdiet. Nunc eu lacus et lacus fringilla commodo ac et lectus.', NULL, '/speakers/cropped-1620648687.jpeg', 3, NULL, 'office@site.com', '675411232', 'http://www.facebook.com', 'http://www.twitter.com/', '', 'http://www.linkedin.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Perturbationes autem nulla naturae vi commoventur, omniaque ea sunt opiniones ac iudicia levitatis. Utinam quidem dicerent alium alio beatiorem! Iam ruinas videres. Quodsi ipsam honestatem undique pertectam atque absolutam. Quid, de quo nulla dissensio est? Tum Quintus: Est plane, Piso, ut', '', 3, 1, '2023-04-10 08:30:36'),
(5, 'Linda Hart', 'linda-hart', 'Project Manager at Human Rights women and child welfare', 'Human Rights Council', '', 226, NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc haec primum fortasse audientis servire debemus. Non minor, inquit, voluptas percipitur ex vilissimis rebus quam ex pretiosissimis. Cum autem in quo sapienter dicimus, id a primo rectissime dicitur. ALIO MODO. Duo Reges: constructio interrete. Nam et a te perfici istam disputationem volo, nec tua mihi oratio longa videri potest. Egone non intellego, quid sit don Graece, Latine voluptas? Omnes enim iucundum motum, quo sensus hilaretur.', NULL, '/speakers/cropped-1620648631.jpeg', 3, NULL, 'office@site.com', '675411232', 'http://www.facebook.com', 'http://www.twitter.com/', '', 'http://www.linkedin.com', '', '', 11, 1, '2023-04-10 08:30:36'),
(6, 'Brian Lindow', 'brian-lindow', 'Piainst', 'Opera Room Theater', '/speakers/company_4.jpg', 226, NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc haec primum fortasse audientis servire debemus. Non minor, inquit, voluptas percipitur ex vilissimis rebus quam ex pretiosissimis. Cum autem in quo sapienter dicimus, id a primo rectissime dicitur. ALIO MODO. Duo Reges: constructio interrete. Nam et a te perfici istam disputationem volo, nec tua mihi oratio longa videri potest. Egone non intellego, quid sit don Graece, Latine voluptas? Omnes enim iucundum motum, quo sensus hilaretur.', NULL, '/speakers/cropped-1620648615.jpeg', 2, NULL, 'office@site.com', '6754112324', 'http://www.facebook.com', 'http://www.facebook.com', '', 'http://www.linkedin.com', '', '', 6, 1, '2023-04-10 08:30:36');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_conference_speaker_types` (`id`, `name`, `color`) VALUES
(2, 'Moderator', ''),
(3, 'Keynote Speaker', '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_countries` (`id`, `country_name`, `country_code`, `country_currency`, `country_currency_short`, `logo`, `description`) VALUES
(1, 'Andorra', 'AD', 'Euro', 'EUR', '/flags/andorra.png', NULL),
(2, 'United Arab Emirates', 'AE', 'UAE Dirham', 'AED', '/flags/united-arab-emirates.png', NULL),
(3, 'Afghanistan', 'AF', 'Afghani', 'AFA', '/flags/afghanistan.png', NULL),
(4, 'Antigua and Barbuda', 'AG', 'East Caribbean Dollar', 'XCD', '/flags/antigua-and-barbuda.png', NULL),
(5, 'Anguilla', 'AI', 'East Caribbean Dollar', 'XCD', '/flags/anguilla.png', NULL),
(6, 'Albania', 'AL', 'Lek', 'ALL', '/flags/albania.png', NULL),
(7, 'Armenia', 'AM', 'Armenian Dram', 'AMD', '/flags/armenia.png', NULL),
(8, 'Netherlands Antilles', 'AN', 'Netherlands Antillean guilder', 'ANG', '/flags/netherlands-antilles.png', NULL),
(9, 'Angola', 'AO', 'Kwanza', 'AOA', '/flags/angola.png', NULL),
(11, 'Argentina', 'AR', 'Argentine Peso', 'ARS', '/flags/argentina.png', NULL),
(12, 'American Samoa', 'AS', 'US Dollar', 'USD', '/flags/american-samoa.png', NULL),
(13, 'Austria', 'AT', 'Euro', 'EUR', '/flags/austria.png', NULL),
(14, 'Australia', 'AU', 'Australian dollar', 'AUD', '/flags/australia.png', NULL),
(15, 'Aruba', 'AW', 'Aruban Guilder', 'AWG', '/flags/aruba.png', NULL),
(16, 'Azerbaijan', 'AZ', 'Azerbaijani Manat', 'AZM', '/flags/azerbaijan.png', NULL),
(17, 'Bosnia and Herzegovina', 'BA', 'Convertible Marka', 'BAM', '/flags/bosnia-and-herzegovina.png', NULL),
(18, 'Barbados', 'BB', 'Barbados Dollar', 'BBD', '/flags/barbados.png', NULL),
(19, 'Bangladesh', 'BD', 'Taka', 'BDT', '/flags/bangladesh.png', NULL),
(20, 'Belgium', 'BE', 'Euro', 'EUR', '/flags/belgium.png', NULL),
(21, 'Burkina Faso', 'BF', 'CFA Franc BCEAO', 'XOF', '/flags/burkina-faso.png', NULL),
(22, 'Bulgaria', 'BG', 'Lev', 'BGL', '/flags/bulgaria.png', NULL),
(23, 'Bahrain', 'BH', 'Bahraini Dinar', 'BHD', '/flags/bahrain.png', NULL),
(24, 'Burundi', 'BI', 'Burundi Franc', 'BIF', '/flags/burundi.png', NULL),
(25, 'Benin', 'BJ', 'CFA Franc BCEAO', 'XOF', '/flags/benin.png', NULL),
(26, 'Bermuda', 'BM', 'Bermudian Dollar', 'BMD', '/flags/bermuda.png', NULL),
(27, 'Brunei Darussalam', 'BN', 'Brunei Dollar', 'BND', '/flags/brunei-darussalam.png', NULL),
(28, 'Bolivia', 'BO', 'Boliviano', 'BOB', '/flags/bolivia.png', NULL),
(29, 'Brazil', 'BR', 'Brazilian Real', 'BRL', '/flags/brazil.png', NULL),
(30, 'The Bahamas', 'BS', 'Bahamian Dollar', 'BSD', '/flags/the-bahamas.png', NULL),
(31, 'Bhutan', 'BT', 'Ngultrum', 'BTN', '/flags/bhutan.png', NULL),
(32, 'Bouvet Island', 'BV', 'Norwegian Krone', 'NOK', '/flags/bouvet-island.png', NULL),
(33, 'Botswana', 'BW', 'Pula', 'BWP', '/flags/botswana.png', NULL),
(34, 'Belarus', 'BY', 'Belarussian Ruble', 'BYN', '/flags/belarus.png', NULL),
(35, 'Belize', 'BZ', 'Belize Dollar', 'BZD', '/flags/belize.png', NULL),
(36, 'Canada', 'CA', 'Canadian Dollar', 'CAD', '/flags/canada.png', NULL),
(37, 'Cocos (Keeling) Islands', 'CC', 'Australian Dollar', 'AUD', '/flags/cocos-(keeling)-islands.png', NULL),
(39, 'Central African Republic', 'CF', 'CFA Franc BEAC', 'XAF', '/flags/central-african-republic.png', NULL),
(41, 'Switzerland', 'CH', 'Swiss Franc', 'CHF', '/flags/switzerland.png', NULL),
(42, 'Cote d\'Ivoire', 'CI', 'CFA Franc BCEAO', 'XOF', '/flags/cote-d\'ivoire.png', NULL),
(43, 'Cook Islands', 'CK', 'New Zealand Dollar', 'NZD', '/flags/cook-islands.png', NULL),
(44, 'Chile', 'CL', 'Chilean Peso', 'CLP', '/flags/chile.png', NULL),
(45, 'Cameroon', 'CM', 'CFA Franc BEAC', 'XAF', '/flags/cameroon.png', NULL),
(46, 'China', 'CN', 'Yuan Renminbi', 'CNY', '/flags/china.png', NULL),
(47, 'Colombia', 'CO', 'Colombian Peso', 'COP', '/flags/colombia.png', NULL),
(48, 'Costa Rica', 'CR', 'Costa Rican Colon', 'CRC', '/flags/costa-rica.png', NULL),
(49, 'Cuba', 'CU', 'Cuban Peso', 'CUP', '/flags/cuba.png', NULL),
(50, 'Cape Verde', 'CV', 'Cape Verdean Escudo', 'CVE', '/flags/cape-verde.png', NULL),
(51, 'Christmas Island', 'CX', 'Australian Dollar', 'AUD', '/flags/christmas-island.png', NULL),
(52, 'Cyprus', 'CY', 'Cyprus Pound', 'CYP', '/flags/cyprus.png', NULL),
(53, 'Czech Republic', 'CZ', 'Czech Koruna', 'CZK', '/flags/czech-republic.png', NULL),
(54, 'Germany', 'DE', 'Euro', 'EUR', '/flags/germany.png', NULL),
(55, 'Djibouti', 'DJ', 'Djibouti Franc', 'DJF', '/flags/djibouti.png', NULL),
(56, 'Denmark', 'DK', 'Danish Krone', 'DKK', '/flags/denmark.png', NULL),
(57, 'Dominica', 'DM', 'East Caribbean Dollar', 'XCD', '/flags/dominica.png', NULL),
(58, 'Dominican Republic', 'DO', 'Dominican Peso', 'DOP', '/flags/dominican-republic.png', NULL),
(59, 'Algeria', 'DZ', 'Algerian Dinar', 'DZD', '/flags/algeria.png', NULL),
(60, 'Ecuador', 'EC', 'US dollar', 'USD', '/flags/ecuador.png', NULL),
(61, 'Estonia', 'EE', 'Kroon', 'EEK', '/flags/estonia.png', NULL),
(62, 'Egypt', 'EG', 'Egyptian Pound', 'EGP', '/flags/egypt.png', NULL),
(63, 'Western Sahara', 'EH', 'Moroccan Dirham', 'MAD', '/flags/western-sahara.png', NULL),
(64, 'Eritrea', 'ER', 'Nakfa', 'ERN', '/flags/eritrea.png', NULL),
(65, 'Spain', 'ES', 'Euro', 'EUR', '/flags/spain.png', NULL),
(66, 'Ethiopia', 'ET', 'Ethiopian Birr', 'ETB', '/flags/ethiopia.png', NULL),
(67, 'Finland', 'FI', 'Euro', 'EUR', '/flags/finland.png', NULL),
(68, 'Fiji', 'FJ', 'Fijian Dollar', 'FJD', '/flags/fiji.png', NULL),
(69, 'Falkland Islands (Islas Malvinas)', 'FK', 'Falkland Islands Pound', 'FKP', '/flags/falkland-islands-(islas-malvinas).png', NULL),
(71, 'Faroe Islands', 'FO', 'Danish Krone', 'DKK', '/flags/faroe-islands.png', NULL),
(72, 'France', 'FR', 'Euro', 'EUR', '/flags/france.png', NULL),
(74, 'Gabon', 'GA', 'CFA Franc BEAC', 'XAF', '/flags/gabon.png', NULL),
(75, 'Grenada', 'GD', 'East Caribbean Dollar', 'XCD', '/flags/grenada.png', NULL),
(76, 'Georgia', 'GE', 'Lari', 'GEL', '/flags/georgia.png', NULL),
(77, 'French Guiana', 'GF', 'Euro', 'EUR', '/flags/french-guiana.png', NULL),
(78, 'Guernsey', 'GG', 'Pound Sterling', 'GBP', '/flags/guernsey.png', NULL),
(79, 'Ghana', 'GH', 'Cedi', 'GHC', '/flags/ghana.png', NULL),
(80, 'Gibraltar', 'GI', 'Gibraltar Pound', 'GIP', '/flags/gibraltar.png', NULL),
(81, 'Greenland', 'GL', 'Danish Krone', 'DKK', '/flags/greenland.png', NULL),
(82, 'The Gambia', 'GM', 'Dalasi', 'GMD', '/flags/the-gambia.png', NULL),
(83, 'Guinea', 'GN', 'Guinean Franc', 'GNF', '/flags/guinea.png', NULL),
(84, 'Guadeloupe', 'GP', 'Euro', 'EUR', '/flags/guadeloupe.png', NULL),
(85, 'Equatorial Guinea', 'GQ', 'CFA Franc BEAC', 'XAF', '/flags/equatorial-guinea.png', NULL),
(86, 'Greece', 'GR', 'Euro', 'EUR', '/flags/greece.png', NULL),
(87, 'South Georgia and the South Sandwich Islands', 'GS', 'Pound Sterling', 'GBP', '/flags/south-georgia-and-the-south-sandwich-islands.png', NULL),
(88, 'Guatemala', 'GT', 'Quetzal', 'GTQ', '/flags/guatemala.png', NULL),
(89, 'Guam', 'GU', 'US Dollar', 'USD', '/flags/guam.png', NULL),
(90, 'Guinea-Bissau', 'GW', 'CFA Franc BCEAO', 'XOF', '/flags/guinea-bissau.png', NULL),
(91, 'Guyana', 'GY', 'Guyana Dollar', 'GYD', '/flags/guyana.png', NULL),
(92, 'Hong Kong (SAR)', 'HK', 'Hong Kong Dollar', 'HKD', '/flags/hong-kong-(sar).png', NULL),
(93, 'Heard Island and McDonald Islands', 'HM', 'Australian Dollar', 'AUD', '/flags/heard-island-and-mcdonald-islands.png', NULL),
(94, 'Honduras', 'HN', 'Lempira', 'HNL', '/flags/honduras.png', NULL),
(95, 'Croatia', 'HR', 'Kuna', 'HRK', '/flags/croatia.png', NULL),
(96, 'Haiti', 'HT', 'Gourde', 'HTG', '/flags/haiti.png', NULL),
(97, 'Hungary', 'HU', 'Forint', 'HUF', '/flags/hungary.png', NULL),
(98, 'Indonesia', 'ID', 'Rupiah', 'IDR', '/flags/indonesia.png', NULL),
(99, 'Ireland', 'IE', 'Euro', 'EUR', '/flags/ireland.png', NULL),
(100, 'Israel', 'IL', 'New Israeli Sheqel', 'ILS', '/flags/israel.png', NULL),
(102, 'India', 'IN', 'Indian Rupee', 'INR', '/flags/india.png', NULL),
(103, 'British Indian Ocean Territory', 'IO', 'US Dollar', 'USD', '/flags/british-indian-ocean-territory.png', NULL),
(104, 'Iraq', 'IQ', 'Iraqi Dinar', 'IQD', '/flags/iraq.png', NULL),
(105, 'Iran', 'IR', 'Iranian Rial', 'IRR', '/flags/iran.png', NULL),
(106, 'Iceland', 'IS', 'Iceland Krona', 'ISK', '/flags/iceland.png', NULL),
(107, 'Italy', 'IT', 'Euro', 'EUR', '/flags/italy.png', NULL),
(108, 'Jersey', 'JE', 'Pound Sterling', 'GBP', '/flags/jersey.png', NULL),
(109, 'Jamaica', 'JM', 'Jamaican dollar', 'JMD', '/flags/jamaica.png', NULL),
(110, 'Jordan', 'JO', 'Jordanian Dinar', 'JOD', '/flags/jordan.png', NULL),
(111, 'Japan', 'JP', 'Yen', 'JPY', '/flags/japan.png', NULL),
(112, 'Kenya', 'KE', 'Kenyan shilling', 'KES', '/flags/kenya.png', NULL),
(113, 'Kyrgyzstan', 'KG', 'Som', 'KGS', '/flags/kyrgyzstan.png', NULL),
(114, 'Cambodia', 'KH', 'Riel', 'KHR', '/flags/cambodia.png', NULL),
(115, 'Kiribati', 'KI', 'Australian dollar', 'AUD', '/flags/kiribati.png', NULL),
(116, 'Comoros', 'KM', 'Comoro Franc', 'KMF', '/flags/comoros.png', NULL),
(117, 'Saint Kitts and Nevis', 'KN', 'East Caribbean Dollar', 'XCD', '/flags/saint-kitts-and-nevis.png', NULL),
(118, 'Korea North', 'KP', 'North Korean Won', 'KPW', '/flags/korea-north.png', NULL),
(119, 'Korea South', 'KR', 'Won', 'KRW', '/flags/korea-south.png', NULL),
(120, 'Kuwait', 'KW', 'Kuwaiti Dinar', 'KWD', '/flags/kuwait.png', NULL),
(121, 'Cayman Islands', 'KY', 'Cayman Islands Dollar', 'KYD', '/flags/cayman-islands.png', NULL),
(122, 'Kazakhstan', 'KZ', 'Tenge', 'KZT', '/flags/kazakhstan.png', NULL),
(123, 'Laos', 'LA', 'Kip', 'LAK', '/flags/laos.png', NULL),
(124, 'Lebanon', 'LB', 'Lebanese Pound', 'LBP', '/flags/lebanon.png', NULL),
(125, 'Saint Lucia', 'LC', 'East Caribbean Dollar', 'XCD', '/flags/saint-lucia.png', NULL),
(126, 'Liechtenstein', 'LI', 'Swiss Franc', 'CHF', '/flags/liechtenstein.png', NULL),
(127, 'Sri Lanka', 'LK', 'Sri Lanka Rupee', 'LKR', '/flags/sri-lanka.png', NULL),
(128, 'Liberia', 'LR', 'Liberian Dollar', 'LRD', '/flags/liberia.png', NULL),
(129, 'Lesotho', 'LS', 'Loti', 'LSL', '/flags/lesotho.png', NULL),
(130, 'Lithuania', 'LT', 'Lithuanian Litas', 'LTL', '/flags/lithuania.png', NULL),
(131, 'Luxembourg', 'LU', 'Euro', 'EUR', '/flags/luxembourg.png', NULL),
(132, 'Latvia', 'LV', 'Latvian Lats', 'LVL', '/flags/latvia.png', NULL),
(133, 'Libya', 'LY', 'Libyan Dinar', 'LYD', '/flags/libya.png', NULL),
(134, 'Morocco', 'MA', 'Moroccan Dirham', 'MAD', '/flags/morocco.png', NULL),
(135, 'Monaco', 'MC', 'Euro', 'EUR', '/flags/monaco.png', NULL),
(136, 'Moldova', 'MD', 'Moldovan Leu', 'MDL', '/flags/moldova.png', NULL),
(137, 'Madagascar', 'MG', 'Malagasy Franc', 'MGF', '/flags/madagascar.png', NULL),
(138, 'Marshall Islands', 'MH', 'US dollar', 'USD', '/flags/marshall-islands.png', NULL),
(140, 'Mali', 'ML', 'CFA Franc BCEAO', 'XOF', '/flags/mali.png', NULL),
(141, 'Burma', 'MM', 'kyat', 'MMK', '/flags/burma.png', NULL),
(142, 'Mongolia', 'MN', 'Tugrik', 'MNT', '/flags/mongolia.png', NULL),
(143, 'Macao', 'MO', 'Pataca', 'MOP', '/flags/macao.png', NULL),
(144, 'Northern Mariana Islands', 'MP', 'US Dollar', 'USD', '/flags/northern-mariana-islands.png', NULL),
(145, 'Martinique', 'MQ', 'Euro', 'EUR', '/flags/martinique.png', NULL),
(146, 'Mauritania', 'MR', 'Ouguiya', 'MRO', '/flags/mauritania.png', NULL),
(147, 'Montserrat', 'MS', 'East Caribbean Dollar', 'XCD', '/flags/montserrat.png', NULL),
(148, 'Malta', 'MT', 'Maltese Lira', 'MTL', '/flags/malta.png', NULL),
(149, 'Mauritius', 'MU', 'Mauritius Rupee', 'MUR', '/flags/mauritius.png', NULL),
(150, 'Maldives', 'MV', 'Rufiyaa', 'MVR', '/flags/maldives.png', NULL),
(151, 'Malawi', 'MW', 'Kwacha', 'MWK', '/flags/malawi.png', NULL),
(152, 'Mexico', 'MX', 'Mexican Peso', 'MXN', '/flags/mexico.png', NULL),
(153, 'Malaysia', 'MY', 'Malaysian Ringgit', 'MYR', '/flags/malaysia.png', NULL),
(154, 'Mozambique', 'MZ', 'Metical', 'MZM', '/flags/mozambique.png', NULL),
(155, 'Namibia', 'NA', 'Namibian Dollar', 'NAD', '/flags/namibia.png', NULL),
(156, 'New Caledonia', 'NC', 'CFP Franc', 'XPF', '/flags/new-caledonia.png', NULL),
(157, 'Niger', 'NE', 'CFA Franc BCEAO', 'XOF', '/flags/niger.png', NULL),
(158, 'Norfolk Island', 'NF', 'Australian Dollar', 'AUD', '/flags/norfolk-island.png', NULL),
(159, 'Nigeria', 'NG', 'Naira', 'NGN', '/flags/nigeria.png', NULL),
(160, 'Nicaragua', 'NI', 'Cordoba Oro', 'NIO', '/flags/nicaragua.png', NULL),
(161, 'Netherlands', 'NL', 'Euro', 'EUR', '/flags/netherlands.png', NULL),
(162, 'Norway', 'NO', 'Norwegian Krone', 'NOK', '/flags/norway.png', NULL),
(163, 'Nepal', 'NP', 'Nepalese Rupee', 'NPR', '/flags/nepal.png', NULL),
(164, 'Nauru', 'NR', 'Australian Dollar', 'AUD', '/flags/nauru.png', NULL),
(165, 'Niue', 'NU', 'New Zealand Dollar', 'NZD', '/flags/niue.png', NULL),
(166, 'New Zealand', 'NZ', 'New Zealand Dollar', 'NZD', '/flags/new-zealand.png', NULL),
(167, 'Oman', 'OM', 'Rial Omani', 'OMR', '/flags/oman.png', NULL),
(168, 'Panama', 'PA', 'balboa', 'PAB', '/flags/panama.png', NULL),
(169, 'Peru', 'PE', 'Nuevo Sol', 'PEN', '/flags/peru.png', NULL),
(170, 'French Polynesia', 'PF', 'CFP Franc', 'XPF', '/flags/french-polynesia.png', NULL),
(171, 'Papua New Guinea', 'PG', 'Kina', 'PGK', '/flags/papua-new-guinea.png', NULL),
(172, 'Philippines', 'PH', 'Philippine Peso', 'PHP', '/flags/philippines.png', NULL),
(173, 'Pakistan', 'PK', 'Pakistan Rupee', 'PKR', '/flags/pakistan.png', NULL),
(174, 'Poland', 'PL', 'Zloty', 'PLN', '/flags/poland.png', NULL),
(175, 'Saint Pierre and Miquelon', 'PM', 'Euro', 'EUR', '/flags/saint-pierre-and-miquelon.png', NULL),
(176, 'Pitcairn Islands', 'PN', 'New Zealand Dollar', 'NZD', '/flags/pitcairn-islands.png', NULL),
(177, 'Puerto Rico', 'PR', 'US dollar', 'USD', '/flags/puerto-rico.png', NULL),
(179, 'Portugal', 'PT', 'Euro', 'EUR', '/flags/portugal.png', NULL),
(180, 'Palau', 'PW', 'US dollar', 'USD', '/flags/palau.png', NULL),
(181, 'Paraguay', 'PY', 'Guarani', 'PYG', '/flags/paraguay.png', NULL),
(182, 'Qatar', 'QA', 'Qatari Rial', 'QAR', '/flags/qatar.png', NULL),
(184, 'Romania', 'RO', 'Leu', 'RON', '/flags/romania.png', NULL),
(185, 'Russia', 'RU', 'Russian Ruble', 'RUB', '/flags/russia.png', NULL),
(186, 'Rwanda', 'RW', 'Rwanda Franc', 'RWF', '/flags/rwanda.png', NULL),
(187, 'Saudi Arabia', 'SA', 'Saudi Riyal', 'SAR', '/flags/saudi-arabia.png', NULL),
(188, 'Solomon Islands', 'SB', 'Solomon Islands Dollar', 'SBD', '/flags/solomon-islands.png', NULL),
(189, 'Seychelles', 'SC', 'Seychelles Rupee', 'SCR', '/flags/seychelles.png', NULL),
(190, 'Sudan', 'SD', 'Sudanese Dinar', 'SDD', '/flags/sudan.png', NULL),
(191, 'Sweden', 'SE', 'Swedish Krona', 'SEK', '/flags/sweden.png', NULL),
(192, 'Singapore', 'SG', 'Singapore Dollar', 'SGD', '/flags/singapore.png', NULL),
(193, 'Saint Helena', 'SH', 'Saint Helenian Pound', 'SHP', '/flags/saint-helena.png', NULL),
(194, 'Slovenia', 'SI', 'Tolar', 'SIT', '/flags/slovenia.png', NULL),
(195, 'Svalbard', 'SJ', 'Norwegian Krone', 'NOK', '/flags/svalbard.png', NULL),
(196, 'Slovakia', 'SK', 'Slovak Koruna', 'SKK', '/flags/slovakia.png', NULL),
(197, 'Sierra Leone', 'SL', 'Leone', 'SLL', '/flags/sierra-leone.png', NULL),
(198, 'San Marino', 'SM', 'Euro', 'EUR', '/flags/san-marino.png', NULL),
(199, 'Senegal', 'SN', 'CFA Franc BCEAO', 'XOF', '/flags/senegal.png', NULL),
(200, 'Somalia', 'SO', 'Somali Shilling', 'SOS', '/flags/somalia.png', NULL),
(201, 'Suriname', 'SR', 'Suriname Guilder', 'SRG', '/flags/suriname.png', NULL),
(203, 'El Salvador', 'SV', 'El Salvador Colon', 'SVC', '/flags/el-salvador.png', NULL),
(204, 'Syria', 'SY', 'Syrian Pound', 'SYP', '/flags/syria.png', NULL),
(205, 'Swaziland', 'SZ', 'Lilangeni', 'SZL', '/flags/swaziland.png', NULL),
(206, 'Turks and Caicos Islands', 'TC', 'US Dollar', 'USD', '/flags/turks-and-caicos-islands.png', NULL),
(207, 'Chad', 'TD', 'CFA Franc BEAC', 'XAF', '/flags/chad.png', NULL),
(208, 'French Southern and Antarctic Lands', 'TF', 'Euro', 'EUR', '/flags/french-southern-and-antarctic-lands.png', NULL),
(209, 'Togo', 'TG', 'CFA Franc BCEAO', 'XOF', '/flags/togo.png', NULL),
(210, 'Thailand', 'TH', 'Baht', 'THB', '/flags/thailand.png', NULL),
(211, 'Tajikistan', 'TJ', 'Somoni', 'TJS', '/flags/tajikistan.png', NULL),
(212, 'Tokelau', 'TK', 'New Zealand Dollar', 'NZD', '/flags/tokelau.png', NULL),
(213, 'Turkmenistan', 'TM', 'Manat', 'TMM', '/flags/turkmenistan.png', NULL),
(214, 'Tunisia', 'TN', 'Tunisian Dinar', 'TND', '/flags/tunisia.png', NULL),
(215, 'Tonga', 'TO', 'Pa\'anga', 'TOP', '/flags/tonga.png', NULL),
(216, 'East Timor', 'TL', 'Timor Escudo', 'TPE', '/flags/east-timor.png', NULL),
(217, 'Turkey', 'TR', 'Turkish Lira', 'TRL', '/flags/turkey.png', NULL),
(218, 'Trinidad and Tobago', 'TT', 'Trinidad and Tobago Dollar', 'TTD', '/flags/trinidad-and-tobago.png', NULL),
(219, 'Tuvalu', 'TV', 'Australian Dollar', 'AUD', '/flags/tuvalu.png', NULL),
(220, 'Taiwan', 'TW', 'New Taiwan Dollar', 'TWD', '/flags/taiwan.png', NULL),
(221, 'Tanzania', 'TZ', 'Tanzanian Shilling', 'TZS', '/flags/tanzania.png', NULL),
(222, 'Ukraine', 'UA', 'Hryvnia', 'UAH', '/flags/ukraine.png', NULL),
(223, 'Uganda', 'UG', 'Uganda Shilling', 'UGX', '/flags/uganda.png', NULL),
(224, 'United Kingdom', 'GB', 'Pound Sterling', 'GBP', '/flags/united-kingdom.png', NULL),
(225, 'United States Minor Outlying Islands', 'UM', 'US Dollar', 'USD', '/flags/united-states-minor-outlying-islands.png', NULL),
(226, 'United States', 'US', 'US Dollar', 'USD', '/flags/united-states.png', NULL),
(227, 'Uruguay', 'UY', 'Peso Uruguayo', 'UYU', '/flags/uruguay.png', NULL),
(228, 'Uzbekistan', 'UZ', 'Uzbekistan Sum', 'UZS', '/flags/uzbekistan.png', NULL),
(229, 'Holy See (Vatican City)', 'VA', 'Euro', 'EUR', '/flags/holy-see-(vatican-city).png', NULL),
(230, 'Saint Vincent and the Grenadines', 'VC', 'East Caribbean Dollar', 'XCD', '/flags/saint-vincent-and-the-grenadines.png', NULL),
(231, 'Venezuela', 'VE', 'Bolivar', 'VEB', '/flags/venezuela.png', NULL),
(232, 'British Virgin Islands', 'VG', 'US dollar', 'USD', '/flags/british-virgin-islands.png', NULL),
(233, 'Virgin Islands', 'VI', 'US Dollar', 'USD', '/flags/virgin-islands.png', NULL),
(234, 'Vietnam', 'VN', 'Dong', 'VND', '/flags/vietnam.png', NULL),
(235, 'Vanuatu', 'VU', 'Vatu', 'VUV', '/flags/vanuatu.png', NULL),
(236, 'Wallis and Futuna', 'WF', 'CFP Franc', 'XPF', '/flags/wallis-and-futuna.png', NULL),
(237, 'Samoa', 'WS', 'Tala', 'WST', '/flags/samoa.png', NULL),
(238, 'Yemen', 'YE', 'Yemeni Rial', 'YER', '/flags/yemen.png', NULL),
(239, 'Mayotte', 'YT', 'Euro', 'EUR', '/flags/mayotte.png', NULL),
(241, 'South Africa', 'ZA', 'Rand', 'ZAR', '/flags/south-africa.png', NULL),
(242, 'Zambia', 'ZM', 'Kwacha', 'ZMK', '/flags/zambia.png', NULL),
(243, 'Zimbabwe', 'ZW', 'Zimbabwe Dollar', 'ZWD', '/flags/zimbabwe.png', NULL),
(244, 'DR Congo', 'CD', 'Congolese Franc', 'CDF', '/flags/congo-(democratic).png', NULL),
(245, 'Republic of the Congo', 'CG', 'Congolese Franc', 'CDF', '/flags/congo-(republic).png', NULL),
(246, 'Curacao', 'CW', 'Netherlands Antillean guilder', 'ANG', '/flags/curacao.png', NULL),
(247, 'Micronesia', 'FM', 'US Dollar', 'USD', '/flags/micronesia-(federated).png', NULL),
(248, 'Montenegro', 'ME', 'Euro', 'EUR', '/flags/montenegro.png', NULL),
(249, 'North Macedonia', 'MK', 'Denar', 'MKD', '/flags/macedonia.png', NULL),
(250, 'Myanmar', 'MM', 'Kyat', 'MMK', '/flags/myanmar.png', NULL),
(251, 'Palestine', 'PS', 'New Israeli Sheqel', 'ISL', '/flags/palestine.png', NULL),
(252, 'Serbia', 'RS', 'Serbian Dinar', 'RSD', '/flags/serbia.png', NULL),
(253, 'South Sudan', 'SS', 'South Sudanese pound', 'SSP', '/flags/south-sudan.png', NULL),
(254, 'Sao Tome and Principe', 'ST', 'Dobra', 'STN', '/flags/sao-tome-and-principe.png', NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_currencies` (`currency_id`, `currency_name`, `currency_description`, `currency_symbol`) VALUES
(2, 'AED', 'UAE Dirham', '#'),
(3, 'AFN', 'Afghani', '?'),
(4, 'ALL', 'Lek', 'Lek'),
(5, 'AMD', 'Armenian Dram', '#'),
(6, 'ANG', 'Netherlands Antillian Guilder', 'f'),
(7, 'AOA', 'Kwanza', '#'),
(8, 'ARS', 'Argentine Peso', '$'),
(9, 'AUD', 'Australian Dollar', '$'),
(10, 'AWG', 'Aruban Guilder', 'f'),
(11, 'AZN', 'Azerbaijanian Manat', '???'),
(12, 'BAM', 'Convertible Marks', 'KM'),
(13, 'BBD', 'Barbados Dollar', '$'),
(14, 'BDT', 'Taka', '#'),
(15, 'BGN', 'Bulgarian Lev', '??'),
(16, 'BHD', 'Bahraini Dinar', '#'),
(17, 'BIF', 'Burundi Franc', '#'),
(18, 'BMD', 'Bermudian Dollar (customarily known as Bermuda Dollar)', '$'),
(19, 'BND', 'Brunei Dollar', '$'),
(20, 'BOB BOV', 'Boliviano Mvdol', '\$b'),
(21, 'BRL', 'Brazilian Real', 'R$'),
(22, 'BSD', 'Bahamian Dollar', '$'),
(23, 'BWP', 'Pula', 'P'),
(24, 'BYN', 'Belarussian Ruble', 'p.'),
(25, 'BZD', 'Belize Dollar', 'BZ$'),
(26, 'CAD', 'Canadian Dollar', '$'),
(27, 'CDF', 'Congolese Franc', '#'),
(28, 'CHF', 'Swiss Franc', 'CHF'),
(29, 'CLP CLF', 'Chilean Peso Unidades de fomento', '$'),
(30, 'CNY', 'Yuan Renminbi', 'Y'),
(31, 'COP COU', 'Colombian Peso Unidad de Valor Real', '$'),
(32, 'CRC', 'Costa Rican Colon', '?'),
(33, 'CUP CUC', 'Cuban Peso Peso Convertible', '?'),
(34, 'CVE', 'Cape Verde Escudo', '#'),
(35, 'CZK', 'Czech Koruna', 'Kč'),
(36, 'DJF', 'Djibouti Franc', '#'),
(37, 'DKK', 'Danish Krone', 'kr'),
(38, 'DOP', 'Dominican Peso', 'RD$'),
(39, 'DZD', 'Algerian Dinar', '#'),
(40, 'EEK', 'Kroon', '#'),
(41, 'EGP', 'Egyptian Pound', 'E£'),
(42, 'ERN', 'Nakfa', '#'),
(43, 'ETB', 'Ethiopian Birr', '#'),
(44, 'EUR', 'Euro', '€'),
(45, 'FJD', 'Fiji Dollar', '$'),
(46, 'FKP', 'Falkland Islands Pound', '£'),
(47, 'GBP', 'Pound Sterling', '£'),
(48, 'GEL', 'Lari', '#'),
(49, 'GHS', 'Cedi', '#'),
(50, 'GIP', 'Gibraltar Pound', '£'),
(51, 'GMD', 'Dalasi', '#'),
(52, 'GNF', 'Guinea Franc', '#'),
(53, 'GTQ', 'Quetzal', 'Q'),
(54, 'GYD', 'Guyana Dollar', '$'),
(55, 'HKD', 'Hong Kong Dollar', '$'),
(56, 'HNL', 'Lempira', 'L'),
(57, 'HRK', 'Croatian Kuna', 'kn'),
(58, 'HTG USD', 'Gourde US Dollar', '$'),
(59, 'HUF', 'Forint', 'Ft'),
(60, 'IDR', 'Rupiah', 'Rp'),
(61, 'ILS', 'New Israeli Sheqel', '?'),
(62, 'INR', 'Indian Rupee', '#'),
(63, 'INR BTN', 'Indian Rupee Ngultrum', '#'),
(64, 'IQD', 'Iraqi Dinar', '#'),
(65, 'IRR', 'Iranian Rial', '?'),
(66, 'ISK', 'Iceland Krona', 'kr'),
(67, 'JMD', 'Jamaican Dollar', 'J$'),
(68, 'JOD', 'Jordanian Dinar', '#'),
(69, 'JPY', 'Yen', 'Y'),
(70, 'KES', 'Kenyan Shilling', '#'),
(71, 'KGS', 'Som', '??'),
(72, 'KHR', 'Riel', '?'),
(73, 'KMF', 'Comoro Franc', '#'),
(74, 'KPW', 'North Korean Won', '?'),
(75, 'KRW', 'Won', '?'),
(76, 'KWD', 'Kuwaiti Dinar', '#'),
(77, 'KYD', 'Cayman Islands Dollar', '$'),
(78, 'KZT', 'Tenge', '??'),
(79, 'LAK', 'Kip', '?'),
(80, 'LBP', 'Lebanese Pound', 'L'),
(81, 'LKR', 'Sri Lanka Rupee', '?'),
(82, 'LRD', 'Liberian Dollar', '$'),
(83, 'LTL', 'Lithuanian Litas', 'Lt'),
(84, 'LVL', 'Latvian Lats', 'Ls'),
(85, 'LYD', 'Libyan Dinar', '#'),
(86, 'MAD', 'Moroccan Dirham', '#'),
(87, 'MDL', 'Moldovan Leu', '#'),
(88, 'MGA', 'Malagasy Ariary', '#'),
(89, 'MKD', 'Denar', '???'),
(90, 'MMK', 'Kyat', '#'),
(91, 'MNT', 'Tugrik', '?'),
(92, 'MOP', 'Pataca', '#'),
(93, 'MRO', 'Ouguiya', '#'),
(94, 'MUR', 'Mauritius Rupee', '?'),
(95, 'MVR', 'Rufiyaa', '#'),
(96, 'MWK', 'Kwacha', '#'),
(97, 'MXN MXV', 'Mexican Peso Mexican Unidad de Inversion (UDI)', '$'),
(98, 'MYR', 'Malaysian Ringgit', 'RM'),
(99, 'MZN', 'Metical', 'MT'),
(100, 'NGN', 'Naira', '?'),
(101, 'NIO', 'Cordoba Oro', 'C$'),
(102, 'NOK', 'Norwegian Krone', 'kr'),
(103, 'NPR', 'Nepalese Rupee', '?'),
(104, 'NZD', 'New Zealand Dollar', '$'),
(105, 'OMR', 'Rial Omani', '?'),
(106, 'PAB USD', 'Balboa US Dollar', 'B/.'),
(107, 'PEN', 'Nuevo Sol', 'S/.'),
(108, 'PGK', 'Kina', '#'),
(109, 'PHP', 'Philippine Peso', 'Php'),
(110, 'PKR', 'Pakistan Rupee', '?'),
(111, 'PLN', 'Zloty', 'zł'),
(112, 'PYG', 'Guarani', 'Gs'),
(113, 'QAR', 'Qatari Rial', '?'),
(114, 'RON', 'New Leu', 'lei'),
(115, 'RSD', 'Serbian Dinar', '???.'),
(116, 'RUB', 'Russian Ruble', '???'),
(117, 'RWF', 'Rwanda Franc', '#'),
(118, 'SAR', 'Saudi Riyal', '?'),
(119, 'SBD', 'Solomon Islands Dollar', '$'),
(120, 'SCR', 'Seychelles Rupee', '?'),
(121, 'SDG', 'Sudanese Pound', '#'),
(122, 'SEK', 'Swedish Krona', 'kr'),
(123, 'SGD', 'Singapore Dollar', '$'),
(124, 'SHP', 'Saint Helena Pound', 'L'),
(125, 'SLL', 'Leone', '#'),
(126, 'SOS', 'Somali Shilling', 'S'),
(127, 'SRD', 'Surinam Dollar', '$'),
(128, 'STD', 'Dobra', '#'),
(129, 'SVC USD', 'El Salvador Colon US Dollar', '$'),
(130, 'SYP', 'Syrian Pound', 'L'),
(131, 'SZL', 'Lilangeni', '#'),
(132, 'THB', 'Baht', '?'),
(133, 'TJS', 'Somoni', '#'),
(134, 'TMT', 'Manat', '#'),
(135, 'TND', 'Tunisian Dinar', '#'),
(136, 'TOP', 'Pa\'anga', '#'),
(137, 'TRY', 'Turkish Lira', 'TL'),
(138, 'TTD', 'Trinidad and Tobago Dollar', 'TT$'),
(139, 'TWD', 'New Taiwan Dollar', 'NT$'),
(140, 'TZS', 'Tanzanian Shilling', '#'),
(141, 'UAH', 'Hryvnia', '?'),
(142, 'UGX', 'Uganda Shilling', '#'),
(143, 'USD', 'US Dollar', '$'),
(144, 'UYU UYI', 'Peso Uruguayo Uruguay Peso en Unidades Indexadas', '\$U'),
(145, 'UZS', 'Uzbekistan Sum', '??'),
(146, 'VEF', 'Bolivar Fuerte', 'Bs'),
(147, 'VND', 'Dong', '?'),
(148, 'VUV', 'Vatu', '#'),
(149, 'WST', 'Tala', '#'),
(150, 'XAF', 'CFA Franc BEAC', '#'),
(151, 'XAG', 'Silver', '#'),
(152, 'XAU', 'Gold', '#'),
(153, 'XBA', 'Bond Markets Units European Composite Unit (EURCO)', '#'),
(154, 'XBB', 'European Monetary Unit (E.M.U.-6)', '#'),
(155, 'XBC', 'European Unit of Account 9(E.U.A.-9)', '#'),
(156, 'XBD', 'European Unit of Account 17(E.U.A.-17)', '#'),
(157, 'XCD', 'East Caribbean Dollar', '$'),
(158, 'XDR', 'SDR', '#'),
(159, 'XFU', 'UIC-Franc', '#'),
(160, 'XOF', 'CFA Franc BCEAO', '#'),
(161, 'XPD', 'Palladium', '#'),
(162, 'XPF', 'CFP Franc', '#'),
(163, 'XPT', 'Platinum', '#'),
(164, 'XTS', 'Codes specifically reserved for testing purposes', '#'),
(165, 'YER', 'Yemeni Rial', '?'),
(166, 'ZAR', 'Rand', 'R'),
(167, 'ZAR LSL', 'Rand Loti', '#'),
(168, 'ZAR NAD', 'Rand Namibia Dollar', '#'),
(169, 'ZMK', 'Zambian Kwacha', '#'),
(170, 'ZWL', 'Zimbabwe Dollar', '#');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_date_formats` (`id`, `name`, `dateFormat`, `calendarFormat`, `defaultDateValue`) VALUES
(1, 'y-m-d', 'Y-m-d', '%Y-%m-%d', '0000-00-00'),
(2, 'd-m-y', 'd-m-Y', '%d-%m-%Y', '00-00-0000'),
(3, 'm/d/y', 'm/d/Y', '%m/%d/%Y', '00-00-0000');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_default_attributes` (`id`, `name`, `listing_config`, `offer_config`, `event_config`) VALUES
(2, 'comercial_name', 3, -1, -1),
(3, 'tax_code', 3, -1, -1),
(4, 'registration_code', 3, -1, -1),
(5, 'website', 2, -1, -1),
(6, 'type', 1, 2, 2),
(7, 'slogan', 2, -1, -1),
(8, 'description', 1, 1, 1),
(9, 'keywords', 2, -1, -1),
(10, 'category', 1, 1, 1),
(11, 'logo', 1, -1, -1),
(12, 'street_number', 1, 1, 1),
(13, 'address', 1, 1, 1),
(14, 'city', 1, 1, 1),
(15, 'region', 1, 1, 1),
(16, 'country', 1, 1, 1),
(17, 'postal_code', 1, 1, 1),
(18, 'map', 1, 1, 1),
(20, 'phone', 1, -1, 1),
(21, 'mobile_phone', 2, -1, -1),
(22, 'fax', 3, -1, -1),
(23, 'email', 1, -1, 1),
(24, 'pictures', 2, 2, 2),
(25, 'video', 2, 2, 2),
(26, 'social_networks', 2, -1, -1),
(27, 'short_description', 2, 2, 2),
(28, 'contact_person', 2, -1, -1),
(29, 'attachments', 2, 2, 2),
(30, 'custom_tab', 2, -1, -1),
(31, 'cover_image', 2, -1, -1),
(32, 'opening_hours', 2, -1, -1),
(33, 'metadata_information', 2, 2, 2),
(34, 'publish_dates', 3, -1, -1),
(35, 'province', 2, 2, 2),
(36, 'area', 2, 2, 2),
(37, 'sounds', 2, 2, -1),
(38, 'establishment_year', 2, -1, -1),
(39, 'employees', 2, -1, -1),
(40, 'services_list', 2, -1, -1),
(41, 'testimonials', 2, -1, -1),
(42, 'ad_images', 3, -1, -1),
(43, 'price_text', -1, 2, -1),
(44, 'related_listing', 2, -1, -1),
(45, 'radius', 3, -1, -1),
(46, 'associated_listings', -1, -1, 2),
(47, 'age', -1, -1, 2),
(48, 'attendance', -1, -1, 2),
(49, 'coupons', -1, 2, -1),
(50, 'zip_codes', 3, -1, -1),
(51, 'business_team', 2, -1, -1),
(52, 'price', -1, -1, 2),
(53, 'enable_subscription', -1, -1, 2),
(54, 'total_tickets', -1, -1, 2),
(55, 'time_zone', -1, -1, 2),
(56, 'booking_dates', -1, -1, 2),
(57, 'address_autocomplete', 1, -1, -1),
(58, 'publish_only_city', 3, -1, -1),
(59, 'facebook', 2, -1, -1),
(60, 'twitter', 2, -1, -1),
(61, 'linkedin', 2, -1, -1),
(62, 'skype', 2, -1, -1),
(63, 'instagram', 2, -1, -1),
(64, 'pinterest', 2, -1, -1),
(65, 'whatsapp', 2, -1, -1),
(66, 'youtube', 2, -1, -1),
(67, 'custom_gallery', 3, -1, -1),
(68, 'ticket_url', -1, -1, 2),
(69, 'min_project_size', 2, -1, -1),
(70, 'hourly_rate', 2, -1, -1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_directory_apps` (`id`, `name`, `app_name`, `description`, `icon`, `store_link`, `doc_link`, `version`, `required_version`, `type`) VALUES
(1, 'WPBD Appointments', 'WPBD Appointments', 'Allows the possibility to define business services and book them online', 'directoryApps/service_booking.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-appointments', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-appointments', NULL, '2.3.1', 1),
(2, 'WPBD Event Bookings', 'WPBD Event Bookings', 'Allows the possibility to create and book tickets for events', 'directoryApps/event_ticket_reservation.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-event-booking', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#event-booking', NULL, '2.3.0', 1),
(3, 'WPBD Event Appointments', 'WPBD Event Appointments', 'Allows the possibility to define event appointments and book them online', 'directoryApps/event_appointment.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-event-appointments', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#event-appointment', NULL, '1.3.1', 1),
(4, 'WPBD Recurring Events', 'WPBD Recurring Events', 'Allows the possibility to have recurring events', 'directoryApps/recurring_events.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-recurring-events', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-recurring-events', NULL, '1.3.0', 1),
(5, 'WPBD Sell Offers', 'WPBD Sell Offers', 'Allows the possibility to sell offers online', 'directoryApps/offer_selling.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-sell-offers', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#offer-selling', NULL, '2.3.0', 1),
(6, 'WPBD Conference', 'WPBD Conference', 'Adds the possibility of creating conferences, sessions and speakers', 'directoryApps/conference.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-conference', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-conference', NULL, '2.2.1', 1),
(7, 'WPBD Paypal Subscriptions', 'WPBD PayPal Subscriptions', 'Provides the possibility of having PayPal subscriptions', 'directoryApps/paypal.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-paypal-subscriptions', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-paypal-subscriptions', NULL, '2.1.0', 1),
(8, 'WPBD Stripe', 'WPBD Stripe', 'Provides the possibility to receive payments using Stripe payment gateway', 'directoryApps/stripe.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-stripe', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-stripe', NULL, '1.3.0', 1),
(9, 'WPBD Stripe Subscriptions', 'WPBD Stripe Subscriptions', 'Enables the receiving of a recurring payment through Stripe Subscriptions', 'directoryApps/stripe_subscription.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-stripe-subscriptions', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-stripe-subscription', NULL, '2.1.0', 1),
(12,'WPBD Payfast Subscriptions', 'WPBD Payfast Subscriptions', 'Provides the possibility of having Payfast recurring payments.', 'directoryApps/payfastsubscription.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-payfast-subscriptions', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-payfast-subscription', NULL, '1.2.0', 1),
(13,'J-BusinessDirectory Maps', 'J-BusinessDirectory Maps', 'Joomla map module that displays business listing on a map', 'directoryApps/jbd_maps.png', 'https://www.cmsjunkie.com/j-businessdirectory-map-module', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-maps', NULL, '1.1.0', 2),
(14,'JBusinessDirectory - Locations', 'JBusinessDirectory - Locations', 'Joomla module that allows you to provide some quick links for your visitors, for the regions and the cities of your directory.', 'directoryApps/jbd_locations.png', 'https://www.cmsjunkie.com/j-businessdirectory-locations-module', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-locations', NULL, '1.1.0', 2),
(15,'JBusinessDirectory - Reviews', 'JBusinessDirectory - Reviews', 'Display reviews based on the selected criterias and options', 'directoryApps/jbd_reviews.png', 'https://www.cmsjunkie.com/j-businessdirectory-reviews', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-reviews', NULL, '1.1.0', 2),
(22,'WPBD Campaigns', 'WPBD Campaigns', 'Allows the possibility to create paid campaigns for businesses', 'directoryApps/jbd_campaigns.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-campaigns', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-campaigns', NULL, '1.4.2', 1),
(23,'WPBD Authorize', 'WPBD Authorize', 'Provides the possibility to receive payments using the Authorize.net payment gateway.', 'directoryApps/jbd_authorize.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-authorize', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-authorize', NULL, NULL, 1),
(24,'WPBD Authorize Subscriptions', 'WPBD Authorize Subscriptions', 'Provides the possibility to receive recurring payments using the Authorize.net payment gateway.', 'directoryApps/jbd_authorize_subscriptions.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-authorize-subscription', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-authorize-subscription', NULL, NULL, 1),
(25,'WPBD Quote Requests', 'WPBD Quote Requests', 'Allows users to request quotes about different categories to business owners', 'directoryApps/jbd_quote_requests.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-quote-requests', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-quote-requests', NULL, '1.6.0', 1),
(26,'WPBD Mercado Pago', 'WPBD Mercado Pago', 'WPBD Mercado Pago payment methdo', 'directoryApps/jbd_mercado_pago.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-mercado-pago', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-mercado-pago', NULL, '1.0.0', 1),
(27,'WPBD Trips', 'WPBD Trips', 'WPBD Trips application', 'directoryApps/jbd_trips.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-trips', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-trips', NULL, '1.0.0', 1),
(28,'WPBD Videos', 'WPBD Videos', 'WPBD Videos application', 'directoryApps/jbd_videos.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-videos', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-videos', NULL, NULL, 1),
(29,'WPBD Mollie', 'WPBD Mollie', 'WPBD Mollie payment methdod', 'directoryApps/jbd_mollie.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-mollie', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-mollie', NULL, '1.0.0', 1),
(30,'WPBD Mollie Subscriptions', 'WPBD Mollie Subscriptions', 'Provides the possibility of having Mollie subscriptions', 'directoryApps/jbd_mollie_subscriptions.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-mollie-subscription', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-mollie-subscription', NULL, '1.0.0', 1),
(31,'WPBD CardLink', 'WPBD CardLink', 'WPBD CardLink payment methdod', 'directoryApps/jbd_cardlink.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-cardlink', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-cardlink', NULL, '1.0.0', 1),
(32,'WPBD CardLink Subscriptions', 'WPBD CardLink Subscriptions', 'Provides the possibility of having CardLink subscriptions', 'directoryApps/jbd_cardlink_subscriptions.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-cardlink-subscription', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-cardlink-subscription', NULL, '1.0.0', 1),
(33,'WPBD Razorpay', 'WPBD Razorpay', 'WPBD Razorpay payment methdod', 'directoryApps/jbd_razorpay.png', 'https://www.cmsjunkie.com/joomla-business-directory/jbd-razorpay', 'https://www.cmsjunkie.com/docs/jbusinessdirectory/directoryapps.html#jbd-razorpay', NULL, '1.0.0', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(2, 'A new review has been posted for your business listing', 'Review Email', 'Review Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e41206e6577207265766965772077617320706f7374656420666f7220627573696e657373206c697374696e67205b627573696e6573735f6e616d655d3c6272202f3e596f752063616e20766965772074686520726576696577206174205b7265766965775f6c696e6b5dc2a03c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f6469763e, 1, 1),
(3, 'Your review has received a response', 'Review Response Email', 'Review Response Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f752068617665207265636569766564206120726573706f6e736520666f72207468652072657669657720706f7374656420666f722074686520636f6d70616e79203c623e5b627573696e6573735f6e616d655d3c2f623e2e203c6272202f3e596f752063616e2076696577207468652072657669657720726573706f6e7365206174205b7265766965775f6c696e6b5d2e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(4, 'Payment Receipt from [company_name]', 'Order E-mail', 'Order Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e0d0a3c703e48656c6c6f205b637573746f6d65725f6e616d655d2c3c6272202f3e3c6272202f3e596f7572207061796d656e7420666f7220796f7572206f6e6c696e65206f7264657220706c61636564206f6e3c2f703e0d0a3c703e3c6272202f3e5b736974655f616464726573735d206f6e205b6f726465725f646174655d20686173206265656e20617070726f7665642e3c6272202f3e3c6272202f3e596f7572207061796d656e742069732063757272656e746c79206265696e672070726f6365737365642e204f726465722070726f63657373696e6720757375616c6c793c6272202f3e74616b6573206120666577206d696e757465732e3c6272202f3e3c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e204f4e4c494e45204f52444552202d205041594d454e542044455441494c5320285041594d454e542052454345495054293c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e3c6272202f3e576562736974653a205b736974655f616464726573735d3c6272202f3e4f72646572207265666572656e6365203a205b6f726465725f69645d3c2f703e0d0a3c703e3c6272202f3e5061796d656e74206d6574686f643a205b7061796d656e745f6d6574686f645d3c6272202f3e496e766f696365204e756d6265723a205b696e766f6963655f6e756d6265725d3c2f703e0d0a3c703e54617820416d6f756e743a205b7461785f616d6f756e745d3c2f703e0d0a3c703e3c6272202f3e446174652f74696d653a205b6f726465725f646174655d3c6272202f3e4f726465722047656e6572616c20546f74616c3a205b746f74616c5f70726963655d3c6272202f3e3c6272202f3e2d2d2d2d2d2d3c2f703e0d0a3c703e5b7461785f64657461696c5d3c6272202f3e2d2d2d2d2d2d3c2f703e0d0a3c703e3c6272202f3e42696c6c696e6720696e666f726d6174696f6e2069733a3c6272202f3e5b62696c6c696e675f696e666f726d6174696f6e5d3c6272202f3e3c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e3c6272202f3e3c6272202f3e5468616e6b20796f752c3c2f703e0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(5, 'You have been contacted for the company [business_name]', 'Contact E-Mail', 'Contact Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c3c6272202f3e2042656c6f7720617265207468652064657461696c732072656c6174656420746f2074686520636f6e7461637420666f7220746865c2a0636f6d70616e79205b627573696e6573735f6e616d655d3c6272202f3e3c6272202f3e204e616d653a5b66697273745f6e616d655d205b6c6173745f6e616d655d3c6272202f3e452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e50686f6e653a205b70686f6e655d3c6272202f3e3c6272202f3e5b636f6e746163745f656d61696c5f636f6e74656e745d3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(6, 'A new review abuse was reported', 'Report Abuse', 'Report Abuse Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c203c6272202f3e3c6272202f3e2041206e657720616275736520776173207265706f7274656420666f722074686520726576696577c2a03c7374726f6e673e5b7265766965775f6e616d655d3c2f7374726f6e673e2c20666f7220746865205b627573696e6573735f6e616d655d2e3c6272202f3e20596f752063616e207669657720746865207265766965772061743a205b7265766965775f6c696e6b5d3c6272202f3e20452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e3c6272202f3e3c623e4162757365206465736372697074696f6e3a3c2f623e3c6272202f3e5b61627573655f6465736372697074696f6e5d203c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(7, 'Your business listing is about to expire', 'Expiration Notification', 'Expiration Notification Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f757220627573696e657373206c697374696e672077697468206e616d65205b627573696e6573735f6e616d655d2069732061626f757420746f2065787069726520696e205b6578705f646179735d20646179732e3c6272202f3e596f752063616e20657874656e642074686520627573696e657373206c697374696e6720627920636c69636b696e67207468652022457874656e6420706572696f6422206f6e20796f757220627573696e657373206c697374696e672064657461696c732e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(8, 'New Business Listing', 'New Business Listing Notification', 'New Company Notification Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20323570783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c203c6272202f3e3c6272202f3e2041206e657720627573696e657373206c697374696e67203c623e205b627573696e6573735f6e616d655d203c2f623e20776173206164646564206f6e20796f7572206469726563746f72792e3c6272202f3e3c6272202f3e0d0a3c7461626c65207374796c653d2270616464696e673a203570783b22206267636f6c6f723d2223464146394641223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a203070783b2070616464696e672d72696768743a20313070783b2220726f777370616e3d2235222076616c69676e3d226d6964646c65223e5b627573696e6573735f6c6f676f5d3c2f74643e0d0a3c74643e3c623e205b627573696e6573735f6e616d655d203c2f623e3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f616464726573735d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f63617465676f72795d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f776562736974655d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f636f6e746163745f706572736f6e5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(9, 'Your business listing was approved', 'Business Listing Approval', 'Approve Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e596f757220627573696e657373206c697374696e672077697468206e616d65c2a03c7374726f6e673e5b627573696e6573735f6e616d655d3c2f7374726f6e673ec2a077617320617070726f766564206279207468652061646d696e6973747261746f722e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f6469763e, 1, 1),
(10, 'Payment details', 'Payment details', 'Payment Details Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e0d0a3c703e48656c6c6f205b637573746f6d65725f6e616d655d2c3c6272202f3e3c6272202f3e596f7572206861766520706c6163656420616e206f7264657220666f72205b736572766963655f6e616d655d206f6e205b736974655f616464726573735d206f6e205b6f726465725f646174655d2e3c2f703e0d0a3c703e506c656173652066696e6420746865207061796d656e742064657461696c732062656c6c6f772e3c2f703e0d0a3c703e3c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e205041594d454e542044455441494c533c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c2f703e0d0a3c703e5b7061796d656e745f64657461696c735d3c2f703e0d0a3c703e3c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e204f4e4c494e45204f524445522044455441494c533c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e3c6272202f3e576562736974653a205b736974655f616464726573735d3c6272202f3e4f72646572207265666572656e6365206e6f3a205b6f726465725f69645d3c2f703e0d0a3c703e496e766f696365206e756d6265723a205b696e766f6963655f6e756d6265725d3c6272202f3e446174652f74696d653a205b6f726465725f646174655d3c6272202f3e3c6272202f3e2d2d2d2d2d2d3c6272202f3e5b7461785f64657461696c5d3c6272202f3e2d2d2d2d2d2d3c6272202f3e3c6272202f3e42696c6c696e6720696e666f726d6174696f6e2069733a3c6272202f3e5b62696c6c696e675f696e666f726d6174696f6e5d3c6272202f3e3c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e3c6272202f3e4265737420726567617264732c3c6272202f3e5b636f6d70616e795f6e616d655d3c2f703e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(11, 'A new quote request was posted', 'Request Quote', 'Request Quote Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e2041206e65772071756f746520726571756573742077617320706f73746564206f6e205b6469726563746f72795f776562736974655d2e3c6272202f3e4e616d653a3c623e5b66697273745f6e616d655d205b6c6173745f6e616d655d3c2f623e3c6272202f3e452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e50686f6e653a205b70686f6e655d3c6272202f3e3c6272202f3e3c623e5265717565737420636f6e74656e743c2f623e3c6272202f3e5b636f6e746163745f656d61696c5f636f6e74656e745d3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c703ec2a03c2f703e, 1, 1);
INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(12, 'Your business was added on our directory', 'Listing creation notification', 'Listing Creation Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a2031707820736f6c696420236666666666663b20636f6c6f723a20233434343434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20323570783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f757220627573696e657373203c623e205b627573696e6573735f6e616d655d203c2f623e20776173206164646564206f6e206f7572206469726563746f72792e3c6272202f3e3c6272202f3e0d0a3c7461626c65207374796c653d2270616464696e673a203570783b22206267636f6c6f723d2223464146394641223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a203070783b2070616464696e672d72696768743a20313070783b2220726f777370616e3d2235222076616c69676e3d226d6964646c65223e5b627573696e6573735f6c6f676f5d3c2f74643e0d0a3c74643e3c623e205b627573696e6573735f6e616d655d203c2f623e3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f616464726573735d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f63617465676f72795d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f776562736974655d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f636f6e746163745f706572736f6e5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(13, 'Your claim was approved', 'Positive claim response', 'Claim Response Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c203c6272202f3e3c6272202f3e20436f6e67726174756c6174696f6e732c20796f757220636c61696d20666f72206c697374696e67205b636c61696d65645f636f6d70616e795f6e616d655d20686173206265656e20617070726f7665642e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(14, 'You claim was not approved', 'Negative claim response', 'Claim Negative Response Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c203c6272202f3e3c6272202f3e20596f757220636c61696d20666f72206c697374696e67203c623e5b636c61696d65645f636f6d70616e795f6e616d655d3c2f623e20776173206e6f7420617070726f7665642e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(15, 'A new offer was added on your directory', 'Offer Creation', 'Offer Creation Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c203c6272202f3e3c6272202f3e2041206e6577206f666665723c7374726f6e673e205b6f666665725f6e616d655d203c2f7374726f6e673e20686173206265656e206164646564206f6e20796f7572206469726563746f72792e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c703ec2a03c2f703e, 1, 1),
(16, 'Your new [item_type] was  approved', 'Offer/Product Approval', 'Offer/Product Approval Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f7572c2a05b6974656d5f747970655dc2a03c623e5b6f666665725f6e616d655d3c2f623e2077617320617070726f766564206279207468652061646d696e6973747261746f722e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(17, 'A new event has been added to your directory', 'Event Creation Notification', 'Event Creation Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c203c6272202f3e3c6272202f3e2041206e6577206576656e74203c7374726f6e673e5b6576656e745f6e616d655d3c2f7374726f6e673e20686173206265656e206164646564206f6e20796f7572206469726563746f72792e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(18, 'Your new event was published', 'Event Approval Notificaiton', 'Event Approval Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f7572206576656e74203c623e5b6576656e745f6e616d655d3c2f623e2077617320617070726f766564206279207468652061646d696e6973747261746f722e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(19, 'Your booking details for [event_name]', 'Event Reservation Notification', 'Event Reservation Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c203c6272202f3e3c6272202f3e0d0a3c6469763e596f757220626f6f6b696e672064657461696c73206d616465206f6e205b6576656e745f626f6f6b696e675f646174655d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f67756573745f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d22626f726465722d626f74746f6d3a2031707820736f6c696420236161613b223e3c7374726f6e673e4576656e7420496e666f726d6174696f6e3c2f7374726f6e673e0d0a3c6469763e3c7374726f6e673e50686f6e653c2f7374726f6e673e3a205b6576656e745f70686f6e655d3c2f6469763e0d0a3c6469763e3c7374726f6e673e456d61696c3c2f7374726f6e673e3a205b6576656e745f656d61696c5d3c2f6469763e0d0a3c703e496620796f752068617665207175657374696f6e732061626f757420746865206576656e742c20706c6561736520636f6e7461637420746865206576656e74206f7267616e697a657220776974682074686520656d61696c2070726f76696465642061626f76652e3c2f703e0d0a3c703e466f7220667572746865722064657461696c732061626f757420746865206576656e742c20796f752063616e20636c69636b205b6576656e745f6c696e6b5d20746f2076697369742074686520706167652e3c2f703e0d0a3c703ec2a03c2f703e0d0a3c2f6469763e0d0a3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(20, 'Your ticket booking payment details for [event_name]', 'Event Payment Details', 'Event Payment Details', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e0d0a3c703e48656c6c6f205b637573746f6d65725f6e616d655d2c3c6272202f3e3c6272202f3e596f7572206861766520706c616365642061207469636b6574207265736572766174696f6e207265717565737420666f723c7374726f6e673e205b6576656e745f6e616d655d3c2f7374726f6e673e206f6e203c7374726f6e673e5b736974655f616464726573735d3c2f7374726f6e673e206f6e205b6576656e745f626f6f6b696e675f646174655d2e3c2f703e0d0a3c703e3c7374726f6e673e5041594d454e542044455441494c533c2f7374726f6e673e3c2f703e0d0a3c703e5b7061796d656e745f64657461696c735d3c2f703e0d0a3c7461626c65207374796c653d22636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c7374726f6e673e596f757220426f6f6b696e673c2f7374726f6e673e3c2f6469763e0d0a3c6469763e426f6f6b696e67206d616465206f6e205b6576656e745f626f6f6b696e675f646174655d3c2f6469763e0d0a3c6469763e3c6272202f3e426f6f6b696e67206e756d6265723a205b6576656e745f626f6f6b696e675f69645d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f67756573745f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(21, 'Your event is about to expire', 'Event Expiration Notification', 'Event Expiration Notification Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e596f7572206576656e742077697468206e616d65205b6576656e745f6e616d655d2069732061626f757420746f2065787069726520696e205b6578705f646179735d20646179732e3c6272202f3e596f752063616e20657874656e6420746865206576656e74206279206368616e67696e672074686520656e642064617465206f6e20796f7572206576656e742064657461696c732e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(22, 'Your offer is about to expire', 'Offer Expiration Notification', 'Offer Expiration Notification Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e596f7572206f666665722077697468206e616d65205b6f666665725f6e616d655d2069732061626f757420746f2065787069726520696e205b6578705f646179735d20646179732e3c6272202f3e596f752063616e20657874656e6420746865206f66666572206279206368616e67696e672074686520656e642064617465206f6e20796f7572206f666665722064657461696c732e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0);
INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(23, 'Report Notification', 'Report Notification', 'Report Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c203c6272202f3e3c6272202f3e56697369746f72207769746820656d61696c3a205b636f6e746163745f656d61696c5d202c207265706f7274656420636175736520225b7265706f72745f63617573655d222c20666f722074686520627573696e657373205b627573696e6573735f6e616d655d20776974682074686973206d657373616765203a3c2f6469763e0d0a3c6469763e5b61627573655f6465736372697074696f6e5d3c6272202f3e203c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(24, 'A business has been updated', 'Business Update Notification', 'Business Update Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a2031707820736f6c696420236666666666663b20636f6c6f723a20233434343434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20323570783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2cc2a03c6272202f3e3c6272202f3e596f757220627573696e657373c2a03c623e5b627573696e6573735f6e616d655dc2a03c2f623e686173206265656e20757064617465642ec2a03c6272202f3e436865636b206974206f7574c2a03c6120687265663d225b627573696e6573735f61646d696e5f706174685d223e686572653c2f613ec2a03c6272202f3e3c6272202f3e0d0a3c7461626c65207374796c653d2270616464696e673a203570783b22206267636f6c6f723d2223464146394641223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a203070783b2070616464696e672d72696768743a20313070783b222076616c69676e3d226d6964646c65223e5b627573696e6573735f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f6469763e, 1, 1),
(25, 'A review on offer has received an response', 'Offer Review Response Email', 'Offer Review Response Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f752068617665207265636569766564206120726573706f6e736520666f72207468652072657669657720706f7374656420666f7220746865206f66666572203c623e5b6f666665725f6e616d655d3c2f623e2c206f6e20627573696e657373203c623e5b627573696e6573735f6e616d655d3c2f623e2e203c6272202f3e596f752063616e2076696577207468652072657669657720726573706f6e7365206174205b7265766965775f6c696e6b5d2e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(26, 'A new review has been posted for an offer', 'Offer Review Email', 'Offer Review Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e2041206e6577207265766965772077617320706f7374656420666f7220746865206f66666572205b6f666665725f6e616d655d2c206f6e20627573696e657373206c697374696e67205b627573696e6573735f6e616d655d3c6272202f3e596f752063616e207669657720746865207265766965772061743a205b7265766965775f6c696e6b5d203c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(27, 'A new abuse was reported for an offer', 'Report Abuse Offer Review', 'Report Abuse Offer Review', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c203c6272202f3e3c6272202f3e2041206e657720616275736520776173207265706f7274656420666f72207468652072657669657720225b7265766965775f6e616d655d222c206164646564206f6e20746865206f66666572205b6f666665725f6e616d655d2c207075626c69736865642066726f6d205b627573696e6573735f6e616d655d2e203c6272202f3e20596f752063616e207669657720746865207265766965772061743a205b7265766965775f6f666665725f6c696e6b5d3c6272202f3e20452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e3c6272202f3e3c7374726f6e673e4162757365206465736372697074696f6e3a3c2f7374726f6e673e3c6272202f3e5b61627573655f6465736372697074696f6e5d203c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(28, 'Your order nr. [offer_order_id] has been completed', 'Offer Order Notification', 'Offer Order Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c0d0a3c6469763e596f7572206f72646572c2a0706c61636564c2a06f6e205b6f666665725f6f726465725f646174655d20686173206265656e20636f6d706c657465642e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b6f666665725f6f726465725f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b6f666665725f6f726465725f62757965725f64657461696c735d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(29, 'You have been contacted for the event [event_name]', 'Event Contact Email', 'Event Contact Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e42656c6f7720617265207468652064657461696c732072656c6174656420746f2074686520636f6e7461637420666f72c2a0746865c2a06576656e74205b6576656e745f6e616d655d3c6272202f3e3c6272202f3e204e616d653a5b66697273745f6e616d655d205b6c6173745f6e616d655d3c6272202f3e452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e3c6272202f3e5b636f6e746163745f656d61696c5f636f6e74656e745d3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(30, 'You have been contacted for the offer [offer_name]', 'Offer Contact Email', 'Offer Contact Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e42656c6f7720617265207468652064657461696c732072656c6174656420746f2074686520636f6e7461637420666f72c2a0746865206f66666572205b6f666665725f6e616d655d3c6272202f3e3c6272202f3e204e616d653a5b66697273745f6e616d655d205b6c6173745f6e616d655d3c6272202f3e452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e3c6272202f3e5b636f6e746163745f656d61696c5f636f6e74656e745d3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(31, 'You have reserved an appointment for [event_name] on [appointment_date]', 'Event Appointment Email', 'Event Appointment Email', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e596f752068617665206d61646520616e206170706f696e746d656e742066726f6d205b627573696e6573735f6e616d655d20666f722074686520666f6c6c6f77696e67206576656e743a3c7374726f6e673e205b6576656e745f6e616d655dc2a03c2f7374726f6e673e6f6e203c656d3e5b6170706f696e746d656e745f646174655d3c2f656d3e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e54686573652061726520746865206170706f696e746d656e742064657461696c733a3c6272202f3e4e616d653a205b66697273745f6e616d655d205b6c6173745f6e616d655d3c6272202f3e452d6d61696c3a205b656d61696c5d3c6272202f3e50686f6e653a205b70686f6e655d3c6272202f3e446174652026616d703b2054696d653a205b6170706f696e746d656e745f646174655d20c2a05b6170706f696e746d656e745f74696d655d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 1),
(32, 'Your appointment status has changed', 'Event Appointment Status Notification', 'Event Appointment Status Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2cc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e596f7572206170706f696e746d656e7420666f72c2a03c7374726f6e673e5b6576656e745f6e616d655dc2a03c2f7374726f6e673e6f6e205b6170706f696e746d656e745f646174655d205b6170706f696e746d656e745f74696d655d20686173206265656ec2a03c656d3e5b6170706f696e746d656e745f7374617475735d2e3c2f656d3e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 1);
INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(33, 'Monthly Statistical for your listing', 'Business Statistics Email', 'Business Statistics Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a2031707820736f6c696420236666666666663b20636f6c6f723a20233434343434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223eefbfbd3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20323570783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f757220627573696e6573732c203c623e205b627573696e6573735f6e616d655d203c2f623e206861732074686573652073746174697374696373206461746120666f722074686973206d6f6e74683a3c6272202f3e3c6272202f3e0d0a3c646976207374796c653d2270616464696e673a20313570783b206261636b67726f756e642d636f6c6f723a20236536663066373b20666c6f61743a206c6566743b2077696474683a203934253b223ec2a00d0a3c7461626c65207374796c653d2270616464696e673a203570783b2077696474683a2034333270783b22206267636f6c6f723d2223653666306637223e0d0a3c74626f64793e0d0a3c7472207374796c653d226865696768743a20323170783b223e0d0a3c7464207374796c653d226c696e652d6865696768743a203070783b2070616464696e672d72696768743a20313070783b2077696474683a2032373670783b206865696768743a20353970783b2220726f777370616e3d2238222076616c69676e3d226d6964646c65223e5b627573696e6573735f6c6f676f5d0d0a3c703ec2a03c2f703e0d0a3c703ec2a03c2f703e0d0a3c703ec2a03c2f703e0d0ac2a0c2a03c2f74643e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20323170783b223e3c623e5b627573696e6573735f6e616d655d3c2f623e3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20323170783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e0d0a3c703e546f74616c205669657720436f756e74203d205b627573696e6573735f766965775f636f756e745d3c2f703e0d0a3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20313770783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e4d6f6e74686c79c2a05669657720436f756e74203d205b6d6f6e74686c795f766965775f636f756e745d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20313770783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e4d6f6e74686c79c2a041727469636c6573c2a05669657773203d205b6d6f6e74686c795f61727469636c655f636f756e745d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20313770783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e546f74616c204176657261676520526174696e67203d205b627573696e6573735f726174696e675d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20313770783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e526576696577204e756d626572203d205b627573696e6573735f7265766965775f636f756e745d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20313770783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e5b627573696e6573735f776562736974655d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d226865696768743a20313770783b223e0d0a3c7464207374796c653d2277696474683a2033383070783b206865696768743a20313570783b223e5b627573696e6573735f616464726573735d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c6272202f3e3c6272202f3e0d0a3c646976207374796c653d22666c6f61743a206c6566743b2077696474683a20313030253b223ec2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a20313570783b206261636b67726f756e642d636f6c6f723a20236536663066373b2077696474683a203433253b20666c6f61743a206c6566743b223e596f7572206576656e74732064657461696c73206172653a203c6272202f3e3c6272202f3e205b6576656e74735f64657461696c5d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a20313570783b206261636b67726f756e642d636f6c6f723a20236536663066373b2077696474683a203433253b20666c6f61743a2072696768743b223e596f7572206f66666572732064657461696c73206172653a203c6272202f3e3c6272202f3e205b6f66666572735f64657461696c5d3c2f6469763e0d0a3c646976207374796c653d22666c6f61743a206c6566743b2077696474683a20313030253b223ec2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b206261636b67726f756e642d636f6c6f723a20236536663066373b2077696474683a203938253b20666c6f61743a206c6566743b2070616464696e672d6c6566743a2032253b223e596f757220726576696577733a203c6272202f3e3c6272202f3e205b627573696e6573735f726576696577735d3c2f6469763e0d0a3c646976207374796c653d22666c6f61743a206c6566743b2077696474683a20313030253b223ec2a03c2f6469763e0d0a3c6272202f3e203c6272202f3e0d0a3c646976207374796c653d2277696474683a20313030253b20666c6f61743a206c6566743b223e5468616e6b20796f752c3c2f6469763e0d0a3c646976207374796c653d2277696474683a20313030253b20666c6f61743a206c6566743b223e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223eefbfbd3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(34, 'Take advantage of new features for your business', 'Business Upgrade Notification', 'Business Upgrade Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c3c6272202f3e3c6272202f3e5468616e6b20796f7520666f72206265696e672070617274206f66206f7572206469726563746f72792e3c6272202f3e3c6272202f3e41742074686973206d6f6d656e7420796f7520617265207573696e67206365727461696e206c696d69746564206665617475726573206f66206f7572206469726563746f72792e20546f20686176652061636365737320746f206f7468657220666561747572657320796f752063616e207570677261646520746f206f7572207061636b6167657320627920636c69636b696e6720746865206c696e6b2062656c6f773a203c6272202f3e203c6120687265663d225b6c696e6b5f627573696e6573735f636f6e74726f6c5f70616e656c5d223e2055706772616465206e6f773c2f613e203c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(35, 'Your appointment booking nr. [service_booking_id] has been completed', 'Service Booking Notification', 'Service Booking Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e596f752068617665206d61646520612073657276696365207265736572766174696f6e206f6e205b736572766963655f626f6f6b696e675f646174655d20666f72205b736572766963655f626f6f6b696e675f6e616d655d3c6272202f3e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b736572766963655f626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b736572766963655f62757965725f64657461696c735d3c2f6469763e0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(36, 'New companies have joined your event [event_name]', 'Company Association Notification', 'Company Association Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e2054686520666f6c6c6f77696e6720636f6d70616e6965732068617665206a6f696e656420796f7572206576656e7420223c7374726f6e673e5b6576656e745f6e616d655d22203a3c6272202f3e3c6272202f3e3c2f7374726f6e673e3c656d3e5b636f6d70616e795f6e616d65735d3c6272202f3e3c2f656d3e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(37, 'Your business [business_name] was disapproved', 'Business Listing Disapproval', 'Disapprove Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20596f757220627573696e657373206c697374696e672077697468206e616d65203c7374726f6e673e5b627573696e6573735f6e616d655d3c2f7374726f6e673e20686173206265656e20646973617070726f766564206279207468652061646d696e6973747261746f722e3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(38, 'Business [company_name] has been claimed.', 'Claim Request Email', 'Claim Request Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e427573696e657373206c697374696e672077697468206e616d65c2a03c7374726f6e673e5b627573696e6573735f6e616d655d3c2f7374726f6e673ec2a0686173206265656ec2a0636c61696d65642e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(39, 'Your order has been shipped', 'Offer Shipping Notification', 'Offer Shipping Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b223e0d0a3c7461626c65207374796c653d2277696474683a203536342e3570783b223e0d0a3c74626f64793e0d0a3c7472207374796c653d226865696768743a20363370783b223e0d0a3c7464207374796c653d2277696474683a203536362e3570783b206865696768743a20363370783b223e3c7370616e207374796c653d22636f6c6f723a20233434343434343b20666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b223e5b636f6d70616e795f6c6f676f5d3c2f7370616e3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e596f7572206f66666572206f7264657220686173206265656e20736869707065642e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c7374726f6e673e4f726465722044657461696c733c2f7374726f6e673e3c2f6469763e0d0a3c6469763e4f72646572c2a06d616465206f6e205b6f666665725f6f726465725f646174655d3c2f6469763e0d0a3c6469763ec2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b206261636b67726f756e642d636f6c6f723a20236536663066373b223e5b6f666665725f6f726465725f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b6f666665725f6f726465725f62757965725f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e596f752063616e20747261636b20796f7572206f7264657220686572653a3c6272202f3e3c6120687265663d225b6f666665725f6f726465725f747261636b696e675f6c696e6b5d223e5b6f666665725f6f726465725f747261636b696e675f6c696e6b5d3c2f613e3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b206865696768743a20383270783b2220626f726465723d2230222077696474683d22353036222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d626f74746f6d2d7374796c653a20736f6c69643b20626f726465722d626f74746f6d2d636f6c6f723a20236161616161613b2077696474683a2035303270783b2220636f6c7370616e3d2232223e0d0a3c68323ec2a03c2f68323e0d0a3c703ec2a03c2f703e0d0a3c703ec2a03c2f703e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2277696474683a2032303570783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c7464207374796c653d2277696474683a2032393570783b223e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2077696474683a2035303270783b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(40, 'Tickets booking for [event_name]', 'Event Reservation Waiting Notification', 'Event Reservation Waiting Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c6469763e4120626f6f6b696e6720686173206265656ec2a0706c61636564206f6e205b6576656e745f626f6f6b696e675f646174655d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f67756573745f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b20666f6e742d73697a653a20313470783b223e3c7374726f6e673e5061796d656e74c2a044657461696c733c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b7061796d656e745f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c6272202f3e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d626f74746f6d3a2031707820736f6c696420236161613b2220636f6c7370616e3d2232223e0d0a3c68323e3c7374726f6e673e4576656e7420496e666f726d6174696f6e3c2f7374726f6e673e3c2f68323e0d0a3c6469763e3c7374726f6e673e50686f6e653c2f7374726f6e673e3a205b6576656e745f70686f6e655d3c2f6469763e0d0a3c6469763e3c7374726f6e673e456d61696c3c2f7374726f6e673e3a205b6576656e745f656d61696c5d3c2f6469763e0d0a3c703e496620796f752068617665207175657374696f6e732061626f757420746865206576656e742c20706c6561736520636f6e7461637420746865206576656e74206f7267616e697a657220776974682074686520656d61696c2070726f76696465642061626f76652e3c2f703e0d0a3c703e466f7220667572746865722064657461696c732061626f757420746865206576656e742c20796f752063616e20636c69636b205b6576656e745f6c696e6b5d20746f2076697369742074686520706167652e3c2f703e0d0a3c703ec2a03c2f703e0d0a3c703ec2a03c2f703e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 1),
(41, 'Service booking nr. [service_booking_id] has been made', 'Service Booking Waiting Notification', 'Service Booking Waiting Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c74643e3c63656e7465723e0d0a3c7461626c65207374796c653d2277696474683a203537342e3570783b222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2277696474683a203537322e3570783b2220616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e4e65772073657276696365207265736572766174696f6e20686173206265656e206d616465c2a06f6e205b736572766963655f626f6f6b696e675f646174655d20666f72c2a03c7374726f6e673e5b736572766963655f626f6f6b696e675f6e616d655d3c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b736572766963655f626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b736572766963655f62757965725f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b20666f6e742d73697a653a20313470783b223e3c7374726f6e673e5061796d656e742044657461696c733c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b7061796d656e745f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b206865696768743a20383270783b2220626f726465723d2230222077696474683d22353036222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d626f74746f6d2d7374796c653a20736f6c69643b20626f726465722d626f74746f6d2d636f6c6f723a20236161616161613b2077696474683a2035303270783b2220636f6c7370616e3d2232223e0d0a3c68323ec2a03c2f68323e0d0a3c703ec2a03c2f703e0d0a3c703ec2a03c2f703e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2277696474683a2032303570783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c7464207374796c653d2277696474683a2032393570783b223e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2077696474683a2035303270783b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 1);
INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(42, 'Order nr. [offer_order_id] has been made', 'Offer Order Waiting Notification', 'Offer Order Waiting Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c6469763e416e206f7264657220686173206265656ec2a0706c61636564206f6e205b6f666665725f6f726465725f646174655d3c2f6469763e0d0a3c6469763ec2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b6f666665725f6f726465725f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b6f666665725f6f726465725f62757965725f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b20666f6e742d73697a653a20313470783b223e3c7374726f6e673e5061796d656e74c2a044657461696c733c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b7061796d656e745f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b206865696768743a20383270783b2220626f726465723d2230222077696474683d22353036222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d626f74746f6d2d7374796c653a20736f6c69643b20626f726465722d626f74746f6d2d636f6c6f723a20236161616161613b2077696474683a2035303270783b2220636f6c7370616e3d2232223e0d0a3c68323ec2a03c2f68323e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2277696474683a2032303570783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c7464207374796c653d2277696474683a2032393570783b223e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2077696474683a2035303270783b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 1),
(43, 'Campaign Payment nr. [campaign_id] has been made', 'Campaign Payment Notification', 'Campaign Payment Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c65207374796c653d2277696474683a203537342e3570783b222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2277696474683a203537322e3570783b2220616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e643a20236666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e4e6577c2a0627564676574207061796d656e74c2a0686173206265656e206d61646520666f722074686520666f6c6c6f77696e672063616d706169676e3a203c7374726f6e673e5b63616d706169676e5f6e616d655dc2a03c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b63616d706169676e5f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b63616d706169676e5f62757965725f64657461696c735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b206865696768743a20383270783b2220626f726465723d2230222077696474683d22353036222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d626f74746f6d2d7374796c653a20736f6c69643b20626f726465722d626f74746f6d2d636f6c6f723a20236161616161613b2077696474683a2035303270783b2220636f6c7370616e3d2232223e0d0a3c68323ec2a03c2f68323e0d0a3c703ec2a03c2f703e0d0a3c703ec2a03c2f703e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2277696474683a2032303570783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c7464207374796c653d2277696474683a2032393570783b223e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2077696474683a2035303270783b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 1),
(44, 'Campaign Payment nr. [campaign_id] waiting for confirmation', 'Campaign Payment Waiting Notification', 'Campaign Payment Waiting Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c65207374796c653d2277696474683a203537342e3570783b222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2277696474683a203537322e3570783b2220616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e4e6577c2a0627564676574207061796d656e74c2a0686173206265656e206d61646520666f722074686520666f6c6c6f77696e672063616d706169676e3ac2a03c7374726f6e673e5b63616d706169676e5f6e616d655dc2a03c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b63616d706169676e5f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b63616d706169676e5f62757965725f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c7374726f6e673e5061796d656e742044657461696c733c2f7374726f6e673e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b7061796d656e745f64657461696c735d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c2073616e732d73657269663b206865696768743a20383270783b2220626f726465723d2230222077696474683d22353036222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d626f74746f6d2d7374796c653a20736f6c69643b20626f726465722d626f74746f6d2d636f6c6f723a20236161616161613b2077696474683a2035303270783b2220636f6c7370616e3d2232223e0d0a3c68323ec2a03c2f68323e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2277696474683a2032303570783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c7464207374796c653d2277696474683a2032393570783b223e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2077696474683a2035303270783b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 1),
(45, 'A new request for [business_name]', 'Request Quote', 'Request Quote', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e546865726520686173206265656e2061207265717565737420666f7220796f7572206c697374696e67205b627573696e6573735f6e616d655d2072656c617465642077697468207468652063617465676f7279205b63617465676f72795f6c696e6b5d2e2042656c6f77206973207468652073756d6d617279206f6620746865207265717565737420746f20776869636820796f752063616e207265706c79206279205b636c69636b5f686572655f6c696e6b5d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b726571756573745f71756f74655f73756d6d6172795dc2a03c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(46, 'User changed for listing [business_name]', 'Listing User Changed', 'Listing User Changed', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468616e6b20796f7520666f72206265696e672070617274206f66206f7572206469726563746f72792e3c6272202f3e3c6272202f3e546865206c697374696e67205b627573696e6573735f6e616d655d20686173206265656e207375636365737366756c6c79207472616e736665727265642066726f6d205b70726576696f75735f757365725d20746f205b61637475616c5f757365725d2e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(47, 'Listing [business_name] is ready to be claimed!', 'Listing Creation Notification to owner', 'New Company Notification Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a2031707820736f6c696420236666666666663b20636f6c6f723a20233434343434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20323570783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e20427573696e657373203c623e205b627573696e6573735f6e616d655d203c2f623e20776173206164646564206f6e206f7572206469726563746f72792e3c6272202f3e596f752063616e20636c61696d20746865206c697374696e6720627920636c69636b696e67206f6e2074686520636c61696d20627573696e65737320627574746f6e206f6e20746865206c6973742064657461696c7320706167652e3c2f6469763e0d0a3c6469763e42656c6f7720617265207468652064657461696c73206f6620746865206c697374696e673a3c6272202f3e3c6272202f3e0d0a3c7461626c65207374796c653d2270616464696e673a203570783b22206267636f6c6f723d2223464146394641223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a203070783b2070616464696e672d72696768743a20313070783b2220726f777370616e3d2235222076616c69676e3d226d6964646c65223e5b627573696e6573735f6c6f676f5d3c2f74643e0d0a3c74643e3c623e205b627573696e6573735f6e616d655d203c2f623e3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f616464726573735d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f63617465676f72795d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f776562736974655d3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c74643e5b627573696e6573735f636f6e746163745f706572736f6e5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c6272202f3e3c6272202f3e205468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(48, 'A new quote was added to the products', 'Product Quote', 'Request Quote Product Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2cc2a03c6272202f3e3c6272202f3e41206e65772071756f746520726571756573742077617320706f73746564206f6e205b6469726563746f72795f776562736974655d2e3c6272202f3e4e616d653ac2a03c623e5b66697273745f6e616d655d205b6c6173745f6e616d655d3c2f623e3c6272202f3e452d6d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e50617468207768657265207468652071756f746520686173206265656e20646f6e653a205b7265717565737465645f706174685d3c6272202f3e5b7265717565737465645f70726f647563745d3c6272202f3e3c6272202f3e3c623e5265717565737420636f6e74656e743c2f623e3c6272202f3e5b636f6e746163745f656d61696c5f636f6e74656e745d3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c70207374796c653d22666f6e742d73697a653a2031322e313670783b223ec2a03c2f703e, 1, 0),
(49, 'A new product has been added to your directory', 'Product Creation', 'Product Creation Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2cc2a03c6272202f3e3c6272202f3e41206e65772070726f647563743c7374726f6e673ec2a05b6f666665725f6e616d655dc2a03c2f7374726f6e673e686173206265656e206164646564206f6e20796f7572206469726563746f72792e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(50, 'There is a new registration request for the listing [business_name]', 'Company Joining Notification', 'Company Joining Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e54686572652069732061206e6577207265717565737420666f72206a6f696e696e6720796f7572206c697374696e67205b627573696e6573735f6e616d655d3c6272202f3e596f752063616e20617070726f7665206974206174205b6c696e6b5f627573696e6573735f6a6f696e5f636f6e74726f6c5f70616e656c5dc2a03c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f6469763e, 1, 1),
(51, 'New review admin notification', 'New review admin notification', 'New Review Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e41206e6577207265766965772077617320706f7374656420666f7220627573696e657373206c697374696e67205b627573696e6573735f6e616d655d2e3c6272202f3e596f752063616e206c6f6720696e20746f207468652061646d696e206172656120746f20617070726f76652f646973617070726f766520746865207265766965772e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223e3c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0);
INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(52, 'Quantity alert! Increase the quantity at [offer_name]', 'Offer low quantity notification', 'Offer Low Quantity Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c203c6272202f3e3c6272202f3e5175616e74697479206174206f66666572205b6f666665725f6e616d655d206973206c657373207468616e205b6e6f74696669636174696f6e5f7175616e746974795d2e3c6272202f3e596f752063616e20696e63726561736520697420696e7369646520796f7572206f666665722064657461696c732e3c2f6469763e0d0a5b73746f636b5f64657461696c735d0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 1),
(53, 'You have posted a quote request', 'Request Quote Confirmation', 'Quote Request Confirmation', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e596f75206861766520706f7374656420612071756f7465207265717565737420666f72205b63617465676f72795f6c696e6b5d2e2042656c6f77206973207468652073756d6d617279206f662074686520726571756573742e20596f752063616e207669657720746865207265706c696573205b636c69636b5f686572655f6c696e6b5d2e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b726571756573745f71756f74655f73756d6d6172795dc2a03c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2270616464696e672d746f703a20313070783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e0d0a3c646976207374796c653d2270616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(54, 'You have received a reply for your quote request', 'Request Quote Reply Notification', 'Quote Request Reply Notification', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e596f7520686176652072656365697665642061207265706c792066726f6d205b627573696e6573735f6e616d655d20746f207468652071756f7465207265717565737420666f72205b63617465676f72795f6c696e6b5d2e203c6272202f3e596f752063616e207669657720746865207265706c7920627920636c69636b696e67206f6e2074686520627574746f6e2062656c6f772e3c2f6469763e0d0a3c646976207374796c653d2277696474683a20313030253b20746578742d616c69676e3a2063656e7465723b223e0d0a3c646976207374796c653d226261636b67726f756e642d636f6c6f723a20233535613332613b20626f726465722d7261646975733a203270783b2077696474683a2031393070783b206d617267696e3a2030206175746f3b2220616c69676e3d2263656e746572223e3c61207374796c653d22666f6e742d73697a653a20313570783b20666f6e742d7765696768743a203330303b20666f6e742d66616d696c793a202748656c766574696361204e657565272c48656c7665746963612c73616e732d73657269663b20636f6c6f723a20236666666666663b20746578742d6465636f726174696f6e3a206e6f6e653b20626f726465722d7261646975733a203370783b2070616464696e673a203132707820313870783b20626f726465723a2031707820736f6c696420233535613332613b20646973706c61793a20696e6c696e652d626c6f636b3b2220687265663d225b636c69636b5f686572655f6c696e6b5d22207461726765743d225f626c616e6b222072656c3d226e6f6f70656e6572206e6f7265666572726572223e56696577207265706c793c2f613e3c2f6469763e0d0a3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c7464207374796c653d2270616464696e672d746f703a20313070783b223e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c2f74723e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e0d0a3c646976207374796c653d2270616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(55, 'Meeting url for the for the appointment for [service_name] on [appointment_date]', 'Appointment URL notification', 'Appointment URL Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e546865206d656574696e672055524c20666f7220746865206170706f696e746d656e74206f6e205b6170706f696e746d656e745f646174655d20666f72c2a05b736572766963655f6e616d655d206973205b6170706f696e746d656e745f75726c5d2e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e54686573652061726520746865206170706f696e746d656e742064657461696c733a3c6272202f3e4e616d653a205b757365725f6e616d655d3c6272202f3e50686f6e653a205b70686f6e655d3c6272202f3e446174652026616d703b2054696d653a205b6170706f696e746d656e745f646174655d3c6272202f3e50726f76696465723a205b70726f76696465725f6e616d655dc2a03c6272202f3e536572766963653a205b736572766963655f6e616d655dc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 0),
(56, 'Please verify your email address', 'Email verification', 'Email verification', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223ec2a03c2f7370616e3e3c2f7370616e3e3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223e5b636f6d70616e795f6c6f676f5d3c2f7370616e3e3c2f7370616e3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223e446561725b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e3c2f7370616e3e3c2f7370616e3e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223e506c6561736520636c69636b205b656d61696c5f766572696669636174696f6e5f6c696e6b5f746578745dc2a0746f20636f6e6669726d20796f757220656d61696c2061646472657373c2a06f7220636f7079207061737465207468652055524c2062656c6f772e3c6272202f3e3c6272202f3e5b656d61696c5f766572696669636174696f6e5f6c696e6b5dc2a03c6272202f3e3c6272202f3e5468616e6b20796f752c3c2f7370616e3e3c2f7370616e3e0d0a3c6469763e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223e5b636f6d70616e795f6e616d655d205465616d3c2f7370616e3e3c2f7370616e3e3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223e5b6469726563746f72795f776562736974655d3c2f7370616e3e3c2f7370616e3e3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f7370616e3e3c2f7370616e3e3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223e3c7370616e207374796c653d22636f6c6f723a20233333333333333b20666f6e742d66616d696c793a205461686f6d612c2048656c7665746963612c20417269616c2c2073616e732d73657269663b223e3c7370616e207374796c653d22666f6e742d73697a653a2031322e313670783b223ec2a03c6272202f3e3c6272202f3e3c2f7370616e3e3c2f7370616e3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(57, 'New message for your [service_name] booking.', 'Appointment Notification Message', 'Appointment Email Notification', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e596f7520686176652061206e6577206d65737361676520666f7220796f7572206170706f696e746d656e742e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e54686573652061726520746865206170706f696e746d656e742064657461696c733a3c6272202f3e3c7374726f6e673e4e616d653c2f7374726f6e673e3a205b757365725f6e616d655d3c6272202f3e3c7374726f6e673e50686f6e653c2f7374726f6e673e3a205b70686f6e655d3c6272202f3e3c7374726f6e673e446174652026616d703b2054696d653c2f7374726f6e673e3a205b6170706f696e746d656e745f646174655d3c6272202f3e3c7374726f6e673e50726f76696465723c2f7374726f6e673e3a205b70726f76696465725f6e616d655dc2a03c6272202f3e3c7374726f6e673e536572766963653c2f7374726f6e673e3a205b736572766963655f6e616d655dc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c7374726f6e673e4d6573736167653c2f7374726f6e673e3a3c6272202f3e5b6170706f696e746d656e745f6d6573736167655d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 0),
(58, 'Directory test email.', 'Test Email', 'Test Email', 0x3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e54686973206973207465737420656d61696c2ec2a03c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(59, 'Your business listing has been hired.', 'Hire Email', 'Hire Email', 0x3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c6272202f3e3c6272202f3e596f757220627573696e657373206c697374696e672077697468206e616d65c2a03c7374726f6e673e5b627573696e6573735f6e616d655d3c2f7374726f6e673ec2a0686173206265656e206869726564206279205b71756f74655f757365725f6e616d655d2e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b726571756573745f71756f74655f73756d6d6172795dc2a03c6272202f3e466f72206675727468657220696e666f726d6174696f6e2c20796f752063616e20636f6e7461637420627920656d61696c3ac2a05b636f6e746163745f656d61696c5d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468616e6b20796f752c3c6272202f3e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(60, 'Your booking reminder for the following service: [service_booking_name]', 'Service Booking Reminder', 'Service Booking Reminder', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b757365725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468697320697320612072656d696e64657220666f722074686520666f6c6c6f77696e6720626f6f6b696e6720796f752068617665206d616465c2a06f6ec2a05b736572766963655f626f6f6b696e675f646174655dc2a020666f72205b736572766963655f626f6f6b696e675f6e616d655d202e3c6272202f3e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b736572766963655f626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223ec2a03c2f6469763e0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(61, 'Appointment reminder for the following event : [event_name]', 'Event Appointment Reminder', 'Event Appointment Reminder', 0x3c7461626c65207374796c653d22666f6e742d66616d696c793a2048656c7665746963612c20417269616c2c2073616e732d73657269663b20666f6e742d73697a653a20313270783b206261636b67726f756e642d636f6c6f723a20236634663366343b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2cc2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468697320697320612072656d696e64657220666f7220746865206170706f696e746d656e7420796f752068617665206d61646520726567617264696e67205b6576656e745f6e616d655dc2a0666f72c2a05b6170706f696e746d656e745f646174655d2e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 0);
INSERT INTO `{$wpdb->prefix}jbusinessdirectory_emails` (`email_id`, `email_subject`, `email_name`, `email_type`, `email_content`, `status`, `send_to_admin`) VALUES
(62, 'Your booking reminder for the following event : [event_name]', 'Event Booking Reminder', 'Event Booking Reminder', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b757365725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e3c6272202f3e5468697320697320612072656d696e64657220666f722074686520666f6c6c6f77696e6720626f6f6b696e6720796f752068617665206d616465206f6ec2a05b6576656e745f626f6f6b696e675f646174655d2e3c6272202f3e0d0a3c6469763ec2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5b626f6f6b696e675f64657461696c735d3c2f6469763e0d0a3c646976207374796c653d22626f726465722d626f74746f6d3a2031707820736f6c696420236161613b223e0d0a3c703ec2a03c2f703e0d0a3c2f6469763e0d0a3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(63, 'Your recent order is pending payment!', 'Payment reminder', 'Payment Reminder', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20236666666666663b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e0d0a3c703e48656c6c6f205b637573746f6d65725f6e616d655d2c3c6272202f3e3c6272202f3e596f7572207061796d656e7420666f7220796f7572206f6e6c696e65206f7264657220706c61636564206f6e3c6272202f3e5b736974655f616464726573735d2069732070656e64696e672e3c6272202f3e3c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e4f4e4c494e45204f524445522044455441494c533c6272202f3e2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a3c6272202f3e3c6272202f3e576562736974653a205b736974655f616464726573735d3c6272202f3e4f72646572207265666572656e6365203a205b6f726465725f69645d3c6272202f3e496e766f696365204e756d6265723a205b696e766f6963655f6e756d6265725d3c6272202f3e4f726465722047656e6572616c20546f74616c3a205b746f74616c5f70726963655d3c2f703e0d0a3c703e596f752063616e206d616b6520746865207061796d656e7420627920636c69636b696e6720746865206c696e6b2062656c6f773a3c6272202f3e5b6f726465725f75726c5d3c6272202f3e3c6272202f3e5468616e6b20796f752c3c2f703e0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0),
(64, 'New subscription for upcoming [form_type]', 'Subscription Email', 'Subscription Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f2c3c6272202f3e3c6272202f3e5b637573746f6d65725f6e616d655d20686173207375627363726962656420746f2072656365697665206e6f74696669636174696f6e7320666f7220746865207570636f6d696e67205b666f726d5f747970655d20696e20746865206469726563746f72792e3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e537562736372696265722044657461696c733a3c6272202f3e4e616d653ac2a05b637573746f6d65725f6e616d655d3c6272202f3e50686f6e653a205b70686f6e655d3c6272202f3e456d61696c3a205b636f6e746163745f656d61696c5d3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c703ec2a03c2f703e, 1, 0),
(65, 'You have been invited to edit [business_name]', 'Listing editor invitation', 'Listing Editor Invitation', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c3c6272202f3e3c6272202f3e596f752068617665206265656e20696e766974656420746f2065646974203c7374726f6e673e5b627573696e6573735f6e616d655d3c2f7374726f6e673e2e3c6272202f3e3c6272202f3e596f752063616e206163636570742074686520696e7669746174696f6e20627920636c69636b696e67206f6e20746865206c696e6b2062656c6f772e3c6272202f3e5b6c696e6b5d3c6272202f3e3c6272202f3e4265737420726567617264732c3c6272202f3e0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 0),
(66, 'General message', 'General Message', 'General Message', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c3c6272202f3e3c6272202f3e5b6d6573736167655d3c6272202f3e3c6272202f3e4265737420726567617264732c3c6272202f3e0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c6272202f3e3c6272202f3e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c703ec2a03c2f703e, 1, 0),
(67, 'Request More Info for [business_name]', 'Request more info', 'Request Info Email', 0x3c646976207374796c653d226d617267696e3a203070783b206261636b67726f756e642d636f6c6f723a20236634663366343b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20666f6e742d73697a653a20313270783b223e0d0a3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f5b757365725f6e616d655d2c3c6272202f3e5468616e6b20796f7520666f7220746865207265717565737420666f72206d6f726520696e666f726d6174696f6e206f6e205b627573696e6573735f6e616d655d2e3c6272202f3e3c6272202f3e4f7572207465616d2077696c6c20636f6f7264696e61746520746f2068617665205b627573696e6573735f6e616d655d207265706c7920746f20796f7520617320736f6f6e20617320706f737369626c6520746f20616e7377657220796f757220726571756573742e203c6272202f3e3c6272202f3e506c656173652074616b652061206d6f6d656e742c20696620796f7520686176656ee280997420616c72656164792c20616e64207369676e20757020746f207265636569766520746865207570646174657320616e64207374617920696e666f726d6564206f6e20746865206c617465737420736f6c7574696f6e7320616e642073657276696365732e3c6272202f3e3c6272202f3e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e, 1, 0),
(68, 'The service booking nr. [service_booking_id] has been [service_booking_status]', 'Service Booking Status Update Notification', 'Service Booking Status Update Notification', 0x3c7461626c6520626f726465723d2230222077696474683d2231303025222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223463446334634223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d2270616464696e673a20313570783b223e3c63656e7465723e0d0a3c7461626c652077696474683d22353730222063656c6c73706163696e673d2230222063656c6c70616464696e673d22302220616c69676e3d2263656e74657222206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c746420616c69676e3d226c656674223e0d0a3c646976207374796c653d22626f726465723a20736f6c69642031707820236439643964393b206261636b67726f756e642d636f6c6f723a20236666666666663b223e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a2048656c7665746963612c417269616c2c73616e732d73657269663b20626f726465723a20736f6c69642031707820236666666666663b20636f6c6f723a20233434343b2077696474683a20313030253b2220626f726465723d2230222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d2232222076616c69676e3d22626f74746f6d22206865696768743d223330223ec2a03c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d226c696e652d6865696768743a20333270783b2070616464696e672d6c6566743a20333070783b222076616c69676e3d22626173656c696e65223e5b636f6d70616e795f6c6f676f5d3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226d617267696e2d746f703a20313570783b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b20636f6c6f723a20233434343b206c696e652d6865696768743a20312e363b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c74723e0d0a3c7464207374796c653d22626f726465722d746f703a20736f6c69642031707820236439643964393b20626f726465722d626f74746f6d3a20736f6c69642031707820236439643964393b2220636f6c7370616e3d2232223e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e48656c6c6f205b637573746f6d65725f6e616d655d2c3c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468652073657276696365207265736572766174696f6e20796f752068617665206d616465206f6e205b736572766963655f626f6f6b696e675f646174655d20666f72205b736572766963655f626f6f6b696e675f6e616d655d20686173206265656e205b736572766963655f626f6f6b696e675f7374617475735d2e3c6272202f3e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223ec2a03c2f6469763e0d0a3c646976207374796c653d2270616464696e673a203135707820303b223e5468616e6b20796f752c0d0a3c6469763e5b636f6d70616e795f6e616d655d205465616d3c2f6469763e0d0a3c2f6469763e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c7461626c65207374796c653d226c696e652d6865696768743a20312e353b20666f6e742d73697a653a20313270783b20666f6e742d66616d696c793a20417269616c2c73616e732d73657269663b206d617267696e2d72696768743a20333070783b206d617267696e2d6c6566743a20333070783b2220626f726465723d2230222077696474683d22353130222063656c6c73706163696e673d2230222063656c6c70616464696e673d223022206267636f6c6f723d2223666666666666223e0d0a3c74626f64793e0d0a3c7472207374796c653d22666f6e742d73697a653a20313170783b20636f6c6f723a20233939393939393b222076616c69676e3d226d6964646c65223e0d0a3c74643e5b6469726563746f72795f776562736974655d3c2f74643e0d0a3c74643e0d0a3c646976207374796c653d22666c6f61743a2072696768743b2070616464696e672d746f703a20313070783b223e5b636f6d70616e795f736f6369616c5f6e6574776f726b735d3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c74723e0d0a3c7464207374796c653d22636f6c6f723a20236666666666663b2220636f6c7370616e3d223222206865696768743d223135223ec2a03c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f6469763e0d0a3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f63656e7465723e3c2f74643e0d0a3c2f74723e0d0a3c2f74626f64793e0d0a3c2f7461626c653e, 1, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_event_videos` (`id`, `eventId`, `url`) VALUES
(56, 12, 'https://www.youtube.com/watch?v=u9prcUCHlqM'),
(55, 12, 'https://www.youtube.com/watch?v=nAvIGmiFilM');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_mobile_app_config` (`id`, `name`, `value`) VALUES
(3, 'app_id', '1'),
(4, 'last_updated', '2023-04-10 11:30:36'),
(5, 'primaryColor', 'ff7142'),
(6, 'backgroundColor', 'fffffff'),
(7, 'textPrimary', '5b5b5b'),
(8, 'genericText', '212E3E'),
(9, 'iconColor', 'f97e5b'),
(10, 'isLocationMandatory', '0'),
(11, 'showLatestListings', '1'),
(12, 'showFeaturedListings', '1'),
(13, 'showFeaturedOffers', '1'),
(14, 'showFeaturedEvents', '1'),
(15, 'showOffers', '1'),
(16, 'enableReviews', '1'),
(17, 'isJoomla', '1'),
(18, 'baseUrl', ''),
(19, 'mapsApiKey', ''),
(20, 'limit', ''),
(21, 'mapType', 'google'),
(22, 'language_keys', ''),
(23, 'language_values', ''),
(24, 'androidOrderId', ''),
(25, 'iosOrderId', ''),
(26, 'androidOrderEmail', ''),
(27, 'mobile_business_img', ''),
(28, 'mobile_offer_img', ''),
(29, 'mobile_event_img', ''),
(30, 'mobile_only_featured_listings', '0'),
(31, 'mobile_only_featured_offers', '0'),
(32, 'mobile_only_featured_events', '0'),
(33, 'mobile_company_categories_filter', ''),
(34, 'mobile_offer_categories_filter', ''),
(35, 'mobile_event_categories_filter', ''),
(36, 'mobile_list_limit', '5'),
(37, 'firebase_server_key', ''),
(38, 'user_email', ''),
(40, 'slide1', ''),
(41, 'slide2', ''),
(42, 'slide3', ''),
(43, 'home_header', ''),
(44, 'featured_placeholder', ''),
(45, 'logo_ios', ''),
(46, 'logo_ios_nb', ''),
(47, 'google-plist', ''),
(48, 'mobileprovisioning', ''),
(49, 'certificate', ''),
(50, 'client_configs', ''),
(51, 'google-services', ''),
(52, 'logo_android', ''),
(53, 'logo_android_nb', ''),
(54, 'iosOrderEmail', '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_offer_videos` (`id`, `offerId`, `url`) VALUES
(78, 22, 'https://youtu.be/oO7Bh3q-Xm4'),
(80, 23, 'https://youtu.be/oO7Bh3q-Xm4'),
(79, 23, 'https://youtu.be/jAk_xhs0Rcw'),
(77, 22, 'https://youtu.be/jAk_xhs0Rcw');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_orders` (`id`, `order_id`, `company_id`, `package_id`, `initial_amount`, `amount`, `amount_paid`, `created`, `paid_at`, `state`, `transaction_id`, `user_name`, `service`, `description`, `start_date`, `end_date`, `start_trial_date`, `end_trial_date`, `trial_initial_amount`, `type`, `currency`, `expiration_email_date`, `discount_code`, `discount_amount`, `vat`, `vat_amount`, `observation`, `trial_amount`, `trial_days`, `subscription_id`, `user_id`, `notify_payment`, `expiration_processed`) VALUES
(1, 'Upgrade-Package: Premium Package', 8, 4, NULL, '99.99', NULL, '2014-09-26 01:46:35', '2014-09-26 00:00:00', 1, '', NULL, 'It Company', 'Upgrade-Package: Premium Package', '2017-02-03', '2018-02-03', NULL, NULL, NULL, 1, 'USD', NULL, NULL, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(2, 'Upgrade-Package: Premium Package', 1, 4, NULL, '99.99', NULL, '2014-09-26 01:46:47', '2014-09-26 00:00:00', 1, '', NULL, 'Wedding company', 'Upgrade-Package: Premium Package', '2017-02-03', '2018-02-03', NULL, NULL, NULL, 1, 'USD', NULL, NULL, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(3, 'Upgrade-Package: Gold Package', 12, 3, NULL, '59.99', NULL, '2014-09-26 01:46:57', '2014-09-26 00:00:00', 1, '', NULL, 'Better Health', 'Upgrade-Package: Gold Package', '2017-02-03', '2017-08-03', NULL, NULL, NULL, 1, 'USD', NULL, NULL, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(4, 'Upgrade-Package: Silver Package', 9, 1, NULL, '49.99', NULL, '2014-09-26 02:17:51', '2014-09-26 00:00:00', 1, '', NULL, 'Coffe delights', 'Upgrade-Package: Silver Package', '2017-02-03', '2017-04-14', NULL, NULL, NULL, 1, 'USD', NULL, NULL, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_packages` (`id`, `name`, `description`, `show_features`, `show_buttons`, `price_description`, `expiration_type`, `price`, `trial_price`, `trial_days`, `trial_period_unit`, `trial_period_amount`, `recurrence_count`, `special_price`, `special_from_date`, `special_to_date`, `days`, `status`, `ordering`, `time_unit`, `time_amount`, `show_price_per_month`, `max_pictures`, `max_videos`, `max_attachments`, `max_categories`, `popular`, `max_offers`, `offer_count_type`, `max_events`, `only_for_admin`, `package_usergroup`, `bg_color`, `text_color`, `border_color`, `show_disable_features`, `max_quote_replies`, `renewal_price`, `max_zipcodes`, `package_type`, `max_trips`, `max_locations`, `max_description_length`, `max_sounds`, `max_projects`, `max_activity_cities`, `max_activity_regions`, `max_activity_countries`) VALUES
(1, 'Silver Package', 'Silver Package', 1, 1, NULL, NULL, '49.99', NULL, 0, 'D', NULL, NULL, '12.00', '1970-01-01', '1970-01-01', 70, 1, 2, 'W', 10, 0, 15, 5, 5, 10, 0, 0, 0, 0, 0, '1', NULL, NULL, NULL, 1, 5, NULL, 0, 1, 0, 0, 1200, 5, 5, 0, 0, 0),
(2, 'Basic', 'Basic Package', 1, 1, NULL, NULL, '0.00', NULL, 0, 'D', NULL, NULL, '12.00', '1970-01-01', '1970-01-01', 0, 1, 1, 'D', 0, 0, 15, 5, 5, 10, 0, 0, 0, 0, 0, '1', NULL, NULL, NULL, 1, 5, NULL, 0, 1, 0, 0, 1200, 5, 5, 0, 0, 0),
(3, 'Gold Package', 'Gold Package', 1, 1, NULL, NULL, '59.99', NULL, 0, 'D', NULL, NULL, '0.00', '1970-01-01', '1970-01-01', 180, 1, 3, 'M', 6, 0, 15, 5, 5, 10, 0, 0, 0, 0, 0, '1', NULL, NULL, NULL, 1, 5, NULL, 0, 1, 0, 0, 1200, 5, 5, 0, 0, 0),
(4, 'Premium Package', 'Premium Package', 1, 1, NULL, NULL, '99.99', NULL, 0, 'D', NULL, NULL, '0.00', '1970-01-01', '1970-01-01', 365, 1, 4, 'Y', 1, 0, 15, 5, 5, 10, 0, 0, 0, 0, 0, '1', NULL, NULL, NULL, 1, 5, NULL, 0, 1, 0, 0, 1200, 5, 5, 0, 0, 0);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_package_fields` (`id`, `package_id`, `feature`) VALUES
(142, 1, 'image_upload'),
(141, 1, 'description'),
(122, 3, 'website_address'),
(121, 3, 'company_logo'),
(120, 3, 'description'),
(138, 4, 'company_offers'),
(137, 4, 'contact_form'),
(136, 4, 'google_map'),
(135, 4, 'videos'),
(134, 4, 'image_upload'),
(133, 4, 'website_address'),
(132, 4, 'company_logo'),
(131, 4, 'featured_companies'),
(130, 4, 'description'),
(123, 3, 'image_upload'),
(124, 3, 'videos'),
(125, 3, 'google_map'),
(126, 3, 'contact_form'),
(127, 3, 'company_offers'),
(128, 3, 'company_events'),
(129, 3, 'social_networks'),
(139, 4, 'company_events'),
(140, 4, 'social_networks'),
(143, 1, 'website_address'),
(144, 1, 'videos'),
(145, 1, 'contact_form'),
(146, 1, 'google_map'),
(147, 4, 'phone'),
(148, 4, 'attachments'),
(149, 4, 'custom_tab'),
(150, 4, 'html_description');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_payment_processors` (`id`, `name`, `type`, `mode`, `timeout`, `status`, `ordering`, `displayfront`, `company_id`) VALUES
(1, 'Paypal', 'Paypal', 'test', NULL, 1, 1, 1, -1),
(2, 'Bank Transfer', 'wiretransfer', 'live', 0, 1, 2, 1, -1),
(3, 'Cash', 'cash', 'live', 0, 1, 3, 0, -1),
(8, 'Authorize', 'authorize', 'test', 10, 1, 4, 1, -1),
(10, 'PayFast', 'payfast', 'test', 10, 1, 5, 1, -1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_payment_processor_fields` (`id`, `column_name`, `column_value`, `processor_id`, `column_mode`) VALUES
(17, 'paypal_email', '', 1, 1),
(88, 'bank_name', 'Bank Name', 2, 1),
(86, 'bank_city', 'City', 2, 1),
(87, 'bank_address', 'Address', 2, 1),
(85, 'bank_country', 'Country', 2, 1),
(84, 'swift_code', 'SW1321', 2, 1),
(83, 'iban', 'BR213 123 123 123', 2, 1),
(82, 'bank_account_number', '123123123123 ', 2, 1),
(81, 'bank_holder_name', 'Account holder name', 2, 1),
(89, 'secretKey', '', 4, 1),
(90, 'merchantId', '', 4, 1),
(100, 'merchantId', '', 6, 1),
(98, 'preSharedKey', '', 6, 1),
(99, 'password', '1M75C4R8', 6, 1),
(116, 'user_name', '', 7, 1),
(115, 'customer_id', '87654321', 7, 1),
(120, 'transaction_key', '9eD5LC7e6h68jFxY', 8, 1),
(119, 'api_login_id', '2bd3DEG6JZ', 8, 1),
(123, 'account_number', '901265403', 9, 1),
(124, 'secret_word', 'tango', 9, 1),
(125, 'merchant_id', '10001965', 10, 1),
(126, 'merchant_key', 'hz7almlp6ma90', 10, 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_reports` (`id`, `name`, `description`, `selected_params`, `custom_params`, `type`, `listing_status`, `start_date`, `end_date`, `created`) VALUES
(1, 'Simple Report', 'Simple Report', 'name,short_description,website,email,viewCount,contactCount,websiteCount', NULL, NULL, 1, NULL, NULL, '2023-04-10 08:30:36');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_request_quote_questions` (`id`, `category_id`, `name`, `type`, `status`, `ordering`, `image`) VALUES
(1, 13, 'Camera type', 2, 1, 1, '/requestquotequestions/1/photo1-1577716977.jpg'),
(2, 13, 'Photography type', 2, 1, 2, ''),
(3, 13, 'Which lesson format(s) would you consider?', 1, 1, 3, ''),
(4, 13, 'Which additional skills would you like to learn?', 1, 1, 4, '');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_request_quote_question_options` (`id`, `question_id`, `answer`, `status`) VALUES
(30, 1, 'Large format', 1),
(29, 1, 'Digital', 1),
(28, 1, 'I\'m not sure yet', 1),
(9, 2, 'Color', 1),
(10, 2, 'Black & white', 1),
(11, 2, 'Not sure yet', 1),
(12, 3, 'Private 1:1 lessons', 1),
(13, 3, 'Classes in a small group', 1),
(14, 3, 'Group lessons', 1),
(15, 3, 'Other', 1),
(22, 4, 'Film developing', 1),
(21, 4, 'Printing/enlargement', 1),
(20, 4, 'Post-processing (digital)', 1),
(23, 4, 'None of this', 1),
(31, 1, '35mm', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_review_pictures` (`id`, `reviewId`, `picture_info`, `picture_path`, `picture_enable`) VALUES
(17, 11, '', '/reviews/11/image2.jpg', 1),
(18, 11, '', '/reviews/11/image1.jpg', 1),
(19, 11, '', '/reviews/11/image7.jpg', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_search_logs` (`id`, `item_type`, `object_type`, `date`, `value`, `has_text`) VALUES
(1, 12, 3, '2021-05-10', '2021-04-25', 1),
(2, 13, 3, '2021-05-10', '2021-06-06', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_statistics` (`id`, `item_id`, `item_type`, `date`, `type`, `article_id`) VALUES
(54, 8, 1, '2021-05-10', 0, NULL),
(53, 8, 1, '2021-05-10', 0, NULL),
(52, 8, 1, '2021-05-10', 0, NULL),
(51, 8, 1, '2021-05-10', 0, NULL),
(50, 22, 2, '2021-05-10', 0, NULL),
(49, 31, 1, '2021-05-10', 0, NULL),
(48, 23, 2, '2021-05-10', 0, NULL),
(47, 22, 2, '2021-05-10', 0, NULL),
(46, 14, 2, '2021-05-10', 0, NULL),
(45, 22, 2, '2021-05-10', 0, NULL),
(44, 23, 2, '2021-05-10', 0, NULL),
(43, 8, 1, '2021-05-10', 0, NULL),
(42, 8, 1, '2021-05-10', 0, NULL),
(41, 8, 1, '2021-05-10', 0, NULL),
(40, 23, 2, '2021-05-10', 0, NULL),
(39, 23, 2, '2021-05-10', 0, NULL),
(38, 8, 1, '2021-05-10', 0, NULL),
(37, 8, 1, '2021-05-10', 0, NULL),
(36, 8, 1, '2021-05-10', 0, NULL),
(35, 8, 1, '2021-05-10', 0, NULL),
(34, 8, 1, '2021-05-10', 0, NULL),
(33, 17, 3, '2021-05-10', 0, NULL),
(32, 33, 1, '2021-05-10', 0, NULL),
(31, 22, 2, '2021-05-10', 0, NULL),
(30, 18, 2, '2021-05-10', 0, NULL),
(29, 18, 2, '2021-05-10', 0, NULL),
(28, 21, 2, '2021-05-10', 0, NULL),
(55, 8, 1, '2021-05-10', 0, NULL),
(56, 8, 1, '2021-05-10', 0, NULL),
(57, 8, 1, '2021-05-10', 0, NULL),
(58, 8, 1, '2021-05-10', 0, NULL),
(59, 8, 1, '2021-05-10', 0, NULL),
(60, 8, 1, '2021-05-10', 0, NULL),
(61, 8, 1, '2021-05-10', 0, NULL),
(62, 8, 1, '2021-05-10', 0, NULL),
(63, 8, 1, '2021-05-10', 0, NULL),
(64, 31, 1, '2021-05-10', 0, NULL),
(65, 31, 1, '2021-05-10', 0, NULL),
(66, 31, 1, '2021-05-10', 0, NULL),
(67, 31, 1, '2021-05-10', 0, NULL),
(68, 31, 1, '2021-05-10', 0, NULL),
(69, 31, 1, '2021-05-10', 0, NULL),
(70, 31, 1, '2021-05-10', 0, NULL),
(71, 31, 1, '2021-05-10', 0, NULL),
(72, 31, 1, '2021-05-10', 0, NULL),
(73, 31, 1, '2021-05-10', 0, NULL),
(74, 31, 1, '2021-05-10', 0, NULL),
(75, 31, 1, '2021-05-10', 0, NULL),
(76, 31, 1, '2021-05-10', 0, NULL),
(77, 8, 1, '2021-05-10', 0, NULL),
(78, 31, 1, '2021-05-10', 0, NULL),
(79, 8, 1, '2021-05-10', 0, NULL),
(80, 8, 1, '2021-05-10', 0, NULL),
(81, 8, 1, '2021-05-10', 0, NULL),
(82, 8, 1, '2021-05-10', 0, NULL),
(83, 8, 1, '2021-05-10', 0, NULL),
(84, 8, 1, '2021-05-10', 0, NULL),
(85, 8, 1, '2021-05-10', 0, NULL),
(86, 31, 1, '2021-05-10', 0, NULL),
(87, 8, 1, '2021-05-10', 0, NULL),
(88, 8, 1, '2021-05-10', 0, NULL),
(89, 8, 1, '2021-05-10', 0, NULL),
(90, 8, 1, '2021-05-10', 0, NULL),
(91, 8, 1, '2021-05-10', 0, NULL),
(92, 8, 1, '2021-05-10', 0, NULL),
(93, 8, 1, '2021-05-10', 0, NULL),
(94, 8, 1, '2021-05-10', 0, NULL),
(95, 8, 1, '2021-05-10', 0, NULL),
(96, 8, 1, '2021-05-10', 0, NULL),
(97, 8, 1, '2021-05-10', 0, NULL),
(98, 15, 3, '2021-05-10', 0, NULL),
(99, 15, 3, '2021-05-10', 0, NULL),
(100, 15, 3, '2021-05-10', 0, NULL),
(101, 15, 3, '2021-05-10', 0, NULL),
(102, 8, 1, '2023-04-10', 0, NULL),
(103, 8, 1, '2023-04-10', 0, NULL),
(104, 8, 1, '2023-04-10', 0, NULL);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_statistics_archive` (`id`, `item_id`, `item_type`, `date`, `type`, `article_id`, `item_count`) VALUES
(1, 7, 1, '2021-05-07', 0, 0, 3),
(2, 32, 1, '2021-05-07', 0, 0, 5),
(3, 30, 1, '2021-05-07', 0, 0, 3),
(4, 9, 1, '2021-05-07', 0, 0, 1),
(5, 8, 1, '2021-05-07', 0, 0, 3),
(6, 5, 1, '2021-05-07', 0, 0, 2),
(7, 31, 1, '2021-05-07', 0, 0, 1),
(8, 21, 2, '2021-05-08', 0, 0, 1),
(9, 33, 1, '2021-05-08', 0, 0, 1),
(10, 13, 2, '2021-05-08', 0, 0, 1),
(11, 10, 3, '2021-05-08', 0, 0, 1),
(12, 13, 3, '2021-05-08', 0, 0, 1),
(13, 11, 3, '2021-05-08', 0, 0, 1),
(14, 12, 3, '2021-05-08', 0, 0, 1),
(15, 12, 3, '2021-05-09', 0, 0, 1),
(16, 11, 3, '2021-05-09', 0, 0, 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_trips` (`id`, `name`, `alias`, `description`, `start_date`, `end_date`, `start_time`, `end_time`, `frequency`, `recurring_info`, `capacity`, `organizer`, `phone`, `email`, `approved`) VALUES
(8, 'Phuket Thailand', 'phuket-thailand', 'Phuket province is located in southern Thailand. It is the biggest Island of Thailand and sits on the Andaman sea. The nearest province to the north is Phang-nga and the nearest provinces to the east are Phang-nga and Krabi.\r\n\r\nPhuket has a large Chinese influence, so you will see many Chinese shrines and Chinese Restaurants around the city. A Chinese Vegetarian Festival is held there every year. While the Chinese community is quite big, there are many other ethnicities bringing all their traditions and festivals from all over the world to Phuket.\r\n\r\nBeing a big island, Phuket is surrounded by many magnificent Beaches such as Rawai, Patong, Karon, Kamala, Kata Yai, Kata Noi, and Mai Khao. Laem Phromthep viewpoint is said to feature the most beautiful sunsets in Thailand.', '2022-08-31', '2023-05-05', '07:00:00', '20:00:00', 3, '{\"frequency\":\"Monthly\",\"interval\":\"1\",\"start_date\":\"2022-08-31\",\"end_date\":\"05-05-2023\",\"repeatby\":\"day of the month\"}', 15, 'Anne Teak', '+5558412365', 'anneteak@email.com', 1),
(7, 'Disneyland Paris', 'disneyland-paris', 'From the Old West to galaxies far far away, you\'ll be transported to the enchanted lands of the stories that you know and love from Disney, Pixar, Star Warsᵀᴹ and MARVEL!\r\n Although this Disney theme park, originally named Euro-Disney, was met with protest following its opening in 1992, Disneyland Paris is now frequented by large crowds drawn by technologically advanced rides and attractions.', '2023-01-19', '2023-07-27', '07:30:00', '18:30:00', 1, '{\"frequency\":\"Daily\",\"interval\":\"1\",\"start_date\":\"2023-01-19\",\"end_date\":\"27-07-2023\"}', 20, 'Deen End', '+695357455', 'deenend@email.com', 1),
(6, 'Luxor,Egypt ', 'pyramids-temples-and-the-nile-the-ultimate-egypt-experience', 'Step back in time on this epic 10-day voyage of discovery through Egypt. After the unforgettable experience of exploring the Sphinx and Great Pyramid of Giza and browsing the stalls of the Khan Al-Khalili bazaar in the historic centre of Cairo, continue your journey south, first by sleeper train and then by luxury cruise ship on the Nile. Marvel at ancient temples, kayak on the river and sip cocktails on a felucca – a traditional Egyptian sailboat. Wrap up your trip in Aswan, where you’ll enjoy an authentic Nubian dinner on your final evening.', '2022-05-05', '2022-07-05', '06:00:00', '21:00:00', 3, '{\"frequency\":\"Monthly\",\"interval\":\"1\",\"start_date\":\"2022-05-05\",\"occurrences\":\"10\",\"repeatby\":\"day of the month\"}', 30, 'Amanda Hug', '+847152369', 'amandahug@email.com', 1),
(9, 'Cappadocia,Turkey', 'cappadocia-turkey', 'Cappadocia is a beautiful region in central Turkey famous for its fairytale scenery, cave dwellings, remarkable rock formations and, of course, the hundreds of hot air balloons that soar in the sky during sunrise each morning. There is also so much awesome hiking in Cappadocia, that it will take you at least a week to explore all of the diverse hiking trails in the region.', '2022-09-13', '2023-05-11', '06:30:00', '21:00:00', 2, '{\"frequency\":\"Weekly\",\"interval\":\"1\",\"start_date\":\"2022-09-13\",\"end_date\":\"11-05-2023\",\"week_days\":[\"Tuesday\"]}', 50, 'John Doe', '+5558412365', 'johndoe@email.com', 1),
(10, 'Paraty,Brasil', 'paraty-brasil', 'Named for a local swamp fish, Paraty sits on Brazil\'s southeastern coast, 125 miles south of Rio, with the Bocaino Mountains at its back. The small colonial town\'s center is a national historic monument with well-preserved buildings on its pedestrian-only streets. Take a boat trip out into the bay to the flotillas of islands and coves nearby. Explore sugarcane plantations and hike or take a train through Atlantica Forest. Keep an eye out for the monkeys that roam the cobblestone streets.', '2022-08-25', '2023-05-10', '07:00:00', '16:00:00', 2, '{\"frequency\":\"Weekly\",\"interval\":\"1\",\"start_date\":\"2022-08-25\",\"end_date\":\"10-04-2023\",\"week_days\":[\"Monday\"]}', 60, 'Mark Ateer', '+69878451552', 'markateer@email.com', 1),
(11, 'Santorini,Greece', 'santorini-greece', 'Santorini is one of the Cyclades islands in the Aegean Sea. It was devastated by a volcanic eruption in the 16th century BC, forever shaping its rugged landscape. The whitewashed, cubiform houses of its 2 principal towns, Fira and Oia, cling to cliffs above an underwater caldera (crater). ', '2022-09-01', '2023-09-30', '06:30:00', '16:30:00', 1, '{\"frequency\":\"Daily\",\"interval\":\"1\",\"start_date\":\"2022-09-01\",\"end_date\":\"30-09-2023\"}', 100, 'Barry Kade', '+659874521', 'barrykade@email.com', 1),
(12, 'Split,Croatia', 'split-croatia', 'Split is the second largest city of Croatia, the largest city in Dalmatia and the largest city on the Croatian coast. It lies on the eastern shore of the Adriatic Sea and is spread over a central peninsula and its surroundings.', '2023-02-01', '2023-10-31', '08:00:00', '22:30:00', 2, '{\"frequency\":\"Weekly\",\"interval\":\"1\",\"start_date\":\"2023-02-01\",\"end_date\":\"31-10-2023\",\"week_days\":[\"Monday\"]}', 150, 'Simon Sais', '+87945123', 'simonsais@email.com', 1);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_trip_bookings` (`id`, `trip_id`, `trip_date`, `trip_time`, `first_name`, `last_name`, `address`, `email`, `phone`, `postal_code`, `created`, `confirmed_at`, `status`, `user_id`) VALUES
(1, 7, '2022-10-15', '07:30:00', 'Anne', 'Smith', '7777 Hollywood Boulevard, 10001', 'annesmith@gmail.com', '+49841521947561', '1001', '2022-09-27 11:10:26', '2022-09-27 07:11:21', 1, 1116);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_trip_capacity_overrides` (`id`, `tripId`, `start_date`, `capacity`) VALUES
(1030, 1, '2021-11-29', 29),
(1031, 1, '2021-11-30', 303),
(1032, 1, '2021-11-18', 76),
(1033, 1, '2021-11-19', 77),
(1034, 1, '2021-11-20', 65),
(1065, 1, '2021-11-26', 330),
(1055, 2, '2021-11-30', 79),
(1054, 2, '2021-11-28', 24),
(1053, 2, '2021-11-29', 64),
(1052, 2, '2021-11-27', 77),
(1056, 2, '2021-11-26', 30),
(1050, 2, '2021-11-25', 22),
(1049, 2, '2021-11-24', 33),
(1048, 2, '2021-11-23', 56),
(1047, 2, '2021-11-22', 54),
(1046, 2, '2021-11-21', 66),
(1058, 3, '2021-12-06', 33),
(1059, 3, '2021-12-13', 45),
(1060, 3, '2021-12-20', 67),
(1061, 3, '2021-12-27', 90),
(1062, 3, '2022-01-03', 86),
(1063, 3, '2022-01-10', 87),
(1066, 2, '2021-12-01', 36),
(1067, 2, '2021-12-02', 54),
(1068, 2, '2021-12-03', 76),
(1069, 2, '2021-12-04', 54),
(1070, 2, '2021-12-05', 90),
(1071, 2, '2021-12-06', 30),
(1072, 1, '2021-12-02', 37),
(1073, 1, '2021-12-03', 43),
(1074, 1, '2021-12-04', 47),
(1075, 1, '2021-12-06', 86),
(1076, 1, '2021-12-07', 47),
(1077, 1, '2021-12-10', 94),
(1078, 1, '2021-12-11', 68),
(1079, 1, '2021-12-14', 54),
(1080, 1, '2021-12-17', 35),
(1081, 1, '2021-12-18', 1),
(1082, 1, '2021-12-22', 54),
(1083, 1, '2021-12-23', 86),
(1084, 1, '2021-12-26', 46),
(1085, 1, '2021-12-30', 65),
(1086, 1, '2022-01-01', 97),
(1087, 2, '2021-12-08', 46),
(1088, 2, '2021-12-07', 43),
(1089, 2, '2021-12-09', 42),
(1090, 2, '2021-12-10', 56),
(1091, 2, '2021-12-11', 79),
(1092, 2, '2021-12-12', 88),
(1093, 2, '2021-12-13', 76),
(1094, 2, '2021-12-14', 57),
(1095, 2, '2021-12-15', 87),
(1096, 2, '2021-12-16', 643),
(1097, 2, '2021-12-17', 745),
(1098, 2, '2021-12-18', 67),
(1099, 2, '2021-12-19', 634),
(1100, 2, '2021-12-20', 73),
(1101, 3, '2021-11-29', 96),
(1102, 3, '2022-01-17', 122),
(1103, 3, '2022-01-31', 855),
(1104, 3, '2022-02-14', 63),
(1105, 3, '2022-02-28', 969),
(1107, 4, '2021-12-11', 424),
(1108, 4, '2021-12-12', 442),
(1109, 4, '2021-12-13', 427),
(1110, 4, '2021-12-14', 555),
(1111, 4, '2021-12-15', 667),
(1112, 4, '2021-12-16', 876),
(1114, 5, '2022-02-13', 6364),
(1115, 5, '2022-03-13', 6562),
(1116, 1, '2022-04-30', 3),
(1117, 1, '2022-01-31', 31),
(1118, 8, '2022-05-28', 16),
(1119, 8, '2022-06-28', 17),
(1120, 8, '2022-07-28', 18),
(1228, 6, '2023-03-05', 100),
(1227, 6, '2023-02-05', 100),
(1226, 6, '2023-01-05', 200),
(1225, 6, '2022-12-05', 100),
(1224, 6, '2022-11-05', 150),
(1223, 6, '2022-10-05', 100),
(1222, 6, '2022-09-05', 150),
(1221, 6, '2022-08-05', 100),
(1220, 6, '2022-07-05', 150),
(1219, 6, '2022-06-05', 100),
(1252, 11, '2022-10-21', 50),
(1251, 11, '2022-10-16', 50),
(1250, 11, '2022-10-11', 50),
(1249, 11, '2022-10-06', 60),
(1248, 11, '2022-10-01', 60),
(1247, 11, '2022-09-26', 60),
(1246, 11, '2022-09-21', 50),
(1245, 11, '2022-09-16', 50),
(1244, 11, '2022-09-11', 50),
(1243, 11, '2022-09-06', 50),
(1234, 8, '2022-10-01', 30),
(1235, 8, '2022-11-01', 50),
(1236, 8, '2022-12-01', 50);

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_trip_dates` (`id`, `tripId`, `start_date`, `end_date`) VALUES
(1354, 1, '2023-06-30', '2024-08-05'),
(1353, 1, '2023-06-24', '2024-07-30'),
(1352, 1, '2023-06-17', '2024-07-23'),
(1351, 1, '2023-05-13', '2024-06-18'),
(1350, 1, '2023-04-16', '2024-05-22'),
(1349, 1, '2023-04-30', '2024-06-05'),
(1635, 6, '2024-03-05', '2024-05-05'),
(1634, 6, '2024-02-05', '2024-04-07'),
(1633, 6, '2024-01-05', '2024-03-07'),
(1632, 6, '2023-12-05', '2024-02-04'),
(1312, 3, '2023-12-17', '2024-01-21'),
(1311, 3, '2023-12-11', '2024-01-15'),
(1310, 3, '2023-12-10', '2024-01-14'),
(1309, 3, '2023-12-04', '2024-01-08'),
(1308, 3, '2023-12-03', '2024-01-07'),
(1307, 3, '2023-11-27', '2024-01-01'),
(1306, 3, '2023-11-26', '2023-12-31'),
(1348, 1, '2023-04-22', '2024-05-28'),
(1347, 1, '2023-04-08', '2024-05-14'),
(1346, 1, '2023-04-01', '2024-05-07'),
(1345, 1, '2023-12-04', '2024-04-09'),
(1344, 1, '2023-12-22', '2024-04-27'),
(1343, 1, '2023-12-15', '2024-04-20'),
(1342, 1, '2023-01-31', '2024-03-08'),
(1341, 1, '2023-01-28', '2024-03-05'),
(1340, 1, '2023-01-21', '2024-02-26'),
(1339, 1, '2023-01-01', '2024-02-06'),
(1338, 1, '2023-12-10', '2024-01-15'),
(1337, 1, '2023-12-03', '2024-01-08'),
(1336, 1, '2023-12-04', '2024-01-09'),
(1335, 1, '2023-12-07', '2024-01-12'),
(1334, 1, '2023-12-02', '2024-01-07'),
(1631, 6, '2023-11-05', '2024-01-05'),
(1630, 6, '2023-10-05', '2023-12-05'),
(1333, 1, '2023-12-26', '2024-01-31'),
(1332, 1, '2023-12-30', '2024-02-04'),
(1331, 1, '2023-12-23', '2024-01-28'),
(1330, 1, '2023-12-17', '2024-01-22'),
(1329, 1, '2023-12-11', '2024-01-16'),
(1328, 1, '2023-12-18', '2024-01-23'),
(1327, 1, '2023-12-22', '2024-01-27'),
(1326, 1, '2023-12-14', '2024-01-19'),
(1325, 1, '2023-12-06', '2024-01-11'),
(1324, 1, '2023-11-20', '2023-12-26'),
(1323, 1, '2023-11-18', '2023-12-24'),
(1322, 1, '2023-11-19', '2023-12-25'),
(1321, 1, '2023-11-26', '2024-01-01'),
(1320, 1, '2023-11-29', '2024-01-04'),
(1319, 1, '2023-11-30', '2024-01-05'),
(1355, 1, '2023-12-25', '2024-04-02'),
(1318, 3, '2023-01-07', '2024-02-11'),
(1317, 3, '2023-01-01', '2024-02-05'),
(1316, 3, '2023-12-31', '2024-02-04'),
(1315, 3, '2023-12-25', '2024-01-29'),
(1314, 3, '2023-12-24', '2024-01-28'),
(1313, 3, '2023-12-18', '2024-01-22'),
(1241, 5, '2023-01-19', '2023-12-09'),
(1240, 5, '2023-01-12', '2023-12-02'),
(1239, 5, '2023-01-05', '2023-12-23'),
(1242, 5, '2023-01-26', '2023-12-16'),
(1243, 5, '2023-01-08', '2023-12-26'),
(1244, 5, '2023-01-15', '2023-12-05'),
(1245, 5, '2023-01-22', '2023-12-12'),
(1246, 5, '2023-01-29', '2023-12-19'),
(1791, 14, '2024-03-03', '2024-03-16'),
(1790, 14, '2024-03-02', '2024-03-15'),
(1789, 14, '2024-03-01', '2024-03-14'),
(1629, 6, '2023-09-05', '2023-11-05'),
(1628, 6, '2023-08-05', '2023-10-05'),
(1627, 6, '2023-07-05', '2023-09-04'),
(1626, 6, '2023-06-05', '2023-08-05'),
(1801, 14, '2024-03-13', '2024-03-26'),
(1800, 14, '2024-03-12', '2024-03-25'),
(1799, 14, '2024-03-11', '2024-03-24'),
(1794, 14, '2024-03-06', '2024-03-19'),
(1793, 14, '2024-03-05', '2024-03-18'),
(1792, 14, '2024-03-04', '2024-03-17'),
(1788, 14, '2024-02-28', '2024-03-13'),
(1787, 14, '2024-02-27', '2024-03-12'),
(1786, 14, '2024-02-26', '2024-03-11'),
(1785, 14, '2024-02-25', '2024-03-10'),
(1784, 14, '2024-02-24', '2024-03-09'),
(1783, 14, '2024-02-23', '2024-03-08'),
(1782, 14, '2024-02-22', '2024-03-07'),
(1798, 14, '2024-03-10', '2024-03-23'),
(1797, 14, '2024-03-09', '2024-03-22'),
(1796, 14, '2024-03-08', '2024-03-21'),
(1795, 14, '2024-03-07', '2024-03-20');

INSERT INTO `{$wpdb->prefix}jbusinessdirectory_trip_pictures` (`id`, `tripId`, `picture_info`, `picture_path`, `picture_enable`, `picture_title`) VALUES
(1179, 3, '', '/trips/3/harbor_cityscape_saranda_albanian_riviera_albania_980x650.jpg', 1, ''),
(1139, 5, '', '/trips/5/jessica_arends_8saVYOMHFzU_unsplash.jpg', 1, ''),
(1188, 1, '', '/trips/1/rear_view_friends_road_trip_driving_convertible_car_67525217.jpg', 1, ''),
(1173, 4, '', '/trips/4/ines_alvarez_fdez_hKug0W1tnVU_unsplash.jpg', 1, ''),
(1174, 4, '', '/trips/4/news_header_webseite__49__342_full.jpg', 1, ''),
(1182, 3, '', '/trips/3/will_b_UXKNbZjHCyw_unsplash.jpg', 1, ''),
(1181, 3, '', '/trips/3/luca_micheli_ruWkmt3nU58_unsplash.jpg', 1, ''),
(1180, 3, '', '/trips/3/cristina_gottardi_CSpjU6hYo_0_unsplash.jpg', 1, ''),
(1187, 1, '', '/trips/1/Safari_GettyImages_143917249.jpg', 1, ''),
(1161, 2, '', '/trips/2/venice.jpg', 1, ''),
(1172, 4, '', '/trips/4/photo_1469854523086_cc02fe5d8800.jpg', 1, ''),
(1160, 2, '', '/trips/2/AlbaniaPhotos2_3.jpg', 1, ''),
(1568, 8, '', '/trips/8/trips_images.jpg', 1, ''),
(1550, 11, '', '/trips/11/dan_MdTtpxGlrz8_unsplash.jpg', 1, ''),
(1576, 10, '', '/trips/10/luca_lago_fwxbrSGG6xY_unsplash.jpg', 1, ''),
(1578, 9, '', '/trips/9/timur_garifov_p2RbLnqWPVY_unsplash.jpg', 1, ''),
(1566, 12, '', '/trips/12/spencer_davis_fX0jFyBehjk_unsplash.jpg', 1, ''),
(1573, 7, '', '/trips/7/disney.jpg', 1, ''),
(1545, 6, '', '/trips/6/jeremy_bezanger_S4EU1vPZwzI_unsplash.jpg', 1, '');
";

return $sql;
}

/**
 * Retrieve uninstall queries
 *
 * @return void
 */
private static function get_uninstall_queries(){
    global $wpdb;
   
$sql = "
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_applicationsettings`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_banners`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_banner_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_categories`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_companies`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_contact`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_claim`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_contact`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offers`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offer_pictures`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offer_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_pictures`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_ratings`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_reviews`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_review_abuses`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_review_responses`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_reviews_criteria`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_reviews_user_criteria`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_videos`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_countries`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_currencies`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_date_formats`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_emails`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_packages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_package_fields`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_orders`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_payment_processors`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_payment_processor_fields`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_attributes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_attribute_options`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_attribute_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_attributes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_payments`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_events`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_pictures`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_default_attributes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_cities`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_activity_city`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_reports`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_locations`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_discounts`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_language_translations`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_attachments`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_billing_details`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_bookmarks`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conferences`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_sessions`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_attachments`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_categories`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_companies`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_levels`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_locations`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_speakers`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_speakers`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_speaker_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_speaker_sessions`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_news`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_event_attributes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_offer_attributes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offer_coupons`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_reviews_question`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_reviews_question_answer`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_tickets`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_bookings`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_booking_tickets`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_related`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offer_orders`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offer_order_products`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_associated_items`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_event_appointments`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_services`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_providers`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_provider_services`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_provider_hours`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_service_bookings`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_taxes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_event_videos`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_offer_videos`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_attribute_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_statistics`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_reviews_criteria_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_review_pictures`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_sounds`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_services_list`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_memberships`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_projects`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_projects_pictures`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_testimonials`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_offer_messages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_event_messages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_mobile_devices`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_regions`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_activity_region`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_articles`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_offer_types`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_marketing_email_sent`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_membership`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_directory_apps`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_application_settings`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_payment_processor_services`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_shipping_methods`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_offer_shipping_methods`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_order_taxes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_order_packages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_announcements`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_messages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_campaigns`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_campaign_plans`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_campaign_has_plans`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_user_profiles`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_statistics_archive`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_request_quote_questions`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_request_quote_question_options`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_request_quote`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_request_quote_replies`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_requests`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_request_quote_messages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_subscriptions`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_product_merchants`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_search_logs`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_search_logs_archive`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_editors`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_images`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_registered`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_tax_services`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_offer_stock`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_offer_stock_config`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_zipcodes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_members`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_tax_countries`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_conference_session_registers`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_videos`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_messages`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_video_attributes`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_video_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_pictures_extra`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_video_category`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_videos`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_trips`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_trip_capacity_overrides`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_trip_dates`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_trip_pictures`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_trip_bookings`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_activity_country`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_logs`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_region`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_company_city`;
DROP TABLE IF EXISTS `{$wpdb->prefix}jbusinessdirectory_mobile_app_config`
";

return $sql;
}

}