<?php
/**
 * Custom template tags for the theme.
 *
 * @package FTPFileSynchronization
 */
if( ! function_exists( 'is_FwpActive' ) ) {
  function is_FwpActive( $opt ) {
    if( ! defined( 'FUTUREWORDPRESS_PROJECT_OPTIONS' ) ) {return false;}
    return ( isset( FUTUREWORDPRESS_PROJECT_OPTIONS[ $opt ] ) && FUTUREWORDPRESS_PROJECT_OPTIONS[ $opt ] == 'on' );
  }
}
if( ! function_exists( 'get_FwpOption' ) ) {
  function get_FwpOption( $opt, $def = false ) {
    if( ! defined( 'FUTUREWORDPRESS_PROJECT_OPTIONS' ) ) {return false;}
    return isset( FUTUREWORDPRESS_PROJECT_OPTIONS[ $opt ] ) ? FUTUREWORDPRESS_PROJECT_OPTIONS[ $opt ] : $def;
  }
}