<?php
/*
Plugin Name:  Nepal Forex API Shortcut Plugin
Plugin URI:
Description:  This plugin allows you to display current day's Nepal Rastra Bank Forex
Version:      1.0
Author:       Santosh Rai
Author URI:   https://github.com/santosrai
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/


if( ! defined( 'ABSPATH' ) ) : exit(); endif; // No direct access allowed.

// create shortcode plugin
add_shortcode('forex_nepal', 'get_forex_in_html_table' );

function get_forex_in_html_table() {

    //get current date
    $current_date = date('Y-m-d');

    $url = 'https://www.nrb.org.np/api/forex/v1/rates/?page=1&per_page=1&from=' . $current_date . '&to=' . $current_date;
    
    $arguments = array(
        'method' => 'GET',
    );

    $response = wp_remote_get( $url, $arguments );

    if( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo 'We have some technical problem. We will back soon'. $error_message;
    }

    $body = wp_remote_retrieve_body( $response );

    $data = json_decode( $body, true );

    //create table with $html
    $html = '<table style="border:1px solid #000000;">';
    $html .= '<tr>';
    $html .= '<th style="border:1px solid #000000; text-align:center;">Currency</th>';
    $html .= '<th style="border:1px solid #000000; text-align:center;">Unit</th>';
    $html .= '<th style="border:1px solid #000000; text-align:center;">Buying/Rs.</th>';
    $html .= '<th style="border:1px solid #000000; text-align:center;">Selling/Rs.</th>';
    $html .= '</tr>';

    //get array from json
    $payload_all = $data['data']['payload'];

    $payload = $payload_all[0]['rates'];
    
    // loop through array
    // Indain 
    foreach( $payload as $key => $value ) {
        //if not key not equal to 'INR
        if ( $key != 'INR' ) {
            $html .= '<tr>';
            $html .= '<td style="border:1px solid #000000;">'. $value['currency']['iso3'] .'(' . $value['currency']['name'] .')</td>';
            $html .= '<td style="border:1px solid #000000; text-align:center;">'. $value['currency']['unit'] .'</td>';
            $html .= '<td style="border:1px solid #000000; text-align:center;">'. $value['buy'] .'</td>';
            $html .= '<td style="border:1px solid #000000; text-align:center;">'. $value['sell'] .'</td>';
            $html .= '</tr>';
        }
    }

    $html .= '</table>';
    
    return $html;

}


