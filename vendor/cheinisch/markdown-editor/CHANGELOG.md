# Changelog

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [0.1.0] - 2026-05-27

Complete redesign and rewrite. Breaking changes throughout.

### Added

**Toolbar**
- `buttons` option to control which toolbar buttons are rendered
- Table button with Spalten/Zeilen popover — inserts a ready-to-use Markdown table
- All buttons are individually toggleable via the `buttons` option

**localStorage**
- `localStorage` option to auto-save and restore editor content in the browser
- Accepts `true` (default key) or a custom string key

**Sync Field (`field`)**
- `field` option to sync editor content with an existing `<textarea>` by ID
- If the element already exists in the DOM it is reused — no duplicate is created
- If it does not exist, a hidden `<textarea>` is created automatically

**Image & File Library**
- `library` option with configurable directory list (name, path, upload, recursive)
- Relative `path` values are automatically resolved from the embedding PHP file
- `upload: true` enables drag & drop and an upload button per directory
- `recursive: true` includes subdirectories in the file listing
- Non-image files (PDF, DOCX, ZIP …) are inserted as Markdown links `[name](…)`
- Image files are inserted as `![name](…)`
- Visual drop zone with persistent dashed border and active highlight on drag
- Live search across the currently loaded file list
- Multi-file selection
- Max upload size (from PHP INI) shown in the modal footer

**Library API (`handleRequest`)**
- New static method `MarkdownEditor::handleRequest()` handles `GET` (listing) and `POST` (upload) in one endpoint
- `listExtensions` option to configure which file types are listed
- `types` option to restrict allowed MIME types for upload
- `maxSize` option to override the default upload size limit
- File listing sorted by modification time (newest first)
- Returns HTTP 404 with error message when a directory does not exist
- Filenames are sanitized on upload, unique suffix added to prevent collisions

### Changed

- `renderHeadAssets()` renamed to `renderHead()`
- `renderFootAssets()` renamed to `renderFoot()`
- Composer is no longer required — installation is two files (`MarkdownEditor.php`, `markdown-editor.js`)
- Minimum PHP version raised to **8.1**

### Removed

- Composer autoloading (`vendor/`)
- `src/Composer/ScriptHandler.php`
- Hardcoded library sidebar (was static HTML, now fully dynamic)

---

## [0.0.1] - 2024-01-01

Initial release.

### Added

- Basic Markdown editor with toolbar (Bold, Italic, Underline, H1, List, Quote, Code, Link)
- Live preview toggle
- Word, character and line count
- Keyboard shortcuts (`Ctrl+B`, `Ctrl+I`, `Ctrl+U`)
- Auto-continuation of list items on Enter
- Composer package (`cheinisch/markdown-editor`)