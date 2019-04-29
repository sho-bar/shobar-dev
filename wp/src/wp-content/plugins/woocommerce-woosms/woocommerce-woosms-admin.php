<?php
error_reporting(1);
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

add_action("init", "woosms_init_controler");
add_action('admin_menu', 'woosms_add_admin_menu');
add_action('admin_init', 'woosms_register_settings');

require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'exc.php');

function woosms_init_controler() {
    global $smscontroller;

    switch (filter_input(INPUT_GET, "page")) {
        case "woosms_profile_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'profile.php');

            $smscontroller = new ControllerSmsProfile();
            break;
        case "woosms_send_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'send.php');

            $smscontroller = new ControllerSmsSend();
            break;
        case "woosms_marketing_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'marketing.php');

            $smscontroller = new ControllerSmsMarketing();
            break;
        case "woosms_history_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'history.php');

            $smscontroller = new ControllerSmsHistory();
            break;
        case "woosms_statistics_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'statistics.php');

            $smscontroller = new ControllerSmsStatistics();
            break;
        case "woosms_answer_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'answers.php');

            $smscontroller = new ControllerSmsAnswers();
            break;
        case "woosms_admin_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'admin.php');

            $smscontroller = new ControllerSmsAdmin();
            break;
        case "woosms_customers_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'customer.php');

            $smscontroller = new ControllerSmsCustomer();
            break;
        case "woosms_settings_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'settings.php');

            $smscontroller = new ControllerSmsSettings();
            break;
        case "woosms_credit_page":

            require_once(dirname(__FILE__) . SMS_INCLUDES_DIR . 'credit.php');

            $smscontroller = new ControllerSmsCredit();
            break;
    }
}

function woosms_add_admin_menu() 
{   
    if(defined("SMS_DEMO"))
    {
        define_menu("read");
    }
    else
    {
        define_menu();
    }

    woosms_admin_menu_style();
    woosms_admin_menu_script();

    add_filter('plugin_action_links', 'woosms_add_settings_link', 10, 2);
}

