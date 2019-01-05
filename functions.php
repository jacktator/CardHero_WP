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
    Create shortcode for displaying CPT and ACF.
    
    Author: Jacktator
    Plugin: Custom Post Type UI 1.6.1 
    Plugin: Advanced Custom Fields PRO 5.7.9
    Reference: https://wordpress.stackexchange.com/a/291525/134082
    Usage: [ch_earn_table earn_rate="2"] // Meaning Earning 2 Credit Card Points per Dollar
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

    $fields = get_field_objects($rewards_program->ID);
    if( $fields )
    {
        // Construct Table Head
        $table_head = '<table style="width: 100%;">
                <thead>
                <tr>
                <th>Partner</th>
                <th>Program</th>
                <th>Effective Earn Rate</th>
                </tr>
                </thead>';
        echo $table_head;

        // Construct Table Body
        echo '<tbody>';
        foreach( $fields as $field_name => $field )
        {
            $value = $field['value'];
            if (is_numeric($value)) {
                echo '<tr>';
                    echo '<td>' . $field['label'] . '</td>';
                    echo '<td>' . $field['append'] . '</td>';
                    if (if_zero($value)) {
                        echo '<td>Not Available</td>';
                    } else {
                        $effective_earn_rate = $value * $earn_rate;
                        echo '<td>$effective_earn_rate</td>';
                    }
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
    }
}

add_shortcode('ch_earn_table', 'ch_generate_earn_table');

?>
