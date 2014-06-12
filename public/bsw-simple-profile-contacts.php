<?php
/**
 * Simple Profile Contacts
 *
 * @package   BSW_SimpleProfileContacts
 * @author    Brian Watson <bswatson@gmail.com>
 * @license   GPL-2.0+
 * @link      http://bsw.io
 * @copyright 2014 Brian Watson
 */

if(!class_exists('BSW_SimpleProfileContacts')) {
	/**
	 * @package   BSW_SimpleProfileContacts
	 * @author    Brian Watson <bswatson@gmail.com>
	 */
	class BSW_SimpleProfileContacts {

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @since   0.1.0
		 *
		 * @var     string
		 */
		const VERSION = '0.1.0';

		/**
		 * The variable name is used as the text domain when internationalizing strings
		 * of text. Its value should match the Text Domain file header in the main
		 * plugin file.
		 *
		 * @since    0.1.0
		 *
		 * @var      string
		 */
		protected $plugin_slug = 'bsw-simple-profile-contacts';
		
		
		/**
		 * 
		 */
		protected $contact_methods = array();

		/**
		 * Instance of this class.
		 *
		 * @since    0.1.0
		 *
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since     0.1.0
		 */
		private function __construct() {

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Activate plugin when new blog is added
			add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

			// Load public-facing style sheet and JavaScript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			
			add_shortcode( 'bsw_simple_profile_contacts', array( $this, 'bsw_simple_profile_contacts_shortcode' ) );
		}

		/**
		 * Return the plugin slug.
		 *
		 * @since    0.1.0
		 *
		 * @return    Plugin slug variable.
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}
		
		/**
		 * Return the available contac methods.
		 *
		 * @since    0.1.0
		 *
		 * @return    Array of contact methods.
		 */
		public function get_contact_methods() {
			return $this->contact_methods;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since     0.1.0
		 *
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
				self::$instance->setup_contact_methods();
			}

			return self::$instance;
		}

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since    0.1.0
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Activate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       activated on an individual blog.
		 */
		public static function activate( $network_wide ) {

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				if ( $network_wide  ) {

					// Get all blog ids
					$blog_ids = self::get_blog_ids();

					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_activate();

						restore_current_blog();
					}

				} else {
					self::single_activate();
				}

			} else {
				self::single_activate();
			}

		}

		/**
		 * Fired when the plugin is deactivated.
		 *
		 * @since    0.1.0
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Deactivate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       deactivated on an individual blog.
		 */
		public static function deactivate( $network_wide ) {

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {

				if ( $network_wide ) {

					// Get all blog ids
					$blog_ids = self::get_blog_ids();

					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_deactivate();

						restore_current_blog();

					}

				} else {
					self::single_deactivate();
				}

			} else {
				self::single_deactivate();
			}

		}

		/**
		 * Fired when a new site is activated with a WPMU environment.
		 *
		 * @since    0.1.0
		 *
		 * @param    int    $blog_id    ID of the new blog.
		 */
		public function activate_new_site( $blog_id ) {

			if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
				return;
			}

			switch_to_blog( $blog_id );
			self::single_activate();
			restore_current_blog();

		}

		/**
		 * Get all blog ids of blogs in the current network that are:
		 * - not archived
		 * - not spam
		 * - not deleted
		 *
		 * @since    0.1.0
		 *
		 * @return   array|false    The blog ids, false if no matches.
		 */
		private static function get_blog_ids() {

			global $wpdb;

			// get an array of blog ids
			$sql = "SELECT blog_id FROM $wpdb->blogs
				WHERE archived = '0' AND spam = '0'
				AND deleted = '0'";

			return $wpdb->get_col( $sql );

		}

		/**
		 * Fired for each blog when the plugin is activated.
		 *
		 * @since    0.1.0
		 */
		private static function single_activate() {
			// @TODO: Define activation functionality here
		}

		/**
		 * Fired for each blog when the plugin is deactivated.
		 *
		 * @since    0.1.0
		 */
		private static function single_deactivate() {
			// @TODO: Define deactivation functionality here
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    0.1.0
		 */
		public function load_plugin_textdomain() {

			$domain = 'bsw-spc-locale';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}
		
		/**
		 * Setup initial contact methods.
		 * 
		 * @since    0.1.0
		 */
		public function setup_contact_methods() {
			$this->contact_methods = array(
				'twitter' => array(
					'admin_label' => esc_html__( 'Twitter', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Twitter Username (without @)', 'bsw-spc-locale' ),
					'output_prefix' => 'http://twitter.com/',
				),
				'facebook' => array(
					'admin_label' => esc_html__( 'Facebook', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Facebook Profile URL', 'bsw-spc-locale' ),
					'output_prefix' => ''
				),
				'googleplus' =>  array(
					'admin_label' => esc_html__( 'Google+', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Google+ URL', 'bsw-spc-locale' ),
					'output_prefix' => ''
				),
				'linkedin' =>  array(
					'admin_label' => esc_html__( 'LinkedIn', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'LinkedIn Public Profile URL', 'bsw-spc-locale' ),
					'output_prefix' => ''
				),
				'pinterest' =>  array(
					'admin_label' => esc_html__( 'Pinterest', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Pinterest Username', 'bsw-spc-locale' ),
					'output_prefix' => 'http://www.pinterest.com/'
				),
				'dribbble' =>  array(
					'admin_label' => esc_html__( 'Dribbble', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Dribbble Username', 'bsw-spc-locale' ),
					'output_prefix' => 'http://www.dribbble.com/'
				),
				'flickr' =>  array(
					'admin_label' => esc_html__( 'Flickr', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Flickr Profile URL', 'bsw-spc-locale' ),
					'output_prefix' => ''
				),
				'github' =>  array(
					'admin_label' => esc_html__( 'GitHub', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'GitHub Username', 'bsw-spc-locale' ),
					'output_prefix' => 'http://github.com/'
				),
				'instagram' =>  array(
					'admin_label' => esc_html__( 'Instagram', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Instagram Username', 'bsw-spc-locale' ),
					'output_prefix' => 'http://instagram.com/'
				),
				'vimeo' =>  array(
					'admin_label' => esc_html__( 'Vimeo', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Vimeo Username', 'bsw-spc-locale' ),
					'output_prefix' => 'http://vimeo.com/'
				),
				'youtube' =>  array(
					'admin_label' => esc_html__( 'Youtube', 'bsw-spc-locale' ),
					'profile_label' => esc_html__( 'Youtube Username', 'bsw-spc-locale' ),
					'output_prefix' => 'https://www.youtube.com/user/'
				)
			);
		}
		
		/** Register and enqueue public-facing style sheet.
		 *
		 * @since    0.1.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style( $this->plugin_slug . '-font-awesome', plugins_url( 'assets/css/font-awesome.min.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
		}

		/**
		 * Register and enqueues public-facing JavaScript files.
		 *
		 * @since    0.1.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		
		function bsw_simple_profile_contacts_shortcode( $atts, $content = null ) {

			global $post;

			extract( shortcode_atts( array(
				'title' => '',
			), $atts ) );

			$available_methods = get_option( 'bsw_spc_available_methods' );
			$author_contact_methods = array();
			
			foreach ($available_methods as $contact_method_name => $options) {
				$contact_method = get_option('bsw_spc_'.$contact_method_name);
				if ($contact_method && !empty(get_the_author_meta($contact_method_name))) {
					$author_contact_methods[$contact_method_name] = array(
						'options' => $options,
						'author_entry' => esc_html(get_the_author_meta($contact_method_name))
					);
				}
			}

			ob_start();

			require( 'views/public.php' );

			$content = ob_get_clean();

			return $content;

		}
	}
}