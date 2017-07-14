<?php if ( isset($_GET['updated']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('The options was updated.', 'imasters-wp-adserver' ); ?></p></div>
<?php endif; ?>

<div class="wrap">
	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-adserver/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Help', 'imasters-wp-adserver'); ?></h2>

	<h3><?php _e('How to show the ads in the template.', 'imasters-wp-adserver'); ?></h3>
    
    <p><?php _e('The first thing is to get the ID of the group; get it at Manager group ads. Second call the iMasters AdServer function to show the ads in your template and pass to it the ID of the group. Look an example:', 'imasters-wp-adserver'); ?></p>
    
    <pre>&lt;?php if ( class_exists( 'IMASTERS_WP_AdServer' ) ) imasters_wp_adserver_get_ads(6, 2); ?&gt;<pre>
    
    <p><?php _e('Where 6 (six) is the ID of a group and 2 (two) is the total of banners to show.', 'imasters-wp-adserver'); ?></p>
    
</div>