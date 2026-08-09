<?php

namespace cheinisch\MarkdownEditor;

/**
 * MarkdownEditor
 *
 * Renders the asset tags and the initialisation snippet for the
 * cheinisch/markdown-editor component.
 *
 * Usage:
 *   $editor = new MarkdownEditor('/vendor/cheinisch/markdown-editor/public');
 *
 *   // In <head>:
 *   echo $editor->css();
 *
 *   // Replace <textarea id="content"> with:
 *   echo $editor->render('content', $existingMarkdown, ['placeholder' => 'Write here…']);
 *
 *   // Before </body>:
 *   echo $editor->js();
 */
class MarkdownEditor
{
    private string $publicPath;

    /**
     * @param string $publicPath  Web-accessible path to the /public directory,
     *                            e.g. '/vendor/cheinisch/markdown-editor/public'
     */
    public function __construct(string $publicPath = '/vendor/cheinisch/markdown-editor/public')
    {
        $this->publicPath = rtrim($publicPath, '/');
    }

    /** Returns the <link> tag for the editor stylesheet. */
    public function css(): string
    {
        return '<link rel="stylesheet" href="' . htmlspecialchars($this->publicPath) . '/md-editor.css">' . PHP_EOL;
    }

    /** Returns the <script> tag for the editor bundle. */
    public function js(): string
    {
        return '<script src="' . htmlspecialchars($this->publicPath) . '/md-editor.js"></script>' . PHP_EOL;
    }

    /**
     * Renders a <textarea> that the JS will upgrade to a full editor.
     *
     * @param string $id       The textarea id (and name attribute).
     * @param string $value    Initial Markdown content.
     * @param array  $options  Optional overrides:
     *                           placeholder  string   Placeholder text
     *                           toolbar      string[] Toolbar button names
     *                           theme        string   'light' | 'dark' | 'auto'
     *                           rows         int      Fallback rows attribute
     *                           class        string   Extra CSS classes on the wrapper
     */
    public function render(
        string $id,
        string $value = '',
        array  $options = []
    ): string {
        $placeholder = htmlspecialchars($options['placeholder'] ?? 'Write Markdown here…');
        $rows        = (int) ($options['rows'] ?? 10);
        $theme       = htmlspecialchars($options['theme'] ?? 'auto');
        $extraClass  = htmlspecialchars($options['class'] ?? '');

        $toolbar = $options['toolbar'] ?? [
            'bold', 'italic', 'heading', '|',
            'quote', 'unordered-list', 'ordered-list', '|',
            'link', '|',
            'preview', 'fullscreen',
        ];
        $toolbarJson = json_encode($toolbar, JSON_UNESCAPED_SLASHES);

        $escapedValue = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return <<<HTML
<div class="md-editor-wrap {$extraClass}"
     data-md-editor
     data-toolbar='{$toolbarJson}'
     data-theme="{$theme}">
    <textarea
        id="{$id}"
        name="{$id}"
        rows="{$rows}"
        placeholder="{$placeholder}"
        class="md-editor-textarea"
    >{$escapedValue}</textarea>
</div>
HTML;
    }
}
