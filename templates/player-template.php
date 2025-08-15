<?php
if (!defined('ABSPATH')) exit;
?>
<div class="mwu-wrap" data-mwu-instance="<?php echo esc_attr($instance_id); ?>">
    <div class="mwu-header">
        <?php if (current_user_can('upload_files')): ?>
            <label class="mwu-file-label" title="Upload a new MP3 file">
                <input type="file" data-mwu="file" accept="audio/mpeg,.mp3" class="mwu-file-input" />
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            </label>
            <button class="mwu-cleanup-btn" title="Clear player and delete all comments" disabled>&times;</button>
        <?php endif; ?>
        <h1 data-mwu="title"></h1>
    </div>
    <div class="mwu-row mwu-player-controls">
        <button type="button" data-mwu="play" disabled></button>
        <div class="mwu-time-wrapper">
            <span data-mwu="time">0:00:00</span>
            <button class="mwu-add-note-btn" title="Add comment at current time" disabled>+</button>
        </div>
        <progress data-mwu="progress" value="0" max="100" style="flex-grow:1"></progress>
    </div>
    <div class="mwu-status"></div>
    <div class="mwu-waveform"></div>
    <div class="mwu-zoom-controls">
        <button class="mwu-zoom-btn" data-zoom="out" title="Zoom Out"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14zM7 9h5v1H7z"/></svg></button>
        <button class="mwu-zoom-btn" data-zoom="in" title="Zoom In"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M10 9h-1V8h1V7h1v1h1v1h-1v1h-1z"/></svg></button>
    </div>
    <div class="mwu-producer-note-container">
        <h3>Producer's Note</h3>
        <div class="mwu-producer-note-display"></div>
        <?php if (current_user_can('upload_files')): ?>
            <div class="mwu-producer-note-edit">
                <textarea class="mwu-producer-note-textarea" rows="3" placeholder="Add a general note for this track..."></textarea>
                <div class="mwu-producer-note-buttons">
                    <button type="button" class="mwu-producer-note-cancel">Cancel</button>
                    <button type="button" class="mwu-producer-note-save">Save Note</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="mwu-notes-container mwu-notes-collapsed">
        <h3 class="mwu-notes-toggle"><span class="mwu-notes-icon"></span>Comments</h3>
        <div class="mwu-notes-list"></div>
    </div>
    <div class="mwu-modal-overlay mwu-comment-modal">
        <div class="mwu-modal-content">
            <h3 class="mwu-modal-title">Add Comment at 0:00:00</h3>
            <form class="mwu-note-form">
                <input type="hidden" class="mwu-note-timestamp" name="timestamp" value="0">
                <div>
                    <label for="mwu-note-handle-<?php echo esc_attr($instance_id); ?>">Your Handle/Nickname</label>
                    <input type="text" class="mwu-note-handle" id="mwu-note-handle-<?php echo esc_attr($instance_id); ?>" name="handle" required>
                </div>
                <div>
                    <label for="mwu-note-comment-<?php echo esc_attr($instance_id); ?>">Comment</label>
                    <textarea class="mwu-note-comment" id="mwu-note-comment-<?php echo esc_attr($instance_id); ?>" name="comment" rows="4" required></textarea>
                </div>
                <div class="mwu-modal-buttons">
                    <button type="button" class="cancel">Cancel</button>
                    <button type="submit" class="save">Save Comment</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="mwu-modal-overlay mwu-cleanup-modal">
        <div class="mwu-modal-content">
            <h3>Confirm Action</h3>
            <p>You are about to clear this player. Do you also want to permanently delete the associated audio file from the Media Library?</p>
            <div class="mwu-modal-buttons">
                <button type="button" class="mwu-cleanup-cancel">Cancel</button>
                <button type="button" class="mwu-cleanup-confirm-no">No, Just Clear Player</button>
                <button type="button" class="mwu-cleanup-confirm-yes">Yes, Delete File</button>
            </div>
        </div>
    </div>
</div>