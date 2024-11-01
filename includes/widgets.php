<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-listings/wp-businessdirectory-search-listings.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-listings/wp-businessdirectory-search-listings.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-events/wp-businessdirectory-search-events.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-events/wp-businessdirectory-search-events.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-offers/wp-businessdirectory-search-offers.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-offers/wp-businessdirectory-search-offers.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-listings/wp-businessdirectory-listings.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-listings/wp-businessdirectory-listings.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-events/wp-businessdirectory-events.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-events/wp-businessdirectory-events.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-offers/wp-businessdirectory-offers.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-offers/wp-businessdirectory-offers.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-users/wp-businessdirectory-users.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-users/wp-businessdirectory-users.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories/wp-businessdirectory-categories.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories/wp-businessdirectory-categories.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories-events/wp-businessdirectory-categories-events.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories-events/wp-businessdirectory-categories-events.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories-offers/wp-businessdirectory-categories-offers.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories-offers/wp-businessdirectory-categories-offers.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-announcements/wp-businessdirectory-announcements.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-announcements/wp-businessdirectory-announcements.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-listing-videos/wp-businessdirectory-listing-videos.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-listing-videos/wp-businessdirectory-listing-videos.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-maps/wp-businessdirectory-maps.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-maps/wp-businessdirectory-maps.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-packages/wp-businessdirectory-packages.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-packages/wp-businessdirectory-packages.php' );
}
if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-reviews/wp-businessdirectory-reviews.php')){
    include_once( dirname( __FILE__ ) . '/widgets/wp-businessdirectory-reviews/wp-businessdirectory-reviews.php' );
}

/**
 * Register Widgets.
 *
 * @since 1.0.0
 */
function wpbd_wp_businessdirectory_register_widgets() {
    if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-listings/wp-businessdirectory-search-listings.php')){
	   register_widget( 'WP_BusinessDirectory_Search_Listings_Widget' );
    }
    if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-events/wp-businessdirectory-search-events.php')){
        register_widget( 'WP_BusinessDirectory_Search_Events_Widget' );
    }
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-search-offers/wp-businessdirectory-search-offers.php')){
        register_widget( 'WP_BusinessDirectory_Search_Offers_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-listings/wp-businessdirectory-listings.php')){
        register_widget( 'WP_BusinessDirectory_Listings_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-events/wp-businessdirectory-events.php')){
        register_widget( 'WP_BusinessDirectory_Events_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-offers/wp-businessdirectory-offers.php')){
        register_widget( 'WP_BusinessDirectory_Offers_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-users/wp-businessdirectory-users.php')){
        register_widget( 'WP_BusinessDirectory_Users_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories/wp-businessdirectory-categories.php')){
        register_widget( 'WP_BusinessDirectory_Categories_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories-events/wp-businessdirectory-categories-events.php')){
        register_widget( 'WP_BusinessDirectory_Categories_Events_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-categories-offers/wp-businessdirectory-categories-offers.php')){
        register_widget( 'WP_BusinessDirectory_Categories_Offers_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-announcements/wp-businessdirectory-announcements.php')){
        register_widget( 'WP_BusinessDirectory_Announcements_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-listing-videos/wp-businessdirectory-listing-videos.php')){
        register_widget( 'WP_BusinessDirectory_Listing_Videos_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-maps/wp-businessdirectory-maps.php')){
        register_widget( 'WP_BusinessDirectory_Maps_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-packages/wp-businessdirectory-packages.php')){
        register_widget( 'WP_BusinessDirectory_Packages_Widget' );
	}
	if(file_exists(dirname( __FILE__ ) . '/widgets/wp-businessdirectory-reviews/wp-businessdirectory-reviews.php')){
        register_widget( 'WP_BusinessDirectory_Reviews_Widget' );
	}

	
    register_sidebar( array(
		'name'          => 'Directory results search ',
		'id'            => 'search_1',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => 'WPBD Listigs Top',
		'id'            => 'wpbd-listings-top',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => 'WPBD Listigs Bottom',
		'id'            => 'wpbd-listings-bottom',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => 'WPBD Listigs Center',
		'id'            => 'wpbd-listings-center',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => 'WPBD Listig Details',
		'id'            => 'wpbd-listing-details',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/offers.php')) {
		register_sidebar( array(
			'name'          => 'WPBD Offers Top',
			'id'            => 'wpbd-offers-top',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
		register_sidebar( array(
			'name'          => 'WPBD Offers Botton',
			'id'            => 'wpbd-offers-bottom',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
		register_sidebar( array(
			'name'          => 'WPBD Offers Center',
			'id'            => 'wpbd-offers-center',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
		register_sidebar( array(
			'name'          => 'WPBD Offer Details',
			'id'            => 'wpbd-offer-details',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
	}

	if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/controllers/events.php')) {
		register_sidebar( array(
			'name'          => 'WPBD Events Top',
			'id'            => 'wpbd-events-top',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
		register_sidebar( array(
			'name'          => 'WPBD Events Bottom',
			'id'            => 'wpbd-events-bottom',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
		register_sidebar( array(
			'name'          => 'WPBD Events Center',
			'id'            => 'wpbd-events-center',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
		register_sidebar( array(
			'name'          => 'WPBD Event Details',
			'id'            => 'wpbd-event-details',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		) );
	}
}

add_action( 'widgets_init', 'wpbd_wp_businessdirectory_register_widgets' );