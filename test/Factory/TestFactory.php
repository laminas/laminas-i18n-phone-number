<?php

declare(strict_types=1);

namespace LaminasTest\I18n\PhoneNumber\Factory;

use Laminas\I18n\PhoneNumber\CountryCode;
use Laminas\I18n\PhoneNumber\Factory\ConfigurationTrait;
use Psr\Container\ContainerInterface;

final class TestFactory
{
    use ConfigurationTrait;

    public function getDefaultCountry(ContainerInterface $container): CountryCode
    {
        return $this->defaultCountryCode($container);
    }
}
