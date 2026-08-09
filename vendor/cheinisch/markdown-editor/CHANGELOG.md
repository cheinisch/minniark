# Changelog – cheinisch/markdown-editor

## [1.0.0] – 2026-08-09

### Added
- Full self-contained JS editor (`md-editor.js`): toolbar, keyboard shortcuts, undo/redo, live preview, fullscreen mode
- Stylesheet (`md-editor.css`): light/dark/auto theme support, split-pane preview, fullscreen
- PHP helper class `MarkdownEditor` with `css()`, `js()`, `render()` methods
- `data-md-editor` + `data-toolbar` attribute-based auto-initialisation (no manual JS call required)
- Public API on wrapper element: `getValue()`, `setValue()`, `destroy()`
- Inline Markdown → HTML renderer (headings, bold, italic, lists, blockquotes, code, links)
- Replaces EasyMDE (blog, pages) and SimpleMDE (albums, collections) in Minniark dashboard

## [0.0.1] – initial placeholder
- Empty package scaffold
