<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\View\Helper;

use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Exception\InvalidPhoneNumberExceptionInterface;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;

final class PhoneNumberFormat
{
    public function __construct(
        private readonly CountryCode $defaultCountryCode
    ) {
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @param non-empty-string|null $countryCodeOrLocale
     */
    private function coalesceCountryCode(?string $countryCodeOrLocale): CountryCode
    {
        $code = $countryCodeOrLocale ? CountryCode::tryFromString($countryCodeOrLocale) : null;

        return $code ?? $this->defaultCountryCode;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $countryCodeOrLocale
     */
    private function tryToPhoneNumber(string $number, ?string $countryCodeOrLocale = null): ?PhoneNumberValue
    {
        try {
            return PhoneNumberValue::fromString(
                $number,
                $this->coalesceCountryCode($countryCodeOrLocale)->toString()
            );
        } catch (InvalidPhoneNumberExceptionInterface) {
            return null;
        }
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $countryCodeOrLocale
     */
    public function toE164(string $number, ?string $countryCodeOrLocale = null): string
    {
        $phone = $this->tryToPhoneNumber($number, $countryCodeOrLocale);

        return $phone ? $phone->toE164() : $number;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $countryCodeOrLocale
     */
    public function toNational(string $number, ?string $countryCodeOrLocale = null): string
    {
        $phone = $this->tryToPhoneNumber($number, $countryCodeOrLocale);

        return $phone ? $phone->toNational() : $number;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $countryCodeOrLocale
     */
    public function toInternational(string $number, ?string $countryCodeOrLocale = null): string
    {
        $phone = $this->tryToPhoneNumber($number, $countryCodeOrLocale);

        return $phone ? $phone->toInternational() : $number;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $countryCodeOrLocale
     */
    public function toRfc3966(string $number, ?string $countryCodeOrLocale = null): string
    {
        $phone = $this->tryToPhoneNumber($number, $countryCodeOrLocale);

        return $phone ? $phone->toRfc3966() : $number;
    }
}
