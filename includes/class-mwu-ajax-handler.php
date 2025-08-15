<?php
if (!defined('ABSPATH')) exit;

/**
 * Handles all AJAX requests for the plugin.
 */
class MWU_Ajax_Handler {

    /**
     * Constructor. Registers all AJAX hooks.
     */
    public function __construct() {
        // Hooks for logged-in users
        add_action('wp_ajax_mwu_upload', [$this, 'handle_upload']);
        add_action('wp_ajax_mwu_save_note', [$this, 'save_audio_note']);
        add_action('wp_ajax_mwu_cleanup_instance', [$this, 'handle_cleanup_instance']);
        add_action('wp_ajax_mwu_delete_note', [$this, 'handle_delete_note']);
        add_action('wp_ajax_mwu_save_producer_note', [$this, 'handle_save_producer_note']);

        // Hooks for visitors
        add_action('wp_ajax_nopriv_mwu_save_note', [$this, 'save_audio_note']);
        add_action('wp_ajax_nopriv_mwu_delete_note', [$this, 'handle_delete_note']);
    }

    /**
     * Handles MP3 file uploads.
     */
    public function handle_upload() {
        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'You do not have permission to upload files.']);
        }
        check_ajax_referer('mwu_upload_nonce', 'nonce');
        if (empty($_POST['instance_id']) || empty($_FILES['mp3File'])) {
            wp_send_json_error(['message' => 'Missing required data.']);
        }

        $instance_id = sanitize_key($_POST['instance_id']);
        $file = $_FILES['mp3File'];

        if (!preg_match('/\\.mp3$/i', $file['name']) && !preg_match('/audio\\/mpeg/i', $file['type'])) {
            wp_send_json_error(['message' => 'Only MP3 files are accepted.']);
        }
        
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        
        $movefile = wp_handle_upload($file, ['test_form' => false, 'mimes' => ['mp3' => 'audio/mpeg']]);
        if (!$movefile || isset($movefile['error'])) {
            wp_send_json_error(['message' => $movefile['error'] ?? 'Upload failed.']);
        }
        
        $attachment = [
            'guid'           => $movefile['url'],
            'post_mime_type' => $movefile['type'],
            'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];
        $attach_id = wp_insert_attachment($attachment, $movefile['file']);
        if (is_wp_error($attach_id)) {
            wp_send_json_error(['message' => 'Failed to create attachment.']);
        }
        
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $movefile['file']));
        
        $player_states = get_option(MP3_Waveform_Uploader::PLAYER_STATES_OPTION_KEY, []);
        $player_states[$instance_id] = $attach_id;
        update_option(MP3_Waveform_Uploader::PLAYER_STATES_OPTION_KEY, $player_states);
        
        wp_send_json_success([
            'id'    => $attach_id,
            'url'   => $movefile['url'],
            'title' => basename($movefile['file'])
        ]);
    }

    /**
     * Saves a new audio note and sends an admin notification.
     */
    public function save_audio_note() {
        check_ajax_referer('mwu_save_note_nonce', 'nonce');
        if (empty($_POST['attachment_id']) || !isset($_POST['timestamp']) || empty($_POST['handle']) || empty($_POST['comment'])) {
            wp_send_json_error(['message' => 'Missing required data.']);
        }

        $attachment_id = intval($_POST['attachment_id']);
        $notes = get_post_meta($attachment_id, MP3_Waveform_Uploader::NOTES_META_KEY, true);
        if (!is_array($notes)) {
            $notes = [];
        }
        
        $new_note = [
            'id'        => uniqid('note_'),
            'timestamp' => floatval($_POST['timestamp']),
            'handle'    => sanitize_text_field($_POST['handle']),
            'comment'   => sanitize_textarea_field($_POST['comment']),
            'date'      => current_time('mysql'),
        ];
        $notes[] = $new_note;
        
        usort($notes, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);
        update_post_meta($attachment_id, MP3_Waveform_Uploader::NOTES_META_KEY, $notes);

        // Send an email notification to the site administrator.
        $admin_email = get_option('admin_email');
        if ($admin_email) {
            $attachment_post = get_post($attachment_id);
            $track_title = $attachment_post ? $attachment_post->post_title : 'Unknown Track';
            $edit_link = get_edit_post_link($attachment_id, 'raw');
            $timestamp_seconds = (int) $new_note['timestamp'];
            $timestamp_formatted = sprintf('%02d:%02d:%02d', ($timestamp_seconds/3600), ($timestamp_seconds/60%60), $timestamp_seconds%60);

            $subject = '[' . get_bloginfo('name') . '] New Comment on: ' . $track_title;
            $message  = "A new comment has been posted on an audio track.\n\n";
            $message .= "Track: " . $track_title . "\n";
            $message .= "Author: " . $new_note['handle'] . "\n";
            $message .= "Timestamp: " . $timestamp_formatted . "\n";
            $message .= "Comment: \n" . $new_note['comment'] . "\n\n";
            $message .= "You can manage the track and its comments here:\n" . $edit_link . "\n";

            wp_mail($admin_email, $subject, $message);
        }

        wp_send_json_success(['notes' => $notes]);
    }

    /**
     * Deletes a single audio note.
     */
    public function handle_delete_note() {
        check_ajax_referer('mwu_delete_note_nonce', 'nonce');
        if (empty($_POST['attachment_id']) || empty($_POST['note_id'])) {
            wp_send_json_error(['message' => 'Missing required data.']);
        }

        $attachment_id = intval($_POST['attachment_id']);
        $note_id_to_delete = sanitize_text_field($_POST['note_id']);
        $notes = get_post_meta($attachment_id, MP3_Waveform_Uploader::NOTES_META_KEY, true);

        if (!is_array($notes) || empty($notes)) {
            wp_send_json_success(['notes' => []]);
        }

        $updated_notes = array_filter($notes, fn($note) => isset($note['id']) && $note['id'] !== $note_id_to_delete);
        $updated_notes = array_values($updated_notes);
        
        update_post_meta($attachment_id, MP3_Waveform_Uploader::NOTES_META_KEY, $updated_notes);
        wp_send_json_success(['notes' => $updated_notes]);
    }

    /**
     * Clears a player instance. Conditionally deletes the associated audio file.
     */
    public function handle_cleanup_instance() {
        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'You do not have permission to perform this action.']);
        }
        check_ajax_referer('mwu_cleanup_nonce', 'nonce');
        if (empty($_POST['instance_id']) || empty($_POST['attachment_id'])) {
            wp_send_json_error(['message' => 'Missing required data for cleanup.']);
        }

        $instance_id = sanitize_key($_POST['instance_id']);
        $attachment_id = intval($_POST['attachment_id']);
        
        // Conditionally delete the attachment if requested.
        if (isset($_POST['delete_file']) && $_POST['delete_file'] === 'true') {
            wp_delete_attachment($attachment_id, true);
        } else {
            // Otherwise, just delete the comment metadata.
            delete_post_meta($attachment_id, MP3_Waveform_Uploader::NOTES_META_KEY);
        }
        
        // Always remove the instance from the player states.
        $player_states = get_option(MP3_Waveform_Uploader::PLAYER_STATES_OPTION_KEY, []);
        unset($player_states[$instance_id]);
        update_option(MP3_Waveform_Uploader::PLAYER_STATES_OPTION_KEY, $player_states);
        
        wp_send_json_success(['message' => 'Instance cleaned up successfully.']);
    }

    /**
     * Saves the producer's note for a track.
     */
    public function handle_save_producer_note() {
        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'You do not have permission to save this note.']);
        }
        check_ajax_referer('mwu_save_producer_note_nonce', 'nonce');
        if (!isset($_POST['attachment_id']) || !isset($_POST['note_text'])) {
            wp_send_json_error(['message' => 'Missing required data.']);
        }

        $attachment_id = intval($_POST['attachment_id']);
        $note_text = sanitize_textarea_field($_POST['note_text']);

        update_post_meta($attachment_id, MP3_Waveform_Uploader::PRODUCER_NOTE_META_KEY, $note_text);

        wp_send_json_success(['note_text' => $note_text]);
    }
}