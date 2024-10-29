<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/Admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Define and execute the hooks for overall functionalities of the plugin and add the admin end like loading resources and defining settings.
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/Admin
 * @author     codersantosh <codersantosh@gmail.com>
 */
class Acme_Coming_Soon_Admin {

	/**
	 * Menu info.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $menu_info    Admin menu information.
	 */
	private $menu_info;

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

			$instance = new self();
		}

		// Always return the instance.
		return $instance;
	}

	/**
	 * Add Admin Page Menu page.
	 *
	 * @access public
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {

		$white_label     = acme_coming_soon_include()->get_white_label();
		$this->menu_info = $white_label['admin_menu_page'];

		add_menu_page(
			$this->menu_info['page_title'],
			$this->menu_info['menu_title'],
			'manage_options',
			$this->menu_info['menu_slug'],
			array( $this, 'add_setting_root_div' ),
			$this->menu_info['icon_url'],
			$this->menu_info['position'],
		);
	}

	/**
	 * Add Root Div For React.
	 *
	 * @access public
	 *
	 * @since    1.0.0
	 */
	public function add_setting_root_div() {
		echo '<div id="' . esc_attr( ACME_COMING_SOON_PLUGIN_NAME ) . '"></div>';
	}

	/**
	 * Register the CSS/JavaScript Resources for the admin area.
	 *
	 * @access public
	 * Use Condition to Load it Only When it is Necessary
	 *
	 * @since    1.0.0
	 */
	public function enqueue_resources() {

		$screen              = get_current_screen();
		$admin_scripts_bases = array( 'toplevel_page_' . ACME_COMING_SOON_PLUGIN_NAME );
		if ( ! ( isset( $screen->base ) && in_array( $screen->base, $admin_scripts_bases, true ) ) ) {
			return;
		}

		/* Atomic CSS */
		wp_enqueue_style( 'atomic' );

		/*Scripts dependency files*/
		$deps_file = ACME_COMING_SOON_PATH . 'build/admin/admin.asset.php';

		/*Fallback dependency array*/
		$dependency = array();
		$version    = ACME_COMING_SOON_VERSION;

		/*Set dependency and version*/
		if ( file_exists( $deps_file ) ) {
			$deps_file  = require $deps_file;
			$dependency = $deps_file['dependencies'];
			$version    = $deps_file['version'];
		}

		wp_enqueue_script( ACME_COMING_SOON_PLUGIN_NAME, ACME_COMING_SOON_URL . 'build/admin/admin.js', $dependency, $version, true );

		wp_enqueue_style( 'google-fonts-open-sans', ACME_COMING_SOON_URL . 'assets/library/fonts/open-sans.css', '', $version );
		wp_enqueue_style( ACME_COMING_SOON_PLUGIN_NAME, ACME_COMING_SOON_URL . 'build/admin/admin.css', array( 'wp-components' ), $version );

		global $wp_roles;

		/* Localize */
		$localize = apply_filters(
			'acme_coming_soon_admin_localize',
			array(
				'version'              => $version,
				'root_id'              => ACME_COMING_SOON_PLUGIN_NAME,
				'nonce'                => wp_create_nonce( 'wp_rest' ),
				'store'                => 'acme-coming-soon',
				'rest_url'             => get_rest_url(),
				'base_url'             => menu_page_url( $this->menu_info['menu_slug'], false ),
				'ACME_COMING_SOON_URL' => ACME_COMING_SOON_URL,
				'white_label'          => acme_coming_soon_include()->get_white_label(),
				'userRoles'            => $wp_roles->get_names(),
			)
		);

		wp_set_script_translations( ACME_COMING_SOON_PLUGIN_NAME, ACME_COMING_SOON_PLUGIN_NAME );
		wp_localize_script( ACME_COMING_SOON_PLUGIN_NAME, 'AcmeComingSoonLocalize', $localize );
	}

	/**
	 * Get settings schema
	 * Schema: http://json-schema.org/draft-04/schema#
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 *
	 * @return array settings schema for this plugin.
	 */
	public function get_settings_schema() {

		$setting_properties = apply_filters(
			'acme_coming_soon_setting_properties',
			array(
				'on'        => array(
					'type' => 'boolean',
				),
				'access'    => array(
					'type' => 'string',
					'enum' => array(
						'',
						'login',
						'admin',
						'roles',
					),
				),
				'exclude'   => array(
					'type'       => 'object',
					'properties' => array(
						'useRoles'  => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'searchBot' => array(
							'type' => 'boolean',
						),
						'ips'       => array(
							'type'  => 'array',
							'items' => array(
								'type'              => 'string',
								'sanitize_callback' => 'acme_coming_soon_sanitize_ip',
							),
						),
						'slugs'     => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'string',
							),
						),
					),
				),
				'template'  => array(
					'type' => 'integer',
				),
				'deleteAll' => array(
					'type' => 'boolean',
				),
			),
		);

		return array(
			'type'       => 'object',
			'properties' => $setting_properties,
		);
	}

	/**
	 * Register settings.
	 * Common callback function of rest_api_init and admin_init
	 * Schema: http://json-schema.org/draft-04/schema#
	 *
	 * Add your own settings fields here
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_settings() {
		$defaults = acme_coming_soon_default_options();

		register_setting(
			'acme_coming_soon_setting_group',
			ACME_COMING_SOON_OPTION_NAME,
			array(
				'type'         => 'object',
				'default'      => $defaults,
				'show_in_rest' => array(
					'schema' => $this->get_settings_schema(),
				),
			)
		);
	}

	/**
	 * Add plugin menu items.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 * @param string[] $actions     An array of plugin action links. By default this can include
	 *                              'activate', 'deactivate', and 'delete'. With Multisite active
	 *                              this can also include 'network_active' and 'network_only' items.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
	 *                              and the {@see 'plugin_row_meta'} filter for the list
	 *                              of possible values.
	 * @param string   $context     The plugin context. By default this can include 'all',
	 *                              'active', 'inactive', 'recently_activated', 'upgrade',
	 *                              'mustuse', 'dropins', and 'search'.
	 * @return array settings schema for this plugin.
	 */
	public function add_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions[] = '<a href="' . esc_url( menu_page_url( $this->menu_info['menu_slug'], false ) ) . '">' . esc_html__( 'Settings', 'acme-coming-soon' ) . '</a>';
		return $actions;
	}
}

if ( ! function_exists( 'acme_coming_soon_admin' ) ) {
	/**
	 * Return instance of  Acme_Coming_Soon_Admin class
	 *
	 * @since 1.0.0
	 *
	 * @return Acme_Coming_Soon_Admin
	 */
	function acme_coming_soon_admin() {
		return Acme_Coming_Soon_Admin::get_instance();
	}
}
