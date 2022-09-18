<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter;

use Laminas\Filter\FilterInterface;
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Exception\ExceptionInterface;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;

use function is_scalar;

/** @internal Laminas\i18n  */
abstract class AbstractFilter implements FilterInterface
{
    final public function __construct(
        private readonly CountryCode $countryCode
    ) {
    }

    final protected function tryMixedToPhoneNumber(mixed $value): ?PhoneNumberValue
    {
        if ($value instanceof PhoneNumberValue) {
            return $value;
        }

        if (! is_scalar($value)) {
            return null;
        }

        $input = (string) $value;
        if ($input === '') {
            return null;
        }

        try {
            return PhoneNumberValue::fromString($input, $this->countryCode->toString());
        } catch (ExceptionInterface) {
            return null;
        }
    }
}
