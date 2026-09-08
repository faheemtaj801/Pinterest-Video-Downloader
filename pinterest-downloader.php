<?php
/**
 * Plugin Name:       Pinterest Downloader
 * Plugin URI:        https://example.com/pinterest-downloader
 * Description:       A modern Pinterest media downloader tool supporting video and image download workflows.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Your Name
 * Author URI:        https://example.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pinterest-downloader
 * Domain Path:       /languages
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PD_VERSION', '1.0.0' );
define( 'PD_PLUGIN_FILE', __FILE__ );
define( 'PD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Autoload provider and engine classes.
require_once PD_PLUGIN_DIR . 'includes/class-provider-result.php';
require_once PD_PLUGIN_DIR . 'includes/interface-provider.php';
require_once PD_PLUGIN_DIR . 'includes/class-pinterest-provider.php';
require_once PD_PLUGIN_DIR . 'includes/class-downloader-engine.php';
require_once PD_PLUGIN_DIR . 'includes/class-ajax.php';
require_once PD_PLUGIN_DIR . 'includes/class-assets.php';
require_once PD_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once PD_PLUGIN_DIR . 'includes/class-seo.php';
require_once PD_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Returns the main plugin instance.
 *
 * @return PD_Plugin
 */
function pd_plugin() {
	return PD_Plugin::get_instance();
}

// Initialise the plugin after WordPress is fully loaded.
add_action( 'plugins_loaded', 'pd_plugin' );
