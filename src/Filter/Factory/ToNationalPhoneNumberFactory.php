<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use Psr\Container\ContainerInterface;

final class ToNationalPhoneNumberFactory
{
    public function __invoke(ContainerInterface $container): ToNationalPhoneNumber
    {
        return new ToNationalPhoneNumber(Configuration::defaultCountryCode($container));
    }
}
