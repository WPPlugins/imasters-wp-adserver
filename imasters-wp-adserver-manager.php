<?php
// Base of the current page
$base_name = plugin_basename('imasters-wp-adserver/imasters-wp-adserver-manager.php');
$base_page = 'admin.php?page=' . $base_name;

//
$mode = $_GET['mode'];

//
if ( isset($_GET['active_toogle'], $_GET['ad_id']) ) :
	//
	$active_toggle = $wpdb->query(sprintf("UPDATE $wpdb->imasters_wp_adserver_ads SET ad_active = ad_active ^ 1 WHERE ad_id = %d", $_GET['ad_id']));
	if ( $active_toggle )
		$text = sprintf('<p style="color: green;">%s</p>', __('The active state of the ad was updated.', 'imasters-wp-adserver'));
endif;

// Form processign
if(!empty($_POST['do'])) :
	// Decide What To Do
	switch($_POST['do']) :
		// Edit Poll
		case __('Edit the ad', 'imasters-wp-adserver'):
			//
			$ad_id 			= (int)$_POST['ad_id'];		
			$ad_title 		= $wpdb->escape(trim($_POST['ad_title']));
			$ad_group_id	= (int)$_POST['ad_group_id'];
			$ad_active 		= (int)$_POST['ad_active'];
			
			// Check the ad type and work properly
			$ad_type 		= $_POST['ad_type'];
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
			elseif ( 'noedit' == $ad_type ) :
				$ad_ad = false;
			endif;

			//
			if ( 'dmy' == get_option( 'imasters_wp_adserver_date_format' ) )
				$ad_expiration_date = $imasters_wp_adserver->convert_date($_POST['ad_expiration_date']);
			else
				$ad_expiration_date = $wpdb->escape(trim($_POST['ad_expiration_date']));
                        
			// Update the ad
			if ( $ad_ad != false ) :
				$update_ad = $wpdb->query(sprintf("UPDATE $wpdb->imasters_wp_adserver_ads
					SET
					ad_title = '%s',
					ad_ad = '%s',
					ad_group_id = %d,
					ad_active = %d,
					ad_expiration_date = '%s'
					WHERE
					ad_id = %d
					",
					$ad_title,
					$ad_ad,
					$ad_group_id,
					$ad_active,
					$ad_expiration_date,
					$ad_id
				));
			else :
				$update_ad = $wpdb->query(sprintf("UPDATE $wpdb->imasters_wp_adserver_ads
					SET
					ad_title = '%s',
					ad_group_id = %d,
					ad_active = %d,
					ad_expiration_date = '%s'
					WHERE
					ad_id = %d
					",
					$ad_title,
					$ad_group_id,
					$ad_active,
					$ad_expiration_date,
					$ad_id
				));
			endif;
			
			
			//
			if ( $update_ad ) :
				$text = sprintf('<p style="color: green;">%s - <a href="%s">%s</a></p>',
					__('Ad updated successfully', 'imasters-wp-adserver'),
					$base_page,
					__('Manager the ads.', 'imasters-wp-adserver')
				);
			endif;
			
		break;
	endswitch;
endif;
?>

<?php if ( !empty($text) ) : ?>
	<div id="message" class="updated fade"><?php echo $text; ?></div>
<?php endif; ?>

<div class="wrap">
<?php 
// Determine which mode interface it is
switch($mode) :

	case 'edit' :
		// Get the ad id
		$ad_id = (int)$_GET['ad_id'];
		// Get the ad to edit
		$objAd = $wpdb->get_row("SELECT * FROM $wpdb->imasters_wp_adserver_ads WHERE ad_id = $ad_id");
		// Get the ad type
		$ad_type = $imasters_wp_adserver->get_ad_type($objAd->ad_ad);
?>
<script type="text/javascript">
var MONTHS = ['<?php _e('January', 'imasters-wp-adserver'); ?>', '<?php _e('February', 'imasters-wp-adserver'); ?>', '<?php _e('March', 'imasters-wp-adserver'); ?>', '<?php _e('April', 'imasters-wp-adserver'); ?>', '<?php _e('May', 'imasters-wp-adserver'); ?>', '<?php _e('June', 'imasters-wp-adserver'); ?>', '<?php _e('July', 'imasters-wp-adserver'); ?>', '<?php _e('August', 'imasters-wp-adserver'); ?>', '<?php _e('September', 'imasters-wp-adserver'); ?>', '<?php _e('October', 'imasters-wp-adserver'); ?>', '<?php _e('November', 'imasters-wp-adserver'); ?>', '<?php _e('December', 'imasters-wp-adserver'); ?>'];
var MONTHS_ABBR = [];

for(var i in MONTHS) {
	MONTHS_ABBR.push(MONTHS[i].substr(0,3));
}

