<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test;

use Laminas\I18n\PhoneNumber\Exception\ExceptionInterface;
use Laminas\I18n\PhoneNumber\Exception\InvalidPhoneNumberException;
use Laminas\I18n\PhoneNumber\Exception\UnrecognizableNumberException;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use PHPUnit\Framework\TestCase;

class PhoneNumberValueTest extends TestCase
{
    /** @uses \Laminas\I18n\PhoneNumber\Test\NumberGeneratorTrait */
    use NumberGeneratorTrait;

    public function testAnExceptionIsThrownForInvalidInput(): void
    {
        $this->expectException(UnrecognizableNumberException::class);
        PhoneNumberValue::fromString('muppets');
    }

    public function testAnExceptionIsThrownForANationalNumberWithoutACountry(): void
    {
        $this->expectException(UnrecognizableNumberException::class);
        PhoneNumberValue::fromString('01392 234 567');
    }

    public function testAnExceptionIsNotThrownForANationalNumberWithTheCorrectCountry(): void
    {
        $number = PhoneNumberValue::fromString('01392 234 567', 'GB');
        self::assertEquals('+441392234567', $number->toE164());
    }

    public function testAnExceptionIsThrownForANationalNumberWithTheWrongCountry(): void
    {
        $this->expectException(InvalidPhoneNumberException::class);
        PhoneNumberValue::fromString('01392 234 567', 'US');
    }

    public function testAnExceptionIsNotThrownForAnInternationalNumberWithTheWrongCountry(): void
    {
        $number = PhoneNumberValue::fromString('+44 1392 234 567', 'US');
        self::assertEquals('+441392234567', $number->toE164());
    }

    public function testThatValidNumbersCanBeFormattedToE164Format(): void
    {
        $number = PhoneNumberValue::fromString('2015550123', 'US');
        self::assertEquals('+12015550123', $number->toE164());
    }

    public function testThatValidNumbersCanBeFormattedToNationalFormat(): void
    {
        $number = PhoneNumberValue::fromString('2015550123', 'US');
        self::assertEquals('(201) 555-0123', $number->toNational());
    }

    public function testThatValidNumbersCanBeFormattedToInternationalFormat(): void
    {
        $number = PhoneNumberValue::fromString('2015550123', 'US');
        self::assertEquals('+1 201-555-0123', $number->toInternational());
    }

    public function testThatCastingToAStringYieldsAnE164NumberForANormalNumber(): void
    {
        $number = PhoneNumberValue::fromString('2015550123', 'US');
        self::assertEquals($number->toE164(), (string) $number);
    }

    public function testThatCastingToAStringYieldsTheNationalNumberForAShortCode(): void
    {
        $number = PhoneNumberValue::fromString('911', 'US');
        self::assertEquals($number->toNational(), (string) $number);
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatTheExpectedTypeOfPhoneNumberIsDetected(
        string $number,
        string $region,
        int $expectedType
    ): void {
        $number   = PhoneNumberValue::fromString($number, $region);
        $detected = $number->type();
        self::assertNotEquals(0, $detected & $expectedType);
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatAnyTypeOfValidPhoneNumberCanBeFormattedToANationalNumber(
        string $number,
        string $region
    ): void {
        $object = PhoneNumberValue::fromString($number, $region);
        self::assertNotEmpty($object->toNational());
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatAnyTypeOfValidPhoneNumberCanBeFormattedToAnInternationalNumber(
        string $number,
        string $region
    ): void {
        $object = PhoneNumberValue::fromString($number, $region);
        self::assertNotEmpty($object->toInternational());
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatAnyTypeOfValidPhoneNumberCanBeFormattedToE164(
        string $number,
        string $region
    ): void {
        $object = PhoneNumberValue::fromString($number, $region);
        self::assertNotEmpty($object->toE164());
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatAnyTypeOfValidPhoneNumberCanBeFormattedToRFC3966(
        string $number,
        string $region
    ): void {
        $object = PhoneNumberValue::fromString($number, $region);
        self::assertNotEmpty($object->toRfc3966());
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatAnyTypeOfValidPhoneNumberCanBeFormattedToAnOutOfCountryNumber(
        string $number,
        string $region
    ): void {
        $object = PhoneNumberValue::fromString($number, $region);
        self::assertNotEmpty($object->toNumberDialedFrom('GB'));
    }

    /**
     * @dataProvider validPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $region
     */
    public function testThatAllValidNumbersWillHaveARegionCode(
        string $number,
        string $region
    ): void {
        /**
         * The example numbers do not necessarily match the given region to the detected region
         */
        $object = PhoneNumberValue::fromString($number, $region);
        self::assertNotEmpty($object->regionCode);
    }

    /** @return array<string, array{0: non-empty-string, 1: non-empty-string}> */
    public function internationalFormatsProvider(): array
    {
        return [
            'E164'        => ['+441392345678', '+441392345678'],
            'GB Spaces'   => ['+44 01392 345 678', '+441392345678'],
            'US'          => ['+12124567890', '+12124567890'],
            'US-Dashes'   => ['+1 212-456-7890', '+12124567890'],
            'US-Brackets' => ['+1 (212) 456-7890', '+12124567890'],
            'US Dots'     => ['+1 212.456.7890', '+12124567890'],
            'US Spaces'   => ['+1 212 456 7890', '+12124567890'],
        ];
    }

    /**
     * @dataProvider internationalFormatsProvider
     * @param non-empty-string $inputNumber
     * @param non-empty-string $e164
     */
    public function testThatANormalNumberIsIdentifiedInVariousInternationalFormats(
        string $inputNumber,
        string $e164
    ): void {
        $number = PhoneNumberValue::fromString($inputNumber);
        self::assertNotEquals(
            0,
            $number->type() & PhoneNumberValue::TYPE_FIXED | PhoneNumberValue::TYPE_MOBILE,
        );
        self::assertEquals($e164, $number->toE164());
    }

    public function testThatAMobileLineIsIdentified(): void
    {
        $number = PhoneNumberValue::fromString('+447843567890');
        self::assertEquals(PhoneNumberValue::TYPE_MOBILE, $number->type());
    }

    public function testThatAnEmergencyNumberIsDetected(): void
    {
        $number = PhoneNumberValue::fromString('911', 'US');
        self::assertTrue($number->isShortNumber);
        self::assertEquals(PhoneNumberValue::TYPE_EMERGENCY, $number->type());
    }

    /**
     * @dataProvider invalidPhoneNumberProvider
     * @param non-empty-string $number
     * @param non-empty-string $country
     */
    public function testThatAllInvalidNumbersCauseExceptions(string $number, string $country): void
    {
        $this->expectException(ExceptionInterface::class);
        PhoneNumberValue::fromString($number, $country);
    }

    public function testExpectedFormatVariations(): void
    {
        $number = PhoneNumberValue::fromString('+447843567890');

        self::assertEquals('07843 567890', $number->toNational());
        self::assertEquals('+447843567890', $number->toE164());
        self::assertEquals('tel:+44-7843-567890', $number->toRfc3966());
        self::assertEquals('+44 7843 567890', $number->toInternational());
    }
}
