<?php
class CF7SMSRU_Plugin_VS extends CF7SMSRU_Plugin {  

  protected $plugin_name = 'Contact Form 7 SMS.ru';  
  protected $language_string = 'wpcf7_sms';
  protected $prefix = 'cf7smsru_cf7';
  protected $folder = '';
  
  public function __construct() {
    parent::__construct();
    
    $this->plugin_callback = array( $this, 'wpcf7' );    
    $this->plugin_dir = basename( dirname( __FILE__ ) );
    
    add_action( 'wpcf7_admin_after_form', array( &$this, 'setup_form_options' ) ); 
    add_action( 'wpcf7_after_save', array( &$this, 'save_form' ) );
    add_action( 'wpcf7_before_send_mail', array( &$this, 'send_sms' ) );
    add_filter( 'wpcf7_editor_panels' , array(&$this, 'new_panel'));
  }


public function new_panel ($panels) {
	$panels['sms-panel'] = array(
			'title' => 'SMS.ru',
			'callback' => array(&$this, 'display_panel')
	);
	return $panels;
}

public function display_panel($form) {
    if ( wpcf7_admin_has_edit_cap() ) {
      $options = get_option( 'wpcf7_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id) );
      if( empty( $options ) || !is_array( $options ) ) {
        $options = array( 'phone' => '', 'message' => '' );
      }
      $options['form'] = $form;
      $this->render_template( 'form-options-4.2', $options );
    }
}

  public function setup_form_options( $form ) {    
    if ( wpcf7_admin_has_edit_cap() ) {
      $options = get_option( 'wpcf7_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id) );
      if( empty( $options ) || !is_array( $options ) ) {
        $options = array( 'phone' => '', 'message' => '' );
      }
      $this->render_template( 'form-options-4.2', $options );
    }
  }
  
  public function setup_admin_navigation() {
    parent::setup_admin_navigation();
  }
  
  public function setup_admin_head() {
    echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( 'css/cf7smsru.css', __FILE__ ) . '">';
  }
  
  public function wpcf7() {}

	public function send_sms( $form ) {
		$options = array_merge( get_option( 'cf7smsru_options' ), get_option( 'wpcf7_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id) ) );
		if( isset( $options['api_id'] ) && isset( $options['phone'] ) && $options['phone'] != '' && isset( $options['message'] ) && $options['message'] != '' ) { 

			// Contact Form 7 > 3.9 
			if(function_exists('wpcf7_mail_replace_tags')) {
				$message = wpcf7_mail_replace_tags($options['message'], array());
				$phone = wpcf7_mail_replace_tags($options['phone'], array());
			} elseif(method_exists($form, 'replace_mail_tags')) {
				$message = $form->replace_mail_tags($options['message']);
				$phone = $form->replace_mail_tags($options['phone']);
			} else {
				return;
			}
							
			$sms_wp = new SMSRU( $options['api_id'] );			
			
			$data = new stdClass();
			$data->to = $phone;
			$data->text = $message;
			
			$data->from = $options['name'];
			if(isset($options['time']) && $options['time'] != '') {
				$data->time = time() + $options['time'];
			}
			$data->translit = $options['lat'];
			$data->partner_id = '18463';
			$request = $sms_wp->send($data);
		}
	}
  
  public function save_form( $form ) {
    update_option( 'wpcf7_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id), $_POST['wpcf7-sms'] );
  }
  
  public function get_existing_username_and_password(){
    if( !defined( WPCF7_PLUGIN_DIR ) ) {
      define( WPCF7_PLUGIN_DIR, WP_PLUGIN_DIR . '/contact-form-7/' );
    }
    
		if( !class_exists( 'WPCF7_ContactForm' ) ) {
			require_once( WPCF7_PLUGIN_DIR . '/includes/functions.php' );
			require_once( WPCF7_PLUGIN_DIR . '/includes/classes.php' );
    }

		$forms = WPCF7_ContactForm::find( array() );
    foreach( $forms as $form ) {
      $options = get_option( 'wpcf7_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id) );
      if( isset( $options['username'] ) && isset( $options['password'] ) ) {
        return array( 'username' => $options['username'], 'password' => $options['password'] );
      }
    }
    
    return false;
  }
  
}

$cp = new CF7SMSRU_Plugin_VS();
