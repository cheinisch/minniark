# Changelog

> [!NOTE]
> You find a complete Changelog here [.changelog/Changelog.md](https://github.com/cheinisch/minniark/blob/main/.changelog/Changelog.md)

## [Unreleased]

### Security
- CSRF protection for all dashboard forms, AJAX requests and action links (token is attached automatically)
- Login check added to endpoints that were reachable without login (pages, essays, collections, albums, start page, navigation, plugin and site settings, backup deletion, cache rebuild)
- Admin-only pages and actions (users, system, themes, start page, menu, plugins, backups, updates, AI) now require admin rights; regular users no longer see them in the menu
- Installer can no longer be run on an installed site (previously anyone could create a new admin account)
- Self-updater requires an admin session
- Users can only change their own account data
- Login brute-force protection: 5 failed attempts per user and IP (20 per IP) lock the login for 15 minutes
- New session ID after login; session cookie is HttpOnly, SameSite=Lax and Secure on HTTPS
- Mail login codes expire after 10 minutes and can only be used once
- Login codes, passwords and license keys are no longer written to the log or echoed in responses
- PHP errors are no longer shown to visitors on the public site

### Fixed
- Fatal error on the public site when `settings.yml` is missing (`erorr_log` typo)
- AI settings link in the legacy navigation pointed to a missing page
- Several dashboard labels showed raw translation keys

### i18n
- Completed missing German and French strings
- Added strings for the image text and EXIF edit dialogs, upload label and empty tag list

## [2026.8.0]
2026-08-09

### Added
- Theme upload modal: install themes directly from a .zip file via drag & drop or file picker
- Client-side ZIP preview: extracts `image.png` from the archive before upload using `DecompressionStream`
- Progress bar and inline error feedback during upload
- Success toast notification after installation

### Security
- Admin-only check on `upload_theme.php` endpoint
- 20 MB file size cap server-side
- ZIP entry count limit (max 500 entries, prevents Zip-Bomb-Light)
- Zip-Slip protection: all entry paths validated against realpath before extraction
- MIME type and extension validation

### i18n
- Upload keys added to `en-US.json`, `de-DE.json`, `fr-FR.json`


## [2026.7.0]
2026-07-12
**Full Changelog**: https://github.com/cheinisch/minniark/compare/v2026.5.0...v2026.7.0

  - Upgrading symfony/deprecation-contracts (v3.7.0 => v3.7.1)
  - Upgrading guzzlehttp/psr7 (2.10.1 => 2.12.4)
  - Upgrading guzzlehttp/promises (2.4.1 => 2.5.1)
  - Upgrading guzzlehttp/guzzle (7.10.4 => 7.14.0)
  - Upgrading symfony/polyfill-mbstring (v1.37.0 => v1.38.2)
  - Upgrading symfony/yaml (v7.4.12 => v7.4.14)
  - Upgrading twig/twig (v3.26.0 => v3.28.0)
  - Upgrading vlucas/phpdotenv (v5.6.3 => v5.6.4)

## [2026.5.0]
2026-05-25
**Full Changelog**: https://github.com/cheinisch/minniark/compare/v2026.2.0...v2026.5.0

### Changes

  - Upgrading guzzlehttp/psr7 (2.8.0 => 2.9.0)
  - Upgrading symfony/polyfill-mbstring (v1.33.0 => v1.36.0)
  - Upgrading erusev/parsedown (1.7.4 => 1.8.0)
  - Upgrading symfony/polyfill-ctype (v1.33.0 => v1.37.0)
  - Upgrading symfony/yaml (v7.4.1 => v7.4.12)
  - Upgrading twig/twig (v3.23.0 => v3.26.0)
  - Upgrading symfony/polyfill-php80 (v1.33.0 => v1.37.0)

## [2026.2.0]
2026-02-13

**Full Changelog**: https://github.com/cheinisch/minniark/compare/v2026.1.0...v2026.2.0

### New Features
- Change AI Model

### Changes
- update twig/twig
- update phpoption/phpoption
- update vlucas/phpdotenv
- update goat/svggraph


## [2026.1.0]
2026-01-15

**Full Changelog**: https://github.com/cheinisch/minniark/compare/v2025.12.2...v2026.1.0

### New Features
- add License Shop for Ai features 

### Bugfixes
- fix media-detail page
