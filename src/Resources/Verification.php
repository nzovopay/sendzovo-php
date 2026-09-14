<?php
namespace Sendzovo\Resources;

use InvalidArgumentException;
use Sendzovo\HttpClient;

final class Verification
{
    public function __construct(private readonly HttpClient $http, private readonly string $apiKey) {}

    public function verify(string $email): array
    {
        $this->validateEmail($email);
        return $this->http->request('GET', '/v1/verifyEmail', $this->apiKey, ['email' => $email]);
    }

    public function verifyDetailed(string $email): array
    {
        $this->validateEmail($email);
        return $this->http->request('GET', '/v1/verifyEmailDetailed', $this->apiKey, ['email' => $email]);
    }

    private function validateEmail(string $email): void
    {
        if (trim($email) === '' || strlen($email) > 254) {
            throw new InvalidArgumentException('A valid email string up to 254 characters is required.');
        }
    }
}
