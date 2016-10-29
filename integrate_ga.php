<?php
/*
Plugin Name: Integrate Google Analytics
Plugin URI: https://github.com/costibotez/Integrate-Google-Analytics
Description: Adds a Google analytics tracking code to the <head> of your theme, by hooking to wp_head().
Author: Botez Costin
Author URI:
Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
define( 'INTEGRATE_GA_PLUGIN_PATH', plugin_basename(__FILE__));

if(!class_exists('WP_Google_Analytics')) {
  class WP_Google_Analytics {

	 function __construct() {
	  add_action('admin_menu', 			   array($this, 'integrate_ga_menu_item'));
	  add_action('admin_init', 			   array($this, 'integrate_ga_settings'));
    add_action('wp_head',            array($this, 'google_analytics_script'));
    add_filter('plugin_action_links_' . INTEGRATE_GA_PLUGIN_PATH, array($this, 'add_action_links' ));
    register_uninstall_hook(__FILE__,array($this, 'integrate_ga_plugin_uninstall'));
  	}

  	function integrate_ga_menu_item() {
    	add_options_page('Integrate Google Analytics', 'Integrate Google Analytics', 'manage_options', 'integrate_ga', array($this, 'integrate_ga_cb'));
  	}

  	function integrate_ga_cb() { ?>
  		<div class="wrap">
        <h1><?php _e('Google Analytics Tracking Code', 'toptal-ss'); ?></h1>
        <form method="post" action="options.php">
          <?php
            settings_fields('integrate_ga_settings');
            do_settings_sections('integrate_ga');
            submit_button();
          ?>
        </form>
      </div>
  		<?php
  	}

  	function integrate_ga_settings() {
    	add_settings_section('integrate_ga_section', '', null, 'integrate_ga');

    	add_settings_field('integrate_ga_tracking_code', 	__('Tracking Code:', 'toptal-ss'), 	array($this, 'integrate_ga_tracking_code_cb'), 'integrate_ga', 'integrate_ga_section');

      register_setting('integrate_ga_settings', 'integrate_ga_tracking_code', 'strval');
  	}

    function integrate_ga_tracking_code_cb() { ?>
      <input type="text" name="integrate_ga_tracking_code" value="<?php echo get_option('integrate_ga_tracking_code'); ?>"/>
      <?php
    }

    function google_analytics_script() { ?>
      <!-- INTEGRATE GOOGLE ANALYTICS. -->
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', <?php echo get_option('integrate_ga_tracking_code'); ?>, 'auto');
        ga('send', 'pageview');

      </script>
      <!-- END INTEGRATE GOOGLE ANALYTICS. -->
      <?php
    }

    function add_action_links ( $links ) {
      $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=integrate_ga' ) . '">' . __('Settings', 'integrate-ga') .  '</a>',
      );
      return array_merge( $links, $mylinks );
    }

    function integrate_ga_plugin_uninstall() {
      delete_option('integrate_ga_tracking_code');
    }
  }
  new WP_Google_Analytics();
}

?>
