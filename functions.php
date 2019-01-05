<?php

add_action('wp_enqueue_scripts', 'ch_theme_enqueue_styles');
function ch_theme_enqueue_styles() {

	$parent_style = 'creditcard-parent-style';

	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');

	wp_enqueue_style('cardhero',
		get_stylesheet_directory_uri() . '/style.css',
		array($parent_style)
	);
}

/*
    Fixes WP5.0 & WPBakery Page Builder loses Role Settings upon 
    
    You can set the post type for which the editor should be
    available by adding the following code to functions.php:
    
    Author: Jacktator
    Plugin: WPBakery Page Builder 5.6
    Reference: https://stackoverflow.com/questions/49316654/wp-bakery-page-builder-loses-settings-in-role-manager?rq=1
*/
add_action( 'vc_before_init', 'Use_wpBakery' );
function Use_wpBakery() {
  $vc_list = array('page','creditcard');
  vc_set_default_editor_post_types($vc_list);
  vc_editor_set_post_types($vc_list);
}

/*
    Enable 'show_in_rest' option in ACF & ACF Pro
    
    Author: Jacktator
    Plugin: ACF Pro 5.7.9
    Reference: https://github.com/airesvsg/acf-to-rest-api
*/
// Enable the option show in rest
add_filter( 'acf/rest_api/field_settings/show_in_rest', '__return_true' );

// Enable the option edit in rest
add_filter( 'acf/rest_api/field_settings/edit_in_rest', '__return_true' );

?>
