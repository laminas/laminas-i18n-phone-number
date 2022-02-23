<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\Filter;

use Laminas\I18n\PhoneNumber\Filter\ToE164;
use PHPUnit\Framework\TestCase;

class ToE164Test extends TestCase
{
    public function testToE164(): void
    {
        self::assertEquals(
            '+441234567890',
            (new ToE164('GB'))->filter('01234 567 890')
        );

        self::assertEquals(
            '01234 567 890',
            (new ToE164())->filter('01234 567 890')
        );
    }
}
