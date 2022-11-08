<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Form\Element;

use Laminas\Form\Element;
use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\PhoneNumberValue;
use Laminas\I18n\PhoneNumber\Validator\PhoneNumber as PhoneNumberValidator;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function is_int;
use function is_string;

/**
 * @psalm-import-type InputSpecification from InputFilterInterface
 * @psalm-type Options = array{
 *     country_context?: non-empty-string|null,
 *     default_country?: non-empty-string|null,
 *     allowed_types?: int-mask-of<PhoneNumberValue::TYPE_*>|null
 * }&array<string, mixed>
 */
final class PhoneNumber extends Element implements InputProviderInterface
{
    /** @inheritDoc */
    protected $attributes = [
        'type' => 'text',
    ];

    /**
     * The default country is passed to the phone number validator so that input in national format can be accepted
     */
    private ?CountryCode $defaultCountry = null;

    /**
     * The name of a form element used to retrieve a user-supplied country code from during validation
     */
    private ?string $countryContext = null;

    /**
     * A bit mask of allowable types of number
     *
     * @see PhoneNumberValue::TYPE_*
     *
     * @var int-mask-of<PhoneNumberValue::TYPE_*>|null
     */
    private ?int $allowedTypes = null;

    /**
     * @param Options|iterable $options
     * @return $this
     */
    public function setOptions(iterable $options): self
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (
            isset($options['country_context'])
            && is_string($options['country_context'])
            && $options['country_context'] !== ''
        ) {
            $this->setCountryContext($options['country_context']);
        }

        if (
            isset($options['default_country'])
            && is_string($options['default_country'])
            && $options['default_country'] !== ''
        ) {
            $this->setDefaultCountry($options['default_country']);
        }

        if (
            isset($options['allowed_types'])
            && is_int($options['allowed_types'])
        ) {
            $this->setAllowedTypes($options['allowed_types']);
        }

        unset($options['country_context'], $options['default_country'], $options['allowed_types']);

        parent::setOptions($options);

        return $this;
    }

    /** @return InputSpecification */
    public function getInputSpecification(): array
    {
        return [
            'name'       => (string) $this->getName(),
            'required'   => true,
            'validators' => [
                [
                    'name'    => PhoneNumberValidator::class,
                    'options' => [
                        'country_context' => $this->countryContext,
                        'country'         => $this->defaultCountry?->toString(),
                        'allowed_types'   => $this->allowedTypes,
                    ],
                ],
            ],
        ];
    }

    /** @param non-empty-string $inputName */
    public function setCountryContext(string $inputName): void
    {
        $this->countryContext = $inputName;
    }

    /** @param non-empty-string $code */
    public function setDefaultCountry(string $code): void
    {
        $this->defaultCountry = CountryCode::fromString($code);
    }

    /** @param int-mask-of<PhoneNumberValue::TYPE_*> $types */
    public function setAllowedTypes(int $types): void
    {
        $this->allowedTypes = $types;
    }
}
