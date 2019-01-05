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
    $rewards_program = get_field( 'rewards_program', $post_id, $format_value );

    $qantas = get_fields($rewards_program->ID, $format_value);
    echo '<pre>';
        print_r( $rewards_program->ID);
        print_r( $rewards_program );
    echo '</pre>';

    // if( $post_object ) {
    //     // override $post
    //     $program = $rewards_program;
    //     setup_postdata( $program ); 

    //     $qantas = the_field('qantas');
    // return $qantas;

    //     wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly 
    // }
    
    // $qantas = $rewards_program['qantas'];

    $output = '<table style="width: 100%;">
                <thead>
                <tr>
                <th>Partner</th>
                <th>Program</th>
                <th>Effective Earn Rate</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                <td>Qantas</td>
                <td>Qantas Points</td>
                <td>Not Available</td>
                </tr>
                <tr>
                <td>Virgin Australia</td>
                <td>Velocity Points</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Cathy Pacific</td>
                <td>Asia Miles</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Singapore Airline</td>
                <td>Krisflyer Miles</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Emirates</td>
                <td>Skywards Miles</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Etihad</td>
                <td>Etihad Guest Miles</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Virgin Atalantic</td>
                <td>Flying Club Miles</td>
                <td>Not Available</td>
                </tr>
                <tr>
                <td>Air New Zealand</td>
                <td>Airpoints</td>
                <td>0.0075</td>
                </tr>
                <tr>
                <td>Thai Airways</td>
                <td>Royal Orchid Plus Miles</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Malaysia Airlines</td>
                <td>Enrich Miles</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>Hilton Honors</td>
                <td>Honors Points</td>
                <td>0.75</td>
                </tr>
                <tr>
                <td>SPG &amp; Marriot</td>
                <td>Marriott Rewards Points</td>
                <td>0.66</td>
                </tr>
                </tbody>
                </table>';
    // return $output;

    // array
    // if( is_array($value) ) {
        
    //     $value = @implode( ', ', $value );
        
    // }

    // $atts = shortcode_atts( array(
    //     'earn_rate' => '', // Default value.
    // ), $atts );

    // $earn_rate = echo (!$atts['earn_rate']) ? '' : $atts['earn_rate'];

    // $output = '[acf field="image" earn_rate="' . $atts['earn_rate'] . '"]';
    // $output = do_shortcode( $output );
    // $output = '<img src="' . $output . '" />';
    // return $output;
}

add_shortcode('ch_earn_table', 'ch_generate_earn_table');

?>
