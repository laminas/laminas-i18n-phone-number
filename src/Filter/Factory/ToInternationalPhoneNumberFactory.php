<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use Psr\Container\ContainerInterface;

final class ToInternationalPhoneNumberFactory
{
    public function __invoke(ContainerInterface $container): ToInternationalPhoneNumber
    {
        return new ToInternationalPhoneNumber(Configuration::defaultCountryCode($container));
    }
}
