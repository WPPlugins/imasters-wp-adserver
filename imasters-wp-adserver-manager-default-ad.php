<?php
// Base of the current page
$base_name = plugin_basename('imasters-wp-adserver/imasters-wp-adserver-manager-default-ad.php');
$base_page = 'admin.php?page=' . $base_name;

// Get an option with the download path
$file_path = get_option('imasters_wp_adserver_download_path');

// See what action perform 
if(!empty($_POST['do'])) :
	// Decide what to do
	switch($_POST['do']) :
		// Add a group ad
		case __('Add default ad', 'imasters-wp-adserver'):
			
			// Check the ad type and work properly
			
			$ad_type = $_POST['ad_type'];
			if ( 'file' == $ad_type ) :
				
				// If the error was 1 (one) the uploaded file exceeds the upload_max_filesize directive in php.ini
				if ( $_FILES['ad_file']['error'] == 1 ) :
					// Message to user
					$text = '<p style="color: red">' . sprintf(__('The uploaded file exceeds the max upload size permited (%s).'), $imasters_wp_adserver->format_filesize( $imasters_wp_adserver->get_max_upload_size())) . '</p>';
					break;
				else :
					$ad_default_ad = $imasters_wp_adserver->upload_ad($_FILES);
				endif;
				
			elseif ( 'text' == $ad_type ) :
				$ad_default_ad = $_POST['ad_text'];
			elseif ( 'remote' == $ad_type ) :
				$ad_default_ad = $_POST['ad_remote'];
			endif;
			
			// Insert a default ad
			$add_default_ad = $wpdb->query(sprintf("INSERT INTO $wpdb->imasters_wp_adserver_ads_default
				(ad_default_title, ad_default_ad, ad_default_link, ad_default_group_id)
				VALUES
				('%s', '%s', '%s', %d)
				", $wpdb->escape(trim($_POST['ad_default_title'])),
				$ad_default_ad,
				$wpdb->escape(trim($_POST['ad_default_link'])),
				$wpdb->escape(trim($_POST['ad_default_group_id']))
			));
			
			// If group ad was added, define a message to the user
			if ( $add_default_ad )
				$text = sprintf('<p style="color: green;">%s</p>', __('Default ad added successfully', 'imasters-wp-adserver'));
		
		break;
	endswitch;
endif;
?>
<?php
// Select all groups ad
$objGroupAds = $wpdb->get_results("SELECT * FROM $wpdb->imasters_wp_adserver_groups ORDER BY group_name");
?>	
<?php if ( !empty($text) ) : ?>
	<div id="message" class="updated fade"><?php echo $text; ?></div>
<?php endif; ?>
		
        <div class="wrap">
        	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Manager defaults ads', 'imasters-wp-adserver'); ?></h2>
            
            <table class="widefat">
				<thead>
					<tr>
						<th><?php _e('Default ad', 'imasters-wp-adserver'); ?></th>
                        <th><?php _e('Default ad link', 'imasters-wp-adserver'); ?></th>
						<th><?php _e('Group ad', 'imasters-wp-adserver'); ?></th>
                        <th><?php _e('Ad type', 'imasters-wp-adserver'); ?></th>
						<th colspan="3"><?php _e('Actions', 'imasters-wp-adserver'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				// Get default ads
				$objDefaultAds = $wpdb->get_results("SELECT * FROM $wpdb->imasters_wp_adserver_ads_default INNER JOIN $wpdb->imasters_wp_adserver_groups ON $wpdb->imasters_wp_adserver_ads_default.ad_default_group_id = $wpdb->imasters_wp_adserver_groups.group_id");
				if ( $objDefaultAds ) :
					foreach($objDefaultAds as $objDefaultAd) :
				?>
                	<tr>
                    	<td><?php echo stripslashes($objDefaultAd->ad_default_title); ?></td>
                        <td><a href="<?php echo htmlspecialchars($objDefaultAd->ad_default_link); ?>"><?php echo $imasters_wp_adserver->format_link_url($objDefaultAd->ad_default_link); ?></a></td>
                        <td><?php echo $objDefaultAd->group_name; ?></td>
                        <td><?php echo $imasters_wp_adserver->get_ad_type($objDefaultAd->ad_default_ad); ?></td>
                        <td><a href="<?php echo $base_page; ?>&amp;preview_ad=true&amp;ad_id=<?php echo $objDefaultAd->ad_default_id; ?>&amp;ad_string=<?php echo base64_encode($objDefaultAd->ad_default_ad); ?>"><?php echo _e('Preview the default ad', 'imasters-wp-adserver'); ?></a></td>
                    </tr>
                <?php
					endforeach;
				else :
				?>
                	<tr>
                    	<td colspan="7"><?php _e('Any default ad found', 'imasters-wp-adserver'); ?></td>
                    </tr>
                <?php endif; ?>
				</tbody>
			</table>
            <p>&nbsp;</p>
        </div><!-- / Manager default ad -->
        
        <div class="wrap<?php echo ( isset($_GET['preview_ad']) ) ? '' : ' hide' ?>" id="imasters-wp-adserver-preview">
        	<h2><?php _e('Preview the default ad', 'imasters-wp-adserver' ); ?></h2>
            <?php echo $imasters_wp_adserver->get_ad_structure(base64_decode($_GET['ad_string']), $_GET['ad_id']); ?>
            <p><a href="<?php echo $base_page; ?>"><?php _e('Close the preview', 'imasters-wp-adserver' ); ?></a></p>
        </div>
        
        <div class="wrap">
        	<h2><?php _e('Add default ad', 'imasters-wp-adserver'); ?></h2>
                        
            <form method="post" action="<?php echo $base_page; ?>" class="form-table" enctype="multipart/form-data">
            	<table class="form-table">
                	<tbody>
                    	<tr valign="top">
                        	<th scope="row">
                            	<label for="ad_default_title"><?php _e('Default ad title', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="text" id="ad_default_title" name="ad_default_title" class="regular-text" value="<?php echo $ad_default_title; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_type_file"><?php _e('The default ad', 'imasters-wp-adserver'); ?></label>
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
                                    <textarea name="ad_text" id="ad_text" cols="50" rows="6"></textarea>
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
                            	<label for="ad_default_link"><?php _e('Default ad link', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="text" id="ad_default_link" name="ad_default_link" class="regular-text" value="<?php echo $ad_default_link; ?>" />
                                <span class="info"><?php _e('Please include <strong>http://</strong> or <strong>ftp://</strong> in front.', 'imasters-wp-adserver'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="ad_default_group_id"><?php _e('Group for the default ad', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<select id="ad_default_group_id" name="ad_default_group_id">
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
                                <?php endif; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                	<input type="submit" name="do" class="button-primary" value="<?php _e('Add default ad', 'imasters-wp-adserver'); ?>" />
                </p>
            </form>
        </div><!-- Add group ad -->        
