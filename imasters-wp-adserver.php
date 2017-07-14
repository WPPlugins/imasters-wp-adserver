<?php
/*
Plugin Name: iMasters WP AdServer
Plugin URI: http://code.imasters.com.br/wordpress/plugins/imasters-wp-adserver/
Description: iMasters WP AdServer is used to manage ads in your WordPress based site. A complete support to manage ads with date expiration controll, clicks and impressions statistics.
Author: Apiki
Version: 0.1.1
Author URI: http://apiki.com/
*/

/* Copyright 2009  Apiki (email : leandro@apiki.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !function_exists( 'add_action' ) ) {
    $wp_root = '../../..';
    if ( file_exists( $wp_root.'/wp-load.php' ) ) {
        require_once( $wp_root.'/wp-load.php' );
    } else {
        require_once( $wp_root.'/wp-config.php' );
    }
}

class IMASTERS_WP_AdServer {

    var $version = '0.1';

    var $date_format = '';
    
    var $date_split = '';

    function IMASTERS_WP_AdServer()
    {
        global $wpdb, $imasters_wp_adserver;
        
        $wpdb->imasters_wp_adserver_ads = $wpdb->prefix . 'imasters_wp_adserver_ads';
        $wpdb->imasters_wp_adserver_ads_default	= $wpdb->prefix . 'imasters_wp_adserver_ads_default';
        $wpdb->imasters_wp_adserver_groups = $wpdb->prefix . 'imasters_wp_adserver_groups';

        add_action( 'activate_imasters-wp-adserver/imasters-wp-adserver.php', array( &$this, 'install' ) );

        add_action( 'init', array( &$this, 'textdomain' ) );

        add_action( 'wp_print_scripts', array( &$this, 'header' ) );

        add_action( 'admin_menu', array( &$this, 'menu' ) );

        $this->_get_ajax_actions();

        $this->set_date_format();
        
        $this->set_date_split();
    }

    function set_date_format()
    {
        $this->date_format = get_option( 'imasters_wp_adserver_date_format' );
    }

    function set_date_split()
    {
       $this->date_split = ( 'dmy' == $this->date_format ) ? '/' : '-';
    }

    function install()
    {
        global $wpdb;

        // This file contains the dbDelta function, and it´s not loaded by default.
        require_once ABSPATH . 'wp-admin/upgrade-functions.php';

        // Check if the table imasters_wp_adserver_ads was already created
        if ( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->imasters_wp_adserver_ads'") != $wpdb->imasters_wp_adserver_ads ) :

            // Build the SQL for the plugin tables
            $sql_table_ads = "CREATE TABLE " . $wpdb->imasters_wp_adserver_ads . " (
                ad_id INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                ad_title VARCHAR( 100 ) NOT NULL,
                ad_ad TEXT NOT NULL,
                ad_link VARCHAR ( 255 ) NOT NULL,
                ad_group_id INT( 11 ) UNSIGNED NOT NULL,
                ad_active SMALLINT( 1 ) UNSIGNED NOT NULL,
                ad_expiration_date DATE NOT NULL,
                ad_max_clicks INT( 11 ) UNSIGNED NOT NULL,
                ad_max_impressions INT( 11 ) UNSIGNED NOT NULL,
                ad_total_clicks INT( 11 ) UNSIGNED NOT NULL,
                ad_total_impressions INT( 11 ) UNSIGNED NOT NULL,
                ad_registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                )  ENGINE = MYISAM COMMENT = 'Table used by iMasters WP AdServer WordPress plugin';
            ";

            // This function examines the current table structure, compares it to the desired table structure, and either adds or modifies the table as necessary
            dbDelta($sql_table_ads);
        endif;

        // Check if the table imasters_wp_adserver_ads_defaults was already created
        if ( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->imasters_wp_adserver_ads_default'") != $wpdb->imasters_wp_adserver_ads_default ) :

            // Build the SQL for the plugin tables
            $sql_table_ads_default = "CREATE TABLE " . $wpdb->imasters_wp_adserver_ads_default . " (
                ad_default_id INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                ad_default_title VARCHAR( 100 ) NOT NULL,
                ad_default_ad TEXT NOT NULL,
                ad_default_link VARCHAR( 255 ) NOT NULL,
                ad_default_group_id INT( 11 ) UNSIGNED NOT NULL
                )  ENGINE = MYISAM COMMENT = 'Table used by iMasters WP AdServer WordPress plugin';
            ";

            // This function examines the current table structure, compares it to the desired table structure, and either adds or modifies the table as necessary
            dbDelta($sql_table_ads_default);
        endif;

        // Check if the table imasters_wp_adserver_groups was already created
        if ( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->imasters_wp_adserver_groups'") != $wpdb->imasters_wp_adserver_groups ) :

            // Build the SQL for the plugin tables
            $sql_table_groups = "CREATE TABLE " . $wpdb->imasters_wp_adserver_groups . " (
                group_id INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                group_name VARCHAR( 100 ) NOT NULL,
                group_dimension VARCHAR( 9 ) NOT NULL
                )  ENGINE = MYISAM COMMENT = 'Table used by iMasters WP AdServer WordPress plugin';
            ";

            // This function examines the current table structure, compares it to the desired table structure, and either adds or modifies the table as necessary
            dbDelta($sql_table_groups);
        endif;

        //
        $adsDimensions = array( '728x90', '468x60', '120x60' );

        // Plugin options
        add_option('imasters_wp_adserver_db_version', '0.1' );
        add_option('imasters_wp_adserver_download_path', WP_CONTENT_DIR . '/ads/' );
        add_option('imasters_wp_adserver_download_url', WP_CONTENT_URL . '/ads/');
        add_option('imasters_wp_adserver_date_format', 'ymd');
        add_option('imasters_wp_adserver_ads_dimensions', serialize( $adsDimensions ) );

        // Create the folder where the adds will be alocated
        if ( !is_dir(WP_CONTENT_DIR . '/ads') ) :
            mkdir(WP_CONTENT_DIR . '/ads');
        endif;
    }

    function textdomain()
    {
        load_plugin_textdomain( 'imasters-wp-adserver', 'wp-content/plugins/imasters-wp-adserver/languages' );
    }

    function menu()
    {
        // Create the parent page
        if ( function_exists('add_menu_page') ) :
            add_menu_page(__('iMasters WP AdServer', 'imasters-wp-adserver'), __('iMasters WP AdServer', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-manager.php', '' , plugins_url( 'imasters-wp-adserver/assets/images/imasters.png' ) );
        endif;

        // Create the submenu pages
        if ( function_exists('add_submenu_page') ) :
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Manager ads', 'imasters-wp-adserver'), __('Manager ads', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-manager.php');
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Add ad', 'imasters-wp-adserver'), __('Add ad', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-add.php');
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Manager group ads', 'imasters-wp-adserver'), __('Manager group ads', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-manager-groups.php');
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Manager defaults ads', 'imasters-wp-adserver'), __('Manager defaults ads', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-manager-default-ad.php');
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Options', 'imasters-wp-adserver'), __('Options', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-options.php');
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Help', 'imasters-wp-adserver'), __('Help', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-help.php');
            add_submenu_page('imasters-wp-adserver/imasters-wp-adserver-manager.php', __('Uninstall', 'imasters-wp-adserver'), __('Uninstall', 'imasters-wp-adserver'), 10, 'imasters-wp-adserver/imasters-wp-adserver-uninstall.php');
        endif;
    }
    
    function header() {
        $datepicker_version = filemtime( dirname( __FILE__ ) . '/assets/javascript/jquery.datepicker.js' );
        $imasters_ads_version =  filemtime( dirname( __FILE__ ) . '/assets/javascript/imasters-wp-adserver-scripts.js' );
    
        wp_enqueue_script('imasters-wp-adserver-jquery-datepicker', WP_PLUGIN_URL . '/imasters-wp-adserver/assets/javascript/jquery.datepicker.js', array( 'jquery' ), $datepicker_version);
        wp_enqueue_script('imasters-wp-adserver-scripts', WP_PLUGIN_URL . '/imasters-wp-adserver/assets/javascript/imasters-wp-adserver-scripts.js', array( 'jquery' ), $imasters_ads_version);
        
        echo "\n<!-- START - Generated by iMasters WP AdServer " . $this->version . " -->";
        echo '<script type="text/javascript">'."\n";
        echo '/* <![CDATA[ */'."\n";
        echo "\t".'var imasters_wp_adserver_admin_ajax_url = \''.WP_PLUGIN_URL . '/imasters-wp-adserver/imasters-wp-adserver.php'."';\n";
        echo '/* ]]> */'."\n";
        echo '</script>'."\n";
        echo "\n" . '<link rel="stylesheet" type="text/css" media="screen" href="' . WP_PLUGIN_URL . '/imasters-wp-adserver/assets/css/imasters-wp-adserver-styles.css" />';
        echo "\n<!-- END - Generated by iMasters WP AdServer " . $this->version . " -->\n";
    }

    function get_ad_type($ad_string)
    {
        // Check if we have an ad in JavaScript format
        $isJavaScriptAd = preg_match('@text\/javascript@', $ad_string);

        if ( $isJavaScriptAd )
            return 'JavaScript';

        // Check if we have an ad in HTML format
        $isHTMLAd = preg_match('@<(.*)>(.*)@', $ad_string);

        if ( $isHTMLAd )
            return 'HTML';

        // Check if we have an image ad
        $isImageAd = preg_match('@([0-9A-Fa-f]{32}).(gif|jpg|jpeg|png)@', $ad_string, $arrPatterns);

        if ( $isImageAd )
            //return sprintf('Image (%s)', $arrPatterns[2]);
            return 'Image';

        // Check if we have a flash ad
        $isFlashAd = preg_match('@([0-9A-Fa-f]{32}).swf@', $ad_string);

        if ( $isFlashAd )
            return 'Flash';

        // Check if the ad is remote
        $isRemoteAd = preg_match('@(http://|ftp://)(.*).(gif|jpg|jpeg|png|swf)@', $ad_string, $arrPatterns);

        if ( $isRemoteAd )
            //return sprintf('Remote ad (%s)', $arrPatterns[3]);
            return 'Remote';
    }

    function get_ad_structure($ad_string, $ad_id = '', $ad_link = '', $ad_group_id = '', $is_default_ad = false )
    {
        global $wpdb;

        // Get the ad type
        $ad_type = $this->get_ad_type($ad_string);

        //
        $ad_id 			= (int)$ad_id;
        $ad_group_id 	= (int)$ad_group_id;

        if ( !$is_default_ad ) :
            // Get the dimensions of the ad by it´s group
            $ad_dimension = strtolower($wpdb->get_var("SELECT group_dimension FROM $wpdb->imasters_wp_adserver_groups WHERE group_id = $ad_group_id"));
        else :
            // Get the dimensions of the ad by it´s group
            $ad_dimension = strtolower($wpdb->get_var("SELECT group_dimension FROM $wpdb->imasters_wp_adserver_groups WHERE group_id = $ad_group_id"));
        endif;

        //
        $arrAdDimension 	= explode("x", $ad_dimension);

        $ad_dimension_width 	= $arrAdDimension[0];
        $ad_dimension_height 	= $arrAdDimension[1];

        switch($ad_type) :
            case 'JavaScript' :
                echo $ad_string;
            break;
            case 'HTML' :
                echo $ad_string;
            break;
            case 'Image' :
                printf('<a href="%s?a=%d&amp;u=%s"><img src="%s/%s" width="%d" height="%d" alt="" /></a>',
                    WP_PLUGIN_URL . '/imasters-wp-adserver/imasters-wp-adserver.php',
                    $ad_id,
                    $ad_link,
                    WP_CONTENT_URL . '/ads',
                    $ad_string,
                    $ad_dimension_width,
                    $ad_dimension_height
                );
            break;
            case 'Remote' :
                printf('<a href="%s?a=%d&amp;u=%s"><img src="%s" width="%d" height="%d" alt="" /></a>',
                    WP_PLUGIN_URL . '/imasters-wp-adserver/imasters-wp-adserver.php',
                    $ad_id,
                    $ad_link,
                    $ad_string,
                    $ad_dimension_width,
                    $ad_dimension_height
                );
            break;
            case 'Flash' :
                printf('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="%d" height="%d">',
                    $ad_dimension_width,
                    $ad_dimension_height
                );
                printf('<param name="movie" value="%s/%s" />',
                    WP_CONTENT_URL . '/ads',
                    $ad_string
                );
                printf('<param name="quality" value="high" />');
                printf('<embed src="%s/%s" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="%d" height="%d"></embed>',
                    WP_CONTENT_URL . '/ads',
                    $ad_string,
                    $ad_dimension_width,
                    $ad_dimension_height
                );
                printf('</object>');
            break;
        endswitch;
    }

    function get_ads_dimensions()
    {
        return unserialize( get_option( 'imasters_wp_adserver_ads_dimensions' ) );
    }

    function get_ads_dimensions_display()
    {
        $ads_dimensions = unserialize(get_option('imasters_wp_adserver_ads_dimensions'));
        $imasters_adserver_ads_dimensions_display = '';
        if ( !empty($ads_dimensions) ) :
            foreach($ads_dimensions as $ad_dimension) :
                if ( !empty($ad_dimension) )
                    $imasters_adserver_ads_dimensions_display .= $ad_dimension . "\n";
            endforeach;
        endif;
        return $imasters_adserver_ads_dimensions_display;
    }

    function get_ads($group_id, $total_banners = 1 )
    {
        global $wpdb;

        /**
         * Get the current date
         */
        $current_date = date('Y-m-d');

        /**
         * Get the group ID of the banner(s)
         */
        $group_id = (int)$group_id;

        /**
         * Get the banner(s)
         */
        if ( $total_banners > 1 ) :

            $objAds = $wpdb->get_results( $wpdb->prepare( "
                SELECT * FROM $wpdb->imasters_wp_adserver_ads
                WHERE
                ad_active = 1
                AND
                ad_group_id = %d
                AND
                ad_expiration_date >= '%s'
                ORDER BY RAND()
                LIMIT %d ",
                $group_id,
                $current_date,
                $total_banners
            ) );

        else :

            $objAds = $wpdb->get_results( $wpdb->prepare( "
                SELECT * FROM $wpdb->imasters_wp_adserver_ads
                WHERE
                ad_active = 1
                AND
                ad_group_id = %d
                AND
                ad_expiration_date >= '%s'
                ORDER BY RAND()
                LIMIT 1 ",
                $group_id,
                $current_date
            ) );

        endif;

        //
        echo "\n <!-- iMasters WP AdServer - Group ID: $group_id -->\n";

        // If we don´t have and to show, get the default ad.
        if ( !$objAds ) :
            $objAd = $wpdb->get_row("SELECT * FROM $wpdb->imasters_wp_adserver_ads_default WHERE ad_default_group_id = $group_id");
            //
            $this->get_ad_structure($objAd->ad_default_ad, $objAd->ad_default_id, $objAd->ad_default_link, $objAd->ad_default_group_id, true);
        else :

            /**
             * Loop throug the banners and show them
             */
            foreach( $objAds as $objAd ) :
                $this->get_ad_structure( $objAd->ad_ad, $objAd->ad_id, $objAd->ad_link, $objAd->ad_group_id );
                $this->_count_ad_impression($objAd->ad_id);
            endforeach;

        endif;

        //
        echo "\n <!-- / iMasters WP AdServer - Group ID: $group_id -->\n";
    }
    
    function _count_ad_impression($ad_id)
    {
        global $wpdb;
        $ad_id = (int)$ad_id;
        $wpdb->query("UPDATE $wpdb->imasters_wp_adserver_ads SET ad_total_impressions = ad_total_impressions + 1 WHERE ad_id = $ad_id");
    }

    function delete_group($group_id)
    {
       global $wpdb;
       return $wpdb->query(sprintf("DELETE FROM $wpdb->imasters_wp_adserver_groups WHERE group_id = %d", $group_id));
    }

    function delete_ad($ad_id)
    {
        global $wpdb;
        return $wpdb->query(sprintf("DELETE FROM $wpdb->imasters_wp_adserver_ads WHERE ad_id = %d", $ad_id));
    }

    function upload_ad($_FILES)
    {
        $file_path = get_option('imasters_wp_adserver_download_path');
        if ( is_uploaded_file($_FILES['ad_file']['tmp_name']) ) :
            if ( move_uploaded_file($_FILES['ad_file']['tmp_name'], $file_path . basename($_FILES['ad_file']['name'])) ) :
                return $this->_rename_file($file_path, $_FILES['ad_file']['name']);
            endif;
        endif;

        //
        return false;
    }

    function convert_date($date, $to_brazil = false)
    {
        if ( $to_brazil )
            return implode('/', array_reverse(explode('-', $date)));
        else
            return implode('-', array_reverse(explode('/', $date)));
    }

    function format_link_url($url)
    {
        $url = preg_replace('@(http://|http://www)@', '', $url);
        return substr($url, 0, 25) . '...';
    }

    function get_ctr($total_clicks, $total_impressions)
    {
        if ( $total_clicks == 0 or $total_impressions == 0 )
            return 0;
        else
            return round($total_clicks / $total_impressions, 2);
    }

    /**
     * Format bytes into TiB/GiB/MiB/KiB/Bytes
     *
     * @credits WordPress WP Download Manager plugin
     */
    function format_filesize($rawSize) {
        if($rawSize / 1099511627776 > 1) {
            return round($rawSize/1099511627776, 1).' '.__('TB', 'imasters-wp-adserver');
        } elseif($rawSize / 1073741824 > 1) {
            return round($rawSize/1073741824, 1).' '.__('GB', 'imasters-wp-adserver');
        } elseif($rawSize / 1048576 > 1) {
            return round($rawSize/1048576, 1).' '.__('MB', 'imasters-wp-adserver');
        } elseif($rawSize / 1024 > 1) {
            return round($rawSize/1024, 1).' '.__('KB', 'imasters-wp-adserver');
        } elseif($rawSize > 1) {
            return round($rawSize, 1).' '.__('bytes', 'imasters-wp-adserver');
        } else {
            return __('unknown', 'imasters-wp-adserver');
        }
    }

    /**
     * Get max file size that can be uploaded
     *
     * @credits WordPress WP Download Manager plugin
     */
    function get_max_upload_size()
    {
        $maxsize = ini_get('upload_max_filesize');
        if (!is_numeric($maxsize)) {
            if (strpos($maxsize, 'M') !== false) {
                $maxsize = intval($maxsize)*1024*1024;
            } elseif (strpos($maxsize, 'K') !== false) {
                $maxsize = intval($maxsize)*1024;
            } elseif (strpos($maxsize, 'G') !== false) {
                $maxsize = intval($maxsize)*1024*1024*1024;
            }
        }
        return $maxsize;
    }

    function _rename_file($file_path, $file_name)
    {
        // Generate a hash
        $hash = md5(uniqid(rand(), true));

        // Get path information
        $arrPathParts = pathinfo($file_path . $_FILES['ad_file']['name']);

        // Build the new name
        $file_new_name = $hash . '.' . $arrPathParts['extension'];

        // Rename the file
        $rename = rename($file_path . $file_name, $file_path . $file_new_name);

        if ( $rename ) :
            return $file_new_name;
        else :
            return $file_name;
        endif;
    }

    function _get_ajax_actions()
    {
        if ( isset($_REQUEST['ajax_action']) ) :
            switch($_REQUEST['ajax_action']) :
                case 'delete_group' :
                    echo $this->delete_group($_REQUEST['group_id']);
                break;
                case 'delete_ad' :
                    echo $this->delete_ad($_REQUEST['ad_id']);
                break;
            endswitch;
        endif;
    }
    
    function _count_ad_click($ad_id)
    {
        global $wpdb;
        $ad_id = (int)$ad_id;
        return $wpdb->query("UPDATE $wpdb->imasters_wp_adserver_ads SET ad_total_clicks = ad_total_clicks + 1 WHERE ad_id = $ad_id");
    }

    function redir($url, $ad_id )
    {
        $this->_count_ad_click( $ad_id );
        header('Location: ' . $url);
        exit;
    }

} // End IMASTERS_WP_AdServer class

$imasters_wp_adserver = new IMASTERS_WP_AdServer;

if ( isset($_GET['a'], $_GET['u']) ) :
    $imasters_wp_adserver->redir($_GET['u'], $_GET['a']);
endif;

function imasters_wp_adserver_get_ads( $group_id, $total_banners )
{
    global $imasters_wp_adserver;
    $imasters_wp_adserver->get_ads($group_id, $total_banners);
}
?>