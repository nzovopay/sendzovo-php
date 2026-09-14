<?php
namespace Sendzovo;

use Sendzovo\Exceptions\SendzovoException;
use Sendzovo\Resources\Messages;
use Sendzovo\Resources\Verification;

final class Client
{
    private HttpClient $http;

    public function __construct(
        private readonly ?string $sendingApiKey = null,
        private readonly ?string $verificationApiKey = null,
        string $baseUrl = 'https://api.sendzovo.com',
        int $timeout = 30
    ) {
        $this->http = new HttpClient($baseUrl, $timeout);
    }

    public static function withSendingKey(string $apiKey, string $baseUrl = 'https://api.sendzovo.com'): self
    {
        return new self(sendingApiKey: $apiKey, baseUrl: $baseUrl);
    }

    public static function withVerificationKey(string $apiKey, string $baseUrl = 'https://api.sendzovo.com'): self
    {
        return new self(verificationApiKey: $apiKey, baseUrl: $baseUrl);
    }

    public function messages(): Messages
    {
        if (!$this->sendingApiKey) throw new SendzovoException('A sending API key is required for Messages.');
        return new Messages($this->http, $this->sendingApiKey);
    }

    public function verification(): Verification
    {
        if (!$this->verificationApiKey) throw new SendzovoException('A verification API key is required for Verification.');
        return new Verification($this->http, $this->verificationApiKey);
    }
}
