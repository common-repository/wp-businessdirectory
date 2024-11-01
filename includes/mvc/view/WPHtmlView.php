<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace MVC\View;
use MVC\Document\Renderer\Html\MessageRenderer;
use MVC\Factory;
require_once WP_BUSINESSDIRECTORY_PATH.'includes/mvc/document/Renderer/Html/MessageRenderer.php';

defined ( 'JPATH_PLATFORM' ) or die ();

/**
 * Base class for a WordPress View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since 1.0.0
 */
class WPHtmlView extends HtmlView {
	
    var $page_title;
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    public function display($tpl = null) {
        
        $document = Factory::getDocument();
        $render = new MessageRenderer($document);
        $messages = $render->render("WP-JBD");
    
        $content = "";
        $content .= $messages;
        $content .= $this->loadTemplate($tpl);
        
        $this->_output = $content;

        add_filter('template_redirect',array($this,'createPage'), 10 , 2 );
        
        add_filter( 'page_template', function(){
            $page_template = WP_BUSINESSDIRECTORY_PATH . '/templates/template.php';
            require $page_template;
            die;
        });

        add_action( 'wp_head', array( $this, 'addMetaData' ) );
    }
    
    
    function addMetaData(){
        global $wp_query;
        
        echo $wp_query->metaDataDir;
        echo $wp_query->facebookMetadata;
    }
    
    public function createPage(){
       
        global $wp,$wp_query;
        
        $post_id = 11111;
        
        //create a fake post intance
        $post = new \stdClass;
        // fill $post with everything a page in the database would have
        $post->ID = $post_id;
        $post->post_author = 1;
        $post->post_date = current_time('mysql');
        $post->post_date_gmt =  current_time('mysql', $gmt = 1);
        $post->post_title = $wp_query->pageTitle;
        $post->post_name = $this->_name; // slug
        $post->post_content = $this->_output;
        $post->post_status = 'publish';
        $post->post_type = 'page';
        $post->post_excerpt = '';
        $post->filter = 'raw'; //important
       
        $post->post_password = '';
        $post->to_ping = '';
        $post->pinged = '';
        $post->modified = $post->post_date;
        $post->modified_gmt = $post->post_date_gmt;
        $post->post_content_filtered = '';
        $post->post_parent = 0;
        $post->guid = get_home_url('/' . $post->post_name); // use url instead?
        $post->menu_order = 0;
       
        $post->post_mime_type = '';
        $post->comment_status = 'closed';
        $post->ping_status = 'closed';
        $post->comment_count = 0;
        $post->ancestors = array(); // 3.6
        
        $wp_post = new \WP_Post( $post );
        wp_cache_add($post_id, $wp_post, 'posts' );
        
        unset($wp_query->query['error']);
        $wp->query = array();
        $wp_query->query_vars['error'] = '';
        $wp_query->is_404 = FALSE;
        
        
        // Update the main query
        $wp_query->post = $wp_post;
        $wp_query->posts = array( $wp_post );
        $wp_query->queried_object = $wp_post;
        $wp_query->queried_object_id = $post_id;
        
        $wp_query->found_posts = 1;
        $wp_query->post_count = 1;
        $wp_query->max_num_pages = 1;
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_single = false;
        $wp_query->is_attachment = false;
        $wp_query->is_archive = false;
        $wp_query->is_category = false;
        $wp_query->is_tag = false;
        $wp_query->is_tax = false;
        $wp_query->is_author = false;
        $wp_query->is_date = false;
        $wp_query->is_year = false;
        $wp_query->is_month = false;
        $wp_query->is_day = false;
        $wp_query->is_time = false;
        $wp_query->is_search = false;
        $wp_query->is_feed = false;
        $wp_query->is_comment_feed = false;
        $wp_query->is_trackback = false;
        $wp_query->is_home = false;
        $wp_query->is_embed = false;
        $wp_query->is_404 = false;
        $wp_query->is_paged = false;
        $wp_query->is_admin = false;
        $wp_query->is_preview = false;
        $wp_query->is_robots = false;
        $wp_query->is_posts_page = false;
        $wp_query->is_post_type_archive = false;
        
        $wp_query->comment_count = 0;
        $wp_query->current_comment = null;
        
        $GLOBALS['wp_query'] = $wp_query;
        $wp->register_globals();
        
        return array($post);
    }
    
    public function setPageTitle($title){
        $this->page_title = $title;
    }
}