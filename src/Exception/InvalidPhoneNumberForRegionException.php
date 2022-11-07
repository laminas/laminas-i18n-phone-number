<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Exception;

use InvalidArgumentException;

use function sprintf;

/** @phpcs:disable Generic.Files.LineLength.TooLong */
final class InvalidPhoneNumberForRegionException extends InvalidArgumentException implements InvalidPhoneNumberExceptionInterface
{
    /** @psalm-pure */
    public static function new(string $phoneNumber, string $countryCode): self
    {
        return new self(sprintf(
            'The phone number "%s" is not a valid number for the region "%s"',
            $phoneNumber,
            $countryCode
        ));
    }
}
