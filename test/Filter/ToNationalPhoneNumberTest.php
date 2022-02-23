<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\Filter;

use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use PHPUnit\Framework\TestCase;

class ToNationalPhoneNumberTest extends TestCase
{
    public function testToNational(): void
    {
        self::assertEquals(
            '01234 567890',
            (new ToNationalPhoneNumber('GB'))->filter('01234 567 890')
        );

        self::assertEquals(
            '01234 567 890',
            (new ToNationalPhoneNumber())->filter('01234 567 890')
        );
    }
}
