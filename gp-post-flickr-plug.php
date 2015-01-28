<?php

/*
Plugin Name: Gracepoint Post Flickr Plugin
Plugin URI: https://github.com/sparkhee93/gp-post-flickr-plug
Version: 1.0
Author: Samuel Park
Description: Adds a flickr set pictures to a post
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

/********************** META BOX ******************************************/
function gp_post_flickr_meta_box_callback($post) {
	    wp_nonce_field(basename(__FILE__), 'gp_post_flickr_meta_box_nonce'); 
	    $value = get_post_meta( $post->ID, '_gp_post_flickr_meta_photoset_key', true );


	    echo '<label for="photoset-id-post-class">';
		_e("Add photoset ID from Flickr to get all the photos from that set", '12345678');
		echo '</label>';
		echo '<input type="text" id="gp_post_flickr_field_set_id" name="gp_post_flickr_field_set_id" value="' . esc_attr( $value ) . '" size="25" />';
}

function gp_post_flickr_add_meta_box() {
	add_meta_box(
		'gp_post_flickr_meta_box_id',
		esc_html__('Flickr Set ID', '1234567'),
		'gp_post_flickr_meta_box_callback',
		'post',
		'side',
		'default'
	);
}

function gp_post_flickr_save_post_class_meta($post_id) {
    
    // Check nonce is set
	if(!isset($_POST['gp_post_flickr_meta_box_nonce']) {
	    return;
	}
	
	// Verify nonce is valid
	if(!wp_verify_nonce($_POST['gp_post_flickr_meta_box_nonce'], basename(__FILE__))) {}
		return;
	}

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

	if(!current_user_can('edit_post', $post_id))
		return;
	}

    // Make sure that it is set.
    if ( ! isset( $_POST['gp_post_flickr_field_set_id'] ) ) {
    	return;
    }

    // Sanitize user input.
    $photoset_id = sanitize_text_field( $_POST['gp_post_flickr_field_set_id'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_gp_post_flickr_meta_photoset_key', $photoset_id );
}


add_action('add_meta_boxes', 'gp_post_flickr_add_meta_box');
add_action('save_post', 'gp_post_flickr_save_post_class_meta', 10, 2);


/******************* SETTINGS PAGE **************************************/

function gp_post_flickr_add_settings_page() {
    add_options_page('GP Post Flickr Plugin', 'GP Post Flickr Plugin', 'manage_options', __FILE__, 'gp_post_flickr_settings_page');
}

function gp_post_flickr_settings_page() {
    $gp_post_flick_plug_options = get_option("gp-post-flickr-plug-settings");

    if(isset($_POST['info_update'])) {
        $gp_post_flickr_plug_api_key = $_POST['gp-post-flickr-plug-api_key'];
        $gp_post_flickr_plug_user_id = $_POST['gp-post-flickr-plug-user_id'];

        $gp_post_flickr_plug_options['api_key'] = $gp_post_flickr_plug_api_key;
        $gp_post_flickr_plug_options['user_id'] = $gp_post_flickr_plug_user_id;

        update_option( 'gp-post-flickr-plug-settings', $gp_post_flickr_plug_options );
    }

    ?>

    <div class="wrap">
        <h2>GP Post Flickr Plugin</h2>
            <form method="post" action="options-general.php?page=gp-post-flickr-plug/gp-post-flickr-settings.php" id="gp-post-flickr-plug-settings">
            <h3>Gracepoint Flickr Settings</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Flickr API Key</th>
                    <td><input type="text" name="gp-post-flickr-plug-api_key" value="<?php echo $gp_post_flickr_plug_options['api_key'];?>"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Flickr User ID</th>
                    <td><input type="text" name="gp-post-flickr-plug-user_id" value="<?php echo $gp_post_flickr_plug_options['user_id'];?>"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="info_update" class="button-primary" value="Save" />
            </p>
            </form>
        </div>
    <?php
}

function gp_post_flickr_register_mysettings() {
    register_setting( 'gp-post-flickr-plug-settings-group', 'gp-post-flickr-plug-api_key' );
}

function gp_post_flickr_plug_settings_link($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="options-general.php?page=gp-post-flickr-plug/gp-post-flickr-settings.php">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
 
    return $links;
}

add_filter('plugin_action_links', 'gp_post_flickr_plug_settings_link', 10, 2);
add_action('admin_menu', 'gp_post_flickr_add_settings_page');
add_action('admin_init', 'gp_post_flickr_register_mysettings');

/**************************** PLUGIN ****************************************/

function gp_post_flickr_plug() {
	$settings_options = get_option('gp-post-flickr-settings');
	$api_key = $settings_options['api_key'];
	$user_id = $settings_options['user_id'];

	$meta_key = 'photoset_id_post_class';
	$meta_value = get_post_meta($post_id, $meta_key, true);

	require('phpflickr-master/phpFlickr.php');
	$phpFlickr = new phpFlickr($api_key);

	$photos = $phpFlickr->photosets_getPhotos($api_key, $meta_value);

    $html = "<div id='gp-post-flickr-plug'>";
    // $photos = $photos_array['photo'];
    foreach ($photos as $photo) {
        $photo_url = flickr_photo_to_image_url($photo);
        $link_url = flickr_photo_to_link_url($photo, $user_id);
        $html .= url_to_html($photo_url, $link_url);
    }
    $html .= "</div>";

    return $html;
}

function url_to_html($photo_url, $link_url) {
	$html = "<a href=$link_url target='_blank'>" . "<img src=$photo_url />" . "</a>";
	return $html;
}
$post_id = get_the_ID();
$post = get_post($post_id);
$post_content = $post['post_content'];

$updated_post = array(
	'ID'			=> $post_id,
	'post_content'	=> $post_content . gp_post_flickr_plug()
	);

//wp_update_post($updated_post);

?>