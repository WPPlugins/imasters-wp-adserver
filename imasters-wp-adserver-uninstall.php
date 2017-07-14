<!-- Uninstall iMasters WP AdServer -->
<?php
    if( !current_user_can('install_plugins')):
        die('Access Denied');
    endif;
$base_name = plugin_basename('imasters-wp-adserver/imasters-wp-adserver.php');
$base_page = 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$imasters_wp_adserver_tables = array( $wpdb->imasters_wp_adserver_ads, $wpdb->imasters_wp_adserver_ads_default, $wpdb->imasters_wp_adserver_groups );
$imasters_wp_adserver_settings = array( 'imasters_wp_adserver_db_version', 'imasters_wp_adserver_download_path', 'imasters_wp_adserver_download_url', 'imasters_wp_adserver_date_format', 'imasters_wp_adserver_ads_dimensions' );

//Form Process
if( isset( $_POST['do'], $_POST['uninstall_imasters_wp_adserver_yes'] ) ) :
    echo '<div class="wrap">';
    ?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Uninstall iMasters WP AdServer', 'imasters-wp-adserver') ?></h2>
    <?php
    switch($_POST['do']) {
        //  Uninstall iMasters WP AdServer
        case __('Uninstall iMasters WP AdServer', 'imasters-wp-adserver') :
            if(trim($_POST['uninstall_imasters_wp_adserver_yes']) == 'yes') :
                echo '<h3>'.__( 'Tables', 'imasters-wp-adserver').'</h3>';
                echo '<ol>';
                foreach($imasters_wp_adserver_tables as $table) :
                    $wpdb->query("DROP TABLE {$table}");
                    printf(__('<li>Table \'%s\' has been deleted.</li>', 'imasters-wp-adserver'), "<strong><em>{$table}</em></strong>");
                endforeach;
                echo '</ol>';
                echo '<h3>'.__( 'Options', 'imasters-wp-adserver').'</h3>';
                echo '<ol>';
                foreach($imasters_wp_adserver_settings as $setting) :
                    $delete_setting = delete_option($setting);
                    if($delete_setting) {
                    printf(__('<li>Option \'%s\' has been deleted.</li>', 'imasters-wp-adserver'), "<strong><em>{$setting}</em></strong>");
                    }
                    else {
                        printf(__('<li>Error deleting Option \'%s\'.</li>', 'imasters-wp-adserver'), "<strong><em>{$setting}</em></strong>");
                        }
                endforeach;
                echo '</ol>';
                echo '<br/>';
                $mode = 'end-UNINSTALL';
            endif;
        break;
    }
endif;
    switch($mode) {
    //  Deactivating Uninstall iMasters WP AdServer
    case 'end-UNINSTALL':
        $deactivate_url = 'plugins.php?action=deactivate&amp;plugin=imasters-wp-adserver/imasters-wp-adserver.php';
        if(function_exists('wp_nonce_url')) {
            $deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_imasters-wp-adserver/imasters-wp-adserver.php');
        }
        echo sprintf(__('<a href="%s" class="button-primary">Deactivate iMasters WP AdServer</a> Disable that plugin to conclude the uninstalling.', 'imasters-wp-adserver'), $deactivate_url);
        echo '</div>';
    break;
    default:
    ?>
    <!-- Uninstall iMasters WP AdServer -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
        <div class="wrap">
            <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Uninstall iMasters WP AdServer', 'imasters-wp-adserver'); ?></h2>
            <p><?php _e('Uninstaling this plugin the options and table used by iMasters WP AdServer will be removed.', 'imasters-wp-adserver'); ?></p>
            <div class="error">
                <p><?php _e('Warning:', 'imasters-wp-adserver'); ?>
                <?php _e('This process is irreversible. We suggest that you do a database backup first.', 'imasters-wp-adserver'); ?></p>
            </div>
            <table>
                <tr>
                    <td>
                    <?php _e('The following WordPress Options and Tables will be deleted:', 'imasters-wp-adserver'); ?>
                    </td>
                </tr>
            </table>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('WordPress Options', 'imasters-wp-adserver'); ?></th>
                        <th><strong><?php _e('WordPress Tables', 'imasters-wp-adserver'); ?></th>
                    </tr>
                </thead>
                <tr>
                    <td valign="top">
                        <ol>
                            <?php
                            foreach($imasters_wp_adserver_settings as $settings)
                                printf( "<li>%s</li>\n", $settings );
                            ?>
                        </ol>
                    </td>
                    <td valign="top" class="alternate">
                        <ol>
                            <?php
                            foreach( $imasters_wp_adserver_tables as $table_name )
                                printf( "<li>%s</li>\n", $table_name );
                            ?>
                        </ol>
                    </td>
                </tr>
            </table>
            <p>
                <input type="checkbox" name="uninstall_imasters_wp_adserver_yes" id="uninstall_imasters_wp_adserver_yes" value="yes" />
                <label for="uninstall_imasters_wp_adserver_yes"><?php _e('Yes. Uninstall iMasters WP AdServer now', 'imasters-wp-adserver'); ?></label>
            </p>
            <p>
                <input type="submit" name="do" value="<?php _e('Uninstall iMasters WP AdServer', 'imasters-wp-adserver'); ?>" class="button-primary" />
            </p>
        </div>
    </form>
<?php
}
?>