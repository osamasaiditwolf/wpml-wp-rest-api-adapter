<?php
/*
Plugin Name: WPML to WP API Adapter
Plugin URI: http://y-designs.com/
Description: A simple wordpress plugin that makes WP API work with WPML - adpated from Ryuhei Yokokawa Plugin (http://y-designs.com)
Version: 0.1
Author: Thomas Mery based on Ryuhei Yokokawa work
License: GPL
*/

/*
	Usage examples
	/wp-json/wp/v2/pages/?lang=en&fields[name]=accueil
	/wp-json/wp/v2/pages/?lang=fr&fields[name]=home
*/

/**
 * Switches WPML languages to the language set in the request
 * triggered just before the request is dispatched
 * via the 'rest_pre_dispatch' filter in WP_Rest_Server dispatch method
 * @param  null $value 
 * @param  WP_Rest_Server  $server  
 * @param  WP_Rest_Request $request 
 * @return null
 */
function wpml_json_api_init( $value, $server, $request ) {

	global $sitepress;

	if (!$sitepress) {

		return $value;

	}

	// Get all available langauges (only the keys, en,ja,fr, etc).
	$langs = array_keys(icl_get_languages('skip_missing=0&orderby=KEY&order=DIR&link_empty_to=str'));

	// Get the set lang variable
	$cur_lang = $request->get_param('lang');

	// set default against it if it doesn't exist
	if(!$cur_lang) {

		$cur_lang = $sitepress->get_default_language(); ;

	}

	//See if the current language is NOT part of the available langauges.
	if( !in_array($cur_lang, $langs ) ) {
		$cur_lang = $default_lang;//If not, set it against the default.
	}
	//finally, set the language to modify the WP-API query.
	$sitepress->switch_lang( $cur_lang );

	return $value;

}

// make the WPML query change happen before WP API runs the query
add_action( 'rest_pre_dispatch', 'wpml_json_api_init', 0, 3 );