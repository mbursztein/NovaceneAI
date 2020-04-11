<?php
defined('ABSPATH') || exit;

get_header();
$width = get_option( 'ultp_options' );
$width = isset($width['container_width']) ? $width['container_width'] : '1140';
?>
<div class="ultp-template-container" style="margin:0 auto;max-width:<?php echo $width;?>px; padding: 0 15px">
	<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
	?>
<div>
<?php
get_footer();