<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\Filter;

use Laminas\I18n\PhoneNumber\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\ToE164;
use PHPUnit\Framework\TestCase;

class ToE164Test extends TestCase
{
    public function testExpectedOutputWhenTheDefaultCountryCodeMatchesTheInput(): void
    {
        self::assertEquals(
            '+441234567890',
            (new ToE164(CountryCode::fromString('GB')))->filter('01234 567 890')
        );
    }

    public function testTheFilterWillNotOperateOnStringsThatCannotBeRecognised(): void
    {
        self::assertEquals(
            'Foo',
            (new ToE164(CountryCode::fromString('US')))->filter('Foo')
        );
    }

    public function testRecognisableNumbersWillBeFilteredRegardlessOfConfiguredCountry(): void
    {
        self::assertEquals(
            '+441234567890',
            (new ToE164(CountryCode::fromString('ZA')))->filter('+44 (0) 1234 567 890')
        );
    }
}
