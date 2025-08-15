# MP3 Waveform Uploader & Annotator
![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue)

## 🔊 Usage Preview
![Usage Screenshot](screenshot.png)

**Contributors:** sasofajon  
**Tags:** audio, mp3, waveform, player, comments, annotations, music, feedback  
**Requires at least:** 6.0  
**Tested up to:** 6.0.8  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

An advanced audio player with a zoomable waveform and timestamped annotations.  
Inspired by SoundCloud. Designed for musicians, podcasters, and educational content.

---

## ✨ Features

- Upload `.mp3` files via media library or frontend form
- Display interactive waveform using [WaveSurfer.js](https://wavesurfer-js.org/)
- Timestamped comments with AJAX support
- Admin interface for managing notes and player settings
- Responsive design, works in most themes

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
---

## 🛠️ Usage

Use the following shortcode to embed a player: [mp3_waveform_uploader id=“unique_id”]
Replace `"unique_id"` with a custom value to uniquely identify each audio instance.

---

📦 **Installation instructions**

1. Download plugin in install ready zip file: [Download the plugin ZIP file](https://github.com/SireGrindalot/mp3-waveform-uploader/raw/main/mp3-waveform-uploader.zip)
2. In WordPress:
   - Go to *Plugins → Add New*
   - Click *Upload Plugin*
   - Choose the downloaded `.zip` file
   - Click *Install Now*, then *Activate*

## 💡 Developer Notes

- Comments are stored in WordPress options table using the unique ID as key
- Uses `WaveSurfer.js` for waveform and timeline display
- AJAX endpoints are namespaced for minimal conflicts
- Fully self-contained, no external dependencies

---

## 📄 License

This plugin is licensed under the [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html) or later.

---

## ✍️ Author

**Sašo Fajon**  
Piran, Slovenia  
https://github.com/SireGrindalot
