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
function dps_add_wp_embed_handler() {

	// Look for URLs that match the current permalink structure -- posts.
	$post_pattern = preg_quote( home_url() . $GLOBALS['wp_rewrite']->permalink_structure, '/' );
	$post_pattern = preg_replace_callback( '/%[^%]+%/', 'dps_cb_wp_embed_regex', $post_pattern );

	wp_embed_register_handler( 'dps_wordpress_post', '#^' . $post_pattern . '$#i', 'dps_wp_embed_handler' );
}
add_action( 'init', 'dps_add_wp_embed_handler' );

/**
 * Callback function for a preg_replace in {@see dps_add_wp_embed_handler()}.
 *
 * @param array $matches Matches from the regular expression
 * @return string Replacement value
 * @since Singleton Sharing (1.0)
 */
function dps_cb_wp_embed_regex( array $matches ) {
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
 * The Google Video embed handler callback. Google Video does not support oEmbed.
 *
 * @param array $matches The regex matches from the provided regex when calling {@link wp_embed_register_handler()}.
 * @param array $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 * @since Singleton Sharing (1.0)
 */
function xwp_embed_handler_googlevideo( $matches, $attr, $url, $rawattr ) {
	// If the user supplied a fixed width AND height, use it
	if ( !empty($rawattr['width']) && !empty($rawattr['height']) ) {
		$width  = (int) $rawattr['width'];
		$height = (int) $rawattr['height'];
	} else {
		list( $width, $height ) = wp_expand_dimensions( 425, 344, $attr['width'], $attr['height'] );
	}

	return apply_filters( 'embed_googlevideo', '<embed type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docid=' . esc_attr($matches[2]) . '&amp;hl=en&amp;fs=true" style="width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px" allowFullScreen="true" allowScriptAccess="always" />', $matches, $attr, $url, $rawattr );
}
