<?php
/**
 * Singleton Sharing
 *
 * Share and embed your blog posts and comments, bbPress discussions, and BuddyPress activity, across your WordPress site.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package SingletonSharing
 */

/*
Plugin Name: Singleton Sharing
Plugin URI: https://github.com/paulgibbs/singleton-sharing/
Description: Share and embed your blog posts and comments, bbPress discussions, and BuddyPress activity, across your WordPress site.
Version: 1.0
Requires at least: 3.8
Tested up to: 3.9.20
License: GPLv3
Author: Paul Gibbs
Author URI: http://byotos.com/
Domain Path: ../../languages/plugins/
Text Domain: singleton-sharing

"Singleton Sharing"
Copyright (C) 2014 Paul Gibbs

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 3 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
//dashicons content: "\f473";

/**
 * Register an embed handler for this site's blog posts and comments.
 *
 * @since Singleton Sharing (1.0)
 */
function dps_add_wp_embed_handlers() {

	// Posts
	$pattern = preg_quote( home_url() . $GLOBALS['wp_rewrite']->permalink_structure, '/' );
	$pattern = preg_replace_callback( '/%[^%]+%/', 'dps_wp_embed_handler_cb', $pattern );
	wp_embed_register_handler( 'dps_wordpress_post', '#^' . $pattern . '$#i', 'dps_wp_embed_handler' );

	// Comments
	$pattern  = preg_quote( home_url() . $GLOBALS['wp_rewrite']->permalink_structure, '/' );
	$pattern .= '(?:comment-page-(?:[0-9]{1,})/?)?';   // example.com/2014/03/hello-world/comment-page-1/#comment-37265
	$pattern .= '\#comment-(?<comment_id>[0-9]{1,})';  // example.com/2014/03/hello-world/#comment-1
	$pattern  = preg_replace_callback( '/%[^%]+%/', 'dps_wp_embed_handler_cb', $pattern );
	wp_embed_register_handler( 'dps_wordpress_comment', '#^' . $pattern . '$#i', 'dps_wp_embed_handler' );
}
add_action( 'init', 'dps_add_wp_embed_handlers' );

/**
 * preg_replace callback function for {@see dps_add_wp_embed_handler()}; strips out unwanted rewrite tokens.
 *
 * @param array $matches Matches from the regular expression
 * @return string Replacement value
 * @since Singleton Sharing (1.0)
 */
function dps_wp_embed_handler_cb( array $matches ) {
	$match = array_shift( $matches );

	// The post_name or post_id are required in the embed handler, so use named capture groups.
	if ( $match === '%postname%' ) {
		return '(?<postname>[^/]+)';
	} elseif ( $match === '%post_id%' ) {
		return '(?<post_id>[0-9]+)';
	} else {
		return '[^/]+';
	}
}


/**
 * Embed handlers
 */

/**
 * Ember handler for blog posts and comments.
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 * @since Singleton Sharing (1.0)
 */
function dps_wp_embed_handler( $matches, $attr, $url, $rawattr ) {
	$html = '';
	return apply_filters( 'dps_wp_embed_handler', $html, $matches, $attr, $url, $rawattr );
}

