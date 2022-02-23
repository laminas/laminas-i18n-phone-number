<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber;

use Laminas\I18n\PhoneNumber\Exception\InvalidPhoneNumberException;
use Laminas\I18n\PhoneNumber\Exception\UnrecognizableNumberException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber as LibPhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ShortNumberInfo;

use function array_key_exists;

final class PhoneNumberValue
{
    public const TYPE_FIXED         = 1;
    public const TYPE_MOBILE        = 2;
    public const TYPE_TOLL_FREE     = 4;
    public const TYPE_PREMIUM_RATE  = 8;
    public const TYPE_SHARED_COST   = 16;
    public const TYPE_VOIP          = 32;
    public const TYPE_PERSONAL      = 64;
    public const TYPE_PAGER         = 128;
    public const TYPE_UAN           = 256;
    public const TYPE_EMERGENCY     = 512;
    public const TYPE_VOICEMAIL     = 1024;
    public const TYPE_SHORT_CODE    = 2048;
    public const TYPE_STANDARD_RATE = 4096;

    public const TYPE_UNKNOWN = 8192;

    /** @var int */
    public const TYPE_KNOWN = self::TYPE_FIXED
                            | self::TYPE_MOBILE
                            | self::TYPE_TOLL_FREE
                            | self::TYPE_PREMIUM_RATE
                            | self::TYPE_SHARED_COST
                            | self::TYPE_VOIP
                            | self::TYPE_PERSONAL
                            | self::TYPE_PAGER
                            | self::TYPE_UAN
                            | self::TYPE_EMERGENCY
                            | self::TYPE_VOICEMAIL
                            | self::TYPE_SHORT_CODE
                            | self::TYPE_STANDARD_RATE;

    public const TYPE_ANY = self::TYPE_KNOWN | self::TYPE_UNKNOWN;

    public const TYPE_RECOMMENDED = self::TYPE_FIXED
                                  | self::TYPE_MOBILE
                                  | self::TYPE_VOIP;

    /** @internal \Laminas\I18n */
    public const TYPE_MAP = [
        PhoneNumberType::FIXED_LINE           => self::TYPE_FIXED,
        PhoneNumberType::MOBILE               => self::TYPE_MOBILE,
        PhoneNumberType::FIXED_LINE_OR_MOBILE => self::TYPE_FIXED | self::TYPE_MOBILE,
        PhoneNumberType::TOLL_FREE            => self::TYPE_TOLL_FREE,
        PhoneNumberType::PREMIUM_RATE         => self::TYPE_PREMIUM_RATE,
        PhoneNumberType::SHARED_COST          => self::TYPE_SHARED_COST,
        PhoneNumberType::VOIP                 => self::TYPE_VOIP,
        PhoneNumberType::PERSONAL_NUMBER      => self::TYPE_PERSONAL,
        PhoneNumberType::PAGER                => self::TYPE_PAGER,
        PhoneNumberType::UAN                  => self::TYPE_UAN,
        PhoneNumberType::UNKNOWN              => self::TYPE_UNKNOWN,
        PhoneNumberType::EMERGENCY            => self::TYPE_EMERGENCY,
        PhoneNumberType::VOICEMAIL            => self::TYPE_VOICEMAIL,
        PhoneNumberType::SHORT_CODE           => self::TYPE_SHORT_CODE,
        PhoneNumberType::STANDARD_RATE        => self::TYPE_STANDARD_RATE,
    ];

    private LibPhoneNumber $number;
    /** @var non-empty-string */
    private string $regionCode;
    private bool $shortNumber;

    /** @param non-empty-string $regionCode */
    private function __construct(
        LibPhoneNumber $number,
        string $regionCode,
        bool $shortNumber
    ) {
        $this->number      = $number;
        $this->regionCode  = $regionCode;
        $this->shortNumber = $shortNumber;
    }

    /**
     * @param non-empty-string      $phoneNumber
     * @param non-empty-string|null $countryCode
     * @throws UnrecognizableNumberException If it is impossible to parse the given string.
     * @throws InvalidPhoneNumberException If the phone number is invalid for the given or detected region.
     * @return static
     */
    public static function fromString(string $phoneNumber, ?string $countryCode = null): self
    {
        $util = PhoneNumberUtil::getInstance();
        try {
            $prototype = $util->parse($phoneNumber, $countryCode, null, true);
        } catch (NumberParseException $error) {
            throw UnrecognizableNumberException::withString($phoneNumber, $error);
        }

        $short      = ShortNumberInfo::getInstance();
        $regionCode = self::regionCodeForNumber($prototype, $countryCode);
        if ($regionCode === null) {
            throw InvalidPhoneNumberException::undetectableRegion($phoneNumber);
        }

        $isShortNumber = $short->isValidShortNumberForRegion($prototype, $regionCode)
            || $short->isValidShortNumber($prototype);
        $isValidNumber = $util->isValidNumberForRegion($prototype, $regionCode)
            || $util->isValidNumber($prototype);

        if (! $isShortNumber && ! $isValidNumber) {
            throw InvalidPhoneNumberException::with(
                $phoneNumber,
                $regionCode
            );
        }

        return new self(
            $prototype,
            $regionCode,
            $isShortNumber
        );
    }

    public function __toString(): string
    {
        if ($this->shortNumber) {
            return $this->toNational();
        }

        return $this->toE164();
    }

    /**
     * The ISO 3166 Country Code associated with the number
     *
     * @return non-empty-string
     */
    public function regionCode(): string
    {
        return $this->regionCode;
    }

    /**
     * Whether this number is considered a 'short number' or not
     */
    public function isShortNumber(): bool
    {
        return $this->shortNumber;
    }

    public function toE164(): string
    {
        return PhoneNumberUtil::getInstance()->format($this->number, PhoneNumberFormat::E164);
    }

    public function toNational(): string
    {
        return PhoneNumberUtil::getInstance()->format($this->number, PhoneNumberFormat::NATIONAL);
    }

    public function toInternational(): string
    {
        return PhoneNumberUtil::getInstance()->format($this->number, PhoneNumberFormat::INTERNATIONAL);
    }

    public function toRfc3966(): string
    {
        return PhoneNumberUtil::getInstance()->format($this->number, PhoneNumberFormat::RFC3966);
    }

    public function type(): int
    {
        $type = PhoneNumberUtil::getInstance()->getNumberType($this->number);

        if ($this->isShortNumber()) {
            $short = ShortNumberInfo::getInstance();
            if ($short->isEmergencyNumber($this->toNational(), $this->regionCode)) {
                return self::TYPE_EMERGENCY;
            }

            return self::TYPE_SHORT_CODE;
        }

        if (! array_key_exists($type, self::TYPE_MAP)) {
            return self::TYPE_UNKNOWN;
        }

        return self::TYPE_MAP[$type];
    }

    /**
     * @param non-empty-string|null $givenCode
     * @return non-empty-string|null
     */
    private static function regionCodeForNumber(LibPhoneNumber $number, ?string $givenCode): ?string
    {
        $dialingCode = $number->getCountryCode();
        if (! $dialingCode) {
            return $givenCode;
        }

        $util       = PhoneNumberUtil::getInstance();
        $regionCode = $util->getRegionCodeForCountryCode($dialingCode);
        $regionCode = $regionCode !== PhoneNumberUtil::UNKNOWN_REGION
            ? $regionCode
            : $givenCode;

        return empty($regionCode) ? null : $regionCode;
    }
}
