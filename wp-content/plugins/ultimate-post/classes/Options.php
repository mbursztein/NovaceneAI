<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Options{
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'menu_page_callback' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public static function menu_page_callback() {
        add_menu_page(
            esc_html__( 'Post Blocks', 'ultimate-post' ),
            esc_html__( 'Post Blocks', 'ultimate-post' ),
            'manage_options',
            'ultp-settings',
            array( self::class, 'create_admin_page' ),
            ULTP_URL.'assets/img/menu-panel.svg'
        );
    }

    /**
     * Register a setting and its sanitization callback.
     */
    public static function register_settings() {
       register_setting( 'ultp_options', 'ultp_options', array( self::class, 'sanitize' ) );
    }

    /**
     * Sanitization callback
     */
    public static function sanitize( $options ) {
        if ($options) {
            $settings = self::get_option_settings();
            foreach ($settings as $key => $setting) {
                if (!empty($key)) {
                    $options[$key] = isset($options[$key]) ? sanitize_text_field($options[$key]) : '';
                }
            }
        }
        return $options;
    }

    public static function get_option_settings(){
        return array(
            'css_save_as' => array(
                'type' => 'select',
                'label' => __('CSS Add Via', 'ultimate-post'),
                'options' => array(
                    'wp_head'   => __( 'Header - (Internal)','ultimate-post' ),
                    'filesystem' => __( 'File System - (External)','ultimate-post' ),
                ),
                'default' => 'wp_head',
                'desc' => __('Select where you want to save CSS.', 'ultimate-post')
            ),
            'preloader_style' => array(
                'type' => 'select',
                'label' => __('Preloader Style', 'ultimate-post'),
                'options' => array(
                    'style1' => __( 'Preloader Style 1','ultimate-post' ),
                    'style2' => __( 'Preloader Style 2','ultimate-post' ),
                ),
                'default' => 'style1',
                'desc' => __('Select Preloader Style.', 'ultimate-post')
            ),
            'container_width' => array(
                'type' => 'number',
                'label' => __('Container Width', 'ultimate-post'),
                'default' => '1140',
                'desc' => __('Change Container Width.', 'ultimate-post')
            ),
            'hide_import_btn' => array(
                'type' => 'switch',
                'label' => __('Hide Import Button', 'ultimate-post'),
                'default' => '',
                'desc' => __('Hide Import Layout Button from the Gutenberg Editor.', 'ultimate-post')
            ),
        );
    }

    public static function get_recommended_themes(){
        $recommended_themes = array(
            'coblog' => array(
                'name'  => 'Coblog',
                'slug'  => 'coblog',
                'url'   => 'https://wordpress.org/themes/coblog',
                'logo'  => ULTP_URL.'assets/img/WordPress.png'
            ),
            'blocksy' => array(
                'name'  => 'Blocksy',
                'slug'  => 'blocksy',
                'url'   => 'https://wordpress.org/themes/blocksy/',
                'logo'  => ULTP_URL.'assets/img/WordPress.png'
            ),
        );

        $html = '';
        foreach ($recommended_themes as $key => $value) {
            $html .= '<div class="ultp-recommended-theme">';
                $html .= '<div class="ultp-recommended-image">';
                    $html .= '<img src="'.$value['logo'].'" alt="'.$value['name'].'" />';
                $html .= '</div>';
                $html .= '<div class="ultp-recommended-name"><a target="_blank" href="'.$value['url'].'">'.$value['name'].'</a></div>';
                $html .= '<div class="ultp-recommended-button">';
                    $html .= '<a target="_blank" href="'.$value['url'].'" class="button button-success">'.__('Download Now').'</a>';
                $html .= '</div>';
            $html .= '</div>';
        }
        echo $html;
    }

    public static function get_changelog_data() {
        $html = '';
        $resource_data = file_get_contents(ULTP_PATH.'/readme.txt', "r");
        $data = array();
        if ($resource_data) {
            $resource_data = explode('== Changelog ==', $resource_data);
            if (isset($resource_data[1])) {
                $resource_data = $resource_data[1];
                $resource_data = explode("\n", $resource_data);
                $inner = false;
                $count = -1;
                
                foreach ($resource_data as $element) {
                    if ($element){
                        if (substr_count($element, '=') > 1) {
                            $count++;
                            $temp = trim(str_replace('=', '', $element));
                            if (strpos($temp, '-') !== false) {
                                $temp = explode('-', $temp);
                                $data[$count]['date'] = trim($temp[1]);
                                $data[$count]['version'] = trim($temp[0]);
                            }
                        }
                        if (strpos($element, '* New:') !== false) {
                            $data[$count]['new'][] = trim(str_replace('* New:', '', $element));
                        }
                        if (strpos($element, '* Fix:') !== false) {
                            $data[$count]['fix'][] = trim(str_replace('* Fix:', '', $element));
                        }
                        if (strpos($element, '* Update:') !== false) {
                            $data[$count]['update'][] = trim(str_replace('* Update:', '', $element));
                        }
                    }
                }
            }
        }
        if (!empty($data)) {
            foreach ($data as $k => $inner_data) {
                $html .= '<div class="ultp-changelog-wrap">';
                foreach ($inner_data as $key => $changelog) {
                    if ($key == 'date') {
                        $html .= '<div class="ultp-changelog-date">'.__('Released on ', 'ultimate-post').' '.$changelog.'</div>';
                    } elseif($key == 'version') {
                        $html .= '<div class="ultp-changelog-version">'.__('Version', 'ultimate-post').' : '.$changelog.'</div>';
                    } else {
                        foreach ($changelog as $keyword => $val) {
                            $html .= '<div class="ultp-changelog-title"><span class="changelog-'.$key.'">'.$key.'</span>'.$val.'</div>';
                        }
                    }
                }
                $html .= '</div>';
            }
        }
        echo $html;
    }
    
    public static function get_settings_data() {
        $html = '';
        $option_data = get_option( 'ultp_options' );
        if(!$option_data){
			$option_data = ultimate_post()->init_set_data();	
		}
        $data = self::get_option_settings();
        $html .= '<div class="ultp-settings">';
            $html .= '<input type="hidden" name="option_page" value="ultp_options" />';
            $html .= '<input type="hidden" name="action" value="update" />';
            $html .= wp_nonce_field( "ultp_options-options" );
            foreach ($data as $key => $value) {
                $html .= '<div class="ultp-settings-wrap">';
                    $html .= '<div class="ultp-settings-label">'.$value['label'].'</div>';
                    $html .= '<div class="ultp-settings-field-wrap">';
                        switch ($value['type']) {

                            case 'select':
                                $html .= '<div class="ultp-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    $html .= '<select name="ultp_options['.$key.']">';
                                        foreach ( $value['options'] as $id => $label ) {
                                            $html .= '<option value="'.$id.'" '.( $val == $id ? ' selected="selected"':'').'>';
                                            $html .= strip_tags( $label );
                                            $html .= '</option>';
                                        }
                                        $html .= '</select>';
                                    $html .= '<p class="description">'.$value['desc'].'</p>';
                                $html .= '</div>';
                                break;

                            case 'color':
                                $html .= '<div class="ultp-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    $html .= '<input name="ultp_options['.$key.']" value="'.$val.'" class="ultp-color-picker" />';
                                    $html .= '<p class="description">'.$value['desc'].'</p>';
                                $html .= '</div>';
                                break;

                            case 'number':
                                $html .= '<div class="ultp-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    $html .= '<input type="number" name="ultp_options['.$key.']" value="'.$val.'"/>';
                                    $html .= '<p class="description">'.$value['desc'].'</p>';
                                $html .= '</div>';
                                break;

                            case 'switch':
                                $html .= '<div class="ultp-settings-field">';
                                    $val = isset($option_data[$key]) ? $option_data[$key] : (isset($value['default']) ? $value['default'] : '');
                                    $html .= '<input type="checkbox" value="yes" name="ultp_options['.$key.']" '.($val == 'yes' ? 'checked' : '').' />';
                                    $html .= '<p class="description">'.$value['desc'].'</p>';
                                $html .= '</div>';
                                break;

                            default:
                                # code...
                                break;

                        }
                    $html .= '</div>';
                $html .= '</div>';        
            }
            $html .= '<div class="ultp-settings-wrap ultp-submit-button">';
            $html .= '<div></div>'.get_submit_button();
            $html .= '</div>';

        $html .= '</div>';

        
        
        echo '<form method="post" action="options.php">'.$html.'</form>';
    }

    /**
     * Settings page output
     */
    public static function create_admin_page() { ?>
        <style>
            /* ----Common--- */
            #wpbody-content, #wpwrap{
                background-color: #f2f2f2;
                -webkit-font-smoothing: subpixel-antialiased;
            }
            .error, .notice {
                display: none;
            }
            #wpcontent {
                padding-left: 0px;
            }
            .ultp-option-body {
                position: relative;
                font-size: 15px;
                max-width: 100%;
                display:block;
            }

            /* ----Header--- */
            .ultp-setting-header {
                width: 100%;
                display: flex;
                align-items: center;
                padding: 20px 40px;
                box-sizing: border-box;
                background: #fff;
            }
            .ultp-setting-header img {
                margin-left:auto;
            }
            .ultp-setting-header-info h1 {
                margin: 0;
                font-weight: 300;
                font-size: 35px;
                line-height: normal;
                color: #000;
            }
            .ultp-setting-header-info p {
                font-size: 14px;
                margin-top: 5px;
                margin-bottom: 0;
                font-weight:300;
            }
            .ultp-setting-header-info p a {
                margin-left: 5px;
                text-decoration: none;
                color: #007AFF;
            }
            .ultp-setting-header-info p a span {
                color: #FF9920;
                margin-left: 10px;
                font-size: 18px;
                letter-spacing: 4px;
            }




            /* ----common--- */



            /* ----Sidebar--- */
            .ultp-sidebar-card {
                padding: 0 20px 25px;
                margin-bottom: 30px;
            }
            .ultp-sidebar-title {
                margin: 0 -20px 0;
                background: #FAFAFA;
                padding: 15px 20px;
                font-size: 16px;
                border-bottom: 1px solid #EEEEEE;
                color: #007AFF;
            }
            .ultp-sidebar-card h4 {
                color: #000;
                margin-top: 25px;
            }
            .ultp-sidebar-list {
                list-style: none;
                padding: 0;
                margin-bottom: 30px;
            }
            .ultp-sidebar-list li {
                font-size: 13px;
                padding: 0 20px;
                position: relative;
                margin-right: auto;
                line-height: 24px;
            }
            .ultp-sidebar-list li:after {
                content: "";
                left: 0;
                width: 8px;
                height: 8px;
                border-radius: 100px;
                background: #c1c1c1;
                position: absolute;
                top: 50%;
                margin-top: -4px;
            }
            .ultp-sidebar-content {
                margin-top: 25px;
            }

            /* ----Tab--- */
            .ultp-content-wrap {
                padding: 40px;
                display: flex;
                width: 100%;
                box-sizing: border-box;
            }
            .ultp-tab-content-wrap {
                width: 75%;
                margin-right: 30px;
                -webkit-box-flex: 0;
                -ms-flex: 0 0 75%;
                flex: 0 0 75%;
                max-width: 75%;
            }

            .ultp-tab-title-wrap{
                padding: 0 40px;
                background-color: #ffffff;
                border-bottom: 3px solid #d5d5d5;
            }
            .ultp-tab-title{
                font-weight: 300;
                color: #555;
                font-size: 15px;
                display: inline-block;
                margin-right: 25px;
                padding: 10px 0;
            }
            .ultp-tab-title:hover{
                cursor: pointer;
            }
            .ultp-tab-title.active{
                border-bottom: 3px solid #007AFF;
                color: #007AFF;
                margin-bottom: -3px;
            }
            .ultp-tab-content{
                display: none;
            }
            .ultp-tab-content.active{
                display: block;
            }

            /* ----Settings--- */
            .ultp-settings-wrap {
                margin-bottom: 20px;
                display: grid;
                grid-template-columns: 0.8fr 1fr;
            }

            /* ---Changelog--- */
            .ultp-changelog-wrap {
                background-color: #ffffff;
                margin: 0 auto 30px;
                padding: 25px 30px;
                position: relative;
                box-shadow: 0 0 10px -5px rgba(0,0,0,0.5);
            }
            .ultp-changelog-date{
                font-size: 14px;
                display: inline-block;
                right: 30px;
                position: absolute;
                font-weight: 300;
            }
            .ultp-changelog-version{
                font-size: 16px;
                color: #000000;
                font-weight: 700;
                margin-bottom: 20px;
            }
            .ultp-changelog-title {
                text-transform: capitalize;
                font-size: 14px;
                color: #555555;
                line-height: 28px;
                font-weight: 300;
                margin-bottom: 8px;
            }
            .ultp-changelog-title > span {
                padding: 2px 10px;
                border-radius: 3px;
                margin-right: 10px;
            }
            .changelog-fix{
                color: #721c24;
                background-color: #f8d7da;
                border-color: #f5c6cb;
            }
            .changelog-update{
                color: #856404;
                background-color: #fff3cd;
                border-color: #ffeeba;
            }
            .changelog-new{
                color: #155724;
                background-color: #d4edda;
                border-color: #c3e6cb;
            }

            /* ----Responsive--- */
            @media (max-width: 1500px) {
                .ultp-overview {
                    flex-wrap: wrap;
                    flex-direction: column-reverse;
                }
                .ultp-overview-content {
                    max-width: 100%;
                }
                .ultp-overview-video {
                    margin-bottom: 40px;
                }
                .ultp-overview-video iframe {
                    width: 640px;
                    height: 360px;
                    box-shadow: 0 0 10px -5px rgba(0,0,0,0.5);
                }
                .ultp-video-tutorials {
                    grid-template-columns: 1fr 1fr;
                }
            }
            @media (max-width: 1400px) {
                .ultp-sidebar-card .button.button-primary {
                    text-align: center;
                    display: block;
                }
                .ultp-sidebar-card .button.button-success {
                    text-align: center;
                    display: block;
                    margin-left: 0;
                    margin-top: 10px;
                }
            }
            @media (max-width: 1200px) {
                .ultp-content-wrap {
                    flex-wrap: wrap;
                }
                .ultp-tab-content-wrap {
                    width: 100%;
                    margin-right: 0;
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
                }
                .ultp-admin-sidebar {
                    margin-top: 40px;
                }
            }
            @media (max-width: 1000px) {
                .ultp-setting-header-info h1 {
                    font-size: 28px;
                }
                .ultp-overview-video iframe {
                    width: 350px;
                    height: 250px;
                }
                .ultp-setting-header {
                    padding: 20px 30px;
                }
                .ultp-tab-title-wrap {
                    padding: 0 30px;
                }
                .ultp-overview {
                    padding: 30px;
                }
                .ultp-video-tutorials {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="ultp-option-body">
            <div class="ultp-setting-header">
                <div class="ultp-setting-header-info">
                    <h1>
                        <?php _e('Welcome to <strong>Gutenberg Post Blocks</strong> - Version', 'ultimate-post'); ?><span> <?php echo ULTP_VER; ?></span>
                    </h1>
                    <p><?php esc_html_e('Most Powerful & Advanced Gutenberg Kit', 'ultimate-post'); ?><a href="https://wordpress.org/support/plugin/ultimate-post/reviews/#new-post"><?php esc_html_e('Rate the plugin', 'ultimate-post'); ?><span>★★★★★<span></a></p>
                </div>
                <img src="<?php echo ULTP_URL.'assets/img/logo-option.svg'; ?>" alt="<?php _e('Gutenberg Post Blocks', 'ultimate-post'); ?>">
            </div>

            <?php $section = isset($_GET['tab']) ? $_GET['tab'] :'settings'; ?>
            <div class="ultp-tab-wrap">
                <div class="ultp-tab-title-wrap">
                    <div data-title="settings" class="ultp-tab-title<?php if($section == 'settings'){ echo ' active'; } ?>"><?php _e('General Settings', 'ultimate-post'); ?></div>
                    <div data-title="changelog" class="ultp-tab-title<?php if($section == 'changelog'){ echo ' active'; } ?>"><?php _e('Changelog', 'ultimate-post'); ?></div>
                </div>
                <div class="ultp-content-wrap">
                    <div class="ultp-tab-content-wrap">
                        <div class="ultp-tab-content<?php if($section == 'settings'){ echo ' active'; } ?>"><!-- #Settings Content -->
                            <div class="ultp-overview ultp-admin-card"><!-- #Settings Content --> 
                                <?php self::get_settings_data(); ?>
                            </div>
                        </div>
                        <div class="ultp-tab-content<?php if($section == 'changelog'){ echo ' active'; } ?>"><!-- #Changelog Content -->
                            <?php self::get_changelog_data(); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <script type="text/javascript">
                jQuery( document ).ready(function() {
                    jQuery( document ).on( "click", '.ultp-tab-title', function(e){ 
                        jQuery(this).closest('.ultp-tab-wrap').find('.ultp-tab-title').removeClass('active').eq(jQuery(this).index()).addClass('active')
                        jQuery(this).closest('.ultp-tab-wrap').find('.ultp-tab-content').removeClass('active').eq(jQuery(this).index()).addClass('active');
                        let refresh = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=ultp-settings&tab='+jQuery(this).data('title');
                        window.history.pushState({ path: refresh }, '', refresh);
                        jQuery('input[name=_wp_http_referer]').val(window.location.pathname + '?page=ultp-settings&tab=settings');
                    });
                });
            </script>
        </div>

    <?php }
}

