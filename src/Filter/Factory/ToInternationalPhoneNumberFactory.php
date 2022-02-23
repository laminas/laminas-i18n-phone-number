<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter\Factory;

use Laminas\I18n\PhoneNumber\Factory\ConfigurationTrait;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use Psr\Container\ContainerInterface;

final class ToInternationalPhoneNumberFactory
{
    use ConfigurationTrait;

    public function __invoke(ContainerInterface $container): ToInternationalPhoneNumber
    {
        return new ToInternationalPhoneNumber($this->defaultCountryCode($container));
    }
}
