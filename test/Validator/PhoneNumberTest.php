<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Validator;

use ArrayObject;
use Laminas\I18n\PhoneNumber\Exception\InvalidOptionException;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\I18n\PhoneNumber\Test\NumberGeneratorTrait;
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stringable;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/** @psalm-suppress InternalClass */
class PhoneNumberTest extends TestCase
{
    use NumberGeneratorTrait;

    private static function assertFailureMessage(PhoneNumber $validator, string $expectedKey): void
    {
        self::assertArrayHasKey($expectedKey, $validator->getMessages());
    }

    /** @return array<array-key, array{0: mixed}> */
    public static function invalidTypeProvider(): array
    {
        return [
            [''],
            [null],
            [[]],
        ];
    }

    /**
     * @param mixed $value
     */
    #[DataProvider('invalidTypeProvider')]
    public function testInvalidTypes($value): void
    {
        $validator = new PhoneNumber();
        self::assertFalse($validator->isValid($value));
        self::assertFailureMessage($validator, PhoneNumber::INVALID_TYPE);
    }

    public function testUnrecognizableNumbers(): void
    {
        $validator = new PhoneNumber();
        self::assertFalse($validator->isValid('Sneezes'));
        self::assertFailureMessage($validator, PhoneNumber::NO_MATCH);
    }

    /** @return array<array-key, array{0: non-empty-string}> */
    public static function invalidCountryProvider(): array
    {
        return [
            ['nuts'],
            ['1'],
            ['en_Muppet'],
        ];
    }

    /**
     * @param non-empty-string $option
     */
    #[DataProvider('invalidCountryProvider')]
    public function testInvalidCountryOption(string $option): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Country codes must be ISO 3166 2-letter codes');
        (new PhoneNumber())->setCountry($option);
    }

    /** @return array<array-key, array{0: int}> */
    public static function invalidAllowedTypeProvider(): array
    {
        return [
            [0],
            [-1],
            [PHP_INT_MAX],
            [PHP_INT_MIN],
            [PhoneNumberValue::TYPE_UNKNOWN],
        ];
    }

    #[DataProvider('invalidAllowedTypeProvider')]
    public function testInvalidAllowedTypes(int $option): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('The allowed types provided do not match known valid types');
        (new PhoneNumber())->setAllowedTypes($option);
    }

    public function testThatWhenTheCountryIsProvidedNationalPhoneNumbersAreValid(): void
    {
        $validator = new PhoneNumber([
            'country' => 'GB',
        ]);
        self::assertTrue($validator->isValid('01392 223 456'));
    }

    public function testAValidInternationalNumberIsAcceptableWhenADifferentCountryIsSpecified(): void
    {
        $validator = new PhoneNumber([
            'country' => 'GB',
        ]);
        self::assertTrue($validator->isValid('+1 201 555 0123'));
    }

    public function testThatTheCountryOptionCanAlsoBeALocaleString(): void
    {
        $validator = new PhoneNumber([
            'country' => 'en_GB',
        ]);
        self::assertTrue($validator->isValid('+1 201 555 0123'));
        self::assertTrue($validator->isValid('01234 567 890'));
    }

    /**
     * @param non-empty-string $number
     * @param non-empty-string $country
     */
    #[DataProvider('invalidPhoneNumberProvider')]
    public function testThatInvalidNumbersAreConsideredInvalid(string $number, string $country): void
    {
        $validator = new PhoneNumber();
        $validator->setCountry($country);

        self::assertFalse($validator->isValid($number));
    }

    /**
     * @param non-empty-string $number
     * @param non-empty-string $country
     */
    #[DataProvider('validPhoneNumberProvider')]
    public function testThatValidNumbersAreConsideredValid(string $number, string $country): void
    {
        $validator = new PhoneNumber();
        $validator->setCountry($country);

        self::assertTrue($validator->isValid($number));
    }

    public function testThatWhenOnlyOneTypeIsAllowedPossibleMatchesAreAcceptable(): void
    {
        $possible = '+12124567890';
        $number   = PhoneNumberValue::fromString($possible);
        self::assertEquals(
            PhoneNumberValue::TYPE_MOBILE | PhoneNumberValue::TYPE_FIXED,
            $number->type(),
            'The test number is expected to be possibly a mobile OR a fixed line'
        );

        $validator = new PhoneNumber();
        self::assertTrue($validator->isValid($possible), 'The number should be "normally" valid');

        $validator = new PhoneNumber();
        $validator->setAllowedTypes(PhoneNumberValue::TYPE_MOBILE);
        self::assertTrue($validator->isValid($possible), 'The number should still be valid when allowed types are set');

        $validator = new PhoneNumber();
        $validator->setAllowedTypes(PhoneNumberValue::TYPE_FIXED);
        self::assertTrue($validator->isValid($possible), 'The number should still be valid when allowed types are set');
    }

    public function testThatDisallowedTypesAreConsideredInvalid(): void
    {
        $validator = new PhoneNumber([
            'country'       => 'US',
            'allowed_types' => PhoneNumberValue::TYPE_FIXED,
        ]);

        self::assertFalse($validator->isValid('911'));
        self::assertFailureMessage($validator, PhoneNumber::NOT_ALLOWED);
    }

    public function testThatOptionsCanBeTraversable(): void
    {
        $options   = new ArrayObject(['country' => 'US', 'allowed_types' => PhoneNumberValue::TYPE_EMERGENCY]);
        $validator = new PhoneNumber($options);
        self::assertTrue($validator->isValid('911'));
    }

    public function testThatAStringableObjectWillBeConsideredForValidation(): void
    {
        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '01234567890';
            }
        };

        $validator = new PhoneNumber();
        $validator->setCountry('GB');
        self::assertTrue($validator->isValid($object));

        $validator = new PhoneNumber();
        $validator->setCountry('US');
        self::assertFalse($validator->isValid($object));
        $messages = $validator->getMessages();
        self::assertArrayHasKey(PhoneNumber::INVALID, $messages);
        self::assertCount(1, $messages);
    }

    public function testAllOptionsCanHaveNullValues(): void
    {
        new PhoneNumber([
            'country'         => null,
            'allowed_types'   => null,
            'country_context' => null,
        ]);
        self::assertTrue(true);
    }

    public function testOptionsCanBeNull(): void
    {
        new PhoneNumber(null);
        self::assertTrue(true);
    }

    public function testThatCountryContextIsConsidered(): void
    {
        $input   = '01234 567 890';
        $context = [
            'number'   => $input,
            'my-field' => 'GB',
        ];

        $validator = new PhoneNumber();
        self::assertFalse($validator->isValid($input, $context));

        $validator = new PhoneNumber([
            'country_context' => 'my-field',
        ]);
        self::assertTrue($validator->isValid($input, $context));
    }

    public function testRuntimeSetOptionsWithEmptyValuesDoNotCauseTypeErrors(): void
    {
        $validator = new PhoneNumber();
        $validator->setOptions([
            'country'         => null,
            'allowed_types'   => null,
            'country_context' => null,
        ]);
        self::assertTrue($validator->isValid('+441234567890'));
    }

    public function testRuntimeSetOptionsAreApplied(): void
    {
        $validator = new PhoneNumber([
            'allowed_types' => PhoneNumberValue::TYPE_MOBILE,
            'country'       => 'GB',
        ]);

        self::assertFalse($validator->isValid('999'));

        $validator->setOptions([
            'allowed_types' => PhoneNumberValue::TYPE_EMERGENCY,
        ]);

        self::assertTrue($validator->isValid('999'));
    }
}
