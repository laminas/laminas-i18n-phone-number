<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Validator;

use Laminas\I18n\PhoneNumber\CountryCode;
use Laminas\I18n\PhoneNumber\Exception\InvalidOptionException;
use Laminas\I18n\PhoneNumber\Exception\InvalidPhoneNumberException;
use Laminas\I18n\PhoneNumber\Exception\UnrecognizableNumberException;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Traversable;

use function is_scalar;
use function sprintf;

/**
 * @psalm-type Options = array{
 *     country?: non-empty-string,
 *     allowed_types?: int,
 * }
 */
final class PhoneNumber extends AbstractValidator
{
    public const INVALID_TYPE = 'invalidInputType';
    public const NO_MATCH     = 'phoneNumberNoMatch';
    public const INVALID      = 'invalidPhoneNumber';
    public const NOT_ALLOWED  = 'typeNotAllowed';

    /** @var array<string, non-empty-string> */
    protected $messageTemplates = [
        self::INVALID_TYPE => 'Invalid type given. Non-empty string expected',
        self::NO_MATCH     => 'The input does not match any known phone number format',
        self::INVALID      => 'The given phone number is not valid',
        self::NOT_ALLOWED  => 'The given phone number is not an acceptable type of number',
    ];

    /**
     * ISO 3166 Country Code
     *
     * If provided, phone numbers without an international prefix will be validated
     * as a national number in this country.
     */
    private ?CountryCode $country = null;

    /**
     * Validate for specific types of phone number
     *
     * Restrict otherwise valid types of phone numbers to a subset of allowed types.
     */
    private int $allowTypes = PhoneNumberValue::TYPE_ANY;

    /** @param Options|Traversable|null $options */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        /** @psalm-var Options $options */

        if (isset($options['country'])) {
            $this->setCountry($options['country']);
        }

        if (isset($options['allowed_types'])) {
            $this->setAllowedTypes($options['allowed_types']);
        }

        parent::__construct($options);
    }

    /** @param mixed $value */
    public function isValid($value): bool
    {
        if (! is_scalar($value)) {
            $this->error(self::INVALID_TYPE);

            return false;
        }

        $value = (string) $value;

        $this->setValue($value);

        if (empty($value)) {
            $this->error(self::INVALID_TYPE);

            return false;
        }

        try {
            $number = PhoneNumberValue::fromString($value, $this->country ? $this->country->toString() : null);
        } catch (UnrecognizableNumberException $error) {
            $this->error(self::NO_MATCH);

            return false;
        } catch (InvalidPhoneNumberException $error) {
            $this->error(self::INVALID);

            return false;
        }

        $type = $number->type();
        if (($type & $this->allowTypes) === 0) {
            $this->error(self::NOT_ALLOWED);

            return false;
        }

        return true;
    }

    /** @param non-empty-string $countryCodeOrLocale */
    public function setCountry(string $countryCodeOrLocale): void
    {
        $code = CountryCode::tryFromString($countryCodeOrLocale);

        if (! $code) {
            throw new InvalidOptionException(sprintf(
                'Country codes must be ISO 3166 2-letter codes or Locale strings. Received "%s"',
                $countryCodeOrLocale
            ));
        }

        $this->country = $code;
    }

    public function setAllowedTypes(int $types): void
    {
        if ($types <= 0 || ($types & PhoneNumberValue::TYPE_KNOWN) !== $types) {
            throw new InvalidOptionException('The allowed types provided do not match known valid types');
        }

        $this->allowTypes = $types;
    }
}
