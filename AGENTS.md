# AGENTS.md

Guidance for AI coding agents working on **Minniark**, a self-hosted, flat-file PHP CMS / portfolio for photographers.
No database: all content and configuration live as YAML, Markdown and JSON files under `userdata/`.

- Project page: https://minniark.app · Docs: https://dev.minniark.app · Demo: https://demo.minniark.app
- Maintainer: Christian Heinisch (solo project)
- License: MIT

## Stack

- PHP 8.3+ (Docker image runs PHP 8.4-FPM behind Nginx; Apache works via `.htaccess`)
- Composer dependencies: Twig 3, Symfony YAML 7, Parsedown, phpdotenv, SVGGraph,
  `cheinisch/openrouter-php-client`, `cheinisch/markdown-editor`
- Frontend themes: Twig templates; dashboard UI: plain PHP + Tailwind CSS (browser CDN build) + vanilla JS
- No build step, no Node toolchain, no test suite

## Repository layout

| Path | Purpose |
|---|---|
| `index.php` | Public front controller: loads plugins, settings, Twig, routes by URL path |
| `dashboard/` | Admin backend. One PHP file per page (`dashboard-*.php`, `*-detail.php`), plus `install.php`, `login.php`, `update.php` |
| `dashboard/backend_api/` | Form/AJAX handlers used by the dashboard (session-protected via `security_checklogin()`) |
| `dashboard/js/`, `dashboard/css/`, `dashboard/inc/`, `dashboard/layout/` | Dashboard scripts, styles, nav and menu partials |
| `api/` | JSON endpoints (upload, metadata, EXIF sync, backups, version …), protected via `secure_API()` |
| `functions/backend/*.php` | Backend helper functions, all auto-included by `functions/function_backend.php` |
| `functions/frontend/*.php` | Frontend helper functions, auto-included by `functions/function_frontend.php` |
| `functions/api/*.php` | API helpers, included by `functions/function_api.php` |
| `functions/twig/twig.php` | Builds the shared `$data` array passed to every Twig render, plugin Twig hooks |
| `app/classes/` | Non-namespaced classes (`Images`, `LicenseManager`, `Releasenotes`), loaded by `app/autoload.php` (class name = file name) |
| `language/*.json` | UI translations (`en-US`, `de-DE`, `fr-FR`) |
| `userdata/template/<theme>/` | Themes (Twig + `theme.json` + `image.png` preview). Bundled: `minniark_basic`, `minniark_boilerplate` |
| `userdata/plugins/<plugin>/` | Plugins (`plugin.json` with `enabled`, optional `routes.php`) |
| `userdata/config/`, `userdata/content/` | Runtime config and content — **git-ignored**, created by the installer |
| `cache/images/`, `cache/pages/` | Generated image thumbnails and Twig cache — git-ignored |
| `docker/` | Dockerfiles, Nginx config, `docker-compose.yml` |
| `.github/workflows/` | Docker image builds and FTP release deploy (on `v*` tags) |
| `vendor/` | **Committed** Composer dependencies (see below) |

## Conventions

