<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Options_Addons{
    public function __construct() {
        add_submenu_page('ultp-settings', 'Addons', 'Addons', 'manage_options', 'ultp-addons', array( $this, 'create_admin_page'), 10);
    }

    public static function all_addons(){
        $all_addons = array(
            'ultp_category' => array(
                'name' => __( 'Category', 'ultimate-post' ),
                'desc' => __( 'Set your category specific color for block design.', 'ultimate-post' ),
                'img' => ULTP_URL.'/assets/img/addons/category-style.svg',
                'is_pro' => true
            )
        );
        return apply_filters('ultp_addons_config', $all_addons);
    }

    /**
     * Settings page output
     */
    public function create_admin_page() { ?>
        <style>
            .style-css{
                background-color: #f2f2f2;
                -webkit-font-smoothing: subpixel-antialiased;
            }
        </style>

        <div class="ultp-option-body">
            
            <?php require_once ULTP_PATH . 'classes/options/Heading.php'; ?>

            <div class="ultp-content-wrap ultp-addons-wrap">
                <div class="ultp-text-center"><h2 class="ultp-admin-title"><?php _e('All Addons', 'ultimate-post'); ?></h2></div> 
                <div class="ultp-addons-items">
                    <?php
                        $option_value = get_option('ultp_addons_option', array());
                        $addons_data = self::all_addons();
                        foreach ($addons_data as $key => $val) {
                            echo '<div class="ultp-addons-item ultp-admin-card">';
                                echo '<div class="ultp-addons-item-content">';
                                    echo '<img src="'.$val['img'].'" />';
                                    echo '<h4>'.$val['name'].'</h4>';
                                    echo '<div class="ultp-addons-desc">'.$val['desc'].'</div>';
                                echo '</div>';
                            if( $val['is_pro'] && !defined('ULTP_PRO_VER') ){
                                echo '<div class="ultp-addons-btn">';
                                    echo '<a class="ultp-btn ultp-btn-default" target="_blank" href="https://www.wpxpo.com/gutenberg-post-blocks/?utm_campaign=go_premium">'.__("Get Pro", "ultimate-post").'</a>';
                                echo '</div>';
                            } else {
                                echo '<div class="ultp-addons-btn">';
                                    echo '<label class="ultp-switch">';
                                        echo '<input class="ultp-addons-enable" '.(($val['is_pro'] && (!defined('ULTP_PRO_VER'))) ? 'disabled' : '').' data-addon="'.$key.'" type="checkbox" '.( isset($option_value[$key]) && $option_value[$key] == 'true' ? 'checked' : '' ).'>';
                                        echo '<span class="ultp-slider ultp-round"></span>';
                                    echo '</label>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                    ?> 
                </div>
            </div>
        </div>

    <?php }
}