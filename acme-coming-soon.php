<?php
/**
 * The plugin main file.
 *
 * @link              https://patternswp.com/wp-plugins/coming-soon-and-maintenance-mode-page/
 * @since             1.0.0
 * @package           Acme_Coming_Soon
 *
 * Plugin Name:       Acme Coming Soon and Maintenance Mode Page
 * Plugin URI:        https://patternswp.com/wp-plugins/coming-soon-and-maintenance-mode-page/
 * Description:       Coming Soon and Maintenance Mode Page simplifies WordPress site management. Easily enable or disable Coming Soon or Maintenance Mode in a toggle, redirecting unauthorized users to a personalized page. Craft your page using Gutenberg Blocks/Patterns or your favorite page builder with complete flexibility and features for creating any WordPress page.
 * Version:           1.0.5
 * Author:            patternswp
 * Author URI:        https://patternswp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acme-coming-soon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin path.
 * Current plugin url.
 * Current plugin version.
 * Current plugin name.
 * Current plugin option name.
 */
define( 'ACME_COMING_SOON_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACME_COMING_SOON_URL', plugin_dir_url( __FILE__ ) );
define( 'ACME_COMING_SOON_VERSION', '1.0.5' );
define( 'ACME_COMING_SOON_PLUGIN_NAME', 'acme-coming-soon' );
define( 'ACME_COMING_SOON_OPTION_NAME', 'acme_coming_soon_options' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class--activator.php
 */
function acme_coming_soon_activate() {

	require_once ACME_COMING_SOON_PATH . 'includes/class-activator.php';
	Acme_Coming_Soon_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function acme_coming_soon_deactivate() {
	require_once ACME_COMING_SOON_PATH . 'includes/class-deactivator.php';
	Acme_Coming_Soon_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'acme_coming_soon_activate' );
register_deactivation_hook( __FILE__, 'acme_coming_soon_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ACME_COMING_SOON_PATH . 'includes/main.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function acme_coming_soon_run() {
	$plugin = new Acme_Coming_Soon();
	$plugin->run();
}

acme_coming_soon_run();
