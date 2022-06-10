<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\View\Helper;

use Laminas\I18n\PhoneNumber\CountryCode;
use Laminas\I18n\PhoneNumber\View\Helper\PhoneNumberFormat;
use PHPUnit\Framework\TestCase;

class PhoneNumberFormatTest extends TestCase
{
    public function testInvokeReturnsSelf(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('GB'));
        self::assertSame(
            $helper,
            $helper()
        );
    }

    public function testE164FormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('GB'));
        self::assertEquals(
            '+41446681800',
            $helper->toE164('044 668 18 00', 'CH')
        );
    }

    public function testE164ReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('US'));
        self::assertEquals(
            '044 668 18 00',
            $helper->toE164('044 668 18 00')
        );
    }

    public function testNationalFormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('ZA'));
        self::assertEquals(
            '044 668 18 00',
            $helper->toNational('+41446681800')
        );
    }

    public function testNationalReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('GB'));
        self::assertEquals(
            '044 668 18 00',
            $helper->toNational('044 668 18 00')
        );
    }

    public function testInternationalFormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('FR'));
        self::assertEquals(
            '+41 44 668 18 00',
            $helper->toInternational('+41446681800')
        );
    }

    public function testInternationalReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('GB'));
        self::assertEquals(
            '044 668 18 00',
            $helper->toInternational('044 668 18 00')
        );
    }

    public function testRfc3966FormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('GB'));
        self::assertEquals(
            'tel:+41-44-668-18-00',
            $helper->toRfc3966('+41446681800')
        );
    }

    public function testRfc3966ReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat(CountryCode::fromString('GB'));
        self::assertEquals(
            '044 668 18 00',
            $helper->toRfc3966('044 668 18 00')
        );
    }
}
