<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   BSW_SimpleProfileContacts
 * @author    Brian Watson <bswatson@gmail.com>
 * @license   GPL-2.0+
 * @link      http://bsw.io
 * @copyright 2014 Brian Watson
 */
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php"> 
		<?php @settings_fields('bsw_simple_profile_contacts'); ?>
		<?php @do_settings_fields('bsw_simple_profile_contacts'); ?>
		
		<?php @do_settings_sections(BSW_SimpleProfileContacts::get_instance()->get_plugin_slug()); ?>
		
		<?php @submit_button(); ?>
	</form>
</div>