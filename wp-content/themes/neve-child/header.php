<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the page header div.
 *
 * @package Neve
 * @since   1.0.0
 */

$header_classes = apply_filters( 'nv_header_classes', 'header' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-164348817-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-164348817-1');
	</script>
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
	<script>
	  (function(){

	    window.ldfdr = window.ldfdr || {};
	    (function(d, s, ss, fs){
	      fs = d.getElementsByTagName(s)[0];

	      function ce(src){
	        var cs  = d.createElement(s);
	        cs.src = src;
	        setTimeout(function(){fs.parentNode.insertBefore(cs,fs)}, 1);
	      }

	      ce(ss);
	    })(document, 'script', 'https://sc.lfeeder.com/lftracker_v1_bElvO73Ax96aZMqj.js');
	  })();
	</script>
	<script type='text/javascript'>
		window.smartlook||(function(d) {
		var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
		var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
		c.charset='utf-8';c.src='https://rec.smartlook.com/recorder.js';h.appendChild(c);
		})(document);
		smartlook('init', '2f813145c4874bb0839a8b0a1b580c785c1207ac');
	</script>


</head>

<body  <?php body_class(); ?> <?php neve_body_attrs(); ?> >
<?php wp_body_open(); ?>
<div class="wrapper">
	<header class="<?php echo esc_attr( $header_classes ); ?>" role="banner">
		<a class="neve-skip-link show-on-focus" href="#content" tabindex="0">
			<?php echo __( 'Skip to content', 'neve' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<?php
		neve_before_header_trigger();
		if ( apply_filters( 'neve_filter_toggle_content_parts', true, 'header' ) === true ) {
			do_action( 'neve_do_header' );
		}
		neve_after_header_trigger();
		?>
	</header>
	<?php do_action( 'neve_before_primary' ); ?>

	<main id="content" class="neve-main" role="main">

<?php
do_action( 'neve_after_primary_start' );

