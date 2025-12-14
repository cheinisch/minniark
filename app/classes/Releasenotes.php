<?php

class Releasenotes
{
    /**
     * Basisverzeichnis des Projekts
     */
    protected static function basePath(): string
    {
        return dirname(__DIR__, 2);
        // /app/classes -> /app -> /
    }

    /**
     * Gibt die Version aus der /VERSION-Datei zurück
     */
    public static function version(): string
    {
        $file = self::basePath() . '/VERSION';

        if (!is_readable($file)) {
            return 'unknown';
        }

        return trim(file_get_contents($file));
    }

    /**
     * Gibt die Release Notes als HTML zurück
     * (Markdown wird geparsed)
     */
    public static function text(): string
    {
        $file = self::basePath() . '/Releasenotes.md';

        if (!is_readable($file)) {
            return '<p>No release notes available.</p>';
        }

        $markdown = file_get_contents($file);

        return self::parseMarkdown($markdown);
    }

    /**
     * Markdown-Parser anbinden
     * (hier nur eine zentrale Stelle)
     */
    protected static function parseMarkdown(string $markdown): string
    {
        // Beispiel mit Parsedown
        if (class_exists('Parsedown')) {
            return (new Parsedown())->text($markdown);
        }

        // Fallback (roh)
        return nl2br(htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8'));
    }
}
