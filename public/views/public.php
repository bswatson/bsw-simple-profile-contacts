<?php
/**
 * @package   BSW_SimpleProfileContacts
 * @author    Brian Watson <bswatson@gmail.com>
 * @license   GPL-2.0+
 * @link      http://bsw.io
 * @copyright 2014 Brian Watson
 */

 // Currently has a dependency on Font Awesome
?>

<div class="widget meta-social">
	<?php if (!empty($title)) : ?>
		<h4><?php echo $title ?></h4>
	<?php endif; ?>
	<ul class="inline center">
		<?php foreach ($author_contact_methods as $key => $data) : ?>
		<li><a href="<?php echo $data['options']['output_prefix'].$data['author_entry']; ?>" class="<?php echo $key; ?>-share border-box"><i class="fa fa-<?php echo $key; ?> fa-lg"></i></a></li>
		<?php endforeach; ?>
	</ul>
</div>