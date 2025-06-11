<?php
/**
 * Plugin Name: Integrate Google Analytics
 * Plugin URI: https://github.com/costibotez/Integrate-Google-Analytics
 * Description: Adds a Google Analytics tracking code to the <head> section of your theme.
 * Author: Botez Costin
 * Version: 1.1
 * Requires PHP: 7.4
 * Text Domain: integrate-google-analytics
 * License: GPLv3 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Integrate_Google_Analytics {

    private string $option_name = 'integrate_ga_tracking_code';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_head', [ $this, 'output_tracking_code' ] );
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'settings_link' ] );
    }

    public function register_menu(): void {
        add_options_page(
            esc_html__( 'Integrate Google Analytics', 'integrate-google-analytics' ),
            esc_html__( 'Integrate GA', 'integrate-google-analytics' ),
            'manage_options',
            'integrate_ga',
            [ $this, 'settings_page' ]
        );
    }

    public function settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Google Analytics Tracking Code', 'integrate-google-analytics' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'integrate_ga_settings' );
                do_settings_sections( 'integrate_ga' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings(): void {
        register_setting( 'integrate_ga_settings', $this->option_name, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );

        add_settings_section( 'integrate_ga_section', '', null, 'integrate_ga' );

        add_settings_field(
            'integrate_ga_tracking_code',
            __( 'Tracking Code:', 'integrate-google-analytics' ),
            [ $this, 'tracking_code_field' ],
            'integrate_ga',
            'integrate_ga_section'
        );
    }

    public function tracking_code_field(): void {
        $value = get_option( $this->option_name, '' );
        ?>
        <input type="text" name="<?php echo esc_attr( $this->option_name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
        <?php
    }

    public function output_tracking_code(): void {
        $code = trim( get_option( $this->option_name, '' ) );

        if ( empty( $code ) ) {
            return;
        }
        ?>
        <!-- INTEGRATE GOOGLE ANALYTICS -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', '<?php echo esc_js( $code ); ?>', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- END INTEGRATE GOOGLE ANALYTICS -->
        <?php
    }

    public function settings_link( array $links ): array {
        $links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=integrate_ga' ) ) . '">' . esc_html__( 'Settings', 'integrate-google-analytics' ) . '</a>';
        return $links;
    }

    public static function uninstall(): void {
        delete_option( 'integrate_ga_tracking_code' );
    }
}

new Integrate_Google_Analytics();
register_uninstall_hook( __FILE__, [ 'Integrate_Google_Analytics', 'uninstall' ] );

?>
