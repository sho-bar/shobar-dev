<?php

/**
 * dFlip CUSTOM POST
 *
 * Initializes and Registers the required custom post for dFlip
 *
 * @since   1.0.0
 *
 * @package dFlip
 * @author  Deepak Ghimire
 */
class DFlip_Post_Type
{
  
  /**
   * Holds the singleton class object.
   *
   * @since 1.0.0
   *
   * @var object
   */
  public static $instance;
  
  /**
   * Holds the base DFlip class object.
   *
   * @since 1.0.0
   *
   * @var object
   */
  public $base;
  
  /**
   * Primary class constructor.
   *
   * @since 1.0.0
   */
  public function __construct()
  {
    
    // Load the base class object.
    $this->base = DFlip::get_instance();
    
    $labels = array(
        'name'               => __( 'dFlip Book', 'DFLIP' ),
        'singular_name'      => __( 'dFlip Book', 'DFLIP' ),
        'menu_name'          => __( 'dFlip Books', 'DFLIP' ),
        'name_admin_bar'     => __( 'dFlip Book', 'DFLIP' ),
        'add_new'            => __( 'Add New Book', 'DFLIP' ),
        'add_new_item'       => __( 'Add New Book', 'DFLIP' ),
        'new_item'           => __( 'New dFlip Book', 'DFLIP' ),
        'edit_item'          => __( 'Edit dFlip Book', 'DFLIP' ),
        'view_item'          => __( 'View dFlip Book', 'DFLIP' ),
        'all_items'          => __( 'All Books', 'DFLIP' ),
        'search_items'       => __( 'Search dFlip Books', 'DFLIP' ),
        'parent_item_colon'  => __( 'Parent dFlip Books:', 'DFLIP' ),
        'not_found'          => __( 'No dFlip-Books found.', 'DFLIP' ),
        'not_found_in_trash' => __( 'No dFlip Books found in Trash.', 'DFLIP' )
    );
    
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'DFLIP' ),
        'public'             => false,  //this removes the permalink option
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false, //array('slug' => $this->base->slug),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-book',
        'supports'           => array( 'title' )
    );
    
    register_post_type( 'dflip', $args );
    
    register_taxonomy( 'dflip_category', 'dflip', array(
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'rewrite'           => array( 'slug' => 'dflip_category' ),
    ) );
    
    if ( is_admin() ) {
      $this->init_admin();
    }
  }
  
  /**
   * Loads all admin related files into scope.
   *
   * @since 1.0.0
   */
  public function init_admin()
  {
    
    // Remove quick editing from the dFlip post type row actions.
    add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 1 );
    
    // Manage post type columns.
    add_filter( 'manage_dflip_posts_columns', array( $this, 'dflip_columns' ) );
    add_action( 'manage_dflip_posts_custom_column', array( $this, 'dflip_columns_content' ), 10, 2 );
    
    add_filter( 'manage_edit-dflip_category_columns', array( $this, 'dflip_cat_columns' ) );
    add_filter( 'manage_dflip_category_custom_column', array( $this, 'dflip_cat_columns_content' ), 10, 3 );
    
    //Optimize the icons for retina display
    add_action( 'admin_head', array( $this, 'menu_icon' ) );
    
  }
  
  
  /**
   * Filter out unnecessary row actions dFlip post table.
   *
   * @since 1.0.0
   *
   * @param array $actions Default row actions.
   *
   * @return array $actions Amended row actions.
   */
  public function remove_quick_edit( $actions )
  {
    if ( isset( get_current_screen()->post_type ) && 'dflip' == get_current_screen()->post_type ) {
      unset( $actions['inline hide-if-no-js'] );
    }
    
    return $actions;
  }
  
  /**
   * Customize the post columns for the dFlip post type.
   *
   * @since 1.0.0
   *
   * @return array $columns New Updated columns.
   */
  public function dflip_columns()
  {
    
    $columns = array(
        'cb'        => '<input type="checkbox" />',
        'title'     => __( 'Title', 'DFLIP' ),
        'shortcode' => __( 'Shortcode', 'DFLIP' ),
        'modified'  => __( 'Last Modified', 'DFLIP' ),
        'date'      => __( 'Date', 'DFLIP' )
    );
    
    return $columns;
  }
  
  /**
   * Customize the post columns for the dFlip post type category page
   *
   * @since 1.2.9
   *
   * @param array $defaults columns.
   *
   * @return array $defaults default columns.
   */
  public function dflip_cat_columns( $defaults )
  {
    $defaults['shortcode'] = 'Shortcode';
    
    return $defaults;
  }
  
  /**
   * Add data to the custom columns added to the dFlip post type.
   *
   * @since 1.0.0
   *
   * @param string $column_name Name of the custom column.
   * @param int    $post_id     Current post ID.
   */
  public function dflip_columns_content( $column_name, $post_id )
  {
    $post_id = absint( $post_id );
    
    switch ( $column_name ) {
      case 'shortcode':
        echo '<code>[dflip id="' . $post_id . '"][/dflip]</code>';
        break;
      
      case 'modified' :
        the_modified_date();
        break;
    }
  }
  
  /**
   * Add data to the custom columns added to the dFlip post type category page.
   *
   * @since 1.2.9
   *
   * @param        $c
   * @param string $column_name Name of the custom column.
   * @param        $term_id
   *
   * @return string
   */
  public function dflip_cat_columns_content( $c, $column_name, $term_id = "" )
  {
    
    return '<code>[dflip books="' . get_term( $term_id, 'dflip_category' )->slug . '"][/dflip]</code>';
    
  }
  
  
  /**
   * Forces the dFlip menu icon width/height for Retina devices.
   *
   * @since 1.0.0
   */
  public function menu_icon()
  {
    
    ?>
    <style type="text/css">#menu-posts-dflip .wp-menu-image img { width: 16px; height: 16px; }</style>
    <?php
    
  }
  
  /**
   * Returns the singleton instance of the class.
   *
   * @since 1.0.0
   *
   * @return object DFlip_Post_Type object.
   */
  public static function get_instance()
  {
    
    if ( !isset( self::$instance ) && !( self::$instance instanceof DFlip_Post_Type ) ) {
      self::$instance = new DFlip_Post_Type();
    }
    
    return self::$instance;
    
  }
}

// Load the post-type class.
$dflip_post_type = DFlip_Post_Type::get_instance();

