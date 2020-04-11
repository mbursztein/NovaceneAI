<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "wrapper" div and all content after.
 *
 * @package Neve
 * @since   1.0.0
 */

do_action( 'neve_before_primary_end' );
?>
</main><!--/.neve-main-->

<?php do_action( 'neve_after_primary' ); ?>

<?php
if ( apply_filters( 'neve_filter_toggle_content_parts', true, 'footer' ) === true ) {
	neve_before_footer_trigger();
	do_action( 'neve_do_footer' );
	neve_after_footer_trigger();
}
?>

</div><!--/.wrapper-->
<?php wp_footer(); ?>


<div id="footer">
	© <?php echo date('Y') ?> Novacene AI Corp. All rights reserved.<br />
	<a href="/privacy-policy">Privacy Policy</a>
</div>


</body>