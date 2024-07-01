<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test;

use Generator;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ShortNumberInfo;

use function assert;
use function is_int;
use function is_string;
use function sprintf;

/**
 * @internal \Laminas\I18n\PhoneNumber\Test
 */
trait NumberGeneratorTrait
{
    /**
     * @psalm-suppress MoreSpecificReturnType
     * @return Generator<string, array{0: non-empty-string, 1: non-empty-string, 2: int}>
     */
    public static function validPhoneNumberProvider(): Generator
    {
        $util  = PhoneNumberUtil::getInstance();
        $short = ShortNumberInfo::getInstance();
        /** @var list<int> $libTypes */
        $libTypes = PhoneNumberType::values();
        /** @var list<non-empty-string> $regions */
        $regions = $util->getSupportedRegions();
        foreach ($regions as $country) {
            /** @var int $type */
            foreach ($util->getSupportedTypesForRegion($country) as $type) {
                $typeName = $libTypes[$type];
                $number   = $util->getExampleNumberForType($country, $type);
                if (! $number) {
                    continue; // There might not be an example number for the given type and country
                }

                $dialCode = $number->getCountryCode();
                $national = $number->getNationalNumber();
                assert(is_int($dialCode));
                assert(is_string($national));

                /**
                 * The number is re-parsed here, because there are several example numbers that are expected to be valid
                 * in libphonenumber but actually are not.
                 */
                $number = $util->parse($national, $country);

                $isShortNumber = $short->isValidShortNumberForRegion($number, $country)
                    || $short->isValidShortNumber($number);
                $isValidNumber = $util->isValidNumberForRegion($number, $country)
                    || $util->isValidNumber($number);

                if (! $isShortNumber && ! $isValidNumber) {
                    continue;
                }

                $label      = sprintf('Valid %s Example Number: %s (%s)', $country, $national, $typeName);
                $expectType = PhoneNumberValue::TYPE_MAP[$type];

                yield $label => [
                    $national,
                    $country,
                    $expectType,
                ];
            }
        }

        /** @var list<non-empty-string> $regions */
        $regions = $short->getSupportedRegions();
        foreach ($regions as $country) {
            $inputNumber = $short->getExampleShortNumber($country);
            $type        = PhoneNumberType::SHORT_CODE;
            $label       = sprintf(
                'Valid %s Short Number: %s (%s)',
                $country,
                $inputNumber,
                $libTypes[$type]
            );

            yield $label => [
                $inputNumber,
                $country,
                PhoneNumberValue::TYPE_SHORT_CODE | PhoneNumberValue::TYPE_EMERGENCY,
            ];
        }
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @return Generator<string, array{0: non-empty-string, 1: non-empty-string}>
     */
    public static function invalidPhoneNumberProvider(): Generator
    {
        $util      = PhoneNumberUtil::getInstance();
        $shortInfo = ShortNumberInfo::getInstance();

        /** @var list<non-empty-string> $regions */
        $regions = $util->getSupportedRegions();
        foreach ($regions as $country) {
            $number = $util->getInvalidExampleNumber($country);
            if (! $number) {
                continue;
            }

            if (
                $util->isValidNumber($number)
                ||
                $util->isValidNumberForRegion($number, $country)
                ||
                $shortInfo->isValidShortNumber($number)
                ||
                $shortInfo->isValidShortNumberForRegion($number, $country)
            ) {
                continue;
            }

            $national = $number->getNationalNumber();
            assert(is_string($national));

            $label = sprintf('Invalid %s Number: %s', $country, $national);

            yield $label => [
                $national,
                $country,
            ];
        }
    }
}
