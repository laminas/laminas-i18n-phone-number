<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber;

use Laminas\ServiceManager\ConfigInterface;

/**
 * @see ConfigInterface
 *
 * @psalm-import-type ServiceManagerConfigurationType from ConfigInterface
 *
 * @psalm-type PackageConfig = array{
 *     default-country-code?: non-empty-string|null
 * }
 */
final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'laminas-i18n-phone-number' => [
                'default-country-code'    => null,
                'acceptable-number-types' => PhoneNumberValue::TYPE_RECOMMENDED,
            ],
            'filters'                   => $this->filterConfiguration(),
            'validators'                => $this->validatorConfiguration(),
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
            'factories' => [],
        ];
    }
}
