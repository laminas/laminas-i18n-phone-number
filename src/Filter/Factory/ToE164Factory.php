<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\Filter\ToE164;
use Psr\Container\ContainerInterface;

final class ToE164Factory
{
    public function __invoke(ContainerInterface $container): ToE164
    {
        return new ToE164(Configuration::defaultCountryCode($container));
    }
}
