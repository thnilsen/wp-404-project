<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/thnilsen/wp-404-project/
 * @since             1.0.0
 * @package           wp-404-project
 *
 * @wordpress-plugin
 * Plugin Name:       WP 404 Project
 * Plugin URI:        https://github.com/thnilsen/wp-404-project/
 * Description:       SANS ISC 404 Project as a wordpress plugin. Forwards 404 Page Not Found URL/URI data to SANS ISC Weblog collector to discover web application attack trends.
 * Version:           1.0.0
 * Author:            Thomas Nilsen
 * Author URI:        https://crossley-nilsen.com
 * License:           GPL-2.0
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-404-project
 * Domain Path:       /languages
 */

define( 'WP_404_PROJECT_VERSION', '1.0.0' );
define( 'WP_404_PROJECT_DOMAIN', 'wp-404-project');

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/* Load dependent classes */
if ( !class_exists( 'RationalOptionPages' ) ) {
	require_once( 'includes/RationalOptionPages/RationalOptionPages.php' );
}

if ( is_admin() ) {

  $wp_404_project_options = wp_404_project_default_options();

  $wp_404_project_pages = array(
  	'wp_404_project_settings'	=> array(
  		'page_title'	=> __( 'WP 404 Project Settings', 'wp-404-project' ),
      'menu_slug' => 'wp_404_project_settings',
      'parent_slug'   => 'options-general.php',
      'sections'        => array(
          'section-one' => array(
               'title' => __( 'WP 404 Project', 'wp-404-project' ),
               'fields' => array(
                       'userid' =>array(
                          'id' => 'user_id',
                          'title' => __( 'User ID', 'wp-404-project' ),
                          'text'  => __( 'User ID # as found on My Account at https://isc.sans.edu/myaccount.html', "wp-404-project" ),
                          'type' => 'default',
                          'attributes' => array(
                              'maxlength' => 20,
                              'required'  => true,
                          ),
                          'sanitize'=> true,
                       ),
                       'apikey' => array(
                          'id' => 'api_key',
                          'title' => __( 'API Key', 'wp-404-project' ),
                          'text'  => __( 'API Key as listed on My Account at https://isc.sans.edu/myaccount.html', "wp-404-project" ),
                          'type' => 'default',
                          'attributes' => array(
                              'maxlength' => 60,
                              'required'  => true,
                          ),
                          'sanitize'=> true,
                       ),
                       'sourceuri' => array(
                          'id' => 'sourceuri',
                          'title' => __( 'Select parameter for source URI to be passed on', "wp-404-project" ),
                          'text'  => __( 'Privacy note: REQUEST_URI will include the query string. </br>If you do not feel comfortable with this, use REDIRECT_URL', "wp-404-project" ),
                          'type'  => 'select',
                          'value' => $wp_404_project_options['sourceuri'],
                          'choices' => array(
                              '_URI' => 'REQUEST_URI',
                              '_URL' => 'REDIRECT_URL',
                          ),
                       ),
                       'rate_limit' => array(
                          'id'    => 'rate_limit',
                          'title' => __( 'Select rate limit for how often a 404 record can be submitted', "wp-404-project" ),
                          'text'  => __( 'To prevent DoS conditions, this parameter will prevent continuous 404 records being passed on to SANS ISC', "wp-404-project" ),
                          'type'  => 'select',
                          'value' => $wp_404_project_options['rate_limit'],

                          'choices' => array(
                              '_60' => 'Once every 60 seconds',
                              '_45' => 'Onve every 45 seconds',
                              '_30' => 'Once every 30 seconds',
                              '_15' => 'Onve every 15 seconds',
                              '_10' => 'Once every 10 seconds',
                          ),
                       ),
                       'ip_mask' => array(
                          'id'    => 'ip_mask',
                          'title' => __( 'IP Mask:', "wp-404-project" ),
                          'text'  => __( 'Can be set to any level combination such as 0xffffff00 (= /24) or 0xffff0000 (= /16)</br> or 0xff000000 (= /8) or mix it up a little with 0x00ffffff.</br>Defaults to 0xffffffff which will report full IP.', "wp-404-project" ),
                          'type'  => 'default',
                          'value' => $wp_404_project_options['ip_mask'],
                          'attributes' => array(
                              'maxlength' => 10,
                              'required'  => false,
                              'pattern'   => '^0x[0fF]{8}$',
                          ),
                          'sanitize'=> true,
                       ),
                       'use_https' => array(
                          'id'      => 'use_https',
                          'title'   => __( 'Use HTTPS', "wp-404-project" ),
                          'text'    => __( 'If left unchecked, HTTP will be used to submit data to SANS ISC', "wp-404-project" ),
                          'checked' => $wp_404_project_options['use_https'],
                          'type'    => 'checkbox',
                       ),
                       'debug' => array(
                         'id'      => 'debug',
                         'title'   => __( 'Debug', "wp-404-project" ),
                         'text'    => __( 'Only leave enabled for testing and debug puproses. WP_DEBUG must be enabled to get logs', "wp-404-project" ),
                         'checked' => $wp_404_project_options['debug'],
                         'type'    => 'checkbox',
                       ),
                  ),
              ),
          ),
      ),
  );
}

/*
 * Settings link for Plugin page
 *
 * @param array $links
 */
function wp_404_project_settings_link( $links ) {
  $links[] = '<a href="' .admin_url( 'options-general.php?page=wp_404_project_settings' ) . '">' . __( 'Settings' ) . '</a>';
  return $links;
}

