<?php
// Base of the current page
$base_name = plugin_basename('imasters-wp-adserver/imasters-wp-adserver-options.php');
$base_page = 'admin.php?page=' . $base_name;

// See what action perform 
if(!empty($_POST['do'])) :
	// Decide what to do
	switch($_POST['do']) :
		// Add a group ad
		case __('Save options', 'imasters-wp-adserver'):
			//
			update_option('imasters_wp_adserver_date_format', $wpdb->escape(trim($_POST['imasters_wp_adserver_date_format'])));
			//
			$ads_dimensions_post = explode("\n", trim($_POST['imasters_wp_adserver_ads_dimensions']));
			if ( !empty($ads_dimensions_post) ) :
				foreach($ads_dimensions_post as $ad_dimension) :
					if ( !empty($ad_dimension) ) 
						$arrAdsDimensions[] = trim($ad_dimension);
				endforeach;
				update_option('imasters_wp_adserver_ads_dimensions', serialize($arrAdsDimensions));
			endif;
                        echo '<div id="message" class="updated fade"><p>'.__( "The options was updated.", "imasters-wp-adserver" ).'</p></div>';
		break;
	endswitch;
endif;
?>
<?php if ( isset($_GET['updated']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e( 'The options was updated.', 'imasters-wp-adserver' ); ?></p></div>
<?php endif; ?>

<div class="wrap">
	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('iMasters WP AdServer Options', 'imasters-wp-adserver'); ?></h2>

    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="form-table">
        <table class="form-table">
            <tbody>
    			<tr valign="top">
                    <th scope="row">
                        <label for="imasters_wp_adserver_date_format"><?php _e('Date format', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>
                        <input type="radio" name="imasters_wp_adserver_date_format" id="dmy" value="dmy"<?php echo ( 'dmy' == get_option('imasters_wp_adserver_date_format') ) ? ' checked="checked"' : ''; ?> />
                        <label for="dmy">9/12/2008</label>
                        
                        <input type="radio" name="imasters_wp_adserver_date_format" id="ymd" value="ymd"<?php echo ( 'ymd' == get_option('imasters_wp_adserver_date_format') ) ? ' checked="checked"' : ''; ?> />
                        <label for="ymd">2008-12-9</label>
                        
                        <span class="info"><?php _e('Choose the better format date for you.', 'imasters-wp-adserver'); ?></span>
                    </td>
                </tr>
                <tr valign="top">
                	<th scope="row">
                    	<label for="imasters_wp_adserver_ads_dimensions"><?php _e('Ads dimensions', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>
                        <textarea id="imasters_wp_adserver_ads_dimensions" name="imasters_wp_adserver_ads_dimensions" cols="30" rows="5"><?php echo $imasters_wp_adserver->get_ads_dimensions_display(); ?></textarea>
                        <span class="info"><?php _e('Inform <strong>one dimension by line</strong> with this format: WIDTHxHEIGHT.<strong> Example: 728x90</strong>. Don´t use spaces between the dimensions.', 'imasters-wp-adserver'); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
         <p class="submit">
             <input class="button-primary" type="submit" name="do" value="<?php _e('Save options', 'imasters-wp-adserver'); ?>" />
        </p>
    </form>
</div>