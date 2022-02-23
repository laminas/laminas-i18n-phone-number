<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter;

/**
 * @psalm-template T
 */
final class ToNationalPhoneNumber extends AbstractFilter
{
    /**
     * Given a valid phone number, return the number in national format, otherwise return the unfiltered input
     *
     * @param T $value
     * @return string|T
     */
    public function filter($value)
    {
        $number = $this->mixedToPhoneNumber($value);

        return $number ? $number->toNational() : $value;
    }
}
