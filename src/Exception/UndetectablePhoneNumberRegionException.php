<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Exception;

use InvalidArgumentException;

use function sprintf;

/** @phpcs:disable Generic.Files.LineLength.TooLong */
final class UndetectablePhoneNumberRegionException extends InvalidArgumentException implements InvalidPhoneNumberExceptionInterface
{
    /** @psalm-pure */
    public static function new(string $phoneNumber): self
    {
        return new self(sprintf(
            'A region cannot be detected for the phone number "%s"',
            $phoneNumber
        ));
    }
}
