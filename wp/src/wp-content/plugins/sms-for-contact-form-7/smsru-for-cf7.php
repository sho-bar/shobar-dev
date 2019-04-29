<?php
/*
Plugin Name: SMS оповещение для Contact Form 7
Plugin URI: http://wordpress.org/extend/plugins/smsruforcf7
Description: Отправка SMS сообщений из формы Contact Form 7
Version: 2.1
Author: Anton Shelestov
Author URI: http://verstaemvse.ru
*/

$GLOBALS['cf7smsru_plugins'][ basename( dirname( __FILE__ ) ) ] = '2.1';

if( !function_exists( 'cf7smsru_loader' ) ) {

  function cf7smsru_loader() {
	  
		
    $versions = array_flip( $GLOBALS['cf7smsru_plugins'] );
    uksort( $versions, 'version_compare' );
    $versions = array_reverse( $versions );
    $first_plugin = reset( $versions );
    
    if( !class_exists( 'CF7SMSRU_Plugin' ) ) {
      require_once( dirname( dirname( __FILE__ ) ) . '/' . $first_plugin . '/lib/class-cf7smsru-plugin.php' );
    }
    
    $version_nums = array_keys($versions);
    preg_match( '/([0-9]+)\./', reset($version_nums), $matches );
    $major_version = intval( $matches[1] );
    
    foreach( $GLOBALS['cf7smsru_plugins'] as $plugin => $version ) {
      preg_match( '/([0-9]+)\./', $version, $matches );
      
      if( intval( $matches[1] ) < $major_version ) {
        require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-admin/includes/plugin.php' );
        $plugin_path = dirname( dirname( __FILE__ ) ) . '/' . $plugin . '/' . $plugin . '.php';
        $plugin_data = get_plugin_data( $plugin_path );
        deactivate_plugins( $plugin_path );
        
        $message = '<div id="message" class="error"><p><strong>The plugin ' . $plugin_data['Name'] . ' has an important update available. It has been disabled until it has been updated.</strong></p></div>';
        print $message;
      } else {
        require_once( dirname( dirname( __FILE__ ) ) . '/' . $plugin . '/main.php' );
      }
      
    }
  }
  
}

add_action( 'plugins_loaded', 'cf7smsru_loader' );
