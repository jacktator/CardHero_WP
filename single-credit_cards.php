<?php
/**
 * Template Name: Credit Card
 * Template Post Type: credit_cards
 *
 * The template to display Custom Post Type 'Credit Card' page
 *
 * @package WordPress
 * @subpackage CREDITCARD
 * @since CREDITCARD 1.0
 */

// get_header();

while (have_posts()) {
	the_post();

	get_template_part('content', 'creditcard');

	// If comments are open or we have at least one comment, load up the comment template.
	if (!is_front_page() && (comments_open() || get_comments_number())) {
		comments_template();
	}
}

get_footer();
?>