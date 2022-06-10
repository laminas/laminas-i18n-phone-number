<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber;

use Laminas\ServiceManager\ConfigInterface;

/**
 * A module class for use with Laminas MVC applications
 *
 * @link ConfigInterface
 *
 * @psalm-import-type ServiceManagerConfigurationType from ConfigInterface
 * @psalm-import-type PackageConfig from ConfigProvider
 */
final class Module
{
    /**
     * @return array{
     *     laminas-i18n-phone-number: PackageConfig,
     *     filters: ServiceManagerConfigurationType,
     *     validators: ServiceManagerConfigurationType,
     *     view_helpers: ServiceManagerConfigurationType,
     * }
     */
    public function getConfig(): array
    {
        return (new ConfigProvider())();
    }
}
