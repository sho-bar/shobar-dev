<div class="wrap">
  <div class="left-content">
      
    <div class="icon32"><img src="<?php echo plugins_url( 'images/logo_32px_32px.png', dirname( __FILE__ ) ); ?>" /></div>
    <h2>Настройки CF7 SMS.ru</h2>
    <?php settings_errors(); ?>
    <form method="post" action="options.php" id="cf7smsru_options_form">
    
    <?php    
    settings_fields( 'cf7smsru_options' );
    do_settings_sections( 'cf7smsru' );
    submit_button();
    ?>
    
    </form>
    
  </div>
  
</div>