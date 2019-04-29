<?php
/*
  Plugin Name: WooCommerce WooSMS module
  Plugin URI: http://www.woo-sms.net/
  Description: Woo SMS is a comprehensive and powerful module that enables you to send SMSs to your customers or administrators during various events in your WooCommerce store. <a href="http://www.woothemes.com/woocommerce/"><strong>| This module needs WooCommerce module |</strong></a>
  Version: 1.14
  Author: TOPefekt s.r.o. (LP)
  Author URI: http://www.topefekt.com/
 */
 
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
  
/**
 * Enable SMS demo feature (Disable save settings in profile)
 */     
//define("SMS_DEMO", true);

global $wpdb;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

/**
 * Define paths
 */
define("_DB_PREFIX_", $wpdb->prefix);
define("SMS_INCLUDES_DIR", "/includes/");
define("WOOSMS_DIR", "/".basename(dirname(__FILE__)));

/**
 * Check if woocommerce is installed
 */  
if(is_plugin_active( 'woocommerce/woocommerce.php' ))
{   
    add_action("init", "woosms_language_load");
    require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'variables.php');
    require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . "hooks.php");

    /**
     * Connect woosms actions for customers and admin SMS
     */              
    add_action("woocommerce_order_status_changed", "woosms_hook_changeOrderStatusHook");
    add_action("woocommerce_checkout_order_processed", "woosms_hook_actionValidateOrder");
    add_action("woocommerce_created_customer", "woosms_hook_customerAddHook");
    add_action("woocommerce_low_stock", "woosms_hook_productOutOfStockHook");
    add_action("woocommerce_no_stock", "woosms_hook_productOutOfStockHook");
    add_action("woocommerce_product_on_backorder", "woosms_hook_productOnBackOrder");

     /**
     * Load backend for woosms
     */  
    if (is_admin()) {
        require("woocommerce-woosms-admin.php");
    }

    /**
     * Load translations
     */ 
    function woosms_language_load() 
    {
        if(defined("SMS_DEMO"))
        {
            session_start();
            if(isset($_GET["SMS_DEMO_LANG"]))
            {
                $_SESSION["SMS_DEMO_LANG"] = $_GET["SMS_DEMO_LANG"];
            }

            if(isset($_SESSION["SMS_DEMO_LANG"]))
            {
                require_once(dirname(__FILE__) . '/lang/' . $_SESSION["SMS_DEMO_LANG"] . '/lang.php');
                return;
            }
        }
        
        $options = get_option('woosms_settings');

        if (isset($options['language'])) {
            require_once(dirname(__FILE__) . '/lang/' . $options['language'] . '/lang.php');
        } else {
            require_once(dirname(__FILE__) . '/lang/en/lang.php');
        }
    }

    /**
     * Woosms actions
     */         
    function woosms_hook_changeOrderStatusHook($order_id)
    {       
        $order = new WC_Order($order_id);
        $hooks = new ModelSmsHooks();
        
        $hooks->changeOrderStatusHook($order->post_status, $order_id);
    }

    function woosms_hook_actionValidateOrder($order_id)
    {
        $hooks = new ModelSmsHooks();
        $hooks->orderAddHook($order_id);
    }

    function woosms_hook_customerAddHook($customer_id)
    {
        $hooks = new ModelSmsHooks();
        $hooks->customerAddHook(0, $customer_id);
    }

    function woosms_hook_productOutOfStockHook($data)
    {
        $stock = get_post_meta($data->id, "_stock");

        if(isset($stock[0]))
        {
            $stock = $stock[0];
        }

        $hooks = new ModelSmsHooks();
        $hooks->productOutOfStockHook($data->id, $stock, $data->post->post_title, $data->post->post_name);
    }

    function woosms_hook_productOnBackOrder($data)
    {
        $product = getIfIsSet($data, "product");
        $hooks = new ModelSmsHooks();
        $hooks->productOnBackOrder($product->id, getIfIsSet($data, "quantity"), $product->post->post_title, $product->post->post_name, getIfIsSet($data, "order_id"));
    }

    function getIfIsSet($data, $key, $default = NULL)
    {
        if(isset($data[$key]))
        {
            return $data[$key];
        }
        return $default;
    }

    /**
     * Woosms install script
     */  
    function woosms_activate() 
    {        
        global $wpdb;

        $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_settings (
                                    `ID` int(5) NOT NULL AUTO_INCREMENT,
                                    `name` varchar(100) CHARACTER SET utf8 NOT NULL,
                                     `value` text NOT NULL,
                                    PRIMARY KEY (`ID`)
                                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

        $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_optout_prices (
                                    `ID` int(7) NOT NULL AUTO_INCREMENT,
                                    `shop_id` int(5) NOT NULL,
                                    `price` double(10,4) NOT NULL,
                                    `currency` varchar(20) NOT NULL,
                                    `active` tinyint(3) NOT NULL,
                                    PRIMARY KEY (`ID`)
                                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

        $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_optout_texts (
                                    `ID` int(7) NOT NULL AUTO_INCREMENT,
                                    `shop_id` int(5) NOT NULL,
                                    `text` text NOT NULL,
                                    `lang` varchar(20) NOT NULL,
                                    PRIMARY KEY (`ID`)
                                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

        $wpdb->get_results("CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "sp_optout_deleted` (
                                    `ID` int(10) NOT NULL AUTO_INCREMENT,
                                    `cart_ID` int(10) NOT NULL,
                                    `currency_ID` int(11) DEFAULT NULL,
                                    `order_id` int(11) DEFAULT '-1',
                                    PRIMARY KEY (`ID`),
                                    KEY `order_id_optout` (`order_id`) USING HASH
                                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8");


        $result = $wpdb->get_results("SELECT count(*) as count FROM " . _DB_PREFIX_ . "sp_settings where name like 'AppID'");

        if (is_array($result) && count($result)) 
        {
            foreach($result as $row)
            {
                $counter = $row->count;
            }
        }

        if (!($counter > 0)) 
        {
            $wpdb->get_results("INSERT INTO " . _DB_PREFIX_ . "sp_settings VALUES (NULL , 'AppID', '" . rand(1000000, 90000000) . "' )");
        }

        $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_smsuser (
                            `ID` tinyint(1) NOT NULL AUTO_INCREMENT,
                            `user` varchar(55) CHARACTER SET utf8 NOT NULL,
                            `passwd` varchar(55) CHARACTER SET utf8 NOT NULL,
                            `email` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `companyname` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `addressstreet` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `addresscity` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `addresszip` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `country0` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `companyid` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `companyvat` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `simulatesms` tinyint(3) NOT NULL,
                            `deletedb` tinyint(3) NOT NULL,
                            `pocetkredit` int(6) NOT NULL,
                            `deliveryemail` varchar(100) NOT NULL,
                            `URLreports` tinyint(3) NOT NULL,
                            `kosoba` varchar(100) CHARACTER SET utf8 NOT NULL,
                            `kprijmeni` varchar(100) CHARACTER SET utf8 NOT NULL,
                            PRIMARY KEY (`ID`)
                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;");


            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_admins (
                                            `ID` int(5) NOT NULL AUTO_INCREMENT,
                                            `name` varchar(100) CHARACTER SET utf8 NOT NULL,
                                            `number` varchar(20) CHARACTER SET utf8 NOT NULL,
                                            PRIMARY KEY (`ID`)
                                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "sp_hooks_admins_multi` (
                                            `name` varchar(100) NOT NULL,
                                            `status` varchar(40) NOT NULL,
                                            `smstext` text NOT NULL,
                                            `adminIDs` varchar(250) NOT NULL,
                                            `storeID` int(5) NOT NULL
                                          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_hooks_customers (
                                            `name` varchar(100) CHARACTER SET utf8 NOT NULL,
                                            `status` varchar(40) NOT NULL,
                                            `smstext` text CHARACTER SET utf8 NOT NULL,
                                            `active` tinyint(3) NOT NULL,
                                            `mutation` varchar(100) NOT NULL,
                                            `storeID` int(5) NOT NULL
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_hooks_templates (
                                            `area` varchar(10) CHARACTER SET utf8 NOT NULL,
                                            `area_text` varchar(50) CHARACTER SET utf8 NOT NULL,
                                            UNIQUE KEY `area` (`area`)
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_hooks_unicode (
                                            `area` varchar(10) NOT NULL,
                                            `unicode` tinyint(3) NOT NULL,
                                            `type` varchar(10) NOT NULL
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_ownnumbersenderIDs (
                                            `ownnumbersenderID` varchar(30) CHARACTER SET utf8 NOT NULL,
                                            UNIQUE KEY `textsenderID` (`ownnumbersenderID`)
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_routes (
                                            `area` int(5) NOT NULL,
                                            `type` varchar(20) CHARACTER SET utf8 NOT NULL,
                                            `isms` int(5) NOT NULL,
                                            `sendertype` tinyint(3) NOT NULL,
                                            `senderID` varchar(30) CHARACTER SET utf8 NOT NULL,
                                            `info` text CHARACTER SET utf8 NOT NULL,
                                            `area_text` varchar(50) CHARACTER SET utf8 NOT NULL
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_sms_history (
                                            `ID` int(8) NOT NULL AUTO_INCREMENT,
                                            `number` varchar(30) NOT NULL,
                                            `date` datetime NOT NULL,
                                            `text` text NOT NULL,
                                            `status` tinyint(3) NOT NULL,
                                            `price` double(5,3) NOT NULL,
                                            `credit` double(15,3) NOT NULL,
                                            `sender` varchar(30) NOT NULL,
                                            `unicode` tinyint(3) NOT NULL,
                                            `type` tinyint(3) NOT NULL,
                                            `smsID` varchar(220) NOT NULL,
                                            `note` varchar(100) NOT NULL,
                                            `total` tinyint(3) NOT NULL,
                                            `admin_ID` int(8) NOT NULL,
                                            `customer_ID` int(8) NOT NULL,
                                            `recipient` varchar(100) NOT NULL,
                                            `subject` varchar(100) NOT NULL,
                                            `change` tinyint(1) NOT NULL DEFAULT '1',
                                            `campaign` int(11) DEFAULT NULL,
                                            PRIMARY KEY (`ID`),
                                            KEY `vyber1` (`date`),
                                            KEY `vyber2` (`date`,`type`),
                                            KEY `vyber3` (`date`,`type`,`status`)
                                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_textsenderIDs (
                                            `textsenderID` varchar(11) CHARACTER SET utf8 NOT NULL,
                                            UNIQUE KEY `textsenderID` (`textsenderID`)
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_answers (
                                            `ID` int(5) NOT NULL AUTO_INCREMENT,
                                            `text` text NOT NULL,
                                            `from` varchar(50) NOT NULL DEFAULT '',
                                            `prohlednuto` tinyint(3) NOT NULL DEFAULT '0',
                                            `smsc` varchar(100) NOT NULL DEFAULT '',
                                            `cas` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                            PRIMARY KEY (`ID`)
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "sp_outofstock (
                                            `ID` int(8) NOT NULL AUTO_INCREMENT,
                                            `datum` date NOT NULL,
                                            `product_ID` int(8) NOT NULL,
                                            PRIMARY KEY (`ID`)
                                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "sp_marketing_filter` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `name` varchar(200) NOT NULL,
                                            `filter` text,
                                            `disabled` text,
                                            `disabled_counter` int(11) NOT NULL DEFAULT '0',
                                            `date` datetime NOT NULL,
                                            PRIMARY KEY (`id`)
                                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "sp_template` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `name` varchar(200) NOT NULL,
                                            `template` text,
                                            `unicode` tinyint(1) NOT NULL DEFAULT '0',
                                            `unique` tinyint(1) NOT NULL DEFAULT '0',
                                            `date` datetime NOT NULL,
                                            `type` int(11) NOT NULL,
                                            PRIMARY KEY (`id`)
                                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "sp_exceptions` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `prefix` int(11) NOT NULL,
                                        `firstPrefix` int(11) NOT NULL,
                                        `length` int(11) NOT NULL,
                                        `trim` int(11) NOT NULL,
                                        PRIMARY KEY (`id`)
                                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $wpdb->get_results("CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "sp_config` (
                                        `shop_id` int(11) NOT NULL,
                                        `config_name` varchar(200) NOT NULL,
                                        `config_value` text NOT NULL,
                                        PRIMARY KEY (`shop_id`,`config_name`)
                                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    /**
     * Woosms uninstall script
     */  
    function woosms_deactivate() 
    {
        global $wpdb;

        $deleteDB = false;

        $result = $wpdb->get_results("SELECT deletedb FROM " . _DB_PREFIX_ . "sp_smsuser WHERE ID=1");

        if(is_array($result) && count($result))
        {
            foreach($result AS $row)
            {
                $deleteDB = ($row->deletedb == 1);
            }
        }        

        if($deleteDB)
        {
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_settings");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_smsuser");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_admins");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_hooks_admins_multi");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_hooks_customers");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_variables");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_hooks_templates");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_hooks_unicode");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_ownnumbersenderIDs");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_routes");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_sms_history");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_textsenderIDs");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_answers");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_outofstock");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_optout_deleted");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_optout_prices");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_optout_texts");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_marketing_filter");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_template");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_exceptions");
            $wpdb->get_results("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "sp_config");

        }
    }

    /**
     * Register install & uninstall scripts
     */         
    register_activation_hook(__FILE__, 'woosms_activate');
    register_deactivation_hook(__FILE__, 'woosms_deactivate');

}
else 
{
    /**
     * Woocommerce is not installed
     */         
    deactivate_plugins( plugin_basename( __FILE__ ) );    
}