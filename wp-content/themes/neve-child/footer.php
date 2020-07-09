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

<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-164348817-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-164348817-1');
	</script>
	
	<!-- Global site tag (gtag.js) - Google Ads: 624384386 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-624384386"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'AW-624384386');
	</script>

	<!-- Event snippet for Submit contact form conversion page -->
	<?php if ( is_page(2412) ) : ?>
		<script>
		  gtag('event', 'conversion', {'send_to': 'AW-624384386/meDwCIWLpNUBEIKz3akC'});
		</script>
	<?php endif; ?>


	<!-- Note: plugin scripts must be included after the tracking snippet. -->
	<script src="https://ipmeta.io/plugin.js"></script>

	<script>
	   provideGtagPlugin({
	      apiKey: '229ca95fb726ef1a7a909b4bfd82ff4b8b177a8029bc66c366e912a91963807c',
	      serviceProvider: 'dimension1',
	      networkDomain: 'dimension2',
	      networkType: 'dimension3',
	   });
	</script>
	<script type='text/javascript'>
		window.smartlook||(function(d) {
		var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
		var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
		c.charset='utf-8';c.src='https://rec.smartlook.com/recorder.js';h.appendChild(c);
		})(document);
		smartlook('init', '2f813145c4874bb0839a8b0a1b580c785c1207ac');
	</script>
<script type="text/javascript"> _linkedin_partner_id = "1941042"; window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || []; window._linkedin_data_partner_ids.push(_linkedin_partner_id); </script><script type="text/javascript"> (function(){var s = document.getElementsByTagName("script")[0]; var b = document.createElement("script"); b.type = "text/javascript";b.async = true; b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js"; s.parentNode.insertBefore(b, s);})(); </script> <noscript> <img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=1941042&fmt=gif" /> </noscript>
</body>