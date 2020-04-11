<?php
defined('ABSPATH') || exit;

$post_loop .= '<div class="ultp-block-excerpt">'.ultimate_post()->excerpt($post_id, $attr['excerptLimit']).'</div>';