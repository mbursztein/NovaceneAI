<?php
namespace ULTP;

defined('ABSPATH') || exit;

class Functions{

    // Init data
    public function init_set_data(){
        $option_data = array(
            'css_save_as' => 'wp_head',
            'preloader_style' => 'style1',
            'preloader_color' => '#1740f5',
            'container_width' => '1140',
            'hide_import_btn' => ''
        );
        update_option('ultp_options', $option_data);
        return $option_data;
    }

    // Excerpt
    public function excerpt( $post_id, $limit = 55 ) {
        return apply_filters( 'the_excerpt', wp_trim_words( get_the_content( $post_id ) , $limit ) );
    }

    // Query Builder
    public function get_query($attr) {
        $query_args = array(
            'posts_per_page'    => isset($attr['queryNumber']) ? $attr['queryNumber'] : 3,
            'post_type'         => isset($attr['queryType']) ? $attr['queryType'] : 'post',
            'orderby'           => isset($attr['queryOrderBy']) ? $attr['queryOrderBy'] : 'date',
            'order'             => isset($attr['queryOrder']) ? $attr['queryOrder'] : 'desc',
            'paged'             => isset($attr['paged']) ? $attr['paged'] : 1,
            'post_status'       => 'publish'
        );

        if(isset($attr['queryOrderBy']) && isset($attr['metaKey'])){
            if($attr['queryOrderBy'] == 'meta_value_num') {
                $query_args['meta_key'] = $attr['metaKey'];
            }
        }

        if(isset($attr['queryOffset']) && $attr['queryOffset'] && !($query_args['paged'] > 1) ){
            $query_args['offset'] = isset($attr['queryOffset']) ? $attr['queryOffset'] : 0;
        }

        if(isset($attr['queryInclude']) && $attr['queryInclude']){
            $query_args['post__in'] = explode(',', $attr['queryInclude']);
        }

        if(isset($attr['queryExclude']) && $attr['queryExclude']){
            $query_args['post__not_in'] = explode(',', $attr['queryExclude']);
        }

        if(isset($attr['queryTax'])){
            if(isset($attr['queryCat'])){
                if($attr['queryTax'] == 'category' && !empty($attr['queryCat'])){
                    $var = array('relation'=>'OR');
                    foreach (json_decode($attr['queryCat']) as $val) {
                        $var[] = array('taxonomy'=>'category', 'field' => 'slug', 'terms' => $val );
                    }
                    $query_args['tax_query'] = $var;
                }
            }
            if(isset($attr['queryTag'])){
                if($attr['queryTax'] == 'tag' && !empty($attr['queryTag'])){
                    $var = array('relation'=>'OR');
                    foreach (json_decode($attr['queryTag']) as $val) {
                        $var[] = array('taxonomy'=>'post_tag', 'field' => 'slug', 'terms' => $val );
                    }
                    $query_args['tax_query'] = $var;
                }
            }
        }

        $query_args['wpnonce'] = wp_create_nonce( 'ultp-nonce' );
        
        return $query_args;
    }


    public function get_page_number($attr, $post_number) {
        if($post_number > 0){
            $post_per_page = isset($attr['queryNumber']) ? $attr['queryNumber'] : 3;
            $pages = ceil($post_number/$post_per_page);
            return $pages ? $pages : 1;
        }else{
            return 1;
        }
    }

    public function get_image_size() {
        $sizes = get_intermediate_image_sizes();
        $filter = array('full' => 'Full');
        foreach ($sizes as $value) {
            $filter[$value] = ucwords(str_replace(array('_', '-'), array(' ', ' '), $value));
        }
        return $filter;
    }


