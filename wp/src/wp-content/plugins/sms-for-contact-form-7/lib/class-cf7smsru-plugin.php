<?php
if( !class_exists('SMSRU') ) {
	require_once( 'cf7smsru/sms.ru.php' );
}

abstract class CF7SMSRU_Plugin {
  
	public $plugin_callback = null;
  
	public $plugin_dir = null;
  
	protected $cf7smsru = null;
	
	public $smsru;
	
	protected $log_sms_status;
	
	protected $options;

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		
		add_action( 'admin_head', array( $this, 'setup_admin_head' ) );  
		add_action( 'admin_menu', array( $this, 'setup_admin_navigation' ) );
		add_action( 'admin_notices', array( $this, 'setup_admin_message' ) ); 
		//add_action( 'admin_bar_menu', array( $this, 'setup_admin_bar' ), 999 );
		add_action( 'admin_init', array( $this, 'setup_admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_cf7smsru_js' ) );
		
		$this->plugin_callback = array( $this, 'main' );
		
		$this->options = get_option( 'cf7smsru_options' );
		
		
		if(!empty($this->options)) {			
			$this->smsru = new SMSRU( $this->options['api_id'] );
		}
	}
    
     
  
	public function install() {
	}
  
	public function setup_admin_message() {
		if(empty($this->options)) {
			$this->show_admin_message('Перед использованием плагина укажите API_id на <a href="' . site_url() . '/wp-admin/admin.php?page=cf7smsru_options">странице с настройками</a>. Если нет аккаунта, его необходимо <a href="http://gosend.sms.ru" target="_blank">создать</a>.');		
		} else {
			if( $this->options['api_id'] == '' ) {
				$this->show_admin_message('Перед использованием плагина укажите API_id на <a href="' . site_url() . '/wp-admin/admin.php?page=cf7smsru_options">странице с настройками</a>. Если нет аккаунта, его необходимо <a href="http://gosend.sms.ru" target="_blank">создать</a>.');
			}
		}
	}
  
  
	public function setup_admin_navigation() {
		global $menu;
		
		$menu_exists = false;
		foreach( $menu as $k => $item ) {
			if( $item[0] == "CF7 SMS.ru" ) {
				$menu_exists = true;
				break;
			}
		}

		if( !$menu_exists ) {    
			add_menu_page( __( 'CF7 SMS.ru', $this->language_string ), __( 'CF7 SMS.ru', $this->language_string ), 'manage_options', 'cf7smsru_options', array( $this, 'cf7smsru_options' ) );
			//add_submenu_page( 'cf7smsru_options', __( 'CF7 SMS.ru настройки', $this->language_string ), __( 'CF7 SMS.ru настройки', $this->language_string ), 'manage_options', 'cf7smsru_options', array( $this, 'cf7smsru_options' ) );
		}		
			//add_submenu_page( 'cf7smsru_options', __( $this->plugin_name, $this->language_string ), __( $this->plugin_name, $this->language_string ), 'manage_options', $this->plugin_callback[1], $this->plugin_callback );
	}
  
	public function setup_cf7smsru_js() {
		wp_enqueue_script( 'cf7smsru_options', plugins_url( 'js/cf7smsru_options.js', dirname( __FILE__ ) ), array( 'jquery' ) );
	}
  
	public function setup_admin_init() {
		register_setting( 'cf7smsru_options', 'cf7smsru_options', array( $this, 'cf7smsru_options_validate' ) );
		add_settings_section( 'cf7smsru_api_keys', 'Данные с сервиса SMS.ru(<a href="http://gosend.sms.ru" target="_blank">регистрация в SMS.ru</a>)', array( $this, 'settings_api_key_text' ), 'cf7smsru' );
	
		if (in_array('contact-form-7/wp-contact-form-7.php',apply_filters('active_plugins',get_option('active_plugins')))) {
			//add_settings_field( 'cf7smsru_api_key', 'Логин в системе', array( $this, 'settings_api_key_input' ), 'cf7smsru', 'cf7smsru_api_keys' );  
			add_settings_field( 'cf7smsru_api_id', 'Ваш api_id', array( $this, 'settings_api_id_input' ), 'cf7smsru', 'cf7smsru_api_keys' ); 
			add_settings_field( 'cf7smsru_name', 'Имя отправителя', array( $this, 'settings_name_input' ), 'cf7smsru', 'cf7smsru_api_keys' );   
			
			add_settings_section( 'cf7smsru_defaults', 'Ваш баланс', array( $this, 'settings_balans_text' ), 'cf7smsru' );  
			add_settings_section( 'cf7smsru_defaults1', 'Дневной лимит', array( $this, 'settings_limit_text' ), 'cf7smsru' ); 
			add_settings_section( 'cf7smsru_defaults2', 'Отправители', array( $this, 'settings_senders_text' ), 'cf7smsru' );  
		} else {
			add_settings_section( 'cf7smsru_defaults3', 'Внимание!!!', array( $this, 'not_install_cf7' ), 'cf7smsru' );  		
		}	
	}
  
	public function not_install_cf7() {
		echo 'У вас не установлен плагин <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a>.<br />';
		echo 'Для продолжения работы необходимо его установить.';
	}
  
	public function settings_api_key_text() {
		echo '';
	}
  
	public function settings_balans_text() {
		echo $this->get_smsru_balance();
	}
	
	public function settings_limit_text() {
		echo $this->get_smsru_limit();
	}
	
	public function settings_senders_text() {
		echo $this->get_smsru_mysender();
	}
  
	function get_smsru_mysender() {
		if ($this->smsru) {
			$request = $this->smsru->getSenders(); 
			
			if ($request->status == "OK") {
				foreach ($request->senders as $key => $sender) {
					echo 'Отправитель № ' . ($key + 1) . ' - ' . $sender . '<br />';
				}
			} else {
				echo "Ошибка при выполнении запроса.<br />";
				echo "Код ошибки: $request->status_code.<br />";
				echo "Текст ошибки: $request->status_text.<br />"; 
			}
			
			echo 'Для редактирования отправителей перейдите по сылке: <a href="https://gosend.sms.ru/?panel=senders" target="_blank">редактировать отправителей</a>';
		} else {
			echo 'Для получения информации по отправителям введите корректные данные в поля выше.';
		}
	}
	
	function get_smsru_limit() {
		if ($this->smsru) {
			$request = $this->smsru->getLimit(); 
		
			if ($request->status == "OK") {
				echo "Ваш лимит: $request->total_limit <br />";
				echo "Использовано сегодня: $request->used_today <br />";
			} else {
				echo "Ошибка при выполнении запроса.  <br />";
				echo "Код ошибки: $request->status_code.  <br />";
				echo "Текст ошибки: $request->status_text.  <br />"; 
			}
			echo 'Для увеличения лимита перейдите по сылке: <a href="https://sms.ru/?panel=my&subpanel=limit" target="_blank">увеличить лимит</a>';
		} else {
			echo 'Для получения информации по лимитам введите корректные данные в поля выше.';
		}
	}
  
	public function get_smsru_balance() {		
		if ($this->smsru) {
			$request = $this->smsru->getBalance();
			if ($request->status == "OK") {
				echo "Ваш баланс: $request->balance руб.";
			} else {
				echo "Ошибка при выполнении запроса.<br />";
				echo "Код ошибки: $request->status_code.<br />";
				echo "Текст ошибки: $request->status_text. "; 
			}
		} else {
			echo 'Для получения информации о балансе введите корректные данные в поля выше.';
		}
	}

  
	public function settings_api_id_input() {			  
		echo '<input id="cf7smsru_api_id" name="cf7smsru_options[api_id]" size="40" type="text" value="' . (isset($this->options['api_id']) ? $this->options['api_id'] : '') . '" />';
	}
  
	public function settings_name_input() {
		if(!isset($this->options['api_id'])) {
			echo 'Сначала укажите API_id и сохрание настройки.';
			return;
		}
		
		$request = $this->smsru->getSenders();
		echo '<select name="cf7smsru_options[name]" id="cf7smsru_name">';			
		if ($request->status == "OK") {
				echo '<option value="">---</option>';
			foreach ($request->senders as $key => $sender) {
				echo '<option value="' . $sender . '" ' . ($sender == $this->options['name'] ? 'selected' : '') . '>' . $sender . '</option>';
			}
		} else {
			echo "Ошибка при загрузки отправителей.<br />";
			echo "Код ошибки: $request->status_code.<br />";
			echo "Текст ошибки: $request->status_text.<br />"; 
		}			
		echo '</select>';
		echo "<br />При пустом значении, в имя отправителя подставляется Ваш номер.";           
	}
  
  
	public function cf7smsru_options_validate( $val ) {
        return $val;
	}
  
	public function cf7smsru_options() {
		$this->render_template( 'cf7smsru-options' );
	}
  
	protected function show_admin_message( $message, $errormsg = false ) {
		if( $errormsg ) {
		  echo '<div id="message" class="error">';
		} else {
		  echo '<div id="message" class="updated fade">';
		}
	  
		echo "<p><strong>$message</strong></p></div>";
	}
  
	protected function render_template( $name, $data = array() ) {
		include( WP_PLUGIN_DIR . '/' . $this->plugin_dir . '/templates/' . $name . '.php');
	}

}
