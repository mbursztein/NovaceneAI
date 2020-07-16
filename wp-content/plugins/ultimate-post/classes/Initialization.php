<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Initialization{

    private $all_blocks;
    private $api_endpoint = 'https://demo.wpxpo.com/wp-json/restapi/v2/';

    public function __construct(){
        $this->requires();
        $this->blocks();

        add_action('wp_head',                       array($this, 'popular_posts_tracker_callback'));
        add_filter('block_categories',              array($this, 'register_category_callback'), 10, 2); // Block Category Register
        add_action('after_setup_theme',             array($this, 'add_image_size'));

        add_action('enqueue_block_editor_assets',   array($this, 'register_scripts_back_callback')); // Only editor
        add_action('admin_enqueue_scripts',         array($this, 'register_scripts_option_panel_callback')); // Option Panel
        add_action('wp_enqueue_scripts',            array($this, 'register_scripts_front_callback')); // Both frontend

        add_action('wp_ajax_ultp_next_prev',        array($this, 'ultp_next_prev_callback')); // Next Previous AJAX Call
        add_action('wp_ajax_nopriv_ultp_next_prev', array($this, 'ultp_next_prev_callback')); // Next Previous AJAX Call Logout User
        add_action('wp_ajax_ultp_filter',           array($this, 'ultp_filter_callback')); // Next Previous AJAX Call
        add_action('wp_ajax_nopriv_ultp_filter',    array($this, 'ultp_filter_callback')); // Next Previous AJAX Call Logout User
        add_action('wp_ajax_ultp_pagination',       array($this, 'ultp_pagination_callback')); // Page Number AJAX Call
        add_action('wp_ajax_nopriv_ultp_pagination',array($this, 'ultp_pagination_callback')); // Page Number AJAX Call Logout User
        
        register_activation_hook(ULTP_PATH.'ultimate-post.php', array($this, 'install_hook'));

        add_action( 'activated_plugin',             array($this, 'activation_redirect'));
    }

    public function register_scripts_option_panel_callback(){
        if( null !== ( $screen = get_current_screen() ) && 'toplevel_page_ultp-settings' !== $screen->id ) {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('ultp-option-script', ULTP_URL.'assets/js/ultp-option.js', array('jquery'), ULTP_VER, true);
    }

    public function register_scripts_common(){
        wp_enqueue_style('dashicons');
        wp_enqueue_style('ultp-slick-style', ULTP_URL.'assets/css/slick.css', array(), ULTP_VER);
        wp_enqueue_style('ultp-slick-theme-style', ULTP_URL.'assets/css/slick-theme.css', array(), ULTP_VER);
        wp_enqueue_style('ultp-style', ULTP_URL.'assets/css/blocks.style.css', array(), ULTP_VER );
        if(is_rtl()){ wp_enqueue_style('ultp-blocks-rtl-css', ULTP_URL.'assets/css/rtl.css', array(), ULTP_VER); }
        wp_enqueue_script('ultp-flexmenu-script', ULTP_URL.'assets/js/flexmenu.js', array('jquery'), ULTP_VER, true);
        wp_enqueue_script('ultp-slick-script', ULTP_URL.'assets/js/slick.min.js', array('jquery'), ULTP_VER, true);
        wp_enqueue_script('ultp-script', ULTP_URL.'assets/js/ultp.js', array('jquery','ultp-flexmenu-script'), ULTP_VER, true);
        wp_localize_script('ultp-script', 'ultp_data', array(
            'url' => ULTP_URL,
            'ajax' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ultp-nonce')
        ));
    }

    // Backend and Frontend Load script
    public function register_scripts_front_callback() {
        if ('yes' == get_post_meta(get_the_ID(), '_ultp_active', true)) {
            $this->register_scripts_common();
        }
    }

    // Only Backend
    public function register_scripts_back_callback() {
        $this->register_scripts_common();
        wp_enqueue_script('ultp-blocks-editor-script', ULTP_URL.'assets/js/editor.blocks.min.js', array('wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ), ULTP_VER, true);
        wp_enqueue_style('ultp-blocks-editor-css', ULTP_URL.'assets/css/blocks.editor.css', array(), ULTP_VER);
        if(is_rtl()){ wp_enqueue_style('ultp-blocks-editor-rtl-css', ULTP_URL.'assets/css/rtl.css', array(), ULTP_VER); }
        
        $import = '';
        $options = get_option('ultp_options');
        if(!$options){
			$options = ultimate_post()->init_set_data();	
		}
        if (isset($options['hide_import_btn'])) {
            if ($options['hide_import_btn']=='yes') {
                $import = 'yes';
            }
        }

        wp_localize_script('ultp-blocks-editor-script', 'ultp_data', array(
            'url' => ULTP_URL,
            'ajax' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ultp-nonce'),
            'hide_import_btn' => $import
        ));
    }

    // Fire When Plugin First Install
    public function install_hook() {
        if (!get_option('ultp_options')) {
            ultimate_post()->init_set_data();
        }
    }

    public function activation_redirect($plugin) {
        if( $plugin == 'ultimate-post/ultimate-post.php' ) {
            exit(wp_redirect(admin_url('admin.php?page=ultp-settings')));
        }
    }

    public function ultp_pagination_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'ultp-nonce') && $local) {
            return ;
        }

        $paged      = sanitize_text_field($_POST['paged']);
        $blockId    = sanitize_text_field($_POST['blockId']);
        $postId     = sanitize_text_field($_POST['postId']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $blockName  = str_replace('_','/', $blockRaw);

        if($paged) {
            $post = get_post($postId); 
            if (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                $this->block_return($blocks, $paged, $blockId, $blockRaw, $blockName);
            }
        }
    }

    public function block_return($blocks, $paged, $blockId, $blockRaw, $blockName) {
        foreach ($blocks as $key => $value) {
            if($blockName == $value['blockName']) {
                if($value['attrs']['blockId'] == $blockId) {
                    $attr = $this->all_blocks[$blockRaw]->get_attributes(true);
                    $attr['paged'] = $paged;
                    $attr = array_merge($attr, $value['attrs']);
                    echo $this->all_blocks[$blockRaw]->content($attr, true);
                    die();
                }
            }
            if(!empty($value['innerBlocks'])){
                $this->block_return($value['innerBlocks'], $paged, $blockId, $blockRaw, $blockName);
            }
        }
    }


    public function ultp_filter_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'ultp-nonce') && $local){
            return ;
        }
     
        $taxtype    = sanitize_text_field($_POST['taxtype']);
        $blockId    = sanitize_text_field($_POST['blockId']);
        $postId     = sanitize_text_field($_POST['postId']);
        $taxonomy   = sanitize_text_field($_POST['taxonomy']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $blockName  = str_replace('_','/', $blockRaw);

        if( $taxtype ) {
            $post = get_post($postId); 
            if (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                $this->filter_block_return($blocks, $blockId, $blockRaw, $blockName, $taxtype, $taxonomy);
            }
        }
    }

    public function filter_block_return($blocks, $blockId, $blockRaw, $blockName, $taxtype, $taxonomy) {
        foreach ($blocks as $key => $value) {
            if($blockName == $value['blockName']) {
                if($value['attrs']['blockId'] == $blockId) {
                    $attr = $this->all_blocks[$blockRaw]->get_attributes(true);
                    $value['attrs']['queryTax'] = $taxtype == 'category' ? 'category' : 'tag';
                    if($taxtype == 'category' && $taxonomy) {
                        $value['attrs']['queryCat'] = json_encode(array($taxonomy));
                    }
                    if($taxtype == 'post_tag' && $taxonomy) {
                        $value['attrs']['queryTag'] = json_encode(array($taxonomy));
                    }
                    if(isset($value['attrs']['queryNumber'])){
                        $value['attrs']['queryNumber'] = $value['attrs']['queryNumber'];
                    }
                    $attr = array_merge($attr, $value['attrs']);
                    echo $this->all_blocks[$blockRaw]->content($attr, true);
                    die();
                }
            }
            if(!empty($value['innerBlocks'])){
                $this->filter_block_return($value['innerBlocks'], $blockId, $blockRaw, $blockName, $taxtype, $taxonomy);
            }
        }
    }


    public function ultp_next_prev_callback() {
        if (!wp_verify_nonce($_REQUEST['wpnonce'], 'ultp-nonce') && $local){
            return ;
        }

        $paged      = sanitize_text_field($_POST['paged']);
        $blockId    = sanitize_text_field($_POST['blockId']);
        $postId     = sanitize_text_field($_POST['postId']);
        $blockRaw   = sanitize_text_field($_POST['blockName']);
        $blockName  = str_replace('_','/', $blockRaw);

        if( $paged && $blockId && $postId && $blockName ) {
            $post = get_post($postId); 
            if (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                $this->block_return($blocks, $paged, $blockId, $blockRaw, $blockName);
            }
        }
    }

    // Require Blocks
    public function blocks() {
        require_once ULTP_PATH.'blocks/Post_List_1.php';
        require_once ULTP_PATH.'blocks/Post_List_2.php';
        require_once ULTP_PATH.'blocks/Post_List_3.php';
        require_once ULTP_PATH.'blocks/Post_Slider_1.php';
        require_once ULTP_PATH.'blocks/Post_Grid_1.php';
        require_once ULTP_PATH.'blocks/Post_Grid_2.php';
        require_once ULTP_PATH.'blocks/Post_Grid_3.php';
        require_once ULTP_PATH.'blocks/Post_Grid_4.php';
        require_once ULTP_PATH.'blocks/Heading.php';
        require_once ULTP_PATH.'blocks/Image.php';
        //require_once ULTP_PATH.'blocks/Taxonomy.php';
        $this->all_blocks['ultimate-post_post-list-1'] = new \ULTP\blocks\Post_List_1();
        $this->all_blocks['ultimate-post_post-list-2'] = new \ULTP\blocks\Post_List_2();
        $this->all_blocks['ultimate-post_post-list-3'] = new \ULTP\blocks\Post_List_3();
        $this->all_blocks['ultimate-post_post-slider-1'] = new \ULTP\blocks\Post_Slider_1();
        $this->all_blocks['ultimate-post_post-grid-1'] = new \ULTP\blocks\Post_Grid_1();
        $this->all_blocks['ultimate-post_post-grid-2'] = new \ULTP\blocks\Post_Grid_2();
        $this->all_blocks['ultimate-post_post-grid-3'] = new \ULTP\blocks\Post_Grid_3();
        $this->all_blocks['ultimate-post_post-grid-4'] = new \ULTP\blocks\Post_Grid_4();
        $this->all_blocks['ultimate-post_heading'] = new \ULTP\blocks\Heading();
        //$this->all_blocks['ultimate-post_taxonomy'] = new \ULTP\blocks\Taxonomy();
        $this->all_blocks['ultimate-post_image'] = new \ULTP\blocks\Image();
    }

    // Require Categories
    public function requires() {
        require_once ULTP_PATH.'classes/Styles.php';
        require_once ULTP_PATH.'classes/Options.php';
        require_once ULTP_PATH.'classes/REST_API.php';

        new \ULTP\Options();
        new \ULTP\Styles();
    }

    // Block Categories
    public function register_category_callback( $categories, $post ) {
        return array_merge(
            array(
                array( 
                    'slug' => 'ultimate-post', 
                    'title' => __( 'Gutenberg Post Blocks', 'ultimate-post' ) 
                )
            ), $categories 
        );
    }

    public function popular_posts_tracker_callback($post_id) {
        if (!is_single()){ return; }
        global $post;
        if (empty($post_id)) { $post_id = $post->ID; }
        $count = (int)get_post_meta( $post_id, '__post_views_count', true );
        update_post_meta($post_id, '__post_views_count', $count ? (int)$count + 1 : 1 );
    }

    public function add_image_size() {
        add_image_size('ultp_layout_landscape_large', 1200, 800, true);
        add_image_size('ultp_layout_landscape', 870, 570, true);
        add_image_size('ultp_layout_portrait', 600, 900, true);
        add_image_size('ultp_layout_square', 600, 600, true);
    }
}