    public function get_post_type() {
        $post_type = get_post_types( '', 'names' );
        return array_diff($post_type, array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block' ));
    }


    // Pagination
    public function pagination($pages = '', $paginationNav, $range = 1) {
        $html = '';
        $showitems = 3;
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        if($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if(!$pages) {
                $pages = 1;
            }
        }
        $data = ($paged>=3?[($paged-1),$paged,$paged+1]:[1,2,3]);

 
        if(1 != $pages) {
            $html .= '<ul class="ultp-pagination">';            
                $display_none = 'style="display:none"';
                if($pages > 4) {
                    $html .= '<li class="ultp-prev-page-numbers" '.($paged==1?$display_none:"").'><a href="'.get_pagenum_link($paged-1).'">'.ultimate_post()->svg_icon('leftAngle2').' '.($paginationNav == 'textArrow'?__("Previous", "ultimate-post"):"").'</a></li>';
                }
                if($pages > 4){
                    $html .= '<li class="ultp-first-pages" '.($paged<2?$display_none:"").' data-current="1"><a href="'.get_pagenum_link(1).'">1</a></li>';
                }
                if($pages > 4){
                    $html .= '<li class="ultp-first-dot" '.($paged<2? $display_none : "").'><a href="#">...</a></li>';
                }
                foreach ($data as $i) {
                    if($pages >= $i){
                        $html .= ($paged == $i) ? '<li class="ultp-center-item pagination-active" data-current="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>':'<li class="ultp-center-item" data-current="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
                    }
                }
                if($pages > 4){
                    $html .= '<li class="ultp-last-dot" '.($pages<=$paged+1?$display_none:"").'><a href="#">...</a></li>';
                }
                if($pages > 4){
                    $html .= '<li class="ultp-last-pages" '.($pages<=$paged+1?$display_none:"").' data-current="'.$pages.'"><a href="'.get_pagenum_link($pages).'">'.$pages.'</a></li>';
                }
                if ($paged != $pages) {
                    $html .= '<li class="ultp-next-page-numbers"><a href="'.get_pagenum_link($paged + 1).'">'.($paginationNav == 'textArrow' ? __("Next", "ultimate-post") : "").ultimate_post()->svg_icon('rightAngle2').'</a></li>';
                }
            $html .= '</ul>';
        }
        return $html;
    }

    public function excerpt_word($charlength = 200) {
        $html = '';
        $charlength++;
        $excerpt = get_the_excerpt();
        if ( mb_strlen( $excerpt ) > $charlength ) {
            $subex = mb_substr( $excerpt, 0, $charlength - 5 );
            $exwords = explode( ' ', $subex );
            $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
            if ( $excut < 0 ) {
                $html = mb_substr( $subex, 0, $excut );
            } else {
                $html = $subex;
            }
            $html .= '...';
        } else {
            $html = $excerpt;
        }
        return $html;
    }


    public function taxonomy( $prams = 'category' ) {
        $data = array();
        $terms = get_terms( $prams, array(
            'hide_empty' => true,
        ));
        if( !empty($terms) ){
            foreach ($terms as $val) {
                $data[$val->slug] = $val->name;
            }
        }
        return $data;
    }


    public function next_prev() {
        $html = '';
        $html .= '<ul>';
            $html .= '<li>';
                $html .= '<a class="ultp-prev-action ultp-disable" href="#">';
                    $html .= ultimate_post()->svg_icon('leftAngle2').'<span class="screen-reader-text">'.__("Previous", "ultimate-post").'</span>';
                $html .= '</a>';
            $html .= '</li>';
            $html .= '<li>';
                $html .= '<a class="ultp-next-action">';
                    $html .= ultimate_post()->svg_icon('rightAngle2').'<span class="screen-reader-text">'.__("Next", "ultimate-post").'</span>';
                $html .= '</a>';
            $html .= '</li>';
        $html .= '</ul>';
        return $html;
    }

    public function loading(){
        $html = '';
        $style = 'style1';
        $option_data = get_option('ultp_options');
		if( isset($option_data['preloader_style']) ){
			$style = $option_data['preloader_style'];
        }
        if( $style == 'style2' ){
            $html .= '<div class="ultp-loading-spinner" style="width:100%;height:100%"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';//ultp-block-items-wrap
        } else {
            $html .= '<div class="ultp-loading-blocks" style="width:100%;height:100%;"><div style="left: 0;top: 0;animation-delay:0s;"></div><div style="left: 21px;top: 0;animation-delay:0.125s;"></div><div style="left: 42px;top: 0;animation-delay:0.25s;"></div><div style="left: 0;top: 21px;animation-delay:0.875s;"></div><div style="left: 42px;top: 21px;animation-delay:0.375s;"></div><div style="left: 0;top: 42px;animation-delay:0.75s;"></div><div style="left: 42px;top: 42px;animation-delay:0.625s;"></div><div style="left: 21px;top: 42px;animation-delay:0.5s;"></div></div>';
        }
        return '<div class="ultp-loading">'.$html.'</div>';
    }

    public function filter($filterText = '', $filterType = '', $filterCat = '[]', $filterTag = '[]'){
        $html = '';
        $html .= '<ul class="ultp-flex-menu">';
            if ($filterType == 'category') {
                $cat = $this->taxonomy('category');
                if($filterText){
                    $html .= '<li class="filter-item"><a data-taxonomy="" href="#">'.$filterText.'</a></li>';
                }
                foreach (json_decode($filterCat) as $val) {
                    $html .= '<li class="filter-item"><a data-taxonomy="'.$val.'" href="#">'.(isset($cat[$val]) ? $cat[$val] : $val).'</a></li>';
                }
            } else {
                $tag = $this->taxonomy('post_tag');
                if($filterText){
                    $html .= '<li class="filter-item"><a data-taxonomy="" href="#">'.$filterText.'</a></li>';
                }
                foreach (json_decode($filterTag) as $val) {
                    $html .= '<li class="filter-item"><a data-taxonomy="'.$val.'" href="#">'.(isset($tag[$val]) ? $tag[$val] : $val).'</a></li>';
                }
            }
        $html .= '</ul>';
        return $html;
    }

    public function svg_icon($icons = ''){

        $icon_lists = array(
            'eye' 			=> file_get_contents(ULTP_PATH.'assets/img/svg/eye.svg'),
            'user' 			=> file_get_contents(ULTP_PATH.'assets/img/svg/user.svg'),
            'calendar'      => file_get_contents(ULTP_PATH.'assets/img/svg/calendar.svg'),
            'comment'       => file_get_contents(ULTP_PATH.'assets/img/svg/comment.svg'),
            'book'  		=> file_get_contents(ULTP_PATH.'assets/img/svg/book.svg'),
            'tag'           => file_get_contents(ULTP_PATH.'assets/img/svg/tag.svg'),
            'clock'         => file_get_contents(ULTP_PATH.'assets/img/svg/clock.svg'),
            'leftAngle'     => file_get_contents(ULTP_PATH.'assets/img/svg/leftAngle.svg'),
            'rightAngle'    => file_get_contents(ULTP_PATH.'assets/img/svg/rightAngle.svg'),
            'leftAngle2'    => file_get_contents(ULTP_PATH.'assets/img/svg/leftAngle2.svg'),
            'rightAngle2'   => file_get_contents(ULTP_PATH.'assets/img/svg/rightAngle2.svg'),
            'leftArrowLg'   => file_get_contents(ULTP_PATH.'assets/img/svg/leftArrowLg.svg'),
            'refresh'       => file_get_contents(ULTP_PATH.'assets/img/svg/refresh.svg'),
            'rightArrowLg'  => file_get_contents(ULTP_PATH.'assets/img/svg/rightArrowLg.svg'),
        ); 
        if($icons){
            return $icon_lists[ $icons ];
        }
    }
    
}
