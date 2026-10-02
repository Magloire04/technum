<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Contact\FormToken;
use Technum\Contact\TokenStatus;

final class FormTokenTest extends TestCase
{
    private FormToken $token;

    protected function setUp(): void
    {
        $this->token = new FormToken(str_repeat('s', 40));
    }

    public function testFreshTokenIsValidOnceTheMinimumDelayHasPassed(): void
    {
        $issued = $this->token->issue(1000);

        self::assertSame(TokenStatus::Valid, $this->token->verify($issued, 1003));
        self::assertSame(TokenStatus::Valid, $this->token->verify($issued, 1000 + FormToken::MAX_AGE_SECONDS));
    }

    public function testTokenSubmittedTooFastIsFlagged(): void
    {
        self::assertSame(TokenStatus::TooFast, $this->token->verify($this->token->issue(1000), 1002));
    }

    public function testOldTokenIsExpired(): void
    {
        $issued = $this->token->issue(1000);

        self::assertSame(TokenStatus::Expired, $this->token->verify($issued, 1000 + FormToken::MAX_AGE_SECONDS + 1));
    }

    public function testTamperedTokensAreInvalid(): void
    {
        [$timestamp, $signature] = explode('.', $this->token->issue(1000));

        self::assertSame(TokenStatus::Invalid, $this->token->verify($timestamp . '.' . strrev($signature), 1010));
        self::assertSame(TokenStatus::Invalid, $this->token->verify('999.' . $signature, 1010));
    }

    /** @return iterable<string, array{string}> */
    public static function malformedTokens(): iterable
    {
        yield 'vide' => [''];
        yield 'sans point' => ['abc'];
        yield 'horodatage seul' => ['1000'];
        yield 'trois parties' => ['1000.ab.cd'];
        yield 'horodatage non numérique' => ['abc.def'];
    }

    #[DataProvider('malformedTokens')]
    public function testMalformedTokensAreInvalid(string $token): void
    {
        self::assertSame(TokenStatus::Invalid, $this->token->verify($token, 1010));
    }

    public function testTokenFromAnotherSecretIsInvalid(): void
    {
        $other = new FormToken(str_repeat('o', 40));

        self::assertSame(TokenStatus::Invalid, $this->token->verify($other->issue(1000), 1010));
    }

    public function testShortSecretIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FormToken('court');
    }
}
