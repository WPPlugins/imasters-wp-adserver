// JavaScript Document
( function($) {

    $(function() {
        IMASTERS_WP_ADSERVER.init();
        IMASTERS_WP_ADSERVER.imasters_wp_adserver_uninstall_button();
        
    });

    IMASTERS_WP_ADSERVER = {

        init : function() {
            IMASTERS_WP_ADSERVER.define_ad_type();
            IMASTERS_WP_ADSERVER.date_picker();
        },

        define_ad_type : function() {
            $('#ad_file').click(function() {
                $('#ad_type_file').attr('checked', 'checked');
            });
            $('#ad_text').click(function() {
                $('#ad_type_text').attr('checked', 'checked');
            });
            $('#ad_remote').click(function() {
                $('#ad_type_remote').attr('checked', 'checked');
            });
            $('#ad_type_text').click(function() {
                $('#ad_text').focus();
            });
            $('#ad_type_remote').click(function() {
                $('#ad_remote').focus();
            });
        },

        date_picker : function() {
            $('#ad_expiration_date').datePicker({startDate:'01/01/2007'});
        },

        delete_group : function(group_id, confirm_message) {
            var delete_group = confirm(confirm_message);
            if ( delete_group ) {
                $.post(imasters_wp_adserver_admin_ajax_url, { ajax_action : 'delete_group', group_id : group_id }, function(ajaxReturn) {
                    $('#group-' + group_id).fadeOut('slow', function() {
                        $(this).remove();
                    });
                });
            }
        },

        delete_ad : function(ad_id, confirm_message) {
            var delete_group = confirm(confirm_message);
            if ( delete_group ) {
                $.post(imasters_wp_adserver_admin_ajax_url, { ajax_action : 'delete_ad', ad_id : ad_id }, function(ajaxReturn) {
                    $('#ad-' + ad_id).fadeOut('slow', function() {
                        $(this).remove();
                    });
                });
            }
        },

        imasters_wp_adserver_uninstall_button : function() {
            var currentPluginPage = window.location.search;
            if ( currentPluginPage.match(/uninstall.php$/) ) {
                    var button = $('input[name="do"]');
                    var checkbox = $('#uninstall_imasters_wp_adserver_yes');
                    button.hide();
                    checkbox.attr( 'checked', '' ).click(function() {
                        var is_checked = checkbox.attr( 'checked' );
                        if ( is_checked )
                            button.fadeIn();
                        else
                            button.fadeOut();
                    })
                }
        }
    };

})(jQuery);