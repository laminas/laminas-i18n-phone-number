<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber;

use Laminas\I18n\PhoneNumber\Exception\InvalidArgumentException;
use Locale;

use function preg_match;
use function sprintf;
use function strtoupper;

/**
 * @psalm-immutable
 */
final class CountryCode
{
    /**
     * @readonly
     * @var non-empty-string
     */
    private string $code;

    /** @param non-empty-string $code */
    private function __construct(string $code)
    {
        $this->code = $code;
    }

    /** @return non-empty-string */
    public function toString(): string
    {
        return $this->code;
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }

    /** @param non-empty-string $code */
    public static function fromString(string $code): self
    {
        $code = strtoupper($code);
        if (! preg_match('/^[A-Z]{2}$/', $code)) {
            throw new InvalidArgumentException('Country codes should be 2 letter ISO 3166 strings');
        }

        $displayName = Locale::getDisplayRegion('-' . $code, 'GB');
        if ($displayName === '' || $displayName === 'Unknown Region') {
            throw new InvalidArgumentException(sprintf(
                'The country code "%s" does not correspond to a known country',
                $code
            ));
        }

        return new self($code);
    }

    /** @param non-empty-string $locale */
    public static function fromLocale(string $locale): self
    {
        $region = Locale::getRegion($locale);
        /** @psalm-suppress TypeDoesNotContainNull */
        if ($region === null || $region === '') {
            throw new InvalidArgumentException(sprintf(
                'The string "%s" could not be parsed as a valid locale',
                $locale
            ));
        }

        return self::fromString($region);
    }

    /** @param string|self|null $countryCodeOrLocale */
    public static function detect($countryCodeOrLocale): self
    {
        if ($countryCodeOrLocale instanceof self) {
            return $countryCodeOrLocale;
        }

        if ($countryCodeOrLocale === null || $countryCodeOrLocale === '') {
            $countryCodeOrLocale = Locale::getDefault();
        }

        /** @psalm-var non-empty-string $countryCodeOrLocale */

        $code = self::tryFromString($countryCodeOrLocale);
        if ($code) {
            return $code;
        }

        throw new InvalidArgumentException(sprintf(
            'The string "%s" could not be understood as either a locale or an ISO 3166 country code',
            $countryCodeOrLocale
        ));
    }

    /** @param non-empty-string $countryCodeOrLocale */
    public static function tryFromString(string $countryCodeOrLocale): ?self
    {
        try {
            return self::fromLocale($countryCodeOrLocale);
        } catch (InvalidArgumentException $e) {
        }

        try {
            return self::fromString($countryCodeOrLocale);
        } catch (InvalidArgumentException $e) {
        }

        return null;
    }
}
