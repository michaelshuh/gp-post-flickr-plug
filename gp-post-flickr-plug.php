<?php
/*
Plugin Name: Gracepoint Post Flickr Plugin
Plugin URI: https://github.com/sparkhee93/gp-post-flickr-plug
Version: 1.0
Author: Samuel Park
Description: Allows set of photos to to be pulled into post from Flickr
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

function gp_post_flickr_add_settings_page() {
    add_options_page('GP Post Flickr Plugin', 'GP Post Flickr Plugin', 'manage_options', __FILE__, 'gp_post_flickr_settings_page');
}

function gp_post_flickr_settings_page() {
    $gp_post_flick_plug_options = get_option("gp-post-flickr-plug-settings");

    if(isset($_POST['info_update'])) {
        $gp_post_flickr_plug_api_key = $_POST['gp-post-flickr-plug-api_key'];

        $gp_post_flickr_plug_options['api_key'] = $gp_post_flickr_plug_api_key;

        update_option( 'gp-post-flickr-plug-settings', $gp_post_flickr_plug_options );
    }

    ?>

    <div class="wrap">
        <h2>GP Post Flickr Plugin</h2>
            <form method="post" action="options-general.php?page=gp-post-flickr-plug/gp-post-flickr-plug.php" id="gp-post-flickr-plug-settings">
            <h3>Gracepoint Flickr Settings</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Flickr API Key</th>
                    <td><input type="text" name="gp-post-flickr-plug-api_key" value="<?php echo $gp_post_flickr_plug_options['api_key'];?>"></td>
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
        $settings_link = '<a href="options-general.php?page=gp-post-flickr-plug/gp-post-flickr-plug.php">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
 
    return $links;
}

add_filter('plugin_action_links', 'gp_post_flickr_plug_settings_link', 10, 2);
add_action('admin_menu', 'gp_post_flickr_add_settings_page');
add_action('admin_init', 'gp_post_flickr_register_mysettings');

?>