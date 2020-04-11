<?php
defined('ABSPATH') || exit;

$wraper_after .= '<div class="ultp-pagination-wrap'.($attr["paginationAjax"] ? " ultp-pagination-ajax-action" : "").'" data-paged="1" data-blockid="'.$attr['blockId'].'" data-postid="'.$page_post_id.'" data-pages="'.$pageNum.'" data-blockname="ultimate-post_'.$block_name.'">';
    $wraper_after .= ultimate_post()->pagination($pageNum, $attr['paginationNav']);
$wraper_after .= '</div>';