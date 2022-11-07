<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\View\Helper\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\View\Helper\PhoneNumberFormat;
use Psr\Container\ContainerInterface;

final class PhoneNumberFormatFactory
{
    public function __invoke(ContainerInterface $container): PhoneNumberFormat
    {
        return new PhoneNumberFormat(Configuration::defaultCountryCode($container));
    }
}
