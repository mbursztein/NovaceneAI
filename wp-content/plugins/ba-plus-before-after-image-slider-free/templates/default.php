
<?php 
	$slider_style = in_array($meta['slider_style'], $GLOBALS['s201_styles'])?$meta['slider_style']:$GLOBALS['s201_styles'][0];
	if(!empty($meta)){
		include('css/default.php');
		include('css/'.$slider_style.'.php');
		$css_style_text = '';
		foreach($meta as $css_k=>$css_v){
			if(!isset($css_ary[$css_k])) continue;
			if($css_ary[$css_k]['s0'][0] != $css_v){
				foreach($css_ary[$css_k] as $css_custom){
					if(strpos($css_k, 'opacity')!==false) $css_v = $css_v/100;
					elseif(is_numeric($css_v)) $css_v = $css_v.'px';
					elseif(strpos($css_k, '_bg_color')!==false) $css_v = s201_hex_to_rgb($css_v);
					if($css_custom[0] != $css_v) $css_style_text .= str_replace($css_custom[0], $css_v, $css_custom[1]);
				}
			}
		}
		if(isset($element_id)) $css_style_text = str_replace('#s201_slides', '#'.$element_id, $css_style_text);
		if($css_style_text) echo '<style type="text/css">'.$css_style_text.'</style>';
	}
	if(isset($meta['side_by_side'])){
		foreach($slides as $k=>$slider){
		?>
		<div class="s201_holder<?php echo(!$k?' s201_slide_active':''); ?> s201_side" data-part="holder">
			<?php 
				$html = '';
				foreach($slider['imgs'] as $k2=>$media_id){
					$image_src = wp_get_attachment_image_src($media_id, 'full');
					$html .= '<div class="s201_side_img"><img src="'.$image_src[0].'" srcset="'.wp_get_attachment_image_srcset($media_id).'" sizes="(max-width: '.$image_src[1].'px) 100vw, '.$image_src[1].'px" alt="'.$slider['label'][$k2].'"/>'.($slider['label'][$k2]?'<div class="s201_label_text'.(isset($meta['always_show_label'])?' s201_always_show':'').'" data-part="label">'.$slider['label'][$k2].'</div>':'').'</div>';
				}
				echo $html;
			?>
			<?php if($slider['caption']) echo '<div class="s201_caption_text"><span>'.$slider['caption'].'</span></div>'; ?>
		</div>
		<?php 
		}
	}
	else{
		foreach($slides as $k=>$slider){
		?>
		<div class="s201_holder<?php echo(!$k?' s201_slide_active':''); ?>" data-part="holder">
			<?php 
				$html = '';
				$n = count($slider['imgs']);
				$slider_handle_space = 100/$n;
				$slider_pos = 0;
				$slider_handle_pos = 0;
				$n-=1;
				foreach($slider['imgs'] as $k2=>$media_id){
					$image_src = wp_get_attachment_image_src($media_id, 'full');
					$img_url = $image_src[0];
					$srcset = wp_get_attachment_image_srcset($media_id);
					$sizes = '(max-width: '.$image_src[1].'px) 100vw, '.$image_src[1].'px';
					if($k2==$n){
						$html = '<img src="'.$img_url.'" srcset="'.$srcset.'" sizes="'.$sizes.'" alt="'.$slider['label'][$k2].'" class="s201_img_holder s201_noselect"/><div class="s201_item_img s201_noselect"><div class="s201_overlay_img" style="background-image:url(\''.$img_url.'\')"><img src="'.$img_url.'" srcset="'.$srcset.'" sizes="'.$sizes.'" alt="'.$slider['label'][$k2].'" class="s201_noselect"/></div>'.($slider['label'][$k2]?'<div class="s201_label_text s201_noselect'.(isset($meta['always_show_label'])?' s201_always_show':'').'" data-part="label">'.$slider['label'][$k2].'</div>':'').'</div>'.$html;
					}
					else{
						if(isset($meta['custom_pos'])){
							if($slider['slider_pos'][$k2] && $slider['slider_pos'][$k2] > $slider_pos){
								$slider_pos = $slider['slider_pos'][$k2];
							}
						}
						else $slider_pos += $slider_handle_space;
						$slider_handle_pos += $slider_handle_space;
						if(isset($meta['vertical']) && $meta['vertical']){
							$slider_pos_style = 'height:'.$slider_pos;
							$slider_handle_pos_style = 'left:'.(100-$slider_handle_pos);
						}
						else{
							$slider_pos_style = 'width:'.$slider_pos;
							$slider_handle_pos_style = 'top:'.(100-$slider_handle_pos);
						}
						$html = '<div class="s201_item_img s201_noselect" style="'.$slider_pos_style.'%;"><div class="s201_overlay_img" style="background-image:url(\''.$img_url.'\')"><img src="'.$img_url.'" srcset="'.$srcset.'" sizes="'.$sizes.'" alt="'.$slider['label'][$k2].'" class="s201_noselect"/></div>'.($slider['label'][$k2]?'<div class="s201_label_text s201_noselect'.(isset($meta['always_show_label'])?' s201_always_show':'').'" data-part="label">'.$slider['label'][$k2].'</div>':'').'
							<div class="s201_slider s201_'.$meta['slider_style'].'" style="'.$slider_handle_pos_style.'%;"><div class="s201_top_line"></div><div class="s201_handle"><div class="s201_left_arrow"></div><div class="s201_right_arrow"></div></div><div class="s201_bottom_line"></div></div>
						</div>'.$html;
					}
				}
				echo $html;
			?>
			<?php if($slider['caption']) echo '<div class="s201_caption_text"><span>'.$slider['caption'].'</span></div>'; ?>
		</div>
		<?php 
		}
	}
	if(count($slides) > 1){
	if($meta['nav_display'] != 'hidden'){ ?>
	<div class="s201_pager s201_<?php echo $meta['nav_display']; ?>">
		<?php 
			if($meta['nav_display']=='thumbnails'){
				$thumbs = array();
				foreach($slides as $k=>$slider){
					if(isset($slider['imgs'][0])) $img = '<img src="'.wp_get_attachment_thumb_url($slider['imgs'][0]).'" alt="[thumb]"/>';
					else $img = '';
					if(!$k) echo '<span class="s201_pager_item s201_pager_active">'.$img.'</span>';
					else echo '<span class="s201_pager_item">'.$img.'</span>';
				}
			}
			else{
				foreach($slides as $k=>$slider){
					if(!$k) echo '<span class="s201_pager_item s201_pager_active"></span>';
					else echo '<span class="s201_pager_item"></span>';
				}
			}
		?>
	</div>
	<?php }
	if($meta['prev_next_display'] != 'hidden'){
	?>
	<div class="s201_prev_next s201_<?php echo $meta['prev_next_display']; ?>">
		<div class="s201_prev s201_transition"></div>
		<div class="s201_next s201_transition"></div>
	</div>
	<?php 
	}
	else echo '<div class="s201_next"></div>';
	}
?>