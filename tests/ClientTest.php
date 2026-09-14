<?php
namespace Sendzovo\Tests;

use PHPUnit\Framework\TestCase;
use Sendzovo\Client;
use Sendzovo\Exceptions\SendzovoException;

final class ClientTest extends TestCase
{
    public function testMessagesRequireSendingKey(): void
    {
        $this->expectException(SendzovoException::class);
        (new Client())->messages();
    }

    public function testVerificationRequiresVerificationKey(): void
    {
        $this->expectException(SendzovoException::class);
        (new Client())->verification();
    }
}
