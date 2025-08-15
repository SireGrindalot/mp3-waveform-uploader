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
- Timeline markers and producer note field
- Optional zoom-in / zoom-out controls

---

## 🛠️ Usage

Use the following shortcode to embed a player: [mp3_waveform_uploader id=“unique_id”]
Replace `"unique_id"` with a custom value to uniquely identify each audio instance.

---

## ⚙️ Installation

Install via WordPress Admin (ZIP file)
	1.	Download the plugin ZIP file [Download mp3-waveform-uploader.zip](https://github.com/SireGrindalot/mp3-waveform-uploader/raw/main/mp3-waveform-uploader.zip)
	2.	In your WordPress Admin:
	•	Go to Plugins → Add New
	•	Click Upload Plugin at the top
	•	Select the downloaded mp3-waveform-uploader.zip
	•	Click Install Now
	•	Click Activate Plugin
---

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
