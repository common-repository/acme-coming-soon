<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across the plugin.
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/main
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-public both end hooks, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/main
 * @author     codersantosh <codersantosh@gmail.com>
 */
class Acme_Coming_Soon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Acme_Coming_Soon_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->define_include_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/* API */
		require_once ACME_COMING_SOON_PATH . 'includes/api/index.php';

		/**Plugin Core Functions*/
		require_once ACME_COMING_SOON_PATH . 'includes/functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ACME_COMING_SOON_PATH . 'includes/class-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ACME_COMING_SOON_PATH . 'includes/class-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in both admin and public area.
		 */
		require_once ACME_COMING_SOON_PATH . 'includes/class-include.php';

		/**
		 * The class responsible for getting and registering patterns.
		 */
		require_once ACME_COMING_SOON_PATH . 'includes/class-patterns.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ACME_COMING_SOON_PATH . 'admin/class-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ACME_COMING_SOON_PATH . 'public/class-public.php';

		$this->loader = new Acme_Coming_Soon_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Acme_Coming_Soon_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Acme_Coming_Soon_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to both admin and public area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_include_hooks() {

		$plugin_include = acme_coming_soon_include();

		/* Register scripts and styles */
		$this->loader->add_action( 'init', $plugin_include, 'register_scripts_and_styles' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = acme_coming_soon_admin();

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_resources' );

		/*Register Settings*/
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'register_settings', 1 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings', 1 );

		$this->loader->add_filter( 'plugin_action_links_acme-coming-soon/acme-coming-soon.php', $plugin_admin, 'add_plugin_links', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = acme_coming_soon_public();

		$this->loader->add_action( 'template_redirect', $plugin_public, 'redirect_to_maintenance', 999999 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Acme_Coming_Soon_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