### Functions over classes
Most logic is procedural: global functions in `functions/backend|frontend/`. A new file dropped into those folders is
included automatically via `glob()`. Keep function names unique — everything shares the global namespace.
Classes go into `app/classes/` without a namespace; the file name must match the class name.
The `Chris\ImagePortfolio\` PSR-4 mapping in `composer.json` points to a non-existent `src/` and is not used.

### Data storage
- Read/write YAML with `Symfony\Component\Yaml\Yaml` (`Yaml::parseFile`, `Yaml::dump($data, 2, 4)`).
- Wrap parsing in `try/catch` and log with `error_log()`.
- Image metadata: `userdata/content/images/<slug>.yml` plus `<slug>.md` for the description.
- Site settings: `userdata/config/settings.yml` (theme, language, site title …).
- Never commit anything from `userdata/config/` or `userdata/content/` — it may contain password hashes and API keys.

### Dashboard pages
Pattern at the top of every page:

```php
require_once __DIR__ . '/../functions/function_backend.php';
security_checklogin();   // redirects to login if no session
onlyAdmin();             // additionally, for admin-only pages
```

Output strings via `languageString('section.key')` — never hard-code UI text.
Styling uses Tailwind utility classes with `dark:` variants; keep the existing look (see `dashboard/colors.md`).

### API endpoints (`api/`, `dashboard/backend_api/`)
- Call `secure_API()` (api/) or `security_checklogin()` (backend_api/) first; add an admin check where the action is privileged.
- JSON endpoints: `header('Content-Type: application/json')`, respond with `['success' => bool, 'error' => '...']`
  and a fitting HTTP status code (400/403/404/500).
- Sanitize every file name from input with `basename()` and validate paths against `realpath()` of the allowed directory.

### Translations
When adding a UI string, add the key to **all three** files: `language/en-US.json`, `de-DE.json`, `fr-FR.json`.
Keys are nested and accessed with dot notation; `{{var}}` placeholders and `_0/_1/_n` plural forms are supported.
A missing key renders as the key itself.

### Themes
A theme needs at least: `home.twig`, `album.twig`, `album.list.twig`, `collection.twig`, `collection.list.twig`,
`image.twig`, `page.twig`, `post.twig`, `blog.twig`, `timeline.twig`, `map.twig`, `404.twig`, `theme.json`, `image.png`.
Templates receive the `$data` array built in `functions/twig/twig.php` (`site_title`, `settings`, `navItems`,
`themepath`, `current_path`, `current_url`, …). Don't break that contract — external themes rely on it.

### Code style
- 4-space indentation in new code; match the surrounding file when editing.
- Use type declarations for new functions and methods.
- Comments may be German or English; user-facing docs (README, CHANGELOG) are English.
- Don't leave debug output (`error_log(print_r(...))`, `var_dump`) in finished code.

## Security checklist

This is a public, self-hosted app — treat every endpoint as internet-facing.

- Auth check on every dashboard page and endpoint; admin check for system, user, theme, plugin, update and backup actions.
- Path traversal: `basename()` + `realpath()` prefix check for all file operations based on user input.
- Uploads: validate extension **and** MIME type, enforce size limits; for ZIPs check entry count and Zip-Slip
  (see `upload_theme.php` as the reference implementation).
- Escape output: Twig autoescaping in themes, `htmlspecialchars()` in plain PHP pages.
- There is currently no CSRF protection — don't rely on it existing; flag it when touching state-changing endpoints.
- Never log secrets, license keys or password hashes.

## Dependencies

`vendor/` is committed on purpose (it is listed in `.gitignore` but tracked): the release ZIP is built straight from the
repo, and users install by uploading files without running Composer. After `composer update`/`require`, commit both
`composer.lock` and the changed `vendor/` files. Note upgraded packages in the CHANGELOG.

## Versioning, changelog, releases

- Calendar versioning `YYYY.M.PATCH` (e.g. `2026.8.0`), stored in `VERSION`.
- `CHANGELOG.md` holds the latest releases (Keep-a-Changelog style: Added / Changed / Fixed / Security / i18n);
  the full history is in `.changelog/Changelog.md`. `Releasenotes.md` is shown in the dashboard update view.
- `.changelog/notes.md` collects ideas for the next release.
- Pushing a `v*` tag builds `latest.zip` and deploys it via FTP. Never create tags unless explicitly asked.

## Verifying changes

There are no automated tests. At minimum:

```bash
# Syntax check of changed files
php -l path/to/file.php

# Syntax check of the whole codebase (without vendor)
find . -path ./vendor -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v 'No syntax errors'

# Local run (needs a writable userdata/ and cache/)
php -S localhost:8080 index.php     # frontend; open /dashboard/ for the installer and admin
```

Or use the Docker setup in `docker/`. For UI changes, check light and dark mode and all three languages.

## Don'ts

- Don't introduce a database, a framework or a JS build pipeline.
- Don't rename existing functions, Twig variables or `userdata/` file formats without a migration — installed sites and
  external themes/plugins depend on them.
- Don't edit files in `vendor/` by hand.
- Don't touch `*.old` files or `.github/backup/` unless asked; they are kept for reference.