jQuery.datePicker.setLanguageStrings(
	['<?php _e('Sunday', 'imasters-wp-adserver'); ?>', '<?php _e('Monday', 'imasters-wp-adserver'); ?>', '<?php _e('Tuesday', 'imasters-wp-adserver'); ?>', '<?php _e('Wenesday', 'imasters-wp-adserver'); ?>', '<?php _e('Thursday', 'imasters-wp-adserver'); ?>', '<?php _e('Friday', 'imasters-wp-adserver'); ?>', '<?php _e('Satudary', 'imasters-wp-adserver'); ?>'],
	MONTHS,
	{p:'<?php _e('Before', 'imasters-wp-adserver'); ?>', n:'<?php _e('Next', 'imasters-wp-adserver'); ?>', c:'<?php _e('Close', 'imasters-wp-adserver'); ?>', b:'<?php _e('Choose date', 'imasters-wp-adserver'); ?>'}
);

jQuery.datePicker.setDateFormat('<?php echo $imasters_wp_adserver->date_format; ?>', '<?php echo $imasters_wp_adserver->date_split; ?>');
</script>
	<h2><?php _e('Edit the ad', 'imasters-wp-adserver'); ?></h2>
    
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
                            <option value="<?php echo $objGroup->group_id; ?>"<?php echo ( $objAd->ad_group_id == $objGroup->group_id ) ? ' selected="selected"' : ''; ?>><?php echo $objGroup->group_name; ?></option>
                        <?php
                            endforeach;
                        else :
                        ?>
                            <option value=""><?php _e('Any group ad found. Create one first.', 'imasters-wp-adserver'); ?></option>
                        <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ad_title"><?php _e('Ad title', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="ad_title" name="ad_title" size="40" value="<?php echo $objAd->ad_title; ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ad_type_file"><?php _e('The ad', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>
                    	<p>
                        	<input type="radio" id="ad_type_same" name="ad_type" value="noedit" checked="checked" />
                            <label for="ad_type_same"><?php _e('Please, use the same ad added before. <strong>I don´t want to edit it</strong>.', 'imasters-wp-adserver'); ?></label>
                        </p>
                        <p>
                            <input type="radio" id="ad_type_file" name="ad_type" value="file" />
                            <label for="ad_type_file"><?php _e('Ad file.', 'imasters-wp-adserver'); ?></label>
                            <input type="file" name="ad_file" id="ad_file" />
                            <span class="info"><?php _e('Select an image file (gif, jpg) or a Flash file (swf).', 'imasters-wp-adserver'); ?></span>
                        </p>
                        <p>
                            <input type="radio" id="ad_type_text" name="ad_type" value="text" />
                            <label for="ad_type_text"><?php _e('Ad text.', 'imasters-wp-adserver'); ?></label>
                            <textarea name="ad_text" id="ad_text" cols="50" rows="5"><?php echo ( 'JavaScript' == $ad_type or 'HTML' == $ad_type ) ? $objAd->ad_ad : ''; ?></textarea>
                            <span class="info"><?php _e('JavaScript (Google AdSense) or HTML (link ad).', 'imasters-wp-adserver'); ?></span>
                        </p>
                        <p>
                            <input type="radio" id="ad_type_remote" name="ad_type" value="remote" />
                            <label for="ad_type_remote"><?php _e('Ad remote', 'imasters-wp-adserver'); ?></label>
                            <input type="text" id="ad_remote" name="ad_remote" size="40" value="<?php echo ( 'Remote' == $ad_type ) ? $objAd->ad_ad : 'http://'; ?>" />
                            <span class="info"><?php _e('Please include <strong>http://</strong> or <strong>ftp://</strong> in front.', 'imasters-wp-adserver'); ?></span>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ad_link"><?php _e('Ad link', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="ad_link" name="ad_link" size="40" value="<?php echo $objAd->ad_link; ?>" />
                        <span class="info"><?php _e('Please include <strong>http://</strong> or <strong>ftp://</strong> in front.', 'imasters-wp-adserver'); ?></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ad_active_yes"><?php _e('Active the ad?', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>
                        <input type="radio" name="ad_active" id="ad_active_yes" value="1"<?php echo ( 1 == $objAd->ad_active ) ? ' checked="checked"' : ''; ?> />
                        <label for="ad_active_yes"><?php _e('Yes. Active the ad.', 'imasters-wp-adserver'); ?></label>
                        
                        <input type="radio" name="ad_active" id="ad_active_no" value="0"<?php echo ( 0 == $objAd->ad_active ) ? ' checked="checked"' : ''; ?> />
                        <label for="ad_active_no"><?php _e('No. I´ll active later in Manager ads.', 'imasters-wp-adserver'); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="ad_expiration_date"><?php _e('Ad expiration date', 'imasters-wp-adserver'); ?></label>
                    </th>
                    <td>

                        <input type="text" id="ad_expiration_date" name="ad_expiration_date" size="20" value="<?php echo ('dmy' == $imasters_wp_adserver->date_format ) ? $imasters_wp_adserver->convert_date($objAd->ad_expiration_date, true) : $objAd->ad_expiration_date; ?>" />
                        <span class="info"><?php _e('Display the ad until the informed date of expiration.', 'imasters-wp-adserver'); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="do" class="button-primary" value="<?php _e('Edit the ad', 'imasters-wp-adserver'); ?>" />
            <?php _e( 'or', 'imasters-wp-adserver' ); ?>
            <a href="<?php echo $base_page; ?>"><?php _e( 'cancel', 'imasters-wp-adserver' ); ?></a>
            <input type="hidden" name="ad_id" value="<?php echo $_GET['ad_id']; ?>" />
        </p>
    </form>
