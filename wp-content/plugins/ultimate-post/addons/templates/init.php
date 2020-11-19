<?php
defined( 'ABSPATH' ) || exit;

add_filter('ultp_addons_config', 'ultp_templates_config');
function ultp_templates_config( $config ) {
	$configuration = array(
		'name' => __( 'Saved Templates', 'ultimate-post' ),
		'desc' => __( 'Create Shortcode and using inside your page or page builder.', 'ultimate-post' ),
		'img' => ULTP_URL.'/assets/img/addons/saved-template.svg',
		'is_pro' => false
	);
	$config['ultp_templates'] = $configuration;
	return $config;
}

$addon_enable = get_option('ultp_addons_option');
if ( isset($addon_enable['ultp_templates']) ) {
    if ($addon_enable['ultp_templates'] == 'true') {
		require_once ULTP_PATH.'/addons/templates/Saved_Templates.php';
		require_once ULTP_PATH.'/addons/templates/Shortcode.php';
		new \ULTP\Saved_Templates();
		new \ULTP\Shortcode();
		if(did_action( 'elementor/loaded' )){
			require_once ULTP_PATH.'/addons/templates/Elementor.php';
			Elementor_ULTP_Extension::instance();
		}
    }
}