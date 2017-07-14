<?php
// Base of the current page
$base_name = plugin_basename('imasters-wp-adserver/imasters-wp-adserver-manager.php');
$base_page = 'admin.php?page=' . $base_name;

// See what action perform 
if(!empty($_POST['do'])) :
	// Decide what to do
	switch($_POST['do']) :
		// Add a group ad
		case __('Add the ad', 'imasters-wp-adserver'):
			
			// Check the ad type and work properly
			$ad_type = $_POST['ad_type'];
			if ( 'file' == $ad_type ) :
				
				// If the error was 1 (one) the uploaded file exceeds the upload_max_filesize directive in php.ini
				if ( $_FILES['ad_file']['error'] == 1 ) :
					// Message to user
					$text = '<p style="color: red">' . sprintf(__('The uploaded file exceeds the max upload size permited (%s).'), $imasters_wp_adserver->format_filesize( $imasters_wp_adserver->get_max_upload_size() ) ) . '</p>';
					break;
				else :
					$ad_ad = $imasters_wp_adserver->upload_ad($_FILES);
				endif;
				
			elseif ( 'text' == $ad_type ) :
				$ad_ad = $_POST['ad_text'];
			elseif ( 'remote' == $ad_type ) :
				$ad_ad = $_POST['ad_remote'];
			endif;
			
			//
			if ( 'dmy' == $imasters_wp_adserver->date_format )
				$ad_expiration_date = $imasters_wp_adserver->convert_date($_POST['ad_expiration_date']);
			else
				$ad_expiration_date = $_POST['ad_expiration_date'];
			
			// Insert the ad
			$add_the_ad = $wpdb->query(sprintf("INSERT INTO $wpdb->imasters_wp_adserver_ads
				(ad_title, ad_ad, ad_link, ad_group_id, ad_active, ad_expiration_date)
				VALUES
				('%s', '%s', '%s', %d, %d, '%s')
				", $wpdb->escape(trim($_POST['ad_title'])),
				$ad_ad,
				$wpdb->escape(trim($_POST['ad_link'])),
				$wpdb->escape(trim($_POST['ad_group_id'])),
				$_POST['ad_active'],
				$ad_expiration_date
			));
			
			//
			if ( $add_the_ad ) :
				$text = sprintf('<p style="color: green;">%s - <a href="%s">%s</a></p>',
					__('Ad added successfully', 'imasters-wp-adserver'),
					$base_page,
					__('Manager the ads.', 'imasters-wp-adserver')
				);
			endif;
			
		break;
	endswitch;
endif;
?>
<?php
// Select all groups ad
$objGroupAds = $wpdb->get_results("SELECT * FROM $wpdb->imasters_adserver_groups ORDER BY group_name");
?>
<script type="text/javascript">
var MONTHS = ['<?php _e('January', 'imasters-wp-adserver'); ?>', '<?php _e('February', 'imasters-wp-adserver'); ?>', '<?php _e('March', 'imasters-wp-adserver'); ?>', '<?php _e('April', 'imasters-wp-adserver'); ?>', '<?php _e('May', 'imasters-wp-adserver'); ?>', '<?php _e('June', 'imasters-wp-adserver'); ?>', '<?php _e('July', 'imasters-wp-adserver'); ?>', '<?php _e('August', 'imasters-wp-adserver'); ?>', '<?php _e('September', 'imasters-wp-adserver'); ?>', '<?php _e('October', 'imasters-wp-adserver'); ?>', '<?php _e('November', 'imasters-wp-adserver'); ?>', '<?php _e('December', 'imasters-wp-adserver'); ?>'];
var MONTHS_ABBR = [];

for(var i in MONTHS) {
	MONTHS_ABBR.push(MONTHS[i].substr(0,3));
}

jQuery.datePicker.setLanguageStrings(
	['<?php _e('Sunday', 'imasters-wp-adserver'); ?>', '<?php _e('Monday', 'imasters-wp-adserver'); ?>', '<?php _e('Tuesday', 'imasters-wp-adserver'); ?>', '<?php _e('Wednesday', 'imasterswp-adserver'); ?>', '<?php _e('Thursday', 'imasters-wp-adserver'); ?>', '<?php _e('Friday', 'imasters-wp-adserver'); ?>', '<?php _e('Satudary', 'imasters-wp-adserver'); ?>'],
	MONTHS,
	{p:'<?php _e('Before', 'imasters-wp-adserver'); ?>', n:'<?php _e('Next', 'imasters-wp-adserver'); ?>', c:'<?php _e('Close', 'imasters-wp-adserver'); ?>', b:'<?php _e('Choose date', 'imasters-wp-adserver'); ?>'}
);

