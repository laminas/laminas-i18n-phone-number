<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\View\Helper;

use Laminas\I18n\PhoneNumber\Exception\InvalidArgumentException;
use Laminas\I18n\PhoneNumber\View\Helper\PhoneNumberFormat;
use PHPUnit\Framework\TestCase;

class PhoneNumberFormatTest extends TestCase
{
    public function testThatAnInvalidDefaultCountryCodeIsExceptional(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumberFormat('Nuts…');
    }

    public function testInvokeReturnsSelf(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertSame(
            $helper,
            $helper()
        );
    }

    public function testThatThePhoneNumberWillBeNullWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertNull($helper->toPhoneNumber('NaN'));
    }

    public function testThePhoneNumberWillBeNonNullWhenValidWithoutCountryCode(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertNotNull($helper->toPhoneNumber('+44 1234 567 890'));
    }

    public function testThePhoneNumberWillBeNonNullWhenValidAgainstDefaultCountry(): void
    {
        $helper = new PhoneNumberFormat('GB');
        self::assertNotNull($helper->toPhoneNumber('01234 567 890'));
    }

    public function testThePhoneNumberWillBeNonNullWhenValidAgainstGivenCountry(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertNotNull($helper->toPhoneNumber('01234 567 890', 'GB'));
    }

    public function testE164FormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '+41446681800',
            $helper->toE164('044 668 18 00', 'CH')
        );
    }

    public function testE164ReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '044 668 18 00',
            $helper->toE164('044 668 18 00')
        );
    }

    public function testNationalFormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '044 668 18 00',
            $helper->toNational('+41446681800')
        );
    }

    public function testNationalReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '044 668 18 00',
            $helper->toNational('044 668 18 00')
        );
    }

    public function testInternationalFormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '+41 44 668 18 00',
            $helper->toInternational('+41446681800')
        );
    }

    public function testInternationalReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '044 668 18 00',
            $helper->toInternational('044 668 18 00')
        );
    }

    public function testRfc3966FormatYieldsTheExpectedValue(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            'tel:+41-44-668-18-00',
            $helper->toRfc3966('+41446681800')
        );
    }

    public function testRfc3966ReturnsInputWhenInvalid(): void
    {
        $helper = new PhoneNumberFormat();
        self::assertEquals(
            '044 668 18 00',
            $helper->toRfc3966('044 668 18 00')
        );
    }
}
