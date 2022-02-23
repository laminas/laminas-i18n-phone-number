<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\Filter;

use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use PHPUnit\Framework\TestCase;

class ToInternationalPhoneNumberTest extends TestCase
{
    public function testToInternational(): void
    {
        self::assertEquals(
            '+44 1234 567890',
            (new ToInternationalPhoneNumber('GB'))->filter('01234 567 890')
        );

        self::assertEquals(
            '01234 567 890',
            (new ToInternationalPhoneNumber())->filter('01234 567 890')
        );
    }
}
