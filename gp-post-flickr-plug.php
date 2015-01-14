<?php

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

wp_update_post($updated_post);

?>