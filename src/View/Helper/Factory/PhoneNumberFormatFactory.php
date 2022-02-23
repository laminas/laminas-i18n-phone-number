<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\View\Helper\Factory;

use Laminas\I18n\PhoneNumber\Factory\ConfigurationTrait;
use Laminas\I18n\PhoneNumber\View\Helper\PhoneNumberFormat;
use Psr\Container\ContainerInterface;

final class PhoneNumberFormatFactory
{
    use ConfigurationTrait;

    public function __invoke(ContainerInterface $container): PhoneNumberFormat
    {
        return new PhoneNumberFormat($this->defaultCountryCode($container));
    }
}
