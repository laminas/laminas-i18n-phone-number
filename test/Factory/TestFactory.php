<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Test\Factory;

use Laminas\I18n\CountryCode;
use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Psr\Container\ContainerInterface;

final class TestFactory
{
    public function getDefaultCountry(ContainerInterface $container): CountryCode
    {
        return Configuration::defaultCountryCode($container);
    }
}
