<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Validator;

use Laminas\Form\Annotation\Options;
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Exception\InvalidOptionException;
use Laminas\I18n\PhoneNumber\Exception\InvalidPhoneNumberExceptionInterface;
use Laminas\I18n\PhoneNumber\Exception\UnrecognizableNumberException;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Stringable;
use Traversable;

use function is_array;
use function is_int;
use function is_scalar;
use function is_string;
use function sprintf;

/**
 * @psalm-type Options = array{
 *     country?: non-empty-string|null,
 *     country_context?: non-empty-string|null,
 *     allowed_types?: int-mask-of<PhoneNumberValue::TYPE_*>|null,
 * }&array<string, mixed>
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
     *
     * @var int-mask-of<PhoneNumberValue::TYPE_*>
     */
    private int $allowTypes = PhoneNumberValue::TYPE_ANY;

    /**
     * Input name to check for an ISO 3166 Country Code during validation
     *
     * If non-empty, the validation context will be searched for the value corresponding to the given key. When found,
     * and when the value is a valid country code, the given phone number will be validated as a number for the country
     * determined by this value.
     *
     * @var non-empty-string|null
     */
    private ?string $countryContext = null;

    /** @param Options|iterable<string, mixed>|null $options */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * @param Options|iterable<string, mixed> $options
     * @return $this
     */
    public function setOptions($options = []): self
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['country']) && is_string($options['country']) && $options['country'] !== '') {
            $this->setCountry($options['country']);
        }

        if (
            isset($options['country_context'])
            && is_string($options['country_context'])
            && $options['country_context'] !== ''
        ) {
            $this->setCountryContext($options['country_context']);
        }

        if (isset($options['allowed_types']) && is_int($options['allowed_types'])) {
            $this->setAllowedTypes($options['allowed_types']);
        }

        unset($options['country'], $options['country_context'], $options['allowed_types']);

        parent::setOptions($options);

        return $this;
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $context
     */
    public function isValid($value, ?array $context = null): bool
    {
        if (! is_scalar($value) && ! $value instanceof Stringable) {
            $this->error(self::INVALID_TYPE);

            return false;
        }

        $value = (string) $value;

        $this->setValue($value);

        if ($value === '') {
            $this->error(self::INVALID_TYPE);

            return false;
        }

        $country = $this->resolveCountry($context);

        try {
            $number = PhoneNumberValue::fromString($value, $country?->toString());
        } catch (UnrecognizableNumberException) {
            $this->error(self::NO_MATCH);

            return false;
        } catch (InvalidPhoneNumberExceptionInterface) {
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

    /** @param non-empty-string $inputName */
    public function setCountryContext(string $inputName): void
    {
        $this->countryContext = $inputName;
    }

    /** @param int-mask-of<PhoneNumberValue::TYPE_*> $types */
    public function setAllowedTypes(int $types): void
    {
        if ($types <= 0 || ($types & PhoneNumberValue::TYPE_KNOWN) !== $types) {
            throw new InvalidOptionException('The allowed types provided do not match known valid types');
        }

        $this->allowTypes = $types;
    }

    /** @param array<string, mixed> $validationContext */
    private function resolveCountry(?array $validationContext): ?CountryCode
    {
        if (! is_array($validationContext) || ! $this->countryContext) {
            return $this->country;
        }

        $code = $validationContext[$this->countryContext] ?? null;
        if (! is_string($code) || $code === '') {
            return $this->country;
        }

        return CountryCode::tryFromString($code) ?? $this->country;
    }
}
