<?php
/*
Plugin Name: SMSC
Description: SMS уведомления с использованием шлюза SMSC.UA
Version: 1.1
Author: SMSC.UA
Author URI: https://smsc.ua
Plugin URI: https://smsc.ua/api/code/#woocommerce
*/
if (!class_exists('smsc_woocommerce'))
	exit;

if (!is_callable('is_plugin_active'))
	include_once(ABSPATH .'wp-admin/includes/plugin.php');

if (is_plugin_active('woocommerce/woocommerce.php'))
	add_action('plugins_loaded', 'smsc_woocommerce', 0);

function smsc_woocommerce() { $smsc_woocommerce = new smsc_woocommerce; }

class smsc_woocommerce {

	function __construct() {
		add_action('admin_menu',array(&$this,'admin_menu'));
		add_action('woocommerce_order_status_changed', array(&$this, 'change_status'), 1, 3);
		register_deactivation_hook(__FILE__, array(&$this, 'smsc_woocommerce_deactivation'));
		include('smsc_api.php');
	}
	 
	function smsc_woocommerce_deactivation() {
	    delete_option('smsc_login');
	    delete_option('smsc_psw');
	    delete_option('smsc_sender');
	    delete_option('smsc_phone');
	    delete_option('smsc_send_new_adm');
	    delete_option('smsc_send_new_cl');
	    delete_option('smsc_new_adm');
	    delete_option('smsc_new_cl');
	    delete_option('smsc_send_change_adm');
	    delete_option('smsc_send_change_cl');
	    delete_option('smsc_change_adm');
	    delete_option('smsc_change_cl');
	}
	
	function admin_menu() {
		add_submenu_page('woocommerce', 'Настройка SMS оповещений', 'SMS оповещения', 'manage_woocommerce', 'smsc_settings', array(&$this,'options_page'));
	}
	
	function options_page() {
		if (isset($_GET['action']) && $_GET['action'] == 'add') {
			update_option('smsc_login', $_POST['login']);
			update_option('smsc_psw', $_POST['psw']);
			update_option('smsc_sender', $_POST['sender']);
			update_option('smsc_phone', $_POST['phone']);
			update_option('smsc_send_new_adm', $_POST['send_new_adm'] == 'on' ? 1 : 0);
			update_option('smsc_send_new_cl', $_POST['send_new_cl'] == 'on' ? 1 : 0);
			update_option('smsc_new_adm', $_POST['new_adm']);
			update_option('smsc_new_cl', $_POST['new_cl']);
			update_option('smsc_send_change_adm', $_POST['send_change_adm'] == 'on' ? 1 : 0);
			update_option('smsc_send_change_cl', $_POST['send_change_cl'] == 'on' ? 1 : 0);
			update_option('smsc_change_adm', $_POST['change_adm']);
			update_option('smsc_change_cl', $_POST['change_cl']);

			$result = 'Настройки обновлены.';
		}

		$login = get_option('smsc_login');
		$psw = get_option('smsc_psw');

		list($balance) = $login && $psw ? _smsc_send_cmd('balance', 'login='.$login.'&psw='.$psw) : '';
?>
		<div class='wrap woocommerce'>
			<form method='post' id='mainform' action="<?php echo admin_url('admin.php?page=smsc_settings&action=add');?>">
				<div class='icon32 icon32-woocommerce-settings' id='icon-woocommerce'></div>
				<?php if (isset($result)) { echo '<h3>'.$result.'</h3>'; } ?>
				<table class='widefat' style='width:auto; float:left; display:inline; clear:none; margin-bottom:30px'>
					<tr><th colspan='2' style='text-align:center'><h2>Настройки шлюза</h2>
					<tr><td><label for='login'>Логин</label><td><input required type='text' name='login' id='login' value='<?php echo $login;?>'>
					<tr><td><label for='psw'>Пароль</label><td><input required type='password' name='psw' id='psw' value='<?php echo $psw;?>'>
					<tr><td><label for='sender'>Имя отправителя</label><td><input type='text' name='sender' id='sender' value='<?php echo get_option('smsc_sender');?>'>
					<tr><td><label for='phone'>Телефон администратора</label><td><input required type='text' name='phone' id='phone' value='<?php echo get_option('smsc_phone');?>'>
					<?php if ($balance) { echo '<tr><td>Ваш баланс<td><b>', $balance, '</b>'; } ?>
				</table>
				<table class='widefat' style='width:auto'>
					<tr><th colspan='3' style='text-align:center'><h2>Шаблоны сообщений</h2>
					<tr><th colspan='2' style='text-align:center;'><b>Шаблоны для события "Новый заказ"</b>
					<tr><td style='padding:0'><td style='padding:0'><td rowspan='4'><br><br><br><br><b>Макросы:</b><br>
						{NUM} - номер заказа<br>{SUM} - сумма заказа<br>{EMAIL} - E-mail клиента<br>{PHONE} - Телефон клиента<br>{FIRSTNAME} - Имя клиента<br>{LASTNAME}
						- Фамилия клиента<br>{CITY} - Город клиента<br>{ADDRESS} - Адрес клиента<br>{BLOGNAME} - Название магазина(блога)<br>{OLD_STATUS} - Старый статус<br>
						{NEW_STATUS} - Новый статус<br>{ITEMS} - Список товаров в формате "Наименование Количество: Стоимость:"<br>{PAYMENT_METHOD} - Метод оплаты<br>{SHIPPING_METHOD} - Способ доставки
					<tr><td><label for='new_adm'>Сообщение администратору</label><br><br><textarea cols='20' rows='5' name='new_adm' id='new_adm'><?php echo get_option('smsc_new_adm');?></textarea>
							<br><label for='send_new_adm'>Отправлять администратору?</label> <input type='checkbox' name='send_new_adm' id='send_new_adm' <?php echo get_option('smsc_send_new_adm') ? 'checked' : '';?>>
						<td><label for='new_cl'>Сообщение клиенту</label><br><br><textarea cols='20' rows='5' name='new_cl' id='new_cl'><?php echo get_option('smsc_new_cl');?></textarea>
							<br><label for='send_new_cl'>Отправлять клиенту?</label> <input type='checkbox' name='send_new_cl' id='send_new_cl' <?php echo get_option('smsc_send_new_cl') ? 'checked' : '';?>>
					<tr><th colspan='2' style='text-align:center;'><br><b>Шаблоны для события "Статус заказа изменен"</b>
					<tr><td><label for='change_adm'>Сообщение администратору</label><br><br><textarea cols='20' rows='5' name='change_adm' id='change_adm'><?php echo get_option('smsc_change_adm');?></textarea>
							<br><label for='send_change_adm'>Отправлять администратору?</label> <input type='checkbox' name='send_change_adm' id='send_change_adm' <?php echo get_option('smsc_send_change_adm') ? 'checked' : '';?>>
						<td><label for='change_cl'>Сообщение клиенту</label><br><br><textarea cols='20' rows='5' name='change_cl' id='change_cl'><?php echo get_option('smsc_change_cl');?></textarea>
							<br><label for='send_change_cl'>Отправлять клиенту?</label> <input type='checkbox' name='send_change_cl' id='send_change_cl' <?php echo get_option('smsc_send_change_cl') ? 'checked' : '';?>>
				</table>
				<br>
				<input type='submit' class='button-primary' value='Сохранить'>
			</form>
		</div>
<?php
	}

