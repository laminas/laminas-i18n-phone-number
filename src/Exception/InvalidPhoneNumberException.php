<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Exception;

use InvalidArgumentException;

use function sprintf;

final class InvalidPhoneNumberException extends InvalidArgumentException implements ExceptionInterface
{
    public static function with(string $phoneNumber, ?string $countryCode): self
    {
        if (! empty($countryCode)) {
            return self::invalidNumberForRegion($phoneNumber, $countryCode);
        }

        return new self(sprintf(
            'The phone number "%s" is not a valid phone number',
            $phoneNumber
        ));
    }

    public static function invalidNumberForRegion(string $phoneNumber, string $countryCode): self
    {
        return new self(sprintf(
            'The phone number "%s" is not a valid number for the region "%s"',
            $phoneNumber,
            $countryCode
        ));
    }

    public static function undetectableRegion(string $phoneNumber): self
    {
        return new self(sprintf(
            'A region cannot be detected for the phone number "%s"',
            $phoneNumber
        ));
    }
}
