<?php

/*
Plugin Name: Gracepoint Metabox Addon
Plugin URI: https://github.com/sparkhee93/gp-post-flickr-plug
Version: 1.0
Author: Samuel Park
Description: Adds a new metabox to the post edit screen
/* License
    Gracepoint Post Flickr Plugin
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function photoset_id_post_class_meta_box($object, $box) {
	?>
	<?php 
	wp_nonce_field(basename(__FILE__), 'photoset_id_post_class_nonce'); 
	?>

	<p>
		<label for="photoset-id-post-class">
			<?php 
			_e("Add an album ID from Flickr to get all the photos from that album", 'example');
			?>
		</label>
		<br />
		<input class="widefat" type="text" name="photoset-id-post-class" id="photoset-id-post-class" 
			value="<?php echo esc_attr(get_post_meta($object->ID, 'photoset_id_post_class', true)); ?>" 
			size="16" />
	</p>

	<?php
}

function photoset_id_add_post_meta_box() {
	add_meta_box(
		'photoset-id-post-class',
		esc_html__('Album ID', 'example'),
		'photoset_id_post_class_meta_box',
		'post',
		'side',
		'default'
	);
}

function photoset_id_save_post_class_meta($post_id, $post) {
	if(!isset($_POST['photoset_id_post_class_nonce']) || !wp_verify_nonce($_POST['photoset_id_post_class_nonce'], basename(__FILE__)))
		return $post_id;

	$post_type = get_post_type_object($post->post_type);

	if(!current_user_can($post_type->cap->edit_post, $post_id))
		return $post_id;

	$new_meta_value = (isset($_POST['photoset-id-post-class']) ? sanitize_html_class ($_POST['photoset-id-post-class']) : '');

	$meta_key = 'photoset_id_post_class';

	$meta_value = get_post_meta($post_id, $meta_key, true);

	if($new_meta_value && '' == $meta_value)
		add_post_meta($post_id, $meta_key, $new_meta_value, true);

	elseif($new_meta_value && $new_meta_value != $meta_value)
		update_post_meta($post_id, $meta_key, $new_meta_value);

	elseif('' == $new_meta_value && $meta_value)
		delete_post_meta($post_id, $meta_key, $meta_value);
}


add_action('add_meta_boxes', 'photoset_id_add_post_meta_box');
add_action('save_post', 'photoset_id_save_post_class_meta', 10, 2);

?>