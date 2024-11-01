<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

if( !defined('FPDF_FONTPATH')){
	define('FPDF_FONTPATH', BD_LIBRARIES_PATH . '/tfpdf/font');
}

if (!defined('AJAX_RESPONSE_SUCCESS')) {
    define('AJAX_RESPONSE_SUCCESS', 1);
}

if (!defined('AJAX_RESPONSE_FAILURE')) {
    define('AJAX_RESPONSE_FAILURE', 0);
}

if (!defined('COMPANY_DESCRIPTIION_MAX_LENGHT')) {
	define('COMPANY_DESCRIPTIION_MAX_LENGHT', 1200);
}
if (!defined('CATEGORY_DESCRIPTIION_MAX_LENGHT')) {
	define('CATEGORY_DESCRIPTIION_MAX_LENGHT', 500);
}
if (!defined('COMPANY_SHORT_DESCRIPTIION_MAX_LENGHT')) {
	define('COMPANY_SHORT_DESCRIPTIION_MAX_LENGHT', 250);
}
if (!defined('OFFER_DESCRIPTIION_MAX_LENGHT')) {
	define('OFFER_DESCRIPTIION_MAX_LENGHT', 1200);
}
if (!defined('COMPANY_SLOGAN_MAX_LENGHT')) {
	define('COMPANY_SLOGAN_MAX_LENGHT', 250);
}
if (!defined('EVENT_DESCRIPTION_MAX_LENGHT')) {
	define('EVENT_DESCRIPTION_MAX_LENGHT', 1200);
}

if (!defined('COMPANY_PICTURES_PATH')) {
	define('COMPANY_PICTURES_PATH', '/companies/');
}
if (!defined('OFFER_PICTURES_PATH')) {
	define('OFFER_PICTURES_PATH', '/offers/');
}
if (!defined('TRIP_PICTURES_PATH')) {
	define('TRIP_PICTURES_PATH', '/trips/');
}
if (!defined('CATEGORY_PICTURES_PATH')) {
	define('CATEGORY_PICTURES_PATH', '/categories/');
}
if (!defined('BANNER_PICTURES_PATH')) {
	define('BANNER_PICTURES_PATH', '/upload/images/banners/');
}
if (!defined('EVENT_PICTURES_PATH')) {
	define('EVENT_PICTURES_PATH', '/events/');
}
if (!defined('COUNTRIES_PICTURES_PATH')) {
	define('COUNTRIES_PICTURES_PATH', '/countries/');
}
if (!defined('VIDEO_PICTURES_PATH')) {
	define('VIDEO_PICTURES_PATH', '/videos/');
}
if (!defined('APP_PICTURES_PATH')) {
	define('APP_PICTURES_PATH', '/app/');
}
if (!defined('MEMBERSHIP_PICTURES_PATH')) {
	define('MEMBERSHIP_PICTURES_PATH', '/memberships/');
}
if (!defined('PROJECT_PICTURES_PATH')) {
	define('PROJECT_PICTURES_PATH', '/project/');
}
if (!defined('PRICE_LIST_PICTURES_PATH')) {
	define('PRICE_LIST_PICTURES_PATH', '/pricelist/');
}
if (!defined('REQUEST_QUOTE_QUESTION_PICTURES_PATH')) {
	define('REQUEST_QUOTE_QUESTION_PICTURES_PATH', '/requestquotequestions/');
}
if (!defined('CAMPAIGN_PLANS_PICTURES_PATH')) {
	define('CAMPAIGN_PLANS_PICTURES_PATH', '/campaign_plans/');
}
if (!defined('SERVICE_PROVIDERS_PICTURES_PATH')) {
	define('SERVICE_PROVIDERS_PICTURES_PATH', '/service_providers/');
}

if (!defined('BUSINESS_ATTACHMENTS_PATH')) {
	define('BUSINESS_ATTACHMENTS_PATH', '/companies/');
}
if (!defined('OFFER_ATTACHMENTS_PATH')) {
	define('OFFER_ATTACHMENTS_PATH', '/offers/');
}
if (!defined('EVENT_ATTACHMENTS_PATH')) {
	define('EVENT_ATTACHMENTS_PATH', '/events/');
}
	
//types for translation
if (!defined('BUSSINESS_ATTACHMENTS')) {
	define('BUSSINESS_ATTACHMENTS', 1);
}
if (!defined('OFFER_ATTACHMENTS')) {
	define('OFFER_ATTACHMENTS', 2);
}
if (!defined('EVENTS_ATTACHMENTS')) {
	define('EVENTS_ATTACHMENTS', 3);
}

if (!defined('MAX_COMPANY_PICTURE_WIDTH')) {
	define('MAX_COMPANY_PICTURE_WIDTH', 1000);
}
if (!defined('MAX_COMPANY_PICTURE_HEIGHT')) {
	define('MAX_COMPANY_PICTURE_HEIGHT', 800);
}

if (!defined('MAX_SPEAKER_IMAGE_WIDTH')) {
	define('MAX_SPEAKER_IMAGE_WIDTH', 600);
}
if (!defined('MAX_SPEAKER_IMAGE_HEIGHT')) {
	define('MAX_SPEAKER_IMAGE_HEIGHT', 600);
}

if (!defined('MAX_LOGO_WIDTH')) {
	define('MAX_LOGO_WIDTH', 800);
}
if (!defined('MAX_LOGO_HEIGHT')) {
	define('MAX_LOGO_HEIGHT', 800);
}

if (!defined('MAX_THUMBNAIL_WIDTH')) {
	define('MAX_THUMBNAIL_WIDTH', 800);
}
if (!defined('MAX_THUMBNAIL_HEIGHT')) {
	define('MAX_THUMBNAIL_HEIGHT', 800);
}

if (!defined('MAX_CATEGORY_ICON_IMAGE_WIDTH')) {
	define('MAX_CATEGORY_ICON_IMAGE_WIDTH', 800);
}
if (!defined('MAX_CATEGORY_ICON_IMAGE_HEIGHT')) {
	define('MAX_CATEGORY_ICON_IMAGE_HEIGHT', 800);
}

if (!defined('MAX_OFFER_PICTURE_WIDTH')) {
	define('MAX_OFFER_PICTURE_WIDTH', 800);
}
if (!defined('MAX_OFFER_PICTURE_HEIGHT')) {
	define('MAX_OFFER_PICTURE_HEIGHT', 800);
}

if (!defined('MAX_GALLERY_WIDTH')) {
	define('MAX_GALLERY_WIDTH', 800);
}
if (!defined('MAX_GALLERY_HEIGHT')) {
	define('MAX_GALLERY_HEIGHT', 800);
}

if (!defined('MAX_BANNER_HEIGHT')) {
	define('MAX_BANNER_HEIGHT', 800);
}
if (!defined('MAX_BANNER_WIDTH')) {
	define('MAX_BANNER_WIDTH', 1000);
}
if (!defined('PICTURE_TYPE_COMPANY')) {
	define('PICTURE_TYPE_COMPANY', 'picture_type_company');
}
if (!defined('PICTURE_TYPE_OFFER')) {
	define('PICTURE_TYPE_OFFER', 'picture_type_offer');
}
if (!defined('PICTURE_TYPE_LOGO')) {
	define('PICTURE_TYPE_LOGO', 'picture_type_logo');
}
if (!defined('PICTURE_TYPE_BANNER')) {
	define('PICTURE_TYPE_BANNER', 'picture_type_banner');
}
if (!defined('PICTURE_TYPE_THUMBNAIL')) {
	define('PICTURE_TYPE_THUMBNAIL', 'picture_type_thumbnail');
}

if (!defined('PICTURE_TYPE_COMPANY_LOGO')) {
	define('PICTURE_TYPE_COMPANY_LOGO', 'picture_type_company_logo');
}

if (!defined('PICTURE_TYPE_EVENT')) {
	define('PICTURE_TYPE_EVENT', 'picture_type_event');
}
if (!defined('PICTURE_TYPE_GALLERY')) {
	define('PICTURE_TYPE_GALLERY', 'picture_type_gallery');
}
if (!defined('PICTURE_TYPE_MARKER')) {
	define('PICTURE_TYPE_MARKER', 'picture_type_marker');
}
if (!defined('PICTURE_TYPE_CATEGORY_ICON')) {
	define('PICTURE_TYPE_CATEGORY_ICON', 'picture_type_category_icon');
}
if (!defined('PICTURE_TYPE_SPEAKER')) {
	define('PICTURE_TYPE_SPEAKER', 'picture_type_speaker');
}

if (!defined('ICON_SIZE')) {
	define('ICON_SIZE', 300);
}

