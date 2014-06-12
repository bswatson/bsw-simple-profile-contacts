<?php
/**
 * Simple Profile Contacts
 *
 * Adds additional social media fields to contact methods in author profiles.
 *
 * @package   BSW_SimpleProfileContacts
 * @author    Brian Watson <bswatson@gmail.com>
 * @license   GPL-2.0+
 * @link      http://bsw.io
 * @copyright 2014 Brian Watson
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Profile Contacts
 * Plugin URI:        http://github.com/bswatson/bsw-simple-profile-contacts
 * Description:       Adds additional social media fields to contact methods in author profiles.
 * Version:           0.1.0
 * Author:            Brian Watson
 * Author URI:        http://bsw.io
 * Text Domain:       bsw-spc-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: http://github.com/bswatson/bsw-simple-profile-contacts
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

/* Copyright 2014 Brian Watson  (email : bswatson@gmail.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( __DIR__ . '/public/bsw-simple-profile-contacts.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
if(class_exists('BSW_SimpleProfileContacts')) {
	register_activation_hook( plugins_url( '/', __FILE__ ), array( 'BSW_SimpleProfileContacts', 'activate' ) );
	register_deactivation_hook( plugins_url( '/', __FILE__ ), array( 'BSW_SimpleProfileContacts', 'deactivate' ) );

	add_action( 'plugins_loaded', array( 'BSW_SimpleProfileContacts', 'get_instance' ) );
}

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( __DIR__ . '/admin/bsw-simple-profile-contacts-admin.php' );
	add_action( 'plugins_loaded', array( 'BSW_SimpleProfileContacts_Admin', 'get_instance' ) );

}