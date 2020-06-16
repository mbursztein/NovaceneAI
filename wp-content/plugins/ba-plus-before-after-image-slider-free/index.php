<?php /*
	
	**************************************************************************
	
	Plugin Name:  BA Plus - Before & After Image Slider FREE
	Description:  Offers a simple and effective way to compare two or more different images in the same frame.
	Version:      1.0.3
	Author:       Aluka
	
**************************************************************************/

if(!function_exists('S201_BeforeAfterImage')){
	if(is_admin()){
		require_once( dirname(__FILE__) . '/admin.php' );
	}

	global $wpdb, $tbl_s201_bai;
	$tbl_s201_bai = $wpdb->prefix . "s201_bai";

	register_activation_hook(__FILE__, function() {
		global $wpdb, $tbl_s201_bai;
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS $tbl_s201_bai (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`post_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
		`slides` text NOT NULL,
		`meta` text NOT NULL,
		PRIMARY KEY (`id`),
		KEY `post_id` (`post_id`)
		) $charset_collate;";
		
		$wpdb->query($sql);
	});
		
	// Start plugin
	add_action('init', 'S201_BeforeAfterImage');
	function S201_BeforeAfterImage() {
		define('S201_VERSION', '1.0.3');
		define('S201_PLUGIN_URL', plugins_url('', __FILE__));
		$GLOBALS['s201_styles'] = array('style_1', 'style_2', 'style_3', 'style_4', 'style_5');
		if(!is_admin()){
			wp_enqueue_style('s201-bai', S201_PLUGIN_URL.'/css/ba-plus.min.css', array(), S201_VERSION, 'screen');
			wp_enqueue_script('s201-bai', S201_PLUGIN_URL.'/js/ba-plus.min.js', array('jquery'), S201_VERSION, true);
		}
	}

	add_shortcode('s201_bai', function($atts){
		ob_start();
		s201_bai_slider($atts['id']);
		$slides_html = ob_get_contents();
		ob_end_clean();
		return $slides_html;
	});

	function s201_bai_slider($row_id){
		global $wpdb, $tbl_s201_bai;
		$row = $wpdb->get_row($wpdb->prepare("SELECT slides, meta FROM $tbl_s201_bai WHERE id=%d", $row_id), ARRAY_A);
		if(!$row){
			echo '[BA Plus Slider "'.$row_id.'" not found]';
			return;
		}
		$slides = json_decode($row['slides'], true);
		$meta = json_decode($row['meta'], true);
		if(isset($GLOBALS['s201_slides'][$row_id])) $GLOBALS['s201_slides'][$row_id]++;
		else $GLOBALS['s201_slides'][$row_id] = 1;
		$element_id = 's201_slides-'.$row_id.'-'.$GLOBALS['s201_slides'][$row_id];
		echo '<div id="'.$element_id.'" class="s201_slides s201_slides-'.$row_id.(isset($meta['full_width'])?' s201_full_width':'').'"'.($meta['max_width']?' style="max-width:'.$meta['max_width'].'px"':'').' data-s201="'.s201_get_data_init($meta).'">';
		include(dirname(__FILE__) . '/templates/default.php');
		//echo s201_get_script_init_js($element_id, $meta);
		echo '</div>';
	}

	function s201_get_data_init($meta){
		return '{&quot;vertical&quot;:'.(int)isset($meta['vertical']).($meta['sliding_behavior']?',&quot;sliding_behavior&quot;:&quot;'.$meta['sliding_behavior'].'&quot;':'').(isset($meta['autoplay'])?',&quot;autoplay_speed&quot;:&quot;'.$meta['autoplay_speed'].'&quot;':'').(isset($meta['auto_slide'])?',&quot;auto_slide&quot;:true,&quot;slide_speed&quot;:'.$meta['slide_speed'].',&quot;slide_delay&quot;:'.$meta['slide_delay']:'').'}';
	}

	function s201_get_script_init_js($element_id, $meta){
		return '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#'.$element_id.'").s201_BAI({vertical:'.(int)isset($meta['vertical']).($meta['sliding_behavior']?',sliding_behavior:"'.$meta['sliding_behavior'].'"':'').',autoplay_speed:'.(isset($meta['autoplay'])?$meta['autoplay_speed']:'0').(isset($meta['auto_slide'])?',auto_slide:true,slide_speed:'.$meta['slide_speed'].',slide_delay:'.$meta['slide_delay']:'').'});});</script>';
	}

	function s201_hex_to_rgb($hex){
		list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
		return $r.','.$g.','.$b;
	}
	
	add_action('admin_notices', function(){
		if(is_plugin_active('ba-plus-before-after-image-slider-free/index.php') && is_plugin_active('ba-plus-before-after-image-slider/index.php')){
			?>
			<div class="error">
				<p>Please deactivate <strong>BA Plus - Before & After Image Slider FREE</strong> plugin!</p>
			</div>
			<?php
		}
	});
}