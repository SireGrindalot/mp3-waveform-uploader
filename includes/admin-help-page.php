<?php
/**
 * This file contains the HTML content for the admin help page.
 * It is included by a callback function registered in the main plugin file.
 */
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1><?php esc_html_e('MP3 Annotator: User & Admin Guide', 'mp3-waveform-uploader'); ?></h1>
    
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    
                    <div class="postbox">
                        <h2 class="hndle"><span>Player Functionality (For All Users)</span></h2>
                        <div class="inside">
                            <p>This guide explains how to use the audio player and annotation features.</p>
                            
                            <h3>Playback & Navigation</h3>
                            <ul>
                                <li><strong>Play / Stop</strong>: Use the main <strong>▶ / ■</strong> button to play or stop the audio. When you stop playback, the playhead will return to the position where it last started.</li>
                                <li><strong>Seek</strong>: Click (or drag) anywhere on the waveform graphic to jump directly to that point in the track.</li>
                            </ul>

                            <h3>Comments & Annotations</h3>
                            <ul>
                                <li><strong>Add a Comment</strong>: Play the track or seek to a specific time, then click the <strong>+</strong> button next to the timestamp. A pop-up window will appear, allowing you to add a new comment at that exact moment.</li>
                                <li><strong>View Comments</strong>: Click the <strong>"Comments"</strong> title to expand or collapse the list of all annotations for the track.</li>
                                <li><strong>Play from a Comment</strong>: Clicking on any comment in the list will automatically start playback from that comment's specific timestamp.</li>
                                <li><strong>Delete a Comment</strong>: Each individual comment has its own small <strong>`&times;`</strong> button to its left, allowing for its precise removal.</li>
                            </ul>

                            <h3>Waveform Display</h3>
                            <ul>
                                <li><strong>Zoom</strong>: Use the magnifying glass icons (<code>🔍+</code> / <code>🔍-</code>) below the waveform to zoom in and out for a more detailed view.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle"><span>Administrator Features</span></h2>
                        <div class="inside">
                            <p>This section details the additional functionality available only to logged-in administrators.</p>

                            <h3>Managing the Track</h3>
                            <ul>
                                <li><strong>Upload a New Track</strong>: Click the upload icon (<strong>`↑`</strong>) to load a new audio file into the player. <strong>Note</strong>: This is for loading a brand new track. It will not retain comments from any file that was previously loaded in this player instance.</li>
                                <li><strong>Clear Player / Delete Track</strong>: Click the main <strong>`&times;`</strong> button next to the track title to completely clear the player. A confirmation window will give you two options:
                                    <ol>
                                        <li><strong>No, Just Clear Player</strong>: This removes all comments and disconnects the file from the player, but the MP3 file remains in your Media Library.</li>
                                        <li><strong>Yes, Delete File</strong>: This does the same as above AND <strong>permanently deletes the MP3 file</strong> from your Media Library.</li>
                                    </ol>
                                </li>
                            </ul>

                            <h3>Producer's Note</h3>
                            <ul>
                                <li><strong>Add/Edit Note</strong>: Click anywhere on the yellow "Producer's Note" box to add or edit a general note that applies to the entire track.</li>
                            </ul>

                            <h3>Email Notifications</h3>
                            <ul>
                                <li><strong>New Comment Alerts</strong>: An email notification is automatically sent to the site administrator whenever a new comment is posted. The recipient address is the one configured in <strong>Settings > General > Administration Email Address</strong>.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>