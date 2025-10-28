<?php
require_once __DIR__ . '/..//vendor/autoload.php';
use GuzzleHttp\Client;

class TranslationController {
    private $apiKey;
    private $client;

    public function __construct() {
        // Load API key from config or environment
        $this->apiKey = 'YOUR_DEEPL_API_KEY'; // Replace with your DeepL API key
        $this->client = new Client(['base_uri' => 'https://api-free.deepl.com/v2/']);
    }

    public function translate($text, $targetLang) {
        try {
            $response = $this->client->request('POST', 'translate', [
                'form_params' => [
                    'auth_key' => $this->apiKey,
                    'text' => $text,
                    'target_lang' => strtoupper($targetLang), // e.g., 'EN', 'FR', 'ES'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['translations'][0]['text'];
        } catch (Exception $e) {
            error_log("Translation error: " . $e->getMessage());
            return $text; // Fallback to original text on error
        }
    }

    public function translateArray($texts, $targetLang) {
        $translated = [];
        foreach ($texts as $key => $text) {
            $translated[$key] = $this->translate($text, $targetLang);
        }
        return $translated;
    }
}