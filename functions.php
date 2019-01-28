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
add_action('vc_before_init', 'Use_wpBakery');
function Use_wpBakery() {
	$vc_list = array('page', 'credit_cards', 'reward_programs', 'companies', 'cpt_services');
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
add_filter('acf/rest_api/field_settings/show_in_rest', '__return_true');

// Enable the option edit in rest
add_filter('acf/rest_api/field_settings/edit_in_rest', '__return_true');

/*
Add shortcode support for ACF Table Field

Author: Jacktator
Plugin: Advanced Custom Field Table Field 1.2.6
Reference: https://wordpress.org/plugins/advanced-custom-fields-table-field/
 */
// function shortcode_acf_tablefield($atts) {

// 	$a = shortcode_atts(array(
// 		'field-name' => false,
// 		'post-id' => false,
// 	), $atts);

// 	$table = get_field($a['field-name'], $a['post-id']);

// 	$return = '';

// 	if ($table) {

// 		$return .= '<table style="width: 100%;">';

// 		if ($table['header']) {

// 			$return .= '<thead>';

// 			$return .= '<tr>';

// 			foreach ($table['header'] as $th) {

// 				$return .= '<th>';
// 				$return .= $th['c'];
// 				$return .= '</th>';
// 			}

// 			$return .= '</tr>';

// 			$return .= '</thead>';
// 		}

// 		$return .= '<tbody>';

// 		foreach ($table['body'] as $tr) {

// 			$return .= '<tr>';

// 			foreach ($tr as $td) {

// 				$return .= '<td>';
// 				$return .= $td['c'];
// 				$return .= '</td>';
// 			}

// 			$return .= '</tr>';
// 		}

// 		$return .= '</tbody>';

// 		$return .= '</table>';
// 	}

// 	return $return;
// }
// add_shortcode('table', 'shortcode_acf_tablefield');

/*
Create shortcode for displaying Earn Table using CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage:  [ch_credit_card_rewards_programs_earn_table rewards_program="24"] // Generate the Reward Table, Based on Earning 2 Credit Card Points per Dollar, and rewards_program ID is 24
 */
function ch_generate_credit_card_rewards_programs_earn_table($atts) {
	// TODO
}
add_shortcode('ch_credit_card_rewards_programs_earn_table', 'ch_generate_credit_card_rewards_programs_earn_table');

/*
Create Tabs

Author: Jacktator
Plugin: Visual Composer Extensions All In One 3.4.9.3
ShortCode: [cq_vc_tab_item]
Usage: [ch_credit_card_rewards_programs_earn_tabs] // Renders Rewards Programs in Tabs, Based on Earning 2 Credit Card Points per Dollar
 */
function ch_generate_credit_card_rewards_programs_earn_tabs($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'rewards_program',
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// extract attributs

	// Construct Tabs
	$rewards_programs_tabs = "";

	// Construct Tabs
	$rewards_programs_tabs .= '[cq_vc_tabs rotatetabs="0"]';

	$rewards_programs = get_field('rewards_programs'); // Repeater

	if (have_rows('rewards_programs')) {

		while (have_rows('rewards_programs')) {

			the_row();

			// Feth Rewards Program Data
			$rewards_program = get_sub_field('rewards_program');
			$rewards_program_unit = get_field('unit', $rewards_program->ID);

			// Construct Single Tab Item
			$rewards_programs_tabs .= '[cq_vc_tab_item tabtitle="' . $rewards_program->post_title . '"]';

			// Find the Max Earn Rate
			if (have_rows('earn_rates')) {

				$rewards_programs_table = '';
				// Construct Table Head
				$table_head = '<table style="width: 100%;">
				                <thead>
				                <tr>
				                <th>Categories</th>
				                <th>Earn Rate</th>
				                </tr>
				                </thead>';
				$rewards_programs_table .= $table_head;

				// Construct Table Body
				$rewards_programs_table .= '<tbody>';

				while (have_rows('earn_rates')) {

					the_row();

					$earn_rate = get_sub_field('earn_rate');
					$categories = get_sub_field('categories');
					$notes = get_sub_field('notes');

					$rewards_programs_table .= '<tr>';
					$rewards_programs_table .= '<td>' . implode(", ", $categories) . '</td>';
					$rewards_programs_table .= '<td> Earn <strong>' . $earn_rate . ' ' . $rewards_program_unit . '</strong> Per Dollar. </td>';
					$rewards_programs_table .= '</tr>';
				}

				// Close Table Body
				$rewards_programs_table .= '</tbody>';

				// Close Table
				$rewards_programs_table .= '</table>';

				// $rewards_programs_table = '[ch_generate_credit_card_rewards_programs_earn_table rewards_program="' . $rewards_program->ID .'"]';

				// Return Table
				$rewards_programs_tabs .= $rewards_programs_table;

			}

			// Close Single Tab Item
			$rewards_programs_tabs .= '[/cq_vc_tab_item]';
		}
	}

	// Close Tabs
	$rewards_programs_tabs .= '[/cq_vc_tabs]';

	return do_shortcode($rewards_programs_tabs);
}
add_shortcode('ch_credit_card_rewards_programs_earn_tabs', 'ch_generate_credit_card_rewards_programs_earn_tabs');

/*
Create shortcode for displaying Maximum Earn Rate Table using CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage:  [ch_credit_card_rewards_programs_redemption_table earn_rate="2" rewards_program="24"] // Generate the Reward Table, Based on Earning 2 Credit Card Points per Dollar, and rewards_program ID is 24
 */
function ch_generate_credit_card_rewards_programs_redemption_table($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'earn_rate' => 1, // Default earn_rate to 1
		'rewards_program' => 0, // ID of Rewards_Program Object
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// extract attributs

	if (have_rows('redemption_parnters', $rewards_program)) {

		$table = '';
		// Construct Table Head
		$table_head = '<table style="width: 100%;">
		                <thead>
		                <tr>
		                <th>Reward Program</th>
		                <th>Maximum Earn Rate</th>
		                </tr>
		                </thead>';
		$table .= $table_head;

		// Construct Table Body
		$table .= '<tbody>';

		while (have_rows('redemption_parnters', $rewards_program)) {

			the_row();

			$partner_program = get_sub_field('partner_program');
			$partner_program_fields = get_field_objects($partner_program->ID);
			$partner_program_company = get_field('company', $partner_program->ID); // Deprecated, use get_field('provider'); instead.
			if (!$partner_program_company) {
				$partner_program_company = get_field('provider', $partner_program->ID)->post_title;
			}
			$partner_program_program = get_field('program', $partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
			if (!$partner_program_program) {
				$partner_program_program = $partner_program->post_title;
			}
			$partner_program_unit = get_field('unit', $partner_program->ID);
			$flexible_points_currency = get_field('flexible_points_currency', $partner_program->ID);
			$partner_program_points_value = get_field('points_value', $partner_program->ID);

			$redemption_rate = get_sub_field('redemption_rate');
			$notes = get_sub_field('notes');

			$table .= '<tr>';
			$table .= '<td>' . $partner_program_company . ' - ' . $partner_program_program . '</td>';
			if ($value === 0) {
				$table .= '<td>Not Available</td>';
			} else {
				$table .= '<td> $1 earns <strong>' . sigFig($redemption_rate * $earn_rate, 4) . ' ' . $partner_program_unit . '.</strong> <br/><small>' . $notes . '</small></td>';
			}
			$table .= '</tr>';

		}
		// Close Table Body
		$table .= '</tbody>';

		// Close Table
		$table .= '</table>';

		return do_shortcode($table);
	} else {
		return 'Reward Program has no Redemption Partner.';
	}
}
add_shortcode('ch_credit_card_rewards_programs_redemption_table', 'ch_generate_credit_card_rewards_programs_redemption_table');

/*
Create Tabs

Author: Jacktator
Plugin: Visual Composer Extensions All In One 3.4.9.3
ShortCode: [cq_vc_tab_item]
Usage: [ch_credit_card_rewards_programs_redemption_tabs] // Renders Rewards Programs in Tabs, Based on Earning 2 Credit Card Points per Dollar
 */
function ch_generate_credit_card_rewards_programs_redemption_tabs($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'earn_rate' => 1, // Default earn_rate to 1, Deprecated
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// Store Flexible Points for Second Tier Table
	$flexible_partner_programs = array();
	$excluding_partner_programs = array();

	$rewards_programs = get_field('rewards_programs'); // Repeater

	if (have_rows('rewards_programs')) {

		// Construct Tabs
		$rewards_programs_tabs = "";

		// Construct Tabs
		$rewards_programs_tabs .= '[cq_vc_tabs rotatetabs="0"]';

		while (have_rows('rewards_programs')) {

			the_row();

			// Feth Rewards Program Data
			$rewards_program = get_sub_field('rewards_program');
			$max_earn_rate = 0;

			// Find the Max Earn Rate
			if (have_rows('earn_rates')) {

				while (have_rows(earn_rates)) {

					the_row();

					$earn_rate = get_sub_field('earn_rate');

					if ($earn_rate > $max_earn_rate) {
						$max_earn_rate = $earn_rate;
					}

				}

			}

			// Construct Single Tab Item
			$rewards_programs_tabs .= '[cq_vc_tab_item tabtitle="' . $rewards_program->post_title . '"]';

			$rewards_programs_table = '[ch_credit_card_rewards_programs_redemption_table earn_rate="' . $max_earn_rate . '" rewards_program="' . $rewards_program->ID . '"]';

			// Return Table
			$rewards_programs_tabs .= $rewards_programs_table;

			// Close Single Tab Item
			$rewards_programs_tabs .= '[/cq_vc_tab_item]';

		}

		// Close Tabs
		$rewards_programs_tabs .= '[/cq_vc_tabs]';

		return do_shortcode($rewards_programs_tabs);
	} else {
		return "Error: Rewards Programs is empty.";
	}
}
add_shortcode('ch_credit_card_rewards_programs_redemption_tabs', 'ch_generate_credit_card_rewards_programs_redemption_tabs');

/*
Create shortcode for displaying Further Earn Rate Table using CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage:  [ch_credit_card_rewards_programs_more_redemption_table earn_rate="2" rewards_program="24"] // Generate the Reward Table with More redemption, Based on Earning 2 Credit Card Points per Dollar, and rewards_program ID is 24
 */
function ch_generate_credit_card_rewards_programs_more_redemption_table($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'earn_rate' => 1, // Default earn_rate to 1
		'rewards_program' => 0, // ID of Rewards_Program Object
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// extract attributs

	// Store Flexible Points for Second Tier Table
	$flexible_partner_programs = array();
	$excluding_partner_programs = array();

	if (have_rows('redemption_parnters', $rewards_program)) {

		$table = '';
		// Construct Table Head
		$table_head = '<table style="width: 100%;">';
		$table .= $table_head;

		// Add all available redemption partners as primary partners
		while (have_rows('redemption_parnters', $rewards_program)) {

			the_row();

			$partner_program = get_sub_field('partner_program');
			$partner_program_fields = get_field_objects($partner_program->ID);
			$partner_program_company = get_field('company', $partner_program->ID); // Deprecated, use get_field('provider'); instead.
			if (!$partner_program_company) {
				$partner_program_company = get_field('provider', $partner_program->ID)->post_title;
			}
			$partner_program_program = get_field('program', $partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
			if (!$partner_program_program) {
				$partner_program_program = $partner_program->post_title;
			}
			$partner_program_unit = get_field('unit', $partner_program->ID);
			$flexible_points_currency = get_field('flexible_points_currency', $partner_program->ID);
			$partner_program_points_value = get_field('points_value', $partner_program->ID);

			$redemption_rate = get_sub_field('redemption_rate');
			$notes = get_sub_field('notes');

			// Add program to $excluding_partner_programs to avoid duplication when handle second tier redemotion
			array_push($excluding_partner_programs, $partner_program_program);

			// Add Flexible Points Program to Array
			if (!in_array($partner_program, $excluding_partner_programs) && $flexible_points_currency) {

				array_push($flexible_partner_programs, $partner_program);
			}

		}

		// Construct Secondary Table Body
		foreach ($flexible_partner_programs as $flexible_partner_program) {

			$flexible_partner_program_company = get_field('company', $flexible_partner_program->ID); // Deprecated, use get_field('provider'); instead.
			if (!$flexible_partner_program_company) {
				$flexible_partner_program_company = get_field('provider', $flexible_partner_program->ID)->post_title;
			}
			$flexible_partner_program_program = get_field('program', $flexible_partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
			if (!$flexible_partner_program_program) {
				$flexible_partner_program_program = $flexible_partner_program->post_title;
			}

			// Construct Secondary Table Head
			$table .= '
			        <thead>
			        <tr>
			        <th> Reward Program via ' . $flexible_partner_program_company . ' - ' . $flexible_partner_program_program . ' </th>
	            	<th>Maximum Earn Rate</th>
			        </tr>
			        </thead>';

			// Construct Secondary Table Body
			$table .= '<tbody>';

			while (have_rows('redemption_parnters', $flexible_partner_program->ID)) {

				// Render Redemption

				the_row();

				$second_tier_partner_program = get_sub_field('partner_program');
				$second_tier_partner_program_fields = get_field_objects($second_tier_partner_program->ID);
				$second_tier_partner_program_company = get_field('company', $second_tier_partner_program->ID); // Deprecated, use get_field('provider'); instead.
				if (!$second_tier_partner_program_company) {
					$second_tier_partner_program_company = get_field('provider', $second_tier_partner_program->ID)->post_title;
				}
				$second_tier_partner_program_program = get_field('program', $second_tier_partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
				if (!$second_tier_partner_program_program) {
					$second_tier_partner_program_program = $second_tier_partner_program->post_title;
				}
				$second_tier_partner_program_unit = get_field('unit', $second_tier_partner_program->ID);
				$second_tier_flexible_points_currency = get_field('flexible_points_currency', $second_tier_partner_program->ID);
				$second_tier_partner_program_points_value = get_field('points_value', $second_tier_partner_program->ID);

				// Only Add new partner in second tier redemption
				// if (!in_array($second_tier_partner_program_program, $excluding_partner_programs)) {

				$second_tier_redemption_rate = get_sub_field('redemption_rate');
				$second_tier_notes = get_sub_field('notes');

				$table .= '<tr>';
				$table .= '<td>' . $second_tier_partner_program_company . ' - ' . $second_tier_partner_program_program . '</td>';
				if ($value === 0) {
					$table .= '<td>Not Available</td>';
				} else {
					$table .= '<td> $1 earns <strong>' . sigFig($redemption_rate * $second_tier_redemption_rate * $earn_rate, 4) . ' ' . $second_tier_partner_program_unit . '.</strong> <br/><small>' . $flexible_partner_program_program . ' 1: ' . $redemption_rate . ' (' . $notes . ').<br/>' . $second_tier_partner_program_unit . ' 1: ' . $second_tier_redemption_rate . ' (' . $second_tier_notes . ').</small></td>';
				}
				$table .= '</tr>';

				// DO NOT add program to $excluding_partner_programs to avoid duplication when handle second tier redemotion
				// array_push($excluding_partner_programs, $flexible_partner_program);

				// Add Flexible Points Program to Array
				if (!in_array($flexible_partner_program, $excluding_partner_programs) && $flexible_points_currency) {
					array_push($flexible_partner_programs, $partner_program);
				}
				// }
			}
			$table .= '</tbody>';
		}

		// Close Table
		$table .= '</table>';

		return do_shortcode($table);
	} else {
		return 'Reward Program has no Redemption Partner.';
	}
}
add_shortcode('ch_credit_card_rewards_programs_more_redemption_table', 'ch_generate_credit_card_rewards_programs_more_redemption_table');

/*
Create Tabs

Author: Jacktator
Plugin: Visual Composer Extensions All In One 3.4.9.3
ShortCode: [cq_vc_tab_item]
Usage: [ch_credit_card_rewards_programs_more_redemption_tabs] // Renders Rewards Programs in Tabs, Based on Earning 2 Credit Card Points per Dollar
 */
function ch_generate_credit_card_rewards_programs_more_redemption_tabs($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'earn_rate' => 1, // Default earn_rate to 1, Deprecated
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// Store Flexible Points for Second Tier Table
	$flexible_partner_programs = array();
	$excluding_partner_programs = array();

	$rewards_programs = get_field('rewards_programs'); // Repeater

	if (have_rows('rewards_programs')) {

		// Construct Tabs
		$rewards_programs_tabs = "";

		// Construct Tabs
		$rewards_programs_tabs .= '[cq_vc_tabs rotatetabs="0"]';

		while (have_rows('rewards_programs')) {

			the_row();

			// Feth Rewards Program Data
			$rewards_program = get_sub_field('rewards_program');
			$max_earn_rate = 0;

			// Find the Max Earn Rate
			if (have_rows('earn_rates')) {

				while (have_rows(earn_rates)) {

					the_row();

					$earn_rate = get_sub_field('earn_rate');

					if ($earn_rate > $max_earn_rate) {
						$max_earn_rate = $earn_rate;
					}

				}

			}

			// Construct Single Tab Item
			$rewards_programs_tabs .= '[cq_vc_tab_item tabtitle="' . $rewards_program->post_title . '"]';

			$rewards_programs_table = '[ch_credit_card_rewards_programs_more_redemption_table earn_rate="' . $max_earn_rate . '" rewards_program="' . $rewards_program->ID . '"]';

			// Return Table
			$rewards_programs_tabs .= $rewards_programs_table;

			// Close Single Tab Item
			$rewards_programs_tabs .= '[/cq_vc_tab_item]';

		}

		// Close Tabs
		$rewards_programs_tabs .= '[/cq_vc_tabs]';

		return do_shortcode($rewards_programs_tabs);
	} else {
		return "Error: Rewards Programs is empty.";
	}
}
add_shortcode('ch_credit_card_rewards_programs_more_redemption_tabs', 'ch_generate_credit_card_rewards_programs_more_redemption_tabs');

/*
Create shortcode for displaying Redemption Table using CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_redemption_table] // Meaning Earning 2 Credit Card Points per Dollar
 */
function ch_generate_redemption_table($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	$base_unit = get_field('unit');

	// Store Flexible Points for Second Tier Table
	$flexible_partner_programs = array();
	$excluding_partner_programs = array();

	if (have_rows('redemption_parnters')) {
		$table = '';
		// Construct Table Head
		$table_head = '<table style="width: 100%;">
                <thead>
                <tr>
                <th>Company</th>
                <th>Reward Program</th>
                <th>Redemption Rate</th>
                <th>Notes</th>
                </tr>
                </thead>';
		$table .= $table_head;

		// Construct Primary Table Body
		$table .= '<tbody>';

		while (have_rows('redemption_parnters')) {

			// Render Redemption

			the_row();

			$partner_program = get_sub_field('partner_program');
			$minimum = get_sub_field('minimum');
			$increment = get_sub_field('increment');
			$partner_program_fields = get_field_objects($partner_program->ID);
			$partner_program_company = get_field('company', $partner_program->ID); // Deprecated, use get_field('provider'); instead.
			if (!$partner_program_company) {
				$partner_program_company = get_field('provider', $partner_program->ID)->post_title;
			}
			$partner_program_program = get_field('program', $partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
			if (!$partner_program_program) {
				$partner_program_program = $partner_program->post_title;
			}
			$partner_program_unit = get_field('unit', $partner_program->ID);
			$flexible_points_currency = get_field('flexible_points_currency', $partner_program->ID);
			$partner_program_points_value = get_field('points_value', $partner_program->ID);

			$redemption_rate = get_sub_field('redemption_rate');
			$notes = get_sub_field('notes');

			$table .= '<tr>';
			$table .= '<td>' . $partner_program_company . '</td>';
			$table .= '<td>' . $partner_program_program . '</td>';
			if ($value === 0) {
				$table .= '<td colspan="2">Not Available</td>';
			} else {
				$table .= '<td> 1 ' . $base_unit . ' = <strong>' . $redemption_rate . ' ' . $partner_program_unit . '.</strong> <br/><small>Minimum ' . $minimum . ', increment ' . $increment . '. ' . '</small></td>';
				$table .= '<td>' . $notes . '. ' . '</td>';
			}
			$table .= '</tr>';

			// Add program to $excluding_partner_programs to avoid duplication when handle second tier redemotion
			array_push($excluding_partner_programs, $partner_program_program);

			// Add Flexible Points Program to Array
			if (!in_array($partner_program, $excluding_partner_programs) && $flexible_points_currency) {

				array_push($flexible_partner_programs, $partner_program);
			}
		}
		$table .= '</tbody>';

		// Construct Secondary Table Body
		foreach ($flexible_partner_programs as $flexible_partner_program) {

			$flexible_partner_program_company = get_field('company', $flexible_partner_program->ID); // Deprecated, use get_field('provider'); instead.
			if (!$flexible_partner_program_company) {
				$flexible_partner_program_company = get_field('provider', $flexible_partner_program->ID)->post_title;
			}
			$flexible_partner_program_program = get_field('program', $flexible_partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
			if (!$flexible_partner_program_program) {
				$flexible_partner_program_program = $flexible_partner_program->post_title;
			}

			// Construct Secondary Table Head
			$table .= '
                <thead>
                <tr>
                <th colspan="2"> Reward Program via ' . $flexible_partner_program_company . ' - ' . $flexible_partner_program_program . ' </th>
                <th> Effective Redemption Rate</th>
                <th> Notes</th>
                </tr>
                </thead>';

			// Construct Secondary Table Body
			$table .= '<tbody>';

			while (have_rows('redemption_parnters', $flexible_partner_program->ID)) {

				// Render Redemption

				the_row();

				$second_tier_partner_program = get_sub_field('partner_program');
				$second_tier_minimum = get_sub_field('minimum');
				$second_tier_increment = get_sub_field('increment');
				$second_tier_partner_program_fields = get_field_objects($second_tier_partner_program->ID);
				$second_tier_partner_program_company = get_field('company', $second_tier_partner_program->ID); // Deprecated, use get_field('provider'); instead.
				if (!$second_tier_partner_program_company) {
					$second_tier_partner_program_company = get_field('provider', $second_tier_partner_program->ID)->post_title;
				}
				$second_tier_partner_program_program = get_field('program', $second_tier_partner_program->ID); // Deprecated, use $partner_program->post_title; instead.
				if (!$second_tier_partner_program_program) {
					$second_tier_partner_program_program = $second_tier_partner_program->post_title;
				}
				$second_tier_partner_program_unit = get_field('unit', $second_tier_partner_program->ID);
				$second_tier_flexible_points_currency = get_field('flexible_points_currency', $second_tier_partner_program->ID);
				$second_tier_partner_program_points_value = get_field('points_value', $second_tier_partner_program->ID);

				// Only Add new partner in second tier redemption
				// if (!in_array($second_tier_partner_program_program, $excluding_partner_programs)) {

				$second_tier_redemption_rate = get_sub_field('redemption_rate');
				$second_tier_notes = get_sub_field('notes');

				$table .= '<tr>';
				$table .= '<td>' . $second_tier_partner_program_company . '</td>';
				$table .= '<td>' . $second_tier_partner_program_program . '</td>';
				if ($value === 0) {
					$table .= '<td>Not Available</td>';
				} else {
					$table .= '<td> 1 ' . $base_unit . ' = <strong>' . sigFig($redemption_rate * $second_tier_redemption_rate, 4) . ' ' . $second_tier_partner_program_unit . '.</strong> <br/><small>' . $flexible_partner_program_program . ' 1: ' . $redemption_rate . ' (Minimum ' . $minimum . ', increment ' . $increment . ').<br/>' . $second_tier_partner_program_unit . ' 1: ' . $second_tier_redemption_rate . ' (Minimum ' . $second_tier_minimum . ', increment ' . $second_tier_increment . ').</small></td>';
					$table .= '<td>' . $notes . '<br/>' . $second_tier_notes . '</td>';
				}
				$table .= '</tr>';

				// DO NOT add program to $excluding_partner_programs to avoid duplication when handle second tier redemotion
				// array_push($excluding_partner_programs, $flexible_partner_program);

				// Add Flexible Points Program to Array
				if (!in_array($flexible_partner_program, $excluding_partner_programs) && $flexible_points_currency) {
					array_push($flexible_partner_programs, $partner_program);
				}
				// }
			}
			$table .= '</tbody>';
		}

		// Close Table
		$table .= '</table>';

		// Return Table
		return do_shortcode($table);
	} else {
		return 'redemption_parnters is empty.';
	}
}
add_shortcode('ch_redemption_table', 'ch_generate_redemption_table');

/*
Trim Number to 4 Significant Digits.

Author: Jacktator
Reference: https://stackoverflow.com/a/48283297/3381997
 */
function sigFig($value, $digits) {
	if ($value == 0) {
		$decimalPlaces = $digits - 1;
	} elseif ($value < 0) {
		$decimalPlaces = $digits - floor(log10($value * -1)) - 1;
	} else {
		$decimalPlaces = $digits - floor(log10($value)) - 1;
	}

	$answer = round($value, $decimalPlaces);
	return $answer;
}

/*
Create shortcode for displaying Credit Card Image using ShadowCard CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Plugin: Visual Composer Extensions All In One 3.4.9.3
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_card_image]
 */
function ch_generate_card_image($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	$featured_image_id = get_post_thumbnail_id($post_id);
	$credit_card_title = get_the_title($post_id);

	$card_image_shortcode = '[cq_vc_shadowcard image="' . $featured_image_id . '" title="' . $credit_card_title . '" tolerance="12"]';

	return do_shortcode($card_image_shortcode);

}
add_shortcode('ch_card_image', 'ch_generate_card_image');

/*
Create shortcode for displaying Credit Card Features using ShadowCard CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Plugin: ThemeREX Addons 11.6.30
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_card_features]
 */
function ch_generate_card_features($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	$features_row_shortcode = '';

	// $features_row_shortcode .= '[vc_row css=".vc_custom_1546576317270{background-color: #eaeaea !important;}"][vc_column]';

	// $features_row_shortcode .= '[vc_empty_space alter_height="medium" hide_on_mobile=""]';

	$features_row_shortcode .= '[vc_row_inner]';

	$features = get_field('features'); // Repeater

	if (have_rows('features')) {

		// $features_objects = get_field_object('features');
		// $features_count = count($features_objects);

		while (have_rows('features')) {

			the_row();

			// Feth Feature Data
			$feature_title = get_sub_field('title');
			$feature_subtitle = get_sub_field('subtitle');
			$feature_description = get_sub_field('description');

			// Construct Feature Column
			$features_row_shortcode .= '[vc_column_inner width="1/4"][trx_sc_title title_style="default" title_align="center" title="' . $feature_title . '" subtitle="' . $feature_subtitle . '"][/vc_column_inner]';

		}
	}
	$features_row_shortcode .= '[/vc_row_inner]';

	// $features_row_shortcode .= '[vc_empty_space alter_height="medium" hide_on_mobile=""]';

	// $features_row_shortcode .= '[/vc_column][/vc_row][vc_row]';

	return do_shortcode($features_row_shortcode);

}
add_shortcode('ch_card_features', 'ch_generate_card_features');

/*
Create shortcode for displaying Credit Card Cons with CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Plugin: ThemeREX Addons 11.6.30
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_card_cons]
 */
function ch_generate_card_cons($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	$cons_list_shortcode = '';

	$cons_list_shortcode .= '<ul class="trx_addons_list_error">';

	$cons = get_field('cons'); // Repeater

	if (have_rows('cons')) {

		// $cons_objects = get_field_object('features');
		// $cons_count = count($cons_objects);

		while (have_rows('cons')) {

			the_row();

			// Feth Pro Data
			$pro_html = get_sub_field('con');

			// Construct Pro List Item
			$cons_list_shortcode .= '<li>' . $pro_html . '</li>';

		}
	}

	$cons_list_shortcode .= '</ul>';

	return $cons_list_shortcode;

}
add_shortcode('ch_card_cons', 'ch_generate_card_cons');

/*
Create shortcode for displaying Credit Card Pros with CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Plugin: ThemeREX Addons 11.6.30
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_card_pros]
 */
function ch_generate_card_pros($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	$pros_list_shortcode = '';

	$pros_list_shortcode .= '<ul class="trx_addons_list_success">';

	$pros = get_field('pros'); // Repeater

	if (have_rows('pros')) {

		// $pros_objects = get_field_object('features');
		// $pros_count = count($pros_objects);

		while (have_rows('pros')) {

			the_row();

			// Feth Pro Data
			$pro_html = get_sub_field('pro');

			// Construct Pro List Item
			$pros_list_shortcode .= '<li>' . $pro_html . '</li>';

		}
	}

	$pros_list_shortcode .= '</ul>';

	return $pros_list_shortcode;

}
add_shortcode('ch_card_pros', 'ch_generate_card_pros');

/*
Create shortcode for displaying Maximum Earn Rate Table using CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage:  [ch_interest_fee_table] // Generate the Interest & Fee Table
 */
function ch_generate_interest_fee_table($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// extract attributs

	$table = '<table style="width: 100%;">
	<tbody>
	<tr>
	<th style="width: 25%;">Product Name</th>
	<td>' . get_the_title() . '</td>
	</tr>
	<tr>
	<th>Card Image</th>
	<td>[ch_card_image]</td>
	</tr>
	<tr>
	<th>Card Network</th>
	<td>' . get_field('network')->post_title . '</td>
	</tr>
	<tr>
	<th>Card Issuer</th>
	<td>' . get_field('issuer')->post_title . '</td>
	</tr>
	<tr>
	<th>Annual fee</th>
	<td><strong>$' . get_field('annual_fee') . '</strong> p.a.</td>
	</tr>
	<tr>
	<th>Purchase Interest Rate</th>
	<td><strong>' . get_field('purchase_rate') . '%</strong> p.a</td>
	</tr>';

	if (get_field('cash_advance') != 'Available') {
		$table .=
			'<tr>
		<th>Cash Advance Interest</th>
		<td><strong>Not Available</strong></td>
		</tr>';
	} else {
		$table .=
		'<tr>
		<th>Cash Advance Interest</th>
		<td><strong>' . get_field('cash_advance_rate') . '% p.a.</strong></td>
		</tr>';
	}

	$table .=
	'<tr>
	<th>Interest Free Period</th>
	<td>Up to ' . get_field('interest_free') . ' days on purchases</td>
	</tr>
	<tr>
	<th>Foreign Transaction Fee</th>
	<td><strong>' . get_field('foreign_currency_conversion_fee') . '%</strong>, using <strong>' . get_field('foreign_exchange_provider') . '</strong>.</td>
	</tr>';

	$table .= '
	</tbody>
	</table>';

	return do_shortcode($table);

}
add_shortcode('ch_interest_fee_table', 'ch_generate_interest_fee_table');

/*
Create shortcode for displaying Eligibility Table using CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage:  [ch_eligibility_table] // Generate the Interest & Fee Table
 */
function ch_generate_eligibility_table($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	// extract attributs

	$table = '<table style="width: 100%;">
			<tbody>
			<tr>
			<th style="width: 25%;">Minimum Income</th>
			<td>$' . get_field('recommended_minimum_income') . ' p.a. ' . empty(get_field('minimum_income')) ? 'Reports indicates <small>$' . get_field('minimum_income') . '</small> p.a with good credit rating can be approaved.' : '' . '</td>
			</tr>
			<tr>
			<th>Minimum Credit Limit</th>
			<td>$' . get_field('min_credit_limit') . '</td>
			</tr>
			<tr>
			<th>Age Requirement</th>
			<td>' . get_field('minimum_age') . ' years old' . empty(get_field('maximum_age')) ? 'to ' . get_field('maximum_age') . ' years old.' : '.' . '</td>
			</tr>
			<tr>
			<th>Residency Requirement</th>
			<td>' . get_field('residency') . '</td>
			</tr>';

	if (get_field('additional_cardholder')) {
		$table .= '
			<tr>
			<th>Additional Cardholder</th>
			<td>Up to <strong>' . get_field('additional_cardholder_number') . '%</strong>, for $<strong>' . get_field('additional_cardholder_fee') . '</strong> each.</td>
			</tr>';
	}

	$table .= '
			</tbody>
			</table>';

	return do_shortcode($table);

}
add_shortcode('ch_eligibility_table', 'ch_generate_eligibility_table');

/*
Create shortcode for displaying Apply Now button with CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Plugin: ThemeREX Addons 11.6.30
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_apply_now_button]
 */
function ch_generate_apply_now_button($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	$button_html = '';

	$links = get_field('links'); // Repeater

	if (have_rows('links')) {

		// $links_objects = get_field_object('links');
		// $links_count = count($links_objects);

		while (have_rows('links')) {

			the_row();

			// Feth Feature Data
			$link_link = get_sub_field('link');
			$link_type = get_sub_field('type');
			$link_note = get_sub_field('notes');

			echo "<pre>";
			print_r($link_type);
			echo "</pre>";

			// Construct Feature Column
			$button_html .= '<a href="' . $link_link . '" class="sc_button color_style_default sc_button_default sc_button_size_normal sc_button_icon_left"><span class="sc_button_text"><span class="sc_button_title">' . strpos($link_type, "referral") ? "Apply Now^" : "Apply Now" . '</span></span></a>';

		}
	}

	return $$button_html;

}
add_shortcode('ch_apply_now_button', 'ch_generate_apply_now_button');

/*
Create shortcode for displaying Apply Now button with CPT and ACF.

Author: Jacktator
Plugin: Custom Post Type UI 1.6.1
Plugin: Advanced Custom Fields PRO 5.7.9
Plugin: ThemeREX Addons 11.6.30
Plugin: Alike - WordPress Custom Post Comparison 2.1.3
Reference: https://wordpress.stackexchange.com/a/291525/134082
Usage: [ch_compare_button]
 */
function ch_generate_compare_button($atts) {

	// extract attributs
	extract(shortcode_atts(array(
		'post_id' => false, // Default
		'format_value' => true, // Default
	), $atts));

	return '<a href="#" class="sc_button color_style_default sc_button_default sc_button_size_normal sc_button_icon_left alike-button alike-button-style" data-post-id="' . $post_id . '" data-post-title="' . get_the_title() . '" data-post-thumb="' . get_the_post_thumbnail_url() . '" data-post-link="' . get_post_link() . '" title="Add To Compare">
Add To Compare</a>';

}
add_shortcode('ch_compare_button', 'ch_generate_compare_button');

/*
Hide Featured Image on Single Post Page.

Author: Jacktator
Reference: https://stackoverflow.com/a/44003967/3381997
 */
function ch_hide_feature_image($html, $post_id, $post_image_id) {
	return is_single() ? '' : $html;
}
// add the filter
add_filter('post_thumbnail_html', 'ch_hide_feature_image', 10, 3);

?>
