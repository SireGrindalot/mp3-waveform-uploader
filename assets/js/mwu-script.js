document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.querySelectorAll('.mwu-wrap[data-mwu-instance]').forEach(initPlayerInstance);
    }, 1);
});

/**
 * Initializes a single player instance.
 * @param {HTMLElement} instanceWrapper The main wrapper element for this player instance.
 */
function initPlayerInstance(instanceWrapper) {
    const instanceId = instanceWrapper.dataset.mwuInstance;
    if (!instanceId || !window.MWU_SETTINGS || !window.MWU_SETTINGS[instanceId]) {
        console.error("Could not initialize player: missing settings for instance", instanceId);
        return;
    }
    
    const settings = window.MWU_SETTINGS[instanceId];

    const elements = {
        fileInput: instanceWrapper.querySelector('[data-mwu="file"]'),
        titleEl: instanceWrapper.querySelector('[data-mwu="title"]'),
        statusEl: instanceWrapper.querySelector('.mwu-status'),
        waveformEl: instanceWrapper.querySelector('.mwu-waveform'),
        playBtn: instanceWrapper.querySelector('[data-mwu="play"]'),
        timeEl: instanceWrapper.querySelector('[data-mwu="time"]'),
        progressEl: instanceWrapper.querySelector('[data-mwu="progress"]'),
        zoomInBtn: instanceWrapper.querySelector('[data-zoom="in"]'),
        zoomOutBtn: instanceWrapper.querySelector('[data-zoom="out"]'),
        addNoteBtn: instanceWrapper.querySelector('.mwu-add-note-btn'),
        notesList: instanceWrapper.querySelector('.mwu-notes-list'),
        commentModal: instanceWrapper.querySelector('.mwu-comment-modal'),
        noteForm: instanceWrapper.querySelector('.mwu-note-form'),
        timestampInput: instanceWrapper.querySelector('.mwu-note-timestamp'),
        handleInput: instanceWrapper.querySelector('.mwu-note-handle'),
        commentInput: instanceWrapper.querySelector('.mwu-note-comment'),
        cleanupBtn: instanceWrapper.querySelector('.mwu-cleanup-btn'),
        notesToggle: instanceWrapper.querySelector('.mwu-notes-toggle'),
        producerNoteContainer: instanceWrapper.querySelector('.mwu-producer-note-container'),
        producerNoteDisplay: instanceWrapper.querySelector('.mwu-producer-note-display'),
        producerNoteEdit: instanceWrapper.querySelector('.mwu-producer-note-edit'),
        producerNoteTextarea: instanceWrapper.querySelector('.mwu-producer-note-textarea'),
        producerNoteSaveBtn: instanceWrapper.querySelector('.mwu-producer-note-save'),
        producerNoteCancelBtn: instanceWrapper.querySelector('.mwu-producer-note-cancel'),
        cleanupModal: instanceWrapper.querySelector('.mwu-cleanup-modal'),
        cleanupCancelBtn: instanceWrapper.querySelector('.mwu-cleanup-cancel'),
        cleanupConfirmNoBtn: instanceWrapper.querySelector('.mwu-cleanup-confirm-no'),
        cleanupConfirmYesBtn: instanceWrapper.querySelector('.mwu-cleanup-confirm-yes'),
    };

    let wavesurfer = null;
    let currentAttachmentId = null;
    let lastStartPosition = 0;
    let currentPxPerSec = 50;
    let minPxPerSec = 20;
    const ZOOM_STEP = 25;
    let lastClickedNoteTimestamp = null;

    const playIcon = `<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>`;
    const stopIcon = `<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h12v12H6z"></path></svg>`;

    const formatTime = (seconds, forceHours = false) => {
        if (isNaN(seconds)) seconds = 0;
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);

        const parts = [];
        if (forceHours || hours > 0) {
            parts.push(hours.toString().padStart(2, '0'));
        }
        parts.push(minutes.toString().padStart(2, '0'));
        parts.push(secs.toString().padStart(2, '0'));
        
        return parts.join(':');
    };
    
    const formatNoteDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString.replace(' ', 'T') + 'Z');
        return date.toLocaleString('sl-SI', { day: 'numeric', month: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    };

    const setStatus = (msg = '', isError = false) => {
        if(elements.statusEl) {
            elements.statusEl.textContent = msg;
            elements.statusEl.style.color = isError ? '#b00020' : '';
        }
    };

    /**
     * Loads a track into the player and initializes WaveSurfer.
     * @param {object} state The initial state object for the track.
     */
    function loadPlayerState(state) {
        setStatus('Loading...');
        currentAttachmentId = state.id;
        if(elements.titleEl) elements.titleEl.textContent = state.title;
        renderProducerNote(state.producer_note);
        
        if(elements.playBtn) {
            elements.playBtn.disabled = true;
            elements.playBtn.innerHTML = playIcon;
        }
        
        if (wavesurfer) wavesurfer.destroy();
        
        wavesurfer = WaveSurfer.create({
            container: elements.waveformEl,
            waveColor: '#c0c0c0',
            progressColor: '#0073aa',
            height: 120,
            normalize: true,
            dragToSeek: true,
        });

        wavesurfer.on('loading', (percent) => { if(elements.progressEl) elements.progressEl.value = percent; });
        wavesurfer.on('ready', () => {
            setStatus('Ready.');
            if(elements.playBtn) elements.playBtn.disabled = false;
            if(elements.addNoteBtn) elements.addNoteBtn.disabled = false;
            if (elements.cleanupBtn) elements.cleanupBtn.disabled = false;
            if(elements.progressEl) elements.progressEl.style.display = 'none';
            
            const waveformWidth = elements.waveformEl.clientWidth;
            minPxPerSec = waveformWidth / wavesurfer.getDuration();
            currentPxPerSec = minPxPerSec; 
            
            wavesurfer.zoom(currentPxPerSec);
            renderNotes(state.notes || []);
        });
        wavesurfer.on('timeupdate', (currentTime) => { 
            if(elements.timeEl) elements.timeEl.textContent = formatTime(currentTime, wavesurfer.getDuration() >= 3600); 
        });
        wavesurfer.on('seek', (progress) => { lastStartPosition = progress * wavesurfer.getDuration(); });
        wavesurfer.on('finish', () => {
            if(elements.playBtn) elements.playBtn.innerHTML = playIcon;
            if (wavesurfer.getDuration() > 0) wavesurfer.seekTo(lastStartPosition / wavesurfer.getDuration());
            lastClickedNoteTimestamp = null;
        });
        wavesurfer.load(state.url);
    }

    /**
     * Resets the player to its initial, empty state.
     */
    function resetPlayer() {
        setStatus('Upload an MP3 to begin.');
        if(elements.titleEl) elements.titleEl.textContent = 'No file loaded';
        if(elements.playBtn) {
            elements.playBtn.disabled = true;
            elements.playBtn.innerHTML = playIcon;
        }
        if(elements.addNoteBtn) elements.addNoteBtn.disabled = true;
        if (elements.cleanupBtn) elements.cleanupBtn.disabled = true;
        if(elements.notesList) elements.notesList.innerHTML = '';
        if(elements.waveformEl) elements.waveformEl.innerHTML = '';
        if(elements.progressEl) {
            elements.progressEl.style.display = 'flex';
            elements.progressEl.value = 0;
        }
        if(elements.timeEl) elements.timeEl.textContent = formatTime(0, true);
        if(elements.producerNoteDisplay) renderProducerNote('');
        currentAttachmentId = null;
        if (wavesurfer) wavesurfer.destroy();
    }
    
    // Event listener for file input change.
    if (elements.fileInput) {
        elements.fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            resetPlayer();
            const fd = new FormData();
            fd.append('action', 'mwu_upload');
            fd.append('nonce', settings.nonces.upload);
            fd.append('instance_id', instanceId);
            fd.append('mp3File', file);
            let responseText = '';
            try {
                setStatus(`Uploading ${file.name}...`);
                const res = await fetch(settings.ajax_url, { method: 'POST', body: fd });
                responseText = await res.text();
                const json = JSON.parse(responseText);
                if (json && json.success) {
                    loadPlayerState({ id: json.data.id, url: json.data.url, title: json.data.title, notes: [], producer_note: '' });
                } else {
                    throw new Error((json?.data?.message) || 'Upload failed.');
                }
            } catch (err) {
                setStatus('An error occurred during upload. See console for details.', true);
                console.error("--- MWU UPLOAD ERROR ---", { response: responseText, error: err });
            }
        });
    }

    // Event listener for the main play/stop button.
    if (elements.playBtn) {
        elements.playBtn.addEventListener('click', () => {
            if (!wavesurfer) return;
            if (wavesurfer.isPlaying()) {
                wavesurfer.pause();
                if (wavesurfer.getDuration() > 0) wavesurfer.seekTo(lastStartPosition / wavesurfer.getDuration());
                elements.playBtn.innerHTML = playIcon;
            } else {
                lastStartPosition = wavesurfer.getCurrentTime();
                wavesurfer.play();
                elements.playBtn.innerHTML = stopIcon;
            }
            lastClickedNoteTimestamp = null;
        });
    }
    
    // Event listeners for zoom controls.
    if (elements.zoomInBtn) elements.zoomInBtn.addEventListener('click', () => { if (wavesurfer) wavesurfer.zoom(currentPxPerSec += ZOOM_STEP); });
    if (elements.zoomOutBtn) elements.zoomOutBtn.addEventListener('click', () => { if (wavesurfer) wavesurfer.zoom(currentPxPerSec = Math.max(minPxPerSec, currentPxPerSec - ZOOM_STEP)); });

    /**
     * Renders the list of comments below the player.
     * @param {Array} notes An array of note objects.
     */
    const renderNotes = (notes) => {
        if (!elements.notesList) return;
        const showHours = wavesurfer && wavesurfer.getDuration() >= 3600;
        elements.notesList.innerHTML = '';
        notes.forEach(note => {
            const noteRow = document.createElement('div');
            noteRow.className = 'mwu-note-row';
            const deleteBtnHtml = `<button class="mwu-delete-note-btn" title="Delete Comment">&times;</button>`;
            const noteItemHtml = `
                <div class="mwu-note-item" data-timestamp="${note.timestamp}" data-note-id="${note.id}">
                    <div class="mwu-note-meta">
                        <span class="timestamp">${formatTime(note.timestamp, showHours)}</span>
                        <span class="handle">${note.handle}</span>
                        <span class="date-time">(${formatNoteDate(note.date)})</span>:
                    </div>
                    <div class="mwu-note-text"> ${note.comment.replace(/\n/g, '<br>')}</div>
                </div>`;
            noteRow.innerHTML = deleteBtnHtml + noteItemHtml;
            elements.notesList.appendChild(noteRow);
        });
    };
    
    /**
     * Renders the producer's note content.
     * @param {string} text The note text.
     */
    const renderProducerNote = (text) => {
        if (!elements.producerNoteDisplay) return;
        const displayText = text ? text.replace(/\n/g, '<br>') : '<em>No note from the producer yet.' + (settings.is_admin ? ' Click to add one.': '') + '</em>';
        elements.producerNoteDisplay.innerHTML = displayText;
        if (elements.producerNoteTextarea) elements.producerNoteTextarea.value = text;
    };
    
    // Event listener for the "Add Note" button.
    if (elements.addNoteBtn) {
        elements.addNoteBtn.addEventListener('click', () => {
            if (!wavesurfer || elements.addNoteBtn.disabled) return;
            const currentTime = wavesurfer.getCurrentTime();
            const showHours = wavesurfer.getDuration() >= 3600;
            elements.commentModal.querySelector('.mwu-modal-title').textContent = `Add Comment at ${formatTime(currentTime, showHours)}`;
            elements.commentModal.querySelector('.mwu-note-timestamp').value = currentTime;
            elements.commentModal.querySelector('.mwu-note-handle').value = localStorage.getItem('mwu_handle') || '';
            elements.commentModal.style.display = 'flex';
            elements.commentModal.querySelector('.mwu-note-comment').focus();
        });
    }
    
    // Event listener for the comment form submission.
    if (elements.noteForm) {
        elements.noteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const handle = elements.handleInput.value.trim();
            const comment = elements.commentInput.value.trim();
            if (!handle || !comment) return alert('Handle and comment are required.');
            localStorage.setItem('mwu_handle', handle);
            const fd = new FormData();
            fd.append('action', 'mwu_save_note');
            fd.append('nonce', settings.nonces.save_note);
            fd.append('attachment_id', currentAttachmentId);
            fd.append('timestamp', elements.timestampInput.value);
            fd.append('handle', handle);
            fd.append('comment', comment);
            try {
                const res = await fetch(settings.ajax_url, { method: 'POST', body: fd });
                const json = await res.json();
                if (!json.success) throw new Error((json.data && json.data.message) || 'Failed to save comment.');
                renderNotes(json.data.notes);
            } catch(err) {
                setStatus(err.message, true);
            } finally {
                elements.commentModal.style.display = 'none';
                elements.noteForm.reset();
            }
        });
    }
    
    // Event listener for the comments section toggle.
    if (elements.notesToggle) elements.notesToggle.addEventListener('click', () => instanceWrapper.querySelector('.mwu-notes-container').classList.toggle('mwu-notes-collapsed'));

    // Event delegation for the notes list (play from comment, delete comment).
    if (elements.notesList) {
        elements.notesList.addEventListener('click', async (e) => {
            const deleteBtn = e.target.closest('.mwu-delete-note-btn');
            if (deleteBtn) {
                e.stopPropagation();
                if (!confirm("Are you sure you want to permanently delete this comment?")) return;
                
                const noteId = deleteBtn.closest('.mwu-note-row').querySelector('.mwu-note-item').dataset.noteId;
                const fd = new FormData();
                fd.append('action', 'mwu_delete_note');
                fd.append('nonce', settings.nonces.delete_note);
                fd.append('attachment_id', currentAttachmentId);
                fd.append('note_id', noteId);
                try {
                    setStatus('Deleting comment...');
                    const res = await fetch(settings.ajax_url, { method: 'POST', body: fd });
                    const json = await res.json();
                    if (!json.success) throw new Error((json.data && json.data.message) || 'Failed to delete comment.');
                    renderNotes(json.data.notes);
                    setStatus('Comment deleted.');
                } catch(err) {
                    setStatus(err.message, true);
                }
                return;
            }

            const noteEl = e.target.closest('.mwu-note-item');
            if (noteEl && wavesurfer) {
                const timestamp = parseFloat(noteEl.dataset.timestamp);
                const isPlaying = wavesurfer.isPlaying();
                if (isPlaying && lastClickedNoteTimestamp === timestamp) {
                    wavesurfer.pause();
                    elements.playBtn.innerHTML = playIcon;
                } else {
                    lastStartPosition = timestamp;
                    lastClickedNoteTimestamp = timestamp;
                    wavesurfer.seekTo(timestamp / wavesurfer.getDuration());
                    if (!isPlaying) wavesurfer.play();
                    elements.playBtn.innerHTML = stopIcon;
                }
            }
        });
    }

    // Logic for the cleanup confirmation modal.
    if (elements.cleanupBtn) {
        elements.cleanupBtn.addEventListener('click', () => {
            if (elements.cleanupModal) elements.cleanupModal.style.display = 'flex';
        });
    }

    const hideCleanupModal = () => { if (elements.cleanupModal) elements.cleanupModal.style.display = 'none'; };

    /**
     * Handles the cleanup action based on user's choice in the modal.
     * @param {boolean} deleteFile Whether to delete the audio file.
     */
    const handleCleanup = async (deleteFile) => {
        if (!currentAttachmentId) return;
        let responseText = '';
        try {
            setStatus(deleteFile ? 'Deleting file and cleaning up...' : 'Cleaning up player...');
            const fd = new FormData();
            fd.append('action', 'mwu_cleanup_instance');
            fd.append('nonce', settings.nonces.cleanup);
            fd.append('instance_id', instanceId);
            fd.append('attachment_id', currentAttachmentId);
            if (deleteFile) {
                fd.append('delete_file', 'true');
            }
            const res = await fetch(settings.ajax_url, { method: 'POST', body: fd });
            responseText = await res.text();
            const json = JSON.parse(responseText);
            if (!json.success) throw new Error((json.data && json.data.message) || 'Cleanup failed.');
            resetPlayer();
        } catch (err) {
            setStatus('An error occurred during cleanup. See console for details.', true);
            console.error("--- MWU CLEANUP ERROR ---", { response: responseText, error: err });
        } finally {
            hideCleanupModal();
        }
    };

    if (elements.cleanupCancelBtn) elements.cleanupCancelBtn.addEventListener('click', hideCleanupModal);
    if (elements.cleanupModal) elements.cleanupModal.addEventListener('click', (e) => { if (e.target === elements.cleanupModal) hideCleanupModal(); });
    if (elements.cleanupConfirmNoBtn) elements.cleanupConfirmNoBtn.addEventListener('click', () => handleCleanup(false));
    if (elements.cleanupConfirmYesBtn) elements.cleanupConfirmYesBtn.addEventListener('click', () => handleCleanup(true));
    
    // Logic for comment modal cancellation.
    if (elements.commentModal) {
        const cancelBtn = elements.commentModal.querySelector('.cancel');
        cancelBtn.addEventListener('click', () => { elements.commentModal.style.display = 'none'; });
        elements.commentModal.addEventListener('click', (e) => { if (e.target === elements.commentModal) elements.commentModal.style.display = 'none'; });
    }

    // Logic for the editable producer's note.
    if (settings.is_admin && elements.producerNoteDisplay) {
        elements.producerNoteDisplay.classList.add('is-admin');
        elements.producerNoteDisplay.addEventListener('click', () => {
            elements.producerNoteContainer.classList.add('is-editing');
            elements.producerNoteTextarea.focus();
        });
        elements.producerNoteCancelBtn.addEventListener('click', () => {
            elements.producerNoteContainer.classList.remove('is-editing');
            elements.producerNoteTextarea.value = settings.initial_state.producer_note;
        });
        elements.producerNoteSaveBtn.addEventListener('click', async () => {
            const newText = elements.producerNoteTextarea.value;
            const fd = new FormData();
            fd.append('action', 'mwu_save_producer_note');
            fd.append('nonce', settings.nonces.save_producer_note);
            fd.append('attachment_id', currentAttachmentId);
            fd.append('note_text', newText);
            try {
                setStatus('Saving producer note...');
                const res = await fetch(settings.ajax_url, { method: 'POST', body: fd });
                const json = await res.json();
                if (!json.success) throw new Error(json.data.message || 'Failed to save note.');
                settings.initial_state.producer_note = json.data.note_text;
                renderProducerNote(json.data.note_text);
                setStatus('Producer note saved.');
            } catch (err) {
                setStatus(err.message, true);
            } finally {
                elements.producerNoteContainer.classList.remove('is-editing');
            }
        });
    }
    
    // Initial load.
    const initialState = settings.initial_state;
    if (initialState && initialState.id) {
        loadPlayerState(initialState);
    } else {
        resetPlayer();
    }
}