	function change_status($order_id, $old_status, $new_status) {
		if (($login = get_option('smsc_login')) && ($psw = get_option('smsc_psw'))) {
			$send_new_adm = get_option('smsc_send_new_adm');
			$new_adm = get_option('smsc_new_adm');
			$send_new_cl = get_option('smsc_send_new_cl');
			$new_cl = get_option('smsc_new_cl');
			$send_change_adm = get_option('smsc_send_change_adm');
			$change_adm = get_option('smsc_change_adm');
			$send_change_cl = get_option('smsc_send_change_cl');
			$change_cl = get_option('smsc_change_cl');

			if (($send_new_adm && $new_adm) || ($send_new_cl && $new_cl) || ($send_change_adm && $change_adm) || ($send_change_cl && $change_cl)) {
				$order = new WC_Order($order_id);

				global $wpdb;
				$shipping = $wpdb->get_var("SELECT order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = $order_id AND order_item_type = 'shipping'");
				
				$search = array('{NUM}', '{SUM}', '{EMAIL}', '{PHONE}', '{FIRSTNAME}', '{LASTNAME}', '{CITY}', '{ADDRESS}', '{BLOGNAME}', '{OLD_STATUS}', '{NEW_STATUS}', '{ITEMS}', '{PAYMENT_METHOD}', '{SHIPPING_METHOD}');
				$replace = array('№' . $order_id, strip_tags($order->get_formatted_order_total()), $order->billing_email, $order->billing_phone, $order->billing_first_name, $order->billing_last_name,
					$order->shipping_city, $order->shipping_address_1 . ' ' . $order->shipping_address_2, get_option('blogname'), __($old_status, 'woocommerce'), __($new_status, 'woocommerce'),
					str_replace("\n", " ", strip_tags($order->email_order_items_table(0, 0, 0, 0, 0, 1))), $order->payment_method_title, $shipping);

				$phone = get_option('smsc_phone');
				$sender = get_option('smsc_sender');
				$query = 'login=' . $login . '&psw=' . $psw;

				if ($order->order_date == $order->modified_date) {
					unset($search[9], $search[10], $replace[9], $replace[10]);

					if ($send_new_adm && $new_adm)
						send_sms($phone, str_replace($search, $replace, $new_adm), 0, 0, 0, 0, $sender, $query);

					if ($send_new_cl && $new_cl)
						send_sms($order->billing_phone, str_replace($search, $replace, $new_cl), 0, 0, 0, 0, $sender, $query);
				} else {
					if ($send_change_adm && $change_adm)
						send_sms($phone, str_replace($search, $replace, $change_adm), 0, 0, 0, 0, $sender, $query);

					if ($send_change_cl && $change_cl)
						send_sms($order->billing_phone, str_replace($search, $replace, $change_cl), 0, 0, 0, 0, $sender, $query);
				}
			}
		}
	}
}