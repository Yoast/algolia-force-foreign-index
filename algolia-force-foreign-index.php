<?php
/**
 * Plugin Name: Algolia force foreign index
 * Version: 2.0
 * Description: Hides the settings in the Algolia options and sets the Yoast.com index as Algolia's source.
 * Author: Team Yoast
 * Author URI: https://yoast.com/
 * License: GPL v3
 */

add_filter( 'algolia_autocomplete_config', function ( $config ) {

	$prefix = 'yoastcom';
	if ( defined( 'YOAST_ENVIRONMENT' ) && YOAST_ENVIRONMENT == 'development' ) {
		$prefix = 'test_dev';
	}

	$index_name = $prefix . '_searchable_posts';

	$config = array(
		0 => array(
			'index_id'        => 'searchable_posts',
			'index_name'      => $index_name,
			'label'           => 'Searchable posts',
			'position'        => 10,
			'max_suggestions' => 5,
			'tmpl_suggestion' => 'autocomplete-post-suggestion',
			'enabled'         => true,
		),
	);

	return $config;
} );

/**
 * Always enable Algolia autocomplete.
 */
add_filter( 'option_algolia_autocomplete_enabled', function ( $option ) {
	return 'yes';
} );
