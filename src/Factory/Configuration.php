<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Factory;

use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\ConfigProvider;
use Psr\Container\ContainerInterface;

/**
 * @psalm-import-type PackageConfig from ConfigProvider
 * @psalm-internal \Laminas\I18n\PhoneNumber
 */
final class Configuration
{
    /** @psalm-suppress UnusedConstructor */
    private function __construct()
    {
    }

    public static function defaultCountryCode(ContainerInterface $container): CountryCode
    {
        $options = self::componentConfig($container);

        return CountryCode::detect($options['default-country-code'] ?? null);
    }

    /** @return PackageConfig */
    public static function componentConfig(ContainerInterface $container): array
    {
        if (! $container->has('config')) {
            return [];
        }

        /** @psalm-var array $config */
        $config = $container->get('config');

        /** @psalm-var PackageConfig|null $options */
        $options = $config['laminas-i18n-phone-number'] ?? null;

        return $options ?? [];
    }
}
