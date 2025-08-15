<?php
if (!defined('ABSPATH')) exit;

/**
 * Handles the [mp3_waveform_uploader] shortcode.
 */
class MWU_Shortcode_Handler {

    /**
     * The version of the WaveSurfer.js library to load.
     */
    const WAVESURFER_VERSION = '7.7.10';
    
    /**
     * Constructor.
     */
    public function __construct() {
        add_shortcode('mp3_waveform_uploader', [$this, 'render_shortcode']);
    }

    /**
     * Renders the shortcode output.
     *
     * This method handles asset enqueueing, fetching the initial state of the player
     * based on the instance ID, and passing all necessary data to the frontend JavaScript.
     *
     * @param array $atts The shortcode attributes. Expects an 'id'.
     * @return string The HTML output for the player.
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(['id' => ''], $atts, 'mp3_waveform_uploader');
        $instance_id = sanitize_key($atts['id']);

        if (empty($instance_id)) {
            return '<p><strong>MP3 Player Error:</strong> A unique "id" is required. Ex: [mp3_waveform_uploader id="player1"]</p>';
        }

        // Enqueue scripts and styles.
        wp_enqueue_style('mwu-style', plugin_dir_url(__DIR__) . 'assets/css/mwu-style.css', [], MP3_Waveform_Uploader::VERSION);
        wp_enqueue_script('wavesurfer', plugin_dir_url(__DIR__) . 'js/wavesurfer.min.js', [], self::WAVESURFER_VERSION, true);
        wp_enqueue_script('mwu-script', plugin_dir_url(__DIR__) . 'assets/js/mwu-script.js', ['wavesurfer'], MP3_Waveform_Uploader::VERSION, true);

        // Fetch the current state for this player instance.
        $player_states = get_option(MP3_Waveform_Uploader::PLAYER_STATES_OPTION_KEY, []);
        $attachment_id = $player_states[$instance_id] ?? 0;
        
        $initial_state = ['id' => null, 'url' => null, 'title' => null, 'notes' => [], 'producer_note' => ''];
        
        if ($attachment_id && ($post = get_post($attachment_id))) {
            $file_path = get_attached_file($attachment_id);
            $initial_state['id'] = $attachment_id;
            $initial_state['url'] = wp_get_attachment_url($attachment_id);
            $initial_state['title'] = $file_path ? basename($file_path) : get_the_title($attachment_id);
            $notes = get_post_meta($attachment_id, MP3_Waveform_Uploader::NOTES_META_KEY, true);
            $initial_state['notes'] = is_array($notes) ? $notes : [];
            $initial_state['producer_note'] = get_post_meta($attachment_id, MP3_Waveform_Uploader::PRODUCER_NOTE_META_KEY, true) ?: '';
        }
        
        // Pass data to the frontend script.
        $script_data = [
            'initial_state' => $initial_state,
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonces'   => [
                'upload'             => wp_create_nonce('mwu_upload_nonce'),
                'save_note'          => wp_create_nonce('mwu_save_note_nonce'),
                'cleanup'            => wp_create_nonce('mwu_cleanup_nonce'),
                'delete_note'        => wp_create_nonce('mwu_delete_note_nonce'),
                'save_producer_note' => wp_create_nonce('mwu_save_producer_note_nonce'),
            ],
            'is_admin' => current_user_can('upload_files')
        ];
        
        wp_add_inline_script(
            'mwu-script',
            'window.MWU_SETTINGS = window.MWU_SETTINGS || {}; window.MWU_SETTINGS["' . esc_js($instance_id) . '"] = ' . wp_json_encode($script_data) . ';',
            'before'
        );

        // Render the player template.
        ob_start();
        include plugin_dir_path(__DIR__) . 'templates/player-template.php';
        return ob_get_clean();
    }
}