/*
 * Simple error log hadling
 *
 * @param string $str_error Text to log to errorlog file
 */
function wp_404_project_error_log($str_error){
  $wp_404_project_options = get_option( 'wp_404_project_settings', array() );

  if ( !empty( $wp_404_project_options['debug']) && $wp_404_project_options['debug'] == 'on' ) {
      error_log( WP_404_PROJECT_DOMAIN . " - " . $str_error );
  }
}

/*
 * Main hook for 404 redirects
 */
function wp_404_project_hook_404(){

  // Make sure we're in a 404 situation
  if( is_404() ){

      $bool_config_missing = false;
      $wp_404_project_options = wp_404_project_default_options();

      /* Validate options */
      $arr_value = array( '_URI' => 'REQUEST_URI', '_URL'=>'REDIRECT_URL' );
      if ( array_key_exists( $wp_404_project_options['sourceuri'], $arr_value ) ) {
          $s_url = $_SERVER[ $arr_value[ $wp_404_project_options['sourceuri'] ] ];
      } else {
          $s_url = $_SERVER['REQUEST_URI'];
      }

      /* Make sure options are set */
      if ( empty( $wp_404_project_options['user_id'] ) || empty( $wp_404_project_options['api_key'] ) || empty( $wp_404_project_options['ip_mask'] ) ) {
          // TODO - Log missig information
          $bool_config_missing = true;
      }

      /* Make sure mask if valid and if not force default */
      $res = preg_match( '/0x[0fF]{8}$/', $wp_404_project_options['ip_mask'] );
      if ( false === $res || $res == 0) {
          $wp_404_project_options['ip_mask'] = '0xFFFFFFFF';
          wp_404_project_error_log( "Mask {$wp_404_project_options['ip_mask']} is invalid - using default 0xFFFFFFFF" );
      }

      if ( ! function_exists('curl_init') ) {
          $bool_config_missing = true;
          wp_404_project_error_log( __( "Curl PHP module is missing", "wp-404-project") );
      }

      $str_protocol = 'http';
      if ( $wp_404_project_options['use_https'] == 'on' ) {
          $str_protocol = 'https';
      }

      /* Make sure rate limit is not below 10 seconds */
      $rate_limit = (int) str_replace('_', '', $wp_404_project_options['rate_limit']);
      if ( $rate_limit < 10 ) {
        $rate_limit = 10;
      }

      if ( $bool_config_missing ) {
          wp_404_project_error_log( __("Missing configuration settings - please check settings page") );
          return;
      }

      $s_ip = $_SERVER['REMOTE_ADDR'];
      $s_ua = $_SERVER['HTTP_USER_AGENT'];


      /* Set IP Mask
       *   Default = 0xffffffff (=/32)
       *   Can be set to any level combination
       *    such as 0xffffff00/24 or 0xffff0000 (=/16) or 0xff000000 (=/8)
       *    or mix it up a little with 0x00ffffff
       */

      /* Apply IP Mask */
      $s_ip = long2ip( ip2long( $s_ip ) & hexdec( $wp_404_project_options['ip_mask'] ) );

      /* Limit submissions to every 60 seconds to prevent DoS conditions */
      $run_time = get_option( 'wp_404_project_lastrun_timestamp' );

      if ( $run_time != false ) {
        if ( (time() - $run_time)  < $rate_limit ) {
          wp_404_project_error_log( 'Rate limit hit (<'. $rate_limit.' seconds) - try again later.' );
          return;
        }
      }
      update_option( 'wp_404_project_lastrun_timestamp', time() );

      $s_submit_site = $str_protocol . '://isc.sans.edu/';
      $s_submit_url  = 'weblogs/404project.html?id='. $wp_404_project_options['user_id'].'&version=2';

      $s_data = $wp_404_project_options['user_id']. chr(0). $wp_404_project_options['api_key'] . chr(0) . $s_url . chr(0) . $s_ip . chr(0) . $s_ua . chr(0) .date('Y-m-d') . chr(0). date('H:i:s') . chr(0) . $wp_404_project_options['ip_mask'];

      $s_post = array( 'timeout'  => 5,
                       'blocking' => true,
                       'headers'  => array(),
                       'body'     => array( 'DATA' => base64_encode( $s_data ) ),
                     );

      if( ! empty( $s_url ) ) {
          $response = wp_remote_post( $s_submit_site.$s_submit_url, $s_post );
          if ( is_wp_error( $response ) ) {
              wp_404_project_error_log( "Something failed.: " . $response->get_error_message() );
          } else {
              wp_404_project_error_log( "Submission sent to $s_submit_site$s_submit_url" );
          }
      }
  }
}

/*
 * Sets default values for the plugins
 *
 * @return array The default settings, or settings from DB if already saved to DB
 */
function wp_404_project_default_options(){
  $defaults = array(
    'rate_limit' => '_60',
    'use_https'  => 'checked',
    'sourceuri'  => '_URL',
    'ip_mask'    => '0xffffffff',
    'use_https'  => true,
    'debug'      => false,
  );
  return get_option( 'wp_404_project_settings', $defaults );

}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wp_404_project_settings_link' );
add_action( 'template_redirect', 'wp_404_project_hook_404' );

if ( is_admin() ) {
  $wp_404_project_option_page = new RationalOptionPages( $wp_404_project_pages );
}
