<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Filter;

use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Filter\AbstractFilter;
use Laminas\I18n\PhoneNumber\Filter\ToE164;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use PHPUnit\Framework\TestCase;
use Stringable;

class AbstractFilterTest extends TestCase
{
    /** @return array<string, array{0: class-string<AbstractFilter>}> */
    public function filterClassProvider(): array
    {
        return [
            'E164'          => [ToE164::class],
            'International' => [ToInternationalPhoneNumber::class],
            'National'      => [ToNationalPhoneNumber::class],
        ];
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatWithAFallbackCountryANumberWillBeFormatted(string $class): void
    {
        $number = '01234 567 890';
        $filter = new $class(CountryCode::fromString('GB'));
        self::assertNotEquals($number, $filter->filter($number));
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatExistingNumberObjectsWillBeFilteredToAString(string $class): void
    {
        $number = '01234 567 890';
        $value  = PhoneNumberValue::fromString($number, 'GB');
        $filter = new $class(CountryCode::fromString('US'));

        $filtered = $filter->filter($value);
        self::assertIsString($filtered);
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatNonScalarValuesWillBeReturnedAsIs(string $class): void
    {
        $filter = new $class(CountryCode::fromString('GB'));
        self::assertSame([], $filter->filter([]));
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatEmptyOrNonStringValuesWillBeReturnedAsIs(string $class): void
    {
        $filter = new $class(CountryCode::fromString('GB'));
        self::assertSame('', $filter->filter(''));
        self::assertNull($filter->filter(null));
        self::assertSame(0, $filter->filter(0));
        self::assertSame(1, $filter->filter(1));
        self::assertSame(1.5, $filter->filter(1.5));
        self::assertTrue($filter->filter(true));
        self::assertFalse($filter->filter(false));
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatAStringableObjectWillConvertedToAStringForFiltering(string $class): void
    {
        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '01234 567 890';
            }
        };

        $filter   = new $class(CountryCode::fromString('GB'));
        $filtered = $filter->filter($object);
        self::assertIsString($filtered);
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatTheDefaultCountryCodeCanBeOverriddenBySettingOptionsAtRuntime(string $class): void
    {
        $filter = new $class(CountryCode::fromString('GB'));
        $filter->setOptions(['country-code' => 'DE']);
        $value = $filter->filter('(0)30-23125 000');
        self::assertIsString($value);
        $expect = [
            'international' => '+49 30 23125000',
            'E164'          => '+493023125000',
            'national'      => '030 23125000',
        ];
        self::assertContains($value, $expect);
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatTheDefaultCountryCodeCanBeOverriddenBySettingOptionsDuringConstruction(string $class): void
    {
        $filter = new $class(CountryCode::fromString('GB'), ['country-code' => 'DE']);
        $value  = $filter->filter('(0)30-23125 000');
        self::assertIsString($value);
        $expect = [
            'international' => '+49 30 23125000',
            'E164'          => '+493023125000',
            'national'      => '030 23125000',
        ];
        self::assertContains($value, $expect);
    }
}
