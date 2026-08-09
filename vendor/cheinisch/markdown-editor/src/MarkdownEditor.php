<?php
declare(strict_types=1);

namespace cheinisch\MarkdownEditor;

final class MarkdownEditor
{
    private const ALL_BUTTONS = [
        'bold', 'italic', 'underline', 'strikethrough',
        'h1', 'h2', 'h3', 'list', 'quote', 'code', 'link', 'hr', 'table', 'image-url',
        'library',
    ];

    private const GROUP1 = ['bold', 'italic', 'underline', 'strikethrough'];
    private const GROUP2 = ['h1', 'h2', 'h3', 'list', 'quote', 'code', 'link', 'hr', 'table', 'image-url'];

    private const THEME_DEFAULTS = [
        'light' => [
            'toolbar-bg'         => '#f8fafc',   // slate-50
            'toolbar-border'     => '#e2e8f0',   // slate-200
            'toolbar-btn-hover'  => '#ffffff',
            'toolbar-text'       => '#64748b',   // slate-500
            'editor-bg'          => '#ffffff',
            'editor-text'        => '#0f172a',   // slate-900
            'editor-border'      => '#f1f5f9',   // slate-100
            'editor-placeholder' => '#94a3b8',   // slate-400
            'preview-bg'         => '#ffffff',
            'preview-text'       => '#0f172a',
            'surface'            => '#ffffff',
            'surface-alt'        => '#f8fafc',   // slate-50
            'muted'              => '#64748b',   // slate-500
            'accent'             => '#1e293b',   // slate-900  (Library / dark btn)
            'accent-text'        => '#ffffff',
            'accent-hover'       => '#334155',   // slate-700
            'insert-bg'          => '#2563eb',   // blue-600
            'insert-hover'       => '#1d4ed8',   // blue-700
            'radius-sm'          => '8px',
            'radius-md'          => '12px',
            'radius-lg'          => '24px',
            'font-sans'          => 'ui-sans-serif, system-ui, -apple-system, sans-serif',
            'font-mono'          => 'ui-monospace, "SF Mono", "Fira Code", monospace',
            'font-size'          => '14px',
            'line-height'        => '1.75',
            'shadow'             => '0 10px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -2px rgba(0,0,0,0.05)',
            'ring'               => 'rgba(226,232,240,1)',  // slate-200
        ],
        'dark' => [
            'toolbar-bg'         => '#1e293b',   // slate-800
            'toolbar-border'     => '#334155',   // slate-700
            'toolbar-btn-hover'  => '#0f172a',   // slate-900
            'toolbar-text'       => '#94a3b8',   // slate-400
            'editor-bg'          => '#0f172a',   // slate-900
            'editor-text'        => '#f1f5f9',   // slate-100
            'editor-border'      => '#1e293b',   // slate-800
            'editor-placeholder' => '#475569',   // slate-600
            'preview-bg'         => '#0f172a',
            'preview-text'       => '#f1f5f9',
            'surface'            => '#0f172a',   // slate-900
            'surface-alt'        => '#1e293b',   // slate-800
            'muted'              => '#64748b',   // slate-500
            'accent'             => '#f1f5f9',   // light btn on dark bg
            'accent-text'        => '#0f172a',
            'accent-hover'       => '#e2e8f0',
            'insert-bg'          => '#3b82f6',   // blue-500
            'insert-hover'       => '#2563eb',   // blue-600
            'radius-sm'          => '8px',
            'radius-md'          => '12px',
            'radius-lg'          => '24px',
            'font-sans'          => 'ui-sans-serif, system-ui, -apple-system, sans-serif',
            'font-mono'          => 'ui-monospace, "SF Mono", "Fira Code", monospace',
            'font-size'          => '14px',
            'line-height'        => '1.75',
            'shadow'             => '0 10px 15px -3px rgba(0,0,0,0.4), 0 4px 6px -2px rgba(0,0,0,0.3)',
            'ring'               => 'rgba(51,65,85,1)',     // slate-700
        ],
    ];

