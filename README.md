# Sendzovo PHP SDK

**[Website](https://sendzovo.com)** · **[Documentation](https://docs.sendzovo.com)** · **[Send Email API](https://docs.sendzovo.com/send-email-api)** · **[Email Verification API](https://docs.sendzovo.com/email-verification-api)** · **[Free Email Checker](https://sendzovo.com/tools/email-checker)**

Official PHP SDK for Sendzovo transactional email and email verification.

## Requirements

- PHP 8.1+
- cURL extension
- A Sendzovo sending API key and/or verification API key

## Installation

```bash
composer require sendzovo/sendzovo-php
```

## Send transactional email

Sending API keys are domain-scoped. The `from` address must belong to the domain assigned to the key and must satisfy that domain's sender restrictions.

```php
<?php
require 'vendor/autoload.php';

use Sendzovo\Client;

$sendzovo = Client::withSendingKey($_ENV['SENDZOVO_SENDING_API_KEY']);

$result = $sendzovo->messages()->send([
    'from' => 'hello@example.com',
    'from_name' => 'Example App',
    'to' => [
        ['email' => 'customer@example.net', 'name' => 'Customer'],
    ],
    'subject' => 'Welcome',
    'html' => '<h1>Welcome!</h1>',
    'text' => 'Welcome!',
    'tag' => 'welcome',
]);

$messageId = $result['id'];
```

A send request accepts 1–50 recipients. At least one of `html` or `text` is required.

## Retrieve a message

```php
$message = $sendzovo->messages()->get($messageId);
```

## Verify an email

Verification API keys are separate from domain-scoped sending keys.

```php
$sendzovo = Client::withVerificationKey($_ENV['SENDZOVO_VERIFICATION_API_KEY']);

$result = $sendzovo->verification()->verify('user@example.com');
```

## Detailed verification

```php
$result = $sendzovo->verification()->verifyDetailed('user@example.com');
```

## Use sending and verification together

```php
$sendzovo = new Client(
    sendingApiKey: $_ENV['SENDZOVO_SENDING_API_KEY'],
    verificationApiKey: $_ENV['SENDZOVO_VERIFICATION_API_KEY'],
);
```

## Error handling

```php
use Sendzovo\Exceptions\SendzovoException;

try {
    $result = $sendzovo->messages()->get('MESSAGE_ID');
} catch (SendzovoException $e) {
    echo $e->getMessage();
    echo $e->statusCode();
    print_r($e->response());
}
```

API validation, authentication, billing and domain errors are exposed through `SendzovoException` with the HTTP status and decoded API response when available.

## API base URL

The SDK defaults to `https://api.sendzovo.com`. A custom base URL can be supplied for development/testing.

## Documentation

Full guides and API reference for every endpoint are at **[docs.sendzovo.com](https://docs.sendzovo.com)**:

- [Quick start](https://docs.sendzovo.com/)
- [Authentication and API keys](https://docs.sendzovo.com/authentication)
- [Send Email API](https://docs.sendzovo.com/send-email-api)
- [Email templates](https://docs.sendzovo.com/templates)
- [SMTP settings](https://docs.sendzovo.com/smtp)
- [Email Verification API](https://docs.sendzovo.com/email-verification-api)
- [HTTP status codes and errors](https://docs.sendzovo.com/http-responses)

Don't have an account yet? [Create a free Sendzovo account](https://app.sendzovo.com/register).

## License

MIT
