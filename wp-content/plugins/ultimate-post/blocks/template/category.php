<?php
defined('ABSPATH') || exit;

$category       = '';
if ($attr['catShow']) {
    $category .= '<div class="ultp-category-grid ultp-category-'.$attr['catStyle'].' ultp-category-'.$attr['catPosition'].'">';
        $category .= '<div class="ultp-category-in">';
            $cat = get_the_terms($post_id, 'category');
            if (!empty($cat)) {
                foreach ($cat as $val) {
                    $category .= '<a href="'.get_term_link($val->term_id).'">'.$val->name.'</a>';
                }
            }
        $category .= '</div>';
    $category .= '</div>';
}