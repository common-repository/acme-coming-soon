<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin deactivation
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/includes
 * @author     codersantosh <codersantosh@gmail.com>
 */
class Acme_Coming_Soon_Deactivator {

	/**
	 * Fired during plugin deactivation.
	 *
	 * For now just placeholder.
	 * Removing options, table and all data related to plugin if user select remove data on deactivate.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		if ( acme_coming_soon_get_options( 'deleteAll' ) ) {
			delete_option( ACME_COMING_SOON_OPTION_NAME );
		}
	}
}