<?php
	break;
	// Main page
	default :
?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Manager ads', 'imasters-wp-adserver'); ?></h2>
    
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Title', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Ad', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Ad link', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Group', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Active', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Expiration date', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Total clicks', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Total impressions', 'imasters-wp-adserver'); ?></th>
                <th><acronym title="<?php _e('Click Through Rate', 'imasters-wp-adserver'); ?>"><?php _e('CTR', 'imasters-wp-adserver'); ?></acronym></th>
                <!--<th><?php _e('Max clicks', 'imasters-wp-adserver'); ?></th>
                <th><?php _e('Max impressions', 'imasters-wp-adserver'); ?></th>-->
                <th colspan="3"><?php _e('Actions', 'imasters-wp-adserver'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
		$objAds = $wpdb->get_results("SELECT * FROM $wpdb->imasters_wp_adserver_ads INNER JOIN $wpdb->imasters_wp_adserver_groups ON $wpdb->imasters_wp_adserver_ads.ad_group_id = $wpdb->imasters_wp_adserver_groups.group_id");
		if ( $objAds ) :
			foreach($objAds as $objAd) :
		?>
        	<tr id="ad-<?php echo $objAd->ad_id; ?>">
            	<td><?php echo $objAd->ad_title; ?></td>
                <td><a href="<?php echo $base_page; ?>&amp;preview_ad=true&amp;ad_id=<?php echo $objAd->ad_id; ?>&amp;ad_group_id=<?php echo $objAd->ad_group_id; ?>&amp;ad_string=<?php echo base64_encode($objAd->ad_ad); ?>"><?php echo _e( 'Preview this ad', 'imasters-wp-adserver' ); ?></a></td>
                <td><a href="<?php echo htmlspecialchars($objAd->ad_link); ?>"><?php echo $imasters_wp_adserver->format_link_url($objAd->ad_link); ?></td>
                <td><?php echo $objAd->group_name; ?></td>
                <td id="ad-active-<?php echo $objAd->ad_id; ?>"><?php echo ( 1 == $objAd->ad_active ) ? _e('Yes', 'imasters-wp-adserver') : _e('No', 'imasters'); ?></td>
                <td><?php echo ( 'dmy' == $imasters_wp_adserver->date_format ) ? $imasters_wp_adserver->convert_date($objAd->ad_expiration_date, true) : $imasters_wp_adserver->convert_date($objAd->ad_expiration_date); ?></td>
                <td><?php echo $objAd->ad_total_clicks; ?></td>
                <td><?php echo $objAd->ad_total_impressions; ?></td>
                <td><?php echo $imasters_wp_adserver->get_ctr($objAd->ad_total_clicks, $objAd->ad_total_impressions); ?>%</td>
                <td><a href="<?php echo $base_page; ?>&amp;active_toogle=true&amp;ad_id=<?php echo $objAd->ad_id; ?>"><?php echo ( 1 == $objAd->ad_active ) ? _e('Deactivate', 'imasters-wp-adserver') : _e('Activate', 'imasters-wp-adserver'); ?></a></td>
                <td><a href="<?php echo $base_page; ?>&amp;mode=edit&amp;ad_id=<?php echo $objAd->ad_id; ?>"><?php _e('Edit', 'imasters-wp-adserver'); ?></a></td>
                <td><a href="javascript:;" onclick="IMASTERS_WP_ADSERVER.delete_ad(<?php echo $objAd->ad_id; ?>, '<?php printf(__('You are about to delete this ad, %s. \\n Choose [Cancel] to Cancel, [OK] to Delete.', 'imasters-wp-adserver'), $objAd->ad_title); ?>');"><?php _e( 'Delete', 'imasters-wp-adserver' ); ?></a></td>
            </tr>
        <?php
			endforeach;
		else :
		?>
        	<tr>
            	<td colspan="12"><?php _e('Any ad found.', 'imasters-wp-adserver'); ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div><!-- / Manager ads -->

<div class="wrap<?php echo ( isset($_GET['preview_ad']) ) ? '' : ' hide' ?>" id="imasters-wp-adserver-preview">
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e( 'Preview the ad', 'imasters-wp-adserver' ); ?></h2>
    <?php echo $imasters_wp_adserver->get_ad_structure(base64_decode($_GET['ad_string']), $_GET['ad_id'], '', $_GET['ad_group_id']); ?>
    <p><a href="<?php echo $base_page; ?>"><?php _e('Close the preview', 'imasters-wp-adserver' ); ?></a></p>
</div>
<?php
	break;
endswitch;
?>