if (!defined('EMAIL_FIRST_NAME')) {
	define('EMAIL_FIRST_NAME', '[first_name]');
}
if (!defined('EMAIL_CATEGORY')) {
	define('EMAIL_CATEGORY', '[category]');
}
if (!defined('EMAIL_LAST_NAME')) {
	define('EMAIL_LAST_NAME', '[last_name]');
}
if (!defined('EMAIL_REVIEW_LINK')) {
	define('EMAIL_REVIEW_LINK', '[review_link]');
}
if (!defined('EMAIL_COMPANY_NAME')) {
	define('EMAIL_COMPANY_NAME', '[company_name]');
}
if (!defined('EMAIL_COMPANY_NAMES')) {
	define('EMAIL_COMPANY_NAMES', '[company_names]');
}
if (!defined('EMAIL_BUSINESS_NAME')) {
	define('EMAIL_BUSINESS_NAME', '[business_name]');
}
if (!defined('EMAIL_BUSINESS_ADDRESS')) {
	define('EMAIL_BUSINESS_ADDRESS', '[business_address]');
}
if (!defined('EMAIL_BUSINESS_WEBSITE')) {
	define('EMAIL_BUSINESS_WEBSITE', '[business_website]');
}
if (!defined('EMAIL_BUSINESS_LOGO')) {
	define('EMAIL_BUSINESS_LOGO', '[business_logo]');
}
if (!defined('EMAIL_BUSINESS_CATEGORY')) {
	define('EMAIL_BUSINESS_CATEGORY', '[business_category]');
}
if (!defined('EMAIL_BUSINESS_CONTACT_PERSON')) {
	define('EMAIL_BUSINESS_CONTACT_PERSON', '[business_contact_person]');
}
if (!defined('EMAIL_BUSINESS_REFFERED_BY')) {
	define('EMAIL_BUSINESS_REFFERED_BY', '[business_referred_by]');
}
if (!defined('EMAIL_OFFER_NAME')) {
	define('EMAIL_OFFER_NAME', '[offer_name]');
}
if (! defined('EMAIL_ITEM_TYPE')) {
	define('EMAIL_ITEM_TYPE', '[item_type]');
}
if (!defined('EMAIL_EVENT_NAME')) {
	define('EMAIL_EVENT_NAME', '[event_name]');
}
if (!defined('EMAIL_EVENT_LINK')) {
	define('EMAIL_EVENT_LINK', '[event_link]');
}
if (!defined('EMAIL_EVENT_START_DATE')) {
	define('EMAIL_EVENT_START_DATE', '[event_start_date]');
}
if (!defined('EMAIL_EVENT_ADDRESS')) {
	define('EMAIL_EVENT_ADDRESS', '[event_address]');
}
if (!defined('EMAIL_EVENT_EMAIL')) {
	define('EMAIL_EVENT_EMAIL', '[event_email]');
}
if (!defined('EMAIL_EVENT_PHONE')) {
	define('EMAIL_EVENT_PHONE', '[event_phone]');
}
if (!defined('EMAIL_CATEGORY_LINK')) {
	define('EMAIL_CATEGORY_LINK', '[category_link]');
}
if (!defined('EMAIL_REQUEST_QUOTE_SUMMARY')) {
	define('EMAIL_REQUEST_QUOTE_SUMMARY', '[request_quote_summary]');
}
if (!defined('EMAIL_CLICK_HERE_LINK')) {
	define('EMAIL_CLICK_HERE_LINK', '[click_here_link]');
}
if (!defined('EMAIL_ORDERS_LINK')) {
	define('EMAIL_ORDERS_LINK', '[orders_link]');
}
if (!defined('EMAIL_USER_NAME')) {
	define('EMAIL_USER_NAME', '[user_name]');
}

if (!defined('EMAIL_USERNAME')) {
	define('EMAIL_USERNAME', '[display_name]');
}

if (!defined('EMAIL_PREVIOUS_USER')) {
	define('EMAIL_PREVIOUS_USER', '[previous_user]');
}
if (!defined('EMAIL_ACTUAL_USER')) {
	define('EMAIL_ACTUAL_USER', '[actual_user]');
}
	
if (!defined('EMAIL_BOOKING_DATE')) {
	define('EMAIL_BOOKING_DATE', '[event_booking_date]');
}
if (!defined('EMAIL_BOOKING_ID')) {
	define('EMAIL_BOOKING_ID', '[event_booking_id]');
}

if (!defined('EMAIL_EVENT_PICTURE')) {
	define('EMAIL_EVENT_PICTURE', '[event_picture]');
}

if (!defined('EMAIL_BOOKING_DETAILS')) {
	define('EMAIL_BOOKING_DETAILS', '[booking_details]');
}
if (!defined('EMAIL_BOOKING_GUEST_DETAILS')) {
	define('EMAIL_BOOKING_GUEST_DETAILS', '[booking_guest_details]');
}


if (!defined('EMAIL_CLAIMED_COMPANY_NAME')) {
	define('EMAIL_CLAIMED_COMPANY_NAME', '[claimed_company_name]');
}
if (!defined('EMAIL_CONTACT_CONTENT')) {
	define('EMAIL_CONTACT_CONTENT', '[contact_email_content]');
}
if (!defined('EMAIL_CONTACT_EMAIL')) {
	define('EMAIL_CONTACT_EMAIL', '[contact_email]');
}
if (!defined('EMAIL_ABUSE_DESCRIPTION')) {
	define('EMAIL_ABUSE_DESCRIPTION', '[abuse_description]');
}
if (!defined('EMAIL_REVIEW_NAME')) {
	define('EMAIL_REVIEW_NAME', '[review_name]');
}


if (!defined('EMAIL_CUSTOMER_NAME')) {
	define('EMAIL_CUSTOMER_NAME', '[customer_name]');
}
if (!defined('EMAIL_SITE_ADDRESS')) {
	define('EMAIL_SITE_ADDRESS', '[site_address]');
}
if (!defined('EMAIL_UNIT_PRICE')) {
	define('EMAIL_UNIT_PRICE', '[unit_price]');
}
if (!defined('EMAIL_SERVICE_NAME')) {
	define('EMAIL_SERVICE_NAME', '[service_name]');
}
if (!defined('EMAIL_PROVIDER_NAME')) {
    define('EMAIL_PROVIDER_NAME', '[provider_name]');
}

if (!defined('EMAIL_TAX_AMOUNT')) {
	define('EMAIL_TAX_AMOUNT', '[tax_amount]');
}
if (!defined('EMAIL_ORDER_ID')) {
	define('EMAIL_ORDER_ID', '[order_id]');
}
if (!defined('EMAIL_PAYMENT_METHOD')) {
	define('EMAIL_PAYMENT_METHOD', '[payment_method]');
}
if (!defined('EMAIL_INVOICE_NUMBER')) {
	define('EMAIL_INVOICE_NUMBER', '[invoice_number]');
}
if (!defined('EMAIL_ORDER_DATE')) {
	define('EMAIL_ORDER_DATE', '[order_date]');
}
if (!defined('EMAIL_TOTAL_PRICE')) {
	define('EMAIL_TOTAL_PRICE', '[total_price]');
}
if (!defined('EMAIL_ORDER_PAYMENT_URL')) {
	define('EMAIL_ORDER_PAYMENT_URL', '[order_url]');
}
if (!defined('EMAIL_SUBTOTAL_PRICE')) {
	define('EMAIL_SUBTOTAL_PRICE', '[subtotal_price]');
}
if (!defined('EMAIL_BILLING_INFORMATION')) {
	define('EMAIL_BILLING_INFORMATION', '[billing_information]');
}
if (!defined('EMAIL_EXPIRATION_DAYS')) {
	define('EMAIL_EXPIRATION_DAYS', '[exp_days]');
}
if (!defined('EMAIL_PAYMENT_DETAILS')) {
	define('EMAIL_PAYMENT_DETAILS', '[payment_details]');
}
if (!defined('EMAIL_DIRECTORY_WEBSITE')) {
	define('EMAIL_DIRECTORY_WEBSITE', '[directory_website]');
}
if (!defined('EMAIL_COMPANY_SOCIAL_NETWORKS')) {
	define('EMAIL_COMPANY_SOCIAL_NETWORKS', '[company_social_networks]');
}
if (!defined('EMAIL_COMPANY_LOGO')) {
	define('EMAIL_COMPANY_LOGO', '[company_logo]');
}

if (!defined('EMAIL_OFFER_ORDER_ID')) {
	define('EMAIL_OFFER_ORDER_ID', '[offer_order_id]');
}
if (!defined('EMAIL_OFFER_ORDER_DATE')) {
	define('EMAIL_OFFER_ORDER_DATE', '[offer_order_date]');
}
if (!defined('EMAIL_OFFER_ORDER_DETAILS')) {
	define('EMAIL_OFFER_ORDER_DETAILS', '[offer_order_details]');
}
if (!defined('EMAIL_OFFER_ORDER_BUYER_DETAILS')) {
	define('EMAIL_OFFER_ORDER_BUYER_DETAILS', '[offer_order_buyer_details]');
}
if (!defined('EMAIL_OFFER_ORDER_TRACKING_LINK')) {
	define('EMAIL_OFFER_ORDER_TRACKING_LINK', '[offer_order_tracking_link]');
}

if (!defined('EMAIL_APPOINTMENT_DATE')) {
	define('EMAIL_APPOINTMENT_DATE', '[appointment_date]');
}
if (!defined('EMAIL_APPOINTMENT_TIME')) {
	define('EMAIL_APPOINTMENT_TIME', '[appointment_time]');
}

if (!defined('EMAIL_APPOINTMENT_MESSAGE')) {
	define('EMAIL_APPOINTMENT_MESSAGE', '[appointment_message]');
}

if (!defined('EMAIL_APPOINTMENT_URL')) {
    define('EMAIL_APPOINTMENT_URL', '[appointment_url]');
}

if (!defined('EMAIL_APPOINTMENT_STATUS')) {
	define('EMAIL_APPOINTMENT_STATUS', '[appointment_status]');
}
if (!defined('EMAIL_EMAIL')) {
	define('EMAIL_EMAIL', '[email]');
}
if (!defined('EMAIL_PHONE')) {
	define('EMAIL_PHONE', '[phone]');
}
if (!defined('EMAIL_FORM_TYPE')) {
	define('EMAIL_FORM_TYPE', '[form_type]');
}

