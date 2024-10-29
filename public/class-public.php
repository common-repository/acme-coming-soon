<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.acmeit.org/
 * @since      1.0.0
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Check for coming soon condition.
 *
 * @package    Acme_Coming_Soon
 * @subpackage Acme_Coming_Soon/public
 * @author     codersantosh <codersantosh@gmail.com>
 */
class Acme_Coming_Soon_Public {

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
	 * Check if the request is from a bot
	 *
	 * @access public
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_search_bot() {

		// Check if the user agent is set and not empty.
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {

			$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );

			$bots = apply_filters(
				'acme_coming_soon_search_bots',
				array(
					'AbachoBOT',
					'Slurp',
					'Spider',
					'Acoon',
					'AcoiRobot',
					'Bot',
					'adidxbot',
					'facebookexternalhit',
					'Twitterbot',
					'Altavista',
					'Scooter',
					'ASPSeek',
					'Atomz',
					'bingbot',
					'Crawler',
					'BingPreview',
					'CrocCrawler',
					'Dumbot',
					'developers',
					'eStyle',
					'FAST-WebCrawler',
					'GeonaBot',
					'Gigabot',
					'Googlebot',
					'IDBot',
					'Lycos',
					'msnbot',
					'MSRBOT',
					'Rambler',
					'Scrubby',
					'Yahoo',
					'DuckDuckGoBot',
					'YandexBot',
					'Baiduspider',
					'Exabot',
					'SeznamBot',
					'Sogou',
				)
			);

			// Sanitize user agent for use in regular expression.
			$user_agent = preg_quote( $user_agent, '~' );

			// Create a case-insensitive regular expression pattern.
			$pattern = '~(' . implode( '|', $bots ) . ')~i';

			// Check if the user agent matches any of the bot patterns.
			$is_search_bot = (bool) preg_match( $pattern, $user_agent );
		}

		return apply_filters(
			'acme_coming_soon_is_search_bot',
			$is_search_bot
		);
	}

	/**
	 * Check exclude search bot to pass or redirect
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return boolean
	 */
	public function pass_search_bot() {
		$settings        = acme_coming_soon_include()->get_settings();
		$pass_search_bot = false;
		if ( isset( $settings['exclude']['passSearchBot'] ) && $settings['exclude']['passSearchBot'] && $this->is_search_bot() ) {
			$pass_search_bot = true;
		}
		return $pass_search_bot;

	}

	/**
	 * Check exclude ip address to pass or redirect
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return boolean
	 */
	public function pass_ip() {
		$settings = acme_coming_soon_include()->get_settings();
		$pass_ip  = false;

		if ( isset( $settings['exclude']['ips'] ) && $settings['exclude']['ips'] && isset( $_SERVER['REMOTE_ADDR'] ) && ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$excluded_ips = $settings['exclude']['ips'];

			// acme_coming_soon_sanitize_ip is sanitization function for IP address.
			// phpcs:ignore
			$remote_address = acme_coming_soon_sanitize_ip( $_SERVER['REMOTE_ADDR'] );
			foreach ( $excluded_ips as $ip ) {
				if ( ! $ip ) {
					continue;
				}

				if ( strstr( $remote_address, $ip ) ) {
					$pass_ip = true;
					break;
				}
			}
		}

		return $pass_ip;
	}

	/**
	 * Check exclude slug to pass or redirect
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return boolean
	 */
	public function pass_slug() {
		$settings  = acme_coming_soon_include()->get_settings();
		$pass_slug = false;

		if ( isset( $settings['exclude']['slugs'] ) && $settings['exclude']['slugs'] && isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$excluded_slugs = $settings['exclude']['slugs'];
			$request_uri    = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			foreach ( $excluded_slugs as $slug ) {
				if ( ! $slug ) {
					continue;
				}

				if ( strstr( $request_uri, $slug ) ) {
					$pass_slug = true;
					break;
				}
			}
		}

		return $pass_slug;
	}

	/**
	 * Check exclude user role to pass or redirect
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return boolean
	 */
	public function pass_user_role() {
		$settings = acme_coming_soon_include()->get_settings();

		$access = $settings['access'];

		if ( ! $access || ! is_user_logged_in() ) {
			return false;
		}
		if ( 'admin' === $access && current_user_can( 'administrator' ) ) {
			return true;
		}
		if ( 'login' === $access ) {
			return true;
		}

		if ( 'roles' === $access ) {
			$allowed_roles = isset( $settings['exclude']['useRoles'] ) && $settings['exclude']['useRoles'] ? $settings['exclude']['useRoles'] : array();
			$current_user  = wp_get_current_user();
			if ( $allowed_roles && array_intersect( $allowed_roles, $current_user->roles ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Coming Soon (Maintenance) request
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return void
	 */
	public function redirect_to_maintenance() {
		$settings = acme_coming_soon_include()->get_settings();

		/* Ensure coming soon is on, not during cron, and meet exclusion criteria for user role, search bot, ip and slug */
		if ( $settings['on'] && ! wp_doing_cron() && ! $this->pass_user_role() && ! $this->pass_search_bot() && ! $this->pass_ip() && ! $this->pass_slug() ) {
			$access = $settings['access'];

			$template = absint( $settings['template'] );

			if ( $template && get_the_ID() !== $template ) {
				wp_safe_redirect( get_the_permalink( $template ) );
				exit;
			}
		}
	}
}

if ( ! function_exists( 'acme_coming_soon_public' ) ) {
	/**
	 * Return instance of  Acme_Coming_Soon_Public class
	 *
	 * @since 1.0.0
	 *
	 * @return Acme_Coming_Soon_Public
	 */
	function acme_coming_soon_public() {
		return Acme_Coming_Soon_Public::get_instance();
	}
}
