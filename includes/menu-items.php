<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

//JBusinessUtil::loadSiteLanguage();

/**
 * Add menu meta box
 *
 * @param object $object The meta box object
 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
 */
function wpbd_add_business_listings_menu_meta_box( $object ) {
    add_meta_box( 'business_listings-menu-metabox', JText::_("LNG_BUSINESS_LISTINGS"), 'wpbd_business_listings_menu_meta_box', 'nav-menus', 'side', 'default' );
    return $object;
}
add_filter( 'nav_menu_meta_box_object', 'wpbd_add_business_listings_menu_meta_box');


function wpbd_add_offers_menu_meta_box( $object ) {
    add_meta_box( 'offers-menu-metabox', JText::_("LNG_OFFERS"), 'wpbd_offers_menu_meta_box', 'nav-menus', 'side', 'default' );
    return $object;
}
add_filter( 'nav_menu_meta_box_object', 'wpbd_add_offers_menu_meta_box');


function wpbd_add_events_menu_meta_box( $object ) {
    add_meta_box( 'events-menu-metabox', JText::_("LNG_EVENTS"), 'wpbd_events_menu_meta_box', 'nav-menus', 'side', 'default' );
    return $object;
}
add_filter( 'nav_menu_meta_box_object', 'wpbd_add_events_menu_meta_box');



function wpbd_add_packages_menu_meta_box( $object ) {
    add_meta_box( 'packages-menu-metabox', JText::_("LNG_PACKAGES"), 'wpbd_packages_menu_meta_box', 'nav-menus', 'side', 'default' );
    return $object;
}
add_filter( 'nav_menu_meta_box_object', 'wpbd_add_packages_menu_meta_box');



function wpbd_add_control_panel_menu_meta_box( $object ) {
    add_meta_box( 'control-panel-menu-metabox', JText::_("LNG_CONTROL_PANEL"), 'wpbd_control_panel_menu_meta_box', 'nav-menus', 'side', 'default' );
    return $object;
}
add_filter( 'nav_menu_meta_box_object', 'wpbd_add_control_panel_menu_meta_box');


/**
 * Displays a metabox for jbd.
 *
 */
