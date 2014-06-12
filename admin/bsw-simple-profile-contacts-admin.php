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

if(!class_exists('BSW_SimpleProfileContacts_Admin')) {
	/**
	 * @package   BSW_SimpleProfileContacts
	 * @author    Brian Watson <bswatson@gmail.com>
	 */
	class BSW_SimpleProfileContacts_Admin {
		/**
		 * Instance of this class.
		 *
		 * @since    0.1.0
		 *
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    0.1.0
		 *
		 * @var      string
		 */
		protected $plugin_screen_hook_suffix = null;
		
		/**
		 * Array of available Contact Methods
		 */
		protected $contact_methods = array();
		
		/**
		 * Initialize the plugin by loading admin scripts & styles and adding a
		 * settings page and menu.
		 *
		 * @since     0.1.0
		 */
		private function __construct() {
			
			// Call $plugin_slug from public plugin class.
			$plugin = BSW_SimpleProfileContacts::get_instance();
			$this->plugin_slug = $plugin->get_plugin_slug();
			
			// Setup list of contact methods
			$this->contact_methods = $plugin->get_contact_methods();

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'init_settings'));

			// Add an action link pointing to the options page.
			$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

			// Priority Set Lower to allow plugins to override these methods
			// Example: Wordpress SEO expects these to be available
			add_filter( 'user_contactmethods', array( $this, 'update_contactmethods' ), 5, 1 );
			
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
			}

			return self::$instance;
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 *
		 * @since     0.1.0
		 *
		 * @return    null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_styles() {

			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), BSW_SimpleProfileContacts::VERSION );
			}

		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @since     0.1.0
		 *
		 * @return    null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_scripts() {

			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), BSW_SimpleProfileContacts::VERSION );
			}

		}

		/**
		 * Register the administration menu for this plugin into the WordPress Dashboard menu.
		 *
		 * @since    0.1.0
		 */
		public function add_plugin_admin_menu() {

			/*
			 * Add a settings page for this plugin to the Settings menu.
			 */
			$this->plugin_screen_hook_suffix = add_options_page(
				esc_html__( 'Profile Contacts Settings', 'bsw-spc-locale' ), 
				esc_html__( 'Profile Contacts', 'bsw-spc-locale' ),
				'manage_options',
				$this->plugin_slug,
				array( $this, 'display_plugin_admin_page' )
			);

		}
		
		/**
		 * Initalize and register settings
		 */
		public function init_settings() {
			
			register_setting('bsw_simple_profile_contacts', 'contact_methods');
			add_settings_section( 
				'bsw_spc_social', 
				esc_html__( 'Social Media', 'bsw-spc-locale' ), 
				array( $this, 'display_social_media_section' ), 
				$this->plugin_slug
			);
			
			// TODO: Create Custom Post Types of Contact Methods
			update_option( 'bsw_spc_available_methods', $this->contact_methods );
			
			foreach ( $this->contact_methods as $contact_method_id => $contact_method_labels ) {
				add_settings_field( 
					'bsw_spc_social' . $contact_method_id, 
					$contact_method_labels['admin_label'],
					array( $this, 'add_social_media_checkbox' ),
					$this->plugin_slug, 
					'bsw_spc_social',
					array(
						'id' => 'bsw_spc_' . $contact_method_id
					)
				);
				register_setting('bsw_simple_profile_contacts', 'bsw_spc_' . $contact_method_id);
			}
		}

		/**
		 * Render the settings page for this plugin.
		 *
		 * @since    0.1.0
		 */
		public function display_plugin_admin_page() {
			if( !current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have suggicient permissions to access this page.' );
			}
			
			include_once( 'views/admin.php' );
		}
		
		/**
		 * Render Social Media header.
		 *
		 * @since    0.1.0
		 */
		public function display_social_media_section() { 
		?>
			<p><?php esc_html_e( 'Select the social media accounts to display on user profile pages', 'bsw-spc-locale' ); ?></p>
		<?php
		}
		
		public function add_social_media_checkbox(array $args) {
		?>
			<input type="checkbox" id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>" value="1" <?php checked( get_option($args['id']), 1 ); ?> />
		<?php
		}

		/**
		 * Add settings action link to the plugins page.
		 *
		 * @since    0.1.0
		 */
		public function add_action_links( $links ) {

			return array_merge(
				array(
					'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'bsw-spc-locale' ) . '</a>'
				),
				$links
			);
		}
		
		/**
		 * Filter the $contactmethods array, adding enabled fields to profile.
		 *
		 * @param	array	$contactmethods currently set contactmethods.
		 * @return	array	$contactmethods with added contactmethods.
		 */
		function update_contactmethods( $contactmethods ) {
			
			$available_methods = get_option('bsw_spc_available_methods');
			
			foreach ($available_methods as $contact_method_name => $labels) {
				$contact_method = get_option('bsw_spc_'.$contact_method_name);
				if ($contact_method) {
					$contactmethods[$contact_method_name] = $labels['profile_label'];
				}
			}
			return $contactmethods;
		}
	}
}