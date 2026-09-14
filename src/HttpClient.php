<?php
namespace Sendzovo;

use JsonException;
use Sendzovo\Exceptions\SendzovoException;

final class HttpClient
{
    public function __construct(
        private readonly string $baseUrl = 'https://api.sendzovo.com',
        private readonly int $timeout = 30
    ) {}

    public function request(string $method, string $path, string $apiKey, ?array $data = null): array
    {
        if (trim($apiKey) === '') {
            throw new SendzovoException('A Sendzovo API key is required.');
        }

        $method = strtoupper($method);
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        if ($method === 'GET' && $data) {
            $url .= '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new SendzovoException('Unable to initialize HTTP request.');
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $apiKey,
            'User-Agent: sendzovo-php/1.0',
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $this->timeout,
        ];

        if ($method !== 'GET' && $data !== null) {
            try {
                $body = json_encode($data, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new SendzovoException('Unable to encode request body: ' . $e->getMessage());
            }
            $options[CURLOPT_POSTFIELDS] = $body;
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($ch, $options);
        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($raw === false) {
            throw new SendzovoException('Sendzovo request failed: ' . $curlError);
        }

        try {
            $decoded = $raw === '' ? [] : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new SendzovoException('Sendzovo returned an invalid JSON response.', $status);
        }

        if ($status < 200 || $status >= 300) {
            $message = is_array($decoded) && isset($decoded['message'])
                ? (string) $decoded['message']
                : 'Sendzovo API request failed with HTTP ' . $status . '.';
            throw new SendzovoException($message, $status, is_array($decoded) ? $decoded : null);
        }

        return is_array($decoded) ? $decoded : [];
    }
}
