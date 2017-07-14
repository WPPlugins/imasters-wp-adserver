<?php
// Base of the current page
$base_name = plugin_basename('imasters-wp-adserver/imasters-wp-adserver-manager-groups.php');
$base_page = 'admin.php?page=' . $base_name;

// Select all groups ad
$objGroupAds = $wpdb->get_results("SELECT * FROM $wpdb->imasters_wp_adserver_groups ORDER BY group_name");


// See what action perform 
if(!empty($_POST['do'])) :
	// Decide what to do
	switch($_POST['do']) :
		// Add a group ad
		case __('Add group ad', 'imasters-wp-adserver'):
			
			// Insert a group ad
			$add_group_ad = $wpdb->query(sprintf("INSERT INTO $wpdb->imasters_wp_adserver_groups
				(group_name, group_dimension)
				VALUES
				('%s', '%s')
				", 
				$wpdb->escape(trim($_POST['group_name'])),
				$wpdb->escape(trim($_POST['group_dimension']))
			));
			
			// If group ad was added, define a message to the user
			if ( $add_group_ad )
				$text = sprintf('<p style="color: green;">%s</p>', __('Group ad added successfully.', 'imasters-wp-adserver'));
		
		break;
		// Update groups ad
		case __('Update groups ads', 'imasters-wp-adserver') :
			
			// Loop through the groups ad
			$i = 0;
                        if( !empty( $objGroupAds ) ) :
                            foreach( $_POST['group_name'] as $group_name ) :
                                    // Update a specific group ad
                                    $update_groups_ad = $wpdb->query(sprintf("UPDATE $wpdb->imasters_wp_adserver_groups
                                            SET
                                            group_name = '%s',
                                            group_dimension = '%s'
                                            WHERE
                                            group_id = %d",
                                            $wpdb->escape(trim($group_name)),
                                            $wpdb->escape(trim($_POST['group_dimension'][$i])),
                                            $_POST['group_id'][$i]
                                    ));
                                    $i++;
                            endforeach;
                        endif;

			
			$text = sprintf('<p style="color: green;">%s</p>', __('Groups ads updated successfully.', 'imasters-wp-adserver'));
			
		break;
	endswitch;
endif;
?>
<?php if ( !empty($text) ) : ?>
	<div id="message" class="updated fade"><?php echo $text; ?></div>
<?php endif; ?>
		
		<div class="wrap">

			<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Manager group ads', 'imasters-wp-adserver'); ?></h2>
			
            <form method="post" action="<?php echo $base_page; ?>" class="form-table">
                <table class="form-table">
                    <tbody>
                    <?php
                                // If we have groups ad, show them.
                                if ( $objGroupAds ) :
                                    foreach($objGroupAds as $objGroup) :
					?>
                        <tr valign="top" id="group-<?php echo $objGroup->group_id; ?>">
                            <th scope="row">
                                <label for="group_name_<?php echo $objGroup->group_id; ?>"><?php _e('Group name', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="group_name_<?php echo $objGroup->group_id; ?>" name="group_name[]" class="regular-text" value="<?php echo $objGroup->group_name; ?>" />
                                <select name="group_dimension[]">
                                	<?php foreach($imasters_wp_adserver->get_ads_dimensions() as $ad_dimension) : ?>
                                    <option value="<?php echo $ad_dimension; ?>"<?php echo ( $ad_dimension == $objGroup->group_dimension ) ? ' selected="selected"' : ''; ?>><?php echo $ad_dimension; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="group_id[]" value="<?php echo $objGroup->group_id; ?>" />
                                <a href="javascript:;" onclick="IMASTERS_WP_ADSERVER.delete_group(<?php echo $objGroup->group_id; ?>, '<?php printf(__('You are about to delete this group ad, %s. \\n Choose [Cancel] to Cancel, [OK] to Delete.', 'imasters-wp-adserver'), $objGroup->group_name); ?>');"><?php _e('Delete this group ad', 'imasters-wp-adserver'); ?></a>
                                <span class="info"><?php printf('<strong>%s</strong>: %d. %s: &lt;?php imasters_wp_adserver_get_ads(<strong>%d</strong>); ?&gt;', __('ID of this group', 'imasters-wp-adserver'), $objGroup->group_id, __('Use it in the template like that', 'imasters-wp-adserver'), $objGroup->group_id); ?></span>
                            </td>
                        </tr>
                    <?php 
                                    endforeach;
                            // Show up a message if we don�t have any group ad
                                else :
                            ?>
                    	<tr valign="top">
                        	<td colspan="2"><?php _e('Any group ad found', 'imasters-wp-adserver'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="do" class="button-primary" value="<?php _e('Update groups ads', 'imasters-wp-adserver'); ?>" />
                </p>
            </form>
		</div><!-- / Manager group ads -->
        
        <p>&nbsp;</p>
        
        <div class="wrap">
        	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Add group ad', 'imasters-wp-adserver'); ?></h2>
                        
            <form method="post" action="<?php echo $base_page; ?>" class="form-table">
            	<table class="form-table">
                	<tbody>
                    	<tr valign="top">
                        	<th scope="row">
                            	<label for="group_name"><?php _e('Group name', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<input type="text" id="group_name" name="group_name" class="regular-text" value="<?php echo $group_name; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                        	<th scope="row">
                            	<label for="group_dimension"><?php _e('Group dimension', 'imasters-wp-adserver'); ?></label>
                            </th>
                            <td>
                            	<select id="group_dimension" name="group_dimension">
                                	<?php foreach($imasters_wp_adserver->get_ads_dimensions() as $ad_dimension) : ?>
                                    <option value="<?php echo $ad_dimension; ?>"><?php echo $ad_dimension; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <a href="<?php echo 'admin.php?page=imasters-wp-adserver/imasters-wp-adserver-options.php'?>"><?php _e( 'Manage group dimension', 'imasters-wp-adserver' ); ?></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="do" class="button-primary" value="<?php _e('Add group ad', 'imasters-wp-adserver'); ?>" />
                </p>
            </form>
        </div><!-- Add group ad -->        
