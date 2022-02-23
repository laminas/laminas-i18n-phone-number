<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\View\Helper;

use Laminas\I18n\PhoneNumber\Exception\ExceptionInterface;
use Laminas\I18n\PhoneNumber\Exception\InvalidArgumentException;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;

use function preg_match;
use function strtoupper;

final class PhoneNumberFormat
{
    /** @var non-empty-string|null */
    private ?string $country;

    /**
     * @param non-empty-string|null $defaultCountryCode
     * @throws InvalidArgumentException If the country code is not a valid ISO 3166 string.
     */
    public function __construct(?string $defaultCountryCode = null)
    {
        $this->country = self::assertValidCountryCode($defaultCountryCode);
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @param non-empty-string|null $code
     * @return non-empty-string|null
     */
    private static function assertValidCountryCode(?string $code): ?string
    {
        if (empty($code)) {
            return null;
        }

        if (! preg_match('/^[A-Z]{2}$/i', $code)) {
            throw new InvalidArgumentException('Country codes should be 2 letter ISO 3166 strings');
        }

        return strtoupper($code);
    }

    /**
     * @param non-empty-string|null $code
     * @return non-empty-string|null
     */
    private function coalesceCountryCode(?string $code): ?string
    {
        $code = self::assertValidCountryCode($code);

        return $code ?: $this->country;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $country
     */
    public function toPhoneNumber(string $number, ?string $country = null): ?PhoneNumberValue
    {
        try {
            return PhoneNumberValue::fromString(
                $number,
                $this->coalesceCountryCode($country)
            );
        } catch (ExceptionInterface $error) {
            return null;
        }
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $country
     */
    public function toE164(string $number, ?string $country = null): string
    {
        $phone = $this->toPhoneNumber($number, $country);

        return $phone ? $phone->toE164() : $number;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $country
     */
    public function toNational(string $number, ?string $country = null): string
    {
        $phone = $this->toPhoneNumber($number, $country);

        return $phone ? $phone->toNational() : $number;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $country
     */
    public function toInternational(string $number, ?string $country = null): string
    {
        $phone = $this->toPhoneNumber($number, $country);

        return $phone ? $phone->toInternational() : $number;
    }

    /**
     * @param non-empty-string      $number
     * @param non-empty-string|null $country
     */
    public function toRfc3966(string $number, ?string $country = null): string
    {
        $phone = $this->toPhoneNumber($number, $country);

        return $phone ? $phone->toRfc3966() : $number;
    }
}
