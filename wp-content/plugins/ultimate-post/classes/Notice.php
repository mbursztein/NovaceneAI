<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Notice {
    public function __construct(){
		add_action('admin_notices', array($this, 'ultp_installation_notice_callback'));
		add_action('wp_ajax_ultp_get_pro_notice', array($this, 'set_dismiss_notice_callback'));
	}
	
	// Dismiss Notice Callback
	public function set_dismiss_notice_callback() {
		if (!wp_verify_nonce($_REQUEST['wpnonce'], 'ultp-free-nonce')) {
			return ;
        }
        set_transient( 'ultp_get_pro_notice_v2', 'off', 2592000 ); // 30 days notice
	}
	public function ultp_installation_notice_callback() {
		if (get_transient('ultp_get_pro_notice_v2') != 'off' && (!defined('ULTP_PRO_VER'))) {
			$this->ultp_notice_css();
			$this->ultp_notice_js();
			?>
			<div class="ultp-pro-notice">
                <div class="ultp-pro-notice-content">
                    <a class="wc-dismiss-notice" data-security=<?php echo wp_create_nonce('ultp-free-nonce'); ?>  data-ajax=<?php echo admin_url('admin-ajax.php'); ?> href="#"><span class="dashicons dashicons-no-alt"></span> <?php _e('Dismiss', 'ultimate-post'); ?></a>
                    <a target="_blank" href="https://www.wpxpo.com/gutenberg-post-blocks/?utm_campaign=go_premium">
                        <img loading="lazy" src="<?php echo ULTP_URL.'assets/img/banner.jpg'; ?>" alt="logo" />
                    </a>
                </div>
			</div>
			<?php
		}
	}
	public function ultp_notice_css() {
		?>
		<style type="text/css">
            .wc-install {
                display: -ms-flexbox;
                display: flex;
                align-items: center;
                background: #fff;
                margin-top: 40px;
                width: calc(100% - 50px);
                border: 1px solid #ccd0d4;
                padding: 15px;
                border-radius: 4px;
            }   
            .ultp-pro-notice {
                margin-top: 40px;
                width: calc(100% - 50px); 
            }
            .ultp-pro-notice img { 
                max-width: 100%;
            }
            .ultp-pro-notice-content {
                position: relative;
                padding-top: 30px;
                display: inline-block;
            }
            .ultp-pro-notice-content .wc-dismiss-notice {
                padding-top: 0px;
                position: absolute;
                right: 0;
                top: 6px;
            }
            .wc-install img {
                margin-right: 10px; 
				max-width: 100%;
            }
            .wc-install-body img{
                -ms-flex: 1;
                flex: 1;
            }
            .wc-install-body > div {
                max-width: 450px;
                margin-bottom: 20px;
            }
            .wc-install-body h3 {
                margin-top: 0;
                font-size: 24px;
                margin-bottom: 15px;
            }
            .ultp-install-btn {
                margin-top: 15px;
                display: inline-block;
            }
			.wc-install .dashicons{
				display: none;
				animation: dashicons-spin 1s infinite;
				animation-timing-function: linear;
			}
			.wc-install.loading .dashicons {
				display: inline-block;
				margin-top: 12px;
				margin-right: 5px;
			}
            .wc-install-body img {
                max-width: 100%;
            }
			.wc-dismiss-notice {
				position: relative;
				text-decoration: none;
				float: right;
				right: 26px;
			}
			.wc-dismiss-notice .dashicons{
				display: inline-block;
    			text-decoration: none;
				animation: none;
			}
		</style>
		<?php
	}


	public function ultp_notice_js() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				'use strict';
				// Dismiss notice
				$(document).on('click', '.wc-dismiss-notice', function(e){
					e.preventDefault();
					const that = $(this);
					$.ajax({
						url: that.data('ajax'),
						type: 'POST',
						data: { 
							action: 'ultp_get_pro_notice', 
							wpnonce: that.data('security')
						},
						success: function (data) {
							that.parents('.wc-install').hide("slow", function() { that.parents('.wc-install').remove(); });
						},
						error: function(xhr) {
							console.log('Error occured. Please try again' + xhr.statusText + xhr.responseText );
						},
					});
				});
			});
		</script>
		<?php
	}

}