jQuery.datePicker.setDateFormat('<?php echo $imasters_wp_adserver->date_format; ?>', '<?php echo $imasters_wp_adserver->date_split; ?>');
</script>
<?php if ( !empty($text) ) : ?>
	<div id="message" class="updated fade"><?php echo $text; ?></div>
<?php endif; ?>
		
        <div class="wrap">
        	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Add ad', 'imasters-wp-adserver'); ?></h2>
                        
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="form-table" enctype="multipart/form-data">
            	<table class="form-table">
                	<tbody>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_group_id"><?php _e('Ad group', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<select id="ad_group_id" name="ad_group_id">
                                <?php
								// Select groups ad
                                                            $objGroupsAd = $wpdb->get_results("SELECT * FROM $wpdb->imasters_wp_adserver_groups ORDER BY group_name");
                                                            if ( $objGroupsAd ) :
                                                                foreach($objGroupsAd as $objGroup) :
                                                        ?>
                                	<option value="<?php echo $objGroup->group_id; ?>"><?php echo $objGroup->group_name; ?></option>
                                <?php
                                                                endforeach;
                                                        else :
                                                        ?>
                                	<option value=""><?php _e('Any group ad found. Create one first.', 'imasters-wp-adserver'); ?></option>
                                </select>
                                        <?php if( empty( $objGroupsAd ) ) : ?>
                                            <a href="<?php echo 'admin.php?page=imasters-wp-adserver/imasters-wp-adserver-manager-groups.php'?>"><?php _e( 'Add group', 'imasters-wp-adserver' ); ?></a>
                                        <?php die(); ?>
                                        <?php endif; ?>
                                <?php endif; ?>
                                
                            </td>
                        </tr>
                    	<tr valign="top">
                            <th scope="row">
                            	<label for="ad_title"><?php _e('Ad title', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="text" id="ad_title" name="ad_title" class="regular-text" value="<?php echo $ad_title; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_type_file"><?php _e('The ad', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<p>
                                    <input type="radio" id="ad_type_file" name="ad_type" value="file" checked="checked" />
                                    <label for="ad_type_file"><?php _e('Ad file.', 'imasters-wp-adserver'); ?></label>
                                    <input type="file" name="ad_file" id="ad_file" />
                                    <span class="info"><?php _e('Select an image file (gif, jpg) or a Flash file (swf).', 'imasters-wp-adserver'); ?></span>
                                </p>
                                <p>
                                    <input type="radio" id="ad_type_text" name="ad_type" value="text" />
                                    <label for="ad_type_text"><?php _e('Ad text.', 'imasters-wp-adserver'); ?></label>
                                    <textarea name="ad_text" id="ad_text" cols="50" rows="5"></textarea>
                                    <span class="info"><?php _e('JavaScript (Google AdSense) or HTML (link ad).', 'imasters-wp-adserver'); ?></span>
                                </p>
                                <p>
                                	<input type="radio" id="ad_type_remote" name="ad_type" value="remote" />
                                    <label for="ad_type_remote"><?php _e('Ad remote', 'imasters-wp-adserver'); ?></label>
                                    <input type="text" id="ad_remote" name="ad_remote" class="regular-text" value="http://" />
                                    <span class="info"><?php _e('Please include <strong>http://</strong> or <strong>ftp://</strong> in front.', 'imasters-wp-adserver'); ?></span>
                                </p>
                            </td>
                        </tr>
                         <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_link"><?php _e('Ad link', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="text" id="ad_link" name="ad_link" class="regular-text" value="<?php echo $ad_link; ?>" />
                                <span class="info"><?php _e('Please include <strong>http://</strong> or <strong>ftp://</strong> in front.', 'imasters-wp-adserver'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_active_yes"><?php _e('Active the ad?', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="radio" name="ad_active" id="ad_active_yes" value="1" checked="checked" />
                                <label for="ad_active_yes"><?php _e('Yes. Active the ad.', 'imasters-wp-adserver'); ?></label>
                                
                                <input type="radio" name="ad_active" id="ad_active_no" value="0" />
                                <label for="ad_active_no"><?php _e('No. I´ll active later in Manager ads.', 'imasters-wp-adserver'); ?></label>
                            </td>
                        </tr>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_expiration_date"><?php _e('Ad expiration date', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="text" id="ad_expiration_date" name="ad_expiration_date" class="regular-text" value="<?php echo $ad_title; ?>" />
                                <span class="info"><?php _e('Display the ad until the informed date of expiration.', 'imasters-wp-adserver'); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="do" class="button-primary" value="<?php _e('Add the ad', 'imasters-wp-adserver'); ?>" />
                </p>
            </form>
        </div><!-- Add group ad -->        