if (!defined('EMAIL_SERVICE_BOOKING_ID')) {
	define('EMAIL_SERVICE_BOOKING_ID', '[service_booking_id]');
}
if (!defined('EMAIL_SERVICE_BOOKING_DATE')) {
	define('EMAIL_SERVICE_BOOKING_DATE', '[service_booking_date]');
}
if (!defined('EMAIL_SERVICE_BOOKING_DETAILS')) {
	define('EMAIL_SERVICE_BOOKING_DETAILS', '[service_booking_details]');
}
if (!defined('EMAIL_SERVICE_BUYER_DETAILS')) {
	define('EMAIL_SERVICE_BUYER_DETAILS', '[service_buyer_details]');
}
if (!defined('EMAIL_SERVICE_BOOKING_NAME')) {
	define('EMAIL_SERVICE_BOOKING_NAME', '[service_booking_name]');
}
if (!defined('EMAIL_SERVICE_BOOKING_STATUS')) {
	define('EMAIL_SERVICE_BOOKING_STATUS', '[service_booking_status]');
}
	
if (! defined('EMAIL_PRODUCT_REQUESTED')) {
	define('EMAIL_PRODUCT_REQUESTED', '[requested_product]');
}
if (! defined('EMAIL_PRODUCT_REQUESTED_PATH')) {
	define('EMAIL_PRODUCT_REQUESTED_PATH', '[requested_path]');
}

if (! defined('EMAIL_OFFER_NOTIFICATION_QUANTITY')) {
	define('EMAIL_OFFER_NOTIFICATION_QUANTITY', '[notification_quantity]');
}

if (! defined('EMAIL_OFFER_STOCK_DETAILS')) {
	define('EMAIL_OFFER_STOCK_DETAILS', '[stock_details]');
}

if (! defined('EMAIL_ACTIVATION_URL')) {
	define('EMAIL_ACTIVATION_URL', '[email_verification_link]');
}

if (! defined('EMAIL_ACTIVATION_URL_TEXT')) {
	define('EMAIL_ACTIVATION_URL_TEXT', '[email_verification_link_text]');
}

if (!defined('EMAIL_TAX_DETAIL')) {
	define('EMAIL_TAX_DETAIL', '[tax_detail]');
}

if (!defined('BUSINESS_PATH_CONTROL_PANEL')) {
	define('BUSINESS_PATH_CONTROL_PANEL', '[link_business_control_panel]');
}

if (!defined('BUSINESS_JOIN_PATH_CONTROL_PANEL')) {
	define('BUSINESS_JOIN_PATH_CONTROL_PANEL', '[link_business_join_control_panel]');
}
if (!defined('EMAIL_CAMPAIGN_ID')) {
	define('EMAIL_CAMPAIGN_ID', '[campaign_id]');
}
if (!defined('EMAIL_CAMPAIGN_NAME')) {
	define('EMAIL_CAMPAIGN_NAME', '[campaign_name]');
}
if (!defined('EMAIL_CAMPAIGN_DETAILS')) {
	define('EMAIL_CAMPAIGN_DETAILS', '[campaign_details]');
}
if (!defined('EMAIL_CAMPAIGN_BUYER_DETAILS')) {
	define('EMAIL_CAMPAIGN_BUYER_DETAILS', '[campaign_buyer_details]');
}
if (! defined('EMAIL_LINK')) {
	define('EMAIL_LINK', '[link]');
}

if (!defined('EMAIL_PASSWORD')) {
	define('EMAIL_PASSWORD', '[password]');
}

if (!defined('EMAIL_DASHBOARD')) {
	define('EMAIL_DASHBOARD', '[business-dashboard]');
}

if (! defined('EMAIL_MESSAGE')) {
	define('EMAIL_MESSAGE', '[message]');
}

if (! defined('EMAIL_DISAPPROVAL_TEXT')) {
	define('EMAIL_DISAPPROVAL_TEXT', '[disapproval_text]');
}

if (!defined('COMPANY_STATUS_CLAIMED')) {
	define('COMPANY_STATUS_CLAIMED', -1);
}

if (!defined('COMPANY_STATUS_CREATED')) {
	define('COMPANY_STATUS_CREATED', 0);
}

if (!defined('COMPANY_STATUS_DISAPPROVED')) {
	define('COMPANY_STATUS_DISAPPROVED', 1);
}

if (!defined('COMPANY_STATUS_APPROVED')) {
	define('COMPANY_STATUS_APPROVED', 2);
}

if (!defined('COMPANY_STATUS_CLAIMED_APPROVED')) {
	define('COMPANY_STATUS_CLAIMED_APPROVED', 3);
}

if (!defined('EVENT_CREATED')) {
	define('EVENT_CREATED', 0);
}
if (!defined('EVENT_APPROVED')) {
	define('EVENT_APPROVED', 1);
}

if (!defined('OFFER_CREATED')) {
	define('OFFER_CREATED', 0);
}
if (!defined('OFFER_APPROVED')) {
	define('OFFER_APPROVED', 1);
}

if (!defined('DESCRIPTION')) {
	define('DESCRIPTION', "description");
}
if (!defined('HTML_DESCRIPTION')) {
	define('HTML_DESCRIPTION', "html_description");
}
if (!defined('FEATURED_COMPANIES')) {
	define('FEATURED_COMPANIES', "featured_companies");
}
if (!defined('REVIEWS')) {
	define('REVIEWS', "reviews");
}
if (!defined('SHOW_COMPANY_LOGO')) {
	define('SHOW_COMPANY_LOGO', "company_logo");
}
if (!defined('WEBSITE_ADDRESS')) {
	define('WEBSITE_ADDRESS', "website_address");
}

if (!defined('MULTIPLE_CATEGORIES')) {
	define('MULTIPLE_CATEGORIES', "multiple_categories");
}
if (!defined('IMAGE_UPLOAD')) {
	define('IMAGE_UPLOAD', "image_upload");
}
if (!defined('VIDEOS')) {
	define('VIDEOS', "videos");
}
if (!defined('GOOGLE_MAP')) {
	define('GOOGLE_MAP', "google_map");
}
if (!defined('CONTACT_FORM')) {
	define('CONTACT_FORM', "contact_form");
}
if (!defined('COMPANY_OFFERS')) {
	define('COMPANY_OFFERS', "company_offers");
}
if (!defined('FEATURED_OFFERS')) {
	define('FEATURED_OFFERS', "featured_offers");
}
if (!defined('SOCIAL_NETWORKS')) {
	define('SOCIAL_NETWORKS', "social_networks");
}
if (!defined('COMPANY_EVENTS')) {
	define('COMPANY_EVENTS', "company_events");
}
if (!defined('PHONE')) {
	define('PHONE', "phone");
}
if (!defined('CUSTOM_TAB')) {
	define('CUSTOM_TAB', "custom_tab");
}
if (!defined('ATTACHMENTS')) {
	define('ATTACHMENTS', "attachments");
}
if (!defined('OPENING_HOURS')) {
	define('OPENING_HOURS', "opening_hours");
}
if (!defined('SECONDARY_LOCATIONS')) {
	define('SECONDARY_LOCATIONS', "secondary_locations");
}
if (!defined('COMPANY_SERVICES')) {
	define('COMPANY_SERVICES', "company_services");
}
if (!defined('SOUNDS_FEATURE')) {
	define('SOUNDS_FEATURE', "company_sounds");
}


if (!defined('PAYMENT_REDIRECT')) {
	define('PAYMENT_REDIRECT', 1);
}
if (!defined('PAYMENT_SUCCESS')) {
	define('PAYMENT_SUCCESS', 2);
}
if (!defined('PAYMENT_WAITING')) {
	define('PAYMENT_WAITING', 3);
}
if (!defined('PAYMENT_ERROR')) {
	define('PAYMENT_ERROR', 4);
}
if (!defined('PAYMENT_CANCELED')) {
	define('PAYMENT_CANCELED', 5);
}

if (!defined('PAYMENT_STATUS_NOT_PAID')) {
	define('PAYMENT_STATUS_NOT_PAID', "0");
}
if (!defined('PAYMENT_STATUS_PAID')) {
	define('PAYMENT_STATUS_PAID', "1");
}
if (!defined('PAYMENT_STATUS_PENDING')) {
	define('PAYMENT_STATUS_PENDING', '2');
}
if (!defined('PAYMENT_STATUS_WAITING')) {
	define('PAYMENT_STATUS_WAITING', '3');
}
if (!defined('PAYMENT_STATUS_FAILURE')) {
	define('PAYMENT_STATUS_FAILURE', '4');
}
if (!defined('PAYMENT_STATUS_CANCELED')) {
	define('PAYMENT_STATUS_CANCELED', '5');
}


if (!defined('UPDATE_TYPE_NEW')) {
	define('UPDATE_TYPE_NEW', "0");
}
if (!defined('UPDATE_TYPE_UPGRADE')) {
	define('UPDATE_TYPE_UPGRADE', "1");
}
if (!defined('UPDATE_TYPE_EXTEND')) {
	define('UPDATE_TYPE_EXTEND', "2");
}

if (!defined('ITEM_CREATED')) {
	define('ITEM_CREATED', "1");
}
if (!defined('ITEM_UPDATED')) {
	define('ITEM_UPDATED', "2");
}
if (!defined('ITEM_DELETED')) {
	define('ITEM_DELETED', "3");
}

if (!defined('LIST_VIEW')) {
	define('LIST_VIEW', "list");
}
if (!defined('GRID_VIEW')) {
	define('GRID_VIEW', "grid");
}


if (!defined('ATTRIBUTE_MANDATORY')) {
	define('ATTRIBUTE_MANDATORY', 1);
}
if (!defined('ATTRIBUTE_OPTIONAL')) {
	define('ATTRIBUTE_OPTIONAL', 2);
}
if (!defined('ATTRIBUTE_NOT_SHOW')) {
	define('ATTRIBUTE_NOT_SHOW', 3);
}

if (!defined('SEARCH_BY_DISTNACE')) {
	define('SEARCH_BY_DISTNACE', 0);
}

