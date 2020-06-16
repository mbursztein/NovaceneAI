<?php
	require_once( dirname(__FILE__) . '/S201_BAI_Table.php' );
	$GLOBALS['s201_is_premium'] = file_exists(dirname(__FILE__).'/premium.php');
	if($GLOBALS['s201_is_premium']) require_once(dirname(__FILE__) . '/premium.php');
	
	add_action('admin_menu', function(){
		add_menu_page('Before After Plus', 'Before After Plus', 'activate_plugins', 's201-bai-slider', 's201_render_bai');
		add_submenu_page('s201-bai-slider', 'Add BAI Slider', 'Add BAI Slider', 'activate_plugins', 'post-new.php?post_type=s201_bai_post');
	});
	
	function s201_render_bai(){
		$s201_bai_tbl = new S201_BAI_Table();
		global $wpdb, $tbl_s201_bai;
		if(isset($_POST['action'])) {
			$action = $s201_bai_tbl->current_action();
			if($action == 'delete' && !empty($_POST['IDs'])){
				foreach($_POST['IDs'] as $id){
					$wpdb->query($wpdb->prepare("DELETE FROM ".$tbl_s201_bai." WHERE id=%d", sanitize_key($id)));
				}
			}
		}
		if(isset($_GET['action'])) {
			if($_GET['action']=='delete' && wp_verify_nonce($_GET['_wpnonce'], 'delete_item'.$_GET['id'])){
				$wpdb->delete($tbl_s201_bai, array('id' => sanitize_key($_GET['id'])));
			}
		}
		if(isset($_POST['s'])){
			$s201_bai_tbl->prepare_items(esc_html($_POST['s']));
		}
		else {
			$s201_bai_tbl->prepare_items();
		}
		echo '<div class="wrap"><h1 class="wp-heading-inline">BA Plus - Before After Image Slider</h1> <a href="'.admin_url('post-new.php?post_type=s201_bai_post').'" class="page-title-action">Add New</a>';
		echo '<form method="post" style="float:right;padding-top:10px;">
			<label class="screen-reader-text" for="search-input">Search by ID:</label>
			<input type="search" id="search-input" name="s" value="'.(isset($_POST['s'])?esc_attr($_POST['s']):'').'"/>
			<input type="submit" id="search-submit" class="button" value="Search by ID"></form><form method="post">';
		$s201_bai_tbl->display();
		echo '</form></div>';
	}
	
	add_action('init', function(){
		register_post_type( 's201_bai_post',
		array(
		'labels' => array(
		'name' => __( 'Before After' ),
		'singular_name' => __( 'Before After' ),
		),
		'supports' => array('title'),
		'public' => false,
		'show_in_menu' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'can_export' => false // Exclude from export
		)
		);
	});
	
	add_action('add_meta_boxes', 's201_add_metaboxes');
	function s201_add_metaboxes(){
		$post_types = array();
		foreach(get_post_types() as $post_type){
			if(post_type_supports($post_type, 'editor')){
				$post_types[]= $post_type;
			}
		}
		$post_types[]= 's201_bai_post';
		add_meta_box('s201_meta_box', 'Before & After Image Slider', 's201_meta_box_html', $post_types, 'normal', 'high');
	}
	
	function s201_meta_box_html() {
		echo '<script type="text/javascript">var s201_ajax_nonce="'.wp_create_nonce('s201_ajax_nonce').'";</script>';
		wp_enqueue_style('s201-bai', S201_PLUGIN_URL.'/css/ba-plus.min.css', array(), S201_VERSION, 'screen');
		wp_enqueue_script('s201-bai', S201_PLUGIN_URL.'/js/ba-plus.min.js', array(), S201_VERSION, true);
		wp_enqueue_style('s201-bai-admin', S201_PLUGIN_URL.'/css/style.css', array(), S201_VERSION, 'screen');
		wp_enqueue_script('s201-bai-admin', S201_PLUGIN_URL.'/js/admin.js', array(), S201_VERSION, true);
		if($GLOBALS['s201_is_premium']) wp_enqueue_script('s201-bai-premium', S201_PLUGIN_URL . '/js/admin-premium.js', array(), S201_VERSION);
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_media();
	
		global $post, $wpdb, $tbl_s201_bai;
		// Use nonce for verification
		echo '<input type="hidden" id="s201_nonce" name="s201_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
		echo '<label><input type="checkbox" id="s201_enable_preview" name="s201_options[preview]" checked/> Enable Preview</label>';
		if(isset($_GET['bai_id']) && is_numeric($_GET['bai_id'])){
			$rows = $wpdb->get_results($wpdb->prepare("SELECT id, slides, meta FROM $tbl_s201_bai WHERE id=%d", sanitize_key($_GET['bai_id'])), ARRAY_A);
		}
		else{
			$rows = $wpdb->get_results($wpdb->prepare("SELECT id, slides, meta FROM $tbl_s201_bai WHERE post_id=%d", $post->ID), ARRAY_A);
		}
		echo '<div id="s201_all_slides">';
		s201_create_slides_rows($rows);
		echo '</div>';
		echo '<div id="s201_add_new_slides_bt" class="s201_button">Add New Slider</div>';
		echo '<fieldset disabled id="s201_add_slides_template" style="display:none;" data-inc="'.count($rows).'">';
		$rows = array('new_01010'=>array('id'=>'new_row_01010', 'slides' => '[{"imgs":[]}]', 'meta' => '[]'));
		s201_create_slides_rows($rows);
		echo '</fieldset>';
		s201_create_sample_slides();
		
	?>
	<?php
	}
	
	add_action('save_post', 's201_save_post');
	
	function s201_save_post($post_id){
		// Verify nonce
		if (!wp_verify_nonce($_POST['s201_nonce'], basename(__FILE__))) return $post_id;
		
		// Check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		// Check permissions
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		}
		elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
		global $wpdb, $tbl_s201_bai;
		$_POST['s201_label'] = array_map('stripslashes_deep', $_POST['s201_label']);
		foreach($_POST['s201_img_id'] as $slides_id=>$slides){
			$data = s201_validate_post_data($slides_id, $slides);
			if(strpos($slides_id, 'new_')===0){
				$data['post_id'] = $post_id;
				$wpdb->insert($tbl_s201_bai, $data);
				$row_id = $wpdb->insert_id;
			}
			else{
				$wpdb->update($tbl_s201_bai, $data, array('id'=>$slides_id));
			}
		}
		if(isset($row_id)) return $row_id; // for create shortcut
		//echo '<pre>';print_r($_POST);echo '</pre>';exit;
	}
	
	function s201_validate_post_data($slides_id, $slides){
		//ksort($slides);
		$data = array();
		foreach($slides as $k=>$slide){
			foreach($slide as $key=>$value) {
				$slide[$key] = sanitize_key($value);
				$_POST['s201_label'][$slides_id][$k][$key] = sanitize_text_field($_POST['s201_label'][$slides_id][$k][$key]);
				$_POST['s201_slider_pos'][$slides_id][$k][$key] = sanitize_key($_POST['s201_slider_pos'][$slides_id][$k][$key]);
			}
			$data[] = array('imgs'=>$slide, 'label'=>$_POST['s201_label'][$slides_id][$k], 'slider_pos'=>$_POST['s201_slider_pos'][$slides_id][$k], 'caption'=>sanitize_text_field($_POST['s201_caption'][$slides_id][$k]));
		}
		$meta = array();
		foreach($_POST['s201_meta'] as $post_name=>$v){
			if(isset($v[$slides_id])) $meta[$post_name] = sanitize_text_field($v[$slides_id]);
		}
		return array('slides'=>json_encode($data), 'meta'=>json_encode($meta));
	}
	
	function s201_create_slides_rows($rows){
		foreach($rows as $k_slides=>$row){
			$slides = json_decode($row['slides'], true);
			$meta = json_decode($row['meta'], true);
			?>
			<div id="s201_slides-<?php echo $k_slides; ?>" class="s201_wrap_slides" data-slides_id="<?php echo $row['id']; ?>">
			<div class="s201_wrap_float">
				<div class="s201_float_left">
					<b>Shortcode:</b> <?php if($row['id']=='new_row_01010'){ ?>
					<!--<i class="s201_howto">You must save post to create Shortcode.</i>-->
					<span class="s201_wrap_ajax"><div class="s201_button s201_create_shortcode_bt">Save Slider & Create Shortcode</div></span>
					<?php }else{ ?>
					<input type="text" readonly="true" class="s201_shortcode" value="[s201_bai id=&quot;<?php echo $row['id']; ?>&quot;]"/>
					<b>PHP Code:</b> <input type="text" readonly="true" class="s201_phpcode" value="&lt;?php if( function_exists(&#039;s201_bai_slider&#039;) ) s201_bai_slider(<?php echo $row['id']; ?>); ?&gt;"/>
					<?php } ?>
				</div>
				<div class="s201_float_right">
					<div class="s201-text-right">
						<div class="s201_button s201_delete_slides">Delete Slider</div>
					</div>
				</div>
			</div>
			<div class="s201_slides_config">
			<div class="s201_tabs" data-key="<?php echo $k_slides; ?>" data-inc="<?php echo count($slides); ?>">
			<?php
			echo '<ul>';
			foreach($slides as $k=>$slider){
				echo '<li class="sortable-item s201_transition"><div class="handle"></div><a href="#tab-'.$k_slides.'-'.$k.'"><span class="s201_transition">Slide #'.($k+1).'</span></a></li>';
			}
			echo '<li class="s201_transition"><span class="s201_add_tab s201_transition" title="Add New Slide">+</span></li></ul>';
			foreach($slides as $k=>$slider){
		?>
		<div id="tab-<?php echo $k_slides.'-'.$k; ?>" class="s201_tab_content"<?php if($k) echo ' style="display:none;"'; ?>>
		<div class="s201_tab_slider">
			<table class="s201_images" data-slider_id="<?php echo $k; ?>">
				<thead><tr><th style="width:10px;"><i alt="f214" class="dashicons dashicons-editor-justify"></i></th><th>Image</th><th>Label</th><th class="s201_start_pos_cell">Start Pos</th><th></th></tr></thead>
				<tbody>
				<?php
					foreach($slider['imgs'] as $k2=>$media_id){
						echo '<tr class="sortable-item"><td class="handle">'.($k2+1).'</td><td><div class="s201_wrap_img"><img id="'.$media_id.'" src="'.wp_get_attachment_thumb_url($media_id).'" data-src="'.wp_get_attachment_url($media_id).'"><div class="s201_change_img" title="Change Image"><i class="dashicons dashicons-update"></i></div><input class="s201_img_id" type="hidden" name="s201_img_id['.$row['id'].']['.$k.'][]" value="'.$media_id.'" autocomplete="off"/></div></td><td><textarea class="s201_label" name="s201_label['.$row['id'].']['.$k.'][]" autocomplete="off" placeholder="Label...">'.$slider['label'][$k2].'</textarea></td><td class="s201_start_pos_cell"><input type="text" readonly name="s201_slider_pos['.$row['id'].']['.$k.'][]" class="s201_slider_pos" value="'.$slider['slider_pos'][$k2].'" autocomplete="off"/>%</td><td><div class="s201_remove_img" title="Remove Image"><i class="dashicons dashicons-no-alt"></i></div></td></tr>';
					}
					?>
				</tbody>
				<tfoot>
				<tr><td colspan="2" style="text-align:left;"><button class="button s201_add_imgs_bt" type="button">Add Images</button></td><td class="s201_start_pos_cell"></td><td colspan="2" style="text-align:right;"><button class="button s201_delete_slide_bt" type="button">Delete Slide</button></td></tr>	
				</tfoot>
			</table>
			<textarea class="s201_caption s201_full_width" name="s201_caption[<?php echo $row['id']; ?>][<?php echo $k; ?>]" autocomplete="off" placeholder="Caption..."><?php echo(isset($slider['caption'])?$slider['caption']:''); ?></textarea>
		</div>
		</div>
		<?php
			}
			?>
			</div>
			<div class="s201_feature">
				<div class="s201_button s201_export_bt">Export to HTML</div> <label><input type="checkbox" name="s201_export_entire_document"/> Entire document</label>
				<textarea class="s201_export_html"></textarea>
				<?php if(!$GLOBALS['s201_is_premium']) echo '<div class="s201_premium"><span><a href="http://dev.abydx.com/bai/pro/">Available in pro version.</a></span></div>'; ?>
			</div>
			</div>
			<div class="s201_options">
				<label><input type="checkbox" class="s201_side_by_side_chk" name="s201_meta[side_by_side][<?php echo $row['id']; ?>]" <?php echo(isset($meta['side_by_side'])&&$meta['side_by_side']?'checked ':''); ?>autocomplete="off" value="1"/> Side by Side</label>
				<label><input type="checkbox" class="s201_vertical_chk" name="s201_meta[vertical][<?php echo $row['id']; ?>]" <?php echo(isset($meta['vertical'])&&$meta['vertical']?'checked ':''); ?>autocomplete="off" value="1"/> Vertical</label>
				<a href="javascript:;" class="s201_toggle_settings_panel">Additional Settings</a>
				<div class="s201_additional_settings">
					<table>
						<tbody>
						<tr><td colspan="2">
							<label><input type="checkbox" class="s201_always_show_label_chk" name="s201_meta[always_show_label][<?php echo $row['id']; ?>]" <?php echo(isset($meta['always_show_label'])&&$meta['always_show_label']?'checked ':''); ?>autocomplete="off" value="1"/> Always show Labels</label>
							<label><input type="checkbox" class="s201_full_width_chk" name="s201_meta[full_width][<?php echo $row['id']; ?>]" <?php echo(isset($meta['full_width'])&&$meta['full_width']?'checked ':''); ?>autocomplete="off" value="1"/> Full-Width</label>
						</td></tr>
						<tr><td><b>Max-Width</b> (px)<br><i class="s201_howto">0 to disable</i></td><td class="s201-text-right"><input type="number" min="0" class="s201_max_width" name="s201_meta[max_width][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['max_width'])?$meta['max_width']:'0'); ?>" autocomplete="off"/></td></tr>
						<tr><td><b><label><input type="checkbox" class="s201_autoplay_chk" name="s201_meta[autoplay][<?php echo $row['id']; ?>]" <?php echo(isset($meta['autoplay'])&&$meta['autoplay']?'checked ':''); ?>autocomplete="off" value="1"/> Autoplay Slideshow</label></b><br><i class="s201_howto">Duration per slide (Milliseconds)</i></td><td class="s201-text-right"><input type="number" min="0" step="500" class="s201_autoplay_speed" name="s201_meta[autoplay_speed][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['autoplay_speed'])?$meta['autoplay_speed']:'3000'); ?>" <?php echo(isset($meta['autoplay'])&&$meta['autoplay']?'':'disabled '); ?>autocomplete="off" placeholder="Duration"/></td></tr>
						<tr><td><b><label><input type="checkbox" class="s201_auto_slide_chk" name="s201_meta[auto_slide][<?php echo $row['id']; ?>]" <?php echo(isset($meta['auto_slide'])&&$meta['auto_slide']?'checked ':''); ?>autocomplete="off" value="1"/> Auto Move Slide Bar</label></b><br><i class="s201_howto">Speed & Delay (Milliseconds)</i></td><td class="s201-text-right"><input type="number" min="0" step="500" class="s201_slide_speed" name="s201_meta[slide_speed][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['slide_speed'])?$meta['slide_speed']:'3000'); ?>" <?php echo(isset($meta['auto_slide'])&&$meta['auto_slide']?'':'disabled '); ?>autocomplete="off" placeholder="Speed"/><input type="number" min="0" step="500" class="s201_slide_delay" name="s201_meta[slide_delay][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['slide_delay'])?$meta['slide_delay']:'500'); ?>" <?php echo(isset($meta['auto_slide'])&&$meta['auto_slide']?'':'disabled '); ?>autocomplete="off" placeholder="Delay"/></td></tr><?php
						$values = array('drag', 'click', 'hover');
						$options = '';
						$sliding_behavior = isset($meta['sliding_behavior'])?$meta['sliding_behavior']:'style_1';
						foreach($values as $v){
							if($sliding_behavior == $v) $selected = ' selected';
							else $selected = '';
							$options .= '<option value="'.$v.'"'.$selected.'>'.ucfirst(str_replace('_', ' ', $v)).'</option>';
						}
						?>
						<tr><td><b>Sliding Behavior</b><br></td><td class="s201-text-right"><select class="s201_sliding_behavior s201_full_width" name="s201_meta[sliding_behavior][<?php echo $row['id']; ?>]" autocomplete="off"><?php echo $options; ?></select></td></tr>
						<?php
						$values = $GLOBALS['s201_styles'];
						$options = '';
						$slider_style = isset($meta['slider_style'])?$meta['slider_style']:'style_1';
						foreach($values as $v){
							if($slider_style == $v) $selected = ' selected';
							else $selected = '';
							$options .= '<option value="'.$v.'"'.$selected.'>'.ucfirst(str_replace('_', ' ', $v)).'</option>';
						}
						?>
						<tr><td><b>Slide Bar Style</b></td><td class="s201-text-right"><select class="s201_slider_style s201_full_width" name="s201_meta[slider_style][<?php echo $row['id']; ?>]" autocomplete="off"><?php echo $options; ?></select></td></tr>
						<tr><td><b>Slide Bar Color & Size</b></td><td class="s201-text-right"><table><tbody><tr><td class="s201-text-right"><input type="text" name="s201_meta[slide_bar_color][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['slide_bar_color'])?$meta['slide_bar_color']:'#ffffff'); ?>" data-css="slide_bar_color" class="s201_custom_color" data-default-color="#ffffff" autocomplete="off" /></td><td class="s201_slide_size"><input type="number" name="s201_meta[slide_size][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['slide_size'])?$meta['slide_size']:'2'); ?>" data-css="slide_size" autocomplete="off" min="0" max="4"/></td></tr></tbody></table></td></tr>
						<tr><td><b>Slide Bar Opacity</b></td><td class="s201-text-right"><input type="number" min="0" max="100" step="5" class="s201_slide_opacity" name="s201_meta[slide_opacity][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['slide_opacity'])?$meta['slide_opacity']:'100'); ?>" data-css="slide_opacity" autocomplete="off"/></td></tr>
						<?php
						$values = array('style_1', 'style_2', 'style_3');
						$values[]= 'hidden';
						$options = '';
						$prev_next_display = isset($meta['prev_next_display'])?$meta['prev_next_display']:'visible';
						foreach($values as $v){
							if($prev_next_display == $v) $selected = ' selected';
							else $selected = '';
							$options .= '<option value="'.$v.'"'.$selected.'>'.ucfirst(str_replace('_', ' ', $v)).'</option>';
						}
						?>
						<tr><td><b>Prev/Next Buttons</b></td><td class="s201-text-right"><select class="s201_pn_style s201_full_width" name="s201_meta[prev_next_display][<?php echo $row['id']; ?>]" autocomplete="off"><?php echo $options; ?></select></td></tr>
						<?php
						$values = array('style_1', 'style_2', 'style_3');
						$values[]= 'thumbnails';
						$values[]= 'hidden';
						$options = '';
						$nav_display = isset($meta['nav_display'])?$meta['nav_display']:'visible';
						foreach($values as $v){
							if($nav_display == $v) $selected = ' selected';
							else $selected = '';
							$options .= '<option value="'.$v.'"'.$selected.'>'.ucfirst(str_replace('_', ' ', $v)).'</option>';
						}
						?>
						<tr><td><b>Navigation</b></td><td class="s201-text-right"><select class="s201_nav_style s201_full_width" name="s201_meta[nav_display][<?php echo $row['id']; ?>]" autocomplete="off"><?php echo $options; ?></select></td></tr>
						<tr><td><b>Label Color & Font Size (px)</b></td><td class="s201-text-right"><table><tbody><tr><td><input type="text" name="s201_meta[label_color][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['label_color'])?$meta['label_color']:'#ffffff'); ?>" data-css="label_color" class="s201_custom_color" data-default-color="#ffffff" autocomplete="off" /></td><td class="s201_font_size"><input type="number" name="s201_meta[label_size][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['label_size'])?$meta['label_size']:'14'); ?>" data-css="label_size" autocomplete="off" /></td></tr></tbody></table></td></tr>
						<tr><td><b>Label Background Color</b></td><td class="s201-text-right"><input type="text" name="s201_meta[label_bg_color][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['label_bg_color'])?$meta['label_bg_color']:'#000000'); ?>" data-css="label_bg_color" class="s201_custom_color s201_rgba" data-default-color="#000000" autocomplete="off" /></td></tr>
						<tr><td><b>Caption Color & Font Size (px)</b></td><td class="s201-text-right"><table><tbody><tr><td><input type="text" name="s201_meta[caption_color][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['caption_color'])?$meta['caption_color']:'#ffffff'); ?>" data-css="caption_color" class="s201_custom_color" data-default-color="#ffffff" autocomplete="off" /></td><td class="s201_font_size"><input type="number" name="s201_meta[caption_size][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['caption_size'])?$meta['caption_size']:'14'); ?>" data-css="caption_size" autocomplete="off" /></td></tr></tbody></table></td></tr>
						<tr><td><b>Caption Background Color</b></td><td class="s201-text-right"><input type="text" name="s201_meta[caption_bg_color][<?php echo $row['id']; ?>]" value="<?php echo(isset($meta['caption_bg_color'])?$meta['caption_bg_color']:'#000000'); ?>" data-css="caption_bg_color" class="s201_custom_color s201_rgba" data-default-color="#000000" autocomplete="off" /></td></tr>
						</tbody>
					</table>
				</div>
				<div class="s201_preview">
					<p>
						<label><input type="checkbox" name="s201_meta[custom_pos][<?php echo $row['id']; ?>]" class="s201_custom_pos_chk" <?php echo(isset($meta['custom_pos'])&&$meta['custom_pos']?'checked ':''); ?>autocomplete="off" value="1"/>Custom start position of slide bar</label>
						<div class="s201_custom_pos_val">[<span></span>] <i class="s201_howto">Move slide bar to change value</i> <button type="button" class="button button-small s201_save_custom_pos_bt">Save</button></div>
					</p>
					<div class="s201_preview_slides"></div>
				</div>
				
				<div class="s201_import_tabs s201_feature">
					<ul>
						<li class="sortable-item s201_transition"><a href="#s201_tab-import"><span class="s201_transition">Import Settings</span></a></li>
						<li class="sortable-item s201_transition"><a href="#s201_tab-export"><span class="s201_transition">Export Settings</span></a></li>
					</ul>
					<div class="s201_tab_body">
						<div id="s201_tab-import">
							<table class="s201_full_width">
								<tbody>
									<tr>
										<td><textarea class="s201_import_settings" autocomplete="off" placeholder="Import settings..."></textarea></td>
										<td style="width:90px;"><div class="s201_button s201_import_settings_bt">Import</div></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="s201_tab-export" style="display:none;">
							<table class="s201_full_width">
								<tbody>
									<tr>
										<td><textarea class="s201_export_settings" autocomplete="off"></textarea></td>
										<td style="width:90px;"><div class="s201_button s201_export_settings_bt">Export</div></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<?php if(!$GLOBALS['s201_is_premium']) echo '<div class="s201_premium"><span><a href="http://dev.abydx.com/bai/pro/">Available in pro version.</a></span></div>'; ?>
				</div>
			</div>
			</div>
		<?php
		}
	}
	
	function s201_create_sample_slides(){
		$slides = array(array('imgs'=>array(0,0),'label'=>array('1','2'),'caption'=>'caption'), array('imgs'=>array(0,0),'label'=>array('1','2'),'caption'=>'caption'));
		$meta = array('slider_style'=>'style_1', 'nav_display'=>'style_1', 'prev_next_display'=>'style_1');
		echo '<div id="s201_default_template" style="display:none;">';
		include(dirname(__FILE__) . '/templates/default.php');
		echo '</div>';
		echo '<script type="text/javascript">';
		include(dirname(__FILE__) . '/templates/css/default.php');
		echo 'var s201_css_ary = {};s201_css_ary.default = '.json_encode($css_ary).';';
		foreach($GLOBALS['s201_styles'] as $tmpl_style){
			$css_ary = array();
			include(dirname(__FILE__) . '/templates/css/'.$tmpl_style.'.php');
			echo 's201_css_ary.'.$tmpl_style.' = '.json_encode($css_ary).';';
		}
		echo '</script>';
	}
	
	add_action('wp_ajax_s201_delete_slides', function(){
		check_ajax_referer('s201_ajax_nonce', 'security');
		global $wpdb, $tbl_s201_bai;
		if(is_numeric($_POST['id'])){
			$wpdb->delete($tbl_s201_bai, array('id' => sanitize_key($_POST['id'])));
		}
		exit;
	});
	
	add_action('wp_ajax_s201_create_shortcode', function(){
		check_ajax_referer('s201_ajax_nonce', 'security');
		$row_id = s201_save_post($_POST['post_id']);
		if($row_id) echo $row_id;
		exit;
	});
	
?>