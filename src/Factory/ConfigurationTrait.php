<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Factory;

use Laminas\I18n\PhoneNumber\ConfigProvider;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * @psalm-import-type PackageConfig from ConfigProvider
 * @psalm-internal \Laminas\I18n\PhoneNumber
 */
trait ConfigurationTrait
{
    private function defaultCountryCode(ContainerInterface $container): ?string
    {
        $options = $this->componentConfig($container);

        return $options['default-country-code'] ?? null;
    }

    /** @return PackageConfig */
    private function componentConfig(ContainerInterface $container): array
    {
        if (! $container->has('config')) {
            return [];
        }

        /** @psalm-var array $config */
        $config = $container->get('config');

        /** @psalm-var PackageConfig|null $options */
        $options = $config['laminas-i18n-phone-number'] ?? null;

        return is_array($options) ? $options : [];
    }
}
