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
  $vc_list = array('page','credit_cards', 'reward_programs', 'card_companies', 'cpt_services');
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


/*
    Add shortcode support for ACF Table Field
    
    Author: Jacktator
    Plugin: Advanced Custom Field Table Field 1.2.6
    Reference: https://wordpress.org/plugins/advanced-custom-fields-table-field/
*/
function shortcode_acf_tablefield( $atts ) {

    $a = shortcode_atts( array(
        'field-name' => false,
        'post-id' => false,
    ), $atts );

    $table = get_field( $a['field-name'], $a['post-id'] );

    $return = '';

    if ( $table ) {

        $return .= '<table border="0">';

            if ( $table['header'] ) {

                $return .= '<thead>';

                    $return .= '<tr>';

                        foreach ( $table['header'] as $th ) {

                            $return .= '<th>';
                                $return .= $th['c'];
                            $return .= '</th>';
                        }

                    $return .= '</tr>';

                $return .= '</thead>';
            }

            $return .= '<tbody>';

                foreach ( $table['body'] as $tr ) {

                    $return .= '<tr>';

                        foreach ( $tr as $td ) {

                            $return .= '<td>';
                                $return .= $td['c'];
                            $return .= '</td>';
                        }

                    $return .= '</tr>';
                }

            $return .= '</tbody>';

        $return .= '</table>';
    }

    return $return;
}

add_shortcode( 'table', 'shortcode_acf_tablefield' );


/*
    Create shortcode for displaying Effective Earn Rate Table using CPT and ACF.
    
    Author: Jacktator
    Plugin: Custom Post Type UI 1.6.1 
    Plugin: Advanced Custom Fields PRO 5.7.9
    Reference: https://wordpress.stackexchange.com/a/291525/134082
    Usage: [ch_effective_earn_table earn_rate="2"] // Meaning Earning 2 Credit Card Points per Dollar
*/
function ch_generate_earn_table( $atts ) {
    
    // extract attributs
    extract( shortcode_atts( array(
        'earn_rate'     => 1,       // Default earn_rate to 1
        'post_id'       => false,   // Default
        'format_value'  => true     // Default
    ), $atts ) );
    
    // // get value and return it
    $rewards_program = get_field( 'rewards_program');

    if ( $rewards_program ) {

        $fields = get_field_objects($rewards_program->ID);
        if( $fields ) {
            $table = '';
            // Construct Table Head
            $table_head = '<table style="width: 100%;">
                    <thead>
                    <tr>
                    <th>Program</th>
                    <th>Effective Earn Rate</th>
                    </tr>
                    </thead>';
            $table .= $table_head;

            // Construct Table Body
            $table .= '<tbody>';
            foreach( $fields as $field_name => $field )
            {
                $value = $field['value'];
                if (is_numeric($value) && $field['label'] !== 'Points Valuation') {
                    $table .= '<tr>';
                        $table .= '<td>' . $field['label'] . '</td>';
                        if ($value === 0) {
                            $table .= '<td>Not Available</td>';
                        } else {
                            $effective_earn_rate = $value * $earn_rate;
                            $table .= '<td>' . $effective_earn_rate . ' ' . $field['append'] . '</td>';
                        }
                    $table .= '</tr>';
                }
            }

            // Close Table
            $table .= '</tbody></table>';

            // Return Table
            return $table;
        } else {
            return 'Fields is empty.';
        }
    } else {
        return 'Rewards Program is empty.';
    }
}
add_shortcode('ch_effective_earn_table', 'ch_generate_earn_table');


/*
    Create shortcode for displaying Redemption Table using CPT and ACF.
    
    Author: Jacktator
    Plugin: Custom Post Type UI 1.6.1 
    Plugin: Advanced Custom Fields PRO 5.7.9
    Reference: https://wordpress.stackexchange.com/a/291525/134082
    Usage: [ch_redemption_table] // Meaning Earning 2 Credit Card Points per Dollar
*/
function ch_generate_redemption_table( $atts ) {
    
    // extract attributs
    extract( shortcode_atts( array(
        'post_id'       => false,   // Default
        'format_value'  => true     // Default
    ), $atts ) );

    $redemption_parnters = get_field('redemption_parnters');

    if( $redemption_parnters ) {
        $table = '';
        // Construct Table Head
        $table_head = '<table style="width: 100%;">
                <thead>
                <tr>
                <th>Partner</th>
                <th>Program</th>
                <th>Redemption Rate (1 point redeems for)</th>
                </tr>
                </thead>';
        $table .= $table_head;

        // Construct Table Body
        $table .= '<tbody>';
        while (the_repeater_field('redemption_parnters')) {

            $partner_program = get_sub_field_object('partner_program');
            $redemption_rate = the_sub_field('redemption_rate');
            $notes = the_sub_field('notes');
                    
            $company_id = $partner_program->ID;
            $company1 = $partner_program->company;
            $company2 = $partner_program['company'];
            echo "<pre>";
            echo "company_id 1: " . $company_id;
            echo "<br/>company 1: " . $company1;
            echo "<br/>company 2: " . $company2;
            echo "</pre>";

            $table .= '<tr>';
                $table .= '<td>' . $partner_program->company . '</td>';
                $table .= '<td>' . $partner_program->program . '</td>';
                if ($value === 0) {
                    $table .= '<td>Not Available</td>';
                } else {
                    $table .= '<td>' . $redemption_rate . ' ' . $partner_program->unit . '<br/>' . $notes . '</td>';
                }
            $table .= '</tr>';
        }

        // Close Table
        $table .= '</tbody></table>';

        // Return Table
        return $table;
    } else {
        return 'redemption_parnters is empty.';
    }
}
add_shortcode('ch_redemption_table', 'ch_generate_redemption_table');

?>