if (!defined('SEARCH_BY_ACTIVITY_RADIUS')) {
	define('SEARCH_BY_ACTIVITY_RADIUS', 1);
}

if (!defined('SEARCH_BY_EXACT_CODE')) {
	define('SEARCH_BY_EXACT_CODE', 2);
}

if (!defined('SEARCH_BY_ACTIVITY_AREA')) {
	define('SEARCH_BY_ACTIVITY_AREA', 3);
}

if (!defined('DS')) {
	define('DS', '/');
}

//types for translation
if (!defined('BUSSINESS_DESCRIPTION_TRANSLATION')) {
	define('BUSSINESS_DESCRIPTION_TRANSLATION', 1);
}

if (!defined('BUSSINESS_SLOGAN_TRANSLATION')) {
	define('BUSSINESS_SLOGAN_TRANSLATION', 2);
}

if (!defined('CATEGORY_TRANSLATION')) {
	define('CATEGORY_TRANSLATION', 3);
}

if (!defined('PACKAGE_TRANSLATION')) {
	define('PACKAGE_TRANSLATION', 4);
}

if (!defined('OFFER_DESCRIPTION_TRANSLATION')) {
	define('OFFER_DESCRIPTION_TRANSLATION', 5);
}

if (!defined('EVENT_DESCRIPTION_TRANSLATION')) {
	define('EVENT_DESCRIPTION_TRANSLATION', 6);
}

if (!defined('COUNTRY_TRANSLATION')) {
	define('COUNTRY_TRANSLATION', 7);
}

if (!defined('TYPE_TRANSLATION')) {
	define('TYPE_TRANSLATION', 8);
}

if (!defined('ATTRIBUTE_TRANSLATION')) {
	define('ATTRIBUTE_TRANSLATION', 9);
}

if (!defined('CONFERENCE_TRANSLATION')) {
	define('CONFERENCE_TRANSLATION', 10);
}

if (!defined('CONFERENCE_SESSION_TRANSLATION')) {
	define('CONFERENCE_SESSION_TRANSLATION', 11);
}

if (!defined('CONFERENCE_SPEAKER_TRANSLATION')) {
	define('CONFERENCE_SPEAKER_TRANSLATION', 12);
}

if (!defined('CONFERENCE_TYPE_TRANSLATION')) {
	define('CONFERENCE_TYPE_TRANSLATION', 13);
}

if (!defined('CONFERENCE_LEVEL_TRANSLATION')) {
	define('CONFERENCE_LEVEL_TRANSLATION', 14);
}

if (!defined('CONFERENCE_SPEAKER_TYPE_TRANSLATION')) {
	define('CONFERENCE_SPEAKER_TYPE_TRANSLATION', 15);
}

if (!defined('REVIEW_CRITERIA_TRANSLATION')) {
	define('REVIEW_CRITERIA_TRANSLATION', 16);
}

if (!defined('EMAIL_TRANSLATION')) {
	define('EMAIL_TRANSLATION', 17);
}

if (!defined('EVENT_TYPE_TRANSLATION')) {
	define('EVENT_TYPE_TRANSLATION', 18);
}

if (!defined('REVIEW_QUESTION_TRANSLATION')) {
	define('REVIEW_QUESTION_TRANSLATION', 19);
}

if (!defined('EVENT_TICKET_TRANSLATION')) {
	define('EVENT_TICKET_TRANSLATION', 20);
}

if (!defined('TERMS_CONDITIONS_TRANSLATION')) {
	define('TERMS_CONDITIONS_TRANSLATION', 21);
}

if (!defined('COMPANY_SERVICE_TRANSLATION')) {
	define('COMPANY_SERVICE_TRANSLATION', 22);
}

if (!defined('COMPANY_PROVIDER_TRANSLATION')) {
	define('COMPANY_PROVIDER_TRANSLATION', 29);
}

if (!defined('CATEGORY_META_TRANSLATION')) {
	define('CATEGORY_META_TRANSLATION', 30);
}

if (!defined('BUSINESS_META_TRANSLATION')) {
	define('BUSINESS_META_TRANSLATION', 25);
}

if (!defined('OFFER_META_TRANSLATION')) {
	define('OFFER_META_TRANSLATION', 26);
}

if (!defined('EVENT_META_TRANSLATION')) {
	define('EVENT_META_TRANSLATION', 27);
}

if (!defined('OFFER_TYPE_TRANSLATION')) {
	define('OFFER_TYPE_TRANSLATION', 28);
}

if (!defined('REVIEWS_TERMS_CONDITIONS_TRANSLATION')) {
	define('REVIEWS_TERMS_CONDITIONS_TRANSLATION', 23);
}

if (!defined('CONTACT_TERMS_CONDITIONS_TRANSLATION')) {
	define('CONTACT_TERMS_CONDITIONS_TRANSLATION', 24);
}

if (!defined('RESPONSIBLE_CONTENT_TRANSLATION')) {
	define('RESPONSIBLE_CONTENT_TRANSLATION', 31);
}

if (!defined('PRIVACY_POLICY_TRANSLATION')) {
	define('PRIVACY_POLICY_TRANSLATION', 36);
}
if (!defined('ANNOUNCEMENT_DESCRIPTION_TRANSLATION')) {
	define('ANNOUNCEMENT_DESCRIPTION_TRANSLATION', 37);
}

if (!defined('TAX_DESCRIPTION_TRANSLATION')) {
	define('TAX_DESCRIPTION_TRANSLATION', 35);
}

if (!defined('MEMBERSHIP_DESCRIPTION_TRANSLATION')) {
	define('MEMBERSHIP_DESCRIPTION_TRANSLATION', 33);
}

if (!defined('PROJECT_DESCRIPTION_TRANSLATION')) {
	define('PROJECT_DESCRIPTION_TRANSLATION', 34);
}

if (!defined('COMPANY_PRICE_LIST_TRANSLATION')) {
	define('COMPANY_PRICE_LIST_TRANSLATION', 38);
}

if (!defined('CUSTOM_TAB_TRANSLATION')) {
	define('CUSTOM_TAB_TRANSLATION', 35);
}

if (!defined('TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION')) {
	define('TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION', 39);
}

if (!defined('REVIEWS_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION')) {
	define('REVIEWS_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION', 40);
}

if (!defined('CONTACT_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION')) {
	define('CONTACT_TERMS_CONDITIONS_ARTICLE_ID_TRANSLATION', 41);
}

if (!defined('PRIVACY_POLICY_ARTICLE_ID_TRANSLATION')) {
	define('PRIVACY_POLICY_ARTICLE_ID_TRANSLATION', 42);
}

if (!defined('DOCUMENTATION_URL')) {
	define('DOCUMENTATION_URL', 'http://cmsjunkie.com/docs/jbusinessdirectory/');
}

if (!defined('LANGUAGE_RECEIVING_EMAIL')) {
	define('LANGUAGE_RECEIVING_EMAIL', 'support@cmsjunkie.com');
}

if (!defined('SESSION_ATTACHMENTS')) {
	define('SESSION_ATTACHMENTS', 4);
}

if (!defined('CONFERENCE_BD_PICTURES_PATH')) {
	define('CONFERENCE_BD_PICTURES_PATH', '/conferences/');
}

if (!defined('SESSION_LOCATION_BD_PICTURES_PATH')) {
	define('SESSION_LOCATION_BD_PICTURES_PATH', '/session_location/');
}

if (!defined('SPEAKER_BD_PICTURES_PATH')) {
	define('SPEAKER_BD_PICTURES_PATH', '/speakers/');
}

if (!defined('SESSION_BD_PICTURES_PATH')) {
	define('SESSION_BD_PICTURES_PATH', '/sessions/');
}

if (!defined('SESSION_ATTACHMENTS_PATH')) {
	define('SESSION_ATTACHMENTS_PATH', '/sessions/');
}

if (!defined('NEWS_REFRESH_PERIOD')) {
	define('NEWS_REFRESH_PERIOD', 7);
}

if (!defined('ATTRIBUTE_TYPE_BUSINESS')) {
	define('ATTRIBUTE_TYPE_BUSINESS', 1);
}

if (!defined('ATTRIBUTE_TYPE_OFFER')) {
	define('ATTRIBUTE_TYPE_OFFER', 2);
}

if (!defined('ATTRIBUTE_TYPE_EVENT')) {
	define('ATTRIBUTE_TYPE_EVENT', 3);
}

if (!defined('ATTRIBUTE_TYPE_VIDEO')) {
	define('ATTRIBUTE_TYPE_VIDEO', 4);
}

if (!defined('CATEGORY_TYPE_BUSINESS')) {
	define('CATEGORY_TYPE_BUSINESS', 1);
}

if (!defined('CATEGORY_TYPE_OFFER')) {
	define('CATEGORY_TYPE_OFFER', 2);
}

if (!defined('CATEGORY_TYPE_EVENT')) {
	define('CATEGORY_TYPE_EVENT', 3);
}

if (!defined('CATEGORY_TYPE_CONFERENCE')) {
	define('CATEGORY_TYPE_CONFERENCE', 4);
}

if (!defined('CATEGORY_TYPE_VIDEO')) {
	define('CATEGORY_TYPE_VIDEO', 5);
}

if (!defined('EVENT_BOOKING_CREATED')) {
	define('EVENT_BOOKING_CREATED', "0");
}
if (!defined('EVENT_BOOKING_CONFIRMED')) {
	define('EVENT_BOOKING_CONFIRMED', "1");
}
if (!defined('EVENT_BOOKING_CANCELED')) {
	define('EVENT_BOOKING_CANCELED', "2");
}

