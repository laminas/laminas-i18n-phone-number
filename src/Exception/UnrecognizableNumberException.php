<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Exception;

use InvalidArgumentException;
use Throwable;

use function sprintf;

/** @phpcs:disable Generic.Files.LineLength.TooLong */
final class UnrecognizableNumberException extends InvalidArgumentException implements InvalidPhoneNumberExceptionInterface
{
    /** @psalm-pure */
    public static function withString(string $number, ?Throwable $previous = null): self
    {
        return new self(sprintf(
            'The phone number "%s" cannot be recognized as a valid phone number',
            $number
        ), 0, $previous);
    }
}
