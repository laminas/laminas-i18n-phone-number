<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber;

use Laminas\ServiceManager\ConfigInterface;

/**
 * A configuration provider for use with other Laminas components and Mezzio applications
 *
 * @link ConfigInterface
 *
 * @psalm-import-type ServiceManagerConfigurationType from ConfigInterface
 *
 * @psalm-type PackageConfig = array{
 *     default-country-code?: non-empty-string|null,
 *     acceptable-number-types?: int
 * }
 */
final class ConfigProvider
{
    /**
     * @return array{
     *     laminas-i18n-phone-number: PackageConfig,
     *     filters: ServiceManagerConfigurationType,
     *     validators: ServiceManagerConfigurationType,
     *     view_helpers: ServiceManagerConfigurationType,
     * }
     */
    public function __invoke(): array
    {
        return [
            'laminas-i18n-phone-number' => [
                'default-country-code'    => null,
                'acceptable-number-types' => PhoneNumberValue::TYPE_RECOMMENDED,
            ],
            'filters'                   => $this->filterConfiguration(),
            'validators'                => $this->validatorConfiguration(),
            'view_helpers'              => $this->viewHelperConfiguration(),
            'form_elements'             => $this->formElementConfiguration(),
        ];
    }

    /**
     * @return ServiceManagerConfigurationType
     */
    private function filterConfiguration(): array
    {
        return [
            'factories' => [
                Filter\ToE164::class                     => Filter\Factory\ToE164Factory::class,
                Filter\ToInternationalPhoneNumber::class => Filter\Factory\ToInternationalPhoneNumberFactory::class,
                Filter\ToNationalPhoneNumber::class      => Filter\Factory\ToNationalPhoneNumberFactory::class,
            ],
            'aliases'   => [
                'toE164'                     => Filter\ToE164::class,
                'toInternationalPhoneNumber' => Filter\ToInternationalPhoneNumber::class,
                'toNationalPhoneNumber'      => Filter\ToNationalPhoneNumber::class,
            ],
        ];
    }

    /**
     * @return ServiceManagerConfigurationType
     */
    private function validatorConfiguration(): array
    {
        return [
            'factories' => [
                Validator\PhoneNumber::class => Validator\Factory\PhoneNumberFactory::class,
            ],
            'aliases'   => [
                'phoneNumber' => Validator\PhoneNumber::class,
            ],
        ];
    }

    /**
     * @return ServiceManagerConfigurationType
     */
    private function viewHelperConfiguration(): array
    {
        return [
            'factories' => [
                View\Helper\PhoneNumberFormat::class => View\Helper\Factory\PhoneNumberFormatFactory::class,
            ],
            'aliases'   => [
                'phoneNumberFormat' => View\Helper\PhoneNumberFormat::class,
            ],
        ];
    }

    /**
     * @return ServiceManagerConfigurationType
     */
    private function formElementConfiguration(): array
    {
        return [
            'factories' => [
                Form\Element\PhoneNumber::class => Form\Element\Factory\PhoneNumberFactory::class,
            ],
        ];
    }
}