if (!defined('EVENT_PAYMENT_STATUS_NOT_PAID')) {
	define('EVENT_PAYMENT_STATUS_NOT_PAID', "0");
}
if (!defined('EVENT_PAYMENT_STATUS_PAID')) {
	define('EVENT_PAYMENT_STATUS_PAID', "1");
}

if (!defined('LISTING_JOIN_STATUS_DISAPPROVED')) {
	define('LISTING_JOIN_STATUS_DISAPPROVED', 1);
}

if (!defined('LISTING_JOIN_STATUS_APPROVED')) {
	define('LISTING_JOIN_STATUS_APPROVED', 2);
}

if (!defined('REVIEW_STATUS_DISAPPROVED')) {
	define('REVIEW_STATUS_DISAPPROVED', 1);
}

if (!defined('REVIEW_STATUS_APPROVED')) {
	define('REVIEW_STATUS_APPROVED', 2);
}

if (!defined('REVIEW_STATUS_CREATED')) {
	define('REVIEW_STATUS_CREATED', 0);
}

if (!defined('EMAIL_NOTIFICATION_PERIOD')) {
	define('EMAIL_NOTIFICATION_PERIOD', 6);
}

if (!defined('EMAIL_BUSINESS_ADMINISTRATOR_URL')) {
	define('EMAIL_BUSINESS_ADMINISTRATOR_URL', '[business_admin_path]');
}

if (!defined('EMAIL_REPORT_CAUSE')) {
	define('EMAIL_REPORT_CAUSE', '[report_cause]');
}

if (!defined('RELATED_COMPANIES')) {
	define('RELATED_COMPANIES', "company_related");
}

if (!defined('MEMBERSHIPS')) {
	define('MEMBERSHIPS', "Memberships");
}

if (! defined('PRODUCTS')) {
	define('PRODUCTS', "Products");
}

if (!defined('REVIEW_TYPE_BUSINESS')) {
	define('REVIEW_TYPE_BUSINESS', 1);
}
if (!defined('REVIEW_TYPE_OFFER')) {
	define('REVIEW_TYPE_OFFER', 2);
}

if (!defined('OFFER_ORDER_CREATED')) {
	define('OFFER_ORDER_CREATED', "0");
}
if (!defined('OFFER_ORDER_CONFIRMED')) {
	define('OFFER_ORDER_CONFIRMED', "1");
}
if (!defined('OFFER_ORDER_SHIPPED')) {
	define('OFFER_ORDER_SHIPPED', "2");
}
if (!defined('OFFER_ORDER_COMPLETED')) {
	define('OFFER_ORDER_COMPLETED', "3");
}

if (!defined('EVENT_APPOINTMENT_UNCONFIRMED')) {
	define('EVENT_APPOINTMENT_UNCONFIRMED', "0");
}
if (!defined('EVENT_APPOINTMENT_CONFIRMED')) {
	define('EVENT_APPOINTMENT_CONFIRMED', "1");
}
if (!defined('EVENT_APPOINTMENT_CANCELED')) {
	define('EVENT_APPOINTMENT_CANCELED', "2");
}

if (!defined('CREATION_LISTING_NOTIFICATION')) {
	define('CREATION_LISTING_NOTIFICATION', "1");
}

if (!defined('STATISTICAT_EMAIL_NOTIFICATION')) {
	define('STATISTICAT_EMAIL_NOTIFICATION', "2");
}

if (!defined('UPGRADE_LISTING_NOTIFICATION')) {
	define('UPGRADE_LISTING_NOTIFICATION', "3");
}

if (!defined('BUSINESS_VIEW_COUNT')) {
	define('BUSINESS_VIEW_COUNT', '[business_view_count]');
}

if (!defined('MONTHLY_VIEW_COUNT')) {
	define('MONTHLY_VIEW_COUNT', '[monthly_view_count]');
}

if (!defined('MONTHLY_ARTICLE_VIEW_COUNT')) {
	define('MONTHLY_ARTICLE_VIEW_COUNT', '[monthly_article_count]');
}

if (!defined('BUSINESS_RATING')) {
	define('BUSINESS_RATING', '[business_rating]');
}

if (!defined('BUSINESS_REVIEW_NUMBER')) {
	define('BUSINESS_REVIEW_NUMBER', '[business_review_count]');
}

if (!defined('EVENTS_DETAILS')) {
	define('EVENTS_DETAILS', '[events_detail]');
}

if (!defined('OFFER_DETAILS')) {
	define('OFFER_DETAILS', '[offers_detail]');
}

if (!defined('BUSINESS_REVIEW')) {
	define('BUSINESS_REVIEW', '[business_reviews]');
}

if (!defined('ALLOWED_FILE_EXTENSIONS')) {
	define('ALLOWED_FILE_EXTENSIONS', 'zip,rar,tgz,tar.gz,bmp,jpg,jpeg,png,gif,webp,xml,css,csv,xls,xlsx,zip,txt,pdf,doc,docx,mp3,mp4,mov,wma');
}

if (!defined('ALLOWED_FILE_SIZE')) {
	define('ALLOWED_FILE_SIZE', '10MB');
}//add also the units ['B', 'KB', 'MB', 'GB', 'TB', 'PB']

if (!defined('STAFF_WORK_HOURS')) {
	define('STAFF_WORK_HOURS', 0);
}
if (!defined('STAFF_BREAK_HOURS')) {
	define('STAFF_BREAK_HOURS', 1);
}

if (!defined('STAFF_HOURS')) {
	define('STAFF_HOURS', 0);
}
if (!defined('BUSINESS_HOURS')) {
	define('BUSINESS_HOURS', 1);
}

if (!defined('SERVICE_TYPE_LIVE')) {
    define('SERVICE_TYPE_LIVE', "1");
}

if (!defined('SERVICE_TYPE_VIRTUAL')) {
    define('SERVICE_TYPE_VIRTUAL', "2");
}

if (!defined('SERVICE_TYPE_MIXED')) {
    define('SERVICE_TYPE_MIXED', "3");
}

if (!defined('SERVICE_BOOKING_CREATED')) {
	define('SERVICE_BOOKING_CREATED', "0");
}
if (!defined('SERVICE_BOOKING_CONFIRMED')) {
	define('SERVICE_BOOKING_CONFIRMED', "1");
}
if (!defined('SERVICE_BOOKING_CANCELED')) {
	define('SERVICE_BOOKING_CANCELED', "2");
}

if (!defined('STATISTIC_ITEM_BUSINESS')) {
	define('STATISTIC_ITEM_BUSINESS', 1);
}
if (!defined('STATISTIC_ITEM_OFFER')) {
	define('STATISTIC_ITEM_OFFER', 2);
}
if (!defined('STATISTIC_ITEM_EVENT')) {
	define('STATISTIC_ITEM_EVENT', 3);
}

if (!defined('STATISTIC_ITEM_CONFERENCE')) {
	define('STATISTIC_ITEM_CONFERENCE', 4);
}
if (!defined('STATISTIC_ITEM_SPEAKER')) {
	define('STATISTIC_ITEM_SPEAKER', 5);
}
if (!defined('STATISTIC_ITEM_SESSION')) {
	define('STATISTIC_ITEM_SESSION', 6);
}

if (!defined('STATISTIC_ITEM_SESSION_LOCATION')) {
	define('STATISTIC_ITEM_SESSION_LOCATION', 7);
}
if (!defined('STATISTIC_ITEM_VIDEO')) {
	define('STATISTIC_ITEM_VIDEO', 8);
}
if (!defined('STATISTIC_ITEM_ARTICLE')) {
	define('STATISTIC_ITEM_ARTICLE', 9);
}

if (!defined('STATISTIC_TYPE_VIEW')) {
	define('STATISTIC_TYPE_VIEW', 0);
}
if (!defined('STATISTIC_TYPE_CONTACT')) {
	define('STATISTIC_TYPE_CONTACT', 1);
}
if (!defined('STATISTIC_TYPE_SHARE')) {
	define('STATISTIC_TYPE_SHARE', 2);
}
if (!defined('STATISTIC_TYPE_WEBSITE_CLICK')) {
	define('STATISTIC_TYPE_WEBSITE_CLICK', 3);
}
if (!defined('REVIEW_BD_PICTURES_PATH')) {
	define('REVIEW_BD_PICTURES_PATH', '/reviews/');
}

if (!defined('LINK_FOLLOW')) {
	define('LINK_FOLLOW', "link_follow");
}
if (!defined('SERVICES_LIST')) {
	define('SERVICES_LIST', "services_list");
}
if (!defined('TESTIMONIALS')) {
	define('TESTIMONIALS', "testimonials");
}
if (!defined('PROJECTS')) {
	define('PROJECTS', "projects");
}
if (!defined('ZIP_CODES')) {
	define('ZIP_CODES', "zip_codes");
}
if (!defined('AREAS_SERVED')) {
	define('AREAS_SERVED', "areas_served");
}

if (!defined('SEND_EMAIL_ON_CONTACT_BUSINESS')) {
	define('SEND_EMAIL_ON_CONTACT_BUSINESS', "send_email_on_contact_business");
}

if (!defined('MEMBERSHIP_TYPE_1')) {
	define('MEMBERSHIP_TYPE_1', 1);
}
if (!defined('MEMBERSHIP_TYPE_2')) {
	define('MEMBERSHIP_TYPE_2', 2);
}
if (!defined('MEMBERSHIP_TYPE_3')) {
	define('MEMBERSHIP_TYPE_3', 3);
}

if (!defined('OFFER_TYPE_OFFER')) {
	define('OFFER_TYPE_OFFER', 1);
}
if (!defined('OFFER_TYPE_PRODUCT')) {
	define('OFFER_TYPE_PRODUCT', 2);
}
if (!defined('MAX_MEMBERSHIPS')) {
	define('MAX_MEMBERSHIPS', 21);
}

