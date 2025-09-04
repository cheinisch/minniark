<?php
use PHPUnit\Framework\TestCase;
use OpenRouter\Client;

class ClientTest extends TestCase {
    public function testChat() {
        $client = new Client(getenv('OPENROUTER_API_KEY'));
        $reply = $client->chat([["role" => "user", "content" => "Hallo Test!"]]);
        $this->assertNotEmpty($reply);
    }
}
