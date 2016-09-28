<?php

/**
 * Plugin Name: Algolia force foreign index
 * Version: 1.0
 * Description: Hides the settings in the Algolia options and sets the Yoast.com index as Algolia's source.
 * Author: Team Yoast
 * Author URI: https://yoast.com/
 * License: GPL v3
 */


/*
 * Removes all filter that Algolia uses in order to update its indexes. We don't want any of this on this site, so we remove it.
 */
add_action( 'admin_init', function () {
	remove_anonymous_object_filter( 'before_delete_post', 'AlgoliaPluginAuto', 'postDeleted' );
	remove_anonymous_object_filter( 'transition_post_status', 'AlgoliaPluginAuto', 'postUnpublished' );
	remove_anonymous_object_filter( 'save_post', 'AlgoliaPluginAuto', 'postUpdated' );
	remove_anonymous_object_filter( 'edited_term_taxonomy', 'AlgoliaPluginAuto', 'termTaxonomyUpdated' );
	remove_anonymous_object_filter( 'created_term', 'AlgoliaPluginAuto', 'termCreated' );
	remove_anonymous_object_filter( 'delete_term', 'AlgoliaPluginAuto', 'termDeleted' );
	remove_anonymous_object_filter( 'admin_post_reindex', 'AlgoliaPlugin', 'admin_post_reindex' );
	remove_anonymous_object_filter( 'admin_post_reindex', 'AlgoliaPlugin', 'admin_post_reindex' );
} );


/*
 * We need to hook right after the algolia script is localized. hence the priority:11.
 * Digs into the localized Algolia data and changes the index_prefix to yoastcom so Algolia's autocomplete functionality on the frontend uses the yoastcom index to render its results.
 */
add_action( 'wp_enqueue_scripts', function () {
	global $wp_scripts;
	$script_data_string_old                                            = $wp_scripts->registered['lib/algoliaBundle.min.js']->extra['data'];
	$script_data_stirng_new                                            = preg_replace( '/"index_prefix":".*?"/', '"index_prefix":"yoastcom_"', $script_data_string_old );
	$wp_scripts->registered['lib/algoliaBundle.min.js']->extra['data'] = $script_data_stirng_new;
}, 11 );


/**
 * Removes the Algolia settings page from the admin menu. This helps preventing an accidental reindex of the (wrong) index.
 */
add_filter( 'admin_menu', function () {
	remove_menu_page( 'algolia-settings' );
}, 11 );


if ( ! function_exists( 'remove_anonymous_object_filter' ) ) {
	/**
	 * Remove an anonymous object filter.
	 *
	 * @param  string $tag    Hook name.
	 * @param  string $class  Class name
	 * @param  string $method Method name
	 *
	 * @return void
	 */
	function remove_anonymous_object_filter( $tag, $class, $method ) {
		$filters = $GLOBALS['wp_filter'][ $tag ];

		if ( empty ( $filters ) ) {
			return;
		}

		foreach ( $filters as $priority => $filter ) {
			foreach ( $filter as $identifier => $function ) {
				if ( is_array( $function )
				     && is_a( $function['function'][0], $class )
				         && $method === $function['function'][1]
				) {
					remove_filter(
						$tag,
						array( $function['function'][0], $method ),
						$priority
					);
				}
			}
		}
	}
}

