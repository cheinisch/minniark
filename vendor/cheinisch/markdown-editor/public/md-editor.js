/**
 * cheinisch/markdown-editor  –  md-editor.js  v1.0.0
 *
 * Self-contained Markdown editor. No external dependencies.
 * Theme is controlled entirely via CSS custom properties (--md-*).
 * See md-editor.css for the full list of available variables.
 *
 * Initialisation:
 *   Automatic: add [data-md-editor] to a wrapper <div> containing a <textarea>.
 *   Manual:    MinniarkMarkdownEditor.init(wrapElement)
 *              MinniarkMarkdownEditor.initAll()   // re-scans the DOM
 *
 * Optional wrapper attributes:
 *   data-toolbar='["bold","italic","|","preview"]'   custom button set
 *
 * Public API (set on the wrapper element as .mdEditor):
 *   .getValue()    → string   current Markdown
 *   .setValue(str) → void     replace content + snapshot
 *   .destroy()     → void     restore original <textarea>, remove editor chrome
 */

(function () {
    'use strict';

    // ── Minimal Markdown → HTML renderer ─────────────────────────────────

    function mdToHtml(md) {
        if (!md) return '';

        let html = md
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Fenced code blocks
        html = html.replace(/```([^\n]*)\n([\s\S]*?)```/g,
            (_, lang, code) => `<pre><code class="language-${lang.trim()}">${code.trim()}</code></pre>`
        );

        // Headings
        html = html.replace(/^(#{1,6})\s+(.+)$/gm,
            (_, hashes, text) => `<h${hashes.length}>${text}</h${hashes.length}>`
        );

        // Horizontal rule
        html = html.replace(/^(-{3,}|\*{3,})$/gm, '<hr>');

        // Blockquote
        html = html.replace(/^&gt;\s?(.*)$/gm, '<blockquote>$1</blockquote>');

        // Lists
        html = html.replace(/^[\*\-\+]\s+(.+)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>(\n|$))+/g, m => `<ul>${m}</ul>`);
        html = html.replace(/^\d+\.\s+(.+)$/gm, '<li>$1</li>');

        // Bold + italic
        html = html.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
        html = html.replace(/\*\*(.+?)\*\*/g,     '<strong>$1</strong>');
        html = html.replace(/\*(.+?)\*/g,          '<em>$1</em>');
        html = html.replace(/___(.+?)___/g,         '<strong><em>$1</em></strong>');
        html = html.replace(/__(.+?)__/g,           '<strong>$1</strong>');
        html = html.replace(/_(.+?)_/g,             '<em>$1</em>');

        // Inline code
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Links
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g,
            '<a href="$2" target="_blank" rel="noopener">$1</a>'
        );

        // Paragraphs
        const blockTags = /^<(h[1-6]|ul|ol|li|pre|blockquote|hr)/;
        html = html.split(/\n{2,}/).map(block => {
            block = block.trim();
            if (!block || blockTags.test(block)) return block;
            return `<p>${block.replace(/\n/g, '<br>')}</p>`;
        }).join('\n');

        return html;
    }

    // ── Toolbar button definitions ────────────────────────────────────────

    const BUTTONS = {
        bold:            { label: 'B',  title: 'Bold (Ctrl+B)',      style: 'font-weight:700;font-style:normal', action: 'wrap',     wrap: ['**', '**'] },
        italic:          { label: 'I',  title: 'Italic (Ctrl+I)',    style: 'font-style:italic',                 action: 'wrap',     wrap: ['*', '*']   },
        heading:         { label: 'H',  title: 'Heading',            style: 'font-weight:600',                   action: 'heading'                      },
        quote:           { label: '❝',  title: 'Blockquote',                                                     action: 'prefix',   prefix: '> '       },
        'unordered-list':{ label: '•',  title: 'Bullet list',                                                    action: 'prefix',   prefix: '- '       },
        'ordered-list':  { label: '1.', title: 'Numbered list',                                                  action: 'prefix',   prefix: '1. '      },
        link:            { label: '🔗', title: 'Insert link',                                                    action: 'link'                         },
        undo:            { label: '↩',  title: 'Undo (Ctrl+Z)',                                                  action: 'undo'                         },
        redo:            { label: '↪',  title: 'Redo (Ctrl+Y)',                                                  action: 'redo'                         },
        preview:         { label: '👁', title: 'Toggle preview',                                                  action: 'preview'                      },
        fullscreen:      { label: '⤢',  title: 'Toggle fullscreen',                                              action: 'fullscreen'                   },
        '|': null,
    };

    // ── Undo/Redo history stack ───────────────────────────────────────────

    function createHistory(textarea) {
        const stack  = [textarea.value];
        let   cursor = 0;
        let   timer  = null;

        function snapshot() {
            const val = textarea.value;
            if (val === stack[cursor]) return;
            stack.splice(cursor + 1);
            stack.push(val);
            cursor = stack.length - 1;
        }

        function scheduleSnapshot() {
            clearTimeout(timer);
            timer = setTimeout(snapshot, 400);
        }

        function undo() {
            clearTimeout(timer);
            if (cursor > 0) { cursor--; textarea.value = stack[cursor]; textarea.dispatchEvent(new Event('input')); }
        }

        function redo() {
            clearTimeout(timer);
            if (cursor < stack.length - 1) { cursor++; textarea.value = stack[cursor]; textarea.dispatchEvent(new Event('input')); }
        }

        return { snapshot, scheduleSnapshot, undo, redo };
    }

    // ── Text manipulation helpers ─────────────────────────────────────────

    function wrapSelection(ta, before, after) {
        const start = ta.selectionStart, end = ta.selectionEnd;
        ta.setRangeText(before + ta.value.slice(start, end) + after, start, end, 'select');
        ta.focus();
    }

    function prefixLines(ta, prefix) {
        const start = ta.selectionStart, end = ta.selectionEnd;
        const lines = ta.value.slice(start, end).split('\n');
        const allPrefixed = lines.every(l => l.startsWith(prefix));
        ta.setRangeText(
            (allPrefixed ? lines.map(l => l.slice(prefix.length)) : lines.map(l => prefix + l)).join('\n'),
            start, end, 'select'
        );
        ta.focus();
    }

    function headingAction(ta) {
        const start = ta.value.lastIndexOf('\n', ta.selectionStart - 1) + 1;
        const end   = ta.value.indexOf('\n', ta.selectionStart);
        const line  = ta.value.slice(start, end === -1 ? undefined : end);
        const match = line.match(/^(#{1,6})\s/);
        const newLine = !match ? '# ' + line
            : match[1].length < 6 ? '#' + line
            : line.replace(/^#{6}\s/, '');
        ta.setRangeText(newLine, start, end === -1 ? ta.value.length : end, 'end');
        ta.focus();
    }

    function linkAction(ta) {
        const sel = ta.value.slice(ta.selectionStart, ta.selectionEnd) || 'Link text';
        wrapSelection(ta, '[' + (sel === 'Link text' ? '' : sel), '](https://)');
    }

    // ── Core init ─────────────────────────────────────────────────────────

    function init(wrap) {
        if (!wrap || wrap.mdEditor) return; // already initialised or invalid

        const textarea = wrap.querySelector('textarea');
        if (!textarea) return;

        // Parse toolbar
        let toolbarKeys = ['bold','italic','heading','|','quote','unordered-list','ordered-list','|','link','|','preview','fullscreen'];
        try { if (wrap.dataset.toolbar) toolbarKeys = JSON.parse(wrap.dataset.toolbar); } catch {}

        // Build toolbar
        const toolbar = document.createElement('div');
        toolbar.className = 'md-editor-toolbar';
        toolbar.setAttribute('role', 'toolbar');
        toolbar.setAttribute('aria-label', 'Markdown editor toolbar');

        // Build panes
        const panes = document.createElement('div');
        panes.className = 'md-editor-panes';
        panes.appendChild(textarea);
        textarea.classList.add('md-editor-textarea');

        const preview = document.createElement('div');
        preview.className = 'md-editor-preview';
        preview.setAttribute('aria-label', 'Markdown preview');
        panes.appendChild(preview);

        wrap.appendChild(toolbar);
        wrap.appendChild(panes);

        // History
        const history = createHistory(textarea);
        textarea.addEventListener('input', history.scheduleSnapshot);

        // State
        let previewActive = false;

        function performAction(key, btn) {
            history.snapshot();
            switch (BUTTONS[key]?.action) {
                case 'wrap':       wrapSelection(textarea, BUTTONS[key].wrap[0], BUTTONS[key].wrap[1]); break;
                case 'prefix':     prefixLines(textarea, BUTTONS[key].prefix); break;
                case 'heading':    headingAction(textarea); break;
                case 'link':       linkAction(textarea); break;
                case 'undo':       history.undo(); break;
                case 'redo':       history.redo(); break;
                case 'preview':
                    previewActive = !previewActive;
                    wrap.classList.toggle('preview-mode', previewActive);
                    preview.innerHTML = mdToHtml(textarea.value);
                    btn?.classList.toggle('active', previewActive);
                    break;
                case 'fullscreen':
                    wrap.classList.toggle('fullscreen');
                    btn?.classList.toggle('active', wrap.classList.contains('fullscreen'));
                    break;
            }
            history.scheduleSnapshot();
            textarea.dispatchEvent(new Event('input'));
        }

        // Render toolbar buttons
        toolbarKeys.forEach(key => {
            if (key === '|') {
                const sep = document.createElement('span');
                sep.className = 'md-sep';
                sep.setAttribute('aria-hidden', 'true');
                toolbar.appendChild(sep);
                return;
            }
            const def = BUTTONS[key];
            if (!def) return;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = def.label;
            btn.title = def.title || key;
            btn.setAttribute('aria-label', def.title || key);
            if (def.style) btn.style.cssText = def.style;
            btn.addEventListener('click', () => performAction(key, btn));
            toolbar.appendChild(btn);
        });

        // Keyboard shortcuts
        textarea.addEventListener('keydown', e => {
            if (e.key === 'Tab') { e.preventDefault(); document.execCommand('insertText', false, '  '); return; }
            if (!(e.ctrlKey || e.metaKey)) return;
            const map = { b: 'bold', i: 'italic', z: 'undo', y: 'redo' };
            const key = map[e.key.toLowerCase()];
            if (key) { e.preventDefault(); performAction(key, toolbar.querySelector(`[aria-label="${BUTTONS[key]?.title}"]`)); }
        });

        // Escape fullscreen
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && wrap.classList.contains('fullscreen')) {
                wrap.classList.remove('fullscreen');
                toolbar.querySelector('[aria-label="Toggle fullscreen"]')?.classList.remove('active');
            }
        });

        // Public API
        wrap.mdEditor = {
            getValue:  ()    => textarea.value,
            setValue:  (val) => { textarea.value = val; history.snapshot(); },
            destroy:   ()    => {
                textarea.classList.remove('md-editor-textarea');
                toolbar.remove();
                preview.remove();
                wrap.insertBefore(textarea, panes);
                panes.remove();
                delete wrap.mdEditor;
            },
        };
    }

    // ── Auto-init ─────────────────────────────────────────────────────────

    function initAll() {
        document.querySelectorAll('[data-md-editor]').forEach(wrap => {
            // Skip wrappers inside hidden containers — they'll be lazy-inited
            // when made visible (see MinniarkMarkdownEditor.init(wrap) call pattern).
            if (!wrap.mdEditor) init(wrap);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Global API
    window.MinniarkMarkdownEditor = { init, initAll, mdToHtml };

})();
