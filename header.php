<?php
/**
 * The Header: Logo and main menu
 *
 * @package WordPress
 * @subpackage CREDITCARD
 * @since CREDITCARD 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js scheme_<?php
										 // Class scheme_xxx need in the <html> as context for the <body>!
										 echo esc_attr(creditcard_get_theme_option('color_scheme'));
										 ?>">
<head>
	<?php wp_head(); ?>
	<script>
		window['_fs_debug'] = false;
		window['_fs_host'] = 'fullstory.com';
		window['_fs_org'] = 'HCQ51';
		window['_fs_namespace'] = 'FS';
		(function(m,n,e,t,l,o,g,y){
		    if (e in m) {if(m.console && m.console.log) { m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].');} return;}
		    g=m[e]=function(a,b,s){g.q?g.q.push([a,b,s]):g._api(a,b,s);};g.q=[];
		    o=n.createElement(t);o.async=1;o.src='https://'+_fs_host+'/s/fs.js';
		    y=n.getElementsByTagName(t)[0];y.parentNode.insertBefore(o,y);
		    g.identify=function(i,v,s){g(l,{uid:i},s);if(v)g(l,v,s)};g.setUserVars=function(v,s){g(l,v,s)};g.event=function(i,v,s){g('event',{n:i,p:v},s)};
		    g.shutdown=function(){g("rec",!1)};g.restart=function(){g("rec",!0)};
		    g.consent=function(a){g("consent",!arguments.length||a)};
		    g.identifyAccount=function(i,v){o='account';v=v||{};v.acctId=i;g(o,v)};
		    g.clearUserCookie=function(){};
		})(window,document,window['_fs_namespace'],'script','user');
		<?php if (is_user_logged_in()) { ?>
		var wpEmail = "<?php $current_user = wp_get_current_user();
		echo $current_user->user_email; ?>";
		var count = <?php echo wc_get_customer_order_count(get_current_user_id()); ?>;
		var spend = "<?php echo wc_get_customer_total_spent(get_current_user_id()); ?>";
		FS.identify(wpEmail, { "displayName": wpEmail,
		"email": wpEmail,
		"orders_int": count,
		"totalSpent_real": spend });
		<?php } ?>
	</script>
</head>

<body <?php	body_class(); ?>>

	<?php do_action( 'creditcard_action_before' ); ?>

	<div class="body_wrap">

		<div class="page_wrap">

			<?php
			// Desktop header
			$creditcard_header_style = creditcard_get_theme_option("header_style");
			if (strpos($creditcard_header_style, 'header-custom-')===0) $creditcard_header_style = 'header-custom';
			get_template_part( "templates/{$creditcard_header_style}");

			// Side menu
			if (in_array(creditcard_get_theme_option('menu_style'), array('left', 'right'))) {
				get_template_part( 'templates/header-navi-side' );
			}

			// Mobile header
			get_template_part( 'templates/header-mobile');
			?>

			<div class="page_content_wrap scheme_<?php echo esc_attr(creditcard_get_theme_option('color_scheme')); ?>">

				<?php if (creditcard_get_theme_option('body_style') != 'fullscreen') { ?>
				<div class="content_wrap">
				<?php } ?>

					<?php
					// Widgets area above page content
					creditcard_create_widgets_area('widgets_above_page');
					?>

					<div class="content">
						<?php
						// Widgets area inside page content
						creditcard_create_widgets_area('widgets_above_content');
						?>
