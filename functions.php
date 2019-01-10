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
					$rewards_programs_table .= '<td> Earn ' . $earn_rate . ' ' . $rewards_program_unit . ' Per Dollar spent. </td>';
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

	return $rewards_programs_tabs;
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

	return $table;
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

		return $rewards_programs_tabs;
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

		return $table;
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

		return $rewards_programs_tabs;
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
                </tr>
                </thead>';
		$table .= $table_head;

		// Construct Primary Table Body
		$table .= '<tbody>';

		while (have_rows('redemption_parnters')) {

			// Render Redemption

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
			$table .= '<td>' . $partner_program_company . '</td>';
			$table .= '<td>' . $partner_program_program . '</td>';
			if ($value === 0) {
				$table .= '<td>Not Available</td>';
			} else {
				$table .= '<td> 1 ' . $base_unit . ' = <strong>' . $redemption_rate . ' ' . $partner_program_unit . '.</strong> <br/><small>' . $notes . ' . ' . '</small></td>';
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
				$table .= '<td>' . $second_tier_partner_program_company . '</td>';
				$table .= '<td>' . $second_tier_partner_program_program . '</td>';
				if ($value === 0) {
					$table .= '<td>Not Available</td>';
				} else {
					$table .= '<td> 1 ' . $base_unit . ' = <strong>' . sigFig($redemption_rate * $second_tier_redemption_rate, 4) . ' ' . $second_tier_partner_program_unit . '.</strong> <br/><small>' . $flexible_partner_program_program . ' 1: ' . $redemption_rate . ' (' . $notes . ').<br/>' . $second_tier_partner_program_unit . ' 1: ' . $second_tier_redemption_rate . ' (' . $second_tier_notes . ').</small></td>';
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
		return $table;
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

?>
