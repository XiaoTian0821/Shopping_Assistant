<?php
/**
 * AI Provider abstraction layer
 * Supports Agnes AI and Google Gemini
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    http_response_code(403);
    exit('Access denied');
}

/**
 * Send chat request to AI provider
 * @param array $messages Array of message arrays with 'role' and 'content'
 * @return array ['success' => bool, 'message' => string, 'response' => array|null]
 */
function ai_chat(array $messages): array {
    $settings = get_ai_settings();
    $provider = $settings['provider'] ?? 'gemini';
    
    switch ($provider) {
        case 'agnes':
            return openai_chat($messages, $settings);
        case 'gemini':
        default:
            return gemini_chat($messages, $settings);
    }
}

/**
 * Send request to OpenAI-compatible API (Agnes AI)
 * @param array $messages
 * @param array $settings
 * @return array
 */
function openai_chat(array $messages, array $settings): array {
    $apiUrl = $settings['api_url'] ?? '';
    $apiKey = $settings['api_key'] ?? '';
    $model = $settings['model'] ?? 'agnes-2.5-flash';
    
    if (empty($apiUrl) || empty($apiKey)) {
        return ['success' => false, 'message' => 'AI configuration is missing. Please configure AI settings.'];
    }
    
    $url = rtrim($apiUrl, '/') . '/chat/completions';
    
    $body = [
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => 500,
        'temperature' => 0.1
    ];
    
    return call_ai_api($url, $apiKey, $body, $settings);
}

/**
 * Send request to Google Gemini API
 * @param array $messages
 * @param array $settings
 * @return array
 */
function gemini_chat(array $messages, array $settings): array {
    $apiUrl = $settings['api_url'] ?? '';
    $apiKey = $settings['api_key'] ?? '';
    $model = $settings['model'] ?? 'gemini-3.8-flash';
    
    if (empty($apiUrl) || empty($apiKey)) {
        return ['success' => false, 'message' => 'AI configuration is missing. Please configure AI settings.'];
    }
    
    // Convert messages to Gemini format
    $contents = [];
    foreach ($messages as $msg) {
        if ($msg['role'] === 'user') {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $msg['content']]]
            ];
        }
    }
    
    $url = rtrim($apiUrl, '/') . '/models/' . rawurlencode($model) . ':generateContent?key=' . urlencode($apiKey);
    
    $body = [
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.1,
            'maxOutputTokens' => 500
        ]
    ];
    
    return call_ai_api($url, '', $body, $settings);
}

/**
 * Make HTTP request to AI API
 * @param string $url
 * @param string $apiKey
 * @param array $body
 * @param array $settings
 * @return array
 */
function call_ai_api(string $url, string $apiKey, array $body, array $settings): array {
    $ch = curl_init();
    
    $headers = [
        'Content-Type: application/json'
    ];
    
    if (!empty($apiKey)) {
        $headers[] = 'Authorization: Bearer ' . $apiKey;
    }
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => AI_TIMEOUT,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log('AI API error: ' . $error);
        return ['success' => false, 'message' => 'AI service error: ' . $error, 'response' => null];
    }
    
    if ($httpCode !== 200) {
        error_log('AI API HTTP error: ' . $httpCode . ' - ' . $response);
        return ['success' => false, 'message' => 'AI service returned HTTP ' . $httpCode, 'response' => null];
    }
    
    $data = json_decode($response, true);
    
    if ($data === null) {
        return ['success' => false, 'message' => 'Invalid response from AI service', 'response' => null];
    }
    
    // Extract response text based on provider format
    $text = null;
    if (isset($data['choices'][0]['message']['content'])) {
        $text = $data['choices'][0]['message']['content'];
    } elseif (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $data['candidates'][0]['content']['parts'][0]['text'];
    }
    
    if ($text === null) {
        return ['success' => false, 'message' => 'Could not parse AI response', 'response' => null];
    }
    
    return [
        'success' => true,
        'message' => 'AI response received',
        'response' => ['text' => $text]
    ];
}

/**
 * Test AI connection
 * @return array
 */
function test_ai_connection(): array {
    $settings = get_ai_settings();
    $provider = $settings['provider'] ?? 'gemini';
    
    $testMessages = [
        ['role' => 'user', 'content' => 'Reply with "OK" only.']
    ];
    
    $result = ai_chat($testMessages);
    
    if ($result['success']) {
        return [
            'success' => true,
            'message' => 'Connection successful. Provider: ' . ucfirst($provider) . '. Model: ' . ($settings['model'] ?? 'unknown'),
            'provider' => $provider,
            'model' => $settings['model'] ?? 'unknown'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Connection failed: ' . $result['message']
    ];
}
