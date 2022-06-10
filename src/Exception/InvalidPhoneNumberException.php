<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Exception;

use InvalidArgumentException;

use function sprintf;

final class InvalidPhoneNumberException extends InvalidArgumentException implements ExceptionInterface
{
    /** @psalm-pure */
    public static function with(string $phoneNumber, ?string $countryCode): self
    {
        if ($countryCode !== null && $countryCode !== '') {
            return self::invalidNumberForRegion($phoneNumber, $countryCode);
        }

        return new self(sprintf(
            'The phone number "%s" is not a valid phone number',
            $phoneNumber
        ));
    }

    /** @psalm-pure */
    public static function invalidNumberForRegion(string $phoneNumber, string $countryCode): self
    {
        return new self(sprintf(
            'The phone number "%s" is not a valid number for the region "%s"',
            $phoneNumber,
            $countryCode
        ));
    }

    /** @psalm-pure */
    public static function undetectableRegion(string $phoneNumber): self
    {
        return new self(sprintf(
            'A region cannot be detected for the phone number "%s"',
            $phoneNumber
        ));
    }
}
