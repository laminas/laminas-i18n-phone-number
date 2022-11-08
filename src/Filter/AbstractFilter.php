<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter;

use Laminas\Filter\FilterInterface;
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Exception\InvalidPhoneNumberExceptionInterface;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Stringable;

use function is_scalar;
use function is_string;

/**
 * @internal Laminas\i18n
 *
 * @psalm-type Options = array{country-code: non-empty-string}
 */
abstract class AbstractFilter implements FilterInterface
{
    /** @param Options|null $options */
    final public function __construct(
        private CountryCode $countryCode,
        array|null $options = null,
    ) {
        if ($options === null) {
            return;
        }

        $this->setOptions($options);
    }

    /** @param non-empty-string|CountryCode $code */
    final public function setCountryCode(string|CountryCode $code): void
    {
        $this->countryCode = is_string($code) ? CountryCode::fromString($code) : $code;
    }

    /** @param array<string, mixed> $options */
    final public function setOptions(array $options): void
    {
        if (isset($options['country-code']) && is_string($options['country-code']) && $options['country-code'] !== '') {
            $this->setCountryCode($options['country-code']);
        }
    }

    final protected function tryMixedToPhoneNumber(mixed $value): ?PhoneNumberValue
    {
        if ($value instanceof PhoneNumberValue) {
            return $value;
        }

        if (! is_scalar($value) && ! $value instanceof Stringable) {
            return null;
        }

        $input = (string) $value;
        if ($input === '') {
            return null;
        }

        try {
            return PhoneNumberValue::fromString($input, $this->countryCode->toString());
        } catch (InvalidPhoneNumberExceptionInterface) {
            return null;
        }
    }
}
