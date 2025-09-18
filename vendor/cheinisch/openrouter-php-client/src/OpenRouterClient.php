<?php
namespace cheinisch;

use GuzzleHttp\Client as HttpClient;

class OpenRouterClient {
    private HttpClient $http;

    public function __construct(string $apiKey, array $defaultHeaders = []) {
        $this->http = new HttpClient([
            'base_uri' => 'https://openrouter.ai/api/v1/',
            'headers'  => array_merge([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ], $defaultHeaders),
        ]);
    }

    public function chat(array $messages, string $model = "openai/gpt-4o-mini", array $headers = []): string {
        $response = $this->http->post("chat/completions", [
            'headers' => $headers, // request-spezifische Header
            'json'    => ['model' => $model, 'messages' => $messages],
        ]);

        $data = json_decode((string)$response->getBody(), true);
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Convenience-Wrapper:
     * - $apiKey: OpenRouter API Key
     * - $model: Modell-Slug (z. B. "openai/gpt-4o-mini")
     * - $prompt: Nutzerprompt
     * - $referer: (optional) Quell-URL für Attribution
     * - $title:   (optional) App-/Seitentitel für Attribution
     */
    public static function OpenRouterChat(
        string $apiKey,
        string $model,
        string $prompt,
        ?string $referer = null,
        ?string $title = null
    ): string {
        $client  = new self($apiKey);
        $headers = [];

        if ($referer) { $headers['HTTP-Referer'] = $referer; }
        if ($title)   { $headers['X-Title']      = $title;   }

        return $client->chat([['role' => 'user', 'content' => $prompt]], $model, $headers);
    }
}
