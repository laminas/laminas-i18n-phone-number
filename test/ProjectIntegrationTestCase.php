<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test;

use Laminas;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @psalm-import-type ServiceManagerConfigurationType from Laminas\ServiceManager\ConfigInterface
 * @phpcs:disable WebimpressCodingStandard.NamingConventions.AbstractClass.Prefix
 */
abstract class ProjectIntegrationTestCase extends TestCase
{
    private const CONFIG_PROVIDERS = [
        Laminas\Filter\ConfigProvider::class,
        Laminas\Form\ConfigProvider::class,
        Laminas\I18n\ConfigProvider::class,
        Laminas\I18n\PhoneNumber\ConfigProvider::class,
        Laminas\InputFilter\ConfigProvider::class,
        Laminas\Validator\ConfigProvider::class,
    ];

    protected static function getContainer(array $userConfig = []): ContainerInterface
    {
        $providers   = self::CONFIG_PROVIDERS;
        $providers[] = new Laminas\ConfigAggregator\ArrayProvider($userConfig);

        $aggregator   = new Laminas\ConfigAggregator\ConfigAggregator($providers);
        $config       = $aggregator->getMergedConfig();
        $dependencies = $config['dependencies'] ?? [];
        self::assertIsArray($dependencies);
        /** @psalm-suppress MixedArrayAssignment */
        $dependencies['services']['config'] = $config;
        /** @psalm-var ServiceManagerConfigurationType $dependencies */

        return new Laminas\ServiceManager\ServiceManager($dependencies);
    }
}
