<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Filter;

use Laminas\I18n\PhoneNumber\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use PHPUnit\Framework\TestCase;

class ToInternationalPhoneNumberTest extends TestCase
{
    public function testExpectedOutputWhenTheDefaultCountryCodeMatchesTheInput(): void
    {
        self::assertEquals(
            '+44 1234 567890',
            (new ToInternationalPhoneNumber(CountryCode::fromString('GB')))->filter('01234 567 890')
        );
    }

    public function testTheFilterWillNotOperateOnStringsThatCannotBeRecognised(): void
    {
        self::assertEquals(
            'Foo',
            (new ToInternationalPhoneNumber(CountryCode::fromString('US')))->filter('Foo')
        );
    }

    public function testRecognisableNumbersWillBeFilteredRegardlessOfConfiguredCountry(): void
    {
        self::assertEquals(
            '+44 1234 567890',
            (new ToInternationalPhoneNumber(CountryCode::fromString('ZA')))->filter('+44 (0) 1234 567 890')
        );
    }
}
