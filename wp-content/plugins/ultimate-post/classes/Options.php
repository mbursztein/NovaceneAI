<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Options{
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'menu_page_callback' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'in_admin_header', array($this, 'remove_all_notices') );
        
        add_filter( 'plugin_row_meta', array( $this, 'plugin_settings_meta' ), 10, 2 );
        add_filter( 'plugin_action_links_'.ULTP_BASE, array( $this, 'plugin_action_links_callback' ) );
    }


    public function plugin_settings_meta( $links, $file ) {
        if ( strpos( $file, 'ultimate-post.php' ) !== false ) {
            $new_links = array(
                'ultp_docs' =>  '<a href="https://docs.wpxpo.com/" target="_blank">'.__('Docs', 'ultimate-post').'</a>',
                'ultp_tutorial' =>  '<a href="https://www.youtube.com/watch?v=JZxIflYKOuM&list=PLPidnGLSR4qcAwVwIjMo1OVaqXqjUp_s4" target="_blank">'.__('Tutorials', 'ultimate-post').'</a>',
                'ultp_support' =>  '<a href="https://www.wpxpo.com/contact/?utm_campaign=go_premium" target="_blank">'.__('Support', 'ultimate-post').'</a>'
            );
            $links = array_merge( $links, $new_links );
        }
        return $links;
    }


    public function plugin_action_links_callback ( $links ) {
        $upgrade_link = array();
        $setting_link = array();
        if(!defined('ULTP_PRO_VER')){
            $upgrade_link = array(
                'ultp_pro' => '<a href="https://www.wpxpo.com/gutenberg-post-blocks/?utm_campaign=go_premium" target="_blank"><span style="color: #e83838; font-weight: bold;">'.__('Go Pro', 'ultimate-post').'</span></a>'
            );
        }
        $setting_link['ultp_settings'] = '<a href="'. menu_page_url('ultp-option-settings', false) .'">'. __('Settings', 'wp-megamenu') .'</a>';
        return array_merge( $setting_link, $links, $upgrade_link);
    }


    public static function menu_page_callback() {
        add_menu_page(
            esc_html__( 'Post Blocks', 'ultimate-post' ),
            esc_html__( 'Post Blocks', 'ultimate-post' ),
            'manage_options',
            'ultp-settings',
            '',
            ULTP_URL.'assets/img/menu-panel.svg'
        );

        require_once ULTP_PATH . 'classes/options/Overview.php';
        require_once ULTP_PATH . 'classes/options/Features.php';
        require_once ULTP_PATH . 'classes/options/Addons.php';
        require_once ULTP_PATH . 'classes/options/Settings.php';
        require_once ULTP_PATH . 'classes/options/Contact.php';
        new \ULTP\Options_Overview();
        new \ULTP\Options_Features();
        new \ULTP\Options_Addons();
        new \ULTP\Options_Settings();
        new \ULTP\Options_Contact();

        if( !function_exists('ultimate_post_pro') ){
            global $submenu;
            $upgrade_link = 'https://www.wpxpo.com/gutenberg-post-blocks/?ultp=plugins';
            $submenu['ultp-settings'][] = array( '<span class="ultp-dashboard-upgrade"><span class="dashicons dashicons-update"></span> Upgrade</span>', 'manage_options', $upgrade_link );
        }
    }

    // Remove All Notification From Menu Page
    public static function remove_all_notices() {
        if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'ultp-settings' || $_GET['page'] === 'ultp-features' || $_GET['page'] === 'ultp-addons' || $_GET['page'] === 'ultp-option-settings' || $_GET['page'] === 'ultp-contact' || $_GET['page'] === 'ultp-license' ) ) {
            remove_all_actions( 'admin_notices' );
            remove_all_actions( 'all_admin_notices' );
        }
    }

    /**
     * Register a setting and its sanitization callback.
     */
    public static function register_settings() {
        register_setting( 'ultp_options', 'ultp_options', array( self::class, 'sanitize' ) );
    }
}

