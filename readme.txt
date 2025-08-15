=== MP3 Waveform Uploader & Annotator ===
Contributors: sasofajon
Tags: audio, mp3, waveform, player, comments, annotations, music, feedback
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 6.8
Stable tag: 4.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An advanced audio player with a zoomable waveform and timestamped annotations. Usage: [mp3_waveform_uploader id="unique_id"].

== Description ==

An advanced audio player with a zoomable waveform, timestamped annotations, conditional file deletion, producer notes, and admin notifications. This plugin allows users to leave time-stamped comments on an audio track, similar to services like SoundCloud, providing a powerful tool for feedback and collaboration. Administrators have full control over the tracks, comments, and notifications.

== Installation ==

1. Upload the `mp3-waveform-uploader` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the shortcode `[mp3_waveform_uploader id="your_unique_id"]` on any page or post, replacing `your_unique_id` with a unique identifier for that player instance (e.g., "track1", "mix-review-alpha").

== Frequently Asked Questions ==

= How do I use the player? =

* **Play / Stop**: Use the main **▶ / ■** button to play or stop the audio. When you stop playback, the playhead will return to the position where it last started.
* **Seek**: Click (or drag) anywhere on the waveform graphic to jump directly to that point in the track.
* **Zoom**: Use the magnifying glass icons (`🔍+` / `🔍-`) below the waveform to zoom in and out for a more detailed view.

= How do comments work? =

* **Add a Comment**: Play the track or seek to a specific time, then click the **+** button next to the timestamp. A pop-up window will appear, allowing you to add a new comment at that exact moment.
* **View Comments**: Click the **"Comments"** title to expand or collapse the list of all annotations for the track.
* **Play from a Comment**: Clicking on any comment in the list will automatically start playback from that comment's specific timestamp.
* **Delete a Comment**: Each individual comment has its own small **`×`** button to its left, allowing for its precise removal.

= What can an administrator do? =

* **Upload a New Track**: Click the upload icon (**`↑`**) to load a new audio file into the player. Note: This is for loading a brand new track and will not retain comments from any file that was previously loaded.
* **Clear Player / Delete Track**: Click the main **`×`** button next to the track title to completely clear the player. A confirmation window will give you two options: (1) "No, Just Clear Player", which removes all comments but keeps the file in your Media Library, or (2) "Yes, Delete File", which removes all comments AND permanently deletes the MP3 file.
* **Add/Edit Producer's Note**: Click anywhere on the yellow "Producer's Note" box to add or edit a general note that applies to the entire track.
* **Receive Email Notifications**: An email notification is automatically sent to the site administrator (the email in Settings > General) whenever a new comment is posted.

== Changelog ==

= 4.2.0 =
* ADD: Complete UI redesign for the comments section.
* ADD: Conditional file deletion with a custom confirmation modal.
* ADD: Email notifications to the site administrator for new comments.
* ADD: A new integrated in-admin help page under the "Settings" menu.
* ADD: A "Help" link on the main Plugins page for quick access to documentation.
* FIX: Resolved complex CSS conflicts with modern block themes for a stable layout.
* FIX: Corrected a UI bug where the play button appeared blank on initial load.
* REMOVE: The old, obsolete help modal.