<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Filter;

use Laminas\I18n\PhoneNumber\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use PHPUnit\Framework\TestCase;

class ToNationalPhoneNumberTest extends TestCase
{
    public function testExpectedOutputWhenTheDefaultCountryCodeMatchesTheInput(): void
    {
        self::assertEquals(
            '01234 567890',
            (new ToNationalPhoneNumber(CountryCode::fromString('GB')))->filter('01234 56 78 90')
        );
    }

    public function testTheFilterWillNotOperateOnStringsThatCannotBeRecognised(): void
    {
        self::assertEquals(
            'Foo',
            (new ToNationalPhoneNumber(CountryCode::fromString('US')))->filter('Foo')
        );
    }

    public function testRecognisableNumbersWillBeFilteredRegardlessOfConfiguredCountry(): void
    {
        self::assertEquals(
            '01234 567890',
            (new ToNationalPhoneNumber(CountryCode::fromString('ZA')))->filter('+44 (0) 1234 567 890')
        );
    }
}
