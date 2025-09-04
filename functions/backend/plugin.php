<?php

function renderPluginSettings(string $pluginDir): void
{
    $pluginKey = basename($pluginDir);
    $pluginJson = $pluginDir . '/plugin.json';
    $settingsJson = $pluginDir . '/settings.json';

    if (!file_exists($pluginJson)) return;

    $meta = json_decode(file_get_contents($pluginJson), true);
    if (!is_array($meta)) return;

    if (!file_exists($settingsJson)) {
        file_put_contents($settingsJson, json_encode(['enabled' => false], JSON_PRETTY_PRINT));
    }

    $settings = json_decode(file_get_contents($settingsJson), true) ?? [];

    $fields = $meta['settings']['fields'] ?? [];
    $name = htmlspecialchars($meta['name'] ?? $pluginKey);
    $author = htmlspecialchars($meta['author'] ?? 'Unknown');
    $url = trim($meta['url'] ?? '');
    $version = htmlspecialchars($meta['version'] ?? '0.0');
    $note = htmlspecialchars($meta['note'] ?? '');
    $enabled = !empty($settings['enabled']) ? 'true' : 'false';
    $isSaved = isset($_GET['saved'], $_GET['plugin']) && $_GET['saved'] === '1' && $_GET['plugin'] === $pluginKey;

    echo <<<HTML
        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
        <h2 class="text-base font-semibold text-gray-700 dark:text-white">{$name}</h2>
        <p class="mt-1 text-sm text-gray-400">Version: {$version}</p>
        <p class="mt-1 text-sm text-gray-400">Author: {$author}</p>
        HTML;

    if (!empty($url)) {
        $safeUrl = htmlspecialchars($url);
        echo <<<HTML
        <p class="mt-1 text-sm text-gray-400">Website: <a href="{$safeUrl}" class="text-sky-600 hover:text-sky-500" target="_blank" rel="noopener noreferrer">{$safeUrl}</a></p>
    HTML;
    }

echo <<<HTML
    
  </div>

  <form class="md:col-span-2" action="backend_api/plugin_save.php" method="post">
    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
    <input type="hidden" name="plugin" value="{$pluginKey}">
HTML;

    if ($isSaved) {
        echo <<<HTML
    <div class="mb-4 col-span-full bg-green-100 px-4 py-2 text-sm text-green-800 shadow-inner ring-1 ring-inset ring-green-300">
      Settings saved successfully
    </div>
HTML;
    }

    if (!empty($note)) {
        echo <<<HTML
    <div class="mb-4 col-span-full bg-gray-100 px-4 py-2 text-sm text-gray-800 shadow-inner ring-1 ring-inset ring-gray-300">
      Note: {$note}
    </div>
HTML;
    }

    echo <<<HTML
    <div class="col-span-full">
      <div class="flex items-center justify-between">
        <span class="flex grow flex-col">
          <span class="text-sm font-medium text-gray-900 dark:text-white">Enable Plugin</span>
          <span class="text-sm text-gray-500">Toggles this plugin on or off globally</span>
        </span>
        <button type="button" data-plugin="{$pluginKey}" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-400 transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-sky-600 focus:ring-offset-2" role="switch" aria-checked="{$enabled}">
          <span aria-hidden="true" class="pointer-events-none inline-block size-5 translate-x-0 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"></span>
        </button>
        <input type="hidden" name="enabled" id="enabled-input-{$pluginKey}" value="{$enabled}">
      </div>
    </div>
HTML;

    foreach ($fields as $field) {
        $key = $field['key'];
        $label = htmlspecialchars($field['label'] ?? ucfirst($key));
        $value = $settings[$key] ?? $field['default'] ?? '';
        $hint = htmlspecialchars($field['hint'] ?? '');
        $type = $field['type'] ?? 'text';

        switch ($type) {
            case 'text':
            case 'number':
            case 'password':
                $escapedValue = $type === 'password' ? '' : htmlspecialchars($value);
                echo <<<HTML
    <div class="sm:col-span-full">
      <label for="{$key}" class="block text-sm font-medium text-gray-700 dark:text-white">{$label}</label>
      <div class="mt-2">
        <input type="{$type}" name="{$key}" id="{$key}" value="{$escapedValue}" class="block w-full bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline outline-1 outline-gray-500 dark:outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:outline-sky-500 sm:text-sm">
      </div>
      <p class="mt-1 text-xs text-gray-500">{$hint}</p>
    </div>
HTML;
                break;

            case 'select':
                echo <<<HTML
    <div class="sm:col-span-full">
      <label for="{$key}" class="block text-sm font-medium text-gray-700 dark:text-white">{$label}</label>
      <div class="mt-2">
        <select name="{$key}" id="{$key}" class="block w-full bg-white/5 px-3 py-1.5 text-base text-gray-700 dark:text-white outline outline-1 outline-gray-500 dark:outline-white/10 focus:outline-2 focus:outline-sky-500 sm:text-sm">
HTML;
                foreach ($field['options'] ?? [] as $opt) {
                    $selected = ($opt == $value) ? 'selected' : '';
                    $optEsc = htmlspecialchars($opt);
                    echo "<option value=\"{$optEsc}\" {$selected}>{$optEsc}</option>";
                }
                echo <<<HTML
        </select>
      </div>
      <p class="mt-1 text-xs text-gray-500">{$hint}</p>
    </div>
HTML;
                break;

            case 'toggle':
                $checked = $value ? 'checked' : '';
                echo <<<HTML
    <div class="col-span-full">
      <div class="flex items-center justify-between">
        <span class="flex grow flex-col">
          <span class="text-sm font-medium text-gray-900 dark:text-white">{$label}</span>
          <span class="text-sm text-gray-500">{$hint}</span>
        </span>
        <input type="checkbox" name="{$key}" value="1" class="h-5 w-10 rounded-full bg-gray-300 checked:bg-sky-600 transition-colors duration-200 ease-in-out" {$checked}>
      </div>
    </div>
HTML;
                break;
        }
    }

    echo <<<HTML
    <div class="mt-8 flex">
      <button type="submit" class="bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-sky-400 focus:outline-sky-500"><?php echo languageString('general.save'); ?></button>
    </div>
    </div>
  </form>
</div>
HTML;
}