    // ── Public API ───────────────────────────────────────────────────────────

    public static function renderHead(): string
    {
        return <<<'HTML'
<script src="https://cdn.tailwindcss.com?plugins=typography"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.7/dist/purify.min.js"></script>
HTML;
    }

    /**
     * Rendert den Markdown-Editor.
     *
     * @param array{
     *   buttons?: list<string>|list<list<string>>,
     *   height?: int,
     *   localStorage?: bool|string,
     *   field?: string,
     *   theme?: 'light'|'dark'|'auto'|string,
     *   themeToggle?: bool,
     *   themeSync?: string,
     *   library?: list<array{name: string, path: string, upload?: bool}>,
     * } $options
     *
     *   - buttons:      Sichtbare Toolbar-Buttons. Flache Liste = eine Zeile.
     *                   Verschachtelte Liste = eine Zeile pro Sub-Array.
     *
     *   - height:       Editor-Höhe in Pixeln. Standard: 700.
     *
     *   - localStorage: true → Key 'markdown-editor-content' | string → eigener Key.
     *
     *   - field:        ID einer <textarea> zum Synchronisieren.
     *
     *   - theme:        'light' | 'dark' | 'auto' (Standard) | Pfad zu JSON-Config.
     *
     *   - themeToggle:  Toggle-Button anzeigen. Standard: true.
     *
     *   - themeSync:    Framework-Dark-Mode-Signal, z.B. 'class:dark' (Tailwind),
     *                   'data-bs-theme:dark' (Bootstrap), 'data-theme:dark' (DaisyUI).
     *
     *   - library:      Bildbibliothek-Verzeichnisse.
     */
    public static function render(array $options = []): string
    {
        // ── Buttons ──────────────────────────────────────────────────────────
        $rawButtons = $options['buttons'] ?? self::ALL_BUTTONS;
        $isMultiRow = isset($rawButtons[0]) && is_array($rawButtons[0]);
        $buttonRows = $isMultiRow
            ? array_values(array_map('array_values', $rawButtons))
            : [array_values($rawButtons)];
        $visible = array_flip(array_merge(...$buttonRows));

        // ── Options ──────────────────────────────────────────────────────────
        $localStorageKey = match(true) {
            isset($options['localStorage']) && is_string($options['localStorage']) => $options['localStorage'],
            isset($options['localStorage']) && $options['localStorage'] === true   => 'markdown-editor-content',
            default => null,
        };

        $fieldId = isset($options['field']) && is_string($options['field']) && $options['field'] !== ''
            ? $options['field'] : null;

        $heightPx = isset($options['height']) && is_numeric($options['height'])
            ? (int) $options['height'] : 700;

        $callerFile = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'];

        // ── Theme ────────────────────────────────────────────────────────────
        $themeRaw   = $options['theme'] ?? 'auto';
        $themeMode  = 'auto';
        $showToggle = (bool) ($options['themeToggle'] ?? true);
        $themeSync  = isset($options['themeSync']) && is_string($options['themeSync'])
            ? $options['themeSync'] : null;
        $themeTokens = ['light' => [], 'dark' => []];

        if (is_string($themeRaw) && in_array($themeRaw, ['light', 'dark', 'auto'], true)) {
            $themeMode = $themeRaw;
        } elseif (is_string($themeRaw) && $themeRaw !== '') {
            $configPath = str_starts_with($themeRaw, '/')
                ? $themeRaw
                : dirname($callerFile) . DIRECTORY_SEPARATOR . $themeRaw;
            if (is_readable($configPath)) {
                $decoded = json_decode(file_get_contents($configPath), true);
                if (is_array($decoded)) {
                    $themeTokens['light'] = $decoded['light'] ?? [];
                    $themeTokens['dark']  = $decoded['dark']  ?? [];
                    if (isset($decoded['mode']) && in_array($decoded['mode'], ['light', 'dark', 'auto'], true)) {
                        $themeMode = $decoded['mode'];
                    }
                }
            }
        }

        $resolvedLight = array_merge(self::THEME_DEFAULTS['light'], $themeTokens['light']);
        $resolvedDark  = array_merge(self::THEME_DEFAULTS['dark'],  $themeTokens['dark']);

        $toCssVars = static function (array $tokens, string $prefix = '--md'): string {
            return implode("\n", array_map(
                fn($k, $v) => "        {$prefix}-{$k}: " . addslashes((string) $v) . ";",
                array_keys($tokens), $tokens
            ));
        };

        $cssLight = $toCssVars($resolvedLight);
        $cssDark  = $toCssVars($resolvedDark);

        // ── Library ──────────────────────────────────────────────────────────
        $libraryDirs = self::resolveLibraryPaths($options['library'] ?? [], $callerFile);
        $maxUpload   = self::maxUploadSize();
        $hasFsPath   = false;

        foreach ($libraryDirs as &$dir) {
            if (empty($dir['fsPath'])) continue;
            $hasFsPath   = true;
            $key         = md5($dir['fsPath']);
            $dir['_key'] = $key;
            if (empty($dir['webPath'])) {
                $docRoot = str_replace(DIRECTORY_SEPARATOR, '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
                $abs     = str_replace(DIRECTORY_SEPARATOR, '/', realpath($dir['fsPath']) ?: '');
                if ($docRoot && str_starts_with($abs, $docRoot)) {
                    $dir['webPath'] = '/' . ltrim(substr($abs, strlen($docRoot)), '/');
                }
            }
            $dir['path'] = self::detectPublicPath() . '/md-handler.php?dir=' . urlencode($key);
        }
        unset($dir);

        if ($hasFsPath) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            foreach ($libraryDirs as $dir) {
                if (empty($dir['_key'])) continue;
                $_SESSION['_md_dirs'][$dir['_key']] = [
                    'fs'      => $dir['fsPath'],
                    'web'     => $dir['webPath'] ?? '',
                    'options' => $dir['handlerOptions'] ?? [],
                ];
            }
        }

        $libraryJson = array_map(static function (array $dir): array {
            unset($dir['_key'], $dir['fsPath'], $dir['webPath'], $dir['handlerOptions']);
            return $dir;
        }, $libraryDirs);

        $show = static fn(string $key): bool => isset($visible[$key]);

        $editorId   = 'md-editor-' . substr(md5(uniqid('', true)), 0, 8);
        $themeAttr  = htmlspecialchars($themeMode, ENT_QUOTES, 'UTF-8');
        $toggleAttr = $showToggle ? 'true' : 'false';
        $syncAttr   = $themeSync !== null
            ? ' data-md-theme-sync="' . htmlspecialchars($themeSync, ENT_QUOTES, 'UTF-8') . '"'
            : '';

        ob_start(); ?>
        <style>
            /* ── Light theme ── */
            #<?= $editorId ?>[data-md-resolved-theme="light"] {
<?= $cssLight ?>
            }
            /* ── Dark theme ── */
            #<?= $editorId ?>[data-md-resolved-theme="dark"] {
<?= $cssDark ?>
            }

            /* Fallback backgrounds when no theme is active (no data-md-resolved-theme set) */
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-wrapper    { background: #ffffff; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-toolbar    { background: #f8fafc; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-section-header { background: #f8fafc; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-editor-area { background: #ffffff; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-stats       { background: #f8fafc; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-preview-area { background: #ffffff; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-modal       { background: #ffffff; }
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-modal-header,
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-modal-footer,
            #<?= $editorId ?>:not([data-md-resolved-theme]) .md-modal-sidebar { background: #f8fafc; }
            /* ── Wrapper: only override when a theme is active ── */
            #<?= $editorId ?>[data-md-resolved-theme] .md-wrapper {
                background:    var(--md-surface) !important;
                box-shadow:    var(--md-shadow);
                outline:       1px solid var(--md-ring);
            }
            /* Toolbar container */
            #<?= $editorId ?>[data-md-resolved-theme] .md-toolbar {
                background:   var(--md-toolbar-bg) !important;
                border-color: var(--md-toolbar-border);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-toolbar-row {
                border-color: var(--md-toolbar-border);
            }
            /* Plain toolbar buttons */
            #<?= $editorId ?>[data-md-resolved-theme] .md-toolbar-btn {
                color: var(--md-toolbar-text) !important;
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-toolbar-btn:hover {
                background: var(--md-toolbar-btn-hover) !important;
            }
            /* Preview + theme-toggle buttons */
            #<?= $editorId ?>[data-md-resolved-theme] .md-toggle-btn {
                background: var(--md-surface) !important;
                color:      var(--md-toolbar-text) !important;
                outline:    1px solid var(--md-ring);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-toggle-btn:hover {
                background: var(--md-surface-alt) !important;
            }
            /* Dark accent buttons (Library, table insert) */
            #<?= $editorId ?>[data-md-resolved-theme] .md-accent-btn {
                background: var(--md-accent) !important;
                color:      var(--md-accent-text) !important;
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-accent-btn:hover {
                background: var(--md-accent-hover) !important;
            }
            /* Blue insert button in library */
            #<?= $editorId ?>[data-md-resolved-theme] .md-insert-btn {
                background: var(--md-insert-bg) !important;
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-insert-btn:hover {
                background: var(--md-insert-hover) !important;
            }
            /* Section headers */
            #<?= $editorId ?>[data-md-resolved-theme] .md-section-header {
                background:   var(--md-surface-alt) !important;
                border-color: var(--md-editor-border);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-section-label {
                color: var(--md-muted) !important;
            }
            /* Editor textarea */
            #<?= $editorId ?>[data-md-resolved-theme] .md-editor-area {
                background:  var(--md-editor-bg) !important;
                color:       var(--md-editor-text) !important;
                font-family: var(--md-font-mono);
                font-size:   var(--md-font-size);
                line-height: var(--md-line-height);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-editor-area::placeholder {
                color: var(--md-editor-placeholder) !important;
            }
            /* Stats bar */
            #<?= $editorId ?>[data-md-resolved-theme] .md-stats {
                background:   var(--md-surface-alt) !important;
                border-color: var(--md-editor-border);
                color:        var(--md-muted) !important;
            }
            /* Preview panel */
            #<?= $editorId ?>[data-md-resolved-theme] .md-preview-area {
                background: var(--md-preview-bg) !important;
                color:      var(--md-preview-text) !important;
            }
            /* Table popover */
            #<?= $editorId ?>[data-md-resolved-theme] .md-popover {
                background: var(--md-surface) !important;
                outline:    1px solid var(--md-ring);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-popover-label { color: var(--md-muted) !important; }
            #<?= $editorId ?>[data-md-resolved-theme] .md-popover-input {
                background:   var(--md-editor-bg) !important;
                color:        var(--md-editor-text) !important;
                border-color: var(--md-editor-border);
            }
            /* Library modal */
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal        { background: var(--md-surface) !important; }
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-header,
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-footer  {
                background:   var(--md-surface-alt) !important;
                border-color: var(--md-toolbar-border);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-sidebar {
                background:   var(--md-surface-alt) !important;
                border-color: var(--md-toolbar-border);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-search {
                background:   var(--md-editor-bg) !important;
                color:        var(--md-editor-text) !important;
                border-color: var(--md-toolbar-border);
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-close,
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-cancel  { color: var(--md-muted) !important; }
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-close:hover,
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-cancel:hover {
                background: var(--md-editor-bg) !important;
                color:      var(--md-editor-text) !important;
            }
            #<?= $editorId ?>[data-md-resolved-theme] .md-modal-muted  { color: var(--md-muted) !important; }
        </style>

        <div id="<?= $editorId ?>" class="mx-auto max-w-7xl p-6"
             data-md-theme="<?= $themeAttr ?>"
             data-md-theme-toggle="<?= $toggleAttr ?>"<?= $syncAttr ?>>

            <section class="md-wrapper overflow-hidden rounded-3xl shadow-lg ring-1 ring-slate-200">

                <!-- Toolbar -->
                <div class="md-toolbar divide-y divide-slate-200 border-b border-slate-200">
                <?php foreach ($buttonRows as $rowIndex => $row):
                    $isLastRow = ($rowIndex === count($buttonRows) - 1);
                ?>
                    <div class="md-toolbar-row flex flex-wrap items-center gap-2 px-4 py-2">

                    <?php foreach ($row as $btn): switch ($btn):

                        case 'bold': ?>
                        <button type="button" id="btn-bold" data-action="bold"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm font-bold hover:bg-white">B</button>
                        <?php break; case 'italic': ?>
                        <button type="button" id="btn-italic" data-action="italic"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm italic hover:bg-white">I</button>
                        <?php break; case 'underline': ?>
                        <button type="button" id="btn-underline" data-action="underline"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm underline hover:bg-white">U</button>
                        <?php break; case 'strikethrough': ?>
                        <button type="button" id="btn-strikethrough" data-action="strikethrough"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm line-through hover:bg-white">S</button>
                        <?php break; case 'h1': ?>
                        <button type="button" id="btn-h1" data-action="h1"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">H1</button>
                        <?php break; case 'h2': ?>
                        <button type="button" id="btn-h2" data-action="h2"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">H2</button>
                        <?php break; case 'h3': ?>
                        <button type="button" id="btn-h3" data-action="h3"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">H3</button>
                        <?php break; case 'list': ?>
                        <button type="button" id="btn-list" data-action="list"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">Liste</button>
                        <?php break; case 'quote': ?>
                        <button type="button" id="btn-quote" data-action="quote"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">Zitat</button>
                        <?php break; case 'code': ?>
                        <button type="button" id="btn-code" data-action="code"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">Code</button>
                        <?php break; case 'link': ?>
                        <button type="button" id="btn-link" data-action="link"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">Link</button>
                        <?php break; case 'hr': ?>
                        <button type="button" id="btn-hr" data-action="hr"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">—</button>
                        <?php break; case 'image-url': ?>
                        <button type="button" id="btn-image-url" data-action="image-url"
                            class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">Bild</button>
                        <?php break; case 'table': ?>
                        <div class="relative">
                            <button type="button" id="btn-table"
                                class="md-toolbar-btn rounded-lg px-3 py-2 text-sm hover:bg-white">Tabelle</button>
                            <div id="tablePopover"
                                class="md-popover absolute left-0 top-full z-30 mt-2 hidden w-60 bg-white p-4 shadow-xl">
                                <p class="md-popover-label mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Tabelle einfügen</p>
                                <div class="mb-3 grid grid-cols-2 gap-2">
                                    <label class="md-popover-label flex flex-col gap-1 text-xs text-slate-500">
                                        Spalten
                                        <input type="number" id="tableCols" value="3" min="1" max="10"
                                            class="md-popover-input rounded-lg border border-slate-200 px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                                    </label>
                                    <label class="md-popover-label flex flex-col gap-1 text-xs text-slate-500">
                                        Zeilen
                                        <input type="number" id="tableRows" value="2" min="1" max="20"
                                            class="md-popover-input rounded-lg border border-slate-200 px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                                    </label>
                                </div>
                                <button type="button" id="tableInsert"
                                    class="md-accent-btn w-full rounded-xl px-4 py-2 text-sm font-semibold bg-slate-900 text-white hover:bg-slate-700">
                                    Einfügen
                                </button>
                            </div>
                        </div>
                        <?php break; case 'library': ?>
                        <button type="button" id="openLibrary"
                            class="md-accent-btn rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Bibliothek
                        </button>
                        <?php break;

                    endswitch; endforeach; ?>

                    <?php if ($isLastRow): ?>
                    <div class="ml-auto flex items-center gap-2">
                        <?php if ($showToggle): ?>
                        <button type="button" id="md-theme-toggle"
                            class="md-toggle-btn rounded-xl px-3 py-2 text-slate-500 ring-1 ring-slate-200 hover:bg-white"
                            aria-label="Toggle dark mode">
                            <svg class="md-theme-icon-sun" width="15" height="15" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <circle cx="12" cy="12" r="5"/>
                                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                            </svg>
                            <svg class="md-theme-icon-moon" width="15" height="15" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </button>
                        <?php endif; ?>
                        <button type="button" id="togglePreview"
                            class="md-toggle-btn rounded-xl bg-white px-4 py-2 text-sm font-semibold ring-1 ring-slate-200 hover:bg-slate-50">
                            Vorschau anzeigen
                        </button>
                    </div>
                    <?php endif; ?>

                    </div>
                <?php endforeach; ?>
                </div><!-- /toolbar -->

                <!-- Editor + Preview -->
                <div id="editorLayout" class="grid grid-cols-1 lg:grid-cols-1" style="height:<?= $heightPx ?>px">

                    <section class="flex min-h-0 flex-col">
                        <div class="md-section-header border-b border-slate-100 px-5 py-3">
                            <h2 class="md-section-label text-sm font-semibold uppercase tracking-wide text-slate-500">Editor</h2>
                        </div>
                        <textarea
                            id="editor"
                            class="md-editor-area flex-1 resize-none overflow-y-auto p-5 font-mono text-sm leading-7 outline-none"
                            placeholder="Schreibe hier Markdown ..."
                            spellcheck="false"
                            <?= $localStorageKey !== null ? 'data-storage-key="' . htmlspecialchars($localStorageKey, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            <?= $fieldId !== null         ? 'data-field-id="'    . htmlspecialchars($fieldId,         ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                        ></textarea>
                        <div id="stats" class="md-stats border-t border-slate-100 px-5 py-3 text-xs text-slate-500">
                            0 Wörter · 0 Zeichen · 0 Zeilen
                        </div>
                    </section>

                    <section id="previewPanel" class="md-preview-area hidden min-h-0 flex-col">
                        <div class="md-section-header border-b border-slate-100 px-5 py-3">
                            <h2 class="md-section-label text-sm font-semibold uppercase tracking-wide text-slate-500">Vorschau</h2>
                        </div>
                        <article id="preview" class="prose prose-slate max-w-none flex-1 overflow-y-auto p-8"></article>
                    </section>

                </div>
            </section>
        <!-- /md-wrapper; library backdrop follows inside #editorId for CSS scoping -->

        <?php if ($fieldId !== null): ?>
        <textarea data-md-sync hidden></textarea>
        <?php endif; ?>

        <?php if ($show('library')): ?>
        <div id="libraryBackdrop"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            data-library="<?= htmlspecialchars(json_encode($libraryJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
            data-max-upload="<?= htmlspecialchars($maxUpload, ENT_QUOTES, 'UTF-8') ?>">

            <section class="md-modal flex max-h-[88vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl shadow-2xl">

                <header class="md-modal-header flex items-center gap-3 border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold">Bildbibliothek</h2>
                        <p class="md-modal-muted text-xs text-slate-500">Wähle ein Bild aus der Bibliothek.</p>
                    </div>
                    <input id="librarySearch"
                        class="md-modal-search ml-auto w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500"
                        placeholder="Bilder suchen ...">
                    <button type="button" id="closeLibrary"
                        class="md-modal-close rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900">✕</button>
                </header>

                <div class="grid min-h-[460px] flex-1 grid-cols-1 overflow-hidden md:grid-cols-[220px_1fr]">
                    <aside class="md-modal-sidebar border-b border-slate-200 p-4 md:border-b-0 md:border-r">
                        <nav id="libraryNav" class="space-y-1 text-sm"></nav>
                    </aside>
                    <div id="libraryGridWrapper" class="relative overflow-y-auto p-5">
                        <div id="libraryGrid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"></div>
                        <input id="libraryFileInput" type="file" accept="image/*" multiple class="sr-only">
                        <div id="libraryDropOverlay"
                            class="pointer-events-none absolute inset-0 hidden items-center justify-center rounded-2xl border-2 border-dashed border-blue-400 bg-blue-50/80">
                            <div class="text-center">
                                <p class="text-base font-semibold text-blue-600">Bilder hier ablegen</p>
                                <p class="text-sm text-blue-400">JPG, PNG, GIF, WebP</p>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="md-modal-footer flex items-center gap-3 border-t border-slate-200 px-5 py-4">
                    <div class="flex items-baseline gap-2">
                        <span id="librarySelectionCount" class="md-modal-muted text-sm text-slate-500">0 Bilder ausgewählt</span>
                        <span id="libraryMaxUpload" class="hidden text-xs text-slate-400"></span>
                    </div>
                    <button type="button" id="cancelLibrary"
                        class="md-modal-cancel ml-auto rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">
                        Abbrechen
                    </button>
                    <button type="button" data-action="insert-image"
                        class="md-insert-btn rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-40"
                        disabled>
                        Einfügen
                    </button>
                </footer>
            </section>
        </div>
        <?php endif; ?>

        </div><!-- /#editorId -->
        <?php
        return ob_get_clean();
    }

    /**
     * Kombinierter GET/POST-Handler für Bildbibliothek-Endpunkte.
     */
    public static function handleRequest(string $fsDirectory, string $webPath, array $options = []): never
    {
        ob_start();
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        try {
            ob_end_clean();
            match ($_SERVER['REQUEST_METHOD'] ?? 'GET') {
                'POST'  => self::handleUploadRequest($fsDirectory, $webPath, $options),
                'GET'   => self::handleListRequest($fsDirectory, $webPath, $options),
                default => self::jsonError(405, 'Method not allowed'),
            };
        } catch (\Throwable $e) {
            ob_end_clean();
            self::jsonError(500, $e->getMessage());
        }
        exit;
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private static function handleListRequest(string $fsDir, string $webPath, array $options = []): void
    {
        if (!is_dir($fsDir)) { self::jsonError(404, 'Verzeichnis existiert nicht. (' . $fsDir . ')'); return; }
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'];
        $exts      = array_map('strtolower', $options['listExtensions'] ?? $imageExts);
        $recursive = !empty($_GET['recursive']);
        $files     = [];
        $fsDir     = rtrim($fsDir, '/\\');
        if ($recursive) {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fsDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($it as $file) {
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, $exts, true)) continue;
                $rel   = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen($fsDir)));
                $isImg = in_array($ext, $imageExts, true);
                $entry = ['src' => rtrim($webPath, '/') . $rel, 'name' => $file->getFilename(), 'type' => $isImg ? 'image' : 'file', 'mtime' => $file->getMTime()];
                if ($isImg) $entry += self::imageSize($file->getPathname());
                $files[] = $entry;
            }
        } else {
            foreach (glob($fsDir . DIRECTORY_SEPARATOR . '*.{' . implode(',', $exts) . '}', GLOB_BRACE) ?: [] as $path) {
                $ext   = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $isImg = in_array($ext, $imageExts, true);
                $entry = ['src' => rtrim($webPath, '/') . '/' . basename($path), 'name' => basename($path), 'type' => $isImg ? 'image' : 'file', 'mtime' => filemtime($path)];
                if ($isImg) $entry += self::imageSize($path);
                $files[] = $entry;
            }
        }
        usort($files, static fn($a, $b) => $b['mtime'] <=> $a['mtime']);
        echo json_encode(array_map(static fn($f) => array_diff_key($f, ['mtime' => 0]), $files));
    }

    private static function imageSize(string $path): array
    {
        [$w, $h] = @getimagesize($path) ?: [null, null];
        return ['width' => $w, 'height' => $h];
    }

    private static function handleUploadRequest(string $fsDir, string $webPath, array $options): void
    {
        if (empty($_FILES['file'])) { self::jsonError(400, 'Keine Datei empfangen.'); return; }
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) { self::jsonError(400, 'Upload-Fehler (Code ' . $file['error'] . ').'); return; }
        $allowed  = $options['types'] ?? ['image/jpeg','image/png','image/gif','image/webp','image/avif'];
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mimeType, $allowed, true)) { self::jsonError(415, 'Ungültiger Dateityp: ' . $mimeType); return; }
        $maxBytes = $options['maxSize'] ?? self::iniSizeToBytes(ini_get('upload_max_filesize'));
        if ($file['size'] > $maxBytes) { self::jsonError(413, 'Datei überschreitet das Limit.'); return; }
        if (!is_dir($fsDir) && !mkdir($fsDir, 0755, true)) { self::jsonError(500, 'Verzeichnis konnte nicht erstellt werden.'); return; }
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $stem     = preg_replace('/[^a-zA-Z0-9_-]+/', '-', pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = trim($stem, '-') . '_' . substr(uniqid(), -6) . '.' . $ext;
        $target   = rtrim($fsDir, '/\\') . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) { self::jsonError(500, 'Datei konnte nicht gespeichert werden.'); return; }
        [$width, $height] = @getimagesize($target) ?: [null, null];
        echo json_encode(['src' => rtrim($webPath, '/') . '/' . $filename, 'name' => $filename, 'width' => $width, 'height' => $height]);
    }

    private static function resolveLibraryPaths(array $dirs, string $callerFile): array
    {
        if (empty($dirs)) return [];
        $callerDir = dirname($callerFile);
        $docRoot   = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ''), '/');
        return array_values(array_map(function (array $dir) use ($callerDir, $docRoot): array {
            $path = trim($dir['path'] ?? '');
            if ($path === '' || str_starts_with($path, '/') || preg_match('#^https?://#', $path)) return $dir;
            $abs = realpath($callerDir . DIRECTORY_SEPARATOR . $path);
            if ($abs === false) return $dir;
            $abs = str_replace(DIRECTORY_SEPARATOR, '/', $abs);
            if ($docRoot !== '' && str_starts_with($abs, $docRoot)) {
                $dir['path'] = '/' . ltrim(substr($abs, strlen($docRoot)), '/');
            }
            return $dir;
        }, $dirs));
    }

    private static function maxUploadSize(): string
    {
        $upload = ini_get('upload_max_filesize') ?: '2M';
        $post   = ini_get('post_max_size')       ?: '8M';
        return self::iniSizeToBytes($upload) <= self::iniSizeToBytes($post) ? $upload : $post;
    }

    private static function iniSizeToBytes(string $val): int
    {
        $val = trim($val); $unit = strtolower(substr($val, -1)); $n = (int) $val;
        return match ($unit) { 'g' => $n * 1_073_741_824, 'm' => $n * 1_048_576, 'k' => $n * 1_024, default => $n };
    }

    private static function jsonError(int $status, string $message): void
    {
        http_response_code($status);
        echo json_encode(['error' => $message]);
    }

    public static function renderFoot(): string
    {
        $publicPath = self::detectPublicPath();
        return sprintf('<script src="%s/markdown-editor.js"></script>',
            htmlspecialchars($publicPath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    }

    private static function detectPublicPath(): string
    {
        $publicDir    = realpath(__DIR__ . '/../public');
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        if ($publicDir === false || $documentRoot === false) return '';
        $publicDir    = str_replace(DIRECTORY_SEPARATOR, '/', $publicDir);
        $documentRoot = str_replace(DIRECTORY_SEPARATOR, '/', $documentRoot);
        if (!str_starts_with($publicDir, $documentRoot)) return '';
        return rtrim(substr($publicDir, strlen($documentRoot)), '/');
    }
}