if (!defined('BOOKMARK_TYPE_BUSINESS')) {
	define('BOOKMARK_TYPE_BUSINESS', 1);
}
if (!defined('BOOKMARK_TYPE_OFFER')) {
	define('BOOKMARK_TYPE_OFFER', 2);
}
if (!defined('BOOKMARK_TYPE_EVENT')) {
    define('BOOKMARK_TYPE_EVENT', 3);
}
if (!defined('BOOKMARK_TYPE_CONFERENCE')) {
    define('BOOKMARK_TYPE_CONFERENCE', 4);
}
if (!defined('BOOKMARK_TYPE_SPEAKER')) {
    define('BOOKMARK_TYPE_SPEAKER', 5);
}
if (!defined('BOOKMARK_TYPE_SESSION')) {
    define('BOOKMARK_TYPE_SESSION', 6);
}


if (!defined('MAX_FILENAME_LENGTH')) {
	define('MAX_FILENAME_LENGTH', 120);
}

if (!defined('ORDER_ALPHABETICALLY')) {
	define('ORDER_ALPHABETICALLY', 1);
}

if (!defined('ORDER_BY_ORDER')) {
	define('ORDER_BY_ORDER', 2);
}

if (! defined('ADDRESS_STREET_NUMBER')) {
	define('ADDRESS_STREET_NUMBER', '{street_number}');
}
if (! defined('ADDRESS_ADDRESS')) {
	define('ADDRESS_ADDRESS', '{address}');
}
if (! defined('ADDRESS_AREA')) {
	define('ADDRESS_AREA', '{area}');
}
if (! defined('ADDRESS_CITY')) {
	define('ADDRESS_CITY', '{city}');
}
if (! defined('ADDRESS_POSTAL_CODE')) {
	define('ADDRESS_POSTAL_CODE', '{postal_code}');
}
if (! defined('ADDRESS_REGION')) {
	define('ADDRESS_REGION', '{region}');
}
if (! defined('ADDRESS_PROVINCE')) {
	define('ADDRESS_PROVINCE', '{province}');
}
if (! defined('ADDRESS_COUNTRY')) {
	define('ADDRESS_COUNTRY', '{country}');
}

if (!defined('ITEMS_BATCH_SIZE')) {
	define('ITEMS_BATCH_SIZE', 1000);
}
if (!defined('STATISTIC_ITEMS_BATCH_SIZE')) {
	define('STATISTIC_ITEMS_BATCH_SIZE', 300);
}

if (!defined('LOG_STATISTIC_ITEMS_BATCH_SIZE')) {
	define('LOG_STATISTIC_ITEMS_BATCH_SIZE', 2000);
}

if (!defined('NUMBER_OF_ARCHIVE_CYCLES')) {
	define('NUMBER_OF_ARCHIVE_CYCLES', 50);
}

if (!defined('PAYMENT_TYPE_PACKAGE')) {
	define('PAYMENT_TYPE_PACKAGE', 1);
}
if (!defined('PAYMENT_TYPE_SERVICE')) {
	define('PAYMENT_TYPE_SERVICE', 2);
}
if (!defined('PAYMENT_TYPE_EVENT')) {
	define('PAYMENT_TYPE_EVENT', 3);
}
if (!defined('PAYMENT_TYPE_OFFER')) {
	define('PAYMENT_TYPE_OFFER', 4);
}
if (!defined('PAYMENT_TYPE_CAMPAIGN')) {
	define('PAYMENT_TYPE_CAMPAIGN', 5);
}

if (!defined('MAP_TYPE_OSM')) {
	define('MAP_TYPE_OSM', 3);
}

if (!defined('FILTER_COMPANY_NAME')) {
	define('FILTER_COMPANY_NAME', 1);
}
if (!defined('FILTER_NAME')) {
	define('FILTER_NAME', 2);
}
if (!defined('FILTER_LAST_NAME')) {
	define('FILTER_LAST_NAME', 3);
}
if (!defined('FILTER_EMAIL')) {
	define('FILTER_EMAIL', 4);
}
if (!defined('FILTER_CONTACT_NAME')) {
	define('FILTER_CONTACT_NAME', 5);
}
if (!defined('FILTER_OFFER_NAME')) {
	define('FILTER_OFFER_NAME', 6);
}
if (!defined('FILTER_EVENT_NAME')) {
	define('FILTER_EVENT_NAME', 7);
}

if (!defined('ITEM_TYPE_BUSINESS')) {
	define('ITEM_TYPE_BUSINESS', 1);
}
if (!defined('ITEM_TYPE_OFFER')) {
	define('ITEM_TYPE_OFFER', 2);
}
if (!defined('ITEM_TYPE_EVENT')) {
	define('ITEM_TYPE_EVENT', 3);
}

if (!defined('RESPONSE_STATUS_SUCCESS')) {
	define('RESPONSE_STATUS_SUCCESS', 1);
}
if (!defined('RESPONSE_STATUS_ERROR')) {
	define('RESPONSE_STATUS_ERROR', 2);
}
if (!defined('RESPONSE_STATUS_INVALID_TOKEN')) {
	define('RESPONSE_STATUS_INVALID_TOKEN', 3);
}
if (!defined('RESPONSE_STATUS_INVALID_CREDENTIALS')) {
	define('RESPONSE_STATUS_INVALID_CREDENTIALS', 4);
}
if (!defined('RESPONSE_STATUS_INVALID_REQUEST')) {
	define('RESPONSE_STATUS_INVALID_REQUEST', 5);
}

if (!defined('NOTIFICATION_TYPE_GENERAL')) {
	define('NOTIFICATION_TYPE_GENERAL', 'jb_notification');
}
if (!defined('NOTIFICATION_TYPE_BOOKMARK')) {
	define('NOTIFICATION_TYPE_BOOKMARK', 'jb_bookmark');
}
if (!defined('NOTIFICATION_TYPE_REVIEW')) {
	define('NOTIFICATION_TYPE_REVIEW', 'jb_review');
}
if (!defined('NOTIFICATION_TYPE_NEW_BOOKMARK_ITEM')) {
	define('NOTIFICATION_TYPE_NEW_BOOKMARK_ITEM', 'jb_new_bookmark_item');
}
if (!defined('NOTIFICATION_TYPE_MESSAGE')) {
	define('NOTIFICATION_TYPE_MESSAGE', 'jb_message');
}

if (!defined('NOTIFICATION_TOPIC_GENERAL')) {
	define('NOTIFICATION_TOPIC_GENERAL', "general");
}

if (!defined('MAP_TYPE_GOOGLE')) {
	define('MAP_TYPE_GOOGLE', 1);
}
if (!defined('MAP_TYPE_BING')) {
	define('MAP_TYPE_BING', 2);
}
if (!defined('MAP_TYPE_OSM')) {
	define('MAP_TYPE_OSM', 3);
}

if (!defined('GET_DATA_FROM_YELP')) {
	define('GET_DATA_FROM_YELP', 0);
}


if (!defined('STATISTIC_TYPE_ARTICLE_CLICK')) {
	define('STATISTIC_TYPE_ARTICLE_CLICK', 4);
}

if (!defined('DISCOUNT_TYPE_VALUE')) {
	define('DISCOUNT_TYPE_VALUE', 1);
}
if (!defined('DISCOUNT_TYPE_PERCENT')) {
	define('DISCOUNT_TYPE_PERCENT', 2);
}

if (!defined('TAX_TYPE_VALUE')) {
	define('TAX_TYPE_VALUE', 1);
}
if (!defined('TAX_TYPE_PERCENT')) {
	define('TAX_TYPE_PERCENT', 2);
}

if (!defined('DEFAULT_ATTRIBUTE_TYPE_LISTING')) {
	define('DEFAULT_ATTRIBUTE_TYPE_LISTING', 1);
}
if (!defined('DEFAULT_ATTRIBUTE_TYPE_OFFER')) {
	define('DEFAULT_ATTRIBUTE_TYPE_OFFER', 2);
}
if (!defined('DEFAULT_ATTRIBUTE_TYPE_EVENT')) {
	define('DEFAULT_ATTRIBUTE_TYPE_EVENT', 3);
}

if (!defined('DEFAULT_ATTRIBUTE_TYPE_VIDEO')) {
	define('DEFAULT_ATTRIBUTE_TYPE_VIDEO', 4);
}

if (!defined('DIRECTORY_APP_UPDATE')) {
	define('DIRECTORY_APP_UPDATE', 2);
}
if (!defined('DIRECTORY_APP_INSTALLED')) {
	define('DIRECTORY_APP_INSTALLED', 1);
}
if (!defined('DIRECTORY_APP_UNINSTALLED')) {
	define('DIRECTORY_APP_UNINSTALLED', 0);
}

if (!defined('LENGTH_ID_BOOKING')) {
	define('LENGTH_ID_BOOKING', 5);
}

if (!defined('JBD_PACKAGES')) {
	define('JBD_PACKAGES', -1);
}
if (!defined('JBD_APP_APPOINTMENTS')) {
	define('JBD_APP_APPOINTMENTS', 1);
}
if (!defined('JBD_APP_SELL_OFFERS')) {
	define('JBD_APP_SELL_OFFERS', 5);
}
if (!defined('JBD_APP_EVENT_BOOKINGS')) {
	define('JBD_APP_EVENT_BOOKINGS', 2);
}
if (!defined('JBD_APP_EVENT_APPOINTMENTS')) {
	define('JBD_APP_EVENT_APPOINTMENTS', 3);
}
if (!defined('JBD_APP_CAMPAIGNS')) {
	define('JBD_APP_CAMPAIGNS', 22);
}
if (!defined('JBD_APP_QUOTE_REQUESTS')) {
	define('JBD_APP_QUOTE_REQUESTS', 25);
}