function define_menu($capabilities = 'manage_options')
{
    global $wp_version;
    
    if(((float)$wp_version) >= 3.8)
    {
        $icon = 'dashicons-email-alt';
    }
    else
    {
        $icon = plugins_url(WOOSMS_DIR.'/img/logo.png');
    }    
    
    add_menu_page('woosms_profile_page', "Woo".v_smsanswers_sms, $capabilities, 'woosms_profile_page', '', $icon);
    add_submenu_page('woosms_profile_page', v_adminsmsprofile_editaccount, v_adminsmsprofile_editaccount, $capabilities, 'woosms_profile_page', 'woosms_profile_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_send, v_smsprestashop_send, $capabilities, 'woosms_send_page', 'woosms_send_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_marketing, v_smsprestashop_marketing, $capabilities, 'woosms_marketing_page', 'woosms_marketing_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_history, v_smsprestashop_history, $capabilities, 'woosms_history_page', 'woosms_history_page');
    add_submenu_page('woosms_profile_page', v_statistics, v_statistics, $capabilities, 'woosms_statistics_page', 'woosms_statistics_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_answers, v_smsprestashop_answers, $capabilities, 'woosms_answer_page', 'woosms_answer_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_admin, v_smsprestashop_admin, $capabilities, 'woosms_admin_page', 'woosms_admin_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_customer, v_smsprestashop_customer, $capabilities, 'woosms_customers_page', 'woosms_customers_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_settings, v_smsprestashop_settings, $capabilities, 'woosms_settings_page', 'woosms_settings_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_credit, v_smsprestashop_credit, $capabilities, 'woosms_credit_page', 'woosms_credit_page');
    add_submenu_page('woosms_profile_page', v_smsprestashop_about, v_smsprestashop_about, $capabilities, 'woosms_about_page', 'woosms_about_page');

    add_options_page("WooSMS module for WordPress & WooCommerce".SMS_VERSION, "Woo".v_smsanswers_sms, $capabilities, 'woosms_admin_menu', 'woosms_display_admin_menu');
}

function woosms_register_settings() {
    register_setting("WOOSMS_CFG_SETTINGS_GROUP", 'woosms_settings', 'woosms_settings_validate');
    add_settings_section('woosms_section', "WooSMS ".v_menu_setting, 'woosms_settings_description', "WOOSMS_CFG_SETTINGS_SECTION");

    add_settings_field('woosms_language', ucfirst(trim(v_customersms_langversion)).": ", 'woosms_language_field', "WOOSMS_CFG_SETTINGS_SECTION", 'woosms_section');
    add_settings_field('woosms_show_example_ui', ucfirst(trim(v_sendsms_timezone2)).":", 'woosms_timezone_field', "WOOSMS_CFG_SETTINGS_SECTION", 'woosms_section');
}

function woosms_settings_validate($input) {
    return $input;
}

function woosms_settings_description() {
    echo '<p>'.v_about_copyright.'</p>';
}

function woosms_timezone_field() {

    $options = get_option('woosms_settings');

    if (!isset($options['timezone'])) {
        $date = new DateTime();
        $tz = $date->getTimezone();
        $timezone = $tz->getName();

        if ($timezone == NULL || empty($timezone)) {
            $timezone = "Europe/Prague";
        }
    } else {
        $timezone = $options['timezone'];
    }

    $timezoneArray = DateTimeZone::listIdentifiers(2047);

    echo '<select name="woosms_settings[timezone]">';
    foreach ($timezoneArray as $value) {
        if ($timezone == $value) {
            $selected = " selected=\"selected\"";
        } else {
            $selected = "";
        }
        echo "<option value=\"" . $value . "\"" . $selected . ">" . $value . "</option>";
    }

    echo '</select>';
}

function woosms_language_field() {

    global $array_langs;

    $options = get_option('woosms_settings');

    $lang = (isset($options['language'])) ? $options['language'] : "en";

    $array_lang_names = explode("|", array_lang_names);
    $array_langs = explode("|", array_langs);

    echo '<select name="woosms_settings[language]">';
    foreach ($array_langs as $key => $value) {
        if ($lang == $value) {
            $selected = " selected=\"selected\"";
        } else {
            $selected = "";
        }
        echo "<option value=\"" . $value . "\"" . $selected . ">" . $array_lang_names[$key] . "</option>";
    }

    echo '</select>';
}

function woosms_display_admin_menu() {

    if (!current_user_can('manage_options')) {
        wp_die("You don't have permission to edit the settings.");
    }

    echo '<div class="wrap">';
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2>'.v_about_version_wo.SMS_VERSION.'</h2>';

    echo '<form method="post" action="options.php">';

    settings_fields("WOOSMS_CFG_SETTINGS_GROUP");
    do_settings_sections("WOOSMS_CFG_SETTINGS_SECTION");

    echo '<p class="submit">
    <input type="submit" class="button-primary" value="'.v_smswizard_savesettings.'" />
    </p>';

    echo '</form>'; 

    echo '</div><hr/>'; 
    echo woosms_about_page();
}

function woosms_admin_menu_style() {
    wp_enqueue_style('woosms', plugins_url(WOOSMS_DIR.'/css/style.css'));
    wp_enqueue_style('woosms-date-picker',  plugins_url(WOOSMS_DIR.'/css/jquery.datetimepicker.css'));
}


function woosms_admin_menu_script() {
}

function woosms_add_settings_link($links, $file) {

    $file = explode('/', $file);
    $file = $file[0];

    if ($file == WOOSMS_DIR) {
        $settings_link = '<a href="options-general.php?page=woosms_admin_menu">'.v_menu_setting.'</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

function woosms_attach_button_script($plugin_array) {
    $plugin_array['woosms_jquery'] = "//code.jquery.com/jquery-1.11.0.min.js";
    $plugin_array['woosms_jquery_m'] = "//code.jquery.com/jquery-migrate-1.2.1.min.js";
    $plugin_array['woosms_jquery_caret'] = WP_PLUGIN_URL.'/'.WOOSMS_DIR.'/js/jquery.at.caret.min.js';
    $plugin_array['woosms_sms'] = WP_PLUGIN_URL.'/'.WOOSMS_DIR.'/js/sms.js';

    return $plugin_array;
}

function woosms_profile_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_send_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_marketing_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_history_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_statistics_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_answer_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_admin_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_customers_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_settings_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}
 
function woosms_credit_page() {
    global $smscontroller;
    echo "<div class=\"woosms-admin\">".$smscontroller->display()."</div>";
}

function woosms_about_page() {
    echo '
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=529113080511289&version=v2.0";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, \'script\', \'facebook-jssdk\'));</script>        

        <div style=\'padding: 20px;\'>
            <h1>' . v_about_about . '</h1>
            <p><img alt="woosms" style="float:right;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAagAAAA4CAYAAACoo2w3AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMkU1REQ0NDQxNjUxMUU0OUIyMTg3Qzk4QkUyOUY5QSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMkU1REQ0NTQxNjUxMUU0OUIyMTg3Qzk4QkUyOUY5QSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkMyRTVERDQyNDE2NTExRTQ5QjIxODdDOThCRTI5RjlBIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkMyRTVERDQzNDE2NTExRTQ5QjIxODdDOThCRTI5RjlBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+AY3riQAACE5JREFUeNrsne9x2zYYh6Gcv9edQMwEVScwPUGdCSrls3N2J7A9gZ3L90ieIO4EkSeoMoHpCapMwAIxdHVVScSLPxRBPc8dT4lFguBL4v3xR0Dg4PP405NSqlDu3LyffbgWrK/0PmrB6qe6/Lmg7GP98beg/Jkuf6IAek5d1wQBsuaNXhbCbYZCcSqE5Y8Sr/+N0w4AkIdASRN2kXj9oXB9qUAtOO0AAHkI1Fy4TZl4fangiARN8vgQAAD2K1BiRyF8bJfaEUnWxz0BAOQiUJPp+VJ/VsLtikTrGo7twIcUAoV7AgDIyEH5OIsy0boi0bFCJhEzBkgAAGQmUNLE7fTYTuiEfFwRAyQAAHouUHPhdkUiAREJoLT897MPCBQAQE4CNZmeSwWqTCxQrttJBmDMOd0AAPk5KIPIXTiO5Bt61ivFIz7cEwDAIQiUcnvM5+ugXPuuJOUzQAIAIFOBkibwMqFAGYdWNnwvHcE353QDAByGgxpGFhCpQ5OI3/L97EPF6QYAyFCgPAZKxBSQ1OXT/wQAkBlHGxK5a+IvEwvUSYiDW+MxpCJNjxs3UKV2bB2s0yjQMa+uv2XDTUuRsHzf671S8tlYksd2MBj859jrul4qgAMRqB8j+XYkvWFg3fbmoGzy/90mJa+EqMswH8aV/qle3kEVlBw6WCdTh7FefotwM7LiVO3uKzT7u4qwn7m9Jj42CIvZ34Xg+Ey593p5CBDCfcUWoHO8Wft/zFdvZPeIz/Sb6eWr/udXmyCKwGMwYnKrlydd7thTVDpXJ8212d6KxSjD697E4NIew+2Wa+svvUyFx1fabUy5ZwcaW4BkAhVzTr7QRLr1MZZwAIbTAAn7u64n5Td3YBOmrlO9j0vh8XeuTjYBX/WoDVzaY3odly+B4rAqY3zgsQWIJ1AeAyWGKQVqRxkpZjCfqvA+lCZuhX1HXavTtUfSzYHxK8dzG+naXZ2/Q48tQDQHJXVRhcT5RBQoSfmNjy3to66ypZhPHd1T1+pkhPKix23hSv3b9xO73EOPLUA0gZK4qDLAPbl0Iv/i4dx8jqfN5FBo8TnLsE7jFtzcPhkp/36jpjYyOvDYAkQTKNFAiS1z8rkIlItTKwIE0Gk/tj+r7c7ok9zqpF5GEPadVDcFZ8QWII5AxZiT78Rhu3vHu1qJc1unchhKvY+RUiPq1EmKROWeEFuACAI1mZ7HGMnX1NCdXzOv3cTIwbGFuLR9PFo5zqxOBU0lmfARW4AtHG35+1zgUoYejW6hnc3c/nDUpXEvPBu0y+NK6d2ric36zBQ/KVk/wiizOkmT6J1evgsdRhn52q62uHTTrynta5rp5XntbxeC2BY9iy3AXgVqIbioizWH47Jd9eqzqYGaxPnQ4Nh2Je6oSU8L6+kWp2fEcLqHc9i1OpkE+kcCV+kjUNdbvnsSCIMRp8mW8jsd27quf3wOBgMGYECWvAlwHtsEw6XhP68J1S5+cXBsGzEuLUHS8/kuqUB1rE7fPbdbqrDpgWLFbNu1mm1szRx8zMMHfRKokLfrugjUSjhcJnEtPARQfAwQjRNCQGwBkgmUx0CJYofj2XX36XJXN2pwbAhUeiR33+b8mHkDzwgbsQVI4aBeuxzXhuPscF7Njec6ietog1Nrgle8x8NnZKeZi8709VwrRqoRW4DIAiVpOMMdjmeX8Lnu49hV/HBQyag8tjHn68om0y/c+RNbgFgC9U3YWP73m6Wmxmh/ROvyiKPc4NSaXNqc0xuVh8Dtz7jzJ7YAsQRKkuBLgcN59nA6ww1ODffULh8jlfP6zr+N2doPNrYML4feCtRkel4pQQeu7R9ycVALDzEpBAIoFVdww1wPd5HLHKuwl/sR24bYapHi0R/00kFJnYgRD8kIvhUuv+0o1z6bYIBEGm4SuFPfl/sRW8fYapEac+lCHwXqUVBWqdxG8C183I7wHVM84kuDcdRm1opZgrKn6rD7TpLGVotUweULh+ygTP/QyKO8SiCATmwQQYibSCc2mVaRy74itsQWIIVAuQhItUFMqsgNbM5pbQUT57d6eRcx5vSXEFsAN4Gy4uE6UMLlEcK3FkQF99QuD/aO3yTUOxU2n96xYubtZLEdDAbEFnrloGIn/G1uKeZElgyQ2A/m3JqZtn9WL4+pfK+bglASWwBXgXqM3NBSi8qc07p3Znr51S4zkiixBei8g9oxu0OsfSwFfVqQnoW9478hFMQWoMsCVXl+J2200D1wtcQWIL5ACQdKeAlUxGHhj5xSAIDDcVCx7tKaxCOGi0rtoMpNPxjWfzOjzy72dA7bqJMp/1b5j7C7oKkRWwApR47rmUEMob+jqBy+L/YgUFJh/KqTv2ohFl2r06VdUnOIryYntgB7dlBNCTD08ZzvAIlqD3GvMqxTW9CPSGwBRAK1aKFxhCbIeUaNtsqwTodWjz5CbKF/AmVfLBhycS9tGSkbj9dvqSIcmw+PudWpxQRKEk0U27quiS300kGF3tUvEpcf4qAM9y3G3IjPQ6Z1Ss09TZLYAvgIVMhsD43iI3j9ewqBC53nTMKNg5vsap1Si+QdTZLYAvgIVIhDeU4sMlVIgrXbvmsh3jO9r7tc65SYiWKUWbLY1nVNbKHXApX6EV/IPoIHFdhpmE4TJsmZepnwM+s6Jbq7N0L8QHNME1stTsQW+i1QgR33rtt99yw/ymSzVhDMqw1uVLzO+h+vTNBlT3xcXhfrFInKHtNbxCldbBEnyJkj4frmoi88kqxrYp0FOIEo2IR9bZbP40/mDcE+7ygyx1vtmBw3tzqtEl4M5vbOfuG5bYybIjNg4DFwn7Fikiy2WpzIcJA1/wgwACqCvlSvgnKYAAAAAElFTkSuQmCC" />
                    <b>' . v_about_version_wo . number_format(SMS_VERSION, 2) . '</b><br/>
                ' . v_about_copyright . '<br/><br/>

                <b>' . v_about_supported_text_wo . '</b> ' . SMS_SUPPORT_VERSION . '<br/><br/>

                <b>' . v_about_developer . '</b><br/>
                ' . v_about_topefekt . '<br/><br/>

                <b>' . v_about_address . '</b><br/>
                ' . v_about_street . '<br/>
                ' . v_about_city . '<br/>
                ' . v_about_state . '<br/>
                ' . v_about_vat . '<br/><br/>

                <b>' . v_about_phone . '</b><br/>
                ' . v_about_phonenr . '<br/><br/>

                <b>' . v_about_mail . '</b><br/>
                <a href="mailto:' . v_about_mailsales . '">' . v_about_mailsales . '</a><br/>
                <a href="mailto:' . v_about_mailsupport . '">' . v_about_mailsupport . '</a><br/><br/>

                <b>' . v_about_web . '</b><br/>
                 <a href="http://' . v_about_website_name_wo . '">' . v_about_website_name_wo . '<br/></a><br/>
                     
                <b>Facebook:</b><br/>
                <div class="fb-like" data-href="https://www.facebook.com/woosms" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                <br/><br/>

                <b>' . v_about_manual_text . '</b><br/>
                <a href="http://' . v_about_manual_url_wo . '">' . v_about_manual_url_wo . '</a><br/><br/><br/>
                <img alt="TOPefekt" src="data:image/gif;base64,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" />
            </p>
        </div>';
}
