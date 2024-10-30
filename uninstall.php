<?php
/**
 * Uninstall file for the plugin
 * php version        7.0
 *
 * @category Plugin
 *
 * @package LlamaAiBlogAssistant
 *
 * @author MarketingLlama.ai <manish.katyan@higheredlab.com>
 *
 * @license GPLv3-or-later https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @link https://marketingllama.ai/
 */

// phpcs:disable
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete the plugin settings
// Unregister settings
unregister_setting( 'llama-plugin-settings', 'llama_model_name' );
unregister_setting( 'llama-plugin-settings', 'llama_api_secret' );

// Delete options
delete_option( 'llama_model_name' );
delete_option( 'llama_api_secret' );


// Delete the post meta
$args = array(
	'post_type' => 'post',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'llama_token_used',
			'compare' => 'EXISTS',
			// Meta field exists
		),
		array(
			'key' => 'llama_time_taken',
			'compare' => 'EXISTS',
			// Meta field exists
		),
	),
);
$query = new WP_Query( $args );

if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		// Delete meta fields
		delete_post_meta( get_the_ID(), 'llama_token_used' );
		delete_post_meta( get_the_ID(), 'llama_time_taken' );
	}

	wp_reset_postdata();
}