if (!defined('JBD_APP_TRIPS')) {
	define('JBD_APP_TRIPS', 28);
}

if (!defined('JBD_APP_STRIPE')) {
	define('JBD_APP_STRIPE', 8);
}
if (!defined('JBD_APP_STRIPE_SUBSCRIPTIONS')) {
	define('JBD_APP_STRIPE_SUBSCRIPTIONS', 9);
}
if (!defined('JBD_APP_PAYPAL_SUBSCRIPTIONS')) {
	define('JBD_APP_PAYPAL_SUBSCRIPTIONS', 7);
}
if (!defined('JBD_APP_PAYFAST_SUBSCRIPTIONS')) {
	define('JBD_APP_PAYFAST_SUBSCRIPTIONS', 12);
}
if (!defined('JBD_APP_AUTHORIZE')) {
	define('JBD_APP_AUTHORIZE', 23);
}
if (!defined('JBD_APP_AUTHORIZE_SUBSCRIPTIONS')) {
	define('JBD_APP_AUTHORIZE_SUBSCRIPTIONS', 24);
}

if (!defined('JBD_APP_MERCADO_PAGO')) {
    define('JBD_APP_MERCADO_PAGO', 26);
}

if (!defined('JBD_APP_ELASTIC_SEARCH')) {
    define('JBD_APP_ELASTIC_SEARCH', 27);
}

if (!defined('JBD_APP_VIDEOS')) {
	define('JBD_APP_VIDEOS', 29);
}

if (!defined('JBD_APP_MOLLIE')) {
	define('JBD_APP_MOLLIE', 30);
}

if (!defined('JBD_APP_MOLLIE_SUBSCRIPTIONS')) {
	define('JBD_APP_MOLLIE_SUBSCRIPTIONS', 31);
}

if (!defined('JBD_APP_CARDLINK')) {
	define('JBD_APP_CARDLINK', 32);
}

if (!defined('JBD_APP_CARDLINK_SUBSCRIPTIONS')) {
	define('JBD_APP_CARDLINK_SUBSCRIPTIONS', 33);
}

if (!defined('JBD_APP_RAZORPAY')) {
	define('JBD_APP_RAZORPAY', 34);
}

if (!defined('JBD_APP_ASAAS')) {
	define('JBD_APP_ASAAS', 35);
}

if (!defined('EVENT_APPOINTMENT')) {
	define('EVENT_APPOINTMENT', "event_appointment");
}
if (!defined('EVENT_BOOKINGS')) {
	define('EVENT_BOOKINGS', "event_bookings");
}
if (!defined('EVENT_RECURRING')) {
	define('EVENT_RECURRING', "event_recurring");
}
if (!defined('SELL_OFFERS')) {
	define('SELL_OFFERS', "sell_offers");
}
if (!defined('ANNOUNCEMENTS')) {
	define('ANNOUNCEMENTS', "announcements");
}
if (!defined('REQUEST_QUOTES_FEATURE')) {
	define('REQUEST_QUOTES_FEATURE', "request_quotes");
}

if (!defined('TEAM_FEATURE')) {
	define('TEAM_FEATURE', "team_feature");
}

if (!defined('SHIPPING_METHOD_TRANSLATION')) {
	define('SHIPPING_METHOD_TRANSLATION', 27);
}

if (!defined('SUGGESTION_TYPE_BUSINESS')) {
	define('SUGGESTION_TYPE_BUSINESS', 1);
}
if (!defined('SUGGESTION_TYPE_OFFER')) {
	define('SUGGESTION_TYPE_OFFER', 2);
}
if (!defined('SUGGESTION_TYPE_EVENT')) {
	define('SUGGESTION_TYPE_EVENT', 3);
}
if (!defined('SUGGESTION_TYPE_CATEGORY')) {
	define('SUGGESTION_TYPE_CATEGORY', 4);
}

if (!defined('TYPE_DIRECTORY_APP')) {
	define('TYPE_DIRECTORY_APP', 1);
}
if (!defined('TYPE_DIRECTORY_EXTENSION')) {
	define('TYPE_DIRECTORY_EXTENSION', 2);
}

if (!defined('CAMPAIGN_STATUS_NOT_PAID')) {
	define('CAMPAIGN_STATUS_NOT_PAID', 0);
}
if (!defined('CAMPAIGN_STATUS_PAID')) {
	define('CAMPAIGN_STATUS_PAID', 1);
}
if (!defined('CAMPAIGN_STATUS_EXPIRED')) {
	define('CAMPAIGN_STATUS_EXPIRED', 2);
}

if (!defined('MESSAGE_TYPE_BUSINESS')) {
	define('MESSAGE_TYPE_BUSINESS', 1);
}
if (!defined('MESSAGE_TYPE_OFFER')) {
	define('MESSAGE_TYPE_OFFER', 2);
}
if (!defined('MESSAGE_TYPE_EVENT')) {
	define('MESSAGE_TYPE_EVENT', 3);
}

if (!defined('PLACEHOLDER_COMPANY_NAME')) {
	define('PLACEHOLDER_COMPANY_NAME', "{COMPANY_NAME}");
}
if (!defined('PLACEHOLDER_ADDRESS')) {
	define('PLACEHOLDER_ADDRESS', "{ADDRESS}");
}
if (!defined('PLACEHOLDER_EMAIL')) {
	define('PLACEHOLDER_EMAIL', "{EMAIL}");
}
if (!defined('PLACEHOLDER_PHONE')) {
	define('PLACEHOLDER_PHONE', "{PHONE}");
}

if (!defined('OAUTH_PROVIDER_FACEBOOK')) {
	define('OAUTH_PROVIDER_FACEBOOK', 1);
}
if (!defined('OAUTH_PROVIDER_GOOGLE')) {
	define('OAUTH_PROVIDER_GOOGLE', 2);
}
if (!defined('OAUTH_PROVIDER_LINKEDIN')) {
	define('OAUTH_PROVIDER_LINKEDIN', 3);
}

if (!defined('EMAIL_REVIEW_LINK_OFFER')) {
	define('EMAIL_REVIEW_LINK_OFFER', '[review_offer_link]');
}

if (!defined('QUESTION_TYPE_RADIO')) {
	define('QUESTION_TYPE_RADIO', 1);
}
if (!defined('QUESTION_TYPE_CHECKBOX')) {
	define('QUESTION_TYPE_CHECKBOX', 2);
}
if (!defined('QUESTION_TYPE_INPUT')) {
	define('QUESTION_TYPE_INPUT', 3);
}

if (!defined('REQUEST_QUOTE_STATUS_OPEN')) {
	define('REQUEST_QUOTE_STATUS_OPEN', 1);
}
if (!defined('REQUEST_QUOTE_STATUS_CLOSED')) {
	define('REQUEST_QUOTE_STATUS_CLOSED', 2);
}
if (!defined('REQUEST_QUOTE_STATUS_EXPIRED')) {
	define('REQUEST_QUOTE_STATUS_EXPIRED', 3);
}

if (!defined('REQUEST_QUOTE_MESSAGE_UNREAD')) {
	define('REQUEST_QUOTE_MESSAGE_UNREAD', 0);
}

if (!defined('REQUEST_QUOTE_MESSAGE_READ')) {
	define('REQUEST_QUOTE_MESSAGE_READ', 1);
}

if (!defined('REQUEST_QUOTE_REPLY_NOT_HIRED')) {
	define('REQUEST_QUOTE_REPLY_NOT_HIRED', 1);
}
if (!defined('REQUEST_QUOTE_REPLY_HIRED')) {
	define('REQUEST_QUOTE_REPLY_HIRED', 2);
}

if (!defined('STATISTIC_ORDER_BY_DAY')) {
	define('STATISTIC_ORDER_BY_DAY', 1);
}
if (!defined('STATISTIC_ORDER_BY_MONTH')) {
	define('STATISTIC_ORDER_BY_MONTH', 2);
}
if (!defined('STATISTIC_ORDER_BY_YEAR')) {
	define('STATISTIC_ORDER_BY_YEAR', 3);
}

if (!defined('SUBSCRIPTION_STATUS_INACTIVE')) {
	define('SUBSCRIPTION_STATUS_INACTIVE', 0);
}
if (!defined('SUBSCRIPTION_STATUS_ACTIVE')) {
	define('SUBSCRIPTION_STATUS_ACTIVE', 1);
}
if (!defined('SUBSCRIPTION_STATUS_CANCELED')) {
	define('SUBSCRIPTION_STATUS_CANCELED', 2);
}


if (!defined('SEARCH_LOG_KEYWORD')) {
	define('SEARCH_LOG_KEYWORD', 1);
}
if (!defined('SEARCH_LOG_CATEGORY')) {
	define('SEARCH_LOG_CATEGORY', 2);
}
if (!defined('SEARCH_LOG_TYPE')) {
	define('SEARCH_LOG_TYPE', 3);
}
if (!defined('SEARCH_LOG_LOCATION')) {
	define('SEARCH_LOG_LOCATION', 4);
}
if (!defined('SEARCH_LOG_COUNTRY')) {
	define('SEARCH_LOG_COUNTRY', 5);
}
if (!defined('SEARCH_LOG_PROVINCE')) {
	define('SEARCH_LOG_PROVINCE', 6);
}
if (!defined('SEARCH_LOG_REGION')) {
	define('SEARCH_LOG_REGION', 7);
}
if (!defined('SEARCH_LOG_CITY')) {
	define('SEARCH_LOG_CITY', 8);
}
if (!defined('SEARCH_LOG_CUSTOM_ATTRIBUTE')) {
	define('SEARCH_LOG_CUSTOM_ATTRIBUTE', 9);
}
if (!defined('SEARCH_LOG_MIN_PRICE')) {
	define('SEARCH_LOG_MIN_PRICE', 10);
}
if (!defined('SEARCH_LOG_MAX_PRICE')) {
	define('SEARCH_LOG_MAX_PRICE', 11);
}
if (!defined('SEARCH_LOG_START_DATE')) {
	define('SEARCH_LOG_START_DATE', 12);
}
if (!defined('SEARCH_LOG_END_DATE')) {
	define('SEARCH_LOG_END_DATE', 13);
}

