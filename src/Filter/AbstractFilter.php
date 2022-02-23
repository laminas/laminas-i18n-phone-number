<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter;

use Laminas\Filter\FilterInterface;
use Laminas\I18n\PhoneNumber\Exception\ExceptionInterface;
use Laminas\I18n\PhoneNumber\Exception\InvalidOptionException;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;

use function is_scalar;
use function preg_match;
use function sprintf;
use function strtoupper;

abstract class AbstractFilter implements FilterInterface
{
    /** @var non-empty-string|null */
    protected ?string $regionCode = null;

    final public function __construct(?string $fallbackCountryCode = null)
    {
        if (! $fallbackCountryCode) {
            return;
        }

        if (! preg_match('/^[A-Z]{2}$/i', $fallbackCountryCode)) {
            throw new InvalidOptionException(sprintf(
                'The fallback country code must be an ISO 3166 2 letter country code. Received "%s"',
                $fallbackCountryCode
            ));
        }

        $this->regionCode = strtoupper($fallbackCountryCode);
    }

    /** @param mixed $value */
    protected function mixedToPhoneNumber($value): ?PhoneNumberValue
    {
        if ($value instanceof PhoneNumberValue) {
            return $value;
        }

        if (! is_scalar($value)) {
            return null;
        }

        $input = (string) $value;
        if (empty($input)) {
            return null;
        }

        try {
            return PhoneNumberValue::fromString($input, $this->regionCode);
        } catch (ExceptionInterface $error) {
            return null;
        }
    }
}
