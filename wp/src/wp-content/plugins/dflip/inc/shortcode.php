<?php

/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 5/3/2016
 * Time: 2:27 PM
 */
class DFlip_ShortCode
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
    
    // Load shortcode hooks and filters.
    add_shortcode( 'dflip', array( $this, 'shortcode' ) );
    //		add_shortcode( 'dflip-multi', array( $this, 'dflip_multi_shortcode' ) );
  }
  
  /**
   * Builds the dFlip Shortcode for the plugin
   *
   * @since 1.0.0
   *
   * @param array  $attr    Attributes of the shortcode.
   * @param string $content Content of the button or thumb
   *
   * @return string HTML content to display image-text.
   */
  public function shortcode( $attr, $content = '' )
  {
    
    $ismulti      = isset( $attr['books'] ) && trim( $attr['books'] ) !== '';
    $atts_default = array(
        'class' => '',
        'id'    => '',
        'books' => ''
    );
    //atts or post defaults
    $atts = shortcode_atts( $atts_default, $attr, 'dflip' );
    
    if ( $ismulti ) {
      $ids   = array();
      $books = explode( ',', $atts['books'] );
      foreach ( (array) $books as $query ) {
        $query = trim( $query );
        if ( is_numeric( $query ) ) {
          array_push( $ids, $query );
        } else {
          if ( $query == 'all' || $query == '*' ) {
            $postslist = get_posts( array( 'post_type' => 'dflip', 'exclude' => $ids ) );
            foreach ( $postslist as $post ) {
              array_push( $ids, $post->ID );
            }
          } else {
            $postslist = get_posts( array(
                'tax_query' => array(
                    array(
                        'taxonomy' => 'dflip_category',
                        'field'    => 'slug',
                        'terms'    => $query,
                    )
                ),
                'post_type' => 'dflip',
                'exclude'   => $ids
            ) );
            foreach ( $postslist as $post ) {
              array_push( $ids, $post->ID );
            }
          }
        }
      }
      $html = '<div class="dflip-books">';
      foreach ( $ids as $id ) {
        $attr['id'] = $id;
        $html       .= $this->book( $attr, $content, true );
      }
      
      return $html . '</div>';
      
    } else {
      return $this->book( $attr, $content );
    }
  }
  
  /**
   * Helper function for dFlip Shortcode
   *
   * @since    1.0.0
   *
   * @param        $attr
   * @param string $content Content of the button or thumb
   *
   * @param bool   $multi   checks if this is a part of multiple books request
   *
   * @return string HTML content to display image-text.
   * @internal param array $attr Attributes of the shortcode.
   */
  public function book( $attr, $content = '', $multi = false )
  {
    $base = $this->base;
    
    $atts_default = array(
        'class' => '',
        'id'    => '',
        'type'  => $multi ? 'thumb' : 'book'
    );
    
    //atts or post defaults
    $atts = shortcode_atts( $atts_default, $attr, 'dflip' );
    
    //default data
    $id    = $atts['id'] === '' ? 'df_rand' . rand() : $atts['id'];
    $type  = $atts['type'];
    $class = $atts['class'];
    $title = do_shortcode( $content );
    
    //get Id
    $post_id   = $atts['id'];
    $hasId     = false;
    $thumb_url = '';
    
    
    $post_data = array();
    
    
    //pull post data if available for the script part only
    if ( !empty( $post_id ) && is_numeric( $post_id ) ) {
      
      /*			$post = get_post( $post_id );
            if ( $post == null ) {
              return '';
            }*/
      
      $id = 'df_' . $post_id;
      
      $post_meta = get_post_meta( $post_id, '_dflip_data' );
      
      if ( $title === '' ) {
        $title = get_the_title( $post_id );
      }
      
      
      if ( is_array( $post_meta ) && count( $post_meta ) > 0 ) {
        $post_data = $post_meta[0];
      }
      
      //conversion
      $post_data['enableDownload']      = $post_data['enable_download'];
      $post_data['backgroundColor']     = $post_data['bg_color'];
      $post_data['backgroundImage']     = $post_data['bg_image'];
      $post_data['autoEnableOutline']   = $post_data['auto_outline'];
      $post_data['autoEnableThumbnail'] = $post_data['auto_thumbnail'];
      $post_data['overwritePDFOutline'] = $post_data['overwrite_outline'];
      $post_data['soundEnable']         = $post_data['auto_sound'];
      $post_data['maxTextureSize']      = $post_data['texture_size'];
      $post_data['pageMode']            = $post_data['page_mode'];
      $post_data['singlePageMode']      = $post_data['single_page_mode'];
      $post_data['forceFit']            = $post_data['force_fit'];
      $post_data['autoPlay']            = $post_data['autoplay'];
      $post_data['autoPlayDuration']    = $post_data['autoplay_duration'];
      $post_data['autoPlayStart']       = $post_data['autoplay_start'];
      
      
      $post_defaults = array(
          'webgl'               => $base->get_default( 'webgl' ),
          'class'               => '',
          'id'                  => '',
          //internal
          'source_type'         => $base->get_default( 'source_type' ),
          'pdf_source'          => '',
          'pdf_thumb'           => '',
          'pages'               => array(),
          'outline'             => '',
          'backgroundColor'     => $base->get_default( 'bg_color' ),
          'height'              => $base->get_default( 'height' ),
          'duration'            => $base->get_default( 'duration' ),
          'hard'                => $base->get_default( 'hard' ),
          'forceFit'            => $base->get_default( 'force_fit' ),
          'autoEnableOutline'   => $base->get_default( 'auto_outline' ),
          'autoEnableThumbnail' => $base->get_default( 'auto_thumbnail' ),
          'overwritePDFOutline' => $base->get_default( 'overwrite_outline' ),
          'enableDownload'      => $base->get_default( 'enable_download' ),
          'backgroundImage'     => $base->get_default( 'bg_image' ),
          'direction'           => $base->get_default( 'direction' ),
          'pageMode'            => $base->get_default( 'page_mode' ),
          'singlePageMode'      => $base->get_default( 'single_page_mode' ),
          'soundEnable'         => $base->get_default( 'auto_sound' ),
          'maxTextureSize'      => $base->get_default( 'texture_size' ),
          'autoPlay'            => $base->get_default( 'autoplay' ),
          'autoPlayDuration'    => $base->get_default( 'autoplay_duration' ),
          'autoPlayStart'       => $base->get_default( 'autoplay_start' ),
          'thumb'               => '',
          'source'              => '',
          'wpOptions'           => 'true'
      );
      
      $post_data = shortcode_atts( $post_defaults, $post_data, 'dflip' );
      //			$data      = shortcode_atts( $post_data, $attr, 'dflip' );
      
      $source_type = $post_data['source_type'];
      $pdf_source  = $post_data['pdf_source'];
      
      $post_data['source'] = '';
      
      if ( $source_type == 'pdf' ) {
        $post_data['source'] = $pdf_source;
        $thumb_url           = empty( $post_data['pdf_thumb'] ) ? '' : $post_data['pdf_thumb'];
      }
      
      if ( $source_type == 'image' ) {
        $pages       = array_map( 'maybe_unserialize', $post_data['pages'] );
        $source_list = array();
        $links       = array();
        $index       = 0;
        foreach ( $pages as $image ) {
          if ( $thumb_url === '' ) {
            $thumb_url = $image['url'];
          }
          if ( $image['url'] !== '' ) {
            array_push( $source_list, $image['url'] );
          }
          if ( isset( $image['hotspots'] ) && $image['hotspots'] !== '' ) {
            $links[ $index ] = $image['hotspots'];
          }
          $index ++;
        }
        $post_data['links']  = $links;
        $post_data['source'] = $source_list;
      }
      
      unset( $post_data['pages'] );
      unset( $post_data['pdf_source'] );
      unset( $post_data['pdf_thumb'] );
      unset( $post_data['thumb'] );
      unset( $post_data['source_type'] );
      unset( $post_data['class'] );
      unset( $post_data['id'] );
      
      foreach ( $post_data as $key => $value ) {
        if ( $value === "" || $value === null || $value == "global" ) {//newly added will be null in old post
          unset( $post_data[ $key ] );
        }
      }
      //			$attr['slug'] = $post->post_name;
      
    } else {
      /*handled by new attribute support*/
    }
    
    //deep-link
    if ( !$multi && isset( $attr['slug'] ) && !empty( $attr['slug'] ) ) {
      $attr['slug'] = sanitize_title( $attr['slug'] );
    }
    
    if ( empty( $title ) ) {
      $title = "Open Book";
    }
    
    //		if (0 === strpos($data['source'], '/wp-content/')) {
    //			$data['source'] = get_site_url() . $data['source'];
    //		}
    
    /*Attribute overrides*/
    $attrHTML = ' ';
    
    $attr['wpoptions'] = 'true';
    unset( $attr['class'] );
    unset( $attr['id'] );
    unset( $attr['type'] );
    
    if ( !isset( $attr['thumb'] ) && $thumb_url !== '' ) {
      $attr['thumb'] = $thumb_url;
    }
    
    foreach ( $attr as $key => $value ) {
      $attrHTML .= $key . '="' . $value . '" ';
    }
    
    $html = "";
    
    if ( $type == 'button' || $type == 'thumb' || $type == 'custom' ) {
      $html = '<div class="_df_' . $type . ' ' . $class . '" id="' . $id . '" ' . $attrHTML . '>' . $title . '</div>';
    } else if ( $type == 'link' ) {
      $html
          = '<a class="_df_custom ' . $class . '" id="' . $id . '" href="#" ' . $attrHTML . '>' . $title . '</a>';
    } else {
      $html = '<div class="_df_book df-container df-loading ' . $class . '" id="' . $id . '" ' . $attrHTML . '></div>';
    }
    
    if ( count( $post_data ) > 0 ) {
      
      /*Normally this occurs only when a valid post id is added*/
      
      $code = 'var option_' . $id . ' = ' . json_encode( $post_data ) . '; if(window.DFLIP && DFLIP.parseBooks){DFLIP.parseBooks();}';
      
      $html .= '<script data-cfasync="false">' . $code . '</script>';
      
    }
    
    return $html;
  }
  
  /**
   * Returns the singleton instance of the class.
   *
   * @since 1.0.0
   *
   * @return object dFlip_PostType object.
   */
  public static function get_instance()
  {
    
    if ( !isset( self::$instance )
         && !( self::$instance instanceof DFlip_ShortCode )
    ) {
      self::$instance = new DFlip_ShortCode();
    }
    
    return self::$instance;
    
  }
  
}

//Load the dFlip Plugin Class
$dflip_shortcode = DFlip_ShortCode::get_instance();


