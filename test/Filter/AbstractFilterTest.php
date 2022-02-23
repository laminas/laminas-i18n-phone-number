<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\Filter;

use Laminas\I18n\PhoneNumber\Exception\InvalidOptionException;
use Laminas\I18n\PhoneNumber\Filter\AbstractFilter;
use Laminas\I18n\PhoneNumber\Filter\ToE164;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use PHPUnit\Framework\TestCase;

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
    public function testThatAnInvalidCountryCodeWillCauseAnException(string $class): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage(
            'The fallback country code must be an ISO 3166 2 letter country code. Received "Whut?"'
        );
        new $class('Whut?');
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatWithoutAFallbackCountryANumberWillBeReturnedAsIs(string $class): void
    {
        $number = '01234 567 890';
        $filter = new $class();
        self::assertSame($number, $filter->filter($number));
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatWithAFallbackCountryANumberWillBeFormatted(string $class): void
    {
        $number = '01234 567 890';
        $filter = new $class('GB');
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
        $filter = new $class();

        $filtered = $filter->filter($value);
        self::assertIsString($filtered);
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatNonScalarValuesWillBeReturnedAsIs(string $class): void
    {
        $filter = new $class('GB');
        self::assertSame([], $filter->filter([]));
    }

    /**
     * @dataProvider filterClassProvider
     * @param class-string<AbstractFilter> $class
     */
    public function testThatEmptyOrNonStringValuesWillBeReturnedAsIs(string $class): void
    {
        $filter = new $class('GB');
        self::assertSame('', $filter->filter(''));
        self::assertNull($filter->filter(null));
        self::assertSame(0, $filter->filter(0));
        self::assertSame(1, $filter->filter(1));
        self::assertSame(1.5, $filter->filter(1.5));
        self::assertTrue($filter->filter(true));
        self::assertFalse($filter->filter(false));
    }
}
