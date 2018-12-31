<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentynineteen' ); ?></a>

		<header id="masthead" class="<?php echo is_singular() && twentynineteen_can_show_post_thumbnail() ? 'site-header featured-image' : 'site-header'; ?>">

			<div class="site-branding-container">
				<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
			</div><!-- .layout-wrap -->

			<?php if ( is_singular() && twentynineteen_can_show_post_thumbnail() ) : ?>
				<div class="site-featured-image">
					<?php
						twentynineteen_post_thumbnail();
						the_post();
						$discussion = ! is_page() && twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null;

						$classes = 'entry-header';
						if ( ! empty( $discussion ) && absint( $discussion->responses ) > 0 ) {
							$classes = 'entry-header has-discussion';
						}
					?>
					<div class="<?php echo $classes; ?>">
						<?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
					</div><!-- .entry-header -->
					<?php rewind_posts(); ?>
				</div>
			<?php endif; ?>
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
		</header><!-- #masthead -->

	<div id="content" class="site-content">
