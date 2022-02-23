<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter\Factory;

use Laminas\I18n\PhoneNumber\Factory\ConfigurationTrait;
use Laminas\I18n\PhoneNumber\Filter\ToNationalPhoneNumber;
use Psr\Container\ContainerInterface;

final class ToNationalPhoneNumberFactory
{
    use ConfigurationTrait;

    public function __invoke(ContainerInterface $container): ToNationalPhoneNumber
    {
        return new ToNationalPhoneNumber($this->defaultCountryCode($container));
    }
}
