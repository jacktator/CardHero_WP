<?php
/**
 * The template to display blog archive
 *
 * @package WordPress
 * @subpackage CREDITCARD
 * @since CREDITCARD 1.0
 */

/*
Template Name: Full Width
 */

/**
 * Make page with this template and put it into menu
 * to display posts as blog archive
 * You can setup output parameters (blog style, posts per page, parent category, etc.)
 * in the Theme Options section (under the page content)
 * You can build this page in the Visual Composer to make custom page layout:
 * just insert %%CONTENT%% in the desired place of content
 */

// Get template page's content
$creditcard_content = '';
$creditcard_blog_archive_mask = '%%CONTENT%%';
$creditcard_blog_archive_subst = sprintf('<div class="blog_archive">%s</div>', $creditcard_blog_archive_mask);
if (have_posts()) {
	the_post();
	if (($creditcard_content = apply_filters('the_content', get_the_content())) != '') {
		if (($creditcard_pos = strpos($creditcard_content, $creditcard_blog_archive_mask)) !== false) {
			$creditcard_content = preg_replace('/(\<p\>\s*)?' . $creditcard_blog_archive_mask . '(\s*\<\/p\>)/i', $creditcard_blog_archive_subst, $creditcard_content);
		} else {
			$creditcard_content .= $creditcard_blog_archive_subst;
		}

		$creditcard_content = explode($creditcard_blog_archive_mask, $creditcard_content);
		// Add VC custom styles to the inline CSS
		$vc_custom_css = get_post_meta(get_the_ID(), '_wpb_shortcodes_custom_css', true);
		if (!empty($vc_custom_css)) {
			creditcard_add_inline_css(strip_tags($vc_custom_css));
		}

	}
}

// Prepare args for a new query
$creditcard_args = array(
	'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
);
$creditcard_args = creditcard_query_add_posts_and_cats($creditcard_args, '', creditcard_get_theme_option('post_type'), creditcard_get_theme_option('parent_cat'));
$creditcard_page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
if ($creditcard_page_number > 1) {
	$creditcard_args['paged'] = $creditcard_page_number;
	$creditcard_args['ignore_sticky_posts'] = true;
}
$creditcard_ppp = creditcard_get_theme_option('posts_per_page');
if ((int) $creditcard_ppp != 0) {
	$creditcard_args['posts_per_page'] = (int) $creditcard_ppp;
}

// Make a new query
query_posts($creditcard_args);
// Set a new query as main WP Query
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];

// Set query vars in the new query!
if (is_array($creditcard_content) && count($creditcard_content) == 2) {
	set_query_var('blog_archive_start', $creditcard_content[0]);
	set_query_var('blog_archive_end', $creditcard_content[1]);
}

get_template_part('index');
?>