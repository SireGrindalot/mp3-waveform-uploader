<?php
/**
 * Plugin Name: MP3 Waveform Uploader & Annotator
 * Description: An advanced audio player with a zoomable waveform and timestamped annotations. Usage: [mp3_waveform_uploader id="unique_id"].
 * Version: 4.2.0
 * Author: Sašo Fajon
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly.

/**
 * Main plugin class.
 *
 * @final
 */
final class MP3_Waveform_Uploader {
    /**
     * Plugin Version.
     */
    const VERSION = '4.2.0';

    /**
     * Post meta key for storing audio notes.
     */
    const NOTES_META_KEY = '_mwu_audio_notes';

    /**
     * Post meta key for storing the producer's note.
     */
    const PRODUCER_NOTE_META_KEY = '_mwu_producer_note';

    /**
     * Option key for storing player instance states.
     */
    const PLAYER_STATES_OPTION_KEY = 'mwu_player_states';

    /**
     * The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Ensures only one instance of the class is loaded.
     *
     * @return MP3_Waveform_Uploader - Main instance.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define Plugin Constants.
     */
    private function define_constants() {
        if (!defined('MWU_PLUGIN_DIR')) {
            define('MWU_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }
    }

    /**
     * Include required plugin files.
     */
    private function includes() {
        require_once MWU_PLUGIN_DIR . 'includes/class-mwu-ajax-handler.php';
        require_once MWU_PLUGIN_DIR . 'includes/class-mwu-shortcode-handler.php';
    }

    /**
     * Initialize plugin hooks.
     */
    private function init_hooks() {
        new MWU_Ajax_Handler();
        new MWU_Shortcode_Handler();
    }
}

/**
 * Main instance of the plugin.
 *
 * Returns the main instance of the plugin to prevent the need to use globals.
 *
 * @return MP3_Waveform_Uploader
 */
function mwu_instance() {
    return MP3_Waveform_Uploader::instance();
}

// Global invocation.
mwu_instance();

/**
 * Adds the plugin's help page to the admin menu under "Settings".
 */
function mwu_add_admin_menu_page() {
    add_options_page(
        'MP3 Annotator Help',
        'MP3 Annotator Help',
        'manage_options',
        'mwu-help-page',
        'mwu_render_help_page_content'
    );
}
add_action('admin_menu', 'mwu_add_admin_menu_page');

/**
 * Renders the content for the admin help page.
 */
function mwu_render_help_page_content() {
    if (!current_user_can('manage_options')) {
        return;
    }
    require_once MWU_PLUGIN_DIR . 'includes/admin-help-page.php';
}

/**
 * Adds a "Help" link to the plugin's action links on the main plugins page.
 *
 * @param array $links An array of existing plugin action links.
 * @return array An array of modified plugin action links.
 */
function mwu_add_plugin_action_links($links) {
    $help_link = [
        'help' => '<a href="' . esc_url(admin_url('options-general.php?page=mwu-help-page')) . '">Help</a>'
    ];
    return array_merge($help_link, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'mwu_add_plugin_action_links');