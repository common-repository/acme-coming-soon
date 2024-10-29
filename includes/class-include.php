<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The common bothend functionality of the plugin.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/includes
 */

/**
 * The common bothend functionality of the plugin.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/includes
 * @author     codersantosh <codersantosh@gmail.com>
 */
class Acme_Coming_Soon_Include {

	/**
	 * Static property to store Options Settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    settings All settings for this plugin.
	 */
	private static $settings = null;

	/**
	 * Static property to store white label settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    settings All settings for this plugin.
	 */
	private static $white_label = null;

	/**
	 * Gets an instance of this object.
	 * Prevents duplicate instances which avoid artefacts and improves performance.
	 *
	 * @static
	 * @access public
	 * @return object
	 * @since 1.0.0
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication.
		static $instance = null;

		// Only run these methods if they haven't been ran previously.
		if ( null === $instance ) {
			/* Query only once */
			self::$settings    = acme_coming_soon_get_options();
			self::$white_label = acme_coming_soon_get_white_label();

			$instance = new self();
		}

		// Always return the instance.
		return $instance;
	}

	/**
	 * Get the settings from the class instance.
	 *
	 * @access public
	 * @return array|null
	 */
	public function get_settings() {
		return self::$settings;
	}

	/**
	 * Get options related to white label.
	 *
	 * @access public
	 * @return array|null
	 */
	public function get_white_label() {
		return self::$white_label;
	}
	/**
	 * Register scripts and styles
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return void
	 */
	public function register_scripts_and_styles() {
		/* Atomic css */
		wp_register_style( 'atomic', ACME_COMING_SOON_URL . 'assets/library/atomic-css/atomic.min.css', array(), ACME_COMING_SOON_VERSION );
	}
}

if ( ! function_exists( 'acme_coming_soon_include' ) ) {
	/**
	 * Return instance of  Acme_Coming_Soon_Include class
	 *
	 * @since 1.0.0
	 *
	 * @return Acme_Coming_Soon_Include
	 */
	function acme_coming_soon_include() {
		return Acme_Coming_Soon_Include::get_instance();
	}
}