function wpbd_business_listings_menu_meta_box(){
    global $nav_menu_selected_id;
    $walker = new Walker_Nav_Menu_Checklist();
    
    require_once( BD_HELPERS_PATH.'/category_lib.php');
    $categoryService = new JBusinessDirectorCategoryLib();
    $categories = $categoryService->getAllCategories(CATEGORY_TYPE_BUSINESS);
    $categories = $categoryService->processCategories($categories);
    
    $current_tab = 'all';
  
    $categoriesMenus = array();
    if ( isset( $_REQUEST['listing-category-tab'] ) && 'admins' == $_REQUEST['listing-category-tab'] ) {
        $current_tab = 'admins';
    }elseif ( isset( $_REQUEST['listing-category-tab'] ) && 'all' == $_REQUEST['listing-category-tab'] ) {
        $current_tab = 'all';
    }
    
    /* set values to required item properties */
    foreach ( $categories as &$category ) {
        $category = $category[0];
        $catMenuItem =  new stdClass();
        $catMenuItem->ID = $category->id;
        $catMenuItem->db_id = $category->id;
        $catMenuItem->menu_item_parent= $category->parent_id;
        $catMenuItem->classes = array();
        $catMenuItem->type = 'custom';
        $catMenuItem->object_id = $category->id;
        $catMenuItem->title = $category->name;
        $catMenuItem->object = 'custom';
        $catMenuItem->post_parent = $category->parent_id;
        $catMenuItem->url =  JBusinessUtil::getCategoryLink($category->id, $category->alias);
        $catMenuItem->attr_title = $category->name;
        $catMenuItem->target = '';
        $catMenuItem->description = '';
        $catMenuItem->xfn = '';
        
        $categoriesMenus[] = $catMenuItem;
     
    }
    
    $removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
    ?>
	<div id="listing-category" class="categorydiv">
		<ul id="listing-category-tabs" class="listing-category-tabs add-menu-item-tabs">
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
				<a class="nav-tab-link" data-type="tabs-panel-listing-category-all" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'listing-category-tab', 'all', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-listing-category-all">
					<?php _e( 'View All' ); ?>
				</a>
			</li><!-- /.tabs -->

		</ul>
		
		<div id="tabs-panel-listing-category-all" class="tabs-panel tabs-panel-view-all <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
			<ul id="listing-category-checklist-all" class="categorychecklist form-no-clear">
			<?php
			echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $categoriesMenus), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php echo esc_url( add_query_arg( array( 'listing-category-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#listing-category" class="select-all"><?php _e('Select All'); ?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-listing-category-menu-item" id="submit-listing-category" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.categorydiv -->
<?php
}



/**
 * Displays a metabox for jbd.
 *
 */
function wpbd_offers_menu_meta_box(){
    global $nav_menu_selected_id;
    $walker = new Walker_Nav_Menu_Checklist();
    
    require_once( BD_HELPERS_PATH.'/category_lib.php');
    $categoryService = new JBusinessDirectorCategoryLib();
    $categories = $categoryService->getAllCategories(CATEGORY_TYPE_OFFER);
    $categories = $categoryService->processCategories($categories);
    
    $current_tab = 'all';
    
    $categoriesMenus = array();
    if ( isset( $_REQUEST['offer-category-tab'] ) && 'admins' == $_REQUEST['offer-category-tab'] ) {
        $current_tab = 'admins';
    }elseif ( isset( $_REQUEST['offer-category-tab'] ) && 'all' == $_REQUEST['offer-category-tab'] ) {
        $current_tab = 'all';
    }
    
    /* set values to required item properties */
    foreach ( $categories as &$category ) {
        $category = $category[0];
        $catMenuItem =  new stdClass();
        $catMenuItem->ID = $category->id;
        $catMenuItem->db_id = $category->id;
        $catMenuItem->menu_item_parent= $category->parent_id;
        $catMenuItem->classes = array();
        $catMenuItem->type = 'custom';
        $catMenuItem->object_id = $category->id;
        $catMenuItem->title = $category->name;
        $catMenuItem->object = 'custom';
        $catMenuItem->post_parent = $category->parent_id;
        $catMenuItem->url =  JBusinessUtil::getCategoryLink($category->id, $category->alias);
        $catMenuItem->attr_title = $category->name;
        $catMenuItem->target = '';
        $catMenuItem->description = '';
        $catMenuItem->xfn = '';
        
        $categoriesMenus[] = $catMenuItem;
        
    }
    
    $removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
    ?>
	<div id="offer-category" class="categorydiv">
		<ul id="offer-category-tabs" class="offer-category-tabs add-menu-item-tabs">
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
				<a class="nav-tab-link" data-type="tabs-panel-offer-category-all" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'offer-category-tab', 'all', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-offer-category-all">
					<?php _e( 'View All' ); ?>
				</a>
			</li><!-- /.tabs -->
		</ul>
		
		<div id="tabs-panel-offer-category-all" class="tabs-panel tabs-panel-view-all <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
			<ul id="offer-category-checklist-all" class="categorychecklist form-no-clear">
			<?php
			echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $categoriesMenus), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php echo esc_url( add_query_arg( array( 'offer-category-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#offer-category" class="select-all"><?php _e('Select All'); ?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-offer-category-menu-item" id="submit-offer-category" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.categorydiv -->
<?php
}

/**
 * Displays a metabox for jbd.
 *
 */
function wpbd_events_menu_meta_box(){
    global $nav_menu_selected_id;
    $walker = new Walker_Nav_Menu_Checklist();
    
    require_once( BD_HELPERS_PATH.'/category_lib.php');
    $categoryService = new JBusinessDirectorCategoryLib();
    $categories = $categoryService->getAllCategories(CATEGORY_TYPE_EVENT);
    $categories = $categoryService->processCategories($categories);
    
    $current_tab = 'all';
    
    $categoriesMenus = array();
    if ( isset( $_REQUEST['event-category-tab'] ) && 'admins' == $_REQUEST['event-category-tab'] ) {
        $current_tab = 'admins';
    }elseif ( isset( $_REQUEST['event-category-tab'] ) && 'all' == $_REQUEST['event-category-tab'] ) {
        $current_tab = 'all';
    }
    
    /* set values to required item properties */
    foreach ( $categories as &$category ) {
        $category = $category[0];
        $catMenuItem =  new stdClass();
        $catMenuItem->ID = $category->id;
        $catMenuItem->db_id = $category->id;
        $catMenuItem->menu_item_parent= $category->parent_id;
        $catMenuItem->classes = array();
        $catMenuItem->type = 'custom';
        $catMenuItem->object_id = $category->id;
        $catMenuItem->title = $category->name;
        $catMenuItem->object = 'custom';
        $catMenuItem->post_parent = $category->parent_id;
        $catMenuItem->url =  JBusinessUtil::getCategoryLink($category->id, $category->alias);
        $catMenuItem->attr_title = $category->name;
        $catMenuItem->target = '';
        $catMenuItem->description = '';
        $catMenuItem->xfn = '';
        
        $categoriesMenus[] = $catMenuItem;
        
    }
    
    $removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
    ?>
	<div id="event-category" class="categorydiv">
		<ul id="event-category-tabs" class="event-category-tabs add-menu-item-tabs">
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
				<a class="nav-tab-link" data-type="tabs-panel-event-category-all" href="<?php if ( $nav_menu_selected_id ) echo esc_url( add_query_arg( 'event-category-tab', 'all', remove_query_arg( $removed_args ) ) ); ?>#tabs-panel-event-category-all">
					<?php _e( 'View All' ); ?>
				</a>
			</li><!-- /.tabs -->

		</ul>
		
		<div id="tabs-panel-event-category-all" class="tabs-panel tabs-panel-view-all <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
			<ul id="event-category-checklist-all" class="categorychecklist form-no-clear">
			<?php
			echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $categoriesMenus), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php echo esc_url( add_query_arg( array( 'event-category-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#event-category" class="select-all"><?php _e('Select All'); ?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-event-category-menu-item" id="submit-event-category" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.categorydiv -->
<?php
}

/**
 * Displays a metabox for jbd.
 *
 */
function wpbd_packages_menu_meta_box(){
    global $nav_menu_selected_id;
    $walker = new Walker_Nav_Menu_Checklist();

    $packageItem =  new stdClass();
    $packageItem->ID = 1;
    $packageItem->db_id = 1;
    $packageItem->menu_item_parent= 0;
    $packageItem->classes = array();
    $packageItem->type = 'custom';
    $packageItem->object_id = 1;
    $packageItem->title = "Packages";
    $packageItem->object = 'custom';
    $packageItem->post_parent = 1;
    $packageItem->url =  home_url()."/packages";
    $packageItem->attr_title = "Packages";
    $packageItem->target = '';
    $packageItem->description = '';
    $packageItem->xfn = '';
        
    
    $packageItems = array($packageItem);
    
    $removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
    ?>
	
	<div id="packages" class="package">
		
		<div id="tabs-panel-packages-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="listing-category-checklist-all" class="categorychecklist form-no-clear">
			<?php
			echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $packageItems), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php echo esc_url( add_query_arg( array( 'packages-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#packages" class="select-all"><?php _e('Select All'); ?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-packages-menu-item" id="submit-packages" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.categorydiv -->
<?php
}


/**
 * Displays a metabox for jbd.
 *
 */
function wpbd_control_panel_menu_meta_box(){
    global $nav_menu_selected_id;
    $walker = new Walker_Nav_Menu_Checklist();

    $menuItem =  new stdClass();
    $menuItem->ID = 1;
    $menuItem->db_id = 1;
    $menuItem->menu_item_parent= 0;
    $menuItem->classes = array();
    $menuItem->type = 'custom';
    $menuItem->object_id = 1;
    $menuItem->title = "Control Panel";
    $menuItem->object = 'custom';
    $menuItem->post_parent = 1;
    $menuItem->url =  home_url()."/control-panel";
    $menuItem->attr_title = "Control Panel";
    $menuItem->target = '';
    $menuItem->description = '';
    $menuItem->xfn = '';
        
    
    $menuItems = array($menuItem);
    
    $removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
    ?>
	
	<div id="control-panels" class="control-panel">
		
		<div id="tabs-panel-control-panels-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="listing-category-checklist-all" class="categorychecklist form-no-clear">
			<?php
			echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $menuItems), 0, (object) array( 'walker' => $walker) );
			?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">
			<span class="list-controls">
				<a href="<?php echo esc_url( add_query_arg( array( 'control-panels-tab' => 'all', 'selectall' => 1, ), remove_query_arg( $removed_args ) )); ?>#control-panels" class="select-all"><?php _e('Select All'); ?></a>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-control-panels-menu-item" id="submit-control-panels" />
				<span class="spinner"></span>
			</span>
		</p>

	</div><!-- /.categorydiv -->
<?php
}