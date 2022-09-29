<?php

declare(strict_types=1);

namespace Laminas\I18n\PhoneNumber\Filter\Factory;

use Laminas\I18n\PhoneNumber\Factory\Configuration;
use Laminas\I18n\PhoneNumber\Filter\AbstractFilter;
use Laminas\I18n\PhoneNumber\Filter\ToInternationalPhoneNumber;
use Psr\Container\ContainerInterface;

/** @psalm-import-type Options from AbstractFilter */
final class ToInternationalPhoneNumberFactory
{
    /** @param Options|null $options */
    public function __invoke(
        ContainerInterface $container,
        string|null $name = null,
        array|null $options = null,
    ): ToInternationalPhoneNumber {
        return new ToInternationalPhoneNumber(Configuration::defaultCountryCode($container), $options);
    }
}
