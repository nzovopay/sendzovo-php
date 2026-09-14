<?php
namespace Sendzovo\Resources;

use InvalidArgumentException;
use Sendzovo\HttpClient;

final class Messages
{
    public function __construct(private readonly HttpClient $http, private readonly string $apiKey) {}

    public function send(array $message): array
    {
        foreach (['from', 'to', 'subject'] as $field) {
            if (!array_key_exists($field, $message)) {
                throw new InvalidArgumentException("Missing required message field: {$field}");
            }
        }
        if (!isset($message['html']) && !isset($message['text'])) {
            throw new InvalidArgumentException('Message requires html or text content.');
        }
        if (!is_array($message['to']) || count($message['to']) < 1 || count($message['to']) > 50) {
            throw new InvalidArgumentException('Message to must contain between 1 and 50 recipients.');
        }
        return $this->http->request('POST', '/v1/messages', $this->apiKey, $message);
    }

    public function get(string $id): array
    {
        if (trim($id) === '') throw new InvalidArgumentException('Message ID is required.');
        return $this->http->request('GET', '/v1/messages/' . rawurlencode($id), $this->apiKey);
    }
}
