<?php

include_once get_template_directory() . '/theme-includes.php';

add_action('wp_enqueue_scripts', 'sk8tech_theme_enqueue_styles');

function sk8tech_theme_enqueue_styles() {

	$parent_style = 'creditcard-parent-style';

	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');

	wp_enqueue_style('cardhero',
		get_stylesheet_directory_uri() . '/style.css',
		array($parent_style)
	);
}

/*
Add FullStory Integration
Author: Jack
 */
add_action('wp_head', 'add_fullstory');
function add_fullstory() {
	?>
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
			<?php if (is_user_logged_in()) {
		?>
			var wpEmail = "<?php $current_user = wp_get_current_user();
		echo $current_user->user_email;?>";
			var count = <?php echo wc_get_customer_order_count(get_current_user_id()); ?>;
			var spend = "<?php echo wc_get_customer_total_spent(get_current_user_id()); ?>";
			FS.identify(wpEmail, { "displayName": wpEmail,
			"email": wpEmail,
			"orders_int": count,
			"totalSpent_real": spend });
			<?php }?>
		</script>
	<?php
};

?>