if (! defined('DEFAULT_PAYMENT_PROCESSOR')) {
	define('DEFAULT_PAYMENT_PROCESSOR', - 1);
}
	
if (!defined('SEARCH_LOG_TYPE_LISTING')) {
	define('SEARCH_LOG_TYPE_LISTING', 1);
}
if (!defined('SEARCH_LOG_TYPE_OFFER')) {
	define('SEARCH_LOG_TYPE_OFFER', 2);
}
if (!defined('SEARCH_LOG_TYPE_EVENT')) {
	define('SEARCH_LOG_TYPE_EVENT', 3);
}
if (!defined('SEARCH_LOG_TYPE_VIDEO')) {
	define('SEARCH_LOG_TYPE_VIDEO', 4);
}

if (!defined('SEARCH_LOG_TYPE_TRIP')) {
	define('SEARCH_LOG_TYPE_TRIP', 5);
}

if (!defined('CREATE_LISTINGS_EMAIL_NOTIFICATION_TYPE')) {
	define('CREATE_LISTINGS_EMAIL_NOTIFICATION_TYPE', 0);
}
if (!defined('STATISTICS_EMAIL_NOTIFICATION_TYPE')) {
	define('STATISTICS_EMAIL_NOTIFICATION_TYPE', 1);
}
if (!defined('UPGRADE_LISTING_EMAIL_NOTIFICATION_TYPE')) {
	define('UPGRADE_LISTING_EMAIL_NOTIFICATION_TYPE', 2);
}

if (!defined('TEXT_LENGTH_LIST_VIEW')) {
	define('TEXT_LENGTH_LIST_VIEW', 250);
}

if (!defined('MAXIMUM_OFFER_QUANTITY_SELLING')) {
	define('MAXIMUM_OFFER_QUANTITY_SELLING', 200);
}

if (!defined('TOTAL_PENDING_ITEMS_DISPLAYED')) {
	define('TOTAL_PENDING_ITEMS_DISPLAYED', 5);
}

if (!defined('TOTAL_PENDING_ITEMS_DISPLAYED')) {
	define('TOTAL_PENDING_ITEMS_DISPLAYED', 5);
}

if (!defined('OFFER_STATUS_NEEDS_APPROVAL')) {
	define('OFFER_STATUS_NEEDS_APPROVAL', 0);
}

if (!defined('OFFER_STATUS_DISAPPROVED')) {
	define('OFFER_STATUS_DISAPPROVED', '-1');
}

if (!defined('OFFER_STATUS_APPROVED')) {
	define('OFFER_STATUS_APPROVED', 1);
}

if (!defined('EVENT_STATUS_NEEDS_APPROVAL')) {
	define('EVENT_STATUS_NEEDS_APPROVAL', 0);
}

if (!defined('EVENT_STATUS_DISAPPROVED')) {
	define('EVENT_STATUS_DISAPPROVED', '-1');
}

if (!defined('EVENT_STATUS_APPROVED')) {
	define('EVENT_STATUS_APPROVED', 1);
}

if (!defined('OFFER_SELLING_DISABLED')) {
	define('OFFER_SELLING_DISABLED', 0);
}
if (!defined('OFFER_SELLING_REGULAR')) {
	define('OFFER_SELLING_REGULAR', 1);
}
if (!defined('OFFER_SELLING_COUPON')) {
	define('OFFER_SELLING_COUPON', 2);
}

if (!defined('MODE_LIVE')) {
    define('MODE_LIVE', "1");
}
if (!defined('MODE_VIRTUAL')) {
    define('MODE_VIRTUAL', "2");
}
if (!defined('MODE_MIXED')) {
    define('MODE_MIXED', "3");
}


if (!defined('INDEX_TYPE_LISTING')) {
    define('INDEX_TYPE_LISTING', "listing");
}

if (!defined('INDEX_TYPE_OFFER')) {
    define('INDEX_TYPE_OFFER', "offer");
}

if (!defined('INDEX_TYPE_EVENT')) {
    define('INDEX_TYPE_EVENT', "event");
}

if (!defined('INDEX_DATE_LISTINGS')) {
    define('INDEX_DATE_LISTINGS', "index_date_listings");
}

if (!defined('INDEXED_ID_LISTINGS')) {
    define('INDEXED_ID_LISTINGS', "indexed_id_listings");
}

if (!defined('INDEX_DATE_OFFERS')) {
    define('INDEX_DATE_OFFERS', "index_date_offers");
}

if (!defined('INDEXED_ID_OFFERS')) {
    define('INDEXED_ID_OFFERS', "indexed_id_offers");
}

if (!defined('INDEX_DATE_EVENTS')) {
    define('INDEX_DATE_EVENTS', "index_date_listings");
}

if (!defined('INDEXED_ID_EVENTS')) {
    define('INDEXED_ID_EVENTS', "indexed_id_events");
}

if( !defined( 'MEMBER_TYPE_LEADERSHIP')){
	define('MEMBER_TYPE_LEADERSHIP',1);
}

if( !defined( 'MEMBER_TYPE_TEAM')){
	define('MEMBER_TYPE_TEAM',2);
}

if( !defined('SESSION_USER_REGISTERED')){
	define('SESSION_USER_REGISTERED',1);
}

if( !defined('SESSION_USER_UNREGISTERED')){
	define('SESSION_USER_UNREGISTERED',0);
}

if( !defined('SESSION_USER_JOINED')){
	define('SESSION_USER_JOINED',1);
}

if( !defined('REVIEWS_LIMIT')){
	define('REVIEWS_LIMIT',10);
}

if( !defined('PACKAGE_TYPE_BUSINESS')){
	define('PACKAGE_TYPE_BUSINESS',1);
}

if( !defined('PACKAGE_TYPE_USER')){
	define('PACKAGE_TYPE_USER',2);
}
if( !defined('QUOTE_LOCATION_SEARCH_EXACT')){
	define('QUOTE_LOCATION_SEARCH_EXACT',2);
}

if( !defined('QUOTE_LOCATION_SEARCH_FLEXIBLE')){
	define('QUOTE_LOCATION_SEARCH_FLEXIBLE',1);
}
if( !defined('COMPANY_OPEN_BY_TIMETABLE')){
	define('COMPANY_OPEN_BY_TIMETABLE',0);
}

if( !defined('COMPANY_ALWAYS_OPEN')){
	define('COMPANY_ALWAYS_OPEN',1);
}

if( !defined('COMPANY_TEMP_CLOSED')){
	define('COMPANY_TEMP_CLOSED',2);
}

if( !defined('COMPANY_OPEN_BY_APPOINTMENT')){
	define('COMPANY_OPEN_BY_APPOINTMENT',3);
}

if( !defined('COMPANY_SEASON_CLOSED')){
	define('COMPANY_SEASON_CLOSED',4);
}

if( !defined('COMPANY_PERMANENTLY_CLOSED')){
	define('COMPANY_PERMANENTLY_CLOSED',5);
}

if (!defined('EMAIL_SERVICE_DATE')) {
	define('EMAIL_SERVICE_DATE', '[service_date]');
}

if (!defined('EMAIL_QUOTE_USER_NAME')) {
	define('EMAIL_QUOTE_USER_NAME', '[quote_user_name]');
}

if (!defined('TRIPS')) {
	define('TRIPS', "trips");
}

if (!defined('TRIP_BOOKING_CREATED')) {
	define('TRIP_BOOKING_CREATED', 0);
}

if (!defined('TRIP_BOOKING_CONFIRMED')) {
	define('TRIP_BOOKING_CONFIRMED', 1);
}

if (!defined('TRIP_BOOKING_CANCELED')) {
	define('TRIP_BOOKING_CANCELED', -1);
}

//Interval in minutes
if (!defined('TRIP_BOOKING_DELETE_INTERVAL_MINS')) {
	define('TRIP_BOOKING_DELETE_INTERVAL_MINS', 15);
}

if (!defined('NOTIFICATION_TYPE_USERGROUP')) {
	define('NOTIFICATION_TYPE_USERGROUP', 'jb_general_user');
}

if (!defined('NOTIFICATION_TYPE_TOPIC')) {
	define('NOTIFICATION_TYPE_TOPIC', 'jb_general_topic');
}


if (!defined('SCHEDULED_NOTIFICATION_TYPE_INPECTION_EXPIRATION')) {
	define('SCHEDULED_NOTIFICATION_TYPE_INPECTION_EXPIRATION', 'jb_inspection_exp');
}

if (!defined('SCHEDULED_NOTIFICATION_TYPE_INSURANCE_EXPIRATION')) {
	define('SCHEDULED_NOTIFICATION_TYPE_INSURANCE_EXPIRATION', 'jb_insurance_exp');
}

if (!defined('NOTIFICATION_STATUS_INACTIVE')) {
	define('NOTIFICATION_STATUS_INACTIVE', 0);
}

if (!defined('NOTIFICATION_STATUS_ACTIVE')) {
	define('NOTIFICATION_STATUS_ACTIVE', 1);
}

