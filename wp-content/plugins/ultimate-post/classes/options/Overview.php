<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Options_Overview{
    public function __construct() {
        add_submenu_page('ultp-settings', 'Overview', 'Overview', 'manage_options', 'ultp-settings', array( self::class, 'create_admin_page'), 0);
    }

    /**
     * Settings page output
     */
    public static function create_admin_page() { ?>
        <style>
            .style-css{
                background-color: #f2f2f2;
                -webkit-font-smoothing: subpixel-antialiased;
            }
        </style>

        <div class="ultp-option-body">
            
            <?php require_once ULTP_PATH . 'classes/options/Heading.php'; ?>

            <div class="ultp-tab-wrap">
                <div class="ultp-content-wrap">
                    <div class="ultp-overview-wrap">
                        <div class="ultp-tab-content-wrap">
                            <div class="ultp-overview ultp-admin-card">
                                <iframe width="650" height="350" src="https://www.youtube.com/embed/JZxIflYKOuM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    <div class="ultp-overview-btn">
                                        <a href="https://ultp.wpxpo.com/" class="ultp-btn ultp-btn-primary"><?php esc_html_e('Live Preview', 'ultimate-post'); ?></a>
                                        <a class="ultp-btn ultp-btn-transparent" target="_blank" href="https://www.wpxpo.com/gutenberg-post-blocks/?utm_campaign=go_premium"><?php esc_html_e('Plugin Details', 'ultimate-post'); ?></a>
                                    </div>
                            </div>
                        </div><!--/overview-->
                        
                        <div class="wopb-admin-sidebar">
                            <div class="ultp-sidebar ultp-admin-card">
                                <h3><?php esc_html_e('Why Upgrade to Pro', 'ultimate-post'); ?></h3>
                                <p><?php esc_html_e('You are using lite version of Gutenberg Post Blocks. To get more awesome features to upgrade to the Pro version', 'ultimate-post'); ?></p>
                                <ul class="ultp-sidebar-list">
                                    <li><?php esc_html_e('Blocks Layouts Options', 'ultimate-post'); ?></li>
                                    <li><?php esc_html_e('Ready Start Packs', 'ultimate-post'); ?></li>
                                    <li><?php esc_html_e('Advanced Quick Query Options', 'ultimate-post'); ?></li>
                                    <li><?php esc_html_e('Get Category Specific Color Options', 'ultimate-post'); ?></li>
                                    <li><?php esc_html_e('Get All Blocks with Full Control', 'ultimate-post'); ?></li>
                                    <li><?php esc_html_e('Fast & Priority Support', 'ultimate-post'); ?></li>
                                </ul>
                                <a href="https://www.wpxpo.com/gutenberg-post-blocks/?utm_campaign=go_premium" target="_blank" class="ultp-btn ultp-btn-primary ultp-btn-pro"><?php esc_html_e('Upgrade Pro  ➤', 'ultimate-post'); ?></a>
                            </div>
                        </div><!--/sidebar-->
                    </div>
                </div><!--/overview wrapper-->

                <div class="ultp-content-wrap">
                    <div class="ultp-promo-items">
                        <div class="ultp-promo-item ultp-admin-card">
                            <h4><?php _e('Advacned <strong>Query</strong> Builder', 'ultimate-post'); ?></h4> 
                        </div>
                        <div class="ultp-promo-item ultp-admin-card">
                            <h4><?php _e('<strong>Ajax</strong> Powered Post Filter', 'ultimate-post'); ?></h4> 
                        </div>
                        <div class="ultp-promo-item ultp-admin-card">
                            <h4><?php _e('Quick <strong>Query</strong> Builder', 'ultimate-post'); ?></h4> 
                        </div>
                        <div class="ultp-promo-item ultp-admin-card">
                            <h4><?php _e('Ready <strong>Starter</strong> Packs', 'ultimate-post'); ?></h4> 
                        </div>
                        <div class="ultp-promo-item ultp-admin-card">
                            <h4><?php _e('Premade <strong>Block</strong> Design', 'ultimate-post'); ?></h4> 
                        </div>
                    </div>
                </div><!--/Promo-->

                <div class="ultp-content-wrap">
                    <div class="ultp-featured-item ultp-admin-card">   
                        <div class="ultp-featured-image"> 
                            <img loading="lazy" src="<?php echo ULTP_URL.'assets/img/admin/starter-packs.png'; ?>" alt="Filter Category"> 
                        </div>
                        <div class="ultp-featured-content">     
                            <h4><?php _e('Design Library', 'ultimate-post'); ?></h4> 
                            <p><?php _e('Gutenberg Post Blocks comes with a rich and beautiful readymade starter pack and design library. It helps you to create a beautiful site without design knowledge.', 'ultimate-post'); ?></p>       
                            <a class="ultp-btn ultp-btn-primary" target="_blank" href="https://ultp.wpxpo.com/starter-packs/"><?php _e('Explore Details', 'ultimate-post'); ?></a>
                        </div>
                    </div>
                </div><!--/ultp-featured-item-->

                <div class="ultp-content-wrap">
                    <div class="ultp-features ultp-admin-card"> 
                        <div class="ultp-text-center"><h2 class="ultp-admin-title"><?php _e('Core Features', 'ultimate-post'); ?></h2></div> 
                        <div class="ultp-feature-items">    
                            <div class="ultp-feature-item">    
                                <div class="ultp-feature-image">    
                                    <img loading="lazy" src="<?php echo ULTP_URL.'assets/img/admin/free-f1.png'; ?>" alt="Filter Category">        
                                </div>
                                <div class="ultp-feature-content">    
                                    <h4><?php _e('AJAX Powered Post Filter', 'ultimate-post'); ?></h4> 
                                    <div><?php _e('Category filter is the best element to show many contents in a small place. By clicking category users can easily read their interesting content.', 'ultimate-post'); ?></div>
                                </div>
                            </div>
                            <div class="ultp-feature-item"> 
                                <div class="ultp-feature-image">    
                                    <img loading="lazy" src="<?php echo ULTP_URL.'assets/img/admin/free-f2.png'; ?>" alt="Filter Category">        
                                </div>   
                                <div class="ultp-feature-content">    
                                    <h4><?php _e('Advacned Pagination Options', 'ultimate-post'); ?></h4> 
                                    <div><?php _e('Gutenberg Post Blocks comes with the AJAX powered Pagination system, that’s why content is loaded without a total page reload.', 'ultimate-post'); ?></div>
                                </div>
                            </div>
                            <div class="ultp-feature-item">    
                                <div class="ultp-feature-image">    
                                    <img loading="lazy" src="<?php echo ULTP_URL.'assets/img/admin/free-f3.png'; ?>" alt="Filter Category">        
                                </div>
                                <div class="ultp-feature-content">    
                                    <h4><?php _e('Awesome Typography Control', 'ultimate-post'); ?></h4> 
                                    <div><?php _e('Typography is one of the major concerns for readability. Gutenberg Post Blocks merges all the Google free fonts in one place.', 'ultimate-post'); ?></div>
                                </div>
                            </div>
                            <div class="ultp-feature-item"> 
                                <div class="ultp-feature-image">    
                                    <img loading="lazy" src="<?php echo ULTP_URL.'assets/img/admin/free-f4.png'; ?>" alt="Filter Category">        
                                </div>   
                                <div class="ultp-feature-content">    
                                    <h4><?php _e('Dynamic Post Slider', 'ultimate-post'); ?></h4> 
                                    <div><?php _e('Post Slider is now easier to build using post slider blocks. It is fully dynamic and can apply any custom query to display the post.', 'ultimate-post'); ?></div>
                                </div>
                            </div>  
                        </div>
                        <div class="ultp-text-center">
                            <a class="ultp-btn ultp-btn-primary" target="_blank" href="https://www.wpxpo.com/gutenberg-post-blocks/all-features/?utm_campaign=go_premium"><?php esc_html_e('More Features', 'ultimate-post'); ?></a>
                        </div>  
                    </div>  
                </div><!--/feature-->

                <?php require_once ULTP_PATH . 'classes/options/Footer.php'; ?>

            </div>
        </div>

    <?php }
}

