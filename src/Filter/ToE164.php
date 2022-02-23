<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter;

/**
 * @psalm-template T
 */
final class ToE164 extends AbstractFilter
{
    /**
     * Given a valid phone number, return the number in E164 format, otherwise return the unfiltered input
     *
     * @param T $value
     * @return string|T
     */
    public function filter($value)
    {
        $number = $this->mixedToPhoneNumber($value);

        return $number ? $number->toE164() : $value;
    }
}
